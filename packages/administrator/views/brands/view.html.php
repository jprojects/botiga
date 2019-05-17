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

class botigaViewBrands extends JViewLegacy
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
 
		botigaHelper::addSubmenu('brands');

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
		JToolBarHelper::title(JText::_('COM_BOTIGA_MANAGER_BRANDS'), 'joomla');

		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('brand.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('brand.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::divider();
            JToolBarHelper::custom('brands.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('brands.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            JToolBarHelper::custom('brands.excel', 'checkin.png', 'checkin_f2.png', 'Excel', false);	
		}
		if ($canDo->get('core.delete')) 
		{   
            JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'brands.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) 
		{
            JToolBarHelper::divider();
			JToolBarHelper::preferences('com_botiga');
		}
		
		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_botiga&view=brands');
        
        $this->extra_sidebar = '';		
	}
}
?>
