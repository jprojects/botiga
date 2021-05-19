<?php
/**
 * @version		1.0.0 laundry $
 * @package		laundry
 * @copyright Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

// no direct access
defined('_JEXEC') or die;

class botigaViewBotiga extends JViewLegacy
{
	function display( $tpl = null )
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$start = $app->input->get('start', 1, 'get');
		$end   = $app->input->get('end', 0, 'get');
		$token = $app->input->get('token', 0, 'get');

		$db->setQuery('SELECT id FROM #__botiga_comandes WHERE status>2 AND id BETWEEN '.$start.' AND '.$end);
		$comandes = $db->loadObjectList();

		$i = 0;

		foreach($comandes as $comanda) {
			$db->setQuery(
				'SELECT u.email, bu.nom_empresa, bu.nombre AS nom_contacte, ' .
					'bu.type, bu.adreca, bu.cp, bu.poblacio, bu.provincia, ' .
					'bu.pais, bu.telefon, bu.cif, c.id, c.status, c.data, ' .
					'c.shipment AS ports, ' .
					'c.total-c.iva_total-c.re_total AS base, c.total AS import, ' .
					'c.processor AS forma_pagament, ug.title AS tarifa' .
				' FROM #__botiga_comandes AS c ' .
					'LEFT JOIN #__botiga_users AS bu ON c.userid = bu.userid ' .
					'INNER JOIN #__users AS u ON c.userid = u.id ' .
					'INNER JOIN #__usergroups ug ON bu.usergroup = ug.id ' .
				' WHERE c.id = ' . $comanda->id );
				/*' GROUP BY c.id, c.status, c.data, u.email, bu.nom_empresa, bu.nombre, bu.type, bu.adreca, bu.cp, ' .
					'bu.poblacio, bu.provincia, bu.pais, bu.telefon, bu.cif' );*/
			$data[$i]['comanda'] = $db->loadObject();
			foreach($data as $dat) {
				$db->setQuery(
					'SELECT i.factusol_codart, i.name AS descripcio, ' .
						'cd.price AS preu, cd.qty AS quantitat, ' .
						'cd.price*cd.qty AS import_brut, ' .
						'cd.dte_linia AS perc_dte, ' .
						'cd.price*cd.qty*dte_linia/100 AS import_dte, ' .
						'cd.price*cd.qty*(1-cd.dte_linia/100) AS base, ' .
						'c.iva_percent AS perc_iva, ' .
						'c.re_percent AS perc_re ' .
					'FROM #__botiga_comandes AS c ' .
						'INNER JOIN #__botiga_comandesDetall AS cd ON c.id = cd.idComanda ' .
						'INNER JOIN #__botiga_items AS i ON cd.idItem = i.id ' .
					'WHERE cd.idComanda = '.$comanda->id);
				$data[$i]['detall'] = $db->loadObjectList();
				//var_dump($data[$i]['detall']);
			}
			$i++;
		}

		// Output the JSON data.
		if($token == 'd3fc0n3') { echo json_encode( $data ); } else { echo 'token invalid!'; }

	}
}
?>
