=== Plugin Name ===
Contributors: ArnaudBan
Tags: twitter, widget, embed
Requires at least: 3.5
Tested up to: 3.9
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Widget to show your latest Tweets.
Visit the option page "Settings->Widget Embed Latest Tweet" to authentify yourself


== Description ==

A Widget to show your latest tweets. Use the oEmbed methode and some cache. It is simple, elegant and it works.

This plugin uses the Twitter API version 1.1. You have to authentify yourself !
Visit the option page "Settings->Widget Embed Latest Tweet" to do so.

Options :

* Number of Tweet to display
* Maximum width
* Alignment (left, right, center, none)
* Choose your language
* Hide the original message in the case that the embedded Tweet is a reply
* Hide images in the Tweet

Note that with the oEmbed method you can not customise easily the display of your Tweet.
However, the oEmbed method ensures you that all Twitter's recommendations for displaying your tweet are respected, they are providing the code !

If you want to help [the code is also on github](https://github.com/ArnaudBan/widget-embed-latest-tweets)

== Screenshots ==

1. Here is an example of how the Tweets displays
2. The admin part of the widget

== Installation ==


1. Upload `/widget-embed-latest-tweets/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the option page "Settings->Widget Embed Latest Tweet" and follow the instructions
4. Go to the Widget page, drag and drop the "Widget embed lastest Tweets" widget where you want it to show
5. Enter at least your Twitter username

== Frequently Asked Questions ==

Do not hesitate to ask questions on the forum !

= Is it possible to modifying link color ? =

Sorry no. With the embed method from Twitter you can't really customise the display of your tweet.

= Can the tweet's width be under 250px ? =

No. Twitter says : This value [the width] is constrained to be between 250 and 550 pixels

== Changelog ==

= 0.6.2 =

* Check if the new Widget::is_preview method exist to avoid fatal error

= 0.6 =

* Change text domaine to be ready for wordpress.org "language packs"
* Less scripts downloded
* Make the widget usable with the widget preview manager

= 0.5 =

* French translation
* Welt's Scripts only when necessary
* Add a "setting" link on the plugins list page

= 0.4.1 =

* Add 'exclude_replies' option
* typo fixes

= 0.4 =

**Importante update**
Be careful, the plugin uses the new API version 1.1
This mean that you have to authentify yourself

* update to API version 1.1
* use AJAX to load the page faster

= 0.3.7 =

* Just some typo fixes

= 0.3.6 =

* fix : You can now show two instance of the widget with different usernames

= 0.3.5 =

* fix : Still showing undefined variables in debug mode

= 0.3.4 =

* fix : Showing lots of undefined variables in this widget in debug mode
* new : The screenshot are no longer in the zip files : http://make.wordpress.org/plugins/2012/09/13/last-december-we-added-header-images-to-the/

= 0.3.3 =

* fix : you can now choose to leave the width empty
* fix : Activation issue when another plugin use the same twitter authentication library
* new FAQ

= 0.3.2 =

* Debug : new Tweets not showing

= 0.3.1 =

* Debug the 0.3 version, sorry for that
* Add the uninstall.php file for a clean unistallation of the plugin

= 0.3 =

* You can authentified your widget and get read of the Twitter's API limitation from anonymous request
* Choose your language
* Hide the original message in the case that the embedded Tweet is a reply

= 0.2 =

* Don't show Warning in case of error

= 0.1 =

* This is the first version.
* Choose the number of tweet, the maximum width and alignment

== Upgrade Notice ==

= 0.2 =
Don't show Warning in case of error

= 0.1 =
This is the first version. Try it and give me your impressions.