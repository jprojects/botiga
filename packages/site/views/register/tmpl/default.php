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
?>

<style>#page-header { border: none; }</style>

<div> 

	<div class="page-header">
		<h3><?= JText::_('COM_BOTIGA_REGISTER_TITLE'); ?></h3>
		<hr>
		<p><?= JText::_('COM_BOTIGA_REGISTER_SUBTITLE'); ?></p>
	</div>

	<div class="col-md-12">

		<form name="register" id="register" action="<?= JRoute::_('index.php?option=com_botiga&task=register'); ?>" method="post" class="form-horizontal form-validate">
		
				<?php foreach($this->form->getFieldset('register') as $field): ?>
				<div class="control-group">
				<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $field->label; ?></label>
						<div class="col-sm-10"><?php echo $field->input ?></div>
				</div>
				</div>
				<?php endforeach; ?>
				
				<div class="checkbox text-right">
				  <label>
					<input type="checkbox" value="" class="tos">
					<?= JText::_('COM_BOTIGA_REGISTER_LOPD'); ?>
				  </label>
				</div>
				
				<div id="form-login-submit" class="control-group">
					<div class="controls text-right">
						<button disabled="disabled" type="submit" class="btn btn-primary validate submit"><?= JText::_('COM_BOTIGA_REGISTER'); ?></button>
					</div>
				</div>
				<input type="hidden" name="option" value="com_botiga" />
				<input type="hidden" name="task" value="register" />
				<?php echo JHtml::_('form.token');?>
				
		</form>
	</div>
	
</div>
