<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$subtotal 	= 0;
$total 		= 0;
$model 		= $this->getModel('checkout');
$app   		= JFactory::getApplication();
$jinput		= JFactory::getApplication()->input;
$uri 		= base64_encode(JFactory::getURI()->toString());
$user  		= JFactory::getUser();
$cupon      = 0; //valor base del descompte del cupó
$re_total   = 0; //valor per defecte de re_total si no està activat
$re_percent = 0; //percent actual del recàrrec d'equivalència
$iva_percent = botigaHelper::getParameter('iva', '21');
$iva_total   = 0; //iva total
$discount_total = 0; //total descomptes

if($user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()), false);
    $app->redirect($returnurl, JText::_('COM_BOTIGA_REDIRECT_GUESTS'), 'warning');
}

$userToken 		= JSession::getFormToken();
$logo			= botigaHelper::getParameter('botiga_logo', '');
$showcoupon 	= botigaHelper::getParameter('show_coupon', 0);
$showprices 	= botigaHelper::getParameter('show_prices', 1);
$showdiscount   = botigaHelper::getParameter('show_discount', 0);
$showobserva 	= botigaHelper::getParameter('show_observa', 0);
$show_btn_delete = botigaHelper::getParameter('show_btn_delete', 0);
$show_btn_save 	= botigaHelper::getParameter('show_btn_save', 0);
$coupon     	= $model->getComandaCoupon();

$dte_linia  = botigaHelper::getUserData('dte_linia', $user->id);
$user_type = botigaHelper::getUserData('type', $user->id); //0-particular; 1-empresa

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$prices_iva = botigaHelper::getParameter('prices_iva', 1);

$count 	    = botigaHelper::getCarritoCount();
$user_params = json_decode(botigaHelper::getUserData('params', $user->id), true);
?>

<script>
jQuery(document).ready(function() {
	jQuery('.input-number').keypress(function(event) {
		var itemid = jQuery(this).attr('data-itemid');
		if (event.keyCode == 13 || event.which == 13) {
		    jQuery('#addtocart-'+itemid).submit();
		    event.preventDefault();
		}
	});
	jQuery('#processor').change(function() {
		if(jQuery('#processor').val() != "") {
            jQuery('.submit').removeAttr('disabled');
        } else {
            jQuery('.submit').attr('disabled', 'disabled');
        }
	});
});
</script>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) : ?>
<header class="head_botiga">

	<div class="col-md-11 mx-auto">

		<div class="row">

		<?php if($logo != '') : ?>
		<div class="col-12 text-right d-none d-sm-block">
			<a href="index.php"><img src="<?= $logo; ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid"></a>
		</div>
		<?php endif; ?>

		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-6 col-md-8 text-left">
					<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<?php if(botigaHelper::hasAccesstoTableView()) : ?>
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<?php endif; ?>
					<span class="pl-3 phone-hide estil02"><?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
				</div>
				<div class="col-6 col-md-4 text-right">
					<a href="<?php if($count > 0) : ?>index.php?option=com_botiga&view=checkout<?php else: ?>#<?php endif; ?>" class="pr-1 carrito">
						<?php if($count > 0) : ?>
						<span class="badge badge-warning"><?= $count; ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>
					<?php if($user->guest) : ?>
					<a href="index.php?option=com_users&view=login" title="Login" class="hasTip">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
					<?php else: ?>
					<a class="ml-4 hasTip" href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" title="Salir">
						<img src="media/com_botiga/icons/salir.png">
					</a>
					<a class="ml-4 hasTip" href="index.php?option=com_botiga&view=history" title="History" title="Perfil"s>
						<img src="media/com_botiga/icons/sesion-iniciada.png">
					</a>
					<div class="d-none d-sm-block"><small><?= JText::sprintf('COM_BOTIGA_WELCOME', $user->name); ?></small></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		</div>

	</div>

</header>
<?php endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="row">

		<div class="mt-5">
			<h1 class="estil08 text-primary"><?= JText::_('COM_BOTIGA_CHECKOUT_TITLE'); ?></h1>
		</div>

		<?php if(count($this->items)) : ?>
		<div class="table-responsive" id="checkout">
			<table class="table table-hover">

