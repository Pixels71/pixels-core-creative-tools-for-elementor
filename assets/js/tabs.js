(function ($) {
	'use strict';

	const PixelsTabsHandler = elementorModules.frontend.handlers.Base.extend({
		getDefaultSettings() {
			return {
				selectors: {
					widget: '.pixeccte-tabs',
					tabTitle: '.pixeccte-tabs__title',
					tabPanel: '.pixeccte-tabs__panels > .e-con',
				},
			};
		},

		getDefaultElements() {
			const selectors = this.getSettings('selectors');

			return {
				$widget: this.$element.find(selectors.widget),
				$tabTitles: this.$element.find(selectors.tabTitle),
				$tabPanels: this.$element.find(selectors.tabPanel),
			};
		},

		bindEvents() {
			this.elements.$tabTitles.on('click', this.onTabClick.bind(this));
			this.elements.$tabTitles.on('keydown', this.onTabKeydown.bind(this));

			elementorFrontend.elements.$window.on(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		unbindEvents() {
			this.elements.$tabTitles.off('click keydown');

			elementorFrontend.elements.$window.off(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			if (!elementorFrontend.isEditMode()) {
				this.elements.$tabPanels.removeAttr('hidden');
				this.elements.$widget.addClass('pixeccte-tabs--animated');
			}

			this.activateTab(this.getDefaultTabIndex(), false);
		},

		getDefaultTabIndex() {
			return parseInt(this.elements.$widget.data('default-tab'), 10) || 1;
		},

		getActiveTabIndex() {
			const $active = this.elements.$tabTitles.filter('.is-active').first();
			return $active.length ? parseInt($active.data('tab'), 10) : 0;
		},

		activateTab(tabIndex, animate = true) {
			const isEditMode = elementorFrontend.isEditMode();
			const shouldAnimate = animate && ! isEditMode && this.elements.$widget.hasClass( 'pixeccte-tabs--animated' );

			this.elements.$tabTitles.each(function () {
				const $title = $(this);
				const isActive = parseInt($title.data('tab'), 10) === tabIndex;

				$title.toggleClass('is-active', isActive);
				$title.attr('aria-selected', isActive ? 'true' : 'false');
				$title.attr('tabindex', isActive ? '0' : '-1');
			});

			const $targetPanel = this.elements.$tabPanels.filter(function () {
				return parseInt($(this).data('tab'), 10) === tabIndex;
			});

			this.elements.$tabPanels.each(function () {
				const $panel = $(this);
				const isActive = $panel.is($targetPanel);

				$panel.toggleClass('is-active', isActive);
				$panel.attr('aria-hidden', isActive ? 'false' : 'true');

				if (isEditMode) {
					if (isActive) {
						$panel.removeAttr('hidden');
					} else {
						$panel.attr('hidden', 'hidden');
					}
					return;
				}

				$panel.removeAttr('hidden');
			});

			if (shouldAnimate && $targetPanel.length) {
				$targetPanel.removeClass('is-active');
				void $targetPanel[0].offsetWidth;
				$targetPanel.addClass('is-active');
			}
		},

		onTabClick(event) {
			event.preventDefault();

			const tabIndex = parseInt($(event.currentTarget).data('tab'), 10);

			if (tabIndex === this.getActiveTabIndex()) {
				return;
			}

			this.activateTab(tabIndex);
		},

		isVerticalLayout() {
			return this.$element.hasClass('pixeccte-tabs--vertical');
		},

		onTabKeydown(event) {
			const $current = $(event.currentTarget);
			const $titles = this.elements.$tabTitles;
			const currentIndex = $titles.index($current);
			let nextIndex = currentIndex;
			const isVertical = this.isVerticalLayout();
			const key = event.key;

			if (key === 'ArrowRight' && !isVertical) {
				nextIndex = (currentIndex + 1) % $titles.length;
			} else if (key === 'ArrowLeft' && !isVertical) {
				nextIndex = (currentIndex - 1 + $titles.length) % $titles.length;
			} else if (key === 'ArrowDown' && isVertical) {
				nextIndex = (currentIndex + 1) % $titles.length;
			} else if (key === 'ArrowUp' && isVertical) {
				nextIndex = (currentIndex - 1 + $titles.length) % $titles.length;
			} else if (key === 'Home') {
				nextIndex = 0;
			} else if (key === 'End') {
				nextIndex = $titles.length - 1;
			} else {
				return;
			}

			event.preventDefault();
			$titles.eq(nextIndex).trigger('click').focus();
		},

		refreshElements() {
			const selectors = this.getSettings('selectors');

			this.elements.$widget = this.$element.find(selectors.widget);
			this.elements.$tabTitles = this.$element.find(selectors.tabTitle);
			this.elements.$tabPanels = this.$element.find(selectors.tabPanel);
		},

		updateIndexValues() {
			const widgetNumber = this.elements.$widget.data('widget-number');

			this.elements.$tabTitles.each((index, element) => {
				const newIndex = index + 1;
				const tabTitleId = `pixeccte-tab-title-${widgetNumber}${newIndex}`;
				const contentId = `pixeccte-tab-content-${widgetNumber}${newIndex}`;

				element.setAttribute('id', tabTitleId);
				element.setAttribute('data-tab', newIndex);
				element.setAttribute('aria-controls', contentId);

				const $label = $(element).find('.pixeccte-tabs__label');
				if ($label.length) {
					$label.attr('data-binding-index', newIndex);
				}

				if (this.elements.$tabPanels[index]) {
					const panel = this.elements.$tabPanels[index];

					panel.setAttribute('id', contentId);
					panel.setAttribute('data-tab', newIndex);
					panel.setAttribute('data-tab-index', newIndex);
					panel.setAttribute('aria-labelledby', tabTitleId);
					panel.classList.add('pixeccte-tabs__panel');
				}
			});
		},

		onAtomicRepeater(event) {
			const { container, action } = event.detail;

			if (!container || container.model.get('id') !== this.getID()) {
				return;
			}

			this.refreshElements();
			this.updateIndexValues();
			this.elements.$tabTitles.off('click keydown');
			this.elements.$tabTitles.on('click', this.onTabClick.bind(this));
			this.elements.$tabTitles.on('keydown', this.onTabKeydown.bind(this));

			if (action && action.type === 'create') {
				const newIndex = this.elements.$tabTitles.length;
				this.activateTab(newIndex, !elementorFrontend.isEditMode());
			} else if (!this.getActiveTabIndex()) {
				this.activateTab(this.getDefaultTabIndex(), false);
			}

			if (elementor.$preview && elementor.$preview[0]) {
				elementor.$preview[0].contentWindow.dispatchEvent(
					new CustomEvent('elementor/elements/link-data-bindings')
				);
			}
		},
	});

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.elementsHandler.attachHandler('pixeccte-tabs', PixelsTabsHandler);
	});
})(jQuery);
