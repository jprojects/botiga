<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyleft (C) 2019
 * @license     Licencia Pública General GNU versión 3 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user  			= JFactory::getUser();
$jinput			= JFactory::getApplication()->input;
$logo			  = botigaHelper::getParameter('botiga_logo', '');
$userToken  = JSession::getFormToken();
$count 	    = botigaHelper::getCarritoCount();
$spain 			= botigaHelper::getParameter('total_shipment_spain', 25);
$islands 		= botigaHelper::getParameter('total_shipment_islands', 50);
$world 			= botigaHelper::getParameter('total_shipment_world', 60);
?>

<header class="head_botiga">

	<div>

		<div class="row">

			<?php if($logo != '') : ?>
			<div class="col-12 d-none d-sm-block">
				<a href="index.php"><img src="<?= $logo; ?>" alt="<?= botigaHelper::getParameter('botiga_name', ''); ?>" class="img-fluid logoBotiga"></a>
			</div>
			<?php endif; ?>

			<div class="col-12 mt-3">
				<div class="row">
					<div class="col-6 col-md-8 text-left">
						<a href="index.php?option=com_botiga&view=botiga" class="pr-1">
							<img src="media/com_botiga/icons/mosaico<?php if($jinput->getCmd('layout', '') == '') : ?>-active<?php endif; ?>.png">
						</a>
						<?php if(botigaHelper::hasAccesstoTableView()) : ?>
						<a href="index.php?option=com_botiga&view=botiga&layout=table">
							<img src="media/com_botiga/icons/lista<?php if($jinput->getCmd('layout', '') == 'table') : ?>-active<?php endif; ?>.png">
						</a>
						<?php endif; ?>
					</div>
					<div class="col-6 col-md-4 text-right">
						<a href="<?php if($count > 0) : ?>index.php?option=com_botiga&view=checkout<?php else: ?>#<?php endif; ?>" class="pr-2 carrito">
							<?php if($count > 0) : ?>
							<span class="badge badge-warning"><?= $count; ?></span>
							<?php endif; ?>
							<img src="media/com_botiga/icons/carrito.png">
						</a>
						<?php if($user->guest) : ?>
						<a href="index.php?option=com_users&view=login" title="Login" class="hasTip">
							<img src="media/com_botiga/icons/iniciar-sesion.png">
						</a>
						<?php else: ?>
						<a class="ml-4 hasTip" href="index.php?option=com_users&task=user.logout&<?= $userToken; ?>=1" title="Logout" title="Salir">
							<img src="media/com_botiga/icons/salir.png">
						</a>
						<a class="ml-4 hasTip" href="index.php?option=com_botiga&view=history" title="History" title="Perfil"s>
							<img src="media/com_botiga/icons/sesion-iniciada.png">
						</a>
						<div class="d-none d-sm-block"><small><?= JText::sprintf('COM_BOTIGA_WELCOME', $user->name); ?></small></div>
						<?php endif; ?>
					</div>
          <span class="col-12 estil02 my-3">
            <?= JText::sprintf('COM_BOTIGA_FREE_SHIPPING_MSG', $spain, $islands, $world); ?>&nbsp;<img src="media/com_botiga/icons/envio_gratis.png">
          </span>
				</div>
			</div>

		</div>

	</div>

</header>
