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
class plgBotigaBotiga_sync extends JPlugin
{
	public static $IMPORTAR_MARQUES = false;
	public static $IMPORTAR_CATEGORIES = true;
	public static $IMPORTAR_TARIFES = true;
	public static $IMPORTAR_PREUS = true;
	public static $IMPORTAR_ARTICLES = true;
	public static $IMPORTAR_CLIENTS = true;
	public static $TARIFA_PARTICULARS = 'WEB_WEBPARTICULARS';

	public static $LOGFILE;

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	public function sincronitza()
	{
		$db = JFactory::getDbo();

		$this->$LOGFILE = JPATH_COMPONENT.'/logs/sincro_' . date('Ymd') . '.log';
		$this->customLog( "INICI SINCRO", 1 );

		$integritat = $this->comprovaIntegritat();

		$errors_brands 		= 0;
		$errors_categories 	= 0;
		$errors_tarifes 	= 0;
		$errors_items 		= 0;
		$errors_preus 		= 0;

		if ($integritat==true)
		{
			$this->customLog("Integritat arxius XML: OK ;-))");

			if ($this->$IMPORTAR_MARQUES) {
				$errors_brands = $this->importaMarques();
			}

			if ($this->$IMPORTAR_CATEGORIES) {
				$errors_categories = $this->importaCategories();
			}

			if ($this->$IMPORTAR_TARIFES) {
				$errors_tarifes = $this->importaTarifes();
			}

			if ($this->$IMPORTAR_ARTICLES) {
				$errors_items = $this->importaArticles();
			}

			if ($this->$IMPORTAR_PREUS) {
				$errors_preus = $this->importaPreus();
			}

			if ($this->$IMPORTAR_CLIENTS) {
				$errors_clients = $this->importaClients();
			}
		}

		$success = $integritat && ($errors_brands + $errors_categories + $errors_tarifes + $errors_items + $errors_preus == 0);

		$this->customLog( $success?"FINAL SINCRO AMB ÈXIT\n":"FINAL SINCRO AMB ERRORS\n" );

		$zip = new ZipArchive();
		$zipfile = JPATH_COMPONENT.'/logs/sincro_' . date('Ymd') . '.zip';
		$zip->open($zipfile, ZipArchive::CREATE);
		$zip->addFile($this->$LOGFILE, 'sincro_' . date('Ymd') . '.log');
		$zip->close();
		//unlink($this->$LOGFILE);

		/*if($success) {
		 	$this->sendEmail( JText::_('COM_BOTIGA_CRON_SUBJECT_OK'), JText::_('COM_BOTIGA_CRON_BODY_OK'), $zipfile );
		} else {
		 	$this->sendEmail( JText::_('COM_BOTIGA_CRON_SUBJECT_ERR'), JText::_('COM_BOTIGA_CRON_BODY_ERR'), $zipfile );
		}*/
	}

	private function comprovaIntegritat()
	{
		$integritat = true;
		if ($this->is_valid_xml(JURI::base().'sincro/brands.xml') || !$this->$IMPORTAR_MARQUES) {
			$this->customLog("Integritat XML marques: CORRECTE!" );
		} else {
			$this->customLog("Integritat XML marques: ERROR!" );
			$integritat = false;
		}
		if ($this->is_valid_xml(JURI::base().'sincro/categories.xml') || !$this->$IMPORTAR_CATEGORIES) {
			$this->customLog("Integritat XML categories: CORRECTE!" );
		} else {
			$this->customLog("Integritat XML categories: ERROR!" );
			$integritat = false;
		}
		if ($this->is_valid_xml(JURI::base().'sincro/items.xml') || !$this->$IMPORTAR_ARTICLES) {
			$this->customLog("Integritat XML articles: CORRECTE!" );
		} else {
			$this->customLog("Integritat XML articles: ERROR!" );
			$integritat = false;
		}
		if ($this->is_valid_xml(JURI::base().'sincro/preus.xml') || !$this->$IMPORTAR_PREUS) {
			$this->customLog("Integritat XML preus especials: CORRECTE!" );
		} else {
			$this->customLog("Integritat XML preus especials: ERROR!" );
			$integritat = false;
		}
		return $integritat;
	}

