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
$showprices = botigaHelper::getParameter('show_prices', 1);
$loginprices = botigaHelper::getParameter('login_prices', 0);
$shownotice = botigaHelper::getParameter('show_notice', 1);
$showref 	= botigaHelper::getParameter('show_ref', 1);
$showdesc 	= botigaHelper::getParameter('show_desc', 1);
$showbrand 	= botigaHelper::getParameter('show_brand', 1);
$showfav 	= botigaHelper::getParameter('show_fav', 1);
$showpvp 	= botigaHelper::getParameter('show_pvp', 1);
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
				<div class="col-xs-12 col-md-6 text-left">			
					<a href="index.php?option=com_botiga&view=botiga&layout=table" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<a href="index.php?option=com_botiga&view=botiga">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
				</div>
				<div class="col-xs-12 col-md-6 text-right">
					<a href="" class="pr-1">
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

<div class="col-md-11 mx-auto">

		<div class="row">

			<?php if($showprices == 1 && ($loginprices == 0 && $user->guest) && $shownotice == 1) : ?>
			<div class="col-12">
				<a href="index.php?option=com_botiga&view=register">
					<div class="alert alert-danger"><?= JText::_('COM_BOTIGA_PRICES_NOTICE'); ?></div>
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
						<select name="catid" id="catid" class="form-control" style="width:100%;">
							<option value=""><?= JText::_('COM_BOTIGA_SELECT_AN_OPTION'); ?></div>
							<?php foreach(botigaHelper::getCategories() as $cat) : ?>
							<option value="<?= $cat->id; ?>" <?php if($catid == $cat->id) : ?>selected<?php endif; ?>><?= $cat->title; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group col-xs-12 col-md-5 mx-auto">
						<select name="orderby" id="orderby" class="form-control" style="width:100%;">
							<option value="id"><?= JText::_('COM_BOTIGA_ORDERBY'); ?></div>
							<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>>Código</option>
							<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>>Precio</option>
						</select>
					</div>					

				</form>
			</div>
			
			<div class="col-10 mx-auto items">
				<div class="row">
				
				<?php 
				if(count($this->items)) :
				$i = 0;
				foreach($this->items as $item) :
					
					$item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg';

					$precio = botigaHelper::getUserPrice($item->id);
					$precio < 1 ? $price = '' : $price = $precio;
					?>
					<div class="col-xs-12 col-md-3 item">
						
						<div class="botiga-img">
							<a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid='.$catid); ?>">
								<?php if(!$user->guest && $item->pvp > 0 && $showprices == 1) : ?>
								<div class="pvp-badge"><span><?= botigaHelper::getPercentDiff($item->pvp, $price); ?> %</span></div>
								<?php endif; ?>
								<img src="<?= $image; ?>" class="img-responsive" alt="<?= $item->name; ?>" />
							</a>
						
						</div>
						
						<?php if($showref == 1) : ?>
						<div class="text-left item-ref"><?= $item->ref; ?></div>
						<?php endif; ?>						
						<div class="text-left"><strong><?= $item->name; ?></strong></div>
						<?php if($showdesc == 1) : ?>
						<div class="text-left"><strong><?= $item->s_description; ?></strong></div>
						<?php endif; ?>	
						<?php if($showbrand == 1) : ?>				
						<div class="text-left"><?= $item->brandname; ?></div>
						<?php endif; ?>	
						<div class="item-divider"></div>
						
						<?php if($showprices == 1) : ?>
						<div class="text-left bold price"><?php if(!$user->guest) : ?><?= $price; ?>&euro;<?php endif; ?></div>
						<?php if(!$user->guest && $showpvp == 1) : ?>
						<div class="text-left faded pvp"><?php if(!$user->guest) : ?>PVP <strike><?= $item->pvp; ?> &euro;</strike><?php endif; ?></div>
						<?php endif; ?>
						<?php endif; ?>
						
						 <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&catid='.$catid); ?>"><i class="fa fa-eye"></i><br><?= JText::_('Ver'); ?></a>
						 <a href="#" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> data-id="<?= $item->id; ?>"  class="<?php if(!$user->guest) : ?>setItem<?php endif; ?>"><i class="fa fa-shopping-cart"></i><br><?= JText::_('COM_BOTIGA_BTN_BUY'); ?></a>
						
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

<?php if($modal == 1) : ?>
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
<script>jQuery('#success').modal('show');</script>
<?php endif; ?>
