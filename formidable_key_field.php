<?php
/*
 * Plugin Name:       Licence Key Generator
 * Plugin URI:        http://wwww.gfirem.com
 * Description:       Add two field to formidable, with the golad to create string in one form and validate in other form
 * Version:           1.1.0
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

if ( ! class_exists( 'FormidableKeyField' ) ) {
	
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
			define( 'FKF_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'FKF_ABSPATH', trailingslashit( str_replace( "\\", "/", plugin_dir_path( __FILE__ ) ) ) );
			define( 'FKF_URLPATH', trailingslashit( str_replace( "\\", "/", plugin_dir_url( __FILE__ ) ) ) );
			define( 'FKF_JS_PATH', FKF_URLPATH . 'assets/js/' );
			define( 'FKF_CSS_PATH', FKF_URLPATH . 'assets/css/' );
			define( 'FKF_IMAGE_PATH', FKF_URLPATH . 'assets/images/' );
			define( 'FKF_CLASS_PATH', FKF_ABSPATH . 'class/' );
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
}