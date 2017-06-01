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

// import Joomla table library
jimport('joomla.database.table');

/**
 * Docs Table class
 *
 * @package    com_laundry
 *
 */
class botigaTableCoupons extends JTable
{
	/**
    * Constructor
    *
    * @param object Database connector object
    */
    function __construct( &$db ) {
    	parent::__construct('#__botiga_coupons', 'id', $db);
    }
      
               
}

?>
