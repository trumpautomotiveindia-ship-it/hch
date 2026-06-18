<?php
/** Single post template. @package HCH_Electric */
get_header(); ?>
<main class="hch-content" id="primary" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large' ); endif; ?>
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer();
