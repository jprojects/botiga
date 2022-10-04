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

include(JPATH_COMPONENT_ADMINISTRATOR.'/libs/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Joomla\CMS\Filesystem\File;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text; 

class botigaControllerTools extends JControllerForm
{
	/**
	 * cancel task.
	 * @since	1.6
	*/
	public function cancel() 
	{
		$link = "index.php?option=com_botiga";
		$this->setRedirect($link);
	}

	public function export()
	{
		$db = Factory::getDbo();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$post_data  = Factory::getApplication()->input->get( 'post' );
   		$data       = $post_data["jform"];
   		
		$usergroup  = $data['usergroup'];

		$db->setQuery('SELECT id, catid, ref, name, pvp, language FROM `#__botiga_items`');
		$rows = $db->loadObjectList();
		
		$sheet->setCellValue('A1', 'Id');
		$sheet->setCellValue('B1', 'Tarifa');
		$sheet->setCellValue('C1', 'Ref');
		$sheet->setCellValue('D1', 'Nom');
		$sheet->setCellValue('E1', 'Idioma');
		$sheet->setCellValue('F1', 'PVP');
		$sheet->setCellValue('G1', 'Preu');

		$i = 2;
		foreach($rows as $row) {
			$sheet->setCellValue('A'.$i, $row->id);
			$sheet->setCellValue('B'.$i, $usergroup);
			$sheet->setCellValue('C'.$i, $row->ref);
			$sheet->setCellValue('D'.$i, $row->name);
			$sheet->setCellValue('E'.$i, $row->language);
			$sheet->setCellValue('F'.$i, $row->pvp);
			$sheet->setCellValue('G'.$i, '');
			
			$i++;
		}
		
		$sheet->setTitle("Productes");
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="productes.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = new Xlsx($spreadsheet);
		$objWriter->save('php://output');
		exit(0);
	}

	public function import()
	{   
		$db			= Factory::getDbo();
		$jinput 	= Factory::getApplication()->input;
		$file  		= $jinput->files->get('jform');  
		
		// $table1     = '#__botiga_items_prices_test';
		// $table2     = '#__botiga_items_test';
		$table1     = '#__botiga_items_prices';
		$table2     = '#__botiga_items';
		
		$filename = File::makeSafe($file['csv']['name']);

		$src = $file['csv']['tmp_name'];
		$dest = JPATH_ROOT . "/tmp/" . $filename;
		$extension = strtolower(File::getExt($filename)); 
		$allow = array('xls', 'xlsx', 'xlsx', 'xlsm', 'xlt');

		if(in_array($extension, $allow)) {
			if(File::upload($src, $dest) ) {

				// $spreadsheet = IOFactory::load($dest);
				// $sheetData = $spreadsheet->getActiveSheet()->toArray();
				// $this->customLog(print_r($sheetData));

				$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
				$reader->setReadDataOnly(TRUE);
				$spreadsheet = $reader->load($dest); //Load the excel form
				 
				$worksheet = $spreadsheet->getActiveSheet();
 				$highestRow = $worksheet->getHighestRow(); // total number of rows
 				$highestColumn = $worksheet->getHighestColumn(); // total number of columns
				$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; ++$row) {

					//insert into shop_items_prices
					$tarifa 			= new stdClass();
					$tarifa->itemId 	= $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$tarifa->usergroup 	= $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$pvp 				= $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$tarifa->price 		= $worksheet->getCellByColumnAndRow(7, $row)->getValue();

					$db->setQuery('SELECT id FROM '.$table1.' WHERE itemId = '.$tarifa->itemId.' AND usergroup = '.$tarifa->usergroup);
					//$this->customLog('SELECT id FROM '.$table1.' WHERE itemId = '.$tarifa->itemId.' AND usergroup = '.$tarifa->usergroup);
					if($id = $db->loadResult()) {
						//$this->customLog('update '.$id);
						$tarifa->id = $id;
						$result1 = $db->updateObject($table1, $tarifa, 'id');
					} else {
						//$this->customLog('Insert');
						$result1 = $db->insertObject($table1, $tarifa);
					}

					//insert into shop_items
					$db->setQuery('SELECT usergroup,price FROM '.$table1.' WHERE itemId = '.$tarifa->itemId);
					$items = $db->loadObjectList();

					$i = 0;
					foreach($items as $item) {
						
						$preus['usergroup'][$i] = $item->usergroup;
						$preus['pricing'][$i] = $item->price;
						$i++;
					}

					$preusJSON = '{"price0":{"usergroup":"'.$preus['usergroup'][0].'","pricing":"'.$preus['pricing'][0].'"},"price1":{"usergroup":"'.$preus['usergroup'][1].'","pricing":"'.$preus['pricing'][1].'"}}';

					$db->setQuery('UPDATE '.$table2.' SET price = '.$db->quote($preusJSON).', pvp = '.$db->quote($pvp).' WHERE id = '.$tarifa->itemId);
					$result2 = $db->execute();

					if($result1 && $result2) {
						$msg  = 'Importació completada amb èxit.';
						$type = 'success';
					} else {
						$msg  = 'La importació ha fallat, torna a provar o revisar el fitxer.';
						$type = 'error';
					}

				}
			} else {
				$msg  = 'El fitxer no ha estat copiat correctament.';
				$type = 'error';
			}
		} else {
			$msg  = 'Tipus de fitxer no soportat.';
			$type = 'error';
		}

		$this->setRedirect('index.php?option=com_botiga&view=tools&layout=edit', $msg, $type);
	}

	/**
	* Utilitat per passar  dades de la db de j3 a j4
	*/
	public function importFromJ3() {

		$jinput 	= Factory::getApplication()->input;
		$post_data  = Factory::getApplication()->input->get( 'post' );
   		$data       = $post_data["jform"]; 

		$option = array();

		$option['driver']   = 'mysql';          	// Database driver name
		$option['host']     = $data['host'];    	// Database host name
		$option['user']     = $data['user'];  		// User for database authentication
		$option['password'] = $data['password'];   	// Password for database authentication
		$option['database'] = $data['database'];    // Database name
		$option['prefix']   = $data['prefix'];      // Database prefix (may be empty)

		$j3db = JDatabaseDriver::getInstance( $option );

		//sync botiga brands
		$j3db->setQuery('SELECT * FROM `#__botiga_brands`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_brands`');
		$j4db->execute();

		foreach($rows as $row) {

			$brand 				= new stdClass();
			$brand->id 			= $row->id;
			$brand->name 		= $row->name;
			$brand->published 	= $row->published;
			$brand->language 	= $row->language;
			$brand->image 		= $row->image;
			$brand->header 		= $row->header;
			$brand->factusol_codfte = $row->factusol_codfte;
			$brand->ordering 	= $row->ordering;

			$j4db->insertObject('#__botiga_brands', $brand);
		}

		//sync botiga comandes
		$j3db->setQuery('SELECT * FROM `#__botiga_comandes`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_comandes`');
		$j4db->execute();

		foreach($rows as $row) {

			$j3db->setQuery('SELECT * FROM `#__botiga_users` WHERE userid = '.$row->userid);
			$j3user = $j3db->loadObject();

			$comanda = new stdClass();
			$comanda->id 		= $row->id;
			$comanda->uniqid 	= $row->uniqid;
			$comanda->data 		= $row->data;
			$comanda->userid 	= $row->userid;
			$comanda->status 	= $row->status;
			$comanda->subtotal 	= $row->subtotal;
			$comanda->shipment 	= $row->shipment;
			$comanda->iva_total = $row->iva_total;
			$comanda->iva_percent = $row->iva_percent;
			$comanda->total 	= $row->total;
			$comanda->observa 	= $row->observa;
			$comanda->name 		= $j3user->nom_empresa;
			$comanda->adreca	= $j3user->adreca;
			$comanda->cp 		= $j3user->cp;
			$comanda->poblacio 	= $j3user->poblacio;
			$comanda->pais 		= $j3user->pais;
			$comanda->nom_empresa = $j3user->nom_empresa;
			$comanda->mail_empresa= $j3user->mail_empresa;
			$comanda->telefon 	= $j3user->telefon;
			$comanda->ordering 	= $row->ordering;

			$j4db->insertObject('#__botiga_comandes', $comanda);
		}

		//sync botiga comandes detall
		$j3db->setQuery('SELECT * FROM `#__botiga_comandesDetall`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_comandesDetall`');
		$j4db->execute();

		foreach($rows as $row) {

			$detall = new stdClass();
			$detall->idComanda 	= $row->idComanda;
			$detall->idItem 	= $row->idItem;
			$detall->price 		= $row->price;
			$detall->qty 		= $row->qty;

			$j4db->insertObject('#__botiga_comandesDetall', $detall);
		}

		//sync botiga favorites
		$j3db->setQuery('SELECT * FROM `#__botiga_favorites`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_favorites`');
		$j4db->execute();

		foreach($rows as $row) {

			$fav = new stdClass();
			$fav->id 			= $row->id;
			$fav->itemid 		= $row->itemid;
			$fav->userid 		= $row->userid;
			$fav->ordering 		= $row->ordering;

			$j4db->insertObject('#__botiga_favorites', $fav);
		}

		//sync botiga items
		$j3db->setQuery('SELECT * FROM `#__botiga_items`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_items`');
		$j4db->execute();

		foreach($rows as $row) {

			$item = new stdClass();
			$item->id 			= $row->id;
			$item->catid 		= $row->catid;
			$item->name 		= $row->name;
			$item->alias 		= $row->alias;
			$item->brand 		= $row->brand;
			$item->s_description = $row->s_description;
			$item->description	= $row->description;
			$item->image1		= $row->image1;
			$item->images		= '{"images0":{"image":"'.$row->image2.'"}, "images1":{"image":"'.$row->image3.'"},"images2":{"image":"'.$row->image4.'"},"images3":{"image":"'.$row->image5.'"},}';
			$item->pdf			= $row->pdf;
			$item->price		= $row->price;
			$item->pvp			= (float)$row->pvp;
			$item->garantia		= $row->garantia;
			$item->envio		= $row->envio;
			$item->published	= $row->published;
			$item->ordering		= $row->ordering;
			$item->language		= $row->language;
			$item->ref			= $row->ref;
			$item->factusol_codart= $row->factusol_codart;
			$item->sincronitzat = $row->sincronitzat;
			$item->publish_up   = $row->publish_date;

			$j4db->insertObject('#__botiga_items', $item);
		}

		//sync botiga prices
		$j3db->setQuery('SELECT * FROM `#__botiga_items_prices`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_items_prices`');
		$j4db->execute();

		foreach($rows as $row) {

			$price = new stdClass();
			$price->itemId 		= $row->itemId;
			$price->usergroup 	= $row->usergroup;
			$price->price 		= $row->price;

			$j4db->insertObject('#__botiga_items_prices', $price);
		}

		//sync botiga rebuts
		$j3db->setQuery('SELECT * FROM `#__botiga_rebuts`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_rebuts`');
		$j4db->execute();

		foreach($rows as $row) {

			$rebut = new stdClass();
			$rebut->id 				= $row->id;
			$rebut->data 			= $row->data;
			$rebut->userid 			= $row->userid;
			$rebut->import 			= $row->import;
			$rebut->idComanda 		= $row->idComanda;
			$rebut->formaPag 		= $row->formaPag;
			$rebut->payment_status 	= $row->payment_status;

			$j4db->insertObject('#__botiga_rebuts', $rebut);
		}

		//sync botiga saved carts
		$j3db->setQuery('SELECT * FROM `#__botiga_savedCarts`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_savedCarts`');
		$j4db->execute();

		foreach($rows as $row) {

			$cart = new stdClass();
			$cart->id 			= $row->id;
			$cart->idComanda 	= $row->idComanda;
			$cart->data 		= $row->data;
			$cart->userid 		= $row->userid;
			$cart->cart 		= $row->cart;

			$j4db->insertObject('#__botiga_savedCarts', $cart);
		}

		// //sync botiga shipments
		$j3db->setQuery('SELECT * FROM `#__botiga_shipments`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_shipments`');
		$j4db->execute();

		foreach($rows as $row) {

			$ship = new stdClass();
			$ship->id 			= $row->id;
			$ship->name 		= $row->name;
			$ship->usergroup 	= $row->usergroup;
			$ship->type			= $row->type;
			$ship->country		= $row->country;
			$ship->min			= $row->min;
			$ship->max			= $row->max;
			$ship->total		= $row->total;
			$ship->published	= $row->published;
			$ship->ordering		= $row->ordering;

			$j4db->insertObject('#__botiga_shipments', $ship);
		}

		// //sync botiga users
		$j3db->setQuery('SELECT * FROM `#__botiga_users`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__botiga_users`');
		$j4db->execute();
		$j4db->setQuery('TRUNCATE TABLE `#__botiga_user_address`');
		$j4db->execute();

		foreach($rows as $row) {

			$users = new stdClass();
			$users->id 				= $row->id;
			$users->usergroup 		= $row->usergroup;
			$users->nom_empresa		= $row->nom_empresa;
			$users->mail_empresa	= $row->mail_empresa;
			$users->cif				= $row->cif;
			$users->userid			= $row->userid;
			$users->adreca			= $row->adreca;
			$users->cp				= $row->cp;
			$users->poblacio		= $row->poblacio;
			$users->pais			= $row->pais;
			$users->telefon			= $row->telefon;
			$users->published		= $row->published;
			$users->validate  		= 1;
			$users->metodo_pago		= $row->metodo_pago;

			$j4db->insertObject('#__botiga_users', $users);

			$address = new stdClass();
			$address->id			= $row->id;
			$address->name			= $row->nom_empresa;
			$address->cif			= $row->cif;
			$address->userid		= $row->userid;
			$address->adreca		= $row->adreca;
			$address->cp			= $row->cp;
			$address->poblacio		= $row->poblacio;
			$address->pais			= $row->pais;
			$address->telefon		= $row->telefon;
			$address->activa        = 1;
			$address->factura       = 1;

			$j4db->insertObject('#__botiga_user_address', $address);
		}

		//sync joomla users
		$j3db->setQuery('SELECT * FROM `#__users`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__users`');
		$j4db->execute();

		foreach($rows as $row) {

			$jusers = new stdClass();
			$jusers->id 			= $row->id;
			$jusers->name 			= $row->name;
			$jusers->username		= $row->username;
			$jusers->email			= $row->email;
			$jusers->password		= $row->password;
			$jusers->block			= $row->block;
			$jusers->params			= $row->params;
			$jusers->registerDate	= $row->registerDate;
			$jusers->lastvisitDate	= $row->lastvisitDate;

			$j4db->insertObject('#__users', $jusers);
		}

		//sync joomla usergroups
		$j3db->setQuery('SELECT * FROM `#__usergroups`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__usergroups`');
		$j4db->execute();

		foreach($rows as $row) {

			$groups = new stdClass();
			$groups->parent_id 	= $row->parent_id;
			$groups->lft		= $row->lft;
			$groups->rgt		= $row->rgt;
			$groups->title		= $row->title;

			$j4db->insertObject('#__usergroups', $groups);
		}

		//sync joomla usergroups map
		$j3db->setQuery('SELECT * FROM `#__user_usergroup_map`');
		$rows = $j3db->loadObjectList();

		$j4db->setQuery('TRUNCATE TABLE `#__user_usergroup_map`');
		$j4db->execute();

		foreach($rows as $row) {

			$map = new stdClass();
			$map->user_id   = $row->user_id;
			$map->group_id	= $row->group_id;

			$j4db->insertObject('#__user_usergroup_map', $map);
		}

		$this->setRedirect('index.php?option=com_botiga&view=tools&layout=edit', 'Import finished', 'info');

	}
}
