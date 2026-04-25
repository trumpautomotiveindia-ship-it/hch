<?php
/**
 * SEO: meta description, canonical, Open Graph, Twitter Cards, JSON-LD structured data,
 * noindex for WooCommerce utility pages, robots.txt filter, and preconnect hints.
 *
 * @package HCH_Electric
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ──────────────────────────────────────────────────────────────────────────
 * 1. Preconnect hints for Google Fonts (speeds up LCP, good for Core Web Vitals)
 * ─────────────────────────────────────────────────────────────────────────── */
/* Force public indexing — production e-commerce store should always be crawlable */
add_filter( 'pre_option_blog_public', '__return_true' );

function hch_seo_preconnect() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	/* Sitemap discovery link for crawlers */
	echo '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'hch_seo_preconnect', 1 );

/* ──────────────────────────────────────────────────────────────────────────
 * 2. Meta description
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_meta_description() {
	$desc = '';

	if ( is_singular( 'product' ) ) {
		global $post;
		$desc = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );
		if ( ! $desc ) {
			$wc  = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
			$desc = $wc ? wp_strip_all_tags( $wc->get_short_description() ?: $wc->get_description() ) : '';
		}
	} elseif ( is_product_category() ) {
		$desc = wp_strip_all_tags( term_description() );
	} elseif ( is_front_page() ) {
		$sub = trim( (string) get_theme_mod( 'hch_hero_sub', '' ) );
		$desc = $sub ?: get_bloginfo( 'description' );
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$desc = sprintf( __( 'Search results for "%s" — HCH Electric', 'hch-electric' ), get_search_query() );
	} elseif ( is_singular() ) {
		$desc = get_the_excerpt();
	} else {
		$desc = get_bloginfo( 'description' );
	}

	$desc = wp_strip_all_tags( $desc );
	$desc = str_replace( array( "\r", "\n", "\t" ), ' ', $desc );
	$desc = mb_substr( trim( $desc ), 0, 160 );

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hch_seo_meta_description', 2 );

/* ──────────────────────────────────────────────────────────────────────────
 * 3. Canonical URL
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_canonical() {
	$canonical = '';

	if ( is_singular() ) {
		$canonical = get_permalink();
	} elseif ( is_front_page() ) {
		$canonical = home_url( '/' );
	} elseif ( is_home() ) {
		$page_id   = get_option( 'page_for_posts' );
		$canonical = $page_id ? get_permalink( $page_id ) : home_url( '/blog/' );
	} elseif ( is_product_category() ) {
		$canonical = get_term_link( get_queried_object() );
	} elseif ( is_search() ) {
		$canonical = get_search_link();
	} elseif ( is_post_type_archive( 'product' ) ) {
		$canonical = get_post_type_archive_link( 'product' );
	}

	if ( $canonical && ! is_wp_error( $canonical ) ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hch_seo_canonical', 3 );

/* ──────────────────────────────────────────────────────────────────────────
 * 4. noindex for WooCommerce private/utility pages and paginated duplicates
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_robots() {
	$noindex = false;

	if ( function_exists( 'is_cart' ) && is_cart() )         $noindex = true;
	if ( function_exists( 'is_checkout' ) && is_checkout() ) $noindex = true;
	if ( function_exists( 'is_account_page' ) && is_account_page() ) $noindex = true;
	if ( is_search() && '' === get_search_query() )           $noindex = true;
	if ( is_404() )                                           $noindex = true;

	if ( $noindex ) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}
}
add_action( 'wp_head', 'hch_seo_robots', 3 );

/* ──────────────────────────────────────────────────────────────────────────
 * 5. Open Graph + Twitter Card
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_social_tags() {
	$site_name = get_bloginfo( 'name' );
	$locale    = 'en_IN';

	/* Determine title */
	$title = wp_get_document_title();

	/* Determine description (reuse same logic as meta description) */
	$desc = '';
	if ( is_singular( 'product' ) ) {
		$wc   = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
		$desc = $wc ? wp_strip_all_tags( $wc->get_short_description() ?: $wc->get_description() ) : '';
	} elseif ( is_product_category() ) {
		$desc = wp_strip_all_tags( term_description() );
	} elseif ( is_front_page() ) {
		$sub  = trim( (string) get_theme_mod( 'hch_hero_sub', '' ) );
		$desc = $sub ?: get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$desc = get_the_excerpt();
	} else {
		$desc = get_bloginfo( 'description' );
	}
	$desc = mb_substr( wp_strip_all_tags( $desc ), 0, 200 );

	/* Determine URL */
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_product_category() ) {
		$url = get_term_link( get_queried_object() );
	} elseif ( is_front_page() ) {
		$url = home_url( '/' );
	} else {
		global $wp;
		$url = home_url( add_query_arg( array(), $wp->request ) );
	}
	if ( is_wp_error( $url ) ) $url = home_url( '/' );

	/* Determine image */
	$image = '';
	if ( is_singular( 'product' ) && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	} elseif ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	} elseif ( is_product_category() ) {
		$term_id  = get_queried_object_id();
		$thumb_id = get_term_meta( $term_id, 'thumbnail_id', true );
		if ( $thumb_id ) {
			$image = wp_get_attachment_image_url( (int) $thumb_id, 'large' );
		}
	}
	if ( ! $image ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		$image   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	}

	/* OG type */
	$og_type = is_singular( 'product' ) ? 'product' : ( is_singular() ? 'article' : 'website' );

	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
	}

	/* Product-specific OG */
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$wc = wc_get_product( get_the_ID() );
		if ( $wc ) {
			echo '<meta property="product:price:amount" content="' . esc_attr( $wc->get_price() ) . '">' . "\n";
			echo '<meta property="product:price:currency" content="' . esc_attr( get_woocommerce_currency() ) . '">' . "\n";
		}
	}

	/* Twitter Card */
	$card = $image ? 'summary_large_image' : 'summary';
	echo '<meta name="twitter:card" content="' . esc_attr( $card ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hch_seo_social_tags', 4 );

/* ──────────────────────────────────────────────────────────────────────────
 * 6. JSON-LD structured data
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_jsonld() {
	$graphs = array();

	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$logo_id   = get_theme_mod( 'custom_logo' );
	$logo_url  = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	$whatsapp  = preg_replace( '/\D/', '', get_theme_mod( 'hch_whatsapp', '' ) );
	$email     = get_theme_mod( 'hch_contact_email', 'hchevinternational@gmail.com' );

	/* ── WebSite (on every page — enables Sitelinks Searchbox) ── */
	$website = array(
		'@type'           => 'WebSite',
		'@id'             => $site_url . '#website',
		'url'             => $site_url,
		'name'            => $site_name,
		'description'     => get_bloginfo( 'description' ),
		'inLanguage'      => 'en-IN',
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $site_url . '?s={search_term_string}&post_type=product',
			),
			'query-input' => 'required name=search_term_string',
		),
	);
	$graphs[] = $website;

	/* ── Organization ── */
	$org = array(
		'@type'       => 'Organization',
		'@id'         => $site_url . '#organization',
		'name'        => $site_name,
		'url'         => $site_url,
		'description' => 'Aftermarket EV spare parts for Indian electric scooters, e-rickshaws & e-cycles.',
		'foundingDate' => '2021',
		'areaServed'  => array(
			array( '@type' => 'Country', 'name' => 'India' ),
		),
		'knowsAbout'  => array( 'Electric Vehicle Parts', 'EV Batteries', 'BLDC Motors', 'E-Rickshaw Parts', 'Electric Scooter Parts' ),
	);
	if ( $logo_url ) {
		$org['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
		$org['image'] = $logo_url;
	}
	if ( $email ) {
		$org['contactPoint'] = array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'email'             => $email,
			'availableLanguage' => array( 'English', 'Hindi' ),
		);
	}
	if ( $whatsapp ) {
		$org['sameAs'] = array( 'https://wa.me/' . $whatsapp );
	}
	$graphs[] = $org;

	/* ── Product page ── */
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$wc = wc_get_product( get_the_ID() );
		if ( $wc ) {
			$product_schema = array(
				'@type'       => 'Product',
				'@id'         => get_permalink() . '#product',
				'name'        => $wc->get_name(),
				'description' => wp_strip_all_tags( $wc->get_short_description() ?: $wc->get_description() ),
				'url'         => get_permalink(),
				'sku'         => $wc->get_sku() ?: (string) $wc->get_id(),
				'brand'       => array(
					'@type' => 'Brand',
					'name'  => $site_name,
				),
			);

			/* Images */
			$img_ids = $wc->get_gallery_image_ids();
			array_unshift( $img_ids, $wc->get_image_id() );
			$img_ids = array_filter( array_unique( $img_ids ) );
			$images  = array();
			foreach ( $img_ids as $img_id ) {
				$src = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
				if ( $src ) $images[] = $src;
			}
			if ( ! empty( $images ) ) {
				$product_schema['image'] = count( $images ) === 1 ? $images[0] : $images;
			}

			/* Offers */
			$avail = $wc->is_in_stock()
				? 'https://schema.org/InStock'
				: 'https://schema.org/OutOfStock';

			$offer = array(
				'@type'         => 'Offer',
				'url'           => get_permalink(),
				'priceCurrency' => get_woocommerce_currency(),
				'price'         => $wc->get_price() ?: '0',
				'availability'  => $avail,
				'seller'        => array( '@id' => $site_url . '#organization' ),
				'priceValidUntil' => gmdate( 'Y-12-31', strtotime( '+1 year' ) ),
				'hasMerchantReturnPolicy' => array(
					'@type'            => 'MerchantReturnPolicy',
					'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
				),
				'shippingDetails' => array(
					'@type'           => 'OfferShippingDetails',
					'shippingRate'    => array(
						'@type'    => 'MonetaryAmount',
						'currency' => get_woocommerce_currency(),
					),
					'shippingDestination' => array(
						'@type'          => 'DefinedRegion',
						'addressCountry' => 'IN',
					),
					'deliveryTime' => array(
						'@type'          => 'ShippingDeliveryTime',
						'handlingTime'   => array( '@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY' ),
						'transitTime'    => array( '@type' => 'QuantitativeValue', 'minValue' => 1, 'maxValue' => 5, 'unitCode' => 'DAY' ),
					),
				),
			);

			/* Price range for variable products */
			if ( $wc->is_type( 'variable' ) ) {
				$offer['@type']    = 'AggregateOffer';
				$offer['lowPrice'] = $wc->get_variation_price( 'min' );
				$offer['highPrice']= $wc->get_variation_price( 'max' );
				$offer['offerCount'] = count( $wc->get_children() );
				unset( $offer['price'] );
			}

			$product_schema['offers'] = $offer;

			/* Category */
			$cats = get_the_terms( get_the_ID(), 'product_cat' );
			if ( $cats && ! is_wp_error( $cats ) ) {
				$product_schema['category'] = implode( ', ', wp_list_pluck( $cats, 'name' ) );
			}

			$graphs[] = $product_schema;
		}
	}

	/* ── Product category / archive — ItemList ── */
	if ( ( is_product_category() || is_post_type_archive( 'product' ) ) && function_exists( 'wc_get_product' ) ) {
		global $wp_query;
		$items = array();
		$pos   = 1;
		if ( $wp_query->have_posts() ) {
			$posts = $wp_query->posts;
			foreach ( $posts as $p ) {
				$wc  = wc_get_product( $p->ID );
				$img = get_the_post_thumbnail_url( $p->ID, 'woocommerce_thumbnail' );
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'item'     => array(
						'@type'  => 'Product',
						'name'   => $p->post_title,
						'url'    => get_permalink( $p->ID ),
						'image'  => $img ?: '',
						'offers' => array(
							'@type'         => 'Offer',
							'price'         => $wc ? $wc->get_price() : '',
							'priceCurrency' => get_woocommerce_currency(),
							'availability'  => ( $wc && $wc->is_in_stock() ) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
						),
					),
				);
			}
		}
		if ( ! empty( $items ) ) {
			$graphs[] = array(
				'@type'           => 'ItemList',
				'name'            => is_product_category() ? single_term_title( '', false ) : __( 'All Products', 'hch-electric' ),
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			);
		}
	}

	/* ── BreadcrumbList ── */
	$crumbs = hch_seo_breadcrumbs_data();
	if ( ! empty( $crumbs ) ) {
		$list_items = array();
		foreach ( $crumbs as $i => $crumb ) {
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb['name'],
				'item'     => $crumb['url'],
			);
		}
		$graphs[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list_items,
		);
	}

	/* ── LocalBusiness on front page ── */
	if ( is_front_page() ) {
		$graphs[] = array(
			'@type'         => 'LocalBusiness',
			'@id'           => $site_url . '#localbusiness',
			'name'          => $site_name,
			'url'           => $site_url,
			'email'         => $email,
			'description'   => 'Wholesale & retail aftermarket EV parts supplier in India.',
			'currenciesAccepted' => 'INR',
			'paymentAccepted'    => 'Cash, Credit Card, UPI, Bank Transfer',
			'openingHoursSpecification' => array(
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
					'opens'     => '09:00',
					'closes'    => '18:00',
				),
			),
			'areaServed' => array(
				'@type' => 'Country',
				'name'  => 'India',
			),
		);
	}

	if ( empty( $graphs ) ) return;

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graphs,
	);

	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n" . '</script>' . "\n";
}
add_action( 'wp_head', 'hch_seo_jsonld', 5 );

