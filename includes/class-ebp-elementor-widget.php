<?php
/**
 * Elementor Widget: Eifelhoster Button Pro.
 *
 * Provides all button options inside the Elementor editor panel.
 * For the "Seite / Beitrag" link type a live AJAX search is provided
 * via the ebp-elementor-editor.js script.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class EBP_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return __( 'Eifelhoster Button', 'eifelhoster-buttons-pro' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'eifelhoster', 'basic' );
	}

	public function get_keywords() {
		return array( 'button', 'link', 'cta', 'eifelhoster' );
	}

	// -------------------------------------------------------------------------
	// Controls
	// -------------------------------------------------------------------------
	protected function register_controls() {

		/* ============================================================
		   Section: Inhalt (Content)
		   ============================================================ */
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Inhalt', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button-Text', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Button', 'eifelhoster-buttons-pro' ),
				'placeholder' => __( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ),
				'label_block' => true,
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
					'content' => __( 'Seite / Beitrag', 'eifelhoster-buttons-pro' ),
					'email'   => 'E-Mail',
					'media'   => __( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		// ---- URL ----
		$this->add_control(
			'button_url',
			array(
				'label'       => 'URL',
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…',
				'default'     => array( 'url' => '' ),
				'condition'   => array( 'link_type' => 'url' ),
				'label_block' => true,
			)
		);

		// ---- Seite / Beitrag ----
		$this->add_control(
			'content_search',
			array(
				'label'       => __( 'Seite / Beitrag suchen', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Mindestens 2 Zeichen eingeben…', 'eifelhoster-buttons-pro' ),
				'condition'   => array( 'link_type' => 'content' ),
				'label_block' => true,
				'description' => __( 'Tippen Sie mindestens 2 Zeichen, um Seiten und Beiträge zu suchen.', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->add_control(
			'content_id',
			array(
				'label'       => __( 'Ausgewählte ID', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => array( 'link_type' => 'content' ),
				'description' => __( 'Wird beim Auswählen aus den Suchergebnissen automatisch gesetzt.', 'eifelhoster-buttons-pro' ),
			)
		);

		// ---- E-Mail ----
		$this->add_control(
			'email_address',
			array(
				'label'       => 'E-Mail',
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'email@beispiel.de',
				'condition'   => array( 'link_type' => 'email' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'       => __( 'Betreff', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => array( 'link_type' => 'email' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_body',
			array(
				'label'       => __( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'condition'   => array( 'link_type' => 'email' ),
				'label_block' => true,
			)
		);

		// ---- Mediendatei ----
		$this->add_control(
			'media_url',
			array(
				'label'       => __( 'Mediendatei-URL', 'eifelhoster-buttons-pro' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://…',
				'default'     => array( 'url' => '' ),
				'condition'   => array( 'link_type' => 'media' ),
				'label_block' => true,
			)
		);

		// ---- Ziel ----
		$this->add_control(
			'target',
			array(
				'label'     => __( 'Ziel', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '_self',
				'options'   => array(
					'_self'  => __( 'Gleiche Seite', 'eifelhoster-buttons-pro' ),
					'_blank' => __( 'Neues Fenster / Tab', 'eifelhoster-buttons-pro' ),
				),
				'condition' => array( 'link_type!' => 'url' ),
			)
		);

		$this->end_controls_section();

		/* ============================================================
		   Section: Stil (Style Tab)
		   ============================================================ */
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Stil', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'default' => 1,
				'min'     => 1,
				'max'     => 2,
				'step'    => 0.01,
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
				'label'   => __( 'Fett', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'font_italic',
			array(
				'label'   => __( 'Kursiv', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'padding_v',
			array(
				'label'   => __( 'Innenabstand Vertikal (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'padding_h',
			array(
				'label'   => __( 'Innenabstand Horizontal (px)', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 20,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->end_controls_section();

		/* ============================================================
		   Section: Symbol (Icon)
		   ============================================================ */
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => __( 'Symbol', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'label'       => 'Dashicon',
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'arrow-right',
				'condition'   => array( 'icon_type' => 'dashicon' ),
				'description' => __( 'Dashicon-Slug ohne "dashicons-" Präfix, z. B. arrow-right.', 'eifelhoster-buttons-pro' ),
			)
		);

		$this->add_control(
			'icon_media_url',
			array(
				'label'     => __( 'Symbol-Bild', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'icon_type' => 'media' ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'     => __( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 20,
				'min'       => 8,
				'max'       => 120,
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'     => __( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 8,
				'min'       => 0,
				'max'       => 60,
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'     => __( 'Position', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => array(
					'before' => __( 'Vor dem Text', 'eifelhoster-buttons-pro' ),
					'after'  => __( 'Hinter dem Text', 'eifelhoster-buttons-pro' ),
				),
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->end_controls_section();

		/* ============================================================
		   Section: Rahmen & Schatten (Border & Shadow)
		   ============================================================ */
		$this->start_controls_section(
			'section_border',
			array(
				'label' => __( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'default' => 'solid',
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
				'label'   => __( 'Schatten aktivieren', 'eifelhoster-buttons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'shadow_x',
			array(
				'label'     => 'X-Offset (px)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_y',
			array(
				'label'     => 'Y-Offset (px)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_blur',
			array(
				'label'     => __( 'Unschärfe (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 0,
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_spread',
			array(
				'label'     => __( 'Ausbreitung (px)', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->add_control(
			'shadow_color',
			array(
				'label'     => __( 'Schattenfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.3)',
				'condition' => array( 'shadow_enabled' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// Render (PHP-side, for page HTML)
	// -------------------------------------------------------------------------
	protected function render() {
		$s = $this->get_settings_for_display();

		$link_type = isset( $s['link_type'] ) ? $s['link_type'] : 'url';

		// Build link / URL.
		$url    = '#';
		$target = isset( $s['target'] ) ? $s['target'] : '_self';

		if ( 'url' === $link_type ) {
			$url    = ! empty( $s['button_url']['url'] ) ? esc_url( $s['button_url']['url'] ) : '#';
			$target = ! empty( $s['button_url']['is_external'] ) ? '_blank' : $target;
		} elseif ( 'content' === $link_type ) {
			$content_id = absint( isset( $s['content_id'] ) ? $s['content_id'] : 0 );
			if ( $content_id > 0 ) {
				$permalink = get_permalink( $content_id );
				$url       = $permalink ? esc_url( $permalink ) : '#';
			}
		} elseif ( 'email' === $link_type ) {
			$email  = isset( $s['email_address'] ) ? sanitize_email( $s['email_address'] ) : '';
			$mailto = 'mailto:' . $email;
			$params = array();
			if ( ! empty( $s['email_subject'] ) ) {
				$params[] = 'subject=' . rawurlencode( wp_strip_all_tags( $s['email_subject'] ) );
			}
			if ( ! empty( $s['email_body'] ) ) {
				$params[] = 'body=' . rawurlencode( wp_strip_all_tags( $s['email_body'] ) );
			}
			if ( $params ) {
				$mailto .= '?' . implode( '&', $params );
			}
			$url = esc_attr( $mailto );
		} elseif ( 'media' === $link_type ) {
			$url = ! empty( $s['media_url']['url'] ) ? esc_url( $s['media_url']['url'] ) : '#';
		}

		// Collect shortcode attributes for the renderer.
		$atts = array(
			'text'             => ! empty( $s['button_text'] ) ? $s['button_text'] : 'Button',
			'font_family'      => ! empty( $s['font_family'] ) ? $s['font_family'] : 'inherit',
			'font_size'        => ! empty( $s['font_size'] ) ? $s['font_size'] : '16',
			'font_bold'        => ( ! empty( $s['font_bold'] ) && 'yes' === $s['font_bold'] ) ? '1' : '0',
			'font_italic'      => ( ! empty( $s['font_italic'] ) && 'yes' === $s['font_italic'] ) ? '1' : '0',
			'bg_color'         => ! empty( $s['bg_color'] ) ? $s['bg_color'] : '#007bff',
			'bg_hover_color'   => ! empty( $s['bg_hover_color'] ) ? $s['bg_hover_color'] : '#0056b3',
			'text_color'       => ! empty( $s['text_color'] ) ? $s['text_color'] : '#ffffff',
			'text_hover_color' => ! empty( $s['text_hover_color'] ) ? $s['text_hover_color'] : '#ffffff',
			'hover_grow'       => ! empty( $s['hover_grow'] ) ? $s['hover_grow'] : '1',
			'padding_v'        => isset( $s['padding_v'] ) ? $s['padding_v'] : '10',
			'padding_h'        => isset( $s['padding_h'] ) ? $s['padding_h'] : '20',
			'icon_type'        => ! empty( $s['icon_type'] ) ? $s['icon_type'] : 'none',
			'icon'             => ! empty( $s['icon'] ) ? $s['icon'] : '',
			'icon_media_url'   => ! empty( $s['icon_media_url']['url'] ) ? $s['icon_media_url']['url'] : '',
			'icon_size'        => ! empty( $s['icon_size'] ) ? $s['icon_size'] : '20',
			'icon_spacing'     => ! empty( $s['icon_spacing'] ) ? $s['icon_spacing'] : '8',
			'icon_position'    => ! empty( $s['icon_position'] ) ? $s['icon_position'] : 'before',
			'border_width'     => isset( $s['border_width'] ) ? $s['border_width'] : '0',
			'border_style'     => ! empty( $s['border_style'] ) ? $s['border_style'] : 'solid',
			'border_color'     => ! empty( $s['border_color'] ) ? $s['border_color'] : '#000000',
			'border_radius'    => isset( $s['border_radius'] ) ? $s['border_radius'] : '4',
			'shadow_enabled'   => ( ! empty( $s['shadow_enabled'] ) && 'yes' === $s['shadow_enabled'] ) ? '1' : '0',
			'shadow_x'         => isset( $s['shadow_x'] ) ? $s['shadow_x'] : '0',
			'shadow_y'         => isset( $s['shadow_y'] ) ? $s['shadow_y'] : '2',
			'shadow_blur'      => isset( $s['shadow_blur'] ) ? $s['shadow_blur'] : '4',
			'shadow_spread'    => isset( $s['shadow_spread'] ) ? $s['shadow_spread'] : '0',
			'shadow_color'     => ! empty( $s['shadow_color'] ) ? $s['shadow_color'] : 'rgba(0,0,0,0.3)',
			'link_type'        => $link_type,
			'url'              => ( 'url' === $link_type && '#' !== $url ) ? $url : '',
			'content_id'       => ( 'content' === $link_type ) ? absint( $s['content_id'] ?? 0 ) : '',
			'email'            => ( 'email' === $link_type ) ? ( $s['email_address'] ?? '' ) : '',
			'email_subject'    => ( 'email' === $link_type ) ? ( $s['email_subject'] ?? '' ) : '',
			'email_body'       => ( 'email' === $link_type ) ? ( $s['email_body'] ?? '' ) : '',
			'media_url'        => ( 'media' === $link_type ) ? ( $s['media_url']['url'] ?? '' ) : '',
			'target'           => $target,
		);

		// Build shortcode string and let EBP_Shortcode render it.
		$sc = '[eifelhoster_button';
		foreach ( $atts as $k => $v ) {
			if ( '' !== (string) $v ) {
				$sc .= ' ' . esc_attr( $k ) . '="' . esc_attr( (string) $v ) . '"';
			}
		}
		$sc .= ']';

		echo do_shortcode( $sc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// -------------------------------------------------------------------------
	// Live preview template (JavaScript / Mustache-like)
	// -------------------------------------------------------------------------
	protected function content_template() {
		?>
		<#
		var fontBold   = settings.font_bold   === 'yes' ? 'bold'   : 'normal';
		var fontItalic = settings.font_italic === 'yes' ? 'italic' : 'normal';
		var shadowOn   = settings.shadow_enabled === 'yes';
		var boxShadow  = shadowOn
			? (settings.shadow_x||0)+'px '+(settings.shadow_y||2)+'px '+(settings.shadow_blur||4)+'px '+(settings.shadow_spread||0)+'px '+(settings.shadow_color||'rgba(0,0,0,0.3)')
			: 'none';
		var gap = settings.icon_type !== 'none' ? (settings.icon_spacing||8)+'px' : '0';
		#>
		<a class="ebp-button" style="
			display:inline-flex;
			align-items:center;
			justify-content:center;
			text-decoration:none;
			cursor:pointer;
			font-family:{{ settings.font_family || 'inherit' }};
			font-size:{{ settings.font_size || 16 }}px;
			font-weight:{{ fontBold }};
			font-style:{{ fontItalic }};
			background-color:{{ settings.bg_color || '#007bff' }};
			color:{{ settings.text_color || '#ffffff' }};
			padding:{{ settings.padding_v || 10 }}px {{ settings.padding_h || 20 }}px;
			border-width:{{ settings.border_width || 0 }}px;
			border-style:{{ settings.border_style || 'solid' }};
			border-color:{{ settings.border_color || '#000000' }};
			border-radius:{{ settings.border_radius || 4 }}px;
			box-shadow:{{ boxShadow }};
			gap:{{ gap }};
			transition:background-color .3s,color .3s,transform .3s;
		">
			<# if ( settings.icon_type !== 'none' && settings.icon_position === 'before' ) { #>
				<# if ( settings.icon_type === 'dashicon' && settings.icon ) { #>
					<span class="dashicons dashicons-{{ settings.icon }}"
						style="font-size:{{ settings.icon_size||20 }}px;width:{{ settings.icon_size||20 }}px;height:{{ settings.icon_size||20 }}px;" aria-hidden="true"></span>
				<# } #>
			<# } #>
			<span class="ebp-btn-text">{{ settings.button_text || 'Button' }}</span>
			<# if ( settings.icon_type !== 'none' && settings.icon_position === 'after' ) { #>
				<# if ( settings.icon_type === 'dashicon' && settings.icon ) { #>
					<span class="dashicons dashicons-{{ settings.icon }}"
						style="font-size:{{ settings.icon_size||20 }}px;width:{{ settings.icon_size||20 }}px;height:{{ settings.icon_size||20 }}px;" aria-hidden="true"></span>
				<# } #>
			<# } #>
		</a>
		<?php
	}
}
