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
class plgBotiga_syncBotiga_sync_aws extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	public function sincronitza()
	{
		require_once(dirname(__FILE__).'/aws/MWSClient.php');
		require_once(dirname(__FILE__).'/aws/MWSProduct.php'); 

		$db = JFactory::getDbo();

		$client = new MCS\MWSClient([
			'Marketplace_Id' => $this->params->get('Marketplace_Id'),
			'Seller_Id' => $this->params->get('Seller_Id'),
			'Access_Key_ID' => $this->params->get('Access_Key_ID'),
			'Secret_Access_Key' => $this->params->get('Secret_Access_Key'),
			'MWSAuthToken' => $this->params->get('MWSAuthToken') // Optional. Only use this key if you are a third party user/developer
		]);

		if ($client->validateCredentials()) {

			$db->setQuery('SELECT * FROM `#__botiga_items` WHERE aws = 1');
			$rows = $db->loadObjectList();

			foreach($rows as $row) {

				$product = new MCS\MWSProduct();
				$product->sku = $row->ref;
				$product->price = '1000.00';
				$product->product_id = $row->id;
				$product->product_id_type = 'ASIN';
				$product->condition_type = 'New';
				$product->quantity = 10;

				if ($product->validate()) {
					$result = $client->postProduct($product);    
				} else {
					$errors = $product->getValidationErrors();        
				}
			}

			return true;

		} else {
			return false;
		}
	}
}
