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
class plgBotigaRedsys extends JPlugin
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
	public function onPaymentNew($paymentmethod, $total, $idComanda)
	{
		if($paymentmethod != $this->gateway) return false;
		
		jimport( 'joomla.access.access' );
		
		$user   = JFactory::getUser();
		$groups = JAccess::getGroupsByUser($user->id, false); // grupos a los que pertenece el usuario
		
		$company_pay_percent = $this->params->get('company_pay_percent', 0);
		if($company_pay_percent == 1 && in_array(10, $groups)) { //10 usergroup empresa
			$total = number_format(($total / 2), 2, '.', ''); // hacemos el 50% del total
		}
		
		$this->params->get('sandbox') == 0 ? $url = 'https://sis.redsys.es/sis/realizarPago' : $url = 'https://sis-t.redsys.es:25443/sis/realizarPago';
		
		$success  = JURI::base().$this->params->get('successURL', '');
		$cancel   = JURI::base().$this->params->get('cancelURL', '');
		
		$base     = str_replace('https', 'http', JURI::base());
		$postback = JURI::base().'index.php?option=com_botiga&view=callback&method='.$this->gateway;
		$postback .= '&userid='.$user->id.'&idComanda='.$idComanda.'&amount='.$total;
		$version = "HMAC_SHA256_V1";
		
		$logo    = JURI::base().$this->params->get('logo', '');

		$order   = str_pad($idComanda, 4, '0', STR_PAD_LEFT);
		$order  .= sprintf("%02d",rand(0, 99));
		
		$amount  = $total * 100;
		
		include dirname(__FILE__).'/redsys/apiRedsys.php';
		$redsys = new RedsysAPI;

		$redsys->setParameter("DS_MERCHANT_AMOUNT", $amount);
		$redsys->setParameter("DS_MERCHANT_CURRENCY", $this->params->get('currency',''));
		$redsys->setParameter("DS_MERCHANT_ORDER", $order);
		$redsys->setParameter("DS_MERCHANT_MERCHANTCODE", $this->params->get('code',''));
		$redsys->setParameter("DS_MERCHANT_TERMINAL", $this->params->get('terminal',''));
		$redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", 0);
		$redsys->setParameter("DS_MERCHANT_MERCHANTURL", $postback);
		$redsys->setParameter("DS_MERCHANT_URLOK", $success);      
		$redsys->setParameter("DS_MERCHANT_URLKO", $cancel);
		$redsys->setParameter("DS_MERCHANT_MERCHANTNAME", JFactory::getConfig()->get( 'sitename' )); 
		$redsys->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", 3);    

		$params = $redsys->createMerchantParameters();
		$signature = $redsys->createMerchantSignature($this->params->get('signature',''));

		$data = (object)array(
			'url'			=> $url,
			'version'		=> $version,
			'signature'		=> $signature,
			'logo'          => $logo,
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
		
		require_once(JPATH_ROOT.DS.'components'.DS.'com_botiga'.DS.'helpers'.DS.'botiga.php');	
		
		//botigaHelper::customLog('entrem a la funció');
		
		include dirname(__FILE__).'/redsys/apiRedsys.php';
		$redsys = new RedsysAPI;
	
		$db = JFactory::getDbo();
		$session = JFactory::getSession();
		
		if(isset($data['Ds_Signature'])) {
		
			$version 	= $_POST['Ds_SignatureVersion'];
			$params 	= $_POST['Ds_MerchantParameters'];
			$signature 	= $_POST['Ds_Signature'];
			
			$decode 	= $redsys->decodeMerchantParameters($params);
			
			$response 	= $redsys->getParameter('Ds_Response');
			$amount 	= $redsys->getParameter('Ds_Merchant_Amount');
			$order 		= $redsys->getParameter('Ds_Order');
			
			$clave 		= $this->params->get('signature', '');
			
			$signaturaCalculada = $redsys->createMerchantSignatureNotif($clave, $params); 
	
			if($signature === $signaturaCalculada) {
			
				//botigaHelper::customLog('validació signatura');
			
				// menos de 100 es un codigo aprobado
				if(intval($response) <= 99) {
				
					//botigaHelper::customLog('resposta vàlida');															
						
					$rebut 					= new stdClass();
					$rebut->data 			= date('Y-m-d');
					$rebut->userid 			= $userid;
					$rebut->import 			= $_POST['amount'];
					$rebut->idComanda 		= $idComanda;
					$rebut->formaPag 		= 'C';
					$rebut->payment_status 	= 'C'; 		
						
					$db->insertObject('#__botiga_rebuts', $rebut);				
						
					//actualitzar estat comanda a pagada (3) o (4) si es empresa i tenim activat el pagament al 50%
					$company_pay_percent == 1 && in_array(10, $groups) ? $status = 4 : $status = 3;
					$db->setQuery('UPDATE #__botiga_comandes SET status = '.$status.', data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
					$db->query();
					
					//instanciar el controller base i allà tenir la funció dels emails i la del pdf
					$controller = JControllerLegacy::getInstance('botiga');
					$controller->sendOrderEmails('Redsys');
					
					//tanquem comanda
					$session->set('idComanda', null);										
				}
			}
		}
		
		return true;
	}
}
