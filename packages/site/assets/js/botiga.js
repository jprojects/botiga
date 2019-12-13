jQuery(document).ready(function() {

	var quantitiy = 0;

	//iniciem fancybox per mostrar les imatges de la vista item en una finestra
	if(jQuery('.fancybox').length) { jQuery(".fancybox").fancybox(); }

	//funció per quan es clica sobre la variació d'un producte t'envia a la seva pàgina
	jQuery('#variacions').change(function() {
		var id 	= jQuery(this).val();
		document.location.href = 'index.php?option=com_botiga&view=item&id='+id;

	});

	//funció per quan es clica més quantitat en un addtocart
    jQuery('.quantity-right-plus').click(function(e) {
        var id = jQuery(this).attr('data-id');
        var quantity = parseInt(jQuery('#quantity_'+id).val());
        jQuery('#quantity_'+id).val(quantity + 1);

    });

		//return key submit quantity input boxes
		jQuery('.input-number').keypress(function(event) {
			var itemid = jQuery(this).attr('data-itemid');
			if (event.keyCode == 13 || event.which == 13) {
			    jQuery('#addtocart-'+itemid).submit();
			    event.preventDefault();
			}
		});

   	//funció per quan es clica menys quantitat en un addtocart
    jQuery('.quantity-left-minus').click(function(e) {
        var id = jQuery(this).attr('data-id');
        var quantity = parseInt(jQuery('#quantity_'+id).val());
        if(quantity > 0){
            jQuery('#quantity_'+id).val(quantity - 1);
        }
    });

    //funció per simular un registre en dos pasos
    jQuery('.next').click(function(e) {
        e.preventDefault();
        jQuery('#registerFields').show();
        jQuery(this).hide();
        jQuery('#fieldType').hide();
    });

    //funció per mostrar uns camps o uns altres al seleccionar el tipus d'usuari en el registre
    jQuery('#jform_type').change(function() {
		if(jQuery(this).val() == 1) {
			jQuery('#empresaFields').show();
			jQuery('#passwordHelpBlock').show();
		} else {
			jQuery('#empresaFields').hide();
			jQuery('#passwordHelpBlock').hide();
		}
	});

	//funció per l'accordion de la vista item
	jQuery('.acordion').click(function() {
		var href = jQuery(this).attr('href');
		if(jQuery(href).hasClass('show')) {
			jQuery(this).find('.fa').addClass('fa-plus');
			jQuery(this).find('.fa').removeClass('fa-minus');
		} else {
			jQuery(this).find('.fa').removeClass('fa-plus');
			jQuery(this).find('.fa').addClass('fa-minus');
		}
	});

});
