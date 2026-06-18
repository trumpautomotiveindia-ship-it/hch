<?php
/**
 * Register custom block styles so users can pick HCH-branded variations
 * from the block sidebar on any page/post.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hch_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) return;

	/* Group variations */
	register_block_style( 'core/group', array(
		'name'  => 'hch-dark-card',
		'label' => __( 'HCH Dark Card', 'hch-electric' ),
	) );
	register_block_style( 'core/group', array(
		'name'  => 'hch-glass',
		'label' => __( 'HCH Glass', 'hch-electric' ),
	) );
	register_block_style( 'core/group', array(
		'name'  => 'hch-bordered',
		'label' => __( 'HCH Bordered', 'hch-electric' ),
	) );

	/* Button variations */
	register_block_style( 'core/button', array(
		'name'  => 'hch-cyan',
		'label' => __( 'HCH Cyan', 'hch-electric' ),
	) );
	register_block_style( 'core/button', array(
		'name'  => 'hch-green',
		'label' => __( 'HCH Green', 'hch-electric' ),
	) );
	register_block_style( 'core/button', array(
		'name'  => 'hch-ghost',
		'label' => __( 'HCH Ghost', 'hch-electric' ),
	) );
	register_block_style( 'core/button', array(
		'name'  => 'hch-whatsapp',
		'label' => __( 'HCH WhatsApp', 'hch-electric' ),
	) );

	/* Heading variations */
	register_block_style( 'core/heading', array(
		'name'  => 'hch-mono-kicker',
		'label' => __( 'HCH Mono Kicker', 'hch-electric' ),
	) );
	register_block_style( 'core/heading', array(
		'name'  => 'hch-big-display',
		'label' => __( 'HCH Big Display', 'hch-electric' ),
	) );

	/* Paragraph kicker */
	register_block_style( 'core/paragraph', array(
		'name'  => 'hch-mono',
		'label' => __( 'HCH Mono', 'hch-electric' ),
	) );

	/* Columns — dense grid style */
	register_block_style( 'core/columns', array(
		'name'  => 'hch-stats',
		'label' => __( 'HCH Stats Grid', 'hch-electric' ),
	) );

	/* Separator glow */
	register_block_style( 'core/separator', array(
		'name'  => 'hch-glow',
		'label' => __( 'HCH Glow Line', 'hch-electric' ),
	) );

	/* List checklist */
	register_block_style( 'core/list', array(
		'name'  => 'hch-checklist',
		'label' => __( 'HCH Checklist', 'hch-electric' ),
	) );

	/* Quote testimonial */
	register_block_style( 'core/quote', array(
		'name'  => 'hch-testimonial',
		'label' => __( 'HCH Testimonial', 'hch-electric' ),
	) );

	/* Cover — hero background */
	register_block_style( 'core/cover', array(
		'name'  => 'hch-hero',
		'label' => __( 'HCH Hero', 'hch-electric' ),
	) );
}
add_action( 'init', 'hch_register_block_styles' );

/**
 * Unregister core block styles we don't want to pollute the theme with
 * (keeps the sidebar clean for store owners).
 */
function hch_unregister_core_block_styles() {
	if ( ! function_exists( 'unregister_block_style' ) ) return;
	// Keep defaults for now; hook left for future cleanup.
}
add_action( 'wp_enqueue_scripts', 'hch_unregister_core_block_styles' );

/**
 * Enqueue the small JS file that filters the block inserter to highlight our
 * patterns when editing the homepage.
 */
function hch_editor_assets() {
	wp_enqueue_script(
		'hch-editor',
		HCH_URL . '/js/editor.js',
		array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
		HCH_VERSION,
		true
	);
	/* Make sure ServerSideRender and i18n are available to our block script. */
	wp_enqueue_script(
		'hch-blocks',
		HCH_URL . '/blocks/hch-blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-i18n', 'wp-components', 'wp-block-editor' ),
		HCH_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'hch_editor_assets' );
