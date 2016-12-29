=== jQuery Responsive Select Menu ===
Contributors:      McGuive7, MIGHTYminnow
Donate link:       http://mightyminnow.com
Tags:              responsive, mobile, navigation, menu, select, mobile, tablet, phone, iPhone, android, drop-down, jQuery
Requires at least: 3.0
Tested up to:      4.1
Stable tag:        1.5.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Automatically turns your standard WordPress navigation menus into responsive select / drop-down menus when the screen is below a certain width. 

== Description ==

**Do you find this plugin helpful? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/jquery-responsive-select-menu).**

The perfect solution to make your site responsive for mobile devices like phones and tablets. jQuery Responsive Select Menu lets you make your WordPress menus responsive by turning them into drop-down `<select>` elements below a width of your choosing.

= Features =
* Fully customizable - choose width, custom menu text, which menus to affect, and more.
* Works for ALL menus, whether custom WordPress 3 or automatically generated.
* Lightweight - jQuery functionality is less than 3kb.

**See the section on [Installation](http://wordpress.org/plugins/jquery-responsive-select-menu/installation/) for details on setting up your responsive mobile select menu.**

= Translations/Languages =
* English
* Spanish (Andrew Kurtis, [WebHostingHub](http://www.webhostinghub.com/))
* German ([Ov3rfly](https://profiles.wordpress.org/ov3rfly/))
* Russian (Олег П)

Please file a [support request](http://wordpress.org/support/plugin/jquery-responsive-select-menu) if you are interested in translating this plugin into another language.

Banner photo by [Susannah Kay](http://susannahkay.com).

== Installation ==
= Installation =
1. Install and activate the plugin.
2. Configure the plugin via **Settings > jQuery Responsive Select Menu**.

= Settings =
**Menu Container(s) Class / ID**  
For each menu you wish to make responsive, enter the class or ID of an element containing that menu's &lt;ul&gt; element. For example, say you have the following HTML markup:

	<nav class="nav-primary">
		<div class="wrap">
			<ul class="menu">
				<li><a>Menu Item 1</a></li>
				<li><a>Menu Item 2</a></li>
				<li><a>Menu Item 3</a></li>
			</ul>
		</div>
	</nav>

You could enter `.nav-primary` or even `.nav-primary .wrap` for the Menu Container(s) Class / ID setting. If you have multiple menus you'd like make responsive, simply enter a comma-separated list.

Note: you may need to use a tool like Chrome inspect or Firebug to determine the appropriate class / ID for your menus.

**Maximum Menu Width**  
The width at which you want your normal menu to switch to the responsive, drop-down select menu.

**Sub Item Spacer**  
The character used to denote sub-items in your drop-down select menus.

**First Term**  
The text for the select menu's top-level "dummy" item. If left blank, the drop-down select's item will be the first item in your normal navigation menu.

**Show Current Page**  
If checked, your drop-down select menu will show the current page. If unchecked, the menu will display the top-level "dummy" item.

**Omit Placeholder Items**  
If checked, the generated drop-down menu won't include any menu items whose `href` attribute is empty or "#".

== Screenshots ==
1. Normal WordPress navigation.
2. Responsive select / drop-down menu at user-specified mobile width.
3. Sample first item and sub-item spacer at responsive / mobile sizes.

== Changelog ==

= 1.5.0 =
* Add Russian translation.

= 1.4.2 =
* Improve behavior for menu links with empty or # href.

= 1.4.1 =
* Fixed jQuery bug with extra comma that affected older browsers.
* Updated translations.

= 1.4 =
* Added fix for bug in which current page was not indicated (now targets both current_page_item and current-menu-item).
* Fixed flash of unstyled content issue - now adding html “jquery” class immediately.
* Added admin option to “disable” placeholder (href=“#”) menu links.
* Fixed typo in description.
* Switched from attr() to prop() for better jQuery semantics.

= 1.3.1 =
* Added German translation (thanks to Ov3rfly).
* Fixed bug in which translations weren’t being applied.
* Properly prefixed jQuery variable passed from PHP.
* Switched from plugins_init to plugins_loaded hook.

= 1.3 =
* Added Spanish translation (thanks to Andrew Kurtis).

= 1.2 =
* Added ability to omit empty "placeholder" menu items from the drop-down menu.
* Add unique class to each generated menu (e.g. jrsm-1).

= 1.1 =
* Minor jQuery modification to prevent JS error when no container is selected.

= 1.0 =
* First release

== Upgrade Notice ==

= 1.5.0 =
* Add Russian translation.

= 1.4.2 =
* Improve behavior for menu links with empty or # href.

= 1.4.1 =
* Fixed jQuery bug with extra comma that affected older browsers.
* Updated translations.

= 1.4 =
* Added fix for bug in which current page was not indicated (now targets both current_page_item and current-menu-item).
* Fixed flash of unstyled content issue - now adding html “jquery” class immediately.
* Added admin option to “disable” placeholder (href=“#”) menu links.
* Fixed typo in description.
* Switched from attr() to prop() for better jQuery semantics.

= 1.3.1 =
* Added German translation (thanks to Ov3rfly).
* Fixed bug in which translations weren’t being applied.
* Properly prefixed jQuery variable passed from PHP.
* Switched from plugins_init to plugins_loaded hook.

= 1.3 =
* Added Spanish translation (thanks to Andrew Kurtis).

= 1.2 =
* Added ability to omit empty "placeholder" menu items from the drop-down menu.
* Add unique class to each generated menu (e.g. jrsm-1).

= 1.1 =
* Minor jQuery modification to prevent JS error when no container is selected.

= 1.0 =
First Release