<?php
/*
Plugin Name: flexIDX Home Search
Plugin URI: http://www.phoenixhomes.com/tech/flexidx-home-search
Description: flexIDX/flexMLS customers only:Provides flexible Home Search widget for your sidebars as well as ability to generate custom search links and iframes that can be embedded into post and page content.
Author: Max Chirkov
Version: 2.1.1
Author URI: http://www.PhoenixHomes.com
*/

define("FLEXIDXHS_BASENAME", plugin_basename(dirname(__FILE__)));
define("FLEXIDXHS_DIR", WP_PLUGIN_DIR . '/' . FLEXIDXHS_BASENAME);
define("FLEXIDXHS_URL", WP_PLUGIN_URL . '/' . FLEXIDXHS_BASENAME);
define("FLEXIDXHS_LIB", FLEXIDXHS_DIR . '/lib');

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
            global $flexidxhs_opt;
            $opt = $flexidxhs_opt;
		extract($args);
                $before_title   = $before_title .  html_entity_decode($opt['widget-markup']['before-title']);
                $after_title    = html_entity_decode($opt['widget-markup']['after-title']) . $after_title;
                $_before_widget  = html_entity_decode($opt['widget-markup']['before-widget']);
                $_after_widget   = html_entity_decode($opt['widget-markup']['after-widget']);
		$title = apply_filters('flexIDXHS_QuickSearch', empty($instance['title']) ? '' : $instance['title']);
		if ( !empty( $title ) ) { $title = $before_title . $title . $after_title; }
			$output = $before_widget;
			$output .= $title;
			$output .= $_before_widget . flexIDXHS_QuickSearch_HTML() . $_after_widget;
			$output .= $after_widget;

                $action  = $instance['action'];
		$show 	 = $instance['show'];
		$slug 	 = $instance['slug'];

                if($action == "1"){
			switch ($show) {
				case "all":
					if($instance['return'] == true){
                                                return $output;
                                        }else{
                                                echo $output;
                                        }
					break;
				case "home":
					if (is_home()) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "post":
					$PiD = explode(",",$slug);
					$onPage = false;
					foreach($PiD as $PageID) {
						if (is_single($PageID)) {
							$onPage = true;
						}
					}
					if ($onPage) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "post_in_category":
					$PiC = explode(",",$slug);
					$InCategory = false;
					foreach($PiC as $CategoryID) {
						if(is_single() && in_category($CategoryID)){
								$InCategory = true;
						}
						elseif (is_category($CategoryID)) {
							$InCategory = true;
						}
					}
					if ($InCategory) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "page":
					$PiD = explode(",",$slug);
					$onPage = false;
					foreach($PiD as $PageID) {
						if (is_page($PageID)) {
							$onPage = true;
						}else{
							$onPage = false;
						}
					}
					if (is_page($PiD)) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "category":
					if (is_category($slug)) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				//Max' Custom Addition
				case "blog":
					if (is_home($slug) || is_single() || is_archive()) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }
					}
			}
                    }else{
                        switch ($show) {
                                case "all":
                                        break;
                                case "home":
                                        if (!is_home()) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "post":
                                        $PiD = explode(",",$slug);
                                        $onPage = false;
                                        foreach($PiD as $PageID) {
                                                if (is_single($PageID)) {
                                                        $onPage = true;
                                                }
                                        }
                                        if (!$onPage) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "post_in_category":
                                        $PiC = explode(",",$slug);
                                        $InCategory = false;
                                        foreach($PiC as $CategoryID) {
                                                if(is_single() && in_category($CategoryID)){
                                                        $InCategory = true;
                                                }
                                                elseif (is_category($CategoryID)) {
                                                        $InCategory = true;
                                                }
                                        }
                                        if (!$InCategory) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "page":
                                        $PiD = explode(",",$slug);
                                        $onPage = false;
                                        foreach($PiD as $PageID) {
                                                if (is_page($PageID)) {
                                                        $onPage = true;
                                                }
                                        }
                                        if (!$onPage) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "category":
                                        if (!is_category($slug)) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                //Max' Custom Addition
                                case "blog":
                                        if (!is_home($slug) && !is_single() && !is_archive()) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }
                                        }
                        }
                    }

	}

	function update( $new_instance, $old_instance ) {
		foreach($new_instance as $k => $v){
                    $instance[$k] = strip_tags($v);
                }
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Quick Home Search' ) );
		$title = strip_tags($instance['title']);

                 $allSelected = $homeSelected = $postSelected = $postInCategorySelected = $pageSelected = $categorySelected = $blogSelected = false;
		switch ($instance['action']) {
			case "1":
			$showSelected = true;
			break;
			case "0":
			$dontshowSelected = true;
			break;
		}
		switch ($instance['show']) {
			case "all":
			$allSelected = true;
			break;
			case "":
			$allSelected = true;
			break;
			case "home":
			$homeSelected = true;
			break;
			case "post":
			$postSelected = true;
			break;
			case "post_in_category":
			$postInCategorySelected = true;
			break;
			case "page":
			$pageSelected = true;
			break;
			case "category":
			$categorySelected = true;
			break;
			case "blog": //Max' Custom Addition
			$blogSelected = true;
			break;
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
                <div><label>On which pages to display this widget:</label><br />
                    <label for="<?php echo $this->get_field_id('action'); ?>"  title="Show only on specified page(s)/post(s)/category. Default is All" style="line-height:35px;"><select name="<?php echo $this->get_field_name('action'); ?>"><option value="1" <?php if ($showSelected){echo "selected";} ?>>Show</option><option value="0" <?php if ($dontshowSelected){echo "selected";} ?>>Do NOT show</option></select> only on: <select name="<?php echo $this->get_field_name('show'); ?>" id="<?php echo $this->get_field_id('show'); ?>"><option label="All" value="all" <?php if ($allSelected){echo "selected";} ?>>All</option><option label="Home" value="home" <?php if ($homeSelected){echo "selected";} ?>>Home</option><option label="Post" value="post" <?php if ($postSelected){echo "selected";} ?>>Post(s)</option><option label="Post in Category ID(s)" value="post_in_category" <?php if ($postInCategorySelected){echo "selected";} ?>>Post In Category ID(s)</option><option label="Page" value="page" <?php if ($pageSelected){echo "selected";} ?>>Page(s)</option><option label="Category" value="category" <?php if ($categorySelected){echo "selected";} ?>>Category</option><option label="Blog" value="blog" <?php if ($blogSelected){echo "selected";} ?>>Blog Main Page, Posts and Archives</option></select></label><br />
                    <label for="<?php echo $this->get_field_id('slug'); ?>"  title="Optional limitation to specific page, post or category. Use ID, slug or title.">Slug/Title/ID: <input type="text" style="width: 250px;" id="<?php echo $this->get_field_id('slug'); ?>" name="<?php echo $this->get_field_name('slug'); ?>" value="<?php echo htmlspecialchars($instance['slug']); ?>" /></label><br />
                    <?php if ($postInCategorySelected) echo "<p>In <strong>Post In Category</strong> add one or more cat. IDs (not Slug or Title) comma separated!</p>";?>
                </div>
<?php
	}
}

