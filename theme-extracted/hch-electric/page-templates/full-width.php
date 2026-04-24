<?php
/**
 * Template Name: Full Width
 * Full-width page with header & footer but no sidebar / no container.
 *
 * @package HCH_Electric
 */
get_header(); ?>

<main id="primary" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'hch-full' ); ?>>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</main>

<?php get_footer();
