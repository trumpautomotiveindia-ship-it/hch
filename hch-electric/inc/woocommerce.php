<?php
/**
 * WooCommerce integration: unwrap default wrappers, AJAX cart, MOQ, WhatsApp.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Strip default wrappers; we provide our own .hch-shop wrapper in archive-product.php */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action( 'woocommerce_before_main_content', function() { echo '<main class="hch-shop" id="primary">'; }, 10 );
add_action( 'woocommerce_after_main_content',  function() { echo '</main>'; }, 10 );

/* Loop columns default 6 */
add_filter( 'loop_shop_columns', function() { return 6; }, 999 );

/* Add .hch-grid to ul.products */
add_filter( 'woocommerce_product_loop_start', function( $html ) {
	return str_replace( 'class="products', 'class="hch-grid products', $html );
} );

/* Remove default sale flash / loop title / price / button — our template renders them */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/**
 * Enforce MOQ on add-to-cart (from _hch_moq meta).
 */
add_filter( 'woocommerce_add_to_cart_quantity', function( $quantity, $product_id ) {
	$moq = (int) get_post_meta( $product_id, '_hch_moq', true );
	if ( $moq > 1 && (int) $quantity < $moq ) {
		return $moq;
	}
	return $quantity;
}, 10, 2 );

/**
 * AJAX: get full drawer HTML + count + totals.
 */
function hch_cart_fragments_cb() {
	check_ajax_referer( 'hch_cart', 'nonce' );
	wp_send_json_success( hch_build_cart_payload() );
}
add_action( 'wp_ajax_hch_cart_fragments',        'hch_cart_fragments_cb' );
add_action( 'wp_ajax_nopriv_hch_cart_fragments', 'hch_cart_fragments_cb' );

/**
 * AJAX: add to cart.
 */
function hch_add_to_cart_cb() {
	check_ajax_referer( 'hch_cart', 'nonce' );
	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce not active' ) );
	}
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$qty        = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Missing product' ) );
	}
	$moq = (int) get_post_meta( $product_id, '_hch_moq', true );
	if ( $moq > 1 ) { $qty = max( $qty, $moq ); }

	$added = WC()->cart->add_to_cart( $product_id, $qty );
	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'Unable to add to cart', 'hch-electric' ) ) );
	}
	wp_send_json_success( hch_build_cart_payload() );
}
add_action( 'wp_ajax_hch_add_to_cart',        'hch_add_to_cart_cb' );
add_action( 'wp_ajax_nopriv_hch_add_to_cart', 'hch_add_to_cart_cb' );

/**
 * AJAX: update qty.
 */
function hch_update_cart_cb() {
	check_ajax_referer( 'hch_cart', 'nonce' );
	$key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
	$qty = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;
	if ( ! $key ) wp_send_json_error();
	if ( $qty < 1 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		WC()->cart->set_quantity( $key, $qty, true );
	}
	wp_send_json_success( hch_build_cart_payload() );
}
add_action( 'wp_ajax_hch_update_cart',        'hch_update_cart_cb' );
add_action( 'wp_ajax_nopriv_hch_update_cart', 'hch_update_cart_cb' );

/**
 * Build payload: count + total html + items array + whatsapp message.
 */
function hch_build_cart_payload() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return array( 'count' => 0, 'total_html' => '', 'items' => array(), 'wa_url' => '' );
	}
	$items = array();
	$raw_lines = array();
	foreach ( WC()->cart->get_cart() as $key => $line ) {
		$product = $line['data'];
		if ( ! $product ) continue;
		$thumb_id = $product->get_image_id();
		$thumb    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : '';
		$pid      = $product->get_id();
		$icon     = get_post_meta( $pid, '_hch_icon', true );
		$spec     = get_post_meta( $pid, '_hch_spec', true );
		$moq      = (int) get_post_meta( $pid, '_hch_moq', true );
		if ( $moq < 1 ) $moq = 1;
		$items[] = array(
			'key'        => $key,
			'id'         => $pid,
			'name'       => $product->get_name(),
			'spec'       => $spec,
			'sku'        => $product->get_sku(),
			'icon'       => $icon ? $icon : '',
			'thumb'      => $thumb,
			'qty'        => (int) $line['quantity'],
			'moq'        => $moq,
			'line_total' => wp_strip_all_tags( wc_price( (float) $line['line_total'] ) ),
		);
		$raw_lines[] = sprintf( '• %s%s x%d = %s',
			$product->get_name(),
			$spec ? " ($spec)" : '',
			(int) $line['quantity'],
			wp_strip_all_tags( wc_price( (float) $line['line_total'] ) )
		);
	}

	$whatsapp = preg_replace( '/\D+/', '', (string) get_theme_mod( 'hch_whatsapp', '919999999999' ) );
	$subtotal = wp_strip_all_tags( wc_price( (float) WC()->cart->get_subtotal() ) );
	$wa_url   = '';
	if ( $whatsapp && ! empty( $items ) ) {
		$msg = __( "Hi HCH Electric, I'd like to order:", 'hch-electric' ) . "\n\n";
		$msg .= implode( "\n", $raw_lines ) . "\n\n";
		/* translators: %s: subtotal */
		$msg .= sprintf( __( 'Subtotal: %s (excl. GST)', 'hch-electric' ), $subtotal ) . "\n\n";
		$msg .= __( 'Please confirm & share GST invoice.', 'hch-electric' );
		$wa_url = 'https://wa.me/' . rawurlencode( $whatsapp ) . '?text=' . rawurlencode( $msg );
	}

	return array(
		'count'      => WC()->cart->get_cart_contents_count(),
		'total_html' => wc_price( (float) WC()->cart->get_subtotal() ),
		'items'      => $items,
		'wa_url'     => $wa_url,
	);
}

