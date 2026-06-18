<?php
/** @package HCH_Electric */
?>
<form role="search" method="get" class="hch-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<span class="hch-search__ico">⌕</span>
	<label for="hch-s" class="screen-reader-text"><?php esc_html_e( 'Search', 'hch-electric' ); ?></label>
	<input id="hch-s" class="hch-search__input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Search parts, specs, SKUs…', 'hch-electric' ); ?>"/>
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product"/>
	<?php endif; ?>
</form>
