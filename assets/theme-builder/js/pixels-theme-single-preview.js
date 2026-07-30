(function ($) {
	'use strict';

	let previewBindingAdded = false;
	let isReady = false;

	function getPageSettingsModel() {
		if (!window.elementor || !elementor.settings || !elementor.settings.page) {
			return null;
		}

		return elementor.settings.page.model || null;
	}

	function reloadPreviewWithSave() {
		if (!isReady || !window.$e || !window.elementor) {
			return;
		}

		$e.run('document/save/auto', { force: true }).then(function () {
			elementor.reloadPreview();
		});
	}

	function bindPreviewPostChange() {
		const model = getPageSettingsModel();

		if (!model || previewBindingAdded) {
			return;
		}

		model.on('change:pixels_preview_post_id', function (model, value) {
			const previous = model.previous('pixels_preview_post_id');

			if (String(previous || '') === String(value || '')) {
				return;
			}

			reloadPreviewWithSave();
		});

		previewBindingAdded = true;
	}

	function markReady() {
		window.setTimeout(function () {
			isReady = true;
		}, 500);
	}

	$(function () {
		if (!window.elementor) {
			return;
		}

		elementor.on('panel:init', function () {
			bindPreviewPostChange();
			markReady();
		});

		elementor.on('document:loaded', function () {
			bindPreviewPostChange();
			markReady();
		});
	});
})(jQuery);
