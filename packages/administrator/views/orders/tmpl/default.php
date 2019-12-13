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
	$saveOrderingUrl = 'index.php?option=com_botiga&task=orders.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
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

				<table class="table table-striped" class="adminList">
					<thead>
						<tr>
							<td style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOTIGA_ORDERS_HEADING_NOM', 'u.username', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_DATA', 'a.data', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_SUBTOTAL', 'a.subtotal', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_SHIPPMENT', 'a.shipment', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_DISCOUNT', 'a.discount', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_IVA_PERCENT', 'a.iva_percent', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_IVA', 'a.iva_total', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_RE_PERCENT', 'a.re_percent', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_RE', 'a.re_total', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_TOTAL', 'a.total', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_PROCESSOR', 'a.processor', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_COUPON', 'a.idCoupon', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_ORDERS_HEADING_STATUS', 'a.status', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								#
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
							<td class="center">
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'orders.', $canChange, 'cb'); ?>
							</td>
				      <td class="small d-none d-md-table-cell">
								<?php if ($canEdit) : ?>
								<a href="<?= JRoute::_('index.php?option=com_botiga&view=order&id='.(int) $item->id); ?>">
								<?= $item->id; ?></a>
								<?php else : ?>
								<?= $item->id; ?>
								<?php endif; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<a href="index.php?option=com_botiga&view=user&layout=edit&id=<?php $item->userid; ?>"><?= $item->username.' ('.$item->email.')'; ?></a>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= date('d-m-Y H:i', strtotime($item->data)); ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->subtotal; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->shipment; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->discount; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->iva_percent; ?>%
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->iva_total; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->re_percent; ?>%
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->re_total; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->total; ?>&euro;
							</td>
							<td class="small d-none d-md-table-cell">
								<span class="label label-info"><?= $item->processor; ?></span>
							</td>
							<td class="small d-none d-md-table-cell">
								<a href="index.php?option=com_botiga&view=coupon&layout=edit&id=<?= $item->idCoupon; ?>"><?= $item->idCoupon; ?></a>
							</td>
							<td class="small d-none d-md-table-cell">
								<?php if($item->status == 1): ?><span class="label label-danger"><?= JText::_('COM_BOTIGA_STATUS_PENDING'); ?></span><?php endif; ?>
								<?php if($item->status == 2 && $item->processor != 'Transferencia'): ?><span class="label label-warning"><?= JText::_('COM_BOTIGA_STATUS_PENDING_PAYMENT'); ?></span><?php endif; ?>
								<?php if($item->status == 2 && $item->processor == 'Transferencia'): ?>
								<a href="index.php?option=com_botiga&task=order.changeStatus&id=<?= $item->id; ?>"><span class="label label-important"><?= JText::_('COM_BOTIGA_STATUS_PAY_RECEIVED'); ?></span></a>
								<?php endif; ?>
								<?php if($item->status == 3): ?><span class="label label-success"><?= JText::_('COM_BOTIGA_STATUS_COMPLETED'); ?></span><?php endif; ?>
								<?php if($item->status == 4): ?><span class="label label-important"><?= JText::_('COM_BOTIGA_STATUS_PENDING_50_PERCENT'); ?></span><?php endif; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<a href="<?= JURI::root(); ?>index.php?option=com_botiga&task=genPdf&id=<?= $item->id; ?>"><span class="icon-file-2"> </span></a>
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
