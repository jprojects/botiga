

<div class="container-fluid margintop">

<div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4">
<form action="<?= $data->url; ?>" name="tpv" id="tpv" method="post">
	<input type='hidden' name='Ds_SignatureVersion' value='<?= $data->version; ?>'> 
	<input type='hidden' name='Ds_MerchantParameters' value='<?= $data->params; ?>'> 
	<input type='hidden' name='Ds_Signature' value='<?= $data->signature; ?>'> 
	<input type="image" src="<?= dirname(__FILE__); ?>/bbva.jpg" border="0" name="submit" alt="bbva" />
</form>
</div>

</div>

<script>
window.setTimeout(function(){

        tpv.submit();

    }, 2000);
</script>
