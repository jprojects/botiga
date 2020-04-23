<?php
/**
 * @version		1.0.0 botiga $
 * @package		botiga
 * @copyright Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class botigaControllerItem extends JControllerForm
{
    /**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Item', $prefix = 'botigaModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	* Method to override the save method
	 *
	 * @return	void
	*/
  function save()
	{
		$model  = $this->getModel();

    if($model->store()) {

			parent::save();
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @return	void
	 * @since	1.6
	*/
	public function batch($model)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Item', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_botiga&view=items'.$this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
