jQuery(document).ready(function() {

	jQuery('#finishCart').change(function() {
		if(jQuery('#processor').val() != '') {  
		    jQuery('.submit').removeAttr('disabled');  
		} else {  
		    jQuery('.submit').attr('disabled', 'true');  
		} 
	});
	
	jQuery('.tos').click(function() {
		if(jQuery(this).is(':checked')) {  
            jQuery('.submit').removeAttr('disabled');  
        } else {  
            jQuery('.submit').attr('disabled', 'disabled');  
        } 
    });
	
	jQuery('.zoom').hover(function() {
		jQuery('.botiga-btns').hide();
		jQuery(this).find('.botiga-btns').show();
	});		

});
