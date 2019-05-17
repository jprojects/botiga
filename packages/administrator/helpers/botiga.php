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

/**
 * BOTIGA component helper.
 */
abstract class botigaHelper
{
    /**
	 * Configure the Linkbar.
	*/
	public static function addSubmenu($submenu) 
	{
        JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_BRANDS'), 'index.php?option=com_botiga&view=brands', $submenu == 'brands');
        JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_botiga', $submenu == 'categories');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_PRODUCTS'), 'index.php?option=com_botiga&view=items', $submenu == 'products');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_USERS'), 'index.php?option=com_botiga&view=users', $submenu == 'users');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_ORDERS'), 'index.php?option=com_botiga&view=orders', $submenu == 'orders');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_COUPONS'), 'index.php?option=com_botiga&view=coupons', $submenu == 'coupons');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_SHIPMENTS'), 'index.php?option=com_botiga&view=shipments', $submenu == 'shipments');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_DISCOUNTS'), 'index.php?option=com_botiga&view=discounts', $submenu == 'discounts');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_DOCS'), 'index.php?option=com_botiga&view=docs', $submenu == 'docs');
		JHtmlSidebar::addEntry(JText::_('COM_BOTIGA_SUBMENU_TOOLS'), 'index.php?option=com_botiga&view=tools&layout=edit', $submenu == 'tools');
				
		if ($submenu == 'categories') 
		{
			$document = JFactory::getDocument();
			$document->setTitle(JText::_('COM_BOTIGA_MANAGER_CATEGORY'));
		}
	}

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_botiga';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}

