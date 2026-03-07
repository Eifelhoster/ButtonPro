<?php
/**
 * Elementor Widget: Eifelhoster Button Pro
 *
 * Provides a full-featured button widget for the Elementor page builder with
 * all options that are also available in the Classic Editor dialog.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return __( 'ButtonPro', 'eifelhoster-buttons-pro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'eifelhoster' );
	}

	public function get_keywords() {
		return array( 'button', 'link', 'eifelhoster', 'buttonpro' );
	}

	// -------------------------------------------------------------------------
	// Controls
	// -------------------------------------------------------------------------
	protected function register_controls() {
		$d = ebp_get_defaults();

		/* =====================================================================
		 * SECTION: Text & Schrift
		 * =================================================================== */
		$this->start_controls_section(
			'section_text',
			array(
				'label' => __( 'Text & Schrift', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => __( 'Button-Text', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Button', 'eifelhoster-buttons-pro' ),
				'placeholder' => __( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->add_control(
			'font_family',
			array(
				'label'       => __( 'Schriftart', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['font_family'],
				'placeholder' => 'inherit, Arial, Georgia, …',
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'   => __( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['font_size'],
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'font_bold',
			array(
				'label'        => __( 'Fett', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => $d['font_bold'],
			)
		);

		$this->add_control(
			'font_italic',
			array(
				'label'        => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => $d['font_italic'],
			)
		);

		$this->add_control(
			'padding_v',
			array(
				'label'   => __( 'Innenabstand oben/unten (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['padding_v'],
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'padding_h',
			array(
				'label'   => __( 'Innenabstand links/rechts (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['padding_h'],
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'button_width',
			array(
				'label'       => __( 'Buttonbreite (px)', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => (int) $d['button_width'],
				'min'         => 0,
				'max'         => 2000,
				'description' => __( '0 = automatische Breite', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->end_controls_section();

		/* =====================================================================
		 * SECTION: Farben & Hover
		 * =================================================================== */
		$this->start_controls_section(
			'section_colors',
			array(
				'label' => __( 'Farben & Hover', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'   => __( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $d['bg_color'],
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'   => __( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $d['bg_hover_color'],
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Textfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $d['text_color'],
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'   => __( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $d['text_hover_color'],
			)
		);

		$this->add_control(
			'hover_grow',
			array(
				'label'   => __( 'Grow bei Hover', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (float) $d['hover_grow'],
				'min'     => 1,
				'max'     => 2,
				'step'    => 0.01,
			)
		);

		$this->end_controls_section();

		/* =====================================================================
		 * SECTION: Symbol (Icon)
		 * =================================================================== */
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => __( 'Symbol (Icon)', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => __( 'Symboltyp', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $d['icon_type'],
				'options' => array(
					'none'     => __( 'Kein Symbol', 'eifelhoster-buttons-pro' ),
					'dashicon' => 'Dashicon',
					'media'    => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Dashicon-Name', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => $d['icon'],
				'condition' => array( 'icon_type' => 'dashicon' ),
			)
		);

		$this->add_control(
			'icon_media_url',
			array(
				'label'       => __( 'Bild-URL', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['icon_media_url'],
				'label_block' => true,
				'condition'   => array( 'icon_type' => 'media' ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['icon_size'],
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'   => __( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['icon_spacing'],
				'min'     => 0,
				'max'     => 60,
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Symbolposition', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $d['icon_position'],
				'options' => array(
					'before' => __( 'Vor dem Text', 'eifelhoster-buttons-pro' ),
					'after'  => __( 'Hinter dem Text', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->end_controls_section();

		/* =====================================================================
		 * SECTION: Rahmen & Schatten
		 * =================================================================== */
		$this->start_controls_section(
			'section_border',
			array(
				'label' => __( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'border_width',
			array(
				'label'   => __( 'Rahmenstärke (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['border_width'],
				'min'     => 0,
				'max'     => 20,
			)
		);

		$this->add_control(
			'border_style',
			array(
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
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'   => __( 'Rahmenfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => $d['border_color'],
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => (int) $d['border_radius'],
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'shadow_enabled',
			array(
				'label'        => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => $d['shadow_enabled'],
			)
		);

		$this->add_control(
			'shadow_x',
			array(
				'label'     => __( 'Schatten X-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_x'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_y',
			array(
				'label'     => __( 'Schatten Y-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_y'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_blur',
			array(
				'label'     => __( 'Schatten Unschärfe (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_blur'],
				'min'       => 0,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_spread',
			array(
				'label'     => __( 'Schatten Ausbreitung (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_spread'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_color',
			array(
				'label'     => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => $d['shadow_color'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->end_controls_section();

		/* =====================================================================
		 * SECTION: Link & Ziel
		 * =================================================================== */
		$this->start_controls_section(
			'section_link',
			array(
				'label' => __( 'Link & Ziel', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => __( 'Linktyp', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $d['link_type'],
				'options' => array(
					'url'   => 'URL',
					'email' => 'E-Mail',
					'media' => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->add_control(
			'url',
			array(
				'label'       => 'URL',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['url'],
				'placeholder' => 'https://beispiel.de',
				'label_block' => true,
				'condition'   => array( 'link_type' => 'url' ),
			)
		);

		$this->add_control(
			'email',
			array(
				'label'       => 'E-Mail',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['email'],
				'placeholder' => 'info@beispiel.de',
				'condition'   => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'     => __( 'Betreff', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => $d['email_subject'],
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_body',
			array(
				'label'     => __( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => $d['email_body'],
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'media_url',
			array(
				'label'       => __( 'Mediendatei-URL', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['media_url'],
				'placeholder' => 'https://…/datei.pdf',
				'label_block' => true,
				'condition'   => array( 'link_type' => 'media' ),
			)
		);

		$this->add_control(
			'target',
			array(
				'label'   => __( 'Ziel', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $d['target'],
				'options' => array(
					'_self'  => __( 'Gleiche Seite', 'eifelhoster-buttons-pro' ),
					'_blank' => __( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// Render
	// -------------------------------------------------------------------------
	protected function render() {
		$s = $this->get_settings_for_display();

		// Build an atts array compatible with the shortcode renderer.
		$atts = array(
			'text'           => isset( $s['text'] )           ? $s['text']           : 'Button',
			'font_family'    => isset( $s['font_family'] )    ? $s['font_family']    : 'inherit',
			'font_size'      => isset( $s['font_size'] )      ? $s['font_size']      : '16',
			'font_bold'      => isset( $s['font_bold'] )      ? $s['font_bold']      : '0',
			'font_italic'    => isset( $s['font_italic'] )    ? $s['font_italic']    : '0',
			'button_width'   => isset( $s['button_width'] )   ? $s['button_width']   : '',
			'bg_color'       => isset( $s['bg_color'] )       ? $s['bg_color']       : '#007bff',
			'bg_hover_color' => isset( $s['bg_hover_color'] ) ? $s['bg_hover_color'] : '#0056b3',
			'text_color'     => isset( $s['text_color'] )     ? $s['text_color']     : '#ffffff',
			'text_hover_color'=> isset( $s['text_hover_color'] ) ? $s['text_hover_color'] : '#ffffff',
			'hover_grow'     => isset( $s['hover_grow'] )     ? $s['hover_grow']     : '1',
			'padding_v'      => isset( $s['padding_v'] )      ? $s['padding_v']      : '10',
			'padding_h'      => isset( $s['padding_h'] )      ? $s['padding_h']      : '20',
			'icon_type'      => isset( $s['icon_type'] )      ? $s['icon_type']      : 'none',
			'icon'           => isset( $s['icon'] )           ? $s['icon']           : '',
			'icon_media_url' => isset( $s['icon_media_url'] ) ? $s['icon_media_url'] : '',
			'icon_size'      => isset( $s['icon_size'] )      ? $s['icon_size']      : '20',
			'icon_spacing'   => isset( $s['icon_spacing'] )   ? $s['icon_spacing']   : '8',
			'icon_position'  => isset( $s['icon_position'] )  ? $s['icon_position']  : 'before',
			'border_width'   => isset( $s['border_width'] )   ? $s['border_width']   : '0',
			'border_style'   => isset( $s['border_style'] )   ? $s['border_style']   : 'solid',
			'border_color'   => isset( $s['border_color'] )   ? $s['border_color']   : '#000000',
			'border_radius'  => isset( $s['border_radius'] )  ? $s['border_radius']  : '4',
			'shadow_enabled' => isset( $s['shadow_enabled'] ) ? $s['shadow_enabled'] : '0',
			'shadow_x'       => isset( $s['shadow_x'] )       ? $s['shadow_x']       : '0',
			'shadow_y'       => isset( $s['shadow_y'] )       ? $s['shadow_y']       : '2',
			'shadow_blur'    => isset( $s['shadow_blur'] )    ? $s['shadow_blur']    : '4',
			'shadow_spread'  => isset( $s['shadow_spread'] )  ? $s['shadow_spread']  : '0',
			'shadow_color'   => isset( $s['shadow_color'] )   ? $s['shadow_color']   : 'rgba(0,0,0,0.3)',
			'link_type'      => isset( $s['link_type'] )      ? $s['link_type']      : 'url',
			'url'            => isset( $s['url'] )            ? $s['url']            : '',
			'email'          => isset( $s['email'] )          ? $s['email']          : '',
			'email_subject'  => isset( $s['email_subject'] )  ? $s['email_subject']  : '',
			'email_body'     => isset( $s['email_body'] )     ? $s['email_body']     : '',
			'media_url'      => isset( $s['media_url'] )      ? $s['media_url']      : '',
			'target'         => isset( $s['target'] )         ? $s['target']         : '_self',
		);

		// Re-use the shortcode renderer so output is always consistent.
		$shortcode = new EBP_Shortcode();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $shortcode->render( $atts );
	}
}
