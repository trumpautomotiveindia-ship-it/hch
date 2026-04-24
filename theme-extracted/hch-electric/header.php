<?php
/**
 * Site header: ticker + navigation bar with logo/search/cart.
 * @package HCH_Electric
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<link rel="profile" href="https://gmpg.org/xfn/11"/>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php echo do_shortcode( '[hch_ticker]' ); ?>

<header class="hch-header" role="banner">
	<div class="hch-header__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hch-logo" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php if ( has_custom_logo() ) {
				$logo_id = get_theme_mod( 'custom_logo' );
				$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
				if ( $logo ) : ?>
					<img src="<?php echo esc_url( $logo[0] ); ?>" alt="<?php bloginfo( 'name' ); ?>"/>
				<?php endif;
			} else { ?>
				<span class="hch-logo__text"><?php bloginfo( 'name' ); ?></span>
			<?php } ?>
		</a>

		<div class="hch-header__right">
			<form role="search" method="get" class="hch-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="hch-search__ico">⌕</span>
				<input class="hch-search__input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search parts, SKUs…', 'hch-electric' ); ?>" autocomplete="off"/>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<input type="hidden" name="post_type" value="product"/>
				<?php endif; ?>
			</form>

			<?php
			$cart_count = 0;
			if ( function_exists( 'WC' ) && WC()->cart ) {
				$cart_count = WC()->cart->get_cart_contents_count();
			}
			?>
			<button type="button" class="hch-cart-btn" id="hchCartBtn" aria-label="<?php esc_attr_e( 'Open cart', 'hch-electric' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
					<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
				</svg>
				<span class="hch-cart-btn__count" id="hchCartCount"><?php echo (int) $cart_count; ?></span>
			</button>
		</div>
	</div>
</header>
