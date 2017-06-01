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
?>

<style>
@media screen and (min-width: 1024px) {
	.mainbody { min-height: 750px; }
}
</style>

<div class="container">
	<div id="page-header">
		<h1><?= JText::_('COM_BOTIGA_HISTORY_TITLE'); ?></h1>
	</div>
	<?php if(count($this->items)) : ?>
	<table class="table table-striped">
	<?php foreach($this->items as $item) : ?>
	<?php $item->status == 2 ? $status = JText::_('COM_BOTIGA_HISTORY_STATUS_FINISHED') : $status = JText::_('COM_BOTIGA_HISTORY_STATUS_PENDING'); ?>
	<tr>
		<td><?= JText::_('COM_BOTIGA_HISTORY_NUM'); ?> <?= $item->id; ?></td>
		<td><?= $item->data; ?></td>
		<td><?= $status; ?></td>
		<td align="right"><div class="blue bold"><?= $item->suma; ?>&euro;</div></td>
		<td align="right"><a href="index.php?option=com_botiga&task=genPdf&id=<?= $item->id; ?>" target="_blank"><i class="fa fa-file-pdf-o"></i></a></td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php else : ?>
	<div><?= JText::_('COM_BOTIGA_HISTORY_NO_ORDERS'); ?></div>
	<?php endif; ?>
</div>