	private function importaMarques()
	{
		$db = JFactory::getDbo();
		$error_brands=0;

		$xmlfile = JURI::base().'sincro/brands.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile" );
			$errors_brands = 1;
		} else {
			$this->customLog("MARQUES : sincro/brands.xml" );
			$marca = array();
			$updates_brands=0;
			$inserts_brands=0;
			$deletes_brands=0;
			$errors_brands=0;
			$errors_brands_detalls = array();
			foreach($nodes as $brand) {
				if(isset($brand->name) && !empty($brand->name) && $brand->name != '') {
					$marca[] = $brand->factusol_codfte;
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
							$this->customLog($insert_query );
						} else {
							$updates_brands++;
							$this->customLog($update_query );
						}
					} else {
						$errors_brands++;
						$this->customLog($update_query );
						$errors_brands_detalls[] = $brand->name;
					}
				}
			}
		}

		//esborrar marques...
		if (count($marca)>0) {
			$this->customLog("DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . ")"  );
			$db->setQuery( "DELETE FROM #__botiga_brands WHERE factusol_codfte NOT IN (" . implode( ",", $marca ) . " )" );
			$db->query();
			$deletes_brands = $db->getAffectedRows();
		} else {
			$deletes_brands = 0;
		}

		$this->customLog(
			sprintf( "INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\nERRORS EN: %s\n",
				$inserts_brands,
				$updates_brands,
				$deletes_brands,
				$errors_brands,
				implode(",", $errors_brands_detalls)) );

		return $error_brands;
	}

	private function importaCategories()
	{
		$db = JFactory::getDbo();
		$errors_categories = 0;

		$xmlfile = JURI::base().'sincro/categories.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile");
			$errors_categories = 1;
		} else {
			$this->customLog("CATEGORIES : " . JURI::base() . "sincro/categories.xml"  );
			$categoria = array();
			$updates_categories=0;
			$inserts_categories=0;
			$deletes_categories=0;
			$errors_categories=0;
			$errors_categories_detalls = array();
			foreach($nodes as $cat) {
				$categoria[] = $cat->factusol_codfam;
				$catid = 0;
				//$parent = $this->getCatIdFromFactusol($cat->parent, $cat->language);
				//if (!$parent) $parent=1;
				// comprovem si la categoria existeix
				$catid = $this->getCatIdFromFactusol($cat->factusol_codfam, $cat->language, $log);
				if ($catid) { // la categoria existeix. fem un update
					//Només s'actualitza el títol, no el parent
					$update_query =
						"UPDATE #__categories " .
						"SET title = " . $db->quote($cat->title) .
						" WHERE id = " . $catid;
					$db->setQuery( $update_query );
					if ($db->execute()) {
						$updates_categories++;
						$this->customLog($update_query );
					} else {
						$errors_categories++;
						$this->customLog($update_query );
						$errors_categories_detalls[] = $cat->title;
					}
				} else { // la categoria no existeix. fem un insert a #__categories i a #__botiga_categories
					$insert_query1 =
						"INSERT #__categories(extension,title,parent_id,access,published,language) " .
						"VALUES (" .
							$db->quote('com_botiga')  . ", " .
							$db->quote($cat->title)    . ", " .
							"1, 1, 1, " .
							$db->quote($cat->language) . ")";
					$this->customLog($insert_query1 );
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
						$this->customLog($insert_query1 );
						$errors_categories_detalls[] = $cat->title;
					}
				}
			}
			//especifiquem el pare de cada categoria
			$this->customLog("Especificar el pare de cata categoria" );
			foreach($nodes as $cat) {
				// // echo date("d-m-Y H:i:s") . ' ' . $cat->factusol_codfam . ' (' . $cat->language . ') ...';
				if ($cat->parent && $cat->parent!='') {
					$parent = $this->getCatIdFromFactusol($cat->parent, $cat->language);
					if (!$parent) $parent=1; //si no es troba la categoria pare, se li assigna 1
					$catid = $this->getCatIdFromFactusol($cat->factusol_codfam, $cat->language, $log);
					$update_query =
						"UPDATE #__categories " .
						"SET parent_id = " . $parent .
						" WHERE id = " . $catid;
					$this->customLog($update_query  );
					// // echo $update_query . "<br/>";
					$db->setQuery($update_query);
					$db->query();
				} else {
					// echo 'no té pare<br/>';
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
			$this->customLog(
				"DELETE #__botiga_categories " .
				"FROM #__botiga_categories INNER JOIN #__categories ON #__botiga_categories.catid=#__categories.id " .
				"WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "') AND parent_id>1" );
			$db->setQuery(
				"DELETE #__botiga_categories " .
				"FROM #__botiga_categories INNER JOIN #__categories ON #__botiga_categories.catid=#__categories.id " .
				"WHERE factusol_codfam NOT IN ('" . implode( "','", $categoria ) . "') AND parent_id>1" );
			$db->query();
			$this->customLog(
				"DELETE #__categories " .
				"FROM #__categories LEFT JOIN #__botiga_categories ON #__categories.id = #__botiga_categories.catid " .
				"WHERE #__categories.extension='com_botiga' AND #__botiga_categories.catid IS NULL AND parent_id>1" );
			$db->setQuery(
				"DELETE #__categories " .
				"FROM #__categories LEFT JOIN #__botiga_categories ON #__categories.id = #__botiga_categories.catid " .
				"WHERE #__categories.extension='com_botiga' AND #__botiga_categories.catid IS NULL AND parent_id>1" );
			$db->query();
			$deletes_categories = $db->getAffectedRows();

			$this->customLog(
				sprintf(
					"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\nERRORS EN: %s\n",
					$inserts_categories,
					$updates_categories,
					$deletes_categories,
					$errors_categories,
					implode(",", $errors_categories_detalls)) );
		}
		return $errors_categories;
	}

	private function importaArticles()
	{
		$db = JFactory::getDbo();
		$errors_items = 0;

		$xmlfile = JURI::base().'sincro/items.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile");
			$errors_items = 1;
		} else {
			$this->customLog("ITEMS : " . JURI::base() . "sincro/items.xml" );
			$updates_items=0;
			$inserts_items=0;
			$deletes_items=0;
			$errors_items=0;
			$errors_items_detalls = array();
			$db->setQuery( "UPDATE #__botiga_items SET sincronitzat=0" );
			if ($db->query()) {
				foreach($nodes as $item) {
					//carreguem l'id de la categoria
					$db->setQuery( "SELECT catid FROM #__categories INNER JOIN #__botiga_categories ON #__categories.id=#__botiga_categories.catid WHERE #__categories.extension='com_botiga' AND LOWER(#__categories.language)=" . $db->quote(strtolower($item->language)) . " AND #__botiga_categories.factusol_codfam=" . $db->quote($item->factusol_codfam) );
					$db->execute();
					$catid = $db->loadResult();
					if ($catid!==null) {
						//carreguem l'id de la marca
						$db->setQuery(
							"SELECT id FROM #__botiga_brands " .
							"WHERE LOWER(language)='es-es' AND factusol_codfte=" . $item->factusol_codfte );
						$db->execute();
						$brandid = $db->loadResult();
						$update_query =
							"UPDATE #__botiga_items " .
							"SET " .
								"catid = " . $catid . ", " .
								"brand = " . $brandid . ", " .
								"sincronitzat = 1, " .
								"name = " . $db->quote($item->name . (($item->capacitat=='' || $item->capacitat==0) ? '' : ' ' . $item->capacitat . 'ml')) . ", " .
								"s_description = " . $db->quote($item->name . (($item->capacitat=='' || $item->capacitat==0) ? '' : ' ' . $item->capacitat . 'ml')) . ", " .
								"pes = " . $item->pesgrams . ", " .
								"capacitat = " . $db->quote($item->capacitat) . ", " .
								"unitats_per_caixa = " . $item->unitatspercaixa .
							" WHERE LOWER(language) = " . $db->quote(strtolower($item->language)) . " AND factusol_codart = " . $db->quote($item->ref) . " AND @factusol_codart:=LENGTH(factusol_codart)";
						$insert_query =
							"INSERT INTO #__botiga_items(catid,brand,name,s_description,published,ref,".
								"factusol_codart,pes,capacitat,unitats_per_caixa,language,sincronitzat) " .
							"VALUES (" .
								$catid . ", " .
								$brandid . ", " .
								$db->quote($item->name . (($item->capacitat=='' || $item->capacitat==0) ? '' : ' ' . $item->capacitat . 'ml')) . ", " .
								$db->quote($item->name . (($item->capacitat=='' || $item->capacitat==0) ? '' : ' ' . $item->capacitat . 'ml')) . ", " .
								"1, " .
								$db->quote($item->ref) . ", " .
								$db->quote($item->ref) . ", " .
								$item->pesgrams . ", " .
								$db->quote($item->capacitat) . ", " .
								$item->unitatspercaixa . ", " .
								$db->quote(strtolower($item->language)) . ", " .
								" 1)";
						$db->setQuery('SET @factusol_codart := 0;');
						$db->query();
						$db->setQuery( $update_query );
						if($db->query()) {
							$db->setQuery('select @factusol_codart');
							$factusol_codart_updated = $db->loadResult();
							if ($factusol_codart_updated==0) {
								$db->setQuery( $insert_query );
								if ($db->query()) {
									$inserts_items++;
								} else {
									$errors_items++;
									$errors_items_detalls[] = $item->ref;
								}
								$this->customLog($insert_query );
							} else {
								$updates_items++;
								$this->customLog($update_query );
							}
						} else {
							$errors_items++;
							$this->customLog( 'Error ' . $update_query );
							$errors_items_detalls[] = $item->ref;
						}
						// actualització del preu per caixes (definit a la taula botiga_discounts)
						// determinar l'id de l'item en funció de ref i language
						/*$db->setQuery(
							"SELECT id FROM #__botiga_items " .
							"WHERE LOWER(language)=" . $db->quote(strtolower($item->language)) .
								"AND factusol_codart=" . $db->quote($item->ref) );
						$db->execute();
						$itemid = $db->loadResult();
						if ($itemid) {
							$db->setQuery( 'DELETE FROM #__botiga_discounts WHERE iditem=' . $itemid );
							$db->query();
							if ($item->price3!=0) {
								$db->setQuery(
									"INSERT INTO #__botiga_discounts(name, type, iditem, min, max, box_items, total, message, published) " .
									"VALUES ('+12uds', 2, $itemid, 12, 12, 12, $item->price3, 'COM_BOTIGA_DISCOUNT_N_ITEMS', 1)" );
								$db->query();
							}
						}*/
					} else {
						// l'article pertany a una categoria inexistent
						$errors_items++;
						$this->customLog("L'article " . $item->ref . " correspon a una categoria inexistent (" . $item->factusol_codfam . ". És possible que en el Factusol falti activar-la a Internet?" );
						$errors_items_detalls[] = $item->ref;
					}
				}

				//esborrar items...
				$this->customLog("DELETE FROM #__botiga_items WHERE sincronitzat=0"  );
				$db->setQuery( "DELETE FROM #__botiga_items WHERE sincronitzat=0" );
				$db->query();
				$deletes_items = $db->getAffectedRows();
				$this->customLog(
					sprintf(
						"INSERTS: %d\nUPDATES: %d\nDELETES: %d\nERRORS: %d\n",
						$inserts_items,
						$updates_items,
						$deletes_items,
						$errors_items) );
			} else {
				$this->customLog("Error al marcar 'sincronitzar=0'. La sincronització dels articles no s'ha pogut dur a terme."  );
			}
		}
		return $errors_items;
	}

	private function importaTarifes()
	{
		$db = JFactory::getDbo();
		$errors_tarifes = 0;

		$xmlfile = JURI::base().'sincro/tarifes.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile");
			$errors_tarifes = 1;
		} else {
			$this->customLog( "TARIFES : " . JURI::base() . "sincro/tarifes.xml" );
			foreach($nodes as $item) {
				$db->setQuery( 'SELECT COUNT(*) FROM #__usergroups WHERE title=' . $db->quote($item->tarifa) );
				if ($db->loadResult()==0) {
					$this->customLog( "Tarifa nova: " . $item->tarifa );
					$usergroupTable = JTable::getInstance('UserGroup', 'JTable');
					$data = array(
					  'id' => 0,
					  'parent_id' => '2',
					  'title' => strval($item->tarifa)
					);
					if (!$usergroupTable->bind($data)) {
					  return false;
					}
					if (!$usergroupTable->check()) {
					  return false;
					}
					if (!$usergroupTable->store()) {
					  return false;
					}
				}
			}
		}
		return $errors_tarifes;
	}

	private function importaPreus()
	{
		$db = JFactory::getDbo();
		$errors_preus = 0;
		$i=0;
		$xmlfile = JURI::base().'sincro/preus.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile");
			$errors_preus = 1;
		} else {
			$this->customLog("PREUS : " . JURI::base() . "sincro/preus.xml" );
			$db->setQuery( 'UPDATE #__botiga_items_prices SET sincronitzat=0' );
			$db->query();
			foreach($nodes as $xml) {
				$db->setQuery( 'SELECT * FROM #__botiga_items WHERE ref=' . $db->quote($xml->ref) );
				$rows = $db->loadObjectList();
				foreach($rows as $row) {
					//determinar l'id del grup
					$db->setQuery( 'SELECT id FROM #__usergroups WHERE title=' . $db->quote($xml->tarifa) );
					$tarifaId = $db->loadResult();
					$itemId = $row->id;
					if ($tarifaId===null) {
						$this->customLog( 'Error en preu per a ref ' . $xml->ref . ' i tarifa ' . $xml->tarifa . ': tarifa inexistent!' );
					} else {
						$this->aplicaPreu( $itemId, $tarifaId, $xml->tarifa, $xml->ref, $xml->preu );
					}
					// Si el preu és de particulars, s'ha de copiar també per al grup 1 (public)
					if ($xml->tarifa == $this->$TARIFA_PARTICULARS) {
						$this->aplicaPreu( $itemId, 1, 'Public', $xml->ref, $xml->preu );
					}
				}
			}
			$db->setQuery( 'DELETE FROM #__botiga_items_prices WHERE sincronitzat=0' );
			$this->referJsonPreus(); //s'han de fer tots els json de preus que hi ha a la taula #__botiga_items, a partir de la subtaula de preus
		}
		return $errors_preus;
	}

	private function importaClients()
	{
		$db = JFactory::getDbo();

		$errors_clients = 0;

		$xmlfile = JURI::base().'sincro/clients.xml';
		$nodes = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$nodes) {
			$this->customLog("Error carregant $xmlfile");
			$errors_clients = 1;
		} else {
			$this->customLog("CLIENTS : " . JURI::base() . "sincro/clients.xml" );
			foreach ($nodes as $xml) {
				//comprovar si existeix
				$db->setQuery( 'SELECT * FROM #__users WHERE username=' . $db->quote($xml->usuari) );
				$usuari = $db->loadObject();
				if ($usuari==null) {
					$this->crearUsuari($xml);
				} else {
					$this->actualitzarUsuari($xml,$usuari->id);
				}
			}
		}
		return $errors_clients;
	}

	/**
	*  Takes XML string and returns a boolean result where valid XML returns true
	*/
	private function is_valid_xml( $file )
	{
		libxml_use_internal_errors( true );
		$doc = new DOMDocument('1.0', 'utf-8');
		$xml = file_get_contents($file);
		libxml_clear_errors();
		$doc->loadXML( $xml );
		$errors = libxml_get_errors();
		return empty( $errors );
	}

	private function customLog( $text, $resetLog=0 )
	{
		if ($resetLog!=0) {
			$handle = fopen($this->$LOGFILE, 'w');
			fclose($handle);
		}
		botigaHelper::customLog( $text, $this->$LOGFILE );
		return;
	}

	private function aplicaPreu( $itemId, $tarifaId, $tarifaNom, $ref, $preu)
	{
		$db = JFactory::getDbo();
		$db->setQuery( 'SELECT * FROM #__botiga_items_prices WHERE itemId=' . $itemId . ' AND usergroup=' . $tarifaId );
		$rowPreu = $db->loadObject();
		if ($rowPreu===null) {
			// no trobat: cal inserir
			$db->setQuery(
				'INSERT INTO #__botiga_items_prices(itemId, usergroup, price, sincronitzat) ' .
				'VALUES (' . $itemId . ', ' . $tarifaId . ', ' . $preu . ', 1)' );
			$this->customLog( 'Preu per a ref ' . $ref . ' i tarifa ' . $tarifaNom . ' inserit amb èxit.' );
			$db->query();
		} else {
			// trobat: cal actualitzar
			$db->setQuery(
				'UPDATE #__botiga_items_prices SET sincronitzat=1, price=' . $preu .
				' WHERE itemId=' . $itemId . ' AND usergroup=' . $tarifaId );
			$this->customLog( 'Preu per a ref ' . $ref . ' i tarifa ' . $tarifaNom . ' actualitzat amb èxit.' );
			$db->query();
		}
		return;
	}

	private function referJsonPreus()
	{
		$db = JFactory::getDbo();
		$db->setQuery( 'SELECT * FROM #__botiga_items ORDER BY id' );
		$items = $db->loadObjectList();
		foreach($items as $item) {
			$db->setQuery( 'SELECT * FROM #__botiga_items_prices WHERE itemId=' .$item->id . ' ORDER BY usergroup');
			$preus = $db->loadObjectList();
			$strGrups = '';
			$strPreus = '';
			foreach($preus as $preu) {
				$strGrups .= ($strGrups==''?'':',') . '"' . $preu->usergroup . '"';
				$strPreus .= ($strPreus==''?'':',') . '"' . $preu->price . '"';
			}
			$strGrups = '"usergroup":[' . $strGrups . ']';
			$strPreus = '"pricing":[' . $strPreus . ']';
			$json = '{' . $strGrups . ',' . $strPreus . '}';
			$db->setQuery( 'UPDATE #__botiga_items SET price=' . $db->quote($json) . ' WHERE id=' . $item->id );
			$db->query();
		}
	}

	private function crearUsuari($node)
	{
		$db = JFactory::getDbo();
		jimport('joomla.user.helper');
		$password = JUserHelper::hashPassword(substr($node->nif,-4));

		//crear usuari joomla
		$user                   = new stdClass();
		$user->name             = utf8_decode($node->nom);
		$user->username         = strtolower(utf8_decode($node->usuari));
		$user->password         = $password;
		$user->email            = strtolower(utf8_decode($node->usuari));
		$user->registerDate     = date('Y-m-d H:i:s');
		$db->insertObject('#__users', $user);
		$userId = $db->insertid();

		//assignar a grup 2 (registered)
		$group              = new stdClass();
		$group->user_id     = $userId;
		$group->group_id    = 2;
		$db->insertObject('#__user_usergroup_map', $group);

		//assignar grup en funció de la tarifa
		$tarifaId = $this->getGroupIdFromTarifa($node->tarifa);
		if ($tarifaId!==null) {
			$group              = new stdClass();
			$group->user_id     = $userId;
			$group->group_id    = $tarifaId;
			$db->insertObject('#__user_usergroup_map', $group);
		}


		//crear usuari com_botiga
		$usrBotiga = new stdClass();
		$usrBotiga->usergroup 		= $tarifaId;
		$usrBotiga->userId			= $userId;
		$usrBotiga->type			= ($node->tarifa==$this->$TARIFA_PARTICULARS?0:1);
		$usrBotiga->mail_empresa 	= strval($node->usuari);
		$usrBotiga->nombre 			= strval($node->nom);
		$usrBotiga->nom_empresa 	= strval($node->nom_empresa);
		$usrBotiga->cif 			= strval($node->nif);
		$usrBotiga->adreca 			= strval($node->adreca);
		$usrBotiga->cp 				= strval($node->cp);
		$usrBotiga->poblacio 		= strval($node->poblacio);
		$usrBotiga->provincia 		= strval($node->provincia);
		$usrBotiga->pais 			= strval($node->pais==''?'195':$node->pais);
		$usrBotiga->telefon 		= strval($node->telefon);
		$usrBotiga->dte_linia		= floatval($node->dte_linia);
		/*
		20/08/2019: fins ara s'activava la condició de pagament habitual en funció
		de si es tractava d'un usuari particular o empresa. Ara, però, en l'XML
		arriben dos paràmetres nous: pago_habitual_enabled i pago_habitual_desc
		$usrBotiga->params			= '{' .
			'"metodo_pago":"habitual",' .
			'"aplicar_iva":"' . $node->aplicar_iva . '",' .
			'"re_equiv":"' . $node->re . '",' .
			'"pago_habitual":"' . ($node->tarifa==$this->$TARIFA_PARTICULARS?0:1) . '"' .
			'}';
		*/
		$usrBotiga->params			= '{' .
			'"metodo_pago":"' . ($node->pago_habitual_enabled!=0?'habitual':'') . '",' .
			'"aplicar_iva":"' . $node->aplicar_iva . '",' .
			'"re_equiv":"' . $node->re . '",' .
			'"pago_habitual":"' . ($node->pago_habitual_enabled==0?0:1) . '",' .
			'"pago_habitual_desc":"' . $node->pago_habitual_desc . '"' .
			'}';
		$usrBotiga->published		= 1;
		$usrBotiga->validate		= 1;
		$db->insertObject('#__botiga_users', $usrBotiga);

		$this->customLog( 'Nou usuari ' . $node->usuari . "'(" . $node->nom . "')");
	}

	private function actualitzarUsuari($node, $userId)
	{
		$db = JFactory::getDbo();
		jimport('joomla.user.helper');
		// actualitzar usuari joomla
		$db->setQuery(
			'UPDATE #__users ' .
			'SET name=' 	. $db->quote(utf8_decode($node->nom)) .
			', username=' 	. $db->quote(strtolower(utf8_decode($node->usuari))) .
			', email=' 		. $db->quote(strtolower(utf8_decode($node->usuari))) .
			', password=' 	. $db->quote(JUserHelper::hashPassword(substr($node->nif,-4))) .
			' WHERE id=' . $userId );
		$db->query();
		// esborrem els grups als quals pertany
		$db->setQuery( "DELETE FROM #__user_usergroup_map WHERE user_id=$userId" );
		$db->query();
		// assignem l'usuari al grup segons la tarifa actual
		$tarifaId = $this->getGroupIdFromTarifa($node->tarifa);
		if ($tarifaId!==null) {
			$db->setQuery( "INSERT INTO #__user_usergroup_map(user_id, group_id) VALUES($userId, $tarifaId)" );
			$db->query();
		}
		// assignem l'usuari al grup 2
		$db->setQuery( "INSERT INTO #__user_usergroup_map(user_id, group_id) VALUES($userId, 2)" );
		$db->query();

		// actualitzar usuari com_botiga
		$usrBotiga = new stdClass();
		$usrBotiga->usergroup 		= $tarifaId;
		$usrBotiga->userId			= $userId;
		$usrBotiga->type			= ($node->tarifa==$this->$TARIFA_PARTICULARS?0:1);
		$usrBotiga->mail_empresa 	= strval($node->usuari);
		$usrBotiga->nombre 			= strval($node->nom);
		$usrBotiga->nom_empresa 	= strval($node->nom_empresa);
		$usrBotiga->cif 			= strval($node->nif);
		$usrBotiga->adreca 			= strval($node->adreca);
		$usrBotiga->cp 				= strval($node->cp);
		$usrBotiga->poblacio 		= strval($node->poblacio);
		$usrBotiga->provincia 		= strval($node->provincia);
		$usrBotiga->pais 			= strval($node->pais==''?'195':$node->pais);
		$usrBotiga->telefon 		= strval($node->telefon);
		$usrBotiga->dte_linia		= floatval($node->dte_linia);
		/*
		20/08/2019: fins ara s'activava la condició de pagament habitual en funció
		de si es tractava d'un usuari particular o empresa. Ara, però, en l'XML
		arriben dos paràmetres nous: pago_habitual_enabled i pago_habitual_desc
		$usrBotiga->params			= '{' .
			'"metodo_pago":"habitual",' .
			'"aplicar_iva":"' . $node->aplicar_iva . '",' .
			'"re_equiv":"' . $node->re . '",' .
			'"pago_habitual":"' . ($node->tarifa==$this->$TARIFA_PARTICULARS?0:1) . '"' .
			'}';
		*/
		$usrBotiga->params			= '{' .
			'"metodo_pago":"' . ($node->pago_habitual_enabled!=0?'habitual':'') . '",' .
			'"aplicar_iva":"' . $node->aplicar_iva . '",' .
			'"re_equiv":"' . $node->re . '",' .
			'"pago_habitual":"' . ($node->pago_habitual_enabled==0?0:1) . '",' .
			'"pago_habitual_desc":"' . $node->pago_habitual_desc . '"' .
			'}';
		$usrBotiga->published		= 1;
		$usrBotiga->validate		= 1;
		$db->setQuery( "SELECT id FROM #__botiga_users WHERE userid=$userId" );
		$usrBotigaId = $db->loadResult();
		if ($usrBotigaId == null) {
			$db->insertObject('#__botiga_users', $usrBotiga);
			//en teoria, aquí no hauria de passar-hi mai (situació en la que existeix usuari de Joomla però no usuari de com_botiga
		} else {
			$usrBotiga->id = $usrBotigaId;
			$db->updateObject('#__botiga_users', $usrBotiga, 'id', $usrBotigaId);
		}

		$this->customLog( "Usuari actualitzat " . $node->usuari . "'(" . $node->nom . "'" . $node->dte_linia . ")");
	}

	private function getGroupIdFromTarifa($nomTarifa)
	{
		$db = JFactory::getDbo();
		$db->setQuery( 'SELECT id FROM #__usergroups WHERE title=' . $db->quote($nomTarifa) );
		return $db->loadResult();
	}

	private function getCatIdFromFactusol($codfam, $idioma, $log)
	{
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
}
