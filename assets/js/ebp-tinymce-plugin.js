/**
 * TinyMCE plugin – registers the "ebp_button" button in the Classic Editor toolbar.
 *
 * This file MUST be a standalone JS file (no jQuery wrapper) because TinyMCE
 * loads it in a separate context.
 */
/* global tinymce */
(function () {
	'use strict';

	tinymce.PluginManager.add( 'ebp_button', function ( editor ) {
		editor.addButton( 'ebp_button', {
			title : 'Eifelhoster Button einfügen',
			icon  : false,
			text  : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen',
			onclick: function () {
				// Store current editor reference so the dialog can insert content.
				if ( typeof window.ebpOpenDialog === 'function' ) {
					window.ebpOpenDialog( editor );
				}
			}
		} );
	} );
}());
