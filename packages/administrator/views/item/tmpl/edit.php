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

 use Joomla\CMS\HTML\HTMLHelper;
 use Joomla\CMS\Language\Text;
 use Joomla\CMS\Layout\LayoutHelper;
 use Joomla\CMS\Router\Route;

 HTMLHelper::_('behavior.formvalidator');
 HTMLHelper::_('behavior.keepalive');
?>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=item&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<div class="row-fluid">
    <div class="col-12 form-horizontal">
      <fieldset class="adminform">
	      <legend><?= JText::_( 'COM_LAUNDRY_PRODUCT_DETAILS' ); ?></legend>

				<?php foreach($this->form->getFieldset('details') as $field): ?>
					<div class="control-group">
					<div class="control-label"><?= $field->label; ?></div>
					<div class="controls"><?= $field->input ?></div>
					</div>
	    	<?php endforeach; ?>

      </fieldset>
    </div>

    <input type="hidden" name="task" value="">
  	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
