<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyleft (C) 2019
 * @license     Licencia Pública General GNU versión 3 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

if(!botigaHelper::hasAccesstoTableView()) { $app->redirect('index.php'); }

$doc   		= JFactory::getDocument();
$doc->addScript('components/com_botiga/assets/js/jquery.fancybox.js');
$doc->addStylesheet('components/com_botiga/assets/css/jquery.fancybox.css');

$model 			  = $this->getModel('botiga');
$user  			  = JFactory::getUser();
$uri 			    = base64_encode(JFactory::getURI()->toString());
$jinput			  = $app->input;
$catid      	= $jinput->get('catid', '');
$marca      	= $jinput->get('marca', '');
$itemid     	= $jinput->get('Itemid', '');
$orderby    	= $jinput->get('orderby', 'ref');
$limit      	= $jinput->get('limit', 24);
$lang 			  = JFactory::getLanguage()->getTag();
$showprices 	= botigaHelper::getParameter('show_prices', 1);
$showdiscount = botigaHelper::getParameter('show_discount', 0);
$loginprices 	= botigaHelper::getParameter('login_prices', 0);
$loginforbuy 	= botigaHelper::getParameter('login_buy', 1);
$shownotice 	= botigaHelper::getParameter('show_notice', 1);
$showref 		  = botigaHelper::getParameter('show_ref', 1);
$showdesc 		= botigaHelper::getParameter('show_desc', 1);
$showbrand 		= botigaHelper::getParameter('show_brand', 1);
$showfav 		  = botigaHelper::getParameter('show_fav', 1);
$showpvp 		  = botigaHelper::getParameter('show_pvp', 1);
$dte_linia  	= botigaHelper::getUserData('dte_linia', $user->id);
$control_stock 	= botigaHelper::getParameter('control_stock', 0);
$subtotal   	= 0;
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div>

		<div class="row">

      <?php if($user->guest && $shownotice == 1) : ?>
			<div class="col-12">
				<?php
				$link1 = 'index.php?option=com_botiga&view=register';
				?>
				<div class="alert alert-warning"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', $link1); ?></div>
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
								<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_REF'); ?></option>
								<option value="s_description" <?php if($orderby == 's_description') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_DESC'); ?></option>
								<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_PRICE'); ?></option>
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
						<?php if($showprices == 1) : ?>
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
							<?php if(botigaHelper::isPriceVisible()) : ?>
							<span class="estil03 text-dark d-block d-md-none">
							<?= $price; ?>&euro;
							</span>
							<?php endif; ?>
						</td>
						<?php if($showbrand == 1) : ?>
						<td class="align-middle"><?= $item->brandname; ?></td>
						<?php endif; ?>
						<td class="align-middle">
							<form name="addtocart" id="addtocart-<?= $item->id; ?>" action="<?= JRoute::_('index.php?option=com_botiga'); ?>" method="get">

							<?php if(botigaHelper::isPriceVisible()) : ?>
							<div class="estil05 text-left text-dark phone-visible"><?= $item->name; ?>&nbsp;<?= $price; ?>&euro;</div>
							<?php endif; ?>

							<?php if($showprices == 1 && ($loginforbuy == 0 || ($loginforbuy == 1 && !$user->guest))) : ?>
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
			                            <input type="text" id="quantity_<?= $item->id; ?>" name="qty" data-itemid="<?= $item->id; ?>" class="form-control bg-qty input-number text-center estil05" min="1" max="100" <?php if($i == 0) : ?>autofocus<?php endif; ?> value="<?= $model->getQtyRow($item->id); ?>">
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
						<?php if(botigaHelper::isPriceVisible()) : ?>
						<?php if($dte_linia != 0.00 && $showdiscount == 1) { $price = botigaHelper::getPercentDiff($price, $dte_linia); } ?>
						<?php $total_row = $model->getNumItemsRow($item->id, $price); ?>
						<td class="align-middle phone-hide">
							<?php if($showprices == 1 && ($loginprices == 1 && !$user->guest)) : ?>
							<div class="bold estil05 text-dark"><?= $total_row; ?>&euro;</div>
							<?php endif; ?>
						</td>
						<?php $subtotal += $total_row; ?>
						<?php endif; ?>
						<?php endif; ?>
						<?php if($showfav == 1) : ?>
						<td class="align-middle d-none d-md-block">
						<?php if(!botigaHelper::isFavorite($item->id)) : ?>
									<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=setFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart"></span> <?= JText::_('COM_BOTIGA_BTN_FAV'); ?></a>
								<?php else : ?>
									<a <?php if(!$user->guest) : ?>href="index.php?option=com_botiga&task=unsetFavorite&id=<?= $item->id; ?>&return=<?= $uri; ?>"<?php else : ?>disabled="true"<?php endif; ?>><span class="glyphicon glyphicon-heart red"></span> <?= JText::_('COM_BOTIGA_BTN_UNFAV'); ?></a>
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

      <!-- Modal login -->
      <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginLabel">
        	<div class="modal-dialog" role="document">
      		<div class="modal-content">
      	  		<div class="modal-header">
      	    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      	  		</div>
      	  		<div class="modal-body loginBody">
      			    <?php
      			    $document	= JFactory::getDocument();
      				  $renderer	= $document->loadRenderer('module');
      				  echo $renderer->render(JModuleHelper::getModule('mod_login'));
      			    ?>
      	  		</div>
      		</div>
        	</div>
      </div>
      <!-- end login modal -->

	</div>

</div>
