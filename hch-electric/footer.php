<?php
/**
 * Footer.
 * Cart drawer and search overlay are output via wp_footer hooks in functions.php
 * so they work in both classic-PHP and block-theme contexts.
 * @package HCH_Electric
 */
$copy  = get_theme_mod( 'hch_footer_copy', sprintf( '© %s HCH Electric. All rights reserved.', gmdate( 'Y' ) ) );
$disc  = get_theme_mod( 'hch_footer_disc', __( 'Not affiliated with Ola, Ather, Bajaj, TVS or Hero. Aftermarket parts only.', 'hch-electric' ) );
$about = get_theme_mod( 'hch_footer_about', __( 'Aftermarket EV spare parts for Indian electric scooters, e-rickshaws & e-cycles.', 'hch-electric' ) );
?>

<footer class="hch-footer" role="contentinfo">
	<div class="hch-footer__inner">
		<div class="hch-footer__grid">
			<div class="hch-footer__brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hch-logo__link">
					<span class="hch-logo__text">hchelectric.in</span>
				</a>
				<p><?php echo esc_html( $about ); ?></p>
			</div>

			<div class="hch-footer__col">
				<h4><?php esc_html_e( 'Categories', 'hch-electric' ); ?></h4>
				<?php if ( has_nav_menu( 'footer-1' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'footer-1',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					) );
				} elseif ( taxonomy_exists( 'product_cat' ) ) {
					$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 6, 'parent' => 0 ) );
					if ( ! is_wp_error( $terms ) && $terms ) {
						echo '<ul>';
						foreach ( $terms as $t ) {
							echo '<li><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
						}
						echo '</ul>';
					}
				} ?>
			</div>

			<div class="hch-footer__col">
				<h4><?php esc_html_e( 'Contact', 'hch-electric' ); ?></h4>
				<?php if ( has_nav_menu( 'footer-2' ) ) {
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
						<li><a href="<?php echo esc_url( get_privacy_policy_url() ?: '#' ); ?>"><?php esc_html_e( 'Privacy Policy', 'hch-electric' ); ?></a></li>
					</ul>
				<?php } ?>
			</div>
		</div>

		<div class="hch-footer__btm">
			<div class="hch-footer__copy"><?php echo esc_html( $copy ); ?></div>
			<div class="hch-footer__disc"><?php echo esc_html( $disc ); ?></div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
