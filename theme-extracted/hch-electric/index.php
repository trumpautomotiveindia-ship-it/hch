<?php
/**
 * Main template / fallback.
 * @package HCH_Electric
 */
get_header(); ?>

<main class="hch-content" id="primary" role="main">
	<?php if ( have_posts() ) : ?>
		<header>
			<h1><?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} elseif ( is_archive() ) {
					the_archive_title();
				} elseif ( is_search() ) {
					/* translators: %s: search query */
					printf( esc_html__( 'Search results for: %s', 'hch-electric' ), '<span>' . get_search_query() . '</span>' );
				} else {
					esc_html_e( 'Latest', 'hch-electric' );
				}
			?></h1>
		</header>

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>

	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'hch-electric' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer();
