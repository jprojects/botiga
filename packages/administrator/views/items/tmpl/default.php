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

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_botiga');
$saveOrder = $listOrder == 'i.ordering';
$model	   = $this->getModel();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=items.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'adminList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?= $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=items'); ?>" method="post" name="adminForm" id="adminForm">

	<div id="j-sidebar-container" class="span2">
		<?= $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">

		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?= JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>

		<table class="table table-striped" id="adminList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'i.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?= JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
                	<?php if (isset($this->items[0]->published)): ?>
					<th width="1%" class="nowrap center">
						<?= JHtml::_('grid.sort', 'JSTATUS', 'i.published', $listDirn, $listOrder); ?>
					</th>
               		<?php endif; ?>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_REF', 'i.ref', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_NAME', 'i.name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CHILD', 'i.child', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_PVP', 'i.pvp', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_BRAND', 'i.marca_name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CAT', 'i.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?= JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'i.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
		            <?php
		            if(isset($this->items[0])){
		                $colspan = count(get_object_vars($this->items[0]));
		            }
		            else{
		                $colspan = 10;
		            }
		        	?>
					<td colspan="<?= $colspan ?>">
						<?= $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'i.ordering');
                $canCreate	= $user->authorise('core.create',		'com_botiga');
                $canEdit	= $user->authorise('core.edit',			'com_botiga');
                $canCheckin	= $user->authorise('core.manage',		'com_botiga');
                $canChange	= $user->authorise('core.edit.state',	'com_botiga');
				?>
				<tr class="row<?= $i % 2; ?>">

				    <?php if (isset($this->items[0]->ordering)): ?>
					<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?= $disableClassName?>" title="<?= $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5" value="<?= $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
						<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					</td>
                	<?php endif; ?>
					<td class="center hidden-phone">
							<?= JHtml::_('grid.id', $i, $item->id); ?>
					</td>
		            <?php if (isset($this->items[0]->published)): ?>
					<td class="center">
							<?= JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange, 'cb'); ?>
					</td>
		            <?php endif; ?>
		            <td>
						<?= $item->ref; ?>
					</td>
					<td>
						<a href="index.php?option=com_botiga&task=item.edit&id=<?= $item->id; ?>"><?= $item->name; ?></a>
					</td>
					<td>
						<?= $item->child == 0 ? '<span class="icon-unpublish"></span>' : '<span class="icon-ok"></span>'; ?>
					</td>
					<td>
						<?= $item->pvp; ?>&euro;
					</td>
					<td>
						<a href="index.php?option=com_botiga&task=brand.edit&id=<?= $item->brand; ?>"><?= $item->bname; ?></a>
					</td>
					<td>
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
	<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?= $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?= $listDirn; ?>" />
		<?= JHtml::_('form.token'); ?>
	</div>
</form>
