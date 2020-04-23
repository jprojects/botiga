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
		$script = array();
		$script[] = '	function jSelectBrand_' . $this->id . '(name) {';
		$script[] = '		document.getElementById("' . $this->id . '").value = name;';
		$script[] = '		jModalClose();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$link	= 'index.php?option=com_botiga&view=brands&layout=modal&tmpl=component&function=jSelectBrand_'.$this->id;

		// The current user display field.
		$html[] = '<div class="input-append">';
		$html[] = parent::getInput()
			. '<a class="btn" title="' . Text::_('COM_BOTIGA_SELECT_BRAND') . '"  href="' . $link
			. '"  data-toggle="modal" data-target="#brandsModal">'
			. Text::_('COM_BOTIGA_SELECT_BRAND') . '</a>';

		$html[] = HTMLHelper::_(
			'bootstrap.renderModal',
			'brandsModal',
			array(
				'url'    => $link,
				'title'  => Text::_('COM_BOTIGA_SELECT_BRAND'),
				'height' => '100%',
				'width'  => '100%',
				'modalWidth'  => '800',
				'bodyHeight'  => '450',
				'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
					. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
			)
		);
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
