<?php

/**
 * A PHP wrapper for the flexmls REST API.
 *
 * Version: 1.3
 *
 * Source URI: https://github.com/flexmls/flexmls_api4p
 * Author: (c) Financial Business Systems, Inc.
 * Author URI: http://flexmls.com
 * License: Licensed under the Apache License, Version 2.0
 */

class flexmlsAPI {

	public $api_base = "api.flexmls.com";
	public $last_error_code = null;
	public $last_error_mess = null;
	public $api_roles = null;
	private $last_token = null;
	private $last_token_expire = null;
	private $api_key = null;
	private $api_secret = null;
	private $ch = null;
	private $debug_log;
	private $debug_mode = false;
	private $application_name = null;
	public $api_version = "v1";
	// pagination vars
	public $last_count = 0;
	public $page_size = 0;
	public $total_pages = 0;
	public $current_page = 0;

	function __construct($key, $secret) {
		// set the api key and secret based on passed parameters
		$this->api_key = $key;
		$this->api_secret = $secret;

		// initialize cURL for use later
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
	}

	function __destruct() {
		// clean cURL up
		curl_close($this->ch);
	}

	
	////////////////////////////////////////////////////////////////////
	//  AUTH FUNCTIONS
	////////////////////////////////////////////////////////////////////


	function Authenticate($force = false) {

		if ($this->last_token == null || $force == true) {
			$result = $this->MakeAPIRequest("POST", "/{$this->api_version}/session", array(), array(), $auth = true, $force);

			if ($result === false) {
				$code = $this->last_error_code;
				$message = $this->last_error_mess;
				throw new Exception($message, $code, $previous);
				return false;
			}

			$this->last_token = $result[0]['AuthToken'];
			$this->last_token_expire = $result[0]['Expires'];
		}
		
	}

