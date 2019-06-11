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
		
		$db 		= JFactory::getDbo();
		$session 	= JFactory::getSession();
		$jinput 	= JFactory::getApplication()->input;
		$itemid 	= $jinput->get('id');
		$price  	= botigaHelper::getUserPrice($itemid);
		$return 	= base64_decode($jinput->getString('return', 'index.php'));		
		$qty    	= $jinput->getInt('qty', 1);
		
		//si no hi ha comanda creem una de nova...
		$idComanda = $session->get('idComanda', '');
		if($idComanda == '') { $idComanda = $this->setComanda(); }
	
		$db->setQuery('select id, qty from #__botiga_comandesDetall where idItem = '.$itemid.' and idComanda = '.$idComanda);
		$row = $db->loadObject();
		
		$detall = new stdClass();
		
		if(count($row) && $row->qty > 0) {
			$detall->id 		= $row->id;
			$detall->price 		= $price;
			$detall->qty 		= $row->qty + $qty;
			$db->updateObject('#__botiga_comandesDetall', $detall, 'id');
		} else {
			$detall->idComanda 	= $idComanda;
			$detall->idItem 	= $itemid;
			$detall->price 		= $price;
			$detall->qty 		= $qty;
			$db->insertObject('#__botiga_comandesDetall', $detall);
		}
		
		//depen de la url per afegir un paràmetre necessitem ? o &
		$pos = strrpos($return, "?");
		$pos === false ? $link = $return.'?m='.$itemid : $link = $return.'&m='.$itemid;
		
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
		
		$db->setQuery('delete from #__botiga_comandes where id = '.$idComanda);
		$result = $db->query();
		
		$db->setQuery('delete from #__botiga_comandesDetall where idComanda = '.$idComanda);
		$result2 = $db->query();
		
		if($result && $result2) {
			$session->set('idComanda', null);		
			$this->setRedirect('index.php', JText::_('COM_BOTIGA_CART_REMOVED_SUCCESS'), '');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=checkout', JText::_('COM_BOTIGA_CART_REMOVED_ERROR'), 'error');
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
     	
     	$order				= new stdClass();
     	$order->id 			= $idComanda;
     	$order->subtotal 	= $data['subtotal'];
     	$order->shipment 	= $data['shipment'];
     	$order->iva_percent = $data['iva_percent'];
     	$order->iva_total   = $data['iva_total'];     	
     	$order->re_percent  = $data['re_percent'];
     	$order->re_total    = $data['re_total']; 
     	$order->discount 	= $data['discount'];   	
     	$order->total 		= $data['total'];
     	$order->processor   = $data['processor'];
     	$order->observa     = $data['observa'];
     	
     	//actualitza comanda     	
     	$db->updateObject('#__botiga_comandes', $order, 'id');
     	
     	$this->processPayment($data['processor']);
     	
     	//redirect to payment
     	$this->setRedirect('index.php?option=com_botiga&view=checkout&layout=payment&processor='.$data['processor']); 
     }
     
     public function processPayment($processor)
     {	
     	$db 		= JFactory::getDbo();
     	$session 	= JFactory::getSession();
     	$user 		= JFactory::getUser();
     	$app        = JFactory::getApplication();
     	
     	$botiga_name = botigaHelper::getParameter('botiga_name', '');
     	$botiga_mail = botigaHelper::getParameter('botiga_mail', '');
     	$botiga_iban = botigaHelper::getParameter('botiga_iban', '');
     	$logo		 = botigaHelper::getParameter('botiga_logo', '');
     	
     	$data 	    = $app->input->getArray($_POST);
     	
     	$idComanda  = $session->get('idComanda');
     	
     	//actualitza comanda, estat 2 pendent de pagament
     	$db->setQuery('UPDATE #__botiga_comandes SET status = 2, data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
     	$result = $db->query();
     	
     	if($result) {
     	
     		$uniqid = botigaHelper::getComandaData('uniqid', $idComanda);
     		   	
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
			
			$this->genPdf('F', $idComanda, $uniqid); 			
		
			//mail para el user
			$this->enviar($subject, $body_user, $user->email, JPATH_ROOT.'/order_'.$uniqid.'.pdf');
			//mail para el admin
			$this->enviar($subject, $body_admin, $botiga_mail, JPATH_ROOT.'/order_'.$uniqid.'.pdf');
			
			unlink(JPATH_ROOT.'/order_'.$uniqid.'.pdf');
     	}    	
     }
     
     public function genPdf($mode = 'D', $uid = '', $uniqid = '')
	 {
		jimport('fpdf.fpdf');
		jimport('fpdfi.fpdi');
		
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id', $uid);

		$pdf 	= new FPDI();
		
		$pdf->AddPage();
		
		$pdf->setSourceFile(JPATH_COMPONENT.'/assets/pdf/invoice.pdf');
		
		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		define('EURO', chr(128));

		$db = JFactory::getDbo();
		
		$db->setQuery('SELECT c.*, u.mail_empresa as email, u.nom_empresa, u.cif, u.telefon, u.adreca, u.cp, u.poblacio FROM #__botiga_comandes c INNER JOIN #__botiga_users u ON u.userid = c.userid WHERE c.id = '.$id);
		$com = $db->loadObject();
		
		if($uniqid == '') $uniqid = $com->uniqid;

		$db->setQuery('SELECT cd.*, i.name, i.ref as referencia, i.image1 as image, i.description FROM #__botiga_comandesDetall cd INNER JOIN #__botiga_items i ON cd.iditem = i.id WHERE cd.idComanda = '.$id);
		$num_rows = $db->getNumRows();
		$detalls  = $db->loadObjectList();
		
		$height = 10;
		
		$pdf->SetFont('Arial', '', '10');
		
		$pag = 1;
		
		//$pdf->SetXY(175, $height); 
		//$pdf->Cell(25, 5, '1/'.$pag, 0, 0, 'R');
		
		$pdf->SetFont('Arial', 'B', '10');
		
		//metadata
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, utf8_decode('Factura nº '), 0, 0, 'R');
		$pdf->SetXY(170, $height);
		$pdf->Cell(15, 5, utf8_decode('Data : '), 0, 0, 'R');
		
		$pdf->SetFont('Arial', '', '10');
		
		$pdf->SetXY(140, $height); 
		$pdf->Cell(15, 5, $id, 0, 0, 'R');
		$pdf->SetXY(190, $height);
		$pdf->Cell(15, 5, date('d-m-Y', strtotime($com->data)), 0, 0, 'R');
		
		$height += 10;
		
		//user
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, $com->nombre, 0, 0, 'R');
		$height += 5;
		$pdf->SetXY(130, $height); 
		$pdf->Cell(16, 5, $com->adreca, 0, 0, 'R');
		$height += 5;
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, $com->email, 0, 0, 'R');
		$height += 5;
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, $com->cp.' - '.$com->poblacio, 0, 0, 'R');
		
		$height += 10;
		$pdf->SetFont('Arial', 'B', '10');
		
		if($com->status == 4) {
			$pdf->SetTextColor(255, 0, 0);
			$pdf->SetXY(20, $height);  
			$pdf->Cell(30, 5, JText::_('COM_BOTIGA_HISTORY_STATUS_PENDING'), 0, 0, '');$pdf->SetTextColor(255, 0, 0);
			$pdf->SetTextColor(0, 0, 0);
		}
		
		$height += 10;
		
		$pdf->SetXY(20, $height);  
		$pdf->Cell(30, 5, JText::_('COM_BOTIGA_INVOICE_REF'), 0, 0, '');
		$pdf->SetXY(60, $height);  
		$pdf->Cell(90, 5, utf8_decode(JText::_('COM_BOTIGA_INVOICE_DESC')), 0, 0, '');
		$pdf->SetXY(110, $height); 
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_QTY'), 0, 0, 'R');	
		$pdf->SetXY(145, $height);
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_UNIT_PRICE'), 0, 0, '');
		$pdf->SetXY(180, $height); 
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_AMOUNT'), 0, 0, '');
		
		//detall
		$total   = 0;		
		$height  += 10;
		$counter = 0;
		
		foreach($detalls as $detall) {
		
			if($counter == 10) {
			
				$pdf->AddPage();
				$pag++;
				$pdf->setSourceFile(JPATH_COMPONENT.'/assets/pdf/invoice.pdf');
				$tplIdx2 = $pdf->importPage(1);
				$pdf->useTemplate($tplIdx2, 0, 0, 0, 0, true);
			}
			
			$pdf->SetFont('Arial', '', '10');					
	
			$pdf->SetXY(20, $height);  
			$pdf->Cell(30, 5, $detall->referencia, 0, 0, '');	
			//$pdf->Image(JURI::root().$detall->image, 20, ($height-4), 5, 10, 'JPG');		
			$pdf->SetXY(60, $height);  
			$pdf->Cell(90, 5, utf8_decode($detall->name), 0, 0, '');
			$pdf->SetXY(110, $height); 
			$pdf->Cell(20, 5, $detall->qty, 0, 0, 'R');	
			$pdf->SetXY(145, $height);
			$pdf->Cell(20, 5, number_format($detall->price, 2, ',', '.'), 0, 0, '');
			$pdf->SetXY(150, $height); 
			$pdf->Cell(20, 5, '', 0, 0, 'R');
			$pdf->SetXY(180, $height); 
			$pdf->Cell(15, 5, number_format(($detall->price * $detall->qty), 2, ',', '.').EURO, 0, 0, '');
			
			$height += 20;
			$counter++;
		}
		
		$height = 244;
		$pdf->SetFont('Arial', 'B', '10');
		
		$pdf->SetXY(20, $height);  
		$pdf->Cell(30, 5, JText::_('COM_BOTIGA_INVOICE_BASE'), 0, 0, '');
		$pdf->SetXY(50, $height);  
		$pdf->Cell(30, 5, JText::_('COM_BOTIGA_INVOICE_BASE'), 0, 0, '');
		$pdf->SetXY(65, $height); 
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_IVA'), 0, 0, 'R');	
		$pdf->SetXY(90, $height);
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_IMPORT_IVA'), 0, 0, '');
		$pdf->SetXY(115, $height); 
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_RE'), 0, 0, '');
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_IMPORT_RE'), 0, 0, '');
		$pdf->SetXY(155, $height); 
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_DISCOUNTS'), 0, 0, '');
		$pdf->SetXY(180, $height); 
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_TOTAL'), 0, 0, '');
		
		$height += 10;
		$pdf->SetFont('Arial', '', '10');
		
		$pdf->SetXY(20, $height);  
		$pdf->Cell(30, 5, number_format($com->subtotal, 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(50, $height);  
		$pdf->Cell(30, 5, number_format(($com->total/1.21), 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(65, $height); 
		$pdf->Cell(20, 5, $com->iva_percent, 0, 0, 'R');	
		$pdf->SetXY(90, $height);
		$pdf->Cell(20, 5, number_format($com->iva_total, 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(115, $height); 
		$pdf->Cell(15, 5, $com->re_percent, 0, 0, '');
		$pdf->SetXY(130, $height); 
		$pdf->Cell(15, 5, number_format($com->re_total, 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(155, $height); 
		$pdf->Cell(15, 5, number_format($com->discount, 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(180, $height); 
		$pdf->Cell(15, 5, number_format($com->total, 2, ',', '.').EURO, 0, 0, '');

		$pdf->Output('order_'.$uniqid.'.pdf', $mode);
		if($uid == '') { die(); }
		return $uniqid;
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
     
    public function enviar($subject, $body, $email, $attach='') 
	{
		$mailer 	= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');	
	
		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;	
		
        $mailer->setSender( $sender );
        $mailer->addRecipient( $email );
        $mailer->setSubject( $subject );
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody( $body );   
        
        if(attach != '') {
        	$mailer->addAttachment($attach);     
        }
        
		return $mailer->Send();			
	}
}
