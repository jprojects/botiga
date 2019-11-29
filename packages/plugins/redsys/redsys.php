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
		$lang = JFactory::getLanguage();
		$lang->load('plg_botiga_redsys', JPATH_ADMINISTRATOR);
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

		$lang   = JFactory::getLanguage()->getTag();
		if($lang == 'es-ES') { $idioma = 1; }
		if($lang == 'en-GB') { $idioma = 2; }
		if($lang == 'ca-ES') { $idioma = 3; }
		if($lang == 'fr-FR') { $idioma = 4; }
		if($lang == 'de-DE') { $idioma = 5; }
		if($lang == 'it-IT') { $idioma = 7; }
		if($lang == 'pt-PT') { $idioma = 9; }

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
		$redsys->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $idioma);

		$params = $redsys->createMerchantParameters();
		$signature = $redsys->createMerchantSignature($this->params->get('signature',''));

		$data = (object)array(
			'url'			=> $url,
			'version'		=> $version,
			'signature'		=> $signature,
			'logo'          => $logo,
			'msg'           => JText::_('PLG_BOTIGA_REDSYS_MSG'),
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
					$rebut->formaPag 			= 'C';
					$rebut->payment_status 	= 'C';

					$db->insertObject('#__botiga_rebuts', $rebut);

					//actualitzar estat comanda a pagada (3) o (4) si es empresa i tenim activat el pagament al 50%
					$company_pay_percent == 1 && in_array(10, $groups) ? $status = 4 : $status = 3;
					$db->setQuery('UPDATE #__botiga_comandes SET status = '.$status.', data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
					$db->query();

					//instanciar el controller base i allà tenir la funció dels emails i la del pdf
					//$controller = JControllerLegacy::getInstance('botiga');
					//$controller->sendOrderEmails('Redsys');
					$this->sendOrderEmails($idComanda);

					//tanquem comanda
					$session->set('idComanda', null);
				}
			}
		}

		return true;
	}

	public function sendOrderEmails($idComanda)
	{
			$db 			= JFactory::getDbo();
			$session 	= JFactory::getSession();
			$user 		= JFactory::getUser();
			$app      = JFactory::getApplication();

			require_once (JPATH_ROOT.'/components/com_botiga/helpers/pdf.php');

			$botiga_name = $this->getParameter('botiga_name', '');
			$botiga_mail = $this->getParameter('botiga_mail', '');
			$botiga_iban = $this->getParameter('botiga_iban', '');
			$logo		 		 = $this->getParameter('botiga_mail_logo', '');

			$db->setQuery('SELECT uniqid FROM #__botiga_comandes WHERE id = '.$idComanda);
			$uniqid = $db->loadResult();

			$subject = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_SUBJECT', $idComanda);

			$body_user = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_BODY', $botiga_name);

			$body_user    .= "<p>&nbsp;</p>";
			$body_user    .= "<p><img src='".JURI::root().$logo."' alt='".$botiga_name."' width='200' /></p></div>";

			$body_admin    = JText::sprintf('COM_BOTIGA_EMAIL_ADMIN_PROCESS_PAYMENT_BODY', $botiga_name);
			$body_admin   .= "<p>&nbsp;</p>";
			$body_admin   .= "<p><img src='".JURI::root().$logo."' alt='".$botiga_name."' width='200' /></p></div>";

			botigaHelperPdf::genPdf($idComanda, 'F', $idComanda, $uniqid);

			//mail para el user
			$this->enviar($subject, $body_user, $user->email, JPATH_ROOT.'/order_'.$uniqid.'.pdf');

			//mail para el admin
			$this->enviar($subject, $body_admin, $botiga_mail, JPATH_ROOT.'/order_'.$uniqid.'.pdf');

	 }

	 /**
	 * method to get component parameters
	 * @param string $param
	 * @param mixed $default
	 * @return mixed
	*/
	public function getParameter($param, $default="")
	{
		$params = JComponentHelper::getParams( 'com_botiga' );
		$param = $params->get( $param, $default );

		return $param;
	}

	public function enviar($subject, $body, $email, $attach='')
	{
		$mailer 	= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');

		$botiga_name = $this->getParameter('botiga_name');
		$botiga_logo = $this->getParameter('botiga_logo');

		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;

		$htmlbody = '<div style="width:100%!important;height:100%;background-color:#683b2b;"><table style="width:50%;padding:20px;margin: 0 auto;"><tr><td></td><td style="text-align:center;background-color:#ffcd00;display:block!important;max-width:600px!important;margin:0"><img src="'.JURI::root().$botiga_logo.'" alt="'.$botiga_name.'" style="margin-top:10px;" /><div style="padding:30px;max-width:600px;margin:0"><table width="100%"><tr><td><p style="color:#683b2b;">'.$body.'</p><p></p><a style="color:#683b2b;" href="'.JURI::root().'">'.$botiga_name.'</a></td></tr></table></div></td><td></td></tr></table></div>';

			$mailer->setSender( $sender );
			$mailer->addRecipient( $email );
			$mailer->setSubject( $subject );
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';
			$mailer->setBody( $htmlbody );

			if(attach != '') {
				$mailer->addAttachment($attach);
			}

		return $mailer->Send();
	}
}
