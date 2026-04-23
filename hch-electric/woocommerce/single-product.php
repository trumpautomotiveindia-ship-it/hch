<?php
/**
 * Single product page template override.
 *
 * In block-theme context this file is never loaded as a page template —
 * WordPress uses templates/single-product.html instead, and the
 * [hch_wc_single] shortcode calls wc_get_template_part('content','single-product')
 * which loads content-single-product.php from the WooCommerce plugin.
 *
 * In classic-theme context (fallback) this file is the page template and
 * wraps the product with the theme header/footer.
 *
 * @package HCH_Electric
 */
defined( 'ABSPATH' ) || exit;

// Only load classic header/footer when NOT in block-theme mode.
$is_block_theme = function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();

if ( ! $is_block_theme ) {
	get_header();
}
?>

<div class="hch-single">
	<?php
	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'single-product' );
	endwhile;
	?>
</div>

<?php
if ( ! $is_block_theme ) {
	get_footer();
}
