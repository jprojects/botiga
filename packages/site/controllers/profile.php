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

class botigaControllerProfile extends botigaController {

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
	public function &getModel($name = 'Profile', $prefix = 'botigaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	public function profile() {
	
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$db 		= JFactory::getDbo();
		$app   	   	= JFactory::getApplication();
		$data 	   	= $app->input->post->get('jform', array(), 'array');
		$valid      = true;
		
		if($data['password1'] !== $data['password2']) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_PASSWORD_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['email2'] != '' && ($data['email1'] !== $data['email2'])) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_EMAIL_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}

		if($valid) {	
		
			$mail = false;
			$pass = false;	
		
			//create joomla user
			$user                   = new stdClass();
			$user->id               = $data['id'];
			
			if($data['email2'] != '' && ($data['email1'] == $data['email2'])) {
				$user->username         = $data['email1'];
				$user->email            = $data['email1'];
				$mail = true;
			}
			if($data['password2'] != '' && ($data['password1'] == $data['password2'])) {
				//we need the encrypted password
				jimport('joomla.user.helper');
				$password = JUserHelper::hashPassword($data['password1']);
				$user->password         = $password;
				$pass = true;
			}
			
			if($mail || $pass) { $valid = $db->updateObject('#__users', $user, 'id'); }
			
			if($valid) {
			
				//create botiga user
				$acjuser            	= new stdClass();
				$acjuser->userid    	= $data['id'];
			    $acjuser->nom_empresa	= $data['empresa'];
			    $acjuser->mail_empresa	= $data['email1'];
			    $acjuser->telefon		= $data['phone'];
			    $acjuser->cargo			= $data['cargo'];
			    $acjuser->adreca		= $data['address'];
			    $acjuser->cp			= $data['zip'];
			    $acjuser->poblacio		= $data['city'];
			    $acjuser->pais   		= $data['pais'];	
			    $acjuser->cif   		= $data['cif'];	
			     $acjuser->cp   		= $data['cp'];
			    
			    $db->updateObject('#__botiga_users', $acjuser, 'userid');    	        	
			
				$msg  = JText::_('El perfil se ha guardado correctamente.');
				$type = '';
			
			} else {
				$msg  = JText::_('Hubo un error al guardar el perfil, intentelo de nuevo.');
				$type = 'error';
			}
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=profile&Itemid=121', $msg, $type);
	}
}
