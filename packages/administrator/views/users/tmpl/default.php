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
		if (order != '<?php echo $listOrder; ?>') {
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

<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=users'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

    	<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="jQuery('#filter_search').val('');this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>        
		<div class="clearfix"> </div>
		
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
		        	<?php if (isset($this->items[0]->state)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
		       		<?php endif; ?>
					<th width="15%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_NAME', 'nom_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',  'Cargo', 'cargo', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_LOGIN_NAME', 'mail_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_PHONE', 'telefon', $listDirn, $listOrder); ?>
					</th>
					<th width="12%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_CITY', 'poblacio', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_COUNTRY', 'pais', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?php echo JHtml::_('grid.sort',  'CP', 'cp', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?php echo JHtml::_('grid.sort',  'CIF', 'cif', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?php echo JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_GROUP', 'title', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?php echo JHtml::_('grid.sort',  'Contacto', 'contacto', $listDirn, $listOrder); ?>
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
					<td colspan="<?php echo $colspan ?>">
						<?php echo $this->pagination->getListFooter(); ?>
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
				<tr class="row<?php echo $i % 2; ?>">
                    
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
		            <?php if (isset($this->items[0]->state)): ?>
					<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange, 'cb'); ?>
					</td>
		            <?php endif; ?>
		            <td>
						<a href="index.php?option=com_botiga&task=user.edit&id=<?= $item->id; ?>"><?= $item->nom_empresa; ?></a>
					</td>
					<td>
						<?php echo $item->cargo; ?>
					</td> 
					<td>
						<a target="_blank" href="mailto:<?= $item->mail_empresa; ?>"><?= $item->mail_empresa; ?></a>
					</td>
					<td>
						<?php echo $item->telefon; ?>
					</td>
					<td>
						<?php 
							echo $item->poblacio; 
							if ($item->provincia!='') echo ' ('.$item->provincia.')';
						?>
					</td>   
					<td>
						<?php echo $item->pais; ?>
					</td>
					<td>
						<?php echo $item->cp; ?>
					</td>
					<td>
						<?php echo $item->cif; ?>
					</td>	
					<td align="center">

					</td>
					<td>
						<?php echo $item->contacto; ?>
					</td>	              		
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
