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
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 

class botigaControllerTools extends JControllerForm
{
    /**
	 * Proxy for getModel.
	 * @since	1.6
	*/
	public function getModel($name = 'Tools', $prefix = 'botigaModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function import()
	{
		$post_data  = JRequest::get( 'post' );
   		$data       = $post_data["jform"];
   		
		$option = array();

		$option['driver']   = 'mysqli';
		$option['host']     = $data['dbhost'];
		$option['user']     = $data['dbuser'];
		$option['password'] = $data['dbpass'];
		$option['database'] = $data['dbname'];
		$option['prefix']   = $data['dbprefix'];

		//connect to external database
		$db = JDatabaseDriver::getInstance( $option );
		
		//get vm categories from spanish table
		$db->setQuery(	
			'SELECT ce.virtuemart_category_id as id, ce.category_name as title, cc.category_parent_id as parent_id '.
			'FROM #__virtuemart_categories_es_es as ce '.
			'INNER JOIN #__virtuemart_category_categories as cc on ce.virtuemart_category_id = cc.category_child_id'
		);
		
		$categories = $db->loadObjectList();
		
		//get vm manufacturers from spanish table
		$db->setQuery(	
			'SELECT me.virtuemart_manufacturer_id as id, me.mf_name as name FROM #__virtuemart_manufacturers_es_es as me'
		);
		
		$manufacturers = $db->loadObjectList();
		
		//get vm products from spanish table
		$db->setQuery(	
			'SELECT p.virtuemart_product_id as id, p.product_sku as ref, p.product_availability as envio, p.published, pe.product_s_desc as description, pe.product_s_desc as s_description, pe.product_name as name, pc.virtuemart_category_id as catid FROM #__virtuemart_products as p left join #__virtuemart_products_es_es as pe on pe.virtuemart_product_id = p.virtuemart_product_id left join #__virtuemart_product_categories as pc on p.virtuemart_product_id = pc.virtuemart_product_id left join #__virtuemart_product_manufacturers as pm on p.virtuemart_product_id = pm.virtuemart_product_id = pm.virtuemart_manufacturer_id'
		);
		
		$products = $db->loadObjectList();
		
		$db->disconnect();
		
		//connect to local database
		$db = JFactory::getDbo();
		
		//delete existent categories
		$db->setQuery('DELETE FROM #__categories WHERE extension = '.$db->quote('com_botiga'));
		$db->query();
		
		//delete existent brands
		$db->setQuery('TRUNCATE TABLE #__botiga_brands');
		$db->query();
		
		//delete existent products
		$db->setQuery('TRUNCATE TABLE #__botiga_items');
		$db->query();
		
		//insert new categories
		$cat = new stdClass();
		
		foreach($categories as $category) {
			
			$cat->id = $category->id;
			$cat->title = $category->title;
			$cat->parent_id = $category->parent_id;
			$cat->extension = 'com_botiga';
			$cat->published = 1;
			
			$db->insertObject('#__categories', $cat);
		}
		
		//insert new brands
		$brand = new stdClass();
		
		foreach($manufacturers as $manufacturer) {
			
			$brand->id = $manufacturer->id;
			$brand->name = $manufacturer->name;
			$brand->language = 'es-ES';
			$brand->published = 1;
			
			$db->insertObject('#__botiga_brands', $brand);
		}
		
		//insert new products
		$item = new stdClass();
		
		foreach($products as $product) {
			
			$item->id = $product->id;
			$item->ref = $product->ref;
			$item->envio = $product->envio;
			$item->description = $product->description;
			$item->s_description = $product->s_description;
			$item->name = $product->name;
			$item->language = 'es-ES';
			$item->published = $product->published;
			
			$db->insertObject('#__botiga_items', $item);
		}
	}
}
