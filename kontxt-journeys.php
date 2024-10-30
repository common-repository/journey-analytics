<?php

/**
 *
 * @link              https://www.kontxt.com
 * @since             1.0.0
 * @package           Kontxt
 *
 * @wordpress-plugin
 * Plugin Name:       Journey Analytics
 * Plugin URI:        https://www.kontxt.com
 * Description:       Track your customer journeys by intent and by business goal. Identify your most lucrative paths and those where customers are dropping off.
 * Version:           1.0.15
 * Author:            RealNetworks KONTXT
 * Author URI:        https://kontxt.com
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       kontxt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kontxt-activator.php
 */
function kontxt_journeys_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-JourneysActivator.php';
	Kontxt_Journeys_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kontxt-deactivator.php
 */

function kontxt_journeys_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kontxt-JourneysDeactivator.php';
	Kontxt_Journeys_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'kontxt_journeys_activate' );
register_deactivation_hook( __FILE__, 'kontxt_journeys_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kontxtJourneys.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kontxt_journeys() {

	/**
	 *  The app ini file to set installation parameters for development or production
	 */
	$kontxt_ini = parse_ini_file(plugin_dir_path( __FILE__ ) . 'app.ini.php' );

	$plugin = new Kontxt_Journeys($kontxt_ini);
	$plugin->run();

}
run_kontxt_journeys();
