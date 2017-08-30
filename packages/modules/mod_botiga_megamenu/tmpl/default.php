<?php
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());
$user = JFactory::getUser();
?>

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
								<?php if(count($products)) : ?>                         
		                        <div id="slider<?= $i; ?>" class="carousel slide" data-ride="carousel">
		                          <div class="carousel-inner">
		                          	<?php 
		                          	$j = 0;
		                          	foreach($products as $prod) : ?>
		                            <div class="item <?php if($j == 0) : ?>active<?php endif; ?>">
		                                <a href="#"><img src="<?= $prod->image1; ?>" class="img-responsive" alt="<?= $prod->name; ?>"></a>
		                                <h4><small><?= $prod->s_description; ?></small></h4>  
		                                <?php $user->guest ? $link = '#' : $link = 'index.php?option=com_botiga&task=botiga.setItem&id='.$prod->id.'&return='.$uri; ?>
		                                <a href="<?= $link; ?>" class="btn btn-primary"><?= modBotigaMegamenuHelper::getUserPrice($prod->id); ?> €</a> <a href="index.php?option=com_botiga&task=setFavorite&id=<?= $prod->id; ?>" class="btn btn-default"><span class="glyphicon glyphicon-heart"></span> Favoritos</a>       
		                            </div><!-- End Item -->
		                            <?php 
		                            $j++;
		                            endforeach; ?>             
		                          </div>
		                         
		                          <a class="left carousel-control" href="#slider<?= $i; ?>" role="button" data-slide="prev">
		                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		                            <span class="sr-only">Previous</span>
		                          </a>
		                          <a class="right carousel-control" href="#slider<?= $i; ?>" role="button" data-slide="next">
		                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		                            <span class="sr-only">Next</span>
		                          </a>
		                        </div>
		                        <?php else : ?>
		                        <?php $p = json_decode($cat->params); ?>
		                        <a href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112">
		                        	<img src="<?= $p->image; ?>" class="img-responsive" alt="">
		                        </a>
		                        <?php endif; ?>
		                        <li class="divider"></li>
		                        <li class=" hidden-xs"><a href="index.php?option=com_botiga&view=botiga&catid=<?= $cat->id; ?>&Itemid=112">Ver colección <span class="glyphicon glyphicon-chevron-right pull-right"></span></a></li>
							</ul>
						</li>
					
						<li class="col-sm-3">
							<ul>
								<li class="dropdown-header hidden-xs">Categorias</li>
								<?php foreach(modBotigaMegamenuHelper::getSubCats($cat->id) as $sub) : ?>
								<li><a href="index.php?option=com_botiga&view=botiga&catid=<?= $sub->id; ?>&Itemid=112"><?= $sub->title; ?></a></li>
								<?php endforeach; ?>	
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
			if (count($modules)) {
			    $i = 1;
			    foreach ($modules as $module) {
			        echo '<li class="dropdown pull-right hidden-xs"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge-text">'.$module->title.'</span> <span class="badge">'.modBotigaMegamenuHelper::getCarritoCount().'</span> <span class="caret"></span></a>';
			        echo '<ul class="dropdown-menu">
			        	<li>
			              <div class="container-cart">
			                    '.JModuleHelper::renderModule($module).'
			              </div>
			            </li>
			        </ul></li>';
			        $i++;
			    }
			}
			?>
			<li class="visible-xs"><a href="index.php?option=com_botiga&view=checkout&Itemid=114"><span class="badge-text">Carrito</span> <span class="badge"><?= modBotigaMegamenuHelper::getCarritoCount(); ?></span></a>
		</ul>
	</div><!-- /.nav-collapse -->
  </nav>
</div>

