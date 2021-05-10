<?php

/**  
 * Remove sidebar of product pages 
 */

add_action( 'get_header', 'remove_storefront_sidebar' );

function remove_storefront_sidebar() {

     if ( is_product() ) {
       remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
    }

}

/**
 * Remove Breadcrumbs
 */
add_action( 'init', 'remove_storefront_breadcrumbs' );

function remove_storefront_breadcrumbs() {
   remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
}

/**
 * Disable the Search Box in the Storefront Theme
 */
add_action( 'init', 'remove_storefront_search' );

function remove_storefront_search() {
   remove_action( 'storefront_header', 'storefront_product_search', 40 );
}

/**
 * Show extra data in a data tab 
 */
add_filter( 'woocommerce_product_tabs', 'new_product_tab' );
 
function new_product_tab( $tabs ) {
   $tabs['test_tab'] = array (
      'title' => __( 'Technical Details', 'woocommerce' ),
      'priority'=> 15,
      'callback'=>'technical_tab_content'
   );
   return $tabs;
}

add_action( 'init', 'custom_remove_footer_credit', 10 );
function custom_remove_footer_credit () {
   remove_action( 'storefront_footer', 'storefront_credit', 20 );
   add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
}

function custom_storefront_credit() {
?>
   <div class="site-info">
      &copy; <?php echo get_bloginfo( 'name' ) . ' ' . get_the_date( 'Y' ); ?><br>
      Made with love by <a href="https://mvbarrios.com" title="Mvbarrios" rel="noreferrer" target="_blank"> mvbarrios </a> using <a href="https://woocommerce.com" target="_blank" title="WooCommerce - The Best eCommerce Platform for WordPress" rel="noreferrer"> Storefront &amp; WooCommerce</a>
   </div><!-- .site-info -->
<?php
}

 /**
 * Show extra data in tables, each category has its own table with information
 */
function technical_tab_content() {
   if ( has_term( 'printers', 'product_cat' ) ) {
      ?>
      <table class="has-background">
         <tr>
             <td class="table-header">Print Resolution</td>
             <td class="table-information"><?php the_field('print_resolution');?></td>
         </tr>
         <tr>
             <td class="table-header"> Monthly Print Volume</td>
             <td class="table-information"><?php the_field('monthly_print_volume');?></td>
         </tr>
         <tr>
             <td class="table-header">Paper Capacity</td>
             <td class="table-information"><?php the_field('paper_capacity');?></td>
         </tr>
         <tr>
             <td class="table-header">Processor</td>
             <td class="table-information"><?php the_field('processor');?></td>
         </tr>
     </table><!-- table for printers-->
     <?php
   } elseif ( has_term( 'monitors', 'product_cat' ) ) {
      ?>
      <table class="has-background">
         <tr>
            <td class="table-header">Standing screen display size</td>
            <td class="table-information"><?php the_field('standing_screen_display_size');?></td>
         </tr>
         <tr>
            <td class="table-header"> Screen Resolution</td>
            <td class="table-information"><?php the_field('screen_resolution');?></td>
         </tr>
         <tr>
            <td class="table-header">Max Screen Resolution</td>
            <td class="table-information"><?php the_field('max_screen_resolution');?></td>
         </tr>
         <tr>
            <td class="table-header">Color</td>
            <td class="table-information"><?php the_field('color');?></td>
         </tr>
      </table><!-- table for monitors-->
      <?php
   } elseif ( has_term( 'computers', 'product_cat' ) || has_term( 'laptops', 'product_cat' ) ) {
      ?>
      <table class="has-background">
         <tr>
            <td class="table-header">Processor</td>
            <td class="table-information"><?php the_field('processor');?></td>
         </tr>
         <tr>
            <td class="table-header"> RAM</td>
            <td class="table-information"><?php the_field('ram');?></td>
         </tr>
         <tr>
            <td class="table-header">Memory Speed</td>
            <td class="table-information"><?php the_field('memory_speed');?></td>
         </tr>
         <tr>
            <td class="table-header">Hard Drive</td>
            <td class="table-information"><?php the_field('hard_drive');?></td>
         </tr>
         <tr>
            <td class="table-header">Graphics Coprocessor</td>
            <td class="table-information"><?php the_field('graphics_coprocessor');?></td>
         </tr>
         <tr>
            <td class="table-header">Chipset Brand</td>
            <td class="table-information"><?php the_field('chipset_brand');?></td>
         </tr>
         <tr>
            <td class="table-header">Card Description</td>
            <td class="table-information"><?php the_field('card_description');?></td>
         </tr>
         <tr>
            <td class="table-header">Graphics Card Ram Size</td>
            <td class="table-information"><?php the_field('graphics_card_ram_size');?></td>
         </tr>
         <tr>
            <td class="table-header">Wireless Type</td>
            <td class="table-information"><?php the_field('wireless_type');?></td>
         </tr>
         <tr>
            <td class="table-header">Number of USB 3.0 Ports</td>
            <td class="table-information"><?php the_field('number_of_usb_30_ports');?></td>
         </tr>
         <tr>
            <td class="table-header">Brand</td>
            <td class="table-information"><?php the_field('brand');?></td>
         </tr>
         <?php if ( has_term( 'laptops', 'product_cat' ) ){?>
         <tr>
            <td class="table-header">Average Battery Life (in hours)</td>
            <td class="table-information"><?php the_field('average_battery_life_in_hours');?></td>
         </tr>
         <?php }?>
      </table><!-- table for laptops and computers-->
   <?php
   }
} 

 /**
 * Add button for pay with splitwise
 */
add_action( 'woocommerce_review_order_after_submit', 'bbloomer_privacy_message_below_checkout_button' );
 
function bbloomer_privacy_message_below_checkout_button() {
   ?>
   <a href="/checkout-page/"  class="button psplit" rel="nofollow">Pay with Splitwise</a>
<?php
}
