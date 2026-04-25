/* HCH Electric — cart drawer + filters + AJAX add-to-cart.
 * Search: inline header expansion (no dropdown overlay, no scroll-lock).
 * Product cards: [−][qty][+] add-to-cart without opening the cart drawer.
 */
(function ($) {
	'use strict';
	if (typeof HCH === 'undefined') return;

	const $overlay = $('#hchOverlay');
	const $drawer  = $('#hchDrawer');
	const $items   = $('#hchDrawerItems');
	const $total   = $('#hchDrawerTotal');
	const $count   = $('#hchCartCount');
	const $waBtn   = $('#hchWaBtn');

	let currentWaUrl = '';

	/* ── Cart drawer open / close ── */
	function openCart()  {
		$overlay.addClass('open');
		$drawer.addClass('open');
		document.body.style.overflow = 'hidden';
		refreshCart();
	}
	function closeCart() {
		$overlay.removeClass('open');
		$drawer.removeClass('open');
		document.body.style.overflow = '';
	}

	$(document).on('click', '#hchCartBtn',   function (e) { e.preventDefault(); openCart(); });
	$(document).on('click', '#hchCartClose', closeCart);
	$(document).on('click', '#hchOverlay',   closeCart);
	$(document).on('keydown', function (e) {
		if (e.key === 'Escape') { closeCart(); closeInlineSearch(); }
	});

	/* ── Inline header search (expands inside the header, no scroll-lock) ── */
	function closeInlineSearch() {
		$('#hchInlineSearch').removeClass('open');
	}

	$(document).on('click', '#hchSearchToggle', function (e) {
		e.stopPropagation();
		var $sw = $('#hchInlineSearch');
		var opening = !$sw.hasClass('open');
		$sw.toggleClass('open');
		if (opening) {
			setTimeout(function () {
				$sw.find('.hch-header__search-input').trigger('focus');
			}, 40);
		}
	});

	/* Close inline search when clicking anywhere else */
	$(document).on('click', function (e) {
		if (!$(e.target).closest('#hchInlineSearch, #hchSearchToggle').length) {
			closeInlineSearch();
		}
	});

	/* ── WhatsApp order button ── */
	$waBtn.on('click', function (e) {
		e.preventDefault();
		if (!currentWaUrl) return;
		window.open(currentWaUrl, '_blank');
	});

	/* ── Product card: decrease qty ── */
	$(document).on('click', '.hch-atc__dec', function (e) {
		e.preventDefault();
		e.stopPropagation();
		var $input = $(this).siblings('.hch-atc__num');
		var min = parseInt($input.attr('min'), 10) || 1;
		var cur = parseInt($input.val(), 10) || 1;
		if (cur > min) $input.val(cur - 1);
	});

	/* ── Product card: add to cart (does NOT open cart drawer) ── */
	$(document).on('click', '.hch-atc__btn', function (e) {
		e.preventDefault();
		e.stopPropagation();
		var $btn = $(this);
		if ($btn.hasClass('loading')) return;
		var pid  = $btn.data('product-id');
		var moq  = parseInt($btn.data('moq'), 10) || 1;
		var qty  = parseInt($btn.closest('.hch-atc__row').find('.hch-atc__num').val(), 10) || moq;
		if (qty < moq) qty = moq;
		$btn.addClass('loading');
		$.post(HCH.ajaxUrl, {
			action:     'hch_add_to_cart',
			nonce:      HCH.nonce,
			product_id: pid,
			quantity:   qty
		}).done(function (res) {
			if (res && res.success) {
				applyPayload(res.data);
				$btn.removeClass('loading').addClass('added').text('✓');
				setTimeout(function () { $btn.removeClass('added').text('+'); }, 1200);
			} else {
				$btn.removeClass('loading');
			}
		}).fail(function () { $btn.removeClass('loading'); });
	});

	/* ── Cart drawer: qty +/− and remove ── */
	$(document).on('click', '.hch-ci__qty-inc, .hch-ci__qty-dec, .hch-ci__rm', function () {
		var $btn   = $(this);
		var $ci    = $btn.closest('.hch-ci');
		var key    = $ci.data('key');
		var $input = $ci.find('.qty__input');
		var cur    = parseInt($input.val(), 10) || 1;
		var moq    = parseInt($ci.data('moq'), 10) || 1;
		var qty    = cur;
		if ($btn.hasClass('hch-ci__qty-inc'))      qty = cur + 1;
		else if ($btn.hasClass('hch-ci__qty-dec')) qty = Math.max(moq, cur - 1);
		else                                       qty = 0;
		$.post(HCH.ajaxUrl, { action: 'hch_update_cart', nonce: HCH.nonce, cart_key: key, quantity: qty })
			.done(function (res) { if (res && res.success) applyPayload(res.data); });
	});

	$(document).on('change blur', '.qty__input', function () {
		var $input = $(this);
		var $ci    = $input.closest('.hch-ci');
		var key    = $ci.data('key');
		var moq    = parseInt($ci.data('moq'), 10) || 1;
		var qty    = parseInt($input.val(), 10);
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
		var rows = data.items.map(function (it) {
			var iconHtml = it.thumb
				? '<img src="' + it.thumb + '" alt="" loading="lazy"/>'
				: (it.icon ? it.icon : '•');
			var spec = [it.spec, it.sku].filter(Boolean).join(' · ');
			return '<div class="hch-ci" data-key="' + it.key + '" data-moq="' + it.moq + '">'
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
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	/* ── Brand filter chips (client-side filter, no page navigation) ── */
	$(document).on('click', '.hch-brand-chip', function (e) {
		e.preventDefault();
		var $c = $(this);
		var brand = $c.data('brand') || 'all';
		$('.hch-brand-chip').removeClass('active');
		$c.addClass('active');
		applyFilters();
	});

	function applyFilters() {
		var activeBrand = $('.hch-brand-chip.active').data('brand') || 'all';
		var shown = 0;
		$('#hchProdGrid > li.hch-product').each(function () {
			var brands = (this.getAttribute('data-brand') || '').split(/\s+/);
			var ok = activeBrand === 'all' || brands.indexOf(activeBrand) !== -1;
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
				if (max <= 2) { left.classList.remove('visible'); right.classList.remove('visible'); return; }
				left.classList.toggle('visible',  scroller.scrollLeft > 4);
				right.classList.toggle('visible', scroller.scrollLeft < max - 4);
			}
			left.addEventListener('click',  function () { scroller.scrollBy({ left: -220, behavior: 'smooth' }); });
			right.addEventListener('click', function () { scroller.scrollBy({ left:  220, behavior: 'smooth' }); });
			scroller.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			update();
			setTimeout(update, 200);
			setTimeout(update, 800);
		});
	}

	$(document.body).on('added_to_cart wc_fragments_refreshed removed_from_cart', refreshCart);

	refreshCart();
	initRailArrows();
})(jQuery);
