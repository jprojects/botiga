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

class botigaModelItems extends JModelList
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
				'id', 'i.id',
				'ref', 'i.ref',
				'child', 'i.child',
				'catid', 'i.catid',
				'name', 'i.name',
				'published', 'i.published',
				'language', 'i.language',
				'pvp', 'i.pvp',
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
	public function getTable($type = 'Items', $prefix = 'botigaTable', $config = array())
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
	protected function populateState($ordering = 'i.id', $direction = 'asc')
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

		$catid = $this->getUserStateFromRequest($this->context.'.filter.catid', 'filter_catid', '');
		$this->setState('filter.catid', $catid);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$child = $this->getUserStateFromRequest($this->context.'.filter.child', 'filter_child', '');
		$this->setState('filter.child', $child);

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
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('i.*, b.name AS bname, c.title AS ctitle');

		$query->from('#__botiga_items i');

		$query->join('inner', '#__categories c on c.id = i.catid');

		$query->join('left', '#__botiga_brands b on b.id = i.brand');

        // Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where('(i.name LIKE '.$search.') OR (i.ref LIKE '.$search.')');
		}

		// Filter on the published.
		$published = $this->getState('filter.published');
		if ($published != '') {
			$query->where('i.published = ' . $published);
		}

		// Filter on the child.
		$child = $this->getState('filter.child');
		if ($child != '') {
			$query->where('i.child = ' . $child);
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('i.language = ' . $db->quote($language));
		}

		// Filter by category.
		if ($catid = $this->getState('filter.catid')) {
			$query->where('i.catid = ' . $db->quote($catid));
		}

        // Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'i.id');
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
		$sheet->setCellValue('B1', JText::_('Name'));
		$sheet->setCellValue('C1', JText::_('Brand'));
		$sheet->setCellValue('D1', JText::_('Category'));

		$this->populateState();
		$db   = JFactory::getDbo();
    	$rows = $db->setQuery($this->getListQuery())->loadObjectList();

		$i = 2;
    	foreach($rows as $row) {
			$sheet->setCellValue('A'.$i, $row->id);
			$sheet->setCellValue('B'.$i, $row->name);
			$sheet->setCellValue('C'.$i, $row->brand);
			$sheet->setCellValue('D'.$i, $row->catid);
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
