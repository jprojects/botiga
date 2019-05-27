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
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
class botigaModelUser extends JModelAdmin
{
    /**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	*/
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_botiga.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	*/
	public function getTable($type = 'Users', $prefix = 'botigaTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	*/
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_botiga.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
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
		$data = JFactory::getApplication()->getUserState('com_botiga.edit.user.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			$params = json_decode($data->params); 
			$data->metodo_pago = $data->params['metodo_pago'];
			$data->re_equiv = $data->params['re_equiv'];
		}
		return $data;
	}
	
	/**
	 * method to store data into the database
	 * @param boolean
	*/
    function store()
	{		
		$row =& $this->getTable();
		$db  = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$post_data  = JRequest::get( 'post' );
   		$data       = $post_data["jform"];
	    	
    	$params['metodo_pago'] = $data['metodo_pago'];
    	$params['re_equiv']    = $data['re_equiv'];
    	
    	unset($data['metodo_pago'], $data['re_equiv']);
    	
    	$data['params'] = json_encode($params);
    	
    	//save usergroup 0 customer (11) 1 company (10)
    	if($data['type'] == 0) {
    		$db->setQuery('UPDATE #__user_usergroup_map SET group_id = 11 WHERE user_id = '.$user->id.' AND group_id = 10');
    		$db->query();
    	} else {
    		$db->setQuery('UPDATE #__user_usergroup_map SET group_id = 10 WHERE user_id = '.$user->id.' AND group_id = 11');
    		$db->query();
    	}
	
		if (!$row->bind( $data )) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		if (!$row->store()) {
			return JError::raiseError(500, $row->getError() );
		}

		return true;

	}
    
}
?>
