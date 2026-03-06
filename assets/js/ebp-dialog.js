/**
 * Dialog UI for Eifelhoster Buttons Pro.
 *
 * Provides  window.ebpOpenDialog( editor )  which is called by the TinyMCE plugin.
 */
/* global jQuery, ebpData, wp */
(function ( $ ) {
	'use strict';

	var currentEditor   = null;
	var mediaFrameIcon  = null;
	var mediaFrameLink  = null;
	var isEditing       = false;

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------
	window.ebpOpenDialog = function ( editor ) {
		currentEditor = editor;
		isEditing     = false;

		var existing = ebpGetShortcodeAtCursor( editor );
		if ( existing ) {
			// Select the shortcode text in the editor so setContent() replaces it.
			ebpSelectShortcodeInEditor( editor, existing );
			isEditing = true;
			ebpResetForm();
			ebpPopulateForm( ebpParseShortcodeAttrs( existing ) );
		} else {
			ebpResetForm();
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
		currentEditor = null;
		isEditing     = false;
	}

	// -------------------------------------------------------------------------
	// Build shortcode and insert / replace in editor
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

		if ( isEditing ) {
			// Replace the previously selected shortcode.
			currentEditor.selection.setContent( sc );
		} else {
			currentEditor.insertContent( sc );
		}
		ebpCloseDialog();
	}

	// -------------------------------------------------------------------------
	// Detect shortcode at TinyMCE cursor position
	// Returns the raw shortcode string (e.g. [eifelhoster_button text="…" …])
	// or null when the cursor is not inside one.
	// -------------------------------------------------------------------------
	function ebpGetShortcodeAtCursor( editor ) {
		var sel            = editor.selection;
		var rng            = sel.getRng( true );
		var startContainer = rng.startContainer;
		var startOffset    = rng.startOffset;

		// Walk up to the nearest element node.
		var container = startContainer.nodeType === 3
			? startContainer.parentNode
			: startContainer;

		// Collect the full text of the container and the cursor's byte-offset
		// within that text using a TreeWalker over the editor document.
		var editorDoc = editor.getDoc();
		var allText   = '';
		var cursorPos = 0;
		var cursorSet = false;

		var walker = editorDoc.createTreeWalker(
			container,
			NodeFilter.SHOW_TEXT,
			null,
			false
		);

		var tn;
		while ( ( tn = walker.nextNode() ) ) {
			if ( ! cursorSet && tn === startContainer ) {
				cursorPos = allText.length + startOffset;
				cursorSet = true;
			}
			allText += tn.textContent;
		}

		// Fallback when the TreeWalker did not encounter the start node.
		if ( ! cursorSet ) {
			allText   = container.textContent || '';
			cursorPos = allText.length;
		}

		// Find every [eifelhoster_button …] shortcode in the collected text
		// and return the one that brackets the cursor position.
		var re = /\[eifelhoster_button\b[^\]]*\]/g;
		var match;
		while ( ( match = re.exec( allText ) ) !== null ) {
			var start = match.index;
			var end   = start + match[0].length;
			if ( cursorPos >= start && cursorPos <= end ) {
				return match[0];
			}
		}
		return null;
	}

	// -------------------------------------------------------------------------
	// Select a shortcode string inside the TinyMCE body so that a subsequent
	// editor.selection.setContent() replaces it in place.
	// -------------------------------------------------------------------------
	function ebpSelectShortcodeInEditor( editor, sc ) {
		var editorDoc = editor.getDoc();
		var body      = editor.getBody();

		var walker = editorDoc.createTreeWalker(
			body,
			NodeFilter.SHOW_TEXT,
			null,
			false
		);

		var tn;
		while ( ( tn = walker.nextNode() ) ) {
			var idx = tn.textContent.indexOf( sc );
			if ( idx !== -1 ) {
				var range = editorDoc.createRange();
				range.setStart( tn, idx );
				range.setEnd( tn, idx + sc.length );
				editor.selection.setRng( range );
				return;
			}
		}
	}

	// -------------------------------------------------------------------------
	// Parse shortcode attribute string into a plain object.
	// Handles both attr="value" and &quot; encoded quotes.
	// -------------------------------------------------------------------------
	function ebpParseShortcodeAttrs( sc ) {
		var attrs = {};
		// Match   key="value"   pairs (value may contain &quot; but not raw quotes).
		var re = /(\w+)="([^"]*)"/g;
		var match;
		while ( ( match = re.exec( sc ) ) !== null ) {
			attrs[ match[1] ] = match[2].replace( /&quot;/g, '"' );
		}
		return attrs;
	}

	// -------------------------------------------------------------------------
	// Populate all dialog form fields from a parsed attributes object.
	// Falls back to the default value (from ebpResetForm) for any missing key.
	// -------------------------------------------------------------------------
	function ebpPopulateForm( attrs ) {
		function v( key ) {
			return attrs.hasOwnProperty( key ) ? attrs[ key ] : null;
		}

		// Text & Font tab.
		if ( v( 'text' )        !== null ) { $( '#ebp-f-text' ).val( v( 'text' ) ); }
		if ( v( 'font_family' ) !== null ) { $( '#ebp-f-font-family' ).val( v( 'font_family' ) ); }
		if ( v( 'font_size' )   !== null ) { $( '#ebp-f-font-size' ).val( v( 'font_size' ) ); }
		if ( v( 'font_bold' )   !== null ) { $( '#ebp-f-font-bold' ).prop( 'checked', v( 'font_bold' )   === '1' ); }
		if ( v( 'font_italic' ) !== null ) { $( '#ebp-f-font-italic' ).prop( 'checked', v( 'font_italic' ) === '1' ); }
		if ( v( 'padding_v' )   !== null ) { $( '#ebp-f-padding-v' ).val( v( 'padding_v' ) ); }
		if ( v( 'padding_h' )   !== null ) { $( '#ebp-f-padding-h' ).val( v( 'padding_h' ) ); }

		// Colors tab.
		if ( v( 'bg_color' )         !== null ) { ebpSetColor( '#ebp-f-bg-color',         v( 'bg_color' ) ); }
		if ( v( 'bg_hover_color' )   !== null ) { ebpSetColor( '#ebp-f-bg-hover-color',   v( 'bg_hover_color' ) ); }
		if ( v( 'text_color' )       !== null ) { ebpSetColor( '#ebp-f-text-color',       v( 'text_color' ) ); }
		if ( v( 'text_hover_color' ) !== null ) { ebpSetColor( '#ebp-f-text-hover-color', v( 'text_hover_color' ) ); }
		if ( v( 'hover_grow' )       !== null ) {
			$( '#ebp-f-hover-grow' ).val( v( 'hover_grow' ) );
			$( '#ebp-f-hover-grow-range' ).val( v( 'hover_grow' ) );
		}

		// Icon tab.
		if ( v( 'icon_type' ) !== null ) {
			var iconType = v( 'icon_type' );
			$( 'input[name="ebp-icon-type"][value="' + iconType + '"]' ).prop( 'checked', true );
			$( '#ebp-dlg-row-dashicon' ).toggle( iconType === 'dashicon' );
			$( '#ebp-dlg-row-media-icon' ).toggle( iconType === 'media' );

			var iconName     = v( 'icon' )           || '';
			var iconMediaUrl = v( 'icon_media_url' ) || '';

			if ( iconType === 'dashicon' && iconName ) {
				$( '#ebp-f-icon' ).val( iconName );
				$( '#ebp-dlg-icon-preview' ).html(
					'<span class="dashicons dashicons-' + ebpEscHtml( iconName ) + '" style="font-size:24px;width:24px;height:24px"></span>' +
					' <span style="font-size:11px;color:#666">' + ebpEscHtml( iconName ) + '</span>'
				);
				$( '#ebp-dlg-icon-grid .ebp-icon-item' ).removeClass( 'selected' );
			$( '#ebp-dlg-icon-grid .ebp-icon-item' ).filter( function () {
				return $( this ).data( 'icon' ) === iconName;
			} ).addClass( 'selected' );
			} else if ( iconType === 'media' && iconMediaUrl ) {
				$( '#ebp-f-icon-media-url' ).val( iconMediaUrl );
				$( '#ebp-dlg-icon-media-preview' ).html(
					'<img src="' + ebpEscHtml( iconMediaUrl ) + '" style="max-height:48px;margin-top:4px" />'
				);
			}
		}
		if ( v( 'icon' )           !== null ) { $( '#ebp-f-icon' ).val( v( 'icon' ) ); }
		if ( v( 'icon_media_url' ) !== null ) { $( '#ebp-f-icon-media-url' ).val( v( 'icon_media_url' ) ); }
		if ( v( 'icon_size' )      !== null ) { $( '#ebp-f-icon-size' ).val( v( 'icon_size' ) ); }
		if ( v( 'icon_spacing' )   !== null ) { $( '#ebp-f-icon-spacing' ).val( v( 'icon_spacing' ) ); }
		if ( v( 'icon_position' )  !== null ) {
			$( 'input[name="ebp-icon-pos"][value="' + v( 'icon_position' ) + '"]' ).prop( 'checked', true );
		}

		// Border & Shadow tab.
		if ( v( 'border_width' )   !== null ) { $( '#ebp-f-border-width' ).val( v( 'border_width' ) ); }
		if ( v( 'border_style' )   !== null ) { $( '#ebp-f-border-style' ).val( v( 'border_style' ) ); }
		if ( v( 'border_color' )   !== null ) { ebpSetColor( '#ebp-f-border-color', v( 'border_color' ) ); }
		if ( v( 'border_radius' )  !== null ) { $( '#ebp-f-border-radius' ).val( v( 'border_radius' ) ); }
		if ( v( 'shadow_enabled' ) !== null ) {
			var shadowOn = v( 'shadow_enabled' ) === '1';
			$( '#ebp-f-shadow-enabled' ).prop( 'checked', shadowOn );
			$( '#ebp-dlg-shadow-fields' ).toggle( shadowOn );
		}
		if ( v( 'shadow_x' )      !== null ) { $( '#ebp-f-shadow-x' ).val( v( 'shadow_x' ) ); }
		if ( v( 'shadow_y' )      !== null ) { $( '#ebp-f-shadow-y' ).val( v( 'shadow_y' ) ); }
		if ( v( 'shadow_blur' )   !== null ) { $( '#ebp-f-shadow-blur' ).val( v( 'shadow_blur' ) ); }
		if ( v( 'shadow_spread' ) !== null ) { $( '#ebp-f-shadow-spread' ).val( v( 'shadow_spread' ) ); }
		if ( v( 'shadow_color' )  !== null ) { $( '#ebp-f-shadow-color' ).val( v( 'shadow_color' ) ); }

		// Link tab.
		if ( v( 'link_type' ) !== null ) {
			var linkType = v( 'link_type' );
			$( 'input[name="ebp-link-type"][value="' + linkType + '"]' ).prop( 'checked', true );
			ebpToggleLinkFields( linkType );
		}
		if ( v( 'url' )           !== null ) { $( '#ebp-f-url' ).val( v( 'url' ) ); }
		if ( v( 'email' )         !== null ) { $( '#ebp-f-email' ).val( v( 'email' ) ); }
		if ( v( 'email_subject' ) !== null ) { $( '#ebp-f-email-subject' ).val( v( 'email_subject' ) ); }
		if ( v( 'email_body' )    !== null ) { $( '#ebp-f-email-body' ).val( v( 'email_body' ) ); }
		if ( v( 'media_url' )     !== null ) {
			$( '#ebp-f-media-url' ).val( v( 'media_url' ) );
			if ( v( 'media_url' ) ) {
				$( '#ebp-dlg-media-preview' ).html(
					'<span class="dashicons dashicons-media-default" style="vertical-align:middle"></span> ' +
					'<a href="' + ebpEscHtml( v( 'media_url' ) ) + '" target="_blank" rel="noopener">' +
					ebpEscHtml( v( 'media_url' ) ) + '</a>'
				);
			}
		}
		if ( v( 'target' ) !== null ) {
			$( 'input[name="ebp-target"][value="' + v( 'target' ) + '"]' ).prop( 'checked', true );
		}

		ebpUpdatePreview();
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
