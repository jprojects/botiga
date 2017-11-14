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
$model = $this->getModel('item');
?>

<script type="text/javascript">
js = jQuery.noConflict();
js(document).ready(function(){

});

Joomla.submitbutton = function(task)
{
if(task == 'item.cancel'){
    Joomla.submitform(task, document.getElementById('item-form'));
}
else{
    
    if (task != 'item.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
        
        Joomla.submitform(task, document.getElementById('item-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
    }
}
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=item&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
 
 	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
 	
 	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_BOTIGA_ITEM_TAB_DETAILS')); ?>
 	
	<div class="row-fluid">
        <div class="span6 form-horizontal">
            <fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('details') as $field): ?>
						<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input ?></div>
						</div>
	    		<?php endforeach; ?>

		</fieldset>
	</div>
	
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'metas', JText::_('COM_BOTIGA_ITEM_TAB_METAS')); ?>
	
	<div class="row-fluid">
        <div class="span6 form-horizontal">
            <fieldset class="adminform">
			
				<?php foreach($this->form->getFieldset('metas') as $field): ?>
						<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input ?></div>
						</div>
	    		<?php endforeach; ?>

		</fieldset>
	</div>
	
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="item.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
