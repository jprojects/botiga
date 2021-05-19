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
$model 		= $this->getModel('categories');
$user  		= JFactory::getUser();
$jinput		= JFactory::getApplication()->input;
$catid      = $jinput->get('catid', '');
$marca      = $jinput->get('marca', '');
$itemid     = $jinput->get('Itemid', 999);
$lang       = JFactory::getLanguage()->getTag();
$shownotice = $this->params->get('show_notice', 1);
?>

<style>
.thumbnail {
    position:relative;
}
.thumbnail img {
    max-height: 288px;
    min-height: 288px;
}
.center {
    position: absolute;
    left: 0;
    top: 40%;
    width: 100%;
    text-align: center;
    font-size: 28px;
    color: #fff;
    font-weight: bold;
    z-index:1000;
}
.center span { text-shadow: 1px 1px #575757; }
</style>

<?php if($this->params->get('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="row">

	<?php if($user->guest && $shownotice == 1) : ?>
	<div class="col-12 my-5">
		<?php $link1 = 'index.php?option=com_botiga&view=register'; ?>
		<div class="alert alert-warning"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', $link1); ?></div>
	</div>
	<?php endif; ?>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_CATEGORIES_TITLE'); ?></h1>
	</div>
	
	<div class="col-12 items my-5">
		<div class="row">
		<?php 
		if(count($this->items)) :
		$i = 0;
		foreach($this->items as $item) : 
		$params  = json_decode($item->params);
		if($marca == '') {
			if($params->image != '') {
				$image = $params->image;
			} else {
				$image = '/components/com_botiga/assets/images/default.jpg';
			}
		}
		?>	
			<!-- Feature Item <?= $i; ?> -->
			<div class="col-md-4 text-center">
				<div class="thumbnail">
					<img class="img-fluid" src="<?= $image; ?>" alt="<?= $item->title; ?>">
					<div class="center">
						<span><?= $item->title; ?></span>
						<div>
						<?php if(count($model->getSubCats($item->id))) : ?>
						<a class="btn btn-primary btn-block" href="index.php?option=com_botiga&view=categories&catid=<?= $item->id; ?>&Itemid=<?= $itemid; ?>">Categories</a>
						<?php endif; ?>
						<a class="btn btn-primary btn-block" href="index.php?option=com_botiga&view=botiga&catid=<?= $item->id; ?>&Itemid=<?= $itemid; ?>">Products</a>
						</div>
					</div>
					<div class="mask"></div>
				</div>
			</div>
		<?php
		$i++;
		endforeach; 
		endif;
		?>
		</div>
	</div>
</div>

