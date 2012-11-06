=== Plugin Name ===
Contributors: ArnaudBan
Tags: twitter, widget, embed
Requires at least: 3
Tested up to: 3.4.2
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Widget to show your latest tweets.
Just type your Twitter username.

== Description ==

A Widget to show your latest tweets. Use the oEmbed methode and some cache. It is simple, elegant and it works.

You can authentified your widget and get rid of the Twitter's API limitation from anonymous request. See the option page of the plugin : plugins -> Widget Embed Latest Tweets

Options :

* number of tweet to display
* Maximum width
* Alignment (left, right, center, none)
* Choose your language
* Hide the original message in the case that the embedded Tweet is a reply

Note that with the oEmbed method you can not customise easily the display of your tweet.
However it assure you that all the twitter's recomandation to display your tweet are respected, they are providing the code !

If you want to help [the code is also on github](https://github.com/ArnaudBan/widget-embed-latest-tweets)

== Screenshots ==

1. Here is an example of how the tweets displays
2. The admin part of the widget

== Installation ==


1. Upload `/widget-embed-latest-tweets/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Widget page, drag and drop the "Widget embed lastest Tweets" widget where you want it to show
4. Enter at least your Twitter username

== Frequently Asked Questions ==

Do not hesitate to ask questions on the forum !

= Is it possible to modifying link color ? =

Sorry no. With the embed method from Twitter you can't really customise the display of your tweet.

= Can the tweet's width be under 250px ? =

No. Twitter says : This value [the width] is constrained to be between 250 and 550 pixels

== Changelog ==

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