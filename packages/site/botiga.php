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

define('DS', DIRECTORY_SEPARATOR);

// Require the base controller
JLoader::registerPrefix('botiga', JPATH_COMPONENT);
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'botiga.php');
require_once (JPATH_COMPONENT.DS.'controller.php');

JHTML::stylesheet('botiga.css', 'components/com_botiga/assets/css/', array('media'=>'all'));

// Perform the Request task
$controller	= JControllerLegacy::getInstance('botiga');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
