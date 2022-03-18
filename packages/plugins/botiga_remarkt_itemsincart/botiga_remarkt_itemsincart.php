<?php
/**
 * @copyright	Copyleft (C) 2019
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

/**
 * Joomla Botiga plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Botiga.joomla
 * @since		3.5
 */
class plgBotiga_remarkt_itemsincart extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	public function notifica()
	{
		$db = JFactory::getDbo();

	}
}
