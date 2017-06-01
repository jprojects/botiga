<?php
/**
 * @version		1.0.0 laundry $
 * @package		laundry
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
class botigaControllerItems extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Item', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}  

	/**
	 * cpanel task.
	 * @since	1.6
	*/
	public function cpanel() 
	{
		$link = "index.php?option=com_botiga";
		$this->setRedirect($link);
	}
	
	/**
	 * Method to clone an existing block.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function duplicate()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$pks = $this->input->post->get('cid', array(), 'array');
		JArrayHelper::toInteger($pks);
	
		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('No hay ningún item seleccionado'));
			}
	
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(JText::plural('Items duplicados correctamente', count($pks)));
		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
	
		$this->setRedirect('index.php?option=com_botiga&view=items');
	}
	
}
