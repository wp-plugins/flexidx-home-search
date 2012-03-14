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
                array(
                    'label'     => __('Quick Search Results Page'),
                    'desc'      => __('Select the page that will be used to display an iFrame with the seach results.'),
                    'id'        => 'iframe',
                    'type'      => 'select',
                    'options'   => _pages_array(),
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
                                        <p>Result example of the code above:<br/>
                                            <label>Field Name Label 1:</label> <select><option>Name of the search 1</option><option>Name of the search 2</option></select>
                                        </p>',
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
        );

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
       $this->add_column(1, '35%');
       $this->add_column(2, '35%');
       $this->add_column(3, '35%');
       $this->add_column(4, '35%');
       
        $this->add_box('flexmls IDX URL', $this->settings('flexmls IDX URL'), 1);
        $this->add_box('Label Visibility', $this->settings('Label Visibility'), 1);
        $this->add_box('Label Titles', $this->settings('Label Titles'), 1);
        $this->add_box('Price Fields', $this->fields($this->settings('Price Fields')) . $this->auto_form($this->settings('Note'), false), 1);


        $this->add_box('Cities in Your Area', $this->auto_form($this->settings('Cities in Your Area'), false), 2);
        $this->add_box('Search Buttons', $this->settings('Search Buttons'), 2);
        $this->add_box('Shortcode Settings', $this->settings('Shortcode Settings'), 2);
        
        $this->add_box('Custom Searches', $this->auto_form($this->settings('Custom Searches'), false), 3);
        $this->add_box('Additional Widget Markup', $this->settings('Additional Widget Markup'), 3);
        $this->add_box('FlexMLS Field Names', $this->settings('FlexMLS Field Names'), 3);


        //Add Columns to Tabs
        $this->add_tab('General Settings', array(1, 2));
        $this->add_tab('Advanced Settings', array(3, 4));

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
    $return[0] = ' - New window - no iFrame - ';
    foreach($pages as $page){        
        $return[$page->ID] = $page->post_title;
    }
    return $return;
}