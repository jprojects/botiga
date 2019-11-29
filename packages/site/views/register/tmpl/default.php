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

$user   = JFactory::getUser();
$app    = JFactory::getApplication();
$jinput	= JFactory::getApplication()->input;

JHtml::_('behavior.formvalidator');
$link = '';

$show_type 			= botigaHelper::getParameter('show_field_tipus', 1);
$show_empresa 		= botigaHelper::getParameter('show_field_empresa', 1);
$show_cif 			= botigaHelper::getParameter('show_field_cif', 1);
$show_nom 			= botigaHelper::getParameter('show_field_nom', 1);
$show_surname 		= botigaHelper::getParameter('show_field_surname', 1);
$show_phone 		= botigaHelper::getParameter('show_field_phone', 1);
$show_address 		= botigaHelper::getParameter('show_field_address', 1);
$show_cp 			= botigaHelper::getParameter('show_field_cp', 1);
$show_state 		= botigaHelper::getParameter('show_field_state', 1);
$show_pais 			= botigaHelper::getParameter('show_field_pais', 1);
$show_newsletter 	= botigaHelper::getParameter('show_newsletter', 1);

$spain 			= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 		= botigaHelper::getParameter('total_shipment_islands', 50);
$world 			= botigaHelper::getParameter('total_shipment_world', 60);
$validation 	= botigaHelper::getParameter('validation_type', 0); //by default 0 Joomla system 1 is google recaptcha
$sitekey 		= botigaHelper::getParameter('captcha_sitekey');
$logo			= botigaHelper::getParameter('botiga_logo', '');
$email			= botigaHelper::getParameter('botiga_mail', '');
$userToken  	= JSession::getFormToken();
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
					<?php if(botigaHelper::hasAccesstoTableView()) : ?>					
					<a href="index.php?option=com_botiga&view=botiga&layout=table">
						<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
					</a>
					<?php endif; ?>
					<span class="pl-3 phone-hide estil02"><?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png"></span>
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
		
			<?php if($show_type == 1) : ?>
			<div class="control-group" id="fieldType">
				<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
			</div>
			<small id="passwordHelpBlock" style="display:none;" class="form-text text-muted"><?= JText::sprintf('COM_BOTIGA_REGISTER_FIELD_TYPE_HELP', $email); ?></small>

			<div id="form-login-submit" class="control-group">
				<div class="controls">
					<a href="#" class="btn btn-primary btn-block estil03 next mt-2"><?= JText::_('COM_BOTIGA_NEXT'); ?></a>
				</div>
			</div>
			<?php endif; ?>
				
			<div id="registerFields" <?php if($show_type == 1) : ?>style="display:none;"<?php endif; ?>>

				<div id="empresaFields" <?php if($show_type == 1) : ?>style="display:none;"<?php endif; ?>>
					<?php if($show_empresa == 1) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('empresa'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('empresa'); ?></div>
					</div>
					<?php endif; ?>

					<?php if($show_cif == 1) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('cif'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('cif'); ?></div>
					</div>
					<?php endif; ?>
				</div>

				<?php if($show_nom == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('nombre'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('nombre'); ?></div>
				</div>
				<?php endif; ?>

				<?php if($show_surname == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('apellidos'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('apellidos'); ?></div>
				</div>
				<?php endif; ?>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email1'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email1'); ?></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email2'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email2'); ?></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('password1'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('password1'); ?></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('password2'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('password2'); ?></div>
				</div>

				<?php if($show_phone == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
				</div>
				<?php endif; ?>

				<?php if($show_address == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('address'); ?></div>
				</div>
				<?php endif; ?>

				<?php if($show_cp == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('cp'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('cp'); ?></div>
				</div>
				<?php endif; ?>

				<?php if($show_state == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('city'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('city'); ?></div>
				</div>
				<?php endif; ?>

				<?php if($show_pais == 1) : ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('pais'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('pais'); ?></div>
				</div>
				<?php endif; ?>
				
				<?php if($validation == 1) : ?>
				<div class="form-group pt-4">
	   		 		<div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?= $sitekey; ?>"></div>
				</div>
				<?php endif; ?>
				
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

			</div>
				
		</form>
	</div>
	
</div>
