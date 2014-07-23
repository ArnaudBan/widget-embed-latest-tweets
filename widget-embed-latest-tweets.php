<?php
/*
 * Plugin Name: Widget embed latest Tweets
 * Plugin URI: http://arnaudban.me/blog/portfolio/widget-embed-latest-tweets/
 * Description: A Widget to show your latest Tweets. Use the oEmbed methode and some cache. Visit the option page "Plugins->Widget Embed Last Plugin" to authentify yourself
 * Version: 0.6.3
 * Author: Arnaud Banvillet
 * Author URI: http://arnaudban.me
 * License: GPL2
 *
 * Text Domain: widget-embed-lastest-tweets
 * Domain Path: /languages
 *
 * Copyright 2012  Arnaud Banvillet  (email : arnaud.banvillet@gmail.com )
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


class Widget_Embed_Latest_Tweets extends WP_Widget {

	var $defaut = array(
			'title'            => 'Latest Tweets',
			'count'            => 3,
			'maxwidth'         => '',
			'align'            => 'none',
			'hide_thread'      => true,
			'include_rts'      => true,
			'hide_media'       => true,
			'exclude_replies'  => false,
	);

	var $align_possible_value = array('none', 'left', 'right', 'center');

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
						'welt_last_tweets', // Base ID
						'Widget embed latest Tweets', // Name
						array('description' => __('Show your latest Tweets', 'widget-embed-lastest-tweets'))// Args
		);

		if( defined( 'WPLANG' ) ){
			$this->defaut['lang'] = substr( WPLANG, 0, 2 );
		} else {
			$this->defaut['lang'] = 'en';
		}
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {

		wp_enqueue_script('welt_script');
		wp_localize_script( 'welt_script', 'weltAjaxurl', admin_url('admin-ajax.php') );


		$instance = wp_parse_args($instance, $this->defaut);

		extract($args);
		extract($instance);

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if ( !empty( $title ) ){
			echo $before_title . $title . $after_title;
		}

		if( !empty( $screen_name ) ){

			$data = ' ';
			if( method_exists('WP_Widget', 'is_preview' ) && $this->is_preview() ){

				foreach ($instance as $key => $value) {

					if( !empty( $value ) ){
						$data .= "data-{$key}='$value' ";
					}
				}

			}

			echo "<div id='welt-{$this->id}' class='welt-tweet-wrapper'$data></div>";

		}

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance = wp_parse_args($instance, $this->defaut);

		$instance['title'] = sanitize_text_field($new_instance['title']);

		$instance['screen_name'] = sanitize_text_field($new_instance['screen_name']);

		$count = intval( $new_instance['count'] );
		if( $count != 0 ){
			$instance['count'] = $count ;
		}

		$maxwidth = intval($new_instance['maxwidth']);
		$instance['maxwidth'] = $maxwidth != 0 ? $maxwidth : '';

		if( in_array($new_instance['align'], $this->align_possible_value ) ){
			$instance['align'] = $new_instance['align'];
		}

		$instance['hide_thread'] = isset( $new_instance['hide_thread'] ) && $new_instance['hide_thread'] == 'hide_thread';
		$instance['hide_media'] = isset( $new_instance['hide_media'] ) && $new_instance['hide_media'] == 'hide_media';
		$instance['exclude_replies'] = isset( $new_instance['exclude_replies'] ) && $new_instance['exclude_replies'] == 'exclude_replies';

		$instance['lang'] = sanitize_text_field($new_instance['lang']);

		// When everythings is check and we are not in the customizer, set the transient
		if( ! method_exists('WP_Widget', 'is_preview' ) || ! $this->is_preview() ){

				welt_set_tweet_transient( $this->id, $instance );

		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {

		$instance = wp_parse_args($instance, $this->defaut);
		extract($instance);

		$twitter_oauth_var = get_option('welt_twitter_oauth_var');

		// Are all the options there ?
		if( is_array( $twitter_oauth_var ) && count($twitter_oauth_var) == 4 ){



			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?> :</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _e('Twitter Username', 'widget-embed-lastest-tweets') ?> :</label>
				<input class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php if( isset($screen_name) ) echo $screen_name; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of Tweet to display', 'widget-embed-lastest-tweets') ?> :</label>
				<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $count; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('maxwidth'); ?>"><?php _e('Width') ?> :</label>
				<input id="<?php echo $this->get_field_id('maxwidth'); ?>" name="<?php echo $this->get_field_name('maxwidth'); ?>" type="number" step="1" min="250" max="550" value="<?php echo $maxwidth; ?>" />
				<br />
				<span class="description"><?php _e('Twitter says :This value is constrained to be between 250 and 550 pixels', 'widget-embed-lastest-tweets') ?></span>

			</p>

			<p>
				<label for="<?php echo $this->get_field_id('align'); ?>"><?php _e('Alignment') ?> :</label>
				<select id="<?php echo $this->get_field_id('align'); ?>" name="<?php echo $this->get_field_name('align'); ?>">
					<?php foreach( $this->align_possible_value as $value ) { ?>
						<option value="<?php echo $value ?>" <?php selected($value, $align, true) ?>><?php echo $value ?></option>
					<?php } ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('hide_thread'); ?>"><?php _e('Hide Thread', 'widget-embed-lastest-tweets') ?> :</label>
				<input id="<?php echo $this->get_field_id('hide_thread'); ?>" name="<?php echo $this->get_field_name('hide_thread'); ?>" type="checkbox" <?php checked( $hide_thread ) ?> value="hide_thread"/>
				<br />
				<span class="description"><?php _e('Hide the original message in the case that the embedded Tweet is a reply', 'widget-embed-lastest-tweets') ?></span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('hide_media'); ?>"><?php _e('Hide Media', 'widget-embed-lastest-tweets') ?> :</label>
				<input id="<?php echo $this->get_field_id('hide_media'); ?>" name="<?php echo $this->get_field_name('hide_media'); ?>" type="checkbox" <?php checked( $hide_media ) ?> value="hide_media"/>
				<br />
				<span class="description"><?php _e('Hide the images in the Tweet' , 'widget-embed-lastest-tweets') ?></span>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('exclude_replies'); ?>"><?php _e('Exclude replies', 'widget-embed-lastest-tweets') ?> :</label>
				<input id="<?php echo $this->get_field_id('exclude_replies'); ?>" name="<?php echo $this->get_field_name('exclude_replies'); ?>" type="checkbox" <?php checked( $exclude_replies ) ?> value="exclude_replies"/>
				<br />
				<span class="description"><?php _e('They will not show but they will count in the number of tweets' , 'widget-embed-lastest-tweets') ?></span>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language', 'widget-embed-lastest-tweets') ?> :</label>
				<input id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" size="2"/>
				<br />
				<span class="description"><?php _e('Two firsts caractere only. Example : "fr" for french', 'widget-embed-lastest-tweets') ?></span>
			</p>
			<?php

		} else {
			?>
			<p>
				<?php printf( __('You have to enter your <a href="%s">Twitter connections information first</a>', 'widget-embed-lastest-tweets'), 'options-general.php?page=welt_options_page' ) ?>
			</p>
			<?php
		}
	}

}

add_action('widgets_init', create_function('', 'register_widget( "Widget_Embed_Latest_Tweets" );'));


/**
	* Cache the twitter json file. Twitter say "Store API responses in your application or on your site"
	* And the page load faster with this cache.
	*
	* @param array $options
	* @param boolean $update
	*/
