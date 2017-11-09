<?php

/**
* @version		$Id: mod_botiga_menu  Kim $
* @package		mod_botiga_menu v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Acceso Restringido');

class modBotigaMenuHelper
{
	public static function getBrands() {
				
		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();

		$db->setQuery('select id, name from #__botiga_brands where published = 1 order by ordering asc');
		return $db->loadObjectList();
	}
	
	public static function getCollections() {
				
		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		$app  = JFactory::getApplication();
		
		$marca = $app->input->get('marca', 0);
		
		$sql = 'select distinct(collection) from #__botiga_items where published = 1 and language = '.$db->quote($lang).' ';
		if($marca != 0) { $sql .= 'and brand = '.$marca.' '; } 
		$sql .= 'order by collection ASC';

		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	public static function getCats() {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select id, title, parent_id from #__categories where extension = '.$db->quote('com_botiga').' and parent_id = 1 and published = 1 order by lft');
		return $db->loadObjectList();
	}
	
	public static function getSubCats($id) {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select id, title, parent_id from #__categories where parent_id = '.$id.' and published = 1 order by lft');
		return $db->loadObjectList();
	}
	
	public static function getCategoriesByBrand($brand) {
	
		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		
		$db->setQuery('select distinct(catid) from #__botiga_items where marca = '.$brand.' and published = 1 and language = '.$db->quote($lang));
		return $db->loadObjectList();
	}	
}

?>
