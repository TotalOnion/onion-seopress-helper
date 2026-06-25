<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://totalonion.com/
 * @since      1.0.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/public
 * @author     Total Onion <enquiries@totalonion.com>
 */
class Onion_Seopress_Helper_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * An array of the languages currently hidden
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 * */
	private $hidden_languages = [];

	/**
	 * A regex pattern to match URLs. Set based on whether the site uses
	 * directories, subdomains, or a query string to specify the market code.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string;
	 * */
	private $url_regex_pattern;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// add any markets hidden in WPML
		$wpml_options = get_option( 'icl_sitepress_settings' );
		if ( ! empty( $wpml_options['hidden_languages'] ) ) {
			$this->hidden_languages = $wpml_options['hidden_languages'];
		}

		// Add any markets that have been hidden in settings
		$suppressed_markets = get_option( ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets' );
		if ( ! empty( $suppressed_markets ) ) {
			$this->hidden_languages = array_unique(
				array_merge(
					$this->hidden_languages,
					array_keys( $suppressed_markets )
				)
			);
		}

		// Create a regex pattern to remove matching URLs _if_ we have any markets to remove
		if ( $this->has_hidden_languages() ) {
			// What style of URLs does this site use?
			switch ( apply_filters( 'wpml_setting', 0, 'language_negotiation_type' ) ) {
				case '1':
					// URLs use "https://site.com/XX/"
					preg_match( '/http[s]?:\/\/(.*)/', get_site_url(), $matches );
					$this->url_regex_pattern = '/' . $matches[1] . '\/([a-zA-Z\-]+)/';
					break;

				case '2':
					// URLs use "https://XX.site.com/"
					$this->url_regex_pattern = '/http[s]?:\/\/([a-zA-Z\-]+)\./';
					break;

				case '3':
					// URLs use "https://site.com/?lang="
					$this->url_regex_pattern = '/\?lang=([a-zA-Z\-]+)/';
					break;
			}
		}
	}

	public function has_hidden_languages(): bool
	{
		return $this->hidden_languages ? true : false;
	}

	/**
	 * Remove references to hidden languages
	 *
	 * @since    1.0.0
	 */
	public function filter_sitemap(
		string $sitemap_url_node,
		?array $seopress_url_object = null
	): string {
		// Is there is a logged in user who has "show hidden languages" set to true?
		if (
			get_current_user_id()
			&& get_user_meta( get_current_user_id(), 'icl_show_hidden_languages', true )
		) {
			return $sitemap_url_node;
		}

		// Does the $sitemap_url_node have a hidden market code in it?
		if (
			preg_match( $this->url_regex_pattern, $sitemap_url_node, $matches )
			&& in_array( $matches[ 1 ], $this->hidden_languages )
		) {
			return '';
		}

		return $sitemap_url_node;
	}

	/**
	 * Remove hidden languages from the hreflangs. WPML does this already
	 * for languages hidden in WPML, but bot for ones set as suppressed in
	 * this plugin.
	 *
	 * @since    1.1.0
	 */
	public function filter_herflangs( $languages ) {
		$filtered_languages = [];

		foreach ( $languages as $language_code => $language ) {
			if ( ! in_array( $language_code, $this->hidden_languages ) ) {
				$filtered_languages[ $language_code ] = $language;
			}
		}
		
		return $filtered_languages;
	}

	/**
	 * Force any URLs on the language to use the market homepage if they are on
	 * a market that is supressed (hidden markets are removed from the language selector)
	 * https://irishdistillers.atlassian.net/browse/MNB-330
	 *
	 * @since  1.3.0
	 * @param  string $url
	 * @param  array  $data
	 * @return string $url
	 */
	public function filter_language_selector(
		string $url,
		array $data
	): string {
		if (
			get_current_user_id()
			&& get_user_meta( get_current_user_id(), 'icl_show_hidden_languages', true )
		) {
			return $url;
		}
		
		// Does the language selector $url have a supressed market code in it?
		if (
			preg_match( $this->url_regex_pattern, $url, $matches)
			&& in_array( $matches[ 1 ], $this->hidden_languages )
		) {
			global $sitepress;
			return $sitepress->convert_url( home_url(), $matches[ 1 ] );
		}

		return $url;
	}

	/**
	 * Ensure that the PR Product single product posts appear in the sitemap.
	 * https://irishdistillers.atlassian.net/browse/GCMSP-982
	 * 
	 * @since  1.3.4
	 * @param array $args     The Seopress query args
	 * @param string $cpt_key The post type name
	 * @return array $args The Seopress query args
	 */
	public function add_product_urls_to_sitemap(
		array $args,
		string $cpt_key
	): array {
		// Are we dealing with the product post_type with a tax_query?
		if ( 
			'product' === $cpt_key 
			&& isset( $args['tax_query'] ) 
			&& !empty( $args['tax_query'] )
		) {
	        $found_index = false;
	        foreach( $args['tax_query'] as $index => $tax_query ){
	        	// Is there the specific tax_query arg to remove?
	            if( 'product_visibility' === $tax_query['taxonomy'] ) {
	                $found_index = $index;
	                break;
	            }            
	        }

	        // If the specific index has been found, we remove it from the tax_query.
	        if( 
	        	false !== $found_index 
	        	&& isset($args['tax_query'][$found_index])
	        ) {
	            unset( $args['tax_query'][$found_index] );
	        }        
    	}

    	return $args;
	}
}
