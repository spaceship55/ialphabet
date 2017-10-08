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
define('DB_NAME', 'ialphabet');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '?o;<<]&nTzLCrtN>U{g]WPrgh~%%r.pzg.]0]xbvm0< .r6E}] 6&y_5q@wVTfs>');
define('SECURE_AUTH_KEY',  'H1}Ri6?32DnxoezkGSzU~0KOnK%i/+l_A@EHbZcgmotxX1VsJ|<%Nuj9}G;}|U0q');
define('LOGGED_IN_KEY',    'RckIdcP+/.y}}]VgV#)Isj7$mCQL1%7}|8fC5MAz./3U*UA8S^Q=/jEqLdQaK$=:');
define('NONCE_KEY',        '+=-@9dfzG}XYUP`s(S6aclO69Jg4N`UVD(Y1b3f~v3obF/u|Pak>F326[K9j=;#|');
define('AUTH_SALT',        '+RT+w/@C+XlGlI?Jo,l3odAc0vLJUAVjDko@W!.>Di0zOYTbe05=o>zX++r%!^Z*');
define('SECURE_AUTH_SALT', 'Ord_&ytEQH4@x=:W^|qVXCQ|W!!Szh.~9gcU,^vo0!ClN&^V;j1}V+/yT0,:&<fJ');
define('LOGGED_IN_SALT',   'p/7JqNc3Fb=gx(zB3@t-,+$&z(`F`298<_m/<YnqQz5wZNDZJ:3nf6L]11VGVNi6');
define('NONCE_SALT',       '*}`{JdDob_tY~{^@j(c#tbbgBC4a28yolN&w;V/]ZaQB`DQb5;7 Q+L$V9wbYMwg');

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
