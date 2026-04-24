<?php
/**
 * One-click demo-data seeder.
 *
 * Accessible under Tools → HCH Demo Data.
 * Creates:
 *   • `pa_brand` product attribute + 10 brand terms
 *   • 16 `product_cat` terms (with emoji icons in term meta)
 *   • 54 sample WooCommerce products with spec / MOQ / badge / category / brand
 *
 * Idempotent: rerunning the seeder skips anything that already exists.
 * Safe-guarded by capability check (`manage_options`) + nonce.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Register the admin page. */
function hch_seed_register_menu() {
	add_management_page(
		__( 'HCH Demo Data', 'hch-electric' ),
		__( 'HCH Demo Data', 'hch-electric' ),
		'manage_options',
		'hch-seed',
		'hch_seed_render_page'
	);
}
add_action( 'admin_menu', 'hch_seed_register_menu' );

function hch_seed_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$report = array();
	if ( isset( $_POST['hch_seed_nonce'] ) && wp_verify_nonce( $_POST['hch_seed_nonce'], 'hch_seed_run' ) ) {
		if ( isset( $_POST['action'] ) && 'run' === $_POST['action'] ) {
			$report = hch_seed_run();
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'HCH Electric — Demo Data Seeder', 'hch-electric' ); ?></h1>
		<p style="max-width:720px;"><?php esc_html_e( 'One-click import of the full EV-parts catalog that matches the homepage design. Creates the Brand attribute, 10 brand terms, 16 product categories (with emoji icons) and 54 products with spec / MOQ / badge / category / brand pre-filled. Safe to rerun — existing items are skipped.', 'hch-electric' ); ?></p>

		<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'WooCommerce is not active.', 'hch-electric' ); ?></strong> <?php esc_html_e( 'Install & activate WooCommerce before running the seeder.', 'hch-electric' ); ?></p></div>
			<?php return; ?>
		<?php endif; ?>

		<?php if ( ! empty( $report ) ) : ?>
			<div class="notice notice-success" style="padding:14px 16px;">
				<h3 style="margin-top:0;"><?php esc_html_e( 'Seeding complete', 'hch-electric' ); ?></h3>
				<ul style="margin:6px 0 0 18px;list-style:disc;">
					<li><?php printf( esc_html__( 'Brand attribute: %s', 'hch-electric' ), esc_html( $report['attribute'] ) ); ?></li>
					<li><?php printf( esc_html__( 'Brand terms created: %d (skipped %d existing)', 'hch-electric' ), (int) $report['brands_new'], (int) $report['brands_skip'] ); ?></li>
					<li><?php printf( esc_html__( 'Categories created: %d (skipped %d existing)', 'hch-electric' ), (int) $report['cats_new'], (int) $report['cats_skip'] ); ?></li>
					<li><?php printf( esc_html__( 'Products created: %d (skipped %d existing)', 'hch-electric' ), (int) $report['prods_new'], (int) $report['prods_skip'] ); ?></li>
				</ul>
				<p style="margin:10px 0 0;"><a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View homepage →', 'hch-electric' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php esc_html_e( 'View products', 'hch-electric' ); ?></a></p>
			</div>
		<?php endif; ?>

		<form method="post" style="margin-top:20px;background:#fff;border:1px solid #ddd;padding:20px;max-width:720px;">
			<?php wp_nonce_field( 'hch_seed_run', 'hch_seed_nonce' ); ?>
			<input type="hidden" name="action" value="run"/>
			<p style="margin-top:0;"><?php esc_html_e( 'This will import demo data into your WooCommerce store. Recommended only on a fresh install or staging site.', 'hch-electric' ); ?></p>
			<ul style="list-style:disc;margin-left:18px;color:#555;font-size:13px;">
				<li><?php esc_html_e( '10 brand terms (OLA S1, Ather 450, Bajaj Chetak, TVS iQube, Hero Vida, Ampere, Okinawa, Revolt, E-Rickshaw, Local EVs)', 'hch-electric' ); ?></li>
				<li><?php esc_html_e( '16 product categories with emoji icons', 'hch-electric' ); ?></li>
				<li><?php esc_html_e( '54 sample products with prices in INR, MOQ, spec lines and stock badges', 'hch-electric' ); ?></li>
			</ul>
			<p><button type="submit" class="button button-primary button-hero" onclick="return confirm('<?php echo esc_js( __( 'Run the seeder now? Existing data will not be overwritten.', 'hch-electric' ) ); ?>');"><?php esc_html_e( 'Seed Demo Data', 'hch-electric' ); ?></button></p>
		</form>
	</div>
	<?php
}