<?php /* Bucle per cada item de la comanda */ ?>

				<?php
				$i = 0;
				foreach($this->items as $item) : ?>
				<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
				<?php
					//
					// Calculem els preus i altres valors que podem necessitar
					// més endavant
					// price1: preus sense descompte, amb i sense l'IVA inclòs
					// price2: preus amb el descompte, amb i sense l'IVA inclòs
					// price: preus amb l'IVA inclòs, segons el tipus d'usuari
					//
					$price1 = $item->price;
					$price1_vat = round($item->price * (1+$iva_percent / 100),2);
					$price2 = round($price1 * (1-$dte_linia/100),2);
					$price2_vat = round($price1_vat * (1-$dte_linia/100),2);
					$total_row = $price2 * $item->qty;
					$total_row_vat = $price2_vat * $item->qty;
					if ($user_type==0) { //particular
						$price = $price1_vat;
						$price_final = $price2_vat;
					} else {
						$price = $price1;
						$price_final = $price2;
					}
					$price_frm = botigaHelper::euroFormat($price);
					$price_final_frm = botigaHelper::euroFormat($price_final);
					$total_row_frm = botigaHelper::euroFormat($total_row);
					$total_row_vat_frm = botigaHelper::euroFormat($total_row_vat);
				?>
				<tr>

<?php /* Imatge */ ?>

					<td width="5%">
						<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->idItem); ?>">
							<img src="<?= $image; ?>" class="img-fluid" alt="<?= $item->name; ?>" width="50" />
						</a>
					</td>

<?php /* Preu */ ?>

					<td width="5%" class="align-middle estil05 text-primary phone-hide">
						<?php if($dte_linia != 0.00 && $showdiscount == 1 && $showprices == 1) : ?>
							<b><strike class="faded"><?= $price_frm; ?></strike></b><br>
							<b><?= $price_final_frm; ?></b>
						<?php else: ?>
							<b><?= $price_final_frm; ?></b>
						<?php endif; ?>
					</td>

<?php /* Descripció */ ?>

					<td width="40%" class="align-middle estil05 phone-hide">
						<span class="estil05 text-primary"><?= $item->name; ?></span>
						<br>
						<?= $item->s_description; ?>
					</td>

<?php /* Total línia */ ?>

					<td width="20%" class="align-middle">
						<div class="col-md-12">
							<div class="estil05 d-block d-sm-none">
								<b>
									<?= $item->name; ?>&nbsp;
									<?= $price_final_frm; ?>
									<br>
									<?= $total_row_frm; ?><br>
								</b>
							</div>
							<form name="addtocart" id="addtocart-<?= $item->id; ?>" action="<?= JRoute::_('index.php?option=com_botiga'); ?>" method="get">
							<input type="hidden" name="id" value="<?= $item->id; ?>">
							<input type="hidden" name="return" value="<?= $uri; ?>">
							<input type="hidden" name="task" value="botiga.updateQty">
							<div class="input-group">
								<span class="input-group-btn phone-hide">
									<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="quantity-left-minus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
										<span class="fa fa-minus"></span>
									</a>
								</span>
								<input type="text" id="quantity_<?= $item->id; ?>" name="qty" data-itemid="<?= $item->id; ?>" class="form-control bg-qty input-number text-center estil05" value="<?= $item->qty; ?>" min="1" max="100" <?php if($i == 0) : ?>autofocus<?php endif; ?>>
								<span class="input-group-btn phone-hide">
									<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="quantity-right-plus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
										<span class="fa fa-plus"></span>
									</a>
								</span>
							</div>
							</form>
						</div>
					</td>

<?php /* Botó per esborrar la línia */ ?>

					<td width="5%" align="right" class="align-middle">
						<a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>">
							<img src="media/com_botiga/icons/papelera.png" alt=""
							title="<?= JText::_('COM_BOTIGA_CHECKOUT_DELETE'); ?>">
						</a>
					</td>

<?php /* Total de la línia, amb IVA inclòs o sense segons $user_type */ ?>

					<td width="15%" align="right" class="align-middle estil05 text-primary phone-hide">
						<b><?= $user_type==0?$total_row_vat_frm:$total_row_frm; ?></b>
					</td>

				</tr>
				<?php
					$subtotal += $total_row;
					$subtotal_vat += $total_row_vat;
					$subtotal_frm = botigaHelper::euroFormat($subtotal);
					$subtotal_vat_frm = botigaHelper::euroFormat($subtotal_vat);
				?>
				<?php
				$i++;
				endforeach; ?>

