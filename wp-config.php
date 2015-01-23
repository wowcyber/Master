<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
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
define('DB_NAME', 'test04dev');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '#2,ko;k,]kz9jlTdMw8g<7*QG}Oqb;A{HXi2zm%&[mx!#{] +TmC_X>ca[|;MY@k');
define('SECURE_AUTH_KEY',  'F/R 1{f(KuW6M/6m)n5xz!]m%6N+.|vDg IRl;<P>M|LlwNW~u:|J+-yAO+izYIM');
define('LOGGED_IN_KEY',    '6{|R9IF8ai*Nud!g+B l|5oAT(Uq[9/GP7>IWa)|= -~:`kqRb>Sy^:@Y+no+m^o');
define('NONCE_KEY',        'Wp/jg&]A$phRe{bQ2bE]aJ%fUxJ6+W+8<0v7w~^#eE|Tp/S{YhFFU$n#e`|*t{|p');
define('AUTH_SALT',        '-[G|M[s#$Ajx?H|G1xrM}#4cR>LKTf!Ay7;Y-C0n}mg0`}+Q0(c37&.-%)WXY:P1');
define('SECURE_AUTH_SALT', 'sM~{]=l=$o9_qRA0AB82yvOB%~xFOO*2/=KIS2-ez;-6L~,|3fo9JWEXkNCwNuzc');
define('LOGGED_IN_SALT',   'tjfJ5T#fSpuZD([*gS]^[18tQcgDk)^:<1Ht9eQGu`+WDhXZf$4UL5dvYl^4rR-r');
define('NONCE_SALT',       '8Dt?a ;|~&YYn?)2i0((LxF/>,|]a9RuM/rll;LDLYa7(#%w@qO:7G:vOUQ0@*y(');

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