class flexIDXHS_CustomSearch extends WP_Widget {

	function flexIDXHS_CustomSearch() {
		$widget_ops = array('classname' => 'flexIDXHS_CustomSearch', 'description' => __('flexIDX Custom Search'));
		$control_ops = array('width' => 350);
		$this->WP_Widget('flexIDXHS_CustomSearch', __('flexIDX Custom Search'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
            global $flexidxhs_opt;
            $opt = $flexidxhs_opt;
		extract($args);
                $before_title   = $before_title .  html_entity_decode($opt['widget-markup']['before-title']);
                $after_title    = html_entity_decode($opt['widget-markup']['after-title']) . $after_title;
                $_before_widget  = html_entity_decode($opt['widget-markup']['before-widget']);
                $_after_widget   = html_entity_decode($opt['widget-markup']['after-widget']);
		$title = apply_filters('flexIDXHS_CustomSearch', empty($instance['title']) ? '' : $instance['title']);

                $action  = $instance['action'];
		$show 	 = $instance['show'];
		$slug 	 = $instance['slug'];

                $fields_array = flexIDXHS_prepare_fields_array(true);
                //$field_names = array_keys($fields_array);

                //$tmp = array_merge(array_keys($instance), array($instance['custom']));
                $tmp = $instance;
                unset($tmp['title']);
                unset($tmp['action']);
                unset($tmp['show']);
                unset($tmp['slug']);
                unset($tmp['custom']);
                $tmp = array_keys($tmp);
                foreach($tmp as $k => $v){
                    if($v == 'Custom Field'){
                        $tmp[$k] = $instance['custom'];
                    }else{
                        $tmp[$k] = $v;
                    }
                }
                $field_names = $tmp;
                foreach($field_names as $name){
                    $fields[$name] = $fields_array[$name];
                }

		if ( !empty( $title ) ) { $title = $before_title . $title . $after_title; }
			$output = $before_widget;
			$output .= $title;
			$output .= $_before_widget . flexIDXHS_QuickSearch_HTML($fields) . $_after_widget;
			$output .= $after_widget;

                    if($action == "1"){
			switch ($show) {
				case "all":
					if($instance['return'] == true){
                                                return $output;
                                        }else{
                                                echo $output;
                                        }
					break;
				case "home":
					if (is_home()) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "post":
					$PiD = explode(",",$slug);
					$onPage = false;
					foreach($PiD as $PageID) {
						if (is_single($PageID)) {
							$onPage = true;
						}
					}
					if ($onPage) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "post_in_category":
					$PiC = explode(",",$slug);
					$InCategory = false;
					foreach($PiC as $CategoryID) {
						if(is_single() && in_category($CategoryID)){
								$InCategory = true;
						}
						elseif (is_category($CategoryID)) {
							$InCategory = true;
						}
					}
					if ($InCategory) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "page":
					$PiD = explode(",",$slug);
					$onPage = false;
					foreach($PiD as $PageID) {
						if (is_page($PageID)) {
							$onPage = true;
						}else{
							$onPage = false;
						}
					}
					if (is_page($PiD)) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				case "category":
					if (is_category($slug)) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
					break;
				//Max' Custom Addition
				case "blog":
					if (is_home($slug) || is_single() || is_archive()) {
						if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }
					}
			}
                    }else{
                        switch ($show) {
                                case "all":
                                        break;
                                case "home":
                                        if (!is_home()) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "post":
                                        $PiD = explode(",",$slug);
                                        $onPage = false;
                                        foreach($PiD as $PageID) {
                                                if (is_single($PageID)) {
                                                        $onPage = true;
                                                }
                                        }
                                        if (!$onPage) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "post_in_category":
                                        $PiC = explode(",",$slug);
                                        $InCategory = false;
                                        foreach($PiC as $CategoryID) {
                                                if(is_single() && in_category($CategoryID)){
                                                        $InCategory = true;
                                                }
                                                elseif (is_category($CategoryID)) {
                                                        $InCategory = true;
                                                }
                                        }
                                        if (!$InCategory) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "page":
                                        $PiD = explode(",",$slug);
                                        $onPage = false;
                                        foreach($PiD as $PageID) {
                                                if (is_page($PageID)) {
                                                        $onPage = true;
                                                }
                                        }
                                        if (!$onPage) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                case "category":
                                        if (!is_category($slug)) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }				}
                                        break;
                                //Max' Custom Addition
                                case "blog":
                                        if (!is_home($slug) && !is_single() && !is_archive()) {
                                                if($instance['return'] == true){
                                                        return $output;
                                                }else{
                                                        echo $output;
                                                }
                                        }
                        }
                    }

	}

	function update( $new_instance, $old_instance ) {
		//$instance = $old_instance;

                foreach($new_instance as $k => $v){
                    $instance[$k] = strip_tags($v);
                }

		return $instance;
	}

	function form( $instance ) {
                global $flexidxhs_opt;
                $opt = $flexidxhs_opt;

		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Home Search' ) );
		$title = strip_tags($instance['title']);

                $allSelected = $homeSelected = $postSelected = $postInCategorySelected = $pageSelected = $categorySelected = $blogSelected = false;
		switch ($instance['action']) {
			case "1":
			$showSelected = true;
			break;
			case "0":
			$dontshowSelected = true;
			break;
		}
		switch ($instance['show']) {
			case "all":
			$allSelected = true;
			break;
			case "":
			$allSelected = true;
			break;
			case "home":
			$homeSelected = true;
			break;
			case "post":
			$postSelected = true;
			break;
			case "post_in_category":
			$postInCategorySelected = true;
			break;
			case "page":
			$pageSelected = true;
			break;
			case "category":
			$categorySelected = true;
			break;
			case "blog": //Max' Custom Addition
			$blogSelected = true;
			break;
		}
?>
                <script type="text/javascript">
                jQuery(document).ready(function(){
                  jQuery("#flexIDXHS-widget-<?php echo $this->number; ?>").sortable();
                  jQuery("#flexIDXHS-widget-<?php echo $this->number; ?> li").css({"cursor" : "move"});
                });
                </script>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
                $fields_array = flexIDXHS_prepare_fields_array();
                $field_names = array_keys($fields_array);

                $custom = false;
                if(is_array($opt['custom-searches']) && !empty($opt['custom-searches'])){
                    $custom = true;
                    $custom_fields = array_keys($opt['custom-searches']);

                    //Unset custom fields from the fields_names array since we're going to use "Custom Field" aliase
                    foreach($custom_fields as $name){
                        if($k = array_search($name, $field_names)){
                                unset($field_names[$k]);
                        }
                    }
                }

                //Merge field_names and array('custom_field') so they can be ordered
                if($custom_fields){
                    $fields = array_merge($field_names, array('Custom Field'));
                }else{
                    $fields = $field_names;
                }

                //Set a temporary array of field names in the $instance
                $tmp = $instance;
                unset($tmp['title']);
                unset($tmp['custom']); //we don't need the title nor the names of the actual custom fields.
                $tmp = array_keys($tmp);

                //Remove fields from the $tmp that don't exist
                //in case there were fields before and now they are not there.
                foreach($tmp as $k => $name){
                    if(!in_array($name, $fields)){
                        unset($tmp[$k]);
                    }
                }

                //Check if each available field is in the $tmp
                //if not - add the missing fields to the end of the array
                foreach($fields as $name){
                    if(!in_array($name, $tmp)){
                        $tmp[] = $name;
                    }
                }

                $output = '<ul id="flexIDXHS-widget-' . $this->number . '" style="border-bottom: 1px solid #dfdfdf; padding-bottom: 10px;">';
                foreach($tmp as $name){
                    $checked = false;
                    if($instance[$name]){
                        $checked = " checked";
                    }
                    $output .= '<li><input type="checkbox" name="' . $this->get_field_name($name) . '" id="' . $this->get_field_id($name) . '"' . $checked . '/> ' . $name . '</li>';
                }
                $output .= '</ul>';
                if($custom){

                    $output .= '<p style="border-bottom: 1px solid #dfdfdf; padding: 10px 0;"><strong>Custom fields:</strong><br/>';

                    foreach($custom_fields as $name){
                        $checked = false;
                        if($instance['custom'] == $name){
                            $checked = ' checked="checked"';
                        }
                        $output .= '<input type="radio" name="' . $this->get_field_name('custom') . '" value="' . $name . '" id="' . $this->get_field_id($name) . '"' . $checked . '/> ' . $name . '<br />';
                    }
                    $output .= '</p>';
                }

                echo $output;
                ?>
                <div><label>On which pages to display this widget:</label><br />
                    <label for="<?php echo $this->get_field_id('action'); ?>"  title="Show only on specified page(s)/post(s)/category. Default is All" style="line-height:35px;"><select name="<?php echo $this->get_field_name('action'); ?>"><option value="1" <?php if ($showSelected){echo "selected";} ?>>Show</option><option value="0" <?php if ($dontshowSelected){echo "selected";} ?>>Do NOT show</option></select> only on: <select name="<?php echo $this->get_field_name('show'); ?>" id="<?php echo $this->get_field_id('show'); ?>"><option label="All" value="all" <?php if ($allSelected){echo "selected";} ?>>All</option><option label="Home" value="home" <?php if ($homeSelected){echo "selected";} ?>>Home</option><option label="Post" value="post" <?php if ($postSelected){echo "selected";} ?>>Post(s)</option><option label="Post in Category ID(s)" value="post_in_category" <?php if ($postInCategorySelected){echo "selected";} ?>>Post In Category ID(s)</option><option label="Page" value="page" <?php if ($pageSelected){echo "selected";} ?>>Page(s)</option><option label="Category" value="category" <?php if ($categorySelected){echo "selected";} ?>>Category</option><option label="Blog" value="blog" <?php if ($blogSelected){echo "selected";} ?>>Blog Main Page, Posts and Archives</option></select></label><br />
                    <label for="<?php echo $this->get_field_id('slug'); ?>"  title="Optional limitation to specific page, post or category. Use ID, slug or title.">Slug/Title/ID: <input type="text" style="width: 250px;" id="<?php echo $this->get_field_id('slug'); ?>" name="<?php echo $this->get_field_name('slug'); ?>" value="<?php echo htmlspecialchars($instance['slug']); ?>" /></label><br />
                    <?php if ($postInCategorySelected) echo "<p>In <strong>Post In Category</strong> add one or more cat. IDs (not Slug or Title) comma separated!</p>";?>
                </div>
                <?php
	}
}

