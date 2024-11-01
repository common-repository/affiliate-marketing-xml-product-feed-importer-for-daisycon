=== Affiliate marketing product feed importer Daisycon ===
Contributors: Daisycon 
Tags: Affiliate marketing, productfeed, XML, Daisycon
Requires at least: 3.4.2
Tested up to: 3.8.1
Donate link: 
Stable tag: 2.5
Author: Daisycon
Author URI: http://www.daisycon.com
License: Daisycon

Affiliate marketing plugin for Daisycon publishers. Load Daisycon XML product feeds into your Wordpress site. The plugin also handles voucher codes.

== Description ==

The Daisycon affiliate marketing XML product feed importer is a plugin that helps non-technical affiliates to use Daisycon XML product feeds. This plugin is available in Dutch, English, French and German.

Click [here](http://plugin.affiliateprogramma.eu/ "Demo Page") for a demo page.

With the plugin you can easily load Daisycon XML product feeds in your Wordpress website, where after you can search in the feeds. Use short tags to add the products you want to your Wordpress website.

Next to the XML product feed importer you can also add all advertisers from one category on your website using a single short tag. Advertisers are automatically ranked on your personal eCPC.

The plugin also contains a simple module to handle voucher codes and an option to automatically replace normal links with affiliate links.

To use this plugin you must subscribe as affiliate at Daisycon via http://www.daisycon.com/en/publishers/.

Daisycon is active as an affiliate network in the Netherlands, Germany, Belgium and France.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

The plugin appears below in the left menu.

== Frequently Asked Questions ==

= A) The plugin is not working =

**1. Check your login name (must be an e-mail address) and password.**

**2. Make sure you downloaded the right program feed:**

- 2.1 Make sure you filtered on "Subscription Approved" (Aanmelding Goedgekeurd)

- 2.2 Make sure you selected XML

**3. If downloading programs, actioncodes or products isn't working you can try the following:**

Create a .php file with Notepad with the following code:
`<?php
if( ini_get('allow_url_fopen') ) {
    echo "allow_url_fopen is on.";
}
else {
    echo "allow_url_fopen is off.";
}
?>`

Upload the file to your website and visit the file (e.g. www.yourwebsite.com/yourfile.php) and check for the message. If allow_url_fopen is off you have to contact your hosting company. If allow_url_fopen is on, proceed to the next step.

**4. Click on "Alle producten ophalen" in the menu item "Instellingen" and go to:**
- Google Chrome: Right click -> Inspect element (see screenshot below)
- Firefox: Right click -> Inspect element (Only possible with a plugin like Firebug)
- Internet Explorer: Use the F12 key to open the developers tab

Within this function click on the Console tab. Any errors from the tool (and your Wordpress website) will appear here. If the console shows any errors, please create a screenshot and send this to support@daisycon.com.

= B) Voucher codes do not work =
1. Make sure you downloaded the program feed first

2. Make sure the media you selected is subscribed to voucher codes

3. Make sure you selected file format XML.

== Screenshots ==

1. The admin view of the product page.
2. The website view of the product shorttag.

== Changelog ==

= 2.5 =
* Important update for downloading feeds, products, logging in and compatibility with Wordpress version 3.8 and 3.8.1. Update is required!
* Added new translations

= 2.4.2 =
* Small bugfix for logging in and receiving the feeds

= 2.4.1 = 
* Bugfix for version 2.4. Please update if you are using 2.4!

= 2.4 =
* Fixes for Wordpress version 3.7 and 3.7.1

= 2.3 =
* Replaced deprecated functions
* Added French, English and German languages

= 2.2 = 
* Code clean-up
* Updated the example views at Producten and Stylesheets
* Load more products button added to the automatically created product lists. Style of the button can be changed in the Stylesheets menu
* Checkboxes added to easily select programs to get products. Selected checkboxes will be saved to the database for future use.
* Easily select a lot of programs to download the products with checkboxes
* Modified the default stylesheets to a better and clear design
* Added two files that can be extended to use as a cronjob file
* Modified the loading icon when downloading products to show a green icon when finished downloading
* Modified the stylesheets to improve compatibility with Wordpress themes
* Added tooltips to improve usability
* Added option to change the time-out between downloading products
* Added a button to download actioncodes at the Actioncodes page
* Added an alternative way to download programs, products and actioncodes (instead of file_get_contents) with the Wordpress functions wp_remote_get and wp_remote_retrieve_body  
* Added confirmation messages for major changes in the plugin (for example resetting the stylesheets)
* Changed siteurl to wpurl in hyperlinks

= 2.1.1 =
* Fix for importing actioncodes

= 2.1 =
* Fixes for the stylesheet of the product lists
* Added classnames to the product lists for custom CSS
* Added a new shorttag to shortcode the products shortcode to prevent issues with WooCommerce that uses the same shortcode. The products shortcode can still be used on websites, but when generating a new list the new shortcode will show.
* Changes in the layout when installing the plugin for a more user-friendly experience
* Added loading icons on multiple screens
* Several bugfixes

= 2.0 =
* Added the feature to automatically download the feeds.
* Several bugfixes

= 1.3.1 =
* Major bugfix

= 1.3 =
* Added a new functionality to easily create lists of products based on a search term.
* Fix for generating products when pressing the Enter key.
* Several bugfixes

= 1.2 = 
* Fix for importing images from the productfeeds
* Fix for including Javascript files, deleted unnecessary code
* Fix for default sorting actioncodes
* Added tablesorter to "Programma's", "Actiecodes" and "Categorieën" to sort the table when clicking on the header of the table
* Several bugfixes

= 1.1 =
* [Linkreplacer](http://www.daisycon.com/nl/blog/linkreplacer-wordpress-plugin "Linkreplacer")
* Several bugfixes

= 1.0 =
* Stable release

== Upgrade Notice ==

= 2.5 =
Important update for downloading feeds, products and logging in. Update is required!

= 2.4.1 = 
Bugfix for version 2.4. Please update if you are using 2.4!

= 2.4 = 
Upgrade the plugin if you are using Wordpress 3.7 or 3.7.1

= 2.3 =
Upgrade the plugin if you want to use it in different languages.

= 2.2 =
A lot of updates

= 2.1.1 =
Fix for importing actioncodes

= 2.1 =
A lot of changes, including a minor update for the product lists

= 2.0 =
This version adds the feature to automatically download the feeds.

= 1.3.1 = 
Update to this version for a major bugfix.

= 1.3 = 
This version adds a new functionality to easily create product lists based on a search term.

= 1.2 =
This version contains a bugfix for importing images and an improvement for including Javascript files

= 1.1 =
This version adds the Linkreplacer tool and contains several bugfixes.

= 1.0 =
Stable release.