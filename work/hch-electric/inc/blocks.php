<?php
/**
 * Native Gutenberg blocks:
 *   • hch/brand-filter  — WooCommerce brand attribute chips
 *   • hch/category-bar  — WooCommerce product-category sticky nav
 *
 * Both are dynamic (server-rendered). The render callbacks reuse the same
 * PHP that powers the legacy shortcodes, so switching between
 * [hch_brand_filter] and <!-- wp:hch/brand-filter /--> is a drop-in change.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register our custom block category so the blocks land under "HCH Electric"
 * in the inserter (not just in patterns).
 */
function hch_add_block_category( $categories ) {
	foreach ( $categories as $c ) {
		if ( isset( $c['slug'] ) && 'hch-electric' === $c['slug'] ) {
			return $categories;
		}
	}
	return array_merge(
		array( array(
			'slug'  => 'hch-electric',
			'title' => __( 'HCH Electric', 'hch-electric' ),
			'icon'  => null,
		) ),
		$categories
	);
}
add_filter( 'block_categories_all', 'hch_add_block_category', 10, 1 );

/**
 * Register the shared block editor script so block.json can reference it by handle.
 */
function hch_register_block_editor_script() {
	wp_register_script(
		'hch-blocks-editor',
		HCH_URL . '/blocks/hch-blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		HCH_VERSION,
		false
	);
}
add_action( 'init', 'hch_register_block_editor_script', 5 );

/**
 * Register the blocks on init.
 */
function hch_register_blocks() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	register_block_type( HCH_DIR . '/blocks/brand-filter', array(
		'render_callback' => 'hch_block_render_brand_filter',
	) );
	register_block_type( HCH_DIR . '/blocks/category-bar', array(
		'render_callback' => 'hch_block_render_category_bar',
	) );
	register_block_type( HCH_DIR . '/blocks/category-grid', array(
		'render_callback' => 'hch_block_render_category_grid',
	) );
	register_block_type( HCH_DIR . '/blocks/category-sections', array(
		'render_callback' => 'hch_block_render_category_sections',
	) );

	/* Header / footer dynamic blocks */
	register_block_type( HCH_DIR . '/blocks/ticker', array(
		'render_callback' => 'hch_block_render_ticker',
	) );
	register_block_type( HCH_DIR . '/blocks/search-form', array(
		'render_callback' => 'hch_block_render_search_form',
	) );
	register_block_type( HCH_DIR . '/blocks/cart-button', array(
		'render_callback' => 'hch_block_render_cart_button',
	) );
	register_block_type( HCH_DIR . '/blocks/footer-categories', array(
		'render_callback' => 'hch_block_render_footer_categories',
	) );
	register_block_type( HCH_DIR . '/blocks/footer-contact', array(
		'render_callback' => 'hch_block_render_footer_contact',
	) );
	register_block_type( HCH_DIR . '/blocks/footer-bottom', array(
		'render_callback' => 'hch_block_render_footer_bottom',
	) );
	register_block_type( HCH_DIR . '/blocks/wc-archive', array(
		'render_callback' => 'hch_block_render_wc_archive',
	) );
	register_block_type( HCH_DIR . '/blocks/wc-single', array(
		'render_callback' => 'hch_block_render_wc_single',
	) );
}
add_action( 'init', 'hch_register_blocks' );

/**
 * Render callback — reuses the shortcode function so output stays in sync.
 */
function hch_block_render_brand_filter( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_brand_filter_shortcode' ) ) {
		return hch_brand_filter_shortcode();
	}
	return '';
}

function hch_block_render_category_bar( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_category_bar_shortcode' ) ) {
		return hch_category_bar_shortcode();
	}
	return '';
}

/**
 * Category grid — large clickable tiles for every product category.
 */
function hch_block_render_category_grid( $attrs = array(), $content = '' ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) return '';
	$columns    = isset( $attrs['columns'] )   ? max( 2, min( 6, (int) $attrs['columns'] ) ) : 4;
	$hide_empty = ! empty( $attrs['hideEmpty'] );
	$show_count = ! isset( $attrs['showCount'] ) || (bool) $attrs['showCount'];
	$heading    = isset( $attrs['heading'] ) ? (string) $attrs['heading'] : 'SHOP BY CATEGORY';

	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => (bool) $hide_empty,
		'parent'     => 0,
		'orderby'    => 'menu_order',
	) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) return '';

	ob_start(); ?>
	<section class="hch-catgrid" data-cols="<?php echo (int) $columns; ?>">
		<div class="hch-catgrid__inner">
			<?php if ( $heading ) : ?>
				<div class="hch-shop__title"><?php echo esc_html( $heading ); ?></div>
			<?php endif; ?>
			<div class="hch-catgrid__grid">
				<?php foreach ( $terms as $t ) :
					$thumb_id = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
					$img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
					$icon     = get_term_meta( $t->term_id, 'hch_icon', true );
					$url      = get_term_link( $t );
					if ( is_wp_error( $url ) ) continue; ?>
					<div class="hch-catgrid__tile hch-cat-filter" data-cat="<?php echo esc_attr( $t->slug ); ?>">
						<div class="hch-catgrid__img">
							<?php if ( $img_url ) : ?>
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $t->name ); ?>" loading="lazy"/>
							<?php elseif ( $icon ) : ?>
								<span class="hch-catgrid__ico"><?php echo esc_html( $icon ); ?></span>
							<?php else : ?>
								<span class="hch-catgrid__ico">•</span>
							<?php endif; ?>
						</div>
						<div class="hch-catgrid__meta">
							<div class="hch-catgrid__name"><?php echo esc_html( $t->name ); ?></div>
							<?php if ( $show_count ) : ?>
								<div class="hch-catgrid__count"><?php
									/* translators: %d: product count */
									printf( esc_html( _n( '%d item', '%d items', (int) $t->count, 'hch-electric' ) ), (int) $t->count );
								?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Category sections — one WooCommerce loop per category, compact homepage view.
 */
