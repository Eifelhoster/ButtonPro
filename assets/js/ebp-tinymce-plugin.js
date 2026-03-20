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
	 * Detect whether the cursor is positioned inside an [eifelhoster_button ...]
	 * shortcode in the current text node.  Returns { shortcode, range } or null.
	 */
	function ebpGetShortcodeAtCursor( editor ) {
		var rng  = editor.selection.getRng( true );
		var node = rng.startContainer;

		// Only handle text nodes.
		if ( node.nodeType !== 3 ) {
			return null;
		}

		var text   = node.textContent || node.nodeValue || '';
		var offset = rng.startOffset;
		var re     = /\[eifelhoster_button\b[^\]]*\]/g;
		var m;

		while ( ( m = re.exec( text ) ) !== null ) {
			var start = m.index;
			var end   = start + m[0].length;
			if ( offset >= start && offset <= end ) {
				// Cursor is inside this shortcode – create a range that selects it.
				var newRng = editor.dom.createRng();
				newRng.setStart( node, start );
				newRng.setEnd( node, end );
				return { shortcode: m[0], range: newRng };
			}
		}
		return null;
	}

	tinymce.PluginManager.add( 'ebp_button', function ( editor ) {
		editor.addButton( 'ebp_button', {
			title  : 'Eifelhoster Button einfügen',
			icon   : false,
			text   : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen',
			onclick: function () {
				if ( typeof window.ebpOpenDialog === 'function' ) {
					// Pass cursor-shortcode info so the dialog can switch to edit mode.
					window.ebpOpenDialog( editor, ebpGetShortcodeAtCursor( editor ) );
				}
			}
		} );
	} );
}());
