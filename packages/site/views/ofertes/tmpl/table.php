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
$model 		= $this->getModel('botiga');
$user  		= JFactory::getUser();
$url        = JFactory::getURI()->toString();
$uri 		= base64_encode($url);
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$catid      = $jinput->get('catid', '');
$marca      = $jinput->get('marca', '');
$itemid     = $jinput->get('Itemid', '');
$orderby    = $jinput->get('orderby', '');
$lang 		= JFactory::getLanguage()->getTag();
$showprices = botigaHelper::getParameter('show_prices', 1);
$login_prices = botigaHelper::getParameter('login_prices', 0);
?>

<style>
.item {
	margin-bottom: 50px;
	padding-left: 0 !important;
	padding-right: 0 !important;
}
.item-wrap {
	padding-left: 10px;
	padding-right: 10px;
}
</style>

<!-- start Modal success -->
<div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header" style="padding: 0 15px;border:none;">
	    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  	</div>
	  	<div class="modal-body">
		<?= JText::_('COM_BOTIGA_PROCESS_CART'); ?>
	  </div>
	</div>
  </div>
</div>
<!-- end Modal success -->

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_ITEMS_TITLE'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<?php if($showprices == 1 && $user->guest) : ?>
	<div class="alert alert-primary"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', 'index.php?option=com_botiga&view=register&Itemid=117'); ?></div>
	<?php endif; ?>
	
	<div class="pull-left">
		<form name="filters" id="filters" method="get" action="index.php?option=com_botiga&view=botiga" class="form-inline" onchange="filters.submit();">
			<input type="hidden" name="option" value="com_botiga" />
			<input type="hidden" name="view" value="botiga" />
			<input type="hidden" name="catid" value="<?= $catid; ?>" />
			<input type="hidden" name="marca" value="<?= $marca; ?>" />
			<input type="hidden" name="Itemid" value="<?= $itemid; ?>" />
			<div class="form-group">
				<select name="orderby" id="orderby" class="form-control">
					<option value="">Ordenar por</option>
					<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>>Código</option>
					<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>>Precio</option>
				</select>
			</div>
		</form>
	</div>
	<div class="pull-right">
		<?php $link = str_replace('&layout=table', '', $url); ?>
		<?php $link = str_replace('?layout=table', '', $url); ?>
		<a href="<?= $link; ?>"><i class="fa fa-th fa-2x"></i></a>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="items">
	<table class="table table-hover">
	<tr>
		<!--<th><?= JText::_('COM_BOTIGA_ITEM_IMAGE'); ?></th>-->
		<th><?= JText::_('COM_BOTIGA_ITEM_TITLE'); ?></th>
		<th><?= JText::_('COM_BOTIGA_ITEM_PRICE'); ?></th>
		<th><?= JText::_('COM_BOTIGA_ITEM_REF'); ?></th>
		<th><?= JText::_('COM_BOTIGA_ITEM_BRAND'); ?></th>
		<th></th>
		<th></th>
	</tr>
	<?php 
	if(count($this->items)) :
	$i = 0;
	foreach($this->items as $item) : ?>			
		<?php $item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg'; ?>

		<?php 
		$precio = botigaHelper::getUserPrice($item->id);
		$precio < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $precio; 
		?>
		<tr>
			<!--<td width="15%"><img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" /></td>-->
			<td><a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>&Itemid=133"><strong><?= $item->name; ?></strong></a></td>
			<td><?php if($showprices == 1) : ?><?= $price; ?>&euro;<?php endif; ?></td>
			<td><?= $item->ref; ?></td>
			<td><?= $item->brandname; ?></td>
			<td><a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><i class="fa fa-shopping-cart"></i> <?= JText::_('Comprar'); ?></a></td>
			<td>
			<?php if(!botigaHelper::isFavorite($item->id)) : ?>
						<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=setFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart"></span> Favorito</a>
					<?php else : ?>
						<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart red"></span> No Favorito</a>
					<?php endif; ?>
			</td>
		</tr>
	<?php
	$i++;
	endforeach; 
	else:
	?>
	<?= JText::_('COM_BOTIGA_NO_ITEMS'); ?>
	<?php
	endif;
	?>
	</table>
	
	<div class="clearfix"></div>
	
	<div class="paginacion">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	
</div>

<?php if($modal == 1) : ?>
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