/* ──────────────────────────────────────────────────────────────────────────
 * Helper: build breadcrumb data array
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_breadcrumbs_data() {
	$crumbs   = array();
	$home_url = home_url( '/' );

	$crumbs[] = array( 'name' => __( 'Home', 'hch-electric' ), 'url' => $home_url );

	if ( is_singular( 'product' ) ) {
		$cats = get_the_terms( get_the_ID(), 'product_cat' );
		if ( $cats && ! is_wp_error( $cats ) ) {
			$cat = $cats[0];
			if ( $cat->parent ) {
				$parent = get_term( $cat->parent, 'product_cat' );
				if ( $parent && ! is_wp_error( $parent ) ) {
					$crumbs[] = array( 'name' => $parent->name, 'url' => get_term_link( $parent ) );
				}
			}
			$crumbs[] = array( 'name' => $cat->name, 'url' => get_term_link( $cat ) );
		} elseif ( function_exists( 'wc_get_page_permalink' ) ) {
			$shop_url = wc_get_page_permalink( 'shop' );
			$crumbs[] = array( 'name' => __( 'Shop', 'hch-electric' ), 'url' => $shop_url );
		}
		$crumbs[] = array( 'name' => get_the_title(), 'url' => get_permalink() );

	} elseif ( is_product_category() ) {
		$term = get_queried_object();
		if ( $term->parent ) {
			$parent = get_term( $term->parent, 'product_cat' );
			if ( $parent && ! is_wp_error( $parent ) ) {
				$crumbs[] = array( 'name' => $parent->name, 'url' => get_term_link( $parent ) );
			}
		}
		$crumbs[] = array( 'name' => $term->name, 'url' => get_term_link( $term ) );

	} elseif ( is_post_type_archive( 'product' ) ) {
		$crumbs[] = array( 'name' => __( 'Shop', 'hch-electric' ), 'url' => get_post_type_archive_link( 'product' ) );

	} elseif ( is_singular() ) {
		if ( is_page() ) {
			$crumbs[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
		} else {
			$crumbs[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
		}

	} elseif ( is_search() ) {
		/* translators: %s: search term */
		$crumbs[] = array( 'name' => sprintf( __( 'Search: %s', 'hch-electric' ), get_search_query() ), 'url' => get_search_link() );
	}

	return count( $crumbs ) > 1 ? $crumbs : array();
}

