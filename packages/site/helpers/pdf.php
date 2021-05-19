<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyright (C) 2015. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access
defined('_JEXEC') or die;

/**
 * helper.
 */
class botigaHelperPdf {

	/**
	 * method to get component parameters
	 * @param string $param
	 * @param mixed $default
	 * @return mixed
	*/
	public static function getParameter($param, $default="")
	{
		$params = JComponentHelper::getParams( 'com_botiga' );
		$param = $params->get( $param, $default );

		return $param;
	}
  /*
	* TODO:: esborrar aquesta funció si només es fa servir als plugins
	*/
	public static function enviar($subject, $body, $email, $attach='')
	{
		$mailer 	= JFactory::getMailer();
		$config 	= JFactory::getConfig();

		$fromname  	= $config->get('fromname');
		$mailfrom	= $config->get('mailfrom');

		$sender[]  	= $fromname;
		$sender[]	= $mailfrom;

	    $mailer->setSender( $sender );
	    $mailer->addRecipient( $email );
	    $mailer->setSubject( $subject );
	    $mailer->isHTML(true);
	    $mailer->Encoding = 'base64';
	    $mailer->setBody( $body );

	    if(attach != '') {
	    	$mailer->addAttachment($attach);
	    }

		return $mailer->Send();
	}

     public static function genPdf($id, $mode = 'D', $uid = '', $uniqid = '')
	 {
		jimport('fpdf.fpdf');
		jimport('fpdfi.fpdi');

		$db = JFactory::getDbo();

		$pdf = new FPDI();

		$pdf->AddPage();

		$pdf->setSourceFile(JPATH_COMPONENT.'/assets/pdf/invoice.pdf');

		$showdiscount = botigaHelperPdf::getParameter('show_discount', 0);

		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		define('EURO', chr(128));

		$db->setQuery(
			'SELECT c.*, u.mail_empresa as email, u.nom_empresa, u.nombre, u.cif, u.telefon, u.adreca, u.cp, u.poblacio, ju.name ' .
			'FROM #__botiga_comandes c ' .
				'INNER JOIN #__botiga_users u ON u.userid = c.userid ' .
				'INNER JOIN #__users ju ON u.userid = ju.id ' .
			'WHERE c.id = '.$id);
		$com = $db->loadObject();
		
		$user_params = json_decode(botigaHelper::getUserData('params', $com->userid), true);
		
		if($uniqid == '') $uniqid = $com->uniqid;

		$db->setQuery('SELECT cd.*, i.name, i.ref as referencia, i.image1 as image, i.description FROM #__botiga_comandesDetall cd INNER JOIN #__botiga_items i ON cd.iditem = i.id WHERE cd.idComanda = '.$id);
		$num_rows = $db->getNumRows();
		$detalls  = $db->loadObjectList();

		$height = 10;

		$pdf->SetFont('Arial', '', '10');

		$pag = 1;

		//$pdf->SetXY(175, $height);
		//$pdf->Cell(25, 5, '1/'.$pag, 0, 0, 'R');

		$pdf->SetFont('Arial', 'B', '10');

		//metadata
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, utf8_decode('Pedido: '), 0, 0, 'R');
		$pdf->SetXY(160, $height);
		$pdf->Cell(15, 5, utf8_decode('Fecha: '), 0, 0, 'R');

		$pdf->SetFont('Arial', '', '10');

		$pdf->SetXY(140, $height);
		$pdf->Cell(15, 5, $id, 0, 0, 'R');
		$pdf->SetXY(180, $height);
		$pdf->Cell(15, 5, date('d-m-Y', strtotime($com->data)), 0, 0, 'R');

		$height += 10;

