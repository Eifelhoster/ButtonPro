/**
 * Eifelhoster Buttons Pro – Elementor editor script.
 *
 * Enhances the "content_search" control inside the Eifelhoster Button widget
 * with AJAX-based autocomplete for posts, pages and CPTs.
 * Results are listed and, on selection, the "content_id" field is updated.
 */
/* global ebpElementorData */
( function ( $ ) {
	'use strict';

	var SEARCH_INPUT_SEL = '.elementor-control-content_search input';
	var ID_INPUT_SEL     = '.elementor-control-content_id input';

	// -------------------------------------------------------------------------
	// Attach autocomplete behaviour to the content_search control
	// -------------------------------------------------------------------------
	function attachContentSearch( $panel ) {
		var $searchInput = $panel.find( SEARCH_INPUT_SEL );

		if ( ! $searchInput.length || $searchInput.data( 'ebp-autocomplete' ) ) {
			return;
		}

		$searchInput.data( 'ebp-autocomplete', true );

		// Create results list
		var $results = $( '<ul class="ebp-content-results ebp-elementor-results"></ul>' );
		$searchInput.closest( '.elementor-control-field' ).append( $results );

		var searchTimer = null;

		$searchInput.on( 'input', function () {
			clearTimeout( searchTimer );
			var term = $( this ).val();

			if ( term.length < 2 ) {
				$results.hide().empty();
				return;
			}

			$results
				.html( '<li class="ebp-content-searching">' + ebpElementorData.i18n.searching + '</li>' )
				.show();

			searchTimer = setTimeout( function () {
				$.ajax( {
					url  : ebpElementorData.ajaxurl,
					type : 'GET',
					data : {
						action : 'ebp_search_content',
						nonce  : ebpElementorData.nonce,
						term   : term,
					},
					success: function ( resp ) {
						$results.empty();
						if ( resp.success && resp.data.length ) {
							$.each( resp.data, function ( i, item ) {
								$( '<li class="ebp-content-result-item"></li>' )
									.html(
										'<strong>' + escHtml( item.title ) + '</strong>' +
										' <span class="ebp-content-type">[' + escHtml( item.type ) + ']</span>'
									)
									.data( 'item', item )
									.appendTo( $results );
							} );
							$results.show();
						} else {
							$results
								.html( '<li class="ebp-content-no-results">' + ebpElementorData.i18n.noResults + '</li>' )
								.show();
						}
					},
				} );
			}, 300 );
		} );

		// On result click: update both inputs and trigger Elementor model update
		$results.on( 'click', '.ebp-content-result-item', function () {
			var item = $( this ).data( 'item' );

			// Update the display / search field
			$searchInput.val( item.title ).trigger( 'input.elementorControl' );

			// Update the hidden content_id numeric field
			var $idInput = $panel.find( ID_INPUT_SEL );
			if ( $idInput.length ) {
				$idInput.val( item.id ).trigger( 'input' ).trigger( 'change' );
			}

			$results.hide().empty();
		} );

		// Hide results when clicking outside
		$( document ).on( 'mousedown.ebp-el-content', function ( e ) {
			if (
				! $( e.target ).closest( $results ).length &&
				! $( e.target ).is( $searchInput )
			) {
				$results.hide();
			}
		} );
	}

	// -------------------------------------------------------------------------
	// HTML-escape helper
	// -------------------------------------------------------------------------
	function escHtml( str ) {
		return String( str )
			.replace( /&/g,  '&amp;' )
			.replace( /</g,  '&lt;'  )
			.replace( />/g,  '&gt;'  )
			.replace( /"/g,  '&quot;' )
			.replace( /'/g,  '&#039;' );
	}

	// -------------------------------------------------------------------------
	// Observe Elementor panel for new widgets opening
	// -------------------------------------------------------------------------
	function observePanel() {
		var panelEl = document.getElementById( 'elementor-panel' );
		if ( ! panelEl ) {
			return;
		}

		var $panel = $( panelEl );

		// Try immediately (panel may already show a widget)
		attachContentSearch( $panel );

		// Watch for DOM changes (widget panel opens / tabs switch)
		var observer = new window.MutationObserver( function () {
			attachContentSearch( $panel );
		} );

		observer.observe( panelEl, { childList: true, subtree: true } );
	}

	// -------------------------------------------------------------------------
	// Bootstrap
	// -------------------------------------------------------------------------
	$( window ).on( 'elementor:init', function () {
		observePanel();
	} );

	// Fallback for cases where elementor:init has already fired
	$( document ).ready( function () {
		setTimeout( observePanel, 800 );
	} );

}( jQuery ) );
