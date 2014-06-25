/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */

jQuery(document).ready( function(){

    var widget_ids = [];

    jQuery('.welt-tweet-wrapper').each( function(){

        var current_widget = jQuery(this);

        var widget_id = current_widget.attr('id');
        var widget_data = current_widget.data();
        widget_id = widget_id.replace('welt-', '');
        widget_ids.push(widget_id);

        var data = {
            action: 'welt_display_tweets',
            widget_id: widget_id,
            widget_data: widget_data
        };

        if(!jQuery.isEmptyObject(widget_data)) {
            jQuery.post(
                weltAjaxurl,
                data,
                function(response) {
                    current_widget.append(response);
                    twttr.widgets.load();
                }
            );

            widget_ids = [];
        }

    });

    if(widget_ids.length > 0) {

        var data = {
            action: 'welt_display_tweets',
            widget_id: widget_ids
        };

        jQuery.post(
            weltAjaxurl,
            data,
            function(response) {

                var newElements = jQuery('<div/>').html(response);

                jQuery.each(widget_ids, function(index, widget_id) {

                    var full_widget_id_selector = '#welt-' + widget_id;
                    jQuery(full_widget_id_selector).html(newElements.find(full_widget_id_selector).first().html());

                });

                twttr.widgets.load();
            }
        );

    }
});

