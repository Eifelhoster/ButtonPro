<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the widget once Elementor is fully loaded.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	public function init() {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
	}

	/** Register the custom Eifelhoster widget category. */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => __( 'Eifelhoster', 'eifelhoster-buttons-pro' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/** Register the button widget. */
	public function register_widgets( $widgets_manager ) {
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}
}