add_action('wp_head', 'flexIDXHS_js');
function flexIDXHS_js(){
    global $flexidxhs_opt;

    $js_search = _js_search();
    $js_advsearch = _js_advsearch();
    $city = _flex_field_name('city');
    $property_type = _flex_field_name('property-type');

    $output = <<<ENDOFHTMLBLOCK
<script type="text/javascript">
/* <![CDATA[ */
var quick_search_base_url = '{$flexidxhs_opt['idx-url']}';

function searchnow(form_id) {
    var fid = '#' + form_id;
    var base_url = quick_search_base_url;
    if(jQuery(fid + ' .flexidxhs_custom_field').val()){
        var url = jQuery(fid + ' .flexidxhs_custom_field').val();
        if(url != ''){
            base_url = url;
        }else{
            base_url = quick_search_base_url;
        }
    }

  if(jQuery(fid + ' .city').val()){
    var city = jQuery(fid + ' .city').val();
  }
  if(jQuery(fid + ' .min_price').val()){
    var min_price = jQuery(fid + ' .min_price').val();
    var max_price = jQuery(fid + ' .max_price').val();
  }
  if(jQuery(fid + ' .price_range').val()){
    var price_range = jQuery(fid + ' .price_range').val();
  }
  if(jQuery(fid + ' .property_type').val()){
    var property_type = jQuery(fid + ' .property_type').val();
  }
  if(jQuery(fid + ' .bedrooms').val()){
    var bedrooms = jQuery(fid + ' .bedrooms').val();
  }
  if(jQuery(fid + ' .bathrooms').val()){
    var bathrooms = jQuery(fid + ' .bathrooms').val();
  }

  var search_link = base_url;
  if(typeof property_type != 'undefined' && property_type != ''){ search_link += '&{$property_type}=' + property_type; }
  if(typeof city  != 'undefined' && city != ''){ search_link += '&{$city}=' + city; }
  if(typeof min_price  != 'undefined' && min_price != '' && typeof max_price  != 'undefined' && max_price != ''){ search_link += '&list_price=' + min_price + ',' + max_price; }
  if(typeof price_range != 'undefined' && price_range != ''){ search_link += '&list_price=' + price_range; }
  if(typeof bedrooms  != 'undefined' && bedrooms != ''){ search_link += '&total_br=>' + bedrooms; }
  if(typeof bathrooms  != 'undefined' && bathrooms != ''){ search_link += '&total_bath=>' + bathrooms; }

  /*popupWin = window.open(search_link, 'open_window');*/
  {$js_search}
}

function advsearch() {
  /*popupWin = window.open(quick_search_base_url, 'open_window');*/
  {$js_advsearch}
}
/* ]]> */
</script>
ENDOFHTMLBLOCK;

    echo $output;
}

