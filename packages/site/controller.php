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

jimport('joomla.application.component.controller');

class botigaController extends JControllerLegacy
{
    function __construct()
    {
		parent::__construct();
    }
    
     /**
	 * method to set order emails
	 * @return bool 
	 */
     public function sendOrderEmails($processor)
     {
     	botigaHelperPdf::sendOrderEmails($processor);
     }
     
     /**
	 * method to generate a pdf
	 * @return bool 
	 */
     public function genPdf($mode = 'D', $uid = '', $uniqid = '')
     {
     	$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id', $uid);
		
     	botigaHelperPdf::genPdf($id, $mode, $uid, $uniqid);
     }
    
	 /**
	 * method to set item to favorites
	 * @return bool 
	 */
     public function setFavorite()
     {
     	$db   = JFactory::getDbo();
     	$user = JFactory::getUser();
     	
     	if($user->guest) { return; }
     	
     	$jinput  = JFactory::getApplication()->input;
     	$id  = $jinput->get('id');
     	$return  = $jinput->getString('return');
     	$url = base64_decode($return);
     	
     	$favorite = new stdClass();
     	
     	$favorite->itemid = $id;
     	$favorite->userid = $user->id;
     	
     	$db->insertObject('#__botiga_favorites', $favorite);
     	
     	$this->setRedirect($url, JText::_('COM_BOTIGA_SET_FAVORITE_SUCCESS'), 'info');
     }
     
     /**
	 * method to unset item from favorites
	 * @return bool 
	 */
     public function unsetFavorite()
     {
     	$db   = JFactory::getDbo();
     	$user = JFactory::getUser();
     	
     	if($user->guest) { return; }
     	
     	$jinput  = JFactory::getApplication()->input;
     	$id  = $jinput->get('id');
     	$return  = $jinput->getString('return');
     	$url = base64_decode($return);
     	
     	$db->setQuery('DELETE FROM #__botiga_favorites WHERE itemid = '.$id);
     	$db->query();
     	
     	$this->setRedirect($url, JText::_('COM_BOTIGA_UNSET_FAVORITE_SUCCESS'), 'info');
     }
     
     //ToDo:: esta función viene con el sistema de documentos de la tienda, valorar si borrar o mejorar
     function sendPdf()
     {
     	$user   = JFactory::getUser();
     	$config = JFactory::getConfig();
     	$mailer = JFactory::getMailer();
     	$app    = JFactory::getApplication(); 
     	$db     = JFactory::getDbo();
     	
     	$result1 = false;
     	$result2 = false; 
     	
     	$botiga_mail = botigaHelper::getParameter('botiga_mail');   	
     	
		$menu = $app->getMenu();
		$menuItem = $menu->getItems( 'link', 'index.php?option=com_botiga&view=docs', true );
     	
     	$data = $app->input->post->get('jform', array(), 'array');   
     	
     	//generate link
     	$db->setQuery('select pdf from #__botiga_docs where id = '.$data['id']);
     	$pdf  = $db->loadResult(); 
     	$link = JURI::base().'images/pdf/'.$pdf;	

		$sender = array( 
    		$config->get( 'mailfrom' ),
    		$config->get( 'fromname' ) 
		);

		$mailer->setSender($sender);
		$mailer->isHtml(true);
		
		//mail to user
		$mailer->addRecipient($data['email']);
		$mailer->setSubject(JText::_('COM_BOTIGA_DOCS_SUBJECT_USER'));
		$mailer->setBody(JText::sprintf('COM_BOTIGA_DOCS_BODY_USER', $link));
		$result1 = $mailer->Send();
		
		//mail to admin
		$mailer->addRecipient($botiga_mail);
		$mailer->setSubject(JText::_('COM_BOTIGA_DOCS_SUBJECT_ADMIN'));
		$mailer->setBody(JText::sprintf('COM_BOTIGA_DOCS_BODY_ADMIN', $data['name'], $data['email']));
		$result2 = $mailer->Send();
		
		if($result1 && $result2) {
			$msg = JText::_('COM_BOTIGA_DOCS_SUCCESS');
			$type = 'message';
		} else {
			$msg = JText::_('COM_BOTIGA_DOCS_ERROR');
			$type = 'error';
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_botiga&view=docs&Itemid='.$menuItem->id), $msg, $type);
     }

	function sincronitza() {
		sincroHelper::sincronitza();
	}
						
	public function sendEmail($email, $subject, $body, $attachment='')
	{
		$config   = JFactory::getConfig();
		$mail 	  = JFactory::getMailer();
		
		$sender[] = $config->get('fromname');
		$sender[] = $config->get('mailfrom');
		
		$botiga_name = botigaHelper::getParameter('botiga_name');
		$botiga_logo = botigaHelper::getParameter('botiga_logo');
		
		$htmlbody = '<div style="width:100%!important;height:100%;background-color:#683b2b;"><table style="width:50%;padding:20px;margin: 0 auto;"><tr><td></td><td style="text-align:center;background-color:#ffcd00;display:block!important;max-width:600px!important;margin:0"><img src="'.JURI::root().$botiga_logo.'" alt="'.$botiga_name.'" style="margin-top:10px;" /><div style="padding:30px;max-width:600px;margin:0"><table width="100%"><tr><td><p style="color:#683b2b;">'.$body.'</p><p></p><a style="color:#683b2b;" href="'.JURI::root().'">'.$botiga_name.'</a></td></tr></table></div></td><td></td></tr></table></div>';    	   
	     	
		$mail->setSender( $sender );
		$mail->addRecipient( $email );
		$mail->setSubject( $subject );
		$mail->setBody( $htmlbody );
		$mail->addAttachment( $attachment );
		$mail->IsHTML(true);
		$mail->Send();
	}

	/**
	* 07/01/2017: Utilitat per passar  a minúscules totes les imatges de la carpeta images/botiga_items
	*/
	function lcasefiles() {
		$files = scandir('/var/www/vhosts/acjsystems.com/httpdocs/images/botiga_items');
		foreach ($files as $file) {
			if ($file!=strtolower($file)) {
				//rename('/var/www/vhosts/acjsystems.com/httpdocs/images/botiga_items/' . $file,'/var/www/vhosts/acjsystems.com/httpdocs/images/botiga_items/' . strtolower($file));
				echo $file . '<br/>';
			}
		}
	}
}
?>
