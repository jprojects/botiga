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

define('DS', DIRECTORY_SEPARATOR);

// Require the base controller
JLoader::registerPrefix('botiga', JPATH_COMPONENT);
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'botiga.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'sincro.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'pdf.php');
require_once (JPATH_COMPONENT.DS.'controller.php');

JHtml::_('jquery.framework');

JHTML::stylesheet('components/com_botiga/assets/css/botiga.css');
JHTML::script('components/com_botiga/assets/js/botiga.js');

// Perform the Request task
$controller	= JControllerLegacy::getInstance('botiga');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
