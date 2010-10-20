<?php

/*
+----------------------------------------------------------------+
+	flexIDXHS-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/

/*
 * ToDo Max: Make the main plugin code as class, so I can get the lists of property types, prices, br/ba etc. without repeating them here.
 */

require_once( dirname( dirname(__FILE__) ) .'/_wp-load.php');
global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));	

$opt = get_option('flexidxhs');

foreach($opt['city-list'] as $city){
    $output_city .= "\t" . '<option value="'. $city .'">'. $city .'</option>' . "\n";
}
$cities = '<select id="city_value">' .  $output_city . '</select>';

$_price_min = flexIDXHS_price_min();
foreach($_price_min as $k => $v){
    $output_price_min .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
}
$price_min = '<select id="price_min_value">' .  $output_price_min . '</select>';

$_price_max = flexIDXHS_price_max();
foreach($_price_max as $k => $v){
    $output_price_max .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
}
$price_max = '<select id="price_max_value">' .  $output_price_max . '</select>';

$_property_types = flexIDXHS_property_types();
foreach($_property_types as $k => $v){
    $output_property_types .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
}
$property_types = '<select id="property_type_value">' .  $output_property_types . '</select>';

$_beds = flexIDXHS_beds();
foreach($_beds as $k => $v){
    $output_beds .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
}
$beds = '<select id="beds_value">' .  $output_beds . '</select>';

$_baths = flexIDXHS_baths();
foreach($_baths as $k => $v){
    $output_baths .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
}
$baths = '<select id="baths_value">' .  $output_baths . '</select>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert flexIDX URL, Link or iFrame</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertflexIDXHSLink() {
		
            var tagtext;

            var width           = '';
            var height          = '';
            var idx_url         = '<?php echo $opt['idx-url'];?>';           
            var city            = document.getElementById('city_value').value;
            var property_type   = document.getElementById('property_type_value').value;
            var price_min       = document.getElementById('price_min_value').value;
            var price_max       = document.getElementById('price_max_value').value;
            var beds            = document.getElementById('beds_value').value;
            var baths           = document.getElementById('baths_value').value;
            var link_types      = document.getElementsByName('link_type');

            for(var i=0; i<link_types.length; i++){
                if(link_types[i].checked){
                    var link_type = link_types[i].value;
                }
            }
				
            var output      = idx_url
                            + '&<?php echo _flex_field_name('city');?>=' + city
                            + '&<?php echo _flex_field_name('property-type');?>=' + property_type
                            + '&list_price=' + price_min + ',' + price_max
                            + '&total_br=>' + beds
                            + '&total_bath=>' + baths;

            if(link_type == 'url'){
                tagtext = output;
            }else
                if(link_type == 'link'){
                    if(document.getElementById('anchortext').value){
                        var anchortext = document.getElementById('anchortext').value;
                    }else{
                        var anchortext = 'View Listings';
                    }
                    tagtext = '<a href="' + output + '">' + anchortext + '</a>';
            }else
                if(link_type == 'iframe'){
                    if(document.getElementById('width').value){
                        width = ' width="' + document.getElementById('width').value + '"';
                    }
                    if(document.getElementById('height').value){
                        height = ' height="' + document.getElementById('height').value + '"';
                    }
                    tagtext = '[idxiframe url="' + output + '"' + width + height + ']';
            }
		
		if(window.tinyMCE) {                    
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false,  tagtext);
			//Peforms a clean up of the current editor HTML. 
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches. 
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		
		return;
	}
	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="flexIDXHSForm" action="#">
	<div class="tabs">
		<ul>
			<li id="flexIDXHS_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('flexIDXHS_tab1','flexIDXHS_panel');" onmousedown="return false;"><?php _e("flexIDX", 'flexIDXHS'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="flexIDXHS_options" class="panel_wrapper" style="height:215px">
		<!-- flexIDXHS panel -->
		<div id="flexIDXHS_panel" class="panel current">
		<br />
			<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
			 <tr>
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("City:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    <?php echo $cities; ?>
                                </td>
			</tr>
                        <tr>
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("Property Type:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    <?php echo $property_types; ?>
                                </td>
			</tr>
                        <tr>
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("Price:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    <?php echo $price_min; ?> - <?php echo $price_max; ?>
                                </td>
			</tr>
                        <tr>
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("Beds/Baths:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    <?php echo $beds; ?> - <?php echo $baths; ?>
                                </td>
			</tr>
                        <tr>
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("Insert:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>                                    
                                    <input type="radio" name="link_type" id="link_type_link" value="link" checked/> Link
                                    <input type="radio" name="link_type" id="link_type_url" value="url"/> URL
                                    <input type="radio" name="link_type" id="link_type_iframe" value="iframe" /> iFrame
                                </td>
			</tr>
                        <tr id="checked_link">
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("Anchor Text:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    <input type="text" name="anchortext" id="anchortext" size="40"/>
                                </td>
			</tr>
                        <tr id="checked_link">
				<td nowrap="nowrap">
                                    <label for="flexIDXHS_type">
                                        <?php _e("iFrame:", 'flexIDXHS'); ?>
                                    </label>
                                </td>
				<td>
                                    Width: <input type="text" name="width" id="width" size="4"/> - Height: <input type="text" name="height" id="height" size="4"/>
                                </td>
			</tr>
			
			</table>

		</div>
		<!-- end flexIDXHS panel -->
				
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'flexIDXHS'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'flexIDXHS'); ?>" onclick="insertflexIDXHSLink();" />
		</div>
	</div>
</form>
</body>
</html>
<?php

?>
