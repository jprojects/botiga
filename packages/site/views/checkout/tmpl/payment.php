<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JPluginHelper::importPlugin( 'botiga' );
$dispatcher = JEventDispatcher::getInstance();

$jinput    = JFactory::getApplication()->input;
$processor = $jinput->get('processor');
$idComanda = JFactory::getSession()->get('idComanda');
$total     = botigaHelper::getComandaData('total', $idComanda);

?>

<?php $dispatcher->trigger( 'onPaymentNew', array( $processor, $total, $idComanda ) ); ?>
