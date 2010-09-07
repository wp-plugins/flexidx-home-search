<?php
add_action('admin_menu', 'register_flexIDXHS_options');
add_action('admin_init', 'flexIDXHS_options_init');

function flexIDXHS_options_init(){
    register_setting('flexidxhs-option-group', 'flexidxhs', 'flexIDXHS_options_validate');
}

function register_flexIDXHS_options(){
    add_options_page('flexIDX Home Search Options', 'flexIDX Options', 8, 'flexIDX_options', 'flexIDXHS_config_page');
}

$opt = array();
$opt['label-visibility']                        = 'outside';
$opt['label-names']['city']                     = 'Select City';
$opt['label-names']['property-type']            = 'Property Type';
$opt['label-names']['min-price']                = 'Min Price';
$opt['label-names']['max-price']                = 'Max Price';
$opt['label-names']['price-range']              = 'Select Price Range';
$opt['label-names']['bedrooms']                 = 'Beds?';
$opt['label-names']['bathrooms']                = 'Baths?';
$opt['price-fields']                            = 'min-max';
$opt['set-min-price']                           = 0;
$opt['search-buttons']['search-label']          = 'Search Now';
$opt['search-buttons']['advanced-search-label'] = 'Advanced Search';
$opt['search-buttons']['display-advanced-search']= true;
$opt['city-list']                               = array('Apache Junction', 'Avondale', 'Carefree', 'Cave Creek', 'Chandler', 'El Mirage', 'Fountain Hills', 'Gilbert', 'Glendale', 'Goodyear', 'Laveen', 'Litchfield Park', 'Mesa', 'Paradise Valley', 'Peoria', 'Phoenix', 'Queen Creek', 'Rio Verde', 'Scottsdale', 'Sun City', 'Sun City West', 'Surprise', 'Tempe', 'Tolleson');
$opt['custom-searches']                         = null;
$opt['widget-markup']['before-title']           = null;
$opt['widget-markup']['after-title']            = null;
$opt['widget-markup']['before-widget']          = null;
$opt['widget-markup']['after-widget']           = null;

add_option("flexidxhs",$opt);

