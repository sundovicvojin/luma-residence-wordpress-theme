/* Luma Residence: nivo 2 (laptop/desktop). Učitava se tek posle provere uređaja, zajedno sa lokalnim GSAP-om i Lenisom.
   Animira se samo transform, opacity i povremeno clip-path. Čuvač performansi vraća sajt na nivo 1. */
(function (w, d) {
	'use strict';

	var gsap = w.gsap;
	var ST = w.ScrollTrigger;
	var Lenis = w.Lenis;
	if (!gsap || !ST) { return; }
	gsap.registerPlugin(ST);

	var h = d.documentElement;
	var EASE = 'power3.out'; // jedna kriva kroz ceo sajt
	var ctx = null;
	var lenis = null;
	var tick = null;
	var rafId = 0;
	var masked = [];
	var split = null;

	var $ = function (s) { return d.querySelector(s); };
	var $$ = function (s) { return Array.prototype.slice.call(d.querySelectorAll(s)); };

	/* Naslov u maski: sadržaj klizne unutar prozora (ne seku se slova). */
	var wrapMask = function (el) {
		var win = d.createElement('span');
		var inner = d.createElement('span');
		win.className = 'mask-wrap';
		inner.style.display = 'block';
		while (el.firstChild) { inner.appendChild(el.firstChild); }
		win.appendChild(inner);
		el.appendChild(win);
		masked.push({ el: el, win: win, inner: inner, rv: el.classList.contains('rv') });
		return inner;
	};

	var unwrapMasks = function () {
		masked.forEach(function (m) {
			while (m.inner.firstChild) { m.el.insertBefore(m.inner.firstChild, m.win); }
			m.el.removeChild(m.win);
			if (m.rv) { m.el.classList.add('rv', 'is-in'); }
		});
		masked = [];
	};

	/* Hero naslov: reč po reč kroz masku. Originalni sadržaj se čuva za vraćanje. */
	var splitWords = function (el) {
		var original = el.innerHTML;
		var words = el.textContent.trim().split(/\s+/);
		el.textContent = '';
		var inner = words.map(function (word, i) {
			var win = d.createElement('span');
			var sp = d.createElement('span');
			win.className = 'split-word';
			sp.textContent = word;
			win.appendChild(sp);
			el.appendChild(win);
			if (i < words.length - 1) { el.appendChild(d.createTextNode(' ')); }
			return sp;
		});
		split = { el: el, original: original };
		return inner;
	};

	/* Čuvač: prosečno vreme frejma kroz ~45 rAF poziva; preko 24 ms se spušta na nivo 1. */
	var guard = function () {
		var n = 0;
		var sum = 0;
		var last = 0;
		var step = function (t) {
			if (last) { sum += t - last; n++; }
			last = t;
			if (n < 45) {
				rafId = w.requestAnimationFrame(step);
				return;
			}
			var avg = sum / n;
			h.setAttribute('data-frame-ms', avg.toFixed(1));
			if (avg > 24) {
				w.__lumaDowngraded = avg;
				api.destroy();
			}
		};
		rafId = w.requestAnimationFrame(step);
	};

	var api = {
		active: false,
		init: function () {
			if (api.active) { return; }
			api.active = true;
			h.classList.add('motion-2');

			// Glatko skrolovanje (Lenis) vođeno GSAP tikerom, usklađeno sa ScrollTrigger-om.
			if (Lenis) {
				lenis = new Lenis({ lerp: 0.09, smoothWheel: true, anchors: { offset: -88 } });
				lenis.on('scroll', ST.update);
				tick = function (time) { lenis.raf(time * 1000); };
				gsap.ticker.add(tick);
				gsap.ticker.lagSmoothing(0);
			}

			ctx = gsap.context(function () {
				// Hero: naslov reč po reč, zatim podnaslov i dugmad; slika spor ulazak i blag pomak pri skrolu.
				var heroH1 = $('.hero__copy h1');
				if (heroH1) {
					var words = splitWords(heroH1);
					var rest = $$('.hero__copy .lead, .hero__copy .actions');
					gsap.set(words, { yPercent: 115 });
					gsap.set(rest, { opacity: 0, y: 20 });
					h.classList.remove('l2-pending');
					var tl = gsap.timeline({ delay: 0.1 });
					tl.to(words, { yPercent: 0, duration: 1, ease: EASE, stagger: 0.09 })
						.to(rest, { opacity: 1, y: 0, duration: 0.8, ease: EASE, stagger: 0.12 }, '-=0.55');
				} else {
					h.classList.remove('l2-pending');
				}

				var heroImg = $('.hero__img');
				if (heroImg) {
					// Parallax: slika klizi do 10% visine (preklop 1.2x pokriva pomak), tekst se blago pomera i bledi.
					gsap.fromTo(heroImg, { scale: 1.3 }, { scale: 1.2, duration: 2, ease: EASE });
					gsap.to(heroImg, {
						yPercent: 10,
						ease: 'none',
						scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }
					});
					gsap.to('.hero__copy', {
						yPercent: -12,
						opacity: 0.2,
						ease: 'none',
						scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom 30%', scrub: true }
					});
				}

				// Naslovi sekcija: maska otkriva naslov kad uđe u vidno polje.
				$$('[data-mask]').forEach(function (el) {
					var wasRv = el.classList.contains('rv');
					var inner = wrapMask(el);
					if (wasRv) { el.classList.remove('rv'); }
					gsap.fromTo(inner, { yPercent: 115 }, {
						yPercent: 0,
						duration: 0.9,
						ease: EASE,
						scrollTrigger: { trigger: el, start: 'top 90%', once: true }
					});
				});

				// Slike: otkrivanje preko clip-path, jednokratno.
				$$('[data-clip]').forEach(function (el) {
					gsap.fromTo(el, { clipPath: 'inset(0 0 100% 0)' }, {
						clipPath: 'inset(0 0 0% 0)',
						duration: 1.1,
						ease: EASE,
						clearProps: 'clipPath',
						scrollTrigger: { trigger: el, start: 'top 88%', once: true }
					});
				});

				// Parallax: najviše plus/minus 5% visine slike, samo translate.
				$$('[data-parallax]').forEach(function (el) {
					var img = el.querySelector('img');
					if (!img) { return; }
					gsap.set(img, { scale: 1.14 });
					gsap.fromTo(img, { yPercent: -5 }, {
						yPercent: 5,
						ease: 'none',
						scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true }
					});
				});

				// Linije se iscrtavaju (scaleX preko CSS promenljive), jednokratno.
				$$('.fact, .phase, .near li').forEach(function (el) {
					gsap.fromTo(el, { '--ls': 0 }, {
						'--ls': 1,
						duration: 1.1,
						ease: EASE,
						scrollTrigger: { trigger: el, start: 'top 92%', once: true }
					});
				});

				// Jedina pinovana sekcija: prodaja stanova. Render se lagano približava dok traje pin.
				var sale = $('.sale[data-pin]');
				if (sale) {
					var img = sale.querySelector('.sale__img');
					var copy = sale.querySelector('.sale__copy');
					gsap.timeline({
						scrollTrigger: { trigger: sale, start: 'top top', end: '+=70%', pin: true, scrub: 0.6, anticipatePin: 1 }
					})
						.fromTo(img, { scale: 1 }, { scale: 1.16, ease: 'none' }, 0)
						.fromTo(copy, { yPercent: 2 }, { yPercent: -3, ease: 'none' }, 0);
				}
			});

			ST.refresh();
			guard();
		},
		destroy: function () {
			if (!api.active) { return; }
			api.active = false;
			w.cancelAnimationFrame(rafId);
			ST.getAll().forEach(function (t) { t.kill(); });
			if (ctx) { ctx.revert(); ctx = null; }
			if (lenis) {
				gsap.ticker.remove(tick);
				gsap.ticker.lagSmoothing(500, 33);
				lenis.destroy();
				lenis = null;
			}
			unwrapMasks();
			if (split) { split.el.innerHTML = split.original; split = null; }
			// Vrati elemente u prirodno stanje (GSAP može ostaviti inline stilove).
			$$('.hero__img, [data-parallax] img, [data-clip], [data-parallax], .sale__img, .sale__copy, .hero__copy .lead, .hero__copy .actions, .fact, .phase, .near li').forEach(function (el) {
				el.style.transform = '';
				el.style.clipPath = '';
				el.style.willChange = '';
				el.style.opacity = '';
				el.style.removeProperty('--ls');
			});
			h.classList.remove('motion-2', 'l2-pending');
		}
	};

	w.LumaMotion = api;
})(window, document);
