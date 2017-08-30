<?php

/**
* @version		$Id: mod_botiga_menu Kim $
* @package		mod_botiga_menu v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restriccion de acceso
defined('_JEXEC') or die('Acceso Restringido');

require_once (dirname(__FILE__).DS.'helper.php');

JHtml::stylesheet('modules/mod_botiga_carrito/assets/css/mod_botiga_carrito.css');

require( JModuleHelper::getLayoutPath( 'mod_botiga_carrito', 'default') );

?>
