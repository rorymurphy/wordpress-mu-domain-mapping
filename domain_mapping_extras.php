<?php
/**************************************************************
 * This file is a companion to domain_mapping.php, however
 * all code in this file was developed by Rory Murphy to
 * support mapping both sub-domain and sub-directory based URLs
 * to a single multisite instance
 */
/*  Copyright Rory Murphy (http://www.rorymurphy.com/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
function dm_fix_upload_url($uploaddir){
    $siteurl = get_bloginfo('wpurl');
    $origurl = get_original_url('siteurl');

    $uploaddir['baseurl'] = str_replace($origurl, $siteurl, $uploaddir['baseurl']);
    $uploaddir['url'] = str_replace($origurl, $siteurl, $uploaddir['url']);

    return $uploaddir;
}

function dm_fix_wp_get_attachment_url($url){
    $siteurl = get_bloginfo('wpurl');
    $origurl = get_original_url('siteurl');

    return str_replace($origurl, $siteurl, $url);
}

function dm_fix_nav_menu_item_url( $atts, $item, $args ) {
  $siteurl = get_bloginfo('wpurl');
  $origurl = get_original_url('siteurl');
  if(array_key_exists('href', $atts)){
    $atts['href'] = str_replace($origurl, $siteurl, $atts['href']);
  }
  return $atts;
}

add_filter( 'nav_menu_link_attributes', 'dm_fix_nav_menu_item_url', 10, 3 );

add_filter('upload_dir', 'dm_fix_upload_url');
add_filter('wp_get_attachment_url', 'dm_fix_wp_get_attachment_url');
add_filter('wp_get_attachment_thumb_url', 'dm_fix_wp_get_attachment_url');
add_filter('theme_mod_background_image', 'dm_fix_wp_get_attachment_url');
add_filter('theme_mod_header_image', 'dm_fix_wp_get_attachment_url');
