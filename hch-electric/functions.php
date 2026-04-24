<?php
/**
 * HCH Electric theme bootstrap.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HCH_VERSION', '2.0.0' );
define( 'HCH_DIR', get_template_directory() );
define( 'HCH_URL', get_template_directory_uri() );

/**
 * Theme setup.
 */
function hch_setup() {
	load_theme_textdomain( 'hch-electric', HCH_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 76,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'style.css', 'editor-style.css' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-units' );
	add_theme_support( 'block-templates' );
	add_theme_support( 'starter-content', array() );

	// WooCommerce.
	add_theme_support( 'woocommerce', array(
		'product_grid' => array(
			'default_rows'    => 4,
			'min_rows'        => 1,
			'default_columns' => 6,
			'min_columns'     => 2,
			'max_columns'     => 8,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary'  => __( 'Primary Menu', 'hch-electric' ),
		'footer-1' => __( 'Footer — Categories', 'hch-electric' ),
		'footer-2' => __( 'Footer — Contact', 'hch-electric' ),
	) );
}
add_action( 'after_setup_theme', 'hch_setup' );

/**
 * Widget areas (optional sidebar for shop page).
 */
function hch_widgets() {
	register_sidebar( array(
		'name'          => __( 'Shop Sidebar', 'hch-electric' ),
		'id'            => 'shop-sidebar',
		'description'   => __( 'Optional sidebar on WooCommerce shop pages.', 'hch-electric' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'hch_widgets' );

/**
 * Enqueue assets.
 */
function hch_enqueue() {
	// Google fonts.
	wp_enqueue_style(
		'hch-fonts',
		'https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700;800&family=DM+Mono:wght@400;500&family=Outfit:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'hch-style', get_stylesheet_uri(), array( 'hch-fonts' ), filemtime( get_stylesheet_directory() . '/style.css' ) );

	wp_enqueue_script( 'hch-main', HCH_URL . '/js/hch.js', array( 'jquery' ), filemtime( HCH_DIR . '/js/hch.js' ), true );

	$whatsapp = trim( (string) get_theme_mod( 'hch_whatsapp', '919999999999' ) );
	$whatsapp = preg_replace( '/\D+/', '', $whatsapp );

	wp_localize_script( 'hch-main', 'HCH', array(
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'nonce'         => wp_create_nonce( 'hch_cart' ),
		'cartUrl'       => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
		'checkoutUrl'   => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ),
		'shopUrl'       => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
		'whatsapp'      => $whatsapp,
		'gstNote'       => __( 'GST calculated at checkout · Invoice provided', 'hch-electric' ),
		'currencySym'   => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '₹',
		'i18n'          => array(
			'empty'      => __( 'Your cart is empty', 'hch-electric' ),
			'subtotal'   => __( 'Subtotal (excl. GST)', 'hch-electric' ),
			'waLabel'    => __( 'Order via WhatsApp', 'hch-electric' ),
			'checkout'   => __( 'Proceed to Checkout', 'hch-electric' ),
			'waMsgIntro' => __( "Hi HCH Electric, I'd like to order:", 'hch-electric' ),
			'waMsgOutro' => __( 'Please confirm & share GST invoice.', 'hch-electric' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'hch_enqueue' );

require_once HCH_DIR . '/inc/seo.php';
require_once HCH_DIR . '/inc/customizer.php';
require_once HCH_DIR . '/inc/meta-boxes.php';
require_once HCH_DIR . '/inc/block-patterns.php';
require_once HCH_DIR . '/inc/block-styles.php';
require_once HCH_DIR . '/inc/blocks.php';
require_once HCH_DIR . '/inc/woocommerce.php';
if ( is_admin() ) {
	require_once HCH_DIR . '/inc/seed.php';
}

/**
 * Shortcode: [hch_brand_filter]
 * Renders WooCommerce brand-attribute chips as clickable links to each brand archive.
 */
function hch_brand_filter_shortcode() {
	if ( ! taxonomy_exists( 'pa_brand' ) ) {
		return '';
	}
	$terms = get_terms( array( 'taxonomy' => 'pa_brand', 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}
	$queried      = get_queried_object();
	$current_slug = ( $queried && isset( $queried->taxonomy ) && 'pa_brand' === $queried->taxonomy ) ? $queried->slug : 'all';
	$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	ob_start(); ?>
	<div class="hch-brands">
		<button type="button" class="hch-rail-arrow hch-rail-arrow--l" aria-label="<?php esc_attr_e( 'Scroll left', 'hch-electric' ); ?>">‹</button>
		<div class="hch-brands__inner" data-scroll-container>
			<span class="hch-brands__label"><?php esc_html_e( 'MODELS', 'hch-electric' ); ?></span>
			<a class="hch-brand-chip<?php echo 'all' === $current_slug ? ' active' : ''; ?>" href="<?php echo esc_url( $shop_url ); ?>" data-brand="all"><?php esc_html_e( 'All', 'hch-electric' ); ?></a>
			<?php foreach ( $terms as $t ) :
				$url = get_term_link( $t );
				if ( is_wp_error( $url ) ) continue; ?>
				<a class="hch-brand-chip<?php echo $current_slug === $t->slug ? ' active' : ''; ?>" href="<?php echo esc_url( $url ); ?>" data-brand="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<button type="button" class="hch-rail-arrow hch-rail-arrow--r" aria-label="<?php esc_attr_e( 'Scroll right', 'hch-electric' ); ?>">›</button>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_brand_filter', 'hch_brand_filter_shortcode' );

/**
 * Shortcode: [hch_category_bar]
 */
function hch_category_bar_shortcode() {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0 ) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}
	$current      = get_queried_object();
	$current_slug = ( $current && isset( $current->taxonomy ) && 'product_cat' === $current->taxonomy ) ? $current->slug : 'all';
	$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	ob_start(); ?>
	<div class="hch-catbar-wrap">
		<button type="button" class="hch-rail-arrow hch-rail-arrow--l" aria-label="<?php esc_attr_e( 'Scroll left', 'hch-electric' ); ?>">‹</button>
		<nav class="hch-catbar" data-scroll-container aria-label="<?php esc_attr_e( 'Product categories', 'hch-electric' ); ?>">
			<a class="hch-cat<?php echo 'all' === $current_slug ? ' active' : ''; ?>" href="<?php echo esc_url( $shop_url ); ?>">
				<span class="hch-cat__ico">⚡</span><?php esc_html_e( 'All', 'hch-electric' ); ?>
			</a>
			<?php foreach ( $terms as $t ) :
				$icon = get_term_meta( $t->term_id, 'hch_icon', true );
				if ( ! $icon ) { $icon = '•'; } ?>
				<a class="hch-cat<?php echo $current_slug === $t->slug ? ' active' : ''; ?>" href="<?php echo esc_url( get_term_link( $t ) ); ?>">
					<span class="hch-cat__ico"><?php echo esc_html( $icon ); ?></span>
					<?php echo esc_html( $t->name ); ?>
					<span class="hch-cat__cnt"><?php echo (int) $t->count; ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
		<button type="button" class="hch-rail-arrow hch-rail-arrow--r" aria-label="<?php esc_attr_e( 'Scroll right', 'hch-electric' ); ?>">›</button>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_category_bar', 'hch_category_bar_shortcode' );

/**
 * Shortcode: [hch_ticker] — edit strings in Customizer.
 */
function hch_ticker_shortcode() {
	$raw = get_theme_mod( 'hch_ticker', "ALL PARTS 18% GST\nPAN-INDIA DISPATCH\nSAME-DAY BEFORE 2PM\nGST INVOICE PROVIDED\n500+ B2B PARTNERS\n120+ CITIES\nLFP · NMC · PMSM · BLDC\nMADE IN INDIA" );
	$lines = array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $raw ) ) );
	if ( empty( $lines ) ) { return ''; }
	$lines = array_merge( $lines, $lines ); // duplicate for seamless scroll
	ob_start(); ?>
	<div class="hch-ticker"><div class="hch-ticker__track">
		<?php foreach ( $lines as $i => $l ) : ?>
			<span><?php echo esc_html( $l ); ?></span><?php if ( $i < count( $lines ) - 1 ) : ?><span class="dot">·</span><?php endif; ?>
		<?php endforeach; ?>
	</div></div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_ticker', 'hch_ticker_shortcode' );

/**
 * Fallback menu for primary.
 */
function hch_fallback_menu() {
	echo '';
}

/**
 * Output the off-canvas cart drawer and overlay via wp_footer so it works in
 * both classic PHP templates and block-theme templates (where footer.php is
 * not loaded by WordPress).
 */
function hch_output_cart_drawer() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
	$subtotal_val = ( function_exists( 'wc_price' ) && WC()->cart ) ? wc_price( WC()->cart->get_subtotal() ) : '₹0';
	?>
	<div class="hch-overlay" id="hchOverlay"></div>
	<aside class="hch-drawer" id="hchDrawer" aria-label="<?php esc_attr_e( 'Cart', 'hch-electric' ); ?>">
		<div class="hch-drawer__hd">
			<div class="hch-drawer__title"><?php esc_html_e( 'Cart', 'hch-electric' ); ?></div>
			<button type="button" class="hch-drawer__close" id="hchCartClose" aria-label="<?php esc_attr_e( 'Close cart', 'hch-electric' ); ?>">✕</button>
		</div>
		<div class="hch-drawer__items" id="hchDrawerItems">
			<div class="hch-empty">
				<span class="hch-empty__ico">🛒</span>
				<?php esc_html_e( 'Your cart is empty', 'hch-electric' ); ?>
			</div>
		</div>
		<div class="hch-drawer__ft">
			<div class="hch-drawer__tot-row">
				<div class="hch-drawer__tot-lbl"><?php esc_html_e( 'Subtotal (excl. GST)', 'hch-electric' ); ?></div>
				<div class="hch-drawer__tot-val" id="hchDrawerTotal"><?php echo $subtotal_val; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			</div>
			<div class="hch-drawer__gst"><?php esc_html_e( 'GST calculated at checkout · Invoice provided', 'hch-electric' ); ?></div>
			<button type="button" class="hch-wa-btn" id="hchWaBtn">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
				<?php esc_html_e( 'Order via WhatsApp', 'hch-electric' ); ?>
			</button>
			<a class="hch-checkout-btn" href="<?php echo esc_url( $checkout_url ); ?>"><?php esc_html_e( 'Proceed to Checkout', 'hch-electric' ); ?></a>
		</div>
	</aside>
	<?php
}
add_action( 'wp_footer', 'hch_output_cart_drawer', 5 );

/**
 * Output the search overlay via wp_footer so it works in both classic and block themes.
 * The search icon button (in header.php or hch/search-form block) toggles this overlay.
 */
function hch_output_search_overlay() {
	?>
	<div class="hch-search-overlay" id="hchSearchOverlay" role="search">
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" class="hch-search-overlay__input"
				placeholder="<?php esc_attr_e( 'Search parts, specs, SKUs…', 'hch-electric' ); ?>"
				name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off"/>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product"/>
			<?php endif; ?>
			<button type="button" class="hch-search-overlay__close" id="hchSearchClose"
				aria-label="<?php esc_attr_e( 'Close search', 'hch-electric' ); ?>">✕</button>
		</form>
	</div>
	<?php
}
add_action( 'wp_footer', 'hch_output_search_overlay', 6 );

/**
 * Tell WordPress this is a block theme by declaring template parts for the
 * Site Editor.
 */
function hch_block_theme_support() {
	add_theme_support( 'block-template-parts' );
}
add_action( 'after_setup_theme', 'hch_block_theme_support', 20 );

/**
 * Update WooCommerce cart count in the block-theme header after AJAX add-to-cart.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $fragments;
	}
	$count = (int) WC()->cart->get_cart_contents_count();
	$fragments['span#hchCartCount'] = '<span class="hch-cart-btn__count" id="hchCartCount">' . $count . '</span>';
	return $fragments;
} );

/**
 * Shortcode: [hch_search_form]
 * WooCommerce-aware search form — filters by post_type=product when WooCommerce
 * is active. Used in parts/header.html so it works in block templates.
 */
function hch_search_form_shortcode() {
	ob_start(); ?>
	<button type="button" class="hch-search-icon" id="hchSearchToggle"
		aria-label="<?php esc_attr_e( 'Search', 'hch-electric' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
			<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
		</svg>
	</button>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_search_form', 'hch_search_form_shortcode' );

/**
 * Shortcode: [hch_cart_button]
 * Cart icon button with live PHP-rendered count. Placed in the block-theme
 * header part so the initial count is correct (JS updates it live thereafter).
 */
function hch_cart_button_shortcode() {
	$count = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$count = (int) WC()->cart->get_cart_contents_count();
	}
	ob_start(); ?>
	<button type="button" class="hch-cart-btn" id="hchCartBtn"
		aria-label="<?php esc_attr_e( 'Open cart', 'hch-electric' ); ?>">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
			<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
			<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
		</svg>
		<span class="hch-cart-btn__count" id="hchCartCount"><?php echo $count; ?></span>
	</button>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_cart_button', 'hch_cart_button_shortcode' );

/**
 * Shortcode: [hch_wc_archive]
 * Renders the WooCommerce product loop using the current main query (which
 * WooCommerce has already filtered for search, categories, etc.).
 * Used in archive-product.html and taxonomy-product_cat.html.
 */
function hch_wc_archive_shortcode() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}
	ob_start();
	if ( have_posts() ) {
		woocommerce_product_loop_start();
		while ( have_posts() ) {
			the_post();
			wc_get_template_part( 'content', 'product' );
		}
		woocommerce_product_loop_end();
		do_action( 'woocommerce_after_shop_loop' );
		wp_reset_postdata();
	} else {
		do_action( 'woocommerce_no_products_found' );
	}
	return ob_get_clean();
}
add_shortcode( 'hch_wc_archive', 'hch_wc_archive_shortcode' );

/**
 * Shortcode: [hch_wc_single]
 * Renders the full WooCommerce single product page (gallery, summary, tabs,
 * reviews) using the plugin's content-single-product.php template.
 * Used in single-product.html.
 */
function hch_wc_single_shortcode() {
	if ( ! class_exists( 'WooCommerce' ) || ! is_singular( 'product' ) ) {
		return '';
	}
	ob_start();
	while ( have_posts() ) {
		the_post();
		wc_get_template_part( 'content', 'single-product' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'hch_wc_single', 'hch_wc_single_shortcode' );

/**
 * Shortcode: [hch_footer_categories]
 * Footer column 1: assigned footer-1 nav menu or WooCommerce category list.
 */
function hch_footer_categories_shortcode() {
	ob_start();
	if ( has_nav_menu( 'footer-1' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'footer-1',
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => false,
		) );
	} elseif ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 8, 'parent' => 0 ) );
		if ( ! is_wp_error( $terms ) && $terms ) {
			echo '<ul>';
			foreach ( $terms as $t ) {
				echo '<li><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
			}
			echo '</ul>';
		}
	}
	return ob_get_clean();
}
add_shortcode( 'hch_footer_categories', 'hch_footer_categories_shortcode' );

/**
 * Shortcode: [hch_footer_contact]
 * Footer column 2: assigned footer-2 nav menu or default contact info.
 */
function hch_footer_contact_shortcode() {
	ob_start();
	if ( has_nav_menu( 'footer-2' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'footer-2',
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => false,
		) );
	} else {
		$email = get_theme_mod( 'hch_contact_email', 'hchevinternational@gmail.com' );
		?>
		<ul>
			<li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
			<li><span><?php esc_html_e( 'GST Reg. Business', 'hch-electric' ); ?></span></li>
			<li><span><?php esc_html_e( '18% GST on parts', 'hch-electric' ); ?></span></li>
			<li><span><?php esc_html_e( '5% GST on chargers', 'hch-electric' ); ?></span></li>
			<li><a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : '#' ); ?>"><?php esc_html_e( 'Privacy Policy', 'hch-electric' ); ?></a></li>
		</ul>
		<?php
	}
	return ob_get_clean();
}
add_shortcode( 'hch_footer_contact', 'hch_footer_contact_shortcode' );

/**
 * Shortcode: [hch_footer_bottom]
 * Renders the footer bottom bar: copyright + disclaimer (both Customizer-editable).
 */
function hch_footer_bottom_shortcode() {
	$copy = get_theme_mod( 'hch_footer_copy', sprintf( '© %s HCH Electric. All rights reserved.', gmdate( 'Y' ) ) );
	$disc = get_theme_mod( 'hch_footer_disc', __( 'Not affiliated with Ola, Ather, Bajaj, TVS or Hero. Aftermarket parts only.', 'hch-electric' ) );
	ob_start(); ?>
	<div class="hch-footer__copy"><?php echo esc_html( $copy ); ?></div>
	<div class="hch-footer__disc"><?php echo esc_html( $disc ); ?></div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'hch_footer_bottom', 'hch_footer_bottom_shortcode' );
