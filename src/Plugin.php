<?php
/**
 * Bootstrap do plugin.
 *
 * @package DuplicatePostExclusionForQueryLoop
 */

namespace DuplicatePostExclusionForQueryLoop;

use DuplicatePostExclusionForQueryLoop\Editor\Editor;
use DuplicatePostExclusionForQueryLoop\Frontend\Query_Filter;
use DuplicatePostExclusionForQueryLoop\Frontend\Render_Tracker;

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
