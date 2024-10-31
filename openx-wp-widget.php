<?php
/***
Plugin Name: openxwpwidget
Plugin URI: http://xclose.de/wordpress/wordpress-widget-for-openx
Description: OpenX Wordpress Widget is a plugin for wordpress websites and provides a simple, straightforward way to place ads from a Revive (formerly OpenX) AdServer on a website.
Version: 1.3.1
Author: Heiko Weber, heiko@wecos.de
Author URI: http://www.wecos.de
Min WP Version: 2.5.0
Max WP Version: 4.5.3
***/

/***
+---------------------------------------------------------------------------+
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
| Copyright (c) 2009 Heiko Weber                                            |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: openx-wp-widget.php 1468231 2016-08-05 05:24:27Z hwde $
*/

/** widget initialization moved into one function,
 *  registered below at the end of the file.
 */
function widget_openxwpwidget_init() {

    global $widget_openxwpwidget_version;
    
    $widget_openxwpwidget_version = 110;

    if ( !function_exists('register_sidebar_widget')
      || !function_exists('register_widget_control') )
            return;

    /** this widget callback function gets called to output
     *  the "widget content" to the sidebar, so this are actually
     *  one or more banners (invocationcodes)
     */
    function widget_openxwpwidget($args, $widget_args = 1) {
        extract($args, EXTR_SKIP );
        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);

        // Data should be stored as array: array(number => data for that instance of the widget, ...)
        $options = get_option('widget_openxwpwidget');
        if (!isset($options['options'][$number]) )
            return;

        // zonecount should be set
        $values = $options['options'][$number];
        if (!isset($values['zonecount']) )
            return;

        // retrieve our widget options and settings
        $location = stripslashes(get_option('openxwpwidget_url2openx'));
        if (!isset($location))
            return;

        $async_js = get_option('openxwpwidget_revive_async');
        $async_js = !empty($async_js);
        
        $params = _openxwpwidget_get_optional_params();
        
        // get title
        $title = empty($values['title']) ? '' : $values['title'];
        // get alignment, defaults to nothing
        $align = (empty($values['align']) || $values['align'] == 'none') ? false : $values['align'];
        // how many banners/zones should we show
        $zoneCount = empty($values['zonecount']) ? 0 : $values['zonecount'];

        // loop over there ...
        $bannercode = '';
        for ($n = 0; $n < intval($zoneCount); $n++) {
            $zoneID = empty($values['zoneid'.$n]) ? '' : $values['zoneid'.$n];
            $bannercode .= _openxwpwidget_get_invocation($location, $zoneID, $params, $async_js).
                           (!empty($values['break'.$n]) ? '<br />' : '');
        }

        // done, most of the echo's are framework
        echo $before_widget;
        if (!empty($title))   { echo $before_title.$title.$after_title; }
        if ($align !== false) { echo "<DIV align='{$align}'>"; }
        echo $bannercode;
        if ($align !== false) { echo "</DIV>"; }
        echo $after_widget;
    }

    function _openxwpwidget_get_optional_params() {
        static $params = false;
        if (is_single() && $params !== false) {
            return $params;
        }
	    $addcustomkeys = get_option('openxwpwidget_addcustomkeys');
        $addcategories = get_option('openxwpwidget_addcategories');
        $addtags = get_option('openxwpwidget_addtags');
		$params = '';
        $addparams = array();
        
		if (!empty($addcustomkeys)) {
            $addparams = _openxwpwidget_get_custom_keys_to_array(array_map('trim', explode(',', $addcustomkeys)));
		}
        if (!empty($addcategories)) {
            if (!array_key_exists($addcategories, $addparams)) {
                $addparams[$addcategories] = array();
            }
            $addparams[$addcategories] = array_merge($addparams[$addcategories], _openxwpwidget_get_categories());
            if (!count($addparams[$addcategories])) unset($addparams[$addcategories]);
        }
        if (!empty($addtags)) {
            if (!array_key_exists($addtags, $addparams)) {
                $addparams[$addtags] = array();
            }
            $addparams[$addtags] = array_merge($addparams[$addtags], _openxwpwidget_get_tags());
            if (!count($addparams[$addtags])) unset($addparams[$addtags]);
        }
        if (!empty($addparams)) {
            $params = _openxwpwidget_join_addparams($addparams);
        }
        $blocksamead = stripslashes(get_option('openxwpwidget_blocksameads'));
        if (!empty($blocksamead)) {
            $params .= ($params != '') ? '&amp;block=1' : 'block=1';
        }

        return $params;
    }
    
    /**
     * retrieve current posts custom key/values and return them as array
     * @param array $keywords list of allowed keywords to be used by this widget
     * @return empty array when no custom keys found,
     *         otherwise an array of keys => array of values
     */
	function _openxwpwidget_get_custom_keys_to_array($keywords) {
		$ret = array();
		if ( $keys = get_post_custom_keys() ) {
			$keywords = array_flip($keywords);
			foreach ( (array) $keys as $key ) {
				$keyt = trim($key);
				if ( '_' == $keyt{0} || !array_key_exists($keyt, $keywords))
					continue;
				$ret[$keyt] = array_map('trim', get_post_custom_values($key));
			}
		}
		return $ret;
	}
    
	function _openxwpwidget_get_categories() {
        $cat = array();
        $categories = get_the_category();
        if ($categories) {
            foreach($categories as $category) {
                $cat[] = $category->cat_name;
            }
        }
        return array_map('trim', $cat);
	}

	function _openxwpwidget_get_tags() {
        $tags = array();
        $posttags = get_the_tags();
        if ($posttags) {
            foreach($posttags as $tag) {
                $tags[] = $tag->name;
            }
        }
        return array_map('trim', $tags);
	}

    function _openxwpwidget_join_addparams($addparams) {
        $ret = '';
        foreach($addparams as $name => $aValues) {
            $value = '_'.implode($aValues,'_').'_';
            $ret .= (($ret != '') ? '&amp;' : '') . $name.'='.urlencode($value);
		}
		return $ret;
	}

    
    /** small helper function, returns a javascript invocationcode
     *
     *  @param string  $location path to the adservers delivery directory
     *  @param integer $zoneID   ID of the zone
     *  @param string  $params   url params added to the request
     *
     *  @return string javascript invocation code
     */
    function _openxwpwidget_get_invocation($location, $zoneID, $params = '', $async_js = 0)
    {
        static $id;
        
        if (empty($location) || $location == '' || intval($zoneID) == 0)
            return '';

        if (!isset($id)) {
            $id = md5("{$location}*{$location}");
        }
        
        if ($async_js) {
            $rparams = '';
            if (!empty($params)) {
                $aParts = explode('&amp;', $params);
                foreach($aParts as $part) {
                    $pos = strpos($part, '=');
                    if ($pos !== false) {
                        $rparams .= ' data-revive-'.substr($part, 0, $pos+1).'"'.substr($part, $pos+1).'"';
                    }
                }
            }
            return '<ins data-revive-zoneid="'.$zoneID.'" data-revive-id="'.$id.'"'.$rparams.'></ins>'.
                    '<script async src="//'.$location.'/asyncjs.php"></script>';
        }
        
        $random = md5(rand(0, 999999999));
        $n = substr(md5(rand(0, 999999999)), 0, 6);
		$amp = ($params == '') ? '' : '&amp;';
        
        return "
<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://" . $location . "/ajs.php':'http://" . $location . "/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);
   document.write (\"?zoneid=". $zoneID ."\");
   document.write ('&amp;cb=' + m3_r);". 
    	(($params != '') ? "\ndocument.write('&amp;{$params}');" : '') . "
   if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);
   document.write (\"&amp;loc=\" + escape(window.location));
   if (document.referrer) document.write (\"&amp;referer=\" + escape(document.referrer));
   if (document.context) document.write (\"&context=\" + escape(document.context));
   if (document.mmm_fo) document.write (\"&amp;mmm_fo=1\");
   document.write (\"'><\/scr\"+\"ipt>\");
//]]>--></script><noscript><a href='http://{$location}/ck.php?n={$n}&amp;cb={$random}' target='_blank'><img src='http://{$location}/avw.php?zoneid={$zoneID}&amp;cb={$random}{$amp}{$params}&amp;n={$n}' border='0' alt='' /></a></noscript>";
    }


    /** this function install the callback-function openxwp_adminsection
     *  for a admin setup page for this plugin
     */
    function openxwpwidget_admin_menuitem()
    {
        if (function_exists('add_options_page')) {
            add_options_page('options-general.php', 'OpenX-WP', 8, basename(__FILE__), 'openxwpwidget_adminsection');
            add_action( "admin_print_scripts", 'openxwpwidget_admin_head' );
        }
    }

    /** this callback function adds some javascript to
     *  the head section (wp-admin-pages only). I've tried
     *  to add this code only for openxwpwidget-pages, but
     *  didn't find a reference-guide how this really work.
     *  So, it gets always added ...
     */
    function openxwpwidget_admin_head()
    {
        ?>
<script type="text/javascript"><!--//<![CDATA[
function openxwpwidget_returnObjByName(name)
{
    if (document.getElementsByName)
       var returnVar = document.getElementsByName(name);
    else if (document.all)
       var returnVar = document.all[name];
    else if (document.layers)
       var returnVar = document.layers[name];
  return returnVar;
}
function openxwpwidget_toggle_visible(whichLayer, on_off) {
  var elem = openxwpwidget_returnObjByName(whichLayer);
  if (elem && elem.length > 0) {
    var item = elem[0];
    var vis = item.style;
    // if the style.display value is blank we try to figure it out here
    if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
      vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'inline':'none';
    if (!on_off)
      on_off = (vis.display!='none') ? -1 : 1;
    vis.display = (on_off < 0)?'none':'inline';
  }
}
function openxwpwidget_showhide_zones(box, widget) {
    var zonecount = box.options[box.selectedIndex].value;
    for (n = 0; n < 10; n++) {
        openxwpwidget_toggle_visible('widget-openxwpwidget-div-zoneid'+n+'-'+widget, (zonecount > n) ? 1 : -1);
    }
}
//]]>-->
</script>
        <?php
    }

    /**
     * this callback function gets called for every real content
     * delivered to normal users. The callback will be installed
     * below (near end-of-file)
     *
     * @param string the content
     *
     * @return string the (maybe un-) modified content
     */
    function openxwpwidget_replace_magic($content)
    {
       // find the magic zone-tags somehow, we replace {openx:NNN}
       // with a invocationcode, whereas NNN is a zoneID
       if (($matches = preg_match_all('/\{openx\:(\d+)\}/', $content, $aResult)) !== false) {
           $content = _openxwpwidget_replace_zones($content, $aResult[1]);
       }

       return $content;
    }

    /**
     * this function replace any magic openx-zones in the given content
     *
     * @param string the content
     * @param array of strings with zone-numbers found in content
     *
     * @return string the (maybe un-) modified content
     */
    function _openxwpwidget_replace_zones($content, $aZoneIds)
    {
        $url2openx = get_option('openxwpwidget_url2openx');
        $url2openx = stripslashes($url2openx);
        $params = _openxwpwidget_get_optional_params();
        $async_js = get_option('openxwpwidget_revive_async');
        $async_js = !empty($async_js);

        // prepare our search/replacement, with perl I would have
        // used a closure to replace it in a single-path
        $from = array();
        $to = array();

        foreach ($aZoneIds as $zoneID) {
            $random = md5(rand(0, 999999999));
            $from[] = '{openx:' . $zoneID . '}';
            $to[]   = _openxwpwidget_get_invocation($url2openx, $zoneID, $params, $async_js);
        }
        return str_replace($from, $to, $content);
    }

    if (is_admin()) {
        require_once dirname(__FILE__).'/admin.php';
    }

    function widget_openxwpwidget_default_options()
    {
        global $widget_openxwpwidget_version;
        
        return array('version' => $widget_openxwpwidget_version,
                     'options' => array(1 => array('title' => '', 'align' => false, 'zonecount' => 0)));
    }
    /** upgrade our options from single instance
     */
    function widget_openxwpwidget_upgrade_from_1_0($options)
    {
        global $widget_openxwpwidget_version;
        
        return array('version' => $widget_openxwpwidget_version,
                     'options' => array(1 => $options));
    }

    /** check the stored options, upgrade if from older
     *  version
     */
    function widget_openxwpwidget_upgrade_check()
    {
        global $widget_openxwpwidget_version;
        
        $options = get_option('widget_openxwpwidget');
        if (!is_array($options)) {
            $options = widget_openxwpwidget_default_options();
        } else if (!isset($options['version'])) {
            $options = widget_openxwpwidget_upgrade_from_1_0($options);
        } else if (isset($options['version']) && $options['version'] == $widget_openxwpwidget_version) {
            return $options;
        }
        // save the modified options
        update_option('widget_openxwpwidget', $options);
        return $options;
    }
    
    /** local init-function, register widget and control
     */
    function widget_openxwpwidget_local_init()
    {
        $options = widget_openxwpwidget_upgrade_check();
        $widget_ops = array('classname' => 'widget_openxwpwidget', 'description' => __('Widget to serve OpenX banners from sidebars', 'openxwpwidget'));
        $control_ops = array('width' => 200, 'height' => 250, 'id_base' => 'widget_openxwpwidget');
        $name = __('OpenX Widget', 'openxwpwidget');
        $values = $options['options'];
        $registered = false;
        foreach (array_keys($values) as $o ) {
            // Old widgets can have null values for some reason
            if (!isset($values[$o]['zonecount']))
                continue;

            // $id should look like {$id_base}-{$o}
            $id = "widget_openxwpwidget-$o"; // Never never never translate an id
            $registered = true;
            wp_register_sidebar_widget( $id, $name, 'widget_openxwpwidget', $widget_ops, array( 'number' => $o ) );
            if (is_admin()) {
                wp_register_widget_control( $id, $name, 'widget_openxwpwidget_control', $control_ops, array( 'number' => $o ) );
            }
        }

	    // If there are none, we register the widget's existance with a generic template
        if ( !$registered ) {
            wp_register_sidebar_widget( 'widget_openxwpwidget-1', $name, 'widget_openxwpwidget', $widget_ops, array('number' => -1));
            if (is_admin()) {
                wp_register_widget_control( 'widget_openxwpwidget-1', $name, 'widget_openxwpwidget_control', $control_ops, array('number' => -1));
            }
        }
        if (is_admin()) {
            // enable languages for admin only ... I assume we don't need it for the frontend
            load_plugin_textdomain('openxwpwidget', false, 'openx-wordpress-widget/languages');
            
            // and finally install our admin-setup-callback ...
            add_action('admin_menu', 'openxwpwidget_admin_menuitem');
        }
        // ... and content-filter
        add_filter('the_content', 'openxwpwidget_replace_magic');
    }

    // register the widget, check our options
    widget_openxwpwidget_local_init();
}

add_action('plugins_loaded', 'widget_openxwpwidget_init');

// add a function get_openx_zone($zoneID), callable
// i.e. from a template
if (!function_exists('get_openx_zone')) {
    function get_openx_zone($zoneID) {
        static $location = null;
        if (is_null($location)) {
            $location = stripslashes(get_option('openxwpwidget_url2openx'));
        }
        if (!$location) return '';
        
        $async_js = get_option('openxwpwidget_revive_async');
        $async_js = !empty($async_js);

        return _openxwpwidget_get_invocation($location, $zoneID, '', $async_js);
    }
}

?>
