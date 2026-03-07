<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the EBP_Elementor_Widget when Elementor is active.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_category' ) );
	}

	/** Register the widget. */
	public function register_widgets( $widgets_manager ) {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}

	/** Add a custom "Eifelhoster" category in the Elementor panel. */
	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => 'Eifelhoster',
				'icon'  => 'fa fa-plug',
			)
		);
	}
}
