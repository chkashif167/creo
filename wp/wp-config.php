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
define('DB_NAME', 'creo_db3');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'EU-B]woUW{j ,Lv359WwLnvHTN7-9aI|%(|fwD+0_rU663]rk?4{9e@(ibG:N|/Q');
define('SECURE_AUTH_KEY',  '4I8G%kh3hG?WGcb IIcT=d+UmBi/jKUd=x/R!EuG|A7Fo^D_S!^L-RI;Ce1Ir~i&');
define('LOGGED_IN_KEY',    'Ny%+A6^)(_J82-(1([8|sW(Hrrpj,2Nhq24Ez;E h/G7_TYHjsXJ-0$15XKEEG-:');
define('NONCE_KEY',        ')Yiha*tN`nAsocYh;_6.|zI5?O1DU@|94V90W[ :x(0PUx}P##<J//9z]Ewnr#;M');
define('AUTH_SALT',        'l~?<J2?TW;-Z}QZ=),4-Z,=lV+#)Oa|?1k-yS;ECe)Wh.^%n^X9y^y8ee2<J=|h|');
define('SECURE_AUTH_SALT', '(K3ypRRr/0O#;#G!0TDW4E+3P~-VA`-H.DjE4kYs|/Xum*@LYfkE{PXnr3?J+6gN');
define('LOGGED_IN_SALT',   ')C++?_bX^uRRcPH}kgDj(uX%(Q>4s%x;aUU^jP:^fb^2A/%_n(pRqZD!5m>D(*cm');
define('NONCE_SALT',       'NL8j=Y4TFY(%m bHba`?,$~o cn?^Y~XhY]Grh%j>o(T+o}fLV,<#fN5G/OJtZwS');

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
