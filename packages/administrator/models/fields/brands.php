<?php
/**
 * @version		1.0.0 com_botiga $
 * @package		botiga
 * @copyright Copyright © 2011 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@aficat.com
 * @website		http://www.aficat.com
 *
*/

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Supports a modal customer picker.
 *
 * @package	Joomla.Administrator
 * @subpackage	com_laundry
 * @since	1.6
 */
class JFormFieldBrands extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Brands';

	/**
	 * Layout to render
	 *
	 * @var   string
	 * @since 3.5
	 */
	protected $layout = 'components.com_botiga.brands.modal';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	*/
	protected function getInput()
	{
		// Add the modal field script to the document head.
		HTMLHelper::_('script', 'system/fields/modal-fields.min.js', array('version' => 'auto', 'relative' => true));

		$script = array();
		$script[] = '	function jSelectBrand_' . $this->id . '(id, name) {';
		$script[] = '		document.getElementById("' . $this->id . '").value = id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = name;';
		$script[] = '	}';

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html = array();
		$link	= 'index.php?option=com_botiga&view=brands&amp;layout=modal&amp;tmpl=component&amp;function=jSelectBrand_'.$this->id;

		$db = Factory::getDbo();
		$db->setQuery('SELECT `name` FROM `#__botiga_brands` WHERE id = '.$this->value);
		$name = $db->loadResult();
		if($this->value == '') { $this->value = 0; }

		$html[] = '<div class="input-group mb-3">';
		$html[] = '<input type="hidden" name="jform['.str_replace('jform_', '', $this->id).']" value="'.$this->value.'" id="'.$this->id.'">';
		$html[] = '<input type="text" id="'.$this->id.'_name" class="form-control" value="'.$name.'" placeholder="'. Text::_('COM_BOTIGA_SELECT_BRAND') .'" aria-label="'. Text::_('COM_BOTIGA_SELECT_BRAND') .'" aria-describedby="button-addon2">';
		$html[] = '<button data-toggle="modal" data-target="#brandsModal" class="btn btn-primary" type="button" id="button-addon2">'. Text::_('COM_BOTIGA_SELECT_BRAND') .'</button>';
		$html[] = '</div>';

		$html[] = HTMLHelper::_(
			'bootstrap.renderModal',
			'brandsModal',
			array(
				'url'    => $link,
				'title'  => Text::_('COM_BOTIGA_SELECT_BRAND'),
				'height' => '100%',
				'width'  => '100%',
				'modalWidth'  => '960',
				'bodyHeight'  => '650',
				'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
					. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		);
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
