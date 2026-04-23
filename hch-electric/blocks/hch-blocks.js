/**
 * HCH Electric — native Gutenberg blocks (editor script).
 *
 * Both blocks are fully server-rendered: the editor simply shows a live
 * ServerSideRender preview so authors see exactly what visitors will see.
 * No build step required — vanilla JS against wp.* globals.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.serverSideRender ) return;

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const ServerSideRender = wp.serverSideRender;
	const __ = wp.i18n ? wp.i18n.__ : function ( s ) { return s; };

	/* Brand Filter */
	registerBlockType( 'hch/brand-filter', {
		edit: function () {
			return el( Fragment, null,
				el( 'div', { className: 'hch-block-preview' },
					el( ServerSideRender, {
						block: 'hch/brand-filter',
						EmptyResponsePlaceholder: function () {
							return el( 'div', { style: { padding: '14px 16px', background: '#101418', color: 'rgba(255,255,255,0.45)', fontFamily: 'DM Mono, monospace', fontSize: '11px', letterSpacing: '0.08em', borderRadius: '8px' } },
								__( 'No brand terms yet. Create a "Brand" product attribute (slug: brand) under Products → Attributes.', 'hch-electric' )
							);
						}
					} )
				)
			);
		},
		save: function () { return null; }
	} );

	/* Category Bar */
	registerBlockType( 'hch/category-bar', {
		edit: function () {
			return el( Fragment, null,
				el( 'div', { className: 'hch-block-preview' },
					el( ServerSideRender, {
						block: 'hch/category-bar',
						EmptyResponsePlaceholder: function () {
							return el( 'div', { style: { padding: '14px 16px', background: '#101418', color: 'rgba(255,255,255,0.45)', fontFamily: 'DM Mono, monospace', fontSize: '11px', letterSpacing: '0.08em', borderRadius: '8px' } },
								__( 'No product categories yet. Add some under Products → Categories.', 'hch-electric' )
							);
						}
					} )
				)
			);
		},
		save: function () { return null; }
	} );

	/* Category Grid — big image tiles */
	registerBlockType( 'hch/category-grid', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const InspectorControls = wp.blockEditor && wp.blockEditor.InspectorControls;
			const { PanelBody, RangeControl, ToggleControl, TextControl } = wp.components || {};
			return el( Fragment, null,
				InspectorControls && el( InspectorControls, null,
					PanelBody && el( PanelBody, { title: __( 'Category Grid', 'hch-electric' ), initialOpen: true },
						TextControl && el( TextControl, {
							label: __( 'Heading', 'hch-electric' ),
							value: attributes.heading,
							onChange: function ( v ) { setAttributes( { heading: v } ); }
						} ),
						RangeControl && el( RangeControl, {
							label: __( 'Columns', 'hch-electric' ),
							min: 2, max: 6, value: attributes.columns,
							onChange: function ( v ) { setAttributes( { columns: v } ); }
						} ),
						ToggleControl && el( ToggleControl, {
							label: __( 'Show product count', 'hch-electric' ),
							checked: !!attributes.showCount,
							onChange: function ( v ) { setAttributes( { showCount: v } ); }
						} ),
						ToggleControl && el( ToggleControl, {
							label: __( 'Hide empty categories', 'hch-electric' ),
							checked: !!attributes.hideEmpty,
							onChange: function ( v ) { setAttributes( { hideEmpty: v } ); }
						} )
					)
				),
				el( 'div', { className: 'hch-block-preview' },
					el( ServerSideRender, { block: 'hch/category-grid', attributes: attributes } )
				)
			);
		},
		save: function () { return null; }
	} );

	/* Category Sections — one product row per category */
	registerBlockType( 'hch/category-sections', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const InspectorControls = wp.blockEditor && wp.blockEditor.InspectorControls;
			const { PanelBody, RangeControl, ToggleControl, TextControl } = wp.components || {};
			return el( Fragment, null,
				InspectorControls && el( InspectorControls, null,
					PanelBody && el( PanelBody, { title: __( 'Category Sections', 'hch-electric' ), initialOpen: true },
						RangeControl && el( RangeControl, {
							label: __( 'Products per section', 'hch-electric' ),
							min: 2, max: 24, value: attributes.perSection,
							onChange: function ( v ) { setAttributes( { perSection: v } ); }
						} ),
						ToggleControl && el( ToggleControl, {
							label: __( 'Show "View all" link', 'hch-electric' ),
							checked: !!attributes.showViewAll,
							onChange: function ( v ) { setAttributes( { showViewAll: v } ); }
						} ),
						ToggleControl && el( ToggleControl, {
							label: __( 'Hide empty categories', 'hch-electric' ),
							checked: !!attributes.hideEmpty,
							onChange: function ( v ) { setAttributes( { hideEmpty: v } ); }
						} ),
						TextControl && el( TextControl, {
							label: __( 'Only these slugs (comma-separated, optional)', 'hch-electric' ),
							value: attributes.onlySlugs,
							onChange: function ( v ) { setAttributes( { onlySlugs: v } ); },
							help: __( 'Leave empty to show all categories in menu-order.', 'hch-electric' )
						} )
					)
				),
				el( 'div', { className: 'hch-block-preview' },
					el( ServerSideRender, { block: 'hch/category-sections', attributes: attributes } )
				)
			);
		},
		save: function () { return null; }
	} );
	/* Announcement Ticker */
	registerBlockType( 'hch/ticker', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/ticker',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', background: '#00e5ff', color: '#0a0e14', fontSize: '12px', fontFamily: 'DM Mono, monospace' } },
							__( 'Ticker — add lines in Customizer → HCH Electric → Top Ticker', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

	/* Search Form */
	registerBlockType( 'hch/search-form', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/search-form',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', background: '#1a2030', color: 'rgba(255,255,255,0.5)', fontSize: '12px', fontFamily: 'DM Mono, monospace', borderRadius: '4px' } },
							__( 'Product Search Form', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

	/* Cart Button */
	registerBlockType( 'hch/cart-button', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/cart-button',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', background: '#1a2030', color: '#00e5ff', fontSize: '12px', fontFamily: 'DM Mono, monospace', borderRadius: '4px' } },
							__( 'Cart Button (requires WooCommerce)', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

	/* Footer Categories */
	registerBlockType( 'hch/footer-categories', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/footer-categories',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', color: 'rgba(255,255,255,0.45)', fontSize: '12px', fontFamily: 'DM Mono, monospace' } },
							__( 'Footer Categories — add WooCommerce product categories to populate.', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

	/* Footer Contact */
	registerBlockType( 'hch/footer-contact', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/footer-contact',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', color: 'rgba(255,255,255,0.45)', fontSize: '12px', fontFamily: 'DM Mono, monospace' } },
							__( 'Footer Contact — edit in Appearance → Menus (Footer Contact).', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

	/* Footer Bottom Bar */
	registerBlockType( 'hch/footer-bottom', {
		edit: function () {
			return el( 'div', { className: 'hch-block-preview' },
				el( ServerSideRender, {
					block: 'hch/footer-bottom',
					EmptyResponsePlaceholder: function () {
						return el( 'div', { style: { padding: '8px 16px', color: 'rgba(255,255,255,0.45)', fontSize: '12px', fontFamily: 'DM Mono, monospace' } },
							__( 'Footer Bottom Bar — copyright & disclaimer from Customizer.', 'hch-electric' )
						);
					}
				} )
			);
		},
		save: function () { return null; }
	} );

} )( window.wp );
