<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_botiga_related
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_botiga/helpers/botiga.php';

abstract class modRelatedItemsHelper extends botigaHelper
{
	public static function getItems()
	{
		$db	 = JFactory::getDbo();
		
		$db->setQuery('select * from #__botiga_items where published = 1 order by rand() limit 4');
		return $db->loadObjectList();

	}
	
	/**
	 * Method to get the category name
	 * 
	 * @return array
	*/
	function getBrandName($id)
	{	
		$db   = JFactory::getDBO();
		
		$query = "SELECT name FROM #__botiga_brands WHERE id = ".$id;
		
		$db->setQuery($query);
		$row = $db->loadResult();	

		return $row;
	}
}
