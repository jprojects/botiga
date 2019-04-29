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

class botigaModelItem extends JModelItem
{ 	
	/**
	 * Get selected item.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getItem()
	{
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        
        $id  = $app->input->get('id');
        
        $db->setQuery('SELECT i.*, b.name AS bname FROM #__botiga_items AS i inner join #__botiga_brands AS b ON b.id = i.brand WHERE i.id = '.$id);
        
        return $db->loadObject();
	}
}
