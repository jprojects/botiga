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

class botigaModelOrder extends JModelList
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
				'id', 'a.id',
				'idComanda', 'a.idComanda',
				'qty', 'a.qty',
				'price', 'a.price',
				'name', 'i.name',
				'ref', 'i.ref',
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
	public function getTable($type = 'Orders', $prefix = 'botigaTable', $config = array())
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

		// List state information.
		parent::populateState('a.id', 'asc');
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
		
		$id = JFactory::getApplication()->input->get('id');
	
		$query->select('a.*, i.name, i.ref, c.subtotal, c.shipment, c.discount, c.iva_percent, c.iva_total, c.re_percent, c.re_total, c.total');

		$query->from('#__botiga_comandesDetall as a');
		
		$query->join('inner', '#__botiga_items as i on a.idItem = i.id');
		
		$query->join('inner', '#__botiga_comandes as c on c.id = a.idComanda');
                
        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(i.name LIKE '.$search.')');
		}
		
		$query->where('a.idComanda = '.$id);
                
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
                
		return $query;
	}
}
