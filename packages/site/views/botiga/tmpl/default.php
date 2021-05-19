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

$user  			 = JFactory::getUser();
$uri 			 = base64_encode(JUri::current());
$jinput			 = JFactory::getApplication()->input;
$modal 			 = $jinput->get('m', 0);
$catid           = $jinput->get('catid', '');
$marca           = $jinput->get('marca', '');
$itemid          = $jinput->get('Itemid', '');
$orderby         = $jinput->get('orderby', 'ref');
$limit           = $jinput->get('limit', 24);
$lang 			 = JFactory::getLanguage()->getTag();
$showprices      = $this->params->get('show_prices', 1);
$showdiscount 	 = $this->params->get('show_discount', 0);
$loginprices 	 = $this->params->get('login_prices', 0);
$loginforbuy 	 = $this->params->get('login_buy', 1);
$shownotice 	 = $this->params->get('show_notice', 1);
$showref 		 = $this->params->get('show_ref', 1);
$showdesc 		 = $this->params->get('show_desc', 1);
$showbrand 		 = $this->params->get('show_brand', 1);
$showfav 		 = $this->params->get('show_fav', 1);
$showpvp 		 = $this->params->get('show_pvp', 1);
$control_stock 	 = $this->params->get('control_stock', 0);
$dte_linia  	 = botigaHelper::getUserData('dte_linia', $user->id);
?>

