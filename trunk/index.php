<?php
/*
Plugin Name: LastFM Played for WordPress
Plugin URI: https://nicolasbettag.com
Description: Clean and simple recently played Last.FM Plugin for WordPress
Version: 0.99.2
Author: Nicolas Bettag
Author URI: https://nicolasbettag.com
License: MIT
*/
/*  Copyright 2017 Nicolas Bettag */

function lastfm_load_widget() {
	register_widget( 'lastfm_widget' );
}
add_action( 'widgets_init', 'lastfm_load_widget' );

class lastfm_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'lastfm_widget',
			__('LastFM WordPress Widget', 'lastfm_widget_domain'),
			array( 'description' => __( 'Clean and simple recently played Last.FM Plugin for WordPress', 'lastfm_widget_domain' ), )
		);
	}

	public function form( $instance ) {
		if( $instance ) {
			$title = $instance[ 'title' ];
			$lastfm_user = $instance[ 'lastfm_user' ];
			$lastfm_tracks = $instance[ 'lastfm_tracks' ];
		} else {
			$title = __( 'Recent tracks', 'lastfm_widget_domain' );
			$lastfm_user = __( '', 'lastfm_widget_user' );
			$lastfm_tracks = __( '5', 'lastfm_widget_tracks' );
		}
	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	<br /><br />
	<label for="<?php echo $this->get_field_id( 'lastfm_user' ); ?>"><?php _e( 'LastFM Username:' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'lastfm_user' ); ?>" name="<?php echo $this->get_field_name( 'lastfm_user' ); ?>" type="text" value="<?php echo esc_attr( $lastfm_user ); ?>" />
	<br /><br />
	<label for="<?php echo $this->get_field_id( 'lastfm_tracks' ); ?>"><?php _e( 'How many tracks would you like to show?' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'lastfm_tracks' ); ?>" name="<?php echo $this->get_field_name( 'lastfm_tracks' ); ?>" type="text" value="<?php echo esc_attr( $lastfm_tracks ); ?>" />

	</p>
	<?php
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$lastfm_user = $instance['lastfm_user'];
		$lastfm_tracks = $instance['lastfm_tracks'];

		if (! empty($lastfm_user)) {

			$lastfm_api = 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user='.$lastfm_user.'&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d&limit='.$lastfm_tracks.'';
			$lastfm_response = @simplexml_load_file($lastfm_api);

			$lastfm_api_user = 'http://ws.audioscrobbler.com/2.0/?method=user.getinfo&user='.$lastfm_user.'&api_key=b3f34d8652bf87d8d1dcbfa5c53d245d';
			$lastfm_user = @simplexml_load_file($lastfm_api_user);

			$user_name = $lastfm_user->user->name;
			$realname = $lastfm_user->user->realname;
			$user_url = $lastfm_user->user->url;
			$userpicture = $lastfm_user->user->image[2];
			$scrobbles = $lastfm_user->user->playcount;
		}

		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		echo <<<HTML

		<div class="lastfm-row lastfm-user">
			<div class="lastfm-col-quarter">
				<img width="100%" height="100%" src="$userpicture" />
			</div>
			<div class="lastfm-col-center">
				<b>$realname</b><br>
				<a target="_blank" href="$user_url">$user_name</a><br>
				<small>$scrobbles Tracks</small>
			</div>
		</div>
HTML;

	foreach ($lastfm_response->recenttracks->track as $tracks) {

		$img = ($tracks->image[1]->__toString()) ? $tracks->image[2] : "https://lastfm-img2.akamaized.net/i/u/64s/c6f59c1e5e7240a4c0d427abd71f3dbb.png";
		$name = $tracks->name;
		$artist = $tracks->artist;
		$time = human_time_diff($tracks->date['uts']);
		$nowplaying = $tracks['nowplaying'];

		if($nowplaying != "") {
			$time_final = "now playing...";
		} else {
			$time_final = $time;
		}

		if (empty($img)) {
			$img = "https://lastfm-img2.akamaized.net/i/u/64s/c6f59c1e5e7240a4c0d427abd71f3dbb.png";
		}

		echo <<<HTML

		<div class="lastfm-row lastfm-tracklist">
			<div class="lastfm-col-twenty">
				<img width="100%" height="100%" src="$img" />
			</div>
			<div class="lastfm-col">
				<small><b>$name</small></b><br>
				<small>$artist</small><br>
				<small>$time_final</small>
			</div>
		</div>
HTML;

	}
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['lastfm_user'] = ( ! empty( $new_instance['lastfm_user'] ) ) ? strip_tags( $new_instance['lastfm_user'] ) : '';
		$instance['lastfm_tracks'] = ( ! empty( $new_instance['lastfm_tracks'] ) ) ? strip_tags( $new_instance['lastfm_tracks'] ) : '';
		return $instance;
	}
}

add_action('wp_enqueue_scripts', 'lastfm_style');

function lastfm_style(){
	wp_enqueue_style( 'lastfm_style', plugins_url( 'style.css' , __FILE__ ) );
}

?>
