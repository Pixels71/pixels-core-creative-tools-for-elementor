(function ($) {
	'use strict';

	function getSettings($scope) {
		const cached = $scope.data('settings');

		if (cached && typeof cached === 'object') {
			return cached;
		}

		return {
			close_on_outside_click: 'yes',
			close_on_link_click: 'yes',
			mobile_hamburger: 'yes',
			desktop_hamburger: '',
			lock_body_scroll: 'yes',
		};
	}

	function getHamburgerModes($menu, settings) {
		const desktopAttr = $menu.attr('data-desktop-hamburger');
		const mobileAttr = $menu.attr('data-mobile-hamburger');
		const desktopHamburger =
			desktopAttr !== undefined ? desktopAttr === '1' : settings.desktop_hamburger === 'yes';
		const mobileHamburger =
			mobileAttr !== undefined ? mobileAttr === '1' : (settings.mobile_hamburger ?? 'yes') === 'yes';

		return {
			desktopHamburger,
			mobileHamburger,
		};
	}

	function shouldUseHamburger($menu, settings, isMobileViewport) {
		const modes = getHamburgerModes($menu, settings);

		return (isMobileViewport && modes.mobileHamburger) || (!isMobileViewport && modes.desktopHamburger);
	}

	function shouldAccordionSubmenus($menu, settings, isMobileViewport) {
		return shouldUseHamburger($menu, settings, isMobileViewport) || isMobileViewport;
	}

	function isAsidePanel($menu) {
		return $menu.hasClass('pixels-core-menu--panel-aside');
	}

	function bindMenu($scope) {
		const $menu = $scope.find('.pixels-core-menu').first();

		if (!$menu.length || $menu.data('pixelsMenuBound')) {
			return;
		}

		$menu.data('pixelsMenuBound', true);

		const $toggle = $menu.find('.pixels-core-menu__toggle');
		const $wrap = $menu.find('.pixels-core-menu__wrap');
		const $overlay = $menu.find('.pixels-core-menu__overlay');
		const $close = $menu.find('.pixels-core-menu__close');
		const parentSelector = '.menu-item-has-children, .pixels-mega-menu-item';
		const menuUid = $menu.attr('id') || 'menu-' + Math.random().toString(36).slice(2);
		const breakpoint = Math.max(
			320,
			Math.min(1920, parseInt($menu.attr('data-breakpoint'), 10) || 1024)
		);
		const mediaQuery = window.matchMedia('(max-width: ' + breakpoint + 'px)');

		const closeAllSubmenus = () => {
			$menu.find('.menu-item.is-submenu-open').removeClass('is-submenu-open');
			$menu.find('[aria-expanded="true"]').not($toggle).attr('aria-expanded', 'false');
		};

		const setBodyScrollLock = (locked) => {
			const settings = getSettings($scope);

			if ('yes' !== (settings.lock_body_scroll ?? 'yes') || !isAsidePanel($menu)) {
				return;
			}

			document.body.classList.toggle('pixels-core-menu-aside-open', locked);
		};

		const closeMenu = () => {
			$menu.removeClass('is-open');
			$toggle.attr('aria-expanded', 'false');
			$overlay.attr('aria-hidden', 'true');
			setBodyScrollLock(false);
			closeAllSubmenus();
		};

		const openMenu = () => {
			$menu.addClass('is-open');
			$toggle.attr('aria-expanded', 'true');
			$overlay.attr('aria-hidden', 'false');
			setBodyScrollLock(true);
		};

		const isHamburgerMode = () => $menu.hasClass('is-hamburger');
		const isAccordionSubmenuMode = () => $menu.hasClass('is-submenu-accordion');

		const updateLayoutMode = () => {
			const settings = getSettings($scope);
			const isMobileViewport = mediaQuery.matches;
			const useHamburger = shouldUseHamburger($menu, settings, isMobileViewport);
			const useAccordionSubmenus = shouldAccordionSubmenus($menu, settings, isMobileViewport);

			$menu.toggleClass('is-hamburger', useHamburger);
			$menu.toggleClass('is-hamburger-desktop', useHamburger && !isMobileViewport);
			$menu.toggleClass('is-hamburger-mobile', useHamburger && isMobileViewport);
			$menu.toggleClass('is-submenu-accordion', useAccordionSubmenus);

			if (!useHamburger) {
				closeMenu();
			}

			if (!useAccordionSubmenus) {
				closeAllSubmenus();
			}
		};

		$toggle.on('click', (event) => {
			event.preventDefault();
			event.stopPropagation();

			if (!isHamburgerMode()) {
				return;
			}

			if ($menu.hasClass('is-open')) {
				closeMenu();
			} else {
				openMenu();
			}
		});

		$close.on('click', (event) => {
			event.preventDefault();
			closeMenu();
			$toggle.trigger('focus');
		});

		$overlay.on('click', (event) => {
			event.preventDefault();
			closeMenu();
			$toggle.trigger('focus');
		});

		const toggleSubmenu = (event) => {
			if (!isAccordionSubmenuMode()) {
				return;
			}

			if (isHamburgerMode() && !$menu.hasClass('is-open')) {
				return;
			}

			const $link = $(event.target).closest('a');

			if (!$link.length || !$menu[0].contains($link[0])) {
				return;
			}

			const $item = $link.parent();

			if (!$item.is(parentSelector)) {
				return;
			}
			const hasSubmenu = $item.children('.sub-menu, .pixels-mega-menu-panel').length > 0;

			if (!hasSubmenu) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			const isOpen = $item.hasClass('is-submenu-open');

			$item.siblings('.is-submenu-open')
				.removeClass('is-submenu-open')
				.find('[aria-expanded="true"]')
				.attr('aria-expanded', 'false');

			if (isOpen) {
				$item.removeClass('is-submenu-open');
				$link.attr('aria-expanded', 'false');
			} else {
				$item.addClass('is-submenu-open');
				$link.attr('aria-expanded', 'true');
			}
		};

		$menu[0].addEventListener('click', toggleSubmenu, true);

		$wrap.on('click', 'a', (event) => {
			const settings = getSettings($scope);

			if ('yes' !== settings.close_on_link_click || !isHamburgerMode()) {
				return;
			}

			const $link = $(event.currentTarget);
			const $item = $link.parent();
			const hasSubmenu = $item.children('.sub-menu, .pixels-mega-menu-panel').length > 0;

			if (hasSubmenu && $item.is(parentSelector) && isAccordionSubmenuMode()) {
				return;
			}

			closeMenu();
		});

		$(document).on('click.pixelsMenu' + menuUid, (event) => {
			const settings = getSettings($scope);

			if (isAsidePanel($menu)) {
				return;
			}

			if ('yes' !== settings.close_on_outside_click || !isHamburgerMode() || !$menu.hasClass('is-open')) {
				return;
			}

			if ($menu[0].contains(event.target)) {
				return;
			}

			closeMenu();
		});

		$(document).on('keydown.pixelsMenu' + menuUid, (event) => {
			if ('Escape' !== event.key || !$menu.hasClass('is-open')) {
				return;
			}

			closeMenu();
			$toggle.trigger('focus');
		});

		const onBreakpointChange = () => updateLayoutMode();

		if (typeof mediaQuery.addEventListener === 'function') {
			mediaQuery.addEventListener('change', onBreakpointChange);
		} else if (typeof mediaQuery.addListener === 'function') {
			mediaQuery.addListener(onBreakpointChange);
		}

		$(window).on('resize.pixelsMenu' + menuUid, updateLayoutMode);
		updateLayoutMode();
	}

	function runReadyTriggers() {
		if (!window.elementorFrontend || !elementorFrontend.elementsHandler) {
			$('.elementor-widget-pixels-menu').each(function () {
				bindMenu($(this));
			});
			return;
		}

		$('.elementor-widget-pixels-menu').each(function () {
			try {
				elementorFrontend.elementsHandler.runReadyTrigger(this);
			} catch (error) {
				bindMenu($(this));
			}
		});
	}

	function registerElementorHooks() {
		if (!window.elementorFrontend || !elementorFrontend.hooks) {
			return false;
		}

		if (registerElementorHooks.initialized) {
			return true;
		}

		registerElementorHooks.initialized = true;

		elementorFrontend.hooks.addAction('frontend/element_ready/pixels-menu.default', function ($scope) {
			bindMenu($scope);
		});

		// Pro extensions can throw during element_ready/widget and abort Elementor's chain.
		// Re-bind any menus that were skipped.
		if (typeof requestAnimationFrame === 'function') {
			requestAnimationFrame(runReadyTriggers);
		} else {
			runReadyTriggers();
		}

		return true;
	}

	$(window).on('elementor/frontend/init', registerElementorHooks);

	// Pro/theme-builder pages may load this file after Elementor already initialized.
	if (window.elementorFrontend && elementorFrontend.hooks) {
		registerElementorHooks();
	}
})(jQuery);
