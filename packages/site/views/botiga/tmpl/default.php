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
$model 		= $this->getModel('botiga');
$user  		= JFactory::getUser();
$uri 		= base64_encode(JFactory::getURI()->toString());
$jinput		= JFactory::getApplication()->input;
$modal 		= $jinput->get('m', 0);
$catid      = $jinput->get('catid', '');
$marca      = $jinput->get('marca', '');
$itemid     = $jinput->get('Itemid', '');
$orderby    = $jinput->get('orderby', 'ref');
$limit      = $jinput->get('limit', 24);
$lang 		= JFactory::getLanguage()->getTag();
$logo		= botigaHelper::getParameter('botiga_logo', '');
$showprices = botigaHelper::getParameter('show_prices', 1);
$loginprices = botigaHelper::getParameter('login_prices', 0);
$shownotice = botigaHelper::getParameter('show_notice', 1);
$showref 	= botigaHelper::getParameter('show_ref', 1);
$showdesc 	= botigaHelper::getParameter('show_desc', 1);
$showbrand 	= botigaHelper::getParameter('show_brand', 1);
$showfav 	= botigaHelper::getParameter('show_fav', 1);
$showpvp 	= botigaHelper::getParameter('show_pvp', 1);
$userToken  = JSession::getFormToken();

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$count 	    = botigaHelper::getCarritoCount();
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) : ?>
<header class="head_botiga">

	<div class="col-md-11 mx-auto">

		<div class="row">

		<?php if($logo != '') : ?>
		<div class="col-12 text-right d-none d-sm-block">
			<a href="index.php"><img src="<?= $logo; ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid"></a>
		</div>
		<?php endif; ?>	
		
		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-9 text-left">			
					<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<span class="pl-3 phone-hide"><?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
				</div>
				<div class="col-3 text-right">
					<a href="<?php if($count > 0) : ?>index.php?option=com_botiga&view=checkout<?php else: ?>#<?php endif; ?>" class="pr-1 carrito">
						<?php if($count > 0) : ?>
						<span class="badge badge-warning"><?= $count; ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>
					<?php if($user->guest) : ?>
					<a href="index.php?option=com_users&view=login" title="Login" class="hasTip pr-1">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
					<?php else: ?>
					<a href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" class="hasTip pr-1">
						<img src="media/com_botiga/icons/salir.png">
					</a>
					<a href="index.php?option=com_botiga&view=history" title="History" class="hasTip">
						<img src="media/com_botiga/icons/sesion-iniciada.png">
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		</div>	
	
	</div>
	
</header>
<?php endif; ?>

