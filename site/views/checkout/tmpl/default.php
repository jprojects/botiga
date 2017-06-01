<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail administracion@joomlanetprojects.com
 * @website		http://www.joomlanetprojects.com
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('jquery.framework');
$total = 0;
$model = $this->getModel();
?>

<style>
.size20 { font-size: 20px; }
</style>

<div class="container">
	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_CHECKOUT_TITLE'); ?></h1>
	</div>
	
	<div class="alert alert-info"><?= JText::_('COM_BOTIGA_NOTICE_CHECKOUT'); ?></div>

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
		<td width="4%">
		<div class="blue bold size20">
		<?php if($item->price == 0) { echo JText::_('COM_BOTIGA_A_CONSULTAR'); } else { echo $item->price.'&euro;'; } ?>
		</div>
		</td>
		<td width="25%">
		<div class="col-md-12">
			<div class="input-group">
				<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=minus" class="btn btn-default value-control"><span class="glyphicon glyphicon-minus"></span></a></span>
				<input type="text" name="qty-<?= $item->id; ?>" value="<?= $item->qty; ?>" class="form-control" id="qty-<?= $item->id; ?>">
				<span class="input-group-btn"><a href="index.php?option=com_botiga&task=botiga.updateQty&id=<?= $item->id; ?>&type=plus" class="btn btn-default value-control"><span class="glyphicon glyphicon-plus"></span></a></span>
			</div>
		</div>
		</td>
		<td width="10%"><div class="blue bold size20 item-total-<?= $item->id; ?>">
		<?php if($item->total == 0) { echo JText::_('COM_BOTIGA_A_CONSULTAR'); } else { echo $item->total.'&euro;'; } ?>
		</div>
		</td>
		<td width="5%"><a href="index.php?option=com_botiga&task=botiga.removeItem&id=<?= $item->id; ?>"><?= JText::_('COM_BOTIGA_CHECKOUT_DELETE'); ?></a></td>
	</tr>
	<?php $total += $item->total; ?>
	<?php endforeach; ?>
	<tr>
		<td width="20%"><a href="index.php?option=com_botiga&view=botiga&catid=20&Itemid=128" class="btn btn-primary"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a></td>
		<td width="20%"><a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a></td>
		<td></td>
		<td><strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></strong></td>
		<td width="20%"><span class="blue bold size20 total"><?= number_format($total, 2); ?>&euro;</span><p></p><a onclick="jQuery('#userData').toggle();" class="btn btn-primary finish"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></a></td>
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
				<div class="blue bold size20 item-total-<?= $item->id; ?>"><?= $item->total; ?>&euro;</div>
			</div>		
		<?php $total += $item->total; ?>
		<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-12 top20">
			<a href="index.php?option=com_botiga&view=botiga&catid=20&Itemid=128" class="btn btn-primary btn-block"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
			<a href="index.php?option=com_botiga&task=botiga.removeCart" class="btn btn-primary btn-block"><?= JText::_('COM_BOTIGA_DELETE_CART'); ?></a>
		</div>
		<div class="col-xs-12">
			<strong><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></strong>
			<span class="blue bold size20 total"><?= number_format($total, 2); ?>&euro;</span><p></p><a onclick="jQuery('#userData').toggle();" class="btn btn-primary btn-block finish"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></a>
		</div>
	</div>
	
	<div id="userData" style="display:none;">
		<p><?= JText::_('COM_BOTIGA_FINISH_ADD_DATA'); ?></p>
		<form name="finishCart" id="finishCart" action="index.php?option=com_laundry&task=botiga.processCart" method="post">
			<div class="form-group">
				<input type="text" class="form-control" name="pais" value="<?= $model->getUserData('pais'); ?>" placeholder="<?= JText::_('COM_BOTIGA_FINISH_CART_COUNTRY'); ?>" />
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="cp" value="<?= $model->getUserData('cp'); ?>" placeholder="<?= JText::_('COM_BOTIGA_FINISH_CART_CP'); ?>" />
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="direccion" value="<?= $model->getUserData('adreca'); ?>" placeholder="<?= JText::_('COM_BOTIGA_FINISH_CART_ADDRESS'); ?>" />
			</div>
			<button type="submit" class="btn btn-primary finish"><?= JText::_('COM_BOTIGA_FINISH_CART'); ?></button>
		</form>
	</div>
	<?php else : ?>
	<?= JText::_('COM_BOTIGA_CART_IS_EMPTY'); ?>
	<?php endif; ?>
	
</div>

