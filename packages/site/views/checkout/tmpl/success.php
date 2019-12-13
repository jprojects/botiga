<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail	kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$logo 		= botigaHelper::getParameter('botiga_logo', '');
$user 		= JFactory::getUser();
$jinput		= JFactory::getApplication()->input;
$userToken 	= JSession::getFormToken();

//cerramos comanda por seguridad
JFactory::getSession()->set('idComanda', null);
//print_r($_SESSION);
?>

<?php if(botigaHelper::getParameter('show_header', 0) == 1) :
  $layout = new JLayoutFile('header', JPATH_ROOT .'/components/com_botiga/layouts');
  $data   = array();
  echo $layout->render($data);
endif; ?>

<div class="col-md-11 mx-auto pb-5">

	<div class="row">

		<div class="block-msg">
			<?= JText::_('COM_BOTIGA_PROCESS_CART_SUCCESS'); ?>
		</div>
	</div>

</div>
