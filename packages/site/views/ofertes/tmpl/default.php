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
$model 		= $this->getModel('ofertes');
$user  		= JFactory::getUser();
$uri 		= base64_encode(JFactory::getURI()->toString());
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$itemid     = $jinput->get('Itemid', '');
$orderby    = $jinput->get('orderby', 'ref');
$limit      = $jinput->get('limit', 24);
$lang 		= JFactory::getLanguage()->getTag();
$showprices = botigaHelper::getParameter('show_prices', 1);
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
		
		<div class="clearfix"></div>
		
		<?php if($showprices == 1 && $user->guest) : ?>
		<a href="index.php?option=com_botiga&view=register&Itemid=113">
		<div class="alert alert-danger"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
		</a>
		<?php endif; ?>
		
		<div class="items">
		<?php 
		if(count($this->items)) :
		$i = 0;
		foreach($this->items as $item) : ?>
			<?php $item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg'; ?>

			<?php 
			$precio = botigaHelper::getUserPrice($item->id);
			$precio < 1 ? $price = '' : $price = $precio; 
			if($i == 4) { echo '<div class="clearfix"></div>'; $i = 0; }
			?>
			<div class="col-xs-12 col-sm-6 col-md-3 item zoom">
			<div class="wrapper">
				<div class="item-wrap">
					<div class="botiga-img">
						<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid='.$catid); ?>&Itemid=115">
							<?php if(!$user->guest && $item->pvp > 0 && $showprices == 1) : ?>
							<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $price); ?> %</span></div>
							<?php endif; ?>
							<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
						</a>
					
					</div>
					<div class="text-left item-ref"><?= $item->ref; ?></div>	
					<div class="text-left"><strong><?= $item->name; ?></strong></div>
					<div class="text-left"><strong><?= $item->s_description; ?></strong></div>				
					<div class="text-left"><?= $item->brandname; ?></div>
					<div class="item-divider"></div>
					<?php if($showprices == 1) : ?>
					<div class="text-left bold price"><?php if(!$user->guest) : ?><?= $price; ?>&euro;<?php endif; ?></div>
					<?php if(!$user->guest) : ?>
					<div class="text-left faded pvp"><?php if(!$user->guest) : ?>PVP <strike><?= $item->pvp; ?> &euro;</strike><?php endif; ?></div>
					<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="botiga-btns" style="display:none;">
				
					<div class="btn-group btn-group-justified" role="group">
					  <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid='.$catid.'&Itemid=115'); ?>" class="btn btn-primary"><i class="fa fa-eye"></i><br><?= JText::_('Ver'); ?></a>
					 <a href="#" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> data-id="<?= $item->id; ?>" class="btn btn-primary <?php if(!$user->guest) : ?>setItem<?php endif; ?>"><i class="fa fa-shopping-cart"></i><br><?= JText::_('Comprar'); ?></a>
					  	<?php if(!botigaHelper::isFavorite($item->id)) : ?>
							<a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="btn btn-group btn-primary <?php if(!$user->guest) : ?>setFavorite<?php endif; ?> item<?= $item->id; ?>"><span class="heart glyphicon glyphicon-heart"></span><br>Favorito</a>
						<?php else : ?>
							<a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="btn btn-group btn-primary <?php if(!$user->guest) : ?>unsetFavorite<?php endif; ?> item<?= $item->id; ?>"><span class="heart glyphicon glyphicon-heart red"></span><br>Favorito</a>
						<?php endif; ?>
					</div>
					
				</div>
			</div>
			</div>
		<?php
		$i++;
		endforeach; 
		endif;
		?>
		
		<div class="paginacion">
			<?= $this->pagination->getPagesLinks(); ?>
		</div>

	</div>
	
</div>

<?php if($modal == 1) : ?>
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
