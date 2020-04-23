<?php

/**
* @version		$Id: mod_botiga_menu  Kim $
* @package		mod_botigamenu v 1.0.0
* @copyright  Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Restricted access');
$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$marcas	    = $params->get('brands', 0);
$itemid	    = $params->get('itemid', 999);
$color		  = $params->get('color');

$app      = JFactory::getApplication();
$lang 		= $app->input->get('lang', 'ca');
$marca 		= $app->input->get('marca', 0);
$catid 		= $app->input->get('catid', 0);
$brands 	= modBotigaMenuHelper::getBrands();
$cats 	  = modBotigaMenuHelper::getCats();
?>

<div id="botiga_menu" class="jquery-accordion-menu <?= $class_sfx; ?>" style="background-color:<?= $color; ?>;">
	<div class="jquery-accordion-menu-header"><?= JText::_('MOD_BOTIGA_MENU_ITEMS'); ?> </div>
	<ul>
		<?php foreach( $cats as $cat ) : ?>
		<li <?php if($cat->id == $catid) : ?>class="active"<?php endif; ?>><a href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=<?= $itemid; ?>"><?= $cat->title; ?> </a>
			<?php
			$subcats   = modBotigaMenuHelper::getSubCats($cat->id);
			$subcatsId = modBotigaMenuHelper::getSubCatsId($cat->id);
			if(count($subcats) > 0) : ?>
			<ul class="submenu" <?php if(in_array($catid, $subcatsId)) : ?>style="display:block;"<?php endif; ?>>
			<?php endif; ?>
				<?php
				$i = 1;
				foreach($subcats as $subcat) : ?>
				<li <?php if($subcat->id == $catid) : ?>class="active"<?php endif; ?>><a href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=<?= $itemid; ?>"><?= $subcat->title; ?> </a>
					<?php
					$subcats   = modBotigaMenuHelper::getSubCats($subcat->id);
					$subcatsId = modBotigaMenuHelper::getSubCatsId($subcat->id);
					if(count($subcats) > 0) : ?>
					<ul class="submenu" <?php if(in_array($catid, $subcatsId)) : ?>style="display:block;"<?php endif; ?>>
					<?php endif; ?>
						<?php
						$i = 1;
						foreach($subcats as $subcat) : ?>
						<li <?php if($subcat->id == $catid) : ?>class="active"<?php endif; ?>><a href="#"><?= $subcat->title; ?> </a></li>
						<?php
						$i++;
						endforeach; ?>
					<?php if(count($subcats) > 0) : ?>
					</ul>
					<?php endif; ?>
				</li>
				<?php
				$i++;
				endforeach; ?>
			<?php if(count($subcats) > 0) : ?>
			</ul>
			<?php endif; ?>
		</li>
	</ul>
	<?php endforeach; ?>
	<?php if($brands == 1) : ?>
	<div class="jquery-accordion-menu-footer"><?= JText::_('MOD_BOTIGA_MENU_BRANDS'); ?> </div>
	<ul class="submenu">
	<?php foreach( $brands as $brand ) : ?>
		<li <?php if($marca == $brand->id) : ?>class="active"<?php endif; ?>>
			<a href="index.php?option=com_botiga&view=botiga&catid=<?= $catid; ?><?php if($marca != $brand->id) : ?>&marca=<?= $brand->id; ?><?php endif; ?>&Itemid=<?= $itemid; ?>"><?= $brand->name; ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
