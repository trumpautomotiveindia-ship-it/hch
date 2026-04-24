<?php
/**
 * Product custom fields: SKU-style spec line, MOQ, badge, emoji icon.
 * No ACF dependency — native meta boxes on the Product edit screen.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "HCH Details" meta box to WooCommerce products.
 */
function hch_add_product_meta_box() {
	add_meta_box(
		'hch_product_details',
		__( 'HCH Electric — Product Details', 'hch-electric' ),
		'hch_render_product_meta_box',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'hch_add_product_meta_box' );

function hch_render_product_meta_box( $post ) {
	wp_nonce_field( 'hch_save_product_meta', 'hch_product_nonce' );
	$spec  = get_post_meta( $post->ID, '_hch_spec', true );
	$moq   = get_post_meta( $post->ID, '_hch_moq', true );
	$badge = get_post_meta( $post->ID, '_hch_badge', true );
	$icon  = get_post_meta( $post->ID, '_hch_icon', true );
	$note  = get_post_meta( $post->ID, '_hch_price_note', true );
	if ( '' === $note ) { $note = '/pc excl. GST'; }
	?>
	<p>
		<label for="hch_spec"><strong><?php esc_html_e( 'Spec line', 'hch-electric' ); ?></strong></label><br/>
		<input type="text" name="hch_spec" id="hch_spec" class="widefat" value="<?php echo esc_attr( $spec ); ?>" placeholder="e.g. 67.2V · 6A · NMC"/>
		<span class="description"><?php esc_html_e( 'Shown above the product title on cards.', 'hch-electric' ); ?></span>
	</p>
	<p>
		<label for="hch_moq"><strong><?php esc_html_e( 'MOQ', 'hch-electric' ); ?></strong></label><br/>
		<input type="number" min="1" step="1" name="hch_moq" id="hch_moq" class="widefat" value="<?php echo esc_attr( $moq ); ?>" placeholder="1"/>
		<span class="description"><?php esc_html_e( 'Minimum order quantity (also enforced on add to cart).', 'hch-electric' ); ?></span>
	</p>
	<p>
		<label for="hch_badge"><strong><?php esc_html_e( 'Badge', 'hch-electric' ); ?></strong></label><br/>
		<select name="hch_badge" id="hch_badge" class="widefat">
			<option value=""><?php esc_html_e( '— None —', 'hch-electric' ); ?></option>
			<option value="s" <?php selected( $badge, 's' ); ?>><?php esc_html_e( 'IN STOCK (green)', 'hch-electric' ); ?></option>
			<option value="h" <?php selected( $badge, 'h' ); ?>><?php esc_html_e( 'POPULAR (orange)', 'hch-electric' ); ?></option>
			<option value="n" <?php selected( $badge, 'n' ); ?>><?php esc_html_e( 'NEW (blue)', 'hch-electric' ); ?></option>
			<option value="d" <?php selected( $badge, 'd' ); ?>><?php esc_html_e( 'DEAL (dark/cyan)', 'hch-electric' ); ?></option>
		</select>
	</p>
	<p>
		<label for="hch_icon"><strong><?php esc_html_e( 'Fallback emoji icon', 'hch-electric' ); ?></strong></label><br/>
		<input type="text" name="hch_icon" id="hch_icon" class="widefat" value="<?php echo esc_attr( $icon ); ?>" placeholder="⚡"/>
		<span class="description"><?php esc_html_e( 'Shown when the product has no featured image.', 'hch-electric' ); ?></span>
	</p>
	<p>
		<label for="hch_price_note"><strong><?php esc_html_e( 'Price note', 'hch-electric' ); ?></strong></label><br/>
		<input type="text" name="hch_price_note" id="hch_price_note" class="widefat" value="<?php echo esc_attr( $note ); ?>"/>
	</p>
	<?php
}

function hch_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['hch_product_nonce'] ) || ! wp_verify_nonce( $_POST['hch_product_nonce'], 'hch_save_product_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$fields = array(
		'_hch_spec'       => isset( $_POST['hch_spec'] ) ? sanitize_text_field( wp_unslash( $_POST['hch_spec'] ) ) : '',
		'_hch_moq'        => isset( $_POST['hch_moq'] ) ? max( 1, absint( $_POST['hch_moq'] ) ) : '',
		'_hch_badge'      => isset( $_POST['hch_badge'] ) ? sanitize_key( $_POST['hch_badge'] ) : '',
		'_hch_icon'       => isset( $_POST['hch_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['hch_icon'] ) ) : '',
		'_hch_price_note' => isset( $_POST['hch_price_note'] ) ? sanitize_text_field( wp_unslash( $_POST['hch_price_note'] ) ) : '',
	);
	foreach ( $fields as $key => $value ) {
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_product', 'hch_save_product_meta' );

/**
 * Category term meta: icon emoji.
 */
function hch_category_icon_field( $term ) {
	$icon = is_object( $term ) ? get_term_meta( $term->term_id, 'hch_icon', true ) : '';
	?>
	<tr class="form-field">
		<th scope="row"><label for="hch_icon"><?php esc_html_e( 'HCH icon (emoji)', 'hch-electric' ); ?></label></th>
		<td>
			<input type="text" name="hch_icon" id="hch_icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="⚡"/>
			<p class="description"><?php esc_html_e( 'Shown in the category bar.', 'hch-electric' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'hch_category_icon_field' );

function hch_category_icon_add_field() {
	?>
	<div class="form-field">
		<label for="hch_icon"><?php esc_html_e( 'HCH icon (emoji)', 'hch-electric' ); ?></label>
		<input type="text" name="hch_icon" id="hch_icon" value="" placeholder="⚡"/>
		<p><?php esc_html_e( 'Shown in the category bar.', 'hch-electric' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'hch_category_icon_add_field' );

function hch_save_category_icon( $term_id ) {
	if ( isset( $_POST['hch_icon'] ) ) {
		update_term_meta( $term_id, 'hch_icon', sanitize_text_field( wp_unslash( $_POST['hch_icon'] ) ) );
	}
}
add_action( 'edited_product_cat', 'hch_save_category_icon' );
add_action( 'create_product_cat', 'hch_save_category_icon' );
