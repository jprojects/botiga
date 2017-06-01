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
$model = $this->getModel('botiga');
$user  = JFactory::getUser();
$uri = JFactory::getURI(); 
$uri = base64_encode($uri->toString());
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$lang = JFactory::getLanguage()->getTag();
?>

<!-- Modal success -->
<div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header" style="padding: 0 15px;border:none;">
	    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  	</div>
	  	<div class="modal-body">
		<?= JText::_('COM_BOTIGA_PROCESS_CART'); ?>
	  </div>
	</div>
  </div>
</div>

<style>
.thumbnail > img, .thumbnail > a > img { height: 250px; } 
.btn-link, .btn-link:hover { border: 1px solid #1d1d1d; color: #1d1d1d; text-decoration:none; }
.item .btn-black { margin-top:10px; }
.price { font-size: 20px; }
.item-title { height: 42px; }
#budget input, #budget textarea { color: #fff; }
</style>

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_PARTS'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<?php if($user->guest) : ?>
	<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
	<?php endif; ?>
	
	<?php foreach($this->items as $item) : ?>
		<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
		<?php $price = $model->getTarifa($item->id); ?>
		<?php $price < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $price.'&euro;'; ?>
		<div class="col-xs-12 col-sm-6 col-md-4 top20 item">
			<div class="thumbnail">
				<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&Itemid='.$itemid); ?>">
					<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
				</a>
			</div>	
			<div class="text-center item-title"><strong><?= $item->name; ?></strong></div>
			<div class="text-center"><?= $model->getBrandName($item->marca); ?>/<?= $model->getCategoryName($item->catid); ?></div>
			<div class="text-center"><?= $item->ref; ?></div>
			<div class="text-center bold blue price"><?= $price; ?></div>
			<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php endif; ?> class="btn btn-primary btn-block btn-black"><?= JText::_('COM_BOTIGA_BUY'); ?> <i class="fa fa-shopping-cart"></i></a>
			<a data-toggle="modal" data-name="<?= $item->name; ?>" data-target="#budget" class="btn btn-primary btn-block btn-link"><?= JText::_('COM_BOTIGA_MORE_INFO'); ?></a>
		</div>
	<?php endforeach; ?>


	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
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
			<form id="budgetForm" name="budgetForm" action="index.php?option=com_laundry&task=sendModalEmail" method="post">
				<input type="hidden" name="url" value="<?= JUri::getInstance(); ?>" />
				<div class="form-group">
					<input type="text" name="maquina" id="modal-maquina" value="" />
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

<?php if($modal == 1) : ?>
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
