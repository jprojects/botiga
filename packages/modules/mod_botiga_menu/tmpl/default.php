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
$collection = $app->input->get('collection', '');
$catid 		= $app->input->get('catid', 0); 
$brands 	= modBotigaMenuHelper::getBrands();
$cols 		= modBotigaMenuHelper::getCollections();
$cats 	    = modBotigaMenuHelper::getCats();
?>

<script>
jQuery(document).ready(function() {
	jQuery('.tree-toggle').click(function (e) {
		e.preventDefault();
		var id    = jQuery(this).attr('data-id');
		var href  = jQuery(this).attr('href');
		var level = jQuery(this).attr('data-level');
		
		//change icon
		jQuery('#products i').removeClass('fa-chevron-down');
		jQuery('#products i').addClass('fa-chevron-down');
		jQuery(this).find('i').removeClass('fa-chevron-right');
		jQuery(this).find('i').addClass('fa-chevron-down');
		
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
	
	jQuery('#marcas').change(function() {
		var id = jQuery(this).val();
		document.location.href = "index.php?option=com_botiga&view=botiga&catid=<?= $catid; ?>&marca="+id+"&Itemid=112";
	});
	jQuery('#products').change(function() {
		var id = jQuery(this).val();
		document.location.href = "index.php?option=com_botiga&view=botiga&catid="+id+"&Itemid=112";
	});
});
</script>

<!-- version mobile -->
<div class="show_body visible-xs visible-sm">

<select name="products" id="products" class="form-control">
	<?php foreach( $cats as $cat ) : ?>
    <optgroup label="<?= $cat->title; ?>">
    	<?php 
        //second level
		$subcats = modBotigaMenuHelper::getSubCats($cat->id);
		if(count($subcats) > 0) : 
        $i = 1;
		foreach($subcats as $subcat) : 
		if($subcat->title != '') : ?>
        <option value="<?= $subcat->id; ?>" <?php if($catid == $subcat->id) : ?>selected<?php endif; ?>><?= $subcat->title; ?></option>
        <?php endif; 
        //third level
		$subcats = modBotigaMenuHelper::getSubCats($subcat->id);
		if(count($subcats) > 0) :
		$i = 1;
		foreach($subcats as $subcat) : 
		if($subcat->title != '') : ?>
        <option value="<?= $subcat->id; ?>" <?php if($catid == $subcat->id) : ?>selected<?php endif; ?>><?= $subcat->title; ?></option>
        <?php 
        endif; 
        $i++;
        endforeach;
 		endif;
        $i++;
        endforeach;									
        endif; ?>
    </optgroup>              			
    <?php endforeach; ?>
</select>

<select name="marcas" id="marcas" class="form-control">
<?php foreach( $brands as $brand ) : ?>
	
	<option value="<?= $brand->id; ?>" <?php if($marca == $brand->id) : ?>selected<?php endif; ?>><?= $brand->name; ?></option>			

<?php endforeach; ?>
</select>

</div>

<!-- version desktop -->
<div class="show_body hidden-xs hidden-sm">

<div class="products-title"><span class="caret"></span> <strong>Productos</strong></div>
			
<ul class="menu sidebar products-sidebar <?= $class_sfx; ?>">

	<div class="row">
  		<div>
    		<div>
        		<div>
        			<?php foreach( $cats as $cat ) : ?>
            		<ul class="sidebar">
            			<?php $subcats = modBotigaMenuHelper::getSubCats($cat->id); ?>          			            			
               			<li class="parent">               			
               				<a data-id="<?= $cat->id; ?>" data-level="1" href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112" class="tree-toggle <?php if($_COOKIE['BotigaMenu1'] == $cat->id) : ?>active<?php endif; ?>">
               				<?php if(count($subcats) > 0 && $_COOKIE['BotigaMenu1'] != $cat->id) : ?><i class="fa fa-chevron-right"></i> <?php endif; ?>
               				<?php if($_COOKIE['BotigaMenu1'] == $cat->id) : ?><i class="fa fa-chevron-down"></i> <?php endif; ?>
               				<?= $cat->title; ?>
               				</a>
               				
               				<?php 
               				//second level					
							if(count($subcats) > 0) : ?>
                    		<ul class="tree" <?php if($_COOKIE['BotigaMenu1'] != $cat->id) : ?>style="display:none;"<?php endif; ?>>
                    			<?php 
								$i = 1;
								foreach($subcats as $subcat) : 
								if($subcat->title != '') : 
								$subcats = modBotigaMenuHelper::getSubCats($subcat->id);
								?>
                        		<li><a data-id="<?= $subcat->id; ?>" data-level="2" href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112" class="tree-toggle <?php if($_COOKIE['BotigaMenu2'] == $subcat->id) : ?>active<?php endif; ?>">
                        		<?php if(count($subcats) > 0 && $_COOKIE['BotigaMenu2'] != $subcat->id) : ?><i class="fa fa-chevron-right"></i> <?php endif; ?>
                        		<?php if($_COOKIE['BotigaMenu2'] == $subcat->id) : ?><i class="fa fa-chevron-down"></i> <?php endif; ?>
                        		<?= $subcat->title; ?>
                        		</a>
                        		<?php endif; 
                        			 
									//third level									
									if(count($subcats) > 0) : ?>
                            		<ul class="tree" <?php if($_COOKIE['BotigaMenu2'] != $subcat->id) : ?>style="display:none;"<?php endif; ?>>
                            			<?php 
										$i = 1;
										foreach($subcats as $subcat) : 
										if($subcat->title != '') : ?>
                                		<li><a data-id="<?= $subcat->id; ?>" data-level="3" href="index.php?option=com_botiga&view=botiga&catid=<?= $subcat->id; ?>&Itemid=112" class=" tree-toggle <?php if($_COOKIE['BotigaMenu3'] == $subcat->id) : ?>active<?php endif; ?>"><?= $subcat->title; ?></a></li>
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

<?php if($params->get('brands', 1) == 1) : ?>
<div class="brands-title"><span class="caret"></span> <strong>Marcas</strong></div>

<ul class="menu sidebar brands-sidebar <?= $class_sfx; ?>">

<?php foreach( $brands as $brand ) : ?>
	
	<li class="parent <?php if($marca == $brand->id) : ?>active<?php endif; ?>">
		<a href="index.php?option=com_botiga&view=botiga&catid=<?= $catid; ?>&marca=<?= $brand->id; ?>&Itemid=112"><?= $brand->name; ?></a>
	</li>			

<?php endforeach; ?>

</ul>
<?php endif; ?>

<?php if($params->get('collections', 1) == 1) : ?>
<div class="brands-title"><span class="caret"></span> <strong>Colecciones</strong></div>

<ul class="menu sidebar brands-sidebar <?= $class_sfx; ?>" <?php if($marca == 0) : ?>style="display:none;"<?php endif; ?>>

<?php foreach( $cols as $col ) : ?>
	
	<li class="parent <?php if($collection == $col->collection) : ?>active<?php endif; ?>">
		<a href="index.php?option=com_botiga&view=botiga&catid=<?= $catid; ?>&marca=<?= $marca; ?>&collection=<?= trim($col->collection); ?>&Itemid=112"><?= trim($col->collection); ?></a>
	</li>			

<?php endforeach; ?>

</ul>
<?php endif; ?>

</div>
