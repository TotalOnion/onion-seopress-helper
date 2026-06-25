<?php

/**
 * Fired during plugin activation
 *
 * @link       https://totalonion.com/
 * @since      1.0.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 * @author     Total Onion <enquiries@totalonion.com>
 */
class Onion_Seopress_Helper_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// WPML is required
		if ( ! is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			wp_die(
				__(
					'This plugin is not needed if WPML is not installed and activated. Cancelling activation',
					'onion-seopress-helper'
				)
			);
		}

		// SEOPress is required
		if ( ! is_plugin_active( 'wp-seopress/seopress.php' ) ) {
			wp_die(
				__(
					'This plugin is not needed if SEOPress is not installed and activated. Cancelling activation',
					'onion-seopress-helper'
				)
			);
		}
	}
}
