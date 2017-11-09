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

class botigaModelBotiga extends JModelList
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
                'name', 'name'
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
		$value = $app->input->getInt('limit', 24);
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $value);

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
    	$app  = JFactory::getApplication();
    	$lang = JFactory::getLanguage(); 
		$def  = $lang->getTag();
        
		$db   = $this->getDbo();

		$query = $db->getQuery(true);
		
		$query->select('i.*, .b.name as brandname');
		
		$query->from('#__botiga_items as i');
		
		$query->join('inner', '#__botiga_brands as b ON b.id = i.brand');

        // Filters
       	$catid   = $app->input->getInt('catid', 0);
		$marca   = $app->input->getInt('marca', 0);
		$collection   = $app->input->getString('collection', '');
		$ref     = $app->input->get('ref', '');
		$orderby = $app->input->get('orderby', 'ref');
		
		$limit = $app->input->getInt('limit', 24);
		$this->setState('list.limit', $limit);

		if($catid != 0) {
			$query->where('(FIND_IN_SET ('.$catid.', i.catid))');
		}
		
		if($marca != 0) {
			$query->where('(i.brand = '.$marca.')');
		}
		
		if($collection != '') {
			$query->where('(i.collection = '.$db->quote($collection).')');
		}
		
		if($ref != '') {
			$query->where('(i.ref = '.$db->quote($ref).')');
		}

		$query->where('i.published = 1');
		$query->where('i.language = '.$db->quote($def).' ORDER BY i.'.$orderby.' ASC');

        $params = JComponentHelper::getParams( 'com_botiga' );
		//echo $query;
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
	
	public function getNumItems() {
	
		$db = JFactory::getDbo();
		$id = JFactory::getApplication()->input->get('id');
		
		$count = count($this->getItems());
		
		$db->setQuery($this->getListQuery());
		
		$total = count($db->loadObjectList());
		
		return JText::sprintf('COM_BOTIGA_TOTAL_ITEMS', $count, $total);
	}
}
