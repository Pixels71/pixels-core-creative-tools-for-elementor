(function($){

	Pixeccte_Theme_Elementor_Pro_Compatibility = {

        /**
		 * Binds events for the Elementor Header Footer.
		 * @access private
		 * @method _bind
		 */
		init: function() {
			elementor.on( "document:loaded", function() {
                setTimeout( function() {
                    jQuery.each( elementorFrontend.documentsManager.documents, function ( index, document ) {
                        var $documentElement = document.$element;
                        var ids_array = JSON.parse( pixeccte_hf_admin.ids_array );
                        ids_array.forEach( function( item, index ){
                        	var elementor_id = $documentElement.data( 'elementor-id' );
                        	if( elementor_id == ids_array[index].id ){
                        		$documentElement.find( '.elementor-document-handle__title' ).text( elementor.translate('edit_element', [ids_array[index].value] ) );
                        	}
                        } );
                    });
                }, 1000 );
            });
		}
	};

	/**
	 * Initialize Pixeccte_Theme_Elementor_Pro_Compatibility
	 */
	$(function(){
		Pixeccte_Theme_Elementor_Pro_Compatibility.init();
	});

})(jQuery);
