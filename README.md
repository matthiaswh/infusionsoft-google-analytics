Infusionsoft Google Analytics
=============================

Plugin for Wordpress which provides Google Analytics ecommerce tracking for sales made through Infusionsoft's shopping cart

== Description ==

This plugin provides a shortcode which you can place on any page on your Wordpress site. When you make a sale through Infusionsoft, you can redirect users back to a "Thank You" page with the shortcode, and the plugin will connect to your Infusionsoft account, get the details of the sale, and submit them to Google Analytics' ecommerce tracking.

*   Uses the latest version (1.8.3) of the Infusionsoft SDK.
*   All connections to your Infusionsoft application are RSA encrypted and secured using Infusionsoft's security certificates.
*   Easy setup, see the "Installation" tab.
*   Requires the Google Analytics tracking code to be installed on your site.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the `[infusionsoft-google-analytics]` shortcode on any page on your site.
4. Log into your Infusionsoft account and go to E-Commerce Setup >> Shopping Cart Themes >> Edit >> Settings.
5. For the Thank You URL, enter the url to the page on your site that has the Infusionsoft Google Analytics shortcode.
6. Go to Admin >> Settings >> Application.
7. Type in any phrase into the "API Passphrase" box and hit Save.
8. After the page reloads, copy the "Encrypted Key" and paste it into the "API Key" box in the Infusionsoft Google Analytics settings.
9. Enter your application name, this is generally the first part of the URL you use to access Infusionsoft (i.e. http://aa667.infusionsoft.com/).
10. Press save options. If all went well, you should see a success message. Your sales will now be tracked.

== Frequently Asked Questions ==

= I'm sure I've entered my API key correctly, but the plugin won't connect =

Sometimes Infusionsoft won't accept API key requests immediately after generating a key. Wait a few hours and try again. If that doesn't work, try generating a new key, or contact Infusionsoft support.

= How do I know it's working? =

Google Analytics Debugger is a great plugin for Chrome which will show you what tracking data your pages are sending to Google.


== Changelog ==

= 0.1 =
* Initial release
