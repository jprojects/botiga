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

$spain 		= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 	= botigaHelper::getParameter('total_shipment_islands', 50);
$world 		= botigaHelper::getParameter('total_shipment_world', 60);

$uri 		= base64_encode(JFactory::getURI()->toString());
$logo		= botigaHelper::getParameter('botiga_logo', '');
$userToken  = JSession::getFormToken();

$user = JFactory::getUser();

?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="mt-5">

		<?php $dispatcher->trigger( 'onPaymentNew', array( $processor, $total, $idComanda ) ); ?>

	</div>

</div>
