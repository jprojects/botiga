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
class plgBotigaHabitual extends JPlugin
{

	var $gateway = 'Habitual';

	public function __construct(&$subject, $config = array())
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_botiga_habitual', JPATH_ADMINISTRATOR);
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

		require_once (JPATH_ROOT.'/components/com_botiga/helpers/botiga.php');

		$db 		= JFactory::getDbo();
		$user 		= JFactory::getUser();
		$session 	= JFactory::getSession();
			
		$user_params = json_decode(botigaHelper::getUserData('params', $user->id), true);
			
		$rebut = new stdClass();
		$rebut->data = date('Y-m-d');
		$rebut->userid = $user->id;
		$rebut->import = $total;
		$rebut->idComanda = $idComanda;
		$rebut->formaPag = 'H';
		$rebut->payment_status = 'P';

		$db->insertObject('#__botiga_rebuts', $rebut);

		$total = abs($total);

		$html  = "";

		$html .= "<div id='page-header'><h1>" . 
				JText::_('PLG_BOTIGA_HABITUAL_TITLE') . 
				"</h1></div>";
		$html .= "<table class='table'>";
		$html .= "<tr><td style='border-bottom: 1px solid white;'><strong>".JText::_('PLG_BOTIGA_HABITUAL_AMOUNT')."</strong></td>" .
			"<td style='border-bottom: 1px solid white;'>{AMOUNT}€</td></tr>";
		$html .= "<tr><td style='border-bottom: 1px solid white;'><strong>".JText::_('PLG_BOTIGA_HABITUAL_PAYMENT_METHOD')."</strong></td>" .
			"<td style='border-bottom: 1px solid white;'>{PAYMENT_METHOD}</td></tr>";
		$html .= "</table>";
		$html .= "<p style='font-style:italic;'>".JText::_('PLG_BOTIGA_HABITUAL_MSG')."</p>";
		$html .= "<p></p>";

		$html = str_replace('{AMOUNT}', $total, $html);
		$html = str_replace('{PAYMENT_METHOD}', $user_params['pago_habitual_desc'], $html);

		$html = '<div>'.$html.'</div>';

		echo $html;

		//actualitzar estat comanda a pagada (3)
		$db->setQuery('update #__botiga_comandes set status = 3, data = '.$db->quote(date('Y-m-d H:i:s')).' WHERE id = '.$idComanda);
		$db->query();

		$this->sendOrderEmails($idComanda);

		//tanquem comanda
		$session->set('idComanda', null);
	}

	public function onPaymentCallback($paymentmethod, $data, $userid, $idComanda)
	{
		return false;
	}

	public function sendOrderEmails($idComanda)
	{
			$db 		= JFactory::getDbo();
			$session 	= JFactory::getSession();
			$user		= JFactory::getUser();
			$app		= JFactory::getApplication();

			require_once (JPATH_ROOT.'/components/com_botiga/helpers/pdf.php');

			$botiga_name = $this->getParameter('botiga_name', '');
			$botiga_mail = $this->getParameter('botiga_mail', '');
			$botiga_iban = $this->getParameter('botiga_iban', '');
			$logo		 		 = $this->getParameter('botiga_mail_logo', '');

			$db->setQuery('SELECT uniqid FROM #__botiga_comandes WHERE id = '.$idComanda);
			$uniqid = $db->loadResult();

			$subject = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_SUBJECT', $idComanda);

			$body_user = JText::sprintf('COM_BOTIGA_EMAIL_USER_PROCESS_PAYMENT_BODY_BY_HABITUAL', $botiga_iban);

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
