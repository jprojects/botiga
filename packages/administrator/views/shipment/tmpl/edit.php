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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$model = $this->getModel('shipment');
?>

<script type="text/javascript">
js = jQuery.noConflict();
js(document).ready(function(){

});

Joomla.submitbutton = function(task)
{
if(task == 'shipment.cancel'){
    Joomla.submitform(task, document.getElementById('item-form'));
}
else{

    if (task != 'shipment.cancel' && document.formvalidator.isValid(document.id('item-form'))) {

        Joomla.submitform(task, document.getElementById('item-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
    }
}
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=shipment&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<div class="row-fluid">
        <div class="span6 form-horizontal">
            <fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_BOTIGA_SHIPMENT_DETAILS' ); ?></legend>
			<?php foreach($this->form->getFieldset('details') as $field): ?>
						<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input ?></div>
						</div>
	    		<?php endforeach; ?>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="shipment.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
