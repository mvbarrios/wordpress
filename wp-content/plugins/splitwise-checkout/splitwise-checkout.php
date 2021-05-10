<?php

/**
* @package Dixp-mine
* @version 1.0

 * Plugin Name: WooCommerce Splitwise Payment Gateway
 * Description: Take splitwise payment on stores
 * Author: Maria Virginia Barrios
 * Author URI: https://mvbarrios.com
 * License: GPL v2 or later
 * Version: 1.0
 * 
*/
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

function register_my_session() {
    if( ! session_id() ) {
        session_start();
    }
}
add_action('init', 'register_my_session');

//after activate

add_action('admin_init', 'plugin_redirect');

function activate_plugin_activate() {
    add_option( 'client_credentials_identifier', '0' );
    add_option( 'client_credentials_secret', '0' );
    add_option( 'callback_uri', '0' );
    add_option('my_plugin_do_activation_redirect', true);
}
register_activation_hook( __FILE__, 'activate_plugin_activate' );

function plugin_redirect() {
    if ( get_option('my_plugin_do_activation_redirect', false) ) {
        delete_option('my_plugin_do_activation_redirect');
        wp_redirect('admin.php?page=configuration_page');
    }
}

register_uninstall_hook( __FILE__, 'split_uninstall' );
function split_uninstall(){
    delete_option( 'client_credentials_identifier', '0' );
    delete_option( 'client_credentials_secret', '0' );
    delete_option( 'callback_uri', '0' );
}

//create a page if no exist
function create_page() {
    $name_of_the_page = 'Checkout Page';
    if ( get_page_by_title($name_of_the_page) == NULL ) {
        $my_post = array(
            'post_title'    => $name_of_the_page,
            'post_content'  => '',
            'post_status'   => 'publish',
            'guid'          => $name_of_the_page,
            'post_type'     => 'page',
            'post_content'  => '[splitwise-checkout]',
        );
        $homepage_id =  wp_insert_post( $my_post );
    }
}
register_activation_hook(__FILE__, 'create_page');

