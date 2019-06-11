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

$user   = JFactory::getUser();
$app    = JFactory::getApplication();
$jinput	= JFactory::getApplication()->input;

JHtml::_('behavior.formvalidator');
$link = '';

$f  = array();
$f[1] = botigaHelper::getParameter('show_field_tipus', 1);
$f[2] = botigaHelper::getParameter('show_field_empresa', 1);
$f[3] = botigaHelper::getParameter('show_field_cif', 1);
$f[4] = botigaHelper::getParameter('show_field_nom', 1);
$f[5] = botigaHelper::getParameter('show_field_phone', 1);
$f[6] = 1;
$f[7] = 1;
$f[8] = 1;
$f[9] = 1;
$f[10] = botigaHelper::getParameter('show_field_address', 1);
$f[11] = botigaHelper::getParameter('show_field_cp', 1);
$f[12] = botigaHelper::getParameter('show_field_state', 1);
$f[13] = botigaHelper::getParameter('show_field_pais', 1);

$show_newsletter = botigaHelper::getParameter('show_newsletter', 1);
$logo		= botigaHelper::getParameter('botiga_logo', '');
$userToken  = JSession::getFormToken();
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) : ?>
<header class="head_botiga">

	<div class="col-md-11 mx-auto">

		<div class="row">

		<?php if($logo != '') : ?>
		<div class="col-12 text-right d-none d-sm-block">
			<a href="index.php">
				<img src="<?= $logo; ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid botiga-logo">
			</a>
		</div>
		<?php endif; ?>	
		
		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-8 text-left">			
					<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
						<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
					</a>
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<span class="pl-3 phone-hide estil02"><?= JText::_('COM_BOTIGA_FREE_SHIPPING_MSG'); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
				</div>
				<div class="col-4 text-right">
					<a href="index.php?option=com_botiga&view=checkout" class="pr-1 carrito">
						<?php if(botigaHelper::getCarritoCount() > 0) : ?>
						<span class="badge badge-warning"><?= botigaHelper::getCarritoCount(); ?></span>
						<?php endif; ?>
						<img src="media/com_botiga/icons/carrito.png">
					</a>
					<?php if($user->guest) : ?>
					<a href="index.php?option=com_users&view=login" title="Login" class="hasTip">
						<img src="media/com_botiga/icons/iniciar-sesion.png">
					</a>
					<?php else: ?>
					<a href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" class="hasTip">
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

<div class="col-md-4 mx-auto  mt-5 mb-5"> 

	<div class="text-center">
		<h1 class="text-dark estil09"><?= JText::_('COM_BOTIGA_REGISTER_TITLE'); ?></h1>
	</div>

	<div>

		<form name="register" id="register" action="index.php?option=com_botiga&task=register.register" method="post" class="form-horizontal form-validate">
		
				<?php 
				$i = 1;
				foreach($this->form->getFieldset('register') as $field): ?>
				<?php if($f[$i] == 1): ?> 
				<?= $field->renderField(); ?>
				<?php endif; ?>
				<?php 
				$i++;
				endforeach; ?>
				
				<div class="checkbox estil01 pt-4">
				  <label>
					<input type="checkbox" value=""  class="tos required" required="required">
					<?= JText::sprintf('COM_BOTIGA_REGISTER_LOPD', ''); ?>
				  </label>
				</div>
				
				<?php if($show_newsletter == 1) : ?>
				<div class="checkbox estil01">
				  <label>
					<input type="checkbox" name="jform[cr]" class="cr" id="jform_cr">
					<?= JText::sprintf('COM_BOTIGA_REGISTER_NEWSLETTER', ''); ?>
				  </label>
				</div>
				<?php endif; ?>
				
				<div id="form-login-submit" class="control-group">
					<div class="controls">
						<button disabled="disabled" type="submit" class="btn btn-primary btn-block validate submit estil03"><?= JText::_('COM_BOTIGA_REGISTER'); ?></button>
					</div>
				</div>
				<input type="hidden" name="option" value="com_botiga" />
				<input type="hidden" name="jform[newsletter]" class="newsletter" value="0" />
				<input type="hidden" name="task" value="register.register" />
				<?= JHtml::_('form.token'); ?>
				
				<div class="estil01 mt-3"><?= JText::_('COM_BOTIGA_REGISTER_SUBTITLE'); ?></div>
				
		</form>
	</div>
	
</div>
