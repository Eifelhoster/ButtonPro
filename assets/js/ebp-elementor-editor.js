/**
 * Elementor Editor – Eifelhoster Buttons Pro
 *
 * Adds live-search (AJAX, min. 1 character) to the content_search control
 * in the "Link & Ziel" section and auto-fills the content_id control on
 * result selection.
 */
/* global jQuery, ebpElementorData */
( function ( $ ) {
	'use strict';

	var searchTimer    = null;
	var $activeResults = null;
	var $activeInput   = null;
	var initialized    = false;

	// -------------------------------------------------------------------------
	// Close open results dropdown
	// -------------------------------------------------------------------------
	function closeResults() {
		if ( $activeResults ) {
			$activeResults.remove();
			$activeResults = null;
		}
		$activeInput = null;
	}

	// -------------------------------------------------------------------------
	// HTML-escape helper (avoids XSS when building results HTML)
	// -------------------------------------------------------------------------
	function escHtml( str ) {
		return $( '<div>' ).text( String( str ) ).html();
	}

	// -------------------------------------------------------------------------
	// Fire AJAX search and render results below the input
	// -------------------------------------------------------------------------
	function doSearch( val, $input ) {
		clearTimeout( searchTimer );
		closeResults();

		if ( val.length < 1 ) {
			return;
		}

		$activeInput = $input;

		searchTimer = setTimeout( function () {
			$.post(
				ebpElementorData.ajaxurl,
				{
					action : 'ebp_search_content',
					nonce  : ebpElementorData.nonce,
					search : val,
				},
				function ( response ) {
					// Discard if input changed while we were waiting.
					if ( $activeInput !== $input ) {
						return;
					}
					closeResults();

					$activeResults = $( '<div class="ebp-el-search-results">' ).css( {
						position   : 'absolute',
						zIndex     : 99999,
						left       : 0,
						right      : 0,
						top        : '100%',
						background : '#fff',
						border     : '1px solid #c3c4c7',
						borderTop  : 'none',
						maxHeight  : '200px',
						overflowY  : 'auto',
						boxShadow  : '0 4px 8px rgba(0,0,0,.15)',
						fontSize   : '12px',
					} );

					if ( ! response.success || ! response.data.length ) {
						$activeResults.append(
							$( '<div>' )
								.css( { padding: '8px 10px', color: '#888' } )
								.text( 'Keine Ergebnisse' )
						);
					} else {
						$.each( response.data, function ( i, item ) {
							var $row = $( '<div>' )
								.css( {
									padding      : '6px 10px',
									cursor       : 'pointer',
									borderBottom : '1px solid #f0f0f0',
								} )
								.html(
									'<strong>' + escHtml( item.title ) + '</strong>' +
									' <span style="color:#999">(' + escHtml( item.type ) + ')</span>'
								)
								.data( 'id',    item.id )
								.data( 'title', item.title );

							$row.on( 'mouseenter', function () {
								$( this ).css( 'background', '#f0f6fc' );
							} );
							$row.on( 'mouseleave', function () {
								$( this ).css( 'background', '' );
							} );

							// Use mousedown (not click) so it fires before the blur on $input.
							$row.on( 'mousedown', function ( e ) {
								e.preventDefault();

								var id    = $( this ).data( 'id' );
								var title = $( this ).data( 'title' );

								// Update the visible search control with the selected title.
								$input.val( title ).trigger( 'input' );

								// Find the content_id control within the same widget controls
								// section to avoid touching other widget panels.
								var $section = $input.closest( '.elementor-controls-stack, .elementor-section-wrap' );
								var $idInput = $section.length
									? $section.find( '.elementor-control-content_id input' )
									: $input.closest( '.elementor-panel' ).find( '.elementor-control-content_id input' );

								if ( $idInput.length ) {
									$idInput.val( String( id ) ).trigger( 'input' ).trigger( 'change' );
								}

								closeResults();
							} );

							$activeResults.append( $row );
						} );
					}

					// Append results inside the control's input-wrapper so they
					// scroll with the panel.
					var $wrapper = $input.closest( '.elementor-control-input-wrapper' );
					if ( ! $wrapper.length ) {
						$wrapper = $input.parent();
					}
					$wrapper.css( 'position', 'relative' );
					$wrapper.append( $activeResults );
				}
			);
		}, 300 );
	}

	// -------------------------------------------------------------------------
	// Initialise event delegation (guard against double-init)
	// -------------------------------------------------------------------------
	function init() {
		if ( initialized ) {
			return;
		}
		initialized = true;

		// Live search on typing in the content_search control.
		$( document ).on(
			'input',
			'.elementor-control-content_search input[type="text"]',
			function () {
				doSearch( $( this ).val().trim(), $( this ) );
			}
		);

		// Close results when the search input loses focus (with a small delay
		// to allow the mousedown handler on result rows to fire first).
		$( document ).on(
			'blur',
			'.elementor-control-content_search input[type="text"]',
			function () {
				setTimeout( closeResults, 200 );
			}
		);

		// Also close when clicking anywhere else in the document.
		$( document ).on( 'click', function ( e ) {
			if (
				$activeResults &&
				! $activeResults.is( e.target ) &&
				! $activeResults.has( e.target ).length
			) {
				closeResults();
			}
		} );
	}

	// Wait for Elementor to initialise, then hook up.
	$( window ).on( 'elementor:init', init );

	// Fallback: also run after DOM ready (handles cases where Elementor fires
	// its init event before our script is loaded).
	$( document ).ready( init );

} )( jQuery );
