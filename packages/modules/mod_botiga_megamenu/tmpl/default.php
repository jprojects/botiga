<?php
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());
$user = JFactory::getUser();
?>

<script>
jQuery(document).ready(function() {
	jQuery('.mega-link').click(function (e) {
		e.preventDefault();
		var id    = jQuery(this).attr('data-id');
		var catid    = jQuery(this).attr('data-catid');
		var href  = jQuery(this).attr('href');
		
 		jQuery.cookie("BotigaMenu1", catid);
		jQuery.cookie("BotigaMenu2", id);
			
		document.location.href = href;	
	});
});
</script>

<div class="megamenu">
  <nav class="navbar navbar-inverse fixed">
    <div class="navbar-header">
    	<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	
	<div class="collapse navbar-collapse js-navbar-collapse">
		<ul class="nav navbar-nav" style="width:100%;">
			<?php 
			$i = 0;
			foreach(modBotigaMegamenuHelper::getCats() as $cat) :
			if(!in_array($cat->id, $disallowed)) :
				$products = modBotigaMegamenuHelper::getItemsSlide($cat->id); ?>
				<li class="dropdown mega-dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $cat->title; ?> <span class="caret"></span></a>				
					<ul class="dropdown-menu mega-dropdown-menu">
						<li class="col-sm-3 hidden-xs">
							<ul>
								<li class="dropdown-header"><?= $cat->title; ?></li>  								 
		                        <?php $p = json_decode($cat->params); ?>
		                        <a class="mega-link" data-id="<?= $cat->id; ?>" href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112">
		                        	<img src="<?= $p->image; ?>" class="img-responsive" alt="">
		                        </a>
		                        <?php //endif; ?>
							</ul>
						</li>
					
						<li class="col-sm-3">
							<ul>
								<li class="dropdown-header hidden-xs"><a href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112">Ver todo</a></li>
								<?php 
								$i = 0;
								foreach(modBotigaMegamenuHelper::getSubCats($cat->id) as $sub) : ?>
								<?php if($i == 4) { echo '</ul></li><li class="col-sm-3"><ul><li class="dropdown-header hidden-xs">&nbsp;</li>'; $i = 0; } ?>
								<li><a class="mega-link" data-catid="<?= $cat->id; ?>" data-id="<?= $sub->id; ?>" href="index.php?option=com_botiga&view=botiga&catid=<?= $sub->id; ?>&Itemid=112"><?= $sub->title; ?></a></li>
								<?php 
								$i++;
								endforeach; ?>	
							</ul>
						</li>
								
					</ul>												
				</li>			
			<?php 
			endif;
			$i++;
			endforeach; ?>
			<?php
			$modules = JModuleHelper::getModules('jpf-inside-menu');
			$counter = modBotigaMegamenuHelper::getCarritoCount();
			
			if (count($modules) && !$user->guest) {
				$i = 1;
				foreach ($modules as $module) {
				    echo '<li class="dropdown pull-right hidden-xs carrito-list"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge-text">'.$module->title.'</span> <span class="badge cart-count">'.$counter.'</span> <span class="caret"></span></a>';
				    if($counter >= 0) {
						echo '<ul class="dropdown-menu">
							<li>
						      <div class="container-cart">
						            '.JModuleHelper::renderModule($module).'
						      </div>
						    </li>
						</ul>';
				    }
				    echo '</li>';
				    $i++;
				}
			}
			
			?>
			<?php $countfavs = modBotigaMegamenuHelper::getFavsCount(); ?>
			<?php if(!$user->guest) : ?>
			<li class="hidden-xs pull-right"><a href="index.php?option=com_botiga&view=favorites&Itemid=116"><span class="badge-text">Favoritos</span> <span class="fav-count badge"><?= $countfavs; ?></span> </a></li>
			<?php endif; ?>
			<li class="visible-xs"><a href="index.php?option=com_botiga&view=favorites&Itemid=116"><span class="badge-text">Favoritos</span><span class="badge"><?= $countfavs; ?></span> </a></li>
			<li class="visible-xs"><a href="index.php?option=com_botiga&view=checkout&Itemid=114"><span class="badge-text">Carrito</span> <span class="badge"><?= $counter; ?></span></a>
			<li class="visible-xs"><a href="index.php?option=com_users&task=user.logout&<?= JSession::getFormToken(); ?>=1">Salir</a></li>
		</ul>
	</div><!-- /.nav-collapse -->
  </nav>
</div>

