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
JHtml::_('jquery.framework');
$subtotal = 0;
$total = 0;
$model = $this->getModel();
$app   = JFactory::getApplication();
$user  = JFactory::getUser();
if($user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()), false);
    $app->redirect($returnurl, JText::_('COM_BOTIGA_REDIRECT_GUESTS'), 'info');
}
$userToken = JSession::getFormToken();
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) : ?>
<header class="head_botiga">

	<div class="col-md-11 mx-auto">

		<div class="row">

		<?php if(botigaHelper::getParameter('botiga_logo', '') != '') : ?>
		<div class="col-12 text-right">
			<img src="<?= botigaHelper::getParameter('botiga_logo', ''); ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid">
		</div>
		<?php endif; ?>	
		
		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-xs-12 col-md-6 text-left">&nbsp;</div>
				<div class="col-xs-12 col-md-6 text-right">
					<a href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout">
						<img src="media/com_botiga/icons/salir.png">
					</a>
					<a href="index.php?option=com_botiga&view=checkout" class="pl-1 carrito">
						<?php if(botigaHelper::getCarritoCount() > 0) : ?>
						<span class="badge badge-warning"><?= botigaHelper::getCarritoCount(); ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>					
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
			<h1><?= JText::_('COM_BOTIGA_CHECKOUT_TITLE'); ?></h1>
		</div>

		<?php if(count($this->items)) : ?>
		<!-- version desktop -->
		<table class="table">
			<?php foreach($this->items as $item) : ?>
			<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
			<?php $total_row = $item->price * $item->qty; ?>
			<tr>
				<td width="10%"><img src="<?= $image; ?>" class="img-fluid" alt="<?= $item->name; ?>" width="50" /></td>
				<td width="5%" class="align-middle"><b><?= $item->price.'&euro;'; ?></b></td>
				<td width="40%" class="align-middle"><?= $item->name; ?><br><?= $item->s_description; ?></td>				
				<td width="20%" class="align-middle">
				<div class="col-md-12">
					<div class="input-group">
						<span class="input-group-btn">
							<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="quantity-left-minus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
							 	<span class="fa fa-minus"></span>
							</a>
						</span>
						<input type="text" id="quantity_<?= $item->id; ?>" name="qty" class="form-control bg-qty input-number text-center estil06" value="<?= $item->qty; ?>" min="1" max="100">
						<span class="input-group-btn">
							<a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="quantity-right-plus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
								<span class="fa fa-plus"></span>
							</a>
						</span>
					</div>
				</div>
				</td>
				<td width="10%" class="align-middle"><a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>"><img src="media/com_botiga/icons/papelera.png" alt="" title="<?= JText::_('COM_BOTIGA_CHECKOUT_DELETE'); ?>"></a></td>
				<td width="15%" class="align-middle"><b><?= number_format($total_row, 2) . ' &euro;'; ?></b></td>				
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php $subtotal += $total_row; ?>
			<?php endforeach; ?>
			<tr>
				<td width="10%" class="align-middle"><span><?= JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?></span></td>
				<td width="5%"></td>
				<td width="40%"></td>
				<td width="20%"></td>
				<td width="10%"></td>
				<td width="15%" class="align-middle"><span class="blue bold total text-right"><?= number_format($subtotal, 2, '.', ''); ?>&euro;</span></td>
			</tr>
			<tr>
				<?php $shipment = $model->getShipment($subtotal); ?>
				<td width="20%" class="align-middle"><span><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL_SHIPMENT'); ?></span></td>
				<td width="5%"></td>
				<td width="40%"></td>
				<td width="20%"></td>
				<td width="10%"></td>		
				<td width="20%" class="align-middle"><span><?= $shipment; ?>&euro;</span></td>
			</tr>
			<tr>
				<?php 
				$iva_percent = botigaHelper::getParameter('iva', '21');
			 	$iva_total  = ($iva_percent / 100) * ($subtotal + $shipment); 
			 	?>
				<td width="20%" class="align-middle"><?= 'IVA '.$iva_percent.'%'; ?></td>
				<td width="5%"></td>
				<td width="40%"></td>
				<td width="20%"></td>
				<td width="10%"></td>				
				<td width="20%" class="align-middle"><span><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span></td>
			</tr>
			<tr>
				<?php $total = $subtotal + $shipment + $iva_total; ?>
				<td width="20%" class="align-middle"><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></td>
				<td width="5%"></td>
				<td width="40%"></td>
				<td width="20%"></td>
				<td width="10%"></td>				
				<td width="20%" class="align-middle"><span><?= number_format($total, 2, '.', ''); ?>&euro;</span></td>
			</tr>
		</table>		
		
		<div id="userData">		
			<form name="finishCart" id="finishCart" action="index.php?option=com_botiga&task=botiga.processCart" method="post">
				<input type="hidden" name="subtotal" value="<?= $subtotal; ?>">
				<input type="hidden" name="shipment" value="<?= $shipment; ?>">
				<input type="hidden" name="total" value="<?= $total; ?>">
				
				<div class="form-group">
					<input type="text" name="observa" class="form-control" placeholder="<?= JText::_('COM_BOTIGA_CHECKOUT_OBSERVA'); ?>" />
				</div>
				
				<?php
				$plugins = botigaHelper::getPaymentPlugins();
				if(count($plugins) > 1) : ?>
				<p><?= JText::_('COM_BOTIGA_FINISH_ADD_DATA'); ?></p>
				<div class="form-group">
					<select name="processor" id="processor" class="form-control">
					<option value=""><?= JText::_('COM_BOTIGA_SELECT_PAYMENT_METHOD'); ?></option>
					<?php 
					$metodo_pago = botigaHelper::getUserData('metodo_pago', $user->id);
					foreach($plugins as $plugin) : 
					$params = json_decode($plugin->params);				
					?>
					<option value="<?= $params->alies; ?>" <?php if($processor == $params->alies || $metodo_pago == $params->alies) : ?>selected<?php endif; ?>><?= $params->title; ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<?php else : 
				foreach($plugins as $plugin) : 
				$params = json_decode($plugin->params); ?>
				<input type="hidden" name="processor" value="<?= $params->alies; ?>">
				<?php endforeach; ?>
				<?php endif; ?>
				
				<button type="submit" <?php if($metodo_pago == '' && count($plugins) > 1) : ?>disabled="true"<?php endif; ?> class="btn btn-primary submit pull-right"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></button>
				<a href="index.php?option=com_botiga&view=botiga" class="btn btn-primary"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
				<a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a>	
				<a href="index.php?option=com_botiga&task=botiga.saveCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_SAVE_CART'); ?></a>	
			</form>
		</div>
		<?php else : ?>
		<?= JText::_('COM_BOTIGA_CART_IS_EMPTY'); ?>
		<?php endif; ?>
			
	</div>
</div>
