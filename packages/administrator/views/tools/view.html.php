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

class botigaViewTools extends JViewLegacy
{    
	protected $form = null;
 
	/**
	 * display method of Item view
	 * @return void
	*/
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		
		//create charts with chart.js and jquery https://www.chartjs.org/docs/latest/getting-started/
		JHtml::_('jquery.framework');
		JHtml::script('https://cdn.jsdelivr.net/npm/chart.js@2.8.0');
 
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
	}
 
	/**
	 * Setting the toolbar
	*/
	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_BOTIGA_MANAGER_TOOLS'), 'joomla');

		JToolBarHelper::custom('tools.import', 'save-new.png', 'save-new_f2.png', 'COM_BOTIGA_TOOLS_IMPORT', false);
        JToolBarHelper::divider();
		JToolBarHelper::cancel('tools.cancel', 'JTOOLBAR_CANCEL');
	}

}
?>