<?php /* Formulari cupó descompte */ ?>

				<tr>
					<td colspan="3" class="phone-hide"></td>
					<td colspan="3" class="align-middle" align="right">
						<?php if($showcoupon == 1 && $coupon == 0) : ?>
						<form name="coupon" id="coupon" action="index.php?option=com_botiga&task=botiga.validateCoupon" method="post">
							<div class="input-group">
								<input type="text" name="coupon" id="coupon" class="form-control estil02" placeholder="<?= JText::_('COM_BOTIGA_COUPON_PROMO'); ?>">
								<div class="input-group-append">
									<button type="submit" class="btn btn-primary"><?= JText::_('COM_BOTIGA_VALIDATE_COUPON'); ?></button>
								</div>
							</div>
						</form>
						<?php endif; ?>
					</td>
				</tr>

<?php /* Subtotal */ ?>

				<tr>
					<td width="10%" class="align-middle estil03">
						<span>
							<?= $user_type==0?
								JText::_('SUMA'):
								JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?>
						</span>
					</td>
					<td width="5%" class="phone-hide"></td>
					<td width="40%" class="phone-hide"></td>
					<td width="20%" class="phone-hide"></td>
					<td width="10%"></td>
					<td width="15%" align="right" class="align-middle estil05">
						<span class="blue bold total text-right">
							<?= $user_type==0?$subtotal_vat_frm:$subtotal_frm; ?>
						</span>
					</td>
				</tr>

<?php /* Descomptes en peu */ ?>

				<?php
					$discount = json_decode($model->getDiscounts(), true);
					if($discount['total'] > 0) :
					$discount_total += $discount['total'];
					$discount_total_vat = round($discount_total * (1+$iva_percent / 100),2);
					$discount_total_frm = botigaHelper::euroFormat($discount_total*-1);
					$discount_total_vat_frm = botigaHelper::euroFormat($discount_total_vat*-1);
				?>

				<tr>
					<td width="20%" class="align-middle estil03"><span><?= $discount['message']; ?></span></td>
					<td width="5%" class="phone-hide"></td>
					<td width="40%" class="phone-hide"></td>
					<td width="20%" class="phone-hide"></td>
					<td width="10%"></td>
					<td width="20%" align="right" class="align-middle estil05">
						<span>
							<?= $user_type==0?$discount_total_vat_frm:$discount_total_frm; ?>
						</span>
					</td>
				</tr>

				<?php endif; ?>

<?php /* Cupons de descompte */ ?>

				<?php $cupon = 0; if($coupon != 0) : ?>
				<?php
					$cupon = botigaHelper::getCouponDiscount($coupon, $subtotal);
					$cupon_vat = round($cupon * (1+$iva_percent / 100),2);
					$cupon_frm = botigaHelper::euroFormat($cupon*-1);
					$cupon_vat_frm = botigaHelper::euroFormat($cupon_vat*-1);
				?>
				<tr>
					<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_COUPON'); ?></td>
					<td width="5%" class="phone-hide"></td>
					<td width="40%" class="phone-hide"></td>
					<td width="20%" class="phone-hide"></td>
					<td width="10%"></td>
					<td width="20%" align="right" class="align-middle estil05">
						<span>
							<?= $user_type==0?$cupon_vat_frm:$cupon_frm; ?>
						</span>
					</td>
				</tr>
				<?php endif; ?>

<?php /* Càlcul i presentació de les despeses d'enviament */ ?>

				<tr>
					<?php
						$subtotal = $subtotal - $discount_total - $cupon;
						$subtotal_vat += $subtotal_vat - $discount_total_vat - $cupon_vat;
						$subtotal_frm = botigaHelper::euroFormat($subtotal);
						$subtotal_vat_frm = botigaHelper::euroFormat($subtotal_vat);

						$shipment = $model->getShipment($subtotal);
						$shipment_vat = round($shipment * (1+$iva_percent/100),2);
						$shipment_frm = botigaHelper::euroFormat($shipment);
						$shipment_vat_frm = botigaHelper::euroFormat($shipment_vat);

						$subtotal += $shipment;
						$subtotal_vat += $shipment_vat;
						$subtotal_frm = botigaHelper::euroFormat($subtotal);
						$subtotal_vat_frm = botigaHelper::euroFormat($subtotal_vat);
					?>
					<td width="20%" class="align-middle estil03">
						<span><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL_SHIPMENT'); ?></span>
					</td>
					<td width="5%" class="phone-hide"></td>
					<td width="40%" class="phone-hide"></td>
					<td width="20%" class="phone-hide"></td>
					<td width="10%"></td>
					<td width="20%" align="right" class="align-middle estil05">
						<span>
							<?= $user_type==0?$shipment_vat_frm:$shipment_frm; ?>
						</span>
					</td>
				</tr>

