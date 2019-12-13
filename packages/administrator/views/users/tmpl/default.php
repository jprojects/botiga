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
	$saveOrderingUrl = 'index.php?option=com_botiga&task=users.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?= JRoute::_('index.php?option=com_botiga&view=users'); ?>" method="post" name="adminForm" id="adminForm">
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
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_NAME', 'u.nombre', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_TYPE', 'u.type', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_NOM_EMPRESA', 'u.nom_empresa', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_MAIL', 'u.mail_empresa', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_PHONE', 'u.telefon', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_CITY', 'u.poblacio', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_COUNTRY', 'u.pais', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'CP', 'u.cp', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'CIF', 'u.cif', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_GROUP', 'u.usergroup', $listDirn, $listOrder); ?>
							</th>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BOTIGA_USERS_HEADING_VALIDATE', 'u.validate', $listDirn, $listOrder); ?>
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
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'users.', $canChange, 'cb'); ?>
							</td>
				      <td class="small d-none d-md-table-cell">
								<a href="index.php?option=com_botiga&task=user.edit&id=<?= $item->id; ?>"><?= $item->nombre; ?></a>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->type == 0 ? '<span class="label label-info">'.JText::_('COM_BOTIGA_TYPE_CUSTOMER').'</span>' : '<span class="label label-warning">'.JText::_('COM_BOTIGA_TYPE_COMPANY').'</span>'; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->nom_empresa; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<a target="_blank" href="mailto:<?= $item->mail_empresa; ?>"><?= $item->mail_empresa; ?></a>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->telefon; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?php
									echo $item->poblacio;
									if ($item->provincia!='') echo ' ('.$item->provincia.')';
								?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->country_name; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->cp; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->cif; ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?= $item->title; ?>
							</td>
							<td class="small d-none d-md-table-cell">
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

				<?php // Load the pagination. ?>
				<?php echo $this->pagination->getListFooter(); ?>

				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>

	</div>
</form>
