jQuery(function($) {

    "use strict";

    let navTabs = jQuery('#kontxt-settings-navigation').children('.nav-tab-wrapper');
    let tabIndex = null;

    navTabs.children().each(function() {

        $(this).on('click', function (evt) {

            evt.preventDefault();

            // If this tab is not active...
            if (!$(this).hasClass('nav-tab-active')) {

                // Unmark the current tab and mark the new one as active
                $('.nav-tab-active').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                // Save the index of the tab that's just been marked as active. It will be 0 - 2.
                tabIndex = jQuery(this).index();

                // Hide the old active content
                $('#kontxt-settings-navigation')
                    .children('div:not( .inside.hidden )')
                    .addClass('hidden');

                $('#kontxt-settings-navigation')
                    .children('div:nth-child(' + ( tabIndex ) + ')')
                    .addClass('hidden');

                // And display the new content
                $('#kontxt-settings-navigation')
                    .children('div:nth-child( ' + ( tabIndex + 2 ) + ')')
                    .removeClass('hidden');

                window.dispatchEvent(new Event('resize'));

            }

        });
    });

    $( "#date_from" ).datepicker({ dateFormat: "yy-mm-dd" }).datepicker("setDate", "-7d");

    $( "#date_to" ).datepicker({dateFormat: "yy-mm-dd"}).datepicker('setDate', new Date());

    // capture date range redraw
    jQuery( '#kontxt-events-date' ).on( 'click',  function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let dimension =  jQuery( '#dimension' ).val();
        let date_from =  jQuery( '#date_from' ).val();
        let date_to =  jQuery( '#date_to' ).val();
        let filter = jQuery( '#filter').val();

        switch( dimension ) {

            case "journey":
                kontxtJourney('journeyEvents', date_from, date_to, filter);
                break;

            case "journeyNodeCount":
                kontxtJourneyPoints(dimension, date_from, date_to, filter );
                break;

            case "journeyFunnel":
                kontxtJourneyFunnel( 'journeyNodeCount' , date_from, date_to)
                break;

        }

    });


    // capture intent filter
    jQuery( '#kontxt-intent-filter' ).on( 'click', function( e ) {
        e.preventDefault();

        jQuery('#spinner').removeClass('is-inactive').addClass('is-active');

        let filter      =  jQuery( '#filter' ).val();
        let date_from;
        let date_to;

        if ( Date.parse( jQuery( '#date_from' ).val() ) ) {
            date_from =  jQuery( '#date_from' ).val();
        }
        if ( Date.parse( jQuery( '#date_to' ).val() ) ) {
            date_to =  jQuery( '#date_to' ).val();
        }

        if( filter ) {
            kontxtJourney( 'journeyEventsByIntent', date_from, date_to, filter );

        } else {
            kontxtJourney('journeyEvents', date_from, date_to, filter);
        }
    });

});

function kontxtJourneyFunnel( dimension, date_from, date_to) {

    "use strict";

    let funnelKeys  = [];
    let promises = [];

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery.each( jQuery( '#journeyKeysDrop' ).find('li' ), function() {

        let nodeKey = jQuery( this ).attr('id');

        let data = jQuery.param({
            'action':       'kontxt_analyze_results',
            'apikey':       kontxtJourneyUserObject.apikey,
            'filter':       nodeKey,
            'dimension':    dimension,
            'from_date':    date_from,
            'to_date':      date_to
        });

        const security = kontxtJourneyUserObject.security;

        let request = jQuery.ajax({
            type: 'post',
            url: kontxtJourneyUserObject.ajaxurl,
            security: security,
            data: data,
            cache: false,
            success: function (response) {
                funnelKeys[nodeKey] = JSON.parse( response );
            }
        });
        promises.push( request );
    });

    jQuery.when.apply( null, promises ).done( function() {

       // console.log(funnelKeys);

        let funnelDataKeys = [];
        let funnelDataValues = [];

        // now we need to preserve original order because ajax
        jQuery.each( jQuery( '#journeyKeysDrop' ).find('li' ), function() {

            funnelDataKeys.push( jQuery( this ).attr( 'id' ).replace(/_/g, ' ') );

            if(  typeof funnelKeys[jQuery( this ).attr( 'id' )][0] == 'undefined' ) {
                funnelDataValues.push(0);
            } else {
                funnelDataValues.push( funnelKeys[jQuery( this ).attr( 'id' )][0].event_key_count );
            }

        });

        let data = [{
            type: 'funnel',
            y: funnelDataKeys,
            x: funnelDataValues,
            textposition: 'inside',
            textinfo: 'value+percent initial',
            hoverinfo: 'percent total+x',
            opacity: 0.65,
            marker: {
                color: ["59D4E8", "DDB6C6", "A696C8", "67EACA", "94D2E6"],
                line: {
                    width: 1

                }
            },
            connector: {
                line: {
                    color: "royalblue",
                    dash: "dot",
                    width: 3
                }
            }
        }];

        const layout = {
            autosize: true,
            font: {
                size: 11
            }
        }

        const config = {
            displayModeBar: true,
            responsive: true,
            displaylogo: false
        }

        jQuery('#journey-results-success').show();

        Plotly.react( 'journey_funnel_chart', data, layout, config );

        jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

    });

}


