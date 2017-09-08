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

<script>
jQuery(document).ready(function() {
	jQuery('.tree-toggle').click(function (e) {
		e.preventDefault();
		var id    = jQuery(this).attr('data-id');
		var href  = jQuery(this).attr('href');
		var level = jQuery(this).attr('data-level');
		
		if(level == 1) { 		
			jQuery.removeCookie('BotigaMenu1'); 
			jQuery.removeCookie('BotigaMenu2'); 
			jQuery.removeCookie('BotigaMenu3'); 
			jQuery.cookie("BotigaMenu1", id);
		}
		
		if(level == 2) { 		
			jQuery.cookie("BotigaMenu2", id);
		}
		
		if(level == 3) { 		
			jQuery.cookie("BotigaMenu3", id);
		}
		
		//jQuery(this).addClass('active');
		//jQuery(this).parent().children('ul.tree').toggle(200);	
		document.location.href = href;	
	});
});
</script>

<div class="show_body">

<div class="products-title"><span class="caret"></span> <strong>Productos</strong></div>
			
<ul class="menu sidebar products-sidebar <?= $class_sfx; ?>">

	<div class="row">
  		<div>
    		<div>
        		<div>
        			<?php foreach( $cats as $cat ) : ?>
            		<ul class="sidebar">
            		
               			<li class="parent"><a data-id="<?= $cat->id; ?>" data-level="1" href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112" class="tree-toggle <?php if($_COOKIE['BotigaMenu1'] == $cat->id) : ?>active<?php endif; ?>"><?= $cat->title; ?></a>
               				<?php 
               				//second level
							$subcats = modBotigaMenuHelper::getSubCats($cat->id);
							if(count($subcats) > 0) : ?>
                    		<ul class="tree" <?php if($_COOKIE['BotigaMenu1'] != $cat->id) : ?>style="display:none;"<?php endif; ?>>
                    			<?php 
								$i = 1;
								foreach($subcats as $subcat) : 
								if($subcat->title != '') : ?>
                        		<li><a data-id="<?= $subcat->id; ?>" data-level="2" href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112" class="tree-toggle <?php if($_COOKIE['BotigaMenu2'] == $subcat->id) : ?>active<?php endif; ?>"><?= $subcat->title; ?></a>
                        		<?php endif; 
                        			 
									//third level
									$subcats = modBotigaMenuHelper::getSubCats($subcat->id);
									if(count($subcats) > 0) : ?>
                            		<ul class="tree" <?php if($_COOKIE['BotigaMenu2'] != $subcat->id) : ?>style="display:none;"<?php endif; ?>>
                            			<?php 
										$i = 1;
										foreach($subcats as $subcat) : 
										if($subcat->title != '') : ?>
                                		<li><a data-id="<?= $subcat->id; ?>" data-level="3" href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112" class="<?php if($_COOKIE['BotigaMenu3'] == $subcat->id) : ?>active<?php endif; ?>"><?= $subcat->title; ?></a></li>
                                		<?php 
                                		endif; 
                                		$i++;
                                		endforeach; ?>
                            		</ul>
                            		<?php endif;
                            		
                        		$i++;
                        		endforeach; ?>									
                            		
                        		</li>
                   			</ul>
                   			<?php endif; ?>
                		</li>
                		
               		</ul>
               		<?php endforeach; ?>
        		</div>
    		</div>
    	</div>
	</div>

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
