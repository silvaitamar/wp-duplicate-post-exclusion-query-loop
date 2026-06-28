<?php
/**
 * Registro em memória dos IDs de posts já exibidos na requisição atual.
 *
 * @package Sidpeql
 */

namespace Sidpeql\Registry;

defined( 'ABSPATH' ) || exit;

/**
 * Armazena IDs renderizados apenas durante o ciclo da página atual.
 */
class Rendered_Posts_Registry {

	/**
	 * IDs de posts já exibidos por Query Loops com uniqueOnPage ativo.
	 *
	 * @var array<int, int>
	 */
	protected static array $post_ids = array();

	/**
	 * Profundidade de rastreamento ativo (suporta loops aninhados).
	 *
	 * @var int
	 */
	protected static int $tracking_depth = 0;

	/**
	 * Inicia o rastreamento de posts renderizados.
	 *
	 * @return void
	 */
	public static function begin_tracking(): void {
		++self::$tracking_depth;
	}

	/**
	 * Encerra o rastreamento de posts renderizados.
	 *
	 * @return void
	 */
	public static function end_tracking(): void {
		self::$tracking_depth = max( 0, self::$tracking_depth - 1 );
	}

	/**
	 * Indica se há um Query Loop com uniqueOnPage em renderização.
	 *
	 * @return bool
	 */
	public static function is_tracking(): bool {
		return self::$tracking_depth > 0;
	}

	/**
	 * Registra um post exibido no loop atual.
	 *
	 * @param int $post_id ID do post renderizado.
	 * @return void
	 */
	public static function register( int $post_id ): void {
		if ( $post_id <= 0 || ! self::is_tracking() ) {
			return;
		}

		self::$post_ids[ $post_id ] = $post_id;
	}

	/**
	 * Retorna todos os IDs registrados na requisição.
	 *
	 * @return int[]
	 */
	public static function get_ids(): array {
		return array_values( self::$post_ids );
	}
}
