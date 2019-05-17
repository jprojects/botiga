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

require_once JPATH_ROOT.'/libraries/PhpOffice/vendor/autoload.php';
		
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class botigaModelBrands extends JModelList
{
    /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	*/
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
			'id', 'a.id',
			'name', 'a.name',
			'published', 'a.published',
			'language', 'a.language',
			);
		}
		parent::__construct($config);
	}
        
    /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	*/
	public function getTable($type = 'Brands', $prefix = 'botigaTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
        
    /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	*/
	protected function populateState($ordering = 'a.ordering', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_botiga');
		$this->setState('params', $params);
                
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', 1);
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState($ordering, $direction);
	}
        
    /**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	*/
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	*/
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		// Select some fields
		$query->select('a.*');

		$query->from('#__botiga_brands as a');
                
        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(a.name LIKE '.$search.')');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter on the language.
		if ($published = $this->getState('filter.published')) {
			$query->where('a.published = ' . $db->quote($published));
		}
                
        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.ordering');
		$orderDirn	= $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
        //echo $query;        
		return $query;
	}
	
	/**
	 * Method to export a csv from history
	 * @return boolean true if success false if not
	*/
	public function getXls()
	{		
		// Create new PHPExcel object
		$spreadsheet = new Spreadsheet();

        // Add some data
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', JText::_('Id'));
		$sheet->setCellValue('B1', JText::_('Brand'));
		$sheet->setCellValue('C1', JText::_('CodFte'));
		$sheet->setCellValue('D1', JText::_('Language'));

		$this->populateState();
		$db   = JFactory::getDbo();
        $rows = $db->setQuery($this->getListQuery())->loadObjectList();

		$i = 2;
    	foreach($rows as $row) {
			$sheet->setCellValue('A'.$i, $row->id);
			$sheet->setCellValue('B'.$i, $row->name);
			$sheet->setCellValue('C'.$i, $row->factusol_codfte);
			$sheet->setCellValue('D'.$i, $row->language);
			$i++;
    	}

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = new Xlsx($spreadsheet);
		$objWriter->save('php://output');
		exit(0);
	}
}
