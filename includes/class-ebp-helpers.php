<?php
/**
 * Helper functions for Eifelhoster Buttons Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the merged defaults (saved options + fallback values).
 *
 * @return array
 */
function ebp_get_defaults() {
	$fallback = array(
		'font_family'      => 'inherit',
		'font_size'        => '16',
		'font_bold'        => '0',
		'font_italic'      => '0',
		'button_width'     => '',
		'bg_color'         => '#007bff',
		'bg_hover_color'   => '#0056b3',
		'text_color'       => '#ffffff',
		'text_hover_color' => '#ffffff',
		'hover_grow'       => '1',
		'padding_v'        => '10',
		'padding_h'        => '20',
		'icon_type'        => 'none',
		'icon'             => '',
		'icon_media_url'   => '',
		'icon_size'        => '20',
		'icon_spacing'     => '8',
		'icon_position'    => 'before',
		'border_width'     => '0',
		'border_style'     => 'solid',
		'border_color'     => '#000000',
		'border_radius'    => '4',
		'shadow_enabled'   => '0',
		'shadow_x'         => '0',
		'shadow_y'         => '2',
		'shadow_blur'      => '4',
		'shadow_spread'    => '0',
		'shadow_color'     => 'rgba(0,0,0,0.3)',
		'link_type'        => 'url',
		'url'              => '',
		'email'            => '',
		'email_subject'    => '',
		'email_body'       => '',
		'media_url'        => '',
		'target'           => '_self',
	);

	$saved = get_option( EBP_OPTION_KEY, array() );

	return wp_parse_args( $saved, $fallback );
}

/**
 * Sanitise a CSS colour value so it is safe to embed in a <style> tag.
 *
 * Accepts hex colours, rgb(), rgba(), hsl(), hsla(), and CSS named colours.
 * Returns '#000000' for any value that does not match a known-safe pattern.
 *
 * @param  string $color Raw colour value.
 * @return string        Sanitised colour value.
 */
function ebp_sanitize_css_color( $color ) {
	$color = trim( (string) $color );

	// Hex colour: #rgb or #rrggbb.
	if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color ) ) {
		return $color;
	}

	// rgb() / rgba() with integers and optional alpha.
	if ( preg_match(
		'/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+)\s*)?\)$/',
		$color,
		$m
	) ) {
		$r = min( 255, (int) $m[1] );
		$g = min( 255, (int) $m[2] );
		$b = min( 255, (int) $m[3] );
		if ( isset( $m[4] ) && '' !== $m[4] ) {
			$a = max( 0.0, min( 1.0, (float) $m[4] ) );
			return "rgba($r,$g,$b,$a)";
		}
		return "rgb($r,$g,$b)";
	}

	// hsl() / hsla().
	if ( preg_match(
		'/^hsla?\(\s*(\d{1,3})\s*,\s*(\d{1,3})%\s*,\s*(\d{1,3})%\s*(?:,\s*([\d.]+)\s*)?\)$/',
		$color,
		$m
	) ) {
		$h = min( 360, (int) $m[1] );
		$s = min( 100, (int) $m[2] );
		$l = min( 100, (int) $m[3] );
		if ( isset( $m[4] ) && '' !== $m[4] ) {
			$a = max( 0.0, min( 1.0, (float) $m[4] ) );
			return "hsla($h,$s%,$l%,$a)";
		}
		return "hsl($h,$s%,$l%)";
	}

	// CSS named colours (letters only, no whitespace, no special chars).
	if ( preg_match( '/^[a-zA-Z]+$/', $color ) ) {
		return sanitize_key( $color );
	}

	return '#000000';
}

/**
 * Sanitise a CSS font-family value to prevent CSS injection.
 *
 * Strips characters that could break out of the CSS value context.
 *
 * @param  string $font_family Raw font-family string.
 * @return string              Sanitised font-family string.
 */
function ebp_sanitize_font_family( $font_family ) {
	// Remove characters that could break out of a CSS string or inject CSS.
	return preg_replace( '/[{}<>;\\/]/', '', sanitize_text_field( $font_family ) );
}

/**
 * Returns an array of all Dashicon slugs (without the "dashicons-" prefix).
 *
 * @return array
 */
