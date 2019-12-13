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
$model = $this->getModel('search');
$search = JFactory::getApplication()->input->get('filter_search');
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="container margin50">

	<div class="col-sm-12 col-md-11">
		<?php if(count($this->items) > 0) : ?>
		<table class="table table-striped">
			<?php foreach($this->items as $item) : ?>
			<tr>
				<td><a href="<?= JURI::root(); ?>'media/com_botiga/docs/<?= $item->filename; ?>"><?= $item->name; ?></a></td>
				<td><?= $item->category; ?></td>
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