<?php if($this->params->get('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="row">

			<?php if($user->guest && $shownotice == 1) : ?>
			<div class="col-12 my-5">
				<?php $link1 = 'index.php?option=com_botiga&view=register'; ?>
				<div class="alert alert-warning"><?= JText::sprintf('COM_BOTIGA_PRICES_NOTICE', $link1); ?></div>
			</div>
			<?php endif; ?>

			<div class="col-12 shop_filters my-4">
				<form name="filters" id="filters" method="get" action="index.php?option=com_botiga&view=botiga" class="form-inline" onchange="filters.submit();">
					<input type="hidden" name="option" value="com_botiga" />
					<input type="hidden" name="view" value="botiga" />
					<input type="hidden" name="marca" value="<?= $marca; ?>" />
					<input type="hidden" name="Itemid" value="<?= $itemid; ?>" />
					<div class="row">
						<div class="form-group col-12 col-md-6 mx-auto">
							<div class="styled-select">
								<select name="catid" id="catid" class="form-control text-primary" style="width:100%;">
									<option value=""><?= JText::_('COM_BOTIGA_SELECT_AN_OPTION'); ?></div>
									<?php foreach(botigaHelper::getCategories() as $cat) : ?>
									<option value="<?= $cat->id; ?>" <?php if($catid == $cat->id) : ?>selected<?php endif; ?>><?= $cat->title; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group col-12 col-md-6 mx-auto">
							<div class="styled-select">
								<select name="orderby" id="orderby" class="form-control text-primary" style="width:100%;">
									<option value="id"><?= JText::_('COM_BOTIGA_ORDERBY'); ?></div>
									<option value="ref" <?php if($orderby == 'ref') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_REF'); ?></option>
									<option value="s_description" <?php if($orderby == 's_description') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_DESC'); ?></option>
									<option value="pvp" <?php if($orderby == 'pvp') : ?>selected<?php endif; ?>><?= JText::_('COM_BOTIGA_FILTER_PRICE'); ?></option>
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="col-12 items">
				<div class="row">

				<?php
				if(count($this->items)) :
				$i = 0;
				foreach($this->items as $item) :

				$item->image1 != '' ? $image = $item->image1 : $image = 'components/com_botiga/assets/images/noimage.jpg';
				$precio = botigaHelper::getUserPrice($item->id);
				$dtos   = botigaHelper::getUserDiscounts($item->id);
				?>

        <?= "<!-- Start item $i -->"; ?>
        <div class="col-md-3 col-sm-6 item">
          <div class="product-grid4">
            <div class="product-image4">
                <a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>">
                  <img class="pic-1" src="<?= $image; ?>">
                  <?php
        					$extra_images = botigaHelper::getImages($item->id);
                  if(count($extra_images)) :
        					foreach($extra_images as $k => $v) : ?>
                  <img class="pic-2" src="<?= $v[0]; ?>">
                  <?php break; endforeach; ?>
                  <?php endif; ?>
                </a>
                <ul class="social">
                  <li><a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>" data-tip="Quick View"><i class="fa fa-eye"></i></a></li>
                  <?php if($showfav == 1) : ?>
      						<?php if(!botigaHelper::isFavorite($item->id)) : ?>
                  <li><a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="<?php if(!$user->guest) : ?>setFavorite<?php endif; ?> item<?= $item->id; ?> data-tip="<?= JText::_('COM_BOTIGA_BTN_FAV'); ?>"><i class="fa fa-shopping-bag"></i></a></li>
                  <?php else : ?>
                  <a href="#" data-id="<?= $item->id; ?>" <?php if($user->guest) : ?>disabled="true"<?php endif; ?> class="<?php if(!$user->guest) : ?>unsetFavorite<?php endif; ?> item<?= $item->id; ?>" data-tip="<?= JText::_('COM_BOTIGA_BTN_UNFAV'); ?>"><i class="fa fa-shopping-bag"></i></a></li>
                  <?php endif; ?>
                  <?php if($showprices == 1 && ($loginforbuy == 0 || ($loginforbuy == 1 && !$user->guest))) : ?>
                  <?php if(botigaHelper::isValidated() && botigaHelper::validateStock($item->stock)) : ?>
                  <li><a href="index.php?view=botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>&<?= JSession::getFormToken(); ?>=1" data-tip="<?= JText::_('COM_BOTIGA_BTN_BUY'); ?>"><i class="fa fa-shopping-cart"></i></a></li>
                  <?php endif; ?>
                  <?php endif; ?>
                </ul>
                <span class="product-new-label">New</span>
                <?php if($dte_linia != 0.00 && $showdiscount == 1 && botigaHelper::isPriceVisible()) : ?>
                <span class="product-discount-label">-<?= $dte_linia; ?>%</span>
                <?php endif; ?>
            </div>
            <div class="product-content">
                <h3 class="title"><a href="<?= JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id); ?>"><?= $item->name; ?></a></h3>
                <?php if($showref == 1) : ?>
    						<div class="item-ref"><?= $item->ref; ?></div>
    						<?php endif; ?>
                <?php if($showbrand == 1) : ?>
    						<div class="text-left item-brand"><?= $item->brandname; ?></div>
    						<?php endif; ?>
                <?php if($showdesc == 1) : ?>
    						<div class="text-left estil03 text-dark item-desc"><?= $item->s_description; ?></div>
    						<?php endif; ?>
                <?php if($control_stock == 1 && $item->stock == 0) : ?>
    						<div class="text-left text-danger"><?= JText::_('COM_BOTIGA_ITEM_WITHOUT_STOCK'); ?></div>
    						<?php endif; ?>
                <div class="price">
                  <?php if(botigaHelper::isPriceVisible()) : ?>
                    <?php if($showdiscount == 1 && $dte_linia != 0.00) : ?>
      							<strike><?php if(!botigaHelper::isEmpresa()) : ?>PVP <?php endif; ?><?= $precio; ?>&euro;</strike>
      							<?php else: ?>
      							<?php if(!botigaHelper::isEmpresa()) : ?>PVP <?php endif; ?><?= $precio; ?>&euro;<?php endif; ?>
                    <?php endif; ?>
                    <?php if($showpvp == 1) : ?>
                    <span><?= $item->pvp; ?>&euro;</span>
                    <?php endif; ?>
                    <?php if($showdiscount == 1 && $dte_linia != 0.00) : ?>
    								<div><?= botigaHelper::getPercentDiff($precio, $dte_linia); ?>&euro;</div>
    								<?php endif; ?>
        						<?php if($dtos !='') : ?>
        						<div><?= $dtos; ?>&euro;</div>
        						<?php endif; ?>
                  <?php endif; ?>
                </div>
                <a class="add-to-cart btn btn-primary" href="index.php?view=botiga&task=botiga.setItem&id=<?= $item->id; ?>&return=<?= $uri; ?>&<?= JSession::getFormToken(); ?>=1"><?= JText::_('COM_BOTIGA_BTN_BUY'); ?></a>
            </div>
          </div>
        </div>
        <?= "<!-- End item $i -->"; ?>

				<?php
				//endif;
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
					$img  = botigaHelper::getItemData('image1', $modal);
					$preu = botigaHelper::getUserPrice($modal);
					$img != '' ? $image = $img : $image = 'components/com_botiga/assets/images/noimage.jpg';
					?>
					<img src="<?= $image; ?>" alt="" class="img-fluid" width="50">
				</div>
				<div class="col-xs-12 col-md-6 text-left py-3">
					<b><?= botigaHelper::getItemData('name', $modal); ?></b>
					<?= botigaHelper::getItemData('s_description', $modal); ?><br>
					<?php if($dte_linia != 0.00 && $showdiscount == 1 && $showprices == 1) : ?>
					<strike class="faded"><?= $preu; ?>&euro;</strike>&nbsp;<b><?= botigaHelper::getPercentDiff($preu, $dte_linia); ?>&euro;</b>
					<?php else: ?>
					<b><?= botigaHelper::getUserPrice($modal); ?>&euro;</b>
					<?php endif; ?>
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
