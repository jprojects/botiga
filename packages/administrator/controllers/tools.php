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
		
		//get joomla and vm users
		$db->setQuery(
			'SELECT ui.*, u.email FROM #__virtuemart_userinfos as ui '.
			'INNER JOIN #__users as u ON ui.virtuemart_user_id = u.id'
		);
		
		$users = $db->loadObjectList();
		
		//get vm categories from spanish table
		$db->setQuery(	
			'SELECT ce.virtuemart_category_id as id, ce.slug, ce.category_name as title, cc.category_parent_id as parent_id '.
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
			'SELECT p.virtuemart_product_id as id, p.product_sku as ref, p.product_availability as envio, p.published, pe.product_desc as description, pe.product_s_desc as s_description, pe.product_name as name FROM #__virtuemart_products as p left join #__virtuemart_products_es_es as pe on pe.virtuemart_product_id = p.virtuemart_product_id left join #__virtuemart_product_manufacturers as pm on p.virtuemart_product_id = pm.virtuemart_product_id = pm.virtuemart_manufacturer_id'
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
		
		//delete existent products
		$db->setQuery('TRUNCATE TABLE #__botiga_users');
		$db->query();
		
		//insert users
		$usr = new stdClass();
		
		foreach($users as $user) {
			
			$usr->id 			= $user->id;
			$usr->usergroup		= $user->group_id;
			$usr->userid		= $user->virtuemart_user_id;
			$usr->nom_empresa 	= $user->company;
			$usr->mail_empresa 	= $user->email;
			$usr->telefon   	= $user->phone_1;
			$usr->adreca 		= $user->address_1;
			$usr->poblacio 		= $user->city;
			$usr->pais			= $user->country;
			$usr->cp			= $user->zip;
			$usr->cif			= $user->cif;
			$usr->published		= 1;
			
			$db->insertObject('#__botiga_users', $usr);						
		}
		
		//insert new categories
		$cat = new stdClass();
		
		foreach($categories as $category) {
		
			$category->parent_id == 0 ? $parent = 1 : $parent = $category->parent_id;
			
			$cat->id 		= $category->id;
			$cat->title 	= $category->title;
			$cat->parent_id = $parent;
			$cat->alias		= $category->slug;
			$cat->extension = 'com_botiga';
			$cat->published = 1;
			$cat->language  = 'es-ES';
			$cat->access    = 1;
			
			$db->insertObject('#__categories', $cat);						
		}
		
		//rebuild categories
		$menu = new JTableNested('#__categories', 'id', $db);
    	$menu->rebuild();
		
		//insert new brands
		$brand = new stdClass();
		
		foreach($manufacturers as $manufacturer) {
			
			$brand->id 			= $manufacturer->id;
			$brand->name 		= $manufacturer->name;
			$brand->language 	= 'es-ES';
			$brand->published 	= 1;
			
			$db->insertObject('#__botiga_brands', $brand);
		}
		
		//insert new products
		$item = new stdClass();
		
		foreach($products as $product) {	
		
			if($product->envio == '3-5d.gif') {
				$envio = str_replace('3-5d.gif', '3-5 dias', $product->envio);
			}
			if($product->envio == '3-5d_en.gif') {
				$envio = str_replace('3-5d_en.gif', '3-5 dias', $product->envio);
			}
			if($product->envio == '7d.gif') {
				$envio = str_replace('7d.gif', '7 dias', $product->envio);
			}
			
			$item->id 				= $product->id;
			$item->ref 				= $product->ref;
			$item->envio 			= $envio;
			$item->description 		= $product->description;
			$item->s_description 	= $product->s_description;
			$item->name 			= $product->name;
			$item->language 		= 'es-ES';
			$item->published 		= 1;
			
			//connect to external database
			$db->disconnect();
			$db = JDatabaseDriver::getInstance( $option );
			
			//insert product categories
			$db->setQuery('SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = '.$product->id);
			$cats = $db->loadObjectList();
			
			$category = '';
			
			foreach($cats as $cat) {
				//$category[] = $cat->virtuemart_category_id;
				$category .= $cat->virtuemart_category_id.',';
			}
			
			$item->catid	= $category;
			//$item->catid			= implode(',', $category);
			
			//insert product medias
			$db->setQuery('SELECT m.file_url FROM #__virtuemart_medias m INNER JOIN #__virtuemart_product_medias pm ON m.virtuemart_media_id = pm.virtuemart_media_id WHERE pm.virtuemart_product_id = '.$product->id);
			$files = $db->loadObjectList();
			
			$images = array();
			
			foreach($files as $file) {
				$images[] = $file->file_url;
			}
			
			$item->image1			= str_replace('images/stories/virtuemart/product/', 'images/products/', $images[0]);
			$item->image2			= str_replace('images/stories/virtuemart/product/', 'images/products/', $images[1]);
			$item->image3			= str_replace('images/stories/virtuemart/product/', 'images/products/', $images[2]);
			$item->image4			= str_replace('images/stories/virtuemart/product/', 'images/products/', $images[3]);
			$item->image5			= str_replace('images/stories/virtuemart/product/', 'images/products/', $images[4]);
			
			//insert custom fields
			$db->setQuery('SELECT virtuemart_custom_id, custom_value FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '.$product->id);
			$fields = $db->loadObjectList();
			
			$collection = '';
			$garantia   = '';
			$pvp	    = '';
			
			foreach($fields as $field) {
				
				if($field->virtuemart_custom_id == 6) {
					$collection = $field->custom_value;
				}
				if($field->virtuemart_custom_id == 7) {
					$garantia = $field->custom_value;
				}
				if($field->virtuemart_custom_id == 9) {
					$pvp = str_replace('€', '', $field->custom_value);
					$pvp = str_replace(',', '.', $pvp);
				}
			}
			
			$item->collection	= $collection;
			$item->garantia		= $garantia;
			$item->pvp			= $pvp;
			
			//insert product brands
			$db->setQuery('SELECT virtuemart_manufacturer_id FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id = '.$product->id);
			$brand = $db->loadResult();
			
			$item->brand = $brand;
			
			//insert prices (shopergroups 2 Registered - 4 HOTUSA)
			$db->setQuery('SELECT product_price, virtuemart_shoppergroup_id as grup FROM #__virtuemart_product_prices WHERE virtuemart_product_id = '.$product->id);
			$prices = $db->loadObjectList();
			
			$registered = 0;
			$hotusa     = 0;
			
			foreach($prices as $price) {
				if($price->grup == 2) {
					$registered = $price->product_price;
				}
				if($price->grup == 4) {
					$hotusa = $price->product_price;
				}
			}
			
			$item->price = '{"usergroup":["2","10"],"pricing":["'.$registered.'","'.$hotusa.'"]}';
		
			//connect to local database
			$db->disconnect();
			$db = JFactory::getDbo();
			
			$db->insertObject('#__botiga_items', $item);
			
		}
		
		$this->setRedirect('index.php?option=com_botiga&view=tools&layout=edit', 'Import finished');
	}
}
