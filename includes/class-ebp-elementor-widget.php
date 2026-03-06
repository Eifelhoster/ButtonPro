<?php
/**
 * Elementor Widget for Eifelhoster Buttons Pro.
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
		return array( 'button', 'link', 'eifelhoster' );
	}

	protected function register_controls() {
		$d = ebp_get_defaults();

		// =====================================================================
		// SECTION: Text & Schrift
		// =====================================================================
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
				'step'    => 1,
			)
		);

		$this->add_control(
			'font_bold',
			array(
				'label'        => __( 'Fett', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
				'return_value' => '1',
				'default'      => $d['font_bold'],
			)
		);

		$this->add_control(
			'font_italic',
			array(
				'label'        => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
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
				'step'    => 1,
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
				'step'    => 1,
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
				'step'        => 1,
				'description' => __( '0 = automatische Breite', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Farben & Hover
		// =====================================================================
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

		// =====================================================================
		// SECTION: Symbol (Icon)
		// =====================================================================
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
				'label'     => __( 'Dashicon (Slug)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => $d['icon'],
				'condition' => array( 'icon_type' => 'dashicon' ),
			)
		);

		$this->add_control(
			'icon_media_url',
			array(
				'label'     => __( 'Bild-URL', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => array( 'url' => $d['icon_media_url'] ),
				'condition' => array( 'icon_type' => 'media' ),
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
				'step'    => 1,
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
				'step'    => 1,
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

		// =====================================================================
		// SECTION: Rahmen & Schatten
		// =====================================================================
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
				'step'    => 1,
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
				'step'    => 1,
			)
		);

		$this->add_control(
			'shadow_enabled',
			array(
				'label'        => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
				'return_value' => '1',
				'default'      => $d['shadow_enabled'],
			)
		);

		$this->add_control(
			'shadow_x',
			array(
				'label'     => __( 'X-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_x'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_y',
			array(
				'label'     => __( 'Y-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_y'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_blur',
			array(
				'label'     => __( 'Unschärfe (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_blur'],
				'min'       => 0,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_spread',
			array(
				'label'     => __( 'Ausbreitung (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => (int) $d['shadow_spread'],
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_color',
			array(
				'label'       => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $d['shadow_color'],
				'placeholder' => 'rgba(0,0,0,0.3)',
				'condition'   => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Link & Ziel
		// =====================================================================
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
				'label'     => 'URL',
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => array( 'url' => $d['url'], 'is_external' => '_blank' === $d['target'] ),
				'condition' => array( 'link_type' => 'url' ),
			)
		);

		$this->add_control(
			'email',
			array(
				'label'     => __( 'E-Mail-Adresse', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => $d['email'],
				'condition' => array( 'link_type' => 'email' ),
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
				'label'     => __( 'Mediendatei-URL', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => array( 'url' => $d['media_url'] ),
				'condition' => array( 'link_type' => 'media' ),
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

	protected function render() {
		$s = $this->get_settings_for_display();
		$d = ebp_get_defaults();

		// Resolve URL control values (Elementor stores them as arrays).
		$url            = isset( $s['url']['url'] )            ? $s['url']['url']            : '';
		$media_url      = isset( $s['media_url']['url'] )      ? $s['media_url']['url']      : '';
		$icon_media_url = isset( $s['icon_media_url']['url'] ) ? $s['icon_media_url']['url'] : '';

		// Resolve target: prefer the explicit 'target' control; override with _blank
		// when the URL control's is_external flag is checked on URL-type links.
		$target = isset( $s['target'] ) ? $s['target'] : $d['target'];
		if ( 'url' === ( isset( $s['link_type'] ) ? $s['link_type'] : '' ) && ! empty( $s['url']['is_external'] ) ) {
			$target = '_blank';
		}

		$atts = array(
			'text'            => isset( $s['text'] )            ? $s['text']                    : 'Button',
			'font_family'     => isset( $s['font_family'] )     ? $s['font_family']             : $d['font_family'],
			'font_size'       => isset( $s['font_size'] )       ? (string) $s['font_size']      : $d['font_size'],
			'font_bold'       => isset( $s['font_bold'] )       ? $s['font_bold']               : $d['font_bold'],
			'font_italic'     => isset( $s['font_italic'] )     ? $s['font_italic']             : $d['font_italic'],
			'bg_color'        => isset( $s['bg_color'] )        ? $s['bg_color']                : $d['bg_color'],
			'bg_hover_color'  => isset( $s['bg_hover_color'] )  ? $s['bg_hover_color']          : $d['bg_hover_color'],
			'text_color'      => isset( $s['text_color'] )      ? $s['text_color']              : $d['text_color'],
			'text_hover_color' => isset( $s['text_hover_color'] ) ? $s['text_hover_color']      : $d['text_hover_color'],
			'hover_grow'      => isset( $s['hover_grow'] )      ? (string) $s['hover_grow']     : $d['hover_grow'],
			'padding_v'       => isset( $s['padding_v'] )       ? (string) $s['padding_v']      : $d['padding_v'],
			'padding_h'       => isset( $s['padding_h'] )       ? (string) $s['padding_h']      : $d['padding_h'],
			'button_width'    => isset( $s['button_width'] )    ? (string) $s['button_width']   : $d['button_width'],
			'icon_type'       => isset( $s['icon_type'] )       ? $s['icon_type']               : $d['icon_type'],
			'icon'            => isset( $s['icon'] )            ? $s['icon']                    : $d['icon'],
			'icon_media_url'  => $icon_media_url,
			'icon_size'       => isset( $s['icon_size'] )       ? (string) $s['icon_size']      : $d['icon_size'],
			'icon_spacing'    => isset( $s['icon_spacing'] )    ? (string) $s['icon_spacing']   : $d['icon_spacing'],
			'icon_position'   => isset( $s['icon_position'] )   ? $s['icon_position']           : $d['icon_position'],
			'border_width'    => isset( $s['border_width'] )    ? (string) $s['border_width']   : $d['border_width'],
			'border_style'    => isset( $s['border_style'] )    ? $s['border_style']            : $d['border_style'],
			'border_color'    => isset( $s['border_color'] )    ? $s['border_color']            : $d['border_color'],
			'border_radius'   => isset( $s['border_radius'] )   ? (string) $s['border_radius']  : $d['border_radius'],
			'shadow_enabled'  => isset( $s['shadow_enabled'] )  ? $s['shadow_enabled']          : $d['shadow_enabled'],
			'shadow_x'        => isset( $s['shadow_x'] )        ? (string) $s['shadow_x']       : $d['shadow_x'],
			'shadow_y'        => isset( $s['shadow_y'] )        ? (string) $s['shadow_y']       : $d['shadow_y'],
			'shadow_blur'     => isset( $s['shadow_blur'] )     ? (string) $s['shadow_blur']    : $d['shadow_blur'],
			'shadow_spread'   => isset( $s['shadow_spread'] )   ? (string) $s['shadow_spread']  : $d['shadow_spread'],
			'shadow_color'    => isset( $s['shadow_color'] )    ? $s['shadow_color']            : $d['shadow_color'],
			'link_type'       => isset( $s['link_type'] )       ? $s['link_type']               : $d['link_type'],
			'url'             => $url,
			'email'           => isset( $s['email'] )           ? $s['email']                   : $d['email'],
			'email_subject'   => isset( $s['email_subject'] )   ? $s['email_subject']           : $d['email_subject'],
			'email_body'      => isset( $s['email_body'] )      ? $s['email_body']              : $d['email_body'],
			'media_url'       => $media_url,
			'target'          => $target,
		);

		// Build and output the shortcode.
		$shortcode = '[eifelhoster_button';
		foreach ( $atts as $key => $value ) {
			$shortcode .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}
		$shortcode .= ']';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_shortcode( $shortcode );
	}
}
