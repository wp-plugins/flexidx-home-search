<?php
require_once(FLEXIDXHS_INC . "/Class-HomeSearch.php");

$idx_instance_cache = array();
$idx_api_auth = false;

//"armls_mp067_key", "xSn0w-h1L"
if($flexidxhs_opt['idx']['api_key'] && $flexidxhs_opt['idx']['api_secret']){
	$idx = new flexIDX($flexidxhs_opt['idx']['api_key'], $flexidxhs_opt['idx']['api_secret']);
}
$idx->api->debug = false;
add_action('init', 'idx_shutdown_function');

function idx_shutdown_function(){
	global $idx;

	if($idx->api->debug == true && !is_admin())
		register_shutdown_function('_execution', &$idx);
}

function idx_search_form(){
	global $idx;
	$idx->search_form();
}

function _execution(&$idx){
	print '<pre>';
	print_r($idx);
	print '</pre>';
}
function _execution_track($time_start,$_params){
	global $idx;
	if(!is_single())
          return;

	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$_params['execution time'] = $time;
	$idx->api->debug(__FUNCTION__, array('execution_tacker' => $_params));
}

//Remove paragraph tags that are atuomatically added to the posts
//on the IDX pages.
function _remove_p_tags(){
	global $wp_query;

	if(is_page('idx') || isset($wp_query->query_vars["idx-action"]))
		remove_filter('the_content', 'wpautop');

}
add_action('the_post', '_remove_p_tags');

function api_error_thrown($api) {
	echo "API Error Code: {$api->last_error_code}<br>\n";
	echo "API Error Message: {$api->last_error_mess}<br>\n";
	exit;
}

add_action('wp_ajax_idx_getStats_ajax', 'idx_getStats_ajax');
add_action('wp_ajax_nopriv_idx_getStats_ajax', 'idx_getStats_ajax');
function idx_getStats_ajax(){
  global $idx;
  $zipcode = $_REQUEST['zipcode'];
  if(!is_numeric($zipcode) || strlen($zipcode) != 5)
     die();

  $type = ($_REQUEST['type']) ? $_REQUEST['type'] : '';
  $property_type = ($_REQUEST['property_type']) ? $_REQUEST['property_type'] : '';
  $options = ($_REQUEST['options']) ? $_REQUEST['options'] : '';

  $stat_types = array('absorption', 'inventory', 'price', 'ratio', 'dom', 'volume');
  foreach($stat_types as $type){
    $result = $idx->api->GetMarketStats($type, $options, $property_type, $location_name = 'PostalCode', $location_value = $zipcode);
    $return[$type] = $result['data'];
  }
  if(!$return)
     die();

  foreach($return as $type => $result){
    foreach($result['Dates'] as $k => $date){
      $return[$type]['Dates'][$k] = date("M", strtotime($date));
    }
  }

  die(json_encode($return));
}

add_filter('idx-listing-tabs', '_add_tabs');
add_filter('idx-listing-tabs-content', '_add_tabs');
function _add_tabs($data){
  global $flexidxhs_opt;

  $filter = current_filter();

  //SREP Mortgage Calc Tab
  if(function_exists('srp_MortgageCalc_shortcode') && $flexidxhs_opt['idx']['display-srp-tabs']){
    if($filter == 'idx-listing-tabs'){
      $content .= ' <li><a href="#mortgage_tab"><span>Financing</span></a></li>';
    }elseif($filter == 'idx-listing-tabs-content'){
      $content .= ' <div id="mortgage_tab">' . srp_MortgageCalc_shortcode(array('price_of_home' => $data['raw'][0]['StandardFields']['ListPrice'])) . '</div>';
    }
  }

  //Market Stats Tab
  if($filter == 'idx-listing-tabs'){
    $content .= ' <li><a href="#market_stats_tab"><span>Market Statistics</span></a></li>';
  }elseif($filter == 'idx-listing-tabs-content'){
    $content .= '<div id="market_stats_tab"><h2><span>Zipcode ' . $data['validated'][0]['StandardFields']['PostalCode'] . ' - ' . $data['validated'][0]['StandardFields']['City'] . ' Market Statistics</span></h2></div>';
  }

  //Walk Score
  if(function_exists('srp_walkscore') && $flexidxhs_opt['idx']['display-srp-tabs']){
    if($filter == 'idx-listing-tabs'){
      $content .= ' <li><a href="#walkscore_tab"><span>Nearby</span></a></li>';
    }elseif($filter == 'idx-listing-tabs-content'){
      $content .= '<div id="walkscore_tab"><h2><span>Nearby Businesses & Organizations</span></h2>' . srp_walkscore($ws_wsid, flexIDX::listing_address($data['validated']), $ws_width = 600, $ws_height = 500, $ws_layout = 'horizontal') . '</div>';
    }
  }

  return $content;
}
?>