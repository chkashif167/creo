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
define('AUTH_KEY',         '!jlNBm<A%JaOs%B-}_{F DY=.6<:*2,_&RDRrbHje *?J)TM7*S)}KY,;E!w)NS,');
define('SECURE_AUTH_KEY',  'FG>,Ei]|A}M]CfYn:THX,).6w8~5qW8V5cy+k]Vl|S(Fm.[?)1vm^sEmf9W@$W!;');
define('LOGGED_IN_KEY',    'bM=[{/WT;_7Y<7Y6D~O}f*w`i?6G7:fdk?cF-%?+C;(pU;A+7@p:-f+ru,j$5U,W');
define('NONCE_KEY',        ':6^+t>[UYITK|fwxqw&M{)`E#$>c8kP14b@5S`Q,N+K9gsAgOAIZ1yl}acFo0V^=');
define('AUTH_SALT',        ']yl<Z|=:.]seW(7ZvuC;e^RzG-p+/VMc4DE , ONH)Hv1q+(^Mruwz?KDB_VSq86');
define('SECURE_AUTH_SALT', 'jG1N0F+rqNp,U|{L}$/s 5$2iKC3Q{jTjnCkS#C$*}-kw2zQY;G;),B,?Q*}X+h_');
define('LOGGED_IN_SALT',   '?qE4YWJSm4<y{|oyCg+]|zu6DSSi!09:9J|.s|EHk7agil~bO0N# V(f_DlFV+b1');
define('NONCE_SALT',       'FIxEfIB3+,E0(WlK3{6.5Ln+Q{Bj.y0uifd(IydJ:U++#HWc;8^j=Nlc*f*Xi#Cs');

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
