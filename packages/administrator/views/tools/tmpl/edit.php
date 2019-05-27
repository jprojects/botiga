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

JHtml::_('behavior.keepalive');
$model = $this->getModel('tools');
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if(task == 'tools.cancel'){
		document.location.href = 'index.php?option=com_botiga';
	}
}
</script>


<form action="<?= JRoute::_('index.php?option=com_botiga'); ?>" method="post" name="adminForm" id="item-form">

	<div class="row-fluid">
		<div class="span5">
			<canvas id="canvas1"></canvas>
			<canvas id="canvas3"></canvas>
		</div>
		<div class="span5">
			<canvas id="canvas2"></canvas>
			<canvas id="canvas4"></canvas>
		</div>
	</div>

	<input type="hidden" name="task" value="tools.cancel" />
	<?= JHtml::_('form.token'); ?>
</form>
