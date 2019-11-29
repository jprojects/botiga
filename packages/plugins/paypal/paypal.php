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
class plgBotigaPaypal extends JPlugin
{
	var $gateway = 'Paypal';
	
	public function __construct(&$subject, $config = array())
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_botiga_paypal', JPATH_ADMINISTRATOR);
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
	public function onPaymentNew($paymentmethod, $total, $idComanda)
	{
		if($paymentmethod != $this->gateway) return false;
		
		jimport( 'joomla.access.access' );
		
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		
		$groups = JAccess::getGroupsByUser($user->id, false); // grupos a los que pertenece el usuario
		
		$postback = 'index.php?option=com_botiga&view=callback&method=Paypal&userid='.$user->id.'&idComanda='.$idComanda;
		
		$this->params->get('sandbox') == 0 ? $url = 'https://www.paypal.com/cgi-bin/webscr' : $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		
		$company_pay_percent = $this->params->get('company_pay_percent', 0);
		if($company_pay_percent == 1 && in_array(10, $groups)) { //10 usergroup empresa
			$total = number_format(($total / 2), 2, '.', ''); // hacemos el 50% del total
		}

		$data = (object)array(
			'url'			=> $url,
			'merchant'		=> $this->params->get('merchantID',''),
			'postback'		=> JURI::base().$postback,
			'success'		=> JURI::base().$this->params->get('successURL',''),
			'cancel'		=> JURI::base().$this->params->get('cancelURL',''),
			'currency'		=> 'EUR',
			'firstname'		=> $user->username,
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

    	// Creem el rebut de la transacció
    	$rebut 					= new stdClass();
    	$rebut->data 			= date('Y-m-d');
    	$rebut->userid 			= $userid;
    	$rebut->import 			= $amount;
    	$rebut->idComanda 		= $idComanda;
    	$rebut->formaPag 		= 'P';
    	$rebut->payment_status 	= $newStatus; 		
    		
    	$db->insertObject('#__botiga_rebuts', $rebut);
    		
    	//actualitzar estat comanda a pagada (3) o (4) si es empresa i tenim activat el pagament al 50%
    	$company_pay_percent == 1 && in_array(10, $groups) ? $status = 4 : $status = 3;
		$db->setQuery('update #__botiga_comandes set status = '.$status.', data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
		$db->query();
		
		//instanciar el controller base i allà tenir la funció dels emails i la del pdf
		$controller = JControllerLegacy::getInstance('botiga');
		$controller->sendOrderEmails('Paypal');
		
		//tanquem comanda
		$session->set('idComanda', null);
					
		return true;
	}
}
