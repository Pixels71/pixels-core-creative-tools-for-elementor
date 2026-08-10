(function ($) {
	'use strict';

	function pad2(value) {
		return value.toString().padStart(2, '0');
	}

	function updateDigit(digitNode, charValue) {
		if (!digitNode) {
			return;
		}

		const flowNode = digitNode.querySelector('.pixeccte-timer-digit-flow');
		if (flowNode && typeof flowNode.update === 'function') {
			flowNode.update(Number.parseInt(charValue, 10) || 0);
			return;
		}

		if (flowNode) {
			flowNode.textContent = charValue;
			return;
		}

		digitNode.textContent = charValue;
	}

	function updateUnit(node, value) {
		const paddedValue = pad2(value);
		const tens = node.querySelector('.pixeccte-timer-digit-tens');
		const ones = node.querySelector('.pixeccte-timer-digit-ones');

		updateDigit(tens, paddedValue.charAt(0));
		updateDigit(ones, paddedValue.charAt(1));
		updateRing(node, value);
	}

	function updateRing(unitNode, value) {
		const ring = unitNode?.querySelector('.pixeccte-timer-ring__progress');
		if (!ring) {
			return;
		}

		const max = Number.parseInt(unitNode.dataset.ringMax, 10) || 1;
		const circumference = Number.parseFloat(ring.getAttribute('stroke-dasharray')) || 0;
		if (!circumference) {
			return;
		}

		const progress = Math.max(0, Math.min(1, value / max));
		ring.style.strokeDashoffset = String(circumference * (1 - progress));
	}

	function setExpired(timerNode) {
		const action = timerNode.dataset.expiryAction || 'show_message';
		const containerNode = timerNode.closest('.pixeccte-countdown-timer');
		const messageNode = containerNode
			? containerNode.querySelector('.pixeccte-timer-expired-message')
			: null;

		if (action === 'hide') {
			if (containerNode) {
				containerNode.style.display = 'none';
			} else {
				timerNode.style.display = 'none';
			}
			return;
		}

		if (action === 'show_message') {
			timerNode.classList.add('is-hidden');
			if (messageNode) {
				messageNode.classList.remove('is-hidden');
			}
		}
	}

	function whenNumberFlowReady(callback) {
		if (customElements.get('number-flow')) {
			callback();
			return;
		}

		customElements.whenDefined('number-flow').then(callback);
	}

	function initTimerNodes(timerNodes) {
		timerNodes.each(function () {
			if (this.dataset.timerReady === 'yes') {
				return;
			}
			runTimer(this);
		});
	}

	function runTimer(timerNode) {
		const endTime = Number.parseInt(timerNode.dataset.endTime, 10);
		if (!endTime) {
			return;
		}

		const daysNode = timerNode.querySelector('[data-unit="days"]');
		const hoursNode = timerNode.querySelector('[data-unit="hours"]');
		const minutesNode = timerNode.querySelector('[data-unit="minutes"]');
		const secondsNode = timerNode.querySelector('[data-unit="seconds"]');
		let intervalId = null;

		function tick() {
			const now = Date.now();
			const difference = Math.max(0, endTime - now);

			if (difference <= 0) {
				if (daysNode) {
					updateUnit(daysNode, 0);
				}
				if (hoursNode) {
					updateUnit(hoursNode, 0);
				}
				if (minutesNode) {
					updateUnit(minutesNode, 0);
				}
				if (secondsNode) {
					updateUnit(secondsNode, 0);
				}
				clearInterval(intervalId);
				setExpired(timerNode);
				return;
			}

			const totalSeconds = Math.floor(difference / 1000);
			const days = Math.floor(totalSeconds / 86400);
			const hours = Math.floor((totalSeconds % 86400) / 3600);
			const minutes = Math.floor((totalSeconds % 3600) / 60);
			const seconds = totalSeconds % 60;

			if (daysNode) {
				updateUnit(daysNode, Math.min(days, 99));
			}
			if (hoursNode) {
				updateUnit(hoursNode, hours);
			}
			if (minutesNode) {
				updateUnit(minutesNode, minutes);
			}
			if (secondsNode) {
				updateUnit(secondsNode, seconds);
			}
		}

		tick();
		intervalId = setInterval(tick, 1000);
		timerNode.dataset.timerReady = 'yes';
		timerNode.dataset.timerIntervalId = String(intervalId);
	}

	const CountdownTimerHandler = elementorModules.frontend.handlers.Base.extend({
		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			const timerNodes = this.$element.find('.pixeccte-timer[data-end-time]');
			if (!timerNodes.length) {
				return;
			}

			whenNumberFlowReady(function () {
				initTimerNodes(timerNodes);
			});
		},

		onDestroy() {
			this.$element.find('.pixeccte-timer[data-end-time]').each(function () {
				const intervalId = Number.parseInt(this.dataset.timerIntervalId, 10);
				if (intervalId) {
					clearInterval(intervalId);
				}
				delete this.dataset.timerReady;
				delete this.dataset.timerIntervalId;
			});
		},
	});

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.elementsHandler.attachHandler('pixeccte-countdown-timer', CountdownTimerHandler);
	});
})(jQuery);
