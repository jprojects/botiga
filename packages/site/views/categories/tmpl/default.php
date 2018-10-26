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
if($lang == 'ca-ES') { $itemid = 123; }
if($lang == 'es-ES') { $itemid = 114; }
if($lang == 'en-GB') { $itemid = 130; }
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

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_CATEGORIES_TITLE'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="items">
	<?php 
	if(count($this->items)) :
	$i = 0;
	foreach($this->items as $item) : 
	$params  = json_decode($item->params);
	if($marca == '') {
		if($params->image != '') {
			$image = $params->image;
		} else {
			$image = 'images/default.jpg';
		}
	}
	?>	
		<!-- Feature Item <?= $i; ?> -->
		<div class="col-md-4 text-center">
			<div class="thumbnail">
			<?php 
			if(count($model->getSubCats($item->id))) {
				$link = 'index.php?option=com_botiga&view=categories&catid='.$item->id.'&Itemid='.$itemid;
			} else {
				$link = 'index.php?option=com_botiga&view=botiga&catid='.$item->id.'&Itemid='.$itemid;
			}
			?>
			<a href="<?= $link; ?>">
			<img class="img-responsive" src="<?= $image; ?>" alt="<?= $item->title; ?>">
			<div class="center"><span><?= $item->title; ?></span></div>
			<div class="mask"></div>
			</a>
			</div>
		</div>
	<?php
	$i++;
	endforeach; 
	endif;
	?>

	<div class="clearfix"></div>
	
</div>

