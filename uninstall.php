<?php

/*
 * Widget embed latest Tweets
 *
 * When the plugin is unintall the options are remove
 */


if( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

//Delete the option
delete_option('welt_twitter_http_hreader');
delete_option('welt_twitter_oauth_var');
delete_option('welt_twitter_authentification');