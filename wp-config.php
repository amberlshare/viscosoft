<?php
define('WP_CACHE', true); // Added by WP Rocket

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'gdungan_viscosoft_dev');

/** MySQL database username */
define('DB_USER', 'gdungan_dev');

/** MySQL database password */
define('DB_PASSWORD', '(U?mv$7bAnrw');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '>$U.p%GQy(Z|/d{eJJdkQDAFHF[Dm?r-3USZf7^#?e7 [OwWx/eRbnU,<~k|OP{n');
define('SECURE_AUTH_KEY',  '&7KM+qst-cnl/HEgXQ,nl8Uy?qBY~j&xdja!E09+-^V$L+:](3q=EPV|ZJ.,Uedx');
define('LOGGED_IN_KEY',    ',S1o#w</+He-xw%^iQ1v8M!YA 8_#U_ClAk8c$DYD]~SA60U1q2SDK{CWx8[^/_P');
define('NONCE_KEY',        'gh*eo]L)*+=r*sX0+Df*i^3Iw3v]KJ:*pDdU<kdCteM-5%zOj9>stgmKbP)?`,1V');
define('AUTH_SALT',        'r=v#ek~]DUnMf!k6/nFm1+>)E7j@I*^*JAB%Wf7~~8r@/x&45_uT$8cRi+Izwx6H');
define('SECURE_AUTH_SALT', '++_tl#/[9[pzf)6wxS7FT:-z51J2rh)tT,e9b g8I:Z1>]x)OC|8EAb8in*}+By*');
define('LOGGED_IN_SALT',   '+uhl)pf.!L=097 r)lQ=[,~x=o*zo+QkC.ID{nYwJ/6~9t5f|Ahgc|oJ(GrBZ/a3');
define('NONCE_SALT',       'vUFH6uk/F^A)hqK-eDp2 n+N}Qq`0?lc$8Z?uvxH-_MUcYoj=|B.+<[>(Q(Vf<|w');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
