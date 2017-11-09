<?php

/**
* @version		$Id: mod_botiga_carrito  Kim $
* @package		mod_botiga_carrito v 1.0.0
* @copyright		Copyright (C) 2014 Afi. All rights reserved.
* @license		GNU/GPL, see LICENSE.txt
*/

// restricted access
defined('_JEXEC') or die('Restricted access');
$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$cart = modBotigaHelper::getCarrito();
$total = 0;
?>

<div id="sidebar">
<div id="scroller">

<table class="table carrito table-responsive <?= $class_sfx; ?>">
<?php foreach($cart as $item ) : ?>
	<?php $item->image1 != '' ? $image = $item->image1 : $image = 'images/noimage.png'; ?>
	<tr class="row-<?= $item->idItem; ?>" id="row-<?= $item->id; ?>">
		<td><img src="<?= $image; ?>" class="img-responsive mini" alt="<?= $item->name; ?>" /></td>
		<td><?= $item->name; ?><br/><div class="bold blue text-right">x<span class="cart-qty"><?= $item->qty; ?></span> - <span class="cart-price"><?= $item->price; ?></span> &euro;</div></td>
		<td><a href="#" class="cart-delete" data-id="<?= $item->id; ?>" data-price="<?= $item->qty * $item->price; ?>"><i class="fa fa-trash-o"></i></a></td>
	</tr>			
	<?php $total += $item->price * $item->qty; ?>
<?php endforeach; ?>
	
</table>
<hr>
<table class="table">
<tr>
	<td><?= JText::_('MOD_BOTIGA_CARRITO_TOTAL'); ?></td>
	<td><div class="bold blue text-right"><span id="total"><?= number_format($total, 2); ?></span> &euro;</div></td>
</tr>
</table>
<a href="index.php?option=com_botiga&view=checkout&Itemid=134" class="btn btn-block btn-primary"><?= JText::_('MOD_BOTIGA_CARRITO_CHECKOUT'); ?></a>

</div>
</div>
