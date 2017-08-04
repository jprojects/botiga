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

class botigaModelItems extends JModelList
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
				'ref', 'ref',
				'catid', 'catid',
				'name', 'name',
				'published', 'published',
				'language', 'language',
				'price', 'price',
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
	public function getTable($type = 'Items', $prefix = 'botigaTable', $config = array())
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
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		$catid = $this->getUserStateFromRequest($this->context.'.filter.catid', 'filter_catid', '');
		$this->setState('filter.catid', $catid);

		// List state information.
		parent::populateState('i.id', 'asc');
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

		$query->select('i.*, c.title as catname');

		$query->from('#__botiga_items i');
		
		$query->join('inner', '#__categories c on c.id = i.catid');
                
        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(i.name LIKE '.$search.') OR (i.ref LIKE '.$search.')');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('i.language = ' . $db->quote($language));
		}
		
		// Filter by category.
		if ($catid = $this->getState('filter.catid')) {
			$query->where('i.catid = ' . $db->quote($catid));
		}
                
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'i.id');
		$orderDirn	= $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
                
		return $query;
	}
}