function flexIDXHS_config_page(){    

    $opt = get_option('flexidxhs');
    
?>
    <div class="wrap">
        <form  action="options.php" method="post" id="flexIDXHS-conf">
        <h2 class="submit">flexIDX Home Search Options <input class="button-primary" type="submit" name="submit" value="Save Options" /></h2>         
            <?php
                settings_fields('flexidxhs-option-group');
            ?>

        <div class="postbox-container" style="width:35%;">
                <div class="metabox-holder">
                        <div class="meta-box-sortables">                               
                                    
                                    <div class="postbox">
                                            <div class="handlediv" title="Click to toggle"><br /></div>
                                            <h3 class="hndle"><span>flexmls IDX URL</span></h3>
                                            <div class="inside">
                                                <p>
                                                    <input type="text" name="flexidxhs[idx-url]" value="<?php echo $opt['idx-url']; ?>" size="50" /> Direct URL to your flexmls&reg; IDX.<br /><em>You should be able to find it in your ARMLS account, under Preferences => IDX Manager.</em>
                                                </p>
                                            </div>
                                    </div>

                                    <div class="postbox">
                                            <div class="handlediv" title="Click to toggle"><br /></div>
                                            <h3 class="hndle"><span>Fields Label Visibility</span></h3>
                                            <div class="inside">
                                                <p>
                                                    <input type="radio" name="flexidxhs[label-visibility]" value="outside" <?php if($opt['label-visibility'] == 'outside'){ echo 'checked';}?>/> Outside the fields.<br />
                                                    <input type="radio" name="flexidxhs[label-visibility]" value="inside" <?php if($opt['label-visibility'] == 'inside'){ echo 'checked';}?>/> Inside the fields, as first value.<br />
                                                    <input type="radio" name="flexidxhs[label-visibility]" value="none" <?php if($opt['label-visibility'] == 'none'){ echo 'checked';}?>/> Do not display labels.
                                                </p>
                                            </div>
                                    </div>

                                    <div class="postbox">
                                            <div class="handlediv" title="Click to toggle"><br /></div>
                                            <h3 class="hndle"><span>Label Titles</span></h3>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr>
                                                        <th valign="top" scope="row">City:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][city]" value="<?php echo $opt['label-names']['city']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Property Type:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][property-type]" value="<?php echo $opt['label-names']['property-type']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Min. Price:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][min-price]" value="<?php echo $opt['label-names']['min-price']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Max. Price:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][max-price]" value="<?php echo $opt['label-names']['max-price']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Price Range:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][price-range]" value="<?php echo $opt['label-names']['price-range']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Bedrooms:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][bedrooms]" value="<?php echo $opt['label-names']['bedrooms']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Bathrooms:</th>
                                                        <td><input type="text" name="flexidxhs[label-names][bathrooms]" value="<?php echo $opt['label-names']['bathrooms']?>" size="40"/></td>
                                                    </tr>
                                                </table>                                                
                                            </div>
                                    </div>

                                    <div class="postbox">
                                            <div class="handlediv" title="Click to toggle"><br /></div>
                                            <h3 class="hndle"><span>Price Fields</span></h3>
                                            <div class="inside">
                                                <p><input type="radio" name="flexidxhs[price-fields]" value="min-max" <?php if($opt['price-fields'] == 'min-max'){ echo 'checked';}?>/> Separate fields for minimum and maximum prices. <br />
                                                    <input type="radio" name="flexidxhs[price-fields]" value="price-range" <?php if($opt['price-fields'] == 'price-range'){ echo 'checked';}?>/> Pre-set price ranges in a single selection list.
                                                </p>
                                                <p>
                                                    Set Minimum Price to <select name="flexidxhs[set-min-price]">
                                                        <?php
                                                        $prices = flexIDXHS_price_min();
                                                        foreach($prices as $price => $label){
                                                            if($opt['set-min-price'] == $price){ $selected = ' selected="selected"';}else{ $selected = ''; }
                                                            echo '<option value="' . $price . '"' . $selected . '>' . $label . '</option>' . "\n";
                                                            //offer min prices up to $500K so values match to both - separate prices and price ranges.
                                                            if($price == 500000)
                                                                break;
                                                        }
                                                        ?>
                                                    </select>
                                                </p>
                                                <p>
                                                    <em>Note: If you have active widgets with custom searches, you will need to update them to match the price field type you select here. For example, if you used price range field in a custom widget, and now decided to switch to separate price fields - you need to do the same for the custom search widgets.</em>
                                                </p>
                                            </div>
                                    </div>

                                    <div class="postbox">
                                            <div class="handlediv" title="Click to toggle"><br /></div>
                                            <h3 class="hndle"><span>Search Buttons</span></h3>
                                            <div class="inside">
                                                <table class="form-table">
                                                    <tr>
                                                        <th valign="top" scope="row">Search Label:</th>
                                                        <td><input type="text" name="flexidxhs[search-buttons][search-label]" value="<?php echo $opt['search-buttons']['search-label']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Advanced Search Label:</th>
                                                        <td><input type="text" name="flexidxhs[search-buttons][advanced-search-label]" value="<?php echo $opt['search-buttons']['advanced-search-label']?>" size="40"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th valign="top" scope="row">Display Advanced Search Button:</th>
                                                        <td><input type="checkbox" name="flexidxhs[search-buttons][display-advanced-search]" value="1" <?php checked($opt['search-buttons']['display-advanced-search'], true, true);?> /></td>
                                                    </tr>
                                                </table>
                                            </div>
                                    </div>
                        </div>
                </div>
        </div>
        <div class="postbox-container" style="width:35%;">
                <div class="metabox-holder">
                        <div class="meta-box-sortables">

                               <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br /></div>
                                    <h3 class="hndle"><span>Cities in your Area</span></h3>
                                    <div class="inside">
                                        <p>One city name per line - enter all the cities that you would like your visitors to be able to search.</p>
                                        <textarea style="width: 100%;" cols="20" rows="10" name="flexidxhs[city-list]"><?php echo _array_to_text($opt['city-list']);?></textarea>
                                    </div>
                               </div>

                               <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br /></div>
                                    <h3 class="hndle"><span>Custom Searches</span></h3>
                                    <div class="inside">
                                        <p>The custom searches can be used as additional options in the Quick Search widget. They will be presented as selection list field types. You can have multiple fields and each field can have multiple searches.</p>
                                        <p><strong>Syntax:</strong></p>
                                        <code>
                                            #Field Name Label 1<br />
                                            Name of the search 1 - Search URL<br />
                                            Name of the search 2 - Search URL
                                        </code>
                                        <p>Result example of the code above:<br/>
                                            <label>Field Name Label 1:</label> <select><option>Name of the search 1</option><option>Name of the search 2</option></select>
                                        </p>
                                        <p>
                                            <textarea style="width: 100%;" cols="20" rows="10" name="flexidxhs[custom-searches]"><?php echo _custom_searches_to_text($opt['custom-searches']);?></textarea>
                                            <em>Make sure you use the correct syntax to add custom searches, otherwise your widget will be broken.</em>
                                        </p>
                                    </div>
                               </div>

                               <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br /></div>
                                    <h3 class="hndle"><span>Additional Widget Markup</span></h3>
                                    <div class="inside">
                                        <p>Some times you may need to wrap widget into additional tags for better styling. You can do it in this section.</p>
                                        <table class="form-table">
                                            <tr>
                                                <th valign="top" scope="row">Before Title:</th>
                                                <td><input type="text" name="flexidxhs[widget-markup][before-title]" value='<?php echo html_entity_decode($opt['widget-markup']['before-title']);?>' size="40"/></td>
                                            </tr>
                                            <tr>
                                                <th valign="top" scope="row">After Title:</th>
                                                <td><input type="text" name="flexidxhs[widget-markup][after-title]" value='<?php echo html_entity_decode($opt['widget-markup']['after-title']);?>' size="40"/></td>
                                            </tr>
                                            <tr>
                                                <th valign="top" scope="row">Before Widget:</th>
                                                <td><input type="text" name="flexidxhs[widget-markup][before-widget]" value='<?php echo html_entity_decode($opt['widget-markup']['before-widget']);?>' size="40"/></td>
                                            </tr>
                                            <tr>
                                                <th valign="top" scope="row">After Widget:</th>
                                                <td><input type="text" name="flexidxhs[widget-markup][after-widget]" value='<?php echo html_entity_decode($opt['widget-markup']['after-widget']);?>' size="40"/></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                        </div>
                </div>

        </div>

        <?php
            echo flexIDXHS_settings_right_column();
        ?>
        <br clear="all" />
        <input class="button-primary" type="submit" name="submit" value="Save Options" />
        </form>
    </div>
