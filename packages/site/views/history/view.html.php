<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class botigaViewHistory extends JViewLegacy
{
  protected $state;
	protected $items;
	protected $pagination;
  protected $params;

  function display($tpl = null)
	{
		// Initialise variables
    $app		          = JFactory::getApplication();
		$this->state		  = $this->get('State');
		$this->items		  = $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->params     = $app->getParams('com_botiga');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$active  = $app->getMenu()->getActive();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$menu    = $menus->getActive();

    JFactory::getDocument()->addScript(JURI::root().'components/com_botiga/assets/js/jquery.repeater.js');

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

		parent::display($tpl);
	}
}
?>
