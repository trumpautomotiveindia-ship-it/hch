/* HCH Electric — cart drawer + filters + AJAX.
 * All server endpoints are registered via wp_ajax_ actions in inc/woocommerce.php.
 */
(function ($) {
	'use strict';
	if (typeof HCH === 'undefined') return;

	const $overlay  = $('#hchOverlay');
	const $drawer   = $('#hchDrawer');
	const $items    = $('#hchDrawerItems');
	const $total    = $('#hchDrawerTotal');
	const $count    = $('#hchCartCount');
	const $waBtn    = $('#hchWaBtn');

	let currentWaUrl = '';

	function openCart()  { $overlay.addClass('open'); $drawer.addClass('open'); document.body.style.overflow = 'hidden'; refreshCart(); }
	function closeCart() { $overlay.removeClass('open'); $drawer.removeClass('open'); document.body.style.overflow = ''; }
	/* Position search overlay exactly at the header's bottom edge */
	function positionSearchOverlay() {
		var header = document.querySelector('.hch-header');
		var overlay = document.getElementById('hchSearchOverlay');
		if (header && overlay) {
			overlay.style.top = header.getBoundingClientRect().bottom + 'px';
		}
	}

	function closeSearch() {
		$('#hchSearchOverlay').removeClass('open');
		document.body.style.overflow = '';
	}

	$(document).on('click', '#hchCartBtn',   function (e) { e.preventDefault(); openCart(); });
	$(document).on('click', '#hchCartClose', closeCart);
	$(document).on('click', '#hchOverlay',   closeCart);
	$(document).on('keydown', function (e) {
		if (e.key === 'Escape') { closeCart(); closeSearch(); }
	});

	/* Search overlay toggle */
	$(document).on('click', '#hchSearchToggle', function () {
		const $so = $('#hchSearchOverlay');
		const opening = !$so.hasClass('open');
		positionSearchOverlay();
		$so.toggleClass('open');
		if (opening) {
			document.body.style.overflow = 'hidden';
			setTimeout(function () { $so.find('.hch-search-overlay__input').trigger('focus'); }, 50);
		} else {
			document.body.style.overflow = '';
		}
	});
	$(document).on('click', '#hchSearchClose', closeSearch);

	/* Reposition when viewport changes (mobile URL bar appearing/disappearing) */
	$(window).on('scroll.hchSearch resize.hchSearch', function () {
		if ($('#hchSearchOverlay').hasClass('open')) {
			positionSearchOverlay();
		}
	});

	$waBtn.on('click', function (e) {
		e.preventDefault();
		if (!currentWaUrl) return;
		window.open(currentWaUrl, '_blank');
	});

	/* Add to cart (grid + single + quick-add) */
	$(document).on('click', '.hch-add-to-cart', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const $btn = $(this);
		if ($btn.hasClass('loading')) return;
		const pid = $btn.data('product-id');
		const qty = $btn.data('qty') || 1;
		$btn.addClass('loading');

		$.post(HCH.ajaxUrl, {
			action: 'hch_add_to_cart',
			nonce: HCH.nonce,
			product_id: pid,
			quantity: qty
		}).done(function (res) {
			if (res && res.success) {
				applyPayload(res.data);
				$btn.removeClass('loading').addClass('added');
				setTimeout(function () { $btn.removeClass('added'); }, 600);
				openCart();
			} else {
				$btn.removeClass('loading');
			}
		}).fail(function () { $btn.removeClass('loading'); });
	});

	/* Prevent the wrapping <a> from navigating when clicking quick-add buttons. */
	$(document).on('click', '.hch-product .hch-add-to-cart', function (e) {
		e.preventDefault();
	});

	/* Qty +/- and remove */
	$(document).on('click', '.hch-ci__qty-inc, .hch-ci__qty-dec, .hch-ci__rm', function () {
		const $btn   = $(this);
		const $ci    = $btn.closest('.hch-ci');
		const key    = $ci.data('key');
		const $input = $ci.find('.qty__input');
		const cur    = parseInt($input.val(), 10) || 1;
		const moq    = parseInt($ci.data('moq'), 10) || 1;
		let qty = cur;
		if ($btn.hasClass('hch-ci__qty-inc')) qty = cur + 1;
		else if ($btn.hasClass('hch-ci__qty-dec')) qty = Math.max(moq, cur - 1);
		else qty = 0;

		$.post(HCH.ajaxUrl, { action: 'hch_update_cart', nonce: HCH.nonce, cart_key: key, quantity: qty })
			.done(function (res) { if (res && res.success) applyPayload(res.data); });
	});

	/* Direct quantity input — type a number in the cart drawer */
	$(document).on('change blur', '.qty__input', function () {
		const $input = $(this);
		const $ci    = $input.closest('.hch-ci');
		const key    = $ci.data('key');
		const moq    = parseInt($ci.data('moq'), 10) || 1;
		let qty      = parseInt($input.val(), 10);
		if (isNaN(qty) || qty < moq) qty = moq;
		$input.val(qty);
		$.post(HCH.ajaxUrl, { action: 'hch_update_cart', nonce: HCH.nonce, cart_key: key, quantity: qty })
			.done(function (res) { if (res && res.success) applyPayload(res.data); });
	});

	function refreshCart() {
		$.post(HCH.ajaxUrl, { action: 'hch_cart_fragments', nonce: HCH.nonce })
			.done(function (res) { if (res && res.success) applyPayload(res.data); });
	}

	function applyPayload(data) {
		if (!data) return;
		$count.text(data.count || 0);
		$total.html(data.total_html || '');
		currentWaUrl = data.wa_url || '';
		if (!data.items || !data.items.length) {
			$items.html('<div class="hch-empty"><span class="hch-empty__ico">🛒</span>' + HCH.i18n.empty + '</div>');
			return;
		}
		const rows = data.items.map(function (it) {
			const iconHtml = it.thumb
				? '<img src="' + it.thumb + '" alt="" loading="lazy"/>'
				: (it.icon ? it.icon : '•');
			const spec = [it.spec, it.sku].filter(Boolean).join(' · ');
			return ''
				+ '<div class="hch-ci" data-key="' + it.key + '" data-moq="' + it.moq + '">'
				+ '<div class="hch-ci__ico">' + iconHtml + '</div>'
				+ '<div class="hch-ci__info">'
				+   '<div class="hch-ci__name">' + escapeHtml(it.name) + '</div>'
				+   '<div class="hch-ci__spec">' + escapeHtml(spec) + '</div>'
				+   '<div class="hch-ci__row">'
				+     '<div class="qty">'
				+       '<button type="button" class="qty__btn hch-ci__qty-dec" aria-label="Decrease">−</button>'
				+       '<input type="number" class="qty__val qty__input" value="' + it.qty + '" min="' + it.moq + '" step="1" aria-label="Quantity"/>'
				+       '<button type="button" class="qty__btn hch-ci__qty-inc" aria-label="Increase">+</button>'
				+     '</div>'
				+     '<div class="hch-ci__price">' + it.line_total + '</div>'
				+     '<button type="button" class="hch-ci__rm" aria-label="Remove">✕</button>'
				+   '</div>'
				+ '</div>'
				+ '</div>';
		}).join('');
		$items.html(rows);
	}

	function escapeHtml(s) {
		return String(s || '').replace(/[&<>"']/g, function (c) {
			return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
		});
	}

	/* ── Filter chips on homepage (client-side product hide/show) ── */
	$(document).on('click', '.hch-brand-chip', function () {
		const $c = $(this);
		const brand = $c.data('brand') || 'all';
		$('.hch-brand-chip').removeClass('active');
		$c.addClass('active');
		applyFilters();
	});

	function applyFilters() {
		const activeBrand = $('.hch-brand-chip.active').data('brand') || 'all';
		let shown = 0;
		$('#hchProdGrid > li.hch-product').each(function () {
			const brands = (this.getAttribute('data-brand') || '').split(/\s+/);
			const ok = activeBrand === 'all' || brands.indexOf(activeBrand) !== -1;
			this.style.display = ok ? '' : 'none';
			if (ok) shown++;
		});
		$('#hchShopCount').text(shown + ' item' + (shown !== 1 ? 's' : ''));
	}

	/* ── Rail scroll arrows (brand + category rails) ── */
	function initRailArrows() {
		document.querySelectorAll('.hch-brands, .hch-catbar-wrap').forEach(function (wrap) {
			var scroller = wrap.querySelector('[data-scroll-container]');
			if (!scroller) return;
			var left  = wrap.querySelector('.hch-rail-arrow--l');
			var right = wrap.querySelector('.hch-rail-arrow--r');
			if (!left || !right) return;

			function update() {
				var max = scroller.scrollWidth - scroller.clientWidth;
				if (max <= 2) {
					left.classList.remove('visible');
					right.classList.remove('visible');
					return;
				}
				left.classList.toggle('visible', scroller.scrollLeft > 4);
				right.classList.toggle('visible', scroller.scrollLeft < max - 4);
			}
			function scrollBy(delta) {
				scroller.scrollBy({ left: delta, behavior: 'smooth' });
			}
			left.addEventListener('click',  function () { scrollBy(-220); });
			right.addEventListener('click', function () { scrollBy(220);  });
			scroller.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			update();
			setTimeout(update, 200);
			setTimeout(update, 800);
		});
	}

	/* Keep header count in sync when WooCommerce fires added_to_cart */
	$(document.body).on('added_to_cart wc_fragments_refreshed removed_from_cart', refreshCart);

	/* Initial */
	refreshCart();
	initRailArrows();
})(jQuery);
