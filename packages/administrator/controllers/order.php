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
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 

class botigaControllerOrder extends JControllerForm
{
    /**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Order', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function changeStatus()
	{
		$db = JFactory::getDbo();
		$id = JFactory::getApplication()->input->get('id');
		
		$db->setQuery('UPDATE #__botiga_comandes SET status = 3 WHERE id = '.$id);
		if($db->query()) {
			$msg = JText::_('COM_BOTIGA_ORDER_CHANGE_STATUS_SUCCESS');
			$type = 'success';
		} else {
			$msg = JText::_('COM_BOTIGA_ORDER_CHANGE_STATUS_ERROR');
			$type = 'success';
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=orders', $msg, $type);
	}
}
