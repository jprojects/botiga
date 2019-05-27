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
					<a href="index.php?option=com_users&view=login" title="Login" class="hasTip pr-1">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
					<?php else: ?>
					<a href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" class="hasTip pr-1">
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

		<div id="page-header">
			<h1><?= JText::_('COM_BOTIGA_ITEMS_TITLE'); ?></h1>
		</div>
		
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
		
		<div class="paginacion">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>	
</div>

<?php if($modal == 1) : ?>
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