		//user
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, utf8_decode($com->nom_empresa), 0, 0, 'L');
		$height += 5;
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, utf8_decode($com->nombre), 0, 0, 'L');
		$height += 5;
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, utf8_decode($com->adreca), 0, 0, 'L');
		$height += 5;
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, $com->email, 0, 0, 'L');
		$height += 5;
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, $com->cp.' - '.utf8_decode($com->poblacio), 0, 0, 'L');
		$height += 5;
		$pdf->SetXY(130, $height);
		$pdf->Cell(15, 5, $com->telefon, 0, 0, 'L');

		$height += 10;
		$pdf->SetFont('Arial', 'B', '10');

		if($com->status == 4) {
			$pdf->SetTextColor(255, 0, 0);
			$pdf->SetXY(20, $height);
			$pdf->Cell(30, 5, JText::_('COM_BOTIGA_HISTORY_STATUS_PENDING'), 0, 0, '');
			$pdf->SetTextColor(255, 0, 0);
			$pdf->SetTextColor(0, 0, 0);
		}

		$height += 5;
		$pdf->SetXY(20, $height);
		
		$formaPag = $com->processor;
		if (strtolower($com->processor)=='habitual' && $user_params['pago_habitual_desc']!='') {
			$formaPag .= ' - ' . $user_params['pago_habitual_desc'];
		}
		$pdf->Cell(30, 5, JText::_('COM_BOTIGA_HISTORY_PROCESSOR').': '.$formaPag, 0, 0, '');

		$height += 10;
		$pdf->SetFont('Arial', 'B', '8');

		$pdf->SetXY(20, $height);
		$pdf->Cell(30, 5, JText::_('COM_BOTIGA_INVOICE_REF'), 0, 0, '');
		$pdf->SetXY(45, $height);
		$pdf->Cell(90, 5, utf8_decode(JText::_('COM_BOTIGA_INVOICE_DESC')), 0, 0, '');
		$pdf->SetXY(130, $height);
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_QTY'), 0, 0, 'R');
		$pdf->SetXY(145, $height);
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_UNIT_PRICE'), 0, 0, 'R');
		$pdf->SetXY(160, $height);
		$pdf->Cell(20, 5, JText::_('%dto'), 0, 0, 'R');
		$pdf->SetXY(180, $height);
		$pdf->Cell(15, 5, JText::_('COM_BOTIGA_INVOICE_AMOUNT'), 0, 0, 'R');

		// Detall del document
		$total   = 0;
		$height  += 10;
		$counter = 0;
		$total_qty = 0;

		foreach($detalls as $detall) {

			if($counter == 40) {
				$pdf->AddPage();
				$pag++;
				$pdf->setSourceFile(JPATH_COMPONENT.'/assets/pdf/invoice.pdf');
				$tplIdx2 = $pdf->importPage(1);
				$pdf->useTemplate($tplIdx2, 0, 0, 0, 0, true);
			}

			$pdf->SetFont('Arial', '', '8');

			$pdf->SetXY(20, $height);
			$pdf->Cell(30, 5, $detall->referencia, 0, 0, '');
			//$pdf->Image(JURI::root().$detall->image, 20, ($height-4), 5, 10, 'JPG');

			$pdf->SetXY(45, $height);
			$pdf->Cell(90, 5, utf8_decode($detall->name), 0, 0, '');

			$pdf->SetXY(130, $height);
			$pdf->Cell(20, 5, $detall->qty, 0, 0, 'R');

			$pdf->SetXY(145, $height);
			if($detall->dte_linia > 0) { $preu = botigaHelper::getPercentDiff($detall->price, $detall->dte_linia); } else { $preu = $detall->price; }
			$pdf->Cell(20, 5, number_format($preu, 2, ',', '.'), 0, 0, 'R');

			$pdf->SetXY(160, $height);
			$pdf->Cell(20, 5, number_format($detall->dte_linia, 2, ',', '.'), 0, 0, 'R');

			$pdf->SetXY(180, $height);
			$pdf->Cell(15, 5, number_format(($preu * $detall->qty), 2, ',', '.').EURO, 0, 0, 'R');

			$height += 5;
			$counter++;
			$total_qty += $detall->qty;
		}

		// Total d'unitats al peu del detall
		$pdf->SetFont('Arial', 'B', '8');

		$pdf->SetXY(45, $height);
		$pdf->Cell(90, 5, 'Total unidades:', 0, 0, 'R');

		$pdf->SetXY(130, $height);
		$pdf->Cell(20, 5, $total_qty, 0, 0, 'R');

		// Peu del document
		$height = 244;
		$pdf->SetFont('Arial', 'B', '10');

		$pdf->SetXY(20, $height);
		$pdf->Cell(19, 5, JText::_('COM_BOTIGA_INVOICE_GROSS'), 0, 0, '');
		$pdf->SetXY(40, $height);
		$pdf->Cell(18, 5, JText::_('COM_BOTIGA_INVOICE_DISCOUNTS'), 0, 0, '');
		$pdf->SetXY(59, $height);
		$pdf->Cell(19, 5, JText::_('COM_BOTIGA_INVOICE_SHIPMENT'), 0, 0, '');
		$pdf->SetXY(79, $height);
		$pdf->Cell(18, 5, JText::_('COM_BOTIGA_INVOICE_BASE'), 0, 0, '');
		$pdf->SetXY(98, $height);
		$pdf->Cell(18, 5, JText::_('COM_BOTIGA_INVOICE_IVA'), 0, 0, 'R');
		$pdf->SetXY(117, $height);
		$pdf->Cell(18, 5, JText::_('COM_BOTIGA_INVOICE_IMPORT_IVA'), 0, 0, 'R');
		$pdf->SetXY(136, $height);
		$pdf->Cell(18, 5, JText::_('COM_BOTIGA_INVOICE_RE'), 0, 0, 'R');
		$pdf->SetXY(155, $height);
		$pdf->Cell(19, 5, JText::_('COM_BOTIGA_INVOICE_IMPORT_RE'), 0, 0, 'R');
		$pdf->SetXY(175, $height);
		$pdf->Cell(20, 5, JText::_('COM_BOTIGA_INVOICE_TOTAL'), 0, 0, 'R');

		$height += 10;
		$pdf->SetFont('Arial', '', '10');

		$pdf->SetXY(20, $height);
		$pdf->Cell(19, 5, number_format($com->subtotal, 2, ',', '.').EURO, 0, 0, '');
		$pdf->SetXY(40, $height);
		$pdf->Cell(18, 5, number_format($com->discount, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(59, $height);
		$pdf->Cell(19, 5, number_format($com->shipment, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(79, $height);
		$pdf->Cell(19, 5, number_format($com->subtotal-$com->discount+$com->shipment, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(98, $height);
		$pdf->Cell(18, 5, $com->iva_percent . '%', 0, 0, 'R');
		$pdf->SetXY(117, $height);
		$pdf->Cell(18, 5, number_format($com->iva_total, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(136, $height);
		$pdf->Cell(18, 5, $com->re_percent . '%', 0, 0, 'R');
		$pdf->SetXY(155, $height);
		$pdf->Cell(18, 5, number_format($com->re_total, 2, ',', '.').EURO, 0, 0, 'R');
		$pdf->SetXY(175, $height);
		$pdf->Cell(18, 5, number_format($com->total, 2, ',', '.').EURO, 0, 0, 'R');

		$pdf->Output('order_'.$uniqid.'.pdf', $mode);
		if($uid == '') { die(); }
		return $uniqid;
	 }

}
