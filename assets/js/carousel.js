(function ($) {
	'use strict';

	const PixelsCarouselHandler = elementorModules.frontend.handlers.Base.extend({
		getDefaultSettings() {
			return {
				selectors: {
					carousel: '.pixels-core-carousel__wrapper',
					slides: '.pixels-core-carousel__slides > .swiper-slide',
					prev: '.elementor-swiper-button-prev',
					next: '.elementor-swiper-button-next',
					pagination: '.swiper-pagination',
					thumbsWrapper: '.pixels-core-carousel__thumbs-wrapper',
					thumbs: '.pixels-core-carousel__thumbs > .swiper-slide',
				},
			};
		},

		getDefaultElements() {
			const selectors = this.getSettings('selectors');

			return {
				$carousel: this.$element.find(selectors.carousel),
				$slides: this.$element.find(selectors.slides),
				$prev: this.$element.find(selectors.prev),
				$next: this.$element.find(selectors.next),
				$pagination: this.$element.find(selectors.pagination),
				$thumbsWrapper: this.$element.find(selectors.thumbsWrapper),
				$thumbs: this.$element.find(selectors.thumbs),
			};
		},

		bindEvents() {
			this.elements.$prev.on('keydown', this.onDirectionArrowKeydown.bind(this));
			this.elements.$next.on('keydown', this.onDirectionArrowKeydown.bind(this));
			this.elements.$pagination.on('keydown', '.swiper-pagination-bullet', this.onDirectionArrowKeydown.bind(this));
			this.elements.$carousel.on('keydown', '.swiper-slide', this.onDirectionArrowKeydown.bind(this));
			this.$element.find(':focusable').on('focus', this.onFocusDisableAutoplay.bind(this));

			elementorFrontend.elements.$window.on(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		unbindEvents() {
			this.elements.$prev.off('keydown');
			this.elements.$next.off('keydown');
			this.elements.$pagination.off('keydown');
			this.elements.$carousel.off('keydown');
			this.$element.find(':focusable').off('focus');
			this.elements.$carousel.off('mouseenter mouseleave');

			elementorFrontend.elements.$window.off(
				'elementor/nested-container/atomic-repeater',
				this.onAtomicRepeater.bind(this)
			);
		},

		getResponsiveSlidesToShow(device = null) {
			const settings = this.getElementSettings();
			let value;

			if (device) {
				value = settings['slides_to_show_' + device];

				if (undefined === value || '' === value || null === value) {
					value = settings.slides_to_show;
				}
			} else {
				value = elementorFrontend.utils.controls.getResponsiveControlValue(
					settings,
					'slides_to_show',
					null,
					null
				);

				if (undefined === value || '' === value || null === value) {
					value = settings.slides_to_show;
				}
			}

			return Math.max(1, parseInt(value, 10) || 1);
		},

		getResponsiveThumbSlidesToShow(device = null) {
			const settings = this.getElementSettings();
			let value;

			if (device) {
				value = settings['thumb_slides_to_show_' + device];

				if (undefined === value || '' === value || null === value) {
					value = settings.thumb_slides_to_show;
				}
			} else {
				value = elementorFrontend.utils.controls.getResponsiveControlValue(
					settings,
					'thumb_slides_to_show',
					null,
					null
				);

				if (undefined === value || '' === value || null === value) {
					value = settings.thumb_slides_to_show;
				}
			}

			return Math.max(1, parseInt(value, 10) || 1);
		},

		getResponsiveSlidesToScroll(device = null) {
			const settings = this.getElementSettings();
			let value;

			if (device) {
				value = settings['slides_to_scroll_' + device];

				if (undefined === value || '' === value || null === value) {
					value = settings.slides_to_scroll;
				}
			} else {
				value = elementorFrontend.utils.controls.getResponsiveControlValue(
					settings,
					'slides_to_scroll',
					null,
					null
				);

				if (undefined === value || '' === value || null === value) {
					value = settings.slides_to_scroll;
				}
			}

			return Math.max(1, parseInt(value, 10) || 1);
		},

		getSpaceBetween(device = null) {
			const value = elementorFrontend.utils.controls.getResponsiveControlValue(
				this.getElementSettings(),
				'slide_gap',
				'size',
				device
			);

			return Number(value) || 0;
		},

		getThumbSpaceBetween(device = null) {
			const value = elementorFrontend.utils.controls.getResponsiveControlValue(
				this.getElementSettings(),
				'thumb_gap',
				'size',
				device
			);

			return Number(value) || 0;
		},

		isThumbGalleryEnabled() {
			return 'yes' === this.getElementSettings('thumb_gallery') && this.elements.$thumbsWrapper.length;
		},

		isVerticalThumbPosition() {
			const position = this.getElementSettings('thumb_position') || 'bottom';

			return 'left' === position || 'right' === position;
		},

		refreshElements() {
			const selectors = this.getSettings('selectors');

			this.elements.$carousel = this.$element.find(selectors.carousel);
			this.elements.$slides = this.$element.find(selectors.slides);
			this.elements.$prev = this.$element.find(selectors.prev);
			this.elements.$next = this.$element.find(selectors.next);
			this.elements.$pagination = this.$element.find(selectors.pagination);
			this.elements.$thumbsWrapper = this.$element.find(selectors.thumbsWrapper);
			this.elements.$thumbs = this.$element.find(selectors.thumbs);
		},

		getThumbsSwiperSettings() {
			const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;
			const isVertical = this.isVerticalThumbPosition();
			const thumbsToShow = this.getResponsiveThumbSlidesToShow();

			const settings = {
				slidesPerView: thumbsToShow,
				slidesPerGroup: 1,
				spaceBetween: this.getThumbSpaceBetween(),
				freeMode: true,
				watchSlidesProgress: true,
				slideToClickedSlide: true,
				direction: isVertical ? 'vertical' : 'horizontal',
				watchOverflow: true,
				handleElementorBreakpoints: true,
				breakpoints: {},
			};

			Object.keys(elementorBreakpoints)
				.reverse()
				.forEach((breakpointName) => {
					settings.breakpoints[elementorBreakpoints[breakpointName].value] = {
						slidesPerView: this.getResponsiveThumbSlidesToShow(breakpointName),
						spaceBetween: this.getThumbSpaceBetween(breakpointName),
					};
				});

			return settings;
		},

		getSwiperSettings() {
			const elementSettings = this.getElementSettings();
			const elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;
			const slidesToShow = this.getResponsiveSlidesToShow();
			const isSingleSlide = 1 === slidesToShow;
			const slidesCount = this.elements.$slides.length;

			const settings = {
				slidesPerView: slidesToShow,
				slidesPerGroup: isSingleSlide ? 1 : this.getResponsiveSlidesToScroll(),
				loop: 'yes' === elementSettings.infinite && slidesCount > slidesToShow,
				speed: parseInt(elementSettings.speed, 10) || 500,
				direction: 'vertical' === elementSettings.direction ? 'vertical' : 'horizontal',
				spaceBetween: this.getSpaceBetween(),
				handleElementorBreakpoints: true,
				watchOverflow: true,
				breakpoints: {},
			};

			Object.keys(elementorBreakpoints)
				.reverse()
				.forEach((breakpointName) => {
					const breakpointSlidesToShow = this.getResponsiveSlidesToShow(breakpointName);
					const breakpointIsSingle = 1 === breakpointSlidesToShow;

					settings.breakpoints[elementorBreakpoints[breakpointName].value] = {
						slidesPerView: breakpointSlidesToShow,
						slidesPerGroup: breakpointIsSingle
							? 1
							: this.getResponsiveSlidesToScroll(breakpointName),
						spaceBetween: this.getSpaceBetween(breakpointName),
					};
				});

			if ('yes' === elementSettings.autoplay) {
				settings.autoplay = {
					delay: parseInt(elementSettings.autoplay_speed, 10) || 5000,
					disableOnInteraction: 'yes' === elementSettings.pause_on_interaction,
				};
			}

			if (isSingleSlide && elementSettings.effect) {
				settings.effect = elementSettings.effect;

				if ('fade' === elementSettings.effect) {
					settings.fadeEffect = {
						crossFade: true,
					};
				}
			}

			const showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation;
			const showPagination = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation;

			if (showArrows && this.elements.$prev.length && this.elements.$next.length) {
				settings.navigation = {
					prevEl: this.elements.$prev[0],
					nextEl: this.elements.$next[0],
				};
			}

			if (showPagination && this.elements.$pagination.length) {
				const paginationType = elementSettings.pagination_type || 'bullets';
				const swiperPaginationType = 'progress' === paginationType ? 'progressbar' : paginationType;

				settings.pagination = {
					el: this.elements.$pagination[0],
					type: swiperPaginationType,
					clickable: 'bullets' === paginationType,
				};

				if ('bullets' === paginationType && 'yes' === elementSettings.pagination_dynamic_bullets) {
					settings.pagination.dynamicBullets = true;
				}

				if ('progress' === paginationType && 'vertical' === elementSettings.direction) {
					settings.pagination.progressbarOpposite = true;
				}
			}

			if (this.thumbsSwiper) {
				settings.thumbs = {
					swiper: this.thumbsSwiper,
				};
			}

			settings.a11y = {
				enabled: true,
				prevSlideMessage: elementorFrontend.config.i18n.a11yCarouselPrevSlideMessage,
				nextSlideMessage: elementorFrontend.config.i18n.a11yCarouselNextSlideMessage,
				firstSlideMessage: elementorFrontend.config.i18n.a11yCarouselFirstSlideMessage,
				lastSlideMessage: elementorFrontend.config.i18n.a11yCarouselLastSlideMessage,
			};

			return settings;
		},

		destroySwipers() {
			if (this.swiper) {
				this.swiper.destroy(true, true);
				this.swiper = null;
			}

			if (this.thumbsSwiper) {
				this.thumbsSwiper.destroy(true, true);
				this.thumbsSwiper = null;
			}
		},

		async initSwiper() {
			const Swiper = elementorFrontend.utils.swiper;

			if (this.isThumbGalleryEnabled() && this.elements.$thumbs.length) {
				this.thumbsSwiper = await new Swiper(
					this.elements.$thumbsWrapper,
					this.getThumbsSwiperSettings()
				);
			}

			this.swiper = await new Swiper(this.elements.$carousel, this.getSwiperSettings());
			this.elements.$carousel.data('swiper', this.swiper);
		},

		togglePauseOnHover(enable) {
			if (!this.swiper?.autoplay) {
				return;
			}

			this.elements.$carousel.off('mouseenter mouseleave');

			if (!enable) {
				return;
			}

			this.elements.$carousel.on({
				mouseenter: () => {
					this.swiper.autoplay.stop();
				},
				mouseleave: () => {
					this.swiper.autoplay.start();
				},
			});
		},

		async reinitSwiper() {
			this.destroySwipers();

			await this.waitForSlides();

			if (!this.elements.$carousel.length || !this.elements.$slides.length) {
				return;
			}

			await this.initSwiper();

			if ('yes' === this.getElementSettings('pause_on_hover')) {
				this.togglePauseOnHover(true);
			}
		},

		updateSpaceBetween(propertyName) {
			if (!this.swiper) {
				return;
			}

			const deviceMatch = propertyName.match(/slide_gap_(.*)/);
			const device = deviceMatch ? deviceMatch[1] : 'desktop';
			const newSpaceBetween = this.getSpaceBetween(device);

			if ('desktop' !== device) {
				const breakpoint = elementorFrontend.config.responsive.activeBreakpoints[device];

				if (breakpoint && this.swiper.params.breakpoints?.[breakpoint.value]) {
					this.swiper.params.breakpoints[breakpoint.value].spaceBetween = newSpaceBetween;
				}
			}

			this.swiper.params.spaceBetween = newSpaceBetween;
			this.swiper.update();
		},

		updateThumbSlidesToShow(propertyName) {
			if (!this.thumbsSwiper) {
				return;
			}

			const deviceMatch = propertyName.match(/thumb_slides_to_show_(.*)/);
			const device = deviceMatch ? deviceMatch[1] : 'desktop';
			const newSlidesPerView = this.getResponsiveThumbSlidesToShow(device);
			const newSpaceBetween = this.getThumbSpaceBetween(device);

			if ('desktop' !== device) {
				const breakpoint = elementorFrontend.config.responsive.activeBreakpoints[device];

				if (breakpoint && this.thumbsSwiper.params.breakpoints?.[breakpoint.value]) {
					this.thumbsSwiper.params.breakpoints[breakpoint.value].slidesPerView = newSlidesPerView;
					this.thumbsSwiper.params.breakpoints[breakpoint.value].spaceBetween = newSpaceBetween;
				}
			}

			this.thumbsSwiper.params.slidesPerView = this.getResponsiveThumbSlidesToShow();
			this.thumbsSwiper.params.spaceBetween = this.getThumbSpaceBetween();
			this.thumbsSwiper.update();
		},

		updateThumbGap(propertyName) {
			this.updateThumbSlidesToShow(propertyName.replace('thumb_gap', 'thumb_slides_to_show'));
		},

		waitForSlides() {
			return new Promise((resolve) => {
				const attemptFindSlides = (attempt = 0) => {
					this.refreshElements();

					if (this.elements.$slides.length || attempt >= 25) {
						resolve();
						return;
					}

					setTimeout(() => attemptFindSlides(attempt + 1), 100);
				};

				attemptFindSlides();
			});
		},

		onDirectionArrowKeydown(event) {
			if (!this.swiper) {
				return;
			}

			const isRTL = elementorFrontend.config.is_rtl;
			const key = event.originalEvent?.code || event.key;
			const directionStart = isRTL ? 'ArrowRight' : 'ArrowLeft';
			const directionEnd = isRTL ? 'ArrowLeft' : 'ArrowRight';

			if (directionStart === key) {
				event.preventDefault();
				this.swiper.slidePrev();
			} else if (directionEnd === key) {
				event.preventDefault();
				this.swiper.slideNext();
			}
		},

		onFocusDisableAutoplay() {
			if (this.swiper?.autoplay) {
				this.swiper.autoplay.stop();
			}
		},

		onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
			this.reinitSwiper();
		},

		onDestroy() {
			this.destroySwipers();

			elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
		},

		onAtomicRepeater(event) {
			const { container } = event.detail;

			if (!container || container.model.get('id') !== this.getID()) {
				return;
			}

			setTimeout(() => {
				this.reinitSwiper();
			}, 150);
		},

		onElementChange(propertyName) {
			if (0 === propertyName.indexOf('slide_gap')) {
				this.updateSpaceBetween(propertyName);
				return;
			}

			if (0 === propertyName.indexOf('thumb_slides_to_show')) {
				this.updateThumbSlidesToShow(propertyName);
				return;
			}

			if (0 === propertyName.indexOf('thumb_gap')) {
				this.updateThumbGap(propertyName);
				return;
			}

			if ('pause_on_hover' === propertyName) {
				this.togglePauseOnHover('yes' === this.getElementSettings('pause_on_hover'));
				return;
			}

			if (
				0 === propertyName.indexOf('slides_to_show') ||
				0 === propertyName.indexOf('slides_to_scroll') ||
				'direction' === propertyName ||
				'infinite' === propertyName ||
				'effect' === propertyName ||
				'speed' === propertyName ||
				'autoplay' === propertyName ||
				'autoplay_speed' === propertyName ||
				'pause_on_interaction' === propertyName ||
				'navigation' === propertyName ||
				'pagination_type' === propertyName ||
				'pagination_dynamic_bullets' === propertyName ||
				'thumb_gallery' === propertyName ||
				'thumb_position' === propertyName ||
				'slides' === propertyName ||
				0 === propertyName.indexOf('thumb_gallery_image')
			) {
				this.reinitSwiper();
			}
		},
	});

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.elementsHandler.attachHandler('pixels-core-carousel', PixelsCarouselHandler);
	});
})(jQuery);