<?php
}

function flexIDXHS_options_validate($opt){
    $opt['city-list']           = _text_to_array($opt['city-list']);
    $opt['custom-searches']     = _custom_searches_to_array($opt['custom-searches']);
    foreach($opt['widget-markup'] as $k => $v){
        $opt['widget-markup'][$k] = htmlentities($v, ENT_QUOTES);
    }
    return $opt;
}

function _array_to_text($array){    
    if(is_array($array)){
        array_filter($array);
        foreach($array as $value){
                $output .= $value."\n";
        }
        return $output;
    }
    return;
}

function _text_to_array($text){
    $text =  trim($text);
    $text = str_replace(" \n", "", $text);
    $array = explode("\n", $text);
    array_filter($array);
    if(is_array($array) && !empty($array)){
        foreach($array as $k=>$v){
            if(!empty($array[$k]) && $array[$k] != NULL && strlen($array[$k])>1){
                $output[] = trim($v);
            }
        }
        array_filter($output);
        return $output;
    }
}

/*
 * Returns a multidimentional array of custom searches
 */
function _custom_searches_to_array($text){

    $field_chunks = explode('#', trim($text));
    $fields = array();
    foreach($field_chunks as $chunk){
        if($chunk != ''){
            $lines = explode("\n", $chunk);
            $selections = array();
            foreach($lines as $k=>$v){
                if(!empty($lines[$k]) && $lines[$k] != NULL && strlen($lines[$k])>1){
                    $selections[] = trim($v);
                }
            }
            array_filter($selections);
            $tmp = array();
            $options = array();
            foreach($selections as $k => $v){
                if($k == 0){
                    $k = 'label';
                    $tmp[$k] = $v;
                }else{
                    $options = explode(' - ', $v);
                    $tmp[$options[1]] = $options[0];
                }
            }
            $fields[$tmp['label']] = array_filter($tmp);
        }
    }
    array_filter($fields);
    return $fields;
}

