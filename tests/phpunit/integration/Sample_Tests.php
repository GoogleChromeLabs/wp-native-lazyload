<?php
/**
 * Class Google\Native_Lazyload\Tests\PHPUnit\Integration\Sample_Tests
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload
 */

namespace Google\Native_Lazyload\Tests\PHPUnit\Integration;

use Google\Native_Lazyload\Tests\PHPUnit\Framework\Integration_Test_Case;

/**
 * Class containing a sample test.
 */
class Sample_Tests extends Integration_Test_Case {

	/**
	 * Performs a sample test.
	 */
	public function testNothingUseful() {
		$this->assertTrue( defined( 'ABSPATH' ) );
	}
}
