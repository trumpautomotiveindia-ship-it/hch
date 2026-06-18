<?php
/**
 * Template Name: Canvas (No Header/Footer)
 * Blank canvas — ideal for landing pages or conversion pages.
 *
 * @package HCH_Electric
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'hch-canvas' ); ?>>
<?php wp_body_open(); ?>

<main id="primary" role="main" class="hch-canvas-main">
	<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
