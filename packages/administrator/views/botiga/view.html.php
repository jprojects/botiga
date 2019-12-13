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

class botigaViewBotiga extends JViewLegacy
{
  protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null)
	{
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
		JToolBarHelper::title(JText::_('COM_BOTIGA_MENU_NAME'), 'joomla');

		if ($canDo->get('core.admin'))
		{
      JToolBarHelper::divider();
			JToolBarHelper::preferences('com_botiga');
		}
	}
}
?>
