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
	
	public function setItem() {
		
		$db = JFactory::getDbo();
		$session = JFactory::getSession();

		$jinput = JFactory::getApplication()->input;
		$itemid = $jinput->get('id');
		$price  = botigaHelper::getUserPrice($itemid);
		$return = base64_decode($jinput->getString('return', 'index.php'));
		
		$qty    = $jinput->getInt('qty', 1);
		
		//si no hi ha comanda creem una de nova...
		$idComanda = $session->get('idComanda', '');
		if($idComanda == '') { $idComanda = $this->setComanda(); }
	
		$db->setQuery('select id, qty from #__botiga_comandesDetall where idItem = '.$itemid.' and idComanda = '.$idComanda);
		$row = $db->loadObject();
		
		$detall = new stdClass();
		
		if(count($row) && $row->qty > 0) {
			$detall->id 		= $row->id;
			$detall->qty 		= $row->qty + $qty;
			$db->updateObject('#__botiga_comandesDetall', $detall, 'id');
		} else {
			$detall->idComanda 	= $idComanda;
			$detall->idItem 	= $itemid;
			$detall->price 		= $price;
			$detall->qty 		= $qty;
			$db->insertObject('#__botiga_comandesDetall', $detall);
		}
		
		$this->setRedirect($return);
	}
	
	public function setItemAjax() {
		
		$db = JFactory::getDbo();
		$session = JFactory::getSession();

		$jinput = JFactory::getApplication()->input;
		$itemid = $jinput->get('id');
		$price  = botigaHelper::getUserPrice($itemid);
		
		$qty    = $jinput->getInt('qty', 1);
		
		//si no hi ha comanda creem una de nova...
		$idComanda = $session->get('idComanda', '');
		if($idComanda == '') { $idComanda = $this->setComanda(); }
	
		$db->setQuery('select cd.* from #__botiga_comandesDetall cd where cd.idItem = '.$itemid.' and cd.idComanda = '.$idComanda);
		$row = $db->loadObject();
		
		$detall = new stdClass();
		
		if(count($row) && $row->qty > 0) {
			$detall->id 		= $row->id;
			$detall->qty 		= $row->qty + $qty;
			$db->updateObject('#__botiga_comandesDetall', $detall, 'id');
		} else {
			$detall->idComanda 	= $idComanda;
			$detall->idItem 	= $itemid;
			$detall->price 		= $price;
			$detall->qty 		= $qty;
			$db->insertObject('#__botiga_comandesDetall', $detall);
		}
		
		$db->setQuery('select name, image1 from #__botiga_items where id = '.$itemid);
		$item = $db->loadObject();
		
		$result 			= array();
		$result['id'] 		= $detall->id;
		$result['qty'] 		= $detall->qty;
		$result['idItem']   = $itemid;
		$result['nombre']   = $item->name;
		$result['imagen'] 	= $item->image1;
		$result['price'] 	= $price;
		
		echo json_encode($result);
	}
	
	public function removeItem() {
	
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
		$id = $app->input->get('id');
		
		$db->setQuery('delete from #__botiga_comandesDetall where id = '.$id);
		$result = $db->query();
		
		if($result) {	
			$this->setRedirect('index.php?option=com_botiga&view=checkout&Itemid=134', JText::_('COM_BOTIGA_ITEM_REMOVED_SUCCESS'), 'success');
		} else {
			$this->setRedirect('index.php?option=com_botiga&view=checkout&Itemid=134', JText::_('COM_BOTIGA_ITEMT_REMOVED_ERROR'), 'error');
		}
	}
	
	public function setComanda() {
	
		$db 		= JFactory::getDbo();
		$session 	= JFactory::getSession();
		$user 		= JFactory::getUser();
		
		$comanda = new stdClass();
		$comanda->uniqid = substr(uniqid('', true), -5);
		$comanda->userid = $user->id;
		$comanda->data = date('Y-m-d H:i:s');
		$comanda->status = 1;
		
		$db->insertObject('#__botiga_comandes', $comanda);
		
		$idComanda = $db->insertid();
		
		$session->set('idComanda', $idComanda);
		
		return $idComanda;
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
			$this->setRedirect('index.php?option=com_botiga&view=checkout&Itemid=134', JText::_('COM_BOTIGA_CART_REMOVED_ERROR'), 'error');
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
     	
     	$subtotal 	= $data['subtotal'];
     	$shipment 	= $data['shipment'];
     	$iva_percent = botigaHelper::getParameter('iva', '21');
     	$iva_total  = ($iva_percent / 100) * ($subtotal + $shipment);
     	$total 		= $data['total'];
     	$processor  = $data['processor'];
     	$observa    = $data['observa'];
     	
     	//actualitza comanda
     	$db->setQuery('update #__botiga_comandes set subtotal = '.$db->quote($subtotal).', shipment = '.$db->quote($shipment).', iva_percent = '.$db->quote($iva_percent).', iva_total = '.$db->quote($iva_total).', total = '.$db->quote($total).', observa = '.$db->quote($observa).' where id = '.$idComanda);
     	$db->query();
     	
     	$this->processPayment();
     	
     	//redirect to payment
     	$this->setRedirect('index.php?option=com_botiga&view=checkout&layout=payment&Itemid=134&processor='.$processor); 
     }
     
     public function processPayment()
     {	
     	$db 		= JFactory::getDbo();
     	$session 	= JFactory::getSession();
     	$user 		= JFactory::getUser();
     	$app        = JFactory::getApplication();
     	
     	$data 	    = $app->input->getArray($_POST);
     	
     	$idComanda  = $session->get('idComanda');
     	
     	//actualitza comanda
     	$db->setQuery('update #__botiga_comandes set status = 2 where id = '.$idComanda);
     	$result = $db->query();
     	
     	if($result) {
     		
     		//ToDo:: add subject and text to component config   	
     		$subject   = "Nuevo pedido, pedido nº ".botigaHelper::getComandaData('uniqid', $idComanda);
			$body      = "<div style='font-family:Arial;font-size:7px;'>Vuestro pedido está en gestión, nos pondremos en contacto para confirmar stock y plazo de entrega.<br>";
			$body     .= "Adjuntamos factura proforma."; 
			$body     .= "<p>*Portes pendientes de valoración para envíos fuera de España a excepción de las Islas Canarias.</p>"; 
			$body     .= "Gracias por confiar en DICOHOTEL<p></p>";   
			$body     .= "<span style='font-family:Arial;font-size:7px;'><strong>ESTEL NOGUER</strong></span><br>"; 
			$body     .= "<span style='font-family:Arial;font-size:7px;'><strong>MARKETING & COMUNICATION</strong></span>"; 
			$body     .= "<p><img src='http://www.dicohotel.com/images/Firma_Dicohotel.jpg' alt='' /></p></div>";
			
			$body2     = "<div style='font-family:Arial;font-size:7px;'>Ha llegado un nuevo pedido desde la web de Dicohotel.<br>";
			$body2    .= "Adjuntamos factura proforma.</div>"; 
			
			$this->genPdf('F', $idComanda); 			
		
			//mail para el user
			$this->enviar($subject, $body, $user->email, $idComanda);
			//mail para el admin
			$this->enviar($subject, $body2, botigaHelper::getParameter('botiga_mail'), $idComanda);
			
			unlink(JPATH_ROOT.'/proforma_'.$idComanda.'.pdf');
     	}    	
     }
     
     public function genPdf($mode = 'D', $uid = '')
	 {
		jimport('fpdf.fpdf');
		jimport('fpdfi.fpdi');
		
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id', $uid);

		$pdf 	= new FPDI();
		
		$pdf->AddPage();
		
		$pdf->setSourceFile(JPATH_COMPONENT.DS.'assets'.DS.'pdf'.DS.'invoice.pdf');
		
		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		define('EURO', chr(128));

		$db = JFactory::getDbo();
		$db->setQuery('SELECT c.*, u.mail_empresa as email, u.nom_empresa, u.cif, u.telefon, u.adreca, u.cp, u.poblacio, u.metodo_pago FROM #__botiga_comandes c INNER JOIN #__botiga_users u ON u.userid = c.userid WHERE c.id = '.$id);
		$com = $db->loadObject();

		$db->setQuery('SELECT cd.*, i.name, i.ref as referencia, i.image1 as image, i.description FROM #__botiga_comandesDetall cd INNER JOIN #__botiga_items i ON cd.iditem = i.id WHERE cd.idComanda = '.$id);
		$db->query();
		$num_rows = $db->getNumRows();
		$detalls  = $db->loadObjectList();
		
		$uniqid = $com->uniqid;
		
		$height = 38;
		
		$pdf->SetFont('Arial', '', '10');
		
		if($num_rows >= 12) { $pag = 2; } else { $pag = 1; }
		
		$pdf->SetXY(175, $height); 
		$pdf->Cell(25, 5, '1/'.$pag, 0, 0, 'R');
		
		$height = 45;
		
		//user
		$pdf->SetXY(170, $height); 
		$pdf->Cell(15, 5, $com->telefon, 0, 0, 'R');
		$height += 4;
		$pdf->SetXY(121, $height); 
		$pdf->Cell(16, 5, $com->cif, 0, 0, 'R');
		$pdf->SetXY(170, $height); 
		$pdf->Cell(15, 5, $com->email, 0, 0, 'R');
		
		$height = 60;
		
		//adreça
		$pdf->SetXY(107, $height); 
		$pdf->Cell(15, 5, $com->nom_empresa, 0, 0);
		$height += 4;
		$pdf->SetXY(107, $height); 
		$pdf->Cell(15, 5, $com->adreca, 0, 0);
		$height += 4;
		$pdf->SetXY(107, $height); 
		$pdf->Cell(15, 5, $com->cp.' '.$com->poblacio, 0, 0);
		
		//metadata
		$height = 71;
		$pdf->SetXY(35, $height);
		$pdf->Cell(15, 5, $com->observa, 0, 0, 'R');
		$pdf->SetXY(50, $height);
		$pdf->Cell(15, 5, $uniqid, 0, 0, 'R');
		$pdf->SetXY(80, $height);
		$pdf->Cell(15, 5, date('d/m/Y', strtotime($com->data)), 0, 0, 'R');
		
		//detall
		$total   = 0;		
		$height  = 85;
		$counter = 0;
		
		foreach($detalls as $detall) {
		
			if($counter == 5) {
				$pdf->AddPage();
				$pdf->setSourceFile(JPATH_COMPONENT.DS.'assets'.DS.'pdf'.DS.'albaran.pdf');
				$tplIdx2 = $pdf->importPage(1);
				$pdf->useTemplate($tplIdx2, 0, 0, 0, 0, true);
				$pdf->SetFont('Arial', '', '10');
				
				$height = 38;
				$pdf->SetXY(175, $height); 
				$pdf->Cell(25, 5, '2/'.$pag, 0, 0, 'R');
				
				$height = 71;		
				
				$pdf->SetXY(35, $height);
				$pdf->Cell(15, 5, $com->observa, 0, 0, 'R');
				$pdf->SetXY(50, $height);
				$pdf->Cell(15, 5, $uniqid, 0, 0, 'R');
				$pdf->SetXY(80, $height);
				$pdf->Cell(15, 5, date('d-m-Y', strtotime($com->data)), 0, 0, 'R');
				$height  = 85;
			}
			
			
	
			$pdf->SetXY(20, $height);  
			$pdf->Cell(30, 5, $detall->referencia, 0, 0, '');
			$new_height = $height+4;
			$pdf->SetXY(20, $new_height);  
			$pdf->Cell(30, 5, $pdf->WriteHTML('<img src="'.$detall->image.'" alt="" width="50" height="50" />'), 0, 0, '');
			
			$pdf->SetXY(60, $height);  
			$pdf->Cell(90, 5, utf8_decode($detall->name), 0, 0, '');
			$pdf->SetXY(110, $height); 
			$pdf->Cell(20, 5, $detall->qty, 0, 0, 'R');	
			$pdf->SetXY(145, $height);
			$pdf->Cell(20, 5, number_format($detall->price, 2, ',', '.'), 0, 0, '');
			$pdf->SetXY(150, $height); 
			$pdf->Cell(20, 5, '', 0, 0, 'R');
			$pdf->SetXY(172, $height); 
			$pdf->Cell(15, 5, number_format(($detall->price * $detall->qty), 2, ',', '.').EURO, 0, 0, '');
			
			$height += 20;
			$counter++;
		}
		
		$height = 244;
		$pdf->SetFont('Arial', 'B', '10');
		$pdf->SetXY(55, $height);  
		$pdf->Cell(30, 5, number_format($com->subtotal, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(75, $height); 
		$pdf->Cell(30, 5, $com->iva_percent, 0, 0, 'R');
		$pdf->SetXY(95, $height);
		$pdf->Cell(30, 5, number_format($com->iva_total, 2, ',', '.').EURO, 0, 0, 'R');

		$pdf->SetXY(163, $height); 
		$pdf->Cell(30, 5, number_format(($com->total), 2, ',', '.').EURO, 0, 0, 'R');
		
		$height += 10;
		
		$pdf->SetXY(35, $height); 
		$com->metodo_pago == '' ? $metodo_pago = 'Pago habitual' : $metodo_pago = $com->metodo_pago;
		$pdf->Cell(30, 5, utf8_decode($metodo_pago), 0, 0, 'R');
		
		$height += 12;
		
		$pdf->SetXY(125, $height); 
		$pdf->Cell(30, 5, utf8_decode($com->observa), 0, 0, 'R');

		$pdf->Output('proforma_'.$id.'.pdf', $mode);
		if($uid == '') { die(); }
	 }
     
     public function updateQty()
     {
     	$db = JFactory::getDbo();
     	$app = JFactory::getApplication();
     	
     	$id = $app->input->get('id');
     	$type = $app->input->get('type');
     	
     	$db->setQuery('select * from #__botiga_comandesDetall where id = '.$id);
     	$row = $db->loadObject();
     	
     	if($type == 'minus') {
     		if($row->qty > 0) {
     			$db->setQuery('update #__botiga_comandesDetall set qty = qty-1 where id = '.$id);
     			$db->query();
     		}
     	} else {
     		$db->setQuery('update #__botiga_comandesDetall set qty = qty+1 where id = '.$id);
     		$db->query();
     	}
     	
     	$db->setQuery('select * from #__botiga_comandesDetall where id = '.$id);
     	$result = $db->loadObject();
     	
     	if($result) {
     		$msg = JText::_('COM_BOTIGA_QTY_UPDATED_SUCCESS');
     		$type = 'info';
     	} else {
     		$msg = JText::_('COM_BOTIGA_QTY_UPDATED_ERROR');
     		$type = 'error';
     	}
     	
     	$this->setRedirect('index.php?option=com_botiga&view=checkout&Itemid=134', $msg, $type);     	     	
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
        	$mailer->addAttachment(JPATH_ROOT.'/proforma_'.$attach.'.pdf');     
        }
        
		return $mailer->Send();			
	}
}
