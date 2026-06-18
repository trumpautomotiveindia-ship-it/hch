<?php
/**
 * ═══════════════════════════════════════════════════════════════
 *  HCH ELECTRIC — EDITABLE TEXT CONFIGURATION
 *
 *  Edit this file to change text on the website.
 *  Save the file and reload the site — changes appear instantly.
 *
 *  These values are fallbacks. If you also set text in the
 *  WordPress Customizer (Appearance → Customize → HCH Electric),
 *  the Customizer value takes priority.
 * ═══════════════════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── HERO HEADLINE (3 lines) ─────────────────────────────────── */
define( 'HCH_HERO_LINE1', 'Every EV part.' );        // White text
define( 'HCH_HERO_LINE2', 'Every spec.' );            // Cyan text
define( 'HCH_HERO_LINE3', 'One source.' );            // Green text

/* ── HERO SUBTITLE (centered below headline) ─────────────────── */
define( 'HCH_HERO_SUB', 'Electric Scooters' );

/* ── HERO STATS (4 numbers + labels) ────────────────────────── */
define( 'HCH_STAT1_NUM', '500+' );  define( 'HCH_STAT1_LBL', 'B2B PARTNERS' );
define( 'HCH_STAT2_NUM', '10M+' );  define( 'HCH_STAT2_LBL', 'PARTS SHIPPED' );
define( 'HCH_STAT3_NUM', '120+' );  define( 'HCH_STAT3_LBL', 'CITIES' );
define( 'HCH_STAT4_NUM', '24/7' );  define( 'HCH_STAT4_LBL', 'SUPPORT' );

/* ── TOP TICKER (scrolling bar — one item per line) ─────────── */
define( 'HCH_TICKER', implode( "\n", array(
	'ALL PARTS 18% GST',
	'PAN-INDIA DISPATCH',
	'SAME-DAY BEFORE 2PM',
	'GST INVOICE PROVIDED',
	'500+ B2B PARTNERS',
	'120+ CITIES',
	'LFP · NMC · PMSM · BLDC',
	'MADE IN INDIA',
) ) );

/* ── CONTACT ─────────────────────────────────────────────────── */
define( 'HCH_WHATSAPP', '919999999999' );       // country code + number, no +
define( 'HCH_EMAIL',    'hchevinternational@gmail.com' );

/* ── FOOTER ──────────────────────────────────────────────────── */
define( 'HCH_FOOTER_ABOUT', 'Aftermarket EV spare parts for Indian electric scooters.' );
define( 'HCH_FOOTER_COPY',  '© ' . date( 'Y' ) . ' HCH Electric. All rights reserved.' );
define( 'HCH_FOOTER_DISC',  'Not affiliated with Ola, Ather, Bajaj, TVS or Hero. Aftermarket parts only.' );
