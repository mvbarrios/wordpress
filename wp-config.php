<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testapplaudo' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'U(H;7If}]N_[KXsEFxWUD}{XntYbyoO h8,!BrlbBOGkd^y,%idp7#puh| i^8w>' );
define( 'SECURE_AUTH_KEY',  'Hsr>S#M8}j!M{6brJLqJG|qmvZlg<(0y6KA0<bi##/-L5*PGK< 2[P_:):viheVJ' );
define( 'LOGGED_IN_KEY',    '8y~^n#a!$@JsosbyF-{a(b.*e@2}zyveZS-imxH5`{ep-mJ/*Jfw)a<d)c6[$![G' );
define( 'NONCE_KEY',        '`J/ba>TVh|=WW:@U0H$$&>oUs.DavVy4qh{~=A(G. vD%joXZuQG(* uunp3z[lm' );
define( 'AUTH_SALT',        'w/q#PKE}^0j,w1|Drg`V:K)2c|CoXhW&?1=k=ak=*[2K4XMpOmoXzmJr;VjetvzF' );
define( 'SECURE_AUTH_SALT', 'HfiCAzS:R(z>OsNoFe;%b&*}lg!8gZL9B$5XC,:I^+Iq0d i]o+cljtx`4a.0h>d' );
define( 'LOGGED_IN_SALT',   'fa*oXrvKx6~#uf@VcXG^/8G*I}/Fh?]viELAN+ZstM&Jn[2<L4@@4Qmr2.L*WR]R' );
define( 'NONCE_SALT',       'XHq$Eb%12oB9Nk;K%/ _lqt&SG_EVkDL=cKyM&43oeE%)Hs9-y~$>KZTQWoNW#&5' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpix_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
