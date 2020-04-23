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

JHtml::stylesheet('modules/mod_botiga_menu/assets/css/botiga_menu.css');
JHtml::script('modules/mod_botiga_menu/assets/js/botiga_menu.js');

$logos = $params->get('logos', 1);
$class_sfx = $params->get('moduleclass_sfx', '');

require( JModuleHelper::getLayoutPath( 'mod_botiga_menu', 'default') );

?>
