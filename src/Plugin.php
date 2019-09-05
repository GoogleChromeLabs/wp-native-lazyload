<?php
/**
 * Class Google\Native_Lazyload\Plugin
 *
 * @package   Google\Native_Lazyload
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://wordpress.org/plugins/native-lazyload/
 */

namespace Google\Native_Lazyload;

/**
 * Main class for the plugin.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Plugin context instance to pass around.
	 *
	 * @since 1.0.0
	 * @var Context
	 */
	protected $context;

	/**
	 * Main instance of the plugin.
	 *
	 * @since 1.0.0
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Sets the plugin main file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 */
	public function __construct( string $main_file ) {
		$this->context = new Context( $main_file );
	}

	/**
	 * Gets the plugin context instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Context The context instance.
	 */
	public function context() : Context {
		return $this->context;
	}

	/**
	 * Registers the plugin with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		add_action(
			'wp',
			function() {
				( new Lazy_Loader( $this->context ) )->register();
			}
		);
	}

	/**
	 * Retrieves the main instance of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin Plugin main instance.
	 */
	public static function instance() : Plugin {
		return static::$instance;
	}

	/**
	 * Loads the plugin main instance and initializes it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @return bool True if the plugin main instance could be loaded, false otherwise.
	 */
	public static function load( string $main_file ) : bool {
		if ( null !== static::$instance ) {
			return false;
		}

		static::$instance = new static( $main_file );
		static::$instance->register();

		return true;
	}
}
