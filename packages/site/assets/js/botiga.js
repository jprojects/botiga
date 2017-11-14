jQuery(document).ready(function() {

	jQuery('#finishCart').change(function() {
		if(jQuery('#processor').val() != '') {  
		    jQuery('.submit').removeAttr('disabled');  
		} else {  
		    jQuery('.submit').attr('disabled', 'true');  
		} 
	});
	
	jQuery('.zoom').hover(function() {
		jQuery('.botiga-btns').hide();
		jQuery(this).find('.botiga-btns').show();
	});
	
	jQuery('.setItem').click(function(e) {
		e.preventDefault();
		var id = jQuery(this).attr('data-id');
		jQuery.ajax({
			url: "index.php?option=com_botiga&task=botiga.setItemAjax&id="+id+"&tmpl=raw", 
			type:  'post',
			dataType: 'json',
			success: function(result) {
			
				//open dropdown
				jQuery('.carrito-list').addClass('open');
				
				//calculate new row total
        		var new_price = parseFloat(result.price);
        		new_price = Math.round(new_price * 100) / 100;
				
				//add item to cart
				if(jQuery('.row-'+id).length) {
					//edit row
					var qty       = parseInt(jQuery('.row-'+id).find('.cart-qty').html());
					var price     = parseFloat(jQuery('.row-'+id).find('.cart-price').html());
					var new_qty   = qty + 1;
					jQuery('.row-'+id).find('.cart-qty').html(new_qty);
				} else {
					//badge counter
        			var count = parseInt(jQuery('.cart-count').html())+1;
        			jQuery('.cart-count').html(count);    
        			//add new row   			
        			jQuery('.carrito').append('<tr class="row-'+result.idItem+'" id="row-'+id+'"><td><img src="'+result.imagen+'" class="img-responsive mini" alt="'+result.nombre+'" /></td><td>'+result.nombre+'<br/><div class="bold blue text-right">x<span class="cart-qty">'+result.qty+'</span> - <span class="cart-price">'+result.price+'</span> &euro;</div></td><td><a href="#" class="cart-delete" data-id="'+id+'" data-price="'+new_price+'"><i class="fa fa-trash-o"></i></a></td></tr>');
        		}
        		
        		//add price to total
        		var total = jQuery('#total').html();
        		var new_total = parseFloat(total) + parseFloat(new_price);
        		new_total = Math.round(new_total * 100) / 100;
        		jQuery('#total').html(new_total);
    		},
    		error: function(result) {
            	console.log(result.id);
        	}
    	});
    	setTimeout(function() { 
    		//close dropdown
        	jQuery('.carrito-list').removeClass('open'); 
        }, 1000);
	});	
	
	jQuery('.cart-delete').on('click', function(e) {
		e.preventDefault();
		var id = jQuery(this).attr('data-id');
		var price = parseFloat(jQuery(this).attr('data-price'));
		var total = parseFloat(jQuery('#total').html());
		var count = parseInt(jQuery('.cart-count').html() -1);
		jQuery.get('index.php?option=com_botiga&task=botiga.removeItem&id='+id+'&tmpl=raw');
		jQuery('#row-'+id).hide();
		jQuery('.cart-count').html(count);
		var new_price = Math.round((total - price) * 100) / 100;
		jQuery('#total').html(new_price);
	});
	
});

jQuery(document).on('click', ".setFavorite", function(e) {
	e.preventDefault();
	var id = jQuery(this).attr('data-id');
	jQuery.ajax({
		url: "index.php?option=com_botiga&task=setFavoriteAjax&id="+id+"&tmpl=raw", 
		type:  'post',
		dataType: 'json',
		success: function(result) {
			jQuery('.item'+id).find('.heart').addClass('red');
			jQuery('.item'+id).removeClass('setFavorite').addClass('unsetFavorite');
			//badge counter
    		var count = parseInt(jQuery('.fav-count').html())+1;
    		jQuery('.fav-count').html(count);
		}, 
		error: function(result) {
			//do nothing
		}
	});	
});

jQuery(document).on('click', ".unsetFavorite", function(e) {
	e.preventDefault();
	var id = jQuery(this).attr('data-id');
	jQuery.ajax({
		url: "index.php?option=com_botiga&task=unsetFavoriteAjax&id="+id+"&tmpl=raw", 
		type:  'post',
		dataType: 'json',
		success: function(result) {
			jQuery('.item'+id).find('.heart').removeClass('red');
			jQuery('.item'+id).addClass('setFavorite').removeClass('unsetFavorite');
			//badge counter
    		var count = parseInt(jQuery('.fav-count').html())-1;
    		jQuery('.fav-count').html(count);
		}, 
		error: function(result) {
			//do nothing
		}
	});
});
