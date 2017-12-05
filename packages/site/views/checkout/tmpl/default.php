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

?>

<style>
.size20 { font-size: 20px; }
</style>

<div class="checkout">
	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_CHECKOUT_TITLE'); ?></h1>
	</div>

	<?php if(count($this->items)) : ?>
	<!-- version desktop -->
	<table class="table hidden-xs">
	<?php foreach($this->items as $item) : ?>
	<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
	<tr>
		<td width="20%"><img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" /></td>
		<td width="40%"><?= $item->name; ?> - <?= $item->ref; ?>
		<?php if($item->price == 0)  : ?>
		<br>
		<div class="alert alert-info hidden-xs hidden-sm"><?= JText::_('COM_BOTIGA_A_CONSULTAR_NOTICE'); ?></div>
		<?php endif; ?>
		</td>
		<td width="5%">
		<div class="blue bold size20">
		<?php if($item->price == 0) { echo JText::_('COM_BOTIGA_A_CONSULTAR'); } else { echo $item->price.'&euro;'; } ?>
		</div>
		</td>
		<td width="20%">
		<div class="col-md-12">
			<div class="input-group">
				<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="btn btn-default value-control"><span class="glyphicon glyphicon-minus"></span></a></span>
				<input type="text" name="qty-<?= $item->id; ?>" value="<?= $item->qty; ?>" class="form-control text-center" id="qty-<?= $item->id; ?>">
				<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="btn btn-default value-control"><span class="glyphicon glyphicon-plus"></span></a></span>
			</div>
		</div>
		</td>
		<td width="10%"><div class="blue bold size20 item-total-<?= $item->id; ?>">
		<?php 
		$total_row = $item->price * $item->qty;
		echo number_format($total_row, 2) . ' &euro;'; 
		?>
		</div>
		</td>
		<td width="5%"><a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>"><i class="fa fa-trash-o fa-2x" title="<?= JText::_('COM_BOTIGA_CHECKOUT_DELETE'); ?>"></i></a></td>
	</tr>
	<?php $subtotal += $total_row; ?>
	<?php endforeach; ?>
	<tr>
		<td width="20%"></td>
		<td width="20%"></td>
		<td></td>
		<td><strong><?= JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?></strong></td>
		<td width="20%"><span class="blue bold size20 total text-right"><?= number_format($subtotal, 2, '.', ''); ?>&euro;</span></td>
	</tr>
	<tr>
		<?php 
		$shipment = $model->getShipment($subtotal);
		$msg = '';
		if($shipment == 0) { $shipment = '0.00'; $msg = 'Envío gratis por compras superiores a 250€'; }
		if($shipment == 9999) { $shipment = '0.00'; $msg = 'Costes de transporte pendientes de valoración'; }
		?>
		<td width="20%"><span class="red"><?= $msg; ?></span></td>
		<td width="20%"></td>
		<td></td>
		<td><strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL_SHIPMENT'); ?></strong></td>		
		<td width="20%"><span class="blue bold size20 total text-right"><?= $shipment; ?>&euro;</span></td>
	</tr>
	<tr>
		<td width="20%"></td>
		<td width="20%"></td>
		<td></td>
		<?php 
		$iva_percent = botigaHelper::getParameter('iva', '21');
     	$iva_total  = ($iva_percent / 100) * ($subtotal + $shipment); 
     	?>
		<td><strong><?= 'IVA '.$iva_percent.'%'; ?></strong></td>
		<td width="20%"><span class="blue bold size20 total text-right"><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span></td>
	</tr>
	<tr>
		<td width="20%"></td>
		<td width="20%"></td>
		<td></td>
		<td><strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></strong></td>
		<?php $total = $subtotal + $shipment + $iva_total; ?>
		<td width="20%"><span class="blue bold size20 total text-right"><?= number_format($total, 2, '.', ''); ?>&euro;</span></td>
	</tr>
	</table>
	
	<!-- version mobile -->
	<div class="visible-xs-block">
		<div class="top20">
			<?php foreach($this->items as $item) : ?>
			<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
			<div class="col-xs-4">
				<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
			</div>
			<div class="col-xs-8">
				<div><?= $item->name; ?> - <?= $item->ref; ?> <a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>"><i class="fa fa-trash"></i></a></div>
			</div>
			<div class="clearfix"></div>
			<div class="col-xs-8">
				<div class="input-group">
					<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="btn btn-default value-control"><span class="glyphicon glyphicon-minus"></span></a></span>
					<input type="text" name="qty-<?= $item->id; ?>" value="<?= $item->qty; ?>" class="form-control" id="qty-<?= $item->id; ?>">
					<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="btn btn-default value-control"><span class="glyphicon glyphicon-plus"></span></a></span>
				</div>
			</div>
			<div class="col-xs-4">
				<div class="blue bold size20 item-total-<?= $item->id; ?>">
				<?php 
				$total_row = $item->price * $item->qty;
				echo number_format($total_row, 2, '.', '') . ' &euro;'; 
				?>
				</div>
			</div>		
		<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-12 text-right">
			<strong><?= JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?></strong>
			<span class="blue bold size20 total"><?= number_format($subtotal, 2, '.', ''); ?>&euro;</span>
		</div>
		<div class="col-xs-12 text-right">
			<strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL-SHIPMENT'); ?></strong>
			<span class="blue bold size20 total"><?= $shipment; ?>&euro;</span>
		</div>
		<div class="col-xs-12 text-right">
			<strong>IVA <?= $iva_percent; ?>%</strong>
			<span class="blue bold size20 total"><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span>
		</div>
		<div class="col-xs-12 text-right">
			<strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></strong>
			<span class="blue bold size20 total"><?= number_format($total, 2, '.', ''); ?>&euro;</span>
		</div>
	</div>
	
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
			<a href="index.php?option=com_botiga&view=botiga&catid=20&Itemid=128" class="btn btn-primary"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
			<a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a>	
			<a href="index.php?option=com_botiga&task=botiga.saveCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_SAVE_CART'); ?></a>	
		</form>
	</div>
	<?php else : ?>
	<?= JText::_('COM_BOTIGA_CART_IS_EMPTY'); ?>
	<?php endif; ?>
	
</div>

