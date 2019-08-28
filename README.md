[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/native-lazyload.svg?maxAge=2592000)](https://wordpress.org/plugins/native-lazyload/)
[![WordPress](https://img.shields.io/wordpress/v/native-lazyload.svg?maxAge=2592000)](https://wordpress.org/plugins/native-lazyload/)
[![Build Status](https://api.travis-ci.org/GoogleChromeLabs/wp-native-lazyload.png?branch=master)](https://travis-ci.org/GoogleChromeLabs/wp-native-lazyload)

# Native Lazyload

Lazy-loads media using the native browser feature. [Learn more about the new `loading` attribute](https://web.dev/native-lazy-loading) or [view the WordPress core ticket](https://core.trac.wordpress.org/ticket/44427) where inclusion of a similar implementation in WordPress core itself is being discussed.

If the `loading` attribute is not supported by the browser, the plugin falls back to a JavaScript solution based on `IntersectionObserver`.

## Requirements

* WordPress >= 4.7
* PHP >= 7.0

## Contributing

Any kind of contributions to Native Lazyload are welcome. Please [read the contributing guidelines](https://github.com/GoogleChromeLabs/wp-native-lazyload/blob/master/CONTRIBUTING.md) to get started.

## Credit

This plugin is partly based on logic from [WP Rig](https://github.com/wprig/wprig/blob/v2.0/inc/Lazyload/Component.php) as well as recommendations from [web.dev](https://web.dev/native-lazy-loading) and [developers.google.com](https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/).