function _flex_field_name($name){
    global $flexidxhs_opt;
    $default_names = array(
        'city'          => 'city',
        'property-type' => 'DwellingType',
        'price_min'     => 'min-price',
        'price_max'     => 'max-price',
        'beds'          => 'bedrooms',
        'baths'         => 'bathrooms',
    );
    if($flexidxhs_opt['field-names'][$name]){
        return $flexidxhs_opt['field-names'][$name];
    }
    return $default_names[$name];
}

function _js_search(){
    $js_string = "popupWin = window.open(search_link, 'open_window');";
    $js_string = apply_filters('flexidx_js_search', $js_string);
    return $js_string;
}

function _js_advsearch(){
    $js_string = "popupWin = window.open(quick_search_base_url, 'open_window');";
    $js_string = apply_filters('flexidx_js_advsearch', $js_string);
    return $js_string;
}

function flexIDXHS_QuickSearch_HTML($fields_array = false){
    global $flexidxhs_opt;

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
        $advanced_search = '<input type="button" name="AdvancedSearch" class="AdvancedSearch" value="'.$adv_search_label.'" onclick="advsearch();" />';
    }else{
        $class_on = 'class="advanced-search-off"';
    }

    $rand = rand(1, 999);
    $output .= "<div class='flexIDXHS_QuickSearch'>";
    $output .= "<div class='flexIDXHS_QuickSearch_form' id='formid_{$rand}'>";
    $output .= flexIDXHS_QuickSearch_form($fields_array);
    $output .= '<div clear="all" '.$class_on.'>';
    $output .= '<input type="button" name="SearchNow" class="SearchNow" value="'.$search_label.'" onclick="searchnow(\'formid_' . $rand . '\');" />';
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
		0 => 'No Min Price', 50000 => '$50,000', 100000 => '$100,000', 150000 => '$150,000', 200000 => '$200,000', 250000 => '$250,000', 300000 => '$300,000', 350000 => '$350,000', 400000 => '$400,000', 450000 => '$450,000', 500000 => '$500,000', 550000 => '$550,000', 600000 => '$600,000', 650000 => '$650,000', 700000 => '$700,000', 750000 => '$750,000', 800000 => '$800,000', 850000 => '$850,000', 900000 => '$900,000', 950000 => '$950,000', 1000000 => '$1,000,000', 1250000 => '$1,250,000', 1500000 => '$1,500,000', 1750000 => '$1,750,000', 2000000 => '$2,000,000', 2500000 => '$2,500,000', 3000000 => '$3,000,000',
	);
    return $price_min;
}
function flexIDXHS_price_max(){
    $price_max = array(
                '999999999999' => 'No Max Price', 50000 => '$50,000', 100000 => '$100,000', 150000 => '$150,000', 200000 => '$200,000', 250000 => '$250,000', 300000 => '$300,000', 350000 => '$350,000', 400000 => '$400,000', 450000 => '$450,000', 500000 => '$500,000', 550000 => '$550,000', 600000 => '$600,000', 650000 => '$650,000', 700000 => '$700,000', 750000 => '$750,000', 800000 => '$800,000', 850000 => '$850,000', 900000 => '$900,000', 950000 => '$950,000', 1000000 => '$1,000,000', 1250000 => '$,1250,000', 1500000 => '$1,500,000', 1750000 => '$1,750,000', 2000000 => '$2,000,000', 2500000 => '$2,500,000', 3000000 => '$3,000,000', 4000000 => '$4,000,000', 5000000 => '$5,000,000', 6000000 => '$6,000,000', 7000000 => '$7,000,000', 8000000 => '$8,000,000',
       );
    return $price_max;
}
function flexIDXHS_property_types(){
    global $flexidxhs_opt;
    if(!$property_types = $flexidxhs_opt['field-names']['property-type-values']){
        $property_types = array(
                    //'label'         => ($opt['label-names']['property-type']) ? $opt['label-names']['property-type'] : 'Select Property Type',
                    'SF,PH'         => 'Single Family Homes',
                    'TH,AF'         => 'Condos/Townhomes',
                    'LS'            => 'Loft Style',
            );
    }
    return $property_types;
}
function flexIDXHS_price_range(){
    $price_range = array(
            '0,50000'          => 'Up to $50,000',
            '50000,100000'     => '$50,000 - $100,000',
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
            '5000000,'            => 'Over $5,000,000',
        );
    return $price_range;
}

