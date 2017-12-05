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
		
		$catid = $app->input->get('catid');
		
		$db->setQuery("select title from #__categories where id = ".$catid);
		return $db->loadResult();
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
		
		return number_format($result, 2, '.', '');
		
	}
	
	public static function getDibujoTecnico($ref)
	{
		if(file_exists('images/products/'.$ref.'-f.jpg')) {
			return $ref.'-f.jpg';
		}
		if(file_exists('images/products/'.$ref.' f.jpg')) {
			return $ref.' f.jpg';
		}
		if(file_exists('images/products/'.$ref.'-F.jpg')) {
			return $ref.'-F.jpg';
		}
		if(file_exists('images/products/'.$ref.' F.jpg')) {
			return $ref.' F.jpg';
		}
		
		return false;
	}
	
	public static function getFichaTecnica($ref)
	{
		if(file_exists('images/pdf/'.$ref.'-f.pdf')) {
			return $ref.'-f.pdf';
		}
		if(file_exists('images/pdf/'.$ref.' f.pdf')) {
			return $ref.' f.pdf';
		}
		if(file_exists('images/pdf/'.$ref.'-F.pdf')) {
			return $ref.'-F.pdf';
		}
		if(file_exists('images/pdf/'.$ref.' F.pdf')) {
			return $ref.' F.pdf';
		}
		
		return false;
	}
}
