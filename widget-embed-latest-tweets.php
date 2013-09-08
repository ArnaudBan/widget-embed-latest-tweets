<?php
/*
 * Plugin Name: Widget embed latest Tweets
 * Plugin URI: http://arnaudban.me/blog/portfolio/widget-embed-latest-tweets/
 * Description: A Widget to show your latest Tweets. Use the oEmbed methode and some cache. Visit the option page "Plugins->Widget Embed Last Plugin" to authentify yourself
 * Version: 0.5
 * Author: Arnaud Banvillet
 * Author URI: http://arnaudban.me
 * License: GPL2
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
			'align'            => 'none',
			'hide_thread'      => true,
			'lang'             => 'en',
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
						array('description' => __('Show your latest Tweets', 'ab-welt-locales'))// Args
		);
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

		if ( !empty( $title ) )
			echo $before_title . $title . $after_title;

		if( !empty( $screen_name ) )
			echo '<div id="welt-' . $this->id . '" class="welt-tweet-wrapper"></div>';

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

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['screen_name'] = strip_tags($new_instance['screen_name']);

		$count = strip_tags($new_instance['count']);

		if( is_numeric($count))
			$instance['count'] = $count ;

		$maxwidth = strip_tags($new_instance['maxwidth']);
		if( is_numeric( $maxwidth ) || empty( $maxwidth ))
			$instance['maxwidth'] = $maxwidth;


		if(in_array($new_instance['align'], $this->align_possible_value ))
			$instance['align'] = $new_instance['align'];

		$instance['hide_thread'] = $new_instance['hide_thread'] == 'hide_thread';
		$instance['hide_media'] = $new_instance['hide_media'] == 'hide_media';
		$instance['exclude_replies'] = $new_instance['exclude_replies'] == 'exclude_replies';

		$instance['lang'] = strip_tags($new_instance['lang']);

		//When everythings is check, set the transient
		welt_set_tweet_transient( $this->id, $instance , true );

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

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?> :</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _e('Twitter Username', 'ab-welt-locales') ?> :</label>
			<input class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php if( isset($screen_name) ) echo $screen_name; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of Tweet to display', 'ab-welt-locales') ?> :</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $count; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('maxwidth'); ?>"><?php _e('Width') ?> :</label>
			<input id="<?php echo $this->get_field_id('maxwidth'); ?>" name="<?php echo $this->get_field_name('maxwidth'); ?>" type="number" step="1" min="250" max="550" value="<?php echo $maxwidth; ?>" />
			<br />
			<span class="description"><?php _e('Twitter says :This value is constrained to be between 250 and 550 pixels', 'ab-welt-locales') ?></span>

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
			<label for="<?php echo $this->get_field_id('hide_thread'); ?>"><?php _e('Hide Thread', 'ab-welt-locales') ?> :</label>
			<input id="<?php echo $this->get_field_id('hide_thread'); ?>" name="<?php echo $this->get_field_name('hide_thread'); ?>" type="checkbox" <?php checked( $hide_thread ) ?> value="hide_thread"/>
			<br />
			<span class="description"><?php _e('Hide the original message in the case that the embedded Tweet is a reply', 'ab-welt-locales') ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('hide_media'); ?>"><?php _e('Hide Media', 'ab-welt-locales') ?> :</label>
			<input id="<?php echo $this->get_field_id('hide_media'); ?>" name="<?php echo $this->get_field_name('hide_media'); ?>" type="checkbox" <?php checked( $hide_media ) ?> value="hide_media"/>
			<br />
			<span class="description"><?php _e('Hide the images in the Tweet' , 'ab-welt-locales') ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('exclude_replies'); ?>"><?php _e('Exclude replies', 'ab-welt-locales') ?> :</label>
			<input id="<?php echo $this->get_field_id('exclude_replies'); ?>" name="<?php echo $this->get_field_name('exclude_replies'); ?>" type="checkbox" <?php checked( $exclude_replies ) ?> value="exclude_replies"/>
			<br />
			<span class="description"><?php _e('They will not show but they will count in the number of tweets' , 'ab-welt-locales') ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language', 'ab-welt-locales') ?> :</label>
			<input id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" size="2"/>
			<br />
			<span class="description"><?php _e('Two firsts caractere only. Example : "fr" for french', 'ab-welt-locales') ?></span>
		</p>
		<?php
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
function welt_set_tweet_transient( $widget_id, $options, $update = false){

	$return_value = false;

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');

	//Check if wee use the authentification methode. We need to have all the key and secret.
	if( is_array( $twitter_oauth_var ) && count($twitter_oauth_var) == 4 ){

		$connection = new TwitterOAuth($twitter_oauth_var['consumer_key'], $twitter_oauth_var['consumer_secret'], $twitter_oauth_var['token_key'],$twitter_oauth_var['token_secret']);
		$last_tweet = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json', $options );

		// if there is an error
		if( isset( $last_tweet->errors ) ){

			delete_transient( 'last_tweet_' . $widget_id );
			$return_value = $last_tweet->errors[0]->message;

		// If there is nothing
		} elseif( $last_tweet == false || empty( $last_tweet )){

			delete_transient( 'last_tweet_' . $widget_id );
			$return_value = false;

		// It should be ok
		} else {

			$return_value = $last_tweet;

			set_transient('last_tweet_' . $widget_id , $last_tweet, 60 * 5);

			foreach ($last_tweet as $tweet) {

				$id = $tweet->id_str;


				if( $update || ! get_transient('last_tweet_html_' . $id)){


					$options['id'] = $id;

					if( empty( $maxwidth) ){
						unset( $options['maxwidth']);
					}

					$last_tweet_html = $connection->get('https://api.twitter.com/1.1/statuses/oembed.json', $options);


					set_transient('last_tweet_html_' . $id, $last_tweet_html );

				}
			}
		}
	}

	return $return_value;
}


/**
 * The function callback in ajax to display Tweet
 */
