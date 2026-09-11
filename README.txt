=== Plugin Name ===
Contributors: 
Donate link: https://totalonion.com/
Tags: sitemap, seopress, wpml, hreflang
Requires at least: 6.5
Tested up to: 7.0
Stable tag: 1.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This helper will add the option of "suppressed" languages to SEOPress, which differ from WPMLs "hidden" languages.

Suppressed languages:
- will appear in language selectors
- will *not* appear in sitemaps
- will *not* appear in hreflangs

You may also choose to group sitemaps per market:
- `/sitemaps.xml` will list one sitemap per language `[country-code]/sitemaps.xml`
- This will list your sitemaps grouped by post type if you chose this option `[country-code]/[post-type]-sitemap1.xml`
- Suppressed markets will not be hidden at the root (`/en/sitemaps.xml` is still listed even if `en` is suppressed)
- Suppressed markets' sitemaps will be empty
- You need to remove anything that temper with sitemaps behaviour


== Description ==

This plugin has no configuration, but requires the following plugins to be present and active:
 - [WPML](https://wpml.org/)
 - [SEOPress / SEOPress Pro](https://www.seopress.org/)
 - [PR Core](https://bitbucket.org/pernod-ricard/wordpress-plugin-pr-core)

== Changelog ==

= 1.4.0 =
* Added the ability to group sitemaps per language

= 1.3.6 =
* Updated the activation script to be less daft

= 1.1.0 =
* Added a settings page in the admin. `Settings` -> `SEOPress Helper Settings`
* Languages can be removed from the sitemaps and hreflangs in this settinmgs page, without them being hidden by WPML
