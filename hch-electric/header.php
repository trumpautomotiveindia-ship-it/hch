<?php
/**
 * Site header: ticker + navigation bar with logo/search/cart.
 * @package HCH_Electric
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#primary" class="hch-skip-link"><?php esc_html_e( 'Skip to content', 'hch-electric' ); ?></a>

<?php echo do_shortcode( '[hch_ticker]' ); ?>

<header class="hch-header" role="banner">
	<div class="hch-header__inner">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hch-logo__link" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<span class="hch-logo__text">hchelectric.in</span>
		</a>

		<?php
		$cart_count = 0;
		if ( function_exists( 'WC' ) && WC()->cart ) {
			$cart_count = WC()->cart->get_cart_contents_count();
		}
		?>
		<button type="button" class="hch-cart-btn" id="hchCartBtn" aria-label="<?php esc_attr_e( 'Open cart', 'hch-electric' ); ?>">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
				<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
				<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
			</svg>
			<span class="hch-cart-btn__count" id="hchCartCount"><?php echo (int) $cart_count; ?></span>
		</button>

		<button type="button" class="hch-search-icon" id="hchSearchToggle" aria-label="<?php esc_attr_e( 'Search', 'hch-electric' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
				<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
			</svg>
		</button>

	</div>
</header>
