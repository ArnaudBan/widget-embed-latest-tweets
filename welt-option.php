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
	add_plugins_page('Widget Embed Latest Tweet', 'Widget Embed Latest Tweet', 'manage_options', 'welt_options_page', 'welt_options_page_display');
}

//Content of the option page
function welt_options_page_display() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Widget Embed Latest Tweet</h2>

		<form action="options.php" method="post">
			<?php
			//Output Error
			settings_errors();

			//Output nonce, action, and option_page
			settings_fields('welt_options_group');

			//Prints out all settings sections added to a particular settings page
			do_settings_sections('welt_options_page');
			?>
			<p class="submit">
				<input name="submit" type="submit" value="<?php _e('Save Changes'); ?>" class="button-primary menu-save" />
			</p>
		</form>

	</div>
	<?php
}

//Define options
add_action('admin_init', 'welt_admin_init');

function welt_admin_init() {
	//register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('welt_options_group', 'welt_twitter_oauth_var', 'welt_twitter_oauth_var_validate');

	//On créer une section dans nos options
	//add_settings_section( $id, $title, $callback, $page );
	add_settings_section('welt_twitter_oauth_section', 'Twitter connection', 'welt_twitter_oauth_section_text', 'welt_options_page');

	//Register a settings field to a settings page and section.
	//add_settings_field( $id, $title, $callback, $page, $section, $args );
	add_settings_field('welt_twitter_consumer_key', 'Twitter consumer Key', 'welt_twitter_consumer_key_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_consumer_secret', 'Twitter consumer Secret', 'welt_twitter_consumer_secret_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_access_token', 'Twitter Access Token', 'welt_twitter_access_token_display', 'welt_options_page', 'welt_twitter_oauth_section');
	add_settings_field('welt_twitter_access_token_secret', 'Twitter Access Token Secret', 'welt_twitter_access_token_secret_display', 'welt_options_page', 'welt_twitter_oauth_section');
}

//Le text au dessus des options
function welt_twitter_oauth_section_text() {
	?>
	<h4><?php _e('Why', 'ab-welt-locales'); ?> ?</h4>
	<p>
		<?php _e('In order to avoid the Twitter\'s API limitation from anonymous request.', 'ab-welt-locales'); ?>
	</p>

	<h4><?php _e('How', 'ab-welt-locales'); ?> ?</h4>

	<p>
		<?php _e('Create a new application with your Twitter account. It is very simple, go to this page :', 'ab-welt-locales'); ?>
		<a href="https://dev.twitter.com/apps" title="dev Twitter apps">https://dev.twitter.com/apps</a>
	</p>
	<p>
		<?php _e('Once your application is created ask to create an access token ( Click the blue button at the bottom of the page of your Twitter application ).', 'ab-welt-locales') ?>
	</p>
	<p>
		<?php _e('You should now have all the information asked bellow !', 'ab-welt-locales') ?>
	</p>
	<?php
}

function welt_twitter_consumer_key_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$consumer_key = $twitter_oauth_var[consumer_key];
	//Attention le "name" du input doit correspondre au nom de l'option
	?>
	<input id='welt_twitter_oauth_var[consumer_key]' name='welt_twitter_oauth_var[consumer_key]' type='text' value='<?php echo $consumer_key; ?>' class="widefat"/>
	<?php
}
function welt_twitter_consumer_secret_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$consumer_secret = $twitter_oauth_var[consumer_secret];
	//Attention le "name" du input doit correspondre au nom de l'option
	?>
	<input id='welt_twitter_oauth_var[consumer_secret]' name='welt_twitter_oauth_var[consumer_secret]' type='text' value='<?php echo $consumer_secret; ?>' class="widefat"/>
	<?php
}
function welt_twitter_access_token_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$token_key = $twitter_oauth_var[token_key];
	//Attention le "name" du input doit correspondre au nom de l'option
	?>
	<input id='welt_twitter_oauth_var[token_key]' name='welt_twitter_oauth_var[token_key]' type='text' value='<?php echo $token_key; ?>' class="widefat"/>
	<?php
}
function welt_twitter_access_token_secret_display() {

	$twitter_oauth_var = get_option('welt_twitter_oauth_var');
	$token_secret = $twitter_oauth_var[token_secret];
	//Attention le "name" du input doit correspondre au nom de l'option
	?>
	<input id='welt_twitter_oauth_var[token_secret]' name='welt_twitter_oauth_var[token_secret]' type='text' value='<?php echo $token_secret; ?>' class="widefat"/>
	<?php
}

function welt_twitter_oauth_var_validate($twitter_variable) {

	$valid_option = $twitter_variable;

	return $valid_option;
}