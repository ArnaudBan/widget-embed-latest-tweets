<?php
/*
 * Plugin Name: Widget embed latest Tweets
 * Plugin URI: http://arnaudban.me/blog/portfolio/widget-embed-latest-tweets/
 * Description: A Widget to show your latest Tweets. Use the oEmbed methode and some cache. Visit the option page "Plugins->Widget Embed Last Plugin" to authentify yourself
 * Version: 0.6.4
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


// Include au php files in the "inc" folder
foreach ( glob( plugin_dir_path( __FILE__ ) . "/inc/*.php" ) as $filename ) {
	require_once $filename;
}

add_action( 'widgets_init', create_function( '', 'register_widget( "Widget_Embed_Latest_Tweets" );' ) );


/**
 * get the lastest tweet
 */
function welt_get_latest_tweet( $options ) {

	$user_timeline_oaut = new WP_Twitter_Oauth( 'statuses/user_timeline', $options );

	$timeline = $user_timeline_oaut->get_infos();

	// If the connection is ok let get the twitter information
	if ( is_array( $timeline ) ) {


		$return_value = array();
		foreach ( $timeline as $tweet ) {
			$return_value[] = $tweet->id_str;
		}

	} else {

		$return_value = $timeline;

	}

	return $return_value;
}

function welt_get_tweet_html( $tweet_id, $options ) {

	$options['id']          = $tweet_id;
	$options['omit_script'] = TRUE;

	if ( empty( $maxwidth ) ) {
		unset( $options['maxwidth'] );
	}

	$oembed_oaut = new WP_Twitter_Oauth( 'statuses/oembed', $options );

	$oembed = $oembed_oaut->get_infos();

	// If the connection is ok let get the twitter information
	$return_value = is_string( $oembed ) ? $oembed : $oembed->html;


	return $return_value;
}


/**
 * The function callback in ajax to display Tweet
 */
function welt_display_tweets() {

	$all_welt_widgets = $_POST['all_welt_widgets'];

	$tweet_html = array();

	foreach ( $all_welt_widgets as $welt_widget ) {

		$widget_id   = $welt_widget['widget_id'];
		$widget_data = isset( $welt_widget['widget_data'] ) && ! empty( $welt_widget['widget_data'] ) ? $welt_widget['widget_data'] : FALSE;

		$tweet_html[ $widget_id ] = '';

		// In preview mode we passe all instance in data html attrribut
		$instance = $widget_data ? $widget_data : welt_get_widget_instance( $widget_id );

		$last_tweet = welt_get_latest_tweet( $instance );

		foreach ( $last_tweet as $tweet_id ) {

			$tweet_html[ $widget_id ] .= welt_get_tweet_html( $tweet_id, $instance );

		}
	}

	echo json_encode( $tweet_html );
	die;
}

add_action( 'wp_ajax_welt_display_tweets', 'welt_display_tweets' );
add_action( 'wp_ajax_nopriv_welt_display_tweets', 'welt_display_tweets' );


/**
 * Return the instance of a widget base on his id
 */
function welt_get_widget_instance( $widget_id ) {

	// Get the widget instance
	$all_instance_widget = get_option( 'widget_welt_last_tweets' );

	$widget_real_id = str_replace( 'welt_last_tweets-', '', $widget_id );

	return $all_instance_widget[ $widget_real_id ];
}

/**
 * Enqueue welt script and Twitter Script
 */
function welt_enqueue_scripts() {
	// welt
	wp_register_script( 'welt_twitter_script', 'https://platform.twitter.com/widgets.js', array(), '1.1', true );
	wp_register_script( 'welt_script', plugins_url( '/js/welt-scripts.js', __FILE__ ), array(
		'welt_twitter_script'
	), '0.7', true );

}

add_action( 'wp_enqueue_scripts', 'welt_enqueue_scripts', 20 );

function welt_plugin_init() {


	load_plugin_textdomain( 'widget-embed-lastest-tweets', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'welt_plugin_init' );


// Option page
require_once 'welt-option.php';


// Add settings link on plugin page
function welt_add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=welt_options_page">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'welt_add_settings_link' );
