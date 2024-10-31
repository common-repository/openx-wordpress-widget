<?php

/** this function represents the admin setup page for this
 *  plugin.
 */
function openxwpwidget_adminsection() {
    
    add_option('openxwpwidget_url2openx', '');
    add_option('openxwpwidget_revive_async', '');
    add_option('openxwpwidget_addcustomkeys', '');
    add_option('openxwpwidget_addcategories', '');
    add_option('openxwpwidget_addtags', '');
    add_option('openxwpwidget_blocksameads', '');
        
    if (isset($_POST['openxwpwidget_url2openx'])) {
        $url2openx = $_POST['openxwpwidget_url2openx'];
        // remove a trailing http://, internally we use it without
        $url2openx = preg_replace('/^https?:\/\//', '', $url2openx);
        update_option('openxwpwidget_url2openx', $url2openx);
        $blocksameads = empty($_POST['openxwpwidget_blocksameads']) ? 0 : 1;
        update_option('openxwpwidget_blocksameads', $blocksameads);
    }
    if (isset($_POST['openxwpwidget_addcustomkeys'])) {
        $addcustomkeys = trim($_POST['openxwpwidget_addcustomkeys']);
        update_option('openxwpwidget_addcustomkeys', $addcustomkeys);
    }
    if (isset($_POST['openxwpwidget_addcategories'])) {
        $addcategories = trim($_POST['openxwpwidget_addcategories']);
        update_option('openxwpwidget_addcategories', trim($addcategories));
    }
    if (isset($_POST['openxwpwidget_addtags'])) {
        $addtags = trim($_POST['openxwpwidget_addtags']);
        update_option('openxwpwidget_addtags', trim($addtags));
    }
    if (isset($_POST['openxwpwidget_revive_async'])) {
        $use_async = (int)($_POST['openxwpwidget_revive_async']);
        update_option('openxwpwidget_revive_async', trim($use_async));
    }

    $addcustomkeys = stripslashes(get_option('openxwpwidget_addcustomkeys'));
    $addcategories = stripslashes(get_option('openxwpwidget_addcategories'));
    $addtags = stripslashes(get_option('openxwpwidget_addtags'));
    $url2openx = stripslashes(get_option('openxwpwidget_url2openx'));
    $blocksameads = stripslashes(get_option('openxwpwidget_blocksameads'));
    $use_async = stripslashes(get_option('openxwpwidget_revive_async'));
?>

<STYLE TYPE="TEXT/CSS">
div#openxwpwidget b {
        color: red;
}
div#openxwpwidget td.first {
		width: 170px;
}
</STYLE>

