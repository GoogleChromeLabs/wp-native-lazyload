[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/native-lazyload.svg?maxAge=2592000)](https://wordpress.org/plugins/native-lazyload/)
[![WordPress](https://img.shields.io/wordpress/v/native-lazyload.svg?maxAge=2592000)](https://wordpress.org/plugins/native-lazyload/)
[![Build Status](https://api.travis-ci.org/GoogleChromeLabs/wp-native-lazyload.png?branch=master)](https://travis-ci.org/GoogleChromeLabs/wp-native-lazyload)

# Native Lazyload

Lazy-loads media using the native browser feature. [Learn more about the new `loading` attribute](https://web.dev/native-lazy-loading) or [view the WordPress core ticket](https://core.trac.wordpress.org/ticket/44427) where inclusion of a similar implementation in WordPress core itself is being discussed.

If the `loading` attribute is not supported by the browser, the plugin falls back to a JavaScript solution based on `IntersectionObserver`. For the case that JavaScript is disabled, but the `loading` attribute _is_ supported by the browser, a `noscript` variant of the respective element will be added that also includes the `loading` attribute without any further changes.

## "Native" means "Fast"

If you have found your way over here, you are probably aware of how crucial performance is for a website's user experience and success. You might also know that lazy-loading is a key feature to improve said performance. However, the solutions for lazy-loading so far still added a bit of overhead themselves, since they relied on loading, parsing and running custom JavaScript logic, that may be more or less heavy on performance.

This plugin largely does away with this pattern. It relies on the new [`loading`](https://github.com/whatwg/html/pull/3752) attribute, which makes lazy-loading a native browser functionality. The attribute is already supported by Chrome, and will be rolled out to other browsers over time. The solution being "native" means that it does not rely on custom JavaScript logic, and thus is more lightweight. And "more lightweight" means "faster".

Last but not least, a neat thing to keep in mind is that this plugin will essentially improve itself over time, as more browsers roll out support for the `loading` attribute.

## Requirements

* WordPress >= 4.7
* PHP >= 7.0

## Contributing

Any kind of contributions to Native Lazyload are welcome. Please [read the contributing guidelines](https://github.com/GoogleChromeLabs/wp-native-lazyload/blob/master/CONTRIBUTING.md) to get started.

## Credit

This plugin is partly based on logic from [WP Rig](https://github.com/wprig/wprig/blob/v2.0/inc/Lazyload/Component.php) as well as recommendations from [web.dev](https://web.dev/native-lazy-loading) and [developers.google.com](https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/).
