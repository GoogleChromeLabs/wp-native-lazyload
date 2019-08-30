<?php
/**
 * Class Google\Native_Lazyload\Tests\PHPUnit\Unit\Plugin_Tests
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload
 */

namespace Google\Native_Lazyload\Tests\PHPUnit\Unit;

use Google\Native_Lazyload\Tests\PHPUnit\Framework\Unit_Test_Case;
use Google\Native_Lazyload\Plugin;
use Google\Native_Lazyload\Context;

/**
 * Class testing the Plugin class.
 */
class Plugin_Tests extends Unit_Test_Case {

	private $plugin;

	public function setUp() {
		parent::setUp();
		$this->plugin = new Plugin( dirname( dirname( dirname( __DIR__ ) ) ) . '/native-lazyload.php' );
	}

	public function test_context() {
		$this->assertInstanceOf( Context::class, $this->plugin->context() );
	}

	public function test_register() {
		$this->plugin->register();
		$this->assertTrue( has_action( 'wp' ) );
	}
}
