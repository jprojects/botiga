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
$uri 		= base64_encode(JFactory::getURI()->toString());
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$lang 		= JFactory::getLanguage()->getTag();
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

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_ITEMS_TITLE'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<?php if(botigaHelper::getParameter('show_prices', 0) == 1 && $user->guest) : ?>
	<div class="alert alert-warning"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', 'index.php?option=com_botiga&view=register&Itemid=113'); ?></div>
	<?php endif; ?>
	
	<?php 
	if(count($this->items)) :
	$i = 0;
	foreach($this->items as $item) : ?>
		<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>

		<?php 
		$precio = botigaHelper::getUserPrice($item->id);
		$precio < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $precio; 
		if($i == 4) { echo '<div class="clearfix"></div>'; $i = 0; }
		?>
		<div class="col-xs-12 col-sm-6 col-md-3 item zoom">
		<div class="wrapper">
			<div class="item-wrap">
				<div class="botiga-img">
					<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>&Itemid=115">
						<?php if(!$user->guest && $item->pvp > 0 && botigaHelper::getParameter('show_prices', 1) == 1) : ?>
						<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $price); ?> %</span></div>
						<?php endif; ?>
						<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
					</a>
				
				</div>	
				<div class="text-left item-title"><strong><?= $item->name; ?></strong></div>
				<div class="text-left"><?= $item->ref; ?></div>
				<div class="text-left"><?= $item->brandname; ?></div>
				<?php if(botigaHelper::getParameter('show_prices', 1) == 1) : ?>
				<div class="text-left bold price"><?php if(!$user->guest) : ?><?= $price; ?>&euro;<?php endif; ?></div>
				<?php if(!$user->guest) : ?>
				<div class="text-left faded pvp"><?php if(!$user->guest) : ?>PVP <strike><?= $item->pvp; ?> &euro;</strike><?php endif; ?></div>
				<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="botiga-btns" style="display:none;">
			
				<div class="btn-group btn-group-justified" role="group">
				  <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&Itemid=115'); ?>" class="btn btn-primary"><i class="fa fa-eye"></i><br><?= JText::_('Ver'); ?></a>
				  <a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary"><i class="fa fa-shopping-cart"></i><br><?= JText::_('Comprar'); ?></a>
				  <a href="index.php?option=com_botiga&task=setFavorite&id=<?= $prod->id; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-heart"></span><br>Favorito</a>
				</div>
				
				
				<?php if(botigaHelper::getParameter('show_ask', 1) == 1) : ?>
				<a data-toggle="modal" data-name="<?= $item->name; ?>" data-target="#budget" class="btn btn-default btn-block"><?= JText::_('COM_BOTIGA_MORE_INFO'); ?></a>
				<?php endif; ?>
				
			</div>
		</div>
		</div>
	<?php 
	$i++;
	endforeach; 
	endif;
	?>


	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	
	<?php if(botigaHelper::getParameter('show_ask', 1) == 1) : ?>
	<!-- Modal -->
	<div class="modal fade" id="budget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><strong><?= JText::_('COM_BOTIGA_BUDGET_TITLE'); ?></strong></h4>
		  </div>
		  <div class="modal-body">
			<form id="budgetForm" name="budgetForm" action="index.php?option=com_botiga&task=sendModalEmail" method="post">
				<input type="hidden" name="url" value="<?= JUri::getInstance(); ?>" />
				<div class="form-group">
					<input type="text" name="maquina" id="modal-maquina" value="" />
				</div>
				<div class="form-group">
					<input type="text" name="nombre" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_NAME'); ?>" />
				</div>
				<div class="form-group">
					<input type="text" name="email" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_EMAIL'); ?>" />
				</div>
				<div class="form-group">
					<input type="text" name="phone" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_PHONE'); ?>" />
				</div>
				<div class="form-group">
					<textarea style="width:100%" rows="8" name="mensaje" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_MESSAGE'); ?>"></textarea>
				</div>
			</form>
		  </div>
		  <div class="modal-footer">
		  	<span class="small" style="margin-right:20px;"><?= JText::_('COM_BOTIGA_BUDGET_TOS'); ?></span>
			<button onclick="budgetForm.submit();" type="button" class="btn btn-default btn-rounded"><?= JText::_('COM_BOTIGA_SEND'); ?></button>
		  </div>
		</div>
	  </div>
	</div>
	<?php endif; ?>
	
</div>
