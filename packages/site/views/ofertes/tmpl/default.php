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
$model 		= $this->getModel('botiga');
$user  		= JFactory::getUser();
$url        = JFactory::getURI()->toString();
$uri 		= base64_encode($url);
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$catid      = $jinput->get('catid', '');
$marca      = $jinput->get('marca', '');
$itemid     = $jinput->get('Itemid', '');
$orderby    = $jinput->get('orderby', '');
$lang 		= JFactory::getLanguage()->getTag();
$showprices = botigaHelper::getParameter('show_prices', 1);
$login_prices = botigaHelper::getParameter('login_prices', 0);
?>

<style>
.item {
	margin-bottom: 50px;
	padding-left: 0 !important;
	padding-right: 0 !important;
}
.item-wrap {
	padding-left: 10px;
	padding-right: 10px;
}
.alert-info, .alert-info a {
	color: #fff !important;
}
</style>

<!-- start Modal success -->
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
<!-- end Modal success -->

<div>

	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_ITEMS_TITLE'); ?></h1>
	</div>
	
	<div class="clearfix"></div>
	
	<?php if($showprices == 1 && $user->guest) : ?>
	<div class="alert alert-info"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', 'index.php?option=com_botiga&view=register&Itemid=113'); ?></div>
	<?php endif; ?>
	
	<div class="pull-left">
		<form name="filters" id="filters" method="get" action="index.php?option=com_botiga&view=botiga" class="form-inline" onchange="filters.submit();">
			<input type="hidden" name="option" value="com_botiga" />
			<input type="hidden" name="view" value="botiga" />
			<input type="hidden" name="catid" value="<?= $catid; ?>" />
			<input type="hidden" name="marca" value="<?= $marca; ?>" />
			<input type="hidden" name="Itemid" value="<?= $itemid; ?>" />
			<div class="form-group">
				<select name="orderby" id="orderby" class="form-control">
					<option value="">Ordenar por</option>
					<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>>Código</option>
					<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>>Precio</option>
				</select>
			</div>
		</form>
	</div>
	<div class="pull-right">
		<?php stripos($url, '&') === false ? $link = $url.'?layout=table' : $link = $url.'&layout=table'; ?>
		<a href="<?= $link; ?>" class="pull-right"><i class="fa fa-list fa-2x"></i></a>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="items">
	<?php 
	if(count($this->items)) :
	$i = 0;
	foreach($this->items as $item) : ?>
	
		<?php //echo JLayoutHelper::render('botiga.item.default', array('params' => $this->params, 'items' => $this->items)); ?>	
		
		<?php $item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg'; ?>

		<?php 
		$precio = botigaHelper::getUserPrice($item->id);
		$precio < 1 ? $price = JText::_('COM_BOTIGA_A_CONSULTAR') : $price = $precio; 
		if($i == 4) { echo '<div class="clearfix"></div>'; $i = 0; }
		?>
		<div class="col-xs-12 col-sm-6 col-md-3 item zoom">
		<div class="wrapper">
			<div class="item-wrap">
				<div class="botiga-img">
					<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>&Itemid=133">
						<?php if($item->pvp > 0 && $showprices == 1) : ?>
						<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $price); ?> %</span></div>
						<?php endif; ?>
						<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
					</a>
				
				</div>	
				<div class="text-left item-title"><strong><?= $item->name; ?></strong></div>
				<div class="text-left"><?= $item->ref; ?></div>
				<div class="text-left"><?= $item->brandname; ?></div>
				<?php if($showprices == 1) : ?>
				<div class="text-left bold price"><?= $price; ?>&euro;</div>
				<?php if($login_prices == 0) : ?>
				<div class="text-left faded pvp"><?php if($item->pvp != 0.00) : ?>PVP <strike><?= $item->pvp; ?> &euro;</strike><?php endif; ?></div>
				<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="botiga-btns" style="display:none;">
			
				<div class="btn-group btn-group-justified" role="group">
				  <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&Itemid=133'); ?>" class="btn btn-primary"><i class="fa fa-eye"></i><br><?= JText::_('Ver'); ?></a>
				  <a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?> class="btn btn-primary"><i class="fa fa-shopping-cart"></i><br><?= JText::_('Comprar'); ?></a>
				  	<?php if(!botigaHelper::isFavorite($item->id)) : ?>
						<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=setFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?> class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart"></span><br>Favorito</a>
					<?php else : ?>
						<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?> class="btn btn-group btn-primary"><span class="glyphicon glyphicon-heart red"></span><br>No Favorito</a>
					<?php endif; ?>
				</div>
				
				
				<?php if(botigaHelper::getParameter('show_ask', 1) == 1) : ?>
				<a data-toggle="modal" data-name="<?= $item->name; ?>" data-target="#budget" class="btn btn-default btn-block"><?= JText::_('COM_BOTIGA_MORE_INFO'); ?></a>
				<?php endif; ?>
				
			</div>
		</div>
		</div>
	<?php
	$i++;
	endforeach; 
	else:
	?>
	<?= JText::_('COM_BOTIGA_NO_ITEMS'); ?>
	<?php
	endif;
	?>

	<div class="clearfix"></div>
	
	<div class="paginacion">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	
	<?php if(botigaHelper::getParameter('show_ask', 1) == 1) : ?>
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
	<?php endif; ?>
	</div>
	
</div>

<?php if($modal == 1) : ?>
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
