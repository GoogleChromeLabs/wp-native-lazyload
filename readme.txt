=== Native Lazyload ===

Contributors:      google, flixos90
Requires at least: 4.7
Tested up to:      5.2
Requires PHP:      7.0
Stable tag:        1.0.0
License:           GNU General Public License v2 (or later)
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Tags:              lazyload, lazy, load, native, loading, images, iframes

Lazy-loads media using the native browser feature.

== Description ==

Lazy-loads media using the native browser feature. [Learn more about the new `loading` attribute](https://web.dev/native-lazy-loading) or [view the WordPress core ticket](https://core.trac.wordpress.org/ticket/44427) where inclusion of a similar implementation in WordPress core itself is being discussed.

If the `loading` attribute is not supported by the browser, the plugin falls back to a JavaScript solution based on `IntersectionObserver`.

Just activate the plugin, and all your images and iframes in post content will be loaded lazily.

== Installation ==

1. Upload the entire `native-lazyload` folder to the `/wp-content/plugins/` directory or download it through the WordPress backend.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Where are the plugin's settings? =

This plugin does not have a settings screen. Just by activating it, the plugin will work.

= How can I prevent an image or iframe from being loaded lazily? =

You can add a class `skip-lazy` to indicate to the plugin you would like to skip lazy-loading for this image or iframe.

= Does this work with AMP? =

If you use AMP, you don't actually need this, since AMP intelligently lazy-loads media out of the box. Still, the plugin is built in a way that it will not break AMP compatibility, just to make sure.

= Where should I submit my support request? =

For regular support requests, please use the [wordpress.org support forums](https://wordpress.org/support/plugin/native-lazyload). If you have a technical issue with the plugin where you already have more insight on how to fix it, you can also [open an issue on Github instead](https://github.com/GoogleChromeLabs/wp-native-lazyload/issues).

= How can I contribute to the plugin? =

If you have some ideas to improve the plugin or to solve a bug, feel free to raise an issue or submit a pull request in the [Github repository for the plugin](https://github.com/GoogleChromeLabs/wp-native-lazyload). Please stick to the [contributing guidelines](https://github.com/GoogleChromeLabs/wp-native-lazyload/blob/master/CONTRIBUTING.md).

You can also contribute to the plugin by translating it. Simply visit [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/native-lazyload) to get started.

== Changelog ==

= 1.0.0 =

* Initial release