function flexIDXHS_QuickSearch_form($fields_array = false){
    global $flexidxhs_opt;
    $opt = $flexidxhs_opt;

    if(is_array($opt['custom-searches'])){
        $custom_search_names = array_keys($opt['custom-searches']);
    }

    if(!is_array($fields_array)){
        $query_properties = flexIDXHS_prepare_fields_array();
    }else{
        $query_properties = $fields_array;
    }

	foreach($query_properties as $name => $properties){

                /*
                 * Checking Fields label Visibility Settings
                 */
                if($properties['label']){

                    if($opt['label-visibility'] == 'outside'){
                        $output .= '<label class="label_'. str_replace(' ', '', strtolower($name)) .'"><span>'. $properties['label'] .'</span>'; $close_label = '</label>';

                    }elseif($opt['label-visibility'] == 'inside'){
                        $inside_label .= "\t" . '<option value="">'. $properties['label'] .'</option>' . "\n";

                    }
                    unset($properties['label']);

                }

                if($name == 'City'){
                    foreach($properties as $k => $v){
                        //check if the key is a string, which meants it's a custom value, otherwise the value and the option label are the same.
                        if(is_string($k)){
                            $_cities[$k] = $v;
                        }else{
                            $_cities[$v] = $v;
                        }
                    }
                    unset($properties);
                    $properties = $_cities;
                }



                if($custom_search_names && in_array($name, $custom_search_names)){
                    $id = 'flexidxhs_custom_field';
                }else{
                    $id = str_replace(' ', '_', strtolower($name));
                }

                $output .= "\n" . '<select class="'. $id .'" name="'. str_replace(' ', '', $name) .'">' . "\n";
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

function flexIDXHS_prepare_fields_array($include_custom_fields = false){
    global $flexidxhs_opt;
    $opt = $flexidxhs_opt;

    $label_options_vocab = array(
            /*
             * array var pointing to option name in the settings
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

        /*
         * Setting minium price level based on the settings.
         */
        $price_min = flexIDXHS_price_min();
        $price_range = flexIDXHS_price_range();
        if($opt['set-min-price'] != 0){
            foreach($price_min as $k => $v){
                if($k == $opt['set-min-price']){
                    break;
                }else{
                    unset($price_min[$k]);
                }
            }
            foreach($price_range as $k => $v){
                $k_arr = explode(',', $k);
                if((int)$k_arr[0] == $opt['set-min-price']){
                    break;
                }else{
                    unset($price_range[$k]);
                }
            }
        }
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

       if ( $include_custom_fields && is_array($opt['custom-searches']) ) {
            $query_properties = array_merge($query_properties, $opt['custom-searches']);
       }

       return $query_properties;
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

function flexIDXHS_scripts(){
    wp_enqueue_script('jquery');
}

function flexIDXHS_styles(){
	$myStyleUrl = FLEXIDXHS_URL . '/style.css';
    $myStyleFile = FLEXIDXHS_DIR . '/style.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('flexIDXHS', $myStyleUrl);
        wp_print_styles( 'flexIDXHS');
    }
}

if($flexidxhs_opt['idx-url']){
    add_action('init', 'flexIDXHS_scripts');
    add_action('wp_head', 'flexIDXHS_styles');
    add_action('widgets_init', create_function('', 'return register_widget("flexIDXHS_QuickSearch");'));
    add_action('widgets_init', create_function('', 'return register_widget("flexIDXHS_CustomSearch");'));
}

/*
 * Shortcodes
 */
/*
 * ToDo Max: Add CustomSearch shortcode.
 */
function flexIDXHS_widget_shortcode($atts = array()){
   return flexIDXHS_QuickSearch_HTML();
}
add_shortcode('flexidxhs', 'flexIDXHS_widget_shortcode');

function flexIDX_iframe_shortcode($atts){
    global $flexidxhs_opt;
    extract(
        shortcode_atts(array(
            'url'       => false,
            'width'     => $flexidxhs_opt['shortcodes']['iframe-width'],
            'height'    => $flexidxhs_opt['shortcodes']['iframe-height'],
        ), $atts)
    );
    if($flexidxhs_opt['shortcodes']['full-screen-link']){
        if($flexidxhs_opt['shortcodes']['link-title']){
            $title = ' title="' . $flexidxhs_opt['shortcodes']['link-title'] . '"';
        }
        $link = '<a class="full-screen-link"'. $title . ' href="' . esc_attr($url) . '" target="_blank">' . $flexidxhs_opt['shortcodes']['link-anchor'] . '</a>';
    }
    return $link . '<iframe src="' . esc_attr($url) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></iframe>' . $link;
}
add_shortcode('idxiframe', 'flexIDX_iframe_shortcode');


/**
 * Customizing JS link for the IDX Quick Homes search to output within custom template
 */
function _idx_js_search($content){
        global $flexidxhs_opt;

        if( !isset($flexidxhs_opt['iframe']) || 0 ==  $flexidxhs_opt['iframe'] )
                return $content;

        $output = "
        var wrapper_link = '". get_permalink( $flexidxhs_opt['iframe'] ) . "';
        popupWin = window.open(wrapper_link + '?idxurl=' + search_link, '_self');
        ";
        return $output;
}
function _idx_js_advsearch($content){
        global $flexidxhs_opt;

        if( !isset($flexidxhs_opt['iframe']) || 0 ==  $flexidxhs_opt['iframe'] )
                return $content;

        $output = "
        var wrapper_link = '". get_permalink( $flexidxhs_opt['iframe'] ) . "';
        popupWin = window.open(wrapper_link + '?idxurl=' + quick_search_base_url, '_self');
        ";
        return $output;
}
function _insert_idx_iframe($content){
        global $flexidxhs_opt;

        if( isset($_GET['idxurl']) && stristr($_GET['idxurl'], 'http://link.flexmls.com') ){
            $atts['url'] = esc_attr( str_replace('idxurl=', '', $_SERVER['QUERY_STRING']) );
        }else{
            //just insert the default search iframe
            $atts['url'] = $flexidxhs_opt['idx-url'];
        }

        return $content . flexIDX_iframe_shortcode($atts);
}
function _check_idx_iframe_page(){
        global $flexidxhs_opt;

        if( isset($flexidxhs_opt['iframe']) && 0 !=  $flexidxhs_opt['iframe'] && is_page($flexidxhs_opt['iframe']) )
                add_filter('the_content', '_insert_idx_iframe', 10);

}

add_action('wp', '_check_idx_iframe_page');
add_filter('flexidx_js_search', '_idx_js_search');
add_filter('flexidx_js_advsearch', '_idx_js_advsearch');