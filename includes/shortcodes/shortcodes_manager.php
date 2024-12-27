<?php
/**
 * This file contains main initialization code for shortcodes.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes
 */

namespace SMP\Shortcodes;

defined( 'ABSPATH' ) or exit;

/**
 * Define the class.
 */
class Shortcodes_Manager {

	/**
	 * Constructor.
	 *
	 * We might remove this in future and leave only the individual controls, so this class can act as an actual
	 * manager.
	 */
	public function __construct() {
		$this->init_wordpress();
	}


	/**
	 * Adds WordPress shortcodes.
	 *
	 * @since 2.0.4
	 */
	public function init_wordpress() {
		include_once __DIR__ . '/wordpress/wp_shortcode.php';
		include_once __DIR__ . '/wordpress/wp_archive.php';
		include_once __DIR__ . '/wordpress/wp_taxonomy.php';
		include_once __DIR__ . '/wordpress/functions.php';

		add_shortcode(
			'smpro_archive',
			function ( $atts ) {
				if ( is_admin() ) {
					return '';
				}

				$shortcode = new WP_Archive( (array) $atts );

				if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
					define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
				}

				return $shortcode->get_content();
			}
		);

		add_shortcode(
			'smpro_tax',
			function ( $atts ) {
				if ( is_admin() ) {
					return '';
				}

				$shortcode = new WP_Taxonomy( (array) $atts );

				if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
					define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
				}

				return $shortcode->get_content();
			}
		);
	}


}