<div class="col-md-11 mx-auto">

		<div class="row">

			<?php if($showprices == 1 && ($loginprices == 0 && $user->guest) && $shownotice == 1) : ?>
			<div class="col-12">
				<a href="index.php?option=com_botiga&view=register">
					<div class="alert alert-warning"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
				</a>
			</div>
			<?php endif; ?>		
			
			<div class="col-12 shop_filters mt-4">
				<form name="filters" id="filters" method="get" action="index.php?option=com_botiga&view=botiga" class="form-inline" onchange="filters.submit();">
					<input type="hidden" name="option" value="com_botiga" />
					<input type="hidden" name="view" value="botiga" />
					<input type="hidden" name="marca" value="<?= $marca; ?>" />
					<input type="hidden" name="Itemid" value="<?= $itemid; ?>" />
					
					<div class="form-group col-xs-12 col-md-5 mx-auto">
						<div class="styled-select">
							<select name="catid" id="catid" class="form-control estil03 text-primary" style="width:100%;">
								<option value=""><?= JText::_('COM_BOTIGA_SELECT_AN_OPTION'); ?></div>
								<?php foreach(botigaHelper::getCategories() as $cat) : ?>
								<option value="<?= $cat->id; ?>" <?php if($catid == $cat->id) : ?>selected<?php endif; ?>><?= $cat->title; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group col-xs-12 col-md-5 mx-auto">
						<div class="styled-select">
							<select name="orderby" id="orderby" class="form-control estil03 text-primary" style="width:100%;">
								<option value="id"><?= JText::_('COM_BOTIGA_ORDERBY'); ?></div>
								<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>>Código</option>
								<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>>Precio</option>
							</select>
						</div>
					</div>					

				</form>
			</div>
			
			<div class="col-8 mx-auto items">
				<div class="row">
				
				<?php 
				if(count($this->items)) :
				$i = 0;
				foreach($this->items as $item) :
					
					$item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg';
					$precio = botigaHelper::getUserPrice($item->id);

					?>
					<div class="col-xs-12 col-md-3 item px-5">
						
						<div class="botiga-img text-center">
							<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>">
								<?php if(!$user->guest && $item->pvp > 0 && $showprices == 1) : ?>
								<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $precio); ?> %</span></div>
								<?php endif; ?>
								<img src="<?= $image; ?>" class="img-fluid" alt="<?= $item->name; ?>" />
							</a>						
						</div>
						
						<?php if($showref == 1) : ?>
						<div class="text-left item-ref"><?= $item->ref; ?></div>
						<?php endif; ?>						
						<div class="text-left estil05 text-dark"><b><?= $item->name; ?></b></div>
						<?php if($showdesc == 1) : ?>
						<div class="text-left estil03 text-dark"><?= $item->s_description; ?></div>
						<?php endif; ?>	
						<?php if($showbrand == 1) : ?>				
						<div class="text-left"><?= $item->brandname; ?></div>
						<?php endif; ?>	
						<?php if($showpvp == 1) : ?>
						<div class="text-left faded pvp">PVP <strike><?= $item->pvp; ?> &euro;</strike></div>
						<?php endif; ?>
						
						<div class="row">
						<?php if($showprices == 1 || ($loginprices == 1 && !$user->guest)) : ?>
						<div class="col-6 text-left bold estil05 text-dark mt-2"><?= $precio; ?>&euro;</div>
						<?php endif; ?>
						<div class="col-6 text-right pr-3">
							<?php if(botigaHelper::isValidated()) : ?>
							<a href="index.php?view=botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>">
								<img src="media/com_botiga/icons/addtocart.png" alt="<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>">
							</a>
							<?php else: ?>
							<a href="#">
								<img src="media/com_botiga/icons/carrito_desactivado.png" alt="<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>">
							</a>
							<?php endif; ?>
						</div>
						</div>
						
						<?php if($showfav == 1) : ?> 
						  	<?php if(!botigaHelper::isFavorite($item->id)) : ?>
							<a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="<?php if(!$user->guest) : ?>setFavorite<?php endif; ?> item<?= $item->id; ?>"><span class="heart glyphicon glyphicon-heart"></span><br><?= JText::_('COM_BOTIGA_BTN_FAV'); ?></a>
							<?php else : ?>
							<a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="<?php if(!$user->guest) : ?>unsetFavorite<?php endif; ?> item<?= $item->id; ?>"><span class="heart glyphicon glyphicon-heart red"></span><br><?= JText::_('COM_BOTIGA_BTN_UNFAV'); ?></a>
							<?php endif; ?>
						<?php endif; ?>
						
					</div>
				<?php
				$i++;
				endforeach; 
				endif;
				?>
				</div>
				
				<div class="paginacion">
					<?= $this->pagination->getPagesLinks(); ?>
				</div>

			</div>
	
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
					<img src="<?= $image; ?>" alt="" class="img-fluid" width="50">
				</div>
				<div class="col-xs-12 col-md-6 text-left py-3">
					<b><?= botigaHelper::getItemData('name', $modal); ?></b>
					<?= botigaHelper::getItemData('s_description', $modal); ?><br>
					<b><?= botigaHelper::getUserPrice($modal); ?>&euro;</b>
				</div>
			</div>
			<a href="index.php?option=com_botiga&view=checkout" class="btn btn-primary btn-block mt-3"><?= JText::_('COM_BOTIGA_GOTO_CHECKOUT'); ?></a>
			<a href="#" class="btn btn-primary btn-block mt-3" data-dismiss="modal"><?= JText::_('COM_BOTIGA_CONTINUE_SHOPPING'); ?></a>
	  </div>
	</div>
  </div>
</div>
<!-- end Modal success -->
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
