(function ($) {
	'use strict';

	const EVENT_NS = '.pixeccteAccordion';

	// Extra grace on top of the measured CSS duration before the fallback timer fires.
	const FALLBACK_BUFFER = 120;

	const AccordionHandler = elementorModules.frontend.handlers.Base.extend({
		getDefaultSettings() {
			return {
				selectors: {
					accordion: '.pixeccte-accordion',
					item: '.pixeccte-accordion__item',
					title: '.pixeccte-accordion__title',
					content: '.pixeccte-accordion__content',
					panel: '.pixeccte-accordion__panel',
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
			const selectors = this.getSettings('selectors');

			this.onAtomicRepeaterBound = this.onAtomicRepeater.bind(this);

			// Delegated so items added by the repeater stay clickable without re-binding.
			this.$element.on('click' + EVENT_NS, selectors.title, this.onTitleClick.bind(this));

			elementorFrontend.elements.$window.on(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeaterBound
			);
		},

		unbindEvents() {
			this.$element.off(EVENT_NS);

			if (this.onAtomicRepeaterBound) {
				elementorFrontend.elements.$window.off(
					'elementor/nested-container/atomic-repeater',
					this.onAtomicRepeaterBound
				);
			}
		},

		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			if (!elementorFrontend.isEditMode()) {
				this.wrapPanels();
				this.elements.$accordion.addClass(
					'pixeccte-accordion--animated pixeccte-accordion--no-transition'
				);
				this.initOpenItems();
				this.releaseInitialTransitionLock();
			}

			this.syncTitleStates();
		},

		/**
		 * Items open by default must not animate open on page load.
		 */
		releaseInitialTransitionLock() {
			window.requestAnimationFrame(() => {
				window.requestAnimationFrame(() => {
					this.elements.$accordion.removeClass('pixeccte-accordion--no-transition');
				});
			});
		},

		isAnimated() {
			return this.elements.$accordion.hasClass('pixeccte-accordion--animated');
		},

		prefersReducedMotion() {
			return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		},

		wrapPanels() {
			const selectors = this.getSettings('selectors');

			this.elements.$items.each(function () {
				const $item = $(this);
				const $panel = $item.children(selectors.panel).first();

				if (!$panel.length || $panel.parent().hasClass('pixeccte-accordion__content-inner')) {
					return;
				}

				$panel.wrap('<div class="pixeccte-accordion__content-inner"></div>');
				$panel.parent().wrap('<div class="pixeccte-accordion__content"></div>');
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

		getContent($item) {
			return $item.children(this.getSettings('selectors.content')).first();
		},

		onTitleClick(event) {
			if (elementorFrontend.isEditMode() || !this.isAnimated()) {
				return;
			}

			const selectors = this.getSettings('selectors');
			const $item = $(event.currentTarget).closest(selectors.item);

			// Ignore clicks belonging to a nested accordion; its own handler owns them.
			if (!$item.length || $item.closest(selectors.accordion)[0] !== this.elements.$accordion[0]) {
				return;
			}

			event.preventDefault();

			if ($item.hasClass('is-expanded')) {
				this.closeItem($item);
				return;
			}

			if (!this.allowMultiple()) {
				// Collapse siblings alongside the opening item rather than waiting for them,
				// so the clicked item starts moving on the same frame as the click.
				this.elements.$items.filter('.is-expanded').each((index, element) => {
					if (element !== $item[0]) {
						this.closeItem($(element));
					}
				});
			}

			this.openItem($item);
		},

		openItem($item) {
			const $content = this.getContent($item);

			$item.prop('open', true);
			$item.addClass('is-active');
			this.updateTitleState($item, true);

			if (!this.isAnimated() || this.prefersReducedMotion()) {
				$item.addClass('is-expanded');
				return Promise.resolve();
			}

			// Flush the collapsed state so the grid-template-rows transition has a start value.
			if ($content.length) {
				void $content[0].offsetHeight;
			}

			$item.addClass('is-expanded');

			return new Promise((resolve) => {
				this.whenContentSettled($content, resolve);
			});
		},

		closeItem($item) {
			const $content = this.getContent($item);

			const settle = () => {
				// Bail out if the item was re-opened while this collapse was running.
				if ($item.hasClass('is-expanded')) {
					return;
				}

				$item.prop('open', false);
				$item.removeClass('is-active');
			};

			if (!$item.hasClass('is-expanded')) {
				settle();
				this.updateTitleState($item, false);
				return Promise.resolve();
			}

			$item.removeClass('is-expanded');
			this.updateTitleState($item, false);

			if (!this.isAnimated() || this.prefersReducedMotion()) {
				settle();
				return Promise.resolve();
			}

			return new Promise((resolve) => {
				this.whenContentSettled($content, () => {
					settle();
					resolve();
				});
			});
		},

		/**
		 * Run a callback once the panel height transition has finished.
		 *
		 * Only the height transition on the content wrapper counts: transitionend bubbles,
		 * so transitions on the panel itself or on any widget inside it would otherwise
		 * end the animation early.
		 *
		 * @param {jQuery}   $content Content wrapper.
		 * @param {Function} callback Invoked exactly once.
		 */
		whenContentSettled($content, callback) {
			if (!$content || !$content.length) {
				callback();
				return;
			}

			let done = false;
			let timer = null;

			const finish = () => {
				if (done) {
					return;
				}

				done = true;
				$content.off('transitionend' + EVENT_NS, onTransitionEnd);
				window.clearTimeout(timer);
				callback();
			};

			const onTransitionEnd = (event) => {
				const originalEvent = event.originalEvent || event;

				if (event.target !== $content[0] || 'grid-template-rows' !== originalEvent.propertyName) {
					return;
				}

				finish();
			};

			$content.on('transitionend' + EVENT_NS, onTransitionEnd);

			timer = window.setTimeout(finish, this.getContentDuration($content) + FALLBACK_BUFFER);
		},

		/**
		 * Measured transition time of the content wrapper, so the fallback timer cannot
		 * fire before the CSS animation is actually done.
		 *
		 * @param {jQuery} $content Content wrapper.
		 * @return {number} Duration in milliseconds.
		 */
		getContentDuration($content) {
			const fallback = this.getSettings('animationDuration');

			if (!$content || !$content.length || !window.getComputedStyle) {
				return fallback;
			}

			const styles = window.getComputedStyle($content[0]);

			const longest = (value) =>
				String(value || '')
					.split(',')
					.reduce((max, part) => {
						const trimmed = part.trim();
						const parsed = parseFloat(trimmed);

						if (isNaN(parsed)) {
							return max;
						}

						return Math.max(max, -1 === trimmed.indexOf('ms') ? parsed * 1000 : parsed);
					}, 0);

			const total = longest(styles.transitionDuration) + longest(styles.transitionDelay);

			return total > 0 ? total : fallback;
		},

		updateTitleState($item, isOpen) {
			const $title = $item.children(this.getSettings('selectors.title')).first();

			$title.attr('aria-expanded', isOpen ? 'true' : 'false');
			$title.attr('tabindex', isOpen ? '0' : '-1');
		},

		syncTitleStates() {
			this.elements.$items.each((index, element) => {
				const $item = $(element);
				const isOpen = $item.hasClass('is-expanded') || element.open;

				this.updateTitleState($item, isOpen);

				if (index === 0) {
					$item.children(this.getSettings('selectors.title')).attr('tabindex', '0');
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
				const titleId = itemId.replace('pixeccte-accordion-item-', 'pixeccte-accordion-title-');

				$item.attr('id', itemId);

				const $title = $item.find('.pixeccte-accordion__title');
				$title.attr('id', titleId);
				$title.attr('data-item-index', index + 1);
				$title.attr('aria-controls', itemId);

				const $label = $item.find('.pixeccte-accordion__label');
				$label.attr('data-binding-index', index + 1);

				const $panel = $item.find('.pixeccte-accordion__panel').first();
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
					const $inner = $targetItem.find('.pixeccte-accordion__content-inner').first();

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
		elementorFrontend.elementsHandler.attachHandler('pixeccte-accordion', AccordionHandler);
	});
})(jQuery);
