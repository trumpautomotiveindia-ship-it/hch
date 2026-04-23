<?php
/**
 * Customizer settings — all homepage text is editable here, plus WhatsApp number.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hch_customize_register( $wp_customize ) {

	/* ─── PANEL: HCH Electric ─── */
	$wp_customize->add_panel( 'hch_panel', array(
		'title'    => __( 'HCH Electric', 'hch-electric' ),
		'priority' => 30,
	) );

	/* Section 1: Ticker */
	$wp_customize->add_section( 'hch_ticker', array(
		'title'    => __( 'Top Ticker', 'hch-electric' ),
		'panel'    => 'hch_panel',
	) );
	$wp_customize->add_setting( 'hch_ticker', array(
		'default'           => "ALL PARTS 18% GST\nPAN-INDIA DISPATCH\nSAME-DAY BEFORE 2PM\nGST INVOICE PROVIDED\n500+ B2B PARTNERS\n120+ CITIES\nLFP · NMC · PMSM · BLDC\nMADE IN INDIA",
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'hch_ticker', array(
		'label'       => __( 'Ticker lines (one per line)', 'hch-electric' ),
		'section'     => 'hch_ticker',
		'type'        => 'textarea',
		'description' => __( 'Each line becomes a scrolling item.', 'hch-electric' ),
	) );

	/* Section 2: Hero */
	$wp_customize->add_section( 'hch_hero', array(
		'title' => __( 'Hero Section', 'hch-electric' ),
		'panel' => 'hch_panel',
	) );
	$hero_fields = array(
		'hch_hero_kicker' => array( __( 'Kicker text', 'hch-electric' ), 'B2B SPECIALISTS · PAN-INDIA DISPATCH' ),
		'hch_hero_line1'  => array( __( 'Headline line 1', 'hch-electric' ), 'Every EV part.' ),
		'hch_hero_line2'  => array( __( 'Headline line 2 (cyan)', 'hch-electric' ), 'Every spec.' ),
		'hch_hero_line3'  => array( __( 'Headline line 3 (green)', 'hch-electric' ), 'One source.' ),
		'hch_hero_sub'    => array( __( 'Sub-text', 'hch-electric' ), 'Premium aftermarket spare parts for Indian electric scooters, e-rickshaws & e-cycles.' ),
	);
	foreach ( $hero_fields as $id => $meta ) {
		$wp_customize->add_setting( $id, array( 'default' => $meta[1], 'sanitize_callback' => 'wp_kses_post' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $meta[0],
			'section' => 'hch_hero',
			'type'    => 0 === strpos( $id, 'hch_hero_sub' ) ? 'textarea' : 'text',
		) );
	}

	/* Section 3: Stats */
	$wp_customize->add_section( 'hch_stats', array(
		'title' => __( 'Hero Stats', 'hch-electric' ),
		'panel' => 'hch_panel',
	) );
	$stats = array(
		1 => array( '500+', 'B2B PARTNERS' ),
		2 => array( '10M+', 'PARTS SHIPPED' ),
		3 => array( '120+', 'CITIES' ),
		4 => array( '24/7', 'SUPPORT' ),
	);
	foreach ( $stats as $i => $defaults ) {
		$wp_customize->add_setting( "hch_stat{$i}_num", array( 'default' => $defaults[0], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "hch_stat{$i}_num", array( 'label' => sprintf( __( 'Stat %d — Number', 'hch-electric' ), $i ), 'section' => 'hch_stats', 'type' => 'text' ) );
		$wp_customize->add_setting( "hch_stat{$i}_lbl", array( 'default' => $defaults[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "hch_stat{$i}_lbl", array( 'label' => sprintf( __( 'Stat %d — Label', 'hch-electric' ), $i ), 'section' => 'hch_stats', 'type' => 'text' ) );
	}

	/* Section 4: Contact & WhatsApp */
	$wp_customize->add_section( 'hch_contact', array(
		'title' => __( 'Contact & WhatsApp', 'hch-electric' ),
		'panel' => 'hch_panel',
	) );
	$wp_customize->add_setting( 'hch_whatsapp', array( 'default' => '919999999999', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'hch_whatsapp', array(
		'label'       => __( 'WhatsApp number (country code, no +)', 'hch-electric' ),
		'description' => __( 'Example: 919876543210', 'hch-electric' ),
		'section'     => 'hch_contact',
		'type'        => 'text',
	) );
	$wp_customize->add_setting( 'hch_contact_email', array( 'default' => 'hchevinternational@gmail.com', 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'hch_contact_email', array( 'label' => __( 'Contact email', 'hch-electric' ), 'section' => 'hch_contact', 'type' => 'email' ) );

	/* Section 5: Footer */
	$wp_customize->add_section( 'hch_footer', array(
		'title' => __( 'Footer', 'hch-electric' ),
		'panel' => 'hch_panel',
	) );
	$wp_customize->add_setting( 'hch_footer_about', array( 'default' => 'Aftermarket EV spare parts for Indian electric scooters, e-rickshaws & e-cycles.', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'hch_footer_about', array( 'label' => __( 'Brand description', 'hch-electric' ), 'section' => 'hch_footer', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'hch_footer_copy', array( 'default' => sprintf( '© %s HCH Electric. All rights reserved.', date( 'Y' ) ), 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'hch_footer_copy', array( 'label' => __( 'Copyright line', 'hch-electric' ), 'section' => 'hch_footer', 'type' => 'text' ) );
	$wp_customize->add_setting( 'hch_footer_disc', array( 'default' => 'Not affiliated with Ola, Ather, Bajaj, TVS or Hero. Aftermarket parts only.', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'hch_footer_disc', array( 'label' => __( 'Disclaimer', 'hch-electric' ), 'section' => 'hch_footer', 'type' => 'textarea' ) );
}
add_action( 'customize_register', 'hch_customize_register' );
