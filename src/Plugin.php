<?php
/**
 * Bootstrap do plugin.
 *
 * @package UniqueQueryLoopExtension
 */

namespace UniqueQueryLoopExtension;

use UniqueQueryLoopExtension\Editor\Editor;
use UniqueQueryLoopExtension\Frontend\Query_Filter;
use UniqueQueryLoopExtension\Frontend\Render_Tracker;

defined( 'ABSPATH' ) || exit;

/**
 * Classe principal do plugin.
 */
final class Plugin {

	/**
	 * Inicializa hooks do plugin.
	 *
	 * @return void
	 */
	public static function init(): void {
		\add_action( 'init', array( self::class, 'load_textdomain' ) );

		if ( \is_admin() ) {
			Editor::register();
		}

		Query_Filter::register();
		Render_Tracker::register();
	}

	/**
	 * Carrega traduções do plugin.
	 *
	 * @return void
	 */
	public static function load_textdomain(): void {
		\load_plugin_textdomain(
			'unique-query-loop-extension',
			false,
			\dirname( \plugin_basename( UQLE_PLUGIN_FILE ) ) . '/languages'
		);
	}
}