/**
 * Master run function.
 *
 * @return array report
 */
function hch_seed_run() {
	$report = array(
		'attribute'   => '—',
		'brands_new'  => 0, 'brands_skip' => 0,
		'cats_new'    => 0, 'cats_skip'   => 0,
		'prods_new'   => 0, 'prods_skip'  => 0,
	);

	/* ── 1. Brand attribute (pa_brand) ── */
	$attr_id = hch_seed_ensure_attribute( 'brand', __( 'Brand', 'hch-electric' ) );
	$report['attribute'] = $attr_id ? 'pa_brand #' . $attr_id : 'failed';

	/* Register the taxonomy for this request so term creation works before flush. */
	if ( ! taxonomy_exists( 'pa_brand' ) ) {
		register_taxonomy( 'pa_brand', array( 'product' ), array( 'public' => true, 'hierarchical' => false ) );
	}

	$brands = array(
		'ola'        => 'OLA S1',
		'ather'      => 'Ather 450',
		'chetak'     => 'Bajaj Chetak',
		'iqube'      => 'TVS iQube',
		'vida'       => 'Hero Vida',
		'ampere'     => 'Ampere',
		'okinawa'    => 'Okinawa',
		'revolt'     => 'Revolt',
		'erickshaw'  => 'E-Rickshaw',
		'ecycle'     => 'E-Cycle',
		'local'      => 'Local EVs',
	);
	foreach ( $brands as $slug => $name ) {
		if ( term_exists( $slug, 'pa_brand' ) ) {
			$report['brands_skip']++;
			continue;
		}
		$res = wp_insert_term( $name, 'pa_brand', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $res ) ) $report['brands_new']++;
	}

	/* ── 2. Product categories with emoji icons ── */
	$cats = array(
		'battery'     => array( 'Battery & BMS',      '🔋' ),
		'charger'     => array( 'Chargers',           '⚡' ),
		'controller'  => array( 'Controllers',        '⚙️' ),
		'motor'       => array( 'Motors',             '🔩' ),
		'throttle'    => array( 'Throttles',          '🎛️' ),
		'cable'       => array( 'Cables',             '🔌' ),
		'dcdc'        => array( 'DC-DC Converters',   '🔄' ),
		'brake'       => array( 'Brake Parts',        '🛑' ),
		'body'        => array( 'Body Kits',          '🏍️' ),
		'suspension'  => array( 'Suspension',         '🔧' ),
		'switch'      => array( 'Switches',           '💡' ),
		'erickshaw'   => array( 'E-Rickshaw Parts',   '🛺' ),
		'ecycle'      => array( 'E-Cycle Parts',      '🚲' ),
		'conversion'  => array( 'Conversion Kits',    '🧰' ),
		'testing'     => array( 'Testing Machines',   '🧪' ),
		'clearance'   => array( 'Clearance Deals',    '🏷️' ),
	);
	$cat_term_ids = array();
	foreach ( $cats as $slug => $meta ) {
		$term = term_exists( $slug, 'product_cat' );
		if ( $term ) {
			$cat_term_ids[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			if ( ! get_term_meta( $cat_term_ids[ $slug ], 'hch_icon', true ) ) {
				update_term_meta( $cat_term_ids[ $slug ], 'hch_icon', $meta[1] );
			}
			$report['cats_skip']++;
			continue;
		}
		$res = wp_insert_term( $meta[0], 'product_cat', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $res ) ) {
			$cat_term_ids[ $slug ] = (int) $res['term_id'];
			update_term_meta( (int) $res['term_id'], 'hch_icon', $meta[1] );
			$report['cats_new']++;
		}
	}

	/* ── 3. Products (54 SKUs from the HTML mockup) ── */
	foreach ( hch_seed_products() as $p ) {
		if ( hch_seed_find_product_by_sku( $p['sku'] ) ) {
			$report['prods_skip']++;
			continue;
		}
		$pid = hch_seed_create_product( $p, $cat_term_ids );
		if ( $pid ) $report['prods_new']++;
	}

	return $report;
}

