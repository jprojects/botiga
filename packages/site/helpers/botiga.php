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
    public function getUserData($field, $userid)
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
    public function getComandaData($field, $idComanda)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_comandes where id = ".$idComanda);
		return $db->loadResult();
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
		
		//if user is guest hide price
		if($user->guest) { return '0.00'; }
		
		$groups = JAccess::getGroupsByUser($user->id, false);
		
		$db->setQuery('select price from #__botiga_items where id = '.$itemid);
		$prices = json_decode($db->loadResult());
		
		foreach ($prices as $sub) 
      	{
			foreach ($sub as $k => $v) 
		    {
		        $result[$k][] = $v;
		    }
      	}
      	
      	foreach ($result as $index=>$value) 
		{   
    		if(in_array($value[0], $groups)) { $result = $value[1]; }
		}
		
		return number_format($result, 2);
		
	}
}