function welt_display_tweets( ){

	$widget_id = $_POST['widget_id'];

	$tweet_html = '';

	$last_tweet = get_transient('last_tweet_' . $widget_id);


	if( false === $last_tweet ) {

		// Get the widget instance
		$all_instance_widget = get_option('widget_welt_last_tweets');

		$widget_real_id = str_replace('welt_last_tweets-', '', $widget_id);

		$instance = $all_instance_widget[$widget_real_id];

		// Set the transient for this widget
		$last_tweet = welt_set_tweet_transient( $widget_id, $instance, false );

	}

	if( is_string( $last_tweet ) ){ // It is a error

		echo $last_tweet;

	} elseif ( $last_tweet != false ){

		foreach ($last_tweet as $tweet) {

			$tweet_id = $tweet->id_str;

			$last_tweet_html = get_transient('last_tweet_html_' . $tweet_id);

			$tweet_html .= $last_tweet_html->html;

		}
	} else {
		$tweet_html = __('Error: Twitter did not respond. Please wait a few minutes and refresh this page.', 'ab-welt-locales');
	}


	echo $tweet_html;
	die;
}

add_action('wp_ajax_welt_display_tweets', 'welt_display_tweets');
add_action('wp_ajax_nopriv_welt_display_tweets', 'welt_display_tweets');


/**
 * Enqueue welt script and Twitter Script
 */
function welt_enqueue_scripts(){
	// welt
	wp_register_script('welt_script', plugins_url('/js/welt-scripts.js', __FILE__) , array( 'jquery' ), '20130129', true );

}
add_action('wp_enqueue_scripts', 'welt_enqueue_scripts');

function welt_plugin_init() {

	//Files needed for the Twitter authentification
	//Check if TwitterOAuth doesn't already existe
	if( ! class_exists( 'TwitterOAuth' )){

		require_once 'twitteroauth/twitteroauth.php';

	}

  load_plugin_textdomain( 'ab-welt-locales', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'welt_plugin_init');


// Option page
require_once 'welt-option.php';


// Add settings link on plugin page
function welt_add_settings_link($links) {
  $settings_link = '<a href="plugins.php?page=welt_options_page">' . __('Settings') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'welt_add_settings_link' );