/**
 * Create the "Brand" WooCommerce product attribute if missing, returns attribute ID.
 */
function hch_seed_ensure_attribute( $slug, $name ) {
	global $wpdb;
	$existing = $wpdb->get_row( $wpdb->prepare(
		"SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s",
		$slug
	) );
	if ( $existing ) {
		return (int) $existing->attribute_id;
	}
	$id = wc_create_attribute( array(
		'name'         => $name,
		'slug'         => $slug,
		'type'         => 'select',
		'order_by'     => 'menu_order',
		'has_archives' => true,
	) );
	if ( is_wp_error( $id ) ) return 0;
	return (int) $id;
}

/**
 * Find a product by SKU (return post_id or false).
 */
function hch_seed_find_product_by_sku( $sku ) {
	if ( empty( $sku ) ) return false;
	$id = wc_get_product_id_by_sku( $sku );
	return $id ? (int) $id : false;
}

/**
 * Create one simple product.
 *
 * @param array $p            product array
 * @param array $cat_term_ids slug => term_id
 * @return int|false product id
 */
function hch_seed_create_product( $p, $cat_term_ids ) {
	$product = new WC_Product_Simple();
	$product->set_name( $p['name'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $p['sku'] );
	$product->set_regular_price( (string) $p['price'] );
	$product->set_price( (string) $p['price'] );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );
	$product->set_short_description( esc_html( $p['spec'] ) );
	$product->set_description( sprintf(
		"<p><strong>%s</strong></p><p>%s</p><p>%s</p>",
		esc_html( $p['spec'] ),
		esc_html__( 'Aftermarket EV spare part. Ships PAN-India same-day before 2PM with GST invoice.', 'hch-electric' ),
		esc_html__( 'MOQ applies. Bulk pricing available on request via WhatsApp.', 'hch-electric' )
	) );

	/* Assign category */
	if ( isset( $p['cat'] ) && isset( $cat_term_ids[ $p['cat'] ] ) ) {
		$product->set_category_ids( array( $cat_term_ids[ $p['cat'] ] ) );
	}

	$pid = $product->save();
	if ( ! $pid ) return false;

	/* Brand attribute (pa_brand) */
	if ( ! empty( $p['brands'] ) ) {
		$terms = array();
		foreach ( (array) $p['brands'] as $brand_slug ) {
			if ( 'all' === $brand_slug ) continue;
			$t = term_exists( $brand_slug, 'pa_brand' );
			if ( $t ) {
				$terms[] = is_array( $t ) ? $t['slug'] : $brand_slug;
			}
		}
		if ( $terms ) {
			wp_set_object_terms( $pid, $terms, 'pa_brand', false );
			$attributes = array();
			$attr = new WC_Product_Attribute();
			$attr->set_id( wc_attribute_taxonomy_id_by_name( 'pa_brand' ) );
			$attr->set_name( 'pa_brand' );
			$attr->set_options( $terms );
			$attr->set_visible( true );
			$attr->set_variation( false );
			$attributes[] = $attr;
			$product->set_attributes( $attributes );
			$product->save();
		}
	}

	/* HCH custom meta */
	update_post_meta( $pid, '_hch_spec',       $p['spec']  ?? '' );
	update_post_meta( $pid, '_hch_moq',        (int) ( $p['moq']  ?? 1 ) );
	update_post_meta( $pid, '_hch_badge',      $p['badge'] ?? '' );
	update_post_meta( $pid, '_hch_icon',       $p['icon']  ?? '' );
	update_post_meta( $pid, '_hch_price_note', '/pc excl. GST' );

	return $pid;
}

