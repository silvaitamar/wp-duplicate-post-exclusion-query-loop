<?php
/**
 * Plugin Name:       Duplicate Post Exclusion for Query Loop Block
 * Plugin URI:        https://github.com/silvaitamar/wp-duplicate-post-exclusion-query-loop
 * Description:       Prevent duplicate posts across multiple Query Loop blocks on the same page.
 * Version:           1.0.2
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Itamar Silva
 * Author URI:        https://github.com/silvaitamar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       silvaitamar-duplicate-post-exclusion-query-loop
 * Domain Path:       /languages
 *
 * @package Sidpeql
 */

defined( 'ABSPATH' ) || exit;

define( 'SIDPEQL_VERSION', '1.0.2' );
define( 'SIDPEQL_PLUGIN_FILE', __FILE__ );
define( 'SIDPEQL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SIDPEQL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

$sidpeql_autoloader = SIDPEQL_PLUGIN_DIR . 'vendor/autoload.php';

if ( is_readable( $sidpeql_autoloader ) ) {
	require_once $sidpeql_autoloader;
} else {
	spl_autoload_register(
		static function ( $class_name ) {
			$prefix   = 'Sidpeql\\';
			$base_dir = SIDPEQL_PLUGIN_DIR . 'src/';

			if ( 0 !== strpos( $class_name, $prefix ) ) {
				return;
			}

			$relative = substr( $class_name, strlen( $prefix ) );
			$file     = $base_dir . str_replace( '\\', '/', $relative ) . '.php';

			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	);
}

\Sidpeql\Plugin::init();
