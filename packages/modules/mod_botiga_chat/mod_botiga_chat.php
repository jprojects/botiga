<?php
/**
 * @package Module EB Whatsapp Chat for Joomla!
 * @version 1.4: mod_ebwhatsappchat.php Jun 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/

   // No direct access
   defined('_JEXEC') or die;

   if (!defined('DS')) {
      define('DS', DIRECTORY_SEPARATOR);
   }

   $document = JFactory::getDocument();
   // Include the syndicate functions only once
   require_once dirname(__FILE__) . '/core/helper.php';

   $whatsapp = BotigaChatHelper::getWhatsapp($params);
   $layout = 'default';
   require JModuleHelper::getLayoutPath($module->module, $layout);
?>