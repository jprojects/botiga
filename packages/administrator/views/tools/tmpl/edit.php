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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

JHtml::_('behavior.keepalive');
$model = $this->getModel('tools');
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if(task == 'tools.cancel'){
		document.location.href = 'index.php?option=com_botiga';
	}
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_shop&task=tools.export'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
 
	<div class="row-fluid">
        <div class="span6 form-horizontal">
            <fieldset class="adminform">
			<legend><?php echo Text::_( 'Exportar csv' ); ?></legend>
			<p><small>Al exportar es crea un arxiu csv amb tots els productes de la base de dades amb la tarifa escollida, cal omplir preus i fer servir el mètode d'importació per afegir nous preus.<br>No cal que siguin tots els productes es poden esborrar els que calgui i importar només la quantitat desitjada.</small></p>
			<?php foreach($this->form->getFieldset('export') as $field): ?>
				<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input ?></div>
				</div>
			<?php endforeach; ?>
			<button type="submit" class="btn btn-primary">Exportar</button>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="tools.export" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<hr>

<form action="<?php echo JRoute::_('index.php?option=com_shop&task=tools.import'); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
 
	<div class="row-fluid">
        <div class="span6 form-horizontal">
            <fieldset class="adminform">
			<legend><?php echo Text::_( 'Importar csv' ); ?></legend>
			<p><small>Una vegada omplert l'arxiu resultant de l'expotació pot pujar aquí l'arxiu per afegir les noves tarifes.</small></p>
			<?php foreach($this->form->getFieldset('import') as $field): ?>
				<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input ?></div>
				</div>
			<?php endforeach; ?>
			<button type="submit" class="btn btn-primary">Importar</button>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="tools.import" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
