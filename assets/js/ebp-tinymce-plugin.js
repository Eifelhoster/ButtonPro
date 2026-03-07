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
				var existing = ebpDetectShortcodeAtCursor( editor );
				if ( existing ) {
					// Select the existing shortcode so that saving will replace it.
					ebpSelectShortcodeText( editor, existing );
					if ( typeof window.ebpOpenDialogEdit === 'function' ) {
						window.ebpOpenDialogEdit( editor, existing );
					}
				} else if ( typeof window.ebpOpenDialog === 'function' ) {
					window.ebpOpenDialog( editor );
				}
			}
		} );

		/**
		 * Returns the raw shortcode string if the cursor is positioned inside
		 * a [eifelhoster_button ...] shortcode, or null otherwise.
		 *
		 * @param  {Object} ed TinyMCE editor instance.
		 * @return {string|null}
		 */
		function ebpDetectShortcodeAtCursor( ed ) {
			var rng = ed.selection.getRng( true );
			if ( ! rng ) {
				return null;
			}

			var container = rng.startContainer;
			var offset    = rng.startOffset;

			// Walk up to the nearest block-level ancestor within the editor body.
			var block = container;
			var body  = ed.getBody();
			while ( block && block !== body ) {
				if ( /^(P|DIV|H[1-6]|LI|TD|TH|BLOCKQUOTE|FIGURE|SECTION|ARTICLE)$/i.test( block.nodeName ) ) {
					break;
				}
				block = block.parentNode;
			}
			if ( ! block || block === body ) {
				block = body;
			}

			// Build the text content of the block and track the cursor position.
			var doc      = ed.getDoc();
			var walker   = doc.createTreeWalker( block, 4 /* NodeFilter.SHOW_TEXT */ );
			var text     = '';
			var cursorPos = -1;
			var nd;
			while ( ( nd = walker.nextNode() ) ) {
				if ( nd === container ) {
					cursorPos = text.length + offset;
				}
				text += nd.nodeValue || '';
			}

			// Search for [eifelhoster_button ...] that contains the cursor position.
			var re = /\[eifelhoster_button(?:\s[^\[\]]*?)?\]/g;
			var match;

			// When cursorPos could not be determined (container was a non-text node),
			// only return a shortcode if there is exactly one in the block – otherwise
			// we cannot reliably determine which one the cursor is in.
			if ( cursorPos === -1 ) {
				var allMatches = text.match( /\[eifelhoster_button(?:\s[^\[\]]*?)?\]/g );
				return ( allMatches && allMatches.length === 1 ) ? allMatches[0] : null;
			}

			while ( ( match = re.exec( text ) ) !== null ) {
				var scStart = match.index;
				var scEnd   = scStart + match[0].length;
				if ( cursorPos >= scStart && cursorPos <= scEnd ) {
					return match[0];
				}
			}
			return null;
		}

		/**
		 * Selects the given shortcode text string inside the TinyMCE editor so
		 * that a subsequent setContent() call will replace exactly that text.
		 *
		 * @param {Object} ed            TinyMCE editor instance.
		 * @param {string} shortcodeStr  The exact shortcode text to select.
		 */
		function ebpSelectShortcodeText( ed, shortcodeStr ) {
			var body   = ed.getBody();
			var doc    = ed.getDoc();
			var walker = doc.createTreeWalker( body, 4 /* NodeFilter.SHOW_TEXT */ );
			var nd;
			while ( ( nd = walker.nextNode() ) ) {
				var idx = ( nd.nodeValue || '' ).indexOf( shortcodeStr );
				if ( idx !== -1 ) {
					var rng = ed.dom.createRng();
					rng.setStart( nd, idx );
					rng.setEnd( nd, idx + shortcodeStr.length );
					ed.selection.setRng( rng );
					return;
				}
			}
		}
	} );
}());

