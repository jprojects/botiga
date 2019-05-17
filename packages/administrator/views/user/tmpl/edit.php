<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail administracion@joomlanetprojects.com
 * @website		http://www.joomlanetprojects.com
 *
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$model = $this->getModel('user');
?>

<script type="text/javascript">
js = jQuery.noConflict();
js(document).ready(function(){

});

Joomla.submitbutton = function(task)
{
if(task == 'user.cancel'){
    Joomla.submitform(task, document.getElementById('item-form'));
}
else{
    
    if (task != 'user.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
        
        Joomla.submitform(task, document.getElementById('item-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
    }
}
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=user&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

		<div class="row-fluid">
		    <div class="span6 form-horizontal">
		        <fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_BOTIGA_USER_DETAILS' ); ?></legend>
				
					<?php foreach($this->form->getFieldset('details') as $field): ?>
							<?php echo $field->renderField() ?>
					<?php endforeach; ?>

			</fieldset>
			</div>
		    <div class="span6 form-horizontal">
		        <fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_BOTIGA_USER_SETTINGS' ); ?></legend>
				
					<?php foreach($this->form->getFieldset('settings') as $field): ?>
							<?php echo $field->renderField() ?>
					<?php endforeach; ?>

				</fieldset>
			</div>
	
		<input type="hidden" name="task" value="user.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
</form>