function ebp_get_dashicons() {
	return array(
		'admin-appearance', 'admin-collapse', 'admin-comments', 'admin-customizer',
		'admin-generic', 'admin-home', 'admin-links', 'admin-media', 'admin-multisite',
		'admin-network', 'admin-page', 'admin-plugins', 'admin-post', 'admin-settings',
		'admin-site', 'admin-site-alt', 'admin-site-alt2', 'admin-site-alt3',
		'admin-tools', 'admin-users',
		'album', 'align-center', 'align-full-width', 'align-left', 'align-none',
		'align-pull-left', 'align-pull-right', 'align-right', 'align-wide',
		'analytics', 'archive', 'arrow-down', 'arrow-down-alt', 'arrow-down-alt2',
		'arrow-left', 'arrow-left-alt', 'arrow-left-alt2', 'arrow-right',
		'arrow-right-alt', 'arrow-right-alt2', 'arrow-up', 'arrow-up-alt',
		'arrow-up-alt2', 'art', 'awards',
		'backup', 'bank', 'bell', 'block-default', 'book', 'book-alt', 'building',
		'buddicons-activity', 'buddicons-bbpress-logo', 'buddicons-community',
		'buddicons-forums', 'buddicons-friends', 'buddicons-groups',
		'buddicons-pm', 'buddicons-replies', 'buddicons-topics', 'buddicons-tracking',
		'businessman', 'businessperson', 'businesswoman',
		'button', 'calculator', 'calendar', 'calendar-alt', 'camera',
		'camera-alt', 'carrot', 'cart', 'category', 'chart-area', 'chart-bar',
		'chart-line', 'chart-pie', 'clipboard', 'clock', 'cloud', 'cloud-saved',
		'cloud-upload', 'code-standards', 'coffee', 'color-picker',
		'columns', 'controls-back', 'controls-forward', 'controls-pause',
		'controls-play', 'controls-repeat', 'controls-skipback', 'controls-skipforward',
		'controls-stop', 'controls-volumeoff', 'controls-volumeon', 'cover-image',
		'dashboard', 'database', 'database-add', 'database-export',
		'database-import', 'database-remove', 'database-view',
		'desktop', 'desktop-alt', 'dismiss', 'download',
		'edit', 'edit-large', 'edit-page',
		'editor-aligncenter', 'editor-alignleft', 'editor-alignright',
		'editor-bold', 'editor-break', 'editor-code', 'editor-contract',
		'editor-customchar', 'editor-expand', 'editor-help', 'editor-indent',
		'editor-insertmore', 'editor-italic', 'editor-justify', 'editor-kitchensink',
		'editor-ltr', 'editor-ol', 'editor-ol-rtl', 'editor-outdent',
		'editor-paragraph', 'editor-paste-text', 'editor-paste-word',
		'editor-quote', 'editor-removeformatting', 'editor-rtl', 'editor-spellcheck',
		'editor-strikethrough', 'editor-table', 'editor-textcolor', 'editor-ul',
		'editor-underline', 'editor-unlink', 'editor-video',
		'email', 'email-alt', 'email-alt2', 'email-open',
		'embed-audio', 'embed-generic', 'embed-photo', 'embed-post', 'embed-video',
		'excerpt-view', 'exit', 'external',
		'facebook', 'facebook-alt', 'feedback', 'filter', 'flag',
		'format-audio', 'format-chat', 'format-gallery', 'format-image',
		'format-links', 'format-quote', 'format-status', 'format-video',
		'fullscreen-alt', 'fullscreen-exit-alt', 'games', 'google',
		'grid-view', 'groups', 'hammer', 'heart', 'hidden', 'home', 'hourglass',
		'html', 'id', 'id-alt', 'image-crop', 'image-filter', 'image-flip-horizontal',
		'image-flip-vertical', 'image-rotate', 'image-rotate-left', 'image-rotate-right',
		'images-alt', 'images-alt2', 'index-card', 'info', 'info-outline',
		'insert', 'insert-after', 'insert-before', 'instagram',
		'laptop', 'layout', 'leftright', 'lightbulb', 'list-view',
		'location', 'location-alt', 'lock', 'lock-duplicate',
		'marker', 'media-archive', 'media-audio', 'media-code', 'media-default',
		'media-document', 'media-interactive', 'media-spreadsheet',
		'media-text', 'media-video', 'megaphone', 'menu', 'menu-alt',
		'menu-alt2', 'menu-alt3', 'microphone', 'migrate', 'minus',
		'money', 'money-alt', 'move', 'nametag', 'networking',
		'no', 'no-alt', 'open-folder', 'palmtree', 'paperclip',
		'performance', 'phone', 'pinterest', 'playlist-audio', 'playlist-video',
		'plus', 'plus-alt', 'plus-alt2', 'podio', 'portfolio', 'post-status',
		'pressthis', 'printer', 'privacy', 'products',
		'randomize', 'reddit', 'redo', 'rest-api', 'rss',
		'saved', 'schedule', 'screenoptions', 'search', 'share',
		'share-alt', 'share-alt2', 'shield', 'shield-alt', 'shortcode',
		'slides', 'smartphone', 'smiley', 'sort', 'sos',
		'star-empty', 'star-filled', 'star-half', 'sticky', 'store',
		'superhero', 'superhero-alt', 'table-col-after', 'table-col-before',
		'table-col-delete', 'table-row-after', 'table-row-before', 'table-row-delete',
		'tablet', 'tag', 'tagcloud', 'testimonial', 'text', 'text-page',
		'thumbs-down', 'thumbs-up', 'tickets', 'tickets-alt', 'tide',
		'translation', 'trash', 'twitter', 'twitter-alt', 'unarchive', 'undo',
		'universal-access', 'universal-access-alt', 'unlock', 'update', 'update-alt',
		'upload', 'vault', 'video-alt', 'video-alt2', 'video-alt3',
		'visibility', 'warning',
		'welcome-add-page', 'welcome-comments', 'welcome-learn-more',
		'welcome-view-site', 'welcome-widgets-menus',
		'wordpress', 'wordpress-alt', 'xing', 'yes', 'yes-alt',
	);
}
