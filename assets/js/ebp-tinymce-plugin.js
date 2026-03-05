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
	 * Parse a shortcode attribute string (the part inside the brackets after the
	 * tag name) into a plain key→value object.  Only key="value" notation is
	 * supported because that is what ebpInsertShortcode always produces.
	 *
	 * @param  {string} attrStr  Raw attribute string, e.g. ' text="Click" bg_color="#336699"'
	 * @return {Object}
	 */
	function ebpParseShortcodeAttrs( attrStr ) {
		var attrs   = {};
		var pattern = /(\w+)="([^"]*)"/g;
		var match;
		while ( ( match = pattern.exec( attrStr ) ) !== null ) {
			attrs[ match[1] ] = match[2].replace( /&quot;/g, '"' ).replace( /&amp;/g, '&' );
		}
		return attrs;
	}

	/**
	 * If the TinyMCE cursor is positioned inside an [eifelhoster_button …]
	 * shortcode, select the entire shortcode text and return its parsed
	 * attributes.  Returns null when the cursor is not on a shortcode.
	 *
	 * @param  {Object} editor  TinyMCE editor instance
	 * @return {Object|null}
	 */
	function ebpGetShortcodeAtCursor( editor ) {
		var sel       = editor.selection;
		var rng       = sel.getRng( true );
		var container = rng.startContainer;

		// We need a text node that holds the shortcode.
		if ( container.nodeType !== 3 ) {
			if ( container.firstChild && container.firstChild.nodeType === 3 ) {
				container = container.firstChild;
			} else {
				return null;
			}
		}

		var text    = container.nodeValue || '';
		var offset  = rng.startOffset;
		var pattern = /\[eifelhoster_button\b([^\]]*)\]/g;
		var match;

		while ( ( match = pattern.exec( text ) ) !== null ) {
			var start = match.index;
			var end   = start + match[0].length;
			if ( offset >= start && offset <= end ) {
				// Expand the editor selection to cover the complete shortcode so
				// that ebpInsertShortcode() can replace it with setContent().
				var newRng = editor.dom.createRng();
				newRng.setStart( container, start );
				newRng.setEnd( container, end );
				sel.setRng( newRng );
				return ebpParseShortcodeAttrs( match[1] );
			}
		}
		return null;
	}

	tinymce.PluginManager.add( 'ebp_button', function ( editor ) {
		editor.addButton( 'ebp_button', {
			title  : 'Eifelhoster Button einfügen / bearbeiten',
			icon   : false,
			text   : '⬛ Button',
			tooltip: 'Eifelhoster Buttons Pro – Button einfügen / bearbeiten',
			onclick: function () {
				var attrs = ebpGetShortcodeAtCursor( editor );
				if ( typeof window.ebpOpenDialog === 'function' ) {
					window.ebpOpenDialog( editor, attrs );
				}
			}
		} );

		// Double-clicking on an existing shortcode in the editor body opens the
		// edit dialog pre-filled with the shortcode's current attribute values.
		editor.on( 'dblclick', function () {
			var attrs = ebpGetShortcodeAtCursor( editor );
			if ( attrs && typeof window.ebpOpenDialog === 'function' ) {
				window.ebpOpenDialog( editor, attrs );
			}
		} );
	} );
}());
