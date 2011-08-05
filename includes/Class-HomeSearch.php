<?php

require_once(FLEXIDXHS_LIB . "/flexmlsAPI.php");
require_once(FLEXIDXHS_INC . "/flexmlsAPI_wp_wrapper.php");

class flexIDX {

  private $api_key = null;
  private $api_secret = null;
  public $api = null;
  private $api_auth = false;
  private $idx_slug = false;
  private $idx_result_page = false;

  function __construct($key, $secret) {
    global $flexidxhs_opt;
    // set the api key and secret based on passed parameters
    $this->api_key = $key;
    $this->api_secret = $secret;
    //Initialize flex API
    $this->api = $this->init_api();

    $result_page = get_page($flexidxhs_opt['idx']['result-page']);
    $this->idx_slug = $result_page->post_name;
    $this->idx_result_page = $result_page->ID;

    add_action('init', array(&$this, 'idx_load_scripts'));
    add_filter('init', array(&$this, 'flush_rewrite_rules'));
    add_filter('query_vars', array(&$this, 'query_vars'));
    add_filter('rewrite_rules_array', array(&$this, 'rewrite_rules'));
    add_filter('posts_request', array(&$this, 'idx_clear_query'));
    add_action('pre_get_posts', array(&$this, 'idx_preactivate'));
    add_filter('the_posts', array(&$this, 'flexIDXHS_details'));
    add_action('the_content', array(&$this, 'flexIDXHS_idx'));
    add_shortcode( 'saved_search', array(&$this, 'saved_search_shortcode' ));
  }

  function init_api() {
    return new flexmlsAPI_wp_wrapper($this->api_key, $this->api_secret);
  }

  function search_form() {
    echo '<form action="/' . $this->idx_slug . '/" method="get">';
    include FLEXIDXHS_INC . '/search_form.php';
    echo '</form>';
  }

  function idx_preactivate($q) {
    global $wp_query;

    if (!is_array($wp_query->query) || !is_array($q->query))
      return;

    if (isset($wp_query->query["idx-action"])) {
      if (!isset($q->query["idx-action"])) {
        $wp_query->query["idx-action-swap"] = $wp_query->query["idx-action"];
        unset($wp_query->query["idx-action"]);
      } else {
        $q->query_vars["caller_get_posts"] = true;
      }
    }
  }

  function idx_clear_query($query) {
    global $wp_query;

    if (!is_array($wp_query->query) || !isset($wp_query->query["idx-action"]))
      return $query;

    return "";
  }

  function idx_search_results($custom_filter = false) {

    if (($_REQUEST['idxs_do'] && $_REQUEST['param'])) {

      $param['City'] = 'City Eq \'' . $_REQUEST['param']['City'] . '\'';
      $param['PropertySubType'] = 'PropertySubType Eq \'' . $_REQUEST['param']['PropertySubType'] . '\'';
      $param['BedsTotal'] = 'BedsTotal Ge ' . $_REQUEST['param']['BedsTotal'];
      $param['BathsTotal'] = 'BathsTotal Ge ' . $_REQUEST['param']['BathsTotal'];
      if ($_REQUEST['param']['PriceMin'])
        $price[] = 'ListPrice Ge ' . $_REQUEST['param']['PriceMin'];

      if ($_REQUEST['param']['PriceMax'])
        $price[] = 'ListPrice Le ' . $_REQUEST['param']['PriceMax'];

      if ($price)
        $param['ListPrice'] = implode(' And ', $price);

      foreach ($param as $k => $v) {
        if (empty($param[$k]))
          unset($param[$k]);
      }

      $query = implode(' And ', $param);

      if (!$query)
        return;

      $parameters = array(
          "_expand" => "Photos",
          "_filter" => "PropertyType Eq 'A' And " . $query,
          "_limit" => 10,
          "_pagination" => 1,
          "_page" => ($_REQUEST['page']) ? $_REQUEST['page'] : 1,
          "_orderby" => "ListPrice",
      );
    }elseif($custom_filter){
      $parameters = array(
          "_expand" => "Photos",
          "_filter" => $custom_filter,
          "_limit" => 10,
          "_pagination" => 1,
          "_page" => ($_REQUEST['page']) ? $_REQUEST['page'] : 1,
          "_orderby" => "ListPrice",
      );
    }

    //print $query;

    $result = $this->api->GetListings($parameters);

    if ($result === false || empty($result['data'])) {
      api_error_thrown($this->api);
    }

    $output .= "<div class=\"idx-total-found\">Total properties found: {$result['last_count']}</div>\n";
    $output .= $this->_pagination($result['last_count'], $parameters['_limit']);
    $i = 0;
    foreach ($result['data'] as $listing) {
      $i++;
      if ($i % 2 == 0) {
        $class = ' even ';
      } else {
        $class = ' odd ';
      }

      $output .= '<div class="idx-results' . $class . '">' . $this->_search_results_snippet($listing) . '</div>';
    }

    $output .= $this->_pagination($result['last_count'], $parameters['_limit']);

    return $output;
  }

