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

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="col-md-4 mx-auto  mt-5 mb-5">

	<div class="text-center">
		<h1 class="text-dark estil09"><?= JText::_('COM_BOTIGA_REGISTER_TITLE'); ?></h1>
	</div>

	<div>

		<form name="register" id="register" action="index.php?option=com_botiga&task=register.register" method="post" class="form-horizontal form-validate">
			<?php if($validation == 1) : ?><input type="hidden" name="recaptcha_response" id="recaptchaResponse"><?php endif; ?>
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

				<div class="checkbox estil01 pt-4">
				  <label>
					<input type="checkbox" value="" id="tos" class="required" required="required">
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
						<button disabled="disabled" type="submit" id="submit" class="btn btn-primary btn-block validate submit estil03"><?= JText::_('COM_BOTIGA_REGISTER'); ?></button>
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
<script>
(function() {
	'use strict'

	//count seconds
	var counter = 0;
	window.setInterval(function(){
	counter++;
	},1000);

	var tos = document.getElementById('tos');
	tos.addEventListener("click",function(e){
		if(tos.checked) {
			document.getElementById('submit').removeAttribute("disabled");
			grecaptcha.ready(function() {
				grecaptcha.execute('<?= $sitekey; ?>', {action: 'register'})
				.then(function(token) {
				var recaptchaResponse = document.getElementById('recaptchaResponse');
				recaptchaResponse.value = token;
				});
			});
		} else {
			document.getElementById('submit').setAttribute("disabled", "true");
		}
	});

	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.querySelectorAll('.needs-validation')

	// Loop over them and prevent submission
	Array.prototype.slice.call(forms)
	.forEach(function (form) {
		form.addEventListener('submit', function (event) {
		if (!form.checkValidity()) {
			event.preventDefault()
			event.stopPropagation()
		}

		form.classList.add('was-validated')
		}, false)
	})
})()
</script>