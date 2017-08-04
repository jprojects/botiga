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

define('DS', 'DIRECTORY_SEPARATOR');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_botiga'))
{
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
JLoader::register('botigaHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'botiga.php');

// import joomla controller library
jimport('joomla.application.component.controller');
 
$controller	= JControllerLegacy::getInstance('botiga');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
