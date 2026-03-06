<?php
/**
 * Elementor widget: EH ButtonPro
 *
 * Provides all button-customisation options from the Classic Editor plugin
 * as a native Elementor widget registered under the "Eifelhoster" category.
 *
 * Standard Elementor controls are used wherever possible (Group_Control_Typography,
 * Group_Control_Border, Group_Control_Box_Shadow, DIMENSIONS, COLOR with Normal/Hover
 * tabs, etc.) so the widget feels native in the Elementor panel.
 * Plugin-specific features (e-mail links, media links, dashicons, hover-grow,
 * media icon) are added on top.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Elementor_Widget extends \Elementor\Widget_Base {

	// -------------------------------------------------------------------------
	// Widget identity
	// -------------------------------------------------------------------------

	public function get_name() {
		return 'eh-buttonpro';
	}

	public function get_title() {
		return esc_html__( 'EH ButtonPro', 'eifelhoster-buttons-pro' );
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

		// =====================================================================
		// CONTENT TAB
		// =====================================================================

		// ----- Section: Button content -----
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Inhalt', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'       => esc_html__( 'Button-Text', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Button', 'eifelhoster-buttons-pro' ),
				'placeholder' => esc_html__( 'Button-Text eingeben…', 'eifelhoster-buttons-pro' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();

		// ----- Section: Link & target -----
		$this->start_controls_section(
			'section_link',
			array(
				'label' => esc_html__( 'Link & Ziel', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => esc_html__( 'Linktyp', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'url',
				'options' => array(
					'url'   => 'URL',
					'email' => 'E-Mail',
					'media' => esc_html__( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		// URL – Elementor's native URL control (includes "Open in new tab" + "nofollow").
		$this->add_control(
			'btn_url',
			array(
				'label'         => 'URL',
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => 'https://…',
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition' => array( 'link_type' => 'url' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		// E-Mail fields.
		$this->add_control(
			'email',
			array(
				'label'       => esc_html__( 'E-Mail-Adresse', 'eifelhoster-buttons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'input_type'  => 'email',
				'placeholder' => 'email@beispiel.de',
				'condition'   => array( 'link_type' => 'email' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label'     => esc_html__( 'Betreff', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array( 'link_type' => 'email' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		$this->add_control(
			'email_body',
			array(
				'label'     => esc_html__( 'Nachrichtentext', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'condition' => array( 'link_type' => 'email' ),
				'dynamic'   => array( 'active' => true ),
			)
		);

		// Media file as link target.
		$this->add_control(
			'media_url',
			array(
				'label'     => esc_html__( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'link_type' => 'media' ),
			)
		);

		// "Open in new tab" for media links (URL links use the built-in is_external toggle).
		$this->add_control(
			'link_target',
			array(
				'label'     => esc_html__( 'In neuem Tab öffnen', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => array( 'link_type' => 'media' ),
			)
		);

		$this->end_controls_section();

		// ----- Section: Icon -----
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => esc_html__( 'Symbol (Icon)', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Symboltyp', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => esc_html__( 'Kein Symbol', 'eifelhoster-buttons-pro' ),
					'icon'     => esc_html__( 'Icon (Elementor)', 'eifelhoster-buttons-pro' ),
					'dashicon' => 'Dashicon (WordPress)',
					'media'    => esc_html__( 'Mediendatei', 'eifelhoster-buttons-pro' ),
				),
			)
		);

		// Elementor native icon picker (Font Awesome etc.).
		$this->add_control(
			'selected_icon',
			array(
				'label'     => esc_html__( 'Symbol auswählen', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array( 'value' => '', 'library' => 'solid' ),
				'condition' => array( 'icon_type' => 'icon' ),
			)
		);

		// WordPress Dashicon select.
		// ebp_get_dashicons() is always available – class-ebp-helpers.php is loaded
		// unconditionally in the main plugin file before any Elementor hooks fire.
		$dashicons_options = array( '' => esc_html__( '— Auswählen —', 'eifelhoster-buttons-pro' ) );
		foreach ( ebp_get_dashicons() as $slug ) {
			$dashicons_options[ $slug ] = $slug;
		}

		$this->add_control(
			'dashicon',
			array(
				'label'     => 'Dashicon',
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $dashicons_options,
				'default'   => '',
				'condition' => array( 'icon_type' => 'dashicon' ),
			)
		);

		// Media image as icon.
		$this->add_control(
			'icon_media',
			array(
				'label'     => esc_html__( 'Symbol-Mediendatei', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array( 'icon_type' => 'media' ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'Symbolgröße (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 20,
				'min'       => 8,
				'max'       => 120,
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'     => esc_html__( 'Abstand zum Text (px)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 8,
				'min'       => 0,
				'max'       => 60,
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => esc_html__( 'Symbolposition', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'before',
				'options' => array(
					'before' => array(
						'title' => esc_html__( 'Vor dem Text', 'eifelhoster-buttons-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'after' => array(
						'title' => esc_html__( 'Hinter dem Text', 'eifelhoster-buttons-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'condition' => array( 'icon_type!' => 'none' ),
			)
		);

		$this->end_controls_section();

		// =====================================================================
		// STYLE TAB
		// =====================================================================

		// ----- Section: Typography & spacing -----
		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => esc_html__( 'Text & Schrift', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .ebp-button',
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Innenabstand', 'eifelhoster-buttons-pro' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '20',
					'bottom'   => '10',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} .ebp-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// ----- Section: Colors (normal / hover tabs) -----
		$this->start_controls_section(
			'section_style_colors',
			array(
				'label' => esc_html__( 'Farben & Hover', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'color_tabs' );

		// Normal state.
		$this->start_controls_tab(
			'color_tab_normal',
			array( 'label' => esc_html__( 'Normal', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => esc_html__( 'Hintergrundfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#007bff',
				'selectors' => array(
					'{{WRAPPER}} .ebp-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Textfarbe', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ebp-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		// Hover state.
		$this->start_controls_tab(
			'color_tab_hover',
			array( 'label' => esc_html__( 'Hover', 'eifelhoster-buttons-pro' ) )
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'     => esc_html__( 'Hintergrundfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0056b3',
				'selectors' => array(
					'{{WRAPPER}} .ebp-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => esc_html__( 'Textfarbe (Hover)', 'eifelhoster-buttons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ebp-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_grow',
			array(
				'label'   => esc_html__( 'Grow bei Hover', 'eifelhoster-buttons-pro' ),
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 1,
						'max'  => 2,
						'step' => 0.01,
					),
				),
				'default'   => array( 'unit' => 'px', 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}} .ebp-button:hover' => 'transform: scale({{SIZE}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// ----- Section: Border & shadow -----
		$this->start_controls_section(
			'section_style_border',
			array(
				'label' => esc_html__( 'Rahmen & Schatten', 'eifelhoster-buttons-pro' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'selector' => '{{WRAPPER}} .ebp-button',
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Rahmenradius', 'eifelhoster-buttons-pro' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '4',
					'right'    => '4',
					'bottom'   => '4',
					'left'     => '4',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .ebp-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .ebp-button',
			)
		);

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// PHP render (server-side)
	// -------------------------------------------------------------------------

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Enqueue frontend assets (dashicons may not be loaded on the frontend by default).
		wp_enqueue_style( 'ebp-frontend' );
		wp_enqueue_style( 'dashicons' );

		// ---- Build href, target, rel ----
		$href   = '#';
		$target = '';
		$rel    = '';

		switch ( $settings['link_type'] ) {

			case 'url':
				if ( ! empty( $settings['btn_url']['url'] ) ) {
					$href = esc_url( $settings['btn_url']['url'] );
				}
				if ( ! empty( $settings['btn_url']['is_external'] ) ) {
					$target = '_blank';
					$rel    = 'noopener noreferrer';
				}
				if ( ! empty( $settings['btn_url']['nofollow'] ) ) {
					$rel .= $rel ? ' nofollow' : 'nofollow';
				}
				break;

			case 'email':
				if ( ! empty( $settings['email'] ) ) {
					$email  = sanitize_email( $settings['email'] );
					$mailto = 'mailto:' . $email;
					$params = array();
					if ( ! empty( $settings['email_subject'] ) ) {
						$params[] = 'subject=' . rawurlencode( wp_strip_all_tags( $settings['email_subject'] ) );
					}
					if ( ! empty( $settings['email_body'] ) ) {
						$params[] = 'body=' . rawurlencode( wp_strip_all_tags( $settings['email_body'] ) );
					}
					if ( $params ) {
						$mailto .= '?' . implode( '&', $params );
					}
					$href = esc_attr( $mailto );
				}
				break;

			case 'media':
				if ( ! empty( $settings['media_url']['url'] ) ) {
					$href = esc_url( $settings['media_url']['url'] );
				}
				if ( ! empty( $settings['link_target'] ) ) {
					$target = '_blank';
					$rel    = 'noopener noreferrer';
				}
				break;
		}

		// ---- Build icon HTML ----
		$icon_html = '';

		switch ( $settings['icon_type'] ) {

			case 'icon':
				if ( ! empty( $settings['selected_icon']['value'] ) ) {
					$icon_size = absint( $settings['icon_size'] );
					ob_start();
					\Elementor\Icons_Manager::render_icon(
						$settings['selected_icon'],
						array( 'aria-hidden' => 'true' )
					);
					$raw_icon  = ob_get_clean();
					$icon_html = '<span class="ebp-el-icon" style="font-size:' . $icon_size . 'px;width:' . $icon_size . 'px;height:' . $icon_size . 'px;display:inline-flex;align-items:center;" aria-hidden="true">' . $raw_icon . '</span>';
				}
				break;

			case 'dashicon':
				if ( ! empty( $settings['dashicon'] ) ) {
					$sz        = absint( $settings['icon_size'] );
					$icon_html = '<span class="dashicons dashicons-' . esc_attr( $settings['dashicon'] ) . '" '
						. 'style="font-size:' . $sz . 'px;width:' . $sz . 'px;height:' . $sz . 'px;" aria-hidden="true"></span>';
				}
				break;

			case 'media':
				if ( ! empty( $settings['icon_media']['url'] ) ) {
					$sz        = absint( $settings['icon_size'] );
					$icon_html = '<img src="' . esc_url( $settings['icon_media']['url'] ) . '" '
						. 'style="width:' . $sz . 'px;height:' . $sz . 'px;" alt="" aria-hidden="true" />';
				}
				break;
		}

		// ---- Inline styles: layout + transition (not covered by group controls) ----
		$inline_parts = array(
			'display:inline-flex',
			'align-items:center',
			'justify-content:center',
			'text-decoration:none',
			'cursor:pointer',
			'transition:background-color .3s,color .3s,transform .3s',
		);
		if ( 'none' !== $settings['icon_type'] && $icon_html ) {
			$inline_parts[] = 'gap:' . absint( $settings['icon_spacing'] ) . 'px';
		}
		$inline_style = implode( ';', $inline_parts ) . ';';

		// ---- Register render attributes ----
		$this->add_render_attribute( 'button', 'class', 'ebp-button' );
		$this->add_render_attribute( 'button', 'href', $href );
		$this->add_render_attribute( 'button', 'style', $inline_style );
		if ( $target ) {
			$this->add_render_attribute( 'button', 'target', $target );
		}
		if ( $rel ) {
			$this->add_render_attribute( 'button', 'rel', $rel );
		}

		$text_span     = '<span class="ebp-btn-text">' . esc_html( $settings['btn_text'] ) . '</span>';
		$icon_position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'before';
		?>
		<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			if ( 'before' === $icon_position ) {
				echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $text_span; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo $text_span; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</a>
		<?php
	}

	// -------------------------------------------------------------------------
	// JS preview template (Elementor editor live preview)
	// -------------------------------------------------------------------------

	protected function content_template() {
		?>
		<#
		var iconHtml = '';
		var iconSize = settings.icon_size || 20;

		if ( 'dashicon' === settings.icon_type && settings.dashicon ) {
			iconHtml = '<span class="dashicons dashicons-' + settings.dashicon + '" style="font-size:' + iconSize + 'px;width:' + iconSize + 'px;height:' + iconSize + 'px;" aria-hidden="true"></span>';
		} else if ( 'media' === settings.icon_type && settings.icon_media && settings.icon_media.url ) {
			iconHtml = '<img src="' + _.escape( settings.icon_media.url ) + '" style="width:' + iconSize + 'px;height:' + iconSize + 'px;" alt="" aria-hidden="true" />';
		} else if ( 'icon' === settings.icon_type && settings.selected_icon && settings.selected_icon.value ) {
			iconHtml = '<span class="ebp-el-icon" style="font-size:' + iconSize + 'px;" aria-hidden="true"><i class="' + _.escape( settings.selected_icon.value ) + '"></i></span>';
		}

		var gap         = ( 'none' !== settings.icon_type && iconHtml ) ? 'gap:' + ( settings.icon_spacing || 8 ) + 'px;' : '';
		var inlineStyle = 'display:inline-flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:background-color .3s,color .3s,transform .3s;' + gap;
		var textSpan    = '<span class="ebp-btn-text">' + _.escape( settings.btn_text ) + '</span>';
		var iconPos     = settings.icon_position || 'before';
		#>
		<a class="ebp-button" href="#" style="{{ inlineStyle }}">
			<# if ( 'before' === iconPos ) { #>
				{{{ iconHtml }}}
				{{{ textSpan }}}
			<# } else { #>
				{{{ textSpan }}}
				{{{ iconHtml }}}
			<# } #>
		</a>
		<?php
	}
}
