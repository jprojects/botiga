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

		$db->setQuery("SELECT id, name, image FROM `#__botiga_brands` WHERE published = 1 ORDER BY ordering asc");
		return $db->loadObjectList();
	}

	public static function getCats() {

		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();

		$db->setQuery("SELECT id, title, parent_id FROM `#__categories` WHERE extension = 'com_botiga' AND (language = '$lang' OR language = '*') AND parent_id = 1 AND published = 1 ORDER BY lft");
		return $db->loadObjectList();
	}

	public static function getSubCats($id) {

		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();

		$db->setQuery("SELECT id, title, parent_id FROM `#__categories` WHERE parent_id = $id AND (language = '$lang' OR language = '*') AND published = 1 ORDER BY lft");
		return $db->loadObjectList();
	}

	public static function getSubCatsId($id) {

		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		$result = array();

		$db->setQuery("SELECT id FROM `#__categories` WHERE parent_id = $id AND (language = '$lang' OR language = '*') AND published = 1 ORDER BY lft");
		$rows = $db->loadObjectList();
		foreach($rows as $row) {
			$result[] = $row->id;
		}
		return $result;
	}

	public static function getCategoriesByBrand($brand) {

		$db   = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();

		$db->setQuery("SELECT DISTINCT(catid) FROM `#__botiga_items` WHERE marca = '$brand' AND published = 1 AND (language = '$lang' OR language = '*')");
		return $db->loadObjectList();
	}
}

?>
