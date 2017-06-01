<?php

/**
* @version		$Id: mod_botiga_menu  Kim $
* @package		mod_botigamenu v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Restricted access');
$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));

$app        = JFactory::getApplication();
$lang 		= $app->input->get('lang', 'ca');
$marca 		= $app->input->get('marca', 0);
$brands 	= modLaundryMenuHelper::getBrands();
$itemid 	= modLaundryMenuHelper::getItemid();
?>

<div class="show_body">
			
<ul class="menu sidebar <?= $class_sfx; ?>">

<?php foreach( $brands as $brand ) : ?>

	<li class="parent <?php if(count($cats)) : ?>deeper<?php endif; ?>" <?php if($footer == 1) : ?>style="padding:0;"<?php endif; ?>>
		<a href="index.php?option=com_botiga&view="><?= strtoupper($brand->name); ?></a>
		<?php 
		$categories = modLaundryMenuHelper::getCategoriesByBrand($brand->id);
		if(count($categories) > 0 && $marca == $brand->id) : ?>
			<ul>
				<?php 
				$i = 1;
				foreach($categories as $cat) : 
				$title = modLaundryMenuHelper::getCatName($cat->catid); 
				if($title != '') : ?>
				<li <?php if($count == $i) : ?>style="border:none;"<?php endif; ?>>
					<a href="index.php?option=com_botiga&view="><?= $title; ?></a>
				</li>
				<?php
				endif; 
				$i++;
				endforeach; ?>
			</ul>
		<?php endif; ?>
		
	</li>			

<?php endforeach; ?>

</ul>
</div>
