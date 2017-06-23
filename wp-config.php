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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'b2568aed6435c1f6c529b604bdd5f16e40a142a60f13a4c7');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'xBK2i%L?Ri.q;m2E;>ykP7_*bI?Z&{ajzK&kY3p^AjLDIs2yeeF~42v 3~C`_2rC');
define('SECURE_AUTH_KEY',  'Z&bxTpH=?k^1{~bh]N)rJojn~J~I :s&H7k:o]<xQ{N61-AD{$]?puV*j5M->@ap');
define('LOGGED_IN_KEY',    'y#eS[<[Q?0,?kZ$5*Z=Y)f7B.CZ(?z2l:lq{J5=*Tz %MHh`kH=(,W/;rtKw,rZ%');
define('NONCE_KEY',        ']-`ZOx4%Vj$*L#23ROtS74ehQ#LAN)!qW!A,77Ftp3/]V#EL${t|_!?TSH~+a4_)');
define('AUTH_SALT',        'a{2TjA^9Vyk]^n-m)uHB}d_KqWv%JW;x,#_ErYs*r,te@zwGB9_?z*ue$e Za)-l');
define('SECURE_AUTH_SALT', 'Fo!p+-{vs[0x?<wk|m=_<,~7LM^v.P/7AQG[_Km0CFJ$Iylm[w8k*0p<>?{{~`)T');
define('LOGGED_IN_SALT',   'MRAI.V_0oiSH`>PEgr_8;@8D`2u,FI:lvLJg>0<i5m5uMCMH~Z,2ckTQ,?}igtgG');
define('NONCE_SALT',       '&[)9(hL`0LVjKJv>PW[tn<4V.][V9{l-3#~Zml^t8aen-HmQG33$[zbml;V%zycf');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
