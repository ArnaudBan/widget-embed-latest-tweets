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

		jQuery.ajax({
			type: "POST",
			url: welt_ajaxurl,
			data : data
		}).done(function(response) {
				current_widget.append(response);
			}
		);

	});
});
