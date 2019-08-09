<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail administracion@joomlanetprojects.com
 * @website		http://www.joomlanetprojects.com
 *
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

		if($valid  && $uparams->get('allowUserRegistration') == 1) {
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
			$uparams->get('useractivation') == 0 ? $user->block = 0 : $user->block = 1; //segon component users 0 es activació directa
			
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
			
			if($valid) {
			
				//create acj user
				$acjuser            	= new stdClass();
				$acjuser->userid    	= $userid;
			    $acjuser->usergroup 	= $data['type'] == 1 ? 10 : 11;
			    $acjuser->nombre        = $data['nombre'];
			    $acjuser->type			= $data['type'];
			    $acjuser->nom_empresa	= $data['empresa'];
			    $acjuser->mail_empresa	= $data['email1'];
			    $acjuser->telefon		= $data['phone'];
			    $acjuser->adreca		= $data['address'];
			    $acjuser->cp			= $data['cp'];
			    $acjuser->poblacio		= $data['city'];
			    $acjuser->pais   		= $data['pais'];	
			    $acjuser->cif   		= $data['cif'];
			    $acjuser->telefon   	= $data['phone'];	
			    $acjuser->published	    = 1;
			    $acjuser->validate	    = $data['type'] == 1 ? 0 : 1;
			    
			    $db->insertObject('#__botiga_users', $acjuser);    	        	
			    
				//create usergroups
				$group              = new stdClass();
				$group->user_id     = $userid;
				$group->group_id    = 2; //group registered
				$db->insertObject('#__user_usergroup_map', $group);
				
				$group2              = new stdClass();
				$group2->user_id     = $userid;
				$group2->group_id    = $data['type'] == 1 ? 10 : 11;
				$db->insertObject('#__user_usergroup_map', $group2);
				
				//send email to the user with his credentials...
				$mail = JFactory::getMailer();
				$sender[]  	= $config->get('fromname');
				$sender[]	= $config->get('mailfrom');
				
				$botiga_name = botigaHelper::getParameter('botiga_name');
				$botiga_mail = botigaHelper::getParameter('botiga_mail', '');
				
				$mail->setSender( $sender );
				
				if($uparams->get('useractivation') == 1) {	//self activation	
				
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
				
				$msg  = JText::_('COM_BOTIGA_REGISTER_SUCCESS');
				$type = 'success';
			
			} else {
				$msg  = JText::_('COM_BOTIGA_REGISTER_ERROR');
				$type = 'error';
			}
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=register', $msg, $type);
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
