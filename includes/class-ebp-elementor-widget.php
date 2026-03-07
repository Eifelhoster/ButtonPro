<?php
/**
 * Elementor Widget for Eifelhoster Buttons Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class EBP_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return __( 'Eifelhoster Button Pro', 'eifelhoster-buttons-pro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'eifelhoster' );
	}

	public function get_keywords() {
		return array( 'button', 'eifelhoster', 'link', 'cta' );
	}

	protected function register_controls() {

		// ============================================================
		// TAB: Text & Schrift
		// ============================================================
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
				'label'       => __( 'Button-Text', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Button', 'eifelhoster-buttons-pro' ),
				'placeholder' => __( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->add_control(
			'font_family',
			array(
				'label'       => __( 'Schriftart', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'inherit',
				'placeholder' => 'inherit, Arial, Georgia, …',
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'   => __( 'Schriftgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 16,
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'font_bold',
			array(
				'label'        => __( 'Fett', 'eifelhoster-buttons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
				'return_value' => '1',
				'default'      => '',
			)
		);

		$this->add_control(
			'font_italic',
			array(
				'label'        => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
				'return_value' => '1',
				'default'      => '',
			)
		);

		$this->add_control(
			'padding_v',
			array(
				'label'   => __( 'Innenabstand Oben/Unten (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'padding_h',
			array(
				'label'   => __( 'Innenabstand Links/Rechts (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 20,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'button_width',
			array(
				'label'       => __( 'Button-Breite gesamt (px)', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 300,
				'min'         => 0,
				'max'         => 2000,
				'description' => __( '0 = automatische Breite', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->end_controls_section();

		// ============================================================
		// TAB: Farben & Hover
		// ============================================================
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
				'default' => '#007bff',
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'   => __( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0056b3',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Textfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'   => __( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'hover_grow',
			array(
				'label'   => __( 'Grow bei Hover', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1.04,
				'min'     => 1,
				'max'     => 2,
				'step'    => 0.01,
			)
		);

		$this->end_controls_section();

		// ============================================================
		// TAB: Symbol (Icon)
		// ============================================================
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
				'default' => 'none',
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
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array( 'icon_type' => 'dashicon' ),
			)
		);

		$this->add_control(
			'icon_media_url',
			array(
				'label'     => __( 'Symbol-Bild URL', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array( 'icon_type' => 'media' ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 32,
				'min'     => 8,
				'max'     => 120,
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'   => __( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 24,
				'min'     => 0,
				'max'     => 60,
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Symbolposition', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => array(
					'before' => __( 'Vor dem Text', 'eifelhoster-buttons-pro' ),
					'after'  => __( 'Hinter dem Text', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->end_controls_section();

		// ============================================================
		// TAB: Rahmen & Schatten
		// ============================================================
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
				'default' => 0,
				'min'     => 0,
				'max'     => 20,
			)
		);

		$this->add_control(
			'border_style',
			array(
				'label'   => __( 'Rahmenstil', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => 'none',
					'solid'  => 'solid',
					'dashed' => 'dashed',
					'dotted' => 'dotted',
					'double' => 'double',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'   => __( 'Rahmenfarbe', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#000000',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Rahmenradius (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'shadow_enabled',
			array(
				'label'        => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Ja', 'eifelhoster-buttons-pro' ),
				'label_off'    => __( 'Nein', 'eifelhoster-buttons-pro' ),
				'return_value' => '1',
				'default'      => '1',
			)
		);

		$this->add_control(
			'shadow_x',
			array(
				'label'     => __( 'Schatten X-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_y',
			array(
				'label'     => __( 'Schatten Y-Offset (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_blur',
			array(
				'label'     => __( 'Schatten Unschärfe (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 0,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_spread',
			array(
				'label'     => __( 'Schatten Ausbreitung (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->add_control(
			'shadow_color',
			array(
				'label'     => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#777777',
				'condition' => array( 'shadow_enabled' => '1' ),
			)
		);

		$this->end_controls_section();

		// ============================================================
		// TAB: Link & Ziel
		// ============================================================
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
				'default' => 'url',
				'options' => array(
					'url'     => 'URL',
					'email'   => 'E-Mail',
					'media'   => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
					'content' => __( 'Inhalt', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->add_control(
			'url',
			array(
				'label'       => 'URL',
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…',
				'default'     => array( 'url' => '' ),
				'condition'   => array( 'link_type' => 'url' ),
			)
		);

		$this->add_control(
			'email',
			array(
				'label'     => 'E-Mail',
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'     => __( 'Betreff', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'email_body',
			array(
				'label'     => __( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => '',
				'condition' => array( 'link_type' => 'email' ),
			)
		);

		$this->add_control(
			'media_url',
			array(
				'label'     => __( 'Mediendatei URL', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array( 'link_type' => 'media' ),
			)
		);

		$this->add_control(
			'content_id',
			array(
				'label'       => __( 'Inhalt (Post/Seite ID)', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'description' => __( 'ID einer Seite, eines Beitrags oder eines Custom Post Types', 'eifelhoster-buttons-pro' ),
				'condition'   => array( 'link_type' => 'content' ),
			)
		);

		$this->add_control(
			'target',
			array(
				'label'   => __( 'Ziel', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '_self',
				'options' => array(
					'_self'  => __( 'Gleiche Seite', 'eifelhoster-buttons-pro' ),
					'_blank' => __( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Map Elementor settings to shortcode attribute array.
		$attrs = array(
			'text'             => isset( $settings['text'] ) ? $settings['text'] : 'Button',
			'font_family'      => isset( $settings['font_family'] ) ? $settings['font_family'] : 'inherit',
			'font_size'        => isset( $settings['font_size'] ) ? (string) $settings['font_size'] : '16',
			'font_bold'        => ( isset( $settings['font_bold'] ) && '1' === $settings['font_bold'] ) ? '1' : '0',
			'font_italic'      => ( isset( $settings['font_italic'] ) && '1' === $settings['font_italic'] ) ? '1' : '0',
			'button_width'     => isset( $settings['button_width'] ) ? (string) $settings['button_width'] : '300',
			'bg_color'         => isset( $settings['bg_color'] ) ? $settings['bg_color'] : '#007bff',
			'bg_hover_color'   => isset( $settings['bg_hover_color'] ) ? $settings['bg_hover_color'] : '#0056b3',
			'text_color'       => isset( $settings['text_color'] ) ? $settings['text_color'] : '#ffffff',
			'text_hover_color' => isset( $settings['text_hover_color'] ) ? $settings['text_hover_color'] : '#ffffff',
			'hover_grow'       => isset( $settings['hover_grow'] ) ? (string) $settings['hover_grow'] : '1.04',
			'padding_v'        => isset( $settings['padding_v'] ) ? (string) $settings['padding_v'] : '10',
			'padding_h'        => isset( $settings['padding_h'] ) ? (string) $settings['padding_h'] : '20',
			'icon_type'        => isset( $settings['icon_type'] ) ? $settings['icon_type'] : 'none',
			'icon'             => isset( $settings['icon'] ) ? $settings['icon'] : '',
			'icon_media_url'   => isset( $settings['icon_media_url'] ) ? $settings['icon_media_url'] : '',
			'icon_size'        => isset( $settings['icon_size'] ) ? (string) $settings['icon_size'] : '32',
			'icon_spacing'     => isset( $settings['icon_spacing'] ) ? (string) $settings['icon_spacing'] : '24',
			'icon_position'    => isset( $settings['icon_position'] ) ? $settings['icon_position'] : 'before',
			'border_width'     => isset( $settings['border_width'] ) ? (string) $settings['border_width'] : '0',
			'border_style'     => isset( $settings['border_style'] ) ? $settings['border_style'] : 'none',
			'border_color'     => isset( $settings['border_color'] ) ? $settings['border_color'] : '#000000',
			'border_radius'    => isset( $settings['border_radius'] ) ? (string) $settings['border_radius'] : '4',
			'shadow_enabled'   => ( isset( $settings['shadow_enabled'] ) && '1' === $settings['shadow_enabled'] ) ? '1' : '0',
			'shadow_x'         => isset( $settings['shadow_x'] ) ? (string) $settings['shadow_x'] : '2',
			'shadow_y'         => isset( $settings['shadow_y'] ) ? (string) $settings['shadow_y'] : '2',
			'shadow_blur'      => isset( $settings['shadow_blur'] ) ? (string) $settings['shadow_blur'] : '4',
			'shadow_spread'    => isset( $settings['shadow_spread'] ) ? (string) $settings['shadow_spread'] : '2',
			'shadow_color'     => isset( $settings['shadow_color'] ) ? $settings['shadow_color'] : '#777777',
			'link_type'        => isset( $settings['link_type'] ) ? $settings['link_type'] : 'url',
			'url'              => ( isset( $settings['url']['url'] ) ) ? $settings['url']['url'] : '',
			'email'            => isset( $settings['email'] ) ? $settings['email'] : '',
			'email_subject'    => isset( $settings['email_subject'] ) ? $settings['email_subject'] : '',
			'email_body'       => isset( $settings['email_body'] ) ? $settings['email_body'] : '',
			'media_url'        => isset( $settings['media_url'] ) ? $settings['media_url'] : '',
			'content_id'       => isset( $settings['content_id'] ) ? (string) $settings['content_id'] : '',
			'target'           => isset( $settings['target'] ) ? $settings['target'] : '_self',
		);

		// Build and execute the shortcode.
		$sc = '[eifelhoster_button';
		foreach ( $attrs as $key => $value ) {
			$sc .= ' ' . sanitize_key( $key ) . '="' . esc_attr( $value ) . '"';
		}
		$sc .= ']';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_shortcode output is safe: all shortcode attributes are passed through esc_attr() above and class-ebp-shortcode.php escapes all HTML output.
		echo do_shortcode( $sc );
	}

	protected function content_template() {
		// Live preview in Elementor editor – simplified JS template.
		?>
		<#
		var text        = settings.text || 'Button';
		var bgColor     = settings.bg_color || '#007bff';
		var textColor   = settings.text_color || '#ffffff';
		var paddingV    = settings.padding_v || 10;
		var paddingH    = settings.padding_h || 20;
		var borderW     = settings.border_width || 0;
		var borderStyle = settings.border_style || 'none';
		var borderColor = settings.border_color || '#000000';
		var borderRadius= settings.border_radius || 4;
		var fontFamily  = settings.font_family || 'inherit';
		var fontSize    = settings.font_size || 16;
		var fontWeight  = settings.font_bold === '1' ? 'bold' : 'normal';
		var fontStyle   = settings.font_italic === '1' ? 'italic' : 'normal';
		var buttonWidth = parseInt( settings.button_width, 10 ) || 0;
		var shadowOn    = settings.shadow_enabled === '1';
		var shadowX     = settings.shadow_x || 2;
		var shadowY     = settings.shadow_y || 2;
		var shadowBlur  = settings.shadow_blur || 4;
		var shadowSprd  = settings.shadow_spread || 2;
		var shadowColor = settings.shadow_color || '#777777';
		var boxShadow   = shadowOn
			? shadowX + 'px ' + shadowY + 'px ' + shadowBlur + 'px ' + shadowSprd + 'px ' + shadowColor
			: 'none';
		var widthStyle  = buttonWidth > 0 ? buttonWidth + 'px' : '';

		var styleStr = 'display:inline-flex;align-items:center;justify-content:center;'
			+ 'text-decoration:none;cursor:pointer;'
			+ 'font-family:' + fontFamily + ';'
			+ 'font-size:' + fontSize + 'px;'
			+ 'font-weight:' + fontWeight + ';'
			+ 'font-style:' + fontStyle + ';'
			+ 'background-color:' + bgColor + ';'
			+ 'color:' + textColor + ';'
			+ 'padding:' + paddingV + 'px ' + paddingH + 'px;'
			+ 'border-width:' + borderW + 'px;'
			+ 'border-style:' + borderStyle + ';'
			+ 'border-color:' + borderColor + ';'
			+ 'border-radius:' + borderRadius + 'px;'
			+ 'box-shadow:' + boxShadow + ';'
			+ ( widthStyle ? 'width:' + widthStyle + ';' : '' );
		#>
		<a href="#" style="{{ styleStr }}">{{ text }}</a>
		<?php
	}
}
