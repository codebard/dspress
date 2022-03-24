jQuery.noConflict(); // Reverts '$' variable back to other JS libraries

jQuery(document).ready(function($) {

	jQuery('body').on("click",'.cb_p8_admin_toggle_button',function(e) {
		target_div = document.getElementById($(this).attr( 'target' ));
		jQuery(target_div).fadeToggle(1800);
	});
	
	jQuery(document).on( 'click', '.cb_p8_notice .notice-dismiss', function(e) {

	
		jQuery.ajax({
			url: ajaxurl,
			type:"POST",
			dataType : 'html',
			data: {
				action: 'cb_p8_dismiss_admin_notice',
				notice_id: jQuery(this).parent().attr("id"),
				notice_type: jQuery(this).parent().attr("notice_type"),
			}
		});

	});
	
	
	jQuery(document).on('click', '.cb_p8_file_upload', function(e) {		
		var cb_p8_input_target = jQuery(this);
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
             cb_p8_input_target.val(image_url);
			 
        });
    });
	

	
	jQuery(document).on( 'click', '.cb_p8_notice .notice-dismiss', function(e) {

		jQuery.ajax({
			url: ajaxurl,
			type:"POST",
			dataType : 'html',
			data: {
				action: 'cb_p8_dismiss_admin_notice',
				notice_id: jQuery( this ).parent().attr( "id" ),
			}
		});
	});	
	
	jQuery(document).on('click', '.cb_p8_clear_prevfield', function(e) {
		e.preventDefault();
		
		jQuery(this).prev().val('');
	
	});		
  
});