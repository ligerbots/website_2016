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

/** 
  File containing secret items, so these do not get checked into Git 
 **/
if ( file_exists( '/etc/private/wp-secrets.php' ) ) include( '/etc/private/wp-secrets.php' );
else include( 'wp-config-dummy.php' ); // assume we're not in production

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** Paul Rensing: don't need the online cron system */
define('DISABLE_WP_CRON', true);

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wdprs_';

// Set SITECOOKIEPATH so that login cookies are set into top directory.
//  COOKIEPATH does not seem to be needed, so leave alone.
define( 'COOKIEPATH', '/' );
//define( 'SITECOOKIEPATH', '/' );

define('AUTOSAVE_INTERVAL', 300); // seconds
define('WP_POST_REVISIONS', 5);

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
