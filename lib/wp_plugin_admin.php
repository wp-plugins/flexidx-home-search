<?php
/*
*  WordPress Plugin Admin Class
** Version 0.2.1
** Author: Max Chirkov
** Based on work of: Joost de Valk (Yoast Plugin Admin), Ian Stewart (Thematic Theme Options)
*/

/*
** Options function convention: prefix_func_options_page()
** options page files in case we wante to split the code:
** func.php - don't forget to declare $prefix (like pref_) as class variable
*/

if(!class_exists('Plugin_Admin_Class')){
	class Plugin_Admin_Class {

		var $hook 		= '';
		var $filename		= '';
		var $longname		= '';
		var $shortname		= '';
		var $ozhicon		= '';
		var $optionname		= '';
		var $accesslevel	= 'manage_options';
		var $prefix			= '';
		var $submenu_pages	= array(); //key - function; value - page title;
                var $autotable          = true;
                var $options            = null;

                var $credits = array(
                    'download_url'  => 'http://wordpress.org/extend/plugins/flexidx-home-search/', //plugin page on wp.org
                    'official_url'  => 'http://www.phoenixhomes.com/tech/flexidx-home-search', //plugin page on author's website
                    'author_url'    => 'http://wordpress.org/extend/plugins/profile/maxchirkov',
                    'sponsored_by'  => '<a href="http://www.phoenixhomes.com">PhoenixHomes.com</a>',
                    'forums_url'    => 'http://wordpress.org/tags/flexidx-home-search?forum_id=10',
                );

		function __construct() {
                    //register_activation_hook( __FILE__, array(&$this, 'plugin_activate') );
                    add_action('admin_init', array(&$this,'options_init'));
                    if(!empty($this->submenu_pages)){
                            add_action('admin_menu', array(&$this, 'register_menu'));
                    }else{
                            add_action('admin_menu', array(&$this, 'register_settings_page'));
                            add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2);
                    }
                    add_filter('ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon'));

                    add_action('admin_print_scripts', array(&$this,'config_page_scripts'));
                    add_action('admin_print_styles', array(&$this,'config_page_styles'));
                    add_action('admin_head', array(&$this, 'admin_head'));

                    //add_action('wp_dashboard_setup', array(&$this,'widget_setup'));
                    $this->options = get_option($this->optionname);
		}

                function plugin_activate(){
                    if(!$this->options){
                        $settings = $this->settings();

                        if(is_array($settings)){
                            foreach($settings as $field){
                                $default = false;
                                if(in_array($field['type'], array('text', 'textarea', 'select'))){
                                    if( isset($field['default']) ){
                                        $default = $field['default'];
                                    }
                                }elseif( isset($field['type']) && $field['type'] == 'checkbox'){
                                    if( isset($field['attr']['checked']) ){
                                        if( isset($field['default']) ){
                                           $default = true;
                                        }
                                    }
                                }elseif( isset($field['attr']['checked']) && $field['default']){
                                    $default = $field['default'];
                                }

                                if($default){
                                    if(is_array($field['id'])){
                                       $opt[$field['id'][0]][$field['id'][1]] = $default;
                                    }else{
                                        $opt[$field['id']] = $default;
                                    }
                                }
                            }
                        }

                        add_option($this->optionname, $opt);
                        $this->options = get_option($this->optionname);
                    }
                }

		function add_ozh_adminmenu_icon( $hook ) {
			if ($hook == $this->hook)
				return WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname($filename)). '/'.$this->ozhicon;
			return $hook;
		}

		/**
		 * Config Page Scripts
		 */
		function config_page_styles() {
			if (isset($_GET['page']) && ($_GET['page'] == $this->filename || $_GET['page'] == $this->hook)) {
				wp_enqueue_style('dashboard');
				wp_enqueue_style('thickbox');
				wp_enqueue_style('global');
				wp_enqueue_style('wp-admin');
				wp_enqueue_style('blogicons-admin-css', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/wp_plugin_admin.css');
			}
		}

		function config_page_scripts() {
			if (isset($_GET['page']) && ($_GET['page'] == $this->filename || $_GET['page'] == $this->hook)) {
                            wp_enqueue_script('jquery');
                            wp_enqueue_script('jquery-ui');
                            wp_enqueue_script('jquery-ui-core');
                            wp_enqueue_script('jquery-ui-tabs');
                            wp_enqueue_script('jquery-ui-sortable');
                            wp_enqueue_script('postbox');
                            wp_enqueue_script('dashboard');
                            wp_enqueue_script('thickbox');
                            wp_enqueue_script('media-upload');
			}
		}

                function admin_head(){
                    //this condition is important, otherwise, if loads on other pages - breaks collapsible sidebar navigation.
                    if (isset($_GET['page']) && ($_GET['page'] == $this->filename || $_GET['page'] == $this->hook)) {
                        //echo '<script type="text/javascript" src="../wp-includes/js/jquery/ui.sortable.js"></script>';
                        echo '<link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/latest/themes/base/jquery.ui.all.css" rel="stylesheet" />';
                        echo '
                            <script type="text/javascript">
                            jQuery(document).ready(function(){
                                jQuery("#' . $this->hook . '_tabs").tabs();
                            });
                            </script>';
                    }
                }

		/**
		 * Register Settings Page
		 */
		function register_settings_page() {
			add_options_page($this->longname, $this->shortname, $this->accesslevel, $this->hook, array(&$this,'config_page'));
		}

		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}

		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}

		/**
		 * Register Menu
		 */
		function options_init(){
                    register_setting($this->optionname . '-option-group', $this->optionname, array(&$this, 'validate_input'));
		}

		function register_menu(){
			add_menu_page($this->longname, $this->shortname, 8, $this->filename, array(&$this,'show_menu'));
			foreach($this->submenu_pages as $submenu_slug => $submenu_title){
				add_submenu_page($this->filename, $submenu_title, $submenu_title, 8, $submenu_slug, array(&$this,'show_menu'));
			}
		}

		function show_menu(){
			$submenu_slugs = array_keys($this->submenu_pages);
			if($_GET['page'] && in_array($_GET['page'], $submenu_slugs)){
				//submenu slug contains prefix, but submenu files don't, so remove prefix
				$submenu_file_name = str_replace($this->prefix, '', $_GET['page']) . '.php';
				$submenu_file_path = dirname (__FILE__) . '/' . $submenu_file_name;
				//options funtion should be $submenu_slug + _options_page()
				$func = $_GET['page'] . '_options_page';
				if(function_exists($func)){
					call_user_func($func);
				}elseif(file_exists($submenu_file_path)){
					include_once ($submenu_file_path);
					call_user_func($func);
				}
			}else{
				$this->config_page();
			}

		}

		/*
                 * Applies input_callback functions form the settings array as well as encodes html entitites for strings.
                 * This function can be overwritten with custom validation from the class extension.
                 */
                function validate_input($input){
                    //overwrite and do something here
                    $settings = $this->settings();
                    foreach($input as $k1 => $fields){
                        if(is_array($fields)){
                            foreach($fields as $k2 => $field){
                                foreach($settings as $item){
                                    if( isset($item['id']) && $item['id'] == array($k1, $k2) && isset($item['input_callback']) ){
                                        if(is_array( $input[$k1][$k2])){
                                            $input[$k1][$k2] = call_user_func_array($item['input_callback'], $input[$k1][$k2]);
                                        }elseif( isset($item['input_callback']) ){
                                            $input[$k1][$k2] = call_user_func($item['input_callback'], htmlentities($input[$k1][$k2], ENT_QUOTES));
                                        }
                                    }elseif( isset($item['id']) && $item['id'] == array($k1, $k2)){
                                        $input[$k1][$k2] = htmlentities($input[$k1][$k2], ENT_QUOTES);
                                    }
                                }
                            }
                        }else{
                            foreach($settings as $item){
                                if( isset($item['id']) && $item['id'] == $k1 &&  isset($item['input_callback']) ){
                                    if(is_array($input[$k1])){
                                        $input[$k1] = call_user_func_array($item['input_callback'], $input[$k1]);
                                    }elseif( isset($item['input_callback']) ){
                                        $input[$k1] = call_user_func($item['input_callback'], htmlentities($input[$k1], ENT_QUOTES));
                                    }
                                }elseif( isset($item['id']) && $item['id'] == $k1){
                                    $input[$k1] = htmlentities($input[$k1], ENT_QUOTES);
                                }
                            }
                        }
                    }

                    //since tabs have separate forms, we need to murge the incomplete array of new settings into the existing settings array
                    //$input = array_replace_recursive($this->options, $input);
                    $new_input = $this->options;
                    foreach($input as $k => $v){
                        $new_input[$k] = $v;
                    }

                    return $new_input;
                }

                function config_page() {
			echo 'Function <code>config_page()</code> needs to be declaired within the extended Class.';
		}

		/**
		 * Create a Checkbox input field
		 */
		function checkbox($id, $label) {
			$options = $this->options;
			return '<input type="checkbox" id="'.$id.'" name="'.$id.'"'. checked($options[$id],true,false).'/> <label for="'.$id.'">'.$label.'</label><br/>';
		}

		/**
		 * Create a Text input field
		 */
		function textinput($id, $label) {
			$options = $this->options;
			return '<label for="'.$id.'">'.$label.':</label><br/><input size="45" type="text" id="'.$id.'" name="'.$id.'" value="'.$options[$id].'"/><br/><br/>';
		}

                /**
                 * Creates a name for a form field which is in a form of an array optiongroup[option]
                 * @param string $option_id
                 * @return string
                 */
                //TODO: Change option_id to field_params
                function _name($field_params){
                    //$parts = explode('###', str_replace(']', '', str_replace('[', '###', $field_params['id'])));
                    $output = '';
                    if(is_string($field_params['id']))
                        $field_params['id'] = array($field_params['id']);

                    $i=0;
                    if(is_array($field_params['id'])){
                        foreach($field_params['id'] as $part){
                            if($i>0){
                                $output .= "[$part]";
                            }else{
                                $output .= $this->optionname . "[$part]";
                            }
                            $i++;
                        }
                        if(!is_array($output))
                            return $output;
                    }else{
                        if(!is_array($parts))
                            return $parts;
                    }
                }

                /**
                 * Creates an id for a form field reflecting option's hierarchy with hyphans
                 * @param string $option_id
                 * @return string
                 */
                function _id($field_params){
                    if( !isset($field_params['id']) )
                        return;

                    //$output = str_replace(']', '', str_replace('[', '-', $field_params['id']));
                    if( !is_array($field_params['id']) )
                        return $field_params['id'];

                    return implode('-', $field_params['id']);
                }
                /**
                 * Grabs a value from a multidimentional array by a string field id
                 * @param string $option_id
                 * @return mixed
                 */
                function _val($field_params){
                    $options = $this->options;
                    if(!$options)
                        return;

                    //$parts = explode('###', str_replace(']', '', str_replace('[', '###', $field_params['id'])));
                    if( isset($field_params['id']) && is_string($field_params['id'])){
                        $val = isset($options[$field_params['id']]) ? $options[$field_params['id']] : false;

                        if( isset($field_params['output_callback']) )
                            $val = call_user_func($field_params['output_callback'], $val);

                        //pa($val);
                        return $val;
                    }elseif(is_array($field_params['id'])){
                        $val = isset($options[$field_params['id'][0]][$field_params['id'][1]]) ? $options[$field_params['id'][0]][$field_params['id'][1]] : false;

                        if( isset($field_params['output_callback']) )
                            $val = call_user_func($field_params['output_callback'], $val);

                        return $val;
                    }
                }


                function _label($field_params){
                    if(!empty($field_params['label'])){
                        return '<label for="' . $this->_id($field_params['id']) . '">' . $field_params['label'] . ':</label>' . "\n";
                    }else{
                        return '';
                    }
                }


        /**
         * Parces array of field parameters and their options and reterns a string or an array
         * of complete input fields with their values and labels.
         * @param array $field_params
         * @param bool $array
         * @return string|array
         */
		function inputfield($field_params, $array = false) {
			$options = $this->options;
            $attributes = false;
            $name_suffix = false;
			//explode array into additioanal input fields parameters
			if(isset($field_params['attr']) && !empty($field_params['attr'])){
                foreach($field_params['attr'] as $k => $v){
                                    if($k != 'checked'){
					$attributes .= $k . '="' . $v . '" ';
                                        //for selection list
                                        if($k == 'multiple'){
                                            $name_suffix = '[]';
                                        }
                                    }
				}
			}

			$desc =  isset($field_params['desc']) ? "<small class='description'>{$field_params['desc']}</small>\n" : '';

                        switch($field_params['type']){
                            case 'html':
                                $output = array(
                                    $this->_label($field_params),
                                    $field_params['default'] ."\n",
                                );
                                break;
                            case 'text':
                                $output = array(
                                    $this->_label($field_params),
                                    '<input ' . $attributes . 'type="text" id="' . $this->_id($field_params) . '" name="' . $this->_name($field_params) . '" value="' . $this->_val($field_params) . '"/>' . "\n" . $desc
                                );
                                break;
                            case 'textarea':
                                $output = array(
                                    $this->_label($field_params),
                                    '<textarea ' . $attributes . 'id="' . $this->_id($field_params) . '" name="' . $this->_name($field_params) . '">' . $this->_val($field_params) . '</textarea>' . "\n" . $desc
                                );
                                break;
                            case 'radio':
                                $output = array(
                                    $this->_label($field_params),
                                    '<input ' . $attributes . 'type="radio" id="' . $this->_id($field_params) . '" name="' . $this->_name($field_params) . '" value="' . $field_params['default'] . '"' . checked($this->_val($field_params), $field_params['default'], false) . '/>' . "\n" . $desc
                                );
                                break;
                            case 'checkbox':
                                $output = array(
                                    $this->_label($field_params),
                                    '<input ' . $attributes . 'type="checkbox" id="' . $this->_id($field_params) . '" name="' . $this->_name($field_params) . '" ' . checked((bool)$this->_val($field_params), true, false) . '/>' . "\n" . $desc
                                );
                                break;
                            case 'select':
                                $keys = false;
                                if(!empty($field_params['options'])){
                                    if($name_suffix){
                                        if($this->_val($field_params)){
                                            $keys = $this->_val($field_params);
                                        }elseif($field_params['default']){
                                            $keys = $field_params['default'];
                                        }
                                    }else{
                                        if($this->_val($field_params)){
                                            $keys = $this->_val($field_params);
                                        }elseif( isset($field_params['default']) ){
                                            $keys = $field_params['default'];
                                        }
                                    }

                                    $select_options = '';
                                    foreach($field_params['options'] as $key => $value){
                                        $selected = '';
                                        if(is_array($keys)){
                                            if(in_array($key, $keys)){
                                                $selected = ' selected="selected"';
                                            }
                                        }elseif($key == $keys){
                                            $selected = ' selected="selected"';
                                        }

                                        $select_options .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>' . "\n";
                                    }
                                }
                                $output = array(
                                    $this->_label($field_params),
                                    '<select name="' . $this->_name($field_params) . $name_suffix . '" ' . $attributes . '>' . "\n"
                                        .$select_options
                                    .'</select>' . "\n"
                                    . $desc
                                );
                                break;
                        }
			if($array){
                            //retrun array
                            return $output;
			}else{
                            //return string
                            return implode('', $output);
                        }

		}

		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) {
			$content = '<table class="form-table">' . "\n";
			foreach ($rows as $row) {
				$content .= '<tr valign="top"><th scrope="row">' . "\n";
				if (isset($row['id']) && $row['id'] != '')
					$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>' . "\n";
				else
					$content .= $row['label'];
				if (isset($row['desc']) && $row['desc'] != '')
					$content .= '<br/><small>'.$row['desc'].'</small>' . "\n";
				$content .= '</th><td>' . "\n";
				$content .= $row['content'];
				$content .= '</td></tr>' . "\n";
			}
			$content .= '</table>' . "\n";
			return $content;
		}

		/*
		* Automatically generate a table from an array of rows
                * Each value of the @row is a cell
		*/
		function auto_table($rows) {
			$content = '<table class="form-table">' . "\n";
			foreach ($rows as $row) {
                            //Begin row
                            $content .= '<tr valign="top">' . "\n";
				for($i=0; $i<count($row); $i++){
                                    //Begin cells
                                    if($i == 0){
                                            $content .= '<th scrope="row">' . "\n";
                                            $content .= $row[$i];
                                            $content .= '</th>' . "\n";
                                    }else{
                                        $content .= '<td>' . "\n";
                                        $content .= $row[$i];
                                        $content .= '</td>' . "\n";
                                    }
                                    //End cells
				}
                            //End row
                            $content .= '</tr>' . "\n";
			}
			$content .= '</table>' . "\n";
			return $content;
		}

                /**
		 * Renders form fields from options array
                 * @array - if true, returns an array of fields
                 * being used in auto_table
		 */
                function auto_form($option_fields, $array = true){
                    $output = false;
                    if(is_array($option_fields) && !empty($option_fields)){
                        foreach($option_fields as $field_params){
                            if($array){
                                $output[] = $this->inputfield($field_params, $array);
                            }else{
                                $output .= $this->inputfield($field_params, $array);
                            }
                        }
                    }
                    return $output;
                }

                function fields($fields_array){
                    if(!is_array($fields_array))
                        return;

                    if($this->autotable){
                        $content = $this->auto_table($this->auto_form($fields_array));
                    }else{
                        $content = $this->auto_form($fields_array, false);
                    }
                    return $content;
                }

                /**
                 * Output options in tabs where array keys are tab names
                 * @param array $columns
                 */
                function add_tab($name, $column_ids){
                    if(is_array($column_ids)){
                        foreach($column_ids as $id){
                            $this->tabs[$name][] = $this->columns[$id];
                        }
                    }else{
                        $this->tabs[$name][] = $this->columns[$column_ids];
                    }
                }

                /**
                 * Column is a canvas where options/postboxes being added into.
                 * You can set multiple columns to create a desired layout.
                 * @param integer $id //has to be unique
                 * @param string $width
                 */
                function add_column($id = 0, $width = '100%'){
                    if( !isset($this->columns[$id]) ){
                        $this->columns[$id]['boxes'] = array();
                        $this->columns[$id]['width'] = $width;
                    }
                }

                /**
                 *
                 * @param string $column_name
                 * @param mixed $rows
                 */
                function add_rows2column($rows, $column_id = 0 ){
                    $this->columns[$column_id]['rows'][] = $rows;
                }

                /**
                 *
                 * @param string $title
                 * @param mixed $content
                 * @param <type> $column_id
                 */
                function add_box($title, $content, $column_id = 0 ){

                    //check if the $content is an array, if so we have to convert the array into fields.
                    if(is_array($content)){
                        $box = array($title, $this->fields($content));
                    }else{
                        $box = array($title, $content);
                    }
                    $this->columns[$column_id]['boxes'][] = $box;
                }

                /**
                 *
                 * @param array $boxes -> multi-array have to contain (str)$title, (mixed)$content, (int)$column_id
                 */
                function add_boxes($boxes = array()){
                    foreach($boxes as $box){
                        add_box($box[0], $box[1], $box[2]);
                    }
                }

                /**
                 * HTML Template for the Options page
                 * @param mixed $content
                 * $columns = array of columns, each column is an array of title, content and width.
                 * TODO: finish this function.
                 */
                /**
                 * Config page template
                 * @param mixed $content
                 *              if array, should be organized in the following way:
                 *              array('Section Title', $section_content, $section_width);
                 *              where $section_content can be string or array as follows: array('Row Title', $row_content); - $row_content is a string
                 *              $section_width is a string - in percentiles i.e. 50%
                 * @param string $title
                 */
                function config_page_template($content, $title = '-'){
                ?>
                <div id="plugin_admin_class" class="wrap">
                    <form  action="options.php" method="post" id="<?php echo $this->hook; ?>-conf">
                        <h2 class="submit"><?php echo $this->longname; ?>&nbsp;&nbsp;&nbsp;<input class="button-primary" type="submit" name="submit" value="Save Options" /></h2>
                        <?php
                            settings_fields($this->optionname . '-option-group');

                            if(!is_array($content)){
                                echo $this->postbox_container($this->postbox($title, $content), '70%');
                            }else{
                                foreach($content as $column){
                                    $output = '';
                                    if(!is_array($column[1])){
                                        $output = $this->postbox($column[0], $column[1]);
                                    }else{
                                        foreach($column[1] as $row){
                                            print '<pre>';
                                            //print_r($row[0]);
                                            print '</pre>';
                                            $output .= $this->postbox($row[0], $row[1]);
                                        }
                                    }
                                    echo $this->postbox_container($output, $column[2]);
                                }
                            }

                            echo $this->postbox_container($this->credits_column(), '25%');
                        ?>
                        <br clear="all" />
                        <input class="button-primary" type="submit" name="submit" value="Save Options" />
                    </form>
                </div>
                <?php
                }

                function _config_page_template(){

                ?>
                <div id="plugin_admin_class" class="wrap">
                    <?php if(!$this->tabs): ?>
                    <form  action="options.php" method="post" id="<?php echo $this->hook; ?>-conf">
                    <?php endif; ?>
                        <h2 class="submit"><?php echo $this->longname; ?><?php if(!$this->tabs): ?>&nbsp;&nbsp;&nbsp;<input class="button-primary" type="submit" name="submit" value="Save Options" /><?php endif;?></h2>
                        <?php
                        if(!$this->tabs):
                            settings_fields($this->optionname . '-option-group');
                        endif;
                        //print_r($this->tabs);
                        //Check if tabs were created
                        if(is_array($this->tabs) && count($this->tabs)>0){

                            $tab_names = array_keys($this->tabs);
                            $i=1;
                            $tabs = false;
                            foreach($tab_names as $tab){
                                $tabs .= '<li><a href="#tab-' . $i . '"><span>'. $tab . '</span></a></li>';
                                $i++;
                            }
                            echo '<div id="' . $this->hook . '_tabs">';
                            echo '<ul>';
                            echo $tabs;
                            echo '</ul>';
                            $i=1;
                            foreach($this->tabs as $columns){
                                echo '<div id="tab-' . $i . '">';
                                //each tab should have it's own form. Settings will merge with the existing ones on submission. See validation function.
                                echo '<form action="options.php" method="post" id="' . $this->hook .'-conf">';
                                echo '<input class="button-primary" type="submit" name="submit" value="Save Options" />';
                                settings_fields($this->optionname . '-option-group');
                                echo $this->_template_content($columns);
                                echo '<div style="clear: both;"></div>';
                                echo '<input class="button-primary" type="submit" name="submit" value="Save Options" />';
                                //duplicated a referrer field to overwrite the one from settings_fields function to include tab anchor
                                $ref = esc_attr( $_SERVER['REQUEST_URI'] );
                                echo '<input type="hidden" name="_wp_http_referer" value="'. $ref . '#tab-' . $i . '" />';
                                echo '</form>';
                                echo '</div>';
                                $i++;
                            }
                            echo '</div>';
                        }else{
                            echo $this->_template_content($this->columns);
                        }
                        ?>
                        <br clear="all" />
                        <?php if(!$this->tabs): ?>
                        <input class="button-primary" type="submit" name="submit" value="Save Options" />
                    </form>
                    <?php endif; ?>
                </div>
                <?php
                }

                function _template_content($columns){
                    if(!is_array($columns) || empty($columns))
                        return;

                    //print_r($columns);
                    $html = false;
                    foreach($columns as $name => $column){
                        $output = '';
                        foreach($column['boxes'] as $box){
                            $output .= $this->postbox($box[0], $box[1]);
                        }
                        $html .= $this->postbox_container($output, $columns[$name]['width']);
                    }

                    $html .= $this->postbox_container($this->credits_column(), '28%');
                    return $html;
                }

                /**
		 * Create a potbox widget
                 *
                 * @param string $title
                 * @param string $content
                 * @param string $id
                 * @return string
                 */
		function postbox($title, $content, $id = null) {
                    if($id){
                        $id = 'id="' . $id . '" ';
                    }
                    $output = '
			<div ' . $id . 'class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>' . $title . '</span></h3>
				<div class="inside">'
                                    .$content
				.'</div>
			</div>';
                    return $output;
		}

                function postbox_container($content, $width = '70%'){
                    $output = '
                    <div class="postbox-container" style="width:' . $width . '; margin-right: 1%;">
                        <div class="metabox-holder">
                            <div class="meta-box-sortables">'
                              .$content
                            . '</div>
                        </div>
                    </div>';
                    return $output;
                }

                function credits_column(){
                    $output = $this->plugin_like();
                    $output .= $this->plugin_support();
                    $output .= $this->plugin_credits();
                    $output .= $this->plugin_donate();
                    return $output;
                }

		/**
		 * Create a "plugin like" box.
		 */
		function plugin_like() {
			$content = '
                                    <p>Help us spread the word :)</p>
                                    <iframe src="http://www.facebook.com/plugins/like.php?href=' . htmlspecialchars($this->credits['official_url']) . '&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:45px;" allowTransparency="true"></iframe>
                                    <ul>
                                        <li>Link to it or blog about the plugin, so other users can find out about it.</li>
                                        <li>Give it a good rating on <a href="' . $this->credits['download_url'] . '">WordPress.org</a></li>
                                    </ul>';
			return $this->postbox('Like this plugin?', $content, $this->hook.'like');
		}

		/**
		 * Info box with link to the support forums.
		 */
		function plugin_support() {
                        $content = '
                        <p>Help us make it better:</p>
                        <ul>
                                <li><a href="' . $this->credits['forums_url'] . '">Ask for help</a></li>
                                <li><a href="' . $this->credits['forums_url'] . '">Report a bug</a></li>
                                <li><a href="' . $this->credits['forums_url'] . '">Suggest improvements or new features</a></li>
                        </ul>';
			return $this->postbox('Need support?', $content, $this->hook.'support');
		}

                function plugin_credits(){
                	$content = '<ul>
                                        <li><a href="' . $this->credits['official_url'] . '">Official Plugin Page</a></li>
                                        <li>Designed by <a href="' . $this->credits['author_url'] . '">Max Chirkov</a></li>
                                        <li>Sponsored by ' . $this->credits['sponsored_by'] . '</li>
                                    </ul>';
                        return $this->postbox('Credits', $content, $this->hook.'credits');
                }

                function plugin_donate(){
                    $content = '<p>If you would like to make a financial contribution, as a gesture of your appreciation for this free plugin, please consider a donation to the <a href="https://www.cancer.org/aspx/Donation/DON_1_Donate_Online_Now.aspx" title="Donate to American Cancer Society">American Cancer Society</a></p>
	<div style="text-align:center"><a href="https://www.cancer.org/aspx/Donation/DON_1_Donate_Online_Now.aspx" title="Donate to American Cancer Society"><img src="'.FLEXIDXHS_URL.'/images/ACS-logo.jpg" alt="American Cancer Society Logo" title="Donate to American Cancer Society" /></a></div>';
                    return $this->postbox('Donate', $content, $this->hook.'donate');
                }


	}
}