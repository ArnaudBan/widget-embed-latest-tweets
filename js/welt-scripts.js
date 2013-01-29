/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */


jQuery(document).ready( function(){

	jQuery('.welt-tweet-wrapper').each( function(){

		var current_widget = jQuery(this);

		var widget_id = current_widget.attr('id');
		widget_id = widget_id.replace('welt-', '');

		var data = {
			action: 'welt_display_tweets',
			widget_id: widget_id
		};

		jQuery.post(
			ajaxurl,
			data,
			function(response) {
				current_widget.append(response);
				//jQuery.getScript('//platform.twitter.com/widgets.js');
			}
		);

	});
});
