jQuery(document).ready(function ($) {
	var pixels_hf_hide_shortcode_field = function() {
		var selected = jQuery('#ehf_template_type').val() || 'none';
		jQuery( '.pixels-theme-options-table' ).removeClass().addClass( 'pixels-theme-options-table widefat pixels-theme-selected-template-type-' + selected );
	}

	jQuery(document).on( 'change', '#ehf_template_type', function( e ) {
		pixels_hf_hide_shortcode_field();
	});

	pixels_hf_hide_shortcode_field();
});
