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

// import Joomla controllerform library
jimport('joomla.application.component.controlleradmin');

/**
 * Items Controller
 */
class botigaControllerDocs extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Doc', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	} 
	
	public function deleteFiles()
	{
		$db = JFactory::getDbo();
		
		$ids = $this->input->get('cid', array(), 'array');
		
		foreach($ids as $id) {
		
			$db->setQuery('SELECT filename FROM #__botiga_documents WHERE id = '.$id);	
			$filename = $db->loadResult();
			@unlink(JPATH_ROOT.'/media/com_botiga/docs/'.$filename);
			$db->setQuery('UPDATE #__botiga_documents SET filename = "" WHERE id = '.$id);
			$db->query();
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=docs', JText::_('COM_BOTIGA_DOCS_FILES_DELETED'), 'success');
	} 
	
}
