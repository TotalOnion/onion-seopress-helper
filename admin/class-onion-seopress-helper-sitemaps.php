<?php

/**
 * Sitemaps Related features of the Plugin
 *
 * @link  https://totalonion.com/
 * @since 1.4.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/sitemaps
 */

/**
 * Sitemaps Related features of the Plugin
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/sitemaps
 * @author     Total Onion <enquiries@totalonion.com>
 */
class Onion_Seopress_Helper_Sitemaps
{

    /**
     * The ID of this plugin.
     *
     * @since  1.4.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.4.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.4.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Remove the "Redirections" tab in the Page edit SEOpress Options.
     *
     * @link   https://irishdistillers.atlassian.net/browse/MNB-323
     * @param  array $seopress_tabs The tabs that are going to be displayed
     * @return array                   The updated tabs
     */
    public function remove_seopress_redirections_tab( $seopress_tabs )
    {
        unset($seopress_tabs['redirect-tab']);
        return $seopress_tabs;
    }

    /**
     * Disable the automatic redirect recommendations by SEOPress.
     *
     * @link   https://irishdistillers.atlassian.net/browse/MNB-323
     * @return bool                    Automatic redirect
     */
    public function disable_seopress_automatic_redirect()
    {
        return false;
    }

    public function seo_filter_sitemap_languages($args) {
        $current_lang = apply_filters( 'wpml_current_language', null );
        $args['lang'] = $current_lang;
        $args['suppress_filters']  = false; // let WPML's own query filtering apply
        return $args;
    }

    public function seo_filter_sitemap_index_xml($content) {
        if (strpos($content, '<sitemapindex') === false) return $content;
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadXML($content);
        libxml_clear_errors();
        $xmlNamespace = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $sitemapIndex = $doc->getElementsByTagName('sitemapindex')->item(0);
        $sitemaps = $doc->getElementsByTagNameNS($xmlNamespace, 'sitemap');
        $a_parsed_url = parse_url($sitemaps->item(0)->getElementsByTagName('loc')->item(0)->nodeValue);
        $path = explode('/', $_SERVER['REQUEST_URI']);

        // If there is no locale, then remove everything and display markets sitemaps
        if ( !(count($path) > 2) ) {
            // Removing all current sitemaps
            // You can't modify a live NodeList while iterating it
            $existingUrls = [];
            foreach ($sitemaps as $sitemapNode) {
                $existingUrls[] = $sitemapNode;
            }
            foreach ($existingUrls as $existingUrl) {
                $sitemapIndex->removeChild($existingUrl);
            }

            // Adding one sitemap per market
            $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
            $now = date('Y-m-d');
            foreach ($languages as $language) {
                $lang = $language['code'];

                $newSitemapNode = $doc->createElementNS($xmlNamespace, 'sitemap');

                $loc = $a_parsed_url['scheme'] . '://' . $a_parsed_url['host'] . '/' . $lang . '/sitemaps.xml';
                $locNode = $doc->createElementNS($xmlNamespace, 'loc', htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
                $newSitemapNode->appendChild($locNode);

                $lastmodNode = $doc->createElementNS($xmlNamespace, 'lastmod', $now);
                $newSitemapNode->appendChild($lastmodNode);

                $sitemapIndex->appendChild($newSitemapNode);
            }
        } else { // If there is a locale then we just display post types sitemaps with locale
            $lang = $path[1];
            foreach ($sitemaps as $sitemap) {
                $loc = $sitemap->getElementsByTagName('loc')->item(0);
                $parsed_url = parse_url($loc->nodeValue);
                $new_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . '/' . $lang . $parsed_url['path'];
                $loc->nodeValue = $new_url;
            }
        }
        return $doc->saveXML();

    }
}
