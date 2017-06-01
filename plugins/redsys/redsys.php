<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Joomla User plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	User.joomla
 * @since		1.5
 */
class plgBuuksAmpa_redsys extends JPlugin
{
	var $gateway = 'Redsys';
	
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 *
	 * @param string $paymentmethod
	 * @param JUser $user
	 * @param $saldo
	 * @return string
	 */
	public function onPaymentNew($paymentmethod, $user, $total, $idComanda)
	{
		if($paymentmethod != $this->gateway) return false;
		
		$usuari = JFactory::getUser();
		
		$this->params->get('sandbox') == 0 ? $url = 'https://sis.redsys.es/sis/realizarPago' : $url = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		
		$success  = $this->params->get('successURL', '');
		$cancel   = $this->params->get('cancelURL', '');
		$postback = JURI::base().'index.php?option=com_buuks&view=callback&method=Redsys';
		$postback .= '&userid='.$usuari->id.'&idComanda='.$idComanda.'&amount='.$total;
		$version = "HMAC_SHA256_V1";

		$order   = str_pad($idComanda, 4, '0', STR_PAD_LEFT);
		$order  .= sprintf("%02d",rand(0, 99));
		
		$amount  = $total * 100;
		
		include dirname(__FILE__).'/redsys/apiRedsys.php';
		$myObj = new RedsysAPI;

		$myObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
		$myObj->setParameter("DS_MERCHANT_CURRENCY", 978);
		$myObj->setParameter("DS_MERCHANT_ORDER", $order);
		$myObj->setParameter("DS_MERCHANT_MERCHANTCODE", $this->params->get('code',''));
		$myObj->setParameter("DS_MERCHANT_TERMINAL", 1);
		$myObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", 0);
		$myObj->setParameter("DS_MERCHANT_MERCHANTURL", $postback);
		$myObj->setParameter("DS_MERCHANT_URLOK", $success);      
		$myObj->setParameter("DS_MERCHANT_URLKO", $cancel);
		$myObj->setParameter("DS_MERCHANT_MERCHANTNAME", JFactory::getConfig()->get( 'sitename' )); 
		$myObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", 3);    

		$params = $myObj->createMerchantParameters();
		$signature = $myObj->createMerchantSignature($this->params->get('signature',''));

		$data = (object)array(
			'url'			=> $url,
			'version'		=> $version,
			'signature'		=> $signature,
			'params'		=> $params			
		);

		@ob_start();
		include dirname(__FILE__).'/redsys/form.php';
		$html = @ob_get_clean();

		echo $html;
	}

	public function onPaymentCallback($paymentmethod, $data, $userid, $idComanda)
	{
		// Check if we're supposed to handle this
		if($paymentmethod != $this->gateway) return false;
		
		require_once(JPATH_ROOT.DS.'components'.DS.'com_buuks'.DS.'helpers'.DS.'buuks.php');		
		
		include dirname(__FILE__).'/redsys/apiRedsys.php';
		$myObj = new RedsysAPI;
		
    		$db = JFactory::getDbo();
    		
    		if(isset($data['Ds_Signature'])) {
    		
    		$version 	= $_POST['Ds_SignatureVersion'];
			$params 	= $_POST['Ds_MerchantParameters'];
			$signature 	= $_POST['Ds_Signature'];
			
			$decode 	= $myObj->decodeMerchantParameters($params);
			
			$response 	= $myObj->getParameter('Ds_Response');
			$amount 	= $myObj->getParameter('Ds_Merchant_Amount');
			$order 		= $myObj->getParameter('Ds_Order');
			
			$clave 		= $this->params->get('signature', '');
			
			$signaturaCalculada = $myObj->createMerchantSignatureNotif($clave, $params); 
	
			if($signature === $signaturaCalculada) {
			
				// menos de 100 es un codigo aprobado
				if(intval($response) >= 0 && intval($response) < 100) {
				
			    	$pagat = 1;

					//calculem el recarrec
					//$cost_pagament_Redsys_fix 	= buuksHelpersBuuks::getParameter('cost_pagament_Redsys_fix');
					//$cost_pagament_Redsys_perc 	= buuksHelpersBuuks::getParameter('cost_pagament_Redsys_perc');

					//$recarrec			= round(($_POST['amount'] * $cost_pagament_Redsys_perc) / 100,2) + $cost_pagament_Redsys_fix;
			    		
			    	//aportació de saldo
			    	$rebut 					= new stdClass();
			    	$rebut->data 			= date('Y-m-d');
			    	$rebut->userid 			= $userid;
			    	$rebut->import 			= $_POST['amount'];
					$rebut->recarrec		= 0;
			    	$rebut->importambrecarrec 	= $_POST['amount'];
			    	$rebut->pagat 			= $pagat;
			    	$rebut->tipus 			= 'A';
			    	$rebut->idComanda 		= $idComanda;
			    	$rebut->formaPag 		= 'C';
			    	$rebut->payment_status 	= 'C'; 		
			    		
			    	$db->insertObject('#__buuks_rebuts', $rebut);
			    		
			    	$saldo = abs(BuuksHelpersBuuks::getUserSaldo($userid));
			    		
			    	//prenem tot el saldo
			    	$rebut 					= new stdClass();
			    	$rebut->data 			= date('Y-m-d');
			    	$rebut->userid 			= $userid;
			    	$rebut->import 			= $saldo * -1;
			    	$rebut->importambrecarrec 	= $saldo * -1;
			    	$rebut->pagat 			= $pagat;
			    	$rebut->tipus 			= 'C';
			    	$rebut->idComanda 		= $idComanda;
			    	$rebut->formaPag 		= 'C';
			    	$rebut->payment_status 	= 'C'; 		
			    		
			    	$db->insertObject('#__buuks_rebuts', $rebut);
			    		
			    	//actualitzar estat comanda a pagada (4)
			    	$db->setQuery('update #__buuks_compres set Estat = 4, Data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
			    	$db->query();

					//actualitzar num factura i data factura
					$db->setQuery('SELECT Factura, DataEnviament FROM #__buuks_compres WHERE id = '.$idComanda);
					$row = $db->loadObject();
					if($row->Factura == 1) {
						$db->setQuery('SELECT MAX(Factura_Num) FROM #__buuks_compres');
						$numf = $db->loadResult() + 1;
						$dataf = date('Y-m-d');
						$db->setQuery('update #__buuks_compres set Factura_Num = '.$numf.', Factura_Data = '.$db->quote($dataf).' where id = '.$idComanda);
						$db->query();
					}
					
					//asociem els llibres desde la funcio del controller base de Buuks
					$controller = JControllerLegacy::getInstance('buuks');
					$db->setQuery( 'SELECT * FROM #__buuks_compres_detall WHERE idVendaDetall=0 AND idComanda=' . $idComanda );
					$detalls = $db->loadObjectList();
					foreach($detalls as $detall) {
						$controller->asociar($detall->idComanda, $detall->id, $detall->idMotherBook, $detall->idEstat, $row->DataEnviament, $item->Urgent);
					}
		    	}
		    }
		}
		
		return true;
	}
}
