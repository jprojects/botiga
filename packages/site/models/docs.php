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

class botigaModelDocs extends JModelList
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
                'name', 'name',
                'category', 'category'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	*/
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		// Initialise variables.
		$app = JFactory::getApplication();

        // List state information
		//$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = JRequest::getInt('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = JRequest::getInt('limitstart', 0);
		$this->setState('list.start', $value);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_botiga');
		$this->setState('params', $params);

		// List state information.
		$this->setState('layout', JRequest::getCmd('layout'));
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
		return parent::getStoreId($id);
	}

	/**
	 * Gets a list of user invoices
	 *
	 * @return	array	An array of banner objects.
	 * @since	1.6
	*/
	function getListQuery()
	{
    	$user = JFactory::getUser();
    	$lang = JFactory::getLanguage()->getTag();
   
		$db   = $this->getDbo();

		$query  = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__botiga_docs');
		
		$search = $this->getState('filter.search', '');		
		if($search != '') {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(name LIKE '.$search.')');
		}
		
		$category = $this->getState('filter.category', '');		
		if($category != '') {
			$query->where('category = '.$db->quote($category));
		}
		
		$query->where('published = 1 AND listed = 1');
		$query->where("(language = ".$db->quote($lang)." OR language='*')");
		echo $query;
		return $query;
	}

	/**
	 * Get a list of items.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getItems()
	{
        $items	= parent::getItems();

		return $items;
	}
}
