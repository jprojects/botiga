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

?>

<form
	action="<?= Route::_('index.php?option=com_botiga&view=discount&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate form-horizontal">

	
	<?= HTMLHelper::_('uitab.startTabSet', 'myTabs', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'details', Text::_('Detalls')); ?>

	<div class="row">
		<div class="col-12">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?= $field->renderField(); ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?= JHtml::_('form.token'); ?>

</form>
