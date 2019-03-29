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

define('WP_DEBUG', true);

define( 'WP_DEBUG_LOG', true );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'gothamqu_wordpress' );

/** MySQL database username */
define( 'DB_USER', 'gothamqu_wp' );

/** MySQL database password */
define( 'DB_PASSWORD', 'ChinUpLady1' );

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
define( 'AUTH_KEY',         '*Rolgoi?!5kMEpqx: @Hq0!y-M:71c}S}L~l?dD.t+d,#HH{]};>dE9yEv:qGo#X' );
define( 'SECURE_AUTH_KEY',  'C`KeRr`#q=ntZts%pgvsGVVpU)w-+wKKZ[D`s&aKmso@-)9UbL{*.zL^iu5IbTU9' );
define( 'LOGGED_IN_KEY',    '(V&d`O;euY ;;9.1eRBY^mm{:dwS.!8kf,jj<K.dx%h`Dz2[}2$oGrV793/Z4l<x' );
define( 'NONCE_KEY',        '!35unR_Z_D3c$*RPJU&Z4A2;iQ.ZW%7(!m6u1EWGtO1&qxB%&{.#MK~3)>+OQLX)' );
define( 'AUTH_SALT',        '<p(|4#yBVs7Bw8jGL-:#&:;6d!azWy7K/eq(*cmSmwi|ggj2Kt=:TM8 j0Ub&PDm' );
define( 'SECURE_AUTH_SALT', '_9Bu:n2t_:CD%fJUHE%Kpb!yuzhh@z:g5dLo!%SXckJ[Wz){LBj6JWNgT!EWo=Rf' );
define( 'LOGGED_IN_SALT',   'kq]*5l.Q$7+gw6Z>153Y0NKgM?M@E)2/6ka%9KtW_s@W$lJVO=IOgM`:#Fhz:^=C' );
define( 'NONCE_SALT',       's;<|:H<c-7#0WY0JiXP{cKDz+X6>ev4U6:8,JH<tduzhXiw~~[Xlb8aRJ.7=bkn?' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
