<?php
/*
 * Plugin Name:       Formidable key field
 * Plugin URI:        http://wwww.gfirem.com
 * Description:       Add two field to formidable, with the golad to create string in one form and validate in other form
 * Version:           0.09
 * Author:            Guillermo Figueroa Mesa
 * Author URI:        http://wwww.gfirem.com
 * Text Domain:       formidable_key_field-locale
 * License:           Apache License 2.0
 * License URI:       http://www.apache.org/licenses/
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FormidableKeyField' ) ) :

	require_once 'plugin-update-checker/plugin-update-checker.php';

	$myUpdateChecker = PucFactory::buildUpdateChecker( 'http://gfirem.com/update-services/?action=get_metadata&slug=formidable_key_field', __FILE__ );
	$myUpdateChecker->addQueryArgFilter( 'appendFormidableKeyFieldQueryArgsCredentials' );

	/**
	 * Append the order key to the update server URL
	 *
	 * @param $queryArgs
	 *
	 * @return
	 */
	function appendFormidableKeyFieldQueryArgsCredentials( $queryArgs ) {
		$queryArgs['order_key'] = get_option( FormidablePatternFieldManager::getShort() . 'licence_key', '' );

		return $queryArgs;
	}

	class FormidableKeyField {

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 */
		private function __construct() {

			// Load plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			require_once 'class/FormidableKeyFieldManager.php';
			$manager = new FormidableKeyFieldManager();

		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'formidable_key_field-locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
	}

	add_action( 'plugins_loaded', array( 'FormidableKeyField', 'get_instance' ) );

endif;