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
$model = $this->getModel('history');
$user  = JFactory::getUser();
?>

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_HISTORY_TITLE'); ?></h1>
	</div>
	
	<!-- Nav tabs -->
  	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab"><?= JText::_('COM_BOTIGA_HISTORY_MY_ORDERS'); ?></a></li>
		<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab"><?= JText::_('COM_BOTIGA_HISTORY_MY_SETTINGS'); ?></a></li>
		<li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab"><?= JText::_('COM_BOTIGA_HISTORY_MY_CARTS'); ?></a></li>
  	</ul>
  
  	<!-- Tab panes -->
  	<div class="tab-content">
  	
  		<div role="tabpanel" class="tab-pane active" id="tab1">
			<?php if(count($this->items)) : ?>
			<table class="table table-striped" style="margin-top:30px;">
			<?php foreach($this->items as $item) : ?>
			<?php $iva = (21 / 100) * $item->subtotal; ?>
			<?php $item->status == 3 ? $status = JText::_('COM_BOTIGA_HISTORY_STATUS_FINISHED') : $status = JText::_('COM_BOTIGA_HISTORY_STATUS_PENDING'); ?>
			<tr>
				<td><?= JText::_('COM_BOTIGA_HISTORY_NUM'); ?> <?= $item->uniqid; ?></td>
				<td><?= $item->data; ?></td>
				<td><?= $status; ?></td>
				<td align="right"><div class="blue bold"><?= number_format(($item->total), 2); ?>&euro;</div></td>
				<td align="right">
					<a href="index.php?option=com_botiga&task=botiga.genPdf&id=<?= $item->id; ?>" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
				</td>
			</tr>
			<?php endforeach; ?>
			</table>
			<?php else : ?>
			<div><?= JText::_('COM_BOTIGA_HISTORY_NO_ORDERS'); ?></div>
			<?php endif; ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab2">
			<div style="margin-top:30px;">
				<form action="index.php?option=com_botiga&task=history.saveConfig" method="post" name="config">
					<div class="form-group">
    					<select name="jform[processor]" id="processor" class="form-control">
							<option value="">Seleciona un método de pago</option>
							<?php 
							foreach(botigaHelper::getPaymentPlugins() as $plugin) : 
							$params = json_decode($plugin->params);
							$processor = botigaHelper::getUserData('metodo_pago', $user->id);
							?>
							<option value="<?= $params->alies; ?>" <?php if($processor == $params->alies) : ?>selected<?php endif; ?>><?= $params->title; ?></option>
							<?php endforeach; ?>
						</select>
  					</div>
  					<button type="submit" class="btn btn-primary">GUARDAR</button>
				</form>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab3">
			<?php 
			$carts = $model->getSavedCarts();
			if(count($carts)) : ?>
			<table class="table table-striped" style="margin-top:30px;">
			<?php foreach($carts as $cart) : ?>
			<tr>
				<td><?= JText::_('COM_BOTIGA_CART'); ?>-<?= $cart->idComanda; ?></td>
				<td><?= $cart->data; ?></td>
				<td>
					<a href="index.php?option=com_botiga&task=botiga.getSavedCart&id=<?= $cart->id; ?>"><i class="fa fa-eye"></i> <?= JText::_('COM_BOTIGA_HISTORY_RECOVERY_SAVED_CART'); ?></a>
				</td>
				<td>
					<a href="index.php?option=com_botiga&task=botiga.removeSavedCart&id=<?= $cart->id; ?>"><i class="fa fa-trash"></i> <?= JText::_('COM_BOTIGA_HISTORY_REMOVE_SAVED_CART'); ?></a>
				</td>
			</tr>
			<?php endforeach; ?>
			</table>
			<?php else : ?>
			<div><?= JText::_('COM_BOTIGA_HISTORY_NO_SAVED_CARTS'); ?></div>
			<?php endif; ?>
		</div>
		
	</div>
	
</div>

