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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
$user  = JFactory::getUser();
$app   = JFactory::getApplication();

if($user->guest) {
	$returnurl = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::current()), false);
    $app->redirect($returnurl, JText::_('COM_BOTIGA_REDIRECT_GUESTS'), 'warning');
}

$f  = array();
$f[1] = botigaHelper::getParameter('show_field_tipus', 1);
$f[2] = botigaHelper::getParameter('show_field_empresa', 1);
$f[3] = botigaHelper::getParameter('show_field_cif', 1);
$f[4] = botigaHelper::getParameter('show_field_nom', 1);
$f[5] = botigaHelper::getParameter('show_field_surname', 1);
$f[6] = botigaHelper::getParameter('show_field_phone', 1);
$f[7] = 1;
$f[8] = 1;
$f[9] = 1;
$f[10] = 1;
$f[11] = botigaHelper::getParameter('show_field_address', 1);
$f[12] = botigaHelper::getParameter('show_field_cp', 1);
$f[13] = botigaHelper::getParameter('show_field_state', 1);
$f[14] = botigaHelper::getParameter('show_field_pais', 1);
?>

<style>#page-header { border: none; }</style>

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
						<img src="media/com_botiga/icons/mosaico<?php if($app->input->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($app->input->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
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
					<div class="d-none d-sm-block"><small><?= JText::sprintf('COM_BOTIGA_WELCOME', $user->name); ?></small></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		</div>

	</div>

</header>
<?php endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="row">

		<div class="page-header">
			<h3><?= JText::_('MIS DATOS'); ?></h3>
			<hr>
			<p><?= JText::_('COM_BOTIGA_REGISTER_SUBTITLE'); ?></p>
		</div>

		<div class="col-md-12">

			<form name="register" id="register" action="<?= JRoute::_('index.php?option=com_botiga&view=profile&task=profile.profile'); ?>" method="post" class="form-horizontal form-validate">
					<input type="hidden" name="jform[id]" value="<?= $user->id; ?>" />
					<?php
					$i = 1;
					foreach($this->form->getFieldset('profile') as $field): ?>
					<?php if($f[$i] == 1): ?>
					<?= $field->renderField(); ?>
					<?php endif; ?>
					<?php
					$i++;
					endforeach; ?>

					<div class="checkbox text-right">
					  <label>
						<input type="checkbox" value="" class="tos">
						<?= JText::_('COM_BOTIGA_REGISTER_LOPD'); ?>
					  </label>
					</div>

					<div id="form-login-submit" class="control-group">
						<div class="controls text-right">
							<button disabled="disabled" type="submit" class="btn btn-primary validate submit"><?= JText::_('JSUBMIT'); ?></button>
						</div>
					</div>
					<input type="hidden" name="option" value="com_botiga" />
					<input type="hidden" name="task" value="profile.profile" />
					<?php echo JHtml::_('form.token');?>

			</form>
		</div>
	</div>
</div>
