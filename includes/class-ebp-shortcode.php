<?php
/**
 * Shortcode handler for [eifelhoster_button …]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EBP_Shortcode {

	/** Counter to generate unique IDs per page. */
	private static $counter = 0;

	public function __construct() {
		add_shortcode( 'eifelhoster_button', array( $this, 'render' ) );
		// Enqueue frontend CSS once per page.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
	}

	public function enqueue_frontend() {
		wp_enqueue_style(
			'ebp-frontend',
			EBP_PLUGIN_URL . 'assets/css/ebp-frontend.css',
			array(),
			EBP_VERSION
		);
		// Dashicons are already bundled in WP but not loaded on the frontend by default.
		wp_enqueue_style( 'dashicons' );
	}

	/**
	 * Render shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML
	 */
	public function render( $atts ) {
		$defaults = ebp_get_defaults();

		$a = shortcode_atts( array(
			'text'             => 'Button',
			'font_family'      => $defaults['font_family'],
			'font_size'        => $defaults['font_size'],
			'font_bold'        => $defaults['font_bold'],
			'font_italic'      => $defaults['font_italic'],
			'bg_color'         => $defaults['bg_color'],
			'bg_hover_color'   => $defaults['bg_hover_color'],
			'text_color'       => $defaults['text_color'],
			'text_hover_color' => $defaults['text_hover_color'],
			'hover_grow'       => $defaults['hover_grow'],
			'padding_v'        => $defaults['padding_v'],
			'padding_h'        => $defaults['padding_h'],
			'button_width'     => $defaults['button_width'],
			'icon_type'        => $defaults['icon_type'],
			'icon'             => $defaults['icon'],
			'icon_media_url'   => $defaults['icon_media_url'],
			'icon_size'        => $defaults['icon_size'],
			'icon_spacing'     => $defaults['icon_spacing'],
			'icon_position'    => $defaults['icon_position'],
			'border_width'     => $defaults['border_width'],
			'border_style'     => $defaults['border_style'],
			'border_color'     => $defaults['border_color'],
			'border_radius'    => $defaults['border_radius'],
			'shadow_enabled'   => $defaults['shadow_enabled'],
			'shadow_x'         => $defaults['shadow_x'],
			'shadow_y'         => $defaults['shadow_y'],
			'shadow_blur'      => $defaults['shadow_blur'],
			'shadow_spread'    => $defaults['shadow_spread'],
			'shadow_color'     => $defaults['shadow_color'],
			'link_type'        => $defaults['link_type'],
			'url'              => $defaults['url'],
			'email'            => $defaults['email'],
			'email_subject'    => $defaults['email_subject'],
			'email_body'       => $defaults['email_body'],
			'media_url'        => $defaults['media_url'],
			'target'           => $defaults['target'],
		), $atts, 'eifelhoster_button' );

		self::$counter++;
		$id = 'ebp-btn-' . self::$counter;

		// ---- Validate whitelisted string fields ----
		$a['target'] = in_array( $a['target'], array( '_self', '_blank' ), true )
			? $a['target'] : '_self';
		$a['icon_position'] = in_array( $a['icon_position'], array( 'before', 'after' ), true )
			? $a['icon_position'] : 'before';

		// ---- Build href ----
		$href = '#';
		if ( 'url' === $a['link_type'] ) {
			$href = esc_url( $a['url'] );
		} elseif ( 'email' === $a['link_type'] ) {
			// The email address itself must NOT be percent-encoded in mailto URIs.
			$email  = sanitize_email( $a['email'] );
			$mailto = 'mailto:' . $email;
			$params = array();
			if ( ! empty( $a['email_subject'] ) ) {
				$params[] = 'subject=' . rawurlencode( wp_strip_all_tags( $a['email_subject'] ) );
			}
			if ( ! empty( $a['email_body'] ) ) {
				$params[] = 'body=' . rawurlencode( wp_strip_all_tags( $a['email_body'] ) );
			}
			if ( $params ) {
				$mailto .= '?' . implode( '&', $params );
			}
			$href = esc_attr( $mailto );
		} elseif ( 'media' === $a['link_type'] ) {
			$href = esc_url( $a['media_url'] );
		}

		// ---- Build inline style ----
		$inline = array(
			'display'        => 'inline-flex',
			'align-items'    => 'center',
			'justify-content'=> 'center',
			'text-decoration'=> 'none',
			'cursor'         => 'pointer',
			'transition'     => 'background-color .3s,color .3s,transform .3s',
		);

		if ( $a['font_family'] && 'inherit' !== $a['font_family'] ) {
			$inline['font-family'] = ebp_sanitize_font_family( $a['font_family'] );
		}
		if ( $a['font_size'] ) {
			$inline['font-size'] = absint( $a['font_size'] ) . 'px';
		}
		$inline['font-weight']  = ( '1' === $a['font_bold']   ) ? 'bold'   : 'normal';
		$inline['font-style']   = ( '1' === $a['font_italic']  ) ? 'italic' : 'normal';
		$inline['background-color'] = ebp_sanitize_css_color( $a['bg_color'] );
		$inline['color']            = ebp_sanitize_css_color( $a['text_color'] );
		$inline['padding']          = absint( $a['padding_v'] ) . 'px ' . absint( $a['padding_h'] ) . 'px';
		if ( absint( $a['button_width'] ) > 0 ) {
			$inline['width'] = absint( $a['button_width'] ) . 'px';
		}
		$inline['border-width']     = absint( $a['border_width'] ) . 'px';
		$inline['border-style']     = in_array( $a['border_style'], array( 'solid','dashed','dotted','double','none' ), true )
			? $a['border_style'] : 'solid';
		$inline['border-color']     = ebp_sanitize_css_color( $a['border_color'] );
		$inline['border-radius']    = absint( $a['border_radius'] ) . 'px';

		if ( '1' === $a['shadow_enabled'] ) {
			$inline['box-shadow'] = implode( ' ', array(
				intval( $a['shadow_x'] ) . 'px',
				intval( $a['shadow_y'] ) . 'px',
				absint( $a['shadow_blur'] ) . 'px',
				intval( $a['shadow_spread'] ) . 'px',
				ebp_sanitize_css_color( $a['shadow_color'] ),
			) );
		}

		if ( 'none' !== $a['icon_type'] ) {
			$inline['gap'] = absint( $a['icon_spacing'] ) . 'px';
		}

		$style_str = '';
		foreach ( $inline as $prop => $val ) {
			$style_str .= $prop . ':' . esc_attr( $val ) . ';';
		}

		// ---- Build hover CSS ----
		$hover_bg    = ebp_sanitize_css_color( $a['bg_hover_color'] );
		$hover_color = ebp_sanitize_css_color( $a['text_hover_color'] );
		$grow        = (float) $a['hover_grow'];
		$grow        = max( 1.0, min( 2.0, $grow ) );

		$hover_css = '#' . esc_attr( $id ) . '{transition:background-color .3s,color .3s,transform .3s}'
			. '#' . esc_attr( $id ) . ':hover{background-color:' . $hover_bg . '!important;color:' . $hover_color . '!important;'
			. ( $grow > 1.001 ? 'transform:scale(' . number_format( $grow, 2, '.', '' ) . ')!important;' : '' )
			. '}';

		// ---- Build icon HTML ----
		$icon_html = '';
		if ( 'dashicon' === $a['icon_type'] && ! empty( $a['icon'] ) ) {
			$sz        = absint( $a['icon_size'] );
			$icon_html = '<span class="dashicons dashicons-' . esc_attr( $a['icon'] ) . '" '
				. 'style="font-size:' . $sz . 'px;width:' . $sz . 'px;height:' . $sz . 'px;" aria-hidden="true"></span>';
		} elseif ( 'media' === $a['icon_type'] && ! empty( $a['icon_media_url'] ) ) {
			$sz        = absint( $a['icon_size'] );
			$icon_html = '<img src="' . esc_url( $a['icon_media_url'] ) . '" '
				. 'style="width:' . $sz . 'px;height:' . $sz . 'px;" alt="" aria-hidden="true" />';
		}

		// ---- Assemble output ----
		$rel = ( '_blank' === $a['target'] ) ? ' rel="noopener noreferrer"' : '';

		$output  = '<style>' . $hover_css . '</style>';
		$output .= '<a id="' . esc_attr( $id ) . '" href="' . $href . '" '
			. 'target="' . esc_attr( $a['target'] ) . '"' . $rel . ' '
			. 'style="' . $style_str . '" class="ebp-button">';

		$text_span = '<span class="ebp-btn-text">' . esc_html( $a['text'] ) . '</span>';

		if ( 'before' === $a['icon_position'] ) {
			$output .= $icon_html . $text_span;
		} else {
			$output .= $text_span . $icon_html;
		}

		$output .= '</a>';

		return $output;
	}
}
