(function ($) {
	'use strict';

	const RING_TYPES = ['circle', 'semi-circle'];

	function parseDuration(wrapper) {
		const raw = wrapper.getAttribute('data-duration');
		const value = Number.parseInt(raw, 10);

		return Number.isFinite(value) && value > 0 ? value : 1200;
	}

	function isRingWrapper(wrapper) {
		return RING_TYPES.some(function (type) {
			return wrapper.classList.contains('pixels-core-progress-bar--' + type);
		});
	}

	function animateLinearItem(item, wrapper) {
		const fill = item.querySelector('.pixels-core-progress-bar__fill');
		if (!fill) {
			return;
		}

		const percent = Math.max(0, Math.min(100, Number.parseInt(item.dataset.percent, 10) || 0));
		const duration = parseDuration(wrapper);
		const isVertical = wrapper.classList.contains('pixels-core-progress-bar--vertical');

		fill.style.transitionDuration = duration + 'ms';

		if (isVertical) {
			fill.style.setProperty('--pixels-target-height', percent + '%');
		} else {
			fill.style.setProperty('--pixels-target-width', percent + '%');
		}
	}

	function animateRingItem(item, wrapper) {
		const ring = item.querySelector('.pixels-core-progress-bar__ring');
		const progress = item.querySelector('.pixels-core-progress-bar__ring-progress');

		if (!ring || !progress) {
			return;
		}

		const percent = Math.max(0, Math.min(100, Number.parseInt(item.dataset.percent, 10) || 0));
		const circumference = Number.parseFloat(ring.dataset.circumference) || Number.parseFloat(progress.getAttribute('stroke-dasharray')) || 0;
		const duration = parseDuration(wrapper);
		const offset = circumference * (1 - percent / 100);

		if (!circumference) {
			return;
		}

		wrapper.style.setProperty('--pixels-progress-duration', duration + 'ms');
		progress.style.setProperty('--pixels-ring-circumference', String(circumference));
		progress.setAttribute('stroke-dasharray', String(circumference));
		progress.setAttribute('stroke-dashoffset', String(circumference));
		progress.style.strokeDashoffset = String(circumference);

		requestAnimationFrame(function () {
			progress.style.strokeDashoffset = String(offset);
			progress.setAttribute('stroke-dashoffset', String(offset));
		});
	}

	function animateItem(item, wrapper) {
		if (isRingWrapper(wrapper)) {
			animateRingItem(item, wrapper);
			return;
		}

		animateLinearItem(item, wrapper);
	}

	function animateWrapper(wrapper) {
		if (wrapper.classList.contains('is-animated')) {
			return;
		}

		const items = wrapper.querySelectorAll('.pixels-core-progress-bar__item');

		items.forEach(function (item) {
			animateItem(item, wrapper);
		});

		wrapper.classList.add('is-animated');
	}

	function initStaticBars(wrapper) {
		if (!wrapper.classList.contains('pixels-core-progress-bar--animate')) {
			return;
		}

		animateWrapper(wrapper);
	}

	function initAnimatedBars(wrapper) {
		if (!wrapper.classList.contains('pixels-core-progress-bar--animate')) {
			return;
		}

		if ('IntersectionObserver' in window) {
			const observer = new IntersectionObserver(
				function (entries, obs) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) {
							return;
						}

						animateWrapper(entry.target);
						obs.unobserve(entry.target);
					});
				},
				{
					threshold: 0.2,
				}
			);

			observer.observe(wrapper);
			return;
		}

		animateWrapper(wrapper);
	}

	function initScope($scope) {
		$scope.find('.pixels-core-progress-bar').each(function () {
			const wrapper = this;
			const isEditor = typeof elementor !== 'undefined' && elementor.previewView;

			if (isEditor) {
				initStaticBars(wrapper);
			} else {
				initAnimatedBars(wrapper);
			}
		});
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/pixels-progress-bar.default', function ($scope) {
			initScope($scope);
		});
	});
})(jQuery);
