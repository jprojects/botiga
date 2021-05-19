<?php
/**
 * @package Module EB Whatsapp Chat for Joomla!
 * @version 1.4: mod_ebwhatsappchat.php Jun 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
defined('_JEXEC') or die;

$mdl_path =  JURI::base().'modules/'.$module->module;
$document = JFactory::getDocument();
$document->addStyleSheet($mdl_path.'/assets/css/whatapp_style.css');
$module_id= $module->id;

$wsac_mbln = isset($whatsapp['whatsapp_number'])?$whatsapp['whatsapp_number']:'';
$wsac_intmsg = isset($whatsapp['initial_message'])?$whatsapp['initial_message']:'';
$wsac_icnpst = isset($whatsapp['icon_position'])?$whatsapp['icon_position']:'bottom_right';
$wsac_dsplytxt = isset($whatsapp['icon_withtext'])?$whatsapp['icon_withtext']:'';
$wsac_bgcolor = ($whatsapp['backgroundcolor']!='')?$whatsapp['backgroundcolor']:'#e4e4e4';
$wsac_txtcolor = ($whatsapp['textcolor']!='')?$whatsapp['textcolor']:'#000000';
$wsac_icnimg = isset($whatsapp['icon_image'])?$whatsapp['icon_image']:'';
$wsac_uplicnimg = isset($whatsapp['upload_iconimg'])?$whatsapp['upload_iconimg']:'';

$wsac_heading_option = isset($whatsapp['heading_option'])?$whatsapp['heading_option']:'';
$wsac_heading_content = isset($whatsapp['heading_content'])?$whatsapp['heading_content']:'';
$wsac_heading_name  = isset($whatsapp['heading_name'])?$whatsapp['heading_name']:''; 
$wsac_heading_department  = isset($whatsapp['heading_department'])?$whatsapp['heading_department']:'';
$wsac_heading_image  = isset($whatsapp['heading_image'])?$whatsapp['heading_image']:'';
$wsac_heading_image_img = $wsac_heading_image != '' ? $wsac_heading_image : $mdl_path.'/assets/images/contact-img.png';
$wsac_middle_content  = isset($whatsapp['middle_content'])?$whatsapp['middle_content']:'';


$url_msg = '';
if($wsac_intmsg != ''){
	$url_msg = '&text='.$wsac_intmsg;
}

$url_redirect = "https://web.whatsapp.com/send?phone=$wsac_mbln".$url_msg;

if($wsac_icnimg == "style_1"){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_1.png";
} else if($wsac_icnimg == "style_2"){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_2.png";
} else if($wsac_icnimg == "style_3"){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_3.png";
} else if($wsac_icnimg == "style_4"){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_4.png";
} else if($wsac_icnimg == "style_5"){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_5.png";
} else if($wsac_icnimg == "upload_icon" && $wsac_uplicnimg == ''){
	$icon_img = $mdl_path."/assets/images/whatsapp_icon_1.png";
} else if ($wsac_icnimg == "upload_icon" && $wsac_uplicnimg != ''){
	$icon_img = $wsac_uplicnimg;
} else {
	$icon_img = "";
} ?>
<style type="text/css">
	<?php if($wsac_icnpst == 'top_left'){ ?>
	.whatsappchat_<?php echo $module_id; ?> .popup-section .popup .myPopup_<?php echo $module_id; ?>::after { border-color: transparent transparent <?php echo $wsac_bgcolor; ?> transparent; }
	<?php } else if($wsac_icnpst == 'top_right'){ ?>
	.whatsappchat_<?php echo $module_id; ?> .popup-section .popup .myPopup_<?php echo $module_id; ?>::after { border-color: transparent transparent <?php echo $wsac_bgcolor; ?> transparent; }
	<?php } else if($wsac_icnpst == 'bottom_left'){ ?>
		.whatsappchat_<?php echo $module_id; ?>.is-bottom_left .popup-section .popup .myPopup_<?php echo $module_id; ?>::after { border-color: <?php echo $wsac_bgcolor; ?> transparent transparent transparent; } 
	<?php } else if($wsac_icnpst == 'bottom_right'){ ?>
		.whatsappchat_<?php echo $module_id; ?>.is-bottom_right .popup-section .popup .myPopup_<?php echo $module_id; ?>::after {  border-color: <?php echo $wsac_bgcolor; ?> transparent transparent transparent; }
	<?php } ?>
	.whatsappchat .popup-section h3 { color: <?php echo $wsac_txtcolor ?> }
</style>
		<div class="is_<?php echo $wsac_icnpst; ?> whatsappchat_<?php echo $module_id; ?> whatsappchat is-<?php echo $wsac_icnpst; ?>">
			<div class="popup-section">
				<div class="popup">
					<?php if($wsac_icnimg == "upload_icon"){ ?>
						<span class="help_btn_<?php echo $module_id; ?> help-you-btn" style="background: unset;box-shadow: unset;">
							<img src="<?php echo $icon_img; ?>">
						</span>
					<?php } else { 
						if($wsac_dsplytxt ==''){ ?>
							<span class="help_btn_<?php echo $module_id; ?> help-you-btn" style="background: unset;box-shadow: unset;">
							<?php if($icon_img!=''){ ?><img src="<?php echo $icon_img; ?>"><?php } ?>
						</span>
						<?php } else { ?>
							<span class="help_btn_<?php echo $module_id; ?> help-you-btn" style="background-color: <?php echo $wsac_bgcolor ?>; color: <?php echo $wsac_txtcolor ?>">
					    	<?php if($icon_img!=''){ ?><img src="<?php echo $icon_img; ?>"  class="is-analytics" id="text_iconimg" alt="WhatsApp" /><?php } ?>
			                <?php echo $wsac_dsplytxt ?>
					    </span>
						<?php } ?>
					<?php } ?>
				    <span class="myPopup_<?php echo $module_id; ?> popuptext" id="myPopup">
				      <div class="popup-box">
				        <div class="popup-top" style="background-color: <?php echo $wsac_bgcolor ?>; color: <?php echo $wsac_txtcolor ?>">
				        	<?php if($wsac_heading_option == 'username_image'){ ?>
						          <div class="image">
						            <img src="<?php echo $wsac_heading_image_img; ?>" >
						          </div>
						          <div class="content">						          	
						            	<?php if($wsac_heading_name!=''){ ?><span class="name"><?php echo $wsac_heading_name; ?></span><?php } ?>
										<?php if($wsac_heading_department!=''){ ?><span class="label" style="color: <?php echo $wsac_bgcolor ?>; background-color: <?php echo $wsac_txtcolor ?>;"><?php echo $wsac_heading_department; ?></span><?php } ?>
						          </div>
						    <?php } else { ?>
						    	<div class="content">		
						    		<?php echo $wsac_heading_content; ?>
						    	</div>
						    <?php } ?>
				        </div>
				        <?php if($wsac_middle_content!=''){ ?>
				        <div class="chat-content">
				          <div class="message">
				            <?php echo $wsac_middle_content; ?>
				          </div>
				        </div>
				    	<?php } ?>
				        <div class="response">
				          <input type="text" name="text" id="response_text_<?php echo $module_id; ?>" placeholder="<?php echo JText::_('WHATSAPP_RESPONESE_PLACEHOLDER'); ?>">
				          <a href="javascript:void(0);" class="submit_btn_<?php echo $module_id; ?> send_btn"><img src="<?php echo $mdl_path.'/assets/images/send-img.png'; ?>"></a>
				        </div>

				      </div>
				    </span>
				</div>
			</div>
		</div>


<script>
  jQuery(".is_<?php echo $wsac_icnpst; ?> .help_btn_<?php echo $module_id; ?>").click(function(){
    jQuery(".is_<?php echo $wsac_icnpst; ?> .popup-section .popup .myPopup_<?php echo $module_id; ?>").toggleClass("show");
  });
  </script>
<script type="text/javascript">
	jQuery( ".response .submit_btn_<?php echo $module_id; ?>" ).click(function() {
	  var r_text = jQuery('#response_text_<?php echo $module_id; ?>').val();
	  // alert(r_text);
	  if(r_text != ''){
	  	wsac_msg = r_text;
	  } else {
	  	wsac_msg = "<?php echo $wsac_intmsg; ?>";
	  }
	  var initial_msg = "&text="+wsac_msg;
	  var initial_msg_for_phone = "?text="+wsac_msg;
	  // alert(initial_msg);
	  var phone_number = "<?php echo $wsac_mbln; ?>";

	  	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			if(/Opera Mini|safari|firefox/i.test(navigator.userAgent) ) 
			var href = "https://api.whatsapp.com/send?phone="+phone_number+""+initial_msg;
			else
			var href = "https://wa.me/"+phone_number+""+initial_msg;
		} else {
			var href = "https://web.whatsapp.com/send?phone="+phone_number+""+initial_msg;
		}
	  // alert(href);
	  window.open(href, '_blank');
	});
</script>
<script type="text/javascript">
	jQuery( document ).ready(function() {
		var icon_position = "<?php echo $wsac_icnpst; ?>";
	    var clslng = jQuery('body').find('.is_<?php echo $wsac_icnpst; ?>').length;
	    // console.log(clslng);
	    if(clslng != 0){
	    	var sum = 0;
	    	var ssum = 50;
	    	jQuery(function(){
			    jQuery(".is_<?php echo $wsac_icnpst; ?>").each(function(i){
			    	
			    	if(i == 0){ sum = sum + 15;
			    	} else { sum= 35; sum += ssum; }
			    	if(icon_position == "bottom_left" || icon_position == "bottom_right"){
			    		jQuery(this).css('bottom', sum+'px');
			    	}
			    	if(icon_position == "top_left" || icon_position == "top_right"){
			    		jQuery(this).css('top', sum+'px');
			    	}
			        
			    });
			});
	    }
	});
</script>

