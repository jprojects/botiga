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

class botigaViewOrder extends JViewLegacy
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

		botigaHelper::addSubmenu('orders');

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
		JToolBarHelper::title(JText::_('Order'), 'joomla');

		if ($canDo->get('core.delete'))
		{
      JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'order.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin'))
		{
      JToolBarHelper::divider();
			JToolBarHelper::preferences('com_botiga');
		}

		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_botiga&view=order');

        $this->extra_sidebar = '';
	}

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.idComanda' => JText::_('IdComanda'),
		'a.itemId' => JText::_('ItemId'),
		'a.price' => JText::_('Price'),
		);
	}
}
?>
