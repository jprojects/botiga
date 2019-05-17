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
jimport('joomla.application.component.controllerform');
 

class botigaControllerDoc extends JControllerForm
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
	
	public function upload()
	{   
		$jinput  = JFactory::getApplication()->input;
        $file    = $jinput->files->get('jform');  
       	$allowed = array('pdf', 'xlsm', 'xls', 'doc', 'docx', 'xlsx', 'odt');
       	
       	$id = $jinput->get('id', 0, 'get');

    	jimport('joomla.filesystem.file');
     
    	$filename = JFile::makeSafe($file['subida']['name']);

    	$src  = $file['subida']['tmp_name'];
    	$dest = JPATH_ROOT."/media/com_botiga/docs/".$filename;
    	$extension = strtolower(JFile::getExt($filename)); 

    	if ( in_array($extension, $allowed) ) {
       		if ( JFile::upload($src, $dest) ) {
				$msg = JText::_('COM_BOTIGA_UPLOAD_OK');
				$type = 'success';
				if($id != 0) {
					$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit&id='.$id, $msg, $type);
				} else {
					$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit', $msg, $type);
				}
       		} else {
          		$msg = JText::_('COM_BOTIGA_TASK_UPLOAD_ERROR');
				$type = 'error';
				if($id != 0) {
					$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit&id='.$id, $msg, $type);
				} else {
					$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit', $msg, $type);
				}
       		}
    	} else {
       		$msg = JText::_('COM_BOTIGA_TASK_UPLOAD_ONLY_XLS');
			$type = 'error';
			if($id != 0) {
				$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit&id='.$id, $msg, $type);
			} else {
				$this->setRedirect('index.php?option=com_botiga&view=doc&layout=edit', $msg, $type);
			}
    	}
	}
}
