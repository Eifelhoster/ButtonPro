/**
 * Admin settings page JS for Eifelhoster Buttons Pro.
 */
/* global jQuery, ebpAdminData, wp */
(function ( $ ) {
	'use strict';

	// -------------------------------------------------------------------------
	// HTML-escape helper (for dynamic DOM insertion)
	// -------------------------------------------------------------------------
	function ebpEscHtml( str ) {
		return String( str )
			.replace( /&/g,  '&amp;' )
			.replace( /</g,  '&lt;'  )
			.replace( />/g,  '&gt;'  )
			.replace( /"/g,  '&quot;' )
			.replace( /'/g,  '&#039;' );
	}

	$( document ).ready( function () {

		// ---- Colour pickers ----
		$( '.ebp-color-picker' ).wpColorPicker( { change: updateAdminPreview } );

		// ---- Tabs ----
		$( '.ebp-tabs' ).on( 'click', '.ebp-tab-btn', function () {
			var tab = $( this ).data( 'tab' );
			$( '.ebp-tab-btn' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			$( '.ebp-tab-panel' ).removeClass( 'active' );
			$( '#ebp-tab-' + tab ).addClass( 'active' );
		} );

		// ---- Hover-grow range ↔ number ----
		$( '#ebp-hover-grow-range' ).on( 'input', function () {
			$( '#ebp-hover-grow' ).val( this.value );
			updateAdminPreview();
		} );
		$( '#ebp-hover-grow' ).on( 'input', function () {
			$( '#ebp-hover-grow-range' ).val( this.value );
			updateAdminPreview();
		} );

		// ---- Icon type radio ----
		$( '.ebp-icon-type-radio' ).on( 'change', function () {
			var val = $( this ).val();
			$( '#ebp-row-dashicon' ).toggle( val === 'dashicon' );
			$( '#ebp-row-media-icon' ).toggle( val === 'media' );
			updateAdminPreview();
		} );

		// ---- Shadow toggle ----
		$( '#ebp-shadow-enabled' ).on( 'change', function () {
			$( '#ebp-shadow-fields' ).toggle( this.checked );
			updateAdminPreview();
		} );

		// ---- Link type radio toggle ----
		$( '.ebp-link-type-radio' ).on( 'change', function () {
			var val = $( this ).val();
			$( '#ebp-admin-row-content' ).toggle( val === 'content' );
		} );

		// ---- Media picker for logo ----
		var mediaFrameLogo = null;
		$( '#ebp-select-logo' ).on( 'click', function () {
			if ( mediaFrameLogo ) {
				mediaFrameLogo.open();
				return;
			}
			mediaFrameLogo = wp.media( {
				title   : 'Logo auswählen',
				button  : { text: 'Logo verwenden' },
				multiple: false,
				library : { type: 'image' },
			} );
			mediaFrameLogo.on( 'select', function () {
				var a = mediaFrameLogo.state().get( 'selection' ).first().toJSON();
				$( '#ebp-logo-url' ).val( a.url );
				var $img = $( '<img>' ).attr( {
					src  : a.url,
					style: 'max-height:80px;max-width:300px;display:block',
				} );
				$( '#ebp-logo-preview' ).empty().append( $img );
				$( '#ebp-remove-logo' ).show();
			} );
			mediaFrameLogo.open();
		} );

		$( '#ebp-remove-logo' ).on( 'click', function () {
			$( '#ebp-logo-url' ).val( '' );
			$( '#ebp-logo-preview' ).empty();
			$( this ).hide();
		} );

		// ---- Media picker for icon ----
		var mediaFrameIcon = null;
		$( '#ebp-select-icon-media' ).on( 'click', function () {
			if ( mediaFrameIcon ) {
				mediaFrameIcon.open();
				return;
			}
			mediaFrameIcon = wp.media( {
				title   : 'Symbol-Mediendatei auswählen',
				button  : { text: 'Verwenden' },
				multiple: false,
				library : { type: 'image' },
			} );
			mediaFrameIcon.on( 'select', function () {
				var a = mediaFrameIcon.state().get( 'selection' ).first().toJSON();
				$( '#ebp-icon-media-url' ).val( a.url );
				$( '#ebp-icon-media-preview' ).html(
					'<img src="' + ebpEscHtml( a.url ) + '" style="max-height:40px;margin-left:8px;vertical-align:middle" />'
				);
				updateAdminPreview();
			} );
			mediaFrameIcon.open();
		} );

		// ---- Content search (admin settings) ----
		var adminContentSearchTimer = null;
		$( '#ebp-admin-content-search' ).on( 'input', function () {
			var val = $( this ).val();
			clearTimeout( adminContentSearchTimer );
			if ( val.length < 2 ) {
				$( '#ebp-admin-content-results' ).empty().hide();
				return;
			}
			adminContentSearchTimer = setTimeout( function () {
				$.post( ebpAdminData.ajaxurl, {
					action : 'ebp_search_content',
					nonce  : ebpAdminData.nonce,
					search : val,
				}, function ( response ) {
					var $results = $( '#ebp-admin-content-results' );
					$results.empty().show().css( { border: '1px solid #ddd', maxHeight: '160px', overflowY: 'auto' } );
					if ( ! response.success || ! response.data.length ) {
						$results.html( '<p style="padding:6px;margin:0;color:#888">Keine Ergebnisse</p>' );
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
							.on( 'mouseenter', function () { $( this ).css( 'background', '#f0f0f0' ); } )
							.on( 'mouseleave', function () { $( this ).css( 'background', '' ); } )
							.on( 'click', function () {
								$( '#ebp-admin-content-id' ).val( $( this ).data( 'id' ) );
								$( '#ebp-admin-content-search' ).val( $( this ).data( 'title' ) );
								$( '#ebp-admin-content-selected' ).html(
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

		// ---- Dashicon grid (admin settings page) ----
		buildDashiconGrid();

		// ---- Live update preview on any input change ----
		$( '#ebp-settings-form' ).on( 'input change', 'input, select, textarea', function () {
			updateAdminPreview();
		} );

		// Initial preview.
		updateAdminPreview();
	} );

	// -------------------------------------------------------------------------
	// Build Dashicon picker grid on the admin settings page
	// -------------------------------------------------------------------------
	function buildDashiconGrid() {
		var icons = ( typeof ebpAdminData !== 'undefined' ) ? ebpAdminData.dashicons : [];
		var $grid = $( '#ebp-dashicon-grid' );
		if ( ! $grid.length ) { return; }

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
			$( '#ebp-selected-icon' ).val( icon );
			$( '#ebp-dashicon-preview' ).html(
				'<span class="dashicons dashicons-' + icon + '" style="font-size:28px;width:28px;height:28px"></span>' +
				' <span class="ebp-icon-name">' + icon + '</span>'
			);
			$grid.find( '.ebp-icon-item' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			updateAdminPreview();
		} );

		$( '#ebp-dashicon-search' ).on( 'input', function () {
			renderGrid( $( this ).val().toLowerCase() );
		} );

		// Mark currently selected icon.
		var current = $( '#ebp-selected-icon' ).val();
		if ( current ) {
			$grid.find( '[title="' + current + '"]' ).addClass( 'selected' );
		}
	}

	// -------------------------------------------------------------------------
	// Update the admin page live preview
	// -------------------------------------------------------------------------
	function updateAdminPreview() {
		var $preview = $( '#ebp-preview-link' );
		if ( ! $preview.length ) { return; }

		var fontFamily   = $( 'input[name$="[font_family]"]' ).val()  || 'inherit';
		var fontSize     = parseInt( $( 'input[name$="[font_size]"]' ).val(), 10 )    || 16;
		var fontBold     = $( 'input[name$="[font_bold]"]' ).is( ':checked' );
		var fontItalic   = $( 'input[name$="[font_italic]"]' ).is( ':checked' );
		var buttonWidth  = parseInt( $( 'input[name$="[button_width]"]' ).val(), 10 ) || 0;
		var bgColor      = $( 'input[name$="[bg_color]"]' ).val()     || '#007bff';
		var textColor    = $( 'input[name$="[text_color]"]' ).val()   || '#ffffff';
		var paddingV     = parseInt( $( 'input[name$="[padding_v]"]' ).val(), 10 )    || 10;
		var paddingH     = parseInt( $( 'input[name$="[padding_h]"]' ).val(), 10 )    || 20;
		var borderW      = parseInt( $( 'input[name$="[border_width]"]' ).val(), 10 ) || 0;
		var borderStyle  = $( 'select[name$="[border_style]"]' ).val() || 'none';
		var borderColor  = $( 'input[name$="[border_color]"]' ).val() || '#000000';
		var borderRadius = parseInt( $( 'input[name$="[border_radius]"]' ).val(), 10 ) || 4;
		var shadowOn     = $( '#ebp-shadow-enabled' ).is( ':checked' );
		var shadowX      = parseInt( $( 'input[name$="[shadow_x]"]' ).val(), 10 )     || 0;
		var shadowY      = parseInt( $( 'input[name$="[shadow_y]"]' ).val(), 10 )     || 2;
		var shadowBlur   = parseInt( $( 'input[name$="[shadow_blur]"]' ).val(), 10 )  || 4;
		var shadowSprd   = parseInt( $( 'input[name$="[shadow_spread]"]' ).val(), 10 ) || 0;
		var shadowColor  = $( 'input[name$="[shadow_color]"]' ).val() || '#777777';
		var iconType     = $( 'input[name$="[icon_type]"]:checked' ).val()   || 'none';
		var iconSpacing  = parseInt( $( 'input[name$="[icon_spacing]"]' ).val(), 10 ) || 24;

		var css = {
			'display'         : 'inline-flex',
			'align-items'     : 'center',
			'justify-content' : 'center',
			'text-decoration' : 'none',
			'cursor'          : 'pointer',
			'font-family'     : fontFamily,
			'font-size'       : fontSize + 'px',
			'font-weight'     : fontBold   ? 'bold'   : 'normal',
			'font-style'      : fontItalic ? 'italic' : 'normal',
			'background-color': bgColor,
			'color'           : textColor,
			'padding'         : paddingV + 'px ' + paddingH + 'px',
			'border-width'    : borderW + 'px',
			'border-style'    : borderStyle,
			'border-color'    : borderColor,
			'border-radius'   : borderRadius + 'px',
			'transition'      : 'background-color .3s, color .3s, transform .3s',
			'box-shadow'      : shadowOn
				? shadowX + 'px ' + shadowY + 'px ' + shadowBlur + 'px ' + shadowSprd + 'px ' + shadowColor
				: 'none',
			'width'           : buttonWidth > 0 ? buttonWidth + 'px' : '',
		};

		if ( iconType !== 'none' ) {
			css['gap'] = iconSpacing + 'px';
		}

		$preview.css( css );

		// Icon.
		var iconHtml = '';
		var iconSize = parseInt( $( 'input[name$="[icon_size]"]' ).val(), 10 ) || 32;
		var iconPos  = $( 'input[name$="[icon_position]"]:checked' ).val() || 'before';

		if ( iconType === 'dashicon' ) {
			var di = $( '#ebp-selected-icon' ).val();
			if ( di ) {
				iconHtml = '<span class="dashicons dashicons-' + ebpEscHtml( di ) + '" ' +
					'style="font-size:' + iconSize + 'px;width:' + iconSize + 'px;height:' + iconSize + 'px"></span>';
			}
		} else if ( iconType === 'media' ) {
			var murl = $( '#ebp-icon-media-url' ).val();
			if ( murl ) {
				iconHtml = '<img src="' + ebpEscHtml( murl ) + '" style="width:' + iconSize + 'px;height:' + iconSize + 'px" alt="" />';
			}
		}

		$( '#ebp-admin-preview .ebp-preview-icon-before' ).html( iconPos === 'before' ? iconHtml : '' );
		$( '#ebp-admin-preview .ebp-preview-icon-after' ).html( iconPos === 'after'  ? iconHtml : '' );
		$( '#ebp-admin-preview .ebp-preview-text' ).text( 'Button Vorschau' );
	}

}( jQuery ) );
