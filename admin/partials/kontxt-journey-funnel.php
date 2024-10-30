<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

$allowed_html = get_option( 'kontxt_allowable_html' );

include_once 'kontxt-banner.php';

?>

<div class="wrap">

    <div id="spinner-analyze" class="spinner is-inactive" style="float: right;"></div>

    <h2>Journey Funnel (Beta)</h2>

    <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">

        <input id="dimension" name="dimension" type="hidden" value="journeyFunnel" />

        <div id="kontxt-input-text">

            <div>
                Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
            </div>
        </div>

        <div class="journeyContainer">
            <div id="journeyKeyGroupLeft">
                <h3><?php esc_attr_e( 'Drag a journey waypoint', 'KONTXT' ); ?></h3>
                <ul id="journeyKeysCore" class="droptrue">
                    Core WordPress Events
                    <li class="ui-state-default" data-arrange="1" id="site_home"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Site home view</li>
                    <li class="ui-state-default" data-arrange="2" id="blog_post"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Blog post view</li>
                    <li class="ui-state-default" data-arrange="3" id="site_page"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Site page view</li>
                    <li class="ui-state-default" data-arrange="4" id="user_register"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> User register</li>
                    <li class="ui-state-default" data-arrange="5" id="page_result_set"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Page result set</li>
                    <li class="ui-state-default" data-arrange="6" id="post_comment_submitted"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Blog post comment submit</li>
                    <li class="ui-state-default" data-arrange="7" id="search_query"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Search query</li>
                    <li class="ui-state-default" data-arrange="8" id="no_search_results"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> No search results found</li>
                </ul>

                <ul id="journeyKeysForm" class="droptrue">
                    Contact Form, Gravity Form Events
                    <li class="ui-state-default" data-arrange="9" id="contact_form_submitted"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Contact form submit</li>
                </ul>
            </div>

            <div id="journeyKeyGroupLeft">

                <h3><?php esc_attr_e( '&nbsp;', 'KONTXT' ); ?></h3>

                <ul id="journeyKeysCommerce" class="droptrue">
                    WooCommerce Events
                    <li class="ui-state-default" data-arrange="10" id="cart_add"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Item added to cart</li>
                    <li class="ui-state-default" data-arrange="11" id="order_received"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Order received</li>
                    <li class="ui-state-default" data-arrange="12" id="product_comment_submitted"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Product comment submit</li>
                    <li class="ui-state-default" data-arrange="13" id="shop_page_category"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Shop category view</li>
                    <li class="ui-state-default" data-arrange="14" id="shop_page_home"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Shop home page view</li>
                    <li class="ui-state-default" data-arrange="15" id="shop_page_product"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Shop product page view</li>
                </ul>

                <ul id="journeyKeysForum" class="droptrue">
                    bbForum Events
                    <li class="ui-state-default" data-arrange="16" id="forum_page"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Forum page view</li>
                    <li class="ui-state-default" data-arrange="17" id="forum_topic_content"><span class="ui-icon ui-icon-arrowthick-2-e-w"></span> Forum topic posted</li>
                </ul>
            </div>

            <div id="journeyKeyGroupRight">
                <h3><?php esc_attr_e( 'Drop here to order the funnel', 'KONTXT' ); ?></h3>
                <div class="inside">
                    <ul id="journeyKeysDrop" class="droptrue"></ul>
                </div>
            </div>
        </div>

        <div style="clear: both"></div>

        <div>
            <p><input id="kontxt-events-date" class="button action" type="submit" value="Apply" /></p>
        </div>

    </form>

    <div style="clear: both"></div>

    <div id="journey-results-success" class="ui-helper-hidden">

        <div class="postbox">

            <div class="inside">

                <div id="journey_funnel_chart"></div>

            </div>

        </div>

    </div>

    <script>
        jQuery( function( $ ) {
            "use strict";
            $( function() {
                $( "ul.droptrue" ).sortable( {
                    connectWith: "ul",
                    update: function ( event, ui ) {

                        $( 'ul.droptrue' ).each( function( i, ul ) {
                            let data = [];
                            $( '#' + ul.id ).find( 'li' ).each( function( i, li ) {
                                data.push( $( '#' + li.id ).data( 'arrange' ) );
                            });
                            localStorage.setItem( ul.id, data );
                        });

                    }
                });

                $( "#journeyKeysCore, #journeyKeysForm, #journeyKeysCommerce, #journeyKeysForum, #journeyKeysDrop" ).disableSelection();
            } );


            let dropList  = 'journeyKeysDrop';
            let dropArray = [];

            if( localStorage.getItem( dropList ) !== null ) {
                dropArray = localStorage.getItem(dropList).split(',').map(x => {
                    return parseInt(x)
                });
            }

            $( 'ul.droptrue' ).each( function( i, ul ) {

                if( localStorage.getItem( ul.id ) ) {

                    let array = localStorage.getItem( ul.id ).split(',');
                    let map = {};
                    let el = $( '#' + ul.id );
                    $( '#' + ul.id + ' > li' ).each( function(i, li) {
                        let el = $('#' + li.id);
                        if( dropArray.indexOf( el.data( 'arrange' ) ) >= 0 ) {
                            $( '#' + dropList ).append( el );
                            map[el.data('arrange')] = el;
                        } else {
                            map[el.data('arrange')] = el;
                        }
                    });

                    for( let i = 0; i < array.length; i++ ) {
                        if( array[i] ) {
                            $( '#' + ul.id ).append( map[array[i]] );
                        }
                    }

                }

            });
        });


    </script>

</div>