/* ──────────────────────────────────────────────────────────────────────────
 * 7. Improve the auto-generated robots.txt
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_robots_txt( $output ) {
	/* Remove "Disallow: /" that WordPress adds when "Discourage search engines" is on */
	$output = preg_replace( '/^Disallow:\s+\/\s*$/m', '', $output );

	/* Ensure explicit Allow: / so crawlers know the site is public */
	if ( false === strpos( $output, 'Allow: /' ) ) {
		$output = "User-agent: *\nAllow: /\n\n" . ltrim( $output );
	}

	/* Add sitemap URL */
	$sitemap = home_url( '/wp-sitemap.xml' );
	if ( false === strpos( $output, 'Sitemap:' ) ) {
		$output .= "\nSitemap: " . esc_url( $sitemap ) . "\n";
	}

	/* Block WooCommerce utility URLs from crawlers */
	$disallow = array(
		'/wp-admin/',
		'/cart/',
		'/checkout/',
		'/my-account/',
		'/?add-to-cart=',
		'/wp-json/',
		'/wp-login.php',
		'/wp-cron.php',
	);
	foreach ( $disallow as $path ) {
		if ( false === strpos( $output, 'Disallow: ' . $path ) ) {
			$output .= 'Disallow: ' . $path . "\n";
		}
	}
	return $output;
}
add_filter( 'robots_txt', 'hch_seo_robots_txt', 10 );

