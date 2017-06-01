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
$this->item->image1 != '' ? $image = $this->item->image1 : $image = 'images/noimage.png';
$model = $this->getModel('item');
$user  = JFactory::getUser();
$price = $model->getTarifa($this->item->id);
$price < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $price.'&euro;';
$marca = $model->getBrandName($this->item->marca);
$category = $model->getCategoryName($this->item->catid);
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());

$js = '{
  "@context": "http://schema.org/",
  "@type": "Product",
  "name": "'.$this->item->name.'",
  "image": "'.JURI::root().$image.'",
  "description": "'.$marca.' '.$category.'",
  "mpn": "'.$this->item->ref.'",
  "brand": {
    "name": "'.$marca.'"
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
JFactory::getDocument()->addScriptDeclaration($js, 'application/ld+json');
?>

<style>
.btn-link, .btn-link:hover { border: 1px solid #1d1d1d; color: #1d1d1d; text-decoration:none; }
.item .btn-black { margin-top:10px; }
.price { font-size: 20px; margin-bottom: 20px; }
.item-title { height: 42px; }
#budget input, #budget textarea { color: #fff; }
</style>

<div>

	<div id="page-header">
		<h1><?= $this->item->name; ?></h1>
	</div>
	
	<?php if($user->guest) : ?>
	<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
	<?php endif; ?>

	<div class="col-xs-12 col-md-8">
		<img src="<?= $image; ?>" class="img-responsive" alt="<?= $this->item->name; ?>" />
	</div>
	<div class="col-xs-12 col-md-4">
		<div class="text-left item-title"><strong><?= $this->item->name; ?></strong></div>
		<div class="text-left"><?= $marca; ?>/<?= $category; ?></div>
		<div class="text-left"><?= $this->item->ref; ?></div>
		<div class="text-right bold blue price"><?= $price; ?></div>
		<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $this->item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary btn-block btn-black"><?= JText::_('COM_BOTIGA_BUY'); ?> <i class="fa fa-shopping-cart"></i></a>
		<a data-toggle="modal" data-name="<?= $this->item->name; ?>" data-target="#budget" class="btn btn-primary btn-block btn-link"><?= JText::_('COM_BOTIGA_MORE_INFO'); ?></a>
		<a href="<?= JRoute::_('index.php?option=com_botiga&view=botiga&catid=20&Itemid=128'); ?>" class="btn btn-primary btn-block btn-black"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
	</div>
	
	<div class="clearfix"></div>
	<hr>
	<div id="page-header">
		<h4><?= JText::_('COM_BOTIGA_RELATED_PRODUCTS'); ?></h4>
		<?php
		jimport( 'joomla.application.module.helper' );
		$module = JModuleHelper::getModule( 'mod_botiga_related' , '' );
		echo JModuleHelper::renderModule( $module );
		?>
	</div>
	
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
					<input type="text" name="maquina" id="modal-maquina" value="<?= $this->item->name; ?>" />
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
	
</div>
