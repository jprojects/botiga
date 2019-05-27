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
$user  		= JFactory::getUser();
$cupon      = 0; //valor base del descompte del cupó
$re_total   = 0; //valor per defecte de re_total si no està activat
$re_percent = 0; //percent actual del recàrrec d'equivalència
$iva_percent = 0; //percent d'iva actual
$iva_total   = 0; //iva total
$discount_total = 0; //total descomptes

if($user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()), false);
    $app->redirect($returnurl, JText::_('COM_BOTIGA_REDIRECT_GUESTS'), 'warning');
}

$userToken 		= JSession::getFormToken();
$logo			= botigaHelper::getParameter('botiga_logo', '');
$showcoupon 	= botigaHelper::getParameter('show_coupon', 0);
$showobserva 	= botigaHelper::getParameter('show_observa', 0);
$show_btn_delete = botigaHelper::getParameter('show_btn_delete', 0);
$show_btn_save 	= botigaHelper::getParameter('show_btn_save', 0);
$coupon     	= $model->getComandaCoupon();

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$count 	    = botigaHelper::getCarritoCount();
$user_params = json_decode(botigaHelper::getUserData('params', $user->id), true);
?>

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
				<div class="col-9 text-left">			
					<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<span class="pl-3 phone-hide"><?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
				</div>
				<div class="col-3 text-right">
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
					<a href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" class="hasTip">
						<img src="media/com_botiga/icons/salir.png">
					</a>
					<a href="index.php?option=com_botiga&view=history" title="History" class="hasTip">
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
		<div class="table-responsive">
			<table class="table table-hover">
				<?php 
				$i = 0;
				foreach($this->items as $item) : ?>
				<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
				<?php $total_row = $item->price * $item->qty; ?>
				<tr>
					<td width="10%">
						<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->idItem); ?>">
							<img src="<?= $image; ?>" class="img-fluid" alt="<?= $item->name; ?>" width="50" />
						</a>
					</td>
					<td width="5%" class="align-middle estil05 text-primary"><b><?= $item->price.'&euro;'; ?></b></td>
					<td width="40%" class="align-middle estil05"><span class="estil05 text-primary"><?= $item->name; ?></span><br><?= $item->s_description; ?></td>				
					<td width="20%" class="align-middle">
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-btn">
								<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="quantity-left-minus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
								 	<span class="fa fa-minus"></span>
								</a>
							</span>
							<input type="text" id="quantity_<?= $item->id; ?>" name="qty" class="form-control bg-qty input-number text-center estil06" value="<?= $item->qty; ?>" min="1" max="100" <?php if($i == 0) : ?>autofocus<?php endif; ?>>
							<span class="input-group-btn">
								<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="quantity-right-plus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
									<span class="fa fa-plus"></span>
								</a>
							</span>
						</div>
					</div>
					</td>
					<td width="10%" align="right" class="align-middle"><a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>"><img src="media/com_botiga/icons/papelera.png" alt="" title="<?= JText::_('COM_BOTIGA_CHECKOUT_DELETE'); ?>"></a></td>
					<td width="15%" align="right" class="align-middle estil05 text-primary"><b><?= number_format($total_row, 2) . ' &euro;'; ?></b></td>				
				</tr>										
				<?php $subtotal += $total_row; ?>
				<?php 
				$i++;
				endforeach; ?>
				<tr>
					<td colspan="3"></div>
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
				<tr>
					<td width="10%" class="align-middle estil03"><span><?= JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?></span></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>
					<td width="15%" class="align-middle estil05"><span class="blue bold total text-right"><?= number_format($subtotal, 2, '.', ''); ?>&euro;</span></td>
				</tr>
				<tr>
					<?php $shipment = $model->getShipment($subtotal); ?>
					<td width="20%" class="align-middle estil03"><span><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL_SHIPMENT'); ?></span></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>		
					<td width="20%" class="align-middle estil05"><span><?= $shipment; ?>&euro;</span></td>
				</tr>
				<?php 
				$discount = json_decode($model->getDiscounts(), true);
				if($discount['total'] > 0) : 
				$discount_total += $discount['total'];
				?>
				<tr>
					<td width="20%" class="align-middle estil03"><span><?= $discount['message']; ?></span></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>		
					<td width="20%" class="align-middle estil05"><span><?= $discount_total; ?>&euro;</span></td>
				</tr>
				<?php endif; ?>
				<tr>
					<?php 
					$subtotal = $subtotal - $discount_total;
					$iva_percent += botigaHelper::getParameter('iva', '21');
				 	$iva_total   += ($iva_percent / 100) * ($subtotal + $shipment); 
				 	?>
					<td width="20%" class="align-middle estil03"><?= 'IVA '.$iva_percent.'%'; ?></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>				
					<td width="20%" class="align-middle estil05"><span><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span></td>
				</tr>
				<?php if($user_params['re_equiv'] == 1) :
				$re_percent  += botigaHelper::getParameter('re_equiv', 5.2);
				$re_total  += ($re_percent / 100) * ($subtotal + $shipment);
				?>
				<tr>
					<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_RE_EQUIV'); ?></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>				
					<td width="20%" class="align-middle estil05"><span><?= number_format($re_total, 2, '.', ''); ?>&euro;</span></td>
				</tr>
				<?php endif; ?>
				<?php if($coupon != 0) : ?>
				<?php $cupon = botigaHelper::getCouponDiscount($coupon, $subtotal); ?>
				<tr>
					<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_DISCOUNT'); ?></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>				
					<td width="20%" class="align-middle estil05"><span><?= $cupon; ?>&euro;</span></td>
				</tr>
				<?php endif; ?>
				<tr>
					<?php $total = $subtotal + $shipment + $iva_total + $re_total - $cupon; ?>
					<td width="20%" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></td>
					<td width="5%"></td>
					<td width="40%"></td>
					<td width="20%"></td>
					<td width="10%"></td>				
					<td width="20%" class="align-middle estil05"><span><?= number_format($total, 2, '.', ''); ?>&euro;</span></td>
				</tr>
			</table>		
		</div>
		
		<div id="userData">		
			<form name="finishCart" id="finishCart" action="index.php?option=com_botiga&task=botiga.processCart" method="post">
				<input type="hidden" name="userid" value="<?= $user->id; ?>">
				<input type="hidden" name="subtotal" value="<?= $subtotal; ?>">
				<input type="hidden" name="shipment" value="<?= $shipment; ?>">
				<input type="hidden" name="re_percent" value="<?= $re_percent; ?>">
				<input type="hidden" name="re_total" value="<?= $re_total; ?>">
				<input type="hidden" name="iva_percent" value="<?= $iva_percent; ?>">
				<input type="hidden" name="iva_total" value="<?= $iva_total; ?>">
				<input type="hidden" name="discount" value="<?= $cupon+$discount_total; ?>">
				<input type="hidden" name="total" value="<?= $total; ?>">
				
				<?php if($showobserva == 1) : ?>
				<div class="form-group">
					<input type="text" name="observa" class="form-control" placeholder="<?= JText::_('COM_BOTIGA_CHECKOUT_OBSERVA'); ?>" />
				</div>
				<?php endif; ?>
				
				<?php
				$plugins = botigaHelper::getPaymentPlugins();
				if(count($plugins) > 1) : ?>
				<p class="estil03"><?= JText::_('COM_BOTIGA_FINISH_ADD_DATA'); ?></p>
				<div class="form-group">
					<div class="styled-select">
						<select name="processor" id="processor" class="form-control">
						<option value=""><?= JText::_('COM_BOTIGA_SELECT_PAYMENT_METHOD'); ?></option>
						<?php 
						foreach($plugins as $plugin) : 
						$params = json_decode($plugin->params);				
						?>
						<option value="<?= $params->alies; ?>" <?php if($user_params['metodo_pago'] == strtolower($params->alies)): ?>selected<?php endif; ?> ><?= $params->title; ?></option>
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
				
				<a href="index.php?option=com_botiga&view=botiga" class="btn btn-primary"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
				<button type="submit" <?php if(count($plugins) > 1) : ?>disabled="true"<?php endif; ?> class="btn btn-primary submit"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></button>				
				<?php if($show_btn_delete == 1) : ?>
				<a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a>	
				<?php endif; ?>
				<?php if($show_btn_save == 1) : ?>
				<a href="index.php?option=com_botiga&task=botiga.saveCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_SAVE_CART'); ?></a>	
				<?php endif; ?>
			</form>
		</div>
		<?php else : ?>
		<?= JText::_('COM_BOTIGA_CART_IS_EMPTY'); ?>
		<?php endif; ?>
			
	</div>
</div>