<DIV CLASS="wrap">
    <DIV id="openxwpwidget">
    <h2>OpenX-WP-Widget Settings</h2>
    <FORM name="openxwpwidget_form" method="post" action="<?php echo admin_url(); ?>options-general.php?page=openx-wp-widget.php&updated=true">
    <table>
    <tr>
    	<td colspan="2"><h3><?php _e('AdServer Settings', 'openxwpwidget'); ?></h3></td>
    </tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Url to OpenX-AdServer:', 'openxwpwidget'); ?></td>
    	<td>
    		<input type="text" size="60" name="openxwpwidget_url2openx" id="url2openx" value="<?php echo $url2openx; ?>"><br />
 			<span class="description"><?php _e(
"Type the url (without http://) and path to your OpenX-Adserver delivery directory into the textfield.<br />A sample url and path could like like: <b>ads.openx.org/delivery.</b><br />", 'openxwpwidget'); ?>
 			</span>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Async. AdTag:', 'openxwpwidget'); ?></td>
    	<td>
    		<input type="checkbox" name="openxwpwidget_revive_async" id="use_async" value="1" <?php echo (!empty($use_async) ? 'checked="checked"' : ''); ?>">
 			<span class="description"><?php _e(
"Revive Adserver (<a href='http://www.revive-adserver.com' target='_blank'>http://www.revive-adserver.com</a>) features an async. Javascript Tag. By checking this option, the widget will use it.<br />", 'openxwpwidget'); ?>
 			</span>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Test zone-id:', 'openxwpwidget'); ?></td>
    	<td>
	    	<input type="text" size="10" name="openxwpwidget_zonetest" id="zonetest" value=""><br />
 			<span class="description"><?php _e(
"To test your above url and path you can type a zone-id of your adserver here. When you click 'Save' the plugin will generate an invocation code of that zone and add it here (below).", 'openxwpwidget'); ?>
 			</span>
    	</td>
    </tr>
    <?php if (!empty($_POST['openxwpwidget_zonetest']) && !empty($_POST['openxwpwidget_url2openx'])): ?>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Test result:', 'openxwpwidget'); ?></td>
    	<td>
    		<?php $tmpurl = 'http://'.$_POST['openxwpwidget_url2openx'].'/ajs.php?zoneid='.$_POST['openxwpwidget_zonetest']; ?>
    		<?php echo _openxwpwidget_get_invocation($_POST['openxwpwidget_url2openx'], $_POST['openxwpwidget_zonetest']); ?><br /><br />
 			<span class="description"><?php echo sprintf(__(
"The inserted invocation code points to url: <a href='%s' target='_blank'>%s</a><br />
Please note, that you only see a banner when you setup the zone correctly within your openx adserver AND any banner match the delivery limitations. Expert only: You may simply click on the link above, it will open a new browser window. The url and path is wrong when the page is just blank. It should show you some 'inscrutable' javascript code ...", ''), $tmpurl, $tmpurl); ?>
 			</span>
    	</td>
    </tr>
    <?php endif; ?>
    <tr>
    	<td colspan="2"><h3><?php _e('Ad-Request Settings','openxwpwidget'); ?></h3></td>
    </tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Custom fields:','openxwpwidget'); ?></td>
    	<td>
    		<input type="text" size="60" name="openxwpwidget_addcustomkeys" id="addcustomkeys" value="<?php echo $addcustomkeys; ?>"><br />
 			<span class="description"><?php echo sprintf(__(
"The plugin can add custom keys/values into the ad-request. This will allow you to add some special targeting rules to your banners so they might match to the current posts much better. Please list the custom keys here (comma separated) which the plugin should observe.<br /><br />Example:<br />A standard openx parameter is named <b>source</b> - so when you type 'source' here and your posts contain a custom key 'source', the plugin will append all values of the custom key 'source' to the ad-request (multiple values are joined by '_', which allow you to specify targeting rules 'contains'. Lets assume your post contains the following values for source: 'motorbike', 'car' and 'driving'. As a result the plugin will add 'source=_motorbike_car_driving_' to the ad-request. A banner with the targeting rule: 'Site:source' contains '_motorbike_' will match the rule and can be displayed.<br /><br />Please read further information about openx targeting rules <a href='%s' target='_blank'>here</a>.", 'openxwpwidget'), 'http://www.openx.org/docs/2.8/userguide/banner+delivery+options'); ?>
 			</span>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Category:','openxwpwidget'); ?></td>
    	<td>
    		<input type="text" size="60" name="openxwpwidget_addcategories" id="addcategories" value="<?php echo $addcategories; ?>"><br />
 			<span class="description"><?php _e(
"The plugin can also add the categories of the posts or page into the ad-request. This work similar to the custom fields. Please specify the name of the parameter which should be used for the categories. (No name disable this feature).",'openxwpwidget'); ?>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Tags:','openxwpwidget'); ?></td>
    	<td>
    		<input type="text" size="60" name="openxwpwidget_addtags" id="addtags" value="<?php echo $addtags; ?>"><br />
 			<span class="description"><?php _e(
"The plugin can also add the tags of the posts or page into the ad-request. This work similar to the custom fields or categories. Please specify the name of the parameter which should be used for the tags. (No name disable this feature).",'openxwpwidget'); ?>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Block duplicate ads:','openxwpwidget'); ?></td>
    	<td>
    		<input type="checkbox" name="openxwpwidget_blocksameads" id="blocksameads"
                   <?php if (!empty($blocksameads)) { echo 'checked="checked"'; } ?>">
                   <?php _e("Don't allow the same banner twince on a webpage",'openxwpwidget'); ?><br />
 			<span class="description"><?php _e(
"By default openx is allowed to serve the same banner again to other (or same) zones on a webpage. Adding 'block=1' to the ad-request tell's the adserver to keep track what has been served so far - and so disallow the same banner.",'openxwpwidget'); ?>
 			</span>
 		</td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td colspan="2"><h3><?php _e('Hints','openxwpwidget'); ?></h3></td>
    </tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Sidebar','openxwpwidget'); ?></td>
    	<td><?php _e("The plugin adds a new widget 'OpenX Widget' to your wordpress. You may add the widget to your sidebars.",'openxwpwidget'); ?></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
    	<td valign="top" class="first"><?php _e('Ads within posts','openxwpwidget'); ?></td>
    	<td><?php _e(
"The plugin automatically adds a filter to your wordpress, any occurence of <b>{openx:N}</b> within the posts (whereas <b>N</b> is a zone-id of your adserver) will be replaced with an ad-request to your adserver (using the specified zone-id).",'openxwpwidget'); ?>
    	</td>
    </tr>
    </table>
    <input type="submit" name="submit" value="Save" />
    </FORM>
    <DIV style="padding:20px;text-align:center;">
       <h2><?php _e("Buy me a beer ... - Thanks !",'openxwpwidget'); ?>
       <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="DFZSNZA7FJTFC">
          <input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="Please donate.">
          <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
       </form>
    </DIV>
  </DIV>
</DIV>

<?php
}

/** This function controls the sidebar functionality,
 *  admin-part. Show and save of the options.
 */
function widget_openxwpwidget_control($widget_args = 1) {
    global $wp_registered_widgets;
    static $updated = false; // Whether or not we have already updated the data after a POST submit

    if (is_numeric($widget_args))
        $widget_args = array('number' => $widget_args);
        
    $widget_args = wp_parse_args($widget_args, array('number' => -1));
    extract($widget_args, EXTR_SKIP);

    // Data should be stored as array:  array(number => data for that instance of the widget, ...)
    $options = get_option('widget_openxwpwidget');
    if (!is_array($options))
        $options = widget_openxwpwidget_default_options();
    
    // We need to update the data
    if (!$updated && !empty($_POST['sidebar'])) {
        // Tells us what sidebar to put the data in
        $sidebar = (string) $_POST['sidebar'];

        $sidebars_widgets = wp_get_sidebars_widgets();
        if (isset($sidebars_widgets[$sidebar]))
            $this_sidebar =& $sidebars_widgets[$sidebar];
        else
            $this_sidebar = array();

        foreach ($this_sidebar as $_widget_id) {
            // Remove all widgets of this type from the sidebar.
            // We'll add the new data in a second.
            // This makes sure we don't get any duplicate data
            // since widget ids aren't necessarily persistent across multiple updates
            if ('widget_openxwpwidget' == $wp_registered_widgets[$_widget_id]['callback']
                  && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                if (!in_array("widget_openxwpwidget-$widget_number", $_POST['widget-id'])) {
                // the widget has been removed. "widget_openxwpwidget-$widget_number" is "{id_base}-{widget_number}
                    unset($options['options'][$widget_number]);
                }
            }
        }

        foreach ( (array) $_POST['widget-widget_openxwpwidget'] as $widget_number => $widget_instance) {
            // compile data from $widget_instance
            if (!isset($widget_instance['zonecount']) && isset($options['options'][$widget_number]) ) // user clicked cancel
                continue;print_r($widget_instance);
            $newoptions['title'] = strip_tags(stripslashes($widget_instance['title']));
            $newoptions['align'] = strip_tags(stripslashes($widget_instance['align']));
            $newoptions['zonecount'] = strip_tags(stripslashes($widget_instance['zonecount']));
            for ($n = 0; $n < intval($newoptions['zonecount']); $n++) {
                $newoptions['zoneid'.$n] = strip_tags(stripslashes($widget_instance['zoneid'.$n]));
                $newoptions['break'.$n] = !empty($widget_instance['break'.$n]) ? 1 : 0;
            }
            $options['options'][$widget_number] = $newoptions;
            // Even simple widgets should store stuff in array, rather than in scalar
        }

        update_option('widget_openxwpwidget', $options);
            
        $updated = true; // So that we don't go through this more than once
    }

    if ($number == -1) {
        $number = '%i%';
        $values = array('title' => '', 'align' => false, 'zonecount' => 0);
    } else {
        $values = $options['options'][$number];
    }

    // prepare our settings to show them at the admin-page
    // we prepare up to 10 zones, and use javascript/style to
    // show or hide them, because if the users changes the
    // total zonecode he like to use, we can't submit to
    // change the number of offered zones.

    $title = htmlspecialchars($values['title'], ENT_QUOTES);
    $alignments = array('left', 'center', 'right', 'none');
    $align = htmlspecialchars($values['align'], ENT_QUOTES);
    if (!in_array($align, $alignments)) {
        $align = 'none';
    }
    $zonecount = htmlspecialchars($values['zonecount'], ENT_QUOTES);
    for ($n = 0; $n < intval($zonecount); $n++) {
        $zoneID[$n] = htmlspecialchars($values['zoneid'.$n], ENT_QUOTES);
        $break[$n] = !empty($values['break'.$n]);
    }
?>
<div>
   <label for="widget_openxwpwidget-title-<?php echo $number; ?>">
      Title:<br />
      <input type="text"
             name="widget-widget_openxwpwidget[<?php echo $number; ?>][title]"
               id="widget_openxwpwidget-title-<?php echo $number; ?>"
            value="<?php echo $title; ?>" />
   </label><br />
   <label for="widget_openxwpwidget-align-<?php echo $number; ?>">
      Alignment:<br />
      <select name="widget-widget_openxwpwidget[<?php echo $number; ?>][align]"
                id="widget_openxwpwidget-align-<?php echo $number; ?>">
         <?php
            foreach($alignments as $n) {
                $sel = ($n == $align) ? 'selected' : '';
                echo "<option value='$n' $sel>$n</option>\n";
            }
         ?>
      </select>
   </label><br />
   <label for="widget_openxwpwidget-zonecount-<?php echo $number; ?>">
      Number of banners:<br />
      <select name="widget-widget_openxwpwidget[<?php echo $number; ?>][zonecount]"
                id="widget_openxwpwidget-zonecount-<?php echo $number; ?>"
          onchange="openxwpwidget_showhide_zones(this, <?php echo $number; ?>);">
         <?php
            for ($n = 0; $n <= 10; $n++) {
                $sel = ($n == $zonecount) ? 'selected' : '';
                echo "<option value='$n' $sel>$n</option>\n";
            }
         ?>
      </select>
   </label>
   <br /><br />
   <?php
      $txtZoneId = __( 'ZoneID for Banner %d', 'openxwpwidget' );
      $txtAppendBR = __( 'append a %s', 'openxwpwidget' );
      for ($n = 0; $n < 10; $n++) {
          $showhide = (intval($zonecount) > $n) ? 'inline' : 'none';
          $n1 = $n+1;
          echo "<div id='widget-openxwpwidget-div-zoneid$n-$number' name='widget-openxwpwidget-div-zoneid$n-$number' style='display:$showhide;'>\n";
          echo "<label for='widget_openxwpwidget-zoneid-$number'>".sprintf($txtZoneId, $n1)."&nbsp;/&nbsp;".sprintf($txtAppendBR, '&lt;br /&gt;')."<br />\n";
          echo "<input type='text' id='widget-openxwpwidget-zoneid$n-$number' name='widget-widget_openxwpwidget[$number][zoneid$n]' value='$zoneID[$n]' />\n";
          echo "<input type='checkbox' id='widget-openxwpwidget-break$n-$number' name='widget-widget_openxwpwidget[$number][break$n]' value='1' ".($break[$n] ? "checked='checked'" : "")." />\n";
          echo "</label>\n";
          echo "<br /></div>\n";
      }
   ?>
   <input type="hidden" name="widget-openxwpwidget-submit-<?php echo $number; ?>" id="openxwpwidget-submit-<?php echo $number; ?>" value="true" />
</div>
<?php
}

?>