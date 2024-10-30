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

    <h2>Journey Points</h2>

    <div id="journey" class="inside">

        <div id="kontxt-results-box" style="height: 50px;">

            <form id="kontxt-input-form" action="" method="post" enctype="multipart/form-data">
                <input id="dimension" name="dimension" type="hidden" value="journeyNodeCount" />

                <div id="kontxt-input-text">

                    <p style="float: left">
                        Date From: <input type="text" style="" name="date_from" id="date_from" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                        Date To: <input type="text" style="" name="date_to" id="date_to" value="" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />

                        <input id="kontxt-events-date" class="button action" type="submit" value="Apply" />
                    </p>

                </div>

            </form>

        </div>

        <div id="kontxt-results-box">

            <div id="journey-results-box">

                <div id="journey-results-success">

                    <div id="poststuff">

                        <div class="postbox">

                            <div class="inside">

                                <div id="journeyNodeCount_results_chart"></div>

                            </div>

                        </div>

                        <p>
                            <em>No data?  Browse your site's front end for a bit then come back to see the paths.</em>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="kontxt-analyze-results-status" class="wrap"></div>

    <script>
        jQuery(function($) {
            "use strict";
            kontxtJourneyPoints('journeyNodeCount');
        });
    </script>

</div>