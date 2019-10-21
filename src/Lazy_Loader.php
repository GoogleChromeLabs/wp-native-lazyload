<?php
/**
 * Class Google\Native_Lazyload\Lazy_Loader
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload/
 */

namespace Google\Native_Lazyload;

/**
 * Class for adding the necessary attributes to enable lazy-loading.
 *
 * @since 1.0.0
 */
class Lazy_Loader {

	// Pipe-separated list of tags to prepare for native lazy-loading.
	const LAZYLOAD_TAGS = 'img|iframe';

	// Pipe-separated list of tags to prepare for JavaScript fallback lazy-loading.
	const LAZYLOAD_FALLBACK_TAGS = 'img';

	// Class to add to all elements to lazy-load.
	const LAZYLOAD_FALLBACK_CLASS = 'native-lazyload-js-fallback';

	// Class to interpret as blacklist indicator for elements to not lazy-load.
	const SKIP_LAZYLOAD_CLASS = 'skip-lazy';

	// Relative path to the placeholder image file.
	const PLACEHOLDER_PATH = 'assets/images/placeholder.svg';

	/**
	 * Plugin context instance to pass around.
	 *
	 * @since 1.0.0
	 * @var Context
	 */
	protected $context;

	/**
	 * Whether native lazy-loading should fall back to a JavaScript solution.
	 *
	 * Utility for {@see Lazy_Loader::fallback_script_enabled()}.
	 *
	 * @since 1.0.0
	 * @var bool|null
	 */
	protected $fallback_enabled = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Context $context The plugin context instance.
	 */
	public function __construct( Context $context ) {
		$this->context = $context;
	}

