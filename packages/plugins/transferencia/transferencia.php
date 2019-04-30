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
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
    		
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
		
		$html  = "<div class='alert alert-success'>Pedido en gestión.<br>Nos pondremos en contacto para confirmar el pedido.</div>";
		
		$html .= "<div><h1>Ha escogido pago por transferencia</h1></div>";
		$html .= "<table class='table'>";
		$html .= "<tr><td><strong>Importe a ingressar</strong></td><td>{AMOUNT}€</td></tr>";
		$html .= "<tr><td><strong>Indicar el concepto</strong></td><td> la id de la comanda (WEB".$idComanda.")</td></tr>";
		$html .= "<tr><td><strong>IBAN</strong></td><td>".$account."</td></tr>";
		$html .= "</table>";
		$html .= "<p>IMPORTANTE: No se prepara el pedido hasta recibir el pago.</p>";
		$html .= "<p></p>";

		$html = str_replace('{AMOUNT}', $total, $html);
		$html = str_replace('{BENEFICIARI}', $benef, $html);

		$html = '<div>'.$html.'</div>';

		echo $html;
		
		//actualitzar estat comanda a pagada (3)
		$db->setQuery('update #__botiga_comandes set status = 3, data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
		$db->query();
		
		//tanquem comanda
		$session->set('idComanda', null);
	}

	public function onPaymentCallback($paymentmethod, $data, $userid, $idComanda)
	{
		return false;
	}
}
