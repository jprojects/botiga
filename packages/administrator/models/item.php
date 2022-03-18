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

 defined('_JEXEC') or die;

 use Joomla\CMS\Factory;
 use Joomla\CMS\Form\Form;
 use Joomla\CMS\Language\Text;
 use Joomla\CMS\MVC\Model\AdminModel;
 use Joomla\CMS\Table\Table;
 use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;

class botigaModelItem extends JModelAdmin
{
    /**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	*/
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_botiga.message.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	*/
	public function getTable($type = 'Items', $prefix = 'botigaTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	*/
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_botiga.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	*/
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_botiga.edit.item.data', array());
		if (empty($data)) {
			$data = $this->getItem();
			$data->catid = explode(',',$data->catid);
		}
		return $data;
	}

	/**
	 * method to store data into the database
	 * @param boolean
	*/
  	function store()
	{
		$row =& $this->getTable();
		$app = JFactory::getApplication();

		$data = Factory::getApplication()->input->get('jform', array(), 'array');

		$data['id'] = $app->input->getInt('id', 0, 'get');

		if($data['child'] == '') { $data['child'] = 0; }
		if($data['brand'] == '') { $data['brand'] = 0; }


		//categories
    	$categories = array();

		foreach($data['catid'] as $k) {
			$categories[] = $k;
		}

    	$data['catid'] = implode(',', $categories);

		//images
		$data['images'] = json_encode($data['images']);

		//images
		if($data['price'] != '') { 
			$data['price'] = json_encode($data['price']); 
			$db     = JFactory::getDbo();
			$preus  = json_decode($data['price']);

			if($data['id'] == 0) { $itemid = $row->id; } else { $itemid = $data['id']; }

		  	foreach($preus as $preu) {
			  	$prices = new stdClass();
				$prices->itemId = $itemid;
				$prices->usergroup = $preu->usergroup;
				$prices->price = $preu->pricing;

				$db->setQuery('SELECT id FROM `#__botiga_items_prices` WHERE itemId = '.$itemid.' AND usergroup = '.$preu->usergroup);
				if($id = $db->loadResult()) {
					$prices->id = $id;
					$db->updateObject('#__botiga_items_prices', $prices, 'id');
				} else {
					$db->insertObject('#__botiga_items_prices', $prices);
				}
		  	}
		}

		//extres
		if($data['extres'] != '') { $data['extres'] = json_encode($data['extres']); }

		if (!$row->bind( $data )) {
			return JFactory::getApplication()->enqueueMessage($row->getError());
		}

		if (!$row->store()) {
			return JFactory::getApplication()->enqueueMessage($row->getError());
		}

		return true;
	}

	/**
	 * Method to duplicate block.
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @since   1.6
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$db = $this->getDbo();
		foreach($pks as $id) {
			$db->setQuery('select * from #__botiga_items where id = '.$id);
			$row 	 = $db->loadObject();
			$row->id = 0;
			$response = $db->insertObject('#__botiga_items', $row, 'id');
		}
		return $response;
	}
}
?>
