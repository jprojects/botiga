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

class botigaViewBrand extends JViewLegacy
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
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = botigaHelper::getActions($this->item->id);
		JToolBarHelper::title($isNew ? JText::_('COM_BOTIGA_MANAGER_BRAND_NEW') : JText::_('COM_BOTIGA_MANAGER_BRAND_EDIT'), 'joomla');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('brand.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::divider();
				JToolBarHelper::save('brand.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::divider();
				JToolBarHelper::custom('brand.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('brand.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('brand.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::divider();
				JToolBarHelper::save('brand.save', 'JTOOLBAR_SAVE');
 
				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) 
				{
                    JToolBarHelper::divider();
					JToolBarHelper::custom('brand.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) 
			{
                JToolBarHelper::divider();
				JToolBarHelper::custom('brand.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
            JToolBarHelper::divider();
			JToolBarHelper::cancel('brand.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
?>
