<?php
/*
Plugin Name: flexIDX Home Search
Plugin URI: http://www.phoenixhomes.com/tech/flexidx-home-search
Description: flexIDX/flexMLS customers only:Provides flexible Home Search widget for your sidebars as well as ability to generate custom search links and iframes that can be embedded into post and page content.
Author: Max Chirkov
Version: 0.1
Author URI: http://www.PhoenixHomes.com
*/

define("PLUGIN_BASENAME", plugin_basename(dirname(__FILE__)));
define("FLEXIDXHS_DIR", WP_PLUGIN_DIR . '/' . PLUGIN_BASENAME);
define("FLEXIDXHS_URL", WP_PLUGIN_URL . '/' . PLUGIN_BASENAME);

include 'options/options.php';
$flexidxhs_opt = get_option('flexidxhs');
include_once ("tinymce/tinymce.php");

class flexIDXHS_QuickSearch extends WP_Widget {

	function flexIDXHS_QuickSearch() {
		$widget_ops = array('classname' => 'flexIDXHS_QuickSearch', 'description' => __('flexIDX Quick Search'));
		$control_ops = array('width' => 280);
		$this->WP_Widget('flexIDXHS_QuickSearch', __('flexIDX Quick Search'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('flexIDXHS_QuickSearch', empty($instance['title']) ? '' : $instance['title']);
		if ( !empty( $title ) ) { $title = $before_title . $title . $after_title; }
			$output = $before_widget;			
			$output .= $title;                        
			$output .= flexIDXHS_QuickSearch_HTML();			
			$output .= $after_widget;
		if($instance['return'] == true){
			return $output;
		}else{
			echo $output;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Quick Home Search' ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

<?php
	}
}

function flexIDXHS_QuickSearch_HTML(){
    global $flexidxhs_opt;

$output = <<<ENDOFHTMLBLOCK
<script type="text/javascript">
/* <![CDATA[ */
var quick_search_base_url = '{$flexidxhs_opt['idx-url']}';

function searchnow() {
  
  var city = document.getElementById('city').options[document.getElementById('city').selectedIndex];
  if(document.getElementById('min_price')){
    var min_price = document.getElementById('min_price').options[document.getElementById('min_price').selectedIndex];
    var max_price = document.getElementById('max_price').options[document.getElementById('max_price').selectedIndex];
  }
  if(document.getElementById('price_range')){
    var price_range = document.getElementById('price_range').options[document.getElementById('price_range').selectedIndex];
  }
  var property_type = document.getElementById('property_type').options[document.getElementById('property_type').selectedIndex];
  var bedrooms = document.getElementById('bedrooms').options[document.getElementById('bedrooms').selectedIndex];
  var bathrooms = document.getElementById('bathrooms').options[document.getElementById('bathrooms').selectedIndex];

  var search_link = quick_search_base_url;
  if(property_type && property_type != ''){ search_link += '&DwellingType=' + property_type.value; }
  if(city.value != ''){ search_link += '&City/TownCode=' + city.value; }
  if(min_price && min_price.value != '' && max_price.value != ''){ search_link += '&ListPrice=' + min_price.value + ',' + max_price.value; }
  if(price_range && price_range.value != ''){ search_link += '&ListPrice=' + price_range.value; }
  if(bedrooms.value != ''){ search_link += '&Bedrooms=>' + bedrooms.value; }
  if(bathrooms.value != ''){ search_link += '&Bathrooms=>' + bathrooms.value; }    

  popupWin = window.open(search_link, 'open_window');

}

function advsearch() {
  popupWin = window.open(quick_search_base_url, 'open_window');
}
/* ]]> */
</script>
ENDOFHTMLBLOCK;

    if($flexidxhs_opt['search-buttons']['search-label'] && !empty($flexidxhs_opt['search-buttons']['search-label'])){
        $search_label = $flexidxhs_opt['search-buttons']['search-label'];
    }else{
        $search_label = 'Search Now';
    }
    if($flexidxhs_opt['search-buttons']['advanced-search-label'] && !empty($flexidxhs_opt['search-buttons']['advanced-search-label'])){
        $adv_search_label = $flexidxhs_opt['search-buttons']['advanced-search-label'];
    }else{
        $adv_search_label = 'Advanced Search';
    }

    if($flexidxhs_opt['search-buttons']['display-advanced-search']){
        $class_on = 'class="advanced-search-on"';
        $advanced_search = '<input type="button" name="AdvancedSearch" id="AdvancedSearch" value="'.$adv_search_label.'" onclick="advsearch();" />';
    }else{
        $class_on = 'class="advanced-search-off"';
    }

    $output .= "<div class='flexIDXHS_QuickSearch'>";			
    $output .= "<div class='flexIDXHS_QuickSearch_form'>";
    $output .= flexIDXHS_QuickSearch_form();
    $output .= '<div clear="all" '.$class_on.'>';
    $output .= '<input type="button" name="SearchNow" id="SearchNow" value="'.$search_label.'" onclick="searchnow();" />';
    $output .= $advanced_search;
    $output .= '</div>';
    $output .= "</div>";
    $output .= "</div>";

    return $output;
}

/*
 * Functions to return preset values
 */

function flexIDXHS_beds(){
    $beds = array(1=>'1+', 2=>'2+', 3=>'3+', 4=>'4+', 5=>'5+');
    return $beds;
}
function flexIDXHS_baths(){
    $baths = array(1=>'1+', 2=>'2+', 3=>'3+', 4=>'4+', 5=>'5+', 6=>'6+');
    return $baths;
}
function flexIDXHS_price_min(){
    $price_min = array(
		100000 => '$100,000', 125000 => '$125,000', 150000 => '$150,000', 175000 => '$175,000', 200000 => '$200,000', 250000 => '$250,000', 300000 => '$300,000', 350000 => '$350,000', 400000 => '$400,000', 450000 => '$450,000', 500000 => '$500,000', 550000 => '$550,000', 600000 => '$600,000', 650000 => '$650,000', 700000 => '$700,000', 750000 => '$750,000', 800000 => '$800,000', 850000 => '$850,000', 900000 => '$900,000', 950000 => '$950,000', 1000000 => '$1,000,000', 1250000 => '$1250,000', 1500000 => '$1500,000', 1750000 => '$1750,000', 2000000 => '$2,000,000', 2500000 => '$2,500,000', 3000000 => '$3,000,000',
	);
    return $price_min;
}
function flexIDXHS_price_max(){
    $price_max = array(
                '999999999999' => 'No Max Price', 125000 => '$125,000', 150000 => '$150,000', 175000 => '$175,000', 200000 => '$200,000', 250000 => '$250,000', 300000 => '$300,000', 350000 => '$350,000', 400000 => '$400,000', 450000 => '$450,000', 500000 => '$500,000', 550000 => '$550,000', 600000 => '$600,000', 650000 => '$650,000', 700000 => '$700,000', 750000 => '$750,000', 800000 => '$800,000', 850000 => '$850,000', 900000 => '$900,000', 950000 => '$950,000', 1000000 => '$1,000,000', 1250000 => '$1250,000', 1500000 => '$1500,000', 1750000 => '$1750,000', 2000000 => '$2,000,000', 2500000 => '$2,500,000', 3000000 => '$3,000,000', 4000000 => '$4,000,000', 5000000 => '$5,000,000', 6000000 => '$6,000,000', 7000000 => '$7,000,000', 8000000 => '$8,000,000',
       );
    return $price_max;
}
function flexIDXHS_property_types(){
    $property_types = array(
		//'label'         => ($opt['label-names']['property-type']) ? $opt['label-names']['property-type'] : 'Select Property Type',
		'SF,PH'         => 'Single Family Homes',
		'TH,AF'         => 'Condos/Townhomes',
		'LS'            => 'Loft Style',
	);
    return $property_types;
}
function flexIDXHS_price_range(){
    $price_range = array(
            '0,100000'          => 'Up to $100,000',
            '100000,150000'     => '$100,000 - $150,000',
            '150000,200000'     => '$150,000 - $200,000',
            '200000,250000'     => '$200,000 - $250,000',
            '250000,300000'     => '$250,000 - $300,000',
            '300000,350000'     => '$300,000 - $350,000',
            '350000,400000'     => '$350,000 - $400,000',
            '400000,450000'     => '$400,000 - $450,000',
            '450000,500000'     => '$450,000 - $500,000',
            '500000,600000'     => '$500,000 - $600,000',
            '600000,700000'     => '$600,000 - $700,000',
            '700000,800000'     => '$700,000 - $800,000',
            '800000,1000000'     => '$800,000 - $1,000,000',
            '1000000,1500000'     => '$1,000,000 - $1,500,000',
            '1500000,2000000'     => '$1,500,000 - $2,000,000',
            '2000000,2500000'     => '$2,000,000 - $2,500,000',
            '2500000,3000000'     => '$2,500,000 - $3,000,000',
            '3000000,3500000'     => '$3,000,000 - $3,500,000',
            '3500000,4000000'     => '$3,500,000 - $4,000,000',
            '4000000,5000000'     => '$4,000,000 - $5,000,000',
            '5000000,'     => 'Over $5,000,000',
        );
    return $price_range;
}

function flexIDXHS_QuickSearch_form(){
    global $flexidxhs_opt;
    $opt = $flexidxhs_opt;

        $label_options_vocab = array(
            /*
             * array var ponting to option name in the settings
             */
            'cities'            => 'city',
            'property_types'    => 'property-type' ,
            'price_min'         => 'min-price',
            'price_max'         => 'max-price',
            'beds'              => 'bedrooms',
            'baths'             => 'bathrooms',
        );

        $cities = $opt['city-list'];        
        if(!isset($opt['label-names']['city'])){
            $cities['label'] = 'Select City';
        }elseif($opt['label-names']['city'] != NULL){
            $cities['label'] = $opt['label-names']['city'];
        }        

	$property_types = flexIDXHS_property_types();
        if(!isset($opt['label-names']['property-type'])){
            $property_types['label'] = 'Select Property Type';
        }elseif($opt['label-names']['property-type']!= NULL){
            $property_types['label'] = $opt['label-names']['property-type'];
        }

	$price_min = flexIDXHS_price_min();
        if(!isset($opt['label-names']['min-price'])){
            $price_min['label'] = 'Min';
        }elseif($opt['label-names']['min-price'] != NULL){
            $price_min['label'] = $opt['label-names']['min-price'];
        }

        $price_max = flexIDXHS_price_max();
        if(!isset($opt['label-names']['max-price'])){
            $price_max['label'] = 'Max';
        }elseif($opt['label-names']['max-price'] != NULL){
            $price_max['label'] = $opt['label-names']['max-price'];
        }

        $price_range = flexIDXHS_price_range();
        if(!isset($opt['label-names']['price-range'])){
            $price_range['label'] = 'Price Range';
        }elseif($opt['label-names']['price-range'] != NULL){
            $price_range['label'] = $opt['label-names']['price-range'];
        }

	$beds = flexIDXHS_beds();
	if(!isset($opt['label-names']['bedrooms'])){
            $beds['label'] = 'Bedrooms';
        }elseif($opt['label-names']['bedrooms'] != NULL){
            $beds['label'] = $opt['label-names']['bedrooms'];
        }

        $baths = flexIDXHS_baths();
        if(!isset($opt['label-names']['bathrooms'])){
            $baths['label'] = 'Bathrooms';
        }elseif($opt['label-names']['bathrooms'] != NULL){
            $baths['label'] = $opt['label-names']['bathrooms'];
        }
        
        /*
         * Check price type option
         */
       if($opt['price-fields'] == 'price-range'){
           $query_properties = array(
		'City'              => $cities,
		'Price Range'       => $price_range,
		'Property Type'     => $property_types,
		'Bedrooms'          => $beds,
		'Bathrooms'         => $baths,
            );
       }else{
           $query_properties = array(
		'City'              => $cities,
		'Min Price'         => $price_min,
		'Max Price'         => $price_max,
		'Property Type'     => $property_types,
		'Bedrooms'          => $beds,
		'Bathrooms'         => $baths,
            );
       }

	

	foreach($query_properties as $name => $properties){
                /*
                 * Checking Fields label Visibility Settings
                 */
                if($properties['label']){
                    if($opt['label-visibility'] == 'outside'){
                        $output .= '<label id="label_'. str_replace(' ', '', strtolower($name)) .'"><span>'. $properties['label'] .'</span>'; $close_label = '</label>';
                        unset($properties['label']);
                    }elseif($opt['label-visibility'] == 'inside'){
                        $inside_label .= "\t" . '<option value="">'. $properties['label'] .'</option>' . "\n";
                        unset($properties['label']);
                    }else{
                        unset($properties['label']);
                    }
                }
                
                if($name == 'City'){
                    foreach($properties as $k => $v){
                        if(is_numeric($k)){
                            $_cities[$v] = $v;
                        }
                    }
                    unset($properties);
                    $properties = $_cities;
                }		

               
                $output .= "\n" . '<select id="'. str_replace(' ', '_', strtolower($name)) .'" name="'. str_replace(' ', '', $name) .'">' . "\n";
                $output .= $inside_label;
                unset($inside_label);
		foreach($properties as $k => $v){
			$output .= "\t" . '<option value="'. $k .'">'. $v .'</option>' . "\n";
		}
		$output .= '</select>' . $close_label ."\n";
                unset($close_label);
	}

	return $output;

}


function flexIDXHS_admin_scripts(){
    if (isset($_GET['page']) && strstr($_GET['page'], 'flexIDX_options') || strstr($_GET['page'], 'flexIDX_options')){
		wp_enqueue_script('postbox');
		wp_enqueue_script('dashboard');
		wp_enqueue_style('dashboard');
		wp_enqueue_style('global');
		wp_enqueue_style('wp-admin');
	}
}
add_action('init', 'flexIDXHS_admin_scripts');


function flexIDXHS_styles(){
	$myStyleUrl = WP_PLUGIN_URL . '/flexidxhs/style.css';
    $myStyleFile = WP_PLUGIN_DIR . '/flexidxhs/style.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('flexIDXHS', $myStyleUrl);
        wp_print_styles( 'flexIDXHS');
    }
}

if($flexidxhs_opt['idx-url']){
    add_action('wp_head', 'flexIDXHS_styles');
    add_action('widgets_init', create_function('', 'return register_widget("flexIDXHS_QuickSearch");'));
}

/*
 * Shortcodes
 */
function flexIDXHS_widget_shortcode($atts = array()){
   return flexIDXHS_QuickSearch_HTML();
}
add_shortcode('flexidxhs', 'flexIDXHS_widget_shortcode');

function flexIDX_iframe_shortcode($atts){
    extract(
        shortcode_atts(array(
            'url'       => false,
            'width'     => '100%',
            'height'    => '800',
        ), $atts)
    );    
    return '<iframe src="' . esc_attr($url) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></iframe>';
}
add_shortcode('idxiframe', 'flexIDX_iframe_shortcode');
?>