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
$app = JFactory::getApplication();

if(!botigaHelper::isEmpresa()) { $app->redirect('index.php'); }

$doc   		= JFactory::getDocument();
$doc->addScript('components/com_botiga/assets/js/jquery.fancybox.js');
$doc->addStylesheet('components/com_botiga/assets/css/jquery.fancybox.css');

$model 		= $this->getModel('botiga');
$user  		= JFactory::getUser();
$uri 		= base64_encode(JFactory::getURI()->toString());
$jinput		= $app->input;
$catid      = $jinput->get('catid', '');
$marca      = $jinput->get('marca', '');
$itemid     = $jinput->get('Itemid', '');
$orderby    = $jinput->get('orderby', 'ref');
$limit      = $jinput->get('limit', 24);
$lang 		= JFactory::getLanguage()->getTag();
$logo		= botigaHelper::getParameter('botiga_logo', '');
$showprices = botigaHelper::getParameter('show_prices', 1);
$showdiscount = botigaHelper::getParameter('show_discount', 0);
$loginprices = botigaHelper::getParameter('login_prices', 0);
$shownotice = botigaHelper::getParameter('show_notice', 1);
$showref 	= botigaHelper::getParameter('show_ref', 1);
$showdesc 	= botigaHelper::getParameter('show_desc', 1);
$showbrand 	= botigaHelper::getParameter('show_brand', 1);
$showfav 	= botigaHelper::getParameter('show_fav', 1);
$showpvp 	= botigaHelper::getParameter('show_pvp', 1);
$userToken  = JSession::getFormToken();

$dte_linia  = botigaHelper::getUserData('dte_linia', $user->id);

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$control_stock = botigaHelper::getParameter('control_stock', 0);

$count 	    = botigaHelper::getCarritoCount();
$subtotal   = 0;
?>

