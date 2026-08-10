(function ($) {
	'use strict';

	const enabledWidgets = window.pixeccteEditor?.enabledNestedWidgets || [];

	const registerPixelsTabs = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsTabsView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			applyPanelAttributes(childView) {
				const $widget = childView._parent.$el.find('.pixeccte-tabs');
				const widgetNumber = $widget.data('widget-number');
				const index = childView.model.attributes.dataIndex;
				const $tabTitle = childView._parent.$el.find(
					`.pixeccte-tabs__title[data-tab="${index}"]`
				);
				const defaultActive = parseInt($widget.data('default-tab'), 10) || 1;
				const isInitialLoad = elementor.previewView.isBuffering;
				const isActive = isInitialLoad ? index === defaultActive : $tabTitle.hasClass('is-active');

				childView.$el.addClass('pixeccte-tabs__panel');

				if (isActive) {
					childView.$el.addClass('is-active');
				}

				childView.$el.attr({
					id: `pixeccte-tab-content-${widgetNumber}${index}`,
					role: 'tabpanel',
					'aria-labelledby': $tabTitle.attr('id') || `pixeccte-tab-title-${widgetNumber}${index}`,
					'data-tab': index,
					'data-tab-index': index,
				});

				if (!isActive) {
					childView.$el.attr('hidden', 'hidden');
				} else {
					childView.$el.removeAttr('hidden');
				}
			}

			onAddChild(childView) {
				this.applyPanelAttributes(childView);
			}
		}

		class PixelsTabsElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-tabs';
			}

			getView() {
				return PixelsTabsView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsTabsElementType());
	};

	const registerPixelsAccordion = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsAccordionView extends NestedView {
			onAddChild(childView) {
				const $summary = childView._parent.$el.find('summary').first();
				const titleId = $summary.attr('id');

				childView.$el.addClass('pixeccte-accordion__panel');
				childView.$el.attr({
					role: 'region',
					'aria-labelledby': titleId,
				});
			}
		}

		class PixelsAccordionElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-accordion';
			}

			getView() {
				return PixelsAccordionView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsAccordionElementType());
	};

	const registerPixelsCarousel = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsCarouselView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			onAddChild(childView) {
				childView.$el.addClass('swiper-slide pixeccte-carousel__slide');
				childView.$el.attr({
					role: 'group',
					'aria-roledescription': 'slide',
				});
			}
		}

		class PixelsCarouselElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-carousel';
			}

			getView() {
				return PixelsCarouselView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsCarouselElementType());
	};

	const registerPixelsStackCard = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsStackCardView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			getChildViewContainer(containerView, childView) {
				const defaults = this.model.config.defaults;

				if (childView?._index !== undefined) {
					const $wrapper = containerView.$el
						.find('.pixeccte-stack-card__card-wrapper')
						.eq(childView._index);

					if ($wrapper.length) {
						return $wrapper;
					}
				}

				if (defaults.elements_placeholder_selector) {
					return containerView.$el.find(defaults.elements_placeholder_selector);
				}

				return NestedView.prototype.getChildViewContainer.apply(this, arguments);
			}

			attachBuffer(compositeView, buffer) {
				const $container = this.getChildViewContainer(compositeView);

				if (
					this.model?.config?.support_improved_repeaters &&
					this.model?.config?.is_interlaced
				) {
					const $wrappers = $container.find('.pixeccte-stack-card__card-wrapper');

					$wrappers.each(function () {
						if (buffer.childNodes.length) {
							this.appendChild(buffer.childNodes[0]);
						}
					});

					return;
				}

				$container.append(buffer);
			}

			onAddChild(childView) {
				childView.$el.addClass('pixeccte-stack-card__card-inner');
			}
		}

		class PixelsStackCardElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-stack-card';
			}

			getView() {
				return PixelsStackCardView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsStackCardElementType());
	};

	const registerPixelsTimeline = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsTimelineView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			getChildViewContainer(containerView, childView) {
				const defaults = this.model.config.defaults;

				if (childView?._index !== undefined) {
					const $item = containerView.$el
						.find('.pixeccte-timeline__item')
						.eq(childView._index);
					const $card = $item.find('.pixeccte-timeline__card');

					if ($card.length) {
						return $card;
					}
				}

				if (defaults.elements_placeholder_selector) {
					return containerView.$el.find(defaults.elements_placeholder_selector);
				}

				return NestedView.prototype.getChildViewContainer.apply(this, arguments);
			}

			attachBuffer(compositeView, buffer) {
				const $container = this.getChildViewContainer(compositeView);

				if (
					this.model?.config?.support_improved_repeaters &&
					this.model?.config?.is_interlaced
				) {
					const $cards = $container.find('.pixeccte-timeline__card');

					$cards.each(function () {
						if (buffer.childNodes.length) {
							this.appendChild(buffer.childNodes[0]);
						}
					});

					return;
				}

				$container.append(buffer);
			}

			onAddChild(childView) {
				childView.$el.addClass('pixeccte-timeline__card-inner');
			}
		}

		class PixelsTimelineElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-timeline';
			}

			getView() {
				return PixelsTimelineView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsTimelineElementType());
	};

	const registerPixelsMarquee = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsMarqueeView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			onAddChild(childView) {
				childView.$el.addClass('pixeccte-marquee__item');
			}
		}

		class PixelsMarqueeElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-marquee';
			}

			getView() {
				return PixelsMarqueeView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsMarqueeElementType());
	};

	const registerPixelsExpandingCard = () => {
		const NestedElementBase = elementor.modules.elements.types.NestedElementBase;
		const NestedView = $e.components.get('nested-elements').exports.NestedView;

		if (!NestedElementBase || !NestedView) {
			return;
		}

		class PixelsExpandingCardView extends NestedView {
			filter(child, index) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			onAddChild(childView) {
				const index = childView.model.attributes.dataIndex;
				const $widget = childView._parent.$el.find('.pixeccte-expanding-card');
				const defaultActive =
					Math.max(0, (parseInt($widget.data('active-index'), 10) || 0));

				childView.$el.addClass('pixeccte-expanding-card__item');
				childView.$el.attr('data-card-index', index - 1);

				if (index - 1 === defaultActive) {
					childView.$el.addClass('is-active');
				}
			}
		}

		class PixelsExpandingCardElementType extends NestedElementBase {
			getType() {
				return 'pixeccte-expanding-card';
			}

			getView() {
				return PixelsExpandingCardView;
			}
		}

		elementor.elementsManager.registerElementType(new PixelsExpandingCardElementType());
	};

	const registry = {
		tabs: registerPixelsTabs,
		accordion: registerPixelsAccordion,
		carousel: registerPixelsCarousel,
		marquee: registerPixelsMarquee,
		timeline: registerPixelsTimeline,
		stack_card: registerPixelsStackCard,
		expanding_card: registerPixelsExpandingCard,
	};

	$(window).on('elementor/nested-element-type-loaded', () => {
		enabledWidgets.forEach((slug) => {
			if (typeof registry[slug] === 'function') {
				registry[slug]();
			}
		});
	});
})(jQuery);
