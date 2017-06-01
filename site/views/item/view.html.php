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

class botigaViewItem extends JViewLegacy
{
    protected $item;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->item = $this->get('Item');
        $this->params = $app->getParams('com_laundry');
        $this->params       = $app->getParams('com_laundry');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$active  = $app->getMenu()->getActive();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$menu    = $menus->getActive();
		
		$title = $this->params->get('page_title', '');

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }       

        $this->_prepareDocument();

        parent::display($tpl);
    }
        
    /**
	 * Method to set up the document properties
	 *
	 * @return void
	*/
	protected function _prepareDocument()
	{
		$app        = JFactory::getApplication();		
        $document   = JFactory::getDocument();
		$menus      = $app->getMenu();
		$title      = null;
		
		$document->addStylesheet('components/com_botiga/assets/css/botiga.css');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$title = $menu->title;
		} else {
			$title = JText::_('COM_BOTIGA_DEFAULT_PAGE_TITLE');
		}

		$this->document->setTitle($title);
	}
}
?>
