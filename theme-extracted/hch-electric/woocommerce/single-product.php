<?php
/**
 * Single product template.
 *
 * @package HCH_Electric
 */
defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<div class="hch-single">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php wc_get_template_part( 'content', 'single-product' ); ?>
	<?php endwhile; ?>
</div>

<?php get_footer( 'shop' );
