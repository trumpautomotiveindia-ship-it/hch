<?php
/**
 * Shop / product archive — reuses hero category bar + product grid layout.
 *
 * @package HCH_Electric
 */
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

echo do_shortcode( '[hch_brand_filter]' );
echo do_shortcode( '[hch_category_bar]' );

?>
<main class="hch-shop" id="primary">
	<div class="hch-shop__head">
		<div class="hch-shop__title">
			<?php if ( is_product_category() ) {
				echo esc_html( single_term_title( '', false ) );
			} elseif ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Search: %s', 'hch-electric' ), esc_html( get_search_query() ) );
			} else {
				esc_html_e( 'All Parts', 'hch-electric' );
			} ?>
		</div>
		<div class="hch-shop__count">
			<?php
			$total = (int) wc_get_loop_prop( 'total' );
			/* translators: %d: number of results */
			printf( esc_html( _n( '%d item', '%d items', $total, 'hch-electric' ) ), $total );
			?>
		</div>
	</div>

	<?php
	/**
	 * woocommerce_before_shop_loop hook.
	 */
	do_action( 'woocommerce_before_shop_loop' );

	if ( woocommerce_product_loop() ) {
		woocommerce_product_loop_start();
		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
		}
		woocommerce_product_loop_end();
		do_action( 'woocommerce_after_shop_loop' );
	} else {
		do_action( 'woocommerce_no_products_found' );
	}
	?>
</main>
<?php
get_footer( 'shop' );
