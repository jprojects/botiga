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

class botigaModelCheckout extends JModelList
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
		$value = JRequest::getInt('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = JRequest::getInt('limitstart', 0);
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
    	$session = JFactory::getSession();
    	$db = JFactory::getDbo();
    	
    	//check if a comanda exist
		$db->setQuery('select id from #__botiga_comandes where userid = '.$user->id.' and status = 1');
		$id = $db->loadResult();
		if($id > 0) { $session->set('idComanda', $id); }
    	
    	$idComanda = $session->get('idComanda', '');
        
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		
		$query->select('cd.*, i.name, i.ref, i.image1, i.brand, i.catid');
		
		$query->from('#__botiga_comandesDetall as cd');
		$query->join('inner', '#__botiga_items as i on i.id = cd.idItem');		
		$query->where('cd.idComanda = '.$idComanda);
		
        $params = JComponentHelper::getParams( 'com_botiga' );
		
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
	
	public function getComandaCoupon()
	{
    	$session 	= JFactory::getSession();
    	$db 		= JFactory::getDbo();
    	
    	$idComanda = $session->get('idComanda', '');
    	
    	$db->setQuery('SELECT idCoupon FROM #__botiga_comandes WHERE id = '.$idComanda);
    	
    	return $db->loadResult();   	    	
	}
	
	/**
	 * Get a shipment amount.
	 * @params float $amount the total cart amount
	 * @return	float
	*/
	public function getShipment($amount)
	{
		jimport( 'joomla.access.access' );
		
		$db 	 = JFactory::getDbo();
		$user 	 = JFactory::getUser();
		$session = JFactory::getSession();
		$groups  = JAccess::getGroupsByUser($user->id, false);
		
		$idComanda = $session->get('idComanda', '');					
		
		$shipment_discount = 0;
		
		$db->setQuery('SELECT pais, cp FROM #__botiga_users WHERE userid = '.$user->id);
		$usr = $db->loadObject();
		
		$db->setQuery('SELECT * FROM #__botiga_shipments WHERE published = 1 AND usergroup IN ('.implode(',', $groups).')');
		$rows = $db->loadObjectList();				
		
		foreach($rows as $row) {

			if($amount > $row->free) { return 0; } //el pedido supera el porte mínimo, salimos retornando 0
			
			$countries = explode(';', $row->country);
				
			if($row->conditional == 0) { 
				if (!in_array($usr->pais, $countries)) {
					botigaHelper::customLog($row->id.' same countries fail');
					continue; //same countries fail end iteration					
				}
			} else {
				if (in_array($usr->pais, $countries)) {
					botigaHelper::customLog($row->id.' distinct countries fail');
					continue; //distinct countries fail end iteration
				}
			}
			
			$total 		= $row->total;
			$operator 	= $row->operator;
			
			//type 1 shippment by zip code method
			if($row->type == 1) {									
				
				if($usr->cp >= $row->min && $usr->cp <= $row->max) {												
					
					//make operations
					if($operator == '%') {
						$shipment_discount += ($total / 100) * $amount;
					}
					if($operator == '+') {
						$shipment_discount += $total;
					}
				}
			}
			
			//type 2 shippment by weight method
			if($row->type == 2) {
			
			botigaHelper::customLog($row->id.' inside type 2');
				
				$db->setQuery('SELECT SUM(i.pes) FROM #__botiga_items AS i INNER JOIN #__botiga_comandesDetall AS cd ON cd.idItem = i.id  where cd.idComanda = '.$idComanda);
				$weight   = $db->loadResult() / 10;
				
				//make operations
				if($operator == '%') {
					$shipment_discount += ($total / 100) * $amount;
				}
				if($operator == '+') {
					$shipment_discount += $total;
				}
				
				$shipment_discount * $weight; //multiplicamos la cantidad a sumar al carro por las veces que se repite el peso
			
			}
			
			//type 3 shippment by country method
			if($row->type == 3) {								
				
				//make operations
				if($operator == '%') {
					$shipment_discount += ($total / 100) * $amount;
				}
				if($operator == '+') {
					$shipment_discount += $total;
				}
			
			}
		}		
		
		return number_format($shipment_discount, 2, '.', '');
		
	}
}
