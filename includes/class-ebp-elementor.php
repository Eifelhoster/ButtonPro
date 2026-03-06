<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the "Eifelhoster" category and the eh-buttonpro widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register',               array( $this, 'register_widget' ) );
	}

	/**
	 * Register the "Eifelhoster" element category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => 'Eifelhoster',
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register the EH ButtonPro widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public function register_widget( $widgets_manager ) {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}
}
