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

class botigaViewCoupons extends JViewLegacy
{
    protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

    $this->addToolbar();

    $this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	*/
	protected function addToolBar()
	{
		$canDo = botigaHelper::getActions();
		JToolBarHelper::title(JText::_('Coupons'), 'joomla');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('coupon.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('coupon.edit', 'JTOOLBAR_EDIT');
      JToolBarHelper::divider();
      JToolBarHelper::custom('coupons.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
      JToolBarHelper::custom('coupons.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      JToolbarHelper::custom('coupons.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}
		if ($canDo->get('core.delete'))
		{
      JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'coupons.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin'))
		{
      JToolBarHelper::divider();
			JToolBarHelper::preferences('com_botiga');
		}
	}
}
?>
