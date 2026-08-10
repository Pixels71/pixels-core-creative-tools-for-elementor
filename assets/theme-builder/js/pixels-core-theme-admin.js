jQuery(document).ready(function ($) {
	var pixels_core_hf_hide_shortcode_field = function() {
		var selected = jQuery('#pixels_core_hf_template_type').val() || 'none';
		jQuery( '.pixels-core-theme-options-table' ).removeClass().addClass( 'pixels-core-theme-options-table widefat pixels-core-theme-selected-template-type-' + selected );
	}

	jQuery(document).on( 'change', '#pixels_core_hf_template_type', function( e ) {
		pixels_core_hf_hide_shortcode_field();
	});

	pixels_core_hf_hide_shortcode_field();
});