	/**
	 * Registers the plugin with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Don't do anything if an AJAX request because lack of context predictability.
		if ( $this->context->is_ajax() ) {
			return;
		}

		// Don't do anything for AMP because it would be unnecessary.
		if ( $this->context->is_amp() ) {
			return;
		}

		// Prepare applicable elements to be lazy-loaded.
		add_action( 'wp_head', [ $this, 'add_lazyload_filters' ], PHP_INT_MAX );

		// Do not lazy-load anything in the admin bar.
		add_action( 'admin_bar_menu', [ $this, 'remove_lazyload_filters' ], 0 );

		// Ensure our necessary attributes are allowed.
		add_filter(
			'wp_kses_allowed_html',
			function( array $allowed_tags ) : array {
				$lazyload_tags = explode( '|', static::LAZYLOAD_TAGS );

				foreach ( $lazyload_tags as $tag ) {
					if ( ! isset( $allowed_tags[ $tag ] ) ) {
						continue;
					}

					$allowed_tags[ $tag ] = array_merge(
						$allowed_tags[ $tag ],
						[
							'loading'     => [],
							'data-src'    => [],
							'data-srcset' => [],
							'data-sizes'  => [],
							'class'       => [],
						]
					);
				}

				return $allowed_tags;
			}
		);

		if ( $this->fallback_script_enabled() ) {
			$script = new Lazy_Load_Script( $this->context );
			add_action( 'wp_footer', [ $script, 'print_script' ] );
			add_action( 'wp_head', [ $script, 'print_style' ] );
			add_action( 'wp_enqueue_scripts', [ $script, 'register_fallback_script' ] );
		}
	}

	/**
	 * Adds filters so that applicable elements are prepared for lazy-loading.
	 *
	 * @since 1.0.0
	 */
	public function add_lazyload_filters() {
		add_filter( 'the_content', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'post_thumbnail_html', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'get_avatar', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'widget_text', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'get_image_tag', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_lazyload_attributes' ], PHP_INT_MAX );
	}

	/**
	 * Removes filters so that applicable elements are no longer prepared for lazy-loading.
	 *
	 * @since 1.0.0
	 */
	public function remove_lazyload_filters() {
		remove_filter( 'the_content', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		remove_filter( 'post_thumbnail_html', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		remove_filter( 'get_avatar', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		remove_filter( 'widget_text', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		remove_filter( 'get_image_tag', [ $this, 'filter_add_lazyload_placeholders' ], PHP_INT_MAX );
		remove_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_lazyload_attributes' ], PHP_INT_MAX );
	}

	/**
	 * Adjusts necessary attributes of applicable elements in content to prepare for lazy-loading.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The content.
	 * @return string Filtered content prepared for lazy-loading.
	 */
	public function filter_add_lazyload_placeholders( string $content ) : string {
		// Don't lazyload for feeds, previews.
		if ( is_feed() || is_preview() ) {
			return $content;
		}

		// Bail if the content has already been prepared previously.
		if ( false !== strpos( $content, 'data-src' ) ) {
			return $content;
		}

		// Find all applicable elements via regex and add lazy-load attributes.
		$content = preg_replace_callback(
			'#<(' . static::LAZYLOAD_TAGS . ')([^>]+?)(>(.*?)</\\1>|[\/]?>)#si',
			function( array $matches ) : string {
				// If there are no attributes, just return the original match.
				if ( empty( $matches[2] ) ) {
					return $matches[0];
				}

				$old_attributes = $this->parse_attributes_string( $matches[2] );
				$new_attributes = $this->filter_lazyload_attributes( $old_attributes, strtolower( $matches[1] ) );

				// If we didn't add lazy-load attributes, just return the original match.
				if ( empty( $new_attributes['loading'] ) ) {
					return $matches[0];
				}

				$new_attributes_str = $this->build_attributes_string( $new_attributes );

				// Replace old attributes with new attributes.
				$output = sprintf( '<%1$s %2$s%3$s', $matches[1], $new_attributes_str, $matches[3] );

				// If JavaScript fallback attributes are present, add a <noscript> fallback.
				if ( isset( $new_attributes['data-src'] ) ) {
					$noscript_tag = str_replace( '<' . $matches[1] . ' ', '<' . $matches[1] . ' loading="lazy" ', $matches[0] );

					$output .= sprintf( '<noscript>%s</noscript>', $noscript_tag );
				}

				return $output;
			},
			$content
		);

		return $content;
	}

	/**
	 * Prepares attributes for lazy-loading.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes Attributes of an element to lazy-load.
	 * @param string $tag        Optional. Tag that the attributes are for. Default 'img'.
	 * @return array Filtered attributes prepared for lazy-loading.
	 */
	public function filter_lazyload_attributes( array $attributes, string $tag = 'img' ) : array {
		if ( empty( $attributes['src'] ) ) {
			return $attributes;
		}

		if ( ! empty( $attributes['class'] ) && $this->has_blacklisted_class( $attributes['class'] ) ) {
			return $attributes;
		}

		// Native browser lazy-loading.
		$attributes['loading'] = 'lazy';

		if ( $this->fallback_script_enabled() && false !== strpos( static::LAZYLOAD_FALLBACK_TAGS, $tag ) ) {
			// JavaScript fallback lazy-loading.
			$attributes = $this->filter_lazyload_attributes_for_js_fallback( $attributes, $tag );
		}

		return $attributes;
	}

	/**
	 * Prepares attributes for lazy-loading via a JavaScript fallback solution.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attributes Attributes of an element to lazy-load.
	 * @param string $tag        Optional. Tag that the attributes are for. Default 'img'.
	 * @return array Filtered attributes prepared for lazy-loading.
	 */
	protected function filter_lazyload_attributes_for_js_fallback( array $attributes, string $tag = 'img' ) : array {
		// Add the JS fallback indicator class to the img element.
		if ( ! empty( $attributes['class'] ) ) {
			$attributes['class'] .= ' ' . static::LAZYLOAD_FALLBACK_CLASS;
		} else {
			$attributes['class'] = static::LAZYLOAD_FALLBACK_CLASS;
		}

		// Set data-src to the original source uri.
		$attributes['data-src'] = $attributes['src'];

		// Process `srcset` attribute.
		if ( ! empty( $attributes['srcset'] ) ) {
			$attributes['data-srcset'] = $attributes['srcset'];
			unset( $attributes['srcset'] );
		}

		// Process `sizes` attribute.
		if ( ! empty( $attributes['sizes'] ) ) {
			$attributes['data-sizes'] = $attributes['sizes'];
			unset( $attributes['sizes'] );
		}

		// Set placeholder image if applicable.
		if ( 'img' === $tag ) {
			$attributes['src'] = $this->context->url( static::PLACEHOLDER_PATH );
		}

		return $attributes;
	}

	/**
	 * Parses a string of attributes into an array of attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string HTML attribute string.
	 * @return array Associative array of attributes.
	 */
	protected function parse_attributes_string( string $string ) : array {
		return array_map(
			function( array $attribute ) : string {
				return $attribute['value'];
			},
			wp_kses_hair( $string, array_merge( wp_allowed_protocols(), [ 'data' ] ) )
		);
	}

	/**
	 * Builds a string of attributes from an array of attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Associative array of attributes.
	 * @return string HTML attribute string.
	 */
	protected function build_attributes_string( array $attributes ) : string {
		return implode(
			' ',
			array_map(
				function( string $name, string $value ) : string {
					if ( '' === $value ) {
						return $name;
					}
					return sprintf( '%s="%s"', $name, esc_attr( $value ) );
				},
				array_keys( $attributes ),
				$attributes
			)
		);
	}

	/**
	 * Checks whether a class attribute string contains a class blacklisted for lazy-loading.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes A string of space-separated classes.
	 * @return bool True if the string contains a blacklisted class.
	 */
	protected function has_blacklisted_class( string $classes ) : bool {
		if ( false !== strpos( $classes, static::SKIP_LAZYLOAD_CLASS ) ) {
			return true;
		}

		// Never lazy-load the Custom Logo.
		if ( false !== strpos( $classes, 'custom-logo' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks whether native lazy-loading should fall back to a JavaScript solution.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if JavaScript fallback should be used, false otherwise.
	 */
	protected function fallback_script_enabled() : bool {
		if ( null === $this->fallback_enabled ) {
			/**
			 * Filters whether native lazy-loading should fall back to a JavaScript solution.
			 *
			 * If enabled, a JavaScript file with lazy-loading logic will be loaded for browsers that
			 * do not support the 'loading' attribute.
			 *
			 * @since 1.0.0
			 *
			 * @param bool $enabled Whether native lazy-loading should fall back to a JavaScript solution.
			 */
			$this->fallback_enabled = (bool) apply_filters( 'native_lazyload_fallback_script_enabled', true );
		}

		return $this->fallback_enabled;
	}
}
