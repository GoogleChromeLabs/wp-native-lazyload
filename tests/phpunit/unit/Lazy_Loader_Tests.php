<?php
/**
 * Class Google\Native_Lazyload\Tests\PHPUnit\Unit\Lazy_Loader_Tests
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload
 */

namespace Google\Native_Lazyload\Tests\PHPUnit\Unit;

use Google\Native_Lazyload\Tests\PHPUnit\Framework\Unit_Test_Case;
use Google\Native_Lazyload\Lazy_Loader;
use Google\Native_Lazyload\Lazy_Load_Script;
use Google\Native_Lazyload\Context;
use Brain\Monkey\Functions;

/**
 * Class testing the Lazy_Loader class.
 */
class Lazy_Loader_Tests extends Unit_Test_Case {

	private $context;
	private $lazy_loader;

	public function setUp() {
		parent::setUp();

		$this->context = $this->getMockBuilder( Context::class )
			->disableOriginalConstructor()
			->setMethods( [ 'basename', 'path', 'url', 'is_amp' ] )
			->getMock();

		$this->lazy_loader = new Lazy_Loader( $this->context );
	}

	public function test_register() {
		$this->context->expects( $this->once() )
			->method( 'is_amp' )
			->will( $this->returnValue( false ) );

		$this->lazy_loader->register();

		$this->assertTrue( has_action( 'wp_head', [ $this->lazy_loader, 'add_lazyload_filters' ] ) );
		$this->assertTrue( has_action( 'admin_bar_menu', [ $this->lazy_loader, 'remove_lazyload_filters' ] ) );
		$this->assertTrue( has_filter( 'wp_kses_allowed_html', 'function( array $allowed_tags )' ) );
		$this->assertTrue( has_action( 'wp_footer', Lazy_Load_Script::class . '->print_script()' ) );
		$this->assertTrue( has_action( 'wp_head', Lazy_Load_Script::class . '->print_style()' ) );
		$this->assertTrue( has_action( 'wp_enqueue_scripts', Lazy_Load_Script::class . '->register_fallback_script()' ) );
	}

	public function test_register_with_fallback_disabled() {
		$this->context->expects( $this->once() )
			->method( 'is_amp' )
			->will( $this->returnValue( false ) );

		Functions\when( 'apply_filters' )->alias(
			function( string $filter, $value ) {
				if ( 'native_lazyload_fallback_script_enabled' === $filter ) {
					return false;
				}
				return $value;
			}
		);

		$this->lazy_loader->register();

		$this->assertTrue( has_action( 'wp_head', [ $this->lazy_loader, 'add_lazyload_filters' ] ) );
		$this->assertTrue( has_action( 'admin_bar_menu', [ $this->lazy_loader, 'remove_lazyload_filters' ] ) );
		$this->assertTrue( has_filter( 'wp_kses_allowed_html', 'function( array $allowed_tags )' ) );
		$this->assertFalse( has_action( 'wp_footer', Lazy_Load_Script::class . '->print_script()' ) );
		$this->assertFalse( has_action( 'wp_head', Lazy_Load_Script::class . '->print_style()' ) );
		$this->assertFalse( has_action( 'wp_enqueue_scripts', Lazy_Load_Script::class . '->register_fallback_script()' ) );
	}

