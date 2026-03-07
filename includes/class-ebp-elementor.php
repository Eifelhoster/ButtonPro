<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the custom Elementor widget and the "Eifelhoster" widget category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	public function init() {
		add_action( 'elementor/widgets/register',        array( $this, 'register_widget' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
	}

	/** Register a custom Elementor widget category. */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => __( 'Eifelhoster', 'eifelhoster-buttons-pro' ),
				'icon'  => 'eicon-button',
			)
		);
	}

	/** Register the widget class. */
	public function register_widget( $widgets_manager ) {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}
}
