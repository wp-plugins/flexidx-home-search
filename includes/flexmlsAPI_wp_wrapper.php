<?php
/**
 * Wrapper class for the flexmls API client
 * Makes it play nicely with WordPress
 * Source: https://github.com/flexmls/flexmls_api4p/blob/master/flexmlsAPI.php
 * @author Max Chirkov
 */
class flexmlsAPI_wp_wrapper extends flexmlsAPI {
  public $debug = true;
  public $debug_output = array();

  function __construct($api_key, $api_secret){
    // set an ApplicationName which identifies us
    $this->SetApplicationName("flexIDXSearch/1.0");
    parent::__construct($api_key, $api_secret);
  }

  function debug($func, $data){
    $this->debug_output['function ' . $func .'()'][] = $data;
  }

  function GetListings($args = array(), $vars = array()){
    return $this->cache(__FUNCTION__, $args, $vars);
  }

  function GetListing($args = array(), $vars = array()){
    return $this->cache(__FUNCTION__, $args, $vars);
  }

  function GetMarketStats($type, $options = "", $property_type = "", $location_name = "", $location_value = ""){
    $vars = array(
        'type' => $type,
        'options' => $options,
        'property_type' => $property_type,
        'location_name' => $location_name,
        'location_value' => $location_value
    );
    return $this->cache(__FUNCTION__, $args = array(), $vars);
  }

  /**
   * Request chaching mechanism
   * checks the wp transient for existing cache
   * if it's expired or doesn't exist - chaces it.
   * @param string $api_method method to be called from flexmlsAPI class
   * @param array $parameters arguments that are being passed via $args parameter of the method
   * @param array $vars specific arguments that are being passed as strings to the method (like $id) when $args is not used.
   */
  function cache($api_method, $parameters = array(), $vars = array()){
		global $idx_instance_cache, $idx_api_auth;
                //Exctract vars if they are given
                if(!empty($vars))
                  extract($vars);

                //$mls will work as $id for single listing retrieval method
		if($mls){
                  $cache_string = $mls;
		}elseif($type){
                  $cache_string = http_build_query($vars);
                }else{
                  $cache_string = http_build_query($parameters);
		}

		$cache_item_name = md5($cache_string);
		$served_from_cache = null;

		// retrieve the cache data for this particular request
		$cache = get_transient('idx_cache_'. $cache_item_name);

		// check if the item's expire time has passed already
		if ($cache !== false) {
			// item exists and it hasn't expired yet, so we'll serve the request from cache
			$served_from_cache = $cache;
		}elseif(!empty($idx_instance_cache[ $cache_item_name ])) {
			$served_from_cache = $idx_instance_cache[ $cache_item_name ]['data'];
			$last_count = $idx_instance_cache[ $cache_item_name ]['last_count'];
		}

		// if we have a cached version of the listing - check if it has custom fields
		// and if it has an ID - that means we're inquiring on a specific listing rather than results
		// check if custom fields are cached - if not - send another API request
		if($mls && $id){
                  if(!$cache['data'][0]['CustomFields'])
                    $served_from_cache = null;
		}

		// since we didn't get any unexpired data from the cache, make the call
		if ($served_from_cache == null) {
			// issue the request to authenticate with the API
			if(!$idx_api_auth){
				$idx_api_auth = $this->Authenticate();

				if($idx_api_auth === false){
					api_error_thrown($this);
				}

			}

                        if($type){
                          $result['data'] = parent::$api_method($type, $options, $property_type, $location_name, $location_value);
                        }elseif($id){
                          $result['data'] = parent::$api_method($id, $parameters);
			}else{
                          $result['data'] = parent::$api_method($parameters);
			}

			if ($result === false) {
				api_error_thrown($this);
			}

			$result['last_count'] = $this->last_count;
			$data_source = "live";

		}else{
			// act like the API returned data when it was really the cache
			$result = $served_from_cache;
			$data_source = "cache";
		}

		// check a couple of conditions to see if we should update the cache
		if ($data_source == "live" && !empty($result['data'])) {

			// update transient item which tracks cache items
			$cache_tracker = get_transient('idx_cache_tracker');
			$cache_tracker[ $cache_item_name ] = true;
			set_transient('idx_cache_tracker', $cache_tracker, 60*60*24*7);

			$cache_expire_length = 12*60*60; //12 hours
			//$result = $this->utf8_encode_mix($result);
			$cache_set_result = set_transient('idx_cache_'. $cache_item_name, $result, $cache_expire_length);

			//cache each listing if they are from search results
                        /* Unnecessary since new API improvement
                         * We now can retreave all details by MLS#
                         *
			$num = count($result['data']);
			if($num > 1 && empty($vars)){

				foreach($result['data'] as $listing){
					$mls_cache_string = $listing['StandardFields']['ListingId'];
					$tmp['data'][0] = $listing;
					$cache_item_name = md5($mls_cache_string);
					$cache_set_result = set_transient('idx_cache_'. $cache_item_name, $tmp, $cache_expire_length);
				}
			}
                         * 
                         */
		}
		$parameters_to_log = array(
			'$api_method' => $api_method,
			'$data_source' => $data_source,
			'$cache_string' => $cache_string,
			'$parameters' => $parameters,
			'$id' => $id,
			'$mls' => $mls,
			//'result[data]' => $result['data']
		);
		$this->debug(__FUNCTION__, $parameters_to_log);
		return $result;
	}
}

?>