	public function test_lazyload_filters() {
		$this->lazy_loader->add_lazyload_filters();

		$this->assertTrue( has_filter( 'the_content', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertTrue( has_filter( 'post_thumbnail_html', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertTrue( has_filter( 'get_avatar', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertTrue( has_filter( 'widget_text', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertTrue( has_filter( 'get_image_tag', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertTrue( has_filter( 'wp_get_attachment_image_attributes', [ $this->lazy_loader, 'filter_lazyload_attributes' ] ) );

		$this->lazy_loader->remove_lazyload_filters();

		$this->assertFalse( has_filter( 'the_content', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertFalse( has_filter( 'post_thumbnail_html', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertFalse( has_filter( 'get_avatar', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertFalse( has_filter( 'widget_text', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertFalse( has_filter( 'get_image_tag', [ $this->lazy_loader, 'filter_add_lazyload_placeholders' ] ) );
		$this->assertFalse( has_filter( 'wp_get_attachment_image_attributes', [ $this->lazy_loader, 'filter_lazyload_attributes' ] ) );
	}

	/**
	 * @dataProvider data_filter_add_lazyload_placeholders
	 */
	public function test_filter_add_lazyload_placeholders( $input, $expected ) {
		$this->context->expects( $this->any() )
			->method( 'url' )
			->will( $this->returnValue( Lazy_Loader::PLACEHOLDER_PATH ) );

		Functions\when( 'is_feed' )->justReturn( false );
		Functions\when( 'is_preview' )->justReturn( false );
		Functions\when( 'wp_allowed_protocols' )->justReturn( [] );
		Functions\when( 'wp_kses_hair' )->alias(
			function( $string ) {
				$parts = array_filter( explode( '" ', $string ) );
				return array_reduce(
					$parts,
					function( array $carry, string $part ) {
						list( $name, $value ) = explode( '=', trim( $part ) );
						$value = trim( $value, '"\'' );

						$carry[ $name ] = [ 'value' => $value ];
						return $carry;
					},
					[]
				);
			}
		);

		$output = $this->lazy_loader->filter_add_lazyload_placeholders( $input );
		$this->assertSame( $expected, $output );
	}

	public function data_filter_add_lazyload_placeholders() {
		return [
			[
				'<img src="my-image.jpg">',
				'<img src="' . Lazy_Loader::PLACEHOLDER_PATH . '" class="lazy" loading="lazy" data-src="my-image.jpg"><noscript><img loading="lazy" src="my-image.jpg"></noscript>',
			],
			[
				'<img src="my-image.jpg" alt="An alt attribute">',
				'<img src="' . Lazy_Loader::PLACEHOLDER_PATH . '" alt="An alt attribute" class="lazy" loading="lazy" data-src="my-image.jpg"><noscript><img loading="lazy" src="my-image.jpg" alt="An alt attribute"></noscript>',
			],
			[
				'<img src="my-image.jpg" class="some-class">',
				'<img src="' . Lazy_Loader::PLACEHOLDER_PATH . '" class="some-class lazy" loading="lazy" data-src="my-image.jpg"><noscript><img loading="lazy" src="my-image.jpg" class="some-class"></noscript>',
			],
			[
				'<img src="my-image.jpg" srcset="a-srcset" sizes="some-sizes"/>',
				'<img src="' . Lazy_Loader::PLACEHOLDER_PATH . '" class="lazy" loading="lazy" data-src="my-image.jpg" data-srcset="a-srcset" data-sizes="some-sizes"/><noscript><img loading="lazy" src="my-image.jpg" srcset="a-srcset" sizes="some-sizes"/></noscript>',
			],
			[
				'<img src="my-image.jpg" class="skip-lazy">',
				'<img src="my-image.jpg" class="skip-lazy">',
			],
			[
				'<img>',
				'<img>',
			],
			[
				'<iframe src="https://example.com"></iframe>',
				'<iframe src="https://example.com" class="lazy" loading="lazy"></iframe>',
			],
			[
				'<iframe src="https://example.com" class="some-class"></iframe>',
				'<iframe src="https://example.com" class="some-class lazy" loading="lazy"></iframe>',
			],
			[
				'<iframe src="https://example.com" class="skip-lazy"></iframe>',
				'<iframe src="https://example.com" class="skip-lazy"></iframe>',
			],
		];
	}

	/**
	 * @dataProvider data_filter_add_lazyload_placeholders_with_fallback_disabled
	 */
	public function test_filter_add_lazyload_placeholders_with_fallback_disabled( $input, $expected ) {
		Functions\when( 'apply_filters' )->alias(
			function( string $filter, $value ) {
				if ( 'native_lazyload_fallback_script_enabled' === $filter ) {
					return false;
				}
				return $value;
			}
		);
		Functions\when( 'is_feed' )->justReturn( false );
		Functions\when( 'is_preview' )->justReturn( false );
		Functions\when( 'wp_allowed_protocols' )->justReturn( [] );
		Functions\when( 'wp_kses_hair' )->alias(
			function( $string ) {
				$parts = array_filter( explode( '" ', $string ) );
				return array_reduce(
					$parts,
					function( array $carry, string $part ) {
						list( $name, $value ) = explode( '=', trim( $part ) );
						$value = trim( $value, '"\'' );

						$carry[ $name ] = [ 'value' => $value ];
						return $carry;
					},
					[]
				);
			}
		);

		$output = $this->lazy_loader->filter_add_lazyload_placeholders( $input );
		$this->assertSame( $expected, $output );
	}

	public function data_filter_add_lazyload_placeholders_with_fallback_disabled() {
		return [
			[
				'<img src="my-image.jpg">',
				'<img src="my-image.jpg" class="lazy" loading="lazy">',
			],
			[
				'<img src="my-image.jpg" alt="An alt attribute">',
				'<img src="my-image.jpg" alt="An alt attribute" class="lazy" loading="lazy">',
			],
			[
				'<img src="my-image.jpg" class="some-class">',
				'<img src="my-image.jpg" class="some-class lazy" loading="lazy">',
			],
			[
				'<img src="my-image.jpg" srcset="a-srcset" sizes="some-sizes"/>',
				'<img src="my-image.jpg" srcset="a-srcset" sizes="some-sizes" class="lazy" loading="lazy"/>',
			],
			[
				'<img src="my-image.jpg" class="skip-lazy">',
				'<img src="my-image.jpg" class="skip-lazy">',
			],
			[
				'<img>',
				'<img>',
			],
			[
				'<iframe src="https://example.com"></iframe>',
				'<iframe src="https://example.com" class="lazy" loading="lazy"></iframe>',
			],
			[
				'<iframe src="https://example.com" class="some-class"></iframe>',
				'<iframe src="https://example.com" class="some-class lazy" loading="lazy"></iframe>',
			],
			[
				'<iframe src="https://example.com" class="skip-lazy"></iframe>',
				'<iframe src="https://example.com" class="skip-lazy"></iframe>',
			],
		];
	}
}
