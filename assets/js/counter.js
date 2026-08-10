(function ($) {
	'use strict';

	const WATCHED_SETTINGS = [
		'starting_number',
		'ending_number',
		'prefix',
		'suffix',
		'duration',
		'thousand_separator',
	];

	function whenNumberFlowReady(callback) {
		if (customElements.get('number-flow')) {
			callback();
			return;
		}

		customElements.whenDefined('number-flow').then(callback);
	}

	function easeOutExpo(progress) {
		return progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
	}

	function roundValue(value, decimals) {
		if (!decimals) {
			return Math.round(value);
		}

		const factor = Math.pow(10, decimals);

		return Math.round(value * factor) / factor;
	}

	function getIntegerDigitCount(value) {
		const intPart = Math.floor(Math.abs(value));

		if (intPart === 0) {
			return 1;
		}

		return String(intPart).length;
	}

	function formatFallback(value, decimals, useGrouping, integerDigits) {
		return new Intl.NumberFormat(undefined, {
			useGrouping: useGrouping,
			minimumIntegerDigits: integerDigits,
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		}).format(value);
	}

	function readCounterData(flowNode) {
		const start = Number.parseFloat(flowNode.dataset.start || '0') || 0;
		const end = Number.parseFloat(flowNode.dataset.end || '0') || 0;
		const parsedIntegerDigits = Number.parseInt(flowNode.dataset.integerDigits || '0', 10);
		const integerDigits = parsedIntegerDigits > 0
			? parsedIntegerDigits
			: Math.max(getIntegerDigitCount(start), getIntegerDigitCount(end));

		return {
			start: start,
			end: end,
			duration: Number.parseInt(flowNode.dataset.duration || '2000', 10) || 2000,
			prefix: flowNode.dataset.prefix || '',
			suffix: flowNode.dataset.suffix || '',
			thousandSeparator: flowNode.dataset.thousandSeparator === 'yes',
			decimals: Number.parseInt(flowNode.dataset.decimals || '0', 10) || 0,
			integerDigits: integerDigits,
		};
	}

	function setupFlowNode(flowNode, data) {
		const format = {
			useGrouping: data.thousandSeparator,
			minimumIntegerDigits: data.integerDigits,
		};

		if (data.decimals > 0) {
			format.minimumFractionDigits = data.decimals;
			format.maximumFractionDigits = data.decimals;
		}

		flowNode.format = format;
		flowNode.transformTiming = {
			duration: Math.max(300, Math.min(data.duration, 1200)),
			easing:
				'linear(0,.005,.019,.039,.066,.096,.129,.165,.202,.24,.278,.316,.354,.39,.426,.461,.494,.526,.557,.586,.614,.64,.665,.689,.711,.731,.751,.769,.786,.802,.817,.831,.844,.856,.867,.877,.887,.896,.904,.912,.919,.925,.931,.937,.942,.947,.951,.955,.959,.962,.965,.968,.971,.973,.976,.978,.98,.981,.983,.984,.986,.987,.988,.989,.99,.991,.992,.992,.993,.994,.994,.995,.995,.996,.996,.9963,.9967,.9969,.9972,.9975,.9977,.9979,.9981,.9982,.9984,.9985,.9987,.9988,.9989,1)',
		};

		if (typeof flowNode.update === 'function') {
			flowNode.update(0);
		}
	}

	function updateFallback(wrapperNode, value, data) {
		const fallbackNode = wrapperNode.querySelector('.pixels-core-counter__value--fallback');

		if (!fallbackNode) {
			return;
		}

		fallbackNode.textContent = formatFallback(
			value,
			data.decimals,
			data.thousandSeparator,
			data.integerDigits
		);
	}

	function animateCounter(flowNode, wrapperNode, data, force) {
		if (!force && flowNode.dataset.counterAnimated === 'yes') {
			return;
		}

		if (flowNode._pixelsCounterFrame) {
			cancelAnimationFrame(flowNode._pixelsCounterFrame);
			flowNode._pixelsCounterFrame = null;
		}

		setupFlowNode(flowNode, data);
		updateFallback(wrapperNode, 0, data);

		const startValue = 0;
		const endValue = data.end;
		const duration = Math.max(100, data.duration);
		const startTime = performance.now();

		function frame(now) {
			const progress = Math.min((now - startTime) / duration, 1);
			const eased = easeOutExpo(progress);
			const current = roundValue(startValue + (endValue - startValue) * eased, data.decimals);

			if (typeof flowNode.update === 'function') {
				flowNode.update(current);
			}

			updateFallback(wrapperNode, current, data);

			if (progress < 1) {
				flowNode._pixelsCounterFrame = requestAnimationFrame(frame);
				return;
			}

			flowNode._pixelsCounterFrame = null;
			flowNode.dataset.counterAnimated = 'yes';
			updateFallback(wrapperNode, endValue, data);

			if (typeof flowNode.update === 'function') {
				flowNode.update(endValue);
			}
		}

		flowNode._pixelsCounterFrame = requestAnimationFrame(frame);
	}

	function initCounterNode(counterNode, force) {
		const flowNode = counterNode.querySelector('.pixels-core-counter__flow');
		const wrapperNode = counterNode.querySelector('.pixels-core-counter__number-wrapper');

		if (!flowNode || !wrapperNode) {
			return;
		}

		const data = readCounterData(flowNode);

		if (force) {
			delete flowNode.dataset.counterAnimated;
		}

		whenNumberFlowReady(function () {
			counterNode.classList.add('pixels-core-counter--flow-ready');
			animateCounter(flowNode, wrapperNode, data, force);
		});
	}

	const CounterHandler = elementorModules.frontend.handlers.Base.extend({
		getDefaultSettings() {
			return {
				selectors: {
					counter: '.pixels-core-counter',
					flow: '.pixels-core-counter__flow',
				},
			};
		},

		getDefaultElements() {
			const selectors = this.getSettings('selectors');

			return {
				$counter: this.$element.find(selectors.counter),
				$flow: this.$element.find(selectors.flow),
			};
		},

		runCounter(force) {
			if (!this.elements.$counter.length) {
				return;
			}

			this.elements.$counter.each(function () {
				initCounterNode(this, !!force);
			});
		},

		bindObserver() {
			if (!this.elements.$counter.length) {
				return;
			}

			const target = this.elements.$counter[0];

			this.intersectionObserver = elementorModules.utils.Scroll.scrollObserver({
				callback: (event) => {
					if (!event.isInViewport) {
						return;
					}

					this.intersectionObserver.unobserve(target);
					this.runCounter(false);
				},
			});

			this.intersectionObserver.observe(target);
		},

		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			if (!this.elements.$counter.length) {
				return;
			}

			if (elementorFrontend.isEditMode()) {
				this.runCounter(true);
				return;
			}

			this.bindObserver();
		},

		onElementChange(propertyName) {
			if (!WATCHED_SETTINGS.includes(propertyName)) {
				return;
			}

			this.runCounter(true);
		},

		onDestroy() {
			this.elements.$flow.each(function () {
				if (this._pixelsCounterFrame) {
					cancelAnimationFrame(this._pixelsCounterFrame);
					delete this._pixelsCounterFrame;
				}

				delete this.dataset.counterAnimated;
			});

			if (this.intersectionObserver && this.elements.$counter.length) {
				this.intersectionObserver.unobserve(this.elements.$counter[0]);
			}
		},
	});

	$(window).on('elementor/frontend/init', function () {
		bootCounterHandler();
	});

	if (typeof elementorFrontend !== 'undefined' && elementorFrontend.elementsHandler) {
		bootCounterHandler();
	}

	function bootCounterHandler() {
		if (bootCounterHandler.initialized) {
			return;
		}

		bootCounterHandler.initialized = true;
		elementorFrontend.elementsHandler.attachHandler('pixels-core-counter', CounterHandler);

		// Widget scripts can load after Elementor init on the frontend.
		$('.elementor-widget-pixels-core-counter').each(function () {
			elementorFrontend.elementsHandler.runReadyTrigger(this);
		});
	}
})(jQuery);
