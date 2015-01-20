<?php
/*
Plugin Name: LastWP LastFM for Wordpress
Plugin URI: http://nicolasbettag.com
Description: Recently played Track Widget for Wordpress.
Version: 1.0
Author: Nicolas Bettag
Author URI: http://nicolasbettag.com
License: GPLv2
*/
	class my_plugin extends WP_Widget {
		function my_plugin() {
		        parent::WP_Widget(false, $name = __('LastWP LastFM for Wordpress', 'LastWP_plugin') );
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
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Last.FM Benutzername:', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>" type="text" value="<?php echo $textarea; ?>" />
	</p>
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
   	echo '<div class="widget-text wp_widget_plugin_box" style="width:269px;">';
    echo '<div class="widget-title" style="width: 90%; height:30px; margin-left:3%; ">';
   
   	if ( $title ) {
    echo  $before_title . $title . $after_title ;
   	}
	echo '</div>';
    echo '<div class="widget-textarea" style="width: 90%; background-color: white; margin-top: -200px;">';
    if( $textarea ) {
    echo '<p class="wp_widget_plugin_textarea" style="font-size:15px;">'.$textarea.'</p>';
    $vonseite = 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user='.$textarea.'&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d';
	$response = @simplexml_load_file($vonseite) or die ("Fehler!");

	echo "<table>";
	foreach ($response->recenttracks->track as $tracks) {

	echo "<tr>";
	echo "<td>";
	$img = $tracks->image[0];
	echo '<img src="'.$img.'" />';
	echo "</td>";
	echo "<td>";
    if ($tracks->name)   	echo $tracks->name . "<br>";
    if ($tracks->artist)    echo $tracks->artist;
    echo "</td>";
    echo "</tr>";
	echo "<br>";
	}
	echo "</table>";
   	}
   	echo '</div>';
   	echo '</div>';
   	echo $after_widget;
}
}
	add_action('widgets_init', create_function('', 'return register_widget("my_plugin");')); 
?>