function kontxtJourneyPoints( dimension, date_from, date_to, filter) {

    "use strict";

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    let data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtJourneyUserObject.apikey,
        'dimension':    dimension,
        'filter':       filter,
        'from_date':    date_from,
        'to_date':      date_to
    });

    const security = kontxtJourneyUserObject.security;

    jQuery.ajax({
        type: 'post',
        url: kontxtJourneyUserObject.ajaxurl,
        security: security,
        data: data,
        cache: false,
        success: function(response) {

            if( response.status === 'error' ) {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            let jsonResponse = JSON.parse(response);

            let journeyPointsLabels = Object.keys(jsonResponse).map((key) => jsonResponse[key].event_key.replace(/_/g, ' '));
            let percentPoints = Object.keys(jsonResponse).map((key) => jsonResponse[key].percent);
            let sumPoints = Object.keys(jsonResponse).map((key) => jsonResponse[key].event_key_count);

            let trace1 = {
                x: percentPoints,
                y: journeyPointsLabels,
                xaxis: 'x1',
                yaxis: 'y1',
                type: 'bar',
                marker: {
                    color: 'rgba(50,171,96,0.6)',
                    line: {
                        color: 'rgba(50,171,96,1.0)',
                        width: 1
                    }
                },
                name: 'Percentage of journey point traffic',
                orientation: 'h',
                hoverinfo: 'x'
            };

            let trace2 = {
                x: sumPoints,
                y: journeyPointsLabels,
                xaxis: 'x2',
                yaxis: 'y1',
                mode: 'lines+markers',
                line: {
                    color: 'rgb(128,0,128)'
                },
                name: 'Total journey point traffic',
                hoverinfo: 'x'
            };

            let data = [trace1, trace2];

            let layout = {
                xaxis1: {
                    autorange: true,
                    domain: [0, 0.5],
                    zeroline: false,
                    showline: false,
                    showticklabels: true,
                    tickformat: '%',
                    showgrid: true,
                    autotick: true
                },
                xaxis2: {
                    autorange: true,
                    domain: [0.5, 1],
                    zeroline: false,
                    showline: false,
                    showticklabels: true,
                    showgrid: true,
                    side: 'top',
                    autotick: true
                },
                yaxis: {
                    automargin: true
                },
                legend: {
                    x: 0.029,
                    y: 1.238,
                    font: {
                        size: 10
                    }
                },
                margin: {
                    l: 200,
                    r: 20,
                    t: 20,
                    b: 70
                },
                autosize: true,
                hovermode: 'closest'

            };

            const config = {
                displayModeBar: true,
                responsive: true,
                displaylogo: false
            }

            Plotly.react(dimension + '_results_chart', data, layout, config);

            jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

        },
        error: function(response) {
            jQuery('#kontxt-results-status').html(response.message);
            return false;
        }

    });

    return false;
}

