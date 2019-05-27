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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_botiga');
$saveOrder	= $listOrder == 'a.ordering';
$model		= $this->getModel();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=items.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'adminList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$function	= JRequest::getCmd('function', 'jSelectProduct');
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

<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=items&layout=modal&tmpl=component&function='.$function);?>" method="post" name="adminForm" id="adminForm">

	<div>

    	<?= JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    	
    	<table class="table table-striped" class="adminList">
			<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
               		<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
                	<?php if (isset($this->items[0]->published)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
               		<?php endif; ?>		
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_REF', 'a.ref', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CHILD', 'a.child', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_PVP', 'a.pvp', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_BRAND', 'a.marca_name', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_CAT', 'a.catid', $listDirn, $listOrder); ?>
					</th>         
					<th width="5%">
						<?= JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
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
				$ordering   = ($listOrder == 'a.ordering');
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
							<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
								<i class="icon-menu"></i>
							</span>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
								<i class="icon-menu"></i>
							</span>
						<?php endif; ?>
					</td>
		            <?php endif; ?>
					<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
		            <?php if (isset($this->items[0]->published)): ?>
					<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange, 'cb'); ?>
					</td>
		            <?php endif; ?>
		            <td>
						<?= $item->ref; ?>
					</td>
					<td>
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');"><?= $item->name; ?></a>
					</td>
					<td>
						<?= $item->child == 0 ? '<span class="icon-unpublish"></span>' : '<span class="icon-ok"></span>'; ?>
					</td> 
					<td>
						<?= $item->pvp; ?>&euro;
					</td>
					<td>
						<?= $item->bname; ?>
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

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?= $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?= $listDirn; ?>" />
		<?= JHtml::_('form.token'); ?>
	</div>
</form>	
