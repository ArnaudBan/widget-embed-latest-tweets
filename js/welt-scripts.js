/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */


jQuery(document).ready( function(){

	jQuery('.welt-tweet-wrapper').each( function(){

		var current_widget = jQuery(this);
		var data = {
			action: 'welt_display_tweets',
			widget_id: current_widget.attr('id')
		};

		jQuery.post(
			ajaxurl,
			data,
			function(response) {
				current_widget.append(response);
			}
		);

	});
});
