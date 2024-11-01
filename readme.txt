=== WC Browser Notify ===
Contributors: wooninjas, adeelraza_786hotmailcom
Tags: tabs, woocommerce, reviews, description
Requires at least: 4.0
Tested up to: 6.0.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

WC Browser Notify (WCBN) is an easy way to let your users be notified of specific WooCommerce or browser actions, through an alert popup. You can select the specific WooCommerce actions (called triggers) and associate them with popup alerts which you can customize in the WP editor.

= Features: =

* Create as many popup templates as you want.
* Associate any popup with a specific WooCommerce action.
* You can also associate popups with browser actions like "on open window" OR  "on scroll".
* Insert delay in seconds for the popup to appear on the page.
* Option to override the “on page load” trigger with a WooCommerce action.
* Works with WooCommerce 2.6.x

= Plan for next version release: =

* Option to select a page/post/product for the on page load trigger.

== Installation ==

Before installation please make sure you have latest WooCommerce installed.

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

= Usage =

* After installation, you will see Browser Notify Triggers and Browser Notify Popups under WooCommerce Menu.
* From here, you can first create your desired popups in Browser Notify Popups, afterwards you can move to the Browser Notify Triggers to create the trigger to display your selected popup to the user.
* This trigger will be fired when a user visits a single product page on your WooCommerce site.
* Delay (in secs) : This field can insert a delay after which the popup is displayed to a user
* Override "On page load" trigger : Consider the case where you have already created a popup for the on page load trigger and then create a popup for a WC action which triggers on a page load too. There would be a conflict if both the popups are allowed to be displayed on frontend so this checkbox provides with the option to have the WC action trigger take precedence over the page load trigger. If you leave the checkbox empty then the popup associated with the ‘on page load’ trigger will have precedence.
* You can modify the contents in the WYSIWYG editor, adding shortcodes, images etc

= Explanation of all triggers : =

**Browser Triggers:**

* On page load : Triggers whenever page loads
* On scrolling through :  Triggers when a user scrolls half way through a page

**WC Action Triggers:**

* On Add to Cart(Non-AJAX) : Triggers when a user clicks 'Add to cart' button. Note that this will work only with the Non-Ajax implementation of ‘Add to cart’ so check your theme to see whether this is an issue or not. WooCommerce also has an Ajax ‘Add to cart’ setting for product category pages. See: WooCommerce -> settings -> products -> display ->  enable AJAX add to cart buttons on archives
* On Proceed to Checkout : Triggers when a user clicks the proceed to checkout button
* On Place Order : Triggers when a user clicks on the Place order button and is redirected to the ‘Order Received’ page (this is after the payment)
* On Cart Empty Page : Triggers when a user visits the cart page while it is empty
* On Product List Page(if products exist) :  Triggers when a user visits the product listing/archive page and the product list is not empty
* On Single Product : Triggers when a user visits the single product page

**Triggers Disabled After Association:**

* Once a trigger is created and associated with a popup, you will not be able to target the same trigger again for some other popup. This is to ensure that there is no conflict when rendering the popup on frontend.

== Frequently Asked Questions ==

= Can I use my existing WordPress theme? =

Yes! WC Browser Notify works out-of-the-box with nearly every WordPress theme.

== Screenshots ==

1. Browser Notify Triggers and Browser Notify Popups under WooCommerce Menu.
2. Browser Notify Triggers
2. popup will look like this

== Changelog ==


= 1.0.1 =
* Fix: Minor issues
* Fix: WooCommerce dependency

= 1.0.0 =
* Initial