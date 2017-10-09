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

jimport('joomla.application.component.controller');

class botigaController extends JControllerLegacy
{
    function __construct()
    {
		parent::__construct();
    }
    
    public function register() {
	
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$db 		= JFactory::getDbo();
		$config 	= JFactory::getConfig();
		$app   	   	= JFactory::getApplication();
		$data 	   	= $app->input->post->get('jform', array(), 'array');
		$valid      = true;
		
		if($data['password1'] !== $data['password2']) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_PASSWORD_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}
		if($data['email1'] !== $data['email2']) {
			$msg  = JText::_('COM_BOTIGA_REGISTER_EMAIL_NOT_MATCH');
			$type = 'danger';
			$valid = false;
		}

		if($valid) {
			//we need the encrypted password
			jimport('joomla.user.helper');
			$password = JUserHelper::hashPassword($data['password1']);
		
			//create joomla user
			$user                   = new stdClass();
			$user->name             = $data['nombre'];
			$user->username         = $data['email1'];
			$user->password         = $password;
			$user->email            = $data['email1'];
			$user->registerDate     = date('Y-m-d H:i:s');
			$user->block 			= 0;
			
			$valid = $db->insertObject('#__users', $user);
			
			$userid  = $db->insertid();
			
			if($valid) {
			
				//create botiga user
				$acjuser            	= new stdClass();
				$acjuser->userid    	= $userid;
			    $acjuser->usergroup 	= 2;
			    $acjuser->nom_empresa	= $data['empresa'];
			    $acjuser->mail_empresa	= $data['email1'];
			    $acjuser->telefon		= $data['phone'];
			    $acjuser->adreca		= $data['address'];
			    $acjuser->cp			= $data['zip'];
			    $acjuser->poblacio		= $data['city'];
			    $acjuser->pais   		= $data['pais'];	
			    $acjuser->cif   		= $data['cif'];	
			    $acjuser->published	    = 1;
			    
			    $db->insertObject('#__botiga_users', $acjuser);    	        	
			    
				//create usergroups
				$group              = new stdClass();
				$group->user_id     = $userid;
				$group->group_id    = 2; //group registered
				$db->insertObject('#__user_usergroup_map', $group);		

				//send email to the user with his credentials...
				$mail = JFactory::getMailer();
				$sender[]  	= $config->get('fromname');
				$sender[]	= $config->get('mailfrom');
				$mail->setSender( $sender );
			    $mail->addRecipient( $data['email1'] );
			    $mail->setSubject( JText::_('COM_BOTIGA_REGISTER_SUBJECT') );
				$mail->setBody( JText::sprintf('COM_BOTIGA_REGISTER_BODY', $data['email1'], $data['password1']) );
				$mail->IsHTML(true);
				$mail->Send();
			
				$msg  = JText::_('COM_BOTIGA_REGISTER_SUCCESS');
				$type = '';
			
			} else {
				$msg  = JText::_('COM_BOTIGA_REGISTER_ERROR');
				$type = 'error';
			}
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=register&Itemid=116', $msg, $type);
	}
    
	/**
	 * method to set item to favorites
	 * @return bool 
	 */
     function setFavorite()
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
     function unsetFavorite()
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

