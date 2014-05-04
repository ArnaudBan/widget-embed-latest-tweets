/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */

window.twttr = (function (d,s,id) {
  var t, js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
  js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
  return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
}(document, "script", "twitter-wjs"));

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
			weltAjaxurl,
			data,
			function(response) {
				current_widget.append(response);
				twttr.widgets.load();
			}
		);

	});
});

