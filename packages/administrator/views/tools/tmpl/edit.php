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
<style>
fieldset {
	padding: 20px;
	border: 1px solid #ccc;
	margin-bottom:20px;
}
</style>

<fieldset name="prices">
	<legend>Exporta/Importa precios</legend>
	<form action="<?php echo JRoute::_('index.php?option=com_botiga&task=tools.export'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	
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

	<form action="<?php echo JRoute::_('index.php?option=com_botiga&task=tools.import'); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	
		<div class="row-fluid">
			<div class="span6 form-horizontal">
				<fieldset class="adminform">
				<legend><?php echo Text::_( 'Importar csv' ); ?></legend>
				<p><small>Una vegada omplert l'arxiu resultant de l'expotació pot pujar aquí l'arxiu per afegir les noves tarifes.</small></p>
				<input type="file" name="jform[csv]" id="jform_csv">
				<button type="submit" class="btn btn-primary">Importar</button>
				</fieldset>
			</div>
			<input type="hidden" name="task" value="tools.import" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</fieldset>
<fieldset name="prices">
	<legend>Importa base de datos J3</legend>
	<form action="<?php echo JRoute::_('index.php?option=com_botiga&task=tools.importFromJ3'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">	
		<div class="control-group">
			<div class="control-label">Host</div>
			<div class="controls"><input type="text" class="form-control" name="jform[host]" value="localhost"></div>
		</div>
		<div class="control-group">
			<div class="control-label">User</div>
			<div class="controls"><input type="text" class="form-control" name="jform[user]" value=""></div>
		</div>
		<div class="control-group">
			<div class="control-label">Password</div>
			<div class="controls"><input type="text" class="form-control" name="jform[password]" value=""></div>
		</div>
		<div class="control-group">
			<div class="control-label">Database</div>
			<div class="controls"><input type="text" class="form-control" name="jform[database]" value=""></div>
		</div>
		<div class="control-group">
			<div class="control-label">Prefix</div>
			<div class="controls"><input type="text" class="form-control" name="jform[prefix]" value="afi_"></div>
		</div>
		<button type="submit" class="btn btn-primary">Importar</button>
	</form>
</fieldset>