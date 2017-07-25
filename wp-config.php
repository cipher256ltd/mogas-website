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
define('DB_NAME', 'mogasmam_db');

/** MySQL database username */
define('DB_USER', 'mogasmam_new');

/** MySQL database password */
define('DB_PASSWORD', 'cipher256');

/** MySQL hostname */
define('DB_HOST', '192.185.166.14');

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
define('AUTH_KEY',         '88B9|[;/x}ty[eIW[8t7m&3Fy/NEz%f`EQK_/($8|nU$K&?|ok]MsUJM;~=%OfZ$');
define('SECURE_AUTH_KEY',  'z`O.96QDg_Sq5x|{IQ-)_>&=+ pwO?8:U1uF`DQ%h9`f9=uf`vb&ZQ1JJRR5[{f>');
define('LOGGED_IN_KEY',    '0}X^Fw6w<DQ`39=5AK_-_e/!RzCA2Z85dKhnvaBYcz?s;fN}/)>W]l~{[43J?U9(');
define('NONCE_KEY',        '-t_!y^$}=?wsZbJ!b3GW_~s@ioIeO3h|uF(^7vMobu&~LaZ-rQsI#xR(k59uDJV=');
define('AUTH_SALT',        '?,Jm$z.psZeATbO+ ?&Q]ZwJHPWN%JP5L&e,]%VAMV{]FWp-mqO0$S6,kPeC,iN$');
define('SECURE_AUTH_SALT', 'OTU6!zdPg]7mtzPF50aKgI*/$.^!i)^T=g){x^;b}M.B6@MOVbq)p/5Md`o?M>ba');
define('LOGGED_IN_SALT',   'NDBkEN-::%&#,tdS!gsIJ->@V^*a(W<5>Pc7IXdktw$tmxs`WF?NZQyp$n[.-__-');
define('NONCE_SALT',       'P3ov1{R.9VAZSzGIJ:Z3n(Y@c|JNLK(wc)OxPY7*M_},,d4Q0C{!L- SWN+:cp/Q');

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
