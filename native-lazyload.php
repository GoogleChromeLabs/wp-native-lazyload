<?php
/**
 * Plugin initialization file
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload/
 *
 * @wordpress-plugin
 * Plugin Name: Native Lazyload
 * Plugin URI:  https://wordpress.org/plugins/native-lazyload/
 * Description: Lazy-loads media using the native browser feature.
 * Version:     1.0.2
 * Author:      Google
 * Author URI:  https://opensource.google.com
 * License:     Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: native-lazyload
 */

/* This file must be parseable by PHP 5.2. */

/**
 * Loads the plugin.
 *
 * @since 1.0.0
 */
function native_lazyload_load() {
	if ( version_compare( phpversion(), '7.0', '<' ) ) {
		add_action( 'admin_notices', 'native_lazyload_display_php_version_notice' );
		return;
	}

	if ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {
		add_action( 'admin_notices', 'native_lazyload_display_wp_version_notice' );
		return;
	}

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	} elseif ( ! class_exists( 'Google\\Native_Lazyload\\Plugin' ) ) {
		$plugin_dir = plugin_dir_path( __FILE__ );
		require_once $plugin_dir . 'src/Plugin.php';
		require_once $plugin_dir . 'src/Context.php';
		require_once $plugin_dir . 'src/Lazy_Loader.php';
		require_once $plugin_dir . 'src/Lazy_Load_Script.php';
	}

	call_user_func( [ 'Google\\Native_Lazyload\\Plugin', 'load' ], __FILE__ );
}

/**
 * Displays an admin notice about an unmet PHP version requirement.
 *
 * @since 1.0.0
 */
function native_lazyload_display_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			sprintf(
				/* translators: 1: required version, 2: currently used version */
				__( 'Native Lazyload requires at least PHP version %1$s. Your site is currently running on PHP %2$s.', 'native-lazyload' ),
				'7.0',
				phpversion()
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Displays an admin notice about an unmet WordPress version requirement.
 *
 * @since 1.0.0
 */
function native_lazyload_display_wp_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			sprintf(
				/* translators: 1: required version, 2: currently used version */
				__( 'Native Lazyload requires at least WordPress version %1$s. Your site is currently running on WordPress %2$s.', 'native-lazyload' ),
				'4.7',
				get_bloginfo( 'version' )
			);
			?>
		</p>
	</div>
	<?php
}

native_lazyload_load();
