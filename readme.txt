=== WooCommerce New Customer Report ===
Contributors: skyverge
Tags: woocommerce, reports, customers, reporting
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=paypal@skyverge.com&item_name=Donation+for+WooCommerce+New+Customer+Report
Requires at least: 4.0
Tested up to: 4.4.2
Requires WooCommerce at least: 2.3
Tested WooCommerce up to: 2.5
Stable Tag: 1.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds a customer report to view new vs returning customers for a selected date range.

== Description ==

WooCommerce New Customer Report adds a "New vs Returning" customer report to the WooCommerce Reports section. WooCommerce's core Customers vs Guests report can tell you the number of new customer _accounts_, but not new customers overall. For a specified date range, this report shows you customers who made a first purchase in this time period vs customers who have made a repeat purchase.

> **Requires: WooCommerce 2.3+ and WordPress 4.0+**

= Features =
Includes options to:

 - adds a new "New vs Returning" customer report
 - lets you select date ranges to see new vs returning customer counts by day / week / etc

= How Customers are counted =
When you select a date range, the plugin takes the start date of that date range as a cut off. For all orders within the date range, it checks if the customer's billing address has placed an order before the range started. If so, the customer is counted as a returning customer. If the customer has not used this billing address to place an order prior to the start date, the customer is counted as a new customer.

This can help you determine a "real" new customer account to properly evaluate metrics like your **customer acquisition cost** since you can accurately see new customers, not just new user accounts.

= Translating =
The plugin is translation-ready and a .pot file is included. The text domain is: `woocommerce-new-customer-report`

= More Details =
 - See the [product page](http://www.skyverge.com/product/woocommerce-new-customer-report/) for full details.
 - View more of SkyVerge's [free WooCommerce extensions](http://profiles.wordpress.org/skyverge/)
 - View all [SkyVerge WooCommerce extensions](http://www.skyverge.com/shop/)

= Support Details =
We do support our free plugins and extensions, but please understand that support for premium products takes priority. We typically check the forums every few days (usually the maximum delay is one week). We will troubleshoot basic compatibility issues or questions, but will not customize reports, add new reports, or troubleshoot non-standard server configurations.

== Installation ==

1. Be sure you're running WooCommerce 2.3+ and WordPress 4.0+ in your shop.
2. Upload the entire `woocommerce-new-customer-report` folder to the `/wp-content/plugins/` directory, or upload the .zip file with the plugin under **Plugins &gt; Add New &gt; Upload**. Alternatively, you could also go to **Plugins &gt; Add New** and search for the plugin name to install directly from wordpress.org.
3. Activate the plugin through the **Plugins** menu in WordPress
4. Go to **WooCommerce &gt; Reports &gt; Customers &gt; New vs. Returning**. The new report will be shown here for the selected date range.
5. View [documentation on the product page](http://www.skyverge.com/product/woocommerce-new-customer-report/) for more help if needed.

== Frequently Asked Questions ==

= Can I use something other than billing address to determine who is counted as a "returning" customer? =
The plugin is developer-friendly and has filters in place that you could use to customize report data, but we do not have plans to change the report structure at this time.

= Does this plugin support multisite? =
If you want to use this plugin in a multi-site install, please **do not network activate it**. Activate it on a per-site basis, as reports will count new customers per-site only, not across the multi-site install.

= Can I translate the plugin? =
Yep! The plugin is translation-ready and the text domain to use is: `woocommerce-new-customer-report`

= This is handy! Can I contribute? =
Yes you can! Join in on our [GitHub repository](https://github.com/skyverge/woocommerce-new-customer-report/) and submit a pull request :)

== Screenshots ==
1. New report under **WooCommerce &gt; Reports &gt; Customers &gt; New vs. Returning**
2. New customers using a custom date range

== Changelog ==

= 2017.03.27 - version 1.1.0 =
 * Feature: Adds support for the [GitHub updater plugin](https://github.com/afragen/github-updater)
= 2016.03.30 - version 1.0.0 =
 * Initial Release
