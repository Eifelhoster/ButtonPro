/**
 * Dialog UI for Eifelhoster Buttons Pro.
 *
 * Provides  window.ebpOpenDialog( editor [, editData] )  which is called by the TinyMCE plugin.
 * editData: { shortcode, node, start, end } – present when editing an existing shortcode.
 */
/* global jQuery, ebpData, wp */
(function ( $ ) {
	'use strict';

	var currentEditor = null;
	var mediaFrameIcon  = null;
	var mediaFrameLink  = null;

	// Edit-mode state.
	var isEditing = false;
	var editNode  = null;
	var editStart = 0;
	var editEnd   = 0;

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------
	window.ebpOpenDialog = function ( editor, editData ) {
		currentEditor = editor;
		isEditing     = false;
		editNode      = null;
		editStart     = 0;
		editEnd       = 0;

		ebpResetForm();

		if ( editData ) {
			isEditing = true;
			editNode  = editData.node;
			editStart = editData.start;
			editEnd   = editData.end;

			var attrs = ebpParseShortcodeAttrs( editData.shortcode );
			ebpPopulateForm( attrs );

			$( '#ebp-modal-title' ).html(
				'<span class="dashicons dashicons-edit" style="margin-right:6px"></span>' +
				ebpData.i18n.editTitle
			);
			$( '#ebp-btn-insert' ).html(
				'<span class="dashicons dashicons-yes" style="vertical-align:middle;margin-right:4px"></span>' +
				ebpData.i18n.update
			);
		} else {
			$( '#ebp-modal-title' ).html(
				'<span class="dashicons dashicons-button" style="margin-right:6px"></span>' +
				ebpData.i18n.title
			);
			$( '#ebp-btn-insert' ).html(
				'<span class="dashicons dashicons-insert" style="vertical-align:middle;margin-right:4px"></span>' +
				ebpData.i18n.insert
			);
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
			if ( e.key === 'Escape' ) {
				if ( $( '#ebp-docs-overlay' ).is( ':visible' ) ) {
					$( '#ebp-docs-overlay' ).fadeOut( 150 );
				} else if ( $( '#ebp-modal-overlay' ).is( ':visible' ) ) {
					ebpCloseDialog();
				}
			}
		} );

		// Insert button.
		$( '#ebp-btn-insert' ).on( 'click', ebpInsertShortcode );

		// Docs popup.
		$( '#ebp-btn-docs' ).on( 'click', function () {
			$( '#ebp-docs-overlay' ).fadeIn( 150 );
		} );
		$( '#ebp-docs-modal-close' ).on( 'click', function () {
			$( '#ebp-docs-overlay' ).fadeOut( 150 );
		} );
		$( '#ebp-docs-overlay' ).on( 'click', function ( e ) {
			if ( $( e.target ).is( '#ebp-docs-overlay' ) ) {
				$( '#ebp-docs-overlay' ).fadeOut( 150 );
			}
		} );

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

		// Content search.
		var contentSearchTimer = null;
		$( '#ebp-f-content-search' ).on( 'input', function () {
			var val = $( this ).val();
			clearTimeout( contentSearchTimer );
			if ( val.length < 2 ) {
				$( '#ebp-dlg-content-results' ).hide().empty();
				return;
			}
			contentSearchTimer = setTimeout( function () {
				$.post( ebpData.ajaxurl, {
					action : 'ebp_search_content',
					nonce  : ebpData.nonce,
					search : val,
				}, function ( response ) {
					var $results = $( '#ebp-dlg-content-results' );
					$results.empty().show();
					if ( ! response.success || ! response.data.length ) {
						$results.html( '<p style="padding:6px;margin:0;color:#888">' + ebpEscHtml( 'Keine Ergebnisse' ) + '</p>' );
						return;
					}
					$.each( response.data, function ( i, item ) {
						var $row = $( '<div>' )
							.css( { padding: '6px 8px', cursor: 'pointer', borderBottom: '1px solid #eee' } )
							.html(
								'<strong>' + ebpEscHtml( item.title ) + '</strong>' +
								' <span style="color:#888;font-size:11px">(' + ebpEscHtml( item.type ) + ')</span>'
							)
							.data( 'id', item.id )
							.data( 'title', item.title )
							.data( 'permalink', item.permalink )
							.on( 'mouseenter', function () { $( this ).css( 'background', '#f0f0f0' ); } )
							.on( 'mouseleave', function () { $( this ).css( 'background', '' ); } )
							.on( 'click', function () {
								$( '#ebp-f-content-id' ).val( $( this ).data( 'id' ) );
								$( '#ebp-f-content-search' ).val( $( this ).data( 'title' ) );
								$( '#ebp-dlg-content-selected' ).html(
									'<span class="dashicons dashicons-yes" style="color:green;vertical-align:middle"></span> ' +
									ebpEscHtml( $( this ).data( 'title' ) )
								);
								$results.hide().empty();
							} );
						$results.append( $row );
					} );
				} );
			}, 300 );
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
		$( '#ebp-f-button-width' ).val( d.button_width );

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
		ebpSetColor( '#ebp-f-shadow-color', d.shadow_color );

		// Link tab.
		$( 'input[name="ebp-link-type"][value="' + d.link_type + '"]' ).prop( 'checked', true );
		ebpToggleLinkFields( d.link_type );
		$( '#ebp-f-url' ).val( d.url );
		$( '#ebp-f-email' ).val( d.email );
		$( '#ebp-f-email-subject' ).val( d.email_subject );
		$( '#ebp-f-email-body' ).val( d.email_body );
		$( '#ebp-f-media-url' ).val( d.media_url );
		$( '#ebp-dlg-media-preview' ).html( '' );
		$( '#ebp-f-content-id' ).val( d.content_id || '' );
		$( '#ebp-f-content-search' ).val( '' );
		$( '#ebp-dlg-content-results' ).hide().empty();
		$( '#ebp-dlg-content-selected' ).html( '' );
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
		$( '#ebp-dlg-row-content' ).toggle( type === 'content' );
	}

	// -------------------------------------------------------------------------
	// Close dialog
	// -------------------------------------------------------------------------
	function ebpCloseDialog() {
		$( '#ebp-modal-overlay' ).fadeOut( 150 );
		currentEditor = null;
		isEditing     = false;
		editNode      = null;
		editStart     = 0;
		editEnd       = 0;
	}

	// -------------------------------------------------------------------------
	// Build shortcode and insert (or replace) in editor
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

		if ( isEditing && editNode ) {
			// Select the existing shortcode text and replace it.
			var rng = currentEditor.dom.createRng();
			rng.setStart( editNode, editStart );
			rng.setEnd(   editNode, editEnd   );
			currentEditor.selection.setRng( rng );
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
			button_width     : $( '#ebp-f-button-width' ).val(),
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
			shadow_color     : ebpGetColor( '#ebp-f-shadow-color' ),
			link_type        : linkType,
			url              : $( '#ebp-f-url' ).val(),
			email            : $( '#ebp-f-email' ).val(),
			email_subject    : $( '#ebp-f-email-subject' ).val(),
			email_body       : $( '#ebp-f-email-body' ).val(),
			media_url        : $( '#ebp-f-media-url' ).val(),
			content_id       : $( '#ebp-f-content-id' ).val(),
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
			'width'           : ( parseInt( attrs.button_width, 10 ) > 0 )
			                    ? parseInt( attrs.button_width, 10 ) + 'px' : '',
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

	// -------------------------------------------------------------------------
	// Parse shortcode attribute string into a plain object
	// -------------------------------------------------------------------------
	function ebpParseShortcodeAttrs( shortcode ) {
		var attrs = {};
		var re    = /(\w+)="([^"]*)"/g;
		var match;
		while ( ( match = re.exec( shortcode ) ) !== null ) {
			attrs[ match[1] ] = match[2].replace( /&quot;/g, '"' );
		}
		return attrs;
	}

	// -------------------------------------------------------------------------
	// Populate dialog form from a parsed attributes object
	// -------------------------------------------------------------------------
	function ebpPopulateForm( attrs ) {
		function v( key, fallback ) {
			return ( attrs[ key ] !== undefined ) ? attrs[ key ] : fallback;
		}

		// Text & Font.
		$( '#ebp-f-text' ).val( v( 'text', 'Button' ) );
		$( '#ebp-f-font-family' ).val( v( 'font_family', '' ) );
		$( '#ebp-f-font-size' ).val( v( 'font_size', '' ) );
		$( '#ebp-f-font-bold' ).prop( 'checked', v( 'font_bold', '0' ) === '1' );
		$( '#ebp-f-font-italic' ).prop( 'checked', v( 'font_italic', '0' ) === '1' );
		$( '#ebp-f-padding-v' ).val( v( 'padding_v', '' ) );
		$( '#ebp-f-padding-h' ).val( v( 'padding_h', '' ) );
		$( '#ebp-f-button-width' ).val( v( 'button_width', '' ) );

		// Colors.
		if ( attrs.bg_color         !== undefined ) { ebpSetColor( '#ebp-f-bg-color',         attrs.bg_color         ); }
		if ( attrs.bg_hover_color   !== undefined ) { ebpSetColor( '#ebp-f-bg-hover-color',   attrs.bg_hover_color   ); }
		if ( attrs.text_color       !== undefined ) { ebpSetColor( '#ebp-f-text-color',       attrs.text_color       ); }
		if ( attrs.text_hover_color !== undefined ) { ebpSetColor( '#ebp-f-text-hover-color', attrs.text_hover_color ); }
		$( '#ebp-f-hover-grow' ).val( v( 'hover_grow', '' ) );
		$( '#ebp-f-hover-grow-range' ).val( v( 'hover_grow', '' ) );

		// Icon.
		var iconType = v( 'icon_type', 'none' );
		$( 'input[name="ebp-icon-type"][value="' + iconType + '"]' ).prop( 'checked', true );
		$( '#ebp-dlg-row-dashicon' ).toggle( iconType === 'dashicon' );
		$( '#ebp-dlg-row-media-icon' ).toggle( iconType === 'media' );
		$( '#ebp-f-icon' ).val( v( 'icon', '' ) );
		$( '#ebp-f-icon-media-url' ).val( v( 'icon_media_url', '' ) );
		$( '#ebp-f-icon-size' ).val( v( 'icon_size', '' ) );
		$( '#ebp-f-icon-spacing' ).val( v( 'icon_spacing', '' ) );
		$( 'input[name="ebp-icon-pos"][value="' + v( 'icon_position', 'before' ) + '"]' ).prop( 'checked', true );

		// Refresh icon preview.
		if ( iconType === 'dashicon' && attrs.icon ) {
			var sz = parseInt( attrs.icon_size, 10 ) || 20;
			$( '#ebp-dlg-icon-preview' ).html(
				'<span class="dashicons dashicons-' + ebpEscHtml( attrs.icon ) + '" ' +
				'style="font-size:' + sz + 'px;width:' + sz + 'px;height:' + sz + 'px"></span>' +
				' <span style="font-size:11px;color:#666">' + ebpEscHtml( attrs.icon ) + '</span>'
			);
			var selectedIcon = attrs.icon;
			$( '#ebp-dlg-icon-grid .ebp-icon-item' ).removeClass( 'selected' ).filter( function () {
				return $( this ).data( 'icon' ) === selectedIcon;
			} ).addClass( 'selected' );
		}
		if ( iconType === 'media' && attrs.icon_media_url ) {
			$( '#ebp-dlg-icon-media-preview' ).html(
				'<img src="' + ebpEscHtml( attrs.icon_media_url ) + '" style="max-height:48px;margin-top:4px" />'
			);
		}

		// Border & Shadow.
		$( '#ebp-f-border-width' ).val( v( 'border_width', '' ) );
		$( '#ebp-f-border-style' ).val( v( 'border_style', '' ) );
		if ( attrs.border_color !== undefined ) { ebpSetColor( '#ebp-f-border-color', attrs.border_color ); }
		$( '#ebp-f-border-radius' ).val( v( 'border_radius', '' ) );
		var shadowEnabled = v( 'shadow_enabled', '0' ) === '1';
		$( '#ebp-f-shadow-enabled' ).prop( 'checked', shadowEnabled );
		$( '#ebp-dlg-shadow-fields' ).toggle( shadowEnabled );
		$( '#ebp-f-shadow-x' ).val( v( 'shadow_x', '' ) );
		$( '#ebp-f-shadow-y' ).val( v( 'shadow_y', '' ) );
		$( '#ebp-f-shadow-blur' ).val( v( 'shadow_blur', '' ) );
		$( '#ebp-f-shadow-spread' ).val( v( 'shadow_spread', '' ) );
		if ( attrs.shadow_color !== undefined ) { ebpSetColor( '#ebp-f-shadow-color', attrs.shadow_color ); }

		// Link.
		var linkType = v( 'link_type', 'url' );
		$( 'input[name="ebp-link-type"][value="' + linkType + '"]' ).prop( 'checked', true );
		ebpToggleLinkFields( linkType );
		$( '#ebp-f-url' ).val( v( 'url', '' ) );
		$( '#ebp-f-email' ).val( v( 'email', '' ) );
		$( '#ebp-f-email-subject' ).val( v( 'email_subject', '' ) );
		$( '#ebp-f-email-body' ).val( v( 'email_body', '' ) );
		$( '#ebp-f-media-url' ).val( v( 'media_url', '' ) );
		$( '#ebp-f-content-id' ).val( v( 'content_id', '' ) );
		$( 'input[name="ebp-target"][value="' + v( 'target', '_self' ) + '"]' ).prop( 'checked', true );

		ebpUpdatePreview();
	}

}( jQuery ) );
