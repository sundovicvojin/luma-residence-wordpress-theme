/* Luma Residence: osnovni skript (nivo 0 i 1). Bez biblioteka, bez scroll listenera. */
(function (w, d) {
	'use strict';

	var h = d.documentElement;
	var $ = function (s, c) { return (c || d).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
	var reduce = w.matchMedia('(prefers-reduced-motion:reduce)').matches;
	var animReady = h.classList.contains('anim-ready');

	/* Mobilna navigacija: aria-expanded, Esc zatvara, fokus ostaje unutar panela dok je otvoren. */
	var nav = $('#nav');
	var btn = $('.nav-toggle');
	var header0 = $('#header');
	if (nav && btn) {
		var label = $('.nav-toggle__label', btn);
		var wide = w.matchMedia('(min-width:1024px)');
		// Redosled fokusa dok je meni otvoren: zaglavlje, pa stavke panela (u DOM-u je panel pre dugmeta).
		var focusables = function () {
			return $$('.brand, .header-call', header0).concat([btn], $$('a[href]', nav));
		};
		var setOpen = function (open) {
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			nav.classList.toggle('is-open', open);
			d.body.classList.toggle('nav-open', open);
			if (label) { label.textContent = open ? 'Zatvori' : 'Meni'; }
		};
		btn.addEventListener('click', function () {
			setOpen(btn.getAttribute('aria-expanded') !== 'true');
		});
		d.addEventListener('keydown', function (e) {
			if (btn.getAttribute('aria-expanded') !== 'true') { return; }
			if (e.key === 'Escape') {
				setOpen(false);
				btn.focus();
			} else if (e.key === 'Tab') {
				var f = focusables();
				var i = f.indexOf(d.activeElement);
				var next = e.shiftKey ? i - 1 : i + 1;
				if (i === -1) { next = 0; }
				e.preventDefault();
				f[(next + f.length) % f.length].focus();
			}
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest && e.target.closest('a')) { setOpen(false); }
		});
		var onWide = function () { if (wide.matches) { setOpen(false); } };
		if (wide.addEventListener) { wide.addEventListener('change', onWide); }
	}

	/* Stanje zaglavlja: jedan observer na elementu na vrhu stranice. */
	var header = $('#header');
	var sentinel = $('#top-sentinel');
	if (header && sentinel && 'IntersectionObserver' in w) {
		new IntersectionObserver(function (entries) {
			header.classList.toggle('is-scrolled', !entries[0].isIntersecting);
		}).observe(sentinel);
	}

	/* Pojavljivanje elemenata: fade + pomak do 24 px, jednokratno. */
	if (animReady) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting || en.boundingClientRect.top < 0) {
					en.target.classList.add('is-in');
					io.unobserve(en.target);
				}
			});
		}, { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
		$$('.rv').forEach(function (el) { io.observe(el); });
	}

	/* Brojači: konačna vrednost je u DOM-u od početka (čitači ekrana), animira se samo vizuelno. */
	if (animReady && !reduce) {
		var ease = function (t) { return 1 - Math.pow(1 - t, 3); };
		var cio = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (!en.isIntersecting) { return; }
				cio.unobserve(en.target);
				var el = en.target;
				var to = parseInt(el.getAttribute('data-count'), 10);
				if (isNaN(to)) { return; }
				var text = String(to);
				el.style.display = 'inline-block';
				el.style.minWidth = el.offsetWidth + 'px';
				el.setAttribute('aria-hidden', 'true');
				var live = d.createElement('span');
				live.className = 'sr-only';
				live.style.cssText = 'position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)';
				live.textContent = text;
				el.parentNode.insertBefore(live, el);
				var t0 = 0;
				var dur = 1400;
				var step = function (t) {
					if (!t0) { t0 = t; }
					var p = Math.min((t - t0) / dur, 1);
					el.textContent = String(Math.round(to * ease(p)));
					if (p < 1) {
						w.requestAnimationFrame(step);
					} else {
						el.textContent = text;
						el.style.minWidth = '';
						el.style.display = '';
					}
				};
				el.textContent = '0';
				w.requestAnimationFrame(step);
			});
		}, { threshold: 0.6 });
		$$('[data-count]').forEach(function (el) { cio.observe(el); });
	}

	w.__lumaOK = true;

	/* Nivo 2: samo laptop/desktop koji prođe sve provere; tek tada se učitava GSAP. */
	var n = w.navigator;
	var level2 = function () {
		return w.matchMedia('(min-width:1024px)').matches &&
			w.matchMedia('(hover:hover) and (pointer:fine)').matches &&
			!w.matchMedia('(prefers-reduced-motion:reduce)').matches &&
			!(n.connection && n.connection.saveData) &&
			(n.deviceMemory === undefined || n.deviceMemory >= 4) &&
			(n.hardwareConcurrency === undefined || n.hardwareConcurrency >= 4);
	};

	if (!level2() || !$('[data-l2]') || !animReady) {
		h.classList.remove('l2-pending');
		return;
	}

	var base = (w.LUMA && w.LUMA.url) || '';
	var ver = (w.LUMA && w.LUMA.v) || '1';
	var load = function (file, cb) {
		var s = d.createElement('script');
		s.src = base + file + '?ver=' + ver;
		s.onload = cb;
		d.head.appendChild(s);
	};
	var started = false;
	var start = function () {
		if (started) { return; }
		started = true;
		var pending = 2;
		var done = function () {
			if (--pending) { return; }
			// Redosled: GSAP, ScrollTrigger i Lenis su učitani, tek onda motion.js.
			load('motion.js', function () {
				if (w.LumaMotion && !w.__lumaDowngraded) { w.LumaMotion.init(); }
			});
		};
		load('vendor/gsap.min.js', function () { load('vendor/ScrollTrigger.min.js', done); });
		load('vendor/lenis.min.js', done);
	};
	var idle = w.requestIdleCallback || function (f) { return w.setTimeout(f, 100); };
	var go = function () { idle(start, { timeout: 800 }); };
	if (d.readyState !== 'loading') { go(); } else { d.addEventListener('DOMContentLoaded', go); }

	/* Pad ispod 1024 px ili promena uslova: nazad na nivo 1. */
	var mq = w.matchMedia('(min-width:1024px)');
	var onMq = function () {
		if (!w.LumaMotion) { return; }
		if (!mq.matches) {
			w.LumaMotion.destroy();
		} else if (!w.__lumaDowngraded && level2()) {
			w.LumaMotion.init();
		}
	};
	if (mq.addEventListener) { mq.addEventListener('change', onMq); }
})(window, document);
