<?php
/*
 * Plugin Name: Widget embed latest Tweets
 * Plugin URI: http://www.arnaudbanvillet.com/blog/portfolio/widget-embed-latest-tweets/
 * Description: A Widget to show your latest tweets. Use the oEmbed methode and some cache. Just type your user name and the numbers of tweets you want to show.
 * Version: 0.3.6
 * Author: Arnaud Banvillet
 * Author URI: http://www.arnaudbanvillet.com
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
			'title'				=> 'Last Tweet',
			'count'				=> 3,
			'align'				=> 'none',
			'hide_thread' => true,
			'lang'				=> 'en',
			'include_rts'	=> true
	);

	var $align_possible_value = array('none', 'left', 'right', 'center');

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
						'last_tweets', // Base ID
						'Widget embed latest Tweets', // Name
						array('description' => __('Show your latest tweets', 'ab-welt-locales'))// Args
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

		$instance = wp_parse_args($instance, $this->defaut);

		extract($args);
		extract($instance);

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if (!empty($title))
			echo $before_title . $title . $after_title;

		if( !empty( $screen_name ) ){

			$last_tweet = get_transient('last_tweet_' . $this->id);

			if (false === $last_tweet) {


				$this->welt_set_tweet_transient( $instance, false );


				$last_tweet = get_transient('last_tweet_' .$this->id);

			}

			if( $last_tweet != false ){

				foreach ($last_tweet as $tweet) {

					$tweet_id = $tweet->id_str;

					$last_tweet_html = get_transient('last_tweet_html_' . $tweet_id);

					echo $last_tweet_html->html;
				}
			} else {
				_e('Error: Twitter did not respond. Please wait a few minutes and refresh this page.', 'ab-welt-locales');
			}
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

		$instance['lang'] = strip_tags($new_instance['lang']);

		$this->welt_set_tweet_transient( $instance , true );

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
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _e('Twitter Usernam :', 'ab-welt-locales') ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php echo $screen_name; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of tweet to dislay :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $count; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('maxwidth'); ?>"><?php _e('Width :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('maxwidth'); ?>" name="<?php echo $this->get_field_name('maxwidth'); ?>" type="number" step="1" min="250" max="550" value="<?php echo $maxwidth; ?>" />
			<br />
			<span class="description"><?php _e('Twitter says :This value is constrained to be between 250 and 550 pixels') ?></span>

		</p>

		<p>
			<label for="<?php echo $this->get_field_id('align'); ?>"><?php _e('Align :', 'ab-welt-locales') ?></label>
			<select id="<?php echo $this->get_field_id('align'); ?>" name="<?php echo $this->get_field_name('align'); ?>">
				<?php foreach( $this->align_possible_value as $value ) { ?>
					<option value="<?php echo $value ?>" <?php selected($value, $align, true) ?>><?php echo $value ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('hide_thread'); ?>"><?php _e('Hide Thread :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('hide_thread'); ?>" name="<?php echo $this->get_field_name('hide_thread'); ?>" type="checkbox" <?php checked( $hide_thread ) ?> value="hide_thread"/>
			<br />
			<span class="description"><?php _e('Hide the original message in the case that the embedded Tweet is a reply') ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" size="2"/>
			<br />
			<span class="description"><?php _e('Two firsts caractere only. Example : "fr" for french') ?></span>
		</p>
		<?php if( get_option('welt_twitter_authentification') ) { ?>
			<p class="description">
				<?php _e('You are authentified', 'ab-welt-locales'); ?>
			</p>
		<?php
		}
	}

	/**
	 * Cache the twitter json file. Twitter say "Store API responses in your application or on your site"
	 * And the page load faster with this cache.
	 *
	 * @param array $options
	 * @param boolean $update
	 */
	private function welt_set_tweet_transient( $options, $update = false){

		extract($options);

		$twitter_oauth_var = get_option('welt_twitter_oauth_var');


		//Check if wee use the authentification methode. We need to have all the key and secret.
		$oauth_methode = ($twitter_oauth_var == false) ? false : ! in_array("", $twitter_oauth_var);


		//The authentification methode
		if( $oauth_methode ){
			$connection = new TwitterOAuth($twitter_oauth_var['consumer_key'], $twitter_oauth_var['consumer_secret'], $twitter_oauth_var['token_key'],$twitter_oauth_var['token_secret']);
			$last_tweet = $connection->get('http://api.twitter.com/1/statuses/user_timeline.json', $options );

		} else {
			// We use the GET statuses/user_timeline to get the latest tweet
			// https://dev.twitter.com/docs/api/1/get/statuses/oembed
			$last_tweet = @file_get_contents('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $screen_name . '&count=' . $count . '&include_rts=1');
			$last_tweet = json_decode($last_tweet);
		}

		if( $last_tweet == false || empty( $last_tweet )){
			delete_transient( 'last_tweet' );
			return;
		}

		set_transient('last_tweet_' . $this->id , $last_tweet, 60 * 5);

		foreach ($last_tweet as $tweet) {

			$id = $tweet->id_str;


			if( $update || ! get_transient('last_tweet_html_' . $id)){


				if( $oauth_methode ){

					$options['id'] = $id;

					if( empty( $maxwidth) ){
						unset( $options['maxwidth']);
					}

					$last_tweet_html = $connection->get('https://api.twitter.com/1/statuses/oembed.json', $options);


					update_option('welt_twitter_authentification', true);

				} else {

					// We use the GET statuses/oembed API to get the html to display
					// https://dev.twitter.com/docs/api/1/get/statuses/oembed
					$option_string = 'id=' . $id . '&align=' . $align . '&hide_thread='. $hide_thread .'&lang=' . $lang;

					if( is_numeric( $maxwidth) ){
						$option_string .= '&maxwidth=' . $maxwidth;
					}

					$last_tweet_html = @file_get_contents('https://api.twitter.com/1/statuses/oembed.json?' . $option_string);
					$last_tweet_html = json_decode($last_tweet_html);

					update_option('welt_twitter_authentification', false);
				}

				set_transient('last_tweet_html_' . $id, $last_tweet_html, 60 * 60 * 24);

			}

		}

	}
}

add_action('widgets_init', create_function('', 'register_widget( "Widget_Embed_Latest_Tweets" );'));

//Files needed for the Twitter authentification
//Check if TwitterOAuth doesn't already existe
if( ! class_exists( 'TwitterOAuth' )){

	require_once 'twitteroauth/twitteroauth.php';

}

require_once 'welt-option.php';