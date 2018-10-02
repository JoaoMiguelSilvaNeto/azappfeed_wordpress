/**
 * Feedier plugin Saving process
 */
jQuery( document ).ready( function () {
 
    jQuery( document ).on( 'submit', '#azappfeed-admin-form', function ( e ) {
 
        e.preventDefault();
 
        // We inject some extra fields required for the security
        jQuery(this).append('<input type="hidden" name="action" value="store_admin_data">');
        jQuery(this).append('<input type="hidden" name="security" value="'+ azappfeed_exchanger._nonce +'">');
		
		// We make our call
        jQuery.ajax( {
            url: azappfeed_exchanger.ajax_url,
            type: 'post',
            data: jQuery(this).serialize(),
            success: function ( response ) {
                alert(response);
            }
        } );
 
	});
});