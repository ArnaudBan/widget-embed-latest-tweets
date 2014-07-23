/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */

jQuery(document).ready( function(){

	var all_welt_widgets = [];

	jQuery('.welt-tweet-wrapper').each( function(){

		var current_widget = jQuery(this);

		var widget_id = current_widget.attr('id');
		var widget_data = current_widget.data();
		widget_id = widget_id.replace('welt-', '');

		all_welt_widgets.push( { widget_id : widget_id, widget_data : widget_data } );

	});


	var data = {
		action: 'welt_display_tweets',
		all_welt_widgets: all_welt_widgets,
	};

	jQuery.post(
		weltAjaxurl,
		data,
		function(response) {

			if( response ){
				response = JSON.parse( response );

				for( var i in response ){

					var widget_selector =  '#welt-' + i;
					jQuery( widget_selector ).append( response[i] );
				}

				twttr.widgets.load();
			}
		}
	);
});

