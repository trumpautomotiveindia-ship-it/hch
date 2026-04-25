<?php
/**
 * Front page — HCH Electric storefront.
 *
 * If the homepage (static page assigned under Settings → Reading) has content
 * inserted via the Block Editor, that content replaces the default layout so
 * store owners may redesign freely using our registered block patterns.
 *
 * @package HCH_Electric
 */
get_header();

/* If the selected front page has Gutenberg content, render it verbatim. */
if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
	the_post();
	$page_content = get_the_content();
	rewind_posts();
	if ( trim( wp_strip_all_tags( $page_content ) ) !== '' ) {
		while ( have_posts() ) : the_post(); ?>
			<div class="hch-page-content">
				<?php the_content(); ?>
			</div>
		<?php endwhile;
		get_footer();
		return;
	}
}

/* ── DEFAULT CURATED LAYOUT (used when no page content) ─────────── */

$h_line1 = get_theme_mod( 'hch_hero_line1', __( 'Every EV part.', 'hch-electric' ) );
$h_line2 = get_theme_mod( 'hch_hero_line2', __( 'Every spec.', 'hch-electric' ) );
$h_line3 = get_theme_mod( 'hch_hero_line3', __( 'One source.', 'hch-electric' ) );
$hsub   = get_theme_mod( 'hch_hero_sub', __( 'Electric Scooters', 'hch-electric' ) );

$stats = array(
	array( get_theme_mod( 'hch_stat1_num', '500+' ), get_theme_mod( 'hch_stat1_lbl', __( 'B2B PARTNERS', 'hch-electric' ) ) ),
	array( get_theme_mod( 'hch_stat2_num', '10M+' ), get_theme_mod( 'hch_stat2_lbl', __( 'PARTS SHIPPED', 'hch-electric' ) ) ),
	array( get_theme_mod( 'hch_stat3_num', '120+' ), get_theme_mod( 'hch_stat3_lbl', __( 'CITIES', 'hch-electric' ) ) ),
	array( get_theme_mod( 'hch_stat4_num', '24/7' ), get_theme_mod( 'hch_stat4_lbl', __( 'SUPPORT', 'hch-electric' ) ) ),
);

if ( ! function_exists( 'hch_highlight' ) ) {
	function hch_highlight( $text ) {
		return wp_kses( $text, array( 'span' => array( 'class' => array() ), 'br' => array() ) );
	}
}
?>

<section class="hch-hero">
	<h1 class="hch-hero__h1">
		<?php echo hch_highlight( $h_line1 ); ?><br/>
		<span class="c"><?php echo hch_highlight( $h_line2 ); ?></span><br/>
		<span class="g"><?php echo hch_highlight( $h_line3 ); ?></span>
	</h1>
	<p class="hch-hero__sub"><?php echo esc_html( $hsub ); ?></p>

	<div class="hch-hero__stats">
		<?php foreach ( $stats as $s ) :
			// Split trailing + into coloured span.
			$num = $s[0];
			$html = preg_replace( '/([+])\s*$/', '<span>$1</span>', esc_html( $num ) );
			?>
			<div class="hch-hero__stat">
				<div class="hch-hero__stat-num"><?php echo $html; // already escaped via esc_html above ?></div>
				<div class="hch-hero__stat-label"><?php echo esc_html( $s[1] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php echo do_shortcode( '[hch_category_bar]' ); ?>

<?php /* ─ Competitor-style homepage: category tile grid + per-category sections ─ */ ?>
<?php echo hch_block_render_category_grid( array( 'columns' => 4, 'showCount' => true, 'hideEmpty' => false, 'heading' => __( 'SHOP BY CATEGORY', 'hch-electric' ) ) ); ?>
<?php echo hch_block_render_category_sections( array( 'perSection' => 12, 'hideEmpty' => true, 'showViewAll' => true ) ); ?>

<main class="hch-shop" id="primary" role="main" style="padding-top:12px;">
	<?php
	$active_cat      = isset( $_GET['hch_cat'] ) ? sanitize_key( wp_unslash( $_GET['hch_cat'] ) ) : '';
	$active_cat_term = $active_cat ? get_term_by( 'slug', $active_cat, 'product_cat' ) : null;
	$shop_title      = $active_cat_term ? $active_cat_term->name : __( 'All Parts', 'hch-electric' );
	?>
	<div class="hch-shop__head">
		<div class="hch-shop__title" id="hchShopTitle"><?php echo esc_html( $shop_title ); ?></div>
		<?php
		$count_args = array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' );
		if ( $active_cat ) {
			$count_args['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $active_cat ) );
		}
		$total = class_exists( 'WooCommerce' ) ? (int) ( new WP_Query( $count_args ) )->found_posts : 0;
		?>
		<div class="hch-shop__count" id="hchShopCount"><?php
			/* translators: %d: number of products */
			printf( esc_html( _n( '%d item', '%d items', $total, 'hch-electric' ) ), $total );
		?></div>
	</div>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<?php
		$per_page = (int) apply_filters( 'hch_front_products_per_page', 30 );
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => $per_page,
			'post_status'    => 'publish',
			'meta_query'     => WC()->query->get_meta_query(),
			'tax_query'      => WC()->query->get_tax_query(),
		);
		if ( $active_cat ) {
			$args['tax_query'][] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $active_cat );
		}
		$q = new WP_Query( $args );
		if ( $q->have_posts() ) : ?>
			<ul class="products hch-grid columns-6" id="hchProdGrid">
				<?php while ( $q->have_posts() ) : $q->the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile; ?>
			</ul>
		<?php else : ?>
			<div style="text-align:center;padding:52px 0;color:rgba(255,255,255,.35);font-size:13px;">
				<?php esc_html_e( 'No products found. Add some in Products → Add New.', 'hch-electric' ); ?>
			</div>
		<?php endif;
		wp_reset_postdata();
		?>
	<?php else : ?>
		<div style="text-align:center;padding:52px 0;color:rgba(255,255,255,.35);font-size:13px;">
			<?php esc_html_e( 'Install and activate WooCommerce to display products.', 'hch-electric' ); ?>
		</div>
	<?php endif; ?>
</main>

<?php get_footer();
