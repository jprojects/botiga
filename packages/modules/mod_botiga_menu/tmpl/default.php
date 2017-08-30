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
$catid 		= $app->input->get('catid', 0); 
$brands 	= modBotigaMenuHelper::getBrands();
$cats 	    = modBotigaMenuHelper::getCats();
?>

<div class="show_body">

<div class="products-title"><span class="caret"></span> <strong>Productos</strong></div>
			
<ul class="menu sidebar products-sidebar <?= $class_sfx; ?>">

<?php foreach( $cats as $cat ) : ?>
	
	<li class="parent <?php if(count($cats)) : ?>deeper<?php endif; ?> <?php if($cat->id == $catid) : ?>active<?php endif; ?>">
		<a href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112"><?= strtoupper($cat->title); ?></a>
		<?php 
		$subcats = modBotigaMenuHelper::getSubCats($cat->id);
		if(count($subcats) > 0) : ?>
			<ul>
				<?php 
				$i = 1;
				foreach($subcats as $subcat) : 
				if($subcat->title != '') : ?>
				<li <?php if($count == $i) : ?>style="border:none;"<?php endif; ?> <?php if($subcat->id == $catid) : ?>class="active"<?php endif; ?>>
					<a href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112"><?= $subcat->title; ?></a>
					<?php 
					$subcats = modBotigaMenuHelper::getSubCats($subcat->id);
					if(count($subcats) > 0) : ?>
						<ul <?php if($subcat->id != $catid) : ?>style="display:none;"<?php endif; ?>>
							<?php 
							$i = 1;
							foreach($subcats as $subcat) : 
							if($subcat->title != '') : ?>
							<li <?php if($count == $i) : ?>style="border:none;"<?php endif; ?> <?php if($subcat->id == $catid) : ?>class="active"<?php endif; ?>>
								<a href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112"><?= $subcat->title; ?></a>
								<?php 
								$subcats = modBotigaMenuHelper::getSubCats($subcat->id);
								if(count($subcats) > 0) : ?>
									<ul <?php if($subcat->id != $catid || $subcat->parent_id != $catid) : ?>style="display:none;"<?php endif; ?>>
										<?php 
										$i = 1;
										foreach($subcats as $subcat) : 
										if($subcat->title != '') : ?>
										<li <?php if($count == $i) : ?>style="border:none;"<?php endif; ?> <?php if($subcat->id == $catid) : ?>class="active"<?php endif; ?>>
											<a href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112"><?= $subcat->title; ?></a>
										</li>
										<?php
										endif; 
										$i++;
										endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
							<?php
							endif; 
							$i++;
							endforeach; ?>
						</ul>
					<?php endif; ?>
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

<div class="brands-title"><span class="caret"></span> <strong>Marcas</strong></div>

<ul class="menu sidebar brands-sidebar <?= $class_sfx; ?>">

<?php foreach( $brands as $brand ) : ?>
	
	<li class="parent <?php if($marca == $brand->id) : ?>active<?php endif; ?>">
		<a href="index.php?option=com_botiga&view=botiga&catid=<?= $catid; ?>&marca=<?= $brand->id; ?>&Itemid=112"><?= $brand->name; ?></a>
	</li>			

<?php endforeach; ?>

</ul>
</div>