	function sincronitza() {

		$db = JFactory::getDbo();
 
		$log = fopen ( $_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt' , 'w' );
		fprintf( $log, "INICI PROCÉS: %s\n\n", date("d-m-Y H:i:s") );
		
		$integritat = true;
		if ($this->is_valid_xml(JURI::base().'sincro/brands.xml')) {
			fputs( $log, "Integritat XML marques: CORRECTE!\n" );	
		} else {
			fputs( $log, "Integritat XML marques: ERROR!\n" );
			$integritat = false;
		}
		if ($this->is_valid_xml(JURI::base().'sincro/categories.xml')) {
			fputs( $log, "Integritat XML categories: CORRECTE!\n" );	
		} else {
			fputs( $log, "Integritat XML categories: ERROR!\n" );	
			$integritat = false;
		}
		if ($this->is_valid_xml(JURI::base().'sincro/items.xml')) {
			fputs( $log, "Integritat XML articles: CORRECTE!\n" );	
		} else {
			fputs( $log, "Integritat XML articles: ERROR!\n" );
			$integritat = false;			
		}
		if ($this->is_valid_xml(JURI::base().'sincro/prices.xml')) {
			fputs( $log, "Integritat XML preus especials: CORRECTE!\n" );	
		} else {
			fputs( $log, "Integritat XML preus especials: ERROR!\n" );
			$integritat = false;			
		}
		
		fputs( $log, "\n");
		
		if ($integritat==true) {
			//comencem amb les marques...
			$xmlfile = JURI::base().'sincro/brands.xml';
			$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
			fputs( $log, "MARQUES : " . JURI::base() . "sincro/brands.xml\n" );
			$marca = array();
			$updates_brands=0;
			$inserts_brands=0;
			$deletes_brands=0;
			$errors_brands=0;
			$errors_brands_detalls = array();
			foreach($nodes as $brand) {
				$marca[] = $brand->factusol_codfte;
				$update_query = 
					"UPDATE #__botiga_brands " .
					"SET name = " . $db->quote($brand->name) .
					" WHERE factusol_codfte = " . $brand->factusol_codfte . " AND @factusol_codfte:=factusol_codfte";
				$insert_query =
					"INSERT #__botiga_brands(name,published,language,factusol_codfte) " .
					"VALUES (" . $db->quote($brand->name) . ", 1, 'ca-ES', " . $brand->factusol_codfte . ")";
				$db->setQuery('SET @factusol_codfte := 0;');
				$db->query();
				$db->setQuery( $update_query );
				if($db->query()) { 
					$db->setQuery('select @factusol_codfte'); 
					$factusol_codfte_updated = $db->loadResult(); 
					if ($factusol_codfte_updated == 0) {
						$db->setQuery( $insert_query );
						if ($db->query()) {
							$inserts_brands++;
						} else {
							$errors_brands++;
							$errors_brands_detalls[] = $brand->name;
						}
						fputs( $log, $insert_query . "\n");
					} else {
						$updates_brands++;
						fputs( $log, $update_query . "\n");
					}
				} else { 
					$errors_brands++;
					fputs( $log, $update_query . "\n");
					$errors_brands_detalls[] = $brand->name;
				}		
			}

			//esborrar marques...
			fputs( $log, "DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . ")\n" );
			$db->setQuery( "DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . " )" );
			$db->query();
			$deletes_brands = $db->getAffectedRows();
			
			fprintf( $log, 
				"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\nERRORS EN: %s\n\n", 
				$inserts_brands, 
				$updates_brands, 
				$deletes_brands, 
				$errors_brands, 
				implode(",", $errors_brands_detalls) );
			
			//comencem amb categories...
			$xmlfile = JURI::base().'sincro/categories.xml';
			$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');  
			fputs( $log, "CATEGORIES : " . JURI::base() . "sincro/categories.xml\n" );
			$categoria = array();
			$updates_categories=0;
			$inserts_categories=0;
			$deletes_categories=0;
			$errors_categories=0;
			$errors_categories_detalls = array();
			foreach($nodes as $cat) {
				$categoria[] = $cat->factusol_codfam;
				$catid = 0;
				$select_query = 
					"SELECT catid " .
					"FROM #__categories " .
					"INNER JOIN #__botiga_categories_cod ON #__categories.id = #__botiga_categories_cod.catid " .
					"WHERE #__categories.extension='com_botiga' AND LOWER(#__categories.language) = " . $db->quote(strtolower($cat->language)) . 
						" AND #__botiga_categories_cod.factusol_codfam = " . $db->quote($cat->factusol_codfam);
				// comprovem si la categoria existeix
				$db->setQuery($select_query);
				$db->execute();
				$num_files = $db->getNumRows();
				$catid = $db->loadResult();
				if ($num_files > 0) { // la categoria existeix. fem un update
					$update_query = 
						"UPDATE #__categories " .
						"SET title = " . $db->quote($cat->title) .
						",parent_id = ".$cat->parent.
						" WHERE id = " . $catid; 
					$db->setQuery( $update_query );
					if ($db->execute()) {
						$updates_categories++;
						fputs( $log, $update_query . "\n");
					} else {
						$errors_categories++;
						fputs( $log, $update_query . "\n");
						$errors_categories_detalls[] = $cat->title;	
					}
				} else { // la categoria no existeix. fem un insert a #__categories i a #__botiga_categories_codç
					$insert_query1 =
						"INSERT #__categories(extension,title,parent_id,language) " .
						"VALUES (" .
							$db->quote('com_botiga')  . ", " .
							$db->quote($cat->title)    . ", " .
							$parent    . ", " .
							$db->quote($cat->language) . ")";
					$db->setQuery( $insert_query1 );
					if ($db->query()) {
						$catid = $db->insertid();
						$insert_query2 =
							"INSERT #__botiga_categories_cod(catid,factusol_codfam) " .
							"VALUES (" .
								$catid . ", " .
								$db->quote($cat->factusol_codfam) . ")";
						$db->setQuery( $insert_query2 );
						$db->query();
						$inserts_categories++;
					} else {
						$errors_categories++;
						fputs( $log, $insert_query1 . "\n");
						$errors_categories_detalls[] = $cat->title;
					}
				}
			}

			//reconstruir categories...
			jimport('joomla.database.tablenested');
			$cat = new JTableNested('#__categories', 'id', $db);
			$cat->rebuild();

			//esborrar categories...
			// eliminem les categories que no pertanyin a cap de les famílies que figuren en l'XML
			// en primer lloc, esborrem de la taula #__botiga_categories_cod. 
			// en segon lloc, esborrem totes les categories de la taula #_categories que no tinguin correspondència amb #_botiga_categories_cod
			fputs( $log, 
				"DELETE FROM #__botiga_categories_cod WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "')\n" .
				"DELETE #__categories FROM #__categories LEFT JOIN #__botiga_categories_cod ON #__categories.id = #__botiga_categories_cod.catid WHERE #__categories.extension='com_botiga' AND #__botiga_categories_cod.catid IS NULL" );
			$db->setQuery( "DELETE FROM #__botiga_categories_cod WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "')" );
			$db->query();	
			$db->setQuery( "DELETE #__categories FROM #__categories LEFT JOIN #__botiga_categories_cod ON #__categories.id = #__botiga_categories_cod.catid WHERE #__categories.extension='com_botiga' AND #__botiga_categories_cod.catid IS NULL" );
			$db->query();
			
			$deletes_categories = $db->getAffectedRows();
			
			fprintf( $log, 
				"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\nERRORS EN: %s\n\n", 
				$inserts_categories, 
				$updates_categories, 
				$deletes_categories, 
				$errors_categories, 
				implode(",", $errors_categories_detalls) );

			//comencem amb items...
			$xmlfile = JURI::base().'sincro/items.xml';
			$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');  
			fputs( $log, "ITEMS : " . JURI::base() . "sincro/items.xml\n" );
			$updates_items=0;
			$inserts_items=0;
			$deletes_items=0;
			$errors_items=0;
			$errors_items_detalls = array();
			$db->setQuery( "UPDATE #__botiga_items SET sincronitzat=0" );
			if ($db->query()) {
				foreach($nodes as $item) {
					$db->setQuery( "SELECT catid FROM #__categories INNER JOIN #__botiga_categories_cod ON #__categories.id=#__botiga_categories_cod.catid WHERE #__categories.extension='com_botiga' AND LOWER(#__categories.language)=" . $db->quote(strtolower($item->language)) . " AND #__botiga_categories_cod.factusol_codfam=" . $db->quote($item->factusol_codfam) );
					$db->execute();
					$num_files_cat = $db->getNumRows();
					$catid = $db->loadResult();
					if ($num_files_cat>0) {
						$db->setQuery( "SELECT id FROM #__botiga_brands WHERE LOWER(language)='ca-es' AND factusol_codfte=" . $item->factusol_codfte );
						$db->execute();
						$num_brands_cat = $db->getNumRows();
						$brandid = $db->loadResult();
						if ($num_brands_cat>0) {
							$update_query = 
								"UPDATE #__botiga_items " .
								"SET " .
									"catid = " . $catid . ", " .
									"marca = " . $brandid . ", " .
									"sincronitzat = 1, " .  
									"name = " . $db->quote($item->name) . ", " .
									"price1 = " . $db->quote($item->price1) . ", " .
									"price2 = " . $db->quote($item->price2) . ", " .
									"description = " . $db->quote($item->description) . ", " .
									"image1 = " . $db->quote($item->image1) .  
								" WHERE LOWER(language) = " . $db->quote(strtolower($item->language)) . " AND factusol_codart = " . $db->quote($item->ref) . " AND @factusol_codart:=LENGTH(factusol_codart)";
							$insert_query =
								"INSERT #__botiga_items(catid,marca,name,image1,price1,price2,published,ref,factusol_codart,language,sincronitzat) " .
								"VALUES (" .
									$catid . ", " .
									$brandid . ", " .
									$db->quote($item->name) . ", " .
									$db->quote($item->image1) . ", " .
									$item->price1 . ", " .
									$item->price2 . ", " .
									"1, " .
									$db->quote($item->ref) . ", " .
									$db->quote($item->ref) . ", " .
									$db->quote(strtolower($item->language)) . ", " .
									" 1)";
							$db->setQuery('SET @factusol_codart := 0;');
							$db->query();
							fputs( $log, $update_query . "\n" ); 
							$db->setQuery( $update_query );
							if($db->query()) { 
								$db->setQuery('select @factusol_codart'); 
								$factusol_codart_updated = $db->loadResult(); 
								if ($factusol_codart_updated==0) {
									$db->setQuery( $insert_query );
									fputs( $log, $insert_query . "\n" ); 
									if ($db->query()) {
										$inserts_items++;
									} else {
										$errors_items++;
										$errors_items_detalls[] = $item->ref;
									}
									fputs( $log, $insert_query . "\n");
								} else {
									$updates_items++;
									fputs( $log, $update_query . "\n");
								}
							} else { 
								$errors_items++;
								fputs( $log, $update_query . "\n");
								$errors_items_detalls[] = $item->ref;
							}
						} else {
							// l'article pertany a una marca inexistent
							$errors_items++;
							fputs( $log, "L'article " . $item->ref . " correspon a una categoria inexistent (" . $item->factusol_codfam . ". És possible que en el Factusol falti activar-la a Internet?\n");
							$errors_items_detalls[] = $item->ref;
						}
					} else {
						// l'article pertany a una categoria inexistent
						$errors_items++;
						fputs( $log, "L'article " . $item->ref . " correspon a una marca inexistent (" . $item->factusol_codfte . ". És possible que en el Factusol falti activar-la a Internet (apartat Fabricantes)?\n");
						$errors_items_detalls[] = $item->ref;						
					}
				}

				//esborrar items...
				fputs( $log, "DELETE FROM #__botiga_items WHERE sincronitzat=0\n" );
				$db->setQuery( "DELETE FROM #__botiga_items WHERE sincronitzat=0" );
				$db->query();	
				$deletes_items = $db->getAffectedRows();
				fprintf( $log, 
					"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\n\n", 
					$inserts_items, 
					$updates_items, 
					$deletes_items, 
					$errors_items );
			} else {
				fputs( $log, "Error al marcar 'sincronitzar=0'. La sincronització dels articles no s'ha pogut dur a terme.\n\n" ); 
			}
			
			//comencem amb preus...
			$xmlfile = JURI::base().'sincro/prices.xml';
			$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');  
			fputs( $log, "PREUS ESPECIALS : " . JURI::base() . "sincro/prices.xml\n" );
			$updates_prices=0;
			$inserts_prices=0;
			$deletes_prices=0;
			$errors_prices=0;
			$errors_prices_detalls = array(); 
			$db->setQuery( "UPDATE #__botiga_items_userprices SET sincronitzat=0" );
			if ($db->query()) {
				foreach($nodes as $price) {
					$db->setQuery('select id from #__users where username = '.$db->quote($price['username']));
					$userid = $db->loadResult();
					$update_query = 
						"UPDATE #__botiga_items_userprices " .
						"SET price = " . $price->price . ", " .
							" sincronitzat = 1 " .
						"WHERE factusol_codart = " . $db->quote($price->ref) . " AND userid = " . $userid . " AND @factusol_codart:=LENGTH(factusol_codart)";
					$insert_query = 
						"INSERT INTO #__botiga_items_userprices( itemid, userid, price, factusol_codart, sincronitzat ) " .
						"SELECT id, " . $userid . ", " . $price->price . ", " . $db->quote($price->ref) . ", 1 " .
						"FROM #__botiga_items " .
						"WHERE factusol_codart = " . $db->quote($price->ref);
					$db->setQuery('SET @factusol_codart := 0;');
					$db->query();
					$db->setQuery( $update_query );
					if($db->query()) { 
						$db->setQuery('select @factusol_codart'); 
						$factusol_codart_updated = $db->loadResult(); 
						if ($factusol_codart_updated==0) {
							$db->setQuery( $insert_query );
							if ($db->query()) {
								$inserts_prices = $insert_prices + $db->getAffectedRows();
							} else {
								$errors_prices++;
								$errors_prices_detalls[] = $price->ref;
							}
							fputs( $log, $insert_query . "\n");
						} else {
							$updates_prices++;
							fputs( $log, $update_query . "\n");
						}
					} else { 
						$errors_prices++;
						fputs( $log, $update_query . "\n");
						$errors_prices_detalls[] = $price->ref;
					}
				}
				//esborrar preus...
				fputs( $log, "DELETE FROM #__botiga_items_userprices WHERE sincronitzat=0\n" );
				$db->setQuery("DELETE FROM #__botiga_items_userprices WHERE sincronitzat=0");
				$db->query();
				$deletes_prices = $db->getAffectedRows();
				fprintf( $log, 
					"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\n\n", 
					$inserts_prices, 
					$updates_prices, 
					$deletes_prices, 
					$errors_prices );
			} else {
				fputs( $log, "Error al marcar 'sincronitzar=0'. La sincronització dels preus especials no s'ha pogut dur a terme.\n\n" ); 
			}
		}
		fprintf( $log, "FINAL PROCÉS: %s\n\n", date("d-m-Y H:i:s") );
	
		$fail = $errors_brands + $errors_categories + $errors_items + $errors_prices;
		if($integritat==true && $fail == 0) { 
		 	$this->sendEmail( JText::_('COM_BOTIGA_CRON_SUBJECT_OK'), JText::_('COM_BOTIGA_CRON_BODY_OK'), $_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt' );
		} else {
		 	$this->sendEmail( JText::_('COM_BOTIGA_CRON_SUBJECT_ERR'), JText::_('COM_BOTIGA_CRON_BODY_ERR'),$_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt'  );
		}
		
		fclose( $log );
		unlink( $_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt' );

	}

	function sendEmail($subject,$body,$attachment)
	{
		$email    = array( 'carles@aficat.com', 'parts@acjsystems.com' );
		$config   = JFactory::getConfig();
		$mail 	  = JFactory::getMailer();
		$sender[] = $config->get('fromname');
		$sender[] = $config->get('mailfrom');    	   
	     	
		$mail->setSender( $sender );
		$mail->addRecipient( $email );
		$mail->setSubject( $subject );
		$mail->setBody( $body );
		$mail->addAttachment( $attachment );
		$mail->IsHTML(true);
		$mail->Send();
	}

	/**
	*  Takes XML string and returns a boolean result where valid XML returns true
	*/
	function is_valid_xml( $file ) {
		libxml_use_internal_errors( true );
		$doc = new DOMDocument('1.0', 'utf-8');
		$xml = file_get_contents($file);
		libxml_clear_errors();
		$doc->loadXML( $xml );
		$errors = libxml_get_errors();
		return empty( $errors );
	}

	/**
	* 07/01/2017: Utilitat per passar  a minúscules totes les imatges de la carpeta images/laundry_items
	*/
	function lcasefiles() {
		$files = scandir('/var/www/vhosts/acjsystems.com/httpdocs/images/laundry_items');
		foreach ($files as $file) {
			if ($file!=strtolower($file)) {
				//rename('/var/www/vhosts/acjsystems.com/httpdocs/images/laundry_items/' . $file,'/var/www/vhosts/acjsystems.com/httpdocs/images/laundry_items/' . strtolower($file));
				echo $file . '<br/>';
			}
		}
	}
	
	public function enviar($subject, $body, $email) 
	{
		$mail 		= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');	
	
		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;	
		
        $mail->setSender( $sender );
        $mail->addRecipient( $email );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->IsHTML(true);
        
		return $mail->Send();			
	}
	
	public function sendModalEmail()
	{
		$app   	   = JFactory::getApplication();
		$data 	   = $app->input->getArray($_POST);

		$subject   = "Nova solicitut d'informació desde el web";
		$body      = "Hem rebut una nova solicitut del producte ".$data['maquina']." desde el web, aquestes son les dades del formulari:<br>";
		$body     .= "Nom i cognoms: ".$data['nombre']."<br>";
		$body     .= "Email: ".$data['email']."<br>";
		$body     .= "Telèfon: ".$data['phone']."<br>";
		$body     .= "Missatge:<br>".$data['mensaje']."<br>";

		$send = $this->enviar($subject, $body, 'info@acjsystems.com');

		if($send) {
			$link = $data['url'];
			$msg  = 'La solicitud ha sido enviada correctamente, en breve nos pondremos en contacto contigo.';
			$type = 'info';
		} else {
			$link = $data['url'];
			$msg  = 'Hubo un fallo al tratar de enviar la solicitud, vuelve a intentarlo por favor.';
			$type = 'error';
		}
			
		$this->setRedirect($link, $msg, $type);
	}
	
	public function genPdf()
	{
		jimport('fpdf.fpdf');
		jimport('fpdfi.fpdi');
		
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id');

		$pdf 	= new FPDI();
		
		$pdf->AddPage();
		
		$pdf->setSourceFile(JPATH_COMPONENT.DS.'assets'.DS.'pdf'.DS.'invoice.pdf');
		
		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		define('EURO', chr(128));

		$db = JFactory::getDbo();
		$db->setQuery('SELECT c.*, u.mail_empresa as email, u.cif, u.telefon, u.metodo_pago FROM #__botiga_comandes c INNER JOIN #__botiga_users u ON u.userid = c.userid WHERE c.id = '.$id);
		$com = $db->loadObject();

		$db->setQuery('SELECT cd.*, i.name, i.ref as referencia, i.image1 as image FROM #__botiga_comandesDetall cd INNER JOIN #__botiga_items i ON cd.iditem = i.id WHERE cd.idComanda = '.$id);
		$db->query();
		$num_rows = $db->getNumRows();
		$detalls  = $db->loadObjectList();
		
		$height = 45;
		
		$pdf->SetFont('Arial', '', '10');
		
		if($num_rows >= 12) { $pag = 2; } else { $pag = 1; }
		
		//user
		$pdf->SetXY(170, $height); 
		$pdf->Cell(15, 5, $com->telefon, 0, 0, 'R');
		$height += 4;
		$pdf->SetXY(121, $height); 
		$pdf->Cell(16, 5, $com->cif, 0, 0, 'R');
		$pdf->SetXY(170, $height); 
		$pdf->Cell(15, 5, $com->email, 0, 0, 'R');
		
		//metadata
		$height = 71;
		$pdf->SetXY(20, $height); 
		$pdf->Cell(15, 5, 'WEB00'.$id, 0, 0, 'R');
		$pdf->SetXY(40, $height);
		$pdf->Cell(15, 5, 'Pag. 1/'.$pag, 0, 0, 'R');
		$pdf->SetXY(80, $height);
		$pdf->Cell(15, 5, date('d-m-Y', strtotime($com->data)), 0, 0, 'R');
		
		//detall
		$total   = 0;		
		$height  = 85;
		$counter = 0;
		
		foreach($detalls as $detall) {
		
			if($counter == 12) {
				$pdf->AddPage();
				$pdf->setSourceFile(JPATH_COMPONENT.DS.'assets'.DS.'pdf'.DS.'albaran.pdf');
				$tplIdx2 = $pdf->importPage(1);
				$pdf->useTemplate($tplIdx2, 0, 0, 0, 0, true);
				$height = 71;		
				$pdf->SetFont('Arial', '', '10');
				$pdf->SetXY(20, $height); 
				$pdf->Cell(15, 5, 'WEB00'.$id, 0, 0, 'R');
				$pdf->SetXY(40, $height);
				$pdf->Cell(15, 5, 'Pag. 2/2', 0, 0, 'R');
				$pdf->SetXY(80, $height);
				$pdf->Cell(15, 5, date('d-m-Y', strtotime($com->data)), 0, 0, 'R');
				$height  = 85;
			}
	
			$pdf->SetXY(20, $height);  
			$pdf->Cell(30, 5, $detall->referencia, 0, 0, '');
			$pdf->SetXY(60, $height);  
			$pdf->Cell(90, 5, utf8_decode($detall->name), 0, 0, '');
			$pdf->SetXY(110, $height); 
			$pdf->Cell(20, 5, $detall->qty, 0, 0, 'R');	
			$pdf->SetXY(145, $height);
			$pdf->Cell(20, 5, number_format($detall->price, 2, ',', '.').EURO, 0, 0, '');
			$pdf->SetXY(150, $height); 
			$pdf->Cell(20, 5, 0, 0, 0, 'R');
			$pdf->SetXY(172, $height); 
			$pdf->Cell(15, 5, number_format(($detall->price * $detall->qty), 2, ',', '.').EURO, 0, 0, '');
			
			$height += 6;
			$counter++;
		}
		
		$height = 243;
		$pdf->SetFont('Arial', 'B', '10');
		$pdf->SetXY(55, $height);  
		$pdf->Cell(30, 5, number_format($com->subtotal, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(75, $height); 
		$pdf->Cell(30, 5, $com->iva_percent.'%', 0, 0, 'R');
		$pdf->SetXY(95, $height);
		$pdf->Cell(30, 5, number_format($com->iva_total, 2, ',', '.').EURO, 0, 0, 'R');

		$pdf->SetXY(163, $height); 
		$pdf->Cell(30, 5, number_format(($com->total), 2, ',', '.').EURO, 0, 0, 'R');
		
		$height += 10;
		
		$pdf->SetXY(35, $height); 
		$com->metodo_pago == '' ? $metodo_pago = 'Pago habitual' : $metodo_pago = $com->metodo_pago;
		$pdf->Cell(30, 5, utf8_decode($metodo_pago), 0, 0, 'R');
		
		$height += 12;
		
		$pdf->SetXY(125, $height); 
		$pdf->Cell(30, 5, utf8_decode($com->observa), 0, 0, 'R');

		$pdf->Output('presupuesto_'.$id.'.pdf', 'D');
		die();
	}
}
?>
