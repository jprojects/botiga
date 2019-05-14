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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_laundry');
$saveOrder	= $listOrder == 'a.ordering';
$model		= $this->getModel();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=users.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'adminList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
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

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=users'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?= $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

    	<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?= JText::_('JSEARCH_FILTER');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?= JText::_('JSEARCH_FILTER'); ?>" value="<?= $this->escape($this->state->get('filter.search')); ?>" title="<?= JText::_('JSEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?= JText::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" type="button" title="<?= JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('#filter_search').val('');this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?= JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?= $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?= JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?= JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?= JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?= JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?= JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?= JText::_('JGLOBAL_SORT_BY');?></option>
					<?= JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>        
		<div class="clearfix"> </div>
		
		<table class="table table-striped" class="adminList">
			<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?= JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
		       		<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?= JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
		        	<?php if (isset($this->items[0]->state)): ?>
					<th width="1%" class="nowrap center">
						<?= JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
		       		<?php endif; ?>
					<th width="15%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_NAME', 'nombre', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_TYPE', 'type', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_NOM_EMPRESA', 'nom_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_MAIL', 'mail_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_PHONE', 'telefon', $listDirn, $listOrder); ?>
					</th>
					<th width="12%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_CITY', 'poblacio', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_COUNTRY', 'pais', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'CP', 'cp', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'CIF', 'cif', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_GROUP', 'title', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_VALIDATE', '', $listDirn, $listOrder); ?>
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
							<span class="sortable-handler hasTooltip <?= $disableClassName?>" title="<?= $disabledLabel?>">
								<i class="icon-menu"></i>
							</span>
							<input type="text" style="display:none" name="order[]" size="5" value="<?= $item->ordering;?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
								<i class="icon-menu"></i>
							</span>
						<?php endif; ?>
					</td>
		            <?php endif; ?>
					<td class="center hidden-phone">
							<?= JHtml::_('grid.id', $i, $item->id); ?>
					</td>
		            <?php if (isset($this->items[0]->state)): ?>
					<td class="center">
							<?= JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange, 'cb'); ?>
					</td>
		            <?php endif; ?>
		            <td>
						<a href="index.php?option=com_botiga&task=user.edit&id=<?= $item->id; ?>"><?= $item->nombre; ?></a>
					</td>
					<td>
						<?= $item->type == 0 ? 'COM_BOTIGA_TYPE_CUSTOMER' : 'COM_BOTIGA_TYPE_COMPANY'; ?>
					</td> 
					<td>
						<?= $item->nom_empresa; ?>
					</td>
					<td>
						<a target="_blank" href="mailto:<?= $item->mail_empresa; ?>"><?= $item->mail_empresa; ?></a>
					</td>
					<td>
						<?= $item->telefon; ?>
					</td>
					<td>
						<?php 
							echo $item->poblacio; 
							if ($item->provincia!='') echo ' ('.$item->provincia.')';
						?>
					</td>   
					<td>
						<?= $item->pais; ?>
					</td>
					<td>
						<?= $item->cp; ?>
					</td>
					<td>
						<?= $item->cif; ?>
					</td>	
					<td align="center">
						<?= $item->usergroup; ?>
					</td>
					<td>
						<?php if($item->validate == 0) : ?>
						<a class="btn btn-danger" href="index.php?option=com_botiga&task=users.validate&userid=<?= $item->userid; ?>"><?= JText::_('COM_BOTIGA_VALIDATE'); ?></a>
						<?php else : ?>
						<span class="label label-success"><?= JText::_('COM_BOTIGA_VALIDATED'); ?></span>
						<?php endif; ?>
					</td>	              		
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?= $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?= $listDirn; ?>" />
		<?= JHtml::_('form.token'); ?>
	</div>
</form>
