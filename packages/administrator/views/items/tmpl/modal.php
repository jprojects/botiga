<?php
/**
 * @version		1.0.0 com_botiga $
 * @package		botiga
 * @copyright   Copyright © 2011 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$app = Factory::getApplication();

HTMLHelper::_('behavior.core');

$function	 = $app->input->getCmd('function', 'jSelectProduct');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<div class="container-popup">

	<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=items&layout=modal&tmpl=component&function='.$function . '&' . Session::getFormToken() . '=1');?>" method="post" name="adminForm" id="adminForm">

	  <?= JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">
				<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
	  <table class="table table-sm">
			<thead>
				<tr>
					<th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_REF', 'a.ref', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CHILD', 'a.child', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_PVP', 'a.pvp', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_BRAND', 'a.marca_name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= HTMLHelper::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CAT', 'a.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?= HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.ordering');
        $canCreate	= $user->authorise('core.create',		'com_botiga');
        $canEdit	= $user->authorise('core.edit',			'com_botiga');
        $canCheckin	= $user->authorise('core.manage',		'com_botiga');
        $canChange	= $user->authorise('core.edit.state',	'com_botiga');
				?>
				<tr class="row<?= $i % 2; ?>">
					<td class="small d-none d-md-table-cell">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'items.', $canChange, 'cb'); ?>
					</td>
		      <td class="small d-none d-md-table-cell">
						<?= $item->ref; ?>
					</td>
					<td class="small d-none d-md-table-cell">
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');"><?= $item->name; ?></a>
					</td>
					<td class="small d-none d-md-table-cell">
						<?= $item->child == 0 ? '<span class="icon-unpublish"></span>' : '<span class="icon-ok"></span>'; ?>
					</td>
					<td class="small d-none d-md-table-cell">
						<?= $item->pvp; ?>&euro;
					</td>
					<td class="small d-none d-md-table-cell">
						<?= $item->bname; ?>
					</td>
					<td class="small d-none d-md-table-cell">
						<?= $item->ctitle; ?>
					</td>
					<td class="center nowrap">
						<?php if ($item->language == '*'):?>
							<?= JText::alt('JALL', 'language'); ?>
						<?php else:?>
							<img src="<?= JURI::root(); ?>media/mod_languages/images/<?= str_replace('-', '_', strtolower($item->language)); ?>.gif" alt="<?= $item->language; ?>">
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
		</table>

		<?php // load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>

	<?php endif; ?>

			<input type="hidden" name="task" value="" />
			<?= HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
