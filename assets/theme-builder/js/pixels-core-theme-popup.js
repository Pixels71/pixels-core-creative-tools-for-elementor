(function () {
	'use strict';

	const seenKey = 'pixelsThemePopupSeen';

	function getPopup(id) {
		return document.querySelector('[data-pixels-core-popup][data-popup-id="' + id + '"]');
	}

	function getFocusableElements(popup) {
		return Array.prototype.slice.call(
			popup.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
		).filter(function (element) {
			return element.offsetWidth || element.offsetHeight || element === document.activeElement;
		});
	}

	function hasSeenPopup() {
		return window.sessionStorage && sessionStorage.getItem(seenKey) === 'yes';
	}

	function markPopupSeen() {
		if (window.sessionStorage) {
			sessionStorage.setItem(seenKey, 'yes');
		}

		window.pixelsThemePopupSeen = true;
	}

	function shouldAvoidPopup(popup) {
		return popup.dataset.avoidMultiple === 'yes' && (window.pixelsThemePopupSeen || hasSeenPopup());
	}

	function getPopupTriggers(popup) {
		if (popup.dataset.triggers) {
			return popup.dataset.triggers.split(',').filter(Boolean);
		}

		return popup.dataset.trigger === 'page_load' ? ['page_load'] : [];
	}

	function hasTrigger(popup, trigger) {
		return getPopupTriggers(popup).indexOf(trigger) !== -1;
	}

	function secondsToMilliseconds(value) {
		const seconds = parseFloat(value || '0') || 0;

		return Math.max(0, seconds * 1000);
	}

	function getScrollPercent() {
		const scrollableHeight = Math.max(
			document.documentElement.scrollHeight,
			document.body.scrollHeight
		) - window.innerHeight;

		if (scrollableHeight <= 0) {
			return 100;
		}

		return Math.min(100, Math.max(0, (window.scrollY / scrollableHeight) * 100));
	}

	function normalizeClassClickSelector(selector) {
		selector = (selector || '').trim();

		if (!selector) {
			return '';
		}

		if (/^[.#[:]/.test(selector)) {
			return selector;
		}

		return '.' + selector.replace(/^\.+/, '');
	}

	function openPopup(popup) {
		if (!popup || popup.classList.contains('is-open') || shouldAvoidPopup(popup)) {
			return false;
		}

		const closeButton = popup.querySelector('.pixels-core-theme-popup__close');
		const closeButtonDelay = parseFloat(popup.dataset.closeButtonDelay || '0') || 0;
		const autoCloseAfter = parseFloat(popup.dataset.autoCloseAfter || '0') || 0;

		popup.classList.remove('is-closing');
		popup.classList.add('is-open');
		popup.setAttribute('aria-hidden', 'false');

		if (popup.dataset.disableScroll === 'yes') {
			document.body.classList.add('pixels-core-theme-popup-open');
		}

		if (closeButton) {
			closeButton.style.display = closeButtonDelay > 0 ? 'none' : '';

			if (closeButtonDelay > 0) {
				window.setTimeout(function () {
					if (popup.classList.contains('is-open')) {
						closeButton.style.display = '';
					}
				}, closeButtonDelay * 1000);
			}
		}

		if (autoCloseAfter > 0) {
			window.setTimeout(function () {
				closePopup(popup);
			}, autoCloseAfter * 1000);
		}

		if (popup.dataset.accessibleNavigation === 'yes') {
			const focusableElements = getFocusableElements(popup);
			const firstFocusable = focusableElements[0] || popup.querySelector('.pixels-core-theme-popup__dialog');

			if (firstFocusable) {
				firstFocusable.focus({ preventScroll: true });
			}
		}

		markPopupSeen();

		return true;
	}

	function openPopupOnce(popup) {
		if (!popup || popup.dataset.automaticTriggerFired === 'yes') {
			return;
		}

		if (openPopup(popup)) {
			popup.dataset.automaticTriggerFired = 'yes';
		}
	}

	function closePopup(popup) {
		if (!popup || !popup.classList.contains('is-open')) {
			return;
		}

		popup.classList.add('is-closing');

		window.setTimeout(function () {
			popup.classList.remove('is-open', 'is-closing');
			popup.setAttribute('aria-hidden', 'true');

			if (!document.querySelector('[data-pixels-core-popup].is-open[data-disable-scroll="yes"]')) {
				document.body.classList.remove('pixels-core-theme-popup-open');
			}
		}, 350);
	}

	function initPageLoadPopups() {
		document.querySelectorAll('[data-pixels-core-popup]').forEach(function (popup) {
			if (!hasTrigger(popup, 'page_load')) {
				return;
			}

			let delay = secondsToMilliseconds(popup.dataset.pageLoadDelay);

			if (!popup.dataset.pageLoadDelay && popup.dataset.delay) {
				delay = parseInt(popup.dataset.delay || '0', 10) || 0;
			}

			window.setTimeout(function () {
				openPopupOnce(popup);
			}, delay);
		});
	}

	function initScrollPopups() {
		const popups = Array.prototype.slice.call(document.querySelectorAll('[data-pixels-core-popup]')).filter(function (popup) {
			return hasTrigger(popup, 'scroll');
		});

		if (!popups.length) {
			return;
		}

		let lastScrollY = window.scrollY;

		function checkScrollPopups() {
			const scrollY = window.scrollY;
			const direction = scrollY >= lastScrollY ? 'down' : 'up';
			const percent = getScrollPercent();

			popups.forEach(function (popup) {
				const targetDirection = popup.dataset.scrollDirection || 'down';
				const targetPercent = parseFloat(popup.dataset.scrollPercent || '50') || 0;
				const reachedTarget = targetDirection === 'up' ? percent <= targetPercent : percent >= targetPercent;

				if (direction === targetDirection && reachedTarget) {
					openPopupOnce(popup);
				}
			});

			lastScrollY = scrollY;
		}

		window.addEventListener('scroll', checkScrollPopups, { passive: true });
		checkScrollPopups();
	}

	function initScrollToElementPopups() {
		document.querySelectorAll('[data-pixels-core-popup]').forEach(function (popup) {
			if (!hasTrigger(popup, 'scroll_to') || !popup.dataset.scrollToSelector) {
				return;
			}

			try {
				document.querySelectorAll(popup.dataset.scrollToSelector).forEach(function (element) {
					if ('IntersectionObserver' in window) {
						const observer = new IntersectionObserver(function (entries) {
							entries.forEach(function (entry) {
								if (entry.isIntersecting) {
									openPopupOnce(popup);
								}
							});
						});

						observer.observe(element);
						return;
					}

					window.addEventListener('scroll', function () {
						const rect = element.getBoundingClientRect();

						if (rect.top < window.innerHeight && rect.bottom > 0) {
							openPopupOnce(popup);
						}
					}, { passive: true });
				});
			} catch (error) {
				// Ignore invalid custom selectors so one bad selector does not break every popup.
			}
		});
	}

	function initClickPopups() {
		const popups = Array.prototype.slice.call(document.querySelectorAll('[data-pixels-core-popup]')).filter(function (popup) {
			return hasTrigger(popup, 'click');
		});

		if (!popups.length) {
			return;
		}

		let clicks = 0;

		document.addEventListener('click', function (event) {
			if (event.target instanceof Element && event.target.closest('[data-pixels-core-popup]')) {
				return;
			}

			clicks += 1;

			popups.forEach(function (popup) {
				const targetClicks = parseInt(popup.dataset.clickCount || '1', 10) || 1;

				if (clicks >= targetClicks) {
					openPopupOnce(popup);
				}
			});
		});
	}

	function initClassClickPopups() {
		const popups = Array.prototype.slice.call(document.querySelectorAll('[data-pixels-core-popup]')).filter(function (popup) {
			return popup.dataset.classClickSelector && (hasTrigger(popup, 'class_click') || !popup.dataset.triggers);
		});

		if (!popups.length) {
			return;
		}

		document.addEventListener('click', function (event) {
			const target = event.target instanceof Element ? event.target : event.target.parentElement;

			if (!target || target.closest('[data-pixels-core-popup]')) {
				return;
			}

			popups.forEach(function (popup) {
				const selector = normalizeClassClickSelector(popup.dataset.classClickSelector);

				if (!selector) {
					return;
				}

				try {
					if (target.closest(selector)) {
						event.preventDefault();
						openPopup(popup);
					}
				} catch (error) {
					// Ignore invalid custom selectors so one bad selector does not break every popup.
				}
			});
		});
	}

	function initInactivityPopups() {
		document.querySelectorAll('[data-pixels-core-popup]').forEach(function (popup) {
			if (!hasTrigger(popup, 'inactivity')) {
				return;
			}

			let timer = null;
			const delay = secondsToMilliseconds(popup.dataset.inactivityDelay);
			const events = ['mousemove', 'keydown', 'scroll', 'touchstart', 'click'];

			function resetTimer() {
				window.clearTimeout(timer);
				timer = window.setTimeout(function () {
					openPopupOnce(popup);
				}, delay);
			}

			events.forEach(function (eventName) {
				document.addEventListener(eventName, resetTimer, { passive: true });
			});

			resetTimer();
		});
	}

	function initExitIntentPopups() {
		document.querySelectorAll('[data-pixels-core-popup]').forEach(function (popup) {
			if (!hasTrigger(popup, 'exit_intent')) {
				return;
			}

			document.addEventListener('mouseleave', function (event) {
				if (event.clientY <= 0) {
					openPopupOnce(popup);
				}
			});
		});
	}

	function initAdBlockPopups() {
		const popups = Array.prototype.slice.call(document.querySelectorAll('[data-pixels-core-popup]')).filter(function (popup) {
			return hasTrigger(popup, 'adblock');
		});

		if (!popups.length) {
			return;
		}

		const bait = document.createElement('div');
		bait.className = 'ads ad adsbox doubleclick pub_300x250';
		bait.style.cssText = 'position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;';
		document.body.appendChild(bait);

		window.setTimeout(function () {
			const blocked = !bait.offsetParent || bait.offsetHeight === 0 || bait.offsetWidth === 0 || window.getComputedStyle(bait).display === 'none';

			if (bait.parentNode) {
				bait.parentNode.removeChild(bait);
			}

			if (!blocked) {
				return;
			}

			popups.forEach(function (popup) {
				window.setTimeout(function () {
					openPopupOnce(popup);
				}, secondsToMilliseconds(popup.dataset.adblockDelay));
			});
		}, 100);
	}

	function initSelectorTriggers() {
		document.querySelectorAll('[data-pixels-core-popup][data-open-selector]').forEach(function (popup) {
			const selector = popup.dataset.openSelector;

			if (!selector) {
				return;
			}

			try {
				document.querySelectorAll(selector).forEach(function (trigger) {
					trigger.addEventListener('click', function (event) {
						event.preventDefault();
						openPopup(popup);
					});
				});
			} catch (error) {
				// Ignore invalid custom selectors so one bad selector does not break every popup.
			}
		});
	}

	document.addEventListener('click', function (event) {
		const target = event.target instanceof Element ? event.target : event.target.parentElement;

		if (!target) {
			return;
		}

		const openTrigger = target.closest('[data-pixels-core-popup-open]');

		if (openTrigger) {
			event.preventDefault();
			openPopup(getPopup(openTrigger.getAttribute('data-pixels-core-popup-open')));
			return;
		}

		const closeTrigger = target.closest('[data-pixels-core-popup-close]');

		if (closeTrigger) {
			const popup = closeTrigger.closest('[data-pixels-core-popup]');

			if (popup && closeTrigger.classList.contains('pixels-core-theme-popup__overlay') && popup.dataset.preventOverlayClose === 'yes') {
				return;
			}

			event.preventDefault();
			closePopup(popup);
		}
	});

	document.addEventListener('keydown', function (event) {
		const openPopupElement = document.querySelector('[data-pixels-core-popup].is-open');

		if (!openPopupElement) {
			return;
		}

		if (event.key === 'Escape') {
			document.querySelectorAll('[data-pixels-core-popup].is-open').forEach(function (popup) {
				if (popup.dataset.preventEscClose !== 'yes') {
					closePopup(popup);
				}
			});
			return;
		}

		if (event.key !== 'Tab' || openPopupElement.dataset.accessibleNavigation !== 'yes') {
			return;
		}

		const focusableElements = getFocusableElements(openPopupElement);

		if (!focusableElements.length) {
			return;
		}

		const firstFocusable = focusableElements[0];
		const lastFocusable = focusableElements[focusableElements.length - 1];

		if (event.shiftKey && document.activeElement === firstFocusable) {
			event.preventDefault();
			lastFocusable.focus();
		} else if (!event.shiftKey && document.activeElement === lastFocusable) {
			event.preventDefault();
			firstFocusable.focus();
		}
	});

	function initPopups() {
		initSelectorTriggers();
		initPageLoadPopups();
		initScrollPopups();
		initScrollToElementPopups();
		initClassClickPopups();
		initClickPopups();
		initInactivityPopups();
		initExitIntentPopups();
		initAdBlockPopups();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initPopups);
	} else {
		initPopups();
	}
})();
