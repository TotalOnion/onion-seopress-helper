<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://totalonion.com/
 * @since      1.0.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 * @author     Total Onion <enquiries@totalonion.com>
 */
class Onion_Seopress_Helper_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'onion-seopress-helper',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
