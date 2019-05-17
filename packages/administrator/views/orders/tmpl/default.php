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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_botiga');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=orders.saveOrderAjax&tmpl=component';
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

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?= $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
       
       <?= JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
       
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
					<?php if (isset($this->items[0]->id)): ?>
					<th width="1%" class="nowrap center hidden-phone">
							<?= JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
		            <?php endif; ?>
					<th width="1%" class="nowrap center">
						<?= JHtml::_('grid.sort', 'COM_BOTIGA_ORDERS_HEADING_NOM', 'a.nom_empresa', $listDirn, $listOrder); ?>
					</th>		
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_DATA', 'a.data', $listDirn, $listOrder); ?>
					</th>   
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_SUBTOTAL', 'a.subtotal', $listDirn, $listOrder); ?>
					</th>  
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_SHIPPMENT', 'a.shipment', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_DISCOUNT', 'a.discount', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_IVA_PERCENT', 'a.iva_percent', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_IVA', 'a.iva_total', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_RE_PERCENT', 'a.re_percent', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_RE', 'a.re_total', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_TOTAL', 'a.total', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_PROCESSOR', 'a.processor', $listDirn, $listOrder); ?>
					</th> 
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_COUPON', 'a.idCoupon', $listDirn, $listOrder); ?>
					</th>    
					<th>
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_ORDERS_HEADING_STATUS', 'a.status', $listDirn, $listOrder); ?>
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
		            <td>
						<?php if ($canEdit) : ?>
						<a href="<?= JRoute::_('index.php?option=com_botiga&view=order&id='.(int) $item->id); ?>">
						<?= $item->id; ?></a>
						<?php else : ?>
						<?= $item->id; ?>
						<?php endif; ?>
					</td>
					<td>
						<a href="index.php?option=com_botiga&view=user&layout=edit&id=<?php $item->user_id; ?>"><?= $item->nom_empresa; ?></a>
					</td>
					<td>
						<?= date('d-m-Y H:i', strtotime($item->data)); ?>
					</td>
					<td>
						<?= $item->subtotal; ?>&euro;
					</td>
					<td>
						<?= $item->shipment; ?>&euro;
					</td>
					<td>
						<?= $item->discount; ?>&euro;
					</td>
					<td>
						<?= $item->iva_percent; ?>%
					</td>
					<td>
						<?= $item->iva_total; ?>&euro;
					</td>
					<td>
						<?= $item->re_percent; ?>%
					</td>
					<td>
						<?= $item->re_total; ?>&euro;
					</td>
					<td>
						<?= $item->total; ?>&euro;
					</td>
					<td>
						<span class="label label-info"><?= $item->processor; ?></span>
					</td>
					<td>
						<a href="index.php?option=com_botiga&view=coupon&layout=edit&id=<?php $item->idCoupon; ?>"><?= $item->idCoupon; ?></a>
					</td>					
					<td class="hidden-phone">
						<?php if($item->status == 1): ?><span class="label label-danger"><?= JText::_('COM_BOTIGA_STATUS_PENDING'); ?></span><?php endif; ?>
						<?php if($item->status == 2): ?><span class="label label-warning"><?= JText::_('COM_BOTIGA_STATUS_PENDING_PAYMENT'); ?></span><?php endif; ?>
						<?php if($item->status == 3): ?><span class="label label-success"><?= JText::_('COM_BOTIGA_STATUS_COMPLETED'); ?></span><?php endif; ?>
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
