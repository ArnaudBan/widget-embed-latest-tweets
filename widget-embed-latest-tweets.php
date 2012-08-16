<?php
/*
 * Plugin Name: Widget embed lastest Tweets
 * Plugin URI: http://www.arnaudbanvillet.com/widget-embed-lastest-tweets
 * Description: A Widget to show your lastest tweets. Use the oEmbed methode and cach. It is simple, elegant and it works.
 * Juste type your user name and the munbers of tweets you want to show.
 * Version: 0.1
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


class Widget_Embed_Lastest_Tweets extends WP_Widget {

	var $defaut = array(
			'nb-tweets' => 3,
			'align'			=> 'none'
	);
	var $align_possible_value = array('left', 'right', 'center', 'none');

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
						'last_tweets', // Base ID
						'Widget embed lastest Tweets', // Name
						array('description' => __('Show your last tweets', 'ab-welt-locales'))// Args
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

		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		$name_user = $instance['user-name'];
		$nb_tweets = ( is_numeric($instance['nb-tweets']) ) ? $instance['nb-tweets'] : 3;
		$maxwidth = ( is_numeric($instance['maxwidth']) ) ? $instance['maxwidth'] : '';


		echo $before_widget;

		if (!empty($title))
			echo $before_title . $title . $after_title;

		if ($name_user) {

			$last_tweet = get_transient('last_tweet');

			if (false === $last_tweet) {

				$options = array(
								'nb_tweets' => $nb_tweets,
								'maxwidth'	=> $maxwidth,
								'align'			=> $instance['align']
							);

				$this->welt_set_tweet_transient($name_user, $options);

				$last_tweet = get_transient('last_tweet');
			}


			foreach ($last_tweet as $tweet) {

				$id = $tweet->id_str;

				$last_tweet_html = get_transient('last_tweet_html_' . $id);

				echo $last_tweet_html->html;
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

		$nb_tweet = strip_tags($new_instance['nb-tweets']);
		$maxwidth = strip_tags($new_instance['maxwidth']);

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['user-name'] = strip_tags($new_instance['user-name']);
		$instance['nb-tweets'] = is_numeric( $nb_tweet ) ? $nb_tweet : '';
		$instance['maxwidth'] = is_numeric( $maxwidth ) ? $maxwidth : '';

		if(in_array($new_instance['align'], $this->align_possible_value ))
			$instance['align'] = $new_instance['align'];

		$options = array(
								'nb_tweets' => $instance['nb-tweets'],
								'maxwidth'	=> $instance['maxwidth'],
								'align'			=> $instance['align']
							);
		$this->welt_set_tweet_transient( $instance['user-name'], $options , true);

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
		if ($instance) {
			$title = esc_attr($instance['title']);
			$user_name = esc_attr($instance['user-name']);
			$nb_tweets = esc_attr($instance['nb-tweets']);
			$maxwidth = esc_attr($instance['maxwidth']);
			$align = $instance['align'];
		} else {
			$title = 'Last Tweet';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('user-name'); ?>"><?php _e('Twitter Usernam :', 'ab-welt-locales') ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('user-name'); ?>" name="<?php echo $this->get_field_name('user-name'); ?>" type="text" value="<?php echo $user_name; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('nb-tweets'); ?>"><?php _e('Number of tweet to dislay :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('nb-tweets'); ?>" name="<?php echo $this->get_field_name('nb-tweets'); ?>" type="number" step="1" min="1" max="20" value="<?php echo $nb_tweets; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('maxwidth'); ?>"><?php _e('Maximum Width :', 'ab-welt-locales') ?></label>
			<input id="<?php echo $this->get_field_id('maxwidth'); ?>" name="<?php echo $this->get_field_name('maxwidth'); ?>" type="number" step="1" min="20" max="1000" value="<?php echo $maxwidth; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('align'); ?>"><?php _e('Align :', 'ab-welt-locales') ?></label>
			<select id="<?php echo $this->get_field_id('align'); ?>" name="<?php echo $this->get_field_name('align'); ?>">
				<?php foreach( $this->align_possible_value as $value ) { ?>
					<option value="<?php echo $value ?>" <?php selected($value, $align, true) ?>><?php echo $value ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
	}

	private function welt_set_tweet_transient( $user_name, $options, $update = false){

		extract( $options );

		$embed_options = '';

		if( !empty( $maxwidth ) )
			$embed_options .= '&maxwidth=' . $maxwidth;
		if( !empty( $align ) )
			$embed_options .= '&align=' . $align;

		$last_tweet = file_get_contents('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $user_name . '&count=' . $nb_tweets . '&include_rts=1');
		$last_tweet = json_decode($last_tweet);

		set_transient('last_tweet', $last_tweet, 60 * 5);

		foreach ($last_tweet as $tweet) {


			if( $update || get_transient('last_tweet_html_' . $id)){

				$id = $tweet->id_str;
				$last_tweet_html = file_get_contents('https://api.twitter.com/1/statuses/oembed.json?id=' . $id . $embed_options);
				$last_tweet_html = json_decode($last_tweet_html);
				set_transient('last_tweet_html_' . $id, $last_tweet_html, 60 * 60 * 24);

			}

		}
	}

}

add_action('widgets_init', create_function('', 'register_widget( "Widget_Embed_Lastest_Tweets" );'));
