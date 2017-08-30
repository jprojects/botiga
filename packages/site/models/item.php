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

class botigaModelItem extends JModelItem
{ 
	
	/**
	 * Get a list of items.
	 *
	 * @return	array
	 * @since	1.6
	*/
	public function getItem()
	{
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        
        $id  = $app->input->get('id');
        
        $db->setQuery('select i.*, b.image as bimage, b.name as bname from #__botiga_items as i inner join #__botiga_brands as b on b.id = i.brand where i.id = '.$id);
        return $db->loadObject();
	}
}
