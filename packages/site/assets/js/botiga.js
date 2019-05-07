jQuery(document).ready(function() {

	var quantitiy = 0;
	
	jQuery('#finishCart').change(function() {
		if(jQuery('#processor').val() != '') {  
		    jQuery('.submit').removeAttr('disabled');  
		} else {  
		    jQuery('.submit').attr('disabled', 'true');  
		} 
	});
	
	jQuery('#variacions').change(function() {
		
		var id 	= jQuery(this).val();
		
		document.location.href = 'index.php?option=com_botiga&view=item&id='+id;
		
	});
	
   jQuery('.quantity-right-plus').click(function(e) {
        
        var id = jQuery(this).attr('data-id');

        var quantity = parseInt(jQuery('#quantity_'+id).val());
            
        jQuery('#quantity_'+id).val(quantity + 1);
        
    });

    jQuery('.quantity-left-minus').click(function(e) {
        
        var id = jQuery(this).attr('data-id');

        var quantity = parseInt(jQuery('#quantity_'+id).val());

        if(quantity > 0){
            jQuery('#quantity_'+id).val(quantity - 1);
        }
    });
	
});
