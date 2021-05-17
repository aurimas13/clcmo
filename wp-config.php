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
define( 'DB_NAME', 'wp_clcmo' );

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
define( 'AUTH_KEY',         'j*fhsy}yQTCBpA}(*Bnv:ss9b#w;AuCxxW4UQ]o/,.(H`no:hL$hPb>wan [SfQQ' );
define( 'SECURE_AUTH_KEY',  '$]+I8e&odskL7]}_=LIJ#i&~kQH}Nojcl++rGKYy$Soq-Zlh$`hv|?hZ^VG6d)<S' );
define( 'LOGGED_IN_KEY',    '`M{ysg #IO)N`ANa&Os+f=yrO2qd1i6:s=Q+10zq&L{|OHmyW;|9HK~RE9[vYa0U' );
define( 'NONCE_KEY',        'TW8jcYfTS?F BnAgf9|65Hu;6ueEl<p?u]OsD/<[m)5*9ZeIb|uCs]c`-R&.%]%y' );
define( 'AUTH_SALT',        'OMniA3/=AU.lmtN@Ef [2P![$g7(hO}/Q=i@s4MKjd:3F~>tlQ@n8vbFQ9cgg#;K' );
define( 'SECURE_AUTH_SALT', '?)W oJ5cRZ!}r`=f-bt-Z*R;WmoUlq9BIR7-yMXbsAWjmBu)l/:]l${}qnUwPN^>' );
define( 'LOGGED_IN_SALT',   'vC,nf!pw]B+PS;T{YabO^PO={~;KmE<K(LTR^r74we~u&9gGL%fKVlg~;_*Il4Ds' );
define( 'NONCE_SALT',       'E/~6E}Gl0@<)f,5`0vsoaiZX=KPI>@Nm@ dW{ M=l}j#}ad(?u@52AXmbDFGki5F' );

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
