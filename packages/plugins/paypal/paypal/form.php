<?php defined('_JEXEC') or die(); ?>

<p>
<form action="<?php echo $data->url ?>"  method="post" id="paymentForm" name="paymentForm">
	<input type="hidden" name="cmd" value="<?php echo $data->cmd ?>" />
	<input type="hidden" name="business" value="<?php echo $data->merchant ?>" />
	<input type="hidden" name="return" value="<?php echo $data->success ?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $data->cancel ?>" />
	<input type="hidden" name="notify_url" value="<?php echo $data->postback ?>" />
	<input type="hidden" name="custom" value="<?php echo $data->idComanda ?>" />

	<input type="hidden" name="item_number" value="" />
	<input type="hidden" name="item_name" value="" />
	<input type="hidden" name="currency_code" value="<?php echo $data->currency ?>" />

	<input type="hidden" name="amount" value="<?php echo $data->amount ?>" />
	<input type="hidden" name="tax" value="" />

	<input type="hidden" name="first_name" value="<?php echo $data->firstname ?>" />
	<input type="hidden" name="last_name" value="" />

	<input type="hidden" name="address_override" value="0">
	<input type="hidden" name="address1" value="">
	<input type="hidden" name="address2" value="">
	<input type="hidden" name="city" value="">
	<input type="hidden" name="state" value="">
	<input type="hidden" name="zip" value="">
	<input type="hidden" name="country" value="">

	<input type="hidden" name="rm" value="2">

	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="no_shipping" value="1" />

	<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" id="paypalsubmit" />
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>
</p>

<script>
window.setTimeout(function(){

        paymentForm.submit();

    }, 2000);
</script>
