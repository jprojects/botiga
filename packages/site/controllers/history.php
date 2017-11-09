<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright   Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@afi.cat
 * @website		http://www.afi.cat
 *
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class botigaControllerHistory extends botigaController {
    
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
	public function &getModel($name = 'History', $prefix = 'botigaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	public function saveConfig()
	{
		$db    = JFactory::getDbo();
		$app   = JFactory::getApplication();
		$user  = JFactory::getUser();
		$data  = $app->input->post->get('jform', array(), 'array');
		
		$conf                   = new stdClass();
		$conf->metodo_pago      = $data['processor'];
		$conf->userid			= $user->id;
			
		$valid = $db->updateObject('#__botiga_users', $conf, 'userid');
		
		if($valid) {
			$msg = JText::_('La configuración se guardó con éxito.');
			$type = '';
		} else {
			$msg = JText::_('Hubo un error al tratar de guardar la configuración.');
			$type = 'error';
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=history&Itemid=117', $msg, $type);
	}
}
