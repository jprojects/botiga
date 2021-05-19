<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
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

		if($data['password2'] != '' && ($data['password1'] !== $data['password2'])) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_PASSWORD_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['email2'] != '' && ($data['email1'] !== $data['email2'])) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_EMAIL_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['pais'] == '') {
			$msg  = JText::_('COM_BOTIGA_REGISTER_SHIPMENT_MANDATORY');
			$type = 'danger';
			$valid = false;
		}

		if($valid) {

			$mail = false;
			$pass = false;

			//create joomla user
			$user                   = new stdClass();
			$user->id               = $data['userid'];
			$user->username         = $data['nombre'];

			if($data['email2'] != '' && ($data['email1'] == $data['email2'])) {
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

			//update botiga user
			$query = "SELECT params FROM #__botiga_users WHERE userid=" . $data['userid'];
			$db->setQuery($query);
			$params = $db->loadResult();
			$params = json_decode($params,true);

			$params['metodo_pago'] = $data['metodo_pago'];
			$params['re_equiv']    = $data['re_equiv'];

			unset($data['metodo_pago'], $data['re_equiv']);

			//edit botiga user
			$acjuser            	= new stdClass();
			$acjuser->userid    	= $data['userid'];
			$acjuser->type				= $data['type'];
	    $acjuser->nom_empresa	= $data['empresa'];
	    $acjuser->nombre			= $data['nombre'];
	    $acjuser->mail_empresa	= $data['email1'];
	    $acjuser->telefon			= $data['phone'];
	    $acjuser->pais   			= $data['pais'];
	    $acjuser->cif   			= $data['cif'];
	    $acjuser->params 			= json_encode($params);

		  $db->updateObject('#__botiga_users', $acjuser, 'userid');

			//save addresses
			$db->setQuery('DELETE FROM #__botiga_user_address WHERE userid = '.$user->id);
			$db->query();

			$i = 0;
			foreach($_POST as $post) {

				$address  					= new stdClass();
				$address->userid 		= $data['userid'];
				$address->adreca 		= $_POST['address_'.$i.''];
				$address->cp 				= $_POST['zip_'.$i.''];
				$address->poblacio 	= $_POST['city_'.$i.''];
				$address->activa 	  = 0;

				if($address->adreca != '' && $address->cp != '' && $address->poblacio != '') {
					$db->insertObject('#__botiga_user_address', $address);
				}

				$i++;
			}

			$msg  = JText::_('COM_BOTIGA_PROFILE_SUCCESS');
			$type = 'success';

		} else {
			$msg  = JText::_('COM_BOTIGA_PROFILE_ERROR');
			$type = 'error';
		}

		$this->setRedirect('index.php?option=com_botiga&view=history', $msg, $type);
	}
}
