=== GeoTargeting WP ===
Contributors: timersys
Donate link: https://geotargetingwp.com/
Tags: geotargeting, wordpress geotargeting, geolocation, geo target, geo targeting, ip geo detect, geo links, geo redirects
Requires at least: 4.4
Tested up to: 6.7
Stable tag: 3.5.1.8
Update URI: https://geotargetingwp.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GeoTargeting for WordPress will let you country-target your content based on users IP's and GeotargetinWP API

== Description ==

Geo Targeting plugin for WordPress will let you create dynamic content based on your users country.

With a simple shortcode you will be able to specify which countries are capable of seing the content or which countries are not allowed.

E.g:
`[geot country="Argentina"] Messi is the best! [/geot]`
`[geot country="Portugal"] Cristiano ronaldo is the best! [/geot]`

More info and docs on ([https://geotargetingwp.com/docs/](https://geotargetingwp.com/docs/))

== Installation ==

1. Unzip and Upload the directory 'geotargetingwp' to the '/wp-content/plugins/' directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Go to the editor and use as many shortcodes as needed


== Frequently Asked Questions ==

= None yet =


== Changelog ==
= 3.5.1.8 =
* Improved menu support on multiple themes

= 3.5.1.7 =
* Added support for Menus in the new Twenty themes
* Fixed some PHP 8 incompatibilities

= 3.5.1.6 =
* Fix compatibility with Wpbeaver new columns
* Fix issue when using query string and anchor in url for redirects in ajax mode
* Fix issue with results loading from cache when using geotdebug

= 3.5.1.5 =
* Fixed object cache not storing empty results, making unnecessary sql calls
* Fixed html tag for ajax placeholder shortcode
* Fixed error with Elementor geo when copying modules

= 3.5.1.4 =
* Fixed compatibility with WP Popups geo fields
* Fixed issue with database update keeps appearing on new installs

= 3.5.1.3 =
* Minor bugfixes
* Maxmind plugin update compatibility

= 3.5.1.2 =
* Added WpRocket Ips to exclusion

= 3.5.1.1 =
* Fixed bug where not all pages where showing on new ajax dropdown for rules
* Fixed bug with redirects not working when iso codes entered in lowercase
* Fixed bug with ajax mode not working on archives pages on certain ocassions
* Fixed imcompatibily issue with multiple select in ACF plugin
* Fixed bug in WooCommerce when tax calculation is required

= 3.5.1 =
* Added Elementor's containers support
* Fixed Elementors Icons
* Added searchable field for posts/pages in rules to improve performance on sites with thousands of posts
* Added hook to bypass geotargeting
* Fixed some PHP8.2 compatibility issues

= 3.5.0.2 =
* Updated crawlers list to exclude new amazon bots and similar

= 3.5.0.1 =
* Fixed small bug introduced on 3.5 for redirects on 404 pages

= 3.5 =
* Geolinks search source urls in admin
* Added Stats to redirects
* Improved performance and reduced sql queries
* Fixed minor errors

= 3.4.2.7 =
* Fixed Gutemberg compatibility with SVG support plugin

= 3.4.2.6 =
* Added some security measures

= 3.4.2.5 =
* Fixed trailing slash on Geolinks
* Fixed some PHP undefined errors
* Fixed passing original querystring on geolinks

= 3.4.2.4 =
* Multiple country dropdown support
* New Microsoft Azure IPs added to bots list
* Minor bugfixes

= 3.4.2.3 =
* Rewrote DIVI integration to try improve performance
* Singular Posts/pages in ajax mode are now hidden but default instead of removed

= 3.4.2.2 =
* Reverted quickfix for DIVI as it was generating issues on some users
* Divi version 4.19.1 ( updated 11-17-2022 ) fix performance issues

= 3.4.2.1 =
* Quick Fix for DIVI performance issues

= 3.4.2 =
* Fix problem with checking license screen
* Fix issue with dropdown shortcode and flags
* Fix issue with weglot translation of ajax redirects
* Fix with divi builder not working on certain hosts
* Added multiple dev filters

= 3.4.1.9 =
* Filter all local private IP
* Fixed old version of Elementor error

= 3.4.1.8 =
* Fixed litespeed cache integration

= 3.4.1.7 =
* Fixed GeoLinks Importer
* Fixed redirect splash screen showing white

= 3.4.1.6 =
* Fixed query string not being passed in the redirects bug
* Fixed woocommerce ajax not being detected on certain occasions

= 3.4.1.5 =
* Fixed typo in function originating PHP error
* Fixed undefined error on ACFv5

= 3.4.1.4 =
* Fixed redirect check for final url when using dynamic shortcodes
* Fixed javascript undefined error
* Fixed redirect one per session when object cache is present
* Rest api calls fixed
* Removed wizard data sharing
* Warning emails now every 12 hours instead of 3

= 3.4.1.3 =
* Improved Elementor updater script
* Fixed PHP8 compatibility issues
* Added Vatican country name to list

= 3.4.1.2 =
* Fix with geolinks and referrer
* Fix with target radius sometimes using ip radius instead of html5

= 3.4.1.1 =
* Fix with some sites getting invalid subscription email
* Added Translatepress support to geo rules

= 3.4.1.0 =
* Bug fix on core function

= 3.4.0.9 =
* Fix with multiple regions not working in dropdown shortcode
* Feature: Add/disable cloakign in geolinks
* Fix with elementor failing to load modules
* Enable cookie creation without wprocket mode

= 3.4.0.8 =
* Fix with gutemberg states
* Feature: added new gutemberg block to multi target
* Changed geo links shortcodes to noreferrer nofollow by default

= 3.4.0.7 =
* Fix with gutemberg regions

= 3.4.0.6 =
* Fix: Divi specialty and fullwidth sections
* Fix: Gutemberg Show blocks not working
* Fix: WpRocket integration refresh everytime settings are saved
* Fix: Multiple region support in country dropdown widget shortcode


= 3.4.0.5 =
* Fix in geoblocker that was blocking users when it shouldn't
* minor bugfixes of undefined warnings

= 3.4.0.4 =
* Added new dev filters
* Fixed update class missing

= 3.4.0.3 =
* Elementor fix

= 3.4.0.2 =
* Fix upgrade routine

= 3.4.0.1 =
* Fixed problem with the updater on certain hostings
* Fixed css issue
* Fixed issue with ajax mode hiding text in product pages
* Added support for Elementor sections templates

= 3.4 =
* Feature: Added opcache support to increase performance and save requests
* Feature: Added global rules to geo redirects to avoid repetition
* Cleaned all builders with less fields
* Css minified
* Fix: Added workaround for Divi country regions bug
* Fix: query string and hascode removal for url matching in rules
* Feature: Remove post checks in AJAX mode to save requests
* Feature: WpRocket integration without cache mode
* Fix: Fixed bug with categories not showing in admin
* Fix: Regions missing from States in builder

= 3.3.8.1 =
* Fix: core function not being cached, adding seconds to load
* Fix: states regions not working on menus
* Fix: HTML geolocation only enable on needed pages
* Fix: query string in url breaking custom url rules

= 3.3.8 =
* Fix: Removed guzzle library in favor of wp native functions for better compatibility
* Feature: Added full location info for fallback and crawlers
* PHP minimum requirement it's 7.2.5
* Fix get data from IP php function
* Fix compatibility with WP5.8 and minor bugfixes

= 3.3.7.1=
* Added Gtranslate ips to automatic exclusion list
* Added megamenu fix where parent items where not being hide in AJAX mode
* Added automatic detection of pantheon host


= 3.3.7 - June 17 2021 =
* Fix: Geoblocker not excluding search engines
* Fix: Media block elements not loading in ajax mode
* Fix: Date time rules not working on some hosting environments
* Fix: PHP 8 compatibility errors
* Feature: Pantheon hosting cache by country support (beta)

= 3.3.6 - May 4 2021 =
* Feature: Added miles or km for radius
* Feature: Changed geolocation overlay popup and made it translatable
* Feature: Hashcode now passed with redirections along with query string
* Feature: Added new time/date rules
* Fix: WPML automatic translation on root pages
* Fix: Geoblocker not working on some enviroments

= 3.3.5.3 - March 20 2021 =
* HOTFIX for bug with ipv6 addresses, some address are still failing in 3.3.5.2

= 3.3.5.2 - March 20 2021 =
* HOTFIX for bug with ipv6 addresses introduced on previous version

= 3.3.5.1 - March 19 2021 =
* Fix: Ajax mode replace %id script for multiple classes in remove_class hook
* Fix: WPML changed to full resolution mode for slug translation in categories
* Feature: Added geo mode to geot radius shortcodes and elementor radius feature
* Fix: Country dropdown also update wprocket cookie now
* Fix: Limit composer php version
* Fix: Some server where returning port on IP address making it fail
* Fix: Missing regions in php ajax functions

= 3.3.5 - March 5 2021 =
* Fix: Woocommerce buy button issue with cache and ajax mode
* Feature: Radius geo mode to select include or exclude
* Fix: Session starting when it shouldn't with session redirects
* Fix: Changed geolinks export structure

= 3.3.4 - Feb 15 2021 =
* Fix geolinks Importer
* Fix gps on mobile not falling back to ip geolocation
* Automatically remove our script from autooptimize due to high wrong results
* Update translations files and added countries from dropdown

= 3.3.3.1 - Jan 19 2021 =
* Fix: Issue with some redirects not working when used page rules
* Fix: Taxonomy rules not working on archives pages

= 3.3.3 - Jan 12 2021 =
* Feature: Import/Export geo links for bulk edit
* Fix: Issue with Gutemberg cities and zip blocks
* Fix: Issue with taxonomy geotargeting with WPML active
* Feature: Dev hooks for replace/hide custom html in AJAX mode

= 3.3.2 - Dec 28 2020 =
* Feature: Hooks to add custom classes for AJAX mode
* Fix: Ajax mode not running when only geo posts are being used
* Fix: Woocommerce short description is now also removed
* Fix: Compatibility with price per country plugin
* Fix: Taxonomy geolocation now applied on every page and not just archives ones
* Fix: Redirects check session and cookies before other rules to save resources
* Fix: Fusion builder regions with ajax mode


= 3.3.1 - Nov 26 2020 =
* Feature: Redirect message can be easily changed from settings page
* Feature: Browser language in rules for redirects
* Fix: With Elementor builder when custom regions where deleter
* Minor bugfixes and dev hooks added

= 3.3 - Nov 12 2020 =
* Fix: State regions shortcodes
* Fix: Page check disabled for redirects. It can be enabled on demand
* Fix: Added missing regions in ACF api v4 a v5
* Feature: Zip code can be wide matched as 456*
* Feature: WP Popups compatibility

= 3.2.3.2 - Nov 3 2020 =
* Added Polylang support for automatic translation
* Fix bug in disable url check introduced in 3.2.3.1

= 3.2.3.1 - Nov 2 2020 =
* Add filter to disable url check in geo redirects and disabled in MU
* Fix: Megamenu compatibility issue
* Whitelist Jetpack Ips to save requests

= 3.2.3 - October 23 2020 =
* Devs: Add filter to disable crawl detection
* Fix: Disable WpRocket cookies when cache mode it's disabled
* Fix: WpBakery frontend editor not showing geolocated modules
* Fix: With Query string and WPML redirects
* Fix: Redirect message filter
* Feature: Check if page exists before redirect

= 3.2.2 - September 14 2020 =
* Fixed issue with Fusion Core > v5 and builder >v3
* Regions shortcode now return all regions and not just countries
* Fixed issue with Maxmin local database and PHP < 7.2
* Fixed issue with flatsome builder

= 3.2.1 - September 14 2020 =
* Fix issue with Fusion builder
* Fix issue with Geo Links States
* Added compatibility with Elementor popups

= 3.2.0.1 - September 5 2020 =
* Fix issue with ajax and states

= 3.2 - September 2 2020 =
* Fixed WP v < 4.4 issue
* Fixed menu items with certain themes that duplicate menus
* Fixed bug with city regions first click
* Feature: New cities can be added to city regions
* Feature: Labels added to Geolinks destinations
* Feature: WPML Auto translation of slugs in redirects
* Feature: Ajax mode use local storage for redirects session
* Feature: State regions added
* Feature: Added radius in missing places

= 3.1.0.2 - August 7 2020 =
* Widget Bug introduced on previous version

= 3.1.0.1 - August 7 2020 =
* Fix AJAX mode redirects
* Fix AJAX mode widgets
* Added fix for ubermenu compatibility

= 3.1 - August 4 2020 =
* Added GPS geolocation based on HTML5
* Fix Divi builder city regions

= 3.0.7.2 - July 1 2020 =
* Fixed bug with shortcodes that will consume requests in backend
* Fixed bug for custom ajax calls and geotargeting, that could lead in caching issues
* Fixed bug with Woocommerce not removing products in cart

= 3.0.7.1 - June 29 2020 =
* Fix issue with taxonomies that was hiding pages/products when it shouldn't
* Fix with menu items in AJAX mode

= 3.0.7.1 - June 16 2020 =
* Fix bug where ipv6 were being flagged as bots
* Fix: Ajax mode won't execute shortcodes or spend requests in visual builders
* Fix bug with taxonomies settings not saving
* Fix bug with Fusion builder and ajax
* Fix bug show with ajax mode on certain widgets

= 3.0.6 - June 2 2020 =
* New Feature: Radius filtering for posts and shortcodes
* New Feature: Widgets now supported in AJAX mode
* New Feature: Added IPs to be blocked automatically and save requests
* Fix: Dropdown shortcodes with ajax mode
* Fix: Menu geotargeting in old WP versions
* Fix: Menu and widget in ajax mode hides by default now
* Fix: Regions fields display issue in Elementor

= 3.0.5 - April 28 2020 =
* WooCommerce geolocation is optional now and not forced like previous version
* Fix Geolinks, slug will remail always at root level
* Added WP 5.4 Menu custom fields instead of replacing walker. Better compatibility with other plugins
* Fixed Elementor bug in regions fields
* Fixed Error with hosting db not resolving or giving critical error
* Added new settings for Geo Redirects to remove iso codes and exclude child pages from redirects

= 3.0.4 - April 14 2020 =
* Feature: Woocommerce geolocate it's done in GeotargetingWP now.
* Fix: Zip regions slugs
* Fix: Geo Redirects - We now remove country code from dynamic urls automatically to avoid redirect loops
* Fix: Addons got deactivated on plugin deactivation
* Fix: Flatsometheme builder compatibility
* Fix: Elementor columns geotargeting fix
* Fix: Removed Kinsta/WpEngine/Litespeed settings for a generic Hosting db setting


= 3.0.3.4 - March 16 2020=
* Fix: Guzzle Host must be a string error introduced with last update
* Fix: More Wpbeaver fixes

= 3.0.3.3 - March 9 2020=
* Fix: issue where sessions cookies where not being created
* Fix: Wpbeaver errors
* Fix: Guzzle library giving errors on certain PHP + 7.2

= 3.0.3.2 - March 5 2020=
* Performance: Added WPRocket htaccess compatibility for countries
* Performance: Improved ajax mode speed 30%
* Fix: Wpbeaver undefined errors

= 3.0.3.1 - Feb 1 2020=
* Feature: rules now support IP Blocks
* Feature: Anonymous data usage
* Feature: Dev filter to change redirect message
* Fix: problem with session starting when it should not
* Fix: zip regions in ajax mode not working
* Fix: Fusion builder error
* Fix: Exclude zip in shortcode generator
* Fix: Error when woo cart is not set

= 3.0.3=
* Added zip regions
* Added Avada fusion builder support
* disable sessions in admin area
* Added new rule to match IPs
* Recoded public js to make methods accesible for devs
* State city and zip can be separated by comma to add multiple values
* Updated Session library (clean old sessions issue)

= 3.0.2.3 =
* Fixed issue with geo taxonomies
* Validate ip to save request with invalid ones

= 3.0.2.2 =
* Fixed issue with page builders because redirects
* Removed .git folder for users that use GIT to upload plugins
* Fixed issue with countries not showing in Popups plugin

= 3.0.2.1 =
* Fixed relative urls not working in redirects
* Fixed divi estetic icon
* Beaver Builder ultimate addon compatibility fix
* FixedPredefined regions not showing in rules

= 3.0.2 =
* Added Beaver builder support
* Fixed issue with debug mode that consumed extra requests

= 3.0.1.1 =
* Fixed issue with DIVI
* Fixed undefined error message appearing in logs
* Fixed bug with predefined regions being duplicated

= 3.0.1 =
* Fixed issue with ajax running when turned off
* Weglot compatibility
* Added exclude ip range support

= 3.0.0.1 =
* Hotfix for menu, dropdown widget and widgets

= 3.0 =
* New plugin all in one
* Welcome screen for better configuration

= 2.6 =
* Separate city regions from city field
* Woocommerce categories support
* Woocommerce remove products from cart when country changes
* Debug query strings works in ajax mode
* Core updates

= 2.5 =
* Added DIVI page builder support
* Added Elementor page builder support
* Added zip code fields
* Added posts cateogories support
* Added predefined continents regions
* Fixed api for plugin updates
* Fixed settings not saving unchecks

= 2.4 =
* Geotargeted Gutemberg blocks
* New settings tabs
* New Country dropdown shortcode
* Fixed uninstall script

= 2.3.6.3 =
* SiteOrigin compatibility

= 2.3.6.2 =
* Hotfix for bug introduced in 2.3.6.1 ( Important update! )

= 2.3.6.1 =
* Core update to try reduce bots consumption
* WooCommerce related products widget fix

= 2.3.6 =
* Core updates
* Added php functions for ajax mode

= 2.3.5 =
* Update debug data page and Core files
* Fix for PHP 7.2

= 2.3.4.4 =
* Fix issue with ACF latest version
* Clean up database of old wp_session records

= 2.3.4.3 =
* Core update that fix headers sent error and exclude geolocation feature not working

= 2.3.4.2 =
* Fixed core bug that on certain php version geo target function won't return results
* Added cache bust for admin assets

= 2.3.4.1 =
* Fixed issue with ACF free version that was breaking javascript

= 2.3.4 =
* Updated Settings page to improve performance
* Fixed error with timezone function

= 2.3.3 =
* Fixed minor errors
* Update core sessions library

= 2.3.2 =
* Moved all js to footer
* Fixed bug with locales and cache mode that could lead into fatal error

= 2.3.1 =
* Fix bug introduced with locales detection

= 2.3 =
* Added locale option for shortcodes
* Also results locale now it's changed automatically with wordpress language
* Fixed bug where geotargeted posts not working with custom queries inside a post
* Improved debug page
* Core updates

= 2.2.1 =
* Core updates
* Fixed bug on geo posts when used with geo redirects

= 2.2.0.1 =
* Fixed issue with WpRocket cache
* Region names are now slugs

= 2.2 =
* Improved shortcodes generator popup codebase
* Fixed debug mode showing on ajax mode when disabled
* Minor bugfixes
* Improved compatibility with geo blocker
* Core update

= 2.1.2.1 =
* Fixed issues with subscription databases

= 2.1.2 =
* Geo flags new addon
* Minor bugfix
* Update core files. Sessions are now DB stored

= 2.1.1 =
* Updated core files
* Visual composer components updated
* WpEngine Support of Geoip (enterprise and business accounts)

= 2.1.0.1 =
* Plugin didn't pack core updates

= 2.1 - Sept 12 =
* Filter by zip function and shortcodes
* Time zone, lat and lng shortcodes
* Admin access roles can be edited now
* Updated core files
* Minor bugfixes

= 2.0.4.5 - August 30 =
* Updated core files
* Minor bugfixes

= 2.0.4.4 - July 5 =
* Updated core files
* Minor bugfixes

= 2.0.4.3 - Jun 27  =
* Fixed multiple undefined errors and warnings
* Fixed debug with query string
* Update core packages for compatibility with Wp Rocket

= 2.0.4.2 - May 23  =
* Fixed warning showing on posts pages
* Preparing plugin for compatibility with WpRocket Cache plugin
* Small bugfixes

= 2.0.4.1 - Apr 26  =
* Fix bug with ajax mdoe introduced in 2.0.4

= 2.0.4 - Apr 26  =
* Changed how settings work
* Reordered admin for upcoming GeoRedirects plugin
* Improved how cache mode works to save more credits
* Updated core files
* Fixed bug with check license admin

= 2.0.3 - Apr 26  =
* Hotfix, dropdown widget was not working

= 2.0.2 - Apr 25  =
* Fixed bug with cache mode on certain configurations
* Debug data not working on Ajax mode
* Make it clear that widget integration don't work in ajax mode

= 2.0.1 - Apr 19  =
* Different bugfixes, preparing release

= 2.0.0 - Apr 14  =
* Plugin recoded for new API
