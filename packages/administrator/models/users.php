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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class botigaModelUsers extends JModelList
{
    /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	*/
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'id',
				'nom_empresa', 'nom_empresa',
				'username', 'username',
				'telefon', 'telefon', 
				'poblacio', 'poblacio',
				'pais', 'pais',
				'tarifa', 'tarifa', 
				'published', 'published',
				'contacto', 'contacto',
				'tipo', 'tipo',
				'cif', 'cif',
			);
		}
		parent::__construct($config);
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	*/
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_botiga');
		$this->setState('params', $params);
                
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$city = $this->getUserStateFromRequest($this->context.'.filter.city', 'filter_city');
		$this->setState('filter.city', $city);

		$country = $this->getUserStateFromRequest($this->context.'.filter.country', 'filter_country');
		$this->setState('filter.country', $country);
		
		$rate = $this->getUserStateFromRequest($this->context.'.filter.rate', 'filter_rate');
		$this->setState('filter.rate', $rate);
		
		$group = $this->getUserStateFromRequest($this->context.'.filter.group', 'filter_group');
		$this->setState('filter.group', $group);

		// List state information.
		parent::populateState('id', 'asc');
	}
        
    /**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	*/
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.city');
		$id	.= ':'.$this->getState('filter.country');
		$id	.= ':'.$this->getState('filter.group');

		return parent::getStoreId($id);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	*/
	protected function getListQuery()
	{
				
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		
		$query->select('u.*');

		$query->from('#__botiga_users u');
                
        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(nom_empresa LIKE '.$search.')');
		}
                
        // Filter by city.
    	$city = $this->getState('filter.city');
		if (!empty($city) and $city!='*') {
			$city = $db->Quote($db->escape($city, true));
			$query->where('(poblacio='.$city.') ' );
		}

		// Filter by country.
    	$country = $this->getState('filter.country');
		if (!empty($country) and $country!='*') {
			$country = $db->Quote($db->escape($country, true));
			$query->where('(pais='.$country.') ' );
		}
		
		// Filter by usergroup.
    	$group = $this->getState('filter.group');
		if (!empty($group)) {
			$query->where('(g.id='.$group.') ' );
		}
		
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));

        //echo $query;
		return $query;
	}
}
