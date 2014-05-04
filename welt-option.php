<?php
/*
 * Widget embed latest Tweets
 * Option page to authentication on twitter
 *
 *
 */
add_action('admin_menu', 'welt_add_option');

function welt_add_option() {
	//add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	add_options_page('Widget Embed Latest Tweet', 'Widget Embed Latest Tweet', 'manage_options', 'welt_options_page', 'welt_options_page_display');
}

//Content of the option page
function welt_options_page_display() {
	?>
	<div class="wrap">

		<h2>Widget Embed Latest Tweet</h2>

		<form action="options.php" method="post">
			<?php

			//Output nonce, action, and option_page
			settings_fields('welt_options_group');

			//Prints out all settings sections added to a particular settings page
			do_settings_sections('welt_options_page');

			submit_button();
			?>
		</form>

	</div>
	<?php
}

//Define options
add_action('admin_init', 'welt_admin_init');

function welt_admin_init() {
	//register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('welt_options_group', 'welt_twitter_oauth_var', 'welt_twitter_oauth_var_validate');

	//add_settings_section( $id, $title, $callback, $page );
	add_settings_section('welt_twitter_oauth_section', __('Twitter connection', 'widget-embed-lastest-tweets'), 'welt_twitter_oauth_section_text', 'welt_options_page');

	//Register a settings field to a settings page and section.
	//add_settings_field( $id, $title, $callback, $page, $section, $args );
	add_settings_field('welt_twitter_consumer_key', 'API key', 'welt_twitter_consumer_key_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_consumer_secret', 'API secret', 'welt_twitter_consumer_secret_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_access_token', 'Access token', 'welt_twitter_access_token_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_access_token_secret', 'Access token secret', 'welt_twitter_access_token_secret_display', 'welt_options_page', 'welt_twitter_oauth_section');
}

//Le text au dessus des options
function welt_twitter_oauth_section_text() {
	?>
	<h4><?php _e('Why', 'widget-embed-lastest-tweets'); ?> ?</h4>
	<p>
		<?php _e('Twitter\'s API version 1.1 require it', 'widget-embed-lastest-tweets'); ?>
	</p>

	<h4><?php _e('How', 'widget-embed-lastest-tweets'); ?> ?</h4>

	<p>
		<?php _e('Create a new application with your Twitter account. It is very simple, go to this page :', 'widget-embed-lastest-tweets'); ?>
		<a href="https://dev.twitter.com/apps" title="dev Twitter apps">https://dev.twitter.com/apps</a>
	</p>
	<p>
		<?php _e('Once your application is created ask to create an access token ( Click the blue button at the bottom of the page of your Twitter application ).', 'widget-embed-lastest-tweets') ?>
	</p>
	<p>
		<?php _e('You should now have all the information asked bellow !', 'widget-embed-lastest-tweets') ?>
	</p>
	<?php
}

function welt_twitter_consumer_key_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$consumer_key = isset( $twitter_oauth_var['consumer_key'] ) ? $twitter_oauth_var['consumer_key'] : '';

	?>
	<input id='welt_twitter_oauth_var[consumer_key]' name='welt_twitter_oauth_var[consumer_key]' type='text' value='<?php echo $consumer_key; ?>' class="widefat"/>
	<?php
}
function welt_twitter_consumer_secret_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$consumer_secret = isset( $twitter_oauth_var['consumer_secret'] ) ? $twitter_oauth_var['consumer_secret'] : '';
	?>
	<input id='welt_twitter_oauth_var[consumer_secret]' name='welt_twitter_oauth_var[consumer_secret]' type='password' value='<?php echo $consumer_secret; ?>' class="widefat"/>
	<?php
}
function welt_twitter_access_token_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$token_key = isset( $twitter_oauth_var['token_key'] ) ? $twitter_oauth_var['token_key'] : '';
	?>
	<input id='welt_twitter_oauth_var[token_key]' name='welt_twitter_oauth_var[token_key]' type='text' value='<?php echo $token_key; ?>' class="widefat"/>
	<?php
}
function welt_twitter_access_token_secret_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$token_secret = isset( $twitter_oauth_var['token_secret'] ) ? $twitter_oauth_var['token_secret'] : '';
	?>
	<input id='welt_twitter_oauth_var[token_secret]' name='welt_twitter_oauth_var[token_secret]' type='text' value='<?php echo $token_secret; ?>' class="widefat"/>
	<?php
}

function welt_twitter_oauth_var_validate($twitter_variable) {

	$valid_option = array();

	foreach ($twitter_variable as $option => $value) {

		if( !empty( $value ) ){

			$valid_option[$option] = sanitize_text_field( $value );
		}

	}

	return $valid_option;
}