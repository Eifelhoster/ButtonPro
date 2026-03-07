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
			title : 'Eifelhoster Button einfügen / bearbeiten',
			icon  : false,
			text  : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen / bearbeiten',
			onclick: function () {
				if ( typeof window.ebpOpenDialog !== 'function' ) {
					return;
				}
				var existing = ebpFindShortcodeAtCursor( editor );
				if ( existing ) {
					// Save TinyMCE bookmark so the selection can be restored after
					// the dialog steals focus.
					var bookmark = editor.selection.getBookmark( 2, true );
					window.ebpOpenDialog( editor, existing.attrs, bookmark );
				} else {
					window.ebpOpenDialog( editor, null, null );
				}
			}
		} );
	} );

	// -------------------------------------------------------------------------
	// Find an [eifelhoster_button …] shortcode at the current cursor position.
	// Returns { attrs: {…} } or null.
	// -------------------------------------------------------------------------
	function ebpFindShortcodeAtCursor( editor ) {
		try {
			var range     = editor.selection.getRng();
			var startNode = range.startContainer;

			// Walk up to find an ancestor block element within the editor body.
			var blockEl = startNode.nodeType === 3 ? startNode.parentNode : startNode;
			var body     = editor.getBody();
			while ( blockEl && blockEl !== body ) {
				var tag = ( blockEl.tagName || '' ).toLowerCase();
				if ( /^(p|div|li|td|th|h[1-6]|blockquote)$/.test( tag ) ) {
					break;
				}
				blockEl = blockEl.parentNode;
			}
			if ( ! blockEl || blockEl === body ) {
				blockEl = startNode.nodeType === 3 ? startNode.parentNode : startNode;
			}

			// Calculate cursor char-offset within the block's full text.
			var doc        = editor.getDoc();
			var treeWalker = doc.createTreeWalker( blockEl, 4 /* SHOW_TEXT */, null, false );
			var charOffset = 0;
			var found      = false;

			while ( treeWalker.nextNode() ) {
				var node = treeWalker.currentNode;
				if ( node === range.startContainer ) {
					charOffset += range.startOffset;
					found = true;
					break;
				}
				charOffset += node.nodeValue.length;
			}

			if ( ! found ) {
				return null;
			}

			var fullText = blockEl.textContent || '';
			var re       = /\[eifelhoster_button(?:\s[^\]]*?)?\]/g;
			var match;

			while ( ( match = re.exec( fullText ) ) !== null ) {
				var start = match.index;
				var end   = start + match[0].length;
				if ( charOffset >= start && charOffset <= end ) {
					// Select the shortcode text in the editor.
					ebpSelectRangeInBlock( editor, blockEl, start, end );
					return { attrs: ebpParseShortcodeAttrs( match[0] ) };
				}
			}
		} catch ( e ) { /* ignore */ }

		return null;
	}

	// -------------------------------------------------------------------------
	// Select characters [start, end) within a block element's text content.
	// -------------------------------------------------------------------------
	function ebpSelectRangeInBlock( editor, blockEl, start, end ) {
		var doc       = editor.getDoc();
		var walker    = doc.createTreeWalker( blockEl, 4 /* SHOW_TEXT */, null, false );
		var charCount = 0;
		var startNode = null, startOff = 0, endNode = null, endOff = 0;

		while ( walker.nextNode() ) {
			var node = walker.currentNode;
			var len  = node.nodeValue.length;

			if ( ! startNode && charCount + len > start ) {
				startNode = node;
				startOff  = start - charCount;
			}
			if ( ! endNode && charCount + len >= end ) {
				endNode = node;
				endOff  = end - charCount;
			}
			if ( startNode && endNode ) { break; }
			charCount += len;
		}

		if ( startNode && endNode ) {
			var range = doc.createRange();
			range.setStart( startNode, startOff );
			range.setEnd( endNode, endOff );
			editor.selection.setRng( range );
		}
	}

	// -------------------------------------------------------------------------
	// Parse [eifelhoster_button attr="value" …] into a plain object.
	// -------------------------------------------------------------------------
	function ebpParseShortcodeAttrs( scText ) {
		var attrs = {};
		var re    = /(\w+)="([^"]*)"/g;
		var match;
		while ( ( match = re.exec( scText ) ) !== null ) {
			attrs[ match[1] ] = ebpDecodeHtmlEntities( match[2] );
		}
		// Also handle single-quoted values.
		re = /(\w+)='([^']*)'/g;
		while ( ( match = re.exec( scText ) ) !== null ) {
			if ( attrs[ match[1] ] === undefined ) {
				attrs[ match[1] ] = ebpDecodeHtmlEntities( match[2] );
			}
		}
		return attrs;
	}

	// -------------------------------------------------------------------------
	// Decode HTML entities using a temporary textarea.
	// -------------------------------------------------------------------------
	function ebpDecodeHtmlEntities( str ) {
		try {
			var doc = tinymce.activeEditor ? tinymce.activeEditor.getDoc() : document;
			var el  = doc.createElement( 'textarea' );
			el.innerHTML = str;
			return el.value;
		} catch ( e ) {
			return str;
		}
	}

}());
