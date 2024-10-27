<?php
/*
Plugin Name: AdSense Above
Plugin URI:  http://bbantispam.com/wp/adsense/
Version:     1.0
Description: A link ``read the rest of this entry'' loads a page with an advertisement block at the top of the browser window. It improves the chances of a click.
Author:      Oleg Parashchenko <olpa uucode com>
Author URI:  http://uucode.com/blog/
License:     GNU General Public License
Credits:     Alex Zhukoff <http://zhukoff.com/> added the admin interface
*/

function adabSetup() {
	if (function_exists('add_submenu_page')) add_submenu_page('options-general.php', __('AdSense Above plugin'), __('AdSenseAbove'), 1, __FILE__, 'adabSetupView');
	$s = <<<EOT
    <div style="background-color:#ffdddd;width:400;height:80;text-align:center;">
      <p style="vertical-align:middle;padding-top:20px;padding-bottom:20px;">Plugin <a href="http://bbantispam.com/wp/adsense/">AdSense Above</a> is successfully installed, please configure it now.</p>
    </div>
EOT;
	add_option('adab_config', __($s . '|Saved!', 'Settings for AdSense Above plugin'));
}

function adabOption($Name, $Value = false) {
	global $wpdb;
	$String = get_option('adab_config');
	$String = explode('|', $String);
	if ($Value == false) {
		if ($Name == 'adsns') return stripslashes($String[0]);
		if ($Name == 'success') return stripslashes($String[1]);
	} else {
		if ($Name == 'adsns') $String[0] = $Value;
		if ($Name == 'success') $String[1] = $Value;
		$Update = $String[0].'|'.$String[1];
		update_option('adab_config', $Update);
	}
}

function adabSetupView() {
	if ($_POST['submit']) {
		check_admin_referer();
		adabOption('adsns', $_POST['linktitle']);
		adabOption('success', $_POST['successmessage']);
?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
	}
?>
<div class="wrap">
 <h2><?php _e('AdSense Above'); ?></h2>
 <p><?php printf(__('Settings for <a href="%1$s">AdSense Above Plugin</a>.'), 'http://bbantispam.com/wp/adsense/'); ?></p>
 <form action="" method="post">
  <fieldset class="options"> 
   <legend><?php _e('Insert AdSense code (or any other HTML code)'); ?></legend> 
   <table class="optiontable"> 
    <tr valign="top"> 
     <th scope="row"></th> 
     <td><textarea rows="15" cols="70" name="linktitle" id="linktitle"><?php echo htmlspecialchars(adabOption('adsns')); ?></textarea><br /></td> 
    </tr> 
   </table>
  </fieldset>
  <p class="submit"><input type="submit" name="submit" value="Update Options &raquo;" /></p>
 </form>
</div>
<?php
}

function adab_filter($text) {
	$adsense_code = adabOption('adsns');
  // "a" for older versions, "span" for newer versions
  $text = ereg_replace('(<(a|span) id="more-[0-9]+"><\/(a|span)>)', "\\1$adsense_code", $text);
  return $text;
}

add_filter('the_content', 'adab_filter', 9); // Run before WPads and family
add_action('admin_menu',  'adabSetup');

?>
