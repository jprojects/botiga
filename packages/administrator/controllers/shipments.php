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
class botigaControllerShipments extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Shipment', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}  
	
}
