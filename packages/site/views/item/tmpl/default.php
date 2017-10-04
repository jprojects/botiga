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
$this->item->image1 != '' ? $image = $this->item->image1 : $image = 'components/com_botiga/assets/images/noimage128.jpg';
$model 		= $this->getModel('item');
$user  		= JFactory::getUser();
$doc   		= JFactory::getDocument();
$precio 	= botigaHelper::getUserPrice($this->item->id);
$precio < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $precio;
$uri 		= base64_encode(JFactory::getURI()->toString());
$show_prices = botigaHelper::getParameter('show_prices', 1);
$show_ask 	= botigaHelper::getParameter('show_ask', 1);
$login_prices = botigaHelper::getParameter('login_prices', 0);

$js = '{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "'.$this->item->name.'",
  "image": "'.JURI::root().$image.'",
  "description": "'.$this->item->brand.' '.$category.'",
  "mpn": "'.$this->item->ref.'",
  "brand": {
    "name": "'.$this->item->brand.'"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "EUR",
    "seller": {
      "@type": "Organization",
      "name": "AcjSystems S.L."
    }
  }
}';
$doc->addScriptDeclaration($js, 'application/ld+json');
$doc->addScript('components/com_botiga/assets/js/jquery.fancybox.js');
$doc->addStylesheet('components/com_botiga/assets/css/jquery.fancybox.css');
?>

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_ITEM_TITLE'); ?></h1>
	</div>
	
	<?php if($show_prices == 1 && $user->guest) : ?>
	<div class="alert alert-primary"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', 'index.php?option=com_botiga&view=register&Itemid=117'); ?></div>
	<?php endif; ?>

	<div class="col-xs-12 col-md-5">
	
		<div id="page-header">
			<h2><?= $this->item->name; ?></h2>
			<span><?= $this->item->s_description; ?></span>
		</div>
		
		<div class="botiga-img">
			<a href="<?= $image; ?>" data-fancybox="gallery">
			<?php if($this->item->pvp > 0 && $show_prices == 1) : ?>
			<div class="pvp-badge big"><span><?= botigaHelper::getPercentDiff($this->item->pvp, $price); ?> %</span></div>
			<?php endif; ?>
			<span class="rollover"></span>
				<img src="<?= $image; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
			</a>
		</div>
		<hr>
		<div class="additional-img">
			<?php if($this->item->image2 != '') : ?>
			<a href="<?= $this->item->image2; ?>" data-fancybox="gallery">
				<img src="<?= $this->item->image2; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
			</a>
			<?php endif; ?>
			<?php if($this->item->image3 != '') : ?>
			<a href="<?= $this->item->image3; ?>" data-fancybox="gallery">
				<img src="<?= $this->item->image3; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
			</a>
			<?php endif; ?>
			<?php if($this->item->image4 != '') : ?>
			<a href="<?= $this->item->image4; ?>" data-fancybox="gallery">
				<img src="<?= $this->item->image4; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
			</a>
			<?php endif; ?>
			<?php if($this->item->image5 != '') : ?>
			<a href="<?= $this->item->image5; ?>" data-fancybox="gallery">
				<img src="<?= $this->item->image5; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
			</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-xs-12 col-md-7">
	
		<div class="brand">
		<?php if($this->item->bimage != '') : ?>
		<img src="<?= $this->item->bimage; ?>" alt="<?= $this->item->bname; ?>" />
		<?php else : ?>
		<div class="text-right bold"><?= $this->item->bname; ?></div>
		<?php endif; ?>
		</div>
		
		<div class="addtocart-block">
		<div class="col-xs-12 col-md-4 text-left nopadding">
			<div class="addtocart">
			<?php if($show_prices == 1 && $login_prices == 0) : ?>
			<div class="text-left bold price"><?= $price; ?> &euro;</div>
			<?php if($this->item->pvp != 0.00) : ?>
			<div class="text-left faded pvp">PVP <strike><?= $this->item->pvp; ?> &euro;</strike></div>
			<?php endif; ?>
			<?php endif; ?>	
			</div>	
		</div>
		<div class="col-xs-12 col-md-8 nopadding">
			<div class="addtocart">
			<?php $user->guest ? $action = '' : $action = 'index.php?option=com_botiga&task=botiga.setItem'; ?>
			<form name="addtocart" action="<?= $action; ?>" method="get" class="form-inline">
				<input type="hidden" name="option" value="com_botiga" />
				<input type="hidden" name="task" value="botiga.setItem" />
				<input type="hidden" name="id" value="<?= $this->item->id; ?>" />
				<input type="hidden" name="return" value="<?= $uri; ?>" />
				<div class="col-md-3">
					<div class="form-group">
						<input type="number" name="qty" value="1" min="1" id="number" />
					</div>
				</div>
				<div class="col-md-9 nopadding">
					<div class="btn-group" role="group">						
						<button class="btn btn-group btn-success" <?php if($user->guest) : ?>disabled="true"<?php endif; ?>><?= JText::_('COM_BOTIGA_BUY'); ?> <i class="fa fa-shopping-cart"></i></button>
						<?php if(!botigaHelper::isFavorite($this->item->id)) : ?>
						<a href="index.php?option=com_botiga&task=setFavorite&id=<?= $this->item->id; ?>&return=<?= $uri; ?>" class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart"></span></a>
						<?php else : ?>
						<a href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $this->item->id; ?>&return=<?= $uri; ?>" class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart red"></span></a>
						<?php endif; ?>
					</div>
		    	</div>
		    </form>
		    </div>
        </div>
        </div>
        <div class="clearfix"></div>
        
		<div class="text-left item-title-item"><strong><?= $this->item->name; ?></strong></div>
		<div class="text-left"><?= $this->item->ref; ?></div>
		
		<div class="product-extra"><span class="caret"></span> <strong>Descripción</strong></div>
		<?= $this->item->description; ?>
		<div class="product-extra"><span class="caret"></span> <strong>Garantía</strong></div>
		<?= $this->item->garantia; ?>
		<div class="product-extra"><span class="caret"></span> <strong>Envío</strong></div>
		<?= $this->item->envio; ?>
		<div class="product-extra"><span class="caret"></span> <strong>Documentación extra</strong></div>
		<?php if(file_exists('images/pdf/'.$this->item->ref.' F.pdf')) : ?>
		<a href="images/pdf/<?= $this->item->ref.' F.pdf'; ?>" target="_blank">Ficha técnica</a><br>
		<?php endif; ?>
		<?php if(file_exists('images/products/'.strtolower($this->item->ref).'-f.jpg')) : ?>
		<a href="images/products/<?= strtolower($this->item->ref).'-f.jpg'; ?>" target="_blank">Dibujo técnico</a>
		<?php endif; ?>
		<?php if($show_ask == 1) : ?>
		<a data-toggle="modal" data-name="<?= $this->item->name; ?>" data-target="#budget" class="btn btn-primary btn-block"><?= JText::_('COM_BOTIGA_MORE_INFO'); ?></a>
		<?php endif; ?>
		<!--<a href="<?= JRoute::_('index.php?option=com_botiga&view=botiga&catid=20&Itemid=128'); ?>" class="btn btn-primary btn-block btn-black"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>-->
	</div>
	
	<div class="clearfix"></div>
	<hr>
	<div id="page-header">
		<!-- Modulo related -->
		<h4><?= JText::_('COM_BOTIGA_RELATED_PRODUCTS'); ?></h4>
		<?php
		jimport( 'joomla.application.module.helper' );
		$module = JModuleHelper::getModule( 'mod_botiga_related' , '' );
		echo JModuleHelper::renderModule( $module );
		?>
	</div>
	
	<?php if($show_ask == 1) : ?>
	<!-- Modal -->
	<div class="modal fade" id="budget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><strong><?= JText::_('COM_BOTIGA_BUDGET_TITLE'); ?></strong></h4>
		  </div>
		  <div class="modal-body">
			<form id="budgetForm" name="budgetForm" action="index.php?option=com_botiga&task=sendModalEmail" method="post">
				<input type="hidden" name="url" value="<?= JUri::getInstance(); ?>" />
				<div class="form-group">
					<input type="text" name="item" id="modal-item" value="<?= $this->item->name; ?>" />
				</div>
				<div class="form-group">
					<input type="text" name="nombre" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_NAME'); ?>" />
				</div>
				<div class="form-group">
					<input type="text" name="email" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_EMAIL'); ?>" />
				</div>
				<div class="form-group">
					<input type="text" name="phone" value="" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_PHONE'); ?>" />
				</div>
				<div class="form-group">
					<textarea style="width:100%" rows="8" name="mensaje" placeholder="<?= JText::_('COM_BOTIGA_BUDGET_MESSAGE'); ?>"></textarea>
				</div>
			</form>
		  </div>
		  <div class="modal-footer">
		  	<span class="small" style="margin-right:20px;"><?= JText::_('COM_BOTIGA_BUDGET_TOS'); ?></span>
			<button onclick="budgetForm.submit();" type="button" class="btn btn-default btn-rounded"><?= JText::_('COM_BOTIGA_SEND'); ?></button>
		  </div>
		</div>
	  </div>
	</div>
	<?php endif; ?>
	
</div>
