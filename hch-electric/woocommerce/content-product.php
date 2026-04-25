<?php
/**
 * Product loop card — static box, no link to product page.
 * Add-to-cart: inline [−] [qty] [+] row at card bottom.
 *
 * @package HCH_Electric
 */
defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$pid   = $product->get_id();
$spec  = get_post_meta( $pid, '_hch_spec', true );
$moq   = (int) get_post_meta( $pid, '_hch_moq', true );
$badge = get_post_meta( $pid, '_hch_badge', true );
$icon  = get_post_meta( $pid, '_hch_icon', true );
$note  = get_post_meta( $pid, '_hch_price_note', true );
if ( '' === $note ) { $note = __( '/pc excl. GST', 'hch-electric' ); }
if ( $moq < 1 ) { $moq = 1; }

$badge_map = array(
	's' => array( 'b-s', __( 'IN STOCK', 'hch-electric' ) ),
	'h' => array( 'b-h', __( 'POPULAR', 'hch-electric' ) ),
	'n' => array( 'b-n', __( 'NEW', 'hch-electric' ) ),
	'd' => array( 'b-d', __( 'DEAL', 'hch-electric' ) ),
);
$badge_data = isset( $badge_map[ $badge ] ) ? $badge_map[ $badge ] : null;
if ( ! $badge_data && $product->is_in_stock() ) {
	$badge_data = $badge_map['s'];
}
if ( ! $badge_data && $product->is_on_sale() ) {
	$badge_data = $badge_map['d'];
}

$brands = array();
if ( taxonomy_exists( 'pa_brand' ) ) {
	$terms = wp_get_post_terms( $pid, 'pa_brand', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $terms ) ) $brands = $terms;
}
$brands[] = 'all';
?>
<li <?php wc_product_class( 'hch-product', $product ); ?>
	data-product-id="<?php echo (int) $pid; ?>"
	data-cat="<?php
		$cats = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'slugs' ) );
		echo is_array( $cats ) ? esc_attr( implode( ' ', $cats ) ) : '';
	?>"
	data-brand="<?php echo esc_attr( implode( ' ', array_unique( $brands ) ) ); ?>">

	<div class="hch-product__inner">

		<div class="hch-product__img">
			<?php if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => esc_attr( $product->get_name() ) ) );
			} elseif ( $icon ) {
				echo '<span class="hch-product__icon">' . esc_html( $icon ) . '</span>';
			} else {
				echo wp_kses_post( woocommerce_placeholder_img( 'woocommerce_thumbnail' ) );
			} ?>

			<?php if ( $badge_data ) : ?>
				<div class="hch-badge <?php echo esc_attr( $badge_data[0] ); ?>"><?php echo esc_html( $badge_data[1] ); ?></div>
			<?php endif; ?>
		</div>

		<div class="hch-product__body">
			<div class="hch-product__sku"><?php
				$sku_parts = array_filter( array( $spec, $product->get_sku() ) );
				echo esc_html( implode( ' · ', $sku_parts ) );
			?></div>

			<h3 class="woocommerce-loop-product__title hch-product__name"><?php echo esc_html( $product->get_name() ); ?></h3>

			<div class="hch-product__foot">
				<div class="hch-product__pricing">
					<div class="hch-product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
					<div class="hch-product__note"><?php echo esc_html( $note ); ?></div>
				</div>
				<?php if ( $moq > 1 ) : ?>
					<div class="hch-moq"><?php
						/* translators: %d: minimum order quantity */
						printf( esc_html__( 'MOQ %d', 'hch-electric' ), $moq );
					?></div>
				<?php endif; ?>
			</div>

			<div class="hch-product__atc">
				<div class="hch-atc__row">
					<button type="button" class="hch-atc__dec" aria-label="<?php esc_attr_e( 'Decrease', 'hch-electric' ); ?>">−</button>
					<input type="number" class="hch-atc__num"
						value="<?php echo (int) $moq; ?>"
						min="<?php echo (int) $moq; ?>"
						step="1"
						aria-label="<?php esc_attr_e( 'Quantity', 'hch-electric' ); ?>"/>
					<button type="button" class="hch-atc__btn"
						data-product-id="<?php echo (int) $pid; ?>"
						data-moq="<?php echo (int) $moq; ?>"
						aria-label="<?php esc_attr_e( 'Add to cart', 'hch-electric' ); ?>">+</button>
				</div>
			</div>

		</div>
	</div>
</li>
