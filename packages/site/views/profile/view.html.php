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

class botigaViewProfile extends JViewLegacy
{
	protected $state;
  protected $item;
  protected $form;
  protected $params;

  /**
   * Display the view
   */
  public function display($tpl = null) {

    $app = JFactory::getApplication();
    $user = JFactory::getUser();

    $this->state = $this->get('State');
		$this->item  = $this->get('Data');
    $this->form  = $this->get('Form');
    $this->params       = $app->getParams('com_botiga');

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

    parent::display($tpl);
	}
}
?>
