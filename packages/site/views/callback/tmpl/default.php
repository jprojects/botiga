<?php
/**
 * @version		2.0 botiga $
 * @package		botiga
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		Kim
 * @author mail	kim@aficat.com
 * @website		http://www.aficat.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');

JLoader::import('joomla.plugin.helper');
JPluginHelper::importPlugin( 'botiga' );
$dispatcher = JEventDispatcher::getInstance();

$jinput    	= JFactory::getApplication()->input;
$idComanda 	= $jinput->get('idComanda');
$amount	   = $jinput->get('amount');

$_POST['idComanda'] = $idComanda;
$_POST['amount']    = $amount;

$dispatcher->trigger( 'onPaymentCallback', array( $_GET['method'], $_POST, $_GET['userid'], $idComanda)); 

?>
