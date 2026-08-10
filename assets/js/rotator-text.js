(function ($) {
	'use strict';

	function parseNumber(value, fallback) {
		const parsed = Number.parseFloat(value);
		return Number.isFinite(parsed) ? parsed : fallback;
	}

	function doubleRaf(callback) {
		window.requestAnimationFrame(function () {
			window.requestAnimationFrame(callback);
		});
	}

	function setRingRotation(ring, degrees) {
		ring.style.transform = 'rotate(' + degrees + 'deg)';
	}

	function getPathElement(textPath) {
		const href = textPath.getAttribute('href') || textPath.getAttribute('xlink:href') || '';

		if (!href.startsWith('#')) {
			return null;
		}

		const pathId = href.slice(1);
		const svg = textPath.ownerSVGElement;

		if (!svg || !pathId) {
			return null;
		}

		return svg.querySelector('#' + CSS.escape(pathId));
	}

	function fitRingText(ring) {
		const textEl = ring.querySelector('.pixels-core-rotator-text__ring-text');
		const textPath = ring.querySelector('textPath');

		if (!textEl || !textPath || '' === textPath.textContent.trim()) {
			return;
		}

		const pathEl = getPathElement(textPath);

		if (!pathEl) {
			return;
		}

		const pathLength = pathEl.getTotalLength();

		if (!pathLength) {
			return;
		}

		textEl.setAttribute('textLength', String(pathLength));
		textEl.setAttribute('lengthAdjust', 'spacing');
		textPath.setAttribute('startOffset', '0%');
	}

	class RotatorText {
		constructor(root) {
			this.root = root;
			this.layout = root.dataset.layout || 'single';
			this.rotationSpeed = parseNumber(root.dataset.rotationSpeed, 20);
			this.scrollSensitivity = parseNumber(root.dataset.scrollSensitivity, 1);

			this.rings = Array.from(root.querySelectorAll('.pixels-core-rotator-text__ring'));
			this.innerRing = root.querySelector('.pixels-core-rotator-text__ring--inner');
			this.outerRing = root.querySelector('.pixels-core-rotator-text__ring--outer');
			this.svg = root.querySelector('.pixels-core-rotator-text__svg');

			this.scrollRotation = 0;
			this.lastScrollY = window.scrollY;
			this.isDestroyed = false;
			this.resizeObserver = null;

			this.onScroll = this.onScroll.bind(this);
			this.scheduleFitAllRings = this.scheduleFitAllRings.bind(this);

			this.applyCssVariables();
			this.init();
		}

		applyCssVariables() {
			this.root.style.setProperty('--pixels-rotator-speed', this.rotationSpeed + 's');
		}

		init() {
			this.scheduleFitAllRings();
			this.setupResizeObserver();

			if ('scroll' === this.layout) {
				this.initScrollMode();
			}
		}

		scheduleFitAllRings() {
			const fit = () => {
				if (this.isDestroyed) {
					return;
				}

				this.fitAllRings();
			};

			doubleRaf(fit);

			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(fit).catch(function () {
					fit();
				});
			}
		}

		fitAllRings() {
			this.rings.forEach(function (ring) {
				fitRingText(ring);
			});
		}

		setupResizeObserver() {
			if (!window.ResizeObserver || !this.svg) {
				return;
			}

			this.resizeObserver = new ResizeObserver(this.scheduleFitAllRings);
			this.resizeObserver.observe(this.svg);
			this.resizeObserver.observe(this.root);
		}

		initScrollMode() {
			this.lastScrollY = window.scrollY;
			window.addEventListener('scroll', this.onScroll, { passive: true });
			this.onScroll();
		}

		onScroll() {
			if (this.isDestroyed) {
				return;
			}

			const currentScrollY = window.scrollY;
			const delta = currentScrollY - this.lastScrollY;

			this.lastScrollY = currentScrollY;
			this.scrollRotation += delta * this.scrollSensitivity * 0.35;

			if (this.innerRing) {
				setRingRotation(this.innerRing, this.scrollRotation);
			}

			if (this.outerRing) {
				setRingRotation(this.outerRing, -this.scrollRotation * 0.85);
			}
		}

		destroy() {
			this.isDestroyed = true;
			window.removeEventListener('scroll', this.onScroll);

			if (this.resizeObserver) {
				this.resizeObserver.disconnect();
				this.resizeObserver = null;
			}
		}
	}

	const instances = new WeakMap();

	function initScope($scope) {
		$scope.find('.pixels-core-rotator-text').each(function () {
			const root = this;

			if (instances.has(root)) {
				instances.get(root).destroy();
			}

			const instance = new RotatorText(root);
			instances.set(root, instance);
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/pixels-core-rotator-text.default', function ($scope) {
			initScope($scope);
		});
	});
})(jQuery);
