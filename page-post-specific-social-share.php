<?php
/*
Plugin Name: Page/Post Specific Social Share Buttons
Description: This plugin allows you to display most used social media share buttons on specific posts or pages.
Author: Ryan Howard, Aboobacker P
Author URI: http://www.completewebresources.com/
Plugin URI: http://www.completewebresources.com/page-post-specific-social-share-wp-plugin/
Version: 2.1
License: GPL
*/

require_once('ppss_admin_page.php');
require_once('ppss_display.php');

if (!function_exists('is_admin')) 
{
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

register_activation_hook(__FILE__,'ppss_install'); 

register_deactivation_hook( __FILE__, 'ppss_remove' );

function ppss_install() 
{

}

function ppss_remove() {
delete_option('ppss_social_share');
}
if(is_admin())
{
add_action('admin_menu', 'ppss_twitter_facebook_admin_menu');
}
else
{
 add_action('init', 'ppss_social_share_init');
 add_action('wp_head', 'ppss_fb_like_thumbnails');
 $option = ppss_social_share_get_options_stored();

  add_filter('the_content', 'ppss_twitter_facebook_contents');
  add_filter('the_excerpt', 'ppss_twitter_facebook_excerpt');
}
?>