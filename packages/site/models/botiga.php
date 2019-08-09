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

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class botigaModelBotiga extends JModelList
{ 
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
		
		$catid = $app->input->getInt('catid', 0);
		$this->setState('list.catid', $catid);
		
		$orderby = $app->input->getInt('orderby', 'id');
		$this->setState('list.orderby', $orderby);

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
	protected function getListQuery()
	{
    	$user = JFactory::getUser();
    	$app  = JFactory::getApplication();
        
		$db         = $this->getDbo();
		
		$query      = $db->getQuery(true);
		
		$query->select('i.*, b.name AS brandname');
		
		$query->from($db->quoteName('#__botiga_items').' AS i');
		
		$query->join('LEFT', $db->quoteName('#__botiga_brands').' AS b ON b.id = i.brand');
		
		// TODO:
		// 1. buscar l'usuari de la sessió en afi_botiga_users i determinar el valor del camp usergroup
		// 2. si l'usuari no existeix, s'agafarà el grup 1 (públic)
		// 3. afegir un join amb la taula afi_botiga_items_prices, i un where per seleccionar sols les files del grup que correspongui 
		// 4. canviar l'order by del preu

        // Filters
       	$catid   	= $app->input->getInt('catid', 0);
		$marca   	= $app->input->getInt('marca', 0);
		$collection = $app->input->getString('collection', '');
		$ref     	= $app->input->get('ref', '');
		$orderby 	= $app->input->get('orderby', 'id');
		
		$groups  = JAccess::getGroupsByUser($user->id, false);
		
		//order by price
		if($orderby == 'pvp') {			
			if(in_array(10, $groups)) {
				$orderby = 'CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(i.price, \'pricing":["\',-1), \'"\', -2), \'"\', 1) AS double(10,2))'; //empresa
			} else {
				$orderby = 'CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(i.price, \'pricing":["\',-1), \'","\', 1) AS double(10,2))'; //registered
			}
		} else {
			$orderby = 'i.'.$orderby;
		}		

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
		$query->where('i.child = ""');
		$query->where('(i.usergroup = 1 OR i.usergroup IN('.implode(',', $groups).'))');
		$query->where('i.language = '.$db->quote(JFactory::getLanguage()->getTag()).' ORDER BY '.$orderby.' ASC');

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
	
	public function getNumItemsRow($id, $price) {
	
		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		
		$idComanda = $session->get('idComanda', '');
		
		if($idComanda != '') {
		
			$db->setQuery('SELECT qty FROM #__botiga_comandesDetall WHERE idItem = '.$id.' AND idComanda = '.$idComanda);
			
			$qty = $db->loadResult();
			
			if($qty > 0) { $price = $qty * $price; } else { $price = 0; }
		} else {
			$price = 0;
		}
			
		return number_format($price, 2);
	}
	
	public function getQtyRow($id) {
	
		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		
		$idComanda = $session->get('idComanda', '');
		$qty = 0;
		
		if($idComanda != '') {
		
			$db->setQuery('SELECT qty FROM #__botiga_comandesDetall WHERE idItem = '.$id.' AND idComanda = '.$idComanda);
			
			$qty = $db->loadResult();
			
			if($qty == '') { $qty = 0; }
		}
			
		return $qty;
	}
}
