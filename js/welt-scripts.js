/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */

jQuery(document).ready( function(){

	jQuery('.welt-tweet-wrapper').each( function(){

		var current_widget = jQuery(this);

		var widget_id = current_widget.attr('id');
		var widget_data = current_widget.data();
		widget_id = widget_id.replace('welt-', '');

		var data = {
			action: 'welt_display_tweets',
			widget_id: widget_id,
			widget_data: widget_data
		};

		jQuery.post(
			weltAjaxurl,
			data,
			function(response) {
				current_widget.append(response);
				twttr.widgets.load();
			}
		);

	});
});