  function _search_results_snippet($listing) {
    $alt = ($listing['StandardFields']['Photos'][0]['Name']) ? ' alt="' . $listing['StandardFields']['Photos'][0]['Name'] . '"' : false;
    $output .= '<h4><a href="' . $this->uri($listing, 'listing') . '">' . $this->_normalize($this->listing_address(array($listing)), 'string') . '</a></h4>';

    if ($listing['StandardFields']['Photos'][0]['Uri300']) {
      $output .= '<div class="main-photo"><a href="' . $this->uri($listing, 'listing') . '"><img src="' . $listing['StandardFields']['Photos'][0]['Uri300'] . '"' . $alt . '></a></div>';
    } else {
      $output .= '<div class="main-photo"><a href="' . $this->uri($listing, 'listing') . '"><img src="' . FLEXIDXHS_URL . '/images/photo-na.png"></a></div>';
    }
    $output .= '<div class="main-details">';

    $output .= '<div class="listprice"><span class="label">Price:</span> $' . number_format($listing['StandardFields']['ListPrice']) . "</div>";
    $output .= "<div class=\"listingid\"><span class=\"label\">MLS#:</span> {$listing['StandardFields']['ListingId']}</div>\n";
    $output .= "<div class=\"propertysubtype\"><span class=\"label\">Property Type:</span> {$listing['StandardFields']['PropertySubType']}</div>\n";
    $output .= "<div class=\"bedrooms\"><span class=\"label\">Bedrooms:</span> {$listing['StandardFields']['BedsTotal']}</div>";
    $output .= "<div class=\"bathrooms\"><span class=\"label\">Bathrooms:</span> {$listing['StandardFields']['BathsTotal']}</div>";
    $output .= "<div class=\"listofficename\"><span class=\"label\">Listing Office:</span> {$listing['StandardFields']['ListOfficeName']}</div>";
    $output .= '<div class="idx-contact-listing">';
    $output .= '<a href="' . $this->uri($listing, 'listing') . '">View Details</a>';
    $output .= '</div>';

    $output .= '</div>';

    return '<div class="idx-result-snippet clearfix">' . $output . '</div>';
  }

