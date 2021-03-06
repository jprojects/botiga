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
    	
    	$sessid = $session->getId();
    	
    	//check if a comanda exist
		$db->setQuery('select MAX(id) from #__botiga_comandes where (userid = '.$user->id.' OR sessid = '.$db->quote($sessid).') and status < 3');
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
	 * Get a total discount.
	 * @return	float
	*/
	public function getDiscounts()
	{
		jimport( 'joomla.access.access' );
		
		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		
		$idComanda = $session->get('idComanda', '');					
		
		$discount = array();
		$discount['total'] = 0;
		$discount['total_products'] = 0;
		$discount['message'] = '';
		
		$db->setQuery('SELECT * FROM #__botiga_comandesDetall WHERE idComanda = '.$idComanda);
		$rows = $db->loadObjectList();				
		
		foreach($rows as $row) {														
			$preu_detall = round($row->price * (1- $row->dte_linia/100), 2);
			
			$db->setQuery('SELECT * FROM #__botiga_discounts WHERE idItem = '.$row->idItem.' AND published = 1');
			$dsc = $db->loadObject();
			
			//type 1 discounts by nºitems in a box
			if($dsc->type == 1) {		

				if($row->qty >= $dsc->box_items) {
				
					$num_capces = round($row->qty / $dsc->box_items); //nº de capces
					$items_sense_capsa = $row->qty - ($num_capces * $dsc->box_items); //items sense capça
					
					$discount['total_products'] += $row->qty;
					
					//botigaHelper::customLog('capces '.$num_capces.' sense capça '.$items_sense_capsa);
					
					$new_price = ($num_capces * $dsc->box_items) * $preu_detall * (1-$dsc->total/100);
					
					$old_price = ($num_capces * $dsc->box_items) * $preu_detall;
					
					$discount['total'] += number_format(($old_price - $new_price), 2, '.', '');
					$discount['message'] = JText::sprintf($dsc->message, $num_capces, $dsc->box_items);
				}		
			}
			
			//type 2 discounts by nºitems
			if($dsc->type == 2) {		

				if($row->qty >= $dsc->min) {
					
					$new_price = $row->qty * $preu_detall * (1-$dsc->total/100);
					
					$old_price = $row->qty * $preu_detall;
					
					$discount['total_products'] += $row->qty;
					
					$discount['total'] += number_format(($old_price - $new_price), 2, '.', '');
					$discount['message'] = JText::sprintf($dsc->message, $discount['total_products']);
				}		
			}
		}		
		
		return json_encode($discount);		
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

		if($user->guest) { return 0; }
		
		$groups  = JAccess::getGroupsByUser($user->id, false);

		$idComanda = $session->get('idComanda', '');

		$shipment_discount = 0;

		$db->setQuery('SELECT pais, cp FROM `#__botiga_users` WHERE userid = '.$user->id);
		$usr = $db->loadObject();

		//$db->setQuery('SELECT * FROM `#__botiga_shipments` WHERE published = 1 AND usergroup IN ('.implode(',', $groups).') ORDER BY type DESC');
		$db->setQuery('SELECT * FROM `#__botiga_shipments` WHERE published = 1 ORDER BY type ASC');
		$rows = $db->loadObjectList();

		//$db->setQuery('SELECT COUNT(id) FROM `#__botiga_comandesDetall` WHERE idComanda = '.$idComanda);
		//$count = $db->loadObject();

		foreach($rows as $row) {

			$countries = explode(';', $row->country);
			$total 	   = $row->total;
			$operator  = $row->operator;

			//type 4 shippment by itemid method
			if($row->type == 4) {

				$db->setQuery('SELECT qty FROM `#__botiga_comandesDetall` WHERE idItem = '.$row->itemid.' AND idComanda = '.$idComanda);
				$qty = $db->loadResult();
				if($qty > 0)	{
					//botigaHelper::customLog('Tipus:'.$row->type.' comanda:'.$idComanda);
					if($amount > $row->free) { return 0; } //el pedido supera el porte mínimo, salimos retornando 0
					//make operations
					if($operator == '%') {
						$shipment_discount += ($total / 100) * $amount;
					}
					if($operator == '+') {
						$shipment_discount += $total;
					}
					//if($count == 1) { // si es l'unic item del carro no continuem sumant ports
						return number_format($shipment_discount, 2, '.', '');
					//}
				}

			}

			//type 1 shippment by zip code method
			if($row->type == 1) {

				//botigaHelper::customLog('CP:'.$usr->cp.' comanda:'.$idComanda);

				if($usr->cp >= $row->min && $usr->cp <= $row->max) {

					if($amount > $row->free) { return 0; } //el pedido supera el porte mínimo, salimos retornando 0

					//botigaHelper::customLog('Tipus:'.$row->type.' comanda:'.$idComanda);

					//make operations
					if($operator == '%') {
						$shipment_discount += ($total / 100) * $amount;
					}
					if($operator == '+') {
						$shipment_discount += $total;
					}

					return number_format($shipment_discount, 2, '.', '');
				}
			}

			//type 2 shippment by weight method
			if($row->type == 2) {

				//botigaHelper::customLog('Tipus:'.$row->type.' comanda:'.$idComanda);

				if($amount > $row->free) { return 0; } //el pedido supera el porte mínimo, salimos retornando 0

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

				return number_format($shipment_discount, 2, '.', '');
			}

			//type 3 shippment by country method
			if($row->type == 3) {

				if($row->conditional == 0) { //0 mateixos països 1 diferents països
					if (!in_array($usr->pais, $countries)) {
						continue; //no concordan els països així que saltem a un altre
					}
				} else {
					if (in_array($usr->pais, $countries)) {
						continue; //concordan els països així que saltem a un altre
					}
				}

				if(!botigaHelper::isEmpresa() && $amount > $row->free) { return 0; } //el pedido supera el porte mínimo, salimos retornando 0

				//botigaHelper::customLog('Tipus:'.$row->type.' comanda:'.$idComanda.' amount:'.$amount);

				// if(botigaHelper::isEmpresa()) {
				// 	$num = 0;
				// 	$db->setQuery('SELECT id, qty, idItem FROM `afi_botiga_comandesDetall` WHERE idComanda = '.$idComanda.' GROUP BY idItem');
				// 	$items = $db->loadObjectList();

				// 	foreach($items as $item) {
				// 		if($item->qty >= 4) { $num++; }
				// 		botigaHelper::customLog('Qty:'.$item->qty.' min:'.$row->min);
				// 	}

				// 	if($num >= $row->min) { return 0; } //es empresa i la quantitat d'items es superior al mínim
				// }

				//make operations
				if($operator == '%') {
					$shipment_discount += ($total / 100) * $amount;
				}
				if($operator == '+') {
					$shipment_discount += $total;
				}

				return number_format($shipment_discount, 2, '.', '');

			}
		}

		return number_format($shipment_discount, 2, '.', '');
	}
}
