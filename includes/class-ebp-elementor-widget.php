<?php
/**
 * Elementor Widget: eh-button-pro
 *
 * Provides all button features from Eifelhoster Buttons Pro as an Elementor widget.
 * Registered under the "Eifelhoster" category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EBP_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return __( 'eh-button-Pro', 'eifelhoster-buttons-pro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'eifelhoster' );
	}

	public function get_keywords() {
		return array( 'button', 'eifelhoster', 'btn', 'cta' );
	}

	/** Register all controls. */
	protected function register_controls() {
		$defaults = ebp_get_defaults();

		// =====================================================================
		// SECTION: Text & Font
		// =====================================================================
		$this->start_controls_section(
			'section_text',
			array(
				'label' => __( 'Text & Schrift', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'   => __( 'Button-Text', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Button',
			)
		);

		$this->add_control(
			'font_family',
			array(
				'label'       => __( 'Schriftart', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $defaults['font_family'],
				'placeholder' => 'inherit, Arial, Georgia, …',
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'   => __( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['font_size'],
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'font_bold',
			array(
				'label'   => __( 'Fett', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '1' === $defaults['font_bold'] ? 'yes' : '',
			)
		);

		$this->add_control(
			'font_italic',
			array(
				'label'   => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '1' === $defaults['font_italic'] ? 'yes' : '',
			)
		);

		$this->add_control(
			'padding_v',
			array(
				'label'   => __( 'Innenabstand Oben/Unten (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['padding_v'],
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'padding_h',
			array(
				'label'   => __( 'Innenabstand Links/Rechts (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['padding_h'],
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'button_width',
			array(
				'label'       => __( 'Gesamtbreite (px)', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => (int) $defaults['button_width'],
				'min'         => 0,
				'max'         => 2000,
				'description' => __( '0 = automatische Breite', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Colors & Hover
		// =====================================================================
		$this->start_controls_section(
			'section_colors',
			array(
				'label' => __( 'Farben & Hover', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'   => __( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $defaults['bg_color'],
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'   => __( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $defaults['bg_hover_color'],
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Textfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $defaults['text_color'],
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'   => __( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $defaults['text_hover_color'],
			)
		);

		$this->add_control(
			'hover_grow',
			array(
				'label'   => __( 'Grow bei Hover', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (float) $defaults['hover_grow'],
				'min'     => 1,
				'max'     => 2,
				'step'    => 0.01,
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Icon
		// =====================================================================
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => __( 'Symbol (Icon)', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => __( 'Symboltyp', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['icon_type'],
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
				'type'      => Controls_Manager::TEXT,
				'default'   => $defaults['icon'],
				'condition' => array( 'icon_type' => 'dashicon' ),
			)
		);

		$this->add_control(
			'icon_media_url',
			array(
				'label'      => __( 'Symbol-Mediendatei', 'eifelhoster-buttons-pro' ),
				'type'       => Controls_Manager::MEDIA,
				'default'    => array( 'url' => $defaults['icon_media_url'] ),
				'condition'  => array( 'icon_type' => 'media' ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['icon_size'],
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'   => __( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['icon_spacing'],
				'min'     => 0,
				'max'     => 60,
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Symbolposition', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['icon_position'],
				'options' => array(
					'before' => __( 'Vor dem Text', 'eifelhoster-buttons-pro' ),
					'after'  => __( 'Hinter dem Text', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'       => __( 'Symbolfarbe', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => $defaults['icon_color'],
				'description' => __( 'Leer = Textfarbe übernehmen', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Border & Shadow
		// =====================================================================
		$this->start_controls_section(
			'section_border',
			array(
				'label' => __( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'border_width',
			array(
				'label'   => __( 'Rahmenstärke (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['border_width'],
				'min'     => 0,
				'max'     => 20,
			)
		);

		$this->add_control(
			'border_style',
			array(
				'label'   => __( 'Rahmenstil', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['border_style'],
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
				'type'    => Controls_Manager::COLOR,
				'default' => $defaults['border_color'],
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => (int) $defaults['border_radius'],
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'shadow_enabled',
			array(
				'label'   => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '1' === $defaults['shadow_enabled'] ? 'yes' : '',
			)
		);

		$this->add_control(
			'shadow_x',
			array(
				'label'     => __( 'Schatten X-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => (int) $defaults['shadow_x'],
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_y',
			array(
				'label'     => __( 'Schatten Y-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => (int) $defaults['shadow_y'],
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_blur',
			array(
				'label'     => __( 'Unschärfe (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => (int) $defaults['shadow_blur'],
				'min'       => 0,
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_spread',
			array(
				'label'     => __( 'Ausbreitung (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => (int) $defaults['shadow_spread'],
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_color',
			array(
				'label'     => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $defaults['shadow_color'],
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// SECTION: Link & Target
		// =====================================================================
		$this->start_controls_section(
			'section_link',
			array(
				'label' => __( 'Link & Ziel', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => __( 'Linktyp', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['link_type'],
				'options' => array(
					'url'     => 'URL',
					'email'   => 'E-Mail',
					'media'   => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
					'content' => __( 'Inhalt (Seite/Beitrag)', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->add_control(
			'url',
			array(
				'label'       => 'URL',
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…',
				'condition'   => array( 'link_type' => 'url' ),
			)
		);

		$this->add_control(
			'email',
			array(
				'label'       => __( 'E-Mail-Adresse', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $defaults['email'],
				'placeholder' => 'info@beispiel.de',
				'condition'   => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'     => __( 'Betreff', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $defaults['email_subject'],
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_body',
			array(
				'label'     => __( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => $defaults['email_body'],
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'media_url',
			array(
				'label'     => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => $defaults['media_url'] ),
				'condition' => array( 'link_type' => 'media' ),
			)
		);

		$this->add_control(
			'content_id',
			array(
				'label'       => __( 'Seiten-/Beitrags-ID', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => (int) $defaults['content_id'],
				'min'         => 0,
				'description' => __( 'WordPress Post-ID der verlinkten Seite oder des Beitrags.', 'eifelhoster-buttons-pro' ),
				'condition'   => array( 'link_type' => 'content' ),
			)
		);

		$this->add_control(
			'target',
			array(
				'label'   => __( 'Ziel', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $defaults['target'],
				'options' => array(
					'_self'  => __( 'Gleiche Seite', 'eifelhoster-buttons-pro' ),
					'_blank' => __( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/** Render the widget on the frontend. */
	protected function render() {
		$s = $this->get_settings_for_display();

		// Normalise Elementor switcher values ('yes'/'') → shortcode ('1'/'0').
		$font_bold      = 'yes' === $s['font_bold']      ? '1' : '0';
		$font_italic    = 'yes' === $s['font_italic']    ? '1' : '0';
		$shadow_enabled = 'yes' === $s['shadow_enabled'] ? '1' : '0';

		// Resolve URL control (Elementor URL control returns array with 'url' key).
		$url       = ! empty( $s['url']['url'] ) ? $s['url']['url'] : '';
		$media_url = ! empty( $s['media_url']['url'] ) ? $s['media_url']['url'] : '';

		// Resolve icon media URL.
		$icon_media_url = ! empty( $s['icon_media_url']['url'] ) ? $s['icon_media_url']['url'] : '';

		// Build shortcode attribute array.
		$atts = array(
			'text'             => $s['text'],
			'font_family'      => $s['font_family'],
			'font_size'        => (string) $s['font_size'],
			'font_bold'        => $font_bold,
			'font_italic'      => $font_italic,
			'bg_color'         => $s['bg_color'],
			'bg_hover_color'   => $s['bg_hover_color'],
			'text_color'       => $s['text_color'],
			'text_hover_color' => $s['text_hover_color'],
			'hover_grow'       => (string) $s['hover_grow'],
			'padding_v'        => (string) $s['padding_v'],
			'padding_h'        => (string) $s['padding_h'],
			'button_width'     => (string) $s['button_width'],
			'icon_type'        => $s['icon_type'],
			'icon'             => $s['icon'],
			'icon_media_url'   => $icon_media_url,
			'icon_size'        => (string) $s['icon_size'],
			'icon_spacing'     => (string) $s['icon_spacing'],
			'icon_position'    => $s['icon_position'],
			'icon_color'       => $s['icon_color'],
			'border_width'     => (string) $s['border_width'],
			'border_style'     => $s['border_style'],
			'border_color'     => $s['border_color'],
			'border_radius'    => (string) $s['border_radius'],
			'shadow_enabled'   => $shadow_enabled,
			'shadow_x'         => (string) $s['shadow_x'],
			'shadow_y'         => (string) $s['shadow_y'],
			'shadow_blur'      => (string) $s['shadow_blur'],
			'shadow_spread'    => (string) $s['shadow_spread'],
			'shadow_color'     => $s['shadow_color'],
			'link_type'        => $s['link_type'],
			'url'              => $url,
			'email'            => $s['email'],
			'email_subject'    => $s['email_subject'],
			'email_body'       => $s['email_body'],
			'media_url'        => $media_url,
			'content_id'       => (string) $s['content_id'],
			'target'           => $s['target'],
		);

		// Render via the shortcode class to keep HTML identical.
		$shortcode = new EBP_Shortcode();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $shortcode->render( $atts );
	}
}
