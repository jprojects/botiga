<?php
/**
 * @version		1.0.0 com_botiga $
 * @package		botiga
 * @copyright Copyright © 2011 - All rights reserved.
 * @license		GNU/GPL
 * @author		kim
 * @author mail kim@gwerp.com
 * @website		http://www.gwerp.com
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
class JFormFieldCoupon extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Coupon';

  /**
	 * Layout to render
	 *
	 * @var   string
	 * @since 3.5
	 */
	protected $layout = 'components.com_botiga.coupons.modal';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	*/
	protected function getInput()
	{
		$script = array();
		$script[] = '	function jSelectCoupon_' . $this->id . '(name) {';
		$script[] = '		document.getElementById("' . $this->id . '").value = name;';
		$script[] = '		jModalClose();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$link	= 'index.php?option=com_botiga&view=coupons&tmpl=component&function=jSelectCoupon_'.$this->id;

		// The current user display field.
		$html[] = '<div class="input-append">';
		$html[] = parent::getInput()
			. '<a class="btn" title="' . Text::_('COM_BOTIGA_SELECT_COUPON') . '"  href="' . $link
			. '"  data-toggle="modal" data-target="#couponModal">'
			. Text::_('COM_BOTIGA_SELECT_COUPON') . '</a>';

		$html[] = HTMLHelper::_(
			'bootstrap.renderModal',
			'couponModal',
			array(
				'url'    => $link,
				'title'  => Text::_('COM_BOTIGA_SELECT_COUPON'),
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
