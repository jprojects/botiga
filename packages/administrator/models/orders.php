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

class botigaModelOrders extends JModelList
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
				'userid', 'a.userid',
				'data', 'a.data',
				'status', 'a.status',
				'subtotal', 'a.subtotal',
				'processor', 'a.processor',
				'discount', 'a.discount',
				'shipment', 'a.shipment',
				'iva_percent', 'a.iva_percent',
				'iva_total', 'a.iva_total',
				're_percent', 'a.re_percent',
				're_total', 'a.re_total',
				'idCoupon', 'a.idCoupon'
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
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_botiga');
		$this->setState('params', $params);
                
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$processor = $this->getUserStateFromRequest($this->context.'.filter.processor', 'filter_processor');
		$this->setState('filter.processor', $processor);
		
		$status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status');
		$this->setState('filter.status', $status);
		
		$from = $this->getUserStateFromRequest($this->context.'.filter.date_from', 'filter_date_from');
		$this->setState('filter.date_from', $from);
		
		$to = $this->getUserStateFromRequest($this->context.'.filter.date_to', 'filter_date_to');
		$this->setState('filter.date_to', $to);

		// List state information.
		parent::populateState($ordering, $direction);
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
		// Create a new query object.		
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('a.*, u.nom_empresa, u.id as user_id');

		$query->from('#__botiga_comandes as a');
		
		$query->join('inner', '#__botiga_users as u on a.userid = u.userid');
                
        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(u.nom_empresa LIKE '.$search.')');
		}
		
		// Filter by processor.
		$processor = $this->getState('filter.processor');
		if (!empty($processor)) {
			$query->where('(a.processor = '.$db->Quote($processor).')');
		}
		
		// Filter by status.
		$status = $this->getState('filter.status');
		if (!empty($status)) {
			$query->where('(a.status = '.$status.')');
		}
		
		// Filter by date from.
		$from = $this->getState('filter.date_from');
		if (!empty($from)) {
			$query->where('(a.data >= '.$db->Quote($from).')');
		}
		
		// Filter by date to.
		$to = $this->getState('filter.date_to');
		if (!empty($to)) {
			$query->where('(a.data <= '.$db->Quote($to).')');
		}
                
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.id');
		$orderDirn	= $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
        //echo $query;        
		return $query;
	}
}
