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
$this->item->image1 != '' ? $image = $this->item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg';
$model 		= $this->getModel('item');
$user  		= JFactory::getUser();
$doc   		= JFactory::getDocument();
$jinput		= JFactory::getApplication()->input;
$precio 	= botigaHelper::getUserPrice($this->item->id);
$uri 		= base64_encode(JFactory::getURI()->toString());
$modal 		= $jinput->get('m', 0);
$showprices = botigaHelper::getParameter('show_prices', 1);
$loginprices = botigaHelper::getParameter('login_prices', 0);
$shownotice = botigaHelper::getParameter('show_notice', 1);
$showref 	= botigaHelper::getParameter('show_ref_item', 1);
$showsdesc 	= botigaHelper::getParameter('show_sdesc_item', 1);
$showdesc 	= botigaHelper::getParameter('show_desc_item', 1);
$showgar 	= botigaHelper::getParameter('show_garantia_item', 1);
$showenvio 	= botigaHelper::getParameter('show_envio_item', 1);
$showbrand 	= botigaHelper::getParameter('show_brand_item', 1);
$showfav 	= botigaHelper::getParameter('show_fav_item', 1);
$showpvp 	= botigaHelper::getParameter('show_pvp_item', 1);
$showrel 	= botigaHelper::getParameter('show_related_item', 1);

$js = '{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "'.$this->item->name.'",
  "image": "'.JURI::root().$image.'",
  "description": "'.$this->item->brand.'",
  "mpn": "'.$this->item->ref.'",
  "brand": {
    "name": "'.$this->item->brand.'"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "EUR",
    "seller": {
      "@type": "Organization",
      "name": "Dicohotel S.L."
    }
  }
}';
$doc->addScriptDeclaration($js, 'application/ld+json');
$doc->addScript('components/com_botiga/assets/js/jquery.fancybox.js');
$doc->addStylesheet('components/com_botiga/assets/css/jquery.fancybox.css');
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) : ?>
<header class="head_botiga">

	<div class="col-md-11 mx-auto">

		<div class="row">

		<?php if(botigaHelper::getParameter('botiga_logo', '') != '') : ?>
		<div class="col-12 text-right">
			<img src="<?= botigaHelper::getParameter('botiga_logo', ''); ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid">
		</div>
		<?php endif; ?>	
		
		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-xs-12 col-md-6 text-left">&nbsp;</div>
				<div class="col-xs-12 col-md-6 text-right">
					<a href="index.php?option=com_botiga&view=checkout" class="pr-1 carrito">
						<?php if(botigaHelper::getCarritoCount() > 0) : ?>
						<span class="badge badge-warning"><?= botigaHelper::getCarritoCount(); ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>
					<a href="index.php?option=com_users&view=login">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
				</div>
			</div>
		</div>
		
		</div>	
	
	</div>
	
</header>
<?php endif; ?>	

