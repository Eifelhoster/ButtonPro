<?php
/**
 * Elementor integration loader for Eifelhoster Buttons Pro.
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
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	public function enqueue_editor_scripts() {
		wp_enqueue_script(
			'ebp-elementor-editor',
			EBP_PLUGIN_URL . 'assets/js/ebp-elementor-editor.js',
			array( 'jquery' ),
			EBP_VERSION,
			true
		);
		wp_localize_script(
			'ebp-elementor-editor',
			'ebpElementorData',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ebp_search_content' ),
			)
		);
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => 'Eifelhoster',
				'icon'  => 'fa fa-plug',
			)
		);
	}

	public function register_widget( $widgets_manager ) {
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}
}

new EBP_Elementor();
