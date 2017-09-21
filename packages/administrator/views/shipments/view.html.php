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

class botigaViewShipments extends JViewLegacy
{
    protected $items;
	protected $pagination;
	protected $state;
        
	function display($tpl = null) 
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
 
		botigaHelper::addSubmenu('shipments');

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
		JToolBarHelper::title(JText::_('COM_BOTIGA_MANAGER_SHIPMENTS'), 'joomla');

		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('shipment.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('shipment.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::divider();
            JToolBarHelper::custom('shipments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('shipments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
		if ($canDo->get('core.delete')) 
		{   
            JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'shipments.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) 
		{
            JToolBarHelper::divider();
			JToolBarHelper::preferences('com_botiga');
		}
		
		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_botiga&view=shipments');
        
        $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);		
	}
	
	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.published' => JText::_('JSTATUS'),
		'a.name' => JText::_('COM_BOTIGA_SHIPMENT_NAME'),
		);
	}
}
?>