<?php /* IVA */ ?>

				<?php
					$iva_total   += ($iva_percent / 100) * $subtotal;
				?>
				<?php if($user_type!=0) : ?>
					<tr>

						<td width="20%" class="align-middle estil03"><?= 'IVA '.$iva_percent.'%'; ?></td>
						<td width="5%" class="phone-hide"></td>
						<td width="40%" class="phone-hide"></td>
						<td width="20%" class="phone-hide"></td>
						<td width="10%"></td>
						<td width="20%" align="right" class="align-middle estil05">
							<span><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span>
						</td>
					</tr>
				<?php endif; ?>

<?php /* Recàrrec d'equivalència */ ?>

				<?php if($user_params['re_equiv'] == 1 && $user_type!=0) :
					$re_percent  += botigaHelper::getParameter('re_equiv', 5.2);
					$re_total  += ($re_percent / 100) * $subtotal;
				?>
					<tr>
						<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_RE_EQUIV'); ?></td>
						<td width="5%" class="phone-hide"></td>
						<td width="40%" class="phone-hide"></td>
						<td width="20%" class="phone-hide"></td>
						<td width="10%"></td>
						<td width="20%" align="right" class="align-middle estil05">
							<span>
								<?= number_format($re_total, 2, '.', ''); ?>&euro;
							</span>
						</td>
					</tr>
				<?php endif; ?>

