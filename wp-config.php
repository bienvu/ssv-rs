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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'default' );

/** MySQL database username */
define( 'DB_USER', 'user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'user' );

/** MySQL hostname */
define( 'DB_HOST', 'db' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_HOME', 'http://riseandshine.docksal' );
define( 'WP_SITEURL', 'http://riseandshine.docksal' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'l(fj_;Szqa[i%vm#NxUS}1-@Z[eR}fSEO4}|=QXYE|5%APvD~:{b!$PjAmagPBPn' );
define( 'SECURE_AUTH_KEY',  'C1wQvb*R70Bs06E;@?^2Fb#(_5Uongpv}LBiDP6U,tHJR<Nzv7jhX>=NFR}p$M^@' );
define( 'LOGGED_IN_KEY',    'yf*bsl.?Ku_s>c@ie.W)b.DGYOn/wfIL(yIKk,;Anu|ocMLDyG^K`t_Q(>vHA~*s' );
define( 'NONCE_KEY',        '~Q-Ec-v1G>CntX{_%[iW:zN29EIdAqz_7qSs(QCn%IG4o_O4_^bx D_IFQt3Ck>@' );
define( 'AUTH_SALT',        'UIqc}up,(vWfe[&%eDzU1OPprOh{[3j(!,s^?(jqss@!1yg<D;U_Xsrd@nD5t-}a' );
define( 'SECURE_AUTH_SALT', 'JhR6v2P_M;oABV?=J`.RFZKM6LIsvMOdq/6sq0lAgT:kF(KWH<K*;FL4iI1$ Dmq' );
define( 'LOGGED_IN_SALT',   '-H<3(hm[bOyjnWM}%%mx!IywDbEKhAjl.bg3NRhA>9l)Hm2.szXpj:`jD:24mC7f' );
define( 'NONCE_SALT',       'pA2kK#4%MKj 4$zUD-Bm,l*GT[1(Csq/oD0)qcxo4.e$o@Lk^:!v3i(Q93]BO{bW' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