/* ──────────────────────────────────────────────────────────────────────────
 * 8. Ensure WordPress XML sitemaps are enabled
 * ─────────────────────────────────────────────────────────────────────────── */
add_filter( 'wp_sitemaps_enabled', '__return_true' );

/* Ensure WooCommerce products are in the sitemap */
add_filter( 'wp_sitemaps_post_types', function ( $post_types ) {
	if ( post_type_exists( 'product' ) && ! isset( $post_types['product'] ) ) {
		$post_types['product'] = get_post_type_object( 'product' );
	}
	return $post_types;
} );

/* Add WooCommerce product categories to the sitemap */
function hch_seo_sitemap_taxonomies( $taxonomies ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$taxonomies['product_cat'] = get_taxonomy( 'product_cat' );
	}
	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'hch_seo_sitemap_taxonomies' );

/* Exclude private/draft products from sitemap */
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'product' === $post_type ) {
		$args['post_status'] = 'publish';
	}
	return $args;
}, 10, 2 );

/* ──────────────────────────────────────────────────────────────────────────
 * 9. SEO-friendly <title> suffix via wp_title filter (pre-5.9 fallback)
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_seo_document_title( $title ) {
	if ( is_product_category() ) {
		$term  = get_queried_object();
		$title = array(
			'title'   => $term->name . ' EV Parts',
			'tagline' => get_bloginfo( 'name' ),
		);
	}
	return $title;
}
add_filter( 'document_title_parts', 'hch_seo_document_title' );

/* ──────────────────────────────────────────────────────────────────────────
 * 10. Render HTML breadcrumb trail (for display in templates via shortcode or block)
 * ─────────────────────────────────────────────────────────────────────────── */
function hch_breadcrumbs_html() {
	$crumbs = hch_seo_breadcrumbs_data();
	if ( empty( $crumbs ) ) return '';

	ob_start();
	echo '<nav class="hch-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'hch-electric' ) . '">';
	echo '<ol class="hch-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';
	foreach ( $crumbs as $i => $crumb ) {
		$is_last = ( $i === count( $crumbs ) - 1 );
		echo '<li class="hch-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		if ( $is_last ) {
			echo '<span itemprop="name">' . esc_html( $crumb['name'] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $crumb['url'] ) . '" itemprop="item"><span itemprop="name">' . esc_html( $crumb['name'] ) . '</span></a>';
			echo '<span class="hch-breadcrumbs__sep" aria-hidden="true"> / </span>';
		}
		echo '<meta itemprop="position" content="' . ( $i + 1 ) . '">';
		echo '</li>';
	}
	echo '</ol></nav>';
	return ob_get_clean();
}
add_shortcode( 'hch_breadcrumbs', 'hch_breadcrumbs_html' );