function welt_set_tweet_transient( $widget_id, $options ){


	$last_tweet = welt_get_latest_tweet( $options );

	if( $last_tweet && is_array( $last_tweet ) ){

		set_transient('last_tweet_' . $widget_id , $last_tweet, 60 * 5);

			foreach ($last_tweet as $tweet_id) {

				$tweet_html = welt_get_tweet_html( $tweet_id, $options );

				if( $tweet_html ){
					set_transient('last_tweet_html_' . $tweet_id, $tweet_html, ( 24 * WEEK_IN_SECONDS ) ); // 6 mouths
				}

			}
	} else {

		delete_transient( 'last_tweet_' . $widget_id );
	}
}

/**
 * Get twitter connection based on the option
 *
 */
function welt_get_twitter_connection(){

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');

	$connection = false;
	//Check if wee use the authentification methode. We need to have all the key and secret.
	if( is_array( $twitter_oauth_var ) && count($twitter_oauth_var) == 4 ){

		$connection = new TwitterOAuth($twitter_oauth_var['consumer_key'], $twitter_oauth_var['consumer_secret'], $twitter_oauth_var['token_key'],$twitter_oauth_var['token_secret']);

	}

	return $connection;
}

/**
 * get the lastest tweet
 */
function welt_get_latest_tweet( $options ){

	$connection = welt_get_twitter_connection();

	// If the connection is ok let get the twitter information
	if( $connection ){

		$last_tweet = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json', $options );

		// if there is an error
		if( isset( $last_tweet->errors ) ){

			$return_value = $last_tweet->errors[0]->message;
			error_log($last_tweet->errors[0]->message);

		// If there is nothing
		} elseif( $last_tweet == false || empty( $last_tweet )){

			$return_value = false;

		// It should be ok
		} else {

			$return_value = array();
			foreach ( $last_tweet as $tweet) {
				$return_value[] = $tweet->id_str;
			}

		}

	} else {

		$return_value = 'Connection error';

	}

	return $return_value;
}

