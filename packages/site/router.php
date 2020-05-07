<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_botiga
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class of com_content
 *
 * @since  3.3
 */
class BotigaRouter extends JComponentRouterView
{
  public function build(&$query)
  {
    $segments = array();

    if($query['view'] == 'item') {
      if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
      }
      if (isset($query['id'])) {
        $segments[] = $query['id'];
        $id = $query['id'];
        unset($query['id']);
      }
      if($segments[0] == 'item') {
        $segments[] = $this->getAlias($id);
      }
    }

    return $segments;
  }

  public function parse(&$segments)
  {
    $vars = array();
    switch($segments[0])
    {
      case 'item':
        $vars['view']  = 'item';
        $vars['id']    = $segments[1];
        $vars['alias'] = $segments[2];
        break;
    }
    return $vars;
  }

  public function getAlias($id)
  {
    jimport('joomla.filter.output');
    $db = JFactory::getDbo();
    $db->setQuery('SELECT alias, name FROM `#__botiga_items` WHERE id = '.(int) $id);
    $row = $db->loadObject();
    if($row->alias == '') {
      return JFilterOutput::stringURLSafe($row->name);
    } else {
      return $row->alias;
    }
  }
}
