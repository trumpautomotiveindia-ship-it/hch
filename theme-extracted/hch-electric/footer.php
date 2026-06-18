<?php
/**
 * Footer + off-canvas cart drawer.
 * @package HCH_Electric
 */
$copy = get_theme_mod( 'hch_footer_copy', sprintf( '© %s HCH Electric. All rights reserved.', date( 'Y' ) ) );
$disc = get_theme_mod( 'hch_footer_disc', __( 'Not affiliated with Ola, Ather, Bajaj, TVS or Hero. Aftermarket parts only.', 'hch-electric' ) );
$about = get_theme_mod( 'hch_footer_about', __( 'Aftermarket EV spare parts for Indian electric scooters, e-rickshaws & e-cycles.', 'hch-electric' ) );
?>

<footer class="hch-footer" role="contentinfo">
	<div class="hch-footer__inner">
		<div class="hch-footer__grid">
			<div class="hch-footer__brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) {
						$logo_id = get_theme_mod( 'custom_logo' );
						$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
						if ( $logo ) : ?>
							<img src="<?php echo esc_url( $logo[0] ); ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:32px;"/>
						<?php endif;
					} else { ?>
						<span class="hch-logo__text"><?php bloginfo( 'name' ); ?></span>
					<?php } ?>
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
						<li><a href="#"><?php esc_html_e( 'GST Reg. Business', 'hch-electric' ); ?></a></li>
						<li><a href="#"><?php esc_html_e( '18% GST on parts', 'hch-electric' ); ?></a></li>
						<li><a href="#"><?php esc_html_e( '5% GST on chargers', 'hch-electric' ); ?></a></li>
						<li><a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : '#' ); ?>"><?php esc_html_e( 'Privacy Policy', 'hch-electric' ); ?></a></li>
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

<!-- CART DRAWER -->
<div class="hch-overlay" id="hchOverlay"></div>
<aside class="hch-drawer" id="hchDrawer" aria-label="<?php esc_attr_e( 'Cart', 'hch-electric' ); ?>">
	<div class="hch-drawer__hd">
		<div class="hch-drawer__title"><?php esc_html_e( 'Cart', 'hch-electric' ); ?></div>
		<button type="button" class="hch-drawer__close" id="hchCartClose" aria-label="<?php esc_attr_e( 'Close cart', 'hch-electric' ); ?>">✕</button>
	</div>
	<div class="hch-drawer__items" id="hchDrawerItems">
		<div class="hch-empty"><span class="hch-empty__ico">🛒</span><?php esc_html_e( 'Your cart is empty', 'hch-electric' ); ?></div>
	</div>
	<div class="hch-drawer__ft">
		<div class="hch-drawer__tot-row">
			<div class="hch-drawer__tot-lbl"><?php esc_html_e( 'Subtotal (excl. GST)', 'hch-electric' ); ?></div>
			<div class="hch-drawer__tot-val" id="hchDrawerTotal"><?php echo function_exists( 'wc_price' ) ? wc_price( 0 ) : '₹0'; ?></div>
		</div>
		<div class="hch-drawer__gst"><?php esc_html_e( 'GST calculated at checkout · Invoice provided', 'hch-electric' ); ?></div>
		<button type="button" class="hch-wa-btn" id="hchWaBtn">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
			<?php esc_html_e( 'Order via WhatsApp', 'hch-electric' ); ?>
		</button>
		<a class="hch-checkout-btn" href="<?php echo function_exists( 'wc_get_checkout_url' ) ? esc_url( wc_get_checkout_url() ) : '#'; ?>"><?php esc_html_e( 'Proceed to Checkout', 'hch-electric' ); ?></a>
	</div>
</aside>

<?php wp_footer(); ?>
</body>
</html>
