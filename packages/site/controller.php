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

		$db = JFactory::getDbo();
 
		$log = fopen ( JPATH_ROOT.'/sincro/log.txt' , 'w' );
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
				if($brand->name != '') {
					$update_query = 
						"UPDATE #__botiga_brands " .
						"SET name = " . $db->quote($brand->name) .
						" WHERE factusol_codfte = " . $brand->factusol_codfte . " AND @factusol_codfte:=factusol_codfte";
					$insert_query =
						"INSERT #__botiga_brands(name,published,language,factusol_codfte) " .
						"VALUES (" . $db->quote($brand->name) . ", 1, 'es-ES', " . $brand->factusol_codfte . ")";
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
			}

			//esborrar marques...
			if (count($marca)>0) {
				fputs( $log, "DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . ")\n" );
				$db->setQuery( "DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . " )" );
				$db->query();
				$deletes_brands = $db->getAffectedRows();
			} else {
				$deletes_brands = 0;
			}
			
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
				$parent = $this->getCatIdFromFactusol($cat->parent, $cat->language);
				if (!$parent) $parent=0;
				// comprovem si la categoria existeix
				$catid = $this->getCatIdFromFactusol($cat->factusol_codfam, $cat->language, $log);
				if ($catid) { // la categoria existeix. fem un update
					if($parent == 0) { $parent = 1; }
					$update_query = 
						"UPDATE #__categories " .
						"SET title = " . $db->quote($cat->title) .
						",parent_id = ".$parent.
						" WHERE id = " . $catid; 
					fputs( $log, 'DEBUG: ' . $update_query . "\n");
					$db->setQuery( $update_query );
					if ($db->execute()) {
						$updates_categories++;
						fputs( $log, $update_query . "\n");
					} else {
						$errors_categories++;
						fputs( $log, $update_query . "\n");
						$errors_categories_detalls[] = $cat->title;	
					}
				} else { // la categoria no existeix. fem un insert a #__categories i a #__botiga_categories
					$insert_query1 =
						"INSERT #__categories(extension,title,parent_id,access,published,language) " .
						"VALUES (" .
							$db->quote('com_botiga')  . ", " .
							$db->quote($cat->title)    . ", " .
							$parent . ", 1, 1, " .
							$db->quote($cat->language) . ")";
					fputs( $log, 'DEBUG: ' . $insert_query1 . "\n");
					$db->setQuery( $insert_query1 );
					if ($db->query()) {
						$catid = $db->insertid();
						$insert_query2 =
							"INSERT #__botiga_categories(catid,factusol_codfam) " .
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
			// en primer lloc, esborrem de la taula #__botiga_categories. 
			// en segon lloc, esborrem totes les categories de la taula #_categories que no tinguin correspondència amb #_botiga_categories
			fputs( $log, 
				"DELETE FROM #__botiga_categories WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "')\n" .
				"DELETE #__categories FROM #__categories LEFT JOIN #__botiga_categories ON #__categories.id = #__botiga_categories.catid WHERE #__categories.extension='com_botiga' AND #__botiga_categories.catid IS NULL" );
			$db->setQuery( "DELETE FROM #__botiga_categories WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "')" );
			$db->query();	
			$db->setQuery( "DELETE #__categories FROM #__categories LEFT JOIN #__botiga_categories ON #__categories.id = #__botiga_categories.catid WHERE #__categories.extension='com_botiga' AND #__botiga_categories.catid IS NULL" );
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
					$db->setQuery( "SELECT catid FROM #__categories INNER JOIN #__botiga_categories ON #__categories.id=#__botiga_categories.catid WHERE #__categories.extension='com_botiga' AND LOWER(#__categories.language)=" . $db->quote(strtolower($item->language)) . " AND #__botiga_categories.factusol_codfam=" . $db->quote($item->factusol_codfam) );
					$db->execute();
					$num_files_cat = $db->getNumRows();
					$catid = $db->loadResult();
					if ($num_files_cat>0) {
						$db->setQuery( "SELECT id FROM #__botiga_brands WHERE LOWER(language)='es-es' AND factusol_codfte=" . $item->factusol_codfte );
						$db->execute();
						$num_brands_cat = $db->getNumRows();
						$brandid = $db->loadResult();
						if ($num_brands_cat>0) {
						
							//creem preu en format json
							$jsonprice = '{"usergroup":["2"],"pricing":["'.$item->price1.'"]}';
							
							$update_query = 
								"UPDATE #__botiga_items " .
								"SET " .
									"catid = " . $catid . ", " .
									"brand = " . $brandid . ", " .
									"sincronitzat = 1, " .  
									"name = " . $db->quote($item->name) . ", " .
									"price = " . $db->quote($jsonprice) . ", " .
									"description = " . $db->quote($item->description) . ", " .
									"image1 = " . $db->quote($item->image1) .  
								" WHERE LOWER(language) = " . $db->quote(strtolower($item->language)) . " AND factusol_codart = " . $db->quote($item->ref) . " AND @factusol_codart:=LENGTH(factusol_codart)";
							$insert_query =
								"INSERT #__botiga_items(catid,brand,name,image1,price,published,ref,factusol_codart,language,sincronitzat) " .
								"VALUES (" .
									$catid . ", " .
									$brandid . ", " .
									$db->quote($item->name) . ", " .
									$db->quote($item->image1) . ", " .
									$db->quote($jsonprice) . ", " .
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
								fputs( $log, "DEBUG: update article ok\n" );
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
								fputs( $log, "DEBUG: update article caput\n" );
								$errors_items++;
								fputs( $log, $update_query . "\n");
								$errors_items_detalls[] = $item->ref;
							}
						} else {
							// l'article pertany a una marca inexistent
							$errors_items++;
							fputs( $log, "L'article " . $item->ref . " correspon a una marca inexistent (" . $item->factusol_codfte . ". És possible que en el Factusol falti activar-la a Internet (apartat Proveedores)?\n");
							$errors_items_detalls[] = $item->ref;
						}
					} else {
						// l'article pertany a una categoria inexistent
						$errors_items++;
						fputs( $log, "L'article " . $item->ref . " correspon a una categoria inexistent (" . $item->factusol_codfam . ". És possible que en el Factusol falti activar-la a Internet?\n");
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
			
			/*
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
			}*/
		}
		
		
		fprintf( $log, "FINAL PROCÉS: %s\n\n", date("d-m-Y H:i:s") );
	
		$fail = $errors_brands + $errors_categories + $errors_items + $errors_prices;
		if($integritat==true && $fail == 0) { 
		 	$this->sendEmail('carles@afi.cat', JText::_('COM_BOTIGA_CRON_SUBJECT_OK'), JText::_('COM_BOTIGA_CRON_BODY_OK'), $_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt' );
		} else {
		 	$this->sendEmail('carles@afi.cat', JText::_('COM_BOTIGA_CRON_SUBJECT_ERR'), JText::_('COM_BOTIGA_CRON_BODY_ERR'),$_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt'  );
		}
		
		fclose( $log );
		unlink( $_SERVER['DOCUMENT_ROOT'].'/sincro/log.txt' );

	}

	function getCatIdFromFactusol($codfam, $idioma, $log) {
		$db = JFactory::getDbo();
		if (!$codfam) {
			return 0;
		} else {
			$select_query = 
				"SELECT catid " .
				"FROM #__categories " .
				"INNER JOIN #__botiga_categories ON #__categories.id = #__botiga_categories.catid " .
				"WHERE #__categories.extension='com_botiga' AND LOWER(#__categories.language) = " . $db->quote(strtolower($idioma)) .
					" AND #__botiga_categories.factusol_codfam = " . $db->quote($codfam);
			fputs( $log, 'DEBUG: ' . $select_query . "\n" );
			$db->setQuery($select_query);
			return $db->loadResult();
		}
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
