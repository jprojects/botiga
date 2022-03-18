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

		$db->setQuery('SELECT id, catid, ref, name, pvp, language FROM `#__shop_items`');
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
		
		// $table1     = '#__shop_items_prices_test';
		// $table2     = '#__shop_items_test';
		$table1     = '#__shop_items_prices';
		$table2     = '#__shop_items';
		
		$filename = File::makeSafe($file['csv']['name']);

		$src = $file['csv']['tmp_name'];
		$dest = JPATH_ROOT . DS . "tmp" . DS . $filename;
		$extension = strtolower(File::getExt($filename)); 

		if($extension == 'xls' || $extension == 'xlsx') {
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

					$db->setQuery('UPDATE '.$table2.' SET price = '.$db->quote(json_encode($preus)).', pvp = '.$db->quote($pvp).' WHERE id = '.$tarifa->itemId);
					$result2 = $db->query();

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

		$this->setRedirect('index.php?option=com_shop&view=tools&layout=edit', $msg, $type);
	}
}
