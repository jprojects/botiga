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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$model = $this->getModel('doc');
?>

<script type="text/javascript">
js = jQuery.noConflict();
js(document).ready(function(){

});

Joomla.submitbutton = function(task)
{
if(task == 'doc.cancel'){
    Joomla.submitform(task, document.getElementById('item-form'));
}
else{
    
    if (task != 'doc.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
        
        Joomla.submitform(task, document.getElementById('item-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
    }
}
}
</script>

<div class="form-horizontal">

	<div class="row-fluid">
		<form
		action="<?php echo JRoute::_('index.php?option=com_botiga&view=doc&layout=edit&id='.(int) $this->item->id); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="span8 form-horizontal">
			<fieldset class="adminform">

				<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php echo $field->renderField() ?>
	    		<?php endforeach; ?>		

			</fieldset>
		</div>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
		</form>
		
		<form
		action="<?php echo JRoute::_('index.php?option=com_botiga&task=doc.upload&id=' . (int) $this->item->id); ?>"
		method="post" enctype="multipart/form-data" name="adminForm" id="documento-form" class="form-validate">
		<div class="span4 form-horizontal">
		<label><?= JText::_('COM_BOTIGA_UPLOAD_LBL'); ?></label>
		<input type="hidden" name="jform[id]" value="<?= $this->item->id; ?>" />
		<?php echo $this->form->renderField('subida'); ?>
		<button type="submit" class="btn btn-primary btn-block"><?= JText::_('JSUBMIT'); ?></button>
		</div>
		<input type="hidden" name="task" value="doc.upload"/>
		<?php echo JHtml::_('form.token'); ?>
		</form>
		
	</div>	

</div>
