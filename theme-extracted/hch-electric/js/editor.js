/**
 * HCH Electric — Block editor assets.
 * Tweaks the editor: sets editor root background to match dark theme
 * and pins HCH pattern category to the top.
 */
( function () {
	if ( typeof wp === 'undefined' ) return;

	wp.domReady( function () {
		// Optional: reorder pattern categories so "hch-electric" appears first.
		if ( wp.blocks && wp.blocks.getCategories && wp.blocks.setCategories ) {
			try {
				var cats = wp.blocks.getCategories();
				var hch  = cats.filter( function ( c ) { return c.slug === 'hch-electric'; } );
				var rest = cats.filter( function ( c ) { return c.slug !== 'hch-electric'; } );
				if ( hch.length ) wp.blocks.setCategories( hch.concat( rest ) );
			} catch ( e ) {}
		}
	} );
} )();