function custom_checkout( $atts ) {

        require 'risan/vendor/autoload.php';
        
        // Create an instance of Risan\OAuth1\OAuth1 class.
        $signer = new Risan\OAuth1\Signature\HmacSha1Signer();
        $oauth1 = Risan\OAuth1\OAuth1Factory::create([
            'client_credentials_identifier' => get_option( 'client_credentials_identifier' ),
            'client_credentials_secret'     => get_option( 'client_credentials_secret' ),
            'temporary_credentials_uri'     => 'https://secure.splitwise.com/oauth/request_token',
            'authorization_uri'             => 'https://secure.splitwise.com/oauth/authorize',
            'token_credentials_uri'         => 'https://secure.splitwise.com/oauth/access_token',
            'callback_uri'                  =>  get_option( 'callback_uri' ),
        ],$signer);
        
        var_dump($oauth1);
        var_dump(get_option( 'client_credentials_identifier' ));

        if ( isset($_SESSION['token_credentials']) ) {
            // Get back the previosuly obtain token credentials (step 3).
            $tokenCredentials = unserialize($_SESSION['token_credentials']);
            $oauth1->setTokenCredentials($tokenCredentials);
            var_dump($tokenCredentials);


            /*
            *
            * Get information of user to create order
            * 
            */

            $getUser ='https://secure.splitwise.com/api/v3.0/get_current_user';
            $getInfo = $oauth1->request( 'GET', $getUser );
            $userArray = json_decode( $getInfo->getBody()->getContents(), true );
            $username = $userArray['user']['first_name'];
            $user_id= username_exists( $username );
            /*
                * 
                *Check if user is created
                *
                */

            if ( ! $user_id && $user_id == false ) {
                $email      = $userArray['user']['email'];
                $password   = wp_generate_password( 12, false );
                $first_name = $userArray['user']['first_name'];
                $last_name  = $userArray['user']['last_name'];
                
                $user_data = array(
                    'user_login' => $username,
                    'user_pass'  => $password,
                    'user_email' => $email,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'role'       => 'customer',
                );

                $user_ids = wp_insert_user( $user_data );

                $userOrder = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $email,
                );
                var_dump($userOrder);
            
                // Update Billing and shipping user data
                foreach( $userOrder as $key => $value ) {
                    update_user_meta( $user_id, 'billing_' . $key, $value ); // Billing user data

                    if( !in_array( $key, array('phone', 'email') ) ) {
                        update_user_meta( $user_id, 'shipping_' . $key, $value ); // Shipping user data
                    }
                }

                // Send Customer new account notification
                WC()->mailer()->get_emails()['WC_Email_Customer_New_Account']->trigger( $user_id, $password, true );
        
            }
            /*
            *
            * Create expense in splitwise
            * 
            */
            $urlToCreateAExpense = 'https://secure.splitwise.com/api/v3.0/create_expense';
            $descriptionToExpense = '';
            $totalToExpense = 0;

            global $woocommerce;
            $userid = get_current_user_id();
            $order = wc_create_order(array( 'customer_id'=>$userid) );
            $order_id= $order->get_id();
            $order->set_address( $userOrder, 'billing' );
            $items = $woocommerce->cart->get_cart();

            foreach( $items as $item => $values ) {
                $_product =  wc_get_product( $values['data']->get_id()); 
                $order->add_product( $_product );
                $price = get_post_meta( $values['product_id'] , '_price', true )*$values['quantity'];
                $descriptionToExpense  .= $values['quantity'].' '.$_product->get_title().' $'.$price.' , ';
                $totalToExpense += $price;
            }

            $order->calculate_totals();
            $descriptionToExpense .= 'TOTAL: $'.$totalToExpense;

            $parametersToCreateAExpense = array(
                'cost' => $totalToExpense,
                'group_id'=> 0,
                'description' => $descriptionToExpense,
                'split_equally' => true,
            );

            $responseRequest = $oauth1->request('POST', $urlToCreateAExpense,[
                'form_params' => $parametersToCreateAExpense
                ]
            );

            $res = json_decode( $responseRequest->getBody()->getContents(), true );
            if ( isset( $res['expenses'][0]['id'] ) ) {
                echo '<h2>'.$descriptionToExpense.'</h2>';
                echo '<h1>Thank you, Your purchase has been processed successfully.</h1>';
                $order->payment_complete();
                wc_reduce_stock_levels( $order_id );
                $woocommerce->cart->empty_cart();
            }
            // Convert the response to array and display it.
        } elseif ( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier']) ) {

            // Get back the previosuly generated temporary credentials (step 1).
            // if(isset($_SESSION['temporaryCredentials'])){
            $temporaryCredentials = unserialize( $_SESSION['temporary_credentials'] );
            unset( $_SESSION['temporary_credentials'] );

            // STEP 3: Obtain the token credentials (also known as access token).
            // echo 'step 3 <br>';
            $tokenCredentials = $oauth1->requestTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

            // Store the token credentials in session for later use.
            // echo 'token_credentials have';
            $_SESSION['token_credentials'] = serialize($tokenCredentials);
            // this basically just redirecting to the current page so that the query string is removed.
            header('Location: ' . (string) $oauth1->getConfig()->getCallbackUri());
            exit();
        }


}
add_shortcode( 'splitwise-checkout', 'custom_checkout' );


add_action( 'admin_menu', 'add_admin_page' );
function add_admin_page() {
    add_menu_page('Splitwise Checkout Settings', 'Splitwise Checkout Settings', 'read', 'configuration_page', 'configuration_page','dashicons-admin-settings');
}

function configuration_page() {

    if( isset( $_POST['client_credentials_identifier'] ) && isset( $_POST['client_credentials_secret'] ) && isset( $_POST['callback_uri'] ) ) {

        if ( update_option( 'client_credentials_identifier', $_POST['client_credentials_identifier'] ) || update_option( 'client_credentials_secret', $_POST['client_credentials_secret'] ) || update_option( 'callback_uri', $_POST['callback_uri'] ))
            echo '<h1>Credentials Saved</h1>';

    } else {
        ?>
        <h2>Please fill the setting data form</h2>
        <a href="https://secure.splitwise.com/oauth_clients">Get data from splitwise</a><hr>
        <form action="<?php echo get_site_url().'/wp-admin/admin.php?page=configuration_page';?>" method="POST">
            <label for="">Consumer Key</label>: 
            <input type="text" name="client_credentials_identifier" value="<?php echo get_option( 'client_credentials_identifier' ) ?>"><br>

            <label for="">Consumer Secret</label>: 
            <input type="text" name="client_credentials_secret" value="<?php echo get_option( 'client_credentials_secret' ) ?>"><br>

            <label for="">Callback URL</label>: 
            <input type="text" name="callback_uri" value="<?php echo get_option( 'callback_uri' ) ?>"><br>
            <input type="submit" value="Save">
        </form>
        <?php
    }
}
?>