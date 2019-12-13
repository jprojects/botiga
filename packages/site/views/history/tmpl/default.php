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
$model = $this->getModel('history');
$user  = JFactory::getUser();
$app   = JFactory::getApplication();

if($user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()), false);
    $app->redirect($returnurl, JText::_('COM_BOTIGA_REDIRECT_GUESTS'), 'warning');
}

$logo		= botigaHelper::getParameter('botiga_logo', '');
$showsavedcarts		= botigaHelper::getParameter('show_savedcarts', 0);
$userToken  = JSession::getFormToken();

$show_tipus 	= botigaHelper::getParameter('show_field_tipus', 1);
$show_empresa 	= botigaHelper::getParameter('show_field_empresa', 1);
$show_cif 		= botigaHelper::getParameter('show_field_cif', 1);
$show_nom 		= botigaHelper::getParameter('show_field_nom', 1);
$show_phone 	= botigaHelper::getParameter('show_field_phone', 1);
$show_address 	= botigaHelper::getParameter('show_field_address', 1);
$show_cp 		= botigaHelper::getParameter('show_field_cp', 1);
$show_state 	= botigaHelper::getParameter('show_field_state', 1);
$show_pais 		= botigaHelper::getParameter('show_field_pais', 1);

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$count 	    = botigaHelper::getCarritoCount();
$user_params = json_decode(botigaHelper::getUserData('params', $user->id), true);

$addresses = botigaHelper::getAddresses();
?>

