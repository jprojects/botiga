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

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="row">

		<div id="page-header">
			<h1><?= JText::_('COM_BOTIGA_FAVORITES_TITLE'); ?></h1>
		</div>

		<?php if(botigaHelper::getParameter('show_prices', 0) == 1 && $user->guest) : ?>
		<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
		<?php endif; ?>

		<div class="items">
		<?php
		if(count($this->items)) :
		$i = 0;
		foreach($this->items as $item) : ?>
			<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>

			<?php
			$precio = botigaHelper::getUserPrice($item->id);
			$precio < 1 ? $price = '' : $price = $precio;
			if($i == 4) { echo '<div class="clearfix"></div>'; $i = 0; }
			?>
			<div class="col-xs-12 col-sm-6 col-md-3 item zoom">
			<div class="wrapper">
				<div class="item-wrap">
					<div class="botiga-img">
						<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid=0'); ?>">
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
					  <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid=0'); ?>" class="btn btn-primary"><i class="fa fa-eye"></i><br><?= JText::_('Ver'); ?></a>
					  <a href="#" data-id="<?= $item->id; ?>" class="btn btn-primary <?php if(!$user->guest) : ?>setItem<?php endif; ?>"><i class="fa fa-shopping-cart"></i><br><?= JText::_('Comprar'); ?></a>
					  	<?php if(!botigaHelper::isFavorite($item->id)) : ?>
							<a href="index.php?option=com_botiga&task=setFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>" class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart"></span><br>Favorito</a>
						<?php else : ?>
							<a href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>" class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart red"></span><br>Favorito</a>
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
		</div>

		<div class="paginacion">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>

	</div>
</div>
