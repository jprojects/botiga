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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.multiselect');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder && !empty($this->items))
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=brands.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=brands'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">

				<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
				?>

				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>

				<table class="table table-striped" id="adminList">
					<thead>
						<tr>
							<td style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_BRANDS_HEADING_BRAND', 'name', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:10%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort',  'CodFte', 'factusol_codfte', $listDirn, $listOrder); ?>
							</th>
							<?php if (Multilanguage::isEnabled()) : ?>
							<th scope="col" style="width:10%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
							</th>
							<?php endif; ?>
							<th scope="col" style="width:10%" class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
						<?php foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
		        $canCreate	= $user->authorise('core.create',		'com_botiga');
		        $canEdit	  = $user->authorise('core.edit',			'com_botiga');
		        $canCheckin	= $user->authorise('core.manage',		'com_botiga');
		        $canChange	= $user->authorise('core.edit.state',	'com_botiga');
						?>
						<tr class="row<?php echo $i % 2; ?>" data-dragable-group="<?php echo $item->catid; ?>">
							<td class="text-center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="text-center d-none d-md-table-cell">
								<?php
								$iconClass = '';

								if (!$canChange)
								{
									$iconClass = ' inactive';
								}
								elseif (!$saveOrder)
								{
									$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler <?php echo $iconClass ?>">
									<span class="fa fa-ellipsis-v" aria-hidden="true"></span>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5"
										value="<?php echo $item->ordering; ?>" class="width-20 text-area-order">
								<?php endif; ?>
							</td>
				      <?php if (isset($this->items[0]->published)): ?>
							<td class="center">
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'brands.', $canChange, 'cb'); ?>
							</td>
				      <?php endif; ?>
				      <td class="small d-none d-md-table-cell">
								<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_botiga&task=brand.edit&id='.(int) $item->id); ?>">
								<?php echo $item->name; ?></a>
								<?php else : ?>
								<?php echo $item->name; ?>
								<?php endif; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?php echo $item->factusol_codfte; ?>
							</td>
							<td>
								<?php if ($item->language == '*'):?>
									<?= JText::alt('JALL', 'language'); ?>
								<?php else:?>
									<img src="<?= JURI::root(); ?>media/mod_languages/images/<?= str_replace('-', '_', strtolower($item->language)); ?>.gif" alt="<?= $item->language; ?>">
								<?php endif;?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?php echo (int) $item->id; ?>
							</td>

						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

		<?php // Load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>

		<?php endif; ?>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
