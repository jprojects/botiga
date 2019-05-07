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

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function	= JRequest::getCmd('function', 'jSelectProduct');
//$listOrder	= $this->escape($this->state->get('list.ordering'));
//$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_botiga&view=items&layout=modal&tmpl=component&function='.$function);?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<label for="filter_search">
				<?php echo JText::_('JSearch_Filter_Label'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php //echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>		
	</fieldset>
	<table class="adminlist">
                <tr>
                    <th width="5">
                        <?php echo JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>			
                    <th>
                        <?php echo JHtml::_('grid.sort',  'COM_BOTIGA_ITEMS_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
                    </th>
                </tr>		
                <tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
                                <td>
                                    <?php echo $item->id; ?>
                                </td>
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $this->escape($item->name); ?>
                                        </a>
				</td>				
			</tr>
			<?php endforeach; ?>
                        <tr>
                            <td colspan="4"><?= $this->pagination->getListFooter(); ?></td>
                        </tr>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
                <input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>		
