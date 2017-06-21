/*
 * Widget embed latest Tweets
 * The script to load tweet in ajax
 */

var weltGetTheWidgetContent =  function(){

	var postParam = 'action=welt_display_tweets';

	var weltTweetWrapper = document.querySelectorAll( '.welt-tweet-wrapper' );
    weltTweetWrapper.forEach( function( current_widget, index ){

        var widget_id = current_widget.getAttribute('id');
        var widget_data = current_widget.dataset;
        widget_id = widget_id.replace('welt-', '');

        postParam += "&all_welt_widgets["+ index +"][widget_id]=" + widget_id;

        if( widget_data ){

            for( var key in widget_data ){

                postParam += "&all_welt_widgets["+ index +"][widget_data]["+ key +"]=" + widget_data[key];

            }
        }
    });



    var request = new XMLHttpRequest();
    request.open('POST', weltAjaxurl, true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
            // Success!
            var response = JSON.parse(request.responseText);

            for( var i in response ){

                if( response.hasOwnProperty( i ) ){

                    var widget_selector =  'welt-' + i;
                    var widget = document.getElementById( widget_selector );
                    widget.innerHTML = response[i];
                }

            }

            window.twttr.widgets.load();
        }
    };

    request.send( postParam );

}();

