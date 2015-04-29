<?php
/*
Plugin Name: LastFM Played for Wordpress
Plugin URI: http://nicolasbettag.com
Description: Clean and simple recently played Last.FM Plugin for Wordpress
Version: 0.7
Author: Nicolas Bettag
Author URI: http://nicolasbettag.com
License: GPLv2
*/
/*  Copyright 2015 Nicolas Bettag

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
*/

	class LastWP_plugin extends WP_Widget {
		function LastWP_plugin() {
		parent::WP_Widget(false, $name = __('LastFM Played for Wordpress', 'LastWP_plugin') );
	}
	function form($instance) {
	if( $instance) {
	    $title = esc_attr($instance['title']);
	    $textarea = $instance['textarea'];
	} else {
	    $title = '';
	    $textarea = '';
	}
	?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Last.FM Username:', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>" type="text" value="<?php echo $textarea; ?>" /></p>
	<?php
	}
	function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['textarea'] = strip_tags($new_instance['textarea']);
    return $instance;
	}
	function widget($args, $instance) {
   	extract( $args );
   	$title = apply_filters('widget_title', $instance['title']);
    $textarea = $instance['textarea'];
   	echo $before_widget;
   	echo '<div class="widget-text wp_widget_plugin_box">';
   	echo '<div class="lastfm">';
    echo '<div class="widget-title">';
   
   	if ( $title ) {
    echo  $before_title . $title . $after_title ;
   	}
	echo '</div>';
    echo '<div class="widget-textarea">';
    if( $textarea ) {

    $lastfm_api = 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user='.$textarea.'&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d&limit=5';
	$lastfm_response = @simplexml_load_file($lastfm_api);

	$lastfm_api_user = 'http://ws.audioscrobbler.com/2.0/?method=user.getinfo&user='.$textarea.'&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d';
	$lastfm_user = @simplexml_load_file($lastfm_api_user);

	$user_name = $lastfm_user->user->name;
	$realname = $lastfm_user->user->realname;
	$user_url = $lastfm_user->user->url;
	$userpicture = $lastfm_user->user->image[1];
	$scrobbles = $lastfm_user->user->playcount;

	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo '<img width="100%" height="100%" src="'.$userpicture.'" />';
	echo "</td>";
	echo "<td style='vertical-align: top; line-height: 1.1; padding: 5px;'>";
	echo "<b>" . $realname . '</b><br>';
	echo '<a target="_blank" href="'.$user_url.'">' . $user_name . '</a><br>';
	echo "<small>" . $scrobbles . ' Tracks</small>';
 	echo "</td>";
    echo "</tr>";
	echo "</table>";

	echo "<table>";
	foreach ($lastfm_response->recenttracks->track as $tracks) {
	
	$img = $tracks->image[1];
	$name = $tracks->name;
	$artist = $tracks->artist;
	$time = $tracks->date['uts'];
	$nowplaying = $tracks['nowplaying'];

	if($nowplaying != ""){
	
	echo "<tr>";
	echo "<td style='width: 50px;'>";
	echo '<img height="50" width="50" src="'.$img.'" />';
	echo "</td>";
	echo "<td style='vertical-align: top; line-height: 1.1; padding-left: 5px; padding-top: 10px;'>";
    echo "<small><b>" . $name . "</b></small><br>";
    echo "<small>" . $artist . "</small><br>";
	echo "<small>now playing...</small>";

	} else {

	echo "<tr>";
	echo "<td style='width: 50px;'>";
	echo '<img height="50" width="50" src="'.$img.'" />';
	echo "</td>";
	echo "<td style='vertical-align: top; line-height: 1.1; padding: 5px; padding-left: 5px; padding-top: 10px;'>";
    echo "<small><b>" . $name . "</small></b><br>";
    echo "<small>" . $artist . "</small><br>";
	echo "<small>" . human_time_diff($time) . " ago</small>";	}
    echo "</td>";
    echo "</tr>";
	}
	echo "</table>";
   	}
   	echo '</div>';
   	echo '</div>';
   	echo '</div>';
   	echo $after_widget;
	}
	}
	add_action('widgets_init', create_function('', 'return register_widget("LastWP_plugin");')); 
	add_action( 'wp_enqueue_scripts', 'last_stylesheet' );
	function last_stylesheet() {
    	wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    	wp_enqueue_style( 'prefix-style' );
	}
?>