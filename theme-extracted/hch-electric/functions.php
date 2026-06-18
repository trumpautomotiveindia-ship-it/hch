<?php
/**
 * HCH Electric theme bootstrap.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HCH_VERSION', '1.5.0' );
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
	// Google fonts — Inter (professional, clean).
	wp_enqueue_style(
		'hch-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'hch-style', get_stylesheet_uri(), array( 'hch-fonts' ), HCH_VERSION );

	wp_enqueue_script( 'hch-main', HCH_URL . '/js/hch.js', array( 'jquery' ), HCH_VERSION, true );

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

if ( file_exists( HCH_DIR . '/inc/text-config.php' ) ) {
	require_once HCH_DIR . '/inc/text-config.php';
}
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
			<button type="button" class="hch-brand-chip active" data-brand="all"><?php esc_html_e( 'All', 'hch-electric' ); ?></button>
			<?php foreach ( $terms as $t ) : ?>
				<button type="button" class="hch-brand-chip" data-brand="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
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
			<button type="button" class="hch-cat active" data-cat="all">
				<span class="hch-cat__ico">⚡</span><?php esc_html_e( 'All', 'hch-electric' ); ?>
			</button>
			<?php foreach ( $terms as $t ) :
				$icon = get_term_meta( $t->term_id, 'hch_icon', true );
				if ( ! $icon ) { $icon = '•'; } ?>
				<button type="button" class="hch-cat" data-cat="<?php echo esc_attr( $t->slug ); ?>">
					<span class="hch-cat__ico"><?php echo esc_html( $icon ); ?></span>
					<?php echo esc_html( $t->name ); ?>
					<span class="hch-cat__cnt"><?php echo (int) $t->count; ?></span>
				</button>
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
