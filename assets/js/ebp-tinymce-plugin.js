/**
 * TinyMCE plugin – registers the "ebp_button" button in the Classic Editor toolbar.
 *
 * This file MUST be a standalone JS file (no jQuery wrapper) because TinyMCE
 * loads it in a separate context.
 */
/* global tinymce */
(function () {
	'use strict';

	/**
	 * If the cursor is positioned inside an [eifelhoster_button ...] shortcode,
	 * select the whole shortcode in the editor and return its raw string.
	 * Returns null when no matching shortcode is found at the cursor.
	 *
	 * @param  {Object} editor  TinyMCE editor instance.
	 * @return {string|null}
	 */
	function ebpGetShortcodeAtCursor( editor ) {
		var rng  = editor.selection.getRng( true );
		var node = rng.startContainer;

		// We need a text node to search within.
		if ( node.nodeType !== 3 ) {
			return null;
		}

		var text   = node.nodeValue || '';
		var offset = rng.startOffset;

		// Walk left from the cursor to find the nearest opening '['.
		var bracketStart = -1;
		for ( var i = offset; i >= 0; i-- ) {
			if ( text.charAt( i ) === '[' ) {
				bracketStart = i;
				break;
			}
		}
		if ( bracketStart === -1 ) {
			return null;
		}

		// Find the first ']' after the opening bracket.
		var bracketEnd = text.indexOf( ']', bracketStart );
		if ( bracketEnd === -1 ) {
			return null;
		}

		// Cursor must lie within [bracketStart … bracketEnd] (inclusive).
		if ( offset < bracketStart || offset > bracketEnd ) {
			return null;
		}

		var shortcode = text.substring( bracketStart, bracketEnd + 1 );

		// Confirm it is our shortcode.
		if ( ! /^\[eifelhoster_button\b/.test( shortcode ) ) {
			return null;
		}

		// Select the entire shortcode so ebpInsertShortcode() can replace it.
		var newRng = editor.dom.createRng();
		newRng.setStart( node, bracketStart );
		newRng.setEnd( node, bracketEnd + 1 );
		editor.selection.setRng( newRng );

		return shortcode;
	}

	tinymce.PluginManager.add( 'ebp_button', function ( editor ) {
		editor.addButton( 'ebp_button', {
			title  : 'Eifelhoster Button einfügen',
			icon   : false,
			text   : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen',
			onclick: function () {
				if ( typeof window.ebpOpenDialog === 'function' ) {
					var existingShortcode = ebpGetShortcodeAtCursor( editor );
					window.ebpOpenDialog( editor, existingShortcode );
				}
			}
		} );
	} );
}());
