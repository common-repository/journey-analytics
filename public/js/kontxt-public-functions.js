jQuery(function($) {

        "use strict";

    // check for events (viewProductID, pageID, searchText, reviewTextPresent )
    // loop through API calls to cognitive

    let data = {
        'action'    : 'kontxt_send_event',
        'eventData' : JSON.stringify(kontxtJourneyUserObject)
    };

    jQuery.ajax({
        type: 'post',
        url: kontxtJourneyAjaxObject.ajaxurl,
        security: kontxtJourneyAjaxObject.security,
        data: jQuery.param( data ),
        action: 'kontxt_send_event',
        cache: false,
        success: function (response) {

        }
    });

});