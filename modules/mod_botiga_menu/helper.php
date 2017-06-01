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
	
	public static function getCatName($catid) {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select title from #__categories where id = '.$catid);
		return $db->loadResult();
	}
	
	public static function getCategoriesByBrand($brand) {
	
		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		
		$db->setQuery('select distinct(catid) from #__botiga_items where marca = '.$brand.' and published = 1 and language = '.$db->quote($lang));
		return $db->loadObjectList();
	}	
}

?>