<div class="col-md-8 mx-auto">

	<div class="row">
	
		<?php if($showprices == 1 && ($loginprices == 0 && $user->guest) && $shownotice == 1) : ?>
		<div class="col-12">
			<a href="index.php?option=com_botiga&view=register">
				<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
			</a>
		</div>
		<?php endif; ?>	
		
		<div class="col-12 mt-4 mb-5">
			<h2 class="estil08 text-primary"><?= strtoupper(botigaHelper::getCategoryName($this->item->catid)); ?></h2>
		</div>

		<div class="col-12 col-md-5">			
	
			<div class="botiga-img">
				<a href="<?= $image; ?>" data-fancybox="gallery">
				<?php if(!$user->guest && $this->item->pvp > 0 && $show_prices == 1) : ?>
				<div class="pvp-badge big"><span><?= botigaHelper::getPercentDiff($this->item->pvp, $precio); ?>%</span></div>
				<?php endif; ?>
				<span class="rollover"></span>
					<img src="<?= $image; ?>" class="img-fluid" alt="<?= $this->item->name; ?>" />
				</a>
			</div>

			<div class="additional-img">
				<?php if($this->item->image2 != '') : ?>
				<a href="<?= $this->item->image2; ?>" data-fancybox="gallery">
					<img src="<?= $this->item->image2; ?>" class="img-fluid" alt="<?= $this->item->name; ?>" />
				</a>
				<?php endif; ?>
				<?php if($this->item->image3 != '') : ?>
				<a href="<?= $this->item->image3; ?>" data-fancybox="gallery">
					<img src="<?= $this->item->image3; ?>" class="img-fluid" alt="<?= $this->item->name; ?>" />
				</a>
				<?php endif; ?>
				<?php if($this->item->image4 != '') : ?>
				<a href="<?= $this->item->image4; ?>" data-fancybox="gallery">
					<img src="<?= $this->item->image4; ?>" class="img-fluid" alt="<?= $this->item->name; ?>" />
				</a>
				<?php endif; ?>
				<?php if($this->item->image5 != '') : ?>
				<a href="<?= $this->item->image5; ?>" data-fancybox="gallery">
					<img src="<?= $this->item->image5; ?>" class="img-fluid" alt="<?= $this->item->name; ?>" />
				</a>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="col-12 col-md-7">
		
			<?php if($showbrand == 1) : ?>
			<div class="brand">
			<div class="text-right bold"><?= $this->item->bname; ?></div>
			</div>	
			<?php endif; ?>			
			
			<div class="row no-gutters mb-2">
			<div class="col-12 col-md-6 text-left estil07 text-primary"><b><?= strtoupper($this->item->name); ?></b></div>
			<div class="col-12 col-md-6 text-right estil08 text-primary">
				<?php if($showprices == 1 || ($loginprices == 1 && !$user->guest)) : ?>
				<b><?= $precio; ?> &euro;</b>
				<?php endif; ?>						
			</div>
			</div>
			
			<?php if($showpvp == 1) : ?>
			<div class="col-12 col-md-12">			
			<div class="text-left faded pvp">PVP <strike><?= $this->item->pvp; ?> &euro;</strike></div>			
			</div>
			<?php endif; ?>
			
			<div class="row">
				<div class="col-12 col-md-4">
					<div class="addtocart">
						<form name="addtocart" action="index.php?option=com_botiga&task=botiga.setItem" method="get" class="form-inline">
							<input type="hidden" name="option" value="com_botiga" />
							<input type="hidden" name="task" value="botiga.setItem" />
							<input type="hidden" name="id" value="<?= $this->item->id; ?>" />
							<input type="hidden" name="return" value="<?= $uri; ?>" />
							<div>
								<div class="input-group">
				                    <span class="input-group-btn">
				                        <button type="button" class="quantity-left-minus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
				                          <span class="fa fa-minus"></span>
				                        </button>
				                    </span>
				                    <input type="text" id="quantity_<?= $item->id; ?>" name="qty" class="form-control bg-qty input-number text-center" value="1" min="1" max="100">
				                    <span class="input-group-btn">
				                        <button type="button" class="quantity-right-plus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
				                            <span class="fa fa-plus"></span>
				                        </button>
				                    </span>
				                </div>														    
							</div>				
						</form>
					</div>
				</div>
				<div class="col-xs-12 col-md-8">
					<a href="index.php?view=botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>" class="btn btn-primary btn-block estil03">
						<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>
					</a>
				</div>
		    </div>
		    
		    <?php if($showfav == 1) : ?>
		    <div class="item-favorite">
		    <?php if(!botigaHelper::isFavorite($this->item->id)) : ?>
			<a title="Añadir a favoritos" class="hasTip" href="index.php?option=com_botiga&task=setFavorite&id=<?= $this->item->id; ?>&return=<?= $uri; ?>"><span class="fa fa-heart-o"></span></a>
			<?php else : ?>
			<a title="Quitar de favoritos" class="hasTip" href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $this->item->id; ?>&return=<?= $uri; ?>"><span class="fa fa-heart red"></span></a>
			<?php endif; ?>
			</div>
			<?php endif; ?>
		    
		    <?php if($showref == 1) : ?>
		    <div class="text-left item-title-ref"><?= $this->item->ref; ?></div>
		    <?php endif; ?>		    			
			
			<?php if($showdesc == 1) : ?>		
			<div class="product-extra">Descripción</div>
			<?= $this->item->description; ?>
			<?php endif; ?>	
			
			<?php if($showgar == 1) : ?>
			<div class="product-extra">Garantía</div>
			<?= JText::_($this->item->garantia); ?>
			<?php endif; ?>	
			
			<?php if($showenvio == 1) : ?>
			<div class="product-extra">Envío</div>
			<?= JText::_($this->item->envio); ?>
			<?php endif; ?>	
			
			<?php 
			$i = 0;
			foreach(botigaHelper::getExtras($this->item->id) as $k => $v) : ?>
			<div class="estil07 text-primary <?php if($i == 0): ?>mt-5<?php else: ?>mt-4<?php endif; ?>" data-toggle="collapse" href="#collapse<?= $i; ?>"><?= strtoupper($v[0]); ?>&nbsp;<i class="fa fa-plus fa-2x text-yellow"></i></div>
			<div class="collapse <?php if($i == 0): ?>show<?php endif; ?> estil03 text-primary mt-2" id="collapse<?= $i; ?>"><?= $v[1]; ?></div>
			<?php 
			$i++;
			endforeach; ?>
		
        </div>                
	
		<?php if($showrel == 1) : ?>
		<div id="page-header">
			<!-- Modulo related -->
			<h4><?= JText::_('COM_BOTIGA_RELATED_PRODUCTS'); ?></h4>
			<?php
			jimport( 'joomla.application.module.helper' );
			$module = JModuleHelper::getModule( 'mod_botiga_related' , '' );
			echo JModuleHelper::renderModule( $module );
			?>
		</div>
		<?php endif; ?>
	
	</div>
	
</div>

<?php if($modal != 0) : ?>
<!-- start Modal success -->
<div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-dialog-centered text-center" role="document">
	<div class="modal-content">
		<div class="modal-header" style="padding:15px;border:none;">
	    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  	</div>
	  	<div class="modal-body">
		<h2><?= JText::_('COM_BOTIGA_PROCESS_CART'); ?></h2>
		<div class="row">
			<div class="col-xs-12 col-md-6">
			<?php 
			$img = botigaHelper::getItemData('image1', $modal);
			$img != '' ? $image = $img : $image = 'components/com_botiga/assets/images/noimage.jpg';
			?>
			<img src="<?= $image; ?>" alt="" class="img-fluid">
			</div>
			<div class="col-xs-12 col-md-6 text-left py-3">
			<b><?= botigaHelper::getItemData('name', $modal); ?></b>
			<?= botigaHelper::getItemData('s_description', $modal); ?>
			</div>
		</div>
		<a href="index.php?option=com_botiga&view=checkout" class="btn btn-primary btn-block mt-2"><?= JText::_('COM_BOTIGA_GOTO_CHECKOUT'); ?></a>
		<a href="#" class="btn btn-primary btn-block" data-dismiss="modal"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
	  </div>
	</div>
  </div>
</div>
<!-- end Modal success -->
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
