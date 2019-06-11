<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyright (C) 2015. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access
defined('_JEXEC') or die;

/**
 * helper.
 */
class botigaHelper {
	
	/**
	 * method to get component parameters
	 * @param string $param
	 * @param mixed $default
	 * @return mixed
	*/
	public static function getParameter($param, $default="")
	{
		$params = JComponentHelper::getParams( 'com_botiga' );
		$param = $params->get( $param, $default );
	
		return $param;
	}
	
	/**
	 * method to get percent discount
	 * @return int
	*/
	public static function getPercentDiff($pvp, $price) 
    {
		$diff = $pvp - $price; 
		$result = ($diff / $pvp) * 100;   
		return round($result);
    }
    
    /**
	 * method to get payment plugins
	 * @return mixed
	*/
    public static function getPaymentPlugins()
    {
	    $db = JFactory::getDbo();
	    $db->setQuery("SELECT params FROM #__extensions WHERE type = 'plugin' AND folder = 'botiga' AND enabled = 1");
	    return $db->loadObjectList();
    }
    
    /**
	 * method to get all countries
	 * @return mixed
	*/
	public static function getCountries() 
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT country_id, country_name FROM #__botiga_countries WHERE published = 1 ORDER BY country_name');
		return $db->loadObjectList();
	}
    
    /**
	 * method to get user data
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function getUserData($field, $userid)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_users where userid = ".$userid);
		return $db->loadResult();
	}
	
	/**
	 * method to get comanda data
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function getItemData($field, $id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_items where id = ".$id);
		return $db->loadResult();
	}
	
	/**
	 * method to get all documents associated to a one document
	 * @params string $id database item id
	 * @return mixed
	*/
    public static function getItemDocuments($id)
	{
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		$db->setQuery("select * from #__botiga_documents where idItem = ".$id." AND language = ".$db->quote($lang));
		return $db->loadObjectList();
	}
	
	/**
	 * method to get comanda data
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function getComandaData($field, $idComanda)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_comandes where id = ".$idComanda);
		return $db->loadResult();
	}
	
	/**
	 * method to get all categories
	 * @return mixed
	*/
	public static function getCategories() 
	{
		$db = JFactory::getDbo();
		$db->setQuery( 
			'SELECT id, title ' .
			' FROM #__categories ' .
			' WHERE extension = ' . $db->quote('com_botiga') .
				' AND published=1 ' .
				' AND language = ' . $db->quote(JFactory::getLanguage()->getTag()) .
			' ORDER BY id ASC' );
		return $db->loadObjectList();
	}
	
	/**
	 * method to get category name
	 * @return mixed
	*/
    public static function getCategoryName($catid=0)
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
		$catid = $app->input->get('catid', $catid);
		
		if($catid > 0) {
			$db->setQuery("select title from #__categories where id = ".$catid);
			return $db->loadResult();
		} else {
			return JText::_('COM_BOTIGA_FAVORITES_TITLE');
		}
	}
	
	/**
	 * method to get item name
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function getItemName()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
		$id = $app->input->get('id');
		
		$db->setQuery("select name from #__botiga_items where id = ".$id);
		return $db->loadResult();
	}
	
	/**
	 * method to know if item is favorite
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function isFavorite($itemid)
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$db->setQuery("select id from #__botiga_favorites where itemid = ".$itemid." and userid = ".$user->id);
		if($id = $db->loadResult()) { return true; }
		return false;
	}
	
	/**
	 * method to know the number of items in cart
	 * @return int
	*/
	public static function getCarritoCount() 
    {
   		$db 	 = JFactory::getDbo();
   		$session = JFactory::getSession();
   		$user 	 = JFactory::getUser();
		
		$idComanda = $session->get('idComanda', '');
		//echo $idComanda;
		if($idComanda != '') {
   			$db->setQuery('SELECT SUM(qty) FROM #__botiga_comandesDetall WHERE idComanda = '.$idComanda);
   			return $db->loadResult();
   		} else {
   			return 0;
   		}
    }
    
    /**
	 * method to know if coupon is in use and its values
	 * @return int
	*/
	public static function getCouponDiscount($idCoupon, $total) 
    {
   		$db = JFactory::getDbo();
		
   		$db->setQuery('SELECT * from #__botiga_coupons WHERE id = '.$idCoupon);
   		$row = $db->loadObject();
   		
   		//0 - percentatge 1- resta
   		if($row->tipus == 0) {
   			$value = ($total * $row->valor) / 100;   
   		} else {
   			$value = $total - $row->valor;   
   		}
   		
   		return number_format($value, 2, '.', '');
    }
    
    /**
	 * method to know if user is validated
	 * @return int
	*/
	public static function isValidated() 
    {
   		$db = JFactory::getDbo();
   		$user = JFactory::getUser();
   		
   		if($user->guest) { return true; }
		
   		$db->setQuery('SELECT validate from #__botiga_users WHERE userid = '.$user->id);
   		if($db->loadResult() == 1) { 
   			return true; 
   		} else {
   			return  false;
   		}   		
    }
    
    /**
	 * method to know if there are stock of a product and validate addtocart button
	 * @return bool
	*/
	public static function validateStock($stock) 
    {
   		$control_stock = botigaHelper::getParameter('control_stock', 0);
   		
   		if($control_stock == 0) { return true; }
		
   		if($control_stock == 1 && $stock > 0) { 
   			return true; 
   		} else {
   			return  false;
   		}   		
    }
    
    /**
	 * method to get user the active Itemid
	 * @return int
	*/
    public static function getItemid()
    {
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		return $active->id;
    }
    
    /**
	 * method to write in a debug log
	*/
    public static function customLog($text) {

		$handle = fopen(JPATH_COMPONENT.'/logs/botiga.log', 'a');
		fwrite($handle, date('d-M-Y H:i:s') . ': ' . $text . "\n");
		fclose($handle);
	}
	
	/**
	 * method to get user the price
	 * @params int $itemid database id requested
	 * @return float
	*/
	public static function getUserPrice($itemid) {
		jimport( 'joomla.access.access' );
		
		$user      = JFactory::getUser();
		$db		   = JFactory::getDbo();
		$resultado = 0;				
		
		$login_prices = botigaHelper::getParameter('login_prices', 0);
		
		//if user is guest hide price
		if($login_prices == 1 && $user->guest) { return '0.00'; }
		
		//check if user is no validated
		if((!botigaHelper::isValidated() && $user->id!=522) || $user->guest) { 
			$groups = array(2, 11); 
		} else {
			$groups = JAccess::getGroupsByUser($user->id, false);
		}			
		
		$db->setQuery('SELECT price FROM #__botiga_items WHERE id = '.$itemid);
		$price = $db->loadResult();
		
		$prices = json_decode($price, true);
		
		foreach ($prices as $sub) {
			foreach ($sub as $k => $v) {
		        $result[$k][] = $v;
		    }
      	}
      	
      	foreach ($result as $index => $value) { 
    		if(in_array($value[0], $groups)) { 
				$resultado = $value[1]; 
			}
		}
		
		return number_format($resultado, 2);
	}

	/**
	 * method to get user the price
	 * @params int $itemid database id requested
	 * @return float
	*/
	public static function getUserDiscounts($itemid) {
		jimport( 'joomla.access.access' );
		
		$user     	= JFactory::getUser();
		$db		  	= JFactory::getDbo();
		$resultat	= '';
		
		$login_prices = botigaHelper::getParameter('login_prices', 0);
		
		//if user is guest hide price
		if($login_prices == 1 && $user->guest) { return '0.00'; }
		
		//check if user is no validated
		if((!botigaHelper::isValidated() && $user->id!=522) || $user->guest) { 
			$groups = array(2, 11); 
		} else {
			$groups = JAccess::getGroupsByUser($user->id, false);
		}			
		
		$db->setQuery('SELECT price FROM #__botiga_items WHERE id = '.$itemid);
		$price = $db->loadResult();
		
		$prices = json_decode($price, true);
		
		foreach ($prices as $sub) {
			foreach ($sub as $k => $v) {
		        $result[$k][] = $v;
		    }
      	}
      	
      	foreach ($result as $index => $value) { 
    		if(in_array($value[0], $groups)) { 
				// grup $value[1]; 
				// article $itemid
				$db->setQuery( "SELECT * FROM #__botiga_discounts WHERE idItem=$itemid AND usergroup=$value[0]" );
				//botigaHelper::customLog("SELECT * FROM #__botiga_discounts WHERE idItem=$itemid AND type=$value[0]");
				$discounts = $db->loadObjectList();
				foreach($discounts as $discount) {
					$resultat .= ($resultat==''?'':'<br/>') . $discount->name . ': ' . $discount->total;
				}
				
			}
		}
		
		return $resultat;
	}
	
	/**
	 * method to get the extra content in the item view
	 * @params int $itemid database id requested
	 * @return mixed
	*/
	public static function getExtras($itemid)
	{
		$result = array();		
		$extras = json_decode(botigaHelper::getItemData('extres', $itemid), true);
		
		foreach ($extras as $extra) 
      	{
			foreach ($extra as $k => $v) 
		    {
		    	if($v != '') {
		        	$result[$k][] = $v;
		        }
		    }
      	}
		
		return $result;
		
	}
	
	/**
	 * method to get the extra images in the item view
	 * @params int $itemid database id requested
	 * @return mixed
	*/
	public static function getImages($itemid)
	{
		$result = array();		
		$extras = json_decode(botigaHelper::getItemData('images', $itemid), true);
		
		foreach ($extras as $extra) 
      	{
			foreach ($extra as $k => $v) 
		    {
		    	if($v != '') {
		        	$result[$k][] = $v;
		        }
		    }
      	}
		
		return $result;
		
	}
	
	/**
	 * method to get the child content in the item view
	 * @params int $itemid database id requested
	 * @return mixed
	*/
	public static function getChilds($itemid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$groups  = JAccess::getGroupsByUser($user->id, false);
		$db->setQuery('SELECT id, name FROM #__botiga_items WHERE child = '.$itemid.' AND published = 1 AND (usergroup = 1 OR usergroup IN('.implode(',', $groups).'))');
		return $db->loadObjectList();
		
	}
}