function kontxtJourney( dimension, date_from, date_to, filter ) {

    "use strict";

    jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

    jQuery('#kontxt-analyze-results-status').hide();

    // prepare data for posting

    let data = jQuery.param({
        'action':       'kontxt_analyze_results',
        'apikey':       kontxtJourneyUserObject.apikey,
        'service':      'events',
        'filter':       filter,
        'from_date':    date_from,
        'to_date':      date_to
    });

    const security = kontxtJourneyUserObject.security;

    let layout;
    let contentTable;

    jQuery.ajax({
        type: 'post',
        url: kontxtJourneyUserObject.ajaxurl,
        security: security,
        data: data + '&dimension=journeyLabels',
        cache: false,
        success: function (response) {

            if (response.status === 'error') {
                jQuery('#kontxt-analyze-results-status').html(response.message).show();
                return false;
            }

            let jsonResponseLabels = JSON.parse(response);

            jQuery.ajax({
                type: 'post',
                url: kontxtJourneyUserObject.ajaxurl,
                security: security,
                data: data + '&dimension=' + dimension,
                cache: false,
                success: function (response) {

                    if (response.status === 'error') {
                        jQuery('#kontxt-analyze-results-status').html(response.message).show();
                        return false;
                    }

                    let jsonResponseEvents = JSON.parse(response);

                    let eventLabels     = Object.keys(jsonResponseLabels).map((key) => jsonResponseLabels[key].event_key_label.replace(/_/g, ' '));
                    let eventSource     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].event_source);
                    let eventTarget     = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].event_target);
                    let eventFlowValue  = Object.keys(jsonResponseEvents).map((key) => jsonResponseEvents[key].flow_value);

                    let data = [{
                        type: "sankey",
                        arrangement: "snap",
                        valueformat: ".0f",
                        valuesuffix: " sessions",
                        orientation: "h",
                        node: {
                            pad: 25,
                            thickness: 25,
                            line: {
                                width: 0
                            },
                            label: eventLabels
                        },

                        link: {
                            source: eventSource,
                            target: eventTarget,
                            value: eventFlowValue
                        }
                    }];

                    const layout = {
                        autosize: true,
                        font: {
                            size: 11
                        },
                        yaxis: {
                            automargin: true
                        },
                        hovermode: true,
                        colorway : ['#EF5350', '#EC407A', '#AB47BC', '#7E57C2', '#5C6BC0', '#42A5F5', '#26A69A', '#9CCC65', '#FFCA28', '#FF7043', '#78909C']
                    };

                    const config = {
                        displayModeBar: true,
                        responsive: true,
                        displaylogo: false
                    }

                    Plotly.react( 'journey_results_chart', data, layout, config ).then( function( kontxt_ja_details ) {

                        jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');

                        kontxt_ja_details.on('plotly_click', function( data ) {

                            jQuery('#spinner-analyze').removeClass('is-inactive').addClass('is-active');

                            let nodeLabel    = ( data.points[0].target.label ).replace( ' ', '_' );
                            let dateFrom     = jQuery( '#date_from' ).val();
                            let dateTo       = jQuery( '#date_to' ).val();
                            let excludeNodes = [ 'one page and out', 'localhost', 'page_result_set', 'session_abandoned' ];

                            if( nodeLabel && ! excludeNodes.includes( nodeLabel ) ) {

                                jQuery('#journey_node_details_box').show();

                                let data = jQuery.param({
                                    'action': 'kontxt_analyze_results',
                                    'apikey': kontxtJourneyUserObject.apikey,
                                    'dimension': 'journeyNode',
                                    'filter': nodeLabel,
                                    'from_date': dateFrom,
                                    'to_date': dateTo
                                });

                                jQuery.ajax({
                                    type: 'post',
                                    url: kontxtJourneyUserObject.ajaxurl,
                                    security: security,
                                    data: data,
                                    cache: false,
                                    success: function (response) {

                                        if (response.status === 'error') {
                                            jQuery('#kontxt-analyze-results-status').html(response.message).show();
                                            return false;
                                        }

                                        let jsonResponse = JSON.parse( response );


                                        contentTable = '<table id="journey_node_results" class="wp-list-table widefat fixed striped posts">' +
                                            '               <thead>' +
                                            '                   <tr>' +
                                            '                       <th>Session Data for <strong>' + nodeLabel + '</strong></th>' +
                                            '                       <th>URL Referrer</th>' +
                                            '                       <th>Count</th>' +
                                            '                   </tr>' +
                                            '               </thead>' +
                                            '               <tbody>';

                                        jsonResponse.forEach(function (element) {

                                            let eventValue = JSON.parse( element.event_value);
                                            let eventValueElement = null;

                                            switch( nodeLabel ) {

                                                case 'shop_page_product':
                                                    eventValueElement = eventValue.view_product_name;
                                                    break;

                                                case 'blog_post':
                                                case 'site_page':
                                                    eventValueElement = eventValue.title;
                                                    break;

                                                case 'shop_page_category':
                                                    eventValueElement = eventValue.view_category_name;
                                                    break;

                                                case 'shop_page_home':
                                                case 'site_home':
                                                    eventValueElement = eventValue.page_name;
                                                    break;

                                                case 'no_search_results':
                                                case 'search_query':
                                                    eventValueElement = eventValue.search_query;
                                                    break;

                                                case 'order_received':
                                                    eventValueElement = 'Order ID: ' + eventValue.order_id + ' Order Value: $' + eventValue.order_total;
                                                    break;

                                                case 'forum_topic_content':
                                                    eventValueElement = eventValue.forum_content;
                                                    break;

                                                case 'forum_page':
                                                    eventValueElement = eventValue.title;
                                                    break;

                                                case 'product_comment_submitted':
                                                case 'post_comment_submitted':
                                                case 'page_comment_submitted':
                                                    eventValueElement = eventValue.comment_text;
                                                    break;

                                                case 'contact_form_submitted':
                                                    eventValueElement = eventValue.contact_form_message;
                                                    break;

                                                case 'cart_add':
                                                    eventValueElement = Object.values(eventValue)[0].cart_product_name;
                                                    break;

                                                case 'user_register':
                                                    eventValueElement = eventValue.user_id;
                                                    break;

                                            }

                                            contentTable += '<tr><td>' + eventValueElement + '</td>';
                                            contentTable += '<td>' + ( eventValue.http_referrer == null ? 'N/A' : eventValue.http_referrer ) + '</td>';
                                            contentTable += '<td>' + element.event_count + '</td></tr>';

                                        });

                                        contentTable += '</tbody></table>';

                                        jQuery( '#journey_node_details_table' ).html( contentTable );

                                    }

                                });

                                jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');


                            } else {
                                jQuery('#spinner-analyze').removeClass('is-active').addClass('is-inactive');
                            }

                        })

                    } );

                }
            });
        }
    });
}