  function flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
  }

  function rewrite_rules($incomingRules) {
    $idxRules = array(
        $this->idx_slug . "/mls-(\\d+)-(.*)" => 'index.php?idx-action=details&idx-MlsNumber=$matches[1]&idx-AddressSlug=$matches[2]',
        $this->idx_slug . '/mls-(.+)' => 'index.php?idx-action=details&idx-MlsNumber=$matches[1]',
    );

    return $idxRules + $incomingRules;
  }

  function query_vars($queryVars) {
    $queryVars[] = 'idx-action';
    $queryVars[] = 'idx-PropertyTypes';
    $queryVars[] = 'idx-Cities';
    $queryVars[] = 'idx-BedsMin';
    $queryVars[] = 'idx-BathsMin';
    $queryVars[] = 'idx-PriceMin';
    $queryVars[] = 'idx-PriceMax';
    $queryVars[] = 'idx-MlsNumber';
    $queryVars[] = 'idx-AddressSlug';

    return $queryVars;
  }

  function get_post($listing, $title, $content, $slug, $parent = false) {
    global $wp_query;

    $wp_query->found_posts = 0;
    $wp_query->max_num_pages = 0;
    $wp_query->is_page = 1;
    $wp_query->is_home = null;
    $wp_query->is_singular = 1;

    $description = NULL;

    set_query_var("name", "idx-listing");
    set_query_var("pagename", "idx-listing");

    $posts = array((object) array(
            "ID" => time(),
            "comment_count" => 0,
            "comment_status" => "closed",
            "ping_status" => "closed",
            "post_author" => 1,
            "post_content" => $content,
            "post_date" => date("c"),
            "post_date_gmt" => gmdate("c"),
            "post_excerpt" => $description,
            "listing" => $listing,
            "post_name" => $slug,
            "post_parent" => $this->idx_result_page, //To Do: add admin settings with selection of the search page
            "post_status" => "publish",
            "post_title" => $title,
            "post_type" => "page",
            ));


    return $posts;
  }

  function flexIDXHS_idx($content) {
    global $wp_query;

    if (!is_page($this->idx_slug))
      return $content;

    if ($_GET['idxs_do']) {
      $title = 'Search Results';
      $slug = $this->idx_slug;
      $content = $this->idx_search_results();
    }


    //$output = get_post($title, $content, $slug, $parent = false);
    //$output = $content;
    return $content;
  }

  function _pagination($total, $on_page = 10) {
    $page = 1;
    if ($_GET['page'])
      $page = $_GET['page'];

    $q = '&';
    if (!strstr($_SERVER["REQUEST_URI"], '?')) {
      $q = '?';
    } elseif (strstr($_SERVER["REQUEST_URI"], '?page')) {
      $q = '?';
    }

    $tmp = explode($q . 'page', $_SERVER["REQUEST_URI"]);
    $url = $tmp[0];
    $left_num = $total - ($page * $on_page);
    $next_page = $page + 1;
    $prev_page = $page - 1;
    $last_page = ceil($total / $on_page);
    $top_listing_num = $prev_page * $on_page + 1;
    $bottom_listing_num = $prev_page * $on_page + $on_page;
    if ($page > 1) {
      $nav[] = '<a href="' . $url . '">&#0171; First</a>';
      if ($prev_page > 1) {
        $nav[] = '<a href="' . $url . $q . 'page=' . $prev_page . '"> &#0171; Previous</a>';
      }
    }
    if ($left_num > 0) {
      $nav[] = '<a href="' . $url . $q . 'page=' . $next_page . '"> Next &#0187</a>';
      $nav[] = '<a href="' . $url . $q . 'page=' . $last_page . '">Last &#0187;</a>';
    } else {
      $bottom_listing_num = $total;
    }
    if ($total > 0 && $nav[0]) {
      $output .= '<div class="idx-pagination">Property listings ' . $top_listing_num . ' - ' . $bottom_listing_num . ' of ' . $total . ' | ';
      $output .= implode(' | ', $nav) . ' </div>';
    }

    return $output;
  }

  function flexIDXHS_details($posts) {
    global $wp_query;

    $action = strtolower($wp_query->query["idx-action"]);

    if (is_array($wp_query->query) && isset($wp_query->query["idx-action-swap"])) {
      $wp_query->query["idx-action"] = $wp_query->query["idx-action-swap"];
      unset($wp_query->query["idx-action-swap"]);
      return $posts;
    }

    if (!is_array($wp_query->query) || !isset($wp_query->query["idx-action"])) {
      return $posts;
    }

    if ($action == 'details' && $wp_query->query_vars['idx-MlsNumber']) {
      $listing = $this->get_listing($wp_query->query_vars['idx-MlsNumber']);
      $content = $this->the_listing($listing);
      $title = 'MLS# ' . $listing['validated'][0]['StandardFields']['ListingId'] . ' - ' . $this->_normalize($this->listing_address($listing['validated']), 'string');
      $slug = $this->slug($listing['validated'][0], 'listing');
      $posts = $this->get_post($listing['validated'], $title, $content, $slug, 570);
      unset($wp_query->query["idx-action"]);
    }
    return $posts;
  }

  //Returns listing object
  function get_listing($mls) {
    global $wp_query;

    $result = $this->api->GetListings(array('_filter' => "ListingId Eq '{$mls}'", '_expand' => "Photos,CustomFields", '_limit' => 1));
    $id = $result['data'][0]['Id'];

    //check if the URL is complete and correct
    $query_slug = "mls-{$mls}-" . $wp_query->query_vars['idx-AddressSlug'] . '/';
    $slug = $this->slug($result['data'][0], 'listing');
    if ($query_slug != $slug) {
      wp_redirect($this->uri($result['data'][0], 'listing'), 301);
      exit;
    }

    /* Unnecessary due to improved API
    if (!$result['data'][0]['CustomFields']) {
      $parameters = array(
          "_expand" => "Photos,CustomFields",
      );

      $result = $this->api->GetListing($parameters, array('id' => $id, 'mls' => $mls));
    }*/

    if (!$result)
      return;

    $data['raw'] = $result['data'];
    $data['validated'] = $this->_validate_fields($this->_restructure_results_array($data['raw']));
    return $data;
  }

  function the_listing($data) {

    $ref_url = getenv("HTTP_REFERER");
    if (stristr($ref_url, 'idxs_do=search')) {
      $back2results = '<div class="idx-return2results"><a href="' . $ref_url . '" alt="Return to Search Results">Return to Search Results</a></div>';
    }

    $photos = $data['validated'][0]['StandardFields']['Photos'];


    //JS listing data for future use by the JS scripts
    $ajax_data = array(
        'zip' => $data['validated'][0]['StandardFields']['PostalCode'],
        'lat' => $data['validated'][0]['StandardFields']['Latitude'],
        'lng' => $data['validated'][0]['StandardFields']['Longitude'],
        'addr' => $this->listing_address($data['validated']),
        'ajaxurl' => admin_url('admin-ajax.php')
    );
    wp_localize_script('idx-scripts', 'idxAjax', $ajax_data);

    $content .= $back2results;
    //Begint Tabs
    //TODO: Make a separate function for tabs and their respective content
    // so it would be possible to organize tabs and content in a spceific order
    // it could be a function that puts tabs into associative array
    // or we could use that function to order content with applied filters and order parameter.
    $content .= '<div id="idx_property_tabs">';
    $content .= '<ul class="clearfix">';
    $content .= ' <li><a href="#property_details_tab"><span>Property Details</span></a></li>';
    $content .= ' <li><a href="#property_map_tab"><span>Maps</span></a></li>';

    $content .= apply_filters('idx-listing-tabs', $content);

    $content .= '</ul>';


    $content .= '<div id="property_details_tab">';
    $content .= '<div id="idx-main-info" class="clearfix">';
    if ($photos[0]['Uri300']) {
      $content .= '<div class="idx-main-img"><a class="idx-photo" rel="idx-photos" href="' . $photos[0]['Uri800'] . '"><img src="' . $photos[0]['Uri300'] . '"></a></div>';
      unset($photos[0]);
    } else {
      $content .= '<div class="idx-main-img"><img src="' . FLEXIDXHS_URL . '/images/photo-na.png"></div>';
    }

    $main_fields = array('Price' => 'ListPrice', 'Bedrooms' => 'BedsTotal', 'Bathrooms' => 'BathsTotal', 'Year Built' => 'YearBuilt');
    $fields = $this->_custom_fields_get($data['raw'][0]['CustomFields'], array('Original List Price', 'Approx SqFt Range', 'Taxes', 'Subdivision'));

    if ($fields['Original List Price']) {

      $op = $fields['Original List Price'];
      $lp = intval($data['raw'][0]['StandardFields']['ListPrice']);

      if ($op > $lp) {
        $fields['Price Reduced'] = '$' . number_format($op - $lp);
      } elseif ($op < $lp) {
        $fields['Price Increased'] = '$' . number_format($lp - $op);
      }
      unset($fields['Original List Price']);
    }

    $fields['Taxes'] = ($fields['Taxes']) ? '$' . number_format($fields['Taxes']) : false;
    $fields['Subdivision'] = ($fields['Subdivision']) ? ucwords(strtolower($fields['Subdivision'])) : false;
    array_filter($fields);

    $content .= '<div class="idx-main-details">';
    foreach ($main_fields as $label => $field) {
      $content .= '<div class="idx-' . $field . '"><span class="label">' . $label . ':</span>' . $data['validated'][0]['StandardFields'][$field] . '</div>';
    }
    if ($fields)
      foreach ($fields as $label => $value) {
        $content .= '<div><span class="label">' . $label . ':</span>' . $value . '</div>';
      }

    //Listing Contact Buttons
    $content .= $this->idx_listing_contact();

    $content .= '</div>';
    $content .= '</div>'; //idx-main-info end
    if (!empty($photos)) {
      $content .= '<div class="idx-addl-imgs">';

      foreach ($photos as $img) {
        $content .= '<a class="idx-photo" rel="idx-photos" href="' . $img['Uri800'] . '"><img src="' . $img['UriThumb'] . '"></a>';
      }
      $content .= '</div>';
    }
    $content .= '<div class="idx-public-remarks clearfix">' . $this->_normalize_remarks($data['validated'][0]['StandardFields']['PublicRemarks']) . '</div>';

    foreach ($data['validated'][0]['StandardFields'] as $k => $v) {
      if (!is_array($v)) {
        $output1 .= "<li><strong>$k:</strong> $v</li>";
      }
    }

    $content .= '<h2><span>Property Details and Features</span></h2>';
    $content .= '<table class="idx-details-table">' . $this->custom_fields_output($data['validated'][0]['CustomFields']) . '</table>';

    $content .= '</div>'; //END property_details_tab

//TODO: Rather use native GMap API v3 to detect if street view and 45 degree views are available.
    $content .= '<div id="property_map_tab">';

    $content .= '<div id="property-map"><h2><span>Property Map</span></h2><iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=' . str_replace('#', ' Unit', $this->listing_address($data['validated'])) . '&amp;ie=UTF8&amp;hl=en&amp;l4=' . $data['validated'][0]['StandardFields']['Latitude'] . ',' . $data['validated'][0]['StandardFields']['Longitude'] . '&amp;output=embed"></iframe></div>';
    $content .= '<div id="birds-eye-view"><h2><span>Bird\'s Eye View</span></h2><iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"  src="http://dev.virtualearth.net/embeddedMap/v1/ajax/Birdseye?center=' . $data['validated'][0]['StandardFields']['Latitude'] . '_' . $data['validated'][0]['StandardFields']['Longitude'] . '&pushpins=' . $data['validated'][0]['StandardFields']['Latitude'] . '_' . $data['validated'][0]['StandardFields']['Longitude'] . '"></iframe></div>';

    $content .= '</div>'; //END property_map_tab

    $content .= apply_filters('idx-listing-tabs-content', $data);

    $content .= '</div>'; //End Tabs

    $content .= $back2results;

    return $content;
  }

  //Listing contact form(s)
  function idx_listing_contact() {
    global $flexidxhs_opt;

    if($flexidxhs_opt['idx']['form1-shortcode'] && $flexidxhs_opt['idx']['form1-button']){
      /*
      $dimensions = "height=450&width=600";
      if($flexidxhs_opt['idx']['form1-dimensions']){
        $wh = explode('x', $flexidxhs_opt['idx']['form1-dimensions']);
        $dimensions = "height={$wh[1]}&width={$wh[0]}";
      }
       *
       */

      $title = $flexidxhs_opt['idx']['form1-button'];
      $form = html_entity_decode($flexidxhs_opt['idx']['form1-shortcode']);

      $contact .= '<div class="idx-contact-listing"><a href="javascript:;" class="more-info-btn" title="' . $title . '">' . $title . '</a></div>';
      $contact .= '<div style="display: none"><div id="form1"><h2><span>' . $title . '</span></h2>' . $form . '</div></div>';

    }

    if($flexidxhs_opt['idx']['form2-shortcode'] && $flexidxhs_opt['idx']['form2-button']){
      /*
      $dimensions = "height=450&width=600";
      if($flexidxhs_opt['idx']['form2-dimensions']){
        $wh = explode('x', $flexidxhs_opt['idx']['form2-dimensions']);
        $dimensions = "height={$wh[1]}&width={$wh[0]}";
      }
      */
      $title = $flexidxhs_opt['idx']['form2-button'];
      $form = html_entity_decode($flexidxhs_opt['idx']['form2-shortcode']);

      $contact .= '<div class="idx-contact-listing"><a href="javascript:;" class="showing-btn" title="' . $title . '">' . $title . '</a></div>';
      $contact .= '<div style="display: none"><div id="form2"><h2><span>' . $title . '</span></h2>' . $form . '</div></div>';

    }

    $contact = apply_filters('idx_listing_contact', $contact, 1);

    return $contact;
  }

  /*
   * Restructures and outputs custom fields from the api response data object
   */
  private function custom_fields_output(&$custom_fields) {
    if (!is_array($custom_fields))
      return;

    foreach ($custom_fields as $name => $section) {
      if (is_numeric($name)) {
        $output .= $this->custom_fields_output($section);
      } elseif (is_array($section)) {
        $output .= '<tr><th colspan="2">' . $name . '</th></tr>';
        if (is_array($section)) {
          $output .= $this->custom_fields_output($section);
        } else {
          $output .= '<tr><th colspan="2">' . $section . '</th></tr>';
        }
      } else {
        $output .= '<tr><td class="idx-listing-custom-filed-name">' . $name . '</td><td>' . $section . '</td></tr>';
      }
    }
    return $output;
  }

  //@param $key => array
  function _custom_fields_get(&$custom_fields, $keys, $return = array()) {
    foreach ($custom_fields as $name => $section) {
      if (is_array($section)) {
        $return = $this->_custom_fields_get($section, $keys, $return);
      } elseif (in_array($name, $keys) && !is_array($section)) {
        $return[$name] = $section;
      }
    }
    return $return;
  }

  function _validate_fields(&$fields) {
    //field key => normalization format
    $validate = array(
        'Subdivision' => 'string',
        'ListPrice' => 'money',
        'Original List Price' => 'money',
        'Taxes' => 'money',
        'Approx Lot SqFt' => 'number',
    );
    foreach ($fields as $name => $value) {

      if (is_array($value)) {

        $fields[$name] = $this->_validate_fields($value);
      } elseif ($validate[$name]) {

        $fields[$name] = $this->_normalize($value, $validate[$name]);
      }
    }
    return $fields;
  }

  function _restructure_results_array($result) {

    //Rearraging hierarchy of the custom fields array
    if ($result[0]['CustomFields']) {
      $result[0]['CustomFields'][0]['Main Details'] = $result[0]['CustomFields'][0]['Main'];
      unset($result[0]['CustomFields'][0]['Main']);
      if ($result[0]['CustomFields'][0]['Details']) {
        foreach ($result[0]['CustomFields'][0]['Details'] as $n => $section) {
          if (is_array($section)) {
            $str = array();
            foreach ($section as $name => $items) {
              foreach ($items as $item) {
                foreach ($item as $title => $v) {
                  if ($v != '0' && $v !== 'N') {
                    if ($v == 1) {
                      $str[] = $title;
                    } else {
                      $str[] = $title . ': ' . $v;
                    }
                  }
                }
              }
              if (!empty($str))
                $details[][$name] = implode('; ', $str);
            }
          }
        }
        unset($result[0]['CustomFields'][0]['Details']);
        $result[0]['CustomFields'][0]['Miscellaneous Details'] = $details;
      }
    }

    return $result;
  }

  function listing_address($result) {
    if ($result[0]['StandardFields']) {
      $address = "{$result[0]['StandardFields']['StreetNumber']} {$result[0]['StandardFields']['StreetDirPrefix']} {$result[0]['StandardFields']['StreetName']}, {$result[0]['StandardFields']['City']}, {$result[0]['StandardFields']['StateOrProvince']} {$result[0]['StandardFields']['PostalCode']}";
    } elseif ($result['StandardFields']) {
      $address = "{$result['StandardFields']['StreetNumber']} {$result['StandardFields']['StreetDirPrefix']} {$result['StandardFields']['StreetName']}, {$result['StandardFields']['City']}, {$result['StandardFields']['StateOrProvince']} {$result['StandardFields']['PostalCode']}";
    }

    return $address;
  }

  function slug($data, $type) {
    switch ($type) {
      case 'listing':
        $string = str_replace(',', '', $this->listing_address($data));
        $string = str_replace('.', '', $string);
        return 'mls-' . $data['StandardFields']['ListingId'] . '-' . preg_replace('/[^a-z0-9]/i', '_', urlencode(strtolower($string))) . '/';
    }
  }

  function uri($data, $type) {
    return '/' . $this->idx_slug . '/' . $this->slug($data, $type);
  }

  function _normalize_remarks($remarks) {
    //Check if words are upper case. If so, we belive the rest of the text as well.
    $words = str_word_count($remarks, 1);
    if (count($words) < 1)
      return;

    $upper = 0;
    foreach ($words as $word) {
      if (ctype_upper($word)) {
        $upper++;
      }
    }
    //percentage of upper case words

    $x = $upper * 100 / count($words);
    //if upper case >30%
    if ($x > 30) {
      $remarks = $this->_sentence_case(strtolower($remarks));
    }
    $remarks = str_replace('â€™', "'", $remarks);
    return $remarks;
  }

  function _normalize($data, $format) {
    switch ($format) {
      case 'string':
        return ucwords(strtolower($data));
        break;
      case 'money':
        return '$' . number_format($data);
        break;
      case 'number':
        return number_format($data);
        break;
      case 'sentence':
        return $this->_sentence_case($data);
      case 'paragraph':
        return $this->_normalize_remarks($data);
    }
  }

  function _sentence_case($string) {
    $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $new_string = '';
    foreach ($sentences as $key => $sentence) {
      $new_string .= ( $key & 1) == 0 ?
              ucfirst(strtolower(trim($sentence))) :
              $sentence . ' ';
    }
    return trim($new_string);
  }

  function idx_load_scripts() {

    //Do not load scripts in the Admin Dashboard
    if(is_admin())
      return;

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');

    $jquery_tabs_css = FLEXIDXHS_CSS . '/ui.tabs.css';
    wp_enqueue_style('jquery-ui-tabs', $jquery_tabs_css, false, false, false);

    $colorbox = FLEXIDXHS_URL . '/lib/colorbox/jquery.colorbox-min.js';
    wp_enqueue_script('colorbox', $colorbox, array('jquery'), false, false);
    $colorbox_css = FLEXIDXHS_URL . '/lib/colorbox/colorbox.css';
    wp_enqueue_style('colorbox', $colorbox_css, false, false, false);

    $google_jsapi = 'https://www.google.com/jsapi';
    wp_enqueue_script('jsapi', $google_jsapi, FALSE, false, false);
    //try to enqueue styles as well
    $js = FLEXIDXHS_URL . '/js/scripts.js';
    wp_enqueue_script('idx-scripts', $js, FALSE, false, false);
    // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
    wp_localize_script('idx-scripts', 'idxAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
  }

  function saved_search_shortcode($atts){
    extract($atts);
    if($id)
      $custom_filter = "PropertyType Eq 'A' And SavedSearch Eq '{$id}'";
      return $this->idx_search_results($custom_filter);

  }

}

?>