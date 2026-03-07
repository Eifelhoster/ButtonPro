<?php
/**
 * Elementor integration for Eifelhoster Buttons Pro.
 *
 * Registers the Eifelhoster category and loads the widget
 * only when Elementor is active.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor {

	public function __construct() {
		add_action( 'elementor/init', array( $this, 'on_elementor_init' ) );
	}

	public function on_elementor_init() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register',               array( $this, 'register_widgets' ) );
		add_action( 'elementor/editor/after_enqueue_scripts',   array( $this, 'editor_scripts' ) );
	}

	/** Register the "Eifelhoster" widget category. */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'eifelhoster',
			array(
				'title' => __( 'Eifelhoster', 'eifelhoster-buttons-pro' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/** Register the ButtonPro widget. */
	public function register_widgets( $widgets_manager ) {
		require_once EBP_PLUGIN_DIR . 'includes/class-ebp-elementor-widget.php';
		$widgets_manager->register( new EBP_Elementor_Widget() );
	}

	/** Enqueue the content-search autocomplete script for the editor. */
	public function editor_scripts() {
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_script(
			'ebp-elementor-editor',
			EBP_PLUGIN_URL . 'assets/js/ebp-elementor-editor.js',
			array( 'jquery' ),
			EBP_VERSION,
			true
		);

		wp_localize_script( 'ebp-elementor-editor', 'ebpElementorData', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'ebp_search_content' ),
			'i18n'    => array(
				'searching' => __( 'Suche…', 'eifelhoster-buttons-pro' ),
				'noResults' => __( 'Keine Ergebnisse gefunden.', 'eifelhoster-buttons-pro' ),
				'selected'  => __( 'Ausgewählt:', 'eifelhoster-buttons-pro' ),
			),
		) );
	}
}
