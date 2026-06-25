<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://heavyweightagency.co.uk/
 * @since      1.0.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/includes
 * @author     Heavyweight <enquiries@heavyweightagency.co.uk>
 */
class Onion_Seopress_Helper {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Onion_Seopress_Helper_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ONION_SEOPRESS_HELPER_VERSION' ) ) {
			$this->version = ONION_SEOPRESS_HELPER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'pr-seopress-helper';

		$this->load_dependencies();
		//$this->set_locale();
		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Onion_Seopress_Helper_Loader. Orchestrates the hooks of the plugin.
	 * - Onion_Seopress_Helper_i18n. Defines internationalization functionality.
	 * - Onion_Seopress_Helper_Admin. Defines all hooks for the admin area.
	 * - Onion_Seopress_Helper_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-onion-seopress-helper-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-onion-seopress-helper-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-onion-seopress-helper-public.php';

		/**
		 * The class responsible for defining all actions that occur in the admin
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-onion-seopress-helper-admin.php';

		$this->loader = new Onion_Seopress_Helper_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Onion_Seopress_Helper_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Onion_Seopress_Helper_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$sitemap = new Onion_Seopress_Helper_Public( $this->get_plugin_name(), $this->get_version() );

		if ( $sitemap->has_hidden_languages() ) {
			$this->loader->add_filter( 'seopress_sitemaps_url', $sitemap, 'filter_sitemap', 1, 2 );
			$this->loader->add_filter( 'wpml_head_langs', $sitemap, 'filter_herflangs', 1, 1 );

			/**
			 * Force market links for hidden markets in the language selector to use the market homepage
			 * https://irishdistillers.atlassian.net/browse/MNB-330
			 */
			$this->loader->add_filter( 'wpml_ls_language_url', $sitemap, 'filter_language_selector', 10, 2 );
		}
		/**
		 *  If PR Product is activated, we have to handle an incompatibility issue with Seopress, preventing the single product posts from apperaing in the sitemap
		 *  https://irishdistillers.atlassian.net/browse/GCMSP-982
		 */
		if(in_array( 'pr-products/pr-products.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )){

			$this->loader->add_filter('seopress_sitemaps_single_query',$sitemap,'add_product_urls_to_sitemap', 10, 2);
		}
	}

	/**
	 * Register all of the hooks related to the admin functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$admin = new Onion_Seopress_Helper_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $admin, 'register_settings' );
		$this->loader->add_action( 'admin_menu', $admin, 'add_settings_page' );

		$switches = get_option( ONION_SEOPRESS_HELPER_NAME . '_switches' );
		if ( ! empty( $switches['redirects_hide'] ) ) {
			$this->loader->add_filter( 'seopress_metabox_seo_tabs', $admin, 'remove_seopress_redirections_tab' );
			// Disable automatic redirect notices in WP admin (MNB-323)
			$this->loader->add_filter( 'seopress_post_automatic_redirect', $admin, 'disable_seopress_automatic_redirect' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Onion_Seopress_Helper_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
