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
	 * method to get user data
	 * @params string $field database field request
	 * @return mixed
	*/
    public static function getCatData($field, $catid)
	{
		if($catid == 0) { return JText::_('COM_BOTIGA_ITEMS_TITLE'); }
		
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__categories where id = ".$catid);
		return $db->loadResult();
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
	 * method to get category name
	 * @return mixed
	*/
    public static function getCategoryName()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
		$catid = $app->input->get('catid', 0);
		
		if($catid == 0) {
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
	 * method to get user the price
	 * @return float
	*/
	public static function getUserPrice($itemid)
	{
		jimport( 'joomla.access.access' );
		$user   = JFactory::getUser();
		$db		= JFactory::getDbo();
		
		$login_prices = botigaHelper::getParameter('login_prices');
		
		//if user is guest hide price
		if($login_prices == 1 && $user->guest) { return '0.00'; }
		
		$groups = JAccess::getGroupsByUser($user->id, false);
		
		$db->setQuery('select price, catid from #__botiga_items where id = '.$itemid);
		$row = $db->loadObject();
		
		//check category marked as hidden for guests...
		if($user->guest) {
			$catid_prices = botigaHelper::getParameter('catid_prices');
			$cats = explode(',', $row->catid);
			foreach($cats as $cat) {
				if(in_array($cat, $catid_prices)) { return '0.00'; }
			}
		}
		
		$prices = json_decode($row->price);
		
		foreach ($prices as $sub) 
      	{
			foreach ($sub as $k => $v) 
		    {
		        $result[$k][] = $v;
		    }
      	}
      	
      	foreach ($result as $index=>$value) 
		{ 
			if($user->guest) { $groups = array(2); }  
    		if(in_array($value[0], $groups)) { $result = $value[1]; }
		}
		
		return number_format($result, 2);
		
	}
}