function _custom_searches_to_text($array = array()){

    if(!is_array($array) || empty($array))
        return;
    
    foreach($array as $field){
        $output .= '#'.$field['label'] . "\n";
        unset($field['label']);
        foreach($field as $url => $label){
            $output .= $label . ' - ' . $url . "\n";
        }
    }

    return trim($output);
}

function flexIDXHS_like_plugin(){
	$content = '
	<p>Help us spread the word :)</p>
        <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.phoenixhomes.com%2Ftech%2Fflexidx-home-search&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:45px;" allowTransparency="true"></iframe>
	<ul>
		<li>Link to it or blog about the plugin, so other users can find out about it.</li>
		<li>Give it a good rating on <a href="http://wordpress.org/extend/plugins/flexidx-home-search/">WordPress.org</a></li>
	</ul>';

	return $content;
}

function flexIDXHS_plugin_support(){
	$content = '<p>If you have any problems with this plugin or good ideas for improvements or new features, please talk about them in the <a href="http://wordpress.org/tags/simple-real-estate-pack-4?forum_id=10">Support forums</a>.</p>';
	$content = '
	<p>Help us make it better:</p>
	<ul>
		<li><a href="http://wordpress.org/tags/flexidx-home-search?forum_id=10">Ask for help</a></li>
		<li><a href="http://wordpress.org/tags/flexidx-home-search?forum_id=10">Report a bug</a></li>
		<li><a href="http://wordpress.org/tags/flexidx-home-search?forum_id=10">Suggest improvements or new features</a></li>
	</ul>';
	return $content;
}

function flexIDXHS_plugin_credits(){
	$content = '
	<ul>
		<li><a href="http://www.phoenixhomes.com/tech/flexidx-home-search">Official Plugin Page</a></li>
		<li>Designed by <a href="http://wordpress.org/extend/plugins/profile/maxchirkov">Max Chirkov</a></li>
		<li>Sponsored by <a href="http://www.phoenixhomes.com">PhoenixHomes.com</a></li>
	</ul>';
	return $content;
}

function flexIDXHS_plugin_donate(){
	$content = '
	<p>
		If you would like to make a financial contribution, as a gesture of your appreciation for this free plugin, please consider a donation to the <a href="https://www.cancer.org/aspx/Donation/DON_1_Donate_Online_Now.aspx" title="Donate to American Cancer Society">American Cancer Society</a>
	</p>
	<div style="text-align:center"><a href="https://www.cancer.org/aspx/Donation/DON_1_Donate_Online_Now.aspx" title="Donate to American Cancer Society"><img src="'.FLEXIDXHS_URL.'/images/ACS-logo.jpg" alt="American Cancer Society Logo" title="Donate to American Cancer Society" /></a>' . flexIDXHS_ga() . '</div>
	';
	return $content;
}

function flexIDXHS_postbox($id, $title, $content) {
    $content ='
        <div id="' . $id .'" class="postbox">
                <div class="handlediv" title="Click to toggle"><br /></div>
                <h3 class="hndle"><span>' . $title . '</span></h3>
                <div class="inside">'
                        . $content .
                '</div>
        </div>';
    return $content;
}

function flexIDXHS_settings_right_column(){
	$content = '<div class="postbox-container" style="width:20%;">
                        <div class="metabox-holder">
                                <div class="meta-box-sortables">'
                                        . flexIDXHS_postbox('flexIDXHS_like_plugin', 'Like this plugin?', flexIDXHS_like_plugin())
                                        . flexIDXHS_postbox('flexIDXHS_plugin_support', 'Plugin Support', flexIDXHS_plugin_support())
                                        . flexIDXHS_postbox('flexIDXHS_plugin_credits', 'Credits', flexIDXHS_plugin_credits())
                                        . flexIDXHS_postbox('flexIDXHS_plugin_donate', 'Donate', flexIDXHS_plugin_donate())
                                . '</div>
                                <br/><br/><br/>
                        </div>
                </div>';
	return $content;
}

function flexIDXHS_ga(){
	return '<img src="http://www.phoenixhomes.com/ga/plugins-img.php" style="border: none; background: none; padding:0; margin:0;" />';
}
?>