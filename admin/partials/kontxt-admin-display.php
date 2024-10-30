<?php
/**
 * Created by PhpStorm.
 * User: michaelbordash
 * Date: 4/21/17
 * Time: 11:04 PM
 */

    $allowed_html = get_option( 'kontxt_allowable_html' );

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="notice notice-info is-dismissible inline">
    <p>
        <?php

        echo wp_kses(__('Thank you for using KONTXT! If you have any questions, comments, suggestions about our software, please <a s href="https://kontxt.com/more-information/">contact us</a>.', 'kontxt'), $allowed_html );

		printf(
		// translators: Leave always a hint for translators to understand the placeholders.
			esc_attr__( '', 'WpAdminStyle' ),
			'<code>.notice-info</code>',
			'<code>.is-dismissible</code>',
			'<code>.inline</code>'
		);
		?>
    </p>
</div>

<div class="wrap">

    <div id="kontxt-settings-navigation">

        <h2 class="nav-tab-wrapper current">
            <a href="javascript:;" class="nav-tab nav-tab-active"><?php echo __('Configuration', 'kontxt'); ?></a>
            <a href="javascript:;" class="nav-tab"><?php echo __('Help/About', 'kontxt'); ?></a>
        </h2>

        <div class="inside">

            <div id="poststuff">

                <div class="postbox">

                    <div class="inside">
                        <?php
                        echo wp_kses(__('

                            <h3>Opt-in for KONTXT</h3>

                            <p>In order for KONTXT to work properly, you must opt-in using the form below. If you do not wish KONTXT to perform our services, please deactivate and delete this plugin. All site data is deleted within 7 days of the last recorded activity automatically.</p>

                            <p>KONTXT services log requests for deep semantic analytics. In order to enable our analytical functions and predictive insights, KONTXT will encrypt, log and process anonymous site activity.
                            This is only done to deliver and improve the machine learning functions that power our deep analytics and future product recommendations. The data we use <strong>does not contain any personally identifiable information</strong>, and is not shared, sold, or made public. Should you have any GDPR requests, please <a href="https://kontxt.com/more-information/">contact us</a>. </p>

                        ', 'kontxt'), $allowed_html );
                        ?>
                        <form action="options.php" method="post">

                            <?php

                                settings_fields( $this->plugin_name );
                                do_settings_sections( $this->plugin_name );
                                submit_button();

                            ?>

                        </form>

                        <?php
                        echo wp_kses(__('
                            <p>KONTXT provides semantic and journey analysis for Wordpress core, WooCommerce, bbPress, Contact Form 7, and Gravity Forms.  If you use a plugin not listed here that could benefit from KONTXT machine learning, please <a href="https://kontxt.com/more-information/">contact us</a>.</p>

                            <p>KONTXT&trade; is a service provided by &copy;RealNetworks, Inc. For more information on our terms of use and data usage, please visit <a href="https://www.realnetworks.com">https://www.realnetworks.com</a>.</p>
                        ', 'kontxt'), $allowed_html );
                        ?>

                     </div>
                </div>
            </div>
        </div>

        <div class="inside hidden">

            <div id="poststuff">

                <div class="postbox">

                    <div class="inside">

                        <h3>About</h3>

                        <p>Our machine learning engine analyzes customer journeys to help discover where your visitors start and end.
                            Use these insights to help shape your site navigation and help your customers along the path.

                            We've trained our journey analyzer on basic content and shop site events, however, we offer customized event discovery including article, product, and custom business logic flows for more advanced analysis and recommendations.  Please <a target="_blank" href="https://kontxt.com/more-information/">contact us</a> for details.
                        </p>


                        <h3>Generic events supported:</h3>

                        <dl>

                            <dt><strong>site_home</strong></dt>
                            <dd>View of the main site page or blog home page</dd>

                            <dt><strong>blog_post</strong></dt>
                            <dd>View of a blog post</dd>

                            <dt><strong>site_page</strong></dt>
                            <dd>View of any site page including custom pages, category pages, and commerce pages like cart, checkout, and account management </dd>

                            <dt><strong>search_query*</strong></dt>
                            <dd>Retrieval of search results</dd>

                            <dt><strong>no_search_results</strong></dt>
                            <dd>Signal indicating your visitor received no search results from their query</dd>

                            <dt><strong>user_registered</strong></dt>
                            <dd>Event indicating a new visitor registered on your site</dd>

                            <dt><strong>comment_submitted*</strong></dt>
                            <dd>Event indicating a visitor posted a comment</dd>

                            <dt><strong>END SESSION</strong></dt>
                            <dd>A calculated event that indicates your visitor ended their time on your site</dd>


                        </dl>

                        <h3>WooCommerce events supported:</h3>

                        <dl>

                            <dt><strong>shop_page_home</strong></dt>
                            <dd>View of your WooCommerce home page </dd>

                            <dt><strong>shop_page_category</strong></dt>
                            <dd>View of a shop category page </dd>

                            <dt><strong>shop_page_product</strong></dt>
                            <dd>View of a shop product page </dd>

                            <dt><strong>cart_add</strong></dt>
                            <dd>Signal indicating your visitor added a product to cart</dd>

                            <dt><strong>order_received</strong></dt>
                            <dd>Signal indicating your visitor completed and order</dd>

                        </dl>

                        <h3>bbPress forum events supported:</h3>

                        <dl>

                            <dt><strong>forum_page</strong></dt>
                            <dd>Indicating a user viewed a forum or topic page</dd>

                            <dt><strong>forum_topic_content*</strong></dt>
                            <dd>Indicating a user posted a topic or replied to an existing topic</dd>

                        </dl>

                        <h3>Gravity Forms and Contact Form 7 events supported:</h3>

                        <dl>

                            <dt><strong>contact_form_submitted*</strong></dt>
                            <dd>Indicating a visitor used the contact form to get in touch</dd>

                        </dl>

                        * indicates that a semantic assessment was performed on the words accompanying these events

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>