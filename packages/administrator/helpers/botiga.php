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
