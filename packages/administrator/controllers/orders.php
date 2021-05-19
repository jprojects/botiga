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
jimport('joomla.application.component.controlleradmin');

/**
 * Items Controller
 */
class botigaControllerOrders extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Orders', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}  
	
	public function delete() 
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$pks = $this->input->post->get('cid', array(), 'array');
		JArrayHelper::toInteger($pks);
		
		$db = JFactory::getDbo();
		
		foreach($pks as $pk) {
			$db->setQuery('DELETE FROM #__botiga_comandesDetall WHERE idComanda = '.$pk);
			$db->query();
		}
		
		foreach($pks as $pk) {
			$db->setQuery('DELETE FROM #__botiga_comandes WHERE id = '.$pk);
			$db->query();
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=orders', 'Comandes esborrades amb èxit', 'info');
	}
	
}
