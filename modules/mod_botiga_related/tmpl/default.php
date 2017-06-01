<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_botiga_related
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$items = modRelatedItemsHelper::getItems();
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());
?>

<style>
.thumbnail > img, .thumbnail > a > img { height: 250px; } 
</style>

<div class="row relateditems <?= $moduleclass_sfx; ?>">
	<?php foreach ($items as $item) :	?>
	<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
	<div class="col-xs-12 col-md-4 item">
		<div class="thumbnail">
		<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&Itemid=135'); ?>">
			<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
		</a>
		</div>
		<div class="text-center item-title"><strong><?= $item->name; ?></strong></div>
		<div class="text-center"><?= modRelatedItemsHelper::getBrandName($item->marca); ?>/<?= modRelatedItemsHelper::getCategoryName($item->catid); ?></div>
		<div class="text-center"><?= $item->ref; ?></div>
		<a <?php if(!JFactory::getUser()->guest) : ?>href="index.php?option=combotiga&task=laundry.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary btn-block btn-black"><?= JText::_('COM_LAUNDRY_BUY'); ?> <i class="fa fa-shopping-cart"></i></a>
	</div>
	<?php endforeach; ?>
</div>
