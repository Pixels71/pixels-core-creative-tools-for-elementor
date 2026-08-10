jQuery(document).ready(function ($) {
	var pixeccte_hf_hide_shortcode_field = function() {
		var selected = jQuery('#pixeccte_hf_template_type').val() || 'none';
		jQuery( '.pixeccte-theme-options-table' ).removeClass().addClass( 'pixeccte-theme-options-table widefat pixeccte-theme-selected-template-type-' + selected );
	}

	jQuery(document).on( 'change', '#pixeccte_hf_template_type', function( e ) {
		pixeccte_hf_hide_shortcode_field();
	});

	pixeccte_hf_hide_shortcode_field();
});
