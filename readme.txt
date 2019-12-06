=== Native Lazyload ===

Contributors:      google, flixos90
Requires at least: 4.7
Tested up to:      5.3
Requires PHP:      7.0
Stable tag:        1.0.2
License:           Apache License 2.0
License URI:       https://www.apache.org/licenses/LICENSE-2.0
Tags:              lazyload, lazy, load, native, loading, images, iframes

Lazy-loads media using the native browser feature.

== Description ==

Lazy-loads media using the native browser feature. [Learn more about the new `loading` attribute](https://web.dev/native-lazy-loading) or [view the WordPress core ticket](https://core.trac.wordpress.org/ticket/44427) where inclusion of a similar implementation in WordPress core itself is being discussed.

If the `loading` attribute is not supported by the browser, the plugin falls back to a JavaScript solution based on `IntersectionObserver`. For the case that JavaScript is disabled, but the `loading` attribute _is_ supported by the browser, a `noscript` variant of the respective element will be added that also includes the `loading` attribute without any further changes.

= "Native" means "Fast" =

If you have found your way over here, you are probably aware of how crucial performance is for a website's user experience and success. You might also know that lazy-loading is a key feature to improve said performance. However, the solutions for lazy-loading so far still added a bit of overhead themselves, since they relied on loading, parsing and running custom JavaScript logic, that may be more or less heavy on performance.

This plugin largely does away with this pattern. It relies on the new [`loading`](https://github.com/whatwg/html/pull/3752) attribute, which makes lazy-loading a native browser functionality. The attribute is already supported by Chrome, and will be rolled out to other browsers over time. The solution being "native" means that it does not rely on custom JavaScript logic, and thus is more lightweight. And "more lightweight" means "faster".

Last but not least, a neat thing to keep in mind is that this plugin will essentially improve itself over time, as more browsers roll out support for the `loading` attribute.

= Usage =

Just activate the plugin, and all your images and iframes in post content will be loaded lazily.

== Installation ==

1. Upload the entire `native-lazyload` folder to the `/wp-content/plugins/` directory or download it through the WordPress backend.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Where are the plugin's settings? =

This plugin does not have a settings screen. Just by activating it, the plugin will work.

= How can I prevent an image or iframe from being loaded lazily? =

You can add a class `skip-lazy` to indicate to the plugin you would like to skip lazy-loading for this image or iframe.

= This plugin still loads an extra JavaScript file! I don't want that. =

This is perfectly fair. Note that the plugin only loads the JavaScript file as a fallback for when the user's browser does not support the native `loading` attribute yet. The file includes logic to still autoload the image in a non-native way. If you prefer to purely rely on the `loading` attribute and not provide any fallback, you can easily disable it by adding a line `add_filter( 'native_lazyload_fallback_script_enabled', '__return_false' )` somewhere in your site's codebase.

= Does this work with AMP? =

If you use AMP, you don't actually need this, since AMP intelligently lazy-loads media out of the box. Still, the plugin is built in a way that it will not break AMP compatibility, just to make sure.

= Where should I submit my support request? =

For regular support requests, please use the [wordpress.org support forums](https://wordpress.org/support/plugin/native-lazyload). If you have a technical issue with the plugin where you already have more insight on how to fix it, you can also [open an issue on Github instead](https://github.com/GoogleChromeLabs/wp-native-lazyload/issues).

= How can I contribute to the plugin? =

If you have some ideas to improve the plugin or to solve a bug, feel free to raise an issue or submit a pull request in the [Github repository for the plugin](https://github.com/GoogleChromeLabs/wp-native-lazyload). Please stick to the [contributing guidelines](https://github.com/GoogleChromeLabs/wp-native-lazyload/blob/master/CONTRIBUTING.md).

You can also contribute to the plugin by translating it. Simply visit [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/native-lazyload) to get started.

== Changelog ==

= 1.0.2 =

* Fix broken images which are using data URI scheme (e.g. base64-encoded images). Props [ieim](https://github.com/ieim).
* Fix images in IE 11 not being loaded until the user starts scrolling. Props [Soean](https://github.com/Soean).
* Fix image loading script not working in IE10 and other browsers that do not support `dataset`.

= 1.0.1 =

* Improve compatibility with other plugins by using more specific class and only adding it for JS fallback.
* Run lazy-load script on `DOMContentLoaded` when necessary to improve compatibility with plugins like Autoptimize.
* Do not transform elements inside an AJAX response due to lack of predictability of the context and script execution.

= 1.0.0 =

* Initial release

== Credit ==

This plugin is partly based on logic from [WP Rig](https://github.com/wprig/wprig/blob/v2.0/inc/Lazyload/Component.php) as well as recommendations from [web.dev](https://web.dev/native-lazy-loading) and [developers.google.com](https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/).
