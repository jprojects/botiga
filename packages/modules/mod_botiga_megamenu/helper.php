<?php

/**
* @version		$Id: mod_botiga_carrito  Kim $
* @package		mod_botiga_carrito v 1.0.0
* @copyright	Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Acceso Restringido');

require_once ('components'.DS.'com_botiga'.DS.'helpers'.DS.'botiga.php');

class modBotigaMegamenuHelper extends botigaHelper
{		
	public static function getCats() {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select id, title, params from #__categories where extension = '.$db->quote('com_botiga').' and parent_id = 1 and published = 1');
		return $db->loadObjectList();
	}
	
	public static function getSubCats($id) {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select id, title from #__categories where parent_id = '.$id.' and published = 1');
		return $db->loadObjectList();
	}
	
	public static function getItemsSlide($cat) {
	
		$db   = JFactory::getDbo();
		
		$db->setQuery('select id, name, image1, s_description from #__botiga_items where catid = '.$cat.' and published = 1 order by id limit 4');
		return $db->loadObjectList();		
	}
	
	public static function getCarritoCount() 
    {
   		$db 	 = JFactory::getDbo();
   		$session = JFactory::getSession();
   		$user 	 = JFactory::getUser();
   		
   		if($user->guest) { return 0; }

		//check if a comanda exist
		$db->setQuery('select id from #__botiga_comandes where userid = '.$user->id.' and status = 1');
		$id = $db->loadResult();
		if($id > 0) { $session->set('idComanda', $id); }
		
		$idComanda = $session->get('idComanda', '');
		
		if($idComanda != '') {
   			$db->setQuery('select count(id) from #__botiga_comandesDetall where idComanda = '.$idComanda);
   			return $db->loadResult();
   		} else {
   			return 0;
   		}
    }
}

?>
