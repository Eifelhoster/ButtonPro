/**
 * Dialog UI for Eifelhoster Buttons Pro.
 *
 * Provides  window.ebpOpenDialog( editor )  which is called by the TinyMCE plugin.
 */
/* global jQuery, ebpData, wp */
(function ( $ ) {
	'use strict';

	var currentEditor    = null;
	var currentShortcode = null;  // non-null when editing an existing button
	var mediaFrameIcon   = null;
	var mediaFrameLink   = null;

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------
	window.ebpOpenDialog = function ( editor, shortcode ) {
		currentEditor    = editor;
		currentShortcode = shortcode || null;

		if ( currentShortcode ) {
			ebpPopulateForm( ebpParseShortcode( currentShortcode ) );
			$( '#ebp-modal-title-text' ).text( ebpData.i18n.titleEdit );
			$( '#ebp-btn-insert-label' ).text( ebpData.i18n.update );
		} else {
			ebpResetForm();
			$( '#ebp-modal-title-text' ).text( ebpData.i18n.title );
			$( '#ebp-btn-insert-label' ).text( ebpData.i18n.insert );
		}

		$( '#ebp-modal-overlay' ).fadeIn( 150 );
		$( '#ebp-f-text' ).trigger( 'focus' );
	};

	// -------------------------------------------------------------------------
	// Tiny HTML-escape helper (for dynamic DOM insertion)
	// -------------------------------------------------------------------------
	function ebpEscHtml( str ) {
		return String( str )
			.replace( /&/g,  '&amp;' )
			.replace( /</g,  '&lt;'  )
			.replace( />/g,  '&gt;'  )
			.replace( /"/g,  '&quot;' )
			.replace( /'/g,  '&#039;' );
	}

	// -------------------------------------------------------------------------
	// Init
	// -------------------------------------------------------------------------
	$( document ).ready( function () {

		// Build dashicon grid (done once).
		ebpBuildIconGrid( '#ebp-dlg-icon-grid', '#ebp-f-icon', '#ebp-dlg-icon-preview', '#ebp-dlg-icon-search' );

		// Init colour pickers.
		$( '.ebp-dialog-color' ).wpColorPicker( { change: ebpUpdatePreview } );

		// Tabs.
		$( '#ebp-modal-tabs' ).on( 'click', '.ebp-modal-tab', function () {
			var tab = $( this ).data( 'tab' );
			$( '.ebp-modal-tab' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			$( '.ebp-modal-panel' ).removeClass( 'active' );
			$( '#ebp-panel-' + tab ).addClass( 'active' );
		} );

		// Close button / overlay click.
		$( '#ebp-modal-close, #ebp-btn-cancel' ).on( 'click', ebpCloseDialog );
		$( '#ebp-modal-overlay' ).on( 'click', function ( e ) {
			if ( $( e.target ).is( '#ebp-modal-overlay' ) ) {
				ebpCloseDialog();
			}
		} );
		$( document ).on( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && $( '#ebp-modal-overlay' ).is( ':visible' ) ) {
				ebpCloseDialog();
			}
		} );

		// Insert button.
		$( '#ebp-btn-insert' ).on( 'click', ebpInsertShortcode );

		// Icon type toggle.
		$( document ).on( 'change', 'input[name="ebp-icon-type"]', function () {
			var val = $( this ).val();
			$( '#ebp-dlg-row-dashicon' ).toggle( val === 'dashicon' );
			$( '#ebp-dlg-row-media-icon' ).toggle( val === 'media' );
			ebpUpdatePreview();
		} );

		// Link type toggle.
		$( document ).on( 'change', 'input[name="ebp-link-type"]', function () {
			ebpToggleLinkFields( $( this ).val() );
		} );

		// Shadow toggle.
		$( '#ebp-f-shadow-enabled' ).on( 'change', function () {
			$( '#ebp-dlg-shadow-fields' ).toggle( this.checked );
			ebpUpdatePreview();
		} );

		// Hover grow range ↔ number sync.
		$( '#ebp-f-hover-grow-range' ).on( 'input', function () {
			$( '#ebp-f-hover-grow' ).val( this.value );
			ebpUpdatePreview();
		} );
		$( '#ebp-f-hover-grow' ).on( 'input', function () {
			$( '#ebp-f-hover-grow-range' ).val( this.value );
			ebpUpdatePreview();
		} );

		// Live preview on any input change.
		$( '#ebp-modal-body' ).on( 'input change', 'input, select, textarea', function () {
			ebpUpdatePreview();
		} );

		// Media picker – link/media.
		$( '#ebp-dlg-select-media' ).on( 'click', function () {
			if ( mediaFrameLink ) {
				mediaFrameLink.open();
				return;
			}
			mediaFrameLink = wp.media( {
				title   : ebpData.i18n.selectMedia,
				button  : { text: ebpData.i18n.use },
				multiple: false,
			} );
			mediaFrameLink.on( 'select', function () {
				var attachment = mediaFrameLink.state().get( 'selection' ).first().toJSON();
				$( '#ebp-f-media-url' ).val( attachment.url );
				$( '#ebp-dlg-media-preview' ).html(
					'<span class="dashicons dashicons-media-default" style="vertical-align:middle"></span> ' +
					'<a href="' + ebpEscHtml( attachment.url ) + '" target="_blank" rel="noopener">' +
					ebpEscHtml( attachment.filename ) + '</a>'
				);
				ebpUpdatePreview();
			} );
			mediaFrameLink.open();
		} );

		// Media picker – icon image.
		$( '#ebp-dlg-select-icon-media' ).on( 'click', function () {
			if ( mediaFrameIcon ) {
				mediaFrameIcon.open();
				return;
			}
			mediaFrameIcon = wp.media( {
				title   : ebpData.i18n.selectFile,
				button  : { text: ebpData.i18n.use },
				multiple: false,
				library : { type: 'image' },
			} );
			mediaFrameIcon.on( 'select', function () {
				var attachment = mediaFrameIcon.state().get( 'selection' ).first().toJSON();
				$( '#ebp-f-icon-media-url' ).val( attachment.url );
				$( '#ebp-dlg-icon-media-preview' ).html(
					'<img src="' + ebpEscHtml( attachment.url ) + '" style="max-height:48px;margin-top:4px" />'
				);
				ebpUpdatePreview();
			} );
			mediaFrameIcon.open();
		} );
	} );

	// -------------------------------------------------------------------------
	// Reset / populate form with defaults
	// -------------------------------------------------------------------------
	function ebpResetForm() {
		var d = ebpData.defaults;

		// Text & Font tab.
		$( '#ebp-f-text' ).val( 'Button' );
		$( '#ebp-f-font-family' ).val( d.font_family );
		$( '#ebp-f-font-size' ).val( d.font_size );
		$( '#ebp-f-font-bold' ).prop( 'checked', d.font_bold === '1' );
		$( '#ebp-f-font-italic' ).prop( 'checked', d.font_italic === '1' );
		$( '#ebp-f-padding-v' ).val( d.padding_v );
		$( '#ebp-f-padding-h' ).val( d.padding_h );

		// Colors tab.
		ebpSetColor( '#ebp-f-bg-color',         d.bg_color );
		ebpSetColor( '#ebp-f-bg-hover-color',   d.bg_hover_color );
		ebpSetColor( '#ebp-f-text-color',       d.text_color );
		ebpSetColor( '#ebp-f-text-hover-color', d.text_hover_color );
		$( '#ebp-f-hover-grow' ).val( d.hover_grow );
		$( '#ebp-f-hover-grow-range' ).val( d.hover_grow );

		// Icon tab.
		$( 'input[name="ebp-icon-type"][value="' + d.icon_type + '"]' ).prop( 'checked', true );
		$( '#ebp-dlg-row-dashicon' ).toggle( d.icon_type === 'dashicon' );
		$( '#ebp-dlg-row-media-icon' ).toggle( d.icon_type === 'media' );
		$( '#ebp-f-icon' ).val( d.icon );
		$( '#ebp-f-icon-media-url' ).val( d.icon_media_url );
		$( '#ebp-f-icon-size' ).val( d.icon_size );
		$( '#ebp-f-icon-spacing' ).val( d.icon_spacing );
		$( 'input[name="ebp-icon-pos"][value="' + d.icon_position + '"]' ).prop( 'checked', true );

		// Update icon preview if a default dashicon is set.
		if ( d.icon_type === 'dashicon' && d.icon ) {
			$( '#ebp-dlg-icon-preview' ).html(
				'<span class="dashicons dashicons-' + d.icon + '" style="font-size:24px;width:24px;height:24px"></span>' +
				' <span style="font-size:11px;color:#666">' + d.icon + '</span>'
			);
		} else {
			$( '#ebp-dlg-icon-preview' ).html( '' );
		}

		if ( d.icon_type === 'media' && d.icon_media_url ) {
			$( '#ebp-dlg-icon-media-preview' ).html(
				'<img src="' + d.icon_media_url + '" style="max-height:48px" />'
			);
		} else {
			$( '#ebp-dlg-icon-media-preview' ).html( '' );
		}

		// Border & Shadow tab.
		$( '#ebp-f-border-width' ).val( d.border_width );
		$( '#ebp-f-border-style' ).val( d.border_style );
		ebpSetColor( '#ebp-f-border-color', d.border_color );
		$( '#ebp-f-border-radius' ).val( d.border_radius );
		$( '#ebp-f-shadow-enabled' ).prop( 'checked', d.shadow_enabled === '1' );
		$( '#ebp-dlg-shadow-fields' ).toggle( d.shadow_enabled === '1' );
		$( '#ebp-f-shadow-x' ).val( d.shadow_x );
		$( '#ebp-f-shadow-y' ).val( d.shadow_y );
		$( '#ebp-f-shadow-blur' ).val( d.shadow_blur );
		$( '#ebp-f-shadow-spread' ).val( d.shadow_spread );
		$( '#ebp-f-shadow-color' ).val( d.shadow_color );

		// Link tab.
		$( 'input[name="ebp-link-type"][value="' + d.link_type + '"]' ).prop( 'checked', true );
		ebpToggleLinkFields( d.link_type );
		$( '#ebp-f-url' ).val( d.url );
		$( '#ebp-f-email' ).val( d.email );
		$( '#ebp-f-email-subject' ).val( d.email_subject );
		$( '#ebp-f-email-body' ).val( d.email_body );
		$( '#ebp-f-media-url' ).val( d.media_url );
		$( '#ebp-dlg-media-preview' ).html( '' );
		$( 'input[name="ebp-target"][value="' + d.target + '"]' ).prop( 'checked', true );

		// Reset to first tab.
		$( '.ebp-modal-tab' ).removeClass( 'active' );
		$( '.ebp-modal-tab[data-tab="text"]' ).addClass( 'active' );
		$( '.ebp-modal-panel' ).removeClass( 'active' );
		$( '#ebp-panel-text' ).addClass( 'active' );

		ebpUpdatePreview();
	}

	// -------------------------------------------------------------------------
	// Helper: set WP colour picker value
	// -------------------------------------------------------------------------
	function ebpSetColor( selector, value ) {
		var $el = $( selector );
		$el.val( value );
		if ( $el.hasClass( 'wp-color-picker' ) || $el.siblings( '.wp-color-picker' ).length ) {
			$el.wpColorPicker( 'color', value );
		}
	}

	// -------------------------------------------------------------------------
	// Parse a raw [eifelhoster_button key="value" ...] string into an attrs map.
	// HTML-encoded &quot; in stored values is unescaped back to ".
	// Note: attribute values that contain literal " characters are not
	// round-tripped perfectly through TinyMCE's DOM (the DOM decodes &quot; to
	// "), but this covers all practical shortcode attribute values.
	// -------------------------------------------------------------------------
	function ebpParseShortcode( shortcode ) {
		var attrs = {};
		var re    = /(\w+)="([^"]*)"/g;
		var match;
		while ( ( match = re.exec( shortcode ) ) !== null ) {
			attrs[ match[1] ] = match[2].replace( /&quot;/g, '"' );
		}
		return attrs;
	}

	// -------------------------------------------------------------------------
	// Populate the form from a parsed attrs object (used when editing a button).
	// Falls back to plugin defaults for any attribute that is missing.
	// -------------------------------------------------------------------------
	function ebpPopulateForm( attrs ) {
		var d = ebpData.defaults;

		function get( key, fallback ) {
			var v = attrs[ key ];
			if ( v !== undefined && v !== '' ) {
				return v;
			}
			return ( fallback !== undefined ) ? fallback : d[ key ];
		}

		// Text & Font tab.
		$( '#ebp-f-text' ).val( get( 'text', 'Button' ) );
		$( '#ebp-f-font-family' ).val( get( 'font_family' ) );
		$( '#ebp-f-font-size' ).val( get( 'font_size' ) );
		$( '#ebp-f-font-bold' ).prop( 'checked', get( 'font_bold' ) === '1' );
		$( '#ebp-f-font-italic' ).prop( 'checked', get( 'font_italic' ) === '1' );
		$( '#ebp-f-padding-v' ).val( get( 'padding_v' ) );
		$( '#ebp-f-padding-h' ).val( get( 'padding_h' ) );

		// Colors tab.
		ebpSetColor( '#ebp-f-bg-color',         get( 'bg_color' ) );
		ebpSetColor( '#ebp-f-bg-hover-color',   get( 'bg_hover_color' ) );
		ebpSetColor( '#ebp-f-text-color',       get( 'text_color' ) );
		ebpSetColor( '#ebp-f-text-hover-color', get( 'text_hover_color' ) );
		$( '#ebp-f-hover-grow' ).val( get( 'hover_grow' ) );
		$( '#ebp-f-hover-grow-range' ).val( get( 'hover_grow' ) );

		// Icon tab.
		var iconType = get( 'icon_type', 'none' );
		$( 'input[name="ebp-icon-type"][value="' + iconType + '"]' ).prop( 'checked', true );
		$( '#ebp-dlg-row-dashicon' ).toggle( iconType === 'dashicon' );
		$( '#ebp-dlg-row-media-icon' ).toggle( iconType === 'media' );
		var iconVal      = get( 'icon' );
		var iconMediaUrl = get( 'icon_media_url' );
		$( '#ebp-f-icon' ).val( iconVal );
		$( '#ebp-f-icon-media-url' ).val( iconMediaUrl );
		$( '#ebp-f-icon-size' ).val( get( 'icon_size' ) );
		$( '#ebp-f-icon-spacing' ).val( get( 'icon_spacing' ) );
		$( 'input[name="ebp-icon-pos"][value="' + get( 'icon_position', 'before' ) + '"]' ).prop( 'checked', true );

		if ( iconType === 'dashicon' && iconVal ) {
			$( '#ebp-dlg-icon-preview' ).html(
				'<span class="dashicons dashicons-' + ebpEscHtml( iconVal ) + '" style="font-size:24px;width:24px;height:24px"></span>' +
				' <span style="font-size:11px;color:#666">' + ebpEscHtml( iconVal ) + '</span>'
			);
			// Use .filter() + .data() to avoid CSS-escaping issues in attribute selectors.
			$( '#ebp-dlg-icon-grid .ebp-icon-item' ).removeClass( 'selected' )
				.filter( function () { return $( this ).data( 'icon' ) === iconVal; } )
				.addClass( 'selected' );
		} else {
			$( '#ebp-dlg-icon-preview' ).html( '' );
		}

		if ( iconType === 'media' && iconMediaUrl ) {
			$( '#ebp-dlg-icon-media-preview' ).html(
				'<img src="' + ebpEscHtml( iconMediaUrl ) + '" style="max-height:48px" />'
			);
		} else {
			$( '#ebp-dlg-icon-media-preview' ).html( '' );
		}

		// Border & Shadow tab.
		$( '#ebp-f-border-width' ).val( get( 'border_width' ) );
		$( '#ebp-f-border-style' ).val( get( 'border_style' ) );
		ebpSetColor( '#ebp-f-border-color', get( 'border_color' ) );
		$( '#ebp-f-border-radius' ).val( get( 'border_radius' ) );
		var shadowEnabled = get( 'shadow_enabled' ) === '1';
		$( '#ebp-f-shadow-enabled' ).prop( 'checked', shadowEnabled );
		$( '#ebp-dlg-shadow-fields' ).toggle( shadowEnabled );
		$( '#ebp-f-shadow-x' ).val( get( 'shadow_x' ) );
		$( '#ebp-f-shadow-y' ).val( get( 'shadow_y' ) );
		$( '#ebp-f-shadow-blur' ).val( get( 'shadow_blur' ) );
		$( '#ebp-f-shadow-spread' ).val( get( 'shadow_spread' ) );
		$( '#ebp-f-shadow-color' ).val( get( 'shadow_color' ) );

		// Link tab.
		var linkType = get( 'link_type', 'url' );
		$( 'input[name="ebp-link-type"][value="' + linkType + '"]' ).prop( 'checked', true );
		ebpToggleLinkFields( linkType );
		$( '#ebp-f-url' ).val( get( 'url', '' ) );
		$( '#ebp-f-email' ).val( get( 'email', '' ) );
		$( '#ebp-f-email-subject' ).val( get( 'email_subject', '' ) );
		$( '#ebp-f-email-body' ).val( get( 'email_body', '' ) );
		$( '#ebp-f-media-url' ).val( get( 'media_url', '' ) );
		$( '#ebp-dlg-media-preview' ).html( '' );
		$( 'input[name="ebp-target"][value="' + get( 'target', '_self' ) + '"]' ).prop( 'checked', true );

		// Reset to first tab.
		$( '.ebp-modal-tab' ).removeClass( 'active' );
		$( '.ebp-modal-tab[data-tab="text"]' ).addClass( 'active' );
		$( '.ebp-modal-panel' ).removeClass( 'active' );
		$( '#ebp-panel-text' ).addClass( 'active' );

		ebpUpdatePreview();
	}

	// -------------------------------------------------------------------------
	// Toggle link-type-dependent rows
	// -------------------------------------------------------------------------
	function ebpToggleLinkFields( type ) {
		$( '#ebp-dlg-row-url' ).toggle( type === 'url' );
		$( '#ebp-dlg-row-email' ).toggle( type === 'email' );
		$( '#ebp-dlg-row-media' ).toggle( type === 'media' );
	}

	// -------------------------------------------------------------------------
	// Close dialog
	// -------------------------------------------------------------------------
	function ebpCloseDialog() {
		$( '#ebp-modal-overlay' ).fadeOut( 150 );
		currentEditor    = null;
		currentShortcode = null;
	}

	// -------------------------------------------------------------------------
	// Build shortcode and insert into editor
	// -------------------------------------------------------------------------
	function ebpInsertShortcode() {
		if ( ! currentEditor ) {
			return;
		}
		var attrs = ebpCollectAttrs();
		var sc    = '[eifelhoster_button';
		$.each( attrs, function ( k, v ) {
			sc += ' ' + k + '="' + v.replace( /"/g, '&quot;' ) + '"';
		} );
		sc += ']';

		if ( currentShortcode ) {
			// Replace the selected (existing) shortcode.
			currentEditor.selection.setContent( sc );
		} else {
			currentEditor.insertContent( sc );
		}
		ebpCloseDialog();
	}

	// -------------------------------------------------------------------------
	// Collect all form values as an attributes object
	// -------------------------------------------------------------------------
	function ebpCollectAttrs() {
		var iconType = $( 'input[name="ebp-icon-type"]:checked' ).val() || 'none';
		var linkType = $( 'input[name="ebp-link-type"]:checked' ).val() || 'url';
		var target   = $( 'input[name="ebp-target"]:checked' ).val() || '_self';
		var iconPos  = $( 'input[name="ebp-icon-pos"]:checked' ).val() || 'before';

		return {
			text             : $( '#ebp-f-text' ).val() || 'Button',
			font_family      : $( '#ebp-f-font-family' ).val(),
			font_size        : $( '#ebp-f-font-size' ).val(),
			font_bold        : $( '#ebp-f-font-bold' ).is( ':checked' ) ? '1' : '0',
			font_italic      : $( '#ebp-f-font-italic' ).is( ':checked' ) ? '1' : '0',
			bg_color         : ebpGetColor( '#ebp-f-bg-color' ),
			bg_hover_color   : ebpGetColor( '#ebp-f-bg-hover-color' ),
			text_color       : ebpGetColor( '#ebp-f-text-color' ),
			text_hover_color : ebpGetColor( '#ebp-f-text-hover-color' ),
			hover_grow       : $( '#ebp-f-hover-grow' ).val(),
			padding_v        : $( '#ebp-f-padding-v' ).val(),
			padding_h        : $( '#ebp-f-padding-h' ).val(),
			icon_type        : iconType,
			icon             : $( '#ebp-f-icon' ).val(),
			icon_media_url   : $( '#ebp-f-icon-media-url' ).val(),
			icon_size        : $( '#ebp-f-icon-size' ).val(),
			icon_spacing     : $( '#ebp-f-icon-spacing' ).val(),
			icon_position    : iconPos,
			border_width     : $( '#ebp-f-border-width' ).val(),
			border_style     : $( '#ebp-f-border-style' ).val(),
			border_color     : ebpGetColor( '#ebp-f-border-color' ),
			border_radius    : $( '#ebp-f-border-radius' ).val(),
			shadow_enabled   : $( '#ebp-f-shadow-enabled' ).is( ':checked' ) ? '1' : '0',
			shadow_x         : $( '#ebp-f-shadow-x' ).val(),
			shadow_y         : $( '#ebp-f-shadow-y' ).val(),
			shadow_blur      : $( '#ebp-f-shadow-blur' ).val(),
			shadow_spread    : $( '#ebp-f-shadow-spread' ).val(),
			shadow_color     : $( '#ebp-f-shadow-color' ).val(),
			link_type        : linkType,
			url              : $( '#ebp-f-url' ).val(),
			email            : $( '#ebp-f-email' ).val(),
			email_subject    : $( '#ebp-f-email-subject' ).val(),
			email_body       : $( '#ebp-f-email-body' ).val(),
			media_url        : $( '#ebp-f-media-url' ).val(),
			target           : target,
		};
	}

	// Get colour-picker value safely.
	function ebpGetColor( selector ) {
		var $el = $( selector );
		// Try WP colour picker API first.
		try {
			var iris = $el.data( 'wpColorPicker' ) || $el.data( 'a8cIris' );
			if ( iris ) {
				return $el.val();
			}
		} catch ( e ) { /* ignore */ }
		return $el.val();
	}

	// -------------------------------------------------------------------------
	// Live preview
	// -------------------------------------------------------------------------
	function ebpUpdatePreview() {
		var attrs = ebpCollectAttrs();
		var $btn  = $( '#ebp-preview-btn' );

		// Styles.
		var css = {
			'display'         : 'inline-flex',
			'align-items'     : 'center',
			'justify-content' : 'center',
			'text-decoration' : 'none',
			'cursor'          : 'pointer',
			'font-family'     : attrs.font_family || 'inherit',
			'font-size'       : ( parseInt( attrs.font_size, 10 ) || 16 ) + 'px',
			'font-weight'     : attrs.font_bold   === '1' ? 'bold'   : 'normal',
			'font-style'      : attrs.font_italic === '1' ? 'italic' : 'normal',
			'background-color': attrs.bg_color,
			'color'           : attrs.text_color,
			'padding'         : ( parseInt( attrs.padding_v, 10 ) || 10 ) + 'px ' +
			                    ( parseInt( attrs.padding_h, 10 ) || 20 ) + 'px',
			'border-width'    : ( parseInt( attrs.border_width, 10 ) || 0 ) + 'px',
			'border-style'    : attrs.border_style || 'solid',
			'border-color'    : attrs.border_color,
			'border-radius'   : ( parseInt( attrs.border_radius, 10 ) || 0 ) + 'px',
			'transition'      : 'background-color .3s, color .3s, transform .3s',
		};

		if ( attrs.shadow_enabled === '1' ) {
			css['box-shadow'] = [
				parseInt( attrs.shadow_x,      10 ) + 'px',
				parseInt( attrs.shadow_y,      10 ) + 'px',
				parseInt( attrs.shadow_blur,   10 ) + 'px',
				parseInt( attrs.shadow_spread, 10 ) + 'px',
				attrs.shadow_color || 'rgba(0,0,0,0.3)',
			].join( ' ' );
		} else {
			css['box-shadow'] = 'none';
		}

		if ( attrs.icon_type !== 'none' ) {
			css['gap'] = ( parseInt( attrs.icon_spacing, 10 ) || 8 ) + 'px';
		}

		$btn.css( css );
		$( '#ebp-preview-btn .ebp-preview-text' ).text( attrs.text || 'Button' );

		// Icon.
		var iconHtml = '';
		if ( attrs.icon_type === 'dashicon' && attrs.icon ) {
			var sz = parseInt( attrs.icon_size, 10 ) || 20;
			iconHtml = '<span class="dashicons dashicons-' + ebpEscHtml( attrs.icon ) + '" ' +
				'style="font-size:' + sz + 'px;width:' + sz + 'px;height:' + sz + 'px"></span>';
		} else if ( attrs.icon_type === 'media' && attrs.icon_media_url ) {
			var sz2 = parseInt( attrs.icon_size, 10 ) || 20;
			iconHtml = '<img src="' + ebpEscHtml( attrs.icon_media_url ) + '" ' +
				'style="width:' + sz2 + 'px;height:' + sz2 + 'px" alt="" />';
		}

		if ( attrs.icon_position === 'before' ) {
			$( '#ebp-preview-btn .ebp-preview-icon-before' ).html( iconHtml );
			$( '#ebp-preview-btn .ebp-preview-icon-after' ).html( '' );
		} else {
			$( '#ebp-preview-btn .ebp-preview-icon-before' ).html( '' );
			$( '#ebp-preview-btn .ebp-preview-icon-after' ).html( iconHtml );
		}
	}

	// -------------------------------------------------------------------------
	// Build Dashicon grid
	// -------------------------------------------------------------------------
	function ebpBuildIconGrid( gridSel, hiddenSel, previewSel, searchSel ) {
		var icons  = ( typeof ebpData !== 'undefined' ) ? ebpData.dashicons : [];
		var $grid  = $( gridSel );

		function renderGrid( filter ) {
			$grid.empty();
			var shown = filter
				? icons.filter( function ( ic ) { return ic.indexOf( filter ) !== -1; } )
				: icons;
			shown.forEach( function ( icon ) {
				var $item = $( '<span>' )
					.addClass( 'ebp-icon-item dashicons dashicons-' + icon )
					.attr( 'title', icon )
					.data( 'icon', icon );
				$grid.append( $item );
			} );
		}

		renderGrid( '' );

		$grid.on( 'click', '.ebp-icon-item', function () {
			var icon = $( this ).data( 'icon' );
			$( hiddenSel ).val( icon );
			$( previewSel ).html(
				'<span class="dashicons dashicons-' + icon + '" style="font-size:24px;width:24px;height:24px"></span>' +
				' <span style="font-size:11px;color:#666">' + icon + '</span>'
			);
			$grid.find( '.ebp-icon-item' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			ebpUpdatePreview();
		} );

		$( searchSel ).on( 'input', function () {
			renderGrid( $( this ).val().toLowerCase() );
		} );
	}

}( jQuery ) );
