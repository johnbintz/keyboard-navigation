=== Keyboard Navigation ===
Contributors: johncoswell
Tags: comicpress, webcomics, posts, plugin, navigation
Requires at least: 2.6.3
Tested up to: 2.6.3
Stable tag: 0.3

Keyboard Navigation easily adds JavaScript-based keyboard navigation to a WordPress site.

== Description ==

Keyboard Navigation uses CSS3 Selectors (via the Prototype JS library) to pluck navigational elements out of your pages and assign them to keyboard shortcuts. This sort of navigation is ideal for sites which have small, sequential archives (such as Webcomic sites). The plugin offers one assistive feature: you can enable the highlighting of the hyperlink elements that are being used to generate the navigation, and you can send selection errors to the Firebug console or to alert() messages.

== Frequently Asked Questions ==

= The plugin isn't working, and I'm getting Prototype-related errors in the JavaScript error console =

Most likely you're using another plugin that's loading some other version of Prototype. Try deactivating your plugins one-by-one, especially any that mention that they provide their own version of Prototype, and see if the problem is resolved.