/**
 * The full 54-SKU catalogue lifted verbatim from the HTML mockup.
 *
 * @return array[]
 */
function hch_seed_products() {
	return array(
		array( 'cat'=>'charger','brands'=>array('ola','ather','chetak','iqube','vida'),'icon'=>'⚡','name'=>'Lithium EV Charger 67.2V 6A','spec'=>'67.2V · 6A · NMC','price'=>848,'moq'=>5,'badge'=>'s','sku'=>'0108' ),
		array( 'cat'=>'charger','brands'=>array('ola','ather'),'icon'=>'⚡','name'=>'Lithium EV Charger CQLI','spec'=>'67.2V · 6A · CQLI','price'=>1999,'moq'=>2,'badge'=>'h','sku'=>'0241' ),
		array( 'cat'=>'charger','brands'=>array('chetak','iqube'),'icon'=>'⚡','name'=>'Lithium EV Charger LFP 69V','spec'=>'69V · 6A · LFP','price'=>898,'moq'=>5,'badge'=>'s','sku'=>'09C4' ),
		array( 'cat'=>'charger','brands'=>array('local','ampere'),'icon'=>'⚡','name'=>'Lithium EV Charger 54.6V','spec'=>'54.6V · 6A · NMC','price'=>898,'moq'=>5,'badge'=>'s','sku'=>'09C5' ),
		array( 'cat'=>'charger','brands'=>array(),'icon'=>'⚡','name'=>'Lithium EV Charger 54.6V 8A','spec'=>'54.6V · 8A · NMC','price'=>1199,'moq'=>3,'badge'=>'n','sku'=>'0478' ),
		array( 'cat'=>'charger','brands'=>array('local','erickshaw'),'icon'=>'⚡','name'=>'Lead Acid EV Charger 60V','spec'=>'60V · 12A · VRLA','price'=>750,'moq'=>10,'badge'=>'s','sku'=>'LA60' ),
		array( 'cat'=>'charger','brands'=>array('erickshaw'),'icon'=>'⚡','name'=>'E-Rickshaw Charger Fast','spec'=>'60V · 20A · Fast Charge','price'=>1400,'moq'=>3,'badge'=>'h','sku'=>'ER60' ),
		array( 'cat'=>'battery','brands'=>array('ola','local'),'icon'=>'🔋','name'=>'EV Scooter BMS 48V','spec'=>'48V · 20A · LFP','price'=>650,'moq'=>10,'badge'=>'s','sku'=>'BMS48' ),
		array( 'cat'=>'battery','brands'=>array('ather','ola'),'icon'=>'🔋','name'=>'EV Scooter BMS 60V','spec'=>'60V · 30A · NMC','price'=>820,'moq'=>10,'badge'=>'s','sku'=>'BMS60' ),
		array( 'cat'=>'battery','brands'=>array(),'icon'=>'🔋','name'=>'Li-Ion Cell 18650','spec'=>'3.7V · 2500mAh','price'=>180,'moq'=>50,'badge'=>'h','sku'=>'CELL18' ),
		array( 'cat'=>'battery','brands'=>array('local'),'icon'=>'🔋','name'=>'LFP Battery Pack 48V','spec'=>'48V · 30Ah · Grade-A','price'=>9800,'moq'=>1,'badge'=>'n','sku'=>'LFP48' ),
		array( 'cat'=>'battery','brands'=>array(),'icon'=>'🔋','name'=>'Cell Holder 4S3P','spec'=>'18650 · 4S3P · ABS','price'=>120,'moq'=>20,'badge'=>'s','sku'=>'HOLD4S' ),
		array( 'cat'=>'battery','brands'=>array(),'icon'=>'🔋','name'=>'Nickel Strip 0.15mm','spec'=>'Pure Nickel · 1M Roll','price'=>380,'moq'=>10,'badge'=>'s','sku'=>'NKSTRIP' ),
		array( 'cat'=>'controller','brands'=>array('local','ampere'),'icon'=>'⚙️','name'=>'Sine Wave Controller 60V','spec'=>'60V · 40A · BLDC','price'=>1250,'moq'=>5,'badge'=>'s','sku'=>'SWC60' ),
		array( 'cat'=>'controller','brands'=>array('ola','ather'),'icon'=>'⚙️','name'=>'FOC Motor Controller','spec'=>'48V · 60A · PMSM','price'=>2100,'moq'=>2,'badge'=>'n','sku'=>'FOC48' ),
		array( 'cat'=>'controller','brands'=>array('erickshaw'),'icon'=>'⚙️','name'=>'E-Rickshaw Controller','spec'=>'60V · 80A · BLDC','price'=>1800,'moq'=>3,'badge'=>'h','sku'=>'ERC80' ),
		array( 'cat'=>'controller','brands'=>array('local'),'icon'=>'⚙️','name'=>'Dual Mode Controller','spec'=>'48/60V · 45A · Auto','price'=>1450,'moq'=>5,'badge'=>'s','sku'=>'DMC45' ),
		array( 'cat'=>'motor','brands'=>array('local'),'icon'=>'🔩','name'=>'Hub Motor Rear 1000W','spec'=>'1000W · PMSM · 48V','price'=>4200,'moq'=>1,'badge'=>'s','sku'=>'HMR10' ),
		array( 'cat'=>'motor','brands'=>array('ecycle'),'icon'=>'🔩','name'=>'BLDC Mid Drive 500W','spec'=>'500W · BLDC · 36V','price'=>2800,'moq'=>1,'badge'=>'n','sku'=>'BDM5' ),
		array( 'cat'=>'motor','brands'=>array('erickshaw'),'icon'=>'🔩','name'=>'E-Rickshaw Motor 1200W','spec'=>'1200W · PMDC · 60V','price'=>5500,'moq'=>1,'badge'=>'h','sku'=>'ERM12' ),
		array( 'cat'=>'motor','brands'=>array(),'icon'=>'🔩','name'=>'Motor Hall Sensor','spec'=>'5V · 3-Phase · IP54','price'=>120,'moq'=>20,'badge'=>'s','sku'=>'MHS3' ),
		array( 'cat'=>'throttle','brands'=>array('ola','local'),'icon'=>'🎛️','name'=>'Twist Throttle w/ Display','spec'=>'48V · Hall · IP65','price'=>320,'moq'=>10,'badge'=>'s','sku'=>'TTD48' ),
		array( 'cat'=>'throttle','brands'=>array(),'icon'=>'🎛️','name'=>'Half Twist Throttle','spec'=>'48-72V · 3-Wire','price'=>180,'moq'=>20,'badge'=>'s','sku'=>'HTT72' ),
		array( 'cat'=>'throttle','brands'=>array(),'icon'=>'🎛️','name'=>'Thumb Throttle Waterproof','spec'=>'36-72V · IP67','price'=>150,'moq'=>20,'badge'=>'h','sku'=>'TT72' ),
		array( 'cat'=>'throttle','brands'=>array(),'icon'=>'🎛️','name'=>'Regen Brake Lever','spec'=>'48V · Hall · L/R','price'=>220,'moq'=>10,'badge'=>'n','sku'=>'RBL48' ),
		array( 'cat'=>'cable','brands'=>array(),'icon'=>'🔌','name'=>'Battery Phase Cable Set','spec'=>'XT60 · 10AWG · 1M','price'=>180,'moq'=>20,'badge'=>'s','sku'=>'BPC10' ),
		array( 'cat'=>'cable','brands'=>array(),'icon'=>'🔌','name'=>'Anderson Connector 50A','spec'=>'SB50 · 12-6AWG','price'=>95,'moq'=>25,'badge'=>'h','sku'=>'AND50' ),
		array( 'cat'=>'cable','brands'=>array(),'icon'=>'🔌','name'=>'XT60 Connector Pair','spec'=>'Male+Female · Gold','price'=>45,'moq'=>50,'badge'=>'s','sku'=>'XT60' ),
		array( 'cat'=>'cable','brands'=>array(),'icon'=>'🔌','name'=>'MX60 Connector Set','spec'=>'60A · High Current','price'=>120,'moq'=>30,'badge'=>'s','sku'=>'MX60' ),
		array( 'cat'=>'cable','brands'=>array(),'icon'=>'🔌','name'=>'Silicone Wire 10AWG','spec'=>'10AWG · Red · 1M','price'=>55,'moq'=>50,'badge'=>'s','sku'=>'PVC10R' ),
		array( 'cat'=>'dcdc','brands'=>array(),'icon'=>'🔄','name'=>'DC-DC Converter 48V-12V','spec'=>'48V-12V · 10A · Isolated','price'=>680,'moq'=>5,'badge'=>'s','sku'=>'DCDC12' ),
		array( 'cat'=>'dcdc','brands'=>array(),'icon'=>'🔄','name'=>'DC-DC Converter USB 5V','spec'=>'60V-5V · 3A · USB','price'=>320,'moq'=>10,'badge'=>'n','sku'=>'DCDC5' ),
		array( 'cat'=>'brake','brands'=>array('local'),'icon'=>'🛑','name'=>'Disc Brake Caliper 160mm','spec'=>'160mm · Hydraulic','price'=>550,'moq'=>5,'badge'=>'s','sku'=>'DBC160' ),
		array( 'cat'=>'brake','brands'=>array(),'icon'=>'🛑','name'=>'Brake Disc Rotor 160mm','spec'=>'160mm · Stainless','price'=>280,'moq'=>10,'badge'=>'s','sku'=>'BDR160' ),
		array( 'cat'=>'brake','brands'=>array(),'icon'=>'🛑','name'=>'Brake Pad Set 160mm','spec'=>'Semi-Metallic','price'=>120,'moq'=>20,'badge'=>'h','sku'=>'BPS160' ),
		array( 'cat'=>'body','brands'=>array('ola'),'icon'=>'🏍️','name'=>'OLA S1 Front Fairing','spec'=>'OLA S1 Pro · ABS','price'=>1200,'moq'=>2,'badge'=>'n','sku'=>'OLA-FF' ),
		array( 'cat'=>'body','brands'=>array('local'),'icon'=>'🏍️','name'=>'Scooter Side Panel Set','spec'=>'Universal · PP','price'=>850,'moq'=>3,'badge'=>'s','sku'=>'SPS-U' ),
		array( 'cat'=>'body','brands'=>array(),'icon'=>'🏍️','name'=>'Mudguard Front 12in','spec'=>'Universal · ABS','price'=>320,'moq'=>10,'badge'=>'s','sku'=>'MG12F' ),
		array( 'cat'=>'suspension','brands'=>array('local'),'icon'=>'🔧','name'=>'Front Fork Shock 12in','spec'=>'12in · 80mm Travel','price'=>780,'moq'=>2,'badge'=>'s','sku'=>'FFS12' ),
		array( 'cat'=>'suspension','brands'=>array(),'icon'=>'🔧','name'=>'Rear Mono Shock 300mm','spec'=>'300mm · Adjustable','price'=>650,'moq'=>2,'badge'=>'h','sku'=>'RMS30' ),
		array( 'cat'=>'suspension','brands'=>array(),'icon'=>'🔧','name'=>'Wheel Bearing 6202 Set','spec'=>'6202 · 2RS · 4pcs','price'=>180,'moq'=>20,'badge'=>'s','sku'=>'WB6202' ),
		array( 'cat'=>'switch','brands'=>array(),'icon'=>'💡','name'=>'Key Lock Set 6-Wire','spec'=>'6-Wire · 3-Position','price'=>220,'moq'=>10,'badge'=>'s','sku'=>'KLS6' ),
		array( 'cat'=>'switch','brands'=>array(),'icon'=>'💡','name'=>'LED Flasher Relay 12V','spec'=>'12V · 3-Pin · LED','price'=>85,'moq'=>25,'badge'=>'h','sku'=>'LFR12' ),
		array( 'cat'=>'switch','brands'=>array(),'icon'=>'💡','name'=>'MOSFET 75N75','spec'=>'75V · 75A · TO-247','price'=>65,'moq'=>30,'badge'=>'s','sku'=>'MOS75' ),
		array( 'cat'=>'switch','brands'=>array(),'icon'=>'💡','name'=>'Headlight LED 18W','spec'=>'12V · 18W · COB','price'=>250,'moq'=>10,'badge'=>'n','sku'=>'HLB18' ),
		array( 'cat'=>'erickshaw','brands'=>array('erickshaw'),'icon'=>'🛺','name'=>'E-Rickshaw Charger 60V','spec'=>'60V · 20A · Auto Cut','price'=>1400,'moq'=>3,'badge'=>'h','sku'=>'ERC-60' ),
		array( 'cat'=>'erickshaw','brands'=>array('erickshaw'),'icon'=>'🛺','name'=>'Rickshaw Brake Shoe','spec'=>'Universal · F/R','price'=>180,'moq'=>10,'badge'=>'s','sku'=>'RBS-U' ),
		array( 'cat'=>'ecycle','brands'=>array('ecycle'),'icon'=>'🚲','name'=>'E-Cycle Controller 36V','spec'=>'36V · 15A · 6MOS','price'=>680,'moq'=>5,'badge'=>'s','sku'=>'ECC36' ),
		array( 'cat'=>'ecycle','brands'=>array('ecycle'),'icon'=>'🚲','name'=>'PAS Pedal Assist Sensor','spec'=>'36V · 8-Magnet','price'=>320,'moq'=>10,'badge'=>'n','sku'=>'PAS8' ),
		array( 'cat'=>'conversion','brands'=>array(),'icon'=>'🧰','name'=>'EV Conversion Kit 48V','spec'=>'48V · 500W · Complete','price'=>8500,'moq'=>1,'badge'=>'h','sku'=>'CVKIT48' ),
		array( 'cat'=>'testing','brands'=>array(),'icon'=>'🧪','name'=>'Battery Capacity Tester','spec'=>'0-100V · 0-20A','price'=>2200,'moq'=>1,'badge'=>'n','sku'=>'BCT100' ),
		array( 'cat'=>'testing','brands'=>array(),'icon'=>'🧪','name'=>'Cell IR Meter YR1035+','spec'=>'4-Wire · 0-9999 mOhm','price'=>3500,'moq'=>1,'badge'=>'s','sku'=>'CIR35' ),
		array( 'cat'=>'clearance','brands'=>array(),'icon'=>'🏷️','name'=>'Mixed Cable Lot 50pcs','spec'=>'Assorted · Bulk','price'=>499,'moq'=>1,'badge'=>'d','sku'=>'CL-CAB' ),
		array( 'cat'=>'clearance','brands'=>array(),'icon'=>'🏷️','name'=>'Old Stock BMS 30A 36V','spec'=>'36V · 30A · Clearance','price'=>180,'moq'=>5,'badge'=>'d','sku'=>'CL-BMS' ),
	);
}
