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
$model 		= $this->getModel('favorites');
$user  		= JFactory::getUser();
$uri 		= base64_encode(JFactory::getURI()->toString());
$jinput		= JFactory::getApplication()->input;
$lang 		= JFactory::getLanguage()->getTag();
?>

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_FAVORITES_TITLE'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<?php if(botigaHelper::getParameter('show_prices', 0) == 1 && $user->guest) : ?>
	<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
	<?php endif; ?>
	
	<?php 
	if(count($this->items)) :
	foreach($this->items as $item) : ?>
		<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>

		<?php 
		$precio = botigaHelper::getUserPrice($item->id);
		$precio < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $precio; 
		?>
		<div class="col-xs-12 col-sm-6 col-md-3 item zoom">
			<div class="botiga-img">
				<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>&Itemid=115">
					<?php if(!$user->guest && $item->pvp > 0 && botigaHelper::getParameter('show_prices', 1) == 1) : ?>
					<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $price); ?> %</span></div>
					<?php endif; ?>
					<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
				</a>
				
			</div>	
			<div class="text-left item-title"><strong><?= $item->name; ?></strong></div>
			<div class="text-left"><?= $item->brand; ?>/<?= $item->catid; ?></div>
			<div class="text-left"><?= $item->ref; ?></div>
			<div class="text-left"><?= $item->brandname; ?></div>
			<?php if(botigaHelper::getParameter('show_prices', 1) == 1) : ?>
			<div class="text-left bold price"><?= $price; ?> &euro;</div>
			<?php if(!$user->guest) : ?>
			<div class="text-left faded pvp">PVP <strike><?= $item->pvp; ?> &euro;</strike></div>
			<?php endif; ?>
			<?php endif; ?>
			<div class="botiga-btns">
				<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary btn-block btn-primary"><?= JText::_('COM_BOTIGA_BUY'); ?> <i class="fa fa-shopping-cart"></i></a>
				<a href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>" class="btn btn-primary btn-block btn-primary"><?= JText::_('COM_BOTIGA_UNSET_FAVORITE'); ?> <i class="fa fa-heart"></i></a>
			</div>
			<hr>
		</div>
	<?php 
	endforeach; 
	endif;
	?>


	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	
</div>

