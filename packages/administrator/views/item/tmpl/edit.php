<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
 */

 defined('_JEXEC') or die;

 use \Joomla\CMS\HTML\HTMLHelper;
 use \Joomla\CMS\Factory;
 use \Joomla\CMS\Uri\Uri;
 use \Joomla\CMS\Router\Route;
 use \Joomla\CMS\Language\Text;

 HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
 HTMLHelper::_('behavior.formvalidator');
 HTMLHelper::_('behavior.keepalive');
?>

<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {

		Joomla.submitbutton = function (task) {
			if (task == 'item.cancel') {
				Joomla.submitform(task, document.getElementById('item-form'));
			}
			else {
				
				if (task != 'item.cancel' && document.formvalidator.isValid('item-form')) {
					
					Joomla.submitform(task, document.getElementById('item-form'));
				}
				else {
					alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				}
			}
		}
	});
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_botiga&view=item&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('Detalls')); ?>

	<div class="row">
		<div class="col-12">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php echo $field->renderField() ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>

	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'params', Text::_('Params')); ?>

	<div class="row">
		<div class="col-12">
			<?php foreach($this->form->getFieldset('params') as $field): ?>
				<?php echo $field->renderField() ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>

	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
