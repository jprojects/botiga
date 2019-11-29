<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@afi.cat
 * @website		http://www.afi.cat
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class botigaControllerBotiga extends botigaController {

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	*/
	public function &getModel($name = 'Botiga', $prefix = 'botigaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save a item purchased in a database.
	*/
	public function setItem() {

		JSession::checkToken( 'get' ) or die( 'Invalid Token' );

		$db 		= JFactory::getDbo();
		$session 	= JFactory::getSession();
		$user       = JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;
		$itemid 	= $jinput->get('id');
		$price  	= botigaHelper::getUserPrice($itemid, 0);
		$return 	= base64_decode($jinput->getString('return', 'index.php'));
		$qty    	= $jinput->getInt('qty', 1);
		$layout    	= $jinput->getCmd('layout', '');

		$dte_linia    = botigaHelper::getUserData('dte_linia', $user->id);

		//si no hi ha comanda creem una de nova...
		$idComanda = $session->get('idComanda', '');
		if($idComanda == '') { $idComanda = $this->setComanda(); }

		$db->setQuery('select id, qty from #__botiga_comandesDetall where idItem = '.$itemid.' and idComanda = '.$idComanda);
		$row = $db->loadObject();

		$detall = new stdClass();

		if(count($row) && $row->qty > 0) {
			$layout == 'table' ? $quantitat = $qty : $quantitat = $row->qty + $qty; //en el layout table l'input mostra el total d'items
			$detall->id 		= $row->id;
			$detall->price 		= $price;
			$detall->qty 		= $quantitat;
			$detall->dte_linia  = $dte_linia;
			$db->updateObject('#__botiga_comandesDetall', $detall, 'id');
		} else {
			$detall->idComanda 	= $idComanda;
			$detall->idItem 	= $itemid;
			$detall->price 		= $price;
			$detall->qty 		= $qty;
			$detall->dte_linia  = $dte_linia;
			$db->insertObject('#__botiga_comandesDetall', $detall);
		}

		$link = $return;

		if($layout != 'table') {
			//depen de la url per afegir un paràmetre necessitem ? o &
			$pos = strrpos($return, "?");
			$pos === false ? $link = $return.'?m='.$itemid : $link = $return.'&m='.$itemid;
		}

		$this->setRedirect($link);
	}

	public function setComanda() {

		$db 		= JFactory::getDbo();
		$session 	= JFactory::getSession();
		$user 		= JFactory::getUser();

		$sessid 	= $session->getId();

		$timeout 	= botigaHelper::getParameter('botiga_timeout_orders', 5); //comanda lifetime from config

		//netejem comandes anteriors a 5 hores sense confirmar
		$db->setQuery('SELECT id FROM #__botiga_comandes WHERE data < (NOW() - INTERVAL '.$timeout.' HOUR) AND status <= 2');
		foreach($db->loadObjectList() as $row) {
			$db->setQuery('DELETE FROM #__botiga_comandes WHERE id = '.$row->id);
			$db->query();
			$db->setQuery('DELETE FROM #__botiga_comandesDetall WHERE idComanda = '.$row->id);
			$db->query();
		}

		if($user->guest) {
			$db->setQuery('SELECT MAX(id) FROM #__botiga_comandes WHERE sessid = '.$db->quote($sessid).' AND status <= 2');
		} else {
			$db->setQuery('SELECT MAX(id) FROM #__botiga_comandes WHERE userid = '.$user->id.' AND status <= 2');
		}

		if($idComanda = $db->loadResult()) {

			$session->set('idComanda', $idComanda); //recuperem la sessió

		} else {

			$comanda = new stdClass();
			$comanda->uniqid = md5(substr(uniqid('', true), -5));
			if(!$user->guest) { $comanda->userid = $user->id; }
			$comanda->sessid = $sessid;
			$comanda->data = date('Y-m-d H:i:s');
			$comanda->status = 1;

			$db->insertObject('#__botiga_comandes', $comanda);

			$idComanda = $db->insertid();

			$session->set('idComanda', $idComanda);
		}

		return $idComanda;
	}

	public function removeItem() {

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$id = $app->input->get('id');

		$db->setQuery('delete from #__botiga_comandesDetall where id = '.$id);
		$result = $db->query();

		if($result) {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_ITEM_REMOVED_SUCCESS'), 'success');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_ITEMT_REMOVED_ERROR'), 'error');
		}
	}

	public function removeCart() {

		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();

		$idComanda = $session->get('idComanda', '');

		if($idComanda != '') {
			$db->setQuery('delete from #__botiga_comandes where id = '.$idComanda);
			$result = $db->query();

			$db->setQuery('delete from #__botiga_comandesDetall where idComanda = '.$idComanda);
			$result2 = $db->query();

			if($result && $result2) {
				$session->set('idComanda', null);
				$this->setRedirect('index.php?option=com_botiga&view=botiga', JText::_('COM_BOTIGA_CART_REMOVED_SUCCESS'), '');
			} else {
				$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_REMOVED_ERROR'), 'error');
			}
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=botiga');
		}
	}

	public function saveCart() {

		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		$user 	 = JFactory::getUser();

		$idComanda = $session->get('idComanda', '');

		$db->setQuery('select idItem, price, qty from #__botiga_comandesDetall where idComanda = '.$idComanda);
		$rows = $db->loadObjectList();

		$cart 			 = new stdClass();
		$cart->idComanda = $idComanda;
		$cart->data 	 = date('Y-m-d H:i:s');
		$cart->userid    = $user->id;
		$cart->cart      = serialize($rows);

		$result = $db->insertObject('#__botiga_savedCarts', $cart);

		if($result) {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_SAVED_SUCCESS'), '');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_SAVED_ERROR'), 'error');
		}
	}

	public function getSavedCart() {

		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		$app     = JFactory::getApplication();
		$user    = JFactory::getUser();

		$id      = $app->input->get('id');

		$db->setQuery('select cart from #__botiga_savedCarts where id = '.$id);
		$row = $db->loadObject();

		$items = unserialize($row->cart);

		//insert cart data
		$cart 			 = new stdClass();
		$cart->data 	 = date('Y-m-d H:i:s');
		$cart->userid    = $user->id;
		$cart->status    = 1;

		$result = $db->insertObject('#__botiga_comandes', $cart);
		$insertid = $db->insertid();

		//insert cart details
		foreach($items as $item) {
			$detall = new stdClass();
			$detall->idComanda = $insertid;
			$detall->idItem    = $item->idItem;
			$detall->price     = $item->price;
			$detall->qty       = $item->qty;
			$detall->published = 1;
			$db->insertObject('#__botiga_comandesDetall', $detall);

		}

		if($result) {
			$idComanda = $session->set('idComanda', $insertid);
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_SAVED_SUCCESS'), '');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_SAVED_ERROR'), 'error');
		}
	}

	public function removeSavedCart() {

		$db 	 = JFactory::getDbo();
		$app     = JFactory::getApplication();

		$id      = $app->input->get('id');

		$db->setQuery('delete from #__botiga_savedCarts where id = '.$id);
		$result = $db->query();

		if($result) {
			$this->setRedirect('index.php?option=com_botiga&view=history', JText::_('COM_BOTIGA_HISTORY_REMOVE_SAVED_CART_SUCCESS'), '');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=history', JText::_('COM_BOTIGA_HISTORY_REMOVE_SAVED_CART_ERROR'), 'error');
		}
	}

	public function processCart()
    {
     	$db 		= JFactory::getDbo();
     	$session 	= JFactory::getSession();
     	$app        = JFactory::getApplication();

     	$data 	    = $app->input->getArray($_POST);

     	$idComanda  = $session->get('idComanda');

     	$control_stock = botigaHelper::getParameter('control_stock', 0);
		if($control_stock == 1) { //el control d'estoc es activat restem les unitats
			$db->setQuery('SELECT idItem, qty FROM #__botiga_comandesDetall WHERE idComanda = '.$idComanda);
			$rows = $db->loadObjectList();
			foreach($rows as $row) {
				$db->setQuery('UPDATE #__botiga_items SET stock = stock - '.$row->qty.' WHERE id = '.$row->idItem);
				$db->query();
			}
		}

		$post = unserialize(base64_decode($data['d']));

     	$order				= new stdClass();
     	$order->id 			= $idComanda;
     	$order->subtotal 	= $post['subtotal'];
     	$order->shipment 	= $post['shipment'];
     	$order->iva_percent = $post['iva_percent'];
     	$order->iva_total   = $post['iva_total'];
     	$order->re_percent  = $post['re_percent'];
     	$order->re_total    = $post['re_total'];
     	$order->discount 	= $post['discount'];
     	$order->total 		= $post['total'];
     	$order->processor   = $data['processor'];
     	$order->observa     = $data['observa'];

     	//actualitza comanda
     	$db->updateObject('#__botiga_comandes', $order, 'id');

     	//actualitza comanda, estat 2 pendent de pagament
     	$db->setQuery('UPDATE #__botiga_comandes SET status = 2, data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
     	$result = $db->query();

     	//redirect to payment
     	$this->setRedirect('index.php?option=com_botiga&view=checkout&layout=payment&processor='.$data['processor']);
     }

     /**
   * TODO:: esborrar aquesta funció si només es fa servir als plugins
	 * method to send order emails from every payment plugin
	 * @param string $processor
	 * @return mixed
	*/
	public function sendOrderEmails($processor)
     {
       botigaHelper::customLog('User processor: '.$processor.'\n');

     	$db 		= JFactory::getDbo();
     	$session 	= JFactory::getSession();
     	$user 		= JFactory::getUser();
     	$app        = JFactory::getApplication();

     	$botiga_name = botigaHelperPdf::getParameter('botiga_name', '');
     	$botiga_mail = botigaHelperPdf::getParameter('botiga_mail', '');
     	$botiga_iban = botigaHelperPdf::getParameter('botiga_iban', '');
     	$logo		 = botigaHelperPdf::getParameter('botiga_mail_logo', '');

     	$idComanda  = $session->get('idComanda');

 		$db->setQuery('SELECT uniqid FROM #__botiga_comandes WHERE id = '.$idComanda);
 		$uniqid = $db->loadResult();

 		$subject = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_SUBJECT', $uniqid);

 		if($processor == 'Transferencia') {
 			$body_user = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_BODY_BY_BANK_TRANSFER', $botiga_iban);
 		} else {
			$body_user = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_BODY', $botiga_name);
		}

		$body_user    .= "<p>&nbsp;</p>";
		$body_user    .= "<p><img src='".JURI::root().$logo."' alt='".$botiga_name."' width='200' /></p></div>";

		$body_admin    = JText::sprintf('COM_BOTIGA_EMAIL_ADMIN_PROCESS_PAYMENT_BODY', $botiga_name);
		$body_admin   .= "<p>&nbsp;</p>";
		$body_admin   .= "<p><img src='".JURI::root().$logo."' alt='".$botiga_name."' width='200' /></p></div>";

		botigaHelperPdf::genPdf($idComanda, 'F', $idComanda, $uniqid);

    botigaHelper::customLog('User mail: '.$user->email.'\n');
    botigaHelper::customLog('Admin mail: '.$botiga_mail.'\n');

		//mail para el user
		$mail1 = botigaHelperPdf::enviar($subject, $body_user, $user->email, JPATH_ROOT.'/order_'.$uniqid.'.pdf');

		//mail para el admin
		$mail2 = botigaHelperPdf::enviar($subject, $body_admin, $botiga_mail, JPATH_ROOT.'/order_'.$uniqid.'.pdf');

    botigaHelper::customLog('User mail response: '.$mail1.'\n');

    botigaHelper::customLog('Admin mail response: '.$mail2.'\n');

		    unlink(JPATH_ROOT.'/order_'.$uniqid.'.pdf');
     }

	 public function validateCoupon()
	 {
 		$db  = JFactory::getDbo();
 		$app = JFactory::getApplication();
 		$session = JFactory::getSession();

     	$data = $app->input->getArray($_POST);

     	$idComanda  = $session->get('idComanda');

 		$db->setQuery('select * from #__botiga_coupons where coupon = '.$db->quote($data['coupon']).' AND published = 1');
 		$row = $db->loadObject();

 		if(count($row)) {

 			if(strtotime($row->finishDate) < strtotime(date('Y-m-d'))) { //Ja no es vàlid així que despubliquem
   				$db->setQuery('UPDATE #__botiga_coupons SET published = 0 WHERE id = '.$row->id);
   				$db->query();
   				$msg = JText::_('COM_BOTIGA_COUPON_VALIDATE_ERROR_DATE');
 				$type = 'error';

   			} else {

	 			$comanda = new stdClass();
	 			$comanda->id = $idComanda;
	 			$comanda->idCoupon = $row->id;

	 			$db->updateObject('#__botiga_comandes', $comanda, 'id');

	 			$msg = JText::_('COM_BOTIGA_COUPON_VALIDATE_SUCCESS');
	 			$type = 'success';
 			}

 		} else {

 			$msg = JText::_('COM_BOTIGA_COUPON_VALIDATE_ERROR');
 			$type = 'error';

 		}

 		$this->setRedirect('index.php?option=com_botiga&view=checkout', $msg, $type);
	 }

     public function updateQty()
     {
     	$db = JFactory::getDbo();
     	$app = JFactory::getApplication();

     	$id = $app->input->get('id');
     	$type = $app->input->get('type');
     	$qty = $app->input->get('qty', 0);

     	if($qty > 0) {

     		$db->setQuery('UPDATE #__botiga_comandesDetall SET qty = '.$qty.' WHERE id = '.$id);
     		$result = $db->query();

     	} else {

		 	$db->setQuery('select * from #__botiga_comandesDetall where id = '.$id);
		 	$row = $db->loadObject();

		 	if($type == 'minus') {
		 		if($row->qty > 1) {
		 			$db->setQuery('UPDATE #__botiga_comandesDetall SET qty = qty-1 WHERE id = '.$id);
		 			$result = $db->query();
		 		} else {
		 			$db->setQuery('DELETE FROM #__botiga_comandesDetall WHERE id = '.$id);
		 			$result = $db->query();
		 		}
		 	} else {
		 		$db->setQuery('UPDATE #__botiga_comandesDetall SET qty = qty+1 WHERE id = '.$id);
		 			$result = $db->query();
		 	}
     	}

     	if($result) {
     		$msg = JText::_('COM_BOTIGA_QTY_UPDATED_SUCCESS');
     		$type = 'info';
     	} else {
     		$msg = JText::_('COM_BOTIGA_QTY_UPDATED_ERROR');
     		$type = 'error';
     	}

     	$this->setRedirect('index.php?option=com_botiga&view=checkout', $msg, $type);
    }
}
