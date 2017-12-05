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

class botigaModelProfile extends JModelForm
{ 
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
     	 * 
	 * @param 	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_botiga.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_botiga.edit.profile.data', array());

		if (empty($data)) {
			$data = $this->getData();
			$data->email1 = $data->mail_empresa;
			$data->empresa = $data->nom_empresa;
			$data->phone = $data->telefon;
			$data->address = $data->adreca;
			$data->city = $data->poblacio;
			$data->nombre = $data->nom_empresa;
		}

		return $data;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getData()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$db->setQuery('select u.* from #__botiga_users u where u.userid = '.$user->id);

		return $db->loadObject();
	}
}
