<?php
/**
 * Class Google\Native_Lazyload\Tests\PHPUnit\Framework\Unit_Test_Case
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   GNU General Public License v2 (or later)
 * @link      https://wordpress.org/plugins/native-lazyload
 */

namespace Google\Native_Lazyload\Tests\PHPUnit\Framework;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

/**
 * Class representing a unit test case.
 */
class Unit_Test_Case extends TestCase {

	/**
	 * Sets up the environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		// We don't care about testing the following functions, so they should just be available.
		Monkey\Functions\stubs(
			[
				// Return original first parameter for these.
				'esc_attr',
				'esc_html',
				'esc_js',
				'esc_textarea',
				'__',
				'_x',
				'esc_html__',
				'esc_html_x',
				'esc_attr_x',

				// Return value determined by callback for these.
				'_n'                  => function( $single, $plural, $number ) {
					return 1 === $number ? $single : $plural;
				},
				'_nx'                 => function( $single, $plural, $number ) {
					return 1 === $number ? $single : $plural;
				},
			]
		);
	}

	/**
	 * Tears down the environment after each test.
	 */
	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Asserts that the contents of two un-keyed, single arrays are equal, without accounting for the order of elements.
	 *
	 * @param array $expected Expected array.
	 * @param array $actual   Array to check.
	 */
	public static function assertEqualSets( $expected, $actual ) {
		sort( $expected );
		sort( $actual );
		self::assertEquals( $expected, $actual );
	}

	/**
	 * Asserts that the contents of two keyed, single arrays are equal, without accounting for the order of elements.
	 *
	 * @param array $expected Expected array.
	 * @param array $actual   Array to check.
	 */
	public static function assertEqualSetsWithIndex( $expected, $actual ) {
		ksort( $expected );
		ksort( $actual );
		self::assertEquals( $expected, $actual );
	}
}
