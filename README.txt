=== ShinyStat Analytics ===
Contributors: shinystat
Donate link: http://www.shinystat.com/
Tags: analytics, marketing, seo, heatmaps, conversions, session recording, counter, statistics, tracking, visits, optimize, uniques, visitors, stats, engagement, recommendation, web analytics, marketing automation, ecommerce, ecommerce tracking, javascript error tracking, surveys, artificial intelligence
Requires at least: 3.1.0
Tested up to: 6.6
Stable tag: 1.0.15
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin to activate the ShinyStat Analytics services on your website.

== Description ==

Activate the ShinyStat Analytics plugin and access to **Web Analytics** and **On-site Marketing Automation** tools provided by ShinyStat.

This plugin makes it easy to connect your website to ShinyStat services, so you can start building your data-driven strategies to improve your business.

Analytics services measure accesses to your website in real time, allowing you to check the progress of traffic in order to improve its performance.

ShinyStat Widget that shows the **counter icon** can be inserted into your website pages, so both you and your visitors can see immediately how traffic is evolving.

The ShinyStat dashboards provide a clear and immediate interpretation of all the main metrics related to traffic and performance of the site, allowing to perform punctual analysis on a census basis through dynamic and customizable graphic representations.

The **Keyword Not Provided** report allows you to obtain a probabilistic projection of the keywords used to reach the site by users on search engines, thus constituting an essential tool for SEO and SEM managers.

If the WooCommerce plugin is installed on your website, you can also view statistics about the monetary **Conversions** completed by your visitors.
To collect data about conversions, the minimum version of WooCommerce is 3.3.0.

Offering a complete statistical picture of the main characteristics of the Audience on each portion of the site, it is therefore the ideal tool for defining strategies, the selection of contents and their optimal allocation on the web pages of your store.

Finally, thanks to Machine Learning and Artificial Intelligence algorithms, ShinyStat also provides On-site Marketing Automation ShinyEngage tool.
With this tool you can send specific messages to targeted users at the most appropriate time in order to increase purchases, for example when user exit intent is detected without completing a purchase.

ShinyStat Analytics plugin is compatible with Wordpress **AMP** plugin, allowing to collect traffic data from AMP pages without any additional configuration.

== Installation ==

1. Install the plugin ShinyStat Analytics either via the WordPress.org plugin repository or by uploading the 'shinystat-analytics' directory to your server inside the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click on 'Settings' link and fill out the required fields to configure the plugin.
4. Configure the 'ShinyStat Analytics' item in the 'Widgets' section.
5. Verify the data acquisition by accessing the ShinyStat reports.

Details about the installation procedure can be found [here](https://addons.shinystat.com/wordpress/files/install.pdf).

== Frequently Asked Questions ==

= Do I need a ShinyStat account before the plugin installation? =

To configure the plugin and collect the traffic information, a ShinyStat account is required.  
Subscribe a free or business account by filling out the related forms [here](https://www.shinystat.com)

== Changelog ==

= 1.0.15 =
* Small fix for php 8.2 compatibility.

= 1.0.14 =
* Extended rest api to get cart content and to change product quantity.
* Compatibily with woocommerce 8.x

= 1.0.13 =
* Updated css rule to improve compatibily with yoast plugin.

= 1.0.12 =
* Added function shn_engage.get_product_details that calls rest api to retrieve product information from id.

= 1.0.11 =
* Compatibility with not defined advanced options section.

= 1.0.10 =
* Added advanced options section in settings to concatenate parameters in tag source request.

= 1.0.9 =
* Updated timestamp in shn_engage.cart_content when add-to-cart is done by entire page refresh (not by ajax request wc-add-to-cart).

= 1.0.8 =
* Fixed timestamp update when cart is empty in get_cart_content js function

= 1.0.7 =
* Compatibility with wp-rocket plugin

= 1.0.6 =
* Fixed empty scripts cleaning that are in page.

= 1.0.5 =
* Fixed to update shn_engage.get_cart_content function result when cart and its items are null.

= 1.0.4 =
* Fixed to update shn_engage.get_cart_content function result when cart is null.

= 1.0.3 =
* Fixed nonce to apply-coupon.
  For the application of coupon code to the current session, made an additional request to the server to get a valid nonce. When the cart session is still not initialized at the time of page request the computed nonce is not valid to apply coupon with the wc-ajax request.

= 1.0.2 =
* Widget for both free and business accounts

= 1.0.1 =
* Added parameter related to version
* Added complete list of products in conversion report

= 1.0.0 =
* First release.


== Upgrade Notice ==

= 1.0.15 =
Small fix for php 8.2 compatibility.

= 1.0.14 =
Compatibily of shn_engage functions with woocommerce 8.x.

= 1.0.13 =
Updated style rules to improve compatibily with yoast plugin.

= 1.0.12 =
Added function shn_engage.get_product_details to get product information from id.

= 1.0.11 =
Compatibility with not defined advanced options section.

= 1.0.10 =
Added advanced options section for additional parameters in tag script request.

= 1.0.9 =
Updated timestamp in shn_engage.cart_content when add-to-cart is done by page refresh.

= 1.0.8 =
Fixed timestamp update when cart is empty in get_cart_content js function

= 1.0.7 =
Compatibility with wp-rocket plugin

= 1.0.6 =
Fixed empty scripts cleaning that are in page.

= 1.0.5 =
Fixed to get valid cart content when cart items are null.

= 1.0.4 =
Fixed to get valid cart content when cart is null.

= 1.0.3 =
Fixed to apply coupon successfully also when woocommerce cart session is still not initialized.

= 1.0.2 =
Widget icon can be inserted in pages for both free and business accounts.

= 1.0.1 =
Minor changes to add details in report pages.

= 1.0.0 =
First release.

== Screenshots ==

1. ShinyStat dashboard shows traffic information highlighting devices, provenience, pages, browsers, socio-demographic estimations and much more.
2. ShinyStat Widget that shows the counter icon can be inserted into your website pages.
3. ShinyStat provides detailed information about the user interation with the website. For example, you can see click heatmaps showing where users engage the most.
4. For e-commerce website, the section related to monetary conversions provides details on the completed purchases.
5. ShinyStat dashboard also shows statistical distribution of the Audience by gender and age groups. All this represents an excellent basis to choose the best communication strategy to sift through the offers, the language to be adopted, the services to be offered.
6. The Keyword Not Provided report allows you to obtain a probabilistic projection of the keywords used to reach the site by users on search engines.
7. ShinyStat Survey dashboard allows you to create online surveys for your visitors with easy drag-and-drop survey builder and see in real-time the received responses.
8. Reach your users with specific messages and deliver communication content, at the most appropriate time, only to those showing a specific behavior.

