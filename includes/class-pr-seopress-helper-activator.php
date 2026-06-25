<?php

/**
 * Fired during plugin activation
 *
 * @link       https://heavyweightagency.co.uk/
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
 * @author     Heavyweight <enquiries@heavyweightagency.co.uk>
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
		// PR Core is required !
		$pr_core_plugin = 'pr-core/pr-core.php';
		if (
			! file_exists( trailingslashit( WPMU_PLUGIN_DIR ) . $pr_core_plugin )
			&& ! is_plugin_active( $pr_core_plugin )
		) {
			wp_die(
				__(
					'You must activate <strong>PR CORE plugin</strong> to activate this plugin. <br><a href="' . admin_url( 'plugins.php' ) . '">Return to Plugins page</a>',
					'pr-seopress-helper'
				)
			);
		}

		// WPML is required
		if ( ! is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			wp_die(
				__(
					'This plugin is not needed if WPML is not installed and activated. Cancelling activation',
					'pr-seopress-helper'
				)
			);
		}

		// SEOPress is required
		if ( ! is_plugin_active( 'wp-seopress/seopress.php' ) ) {
			wp_die(
				__(
					'This plugin is not needed if SEOPress is not installed and activated. Cancelling activation',
					'pr-seopress-helper'
				)
			);
		}
	}
}
