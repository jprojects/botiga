<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyleft (C) 2019
 * @license     Licencia Pública General GNU versión 3 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class botigaControllerRegister extends botigaController {

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
	public function &getModel($name = 'Register', $prefix = 'botigaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	public function register() {

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$db 		= JFactory::getDbo();
		$config 	= JFactory::getConfig();
		$app   	   	= JFactory::getApplication();
		$data 	   	= $app->input->post->get('jform', array(), 'array');
		$valid      = true;

		$validation = botigaHelper::getParameter('validation_type', 0); //by default 0 = Joomla system, 1 = Captcha and direct register
		$secretkey 	= botigaHelper::getParameter('captcha_secretkey');

		//Si el captcha es activat
		if($validation == 1) {

			$options['secret'] = $secretkey;
			$options['response'] = $_POST['recaptcha_response'];
	
			$verify = curl_init();
			curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($verify, CURLOPT_POST, true);
			curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($options));
			curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($verify);
			$responseKeys   = json_decode($response, true);
		}

		$uparams = JComponentHelper::getParams( 'com_users' );

		if($data['password1'] !== $data['password2']) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_PASSWORD_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['email1'] !== $data['email2']) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_EMAIL_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['address'] == '' || $data['cp'] == '' || $data['city'] == '' || $data['pais'] == '' || $data['phone'] == '') {
			$msg  = JText::_('COM_BOTIGA_REGISTER_SHIPMENT_MANDATORY');
			$type = 'danger';
			$valid = false;
		}
		if($data['type'] == 1 && ($data['empresa'] == '' && $data['cif'] == '')) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_EMPRESA_DATA_MANDATORY');
			$type = 'danger';
			$valid = false;
		}

		if($valid && $uparams->get('allowUserRegistration') == 1 && ($validation == 0 || ($validation == 1 && $responseKeys['score'] >= 0.6))) {

			//we need the encrypted password
			jimport('joomla.user.helper');

			$password = JUserHelper::hashPassword($data['password1']);

			//create joomla user
			$user                   = new stdClass();
			$user->name             = $data['nombre'];
			$user->username         = $data['email1'];
			$user->password         = $password;
			$user->email            = $data['email1'];
			$user->registerDate     = date('Y-m-d H:i:s');
			$uparams->get('useractivation') == 0 || $validation == 1 ? $user->block = 0 : $user->block = 1; //segon component users 0 es activació directa

			$valid = $db->insertObject('#__users', $user);

			$userid  = $db->insertid();

			//if newsletter is active
			if($data['newsletter'] == 1) {

				require_once(JPATH_COMPONENT.'/assets/php/cm.php');

				$api_key 		= botigaHelper::getParameter('cr_apikey');
				$list_id 		= botigaHelper::getParameter('cr_listid');
				$client_id 		= botigaHelper::getParameter('cr_clientid');
				$campaign_id 	= null;

				//botigaHelper::customLog('Newsletter: '.$api_key);

				//newsletter subscribe...
				$cm = new CampaignMonitor( $api_key, $client_id, $campaign_id, $list_id );
				$cm->subscriberAdd($data['email1'], $data['nombre']);
			}

			$params['metodo_pago'] 		= '';
			$params['aplicar_iva']		= 1;
			$params['re_equiv']    		= 0;
			$params['pago_habitual']    = 0;

			//create botiga user
			$botiga            	  = new stdClass();
			$botiga->userid    		= $userid;
	    $botiga->usergroup 		= $data['type'] == 1 ? 10 : 11; //0 es particular group 11 - 1 es empresa group 10
	    $botiga->nombre       = $data['nombre'].' '.$data['apellidos'];
	    $botiga->type					= $data['type'];
	    $botiga->nom_empresa	= $data['type'] == 1 ? $data['empresa'] : ''; //si no es empresa deixem en blanc
	    $botiga->mail_empresa	= $data['email1'];
	    $botiga->telefon			= $data['phone'];
	    $botiga->adreca				= $data['address'];
	    $botiga->cp						= $data['cp'];
	    $botiga->poblacio			= $data['city'];
	    $botiga->pais   			= $data['pais'];
	    $botiga->cif   				= $data['type'] == 1 ? $data['cif'] : ''; //si no es empresa deixem en blanc
	    $botiga->telefon   		= $data['phone'];
	    $botiga->published	  = 1;
	    $botiga->validate	    = $data['type'] == 1 ? 0 : 1;
	    $botiga->params       = json_encode($params);

		  $db->insertObject('#__botiga_users', $botiga);

			//add address to addresses table
			$address 							= new stdClass();
			$address->adreca			= $data['address'];
	    $address->cp					= $data['cp'];
	    $address->poblacio		= $data['city'];

			$db->insertObject('#__botiga_user_address', $address);

			//create usergroups
			$group              = new stdClass();
			$group->user_id     = $userid;
			$group->group_id    = 2; //group registered
			$db->insertObject('#__user_usergroup_map', $group);

			$group2              = new stdClass();
			$group2->user_id     = $userid;

			//Si el codi postal es de Canaries necessitem un altre grup
			if(($data['cp'] >= 38000 && $data['cp'] <= 38999) || ($data['cp'] >= 35000 && $data['cp'] <= 35999)) {
				$group2->group_id    = $data['type'] == 1 ? 144 : 145; //pertany a 144-negocis i 145-particulars Canaries
			} else {
				$group2->group_id    = $data['type'] == 1 ? 10 : 11; //pertany a 10-negocis i 11-particulars Resta
			}

			$db->insertObject('#__user_usergroup_map', $group2);

			//send email to the user with his credentials...
			$mail = JFactory::getMailer();
			$sender[]  	= $config->get('fromname');
			$sender[]	= $config->get('mailfrom');

			$botiga_name = botigaHelper::getParameter('botiga_name');
			$botiga_mail = botigaHelper::getParameter('botiga_mail', '');

			$mail->setSender( $sender );

			if($validation == 0 && $uparams->get('useractivation') == 1) {	//self activation

				$link 		= JURI::root().'index.php?option=com_botiga&task=register.validateUser&id='.$userid;
				$subject 	= JText::sprintf('COM_BOTIGA_REGISTER_SUBJECT', $botiga_name);
				$body 		= JText::sprintf('COM_BOTIGA_REGISTER_BODY', $data['email1'], $link);
				//Si es empresa enviem el texte per presentació de credenacials
				if($data['type'] == 1) { $body .= '<p>'.JText::sprintf('COM_BOTIGA_REGISTER_FIELD_TYPE_HELP', $botiga_mail).'</p>'; }
				$this->sendEmail($data['email1'], $subject, $body);

			}

			//send email to admin if configured
			if(botigaHelper::getParameter('send_mail_admin_register', 1) == 1) {
				$config 	= JFactory::getConfig();
				$subject 	= JText::sprintf('COM_BOTIGA_REGISTER_ADMIN_SUBJECT', $botiga_name);
				$data['type'] == 1 ? $type = 'empresa' : $type = 'client';
				$body 		= JText::sprintf('COM_BOTIGA_REGISTER_ADMIN_BODY', $botiga_name, $data['nombre'], $data['email1'], $type);
				$this->sendEmail($config->get('mailfrom'), $subject, $body);
			}

			if($validation == 0) {
				$msg  = JText::_('COM_BOTIGA_REGISTER_SUCCESS');
				$link = 'index.php?option=com_botiga&view=register';
			} else {
				$msg  = JText::_('COM_BOTIGA_REGISTER_SUCCESS_VALIDATION_CAPTCHA');
				$link = 'index.php?option=com_users&view=login';
			}

			$type = 'success';

		} else {
			$msg  = JText::_('COM_BOTIGA_REGISTER_ERROR');
			$type = 'error';
			$link = 'index.php?option=com_botiga&view=register';
		}

		$this->setRedirect($link, $msg, $type);
	}

	/**
	 * method to validate a user before login
	 * @return bool
	 */
     public function validateUser()
     {
     	$db   = JFactory::getDbo();
     	$jinput  = JFactory::getApplication()->input;

     	$id  = $jinput->get('id');

     	$db->setQuery('UPDATE #__users SET block = 0 WHERE id = '.$id);
     	if($db->query()) {
     		$msg = JText::_('COM_BOTIGA_REGISTER_VALIDATED_SUCCESS');
     		$type = 'success';
     	} else {
     		$msg = JText::_('COM_BOTIGA_REGISTER_VALIDATED_ERROR');
     		$type = 'success';
     	}

     	$this->setRedirect('index.php?option=com_users&view=login', $msg, $type);
     }
}
