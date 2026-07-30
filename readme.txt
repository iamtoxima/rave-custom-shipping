=== Rave Custom Shipping for WooCommerce ===
Contributors: toxima
Tags: woocommerce, shipping, shipping quote, custom shipping
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a selectable WooCommerce shipping method whose final fee is calculated and emailed after checkout.

== Description ==

Rave Custom Shipping adds a zero-cost shipping option to WooCommerce shipping zones.

The store owner can edit:

* The shipping method title shown to customers.
* The explanatory message shown under the method.

The default message is:

"The shipping fee will be calculated and emailed to you after you place your order."

The message is also repeated on the customer order page and in WooCommerce order emails. The plugin does not calculate the eventual shipping fee or send a separate fee quote automatically; that remains a manual store process.

== Installation ==

1. Upload the `custom-shipping-quote` folder to `/wp-content/plugins/`, or upload the plugin ZIP from Plugins > Add New > Upload Plugin.
2. Activate "Rave Custom Shipping for WooCommerce".
3. Go to WooCommerce > Settings > Shipping > Shipping zones.
4. Edit the zone where the option should be available.
5. Select "Add shipping method".
6. Choose "Rave Custom Shipping".
7. Edit the method title and customer message, then save.

== Frequently Asked Questions ==

= Does this add a shipping charge at checkout? =

No. The checkout shipping cost is zero because the final fee is still pending.

= Does this calculate or email the final fee automatically? =

No. Version 1.0.3 communicates that the fee will be handled later. It does not yet include courier-rate calculation or a separate quote-email workflow.

= Does it support WooCommerce Cart and Checkout blocks? =

Yes. WooCommerce 9.7 or newer displays the message as the shipping-rate description.

= Does it support High-Performance Order Storage? =

Yes. The plugin uses WooCommerce APIs and declares HPOS compatibility.

== Changelog ==

= 1.0.3 =

* Added complete GPL licensing and public security-reporting documentation.
* Added contribution guidance and automated PHP syntax checks.
* Added explicit WooCommerce dependency and GitHub update metadata.

= 1.0.2 =

* Added Toxima as the plugin author and linked the author profile to https://github.com/iamtoxima.

= 1.0.1 =

* Renamed the plugin and shipping method to Rave Custom Shipping.
* Clarified that the planned shipping quote and payment workflow applies only to orders that selected Rave Custom Shipping.

= 1.0.0 =

* Initial release.
* Added a zone-based zero-cost custom shipping quote method.
* Added editable title and plain-text customer message.
* Added classic checkout, block checkout, order page, and order email messaging.
* Declared HPOS and Cart/Checkout block compatibility.
