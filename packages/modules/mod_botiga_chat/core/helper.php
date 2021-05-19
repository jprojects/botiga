<?php
/**
 * @package Module EB Whatsapp Chat for Joomla!
 * @version 1.4: mod_ebwhatsappchat.php Jun 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
// Add in all PHP fiels:
defined('_JEXEC') or die;
   class BotigaChatHelper {
      /**
         * Retrieves the Whatsapp Chat
         *
         * @param   array  $params An object containing the module parameters
         *
        * @access public
      */
		
      public static function getWhatsapp(&$params) {

         $chatdata               = array();
         $chatdata['whatsapp_number'] = $params->get('whatsapp_number', 0);
         $chatdata['initial_message'] = $params->get('initial_message', '');
         $chatdata['icon_position'] = $params->get('icon_position', 'bottom_right');
         $chatdata['icon_withtext'] = $params->get('icon_withtext', '');
         $chatdata['backgroundcolor'] = $params->get('backgroundcolor', '');
         $chatdata['textcolor'] = $params->get('textcolor', '');
         $chatdata['icon_image'] = $params->get('icon_image', '');
         $chatdata['upload_iconimg'] = $params->get('upload_iconimg', '');

         $chatdata['heading_option'] = $params->get('heading_option', '');
         $chatdata['heading_content'] = $params->get('heading_content', '');
         $chatdata['heading_name'] = $params->get('heading_name', '');
         $chatdata['heading_department'] = $params->get('heading_department', '');
         $chatdata['heading_image'] = $params->get('heading_image', '');
         $chatdata['middle_content'] = $params->get('middle_content', '');

         return $chatdata;
      }
   }
	
?>