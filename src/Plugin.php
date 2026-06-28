<?php
/**
 * Bootstrap do plugin.
 *
 * @package Sidpeql
 */

namespace Sidpeql;

use Sidpeql\Editor\Editor;
use Sidpeql\Frontend\Query_Filter;
use Sidpeql\Frontend\Render_Tracker;

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
		if ( \is_admin() ) {
			Editor::register();
		}

		Query_Filter::register();
		Render_Tracker::register();
	}
}
