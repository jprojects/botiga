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
class plgBotigapaypal extends JPlugin
{
	var $gateway = 'Paypal';
	
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
		
		$postback = 'index.php?option=com_botiga&view=callback&method=Paypal&userid='.$usuari->id.'&idComanda='.$idComanda;
		
		$this->params->get('sandbox') == 0 ? $url = 'https://www.paypal.com/cgi-bin/webscr' : $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

		$data = (object)array(
			'url'			=> $url,
			'merchant'		=> $this->params->get('merchantID',''),
			'postback'		=> JURI::base().$postback,
			'success'		=> $this->params->get('successURL',''),
			'cancel'		=> $this->params->get('cancelURL',''),
			'currency'		=> 'EUR',
			'firstname'		=> $usuari->username,
			'cmd'			=> '_xclick',
			'recurring'		=> 0,
			'idComanda'		=> $idComanda,
			'amount'		=> $total			
		);

		@ob_start();
		include dirname(__FILE__).'/paypal/form.php';
		$html = @ob_get_clean();

		echo $html;
	}

	public function onPaymentCallback($paymentmethod, $data, $userid, $idComanda)
	{
		// Check if we're supposed to handle this
		if($paymentmethod != $this->gateway) return false;

		// Check the payment_status
		switch($data['payment_status'])
		{
			case 'Canceled_Reversal':
			case 'Completed':
				$newStatus = 'C';
				break;

			case 'Created':
			case 'Pending':
			case 'Processed':
				$newStatus = 'P';
				break;

			case 'Denied':
			case 'Expired':
			case 'Failed':
			case 'Refunded':
			case 'Reversed':
			case 'Voided':
			default:
				$newStatus = 'X';
				break;
		}
		
    	$db = JFactory::getDbo();
    		
    	if($newStatus == 'X') { return; }
    		
    	$newStatus == 'C' ? $pagat = 1 : $pagat = 2;
    	$amount = $data['mc_gross'];

		require_once(JPATH_ROOT.DS.'components'.DS.'com_buuks'.DS.'helpers'.DS.'buuks.php');

		//$cost_pagament_Paypal_fix 	= buuksHelpersBuuks::getParameter('cost_pagament_Paypal_fix');
		//$cost_pagament_Paypal_perc 	= buuksHelpersBuuks::getParameter('cost_pagament_Paypal_perc');

		//$recarrec 			= round(($amount * $cost_pagament_Paypal_perc) / 100,2) + $cost_pagament_Paypal_fix;

    	// Set the payment status to Pending
    	$rebut 					= new stdClass();
    	$rebut->data 			= date('Y-m-d');
    	$rebut->userid 			= $userid;
    	$rebut->import 			= $amount;
		$rebut->recarrec		= 0;
    	$rebut->importambrecarrec 	= $amount;
    	$rebut->pagat 			= $pagat;
    	$rebut->tipus 			= 'A';
    	$rebut->idComanda 		= $idComanda;
    	$rebut->formaPag 		= 'P';
    	$rebut->payment_status 	= $newStatus; 		
    		
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
    	$rebut->formaPag 		= 'P';
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
			BuuksHelpersBuuks::customLog('dintre loop');
			$controller->asociar($detall->idComanda, $detall->id, $detall->idMotherBook, $detall->idEstat, $row->DataEnviament, $item->Urgent);
		}
					
		return true;
	}
}
