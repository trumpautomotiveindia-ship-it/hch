<?php
/**
 * Register Gutenberg block patterns. Every section of the homepage (and many
 * bonus marketing sections) is available as a one-click pattern from the
 * Block Editor's Pattern inserter under the "HCH Electric" category.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hch_register_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) return;

	register_block_pattern_category( 'hch-electric', array(
		'label' => __( 'HCH Electric', 'hch-electric' ),
	) );

	/* ── 1. Ticker ── */
	register_block_pattern( 'hch/ticker', array(
		'title'       => __( 'HCH Ticker', 'hch-electric' ),
		'description' => __( 'Cyan announcement ticker (edit lines in Customizer).', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:shortcode -->[hch_ticker]<!-- /wp:shortcode -->',
	) );

	/* ── 2. Hero ── */
	register_block_pattern( 'hch/hero', array(
		'title'       => __( 'HCH Hero', 'hch-electric' ),
		'description' => __( 'Kicker + bold headline + subtext + stats row.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-hero">
<div class="hch-hero__kicker">B2B SPECIALISTS · PAN-INDIA DISPATCH</div>
<h1 class="hch-hero__h1">Every EV part.<br/><span class="c">Every spec.</span><br/><span class="g">One source.</span></h1>
<p class="hch-hero__sub">Premium aftermarket spare parts for Indian electric scooters, e-rickshaws &amp; e-cycles.</p>
<div class="hch-hero__stats">
<div class="hch-hero__stat"><div class="hch-hero__stat-num">500<span>+</span></div><div class="hch-hero__stat-label">B2B PARTNERS</div></div>
<div class="hch-hero__stat"><div class="hch-hero__stat-num">10M<span>+</span></div><div class="hch-hero__stat-label">PARTS SHIPPED</div></div>
<div class="hch-hero__stat"><div class="hch-hero__stat-num">120<span>+</span></div><div class="hch-hero__stat-label">CITIES</div></div>
<div class="hch-hero__stat"><div class="hch-hero__stat-num">24/7</div><div class="hch-hero__stat-label">SUPPORT</div></div>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 3. Brand filter ── */
	register_block_pattern( 'hch/brand-filter', array(
		'title'       => __( 'HCH Brand Filter', 'hch-electric' ),
		'description' => __( 'Horizontal brand attribute chips.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:hch/brand-filter /-->',
	) );

	/* ── 4. Category bar ── */
	register_block_pattern( 'hch/category-bar', array(
		'title'       => __( 'HCH Category Bar', 'hch-electric' ),
		'description' => __( 'Sticky category navigation.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:hch/category-bar /-->',
	) );

	/* ── 5. Product grid ── */
	register_block_pattern( 'hch/products', array(
		'title'       => __( 'HCH Product Grid', 'hch-electric' ),
		'description' => __( 'Dense 6-column WooCommerce product grid.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:shortcode -->[products columns="6" limit="30"]<!-- /wp:shortcode -->',
	) );

	/* ── 6. Feature grid (benefits) ── */
	register_block_pattern( 'hch/feature-grid', array(
		'title'       => __( 'HCH Feature Grid (3 benefits)', 'hch-electric' ),
		'description' => __( 'Three-column dark cards — icon + title + description.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-features">
<div class="hch-features__inner">
<div class="hch-feature">
<div class="hch-feature__ico">⚡</div>
<h3 class="hch-feature__t">Same-day dispatch</h3>
<p class="hch-feature__d">Order before 2PM, ships PAN-India the same afternoon with tracking.</p>
</div>
<div class="hch-feature">
<div class="hch-feature__ico">🏆</div>
<h3 class="hch-feature__t">Grade-A components</h3>
<p class="hch-feature__d">Only tier-1 manufacturer SKUs. Each batch serial-logged and QC tested.</p>
</div>
<div class="hch-feature">
<div class="hch-feature__ico">🧾</div>
<h3 class="hch-feature__t">GST invoice always</h3>
<p class="hch-feature__d">Proper tax invoice for every order so you stay B2B compliant.</p>
</div>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 7. CTA banner ── */
	register_block_pattern( 'hch/cta', array(
		'title'       => __( 'HCH CTA Banner', 'hch-electric' ),
		'description' => __( 'Bold dark CTA banner with WhatsApp + shop buttons.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-cta">
<div class="hch-cta__inner">
<div class="hch-cta__kicker">READY TO ORDER</div>
<h2 class="hch-cta__h">Bulk pricing for workshops, dealers &amp; fleets.</h2>
<p class="hch-cta__sub">Get a custom quote within 24 hours. 500+ businesses already trust us.</p>
<div class="hch-cta__btns">
<a class="hch-cta__btn hch-cta__btn--wa" href="https://wa.me/919999999999">Chat on WhatsApp</a>
<a class="hch-cta__btn hch-cta__btn--sec" href="/shop/">Browse Catalog</a>
</div>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 8. Testimonials row ── */
	register_block_pattern( 'hch/testimonials', array(
		'title'       => __( 'HCH Testimonials', 'hch-electric' ),
		'description' => __( 'Three customer quote cards.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-testis">
<div class="hch-testis__inner">
<div class="hch-testis__head">
<div class="hch-shop__title">WORKSHOPS &amp; DEALERS</div>
<h2 class="hch-testis__h">What B2B partners say</h2>
</div>
<div class="hch-testis__grid">
<figure class="hch-testi">
<blockquote class="hch-testi__q">Same-day dispatch literally saved our service queue. Chargers arrived in Delhi the next morning.</blockquote>
<figcaption class="hch-testi__c"><strong>Ravi S.</strong><span>EV Service Hub · Delhi NCR</span></figcaption>
</figure>
<figure class="hch-testi">
<blockquote class="hch-testi__q">Best margins on BMS units I have seen. Quality is consistent across batches — that matters for us.</blockquote>
<figcaption class="hch-testi__c"><strong>Priya M.</strong><span>GreenWheels Workshop · Pune</span></figcaption>
</figure>
<figure class="hch-testi">
<blockquote class="hch-testi__q">WhatsApp ordering is a huge time saver. GST invoice comes in minutes.</blockquote>
<figcaption class="hch-testi__c"><strong>Arjun K.</strong><span>EcoRide Spares · Bengaluru</span></figcaption>
</figure>
</div>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 9. Trust bar (partner logos placeholders) ── */
	register_block_pattern( 'hch/trust-bar', array(
		'title'       => __( 'HCH Trust Bar', 'hch-electric' ),
		'description' => __( 'Greyscale logo/brand strip.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-trust">
<div class="hch-trust__inner">
<span class="hch-trust__lbl">SUPPORTED MODELS</span>
<span class="hch-trust__i">OLA S1</span>
<span class="hch-trust__i">Ather 450</span>
<span class="hch-trust__i">Bajaj Chetak</span>
<span class="hch-trust__i">TVS iQube</span>
<span class="hch-trust__i">Hero Vida</span>
<span class="hch-trust__i">Ampere</span>
<span class="hch-trust__i">Okinawa</span>
<span class="hch-trust__i">Revolt</span>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 10. Contact block ── */
	register_block_pattern( 'hch/contact', array(
		'title'       => __( 'HCH Contact Card', 'hch-electric' ),
		'description' => __( 'WhatsApp + email + address contact block.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-contact">
<div class="hch-contact__inner">
<div class="hch-contact__head">
<div class="hch-shop__title">GET IN TOUCH</div>
<h2 class="hch-contact__h">Questions? Bulk order?</h2>
<p class="hch-contact__sub">We respond within 2 working hours on WhatsApp. GST invoices, custom SKUs, dealer pricing — all handled.</p>
</div>
<div class="hch-contact__grid">
<a class="hch-contact__card" href="https://wa.me/919999999999">
<div class="hch-contact__ico">💬</div>
<div class="hch-contact__lbl">WHATSAPP</div>
<div class="hch-contact__val">+91 99999 99999</div>
</a>
<a class="hch-contact__card" href="mailto:hchevinternational@gmail.com">
<div class="hch-contact__ico">✉️</div>
<div class="hch-contact__lbl">EMAIL</div>
<div class="hch-contact__val">hchevinternational@gmail.com</div>
</a>
<div class="hch-contact__card">
<div class="hch-contact__ico">📍</div>
<div class="hch-contact__lbl">DISPATCH HUB</div>
<div class="hch-contact__val">Delhi NCR · PAN-India courier</div>
</div>
</div>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 11. FAQ ── */
	register_block_pattern( 'hch/faq', array(
		'title'       => __( 'HCH FAQ', 'hch-electric' ),
		'description' => __( 'Expandable FAQ list using native details/summary.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-faq">
<div class="hch-faq__inner">
<div class="hch-shop__title">FAQ</div>
<h2 class="hch-faq__h">Common questions</h2>
<details class="hch-faq__i"><summary>Do you ship PAN India?</summary><p>Yes. Same-day dispatch before 2PM. Typically 2–4 working days across India.</p></details>
<details class="hch-faq__i"><summary>Do you offer GST invoice?</summary><p>Every order ships with a proper GST tax invoice. 18% GST on parts, 5% on chargers.</p></details>
<details class="hch-faq__i"><summary>What is the minimum order quantity?</summary><p>Each SKU has its own MOQ shown on the product card (typically 1–50). Bulk discounts apply over MOQ × 5.</p></details>
<details class="hch-faq__i"><summary>Do you have genuine OEM parts?</summary><p>We supply premium aftermarket parts only. We are not an authorised OEM dealer.</p></details>
<details class="hch-faq__i"><summary>How do I place a large order?</summary><p>Add to cart and click "Order via WhatsApp" — our team will confirm and send a pro-forma invoice within 2 hours.</p></details>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 12. Newsletter ── */
	register_block_pattern( 'hch/newsletter', array(
		'title'       => __( 'HCH Newsletter CTA', 'hch-electric' ),
		'description' => __( 'Simple dark newsletter signup row.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<section class="hch-newsletter">
<div class="hch-newsletter__inner">
<div>
<div class="hch-shop__title">STAY UPDATED</div>
<h3 class="hch-newsletter__h">New SKUs + deals every week</h3>
</div>
<form class="hch-newsletter__form" onsubmit="return false;">
<input type="email" placeholder="you@workshop.com" required>
<button type="submit">Subscribe</button>
</form>
</div>
</section>
<!-- /wp:html -->',
	) );

	/* ── 13. Category pills row (visual only) ── */
	register_block_pattern( 'hch/category-pills', array(
		'title'       => __( 'HCH Category Pills', 'hch-electric' ),
		'description' => __( 'Static visual pills — for use in footers, CTAs.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:html -->
<div class="hch-pills">
<span class="hch-pill">🔋 Battery</span>
<span class="hch-pill">⚡ Chargers</span>
<span class="hch-pill">⚙️ Controllers</span>
<span class="hch-pill">🔩 Motors</span>
<span class="hch-pill">🎛️ Throttles</span>
<span class="hch-pill">🔌 Cables</span>
<span class="hch-pill">🛺 E-Rickshaw</span>
<span class="hch-pill">🚲 E-Cycle</span>
</div>
<!-- /wp:html -->',
	) );

	/* ── 14. Complete homepage (all-in-one) ── */
	register_block_pattern( 'hch/homepage', array(
		'title'       => __( 'HCH Full Homepage', 'hch-electric' ),
		'description' => __( 'One-click full homepage: hero → brand filter → category bar → category tile grid → per-category product sections.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:hch/brand-filter /-->
<!-- wp:hch/category-bar /-->
<!-- wp:hch/category-grid {"columns":4,"showCount":true,"heading":"SHOP BY CATEGORY"} /-->
<!-- wp:hch/category-sections {"perSection":12,"showViewAll":true,"hideEmpty":true} /-->',
	) );

	/* ── 15. Category tile grid (competitor-style) ── */
	register_block_pattern( 'hch/category-grid', array(
		'title'       => __( 'HCH Category Grid (tiles)', 'hch-electric' ),
		'description' => __( 'Large clickable image tiles for all product categories.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:hch/category-grid {"columns":4,"showCount":true,"heading":"SHOP BY CATEGORY"} /-->',
	) );

	/* ── 16. Category sections (products per category) ── */
	register_block_pattern( 'hch/category-sections', array(
		'title'       => __( 'HCH Category Sections', 'hch-electric' ),
		'description' => __( 'One product grid per category with a clickable "View all" header.', 'hch-electric' ),
		'categories'  => array( 'hch-electric' ),
		'content'     => '<!-- wp:hch/category-sections {"perSection":12,"showViewAll":true,"hideEmpty":true} /-->',
	) );
}
add_action( 'init', 'hch_register_patterns' );
