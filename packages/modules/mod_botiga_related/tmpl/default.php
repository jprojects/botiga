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

// no direct access
defined('_JEXEC') or die;
$items = modRelatedItemsHelper::getItems();
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());
$show_prices = botigaHelper::getParameter('show_prices', 1);
?>

<style>
.thumbnail > img, .thumbnail > a > img { height: 250px; } 
.item-title, .item-brand, .item-ref { height: 20px; }
</style>

<div class="row relateditems <?= $moduleclass_sfx; ?>">
	<?php foreach ($items as $item) :	?>
	<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
	<div class="col-xs-12 col-md-3 item">
		<div class="thumbnail">
		<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&catid=0&id='.$item->id.'&Itemid=115'); ?>">
			<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
		</a>
		</div>
		<div class="text-left item-title"><strong><?= $item->name; ?></strong></div>
		<div class="text-left item-brand"><?= modRelatedItemsHelper::getBrandName($item->brand); ?></div>
		<div class="text-left item-ref"><?= $item->ref; ?></div>
		<?php if($show_prices == 1) : ?>
		<a <?php if(!JFactory::getUser()->guest) : ?>href="index.php?option=combotiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary btn-block btn-black"><?= JText::_('COM_BOTIGA_BUY'); ?> <i class="fa fa-shopping-cart"></i></a>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>
