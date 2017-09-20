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
			$session->set('idComanda', '');		
			$this->setRedirect('index.php?option=com_botiga&view=botiga', JText::_('COM_BOTIGA_CART_REMOVED_SUCCESS'), '');
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
     	
     		$db->setQuery('select * from #__botiga_users where userid = '.$user->id);
     		$row = $db->loadObject();
     	
     		$db->setQuery('select cd.*, i.name, i.ref, i.image1 from #__botiga_comandesDetall as cd inner join #__botiga_items as i on i.id = cd.itemId where cd.idComanda = '.$idComanda);
     		$items = $db->loadObjectList();
     		
     		$total     = 0;
     		$subject   = "Nuevo pedido, pedido nº ".$idComanda;
			$body      = "Se ha registrado un nuevo pedido en ".botigaHelper::getParameter('botiga_name')." del usuario:";
			$body 	  .= "Usuario: <strong>".$user->username."</strong> :<br>";
			$body 	  .= "Email: <strong>".$user->email."</strong> :<br>";
			$body 	  .= "Teléfono: <strong>".$row->telefon."</strong> :<br>";
			$body 	  .= "Dirección: <strong>".$row->adreca." ".$row->poblacio." ".$row->provincia."</strong> :<br>";
			$body     .= "<table class='table'>";
			
			foreach($items as $item) {
				$item->image1 != '' ? $image = JURI::base().$item->image1 : $image = JURI::base().'images/noimage.png';
				$total     = $item->total;
				$body     .= "<tr><td width='10%'><img src='".$image."' style='max-width:100px;' alt='' /></td>";
				$body     .= "<td width='45%'>".$item->name." - ".$item->ref."</td>";
				$body     .= "<td width='4%'><strong>".$item->price."&euro;</strong></td>";
				$body     .= "<td width='25%' align='center'>".$item->qty."</td>";
				$body     .= "<td width='20%' align='right'><strong>".$item->subtotal."&euro;</strong></td></tr>";
			}
			
			$body .= "<tr><td colspan='5' align='right'>".JText::_('COM_BOTIGA_CHECKOUT_TOTAL')." ".number_format($total, 2)."</td></tr></table>";
			
			$body2 = "<p>Gracias.</p>";			
	
			//mail para el admin
			$this->enviar($subject, $body, botigaHelper::getParameter('botiga_mail'));
			//mail para el user
			$this->enviar($subject, $body.$body2, $user->email);
			
     		$session->clear('idComanda');
     	}
     	
     	$this->setRedirect('index.php?option=com_botiga&view=botiga&layout=success&Itemid=134', $type);     	
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
     
    public function enviar($subject, $body, $email) 
	{
		$mail 		= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');	
	
		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;	
		
        $mail->setSender( $sender );
        $mail->addRecipient( $email );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->IsHTML(true);
        
		return $mail->Send();			
	}
}
