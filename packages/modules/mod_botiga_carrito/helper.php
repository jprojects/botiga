<?php

/**
* @version		$Id: mod_botiga_carrito  Kim $
* @package		mod_botiga_carrito v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Acceso Restringido');

class modBotigaHelper
{		
	public static function getCarrito() {
	
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		
		if($user->guest) { return; }
		
		//check if a comanda exist
		$db->setQuery('select id from #__botiga_comandes where userid = '.$user->id.' and status = 1');
		$id = $db->loadResult();
		if($id > 0) { $session->set('idComanda', $id); }
		
		$idComanda = $session->get('idComanda', '');

		$db->setQuery('select cd.*, i.name, i.image1 from #__botiga_comandesDetall cd inner join #__botiga_items i on i.id = cd.itemId where idComanda = '.$idComanda);
		return $db->loadObjectList();
	}
}

?>
