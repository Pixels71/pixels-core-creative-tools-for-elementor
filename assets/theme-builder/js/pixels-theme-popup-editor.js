(function ($) {
	'use strict';

	const popupClassPrefix = 'pixels-theme-popup-';

	function getPreviewDocument() {
		if (!window.elementor || !elementor.$previewContents || !elementor.$previewContents[0]) {
			return null;
		}

		return elementor.$previewContents[0];
	}

	function getModel() {
		if (!window.elementor || !elementor.settings || !elementor.settings.page) {
			return null;
		}

		return elementor.settings.page.model || null;
	}

	function dimensionToCss(value, fallback) {
		if (!value || typeof value !== 'object' || value.size === undefined || value.size === '') {
			return fallback;
		}

		return `${value.size}${value.unit || 'px'}`;
	}

	function getCurrentDeviceMode() {
		if (!window.elementor || !elementor.channels || !elementor.channels.deviceMode) {
			return 'desktop';
		}

		if (typeof elementor.channels.deviceMode.request === 'function') {
			return elementor.channels.deviceMode.request('currentMode') || 'desktop';
		}

		return 'desktop';
	}

	function getResponsiveValue(model, key) {
		const deviceMode = getCurrentDeviceMode();
		let responsiveKey = key;

		if (deviceMode === 'tablet' || deviceMode === 'mobile') {
			responsiveKey = `${key}_${deviceMode}`;
		}

		return model.get(responsiveKey) || model.get(key);
	}

	function horizontalPositionToFlex(value) {
		if (value === 'left' || value === 'flex-start') {
			return 'flex-start';
		}

		if (value === 'right' || value === 'flex-end') {
			return 'flex-end';
		}

		return 'center';
	}

	function verticalPositionToFlex(value) {
		if (value === 'top' || value === 'flex-start') {
			return 'flex-start';
		}

		if (value === 'bottom' || value === 'flex-end') {
			return 'flex-end';
		}

		return 'center';
	}

	function spacingToCss(value) {
		if (!value || typeof value !== 'object') {
			return '0px 0px 0px 0px';
		}

		const unit = value.unit || 'px';

		return `${value.top || 0}${unit} ${value.right || 0}${unit} ${value.bottom || 0}${unit} ${value.left || 0}${unit}`;
	}

	function boxShadowLengthToCss(value) {
		if (value && typeof value === 'object') {
			return dimensionToCss(value, '0px');
		}

		if (value === undefined || value === null || value === '') {
			return '0px';
		}

		if (typeof value === 'string' && /^-?\d+(\.\d+)?(px|em|rem)$/.test(value)) {
			return value;
		}

		return `${parseFloat(value) || 0}px`;
	}

	function boxShadowToCss(shadow, type) {
		if (type !== 'yes' || !shadow || typeof shadow !== 'object') {
			return 'none';
		}

		const position = shadow.position === 'inset' ? 'inset ' : '';
		const horizontal = boxShadowLengthToCss(shadow.horizontal);
		const vertical = boxShadowLengthToCss(shadow.vertical);
		const blur = boxShadowLengthToCss(shadow.blur);
		const spread = boxShadowLengthToCss(shadow.spread);
		const color = shadow.color || 'rgba(0, 0, 0, 0.5)';

		return `${position}${horizontal} ${vertical} ${blur} ${spread} ${color}`;
	}

	function applyCloseButtonPreview(closeButton, model) {
		const placement = model.get('pixels_popup_close_button_placement') || 'inside';
		const horizontal = model.get('pixels_popup_close_button_horizontal_position') || 'right';
		const vertical = model.get('pixels_popup_close_button_vertical_position') || 'top';
		const offsetX = dimensionToCss(model.get('pixels_popup_close_button_offset_x'), '11px');
		const offsetY = dimensionToCss(model.get('pixels_popup_close_button_offset_y'), '11px');
		const positionX = placement === 'outside' ? `calc(${offsetX} * -1)` : offsetX;
		const positionY = placement === 'outside' ? `calc(${offsetY} * -1)` : offsetY;

		closeButton.style.left = 'auto';
		closeButton.style.right = 'auto';
		closeButton.style.top = 'auto';
		closeButton.style.bottom = 'auto';
		closeButton.style[horizontal === 'left' ? 'left' : 'right'] = positionX;
		closeButton.style[vertical === 'bottom' ? 'bottom' : 'top'] = positionY;
		closeButton.style.width = dimensionToCss(model.get('pixels_popup_close_button_size'), '18px');
		closeButton.style.height = dimensionToCss(model.get('pixels_popup_close_button_size'), '18px');
		closeButton.style.fontSize = dimensionToCss(model.get('pixels_popup_close_button_icon_size'), '20px');
		closeButton.style.color = model.get('pixels_popup_close_button_color') || '#303030';
		closeButton.style.background = model.get('pixels_popup_close_button_background_color') || 'transparent';
		closeButton.style.borderStyle = 'solid';
		closeButton.style.borderColor = model.get('pixels_popup_close_button_border_color') || 'transparent';
		closeButton.style.borderWidth = dimensionToCss(model.get('pixels_popup_close_button_border_width'), '0px');
		closeButton.style.borderRadius = dimensionToCss(model.get('pixels_popup_close_button_border_radius'), '0px');
	}

	function replaceClassByPrefix(element, prefix, nextClass) {
		Array.from(element.classList).forEach(function (className) {
			if (className.indexOf(prefix) === 0) {
				element.classList.remove(className);
			}
		});

		if (nextClass) {
			element.classList.add(nextClass);
		}
	}

	function applyPopupPreview() {
		const previewDocument = getPreviewDocument();
		const model = getModel();

		if (!previewDocument || !model) {
			return;
		}

		const popup = previewDocument.querySelector('.pixels-theme-popup-editor-preview');
		const dialog = previewDocument.querySelector('.pixels-theme-popup__dialog');
		const overlay = previewDocument.querySelector('.pixels-theme-popup__overlay');
		const content = previewDocument.querySelector('.pixels-theme-popup__content');
		const closeButton = previewDocument.querySelector('.pixels-theme-popup__close');

		if (!popup || !dialog || !content) {
			return;
		}

		const heightType = model.get('pixels_popup_height_type') || 'fit';
		const horizontal = getResponsiveValue(model, 'pixels_popup_horizontal_position') || 'center';
		const vertical = getResponsiveValue(model, 'pixels_popup_vertical_position') || 'center';
		const entrance = model.get('pixels_popup_entrance_animation') || 'default';
		const exit = model.get('pixels_popup_exit_animation') || 'default';

		dialog.style.setProperty('--pixels-popup-width', dimensionToCss(getResponsiveValue(model, 'pixels_popup_width'), '420px'));
		dialog.style.setProperty('--pixels-popup-height', heightType === 'custom' ? dimensionToCss(model.get('pixels_popup_height'), 'auto') : 'auto');
		dialog.style.setProperty('--pixels-popup-margin', spacingToCss(model.get('pixels_popup_margin')));
		content.style.setProperty('--pixels-popup-padding', spacingToCss(model.get('pixels_popup_padding')));
		content.style.setProperty('--pixels-popup-content-background-color', model.get('pixels_popup_content_background_color') || '#fff');
		content.style.setProperty('--pixels-popup-content-border-color', model.get('pixels_popup_content_border_color') || '#dcdcdc');
		content.style.setProperty('--pixels-popup-content-border-style', model.get('pixels_popup_content_border_style') || 'dashed');
		content.style.setProperty('--pixels-popup-content-border-width', dimensionToCss(model.get('pixels_popup_content_border_width'), '1px'));
		content.style.setProperty('--pixels-popup-content-border-radius', dimensionToCss(model.get('pixels_popup_content_border_radius'), '0px'));
		content.style.setProperty(
			'--pixels-popup-content-box-shadow',
			boxShadowToCss(
				model.get('pixels_popup_content_box_shadow_box_shadow'),
				model.get('pixels_popup_content_box_shadow_box_shadow_type')
			)
		);
		popup.style.justifyContent = horizontalPositionToFlex(horizontal);
		popup.style.alignItems = verticalPositionToFlex(vertical);

		replaceClassByPrefix(popup, `${popupClassPrefix}h-`, `${popupClassPrefix}h-${horizontal}`);
		replaceClassByPrefix(popup, `${popupClassPrefix}v-`, `${popupClassPrefix}v-${vertical}`);
		replaceClassByPrefix(popup, `${popupClassPrefix}enter-`, `${popupClassPrefix}enter-${entrance}`);
		replaceClassByPrefix(popup, `${popupClassPrefix}exit-`, `${popupClassPrefix}exit-${exit}`);

		if (overlay) {
			overlay.style.display = model.get('pixels_popup_show_overlay') === 'yes' ? '' : 'none';
			overlay.style.setProperty('--pixels-popup-overlay-color', model.get('pixels_popup_overlay_color') || 'rgba(0, 0, 0, 0.78)');
		}

		if (closeButton) {
			applyCloseButtonPreview(closeButton, model);
			closeButton.style.display = model.get('pixels_popup_show_close_button') === 'yes' ? '' : 'none';
		}

		if (popup.dataset.customClasses) {
			popup.dataset.customClasses.split(/\s+/).forEach(function (className) {
				if (className) {
					popup.classList.remove(className);
				}
			});
		}

		const customClasses = (model.get('pixels_popup_css_classes') || '').trim();

		if (customClasses) {
			customClasses.split(/\s+/).forEach(function (className) {
				if (className) {
					popup.classList.add(className);
				}
			});
		}

		popup.dataset.customClasses = customClasses;

		const triggers = [
			'page_load',
			'scroll',
			'scroll_to',
			'class_click',
			'click',
			'inactivity',
			'exit_intent',
			'adblock',
		].filter(function (key) {
			return model.get(`pixels_popup_trigger_${key}`) === 'yes';
		});

		popup.dataset.triggers = triggers.join(',');
		popup.dataset.trigger = triggers.indexOf('page_load') !== -1 ? 'page_load' : 'manual';
		popup.dataset.delay = model.get('pixels_popup_delay') || '0';
		popup.dataset.pageLoadDelay = model.get('pixels_popup_page_load_delay') || '0';
		popup.dataset.scrollDirection = model.get('pixels_popup_scroll_direction') || 'down';
		popup.dataset.scrollPercent = model.get('pixels_popup_scroll_percent') || '50';
		popup.dataset.scrollToSelector = model.get('pixels_popup_scroll_to_selector') || '';
		popup.dataset.classClickSelector = model.get('pixels_popup_class_click_selector') || '';
		popup.dataset.clickCount = model.get('pixels_popup_click_count') || '1';
		popup.dataset.inactivityDelay = model.get('pixels_popup_inactivity_delay') || '30';
		popup.dataset.adblockDelay = model.get('pixels_popup_adblock_delay') || '0';
		popup.dataset.autoCloseAfter = model.get('pixels_popup_auto_close_after') || '';
		popup.dataset.closeButtonDelay = model.get('pixels_popup_close_button_delay') || '';
		popup.dataset.preventOverlayClose = model.get('pixels_popup_prevent_overlay_close') === 'yes' ? 'yes' : 'no';
		popup.dataset.preventEscClose = model.get('pixels_popup_prevent_esc_close') === 'yes' ? 'yes' : 'no';
		popup.dataset.disableScroll = model.get('pixels_popup_disable_scroll') === 'yes' ? 'yes' : 'no';
		popup.dataset.avoidMultiple = model.get('pixels_popup_avoid_multiple') === 'yes' ? 'yes' : 'no';
		popup.dataset.accessibleNavigation = model.get('pixels_popup_accessible_navigation') === 'yes' ? 'yes' : 'no';
		popup.dataset.openSelector = model.get('pixels_popup_open_selector') || '';
	}

	function bindModelChanges() {
		const model = getModel();

		if (!model || model.pixelsPopupLivePreviewBound) {
			applyPopupPreview();
			return;
		}

		model.pixelsPopupLivePreviewBound = true;
		model.on('change', function (changedModel) {
			const changed = changedModel.changed || {};
			const shouldUpdate = Object.keys(changed).some(function (key) {
				return key.indexOf('pixels_popup_') === 0;
			});

			if (shouldUpdate) {
				applyPopupPreview();
			}
		});

		applyPopupPreview();
	}

	function init() {
		elementor.on('document:loaded preview:loaded', function () {
			window.setTimeout(bindModelChanges, 100);
		});

		if (elementor.channels && elementor.channels.deviceMode) {
			elementor.channels.deviceMode.on('change', applyPopupPreview);
		}

		window.setTimeout(bindModelChanges, 100);
	}

	if (window.elementor) {
		init();
	} else {
		$(window).on('elementor:init', init);
	}
})(jQuery);
