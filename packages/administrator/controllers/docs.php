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
class botigaControllerDocs extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Doc', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}  
	
}