function welt_get_tweet_html( $tweet_id, $options ){

	$connection = welt_get_twitter_connection();

	// If the connection is ok let get the twitter information
	if( $connection ){
		$options['id'] = $tweet_id;
		$options['omit_script'] = true;

		if( empty( $maxwidth) ){
			unset( $options['maxwidth']);
		}

		$tweet = $connection->get('https://api.twitter.com/1.1/statuses/oembed.json', $options);

		if( isset( $tweet->errors ) ){

			$return_value = 'false';
			error_log( $last_tweet->errors[0]->message );

		} else {

			$return_value = $tweet->html;
		}

	} else {

		$return_value = 'false';
	}

	return $return_value;
}


/**
 * The function callback in ajax to display Tweet
 */
function welt_display_tweets( ){

	$all_welt_widgets = $_POST['all_welt_widgets'];

	$tweet_html = array();

	foreach ($all_welt_widgets as $welt_widget ) {

		$widget_id = $welt_widget['widget_id'];
		$widget_data = isset( $welt_widget['widget_data'] ) && ! empty( $welt_widget['widget_data'] ) ? $welt_widget['widget_data'] : false;

		$tweet_html[ $widget_id ] = '';

		// In preview mode we passe all instance in data html attrribut
		if( $widget_data ){

			$instance = $widget_data;

			$last_tweet = welt_get_latest_tweet( $instance );

			foreach ($last_tweet as $tweet_id) {

				$tweet_html[ $widget_id ] .= welt_get_tweet_html( $tweet_id, $instance );

			}


		} else {


			$last_tweet = get_transient('last_tweet_' . $widget_id);

			if( false === $last_tweet ) {

				// Get the widget instance
				$instance = $instance = welt_get_widget_instance( $widget_id );


				// Set the transient for this widget
				$last_tweet = welt_get_latest_tweet( $instance );
			}

			if( $last_tweet && is_array( $last_tweet ) ){

				set_transient('last_tweet_' . $widget_id , $last_tweet, 60 * 5);

				foreach ($last_tweet as $tweet_id) {

					// retrocompatibility
					if( is_object( $tweet_id ) ){
						$tweet_id = $tweet_id->id_str;
					}

					$tweet_html_transient = get_transient('last_tweet_html_' . $tweet_id);

					if( false === $tweet_html_transient ){

						$instance = welt_get_widget_instance( $widget_id );

						$tweet_html_transient = welt_get_tweet_html( $tweet_id, $instance );

						if( $tweet_html_transient ){
							set_transient('last_tweet_html_' . $tweet_id, $tweet_html_transient, ( 24 * WEEK_IN_SECONDS ) ); // 6 mouths
							$tweet_html[ $widget_id ] .= $tweet_html_transient;
						}

					} else {

						// retrocompatibility
						if( is_object( $tweet_html_transient ) ){
							$tweet_html_transient = $tweet_html_transient->html;
						}
						$tweet_html[ $widget_id ] .= $tweet_html_transient;
					}

				}
			}

		}
	}


	echo json_encode( $tweet_html );
	die;
}

add_action('wp_ajax_welt_display_tweets', 'welt_display_tweets');
add_action('wp_ajax_nopriv_welt_display_tweets', 'welt_display_tweets');


/**
 * Return the instance of a widget base on his id
 */
function welt_get_widget_instance( $widget_id ){

	// Get the widget instance
	$all_instance_widget = get_option('widget_welt_last_tweets');

	$widget_real_id = str_replace('welt_last_tweets-', '', $widget_id);

	return $all_instance_widget[$widget_real_id];
}

/**
 * Enqueue welt script and Twitter Script
 */
function welt_enqueue_scripts(){
	// welt
	wp_register_script('welt_twitter_script', '//platform.twitter.com/widgets.js' , array(), '1.1', true );
	wp_register_script('welt_script', plugins_url('/js/welt-scripts.js', __FILE__) , array( 'jquery', 'welt_twitter_script' ), '20140504', true );

}
add_action('wp_enqueue_scripts', 'welt_enqueue_scripts');

function welt_plugin_init() {

	//Files needed for the Twitter authentification
	//Check if TwitterOAuth doesn't already existe
	if( ! class_exists( 'TwitterOAuth' )){

		require_once 'twitteroauth/twitteroauth.php';

	}

  load_plugin_textdomain( 'widget-embed-lastest-tweets', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'welt_plugin_init');


// Option page
require_once 'welt-option.php';


// Add settings link on plugin page
function welt_add_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=welt_options_page">' . __('Settings') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'welt_add_settings_link' );
