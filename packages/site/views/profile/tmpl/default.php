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
$user = JFactory::getUser();
?>

<style>#page-header { border: none; }</style>

<div> 

	<div class="page-header">
		<h3><?= JText::_('MIS DATOS'); ?></h3>
		<hr>
		<p><?= JText::_('COM_BOTIGA_REGISTER_SUBTITLE'); ?></p>
	</div>

	<div class="col-md-12">

		<form name="register" id="register" action="<?= JRoute::_('index.php?option=com_botiga&view=profile&task=profile.profile'); ?>" method="post" class="form-horizontal form-validate">
				<input type="hidden" name="jform[id]" value="<?= $user->id; ?>" />
				<?php foreach($this->form->getFieldset('profile') as $field): ?>
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
						<button disabled="disabled" type="submit" class="btn btn-primary validate submit"><?= JText::_('JSUBMIT'); ?></button>
					</div>
				</div>
				<input type="hidden" name="option" value="com_botiga" />
				<input type="hidden" name="task" value="profile.profile" />
				<?php echo JHtml::_('form.token');?>
				
		</form>
	</div>
	
</div>
