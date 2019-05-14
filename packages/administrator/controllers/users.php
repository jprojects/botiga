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

// import Joomla controllerform library
jimport('joomla.application.component.controlleradmin');

/**
 * Items Controller
 */
class botigaControllerUsers extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'User', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	} 
	
	public function validate()
	{
		$db = JFactory::getDbo();
		$userid = JFactory::getApplication()->input->get('userid');
		
		$db->setQuery('UPDATE #__botiga_users SET validate = 1 WHERE userid = '.$userid);
		if($db->query()) {
			$msg = JText::_('COM_BOTIGA_USERS_VALIDATE_SUCCESS');
			$type = 'success';
		} else {
			$msg = JText::_('COM_BOTIGA_USERS_VALIDATE_ERROR');
			$type = 'error';
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=users', $msg, $type);
	} 
	
}
