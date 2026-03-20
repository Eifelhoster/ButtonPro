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
	 * Detect whether the cursor is currently inside an [eifelhoster_button ...]
	 * shortcode. Returns { shortcode, range } when found, null otherwise.
	 */
	function ebpGetShortcodeAtCursor( editor ) {
		var selection = editor.selection;
		var rng  = selection.getRng();
		var node = rng.startContainer;

		// We only operate on text nodes.
		if ( ! node || node.nodeType !== 3 ) {
			return null;
		}

		var text   = node.nodeValue || '';
		var offset = rng.startOffset;

		// Search backwards from cursor for opening bracket.
		var scStart = text.lastIndexOf( '[eifelhoster_button', offset );
		if ( scStart === -1 ) {
			return null;
		}

		// Search forward for the closing bracket.
		var scEnd = text.indexOf( ']', scStart );
		if ( scEnd === -1 ) {
			return null;
		}

		// Cursor must sit within [scStart … scEnd+1].
		if ( offset < scStart || offset > scEnd + 1 ) {
			return null;
		}

		var shortcode = text.substring( scStart, scEnd + 1 );

		// Build a DOM range that exactly covers the shortcode text.
		var newRng = editor.dom.createRng();
		newRng.setStart( node, scStart );
		newRng.setEnd( node, scEnd + 1 );

		return { shortcode: shortcode, range: newRng };
	}

	tinymce.PluginManager.add( 'ebp_button', function ( editor ) {
		editor.addButton( 'ebp_button', {
			title  : 'Eifelhoster Button einfügen',
			icon   : false,
			text   : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen',
			onclick: function () {
				if ( typeof window.ebpOpenDialog === 'function' ) {
					// Pass shortcode data (or null) so the dialog can switch
					// between "insert new" and "edit existing" mode.
					var shortcodeData = ebpGetShortcodeAtCursor( editor );
					window.ebpOpenDialog( editor, shortcodeData );
				}
			}
		} );
	} );
}());
