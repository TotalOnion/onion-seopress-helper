<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://totalonion.com/
 * @since 1.1.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/admin
 * @author     Total Onion <enquiries@totalonion.com>
 */
class Onion_Seopress_Helper_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since  1.1.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.1.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;


    /**
     * The ID for the setting that holds configuration switches.
     *
     * @since  1.2.0
     * @access protected
     * @var    string $switch_id  The ID of the switches element/setting.
     */
    protected $switch_id;

    /**
     * The switches available for setting
     *
     * @since  1.2.0
     * @access protected
     * @var    arrary    Associative array for setting_slug => Label
     */
    protected static $switches;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.1.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->switch_id    = ONION_SEOPRESS_HELPER_NAME . '_switches';
        self::$switches    = array(
        	'redirects_hide' => __('Hide Redirection tab when editing content and disable SEOPress redirection recommendations', 'PR SEOPress Helper'),
        );
    }

    /**
     * Add the settings page. Reference:
     * https://developer.wordpress.org/reference/functions/add_options_page/
     *
     * @since 1.1.0
     */
    public function add_settings_page()
    {
        add_options_page(
            'SEOPress Helper Settings',               // page title
            'SEOPress Helper Settings',               // menu title
            'manage_options',                         // capability required to access / see it
            ONION_SEOPRESS_HELPER_SLUG . '-settings-page', // slug (needs to be unique)
            array( $this, 'render_settings_page' )    // callable function to render the page
        );
    }

    /**
     * Register an Option to store the market values in, and the
     * Settings section and page to display it on
     *
     * @since 1.1.0
     */
    public function register_settings()
    {
        // Add the option
        add_option(ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets');

        // Mark the above Option as a Setting we can edit in the admin
        // reference https://developer.wordpress.org/reference/functions/register_setting/
        register_setting(
            ONION_SEOPRESS_HELPER_NAME . '_options',
            ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets',
            array(
            'type'         => 'array',
            'description'  => 'Markets to supress from the XML sitemap',
            'show_in_rest' => false,
            'default'      => '',
            )
        );

        // Adds the settings *section*
        // reference https://developer.wordpress.org/reference/functions/add_settings_section/
        add_settings_section(
            ONION_SEOPRESS_HELPER_NAME . '_options_section', // Unique ID for the section
            'Markets to supress from the XML sitemap',  // Title for the section
            array( $this, 'render_section_into' ),      // Callable function to echo the intro
            ONION_SEOPRESS_HELPER_SLUG . '-settings-page'    // the page this section appears on (defined in registerPage above)
        );

        // This adds the html field that renders the setting
        // reference https://developer.wordpress.org/reference/functions/add_settings_field/
        add_settings_field(
            ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets', // id="" value
            'Markets',                                     // <label> vale
            array( $this, 'render_market_checkboxes' ),    // callback to actually do the rendering of the input
            ONION_SEOPRESS_HELPER_SLUG . '-settings-page',      // Slug of the page to show this on (defined in registerPage above)
            ONION_SEOPRESS_HELPER_NAME . '_options_section'     // slug of the sction the field appears in
        );

        // Redirect options
        // -----------------

        // Add the option
        add_option(ONION_SEOPRESS_HELPER_NAME . '_switches');

        // Mark the above Option as a Setting we can edit in the admin
        // reference https://developer.wordpress.org/reference/functions/register_setting/
        register_setting(
            ONION_SEOPRESS_HELPER_NAME . '_options',
            ONION_SEOPRESS_HELPER_NAME . '_switches',
            array(
            'type'         => 'array',
            'description'  => 'Plugin redirection settings',
            'show_in_rest' => false,
            'default'      => '',
            )
        );

        // Adds the settings *section*
        // reference https://developer.wordpress.org/reference/functions/add_settings_section/
        add_settings_section(
            ONION_SEOPRESS_HELPER_NAME . '_switches_section', // Unique ID for the section
            __('Behaviour Switches', 'PR SEOPress Helper'),  // Title for the section
            null,                                       // Callable function to echo the intro
            ONION_SEOPRESS_HELPER_SLUG . '-settings-page'    // the page this section appears on (defined in registerPage above)
        );

        // This adds the html field that renders the setting
        // reference https://developer.wordpress.org/reference/functions/add_settings_field/
        add_settings_field(
            ONION_SEOPRESS_HELPER_NAME . '_switches',           // id="" value
            __('Redirects', 'PR SEOPress Helper'),       // <label> vale
            array( $this, 'render_options_checkboxes' ),   // callback to actually do the rendering of the input
            ONION_SEOPRESS_HELPER_SLUG . '-settings-page',      // Slug of the page to show this on (defined in registerPage above)
            ONION_SEOPRESS_HELPER_NAME . '_switches_section'    // slug of the sction the field appears in
        );
    }

    /**
     * Display the intro for the settiongs section
     *
     * @since 1.1.0
     */
    public function render_section_into()
    {
        __(
            'Tick markets below to remove them from the <a href="/sitemaps.xml">XML sitemaps</a>.',
            'PR SEOPress Helper'
        );
    }

    /**
     * Render the field itself
     *
     * @since 1.1.0
     */
    public function render_market_checkboxes()
    {
        // Get the WPML settings or return if there are none (ie WPML has been deactivayed)
        $wpml_options = get_option('icl_sitepress_settings');
        if (! $wpml_options
            || empty($wpml_options['active_languages'])
        ) {
            return;
        }

        // Get the array of market codes from the Option
        $suppressed_markets = get_option(ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets');
        if ($suppressed_markets ) {
            $suppressed_markets = array_keys($suppressed_markets);
        } else {
            $suppressed_markets = array();
        }

        global $sitepress;    // Urgh

        // Loop over all the markets and get the name, and if they are currently hidden
        $market_data = array();
        foreach ( $wpml_options['active_languages'] as $active_language ) {
            $details = $sitepress->get_language_details($active_language);
            if (! $details ) {
                continue;
            }

            $is_hidden = in_array(
                $details['code'],
                $wpml_options['hidden_languages'] ?? array()
            );

            $market_data[] = array(
            'code'      => $details['code'],
            'name'      => $details['english_name'],
            'is_hidden' => $is_hidden,
            );
        }

        // Sort the list by the English name of the market
        usort(
            $market_data,
            function ( $a, $b ) {
                return $a['name'] <=> $b['name'];
            }
        );

        foreach ( $market_data as $market ) {
            $field_name    = ONION_SEOPRESS_HELPER_NAME . '_suppressed_markets[' . $market['code'] . ']';
            $is_suppressed = in_array($market['code'], $suppressed_markets);
            include __DIR__ . '/partials/market-checkbox.php';
        }
    }

    /**
     * Render the field itself
     *
     * @since 1.1.0
     */
    public function render_options_checkboxes()
    {
        $options  = get_option(ONION_SEOPRESS_HELPER_NAME . '_switches');
        $field_id = $this->switch_id;
        foreach ( self::$switches as $slug => $label ) {
            $field_name    = sprintf('%s[%s]', $this->switch_id, $slug);
            $field_enabled = false;
            if (! empty($options[ $slug ]) ) {
                $field_enabled = true;
            }
            include __DIR__ . '/partials/basic-checkbox.php';
        }
    }


    /**
     * Render the settings page in the admin.
     *
     * @since 1.1.0
     */
    public function render_settings_page()
    {
        include __DIR__ . '/partials/settings-form.php';
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
}