function hch_block_render_category_sections( $attrs = array(), $content = '' ) {
	if ( ! class_exists( 'WooCommerce' ) || ! taxonomy_exists( 'product_cat' ) ) return '';

	$per_section   = isset( $attrs['perSection'] ) ? max( 2, min( 24, (int) $attrs['perSection'] ) ) : 12;
	$hide_empty    = ! isset( $attrs['hideEmpty'] ) || (bool) $attrs['hideEmpty'];
	$show_view_all = ! isset( $attrs['showViewAll'] ) || (bool) $attrs['showViewAll'];
	$only_slugs    = isset( $attrs['onlySlugs'] ) ? trim( (string) $attrs['onlySlugs'] ) : '';

	$term_args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => (bool) $hide_empty,
		'parent'     => 0,
		'orderby'    => 'menu_order',
	);
	if ( '' !== $only_slugs ) {
		$slugs = array_filter( array_map( 'trim', explode( ',', $only_slugs ) ) );
		if ( $slugs ) $term_args['slug'] = $slugs;
	}
	$terms = get_terms( $term_args );
	if ( is_wp_error( $terms ) || empty( $terms ) ) return '';

	ob_start(); ?>
	<section class="hch-catsect-wrap">
	<?php foreach ( $terms as $t ) :
		$url = get_term_link( $t );
		if ( is_wp_error( $url ) ) continue;
		$q = new WP_Query( array(
			'post_type'      => 'product',
			'posts_per_page' => (int) $per_section,
			'post_status'    => 'publish',
			'tax_query'      => array( array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (int) $t->term_id,
			) ),
		) );
		if ( ! $q->have_posts() ) { wp_reset_postdata(); continue; }
		?>
		<div class="hch-catsect">
			<div class="hch-catsect__hd">
				<span class="hch-catsect__title">
					<?php $icon = get_term_meta( $t->term_id, 'hch_icon', true );
					if ( $icon ) : ?><span class="hch-catsect__ico"><?php echo esc_html( $icon ); ?></span><?php endif; ?>
					<?php echo esc_html( $t->name ); ?>
				</span>
				<?php if ( $show_view_all ) : ?>
					<span class="hch-catsect__all">
						<?php esc_html_e( 'View all', 'hch-electric' ); ?>
						<span>(<?php echo (int) $t->count; ?>)</span>
					</span>
				<?php endif; ?>
			</div>
			<ul class="products hch-grid hch-catsect__grid columns-6">
				<?php while ( $q->have_posts() ) : $q->the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile; ?>
			</ul>
		</div>
	<?php wp_reset_postdata(); endforeach; ?>
	</section>
	<?php
	return ob_get_clean();
}

function hch_block_render_ticker( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_ticker_shortcode' ) ) {
		return hch_ticker_shortcode();
	}
	return '';
}

function hch_block_render_search_form( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_search_form_shortcode' ) ) {
		return hch_search_form_shortcode();
	}
	return '';
}

function hch_block_render_cart_button( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_cart_button_shortcode' ) ) {
		return hch_cart_button_shortcode();
	}
	return '';
}

function hch_block_render_footer_categories( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_footer_categories_shortcode' ) ) {
		return hch_footer_categories_shortcode();
	}
	return '';
}

function hch_block_render_footer_contact( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_footer_contact_shortcode' ) ) {
		return hch_footer_contact_shortcode();
	}
	return '';
}

function hch_block_render_footer_bottom( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_footer_bottom_shortcode' ) ) {
		return hch_footer_bottom_shortcode();
	}
	return '';
}

function hch_block_render_wc_archive( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_wc_archive_shortcode' ) ) {
		return hch_wc_archive_shortcode();
	}
	return '';
}

function hch_block_render_wc_single( $attrs = array(), $content = '' ) {
	if ( function_exists( 'hch_wc_single_shortcode' ) ) {
		return hch_wc_single_shortcode();
	}
	return '';
}
