<?php

/**
 * @version     1.0.0
 * @package     com_botiga
 * @copyright   Copyleft (C) 2019
 * @license     Licencia Pública General GNU versión 3 o posterior. Consulte LICENSE.txt
 * @author      aficat <kim@aficat.com> - http://www.afi.cat
*/

// No direct access
defined('_JEXEC') or die;

/**
 * helper.
*/
class botigaHelper {

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

	public function getAddresses()
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();

		$db->setQuery('SELECT * FROM #__botiga_user_address WHERE userid = '.$user->id.' ORDER BY activa DESC');
		return $db->loadObjectList();
	}

	/**
	 * method to get percent discount
	 * @return int
	*/
	public static function getPercentDiff($price, $percent)
    	{
		$result = ($price * $percent) / 100;
		return number_format(($price-$result), 2, '.', '');
    	}

    /**
	 * method to get payment plugins
	 * @return mixed
	*/
    public static function getPaymentPlugins()
    	{
	    $db = JFactory::getDbo();
	    $db->setQuery("SELECT params FROM #__extensions WHERE type = 'plugin' AND folder = 'botiga' AND enabled = 1 AND name NOT LIKE '%sync%'");
	    return $db->loadObjectList();
    }

    /**
	 * method to get all countries
	 * @return mixed
	*/
	public static function getCountries()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT country_id, country_name FROM #__botiga_countries WHERE published = 1 ORDER BY country_name');
		return $db->loadObjectList();
	}

    /**
	 * method to get user data
	 * @param string $field database field request
	 * @return mixed
	*/
   	public static function getUserData($field, $userid)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_users where userid = ".$userid);
		return $db->loadResult();
	}

	/**
	 * method to get comanda data
	 * @param string $field database field request
	 * @return mixed
	*/
    public static function getItemData($field, $id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_items where id = ".$id);
		return $db->loadResult();
	}

	/**
	 * method to know if user is company or not
	 * @param int $userid The userid, if 0 the current userid
	 * @return bool
	*/
   	public static function isEmpresa($userid=0)
	{
		$user 	= JFactory::getUser();
		$db 	= JFactory::getDbo();

		if($user->guest) { return false; }
		$db->setQuery("select type from #__botiga_users where userid = ".$user->id);
		$type = $db->loadResult();

		if($type == 0) { return false; } else { return true; } //0 customer 1 company
	}

	/**
	 * method to get all documents associated to a one document
	 * @param string $id database item id
	 * @return mixed
	*/
    public static function getItemDocuments($id)
	{
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage()->getTag();
		$db->setQuery("select * from #__botiga_documents where idItem = ".$id." AND language = ".$db->quote($lang));
		return $db->loadObjectList();
	}

	/**
	 * method to get comanda data
	 * @param string $field database field request
	 * @return mixed
	*/
    public static function getComandaData($field, $idComanda)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select $field from #__botiga_comandes where id = ".$idComanda);
		return $db->loadResult();
	}

	/**
	 * method to get all categories
	 * @return mixed
	*/
	public static function getCategories()
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT id, title ' .
			' FROM #__categories ' .
			' WHERE extension = ' . $db->quote('com_botiga') .
				' AND published=1 ' .
				' AND language = ' . $db->quote(JFactory::getLanguage()->getTag()) .
			' ORDER BY id ASC' );
		return $db->loadObjectList();
	}

	/**
	 * method to get category name
	 * @return mixed
	*/
    public static function getCategoryName($catid=0)
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$catid = $app->input->get('catid', $catid);

		if($catid > 0) {
			$db->setQuery("select title from #__categories where id = ".$catid);
			return $db->loadResult();
		} else {
			return JText::_('COM_BOTIGA_FAVORITES_TITLE');
		}
	}

	/**
	 * method to get item name
	 * @param string $field database field request
	 * @return mixed
	*/
    public static function getItemName()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$id = $app->input->get('id');

		$db->setQuery("select name from #__botiga_items where id = ".$id);
		return $db->loadResult();
	}

	/**
	 * method to know if item is favorite
	 * @param string $field database field request
	 * @return mixed
	*/
    public static function isFavorite($itemid)
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();

		$db->setQuery("select id from #__botiga_favorites where itemid = ".$itemid." and userid = ".$user->id);
		if($id = $db->loadResult()) { return true; }
		return false;
	}

	/**
	 * method to know the number of items in cart
	 * @return int
	*/
	public static function getCarritoCount()
    {
		$db 	 = JFactory::getDbo();
		$session = JFactory::getSession();
		$user 	 = JFactory::getUser();
			
		//check if a comanda exist
		$db->setQuery('select id from #__botiga_comandes where userid = '.$user->id.' and status < 3 ORDER BY id DESC');
		$id = $db->loadResult();
		if($id > 0) { $session->set('idComanda', $id); }

		$idComanda = $session->get('idComanda', '');
		//echo $idComanda;
		if($idComanda != '') {
			$db->setQuery('SELECT SUM(qty) FROM #__botiga_comandesDetall WHERE idComanda = '.$idComanda);
			return $db->loadResult();
		} else {
			return 0;
		}
    }

    /**
	 * method to know if coupon is in use and its values
	 * @param int $idCoupon the id of the coupon
	 * @param float $total the order total amount
	 * @return int
	*/
	public static function getCouponDiscount($idCoupon, $total)
    {
   		$db = JFactory::getDbo();

   		$db->setQuery('SELECT * from #__botiga_coupons WHERE id = '.$idCoupon);
   		$row = $db->loadObject();

   		//0 - percentatge 1- resta
   		if($row->tipus == 0) {
   			$value = ($total * $row->valor) / 100;
   		} else {
   			$value = $total - $row->valor;
   		}

   		return number_format($value, 2, '.', '');
    }

    /**
	 * method to know if user is validated
	 * @return int
	*/
	public static function isValidated()
    {
   		$db = JFactory::getDbo();
   		$user = JFactory::getUser();

   		if($user->guest) { return true; }

   		$db->setQuery('SELECT validate from #__botiga_users WHERE userid = '.$user->id);
   		if($db->loadResult() == 1) {
   			return true;
   		} else {
   			return  false;
   		}
    }

    /**
	 * method to know if there are stock of a product and validate addtocart button
	 * @param int  $stock the stock of the current product item
	 * @return bool
	*/
	public static function validateStock($stock)
    {
   		$control_stock = botigaHelper::getParameter('control_stock', 0);

   		if($control_stock == 0) { return true; }

   		if($control_stock == 1 && $stock > 0) {
   			return true;
   		} else {
   			return  false;
   		}
    }

    /**
	 * method to get user the active Itemid
	 * @return int
	*/
    public static function getItemid()
    {
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		return $active->id;
    }

    /**
	 * method to write in a debug log
	 * @param $text string The text to write in the log file
	 * @param $logfile string The file where to write the log file
	 * @params $logfile the destination lof file
	*/
    public static function customLog($text, $logfile=null) {
		if ($logfile==null) {
			$logfile = JPATH_COMPONENT.'/logs/botiga.log';
		}
		$handle = fopen($logfile, 'a');
		fwrite($handle, date('d-M-Y H:i:s') . ': ' . $text . "\n");
		fclose($handle);
	}

	/**
	 * method to know if a user has access to the table view
	*/
    public static function hasAccesstoTableView()
    {
		$user   = JFactory::getUser();
		$groups = JAccess::getGroupsByUser($user->id, false);
		$access = botigaHelper::getParameter('table_access', '');

		foreach($access as $a) {
			if(in_array($a, $groups)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * method to know if a price will be visible
	*/
    public static function isPriceVisible()
    {
    		$user   = JFactory::getUser();

		$showprices 	= botigaHelper::getParameter('show_prices', 1);
		$loginprices 	= botigaHelper::getParameter('login_prices', 0);

		if($showprices == 0) { return false; }
		if($loginprices == 1 && $user->guest) { return false; }

		return true;
	}

	/**
	 * method to get user the price
	 * @param int $itemid database id requested
	 * @param int $activaIVA 1 if we want to show IVA in products 0 if not
	 * @return float
	*/
	public static function getUserPrice($itemid, $activaIVA=1)
	{
		jimport( 'joomla.access.access' );

		$user      	= JFactory::getUser();
		$db			= JFactory::getDbo();
		$resultado 	= 0;

		$login_prices 	= botigaHelper::getParameter('login_prices', 0);
		$prices_iva   	= botigaHelper::getParameter('prices_iva', 1);
		$iva   			= botigaHelper::getParameter('iva', 21);

		// if user is guest hide price
		if($login_prices == 1 && $user->guest) { return '0.00'; }

		//Si l'usuari es logat i te aplicar_iva desactivat o fem saber a la funció
		if(!$user->guest) {
			$user_params  = json_decode(botigaHelper::getUserData('params', $itemid), true);
			$activaIVA    = $user_params['aplicar_iva'];
		}

		$groups = JAccess::getGroupsByUser($user->id, false);

		// check if user is no validated and not demo, or guest
		if((!botigaHelper::isValidated() && !in_array(12, $groups)) || $user->guest) {
			$groups = array(2, 11);
		}

		//eliminem l'usuari demo (usergroup 12) i aixì agafarà la tarifa que li toqui
		if (($key = array_search(12, $groups)) !== false) {
    		unset($groups[$key]);
		}

		$usergroup = max($groups);
		//IMPORTANT: l'id del grup demo ha de ser inferior a tota la resta de grups que es creïn

		$db->setQuery('SELECT price FROM #__botiga_items WHERE id = '.$itemid);
		$price = $db->loadResult();

		$prices = json_decode($price, true);

		$db->setQuery('SELECT price FROM #__botiga_items_prices WHERE itemId = '.$itemid.' AND usergroup = '.$usergroup);
		$resultado = $db->loadResult();

		if($resultado === null) { $resultado = 0; }

		//si el preu s'ha de mostrar amb IVA l'apliquem aquí
		// 19/06/2019 (Carles): crec que no cal comprovar si l'usuari és validat o no per determinar si aplicar IVA o no
		//if($prices_iva == 1 && ($user->guest || $usergroup == 11 || !botigaHelper::isValidated()) && $activaIVA == 1) {
		if($prices_iva == 1 && ($user->guest || $usergroup == 11) && $activaIVA == 1) {
			$iva_percent = ($iva / 100) * $resultado;
			$resultado += $iva_percent;
		}

		return number_format($resultado, 2);
	}

	/**
	 * method to get user the price
	 * @param int $itemid database id requested
	 * @return float
	*/
	public static function getUserDiscounts($itemid)
	{
		jimport( 'joomla.access.access' );

		$user     	= JFactory::getUser();
		$db		  	= JFactory::getDbo();
		$resultat	= '';

		$login_prices = botigaHelper::getParameter('login_prices', 0);

		//if user is guest hide price
		if($login_prices == 1 && $user->guest) { return '0.00'; }

		$groups = JAccess::getGroupsByUser($user->id, false);

		//check if user is no validated
		if((!botigaHelper::isValidated() && in_array(12, $groups)) || $user->guest) {
			$groups = array(2, 11);
		}

		$db->setQuery('SELECT price FROM #__botiga_items WHERE id = '.$itemid);
		$price = $db->loadResult();

		$prices = json_decode($price, true);

		foreach ($prices as $sub) {
			foreach ($sub as $k => $v) {
		        $result[$k][] = $v;
		    }
      	}

      	foreach ($result as $index => $value) {
    		if(in_array($value[0], $groups)) {
				// grup $value[1];
				// article $itemid
				$db->setQuery( "SELECT * FROM #__botiga_discounts WHERE idItem=$itemid AND usergroup=$value[0] AND published=1" );
				//botigaHelper::customLog("SELECT * FROM #__botiga_discounts WHERE idItem=$itemid AND type=$value[0]");
				$discounts = $db->loadObjectList();
				foreach($discounts as $discount) {
					$resultat .= ($resultat==''?'':'<br/>') . $discount->name . ': ' . $discount->total;
				}
			}
		}

		return $resultat;
	}

	/**
	 * method to get the extra content in the item view
	 * @param int $itemid database id requested
	 * @return mixed
	*/
	public static function getExtras($itemid)
	{
		$result = array();
		$extras = json_decode(botigaHelper::getItemData('extres', $itemid), true);

		foreach ($extras as $extra)
      	{
			foreach ($extra as $k => $v)
		    {
		    	if($v != '') {
		        	$result[$k][] = $v;
		        }
		    }
      	}

		return $result;
	}

	/**
	 * method to get the extra images in the item view
	 * @param int $itemid database id requested
	 * @return mixed
	*/
	public static function getImages($itemid)
	{
		$result = array();
		$extras = json_decode(botigaHelper::getItemData('images', $itemid), true);

		foreach ($extras as $extra)
      		{
			foreach ($extra as $k => $v)
		    {
		    	if($v != '') {
		        	$result[$k][] = $v;
		        }
		    }
      		}

		return $result;

	}

	/**
	 * method to get the child content in the item view
	 * @param int $itemid database id requested
	 * @return mixed
	*/
	public static function getChilds($itemid)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$groups  = JAccess::getGroupsByUser($user->id, false);
		$db->setQuery('SELECT id, name FROM #__botiga_items WHERE child = '.$itemid.' AND published = 1 AND (usergroup = 1 OR usergroup IN('.implode(',', $groups).'))');
		return $db->loadObjectList();

	}

	/**
	 * method to get prices formatted in euro format
	 * @return string
	*/
	public static function euroFormat($value) {
		return number_format($value, 2, '.', '') . '&euro;';
	}
}
