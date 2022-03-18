<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_botiga
 * @author     aficat <kim@aficat.com>
 * @copyright  2021 aficat
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
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
			if (task == 'user.cancel') {
				Joomla.submitform(task, document.getElementById('item-form'));
			}
			else {
				
				if (task != 'user.cancel' && document.formvalidator.isValid('item-form')) {
					
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
	action="<?php echo JRoute::_('index.php?option=com_botiga&view=user&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_BOTIGA_USER_DETAILS')); ?>

	<div class="row">
		<div class="col-12">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php echo $field->renderField() ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>

	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'settings', Text::_('COM_BOTIGA_USER_SETTINGS')); ?>

	<div class="row">
		<div class="col-12">
			<?php foreach($this->form->getFieldset('settings') as $field): ?>
				<?php echo $field->renderField() ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
