<?php
/*
Plugin Name: Local Indicator
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: Indicates when a site is used locally via a dashed broder on the top admin bar, useful for tellign Live and Local dev environments apart
Author: Tom J Nowell, Interconnect/IT
Version: 1.0
Author URI: http://tomjn.com/
*/

add_action('admin_head', 'tomjn_top_border');

function tomjn_top_border() {
	if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
		?>
		<style type="text/css">
			#wpadminbar  { border-top: 3px dashed  #DBCF00; }
			body.admin-bar #wpcontent, body.admin-bar #adminmenu {
				padding-top: 31px;
			}
		</style>
		<?php
	}
}