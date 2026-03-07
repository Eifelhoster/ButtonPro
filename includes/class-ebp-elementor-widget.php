<?php
/**
 * Elementor Widget for Eifelhoster Buttons Pro.
 *
 * Provides the same button configuration options as the Classic Editor dialog
 * inside the Elementor page builder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return __( 'EH Button Pro', 'eifelhoster-buttons-pro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'eifelhoster' );
	}

	public function get_keywords() {
		return array( 'button', 'btn', 'link', 'eifelhoster' );
	}

	/** Register widget controls (settings). */
	protected function register_controls() {
		$d = ebp_get_defaults();

		/* ── Section: Text & Size ── */
		$this->start_controls_section(
			'section_text',
			array( 'label' => __( 'Text & Größe', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control( 'text', array(
			'label'       => __( 'Button-Text', 'eifelhoster-buttons-pro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __( 'Button', 'eifelhoster-buttons-pro' ),
			'placeholder' => __( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ),
		) );

		$this->add_control( 'button_width', array(
			'label'       => __( 'Buttonbreite', 'eifelhoster-buttons-pro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $d['button_width'],
			'placeholder' => __( 'z.B. 200px oder 100% (leer = automatisch)', 'eifelhoster-buttons-pro' ),
			'description' => __( 'Leer lassen = Breite passt sich dem Inhalt an.', 'eifelhoster-buttons-pro' ),
		) );

		$this->add_control( 'font_family', array(
			'label'       => __( 'Schriftart', 'eifelhoster-buttons-pro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $d['font_family'],
			'placeholder' => 'inherit, Arial, Georgia, …',
		) );

		$this->add_control( 'font_size', array(
			'label'   => __( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['font_size'],
			'min'     => 8,
			'max'     => 120,
		) );

		$this->add_control( 'font_bold', array(
			'label'   => __( 'Fett', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => '1' === $d['font_bold'] ? 'yes' : '',
		) );

		$this->add_control( 'font_italic', array(
			'label'   => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => '1' === $d['font_italic'] ? 'yes' : '',
		) );

		$this->add_control( 'padding_v', array(
			'label'   => __( 'Innenabstand oben/unten (px)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['padding_v'],
			'min'     => 0,
			'max'     => 100,
		) );

		$this->add_control( 'padding_h', array(
			'label'   => __( 'Innenabstand links/rechts (px)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['padding_h'],
			'min'     => 0,
			'max'     => 200,
		) );

		$this->end_controls_section();

		/* ── Section: Colors ── */
		$this->start_controls_section(
			'section_colors',
			array( 'label' => __( 'Farben & Hover', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control( 'bg_color', array(
			'label'   => __( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $d['bg_color'],
		) );

		$this->add_control( 'bg_hover_color', array(
			'label'   => __( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $d['bg_hover_color'],
		) );

		$this->add_control( 'text_color', array(
			'label'   => __( 'Textfarbe', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $d['text_color'],
		) );

		$this->add_control( 'text_hover_color', array(
			'label'   => __( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $d['text_hover_color'],
		) );

		$this->add_control( 'hover_grow', array(
			'label'   => __( 'Grow bei Hover', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['hover_grow'],
			'min'     => 1,
			'max'     => 2,
			'step'    => 0.01,
			'description' => __( '1 = kein Grow, 1.05 = 5% größer', 'eifelhoster-buttons-pro' ),
		) );

		$this->end_controls_section();

		/* ── Section: Border & Shadow ── */
		$this->start_controls_section(
			'section_border',
			array( 'label' => __( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control( 'border_width', array(
			'label'   => __( 'Rahmenstärke (px)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['border_width'],
			'min'     => 0,
			'max'     => 20,
		) );

		$this->add_control( 'border_style', array(
			'label'   => __( 'Rahmenstil', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $d['border_style'],
			'options' => array(
				'solid'  => 'solid',
				'dashed' => 'dashed',
				'dotted' => 'dotted',
				'double' => 'double',
				'none'   => 'none',
			),
		) );

		$this->add_control( 'border_color', array(
			'label'   => __( 'Rahmenfarbe', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::COLOR,
			'default' => $d['border_color'],
		) );

		$this->add_control( 'border_radius', array(
			'label'   => __( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => $d['border_radius'],
			'min'     => 0,
			'max'     => 100,
		) );

		$this->add_control( 'shadow_enabled', array(
			'label'   => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => '1' === $d['shadow_enabled'] ? 'yes' : '',
		) );

		$this->add_control( 'shadow_x', array(
			'label'     => __( 'Schatten X-Offset', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => $d['shadow_x'],
			'condition' => array( 'shadow_enabled' => 'yes' ),
		) );

		$this->add_control( 'shadow_y', array(
			'label'     => __( 'Schatten Y-Offset', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => $d['shadow_y'],
			'condition' => array( 'shadow_enabled' => 'yes' ),
		) );

		$this->add_control( 'shadow_blur', array(
			'label'     => __( 'Schatten Unschärfe', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => $d['shadow_blur'],
			'min'       => 0,
			'condition' => array( 'shadow_enabled' => 'yes' ),
		) );

		$this->add_control( 'shadow_spread', array(
			'label'     => __( 'Schatten Ausbreitung', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::NUMBER,
			'default'   => $d['shadow_spread'],
			'condition' => array( 'shadow_enabled' => 'yes' ),
		) );

		$this->add_control( 'shadow_color', array(
			'label'       => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $d['shadow_color'],
			'placeholder' => 'rgba(0,0,0,0.3)',
			'condition'   => array( 'shadow_enabled' => 'yes' ),
		) );

		$this->end_controls_section();

		/* ── Section: Link ── */
		$this->start_controls_section(
			'section_link',
			array( 'label' => __( 'Link & Ziel', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control( 'link_type', array(
			'label'   => __( 'Linktyp', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $d['link_type'],
			'options' => array(
				'url'   => 'URL',
				'email' => 'E-Mail',
				'media' => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
			),
		) );

		$this->add_control( 'url', array(
			'label'       => 'URL',
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $d['url'],
			'placeholder' => 'https://…',
			'condition'   => array( 'link_type' => 'url' ),
		) );

		$this->add_control( 'email', array(
			'label'       => 'E-Mail',
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => $d['email'],
			'placeholder' => 'email@beispiel.de',
			'condition'   => array( 'link_type' => 'email' ),
		) );

		$this->add_control( 'email_subject', array(
			'label'     => __( 'Betreff', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => $d['email_subject'],
			'condition' => array( 'link_type' => 'email' ),
		) );

		$this->add_control( 'email_body', array(
			'label'     => __( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::TEXTAREA,
			'default'   => $d['email_body'],
			'condition' => array( 'link_type' => 'email' ),
		) );

		$this->add_control( 'media_url', array(
			'label'     => __( 'Mediendatei-URL', 'eifelhoster-buttons-pro' ),
			'type'      => \Elementor\Controls_Manager::TEXT,
			'default'   => $d['media_url'],
			'condition' => array( 'link_type' => 'media' ),
		) );

		$this->add_control( 'target', array(
			'label'   => __( 'Ziel', 'eifelhoster-buttons-pro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => $d['target'],
			'options' => array(
				'_self'  => __( 'Gleiche Seite', 'eifelhoster-buttons-pro' ),
				'_blank' => __( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ),
			),
		) );

		$this->end_controls_section();
	}

	/** Render the widget on the frontend and in the Elementor editor. */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Map Elementor control values back to shortcode attribute format.
		$atts = array(
			'text'           => sanitize_text_field( $settings['text'] ),
			'button_width'   => sanitize_text_field( $settings['button_width'] ),
			'font_family'    => sanitize_text_field( $settings['font_family'] ),
			'font_size'      => absint( $settings['font_size'] ),
			'font_bold'      => 'yes' === $settings['font_bold']   ? '1' : '0',
			'font_italic'    => 'yes' === $settings['font_italic']  ? '1' : '0',
			'padding_v'      => absint( $settings['padding_v'] ),
			'padding_h'      => absint( $settings['padding_h'] ),
			'bg_color'       => sanitize_text_field( $settings['bg_color'] ),
			'bg_hover_color' => sanitize_text_field( $settings['bg_hover_color'] ),
			'text_color'     => sanitize_text_field( $settings['text_color'] ),
			'text_hover_color' => sanitize_text_field( $settings['text_hover_color'] ),
			'hover_grow'     => floatval( $settings['hover_grow'] ),
			'border_width'   => absint( $settings['border_width'] ),
			'border_style'   => sanitize_text_field( $settings['border_style'] ),
			'border_color'   => sanitize_text_field( $settings['border_color'] ),
			'border_radius'  => absint( $settings['border_radius'] ),
			'shadow_enabled' => 'yes' === $settings['shadow_enabled'] ? '1' : '0',
			'shadow_x'       => intval( $settings['shadow_x'] ),
			'shadow_y'       => intval( $settings['shadow_y'] ),
			'shadow_blur'    => absint( $settings['shadow_blur'] ),
			'shadow_spread'  => intval( $settings['shadow_spread'] ),
			'shadow_color'   => sanitize_text_field( $settings['shadow_color'] ),
			'link_type'      => sanitize_text_field( $settings['link_type'] ),
			'url'            => esc_url_raw( $settings['url'] ),
			'email'          => sanitize_email( $settings['email'] ),
			'email_subject'  => sanitize_text_field( $settings['email_subject'] ),
			'email_body'     => sanitize_textarea_field( $settings['email_body'] ),
			'media_url'      => esc_url_raw( $settings['media_url'] ),
			'target'         => sanitize_text_field( $settings['target'] ),
			'icon_type'      => 'none',
			'icon'           => '',
			'icon_media_url' => '',
			'icon_size'      => 20,
			'icon_spacing'   => 8,
			'icon_position'  => 'before',
		);

		// Use the shortcode renderer so the output is always consistent.
		$shortcode = new EBP_Shortcode();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $shortcode->render( $atts );
	}
}
