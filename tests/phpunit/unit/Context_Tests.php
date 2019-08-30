<?php
/**
 * Class Google\Native_Lazyload\Tests\PHPUnit\Unit\Context_Tests
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload
 */

namespace Google\Native_Lazyload\Tests\PHPUnit\Unit;

use Google\Native_Lazyload\Tests\PHPUnit\Framework\Unit_Test_Case;
use Google\Native_Lazyload\Context;
use Brain\Monkey\Functions;

/**
 * Class testing the Context class.
 */
class Context_Tests extends Unit_Test_Case {

	const TEST_MAIN_FILE = 'wp-content/plugins/native-lazyload/native-lazyload.php';

	private $context;

	public function setUp() {
		parent::setUp();
		$this->context = new Context( static::TEST_MAIN_FILE );
	}

	public function test_basename() {
		$expected = explode( '/plugins/', static::TEST_MAIN_FILE )[1];

		Functions\expect( 'plugin_basename' )
			->once()
			->with( static::TEST_MAIN_FILE )
			->andReturn( $expected );

		$this->assertSame( $expected, $this->context->basename() );
	}

	public function test_path() {
		$path = explode( '/', static::TEST_MAIN_FILE );
		array_pop( $path );
		$path = implode( '/', $path ) . '/';

		Functions\expect( 'plugin_dir_path' )
			->once()
			->with( static::TEST_MAIN_FILE )
			->andReturn( $path );

		$this->assertSame( $path . 'assets/my-file.js', $this->context->path( '/assets/my-file.js' ) );
	}

	public function test_url() {
		$url = explode( '/', static::TEST_MAIN_FILE );
		array_pop( $url );
		$url = 'https://example.com/' . implode( '/', $url ) . '/';

		Functions\expect( 'plugin_dir_url' )
			->once()
			->with( static::TEST_MAIN_FILE )
			->andReturn( $url );

		$this->assertSame( $url . 'assets/my-file.js', $this->context->url( '/assets/my-file.js' ) );
	}

	public function test_is_amp_without_plugin() {
		$this->assertFalse( $this->context->is_amp() );
	}

	public function test_is_amp_with_plugin() {
		Functions\expect( 'is_amp_endpoint' )
			->once()
			->withNoArgs()
			->andReturn( true );

		$this->assertTrue( $this->context->is_amp() );
	}
}
