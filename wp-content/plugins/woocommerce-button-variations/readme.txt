=== WooCommerce Button Variations ===
Tags: woocommerce, button variations, swatches, buttons

== Description ==

Enable variations to be displayed as buttons. This is useful if you want to use color swatches or letters instead of words, also it can be useful to enhance the mobile experience.
*This version supports Woocommerce 2.4.0 and up.*

== Installation ==

1. Download the plugin file to your computer and unzip it
    * It is recommended that if you had a previous version you delete that one before uploading this one.
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

== Frequently Asked Questions ==

This plugin works with Variable Products only.

This plugin has been tested with the latest WooCommerce version.

You can find more support and tutorials at http://support.candlestudio.net/

== Changelog ==
=1.4.1 - 30-SEP-2016
* Fix - Missing tag.

=1.4 - 30-AUG-2016
* Feature: Disabled Products now handled also in General Settings tab.

=1.3 - 26-AUG-2016
* Feature: Add text underneath Color/Image buttons.
* Feature: Add margins between terms.
* Feature: Option to keep aspect ratio for images with fixed Height and Width.
* Feature: Added class for Out of Stock variations (non-ajax).
* Feature: Click on already selected terms resets form.
* Feature: Set opacity value.
* Feature: Preview buttons enhancements.
* Fix: Some settings (opacity, border, checkmark, among others) now impact more button types.
* FIX: Disabled term click now resets selection. Click on disabled resets form.
* FIX: Removed not-allowed cursor from disabled terms.
* FIX: Added rounded option for radius (Images and Color buttons).
* FIX: Settings re-arrangement & major code architecture changes.

=1.2 - 4-AUG-2016
* Feature: Add button images.

=1.1.2 - 16-MAY-2016
* Feature: Disabled buttons now show cursor:not-allowed, when no matching variation is found.
* FIX: Transparent buttons bug

=1.1.1 - 19-APR-2016
* FIX: image not showing for large number of variations.
* FIX: Revamp and code cleaning in js file.

=1.1.0 - 11-APR-2016
* Version 1.1.0 is a minor enhancement.
* Feature: Using filter instad of action to hook to Product page.
* FIX: Revamp of js to match WCs features.
* FIX: Removing futher theming support, only css theming available now.
* FIX: Code cleaning.

=1.0.14 - 28-MAR-2016
* FIX: Image display and updating.
* FIX: Enqueued underscore.js template for variations (wp.template).
* FIX: Added additional i18n text.
* FIX: Static functions for wc_ea_plugin_action_links and wc_ea_plugin_row_meta.

=1.0.13 - 31-OCT-2015
* FIX: Variation descriptions were not showing in frontend.

=1.0.12 - 26-OCT-2015
* FIX: Typo.

=1.0.11 - 23-OCT-2015
* FIX: Text button border color not being applied in frontend.

=1.0.10 - 16-OCT-2015
* FIX: Uppercase attributes causing javascript endless loop.
* FIX: Delete unused file.

=1.0.9 - 10-OCT-2015
* FIX: Allow default variations.
* FIX: Homologated variable.php template to current WC version. This version of the plugin works with WC 2.4.0 and up.
* FIX: Code cleanup.

= 1.0.8 - 15-SEP-2015
* FIX: Allow Shop Manager role to see the admin options.

= 1.0.7 - 7-JUL-2015
* FIX: Error with the use of empty() in class-wc-extended-attributes-admin-panel.php

= 1.0.6 - 31-JUL-2015
* Fix: WP_DEBUG related notices and warnings

= 1.0.5 - 27-JUL-2015
* Feature: Button preview: preview the way your text button will look like.
* Feature: Button sizing: select your button's size.
* Feature: Border radius: set rounded borders for your buttons.
* Feature: Border sizing: set the button's border size.
* Feature: Opacity for disabled options.Guide users to only select available options.

= 1.0.4 - 16-JUL-2015
* Fix - Rebranded plugin

= 1.0.3 - 28-APR-2015
* Fix - Removed the opacity values for disabled button.

= 1.0.2 - 27-APR-2015
 * Fix - Issue with strip_slashes_recursive when it is already declared by another plugin.

= 1.0.1 - 15-DEC-2014
 * Fix - Initialize text buttons styling options by default.
 * Fix - Ajax 'Disable Buttons for this product' bug.

= 1.0.0 - 14-NOV-2014 =
 * Initial release.