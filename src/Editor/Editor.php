<?php
/**
 * Assets do editor de blocos.
 *
 * @package UniqueQueryLoopExtension
 */

namespace UniqueQueryLoopExtension\Editor;

defined( 'ABSPATH' ) || exit;

/**
 * Enfileira o script que estende o bloco core/query no editor.
 */
class Editor {

	/**
	 * Registra hooks do editor.
	 *
	 * @return void
	 */
	public static function register(): void {
		\add_action( 'enqueue_block_editor_assets', array( self::class, 'enqueue_assets' ) );
	}

	/**
	 * Enfileira script compilado do editor.
	 *
	 * @return void
	 */
	public static function enqueue_assets(): void {
		$asset_file = UQLE_PLUGIN_DIR . 'build/index.asset.php';
		$asset      = is_readable( $asset_file )
			? require $asset_file
			: array(
				'dependencies' => array(),
				'version'      => UQLE_VERSION,
			);

		\wp_enqueue_script(
			'unique-query-loop-extension-editor',
			UQLE_PLUGIN_URL . 'build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		\wp_set_script_translations(
			'unique-query-loop-extension-editor',
			'unique-query-loop-extension',
			UQLE_PLUGIN_DIR . 'languages'
		);
	}
}
