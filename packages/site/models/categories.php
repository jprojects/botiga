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

class botigaModelCategories extends JModelList
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
		$value = $app->get('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = $app->get('limitstart', 0);
		$this->setState('list.start', $value);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_botiga');
		$this->setState('params', $params);

		// List state information.
		$this->setState('layout', $app->get('layout'));
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
    	$lang = JFactory::getLanguage()->getTag();
 
		// Filters
       	$catid  = $app->input->get('catid', 1);
        
		$db    = $this->getDbo();

		$query = $db->getQuery(true);
		
		$query->select('i.id, i.title, i.params');
			
		$query->from('#__categories AS i');        

		$query->where('i.parent_id = '.$catid.' AND (i.language = '.$db->quote($lang).' OR i.language = '.$db->quote('*').') AND i.published = 1 AND i.extension = '.$db->quote('com_botiga'));

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
	
	public static function getSubCats($id) {
	
		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		
		$db->setQuery('SELECT i.id, i.title, i.parent_id FROM `#__categories` AS i WHERE i.parent_id = '.$id.' AND i.published = 1 AND (i.language = '.$db->quote($lang).' OR i.language = '.$db->quote('*').') AND i.extension = '.$db->quote('com_botiga'));
		return $db->loadObjectList();
	}
}
