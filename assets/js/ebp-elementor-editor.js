/**
 * Elementor editor JS for Eifelhoster Buttons Pro.
 *
 * Provides live content-search for the content_search control inside
 * the Elementor panel, populating content_id on selection.
 */
/* global jQuery, ebpElementorData */
( function ( $ ) {
	'use strict';

	// -------------------------------------------------------------------------
	// HTML-escape helper
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
	// Content search
	// -------------------------------------------------------------------------
	var searchTimer  = null;
	var $dropdown    = null;
	var $lastInput   = null;

	function positionDropdown( $input ) {
		if ( ! $dropdown ) {
			return;
		}
		$dropdown.css( {
			position : 'absolute',
			zIndex   : 9999,
			left     : $input.offset().left,
			top      : $input.offset().top + $input.outerHeight(),
			width    : $input.outerWidth(),
		} );
	}

	function getOrCreateDropdown( $input ) {
		if ( ! $dropdown || ! $dropdown.length || ! $.contains( document, $dropdown[0] ) ) {
			$dropdown = $( '<div class="ebp-elementor-search-results">' ).css( {
				position   : 'fixed',
				zIndex     : 99999,
				background : '#fff',
				border     : '1px solid #ddd',
				maxHeight  : '160px',
				overflowY  : 'auto',
				boxShadow  : '0 2px 6px rgba(0,0,0,.15)',
			} ).appendTo( 'body' );
		}
		$lastInput = $input;
		return $dropdown;
	}

	function hideDropdown() {
		if ( $dropdown ) {
			$dropdown.hide().empty();
		}
	}

	$( document ).on( 'input', '[data-setting="content_search"]', function () {
		var $input = $( this );
		var val    = $input.val();
		clearTimeout( searchTimer );

		if ( val.length < 2 ) {
			hideDropdown();
			return;
		}

		searchTimer = setTimeout( function () {
			$.post(
				ebpElementorData.ajaxurl,
				{
					action : 'ebp_search_content',
					nonce  : ebpElementorData.nonce,
					search : val,
				},
				function ( response ) {
					var $dd = getOrCreateDropdown( $input );
					$dd.empty();
					positionDropdown( $input );

					if ( ! response.success || ! response.data.length ) {
						$dd.html( '<div style="padding:6px;color:#888">' + ebpEscHtml( 'Keine Ergebnisse' ) + '</div>' ).show();
						return;
					}

					$.each( response.data, function ( i, item ) {
						$( '<div>' )
							.css( { padding: '6px 8px', cursor: 'pointer', borderBottom: '1px solid #eee', fontSize: '12px' } )
							.html(
								'<strong>' + ebpEscHtml( item.title ) + '</strong>' +
								' <span style="color:#888">(' + ebpEscHtml( item.type ) + ')</span>'
							)
							.data( 'id', item.id )
							.data( 'title', item.title )
							.on( 'mouseenter', function () { $( this ).css( 'background', '#f0f0f0' ); } )
							.on( 'mouseleave', function () { $( this ).css( 'background', '' ); } )
							.on( 'mousedown', function ( e ) {
								e.preventDefault();
								var id    = $( this ).data( 'id' );
								var title = $( this ).data( 'title' );

								// Update the search display field.
								$input.val( title );

								// Update the content_id control (triggers Elementor model sync).
								var $idInput = $( '[data-setting="content_id"]' );
								$idInput.val( id ).trigger( 'input' );

								hideDropdown();
							} )
							.appendTo( $dd );
					} );

					$dd.show();
				}
			);
		}, 300 );
	} );

	// Hide dropdown on outside click.
	$( document ).on( 'click', function ( e ) {
		if (
			$dropdown &&
			$lastInput &&
			! $dropdown.is( e.target ) &&
			! $dropdown.has( e.target ).length &&
			! $lastInput.is( e.target )
		) {
			hideDropdown();
		}
	} );

	// Reposition on scroll/resize.
	$( window ).on( 'scroll resize', function () {
		if ( $dropdown && $dropdown.is( ':visible' ) && $lastInput ) {
			positionDropdown( $lastInput );
		}
	} );

}( jQuery ) );
