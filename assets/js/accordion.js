(function ($) {
	'use strict';

	const AccordionHandler = elementorModules.frontend.handlers.Base.extend({
		getDefaultSettings() {
			return {
				selectors: {
					accordion: '.pixels-core-accordion',
					item: '.pixels-core-accordion__item',
					title: '.pixels-core-accordion__title',
					content: '.pixels-core-accordion__content',
					panel: '.pixels-core-accordion__panel',
				},
				animationDuration: 550,
			};
		},

		getDefaultElements() {
			const selectors = this.getSettings('selectors');

			return {
				$accordion: this.$element.find(selectors.accordion),
				$items: this.$element.find(selectors.item),
				$titles: this.$element.find(selectors.title),
			};
		},

		bindEvents() {
			this.elements.$titles.on('click', this.onTitleClick.bind(this));

			elementorFrontend.elements.$window.on(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		unbindEvents() {
			this.elements.$titles.off('click');

			elementorFrontend.elements.$window.off(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			if (!elementorFrontend.isEditMode()) {
				this.wrapPanels();
				this.elements.$accordion.addClass('pixels-core-accordion--animated');
				this.initOpenItems();
			}

			this.syncTitleStates();
		},

		isAnimated() {
			return this.elements.$accordion.hasClass('pixels-core-accordion--animated');
		},

		prefersReducedMotion() {
			return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		},

		wrapPanels() {
			const selectors = this.getSettings('selectors');

			this.elements.$items.each(function () {
				const $item = $(this);
				const $panel = $item.children(selectors.panel).first();

				if (!$panel.length || $panel.parent().hasClass('pixels-core-accordion__content-inner')) {
					return;
				}

				$panel.wrap('<div class="pixels-core-accordion__content-inner"></div>');
				$panel.parent().wrap('<div class="pixels-core-accordion__content"></div>');
			});
		},

		initOpenItems() {
			this.elements.$items.filter('[open]').each((index, element) => {
				const $item = $(element);
				$item.addClass('is-active is-expanded');
				this.updateTitleState($item, true);
			});
		},

		allowMultiple() {
			return (
				this.elements.$accordion.data('allowMultiple') === true ||
				this.elements.$accordion.attr('data-allow-multiple') === 'true'
			);
		},

		onTitleClick(event) {
			if (elementorFrontend.isEditMode() || !this.isAnimated() || this.prefersReducedMotion()) {
				return;
			}

			event.preventDefault();

			const $item = $(event.currentTarget).closest(this.getSettings('selectors.item'));
			const isOpen = $item.hasClass('is-expanded');

			if (isOpen) {
				this.closeItem($item);
				return;
			}

			if (!this.allowMultiple()) {
				const closePromises = [];

				this.elements.$items.filter('.is-expanded').each((index, element) => {
					if (element !== $item[0]) {
						closePromises.push(this.closeItem($(element)));
					}
				});

				Promise.all(closePromises).then(() => {
					this.openItem($item);
				});
				return;
			}

			this.openItem($item);
		},

		openItem($item) {
			$item.prop('open', true);
			$item.addClass('is-active');

			window.requestAnimationFrame(() => {
				window.requestAnimationFrame(() => {
					$item.addClass('is-expanded');
					this.updateTitleState($item, true);
				});
			});
		},

		closeItem($item) {
			return new Promise((resolve) => {
				if (!$item.hasClass('is-expanded')) {
					resolve();
					return;
				}

				const $content = $item.find(this.getSettings('selectors.content')).first();
				let resolved = false;

				const finish = () => {
					if (resolved) {
						return;
					}

					resolved = true;
					$item.prop('open', false);
					$item.removeClass('is-active is-expanded');
					this.updateTitleState($item, false);
					resolve();
				};

				$item.removeClass('is-expanded');

				if ($content.length) {
					$content.one('transitionend', finish);
					window.setTimeout(finish, this.getSettings('animationDuration') + 50);
				} else {
					finish();
				}
			});
		},

		updateTitleState($item, isOpen) {
			const $title = $item.find(this.getSettings('selectors.title')).first();

			$title.attr('aria-expanded', isOpen ? 'true' : 'false');
			$title.attr('tabindex', isOpen ? '0' : '-1');
		},

		syncTitleStates() {
			this.elements.$items.each((index, element) => {
				const $item = $(element);
				const isOpen = $item.hasClass('is-expanded') || element.open;

				this.updateTitleState($item, isOpen);

				if (index === 0) {
					$item.find(this.getSettings('selectors.title')).attr('tabindex', '0');
				}
			});
		},

		refreshElements() {
			const selectors = this.getSettings('selectors');

			this.elements.$accordion = this.$element.find(selectors.accordion);
			this.elements.$items = this.$element.find(selectors.item);
			this.elements.$titles = this.$element.find(selectors.title);
		},

		updateIndexValues() {
			const idBase = this.elements.$items.first().attr('id');

			if (!idBase) {
				return;
			}

			const base = idBase.replace(/\d+$/, '');

			this.elements.$items.each(function (index) {
				const $item = $(this);
				const itemId = base + index;
				const titleId = itemId.replace('pixels-accordion-item-', 'pixels-accordion-title-');

				$item.attr('id', itemId);

				const $title = $item.find('.pixels-core-accordion__title');
				$title.attr('id', titleId);
				$title.attr('data-item-index', index + 1);
				$title.attr('aria-controls', itemId);

				const $label = $item.find('.pixels-core-accordion__label');
				$label.attr('data-binding-index', index + 1);

				const $panel = $item.find('.pixels-core-accordion__panel').first();
				if ($panel.length) {
					$panel.attr('aria-labelledby', titleId);
				}
			});
		},

		onAtomicRepeater(event) {
			const { container, action } = event.detail;

			if (!container || container.model.get('id') !== this.getID()) {
				return;
			}

			if (action && (action.type === 'move' || action.type === 'duplicate')) {
				const { index, targetContainer } = event.detail;
				const targetIndex = action.type === 'duplicate' ? index + 1 : index;
				const $targetItem = this.elements.$items.eq(targetIndex);

				if ($targetItem.length && targetContainer && targetContainer.view) {
					const $inner = $targetItem.find('.pixels-core-accordion__content-inner').first();

					if ($inner.length) {
						$inner.append(targetContainer.view.$el[0]);
					} else {
						$targetItem.append(targetContainer.view.$el[0]);
					}
				}
			}

			this.refreshElements();

			if (!elementorFrontend.isEditMode()) {
				this.wrapPanels();
			}

			this.updateIndexValues();
			this.syncTitleStates();

			if (elementor.$preview && elementor.$preview[0]) {
				elementor.$preview[0].contentWindow.dispatchEvent(
					new CustomEvent('elementor/elements/link-data-bindings')
				);
			}
		},
	});

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.elementsHandler.attachHandler('pixels-accordion', AccordionHandler);
	});
})(jQuery);
