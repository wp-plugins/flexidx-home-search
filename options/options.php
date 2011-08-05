<?php
if(!class_exists('Plugin_Admin_Class')){
	require_once FLEXIDXHS_LIB . '/wp_plugin_admin.php';
}

class flexIDXHS_Admin extends Plugin_Admin_Class {
    var $hook		= 'flexidxhs';
    var $longname	= 'flexMLS IDX Options';
    var $shortname	= 'flexIDX Plugin';
    var $filename	= 'flexidx-home-search/flexIDXHS.php';
    var $optionname	= 'flexidxhs';
    var $menu		= true;
    var $prefix		= 'flex_';

    function settings($key = null){

        $settings = array(

            'flexmls IDX URL'       => array(
                array(
                    'label'		=> __('flexmls® IDX URL'),
                    'desc'		=> __('You should be able to find it in your ARMLS account, under Preferences => IDX Manager.'),
                    'id'		=> 'idx-url',
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                ),
            ),


            'Label Visibility'      => array(
                array(
                    'label'		=> __('Outside the fields'),
                    'id'		=> 'label-visibility',
                    'default'           => 'outside',
                    'type'		=> 'radio',
                ),
                array(
                    'label'		=> __('Inside the fields, as first value'),
                    'id'		=> 'label-visibility',
                    'default'           => 'inside',
                    'type'		=> 'radio',
                    'attr'              => array('checked' => 'checked'),
                ),
                 array(
                    'label'		=> __('Do not display labels'),
                    'id'		=> 'label-visibility',
                    'default'           => 'none',
                    'type'		=> 'radio',
                ),
            ),


            'Label Titles'          => array(
                array(
                    'label'		=> __('City'),
                    'id'		=> array('label-names','city'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Select City',
                ),
                array(
                    'label'		=> __('Property Type'),
                    'id'		=> array('label-names', 'property-type'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Property Type',
                ),
                array(
                    'label'		=> __('Min. Price'),
                    'id'		=> array('label-names', 'min-price'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Min Price',
                ),
                array(
                    'label'		=> __('Max. Price'),
                    'id'		=> array('label-names', 'max-price'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Max Price',
                ),
                array(
                    'label'		=> __('Price Range'),
                    'id'		=> array('label-names', 'price-range'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Select Price Range',
                ),
                array(
                    'label'		=> __('Bedrooms'),
                    'id'		=> array('label-names', 'bedrooms'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Beds?',
                ),
                array(
                    'label'		=> __('Bathrooms'),
                    'id'		=> array('label-names', 'bathrooms'),
                    'type'		=> 'text',
                    'attr'              => array('size' => 40),
                    'default'           => 'Baths?',
                ),
            ),


            'Price Fields'          => array(
                array(
                    'label'		=> __('Separate fields'),
                    'desc'		=> __('2 separate fields for minimum and maximum prices.'),
                    'id'		=> 'price-fields',
                    'default'           => 'min-max',
                    'type'		=> 'radio',
                    'attr'              => array('checked' => 'checked'),
                ),
                array(
                    'label'		=> __('Price range list'),
                    'desc'		=> __('Single selection list with pre-set price ranges.'),
                    'id'		=> 'price-fields',
                    'default'           => 'price-range',
                    'type'		=> 'radio',
                ),
                array(
                    'label'		=> __('Set Minimum Price to'),
                    'id'		=> 'set-min-price',
                    'type'		=> 'select',
                    'default'           => '50000',
                    'options'       => array(
                        '0'         => 'No Min Price',
                        '50000'     => '$50,000',
                        '100000'    => '$100,000',
                        '150000'    => '$150,000',
                        '200000'    => '$200,000',
                        '250000'    => '$250,000',
                        '300000'    => '$300,000',
                        '350000'    => '$350,000',
                        '400000'    => '$400,000',
                        '450000'    => '$450,000',
                        '500000'    => '$500,000',
                    ),
                ),
            ),

            'Note'                      => array(
                array(
                      'type'            => 'html',
                      'default'         => '<p><em>Note: If you have active widgets with custom searches, you will need to update them to match the price field type you select here. For example, if you used price range field in a custom widget, and now decided to switch to separate price fields - you need to do the same for the custom search widgets.</em></p>',
                ),
            ),

            'Search Buttons'            => array(
                array(
                    'id'                => array('search-buttons', 'search-label'),
                    'label'             => 'Search Label',
                    'type'              => 'text',
                    'default'           => 'Search Now',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('search-buttons', 'advanced-search-label'),
                    'label'             => 'Advanced Search Label',
                    'type'              => 'text',
                    'default'           => 'Advanced Search',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('search-buttons', 'display-advanced-search'),
                    'label'             => 'Display Advanced Search Button',
                    'type'              => 'checkbox',
                ),
            ),

            'Shortcode Settings'        => array(
                array(
                    'id'                => array('shortcodes', 'full-screen-link'),
                    'label'             => 'Display "Full Screen" iFrame Link',
                    'desc'              => 'This link opens iframe in new window in full screen.',
                    'type'              => 'checkbox',
                    'attr'              => array('checked' => 'checked'),
                ),
                array(
                    'id'                => array('shortcodes', 'link-anchor'),
                    'label'             => 'Link Anchor Text',
                    'type'              => 'text',
                    'default'           => 'View in Full Screen',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('shortcodes', 'link-title'),
                    'label'             => 'Link Title',
                    'type'              => 'text',
                    'default'           => 'Open Search Results in Full Screen',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('shortcodes', 'iframe-width'),
                    'label'             => 'iFrame Width',
                    'type'              => 'text',
                    'default'           => '100%',
                    'desc'              => 'Default width parameter.',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('shortcodes', 'iframe-height'),
                    'label'             => 'iFrame Height',
                    'type'              => 'text',
                    'default'           => '800px',
                    'desc'              => 'Default height parameter.',
                    'attr'              => array('size' => 40),
                ),
            ),

            'Cities in Your Area'       => array(
                array(
                      'type'            => 'html',
                      'default'         => '<p>One city name per line - enter all the cities that you would like your visitors to be able to search.</p>',
                ),
                array(
                    'id'		=> 'city-list',
                    'default'           => array('Apache Junction', 'Avondale', 'Carefree', 'Cave Creek', 'Chandler', 'El Mirage', 'Fountain Hills', 'Gilbert', 'Glendale', 'Goodyear', 'Laveen', 'Litchfield Park', 'Mesa', 'Paradise Valley', 'Peoria', 'Phoenix', 'Queen Creek', 'Rio Verde', 'Scottsdale', 'Sun City', 'Sun City West', 'Surprise', 'Tempe', 'Tolleson'),
                    'type'		=> 'textarea',
                    'attr'              => array(
                        'cols'  => 20,
                        'rows'  => 10,
                        'style' => 'width: 100%',
                    ),
                    'output_callback'   => '_selectionlist_to_text',
                    'input_callback'    => '_selectionlist_to_array',
                ),

            ),
            'Custom Searches'           => array(
                array(
                    'type'              => 'html',
                    'default'           => '<p>The custom searches can be used as additional options in the Quick Search widget. They will be presented as selection list field types. You can have multiple fields and each field can have multiple searches.</p>
                                        <p><strong>Syntax:</strong></p>
                                        <code>
                                            #Field Name Label 1<br />
                                            Name of the search 1 - Search URL<br />
                                            Name of the search 2 - Search URL
                                        </code>
                                        <p>Result of the code above:</p>
                                        <p>
                                            <label>Field Name Label 1:</label> <select><option>Name of the search 1</option><option>Name of the search 2</option></select>
                                        </p><hr />',
                ),
                array(
                    'type'              => 'html',
                    'default'           => '<p>Enter your custom searches:</p>',
                ),
                array(
                    'id'		=> 'custom-searches',
                    'desc'              => 'Make sure you use the correct syntax to add custom searches, otherwise your widget will be broken.',
                    'type'		=> 'textarea',
                    'attr'          => array(
                        'cols'  => 20,
                        'rows'  => 10,
                        'style' => 'width: 100%',
                    ),
                    'output_callback'   => '_custom_searches_to_text',
                    'input_callback'    => '_custom_searches_to_array',
                ),
            ),
            'Additional Widget Markup'  => array(
                array(
                    'id'                => array('widget-markup', 'before-title'),
                    'label'             => __('Before Title'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('widget-markup', 'after-title'),
                    'label'             => __('After Title'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('widget-markup', 'before-widget'),
                    'label'             => __('Before Widget'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('widget-markup', 'after-widget'),
                    'label'             => __('After Widget'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
            ),
            'FlexMLS Field Names'  => array(
                array(
                    'id'                => array('field-names', 'city'),
                    'label'             => __('City'),
                    'default'           => 'city',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('field-names', 'property-type'),
                    'label'             => __('Property Type'),
                    'default'           => 'DwellingType',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('field-names', 'property-type-values'),
                    'label'             => __('Property Type Field Values'),
                    'default'           => array(
                        'SF,PH'         => 'Single Family Homes',
                        'TH,AF'         => 'Condos/Townhomes',
                        'LS'            => 'Loft Style',
                    ),
                    'type'              => 'textarea',
                    'attr'          => array(
                        'cols'  => 20,
                        'rows'  => 10,
                        'style' => 'width: 100%',
                    ),
                    'desc'              => 'Values have to be entered in this form "key - label" for example: SF - Single Family Homes. If you don\'t know the keys for your IDX - contact flexmls.com',
                    'output_callback'   => '_selectionlist_to_text',
                    'input_callback'    => '_selectionlist_to_array',
                ),
            ),
            'flexIDX API Keys'   => array(
                array(
                    'id'                => array('idx', 'api_key'),
                    'label'             => __('flexIDX API Key'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
                array(
                    'id'                => array('idx', 'api_secret'),
                    'label'             => __('flexIDX API Secret'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                ),
            ),
            'IDX Search Results Page'   => array(
                array(
                    'id'                => array('idx', 'result-page'),
                    'label'             => __('IDX Search Results Page'),
                    'type'              => 'select',
                    'options'           => _pages_array(),
                ),
            ),
            'Note 2'                    => array(
                array(
                        'type'              => 'html',
                        'default'           => '<p>The call to action buttons and lead generation form will appear on the listing details pages. Enter <strong>shortcodes</strong> for each form you would like to use.</p>',
                ),
            ),
            'Calls to Action & Lead Generation Forms'   => array(
                array(
                    'id'                => array('idx', 'form1-button'),
                    'label'             => __('Form 1 call to action'),
                    'default'           => 'Ask Agent a Question',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Title of the call to action button.',
                ),
                array(
                    'id'                => array('idx', 'form1-shortcode'),
                    'label'             => __('Form 1 Shortcode'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Enter the shortcode of the form you want to use. If you wish to omit this form, leave the field blank.',
                ),
                array(
                    'id'                => array('idx', 'form2-button'),
                    'label'             => __('Form 2 call to action'),
                    'default'           => 'Schedule a Showing',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Title of the call to action button.',
                ),
                array(
                    'id'                => array('idx', 'form2-shortcode'),
                    'label'             => __('Form 2 Shortcode'),
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Enter the shortcode of the form you want to use. If you wish to omit this form, leave the field blank.',
                ),

                //Dimensions are no longer needed since ColorBox adjusts automatically according to the dimensions of the content.
                /*
                array(
                    'id'                => array('idx', 'form1-dimensions'),
                    'label'             => __('Form 1 Width and Height'),
                    'default'           => '600x450',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Syntax: width by height in pixels separated by "x" without any spaces. Example: 600x450.',
                ),
                array(
                    'id'                => array('idx', 'form2-dimensions'),
                    'label'             => __('Form 2 Width and Height'),
                    'default'           => '600x450',
                    'type'              => 'text',
                    'attr'              => array('size' => 40),
                    'desc'              => 'Syntax: width by height in pixels separated by "x" without any spaces. Example: 600x450.',
                ),
                 *
                 */
            ),
            'Colors'                    => array(
                array(
                    'type'              => 'html',
                    'default'           => _color_options(),
                ),
                array(
                    'id'                => array('color', 'tab_bg_color'),
                    'label'             => __('Tabs Background'),
                    'default'           => '#EFEFEF',
                    'type'              => 'text',
                    'desc'              => '<div id="tab_bg_color_picker"></div>',
                ),
                array(
                    'id'                => array('color', 'tab_brdr_color'),
                    'label'             => __('Tabs Border'),
                    'default'           => '#CCCCCC',
                    'type'              => 'text',
                    'desc'              => '<div id="tab_brdr_color_picker"></div>',
                ),
                array(
                    'id'                => array('color', 'tab_bghover_color'),
                    'label'             => __('Tabs Hover Background'),
                    'default'           => '#DDDDDD',
                    'type'              => 'text',
                    'desc'              => '<div id="tab_bghover_color_picker"></div>',
                ),
                array(
                    'id'                => array('color', 'row_bg_color'),
                    'label'             => __('Table Rows Background'),
                    'default'           => '#EFEFEF',
                    'type'              => 'text',
                    'desc'              => '<div id="row_bg_color_picker"></div>',
                ),
                array(
                    'id'                => array('color', 'btn_bg_color'),
                    'label'             => __('Button Background'),
                    'default'           => '#DDDDDD',
                    'type'              => 'text',
                    'desc'              => '<div id="btn_bg_color_picker"></div>',
                ),
            ),
        );

        //Adding Simple Real Estate Plugin option if its activated.
        if(function_exists('srp_profile')){
          $srp_option = array(
           'SREP Detected'                => array(
                array(
                      'type'      => 'html',
                      'default'   => '<p><strong>Attention:</strong> Simple Real Estate Plugin has been detected. If you wish to add mortgage calculator and a Walk Score&reg; widget to the listing details, check the box below.</p>',
                ),
            ),
            'Additional Information' => array(
                array(
                  'id'                => array('idx', 'display-srp-tabs'),
                  'label'             => 'Display additional widgets from Simple Real Estate Pack',
                  'type'              => 'checkbox',
              ),
           ),
          );
          $settings = array_merge($settings, $srp_option);
        }

        if($key){
            return $settings[$key];
        }
        //settings have to return a regular array of fields - no section
        foreach($settings as $section => $fields){
            foreach($fields as $field){
                $settings_array[] = $field;
            }
        }
        return $settings_array;
    }

    function config_page(){

        //Generate columns on the page
       $this->add_column(1, '70%');
       $this->add_column(2, '70%');
       $this->add_column(3, '70%');


        $this->add_box('flexmls IDX URL', $this->settings('flexmls IDX URL'), 1);
        $this->add_box('Label Visibility', $this->settings('Label Visibility'), 1);
        $this->add_box('Label Titles', $this->settings('Label Titles'), 1);
        $this->add_box('Price Fields', $this->fields($this->settings('Price Fields')) . $this->auto_form($this->settings('Note'), false), 1);


        $this->add_box('Cities in Your Area', $this->auto_form($this->settings('Cities in Your Area'), false), 1);
        $this->add_box('Widget Search Buttons', $this->settings('Search Buttons'), 1);
        $this->add_box('Shortcode Settings', $this->settings('Shortcode Settings'), 1);

        $this->add_box('Custom Searches', $this->auto_form($this->settings('Custom Searches'), false), 2);
        $this->add_box('Additional Widget Markup', $this->settings('Additional Widget Markup'), 2);
        $this->add_box('FlexMLS Field Names', $this->settings('FlexMLS Field Names'), 2);

        $this->add_box('flexIDX API Keys', $this->settings('flexIDX API Keys'), 3);
        $this->add_box('IDX Search Results Page', $this->settings('IDX Search Results Page'), 3);
        $this->add_box('Calls to Action & Lead Generation Forms', $this->auto_form($this->settings('Note 2'), false) . $this->fields($this->settings('Calls to Action & Lead Generation Forms')), 3);
        $this->add_box('Colors', $this->settings('Colors'), 3);

        if(function_exists('srp_profile')){
          $this->add_box('Third Party Widgets', $this->settings('Additional Information'), 3);
        }

        //Add Columns to Tabs
        $this->add_tab('General Settings', array(1));
        $this->add_tab('Advanced Settings', array(2));
        $this->add_tab('IDX Settings', array(3));

        //Generate Config Page
        $this->_config_page_template();
    }

  /*
    function validate_input($input){
        return flexIDXHS_options_validate($input);
    }
    */
}

new flexIDXHS_Admin();

//Misc helper functions
function _array_to_text($array){
    if(!is_array($array))
        return;

        array_filter($array);
        foreach($array as $value){
                $output .= $value."\n";
        }
        return $output;
}

function _text_to_array($text){
    if(!is_string($text))
        return;

    $text =  trim($text);
    $text = str_replace(" \n", "", $text);
    $array = explode("\n", $text);
    array_filter($array);
    if(is_array($array) && !empty($array)){
        foreach($array as $k=>$v){
            if(!empty($array[$k]) && $array[$k] != NULL && strlen($array[$k])>1){
                $output[] = trim($v);
            }
            //$output = array_filter($output);
        }

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

function _selectionlist_to_array($text){
    $text =  trim($text);
    $text = str_replace(" \n", "", $text);
    $array = explode("\n", $text);
    array_filter($array);
    if(is_array($array) && !empty($array)){
        foreach($array as $k=>$v){
            $match = null;
            if(!empty($array[$k]) && $array[$k] != NULL && strlen($array[$k])>1){
                $match = explode(" - ", trim($v));
                if($match[1]){
                    $output["{$match[0]}"] = $match[1];
                }else{
                    $output[] = $match[0];
                }
            }
        }
        array_filter($output);
        return $output;
    }
}

function _selectionlist_to_text($array){
    if(!is_array($array))
        return;


    foreach($array as $k => $v){
        if(is_string($k)){
            $output .= $k . ' - ' . $v . "\n";
        }else{
            $output .= $v . "\n";
        }
    }

    return $output;
}

function _pages_array(){
    $pages = get_pages();
    foreach($pages as $page){
        $return[$page->ID] = $page->post_title;
    }
    return $return;
}

function _color_options(){
  $html = <<<HTML
<script>
  jQuery(document).ready(function() {
    jQuery("#tab_bg_color_picker").farbtastic("#color-tab_bg_color");
    jQuery("#tab_brdr_color_picker").farbtastic("#color-tab_brdr_color");
    jQuery("#tab_bghover_color_picker").farbtastic("#color-tab_bghover_color");
    jQuery("#row_bg_color_picker").farbtastic("#color-row_bg_color");
    jQuery("#btn_bg_color_picker").farbtastic("#color-btn_bg_color");
  });
</script>
HTML;
  return $html;
}
?>