	function GetSystemInfo() {

		$args = array();

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/system", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}

	function SetApplicationName($name) {
		$this->application_name = str_replace(array("\r", "\r\n", "\n"), '', trim($name));
	}

	function SetDebugMode($mode = false) {
		$this->debug_mode = $mode;
		// enable logging if we're in debug mode
		if ($this->debug_mode == true) {
			$this->debug_log = fopen("debug.log", 'a');
			curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
			curl_setopt($this->ch, CURLOPT_STDERR, $this->debug_log);
		}
	}
	
	function SetDeveloperMode($enable = false) {
		if ($enable) {
			$this->api_base = "api.developers.flexmls.com";
			return true;
		}
		else {
			return false;
		}
	}

	function GetErrors() {
		if ($this->last_error_code && $this->last_error_mess) {
			return $this->last_error_code . ' - ' . $this->last_error_mess;
		}
		else {
			return false;
		}
	}

	function HasBasicRole() {

		if (is_array($this->api_roles)) {
			if (in_array('basic', $this->api_roles)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
		
	}

	
	////////////////////////////////////////////////////////////////////
	//  ACCOUNT FUNCTIONS
	////////////////////////////////////////////////////////////////////
	
	
	function GetMyAccount() {
		
		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/my/account", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}
		
	function GetConnectPrefs() {

		$args = array();

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/connect/prefs", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		$records = array();
		foreach ($result as $pref) {
			$records[$pref['Name']] = $pref['Value'];
		}
		return $records;
		
	}
	
	function GetIDXLinks($tags = "") {

		$args = array();

		$tags = trim($tags);
		if (!empty($tags)) {
			$args['tags'] = $this->clean_comma_list($tags);
		}

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/idxlinks", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	function GetSavedSearches($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/savedsearches/{$id}", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	
	////////////////////////////////////////////////////////////////////
	//  CONTACTS FUNCTIONS
	////////////////////////////////////////////////////////////////////
	
	
	function GetContacts($tags = "") {

		$endpoint = "/{$this->api_version}/contacts";
		if (!empty($tags)) {
			$endpoint = "/{$this->api_version}/contacts/tags/" . rawurlencode($tags);
		}

		$result = $this->MakeAPIRequest("GET", $endpoint, array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	function SendContact($contact_data) {
		
		$args = array();

		$data = array('Contacts' => array($contact_data));

		$result = $this->MakeAPIRequest("POST", "/{$this->api_version}/contacts", $args, $data, $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}
	
	
	////////////////////////////////////////////////////////////////////
	//  METADATA FUNCTIONS
	////////////////////////////////////////////////////////////////////
	
	
	function GetStandardFields() {
		
		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/standardfields", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}

	function GetStandardFieldList($field) {
		
		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/standardfields/{$field}", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0][$field]['FieldList'];
		
	}
	
	function GetPropertyTypes() {

		$args = array();

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/propertytypes", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		$records = array();
		foreach ($result as $res) {
			$records[$res['MlsCode']] = $res['MlsName'];
		}

		return $records;
		
	}
	
	
	////////////////////////////////////////////////////////////////////
	//  SEARCH/LISTING FUNCTIONS
	////////////////////////////////////////////////////////////////////


	function GetListing($id, $args = array()) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetListingOpenHouses($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}/openhouses", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetListingPhotos($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}/photos", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetListingDocs($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}/documents", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetListingVirtualTours($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}/virtualtours", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	function GetListingVideos($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings/{$id}/videos", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;

	}

	function GetListings($args = array()) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/listings", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetMyListings($args = array()) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/my/listings", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetOfficeListings($args = array()) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/office/listings", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}

	function GetCompanyListings($args = array()) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/company/listings", $args, array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	
	////////////////////////////////////////////////////////////////////
	//  ROSTER FUNCTIONS
	////////////////////////////////////////////////////////////////////
	
	
	function GetOfficeAgents($id) {

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/accounts/by/office/{$id}", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result;
		
	}
	
	function GetOfficeRoster($args = array()) {
	
		$query = "UserType Eq 'Office'";
		if (array_key_exists('_filter', $args)) {
			$args['_filter'] .= " And ". $query;
		}
		else {
			$args['_filter'] = $query;
		}
		
		return $this->GetRoster($args);
		
	}
		
	function GetAgentRoster($args = array()) {
	
		$query = "UserType Eq 'Member'";
		if (array_key_exists('_filter', $args)) {
			$args['_filter'] .= " And ". $query;
		}
		else {
			$args['_filter'] = $query;
		}
		
		return $this->GetRoster($args);
		
	}
		
	function GetRoster($args = array()) {
			
		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/accounts", $args, array(), $auth = false);
		
		if ($result === false) {
			return $result;
		}
		
		return $result;
		
	}
	
	function GetAccountDetails($id) {
		
		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/accounts/{$id}", array(), array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}
	
	
	////////////////////////////////////////////////////////////////////
	//  OTHER FUNCTIONS
	////////////////////////////////////////////////////////////////////
	

	function GetMarketStats($type, $options = "", $property_type = "", $location_name = "", $location_value = "") {

		$args = array();

		if (!empty($options)) {
			$args['Options'] = $options;
		}

		if (!empty($property_type)) {
			$args['PropertyTypeCode'] = $property_type;
		}

		if (!empty($location_name)) {
			$args['LocationField'] = $location_name;
			$args['LocationValue'] = $location_value;
		}

		$result = $this->MakeAPIRequest("GET", "/{$this->api_version}/marketstatistics/{$type}", $args, $data = array(), $auth = false);

		if ($result === false) {
			return false;
		}

		return $result[0];
		
	}



	/*
	 * Makes the API call to the flexmls API.
	 *
	 * @param string $method HTTP method to use when making the call.  GET, POST, etc.
	 * @param string $uri HTTP request URI to hit with the request
	 * @param array $args array of key/value pairs of parameters.  added to request depending on HTTP method
	 * @param array $caching array of caching settings. 'enabled' is true/false. 'minutes' defines how long if enabled
	 * @return mixed Returns array of parsed JSON results if successful.  Returns false if API call fails
	 */

	function MakeAPIRequest($method, $uri, $args = array(), $data = array(), $is_auth_request = false, $a_retry = false) {

		if (!is_array($args)) {
			$args = array();
		}

		$http_parameters = $args;


		// start with the basic part of the security string and add to it as we go
		$sec_string = "{$this->api_secret}ApiKey{$this->api_key}";

		$post_body = "";

		if ($method == "POST" && count($data) > 0) {
			// the request is to post some JSON data back to the API (like adding a contact)
			$post_body = json_encode(array('D' => $data));
		}

		if ($is_auth_request) {
			$http_parameters['ApiKey'] = $this->api_key;
		}
		else {
			$http_parameters['AuthToken'] = $this->last_token;

			// since this isn't an authentication request, add the ServicePath to the security string
			$sec_string .= "ServicePath" . rawurldecode($uri);

			ksort($http_parameters);

			// add each of the HTTP query string parameters to the security string
			foreach ($http_parameters as $k => $v) {
				$sec_string .= $k . $v;
			}
		}

		if (isset($post_body) && !empty($post_body)) {
			// add the JSON data to the end of the security string if it exists
			$sec_string .= $post_body;
		}

		// calculate the security string as ApiSig
		$api_sig = md5($sec_string);
		$http_parameters['ApiSig'] = $api_sig;

		if ($is_auth_request == true) {
			$http_proto = "https://";
		}
		else {
			$http_proto = "http://";
		}

		// start putting the URL parts together
		$full_url = $http_proto . $this->api_base . $uri;

		// take the parameter key/values and put them into a URL-like structure.  key=value&key2=value2& etc.
		$query_string = "";
		foreach ($http_parameters as $k => $v) {
			if (!empty($query_string)) {
				$query_string .= "&";
			}
			$query_string .= $k . '=' . rawurlencode($v);
		}

		if (!empty($query_string)) {
			$full_url .= '?' . $query_string;
		}

		if ($this->debug_mode) {
			echo $full_url . "\n\n";
		}

		$request_headers = "";

		curl_setopt($this->ch, CURLOPT_URL, $full_url);

		if ($method == "POST") {
			// put the built parameter key/values as the body of the POST request
			$request_headers .= "Content-Type: application/json\r\n";
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_body);
		}
		else {
			curl_setopt($this->ch, CURLOPT_POST, 0);
		}

		$request_headers .= "User-Agent: flexmls API PHP Client/0.1\r\n";
		if (!empty($this->application_name)) {
			$request_headers .= "X-flexmlsApi-User-Agent: {$this->application_name}\r\n";
		}

		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(trim($request_headers)));

		$response_body = curl_exec($this->ch);

		if ($this->debug_mode == true) {
			fwrite($this->debug_log, $response_body . "\n");
		}

		if ($this->debug_mode) {
			echo $response_body . "\n\n";
		}

		// start handling the response
		$json = json_decode(utf8_encode($response_body), true);

		if (!is_array($json) || !array_key_exists('D', $json)) {
			return false;
		}

		if (array_key_exists('Code', $json['D'])) {
			$this->last_error_code = $json['D']['Code'];
		}
		
		if (array_key_exists('Message', $json['D'])) {
			$this->last_error_mess = $json['D']['Message'];
		}

		if (array_key_exists('Pagination', $json['D'])) {
			$this->last_count = $json['D']['Pagination']['TotalRows'];
			$this->page_size = $json['D']['Pagination']['PageSize'];
			$this->total_pages = $json['D']['Pagination']['TotalPages'];
			$this->current_page = $json['D']['Pagination']['CurrentPage'];
		}

		if ($json['D']['Success'] == true) {
			return $json['D']['Results'];
		}
		elseif ($a_retry == false && $is_auth_request == false && ($this->last_error_code == 1020 || $this->last_error_code == 1000)) {
			$this->Authenticate(true);
			$return = $this->MakeAPIRequest($method, $uri, $args, $data, $is_auth_request, $a_retry = true);
			return $return;
		}
		else {

			if ($this->last_error_code == "") {
				$this->last_error_code = "API Down";
				$this->last_error_mess = "The flexmls IDX API didn't respond as expected.";
			}

			return false;
		}
		
	}

	/*
	 * Take a value and clean it so it can be used as a parameter value in what's sent to the API.
	 *
	 * @param string $var Regular string of text to be cleaned
	 * @return string Cleaned string
	 */

	function clean_comma_list($var) {

		$return = "";

		if (strpos($var, ',') !== false) {
			// $var contains a comma so break it apart into a list...
			$list = explode(",", $var);
			// trim the extra spaces and weird characters from the beginning and end of each item in the list...
			$list = array_map('trim', $list);
			// and put it back together as a comma-separated string to be returned
			$return = implode(",", $list);
		}
		else {
			// trim the extra spaces and weird characters from the beginning and end of the string to be returned
			$return = trim($var);
		}

		return $return;
	}

}