<script>
jQuery(document).ready(function() {
	jQuery('.input-number').keypress(function(event) {
		if (event.keyCode == 13 || event.which == 13) {
		    document.forms.addtocart.submit();
		    event.preventDefault();
		}
	});
});
</script>

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
				<div class="col-6 col-md-8 text-left">			
					<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<?php if(botigaHelper::isEmpresa()) : ?>					
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<?php endif; ?>
					<span class="pl-3 phone-hide estil02"><?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
				</div>
				<div class="col-6 col-md-4 text-right">
					<a href="<?php if($count > 0) : ?>index.php?option=com_botiga&view=checkout<?php else: ?>#<?php endif; ?>" class="pr-2 carrito">
						<?php if($count > 0) : ?>
						<span class="badge badge-warning"><?= $count; ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>
					<?php if($user->guest) : ?>
					<a href="index.php?option=com_users&view=login" title="Login" class="hasTip">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
					<?php else: ?>
					<a class="ml-4 hasTip" href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" title="Salir">
						<img src="media/com_botiga/icons/salir.png">
					</a>
					<a class="ml-4 hasTip" href="index.php?option=com_botiga&view=history" title="History" title="Perfil"s>
						<img src="media/com_botiga/icons/sesion-iniciada.png">
					</a>
					<div class="d-none d-sm-block"><small><?= JText::sprintf('COM_BOTIGA_WELCOME', $user->name); ?></small></div>
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
			
			<div class="table-responsive items">
			
				<table class="table">
				<?php 
				if(count($this->items)) :
				$i = 0;
				foreach($this->items as $item) : ?>	
						
					<?php $item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg'; ?>
					<?php $price = botigaHelper::getUserPrice($item->id); ?>
					<tr>
						<td width="12%">
							<div class="botiga-img text-center">
								<?php 
								$extra_images = botigaHelper::getImages($item->id);
								$j = 0;
								foreach($extra_images as $k => $v) :
								if($j == 1) { break; }
								$second_image = $v[0];
								$j++;
								endforeach; 
								?>
								<a href="<?= $second_image; ?>" data-fancybox="gallery<?= $i; ?>">
									<?php if($dte_linia != 0.00 && $showdiscount == 1 && $showprices == 1) : ?>
									<div class="pvp-badge"><span>-<?= $dte_linia; ?>%</span></div>
									<?php endif; ?>
									<span class="rollover"><img src="media/com_botiga/icons/lupa.png" width="30"></span>
									<img src="<?= $image; ?>" class="img-fluid" alt="<?= $item->name; ?>" rel="gallery<?= $i; ?>" width="50" />
								</a>
								<?php 
								$extra_images = botigaHelper::getImages($item->id);
								foreach($extra_images as $k => $v) : ?>
								<a href="<?= $v[0]; ?>" data-fancybox="gallery<?= $i; ?>">
								<img src="<?= $v[0]; ?>" class="img-fluid" alt="<?= $item->name; ?>" rel="gallery<?= $i; ?>" style="display:none;" />
								</a>
								<?php endforeach; ?>
							</div>
						</td>
						<?php if($showprices == 1 || ($loginprices == 1 && !$user->guest)) : ?>
						<td width="12%" class="align-middle phone-hide">
							<?php if($dte_linia != 0.00 && $showdiscount == 1 && $showprices == 1) : ?>
							<span class="bold estil05 text-dark"><strike class="faded"><?= $price; ?>&euro;</strike>
							<br>
							<?= botigaHelper::getPercentDiff($price, $dte_linia); ?>&euro;</span>
							<?php else : ?>
							<span class="bold estil05 text-dark"><?= $price; ?>&euro;</span>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($showref == 1) : ?>
						<td class="align-middle"><?= $item->ref; ?></td>
						<?php endif; ?>						
						<td width="18%" class="align-middle phone-hide table-text">
							<span class="estil05 text-dark"><?= $item->name; ?></span>
							
							<?php if($control_stock == 1 && $item->stock == 0) : ?>
							<span class="estil03 text-danger"><?= JText::_('COM_BOTIGA_ITEM_WITHOUT_STOCK'); ?></span>
							<?php endif; ?>
							
							<span class="estil03 text-dark"><?php if($showdesc == 1) : ?><br><?= $item->s_description; ?><?php endif; ?></span>
							<span class="estil03 text-dark d-block d-md-none">
							<?= $price; ?>&euro;
							</span>
						</td>						
						<?php if($showbrand == 1) : ?>
						<td class="align-middle"><?= $item->brandname; ?></td>
						<?php endif; ?>
						<td class="align-middle">
							<form name="addtocart" id="addtocart" action="<?= JRoute::_('index.php?option=com_botiga'); ?>" method="get">
							<div class="estil05 text-left text-dark phone-visible"><?= $item->name; ?>&nbsp;<?= $price; ?>&euro;</div>
							<div class="row text-right">
								<div class="col-8">								
									<input type="hidden" name="id" value="<?= $item->id; ?>">
									<input type="hidden" name="return" value="<?= $uri; ?>">
									<input type="hidden" name="task" value="botiga.setItem">
									<input type="hidden" name="layout" value="table">
			                        <div class="input-group">
			                            <span class="input-group-btn">
			                                <button type="button" class="quantity-left-minus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
			                                  <span class="fa fa-minus"></span>
			                                </button>
			                            </span>
			                            <input type="text" id="quantity_<?= $item->id; ?>" name="qty" class="form-control bg-qty input-number text-center estil05" min="1" max="100" <?php if($i == 0) : ?>autofocus<?php endif; ?> value="<?= $model->getQtyRow($item->id); ?>">
			                            <span class="input-group-btn">
			                                <button type="button" class="quantity-right-plus btn btn-primary btn-number" data-id="<?= $item->id; ?>">
			                                    <span class="fa fa-plus"></span>
			                                </button>
			                            </span>
			                        </div>
				                </div>
				                <div class="col-1">
				                <?php if(botigaHelper::isValidated() && botigaHelper::validateStock($item->stock)) : ?>
								<input type="image" src="media/com_botiga/icons/addtocart.png" alt="<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>">
								<?php else: ?>
								<img src="media/com_botiga/icons/carrito_desactivado.png" alt="<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>">
								<?php endif; ?>
								</div>
							</div>
							<?php echo JHtml::_( 'form.token' ); ?>
							</form>
						</td>
						<?php if($showprices == 1 || ($loginprices == 1 && !$user->guest)) : ?>
						<?php if($dte_linia != 0.00 && $showdiscount == 1 && $showprices == 1) { $price = botigaHelper::getPercentDiff($price, $dte_linia); } ?>
						<?php $total_row = $model->getNumItemsRow($item->id, $price); ?>
						<td class="align-middle phone-hide"><div class="bold estil05 text-dark"><?= $total_row; ?>&euro;</div></td>
						<?php $subtotal += $total_row; ?>
						<?php endif; ?>
						<?php if($showfav == 1) : ?> 
						<td class="align-middle d-none d-md-block">
						<?php if(!botigaHelper::isFavorite($item->id)) : ?>
									<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=setFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart"></span> Favorito</a>
								<?php else : ?>
									<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart red"></span> No Favorito</a>
								<?php endif; ?>
						</td>
						<?php endif; ?>
					</tr>
				<?php
				$i++;
				endforeach; 
				else:
				?>
				<?= JText::_('COM_BOTIGA_NO_ITEMS'); ?>
				<?php
				endif;
				?>
				</table>
				
				<?php if($subtotal > 0) : ?>
				<table class="table mt-5">
					<tr>
						<td colspan="5" class="align-middle estil03"><span><?= JText::_('COM_BOTIGA_CHECKOUT_SUBTOTAL'); ?></span></td>
						<td align="right" class="align-middle estil05"><span class="blue bold total text-right"><?= number_format($subtotal, 2, '.', ''); ?>&euro;</span></td>
					</tr>
					<tr>
						<?php 
						$iva_percent += botigaHelper::getParameter('iva', '21');
					 	$iva_total   += ($iva_percent / 100) * ($subtotal + $shipment); 
					 	?>
						<td colspan="5" class="align-middle estil03"><?= 'IVA '.$iva_percent.'%'; ?></td>				
						<td align="right" class="align-middle estil05"><span><?= number_format($iva_total, 2, '.', ''); ?>&euro;</span></td>
					</tr>
					<tr>
						<?php $total = $subtotal + $iva_total; ?>
						<td colspan="5" class="align-middle estil03"><?= JText::_('COM_BOTIGA_CHECKOUT_TOTAL'); ?></td>				
						<td align="right" class="align-middle estil05"><span><?= number_format($total, 2, '.', ''); ?>&euro;</span></td>
					</tr>
				</table>
				<a href="index.php?option=com_botiga&view=checkout" class="btn btn-primary my-5"><?= JText::_('COM_BOTIGA_GOTO_CHECKOUT'); ?></a>
				<?php endif; ?>
				
			</div>
			
			<div class="paginacion">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
	
	</div>
	
</div>
