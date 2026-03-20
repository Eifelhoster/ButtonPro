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

				// 1. Check whether the cursor is inside an HTML button element (new style).
				var buttonNode = ebpGetButtonAtCursor( editor );
				if ( buttonNode ) {
					window.ebpOpenDialog( editor, buttonNode );
					return;
				}

				// 2. Fall back to shortcode detection (backward compatibility).
				var scData = ebpGetShortcodeAtCursor( editor );
				window.ebpOpenDialog( editor, scData );
			}
		} );
	} );

	/**
	 * Walk up the DOM from the cursor position and return the nearest
	 * <a class="ebp-button"> ancestor, or null if none is found.
	 *
	 * @param  {Object} editor  TinyMCE editor instance.
	 * @return {Element|null}
	 */
	function ebpGetButtonAtCursor( editor ) {
		var node = editor.selection.getStart();
		while ( node && node.nodeName !== 'BODY' ) {
			if ( node.nodeName === 'A' &&
				( node.className || '' ).split( ' ' ).indexOf( 'ebp-button' ) !== -1 ) {
				return node;
			}
			node = node.parentNode;
		}
		return null;
	}

	/**
	 * Inspect the text node at the cursor position.
	 * Returns { shortcode, node, start, end } when the cursor is inside an
	 * [eifelhoster_button …] shortcode, or null otherwise.
	 * Kept for backward compatibility with existing posts that still contain
	 * the raw shortcode markup.
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