<script>
jQuery(document).ready(function() {
	jQuery('.repeatable').repeater({
		btnAddClass: 'r-btnAdd',
		btnRemoveClass: 'r-btnRemove',
		groupClass: 'r-group',
		minItems: 1,
		maxItems: 0,
		startingIndex: 0,
		showMinItemsOnLoad: true,
		reindexOnDelete: true,
		repeatMode: 'append',
		animation: 'fade',
		animationSpeed: 400,
		animationEasing: 'swing',
		clearValues: true
	}, [
		<?php
		$i = 0;
		foreach($addresses as $adreca) : ?>
				{"address_<?= $i; ?>" : "<?= $adreca->adreca; ?>",
				"zip_<?= $i; ?>" : "<?= $adreca->cp; ?>",
				"city_<?= $i; ?>" : "<?= $adreca->poblacio; ?>"}
		<?php
		$i++;
		if($i < count($addresses)) { echo ','; }
		endforeach; ?>
	]);
});
</script>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="row">

		<!-- Nav tabs -->
		<nav class="mt-4 estil08">
		  	<div class="nav nav-tabs nav-fill" role="tablist">
				<a class="nav-item nav-link <?php if($app->input->getInt('tab', 1) == 1) : ?>active<?php endif; ?> text-primary" href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab"><?= strtoupper(JText::_('COM_BOTIGA_HISTORY_MY_ORDERS')); ?></a>
				<span class="nav-span">/</span>
				<a class="nav-item nav-link <?php if($app->input->getInt('tab', 1) == 2) : ?>active<?php endif; ?> text-primary" href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab"><?= strtoupper(JText::_('COM_BOTIGA_HISTORY_MY_SETTINGS')); ?></a>
				<?php if($showsavedcarts == 1): ?>
				<span class="nav-span">/</span>
				<a class="nav-item nav-link <?php if($app->input->getInt('tab', 1) == 3) : ?>active<?php endif; ?> text-primary" href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab"><?= strtoupper(JText::_('COM_BOTIGA_HISTORY_MY_CARTS')); ?></a>
				<?php endif; ?>
		  	</div>
	  	</nav>

	  	<!-- Tab panes -->
	  	<div class="tab-content col-12 py-3 px-3 px-sm-0">

	  		<div role="tabpanel" class="tab-pane fade <?php if($app->input->getInt('tab', 1) == 1) : ?>show active<?php endif; ?>" id="tab1">
				<?php if(count($this->items)) : ?>
				<table class="table">
					<?php foreach($this->items as $item) : ?>
					<?php $iva = (21 / 100) * $item->subtotal; ?>
					<?php $item->status == 3 ? $status = JText::_('COM_BOTIGA_HISTORY_STATUS_FINISHED') : $status = JText::_('COM_BOTIGA_HISTORY_STATUS_PENDING'); ?>
					<tr class="estil03">
						<td><?= JText::_('COM_BOTIGA_HISTORY_NUM'); ?> <?= $item->uniqid; ?></td>
						<td class="phone-hide"><?= $item->data; ?></td>
						<td class="phone-hide"><?= $status; ?></td>
						<td align="right" class="phone-hide"><b class="estil05"><?= number_format(($item->total), 2); ?>&euro;</b></td>
						<td align="right">
							<a href="index.php?option=com_botiga&task=genPdf&id=<?= $item->id; ?>" target="_blank">
								<img src="media/com_botiga/icons/descargar.png" alt="Descargar" class="img-fluid">
							</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php else : ?>
				<div><?= JText::_('COM_BOTIGA_HISTORY_NO_ORDERS'); ?></div>
				<?php endif; ?>
			</div>
			<div role="tabpanel" class="tab-pane fade <?php if($app->input->getInt('tab', 1) == 2) : ?>show active<?php endif; ?>" id="tab2">
				<div class="col-xs-12 col-md-6 mx-auto">
					<form name="register" id="register" action="index.php?option=com_botiga&task=profile.profile" method="post" class="form-validate">
						<input type="hidden" name="jform[userid]" value="<?= $user->id; ?>" />
						<input type="hidden" name="option" value="com_botiga" />
						<input type="hidden" name="task" value="profile.profile" />
						<?php echo JHtml::_('form.token');?>

						<?php if(botigaHelper::getUserData('type', $user->id) == 1) : ?>
						<div class="form-group">
							<label for="jform_empresa estil03"><?= JText::_('COM_BOTIGA_REGISTER_NOMBRE_EMPRESA_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[empresa]" id="jform_empresa" class="form-control required" value="<?= botigaHelper::getUserData('nom_empresa', $user->id); ?>">
						</div>

						<div class="form-group">
							<label for="jform_cif estil03"><?= JText::_('COM_BOTIGA_REGISTER_CIF_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[cif]" id="jform_cif" class="form-control required" value="<?= botigaHelper::getUserData('cif', $user->id); ?>">
						</div>
						<?php endif; ?>

						<div class="form-group">
							<label for="jform_nombre estil03"><?= JText::_('COM_BOTIGA_REGISTER_NOMBRE_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[nombre]" id="jform_nombre" class="form-control required" value="<?= botigaHelper::getUserData('nombre', $user->id); ?>">
						</div>

						<div class="form-group">
							<label for="jform_email1 estil03"><?= JText::_('COM_BOTIGA_REGISTER_EMAIL_LBL'); ?></label>
							<input type="email" name="jform[emal1]" id="jform_email1" class="form-control" value="<?= $user->email; ?>">
						</div>

						<div class="form-group">
							<label for="jform_email2 estil03"><?= JText::_('COM_BOTIGA_REGISTER_EMAIL2_LBL'); ?></label>
							<input type="email" name="jform[emal2]" id="jform_email2" class="form-control" value="">
						</div>

						<div class="form-group">
							<label for="jform_password1 estil03"><?= JText::_('COM_BOTIGA_REGISTER_PWD_LBL'); ?></label>
							<input type="password" name="jform[password1]" id="jform_password1" class="form-control" value="">
						</div>

						<div class="form-group">
							<label for="jform_password2 estil03"><?= JText::_('COM_BOTIGA_REGISTER_PWD2_LBL'); ?></label>
							<input type="password" name="jform[password2]" id="jform_password2" class="form-control" value="">
						</div>

						<div class="form-group">
							<label for="jform_phone estil03"><?= JText::_('COM_BOTIGA_REGISTER_PHONE_LBL'); ?></label>
							<input type="text" name="jform[phone]" id="jform_phone" class="form-control required" value="<?= botigaHelper::getUserData('telefon', $user->id); ?>">
						</div>
						<!--
						<div class="form-group">
							<label for="jform_address estil03"><?//= JText::_('COM_BOTIGA_REGISTER_ADDRESS_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[address]" id="jform_address" class="form-control required" value="<?//= botigaHelper::getUserData('adreca', $user->id); ?>">
						</div>

						<div class="form-group">
							<label for="jform_zip estil03"><?//= JText::_('COM_BOTIGA_REGISTER_CP_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[zip]" id="jform_zip" class="form-control required" value="<?//= botigaHelper::getUserData('cp', $user->id); ?>">
						</div>

						<div class="form-group">
							<label for="jform_city estil03"><?//= JText::_('COM_BOTIGA_REGISTER_CITY_LBL'); ?> *</label>
							<input type="text" required="required" name="jform[city]" class="form-control required" value="<?//= botigaHelper::getUserData('poblacio', $user->id); ?>">
						</div>
						-->
						<div class="form-group">
							<label for="jform_pais estil03"><?= JText::_('COM_BOTIGA_REGISTER_PAIS_LABEL'); ?> *</label>
							<select name="jform[pais]" required="required" id="jform_pais" class="form-control required">
								<option value=""><?= JText::_('COM_BOTIGA_SELECT_AN_OPTION'); ?></option>
								<?php foreach(botigaHelper::getCountries() as $country) : ?>
								<option value="<?= $country->country_id; ?>" <?php if($country->country_id == botigaHelper::getUserData('pais', $user->id)) : ?>selected<?php endif; ?>><?= $country->country_name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<legend><?= JText::_('COM_BOTIGA_ADDRESSES_SETTINGS'); ?></legend>

						<div class="repeatable">
							<div class="r-group">
								<div class="form-group">
									<label for="jform_address estil03"><?= JText::_('COM_BOTIGA_REGISTER_ADDRESS_LBL'); ?> *</label>
									<input type="text" required="required" name="address_0" id="address_0" class="form-control" value="" data-pattern-name="address_++" data-pattern-id="address_++">
								</div>

								<div class="form-group">
									<label for="jform_zip estil03"><?= JText::_('COM_BOTIGA_REGISTER_CP_LBL'); ?> *</label>
									<input type="text" required="required" name="zip_0" id="zip_0" class="form-control"  value="" data-pattern-name="zip_++" data-pattern-id="zip_++">
								</div>

								<div class="form-group">
									<label for="jform_city estil03"><?= JText::_('COM_BOTIGA_REGISTER_CITY_LBL'); ?> *</label>
									<input type="text" required="required" name="city_0" class="form-control" id="city_0"  value="" data-pattern-name="city_++" data-pattern-id="city_++">
								</div>
								<button type="button" class="r-btnRemove btn btn-danger">Eliminar</button>
							</div>
							<button type="button" class="r-btnAdd btn btn-primary">Añadir dirección</button>
						</div>

						<legend><?= JText::_('COM_BOTIGA_USER_SETTINGS'); ?></legend>

						<div class="form-group">
							<label for="jform_pais estil03"><?= JText::_('COM_BOTIGA_REGISTER_METODO_PAGO_LABEL'); ?> *</label>
							<select name="jform[metodo_pago]" id="jform_metodo_pago" class="form-control">
								<option value=""><?= JText::_('COM_BOTIGA_SELECT_AN_OPTION'); ?></option>
								<?php
								foreach(botigaHelper::getPaymentPlugins() as $plugin) :
									$params = json_decode($plugin->params);
									if($params->test_user != '' && $params->sandbox == 1) {
										if($user->id != $params->test_user) { continue; }
									}
									if($user_params['pago_habitual'] == 0 && strtolower($params->alies) == 'habitual') { continue; }
								?>
								<option value="<?= $params->alies; ?>" <?php if($user_params['metodo_pago'] == strtolower($params->alies)): ?>selected<?php endif; ?> ><?= JText::_($params->label); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label for="jform_pais estil03"><?= JText::_('COM_BOTIGA_REGISTER_RE_EQUIV_LABEL'); ?></label>
							<div class="form-check form-check-inline">
							  	<input class="form-check-input" type="radio" name="jform[re_equiv]" id="jform_re_equiv0" value="0" <?php if($user_params['re_equiv'] == 0) : ?>checked<?php endif; ?>>
							  	<label class="form-check-label" for="jform_re_equiv0"><?= JText::_('JNO'); ?></label>
							</div>
							<div class="form-check form-check-inline">
							  	<input class="form-check-input" type="radio" name="jform[re_equiv]" id="jform_re_equiv1" value="1" <?php if($user_params['re_equiv'] == 1) : ?>checked<?php endif; ?>>
							  	<label class="form-check-label" for="jform_re_equiv1"><?= JText::_('JYES'); ?></label>
							</div>
						</div>

						<div id="form-login-submit" class="control-group">
							<div class="controls">
								<button type="submit" class="btn btn-primary btn-block validate submit estil03"><?= JText::_('JSUBMIT'); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<?php if($showsavedcarts == 1): ?>
			<div role="tabpanel" class="tab-pane fade <?php if($app->input->getInt('tab', 1) == 3) : ?>show active<?php endif; ?>" id="tab3">
				<?php
				$carts = $model->getSavedCarts();
				if(count($carts)) : ?>
				<table class="table table-striped">
					<?php foreach($carts as $cart) : ?>
					<tr>
						<td><?= JText::_('COM_BOTIGA_CART'); ?>-<?= $cart->idComanda; ?></td>
						<td><?= $cart->data; ?></td>
						<td>
							<a href="index.php?option=com_botiga&task=botiga.getSavedCart&id=<?= $cart->id; ?>"><i class="fa fa-eye"></i> <?= JText::_('COM_BOTIGA_HISTORY_RECOVERY_SAVED_CART'); ?></a>
						</td>
						<td>
							<a href="index.php?option=com_botiga&task=botiga.removeSavedCart&id=<?= $cart->id; ?>"><i class="fa fa-trash"></i> <?= JText::_('COM_BOTIGA_HISTORY_REMOVE_SAVED_CART'); ?></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php else : ?>
				<div><?= JText::_('COM_BOTIGA_HISTORY_NO_SAVED_CARTS'); ?></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>
