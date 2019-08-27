<?php
/**
 * Unit tests bootstrap script.
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   GNU General Public License v2 (or later)
 * @link      https://wordpress.org/plugins/native-lazyload
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
