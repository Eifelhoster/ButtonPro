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
				if ( typeof window.ebpOpenDialog !== 'function' ) {
					return;
				}
				// Pass edit-data when the cursor sits inside an existing shortcode.
				var editData = ebpGetShortcodeAtCursor( editor );
				window.ebpOpenDialog( editor, editData );
			}
		} );
	} );

	/**
	 * Inspect the text node at the cursor position.
	 * Returns { shortcode, node, start, end } when the cursor is inside an
	 * [eifelhoster_button …] shortcode, or null otherwise.
	 *
	 * @param  {Object} editor  TinyMCE editor instance.
	 * @return {Object|null}
	 */
	function ebpGetShortcodeAtCursor( editor ) {
		var rng       = editor.selection.getRng();
		var container = rng.startContainer;

		// Only works within a text node.
		if ( ! container || container.nodeType !== 3 ) {
			return null;
		}

		var text   = container.nodeValue || '';
		var offset = rng.startOffset;
		var re     = /\[eifelhoster_button\b[^\]]*\]/g;
		var match;
		re.lastIndex = 0;

		while ( ( match = re.exec( text ) ) !== null ) {
			if ( match.index <= offset && offset <= match.index + match[0].length ) {
				return {
					shortcode : match[0],
					node      : container,
					start     : match.index,
					end       : match.index + match[0].length,
				};
			}
		}
		return null;
	}
}());
