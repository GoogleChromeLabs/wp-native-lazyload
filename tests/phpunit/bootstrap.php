<?php
/**
 * Unit tests bootstrap script.
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload/
 */

// Detect project directory.
define( 'TESTS_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) );

// Disable xdebug backtrace.
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

require_once TESTS_PLUGIN_DIR . '/vendor/autoload.php';

// PHPUnit < 6.0 compatibility shim.
require_once __DIR__ . '/phpunit-compat.php';
