<?php
/**
 * Plugin Name:       Silvaitamar Duplicate Post Exclusion for Query Loop
 * Plugin URI:        https://github.com/silvaitamar/wp-duplicate-post-exclusion-query-loop
 * Description:       Prevent duplicate posts across multiple Query Loop blocks on the same page.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Itamar Silva
 * Author URI:        https://github.com/silvaitamar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       silvaitamar-duplicate-post-exclusion-query-loop
 * Domain Path:       /languages
 *
 * @package DuplicatePostExclusionForQueryLoop
 */

defined( 'ABSPATH' ) || exit;

define( 'DPEQL_VERSION', '1.0.0' );
define( 'DPEQL_PLUGIN_FILE', __FILE__ );
define( 'DPEQL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPEQL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

$dpeql_autoloader = DPEQL_PLUGIN_DIR . 'vendor/autoload.php';

if ( is_readable( $dpeql_autoloader ) ) {
	require_once $dpeql_autoloader;
} else {
	spl_autoload_register(
		static function ( $class_name ) {
			$prefix   = 'DuplicatePostExclusionForQueryLoop\\';
			$base_dir = DPEQL_PLUGIN_DIR . 'src/';

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

\DuplicatePostExclusionForQueryLoop\Plugin::init();
