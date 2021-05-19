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

// import Joomla table library
jimport('joomla.database.table');

/**
 * Discounts Table class
 *
 * @package    com_botiga
 *
 */
class botigaTableDiscounts extends JTable
{
	/**
    * Constructor
    *
    * @param object Database connector object
    */
    function __construct( &$db ) {
    	parent::__construct('#__botiga_discounts', 'id', $db);
    }
               
}

?>
