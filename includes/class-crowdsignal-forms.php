<?php

/**
 * File containing the class \Crowdsignal_Forms\Crowdsignal_Forms.
 *
 * @package Crowdsignal_Forms
 * @since   1.0.0
 */

namespace Crowdsignal_Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Crowdsignal Forms class.
 *
 * @class Crowdsignal_Forms
 */
final class Crowdsignal_Forms {

    /**
	 * Instance of class.
	 *
	 * @var Crowdsignal_Forms
	 */
	private static $instance;

    /**
     * @var string
     */
    private $plugin_dir;

    /**
     * @var string
     */
    private $plugin_url;

    /**
	 * Initialize the singleton instance.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->plugin_dir          = dirname( __DIR__ );
		$this->plugin_url          = untrailingslashit( plugins_url( '', CROWDSIGNAL_FORMS_PLUGIN_BASENAME ) );

		register_deactivation_hook( CROWDSIGNAL_FORMS_PLUGIN_FILE, [ $this, 'deactivation' ] );
	}

	/**
	 * Fetches an instance of the class.
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
     * Clean up on deactivation.
     *
     * @since 1.0.0
     */
	public function deactivation() {
	}

    /**
     * Includes all php files needed and sets all the objects this class will use for initializing.
     *
     * @since 1.0.0
     *
     * @return $this
     */
	public function bootstrap() {
	    return $this;
    }

    /**
     * Setup all filters and hooks. For frontend and optionally, admin.
     *
     * @param bool $init_all Pass in `true` to load and initialize both frontend and admin functionality. `false` by default.
     *
     * @return $this
     */
    public function setup_hooks( $init_all = false ) {
	    return $this;
    }

	/**
	 * Initializes the class and adds all filters and actions.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $init_all Pass in `true` to load and initialize both frontend and admin functionality. `false` by default.
     *
     * @return self
	 */
	public static function init( $init_all = false ) {
	    return self::instance()->bootstrap()->setup_hooks( $init_all );
	}
}