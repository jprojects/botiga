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
$model = $this->getModel('search');
$search = JFactory::getApplication()->input->get('filter_search');
?>

<div class="container margin50">
	
	<div class="col-md-1 hidden-xs">
		<img class="img-responsive" src="images/icons/icono_buscador.png" alt="iconos" />
	</div>
	<div class="col-xs-12 col-md-11">
		<?php if(count($this->items) > 0) : ?>
		<table class="table table-striped">
		<?php foreach($this->items as $item) : ?>
		<?php $link = JRoute::_('index.php?option=com_botiga&view=item&id='.$item->id.'&Itemid=135'); ?>
			<tr>
				<td><a href="<?= $link; ?>"><?= $item->ref; ?> <?= $item->name; ?></a></td>	
			</tr>
		<?php endforeach; ?>
		</table>
		<?php else : ?>
		<?php echo JText::sprintf('COM_BOTIGA_NO_SEARCH_ITEMS', $search); ?>
		<?php endif; ?>

		<?php if(count($this->items) > 0) : ?>
		<div id="system">
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
