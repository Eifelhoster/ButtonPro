<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the "Eifelhoster" widget category and loads the widget class
 * once Elementor is initialised.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	public function init() {
		// Register the "Eifelhoster" widget category.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

		// Register the widget.
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}

	/** Add the "Eifelhoster" category to the Elementor widget panel. */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => __( 'Eifelhoster', 'eifelhoster-buttons-pro' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/** Register the eh-button-pro widget. */
	public function register_widget( $widgets_manager ) {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}
}