<?php /* Total */ ?>

				<tr>
					<?php $total = $subtotal + $iva_total + $re_total; ?>
					<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></td>
					<td width="5%" class="phone-hide"></td>
					<td width="40%" class="phone-hide"></td>
					<td width="20%" class="phone-hide"></td>
					<td width="10%"></td>
					<td width="20%" align="right" class="align-middle estil05"><span><?= number_format($total, 2, '.', ''); ?>&euro;</span></td>
				</tr>
			</table>
		</div>

		<div id="userData" class="col-12 col-md-4">
			<h1 class="estil08 text-primary"><?= JText::_('COM_BOTIGA_CART_SHIPPING_ADDRESS'); ?></h1>
			<hr>
			<?php
			$addresses = botigaHelper::getAddresses();
			$i = 0;
			foreach($addresses as $adreca) : ?>
				<?php if($adreca->activa == 1) : ?><h5><?= JText::_('COM_BOTIGA_ACTIVE_ADDRESS'); ?></h5><?php endif; ?>
				<div><b><?= JText::_('COM_BOTIGA_CART_SHIPPING_ADDRESS'); ?></b> <?= $adreca->adreca; ?></div>
				<div><b><?= JText::_('COM_BOTIGA_CART_SHIPPING_CP'); ?></b> <?= $adreca->cp; ?></div>
				<div><b><?= JText::_('COM_BOTIGA_CART_SHIPPING_STATE'); ?></b> <?= $adreca->poblacio; ?></div>
				<?php if($adreca->activa == 0) : ?><p><a href="index.php?option=com_botiga&task=history.addPrimary&id=<?= $adreca->id; ?>"><u><?= JText::_('COM_BOTIGA_ADD_PRIMARY_ADDRESS'); ?></u></a></p><?php endif; ?>
				<hr>
			<?php
			$i++;
			endforeach; ?>
			<p><a href="index.php?option=com_botiga&view=history&tab=2"><u><?= JText::_('COM_BOTIGA_CHANGE_SHIPPING_ADDRESS'); ?></u></a></p>
		</div>
		<div id="userData" class="col-12 col-md-8">
			<form name="finishCart" id="finishCart" action="index.php?option=com_botiga&task=botiga.processCart" method="post">
				<?php
				$data 					= array();
				$data['userid'] 		= $user->id;
				$data['subtotal'] 		= $subtotal + $discount_total + $cupon - $shipment;
				$data['discount'] 		= $discount_total;
				$data['shipment'] 		= $shipment;
				$data['re_percent'] 	= $re_percent;
				$data['re_total'] 		= $re_total;
				$data['iva_percent'] 	= $iva_percent;
				$data['iva_total'] 		= $iva_total;
				$data['total'] 			= $total;
				$serial 				= serialize($data);
				?>
				<input type="hidden" name="d" value='<?= base64_encode($serial); ?>'>

				<?php if($showobserva == 1) : ?>
				<div class="form-group">
					<input type="text" name="observa" class="form-control" placeholder="<?= JText::_('COM_BOTIGA_CHECKOUT_OBSERVA'); ?>" />
				</div>
				<?php endif; ?>

				<?php
				$plugins = botigaHelper::getPaymentPlugins();
				if(count($plugins) > 1) : ?>
				<h1 class="estil08 text-primary"><?= JText::_('COM_BOTIGA_FINISH_ADD_DATA'); ?></h1>
				<div class="form-group">
					<div class="styled-select">
						<select name="processor" id="processor" class="form-control">
						<option value=""><?= JText::_('COM_BOTIGA_SELECT_PAYMENT_METHOD'); ?></option>
						<?php
						$pago_selected == 0;
						foreach($plugins as $plugin) :
							$params = json_decode($plugin->params);
							if($params->test_user != '' && $params->sandbox == 1) {
								if($user->id != $params->test_user) { continue; }
							}
							/*botigaHelper::customLog('Pago_habitual (usuari) : ' . $user_params['pago_habitual']);
							botigaHelper::customLog('Pago_habitual_desc (usuari) : ' . $user_params['pago_habitual_desc']);
							botigaHelper::customLog('Mètode pagament usuari : ' . $user_params['metodo_pago']);
							botigaHelper::customLog('Pago_habitual (plugin): ' . $params->alies);*/
							if($user_params['pago_habitual'] != 1 && strtolower($params->alies) == 'habitual') { continue; }
							if($user_params['metodo_pago'] == strtolower($params->alies)) { $pago_selected = 1; }
							?>
							<?php if (strtolower($params->alies)=='habitual') { ?>
								<option value="<?= $params->alies; ?>" <?php if(strtolower($user_params['metodo_pago']) == strtolower($params->alies)): ?>selected<?php endif; ?> ><?= JText::_($params->label) . ($user_params['pago_habitual_desc']!='' ? ' (' . $user_params['pago_habitual_desc'] . ')' : ''); ?></option>
							<?php } else { ?>
								<option value="<?= $params->alies; ?>" <?php if(strtolower($user_params['metodo_pago']) == strtolower($params->alies)): ?>selected<?php endif; ?> ><?= JText::_($params->label); ?></option>
							<?php } ?>
						<?php endforeach; ?>
						</select>
					</div>
				</div>
				<?php else :
				foreach($plugins as $plugin) :
				$params = json_decode($plugin->params); ?>
				<input type="hidden" name="processor" value="<?= $params->alies; ?>">
				<?php endforeach; ?>
				<?php endif; ?>

				<a href="index.php?option=com_botiga&view=botiga" class="btn btn-primary btn-xs-block"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
				<?php if($show_btn_delete == 1) : ?>
				<a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary btn-xs-block"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a>
				<?php endif; ?>
				<?php if($show_btn_save == 1) : ?>
				<a href="index.php?option=com_botiga&task=botiga.saveCart" class="btn btn-primary btn-xs-block"><?= JText::_('COM_BOTIGA_SAVE_CART'); ?></a>
				<?php endif; ?>
				<button href="#" data-toggle="modal" data-target="#final" class="btn btn-yellow btn-xs-block submit" <?php if ($pago_selected==0) { echo 'disabled="true"'; } ?>><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></button>
			</form>
		</div>
		<?php else : ?>
		<div class="table-responsive" id="checkout">
		<?= JText::_('COM_BOTIGA_CART_IS_EMPTY'); ?>
		</div>
		<?php endif; ?>

	</div>
</div>

<!-- start Final modal -->
<div class="modal fade" id="final" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-dialog-centered text-center" role="document">
	<div class="modal-content">
		<div class="modal-header" style="padding:15px;border:none;">
	    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  	</div>
	  	<div class="modal-body">
		<h2><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></h2>
		<div class="row">
			<div class="col-12">
				<?= JText::_('COM_BOTIGA_CHECKOUT_MODAL_CONFIRM'); ?>
			</div>
		</div>
		<a href="javascript:jQuery('#finishCart').submit();" class="btn btn-primary btn-block mt-2"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></a>
	  </div>
	</div>
  </div>
</div>
<!-- end final modal -->
