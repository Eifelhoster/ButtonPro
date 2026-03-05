/**
 * TinyMCE View for Eifelhoster Buttons Pro.
 *
 * Registers a wp.mce.views view type for the [eifelhoster_button] shortcode so
 * that TinyMCE replaces the raw shortcode text with the actual rendered button,
 * giving editors a true WYSIWYG preview inside the Classic Editor.
 *
 * The rendered HTML is fetched from the server via AJAX so the output is
 * pixel-perfect identical to the frontend.  A loading placeholder is shown
 * while the request is in-flight.
 */
/* global wp, jQuery, ebpMceData */
( function( $ ) {
	'use strict';

	if ( typeof wp === 'undefined' || ! wp.mce || ! wp.mce.views ) {
		return;
	}

	wp.mce.views.register( 'eifelhoster_button', {

		View: {

			/**
			 * Called by wp.mce.views for every [eifelhoster_button] shortcode
			 * found in the editor content.
			 *
			 * @param {Object} options  Contains `options.shortcode` (wp.shortcode instance).
			 */
			initialize: function( options ) {
				var self = this;
				this.shortcode = options.shortcode;

				// Show a loading indicator while we fetch the rendered HTML.
				this.setContent(
					'<p class="ebp-mce-loading" style="text-align:center;padding:8px;color:#666;">' +
					'⏳ Button wird geladen…</p>',
					{ parse: false }
				);

				$.post(
					ebpMceData.ajaxurl,
					{
						action   : 'ebp_render_button',
						nonce    : ebpMceData.nonce,
						shortcode: options.shortcode.string(),
					},
					function( response ) {
						if ( response && response.success ) {
							self.setContent( response.data.html, { parse: false } );
						} else {
							// On error fall back to displaying the raw shortcode.
							self.setContent(
								'<p class="ebp-mce-error" style="color:#c00;padding:4px 8px;">' +
								'[eifelhoster_button …]</p>',
								{ parse: false }
							);
						}
					}
				);
			},
		},

	} );

}( jQuery ) );
