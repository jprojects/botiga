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
$link = '';
$f  = array();
$f[1] = botigaHelper::getParameter('show_field_empresa', 1);
$f[2] = botigaHelper::getParameter('show_field_cif', 1);
$f[3] = botigaHelper::getParameter('show_field_nom', 1);
$f[4] = botigaHelper::getParameter('show_field_cargo', 1);
$f[5] = botigaHelper::getParameter('show_field_phone', 1);
$f[6] = botigaHelper::getParameter('show_field_address', 1);
$f[7] = botigaHelper::getParameter('show_field_cp', 1);
$f[8] = botigaHelper::getParameter('show_field_state', 1);
$f[9] = botigaHelper::getParameter('show_field_pais', 1);
?>

<div class="col-md-4 mx-auto  mt-5 mb-5"> 

	<div class="text-center">
		<h1 class="text-dark estil09"><?= JText::_('COM_BOTIGA_REGISTER_TITLE'); ?></h1>
	</div>

	<div>

		<form name="register" id="register" action="<?= JRoute::_('index.php?option=com_botiga&task=register'); ?>" method="post" class="form-horizontal form-validate">
		
				<?php 
				$i = 1;
				foreach($this->form->getFieldset('register') as $field): ?>
				<?php if($f[$i] == 1): ?> 
				<div class="control-group">
				<div class="form-group">
						<label class="control-label estil03"><?php echo $field->label; ?></label>
						<div class="estil02"><?php echo $field->input ?></div>
				</div>
				</div>
				<?php endif; ?>
				<?php 
				$i++;
				endforeach; ?>
				
				<div class="checkbox estil01">
				  <label>
					<input type="checkbox" value="" class="tos">
					<?= JText::sprintf('COM_BOTIGA_REGISTER_LOPD', ''); ?>
				  </label>
				</div>
				
				<div id="form-login-submit" class="control-group">
					<div class="controls">
						<button disabled="disabled" type="submit" class="btn btn-primary btn-block validate submit estil03"><?= JText::_('COM_BOTIGA_REGISTER'); ?></button>
					</div>
				</div>
				<input type="hidden" name="option" value="com_botiga" />
				<input type="hidden" name="task" value="register" />
				<?php echo JHtml::_('form.token');?>
				
				<div class="estil01 mt-3"><?= JText::_('COM_BOTIGA_REGISTER_SUBTITLE'); ?></div>
				
		</form>
	</div>
	
</div>
