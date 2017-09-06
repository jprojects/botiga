jQuery(document).ready(function() {

	jQuery('#finishCart').change(function() {
		if(jQuery('#processor').val() != '') {  
		    jQuery('.submit').removeAttr('disabled');  
		} else {  
		    jQuery('.submit').attr('disabled', 'true');  
		} 
	});
	
	$('.tos').click(function() {
		if($(this).is(':checked')) {  
            $('.submit').removeAttr('disabled');  
        } else {  
            $('.submit').attr('disabled', 'disabled');  
        } 
    });
});
