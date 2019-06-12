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
class plgBotigaTransferencia extends JPlugin
{

	var $gateway = 'Transferencia';
	
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
	public function onPaymentNew($paymentmethod, $total, $idComanda)
	{
		if($paymentmethod != $this->gateway) return false;
		
		jimport( 'joomla.access.access' );
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		
		$groups = JAccess::getGroupsByUser($user->id, false); // grupos a los que pertenece el usuario
		
		$company_pay_percent = $this->params->get('company_pay_percent', 0);
		if($company_pay_percent == 1 && in_array(10, $groups)) { //10 usergroup empresa
			$total = number_format(($total / 2), 2, '.', ''); // hacemos el 50% del total
		}
    		
		$rebut = new stdClass();
		$rebut->data = date('Y-m-d');
		$rebut->userid = $user->id;
		$rebut->import = $total;
		$rebut->idComanda = $idComanda;
		$rebut->formaPag = 'T';
		$rebut->payment_status = 'P'; 		
		
		$db->insertObject('#__botiga_rebuts', $rebut);
		
		$total = abs($total);
			
		$account = $this->params->get('account','');
		$benef   = $this->params->get('beneficiari','');
		
		$html  = "";
		
		$html .= "<div><h1>Ha escogido pago por transferencia</h1></div>";
		$html .= "<table class='table'>";
		$html .= "<tr><td><strong>Importe a ingressar</strong></td><td>{AMOUNT}€</td></tr>";
		$html .= "<tr><td><strong>Indicar el concepto</strong></td><td> la id de la comanda (".$idComanda.")</td></tr>";
		$html .= "<tr><td><strong>IBAN</strong></td><td>".$account."</td></tr>";
		$html .= "</table>";
		$html .= "<p class='text-danger'>IMPORTANTE: No se prepara el pedido hasta recibir el pago.</p>";
		$html .= "<p></p>";

		$html = str_replace('{AMOUNT}', $total, $html);
		$html = str_replace('{BENEFICIARI}', $benef, $html);

		$html = '<div>'.$html.'</div>';

		echo $html;
		
		//actualitzar estat comanda a pagada (3) o (4) si es empresa i tenim activat el pagament al 50%
    	$company_pay_percent == 1 && in_array(10, $groups) ? $status = 4 : $status = 3;
		$db->setQuery('UPDATE #__botiga_comandes SET status = '.$status.', data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
		$db->query();
		
		//tanquem comanda
		$session->set('idComanda', null);
	}

	public function onPaymentCallback($paymentmethod, $data, $userid, $idComanda)
	{
		return false;
	}
}
