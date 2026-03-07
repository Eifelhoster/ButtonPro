/**
 * Elementor editor JS for Eifelhoster Buttons Pro.
 *
 * Handles the content search/select UI inside the Elementor panel
 * (replaces the raw content_id number input with a live-search field).
 */
/* global jQuery, ebpElData */
( function ( $ ) {
	'use strict';

	// Simple HTML-escape helper to prevent XSS when building DOM via .html().
	function escHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;'  )
			.replace( />/g, '&gt;'  )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#039;' );
	}

	// -------------------------------------------------------------------------
	// Content search – event delegation so it works after Elementor re-renders.
	// -------------------------------------------------------------------------
	var searchTimer = null;

	$( document ).on( 'input', '.ebp-el-content-search', function () {
		var $input   = $( this );
		var $wrap    = $input.closest( '.ebp-el-content-search-wrap' );
		var $results = $wrap.find( '.ebp-el-content-results' );
		var val      = $input.val();

		clearTimeout( searchTimer );

		if ( val.length < 2 ) {
			$results.hide().empty();
			return;
		}

		searchTimer = setTimeout( function () {
			$.post(
				ebpElData.ajaxurl,
				{
					action : 'ebp_search_content',
					nonce  : ebpElData.nonce,
					search : val,
				},
				function ( response ) {
					$results.empty().show();
					if ( ! response.success || ! response.data.length ) {
						$results.html(
							'<p style="padding:6px;margin:0;color:#888">' +
							escHtml( ebpElData.i18n.noResults ) + '</p>'
						);
						return;
					}
					$.each( response.data, function ( i, item ) {
						var $row = $( '<div>' )
							.css( { padding: '6px 8px', cursor: 'pointer', borderBottom: '1px solid #eee' } )
							.html(
								'<strong>' + escHtml( item.title ) + '</strong> ' +
								'<span style="color:#888;font-size:11px">(' + escHtml( item.type ) + ')</span>'
							)
							.data( 'id',    item.id )
							.data( 'title', item.title )
							.on( 'mouseenter', function () { $( this ).css( 'background', '#f0f0f0' ); } )
							.on( 'mouseleave', function () { $( this ).css( 'background', ''        ); } )
							.on( 'click', function () {
								var id    = $( this ).data( 'id' );
								var title = $( this ).data( 'title' );

								// Show the selected title in the search field.
								$input.val( title );

								// Update the selection display.
								$wrap.find( '.ebp-el-content-selected' ).html(
									'<span style="color:green;vertical-align:middle">&#10003;</span> ' +
									escHtml( title )
								);

								$results.hide().empty();

								// Update the Elementor content_id control so the setting is saved.
								var $idInput = $( '.elementor-control-content_id input[data-setting="content_id"]' );
								if ( $idInput.length ) {
									$idInput.val( id ).trigger( 'input' );
								}
							} );
						$results.append( $row );
					} );
				}
			);
		}, 300 );
	} );

	// Close results when clicking outside the search wrap.
	$( document ).on( 'click', function ( e ) {
		if ( ! $( e.target ).closest( '.ebp-el-content-search-wrap' ).length ) {
			$( '.ebp-el-content-results' ).hide().empty();
		}
	} );

	// -------------------------------------------------------------------------
	// When the Elementor panel shows an existing widget that already has a
	// content_id saved, pre-fill the search field so the user can see the value.
	// -------------------------------------------------------------------------
	$( document ).on( 'input change', '.elementor-control-content_id input[data-setting="content_id"]', function () {
		var val = $( this ).val();
		var $panel = $( this ).closest( '.elementor-panel-box-content, .elementor-section-wrap' );
		var $wrap  = $panel.find( '.ebp-el-content-search-wrap' );

		if ( ! $wrap.length ) {
			$wrap = $( '.ebp-el-content-search-wrap' );
		}

		// Only pre-fill if the search field is still empty (don't override user input).
		var $search = $wrap.find( '.ebp-el-content-search' );
		if ( $search.length && ! $search.val() && val ) {
			$wrap.find( '.ebp-el-content-selected' ).html(
				'<span style="color:#888">ID: ' + escHtml( String( val ) ) + '</span>'
			);
		}
	} );

}( jQuery ) );
