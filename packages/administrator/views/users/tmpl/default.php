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

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_botiga');
$saveOrder	= $listOrder == 'a.ordering';
$model		= $this->getModel();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_botiga&task=users.saveOrderAjax&tmpl=component';
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

<form action="<?= JRoute::_('index.php?option=com_botiga&view=users'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?= $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

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
                	<?php if (isset($this->items[0]->published)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
               		<?php endif; ?>
					<th width="15%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_NAME', 'u.nombre', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_TYPE', 'u.type', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_NOM_EMPRESA', 'u.nom_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_MAIL', 'u.mail_empresa', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_PHONE', 'u.telefon', $listDirn, $listOrder); ?>
					</th>
					<th width="12%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_CITY', 'u.poblacio', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_COUNTRY', 'u.pais', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'CP', 'u.cp', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'CIF', 'u.cif', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_GROUP', 'u.usergroup', $listDirn, $listOrder); ?>
					</th>
					<th width="13%">
						<?= JHtml::_('grid.sort',  'COM_BOTIGA_USERS_HEADING_VALIDATE', 'u.validate', $listDirn, $listOrder); ?>
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
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'users.', $canChange, 'cb'); ?>
					</td>
		            <?php endif; ?>
		            <td>
						<a href="index.php?option=com_botiga&task=user.edit&id=<?= $item->id; ?>"><?= $item->nombre; ?></a>
					</td>
					<td>
						<?= $item->type == 0 ? '<span class="label label-info">'.JText::_('COM_BOTIGA_TYPE_CUSTOMER').'</span>' : '<span class="label label-warning">'.JText::_('COM_BOTIGA_TYPE_COMPANY').'</span>'; ?>
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
						<?= $item->country_name; ?>
					</td>
					<td>
						<?= $item->cp; ?>
					</td>
					<td>
						<?= $item->cif; ?>
					</td>
					<td align="center">
						<?= $item->title; ?>
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
