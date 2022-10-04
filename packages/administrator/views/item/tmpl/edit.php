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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>

<form
	action="<?= JRoute::_('index.php?option=com_botiga&view=item&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate form-horizontal">

	<div class="main-card">
	<?= HTMLHelper::_('uitab.startTabSet', 'myTabs', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'details', Text::_('Detalls')); ?>

	<fieldset id="fieldset-details" class="options-form">
		<legend><?php echo Text::_('Details'); ?></legend>
		<div>
			<?php echo $this->form->renderFieldset('details'); ?>
		</div>
	</fieldset>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'prices', Text::_('Prices')); ?>

	<fieldset id="fieldset-prices" class="options-form">
		<legend><?php echo Text::_('Prices'); ?></legend>
		<div>
			<?php echo $this->form->renderFieldset('prices'); ?>
		</div>
	</fieldset>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'images', Text::_('Images')); ?>

	<fieldset id="fieldset-images" class="options-form">
		<legend><?php echo Text::_('Images'); ?></legend>
		<div>
			<?php echo $this->form->renderFieldset('images'); ?>
		</div>
	</fieldset>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'related', Text::_('Related products')); ?>

	<fieldset id="fieldset-related" class="options-form">
		<legend><?php echo Text::_('Related products'); ?></legend>
		<div>
			<?php echo $this->form->renderFieldset('related_products'); ?>
		</div>
	</fieldset>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.addTab', 'myTabs', 'params', Text::_('Params')); ?>

	<fieldset id="fieldset-params" class="options-form">
		<legend><?php echo Text::_('Params'); ?></legend>
		<div>
			<?php echo $this->form->renderFieldset('params'); ?>
		</div>
	</fieldset>

	<?= HTMLHelper::_('uitab.endTab'); ?>
	<?= HTMLHelper::_('uitab.endTabSet'); ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<?= JHtml::_('form.token'); ?>

</form>
