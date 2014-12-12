=== Plugin Name ===
Contributors: augustinfotech
Tags: woocommerce, login, log in, logout, menu, checkout, cart, account, register, payment, links.
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Add *"WooCommerce Links"* into your WordPress menus just the way you are adding other menu items!

Features :

* With this plugin you can now add a WooCommerce links to existing WordPress menu.
* You can also add login/logout menu with auto switch functionality based on user's login state.
* Nonce token is present on logout item.

Plugin Developed by August Infotech [Visit website](http://www.augustinfotech.com)

== Installation ==

1. Upload the *"ai-woocommerce-links"* folder into the *"/wp-content/plugins/"* directory.
2. Activate the plugin through the *"Plugins"* menu in WordPress.
3. You can now add WooCommerce links in your Navigation Menus.
4. See FAQ for usage.

Plugin Developed by August Infotech [Visit website](http://www.augustinfotech.com)

== Frequently Asked Questions ==

= How does this works? =

Visit your navigation admin menu page, you got a new box including 9 links, *"shop"*, *"account"*, *"cart"*, *"log in"*, *"logout"*, *"log in/logout"*, *"checkout"*, *"payment"*, *"register"*.

* Add the link you want, for example *"Log in|Logout"*
* You can change the 2 titles links, just separate them with a | (pipe)

You can also add 9 shortcodes in your theme template or in your pages/posts. just do this :

* For theme : `<?php echo do_shortcode( '[loginout]' ); ?>`
* In you posts/pages : `[loginout]`

The 9 shortcodes are `[shop]`, `[account]`, `[cart]`, `[login]`, `[logout]`, `[loginout]`, `[checkout]`, `[payment]` and `[register]`.

* You can set 1 parameters, named *"edit_tag"*.
* Edit_tag: used to modify the tag, example <a> "class='myclass'" or "id='myid' class='myclass' rel='friend'" etc.

You can also modify the title link with [login]Click here to connect[/login] for example.

== Screenshots ==

1. The woocommerce menu in nav menu admin page

== Changelog ==

= 1.0 =
* First Version


== Upgrade Notice ==

None