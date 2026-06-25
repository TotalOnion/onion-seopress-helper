<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://heavyweightagency.co.uk/
 * @since             1.0.0
 * @package           Onion_Seopress_Helper
 *
 * @wordpress-plugin
 * Plugin Name:       SEOPress Helper
 * Plugin URI:        https://bitbucket.org/pernod-ricard/wordpress-plugin-pr-seopress-helper
 * Description:       A plugin to allow sitemaps to account for "hidden" markets in WPML
 * Version:           1.3.6
 * Author:            Heavyweight
 * Author URI:        https://heavyweightagency.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pr-seopress-helper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ONION_SEOPRESS_HELPER_VERSION', '1.3.6' );
define( 'ONION_SEOPRESS_HELPER_NAME', 'onion_seopress_helper' );
define( 'ONION_SEOPRESS_HELPER_SLUG', 'pr-seopress-helper' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-onion-seopress-helper-activator.php
 */
function activate_onion_seopress_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-onion-seopress-helper-activator.php';
	Onion_Seopress_Helper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-onion-seopress-helper-deactivator.php
 */
function deactivate_onion_seopress_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-onion-seopress-helper-deactivator.php';
	Onion_Seopress_Helper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_onion_seopress_helper' );
register_deactivation_hook( __FILE__, 'deactivate_onion_seopress_helper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-onion-seopress-helper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_onion_seopress_helper() {
	$plugin = new Onion_Seopress_Helper();
	$plugin->run();

}
run_onion_seopress_helper();
