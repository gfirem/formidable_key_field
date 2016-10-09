<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableKeyFieldManager {

	protected $plugin_slug;
	private static $plugin_short = 'FormidableKeyField';

	protected static $version;

	public function __construct() {

		$this->plugin_slug = 'FormidableKeyField';
		self::$version     = '1.02';

		//Load dependencies
		require_once 'FormidableGeneratorField.php';
		$generator = new FormidableGeneratorField();

		require_once 'FormidableValidatorField.php';
		$validation = new FormidableValidatorField();

		require_once 'FormidableStatusField.php';
		$status = new FormidableStatusField();

		require_once 'FormidableKeyFieldSettings.php';
		$settings = new FormidableKeyFieldSettings();

		require_once 'FormidableKeyFieldAdmin.php';
		$admin = new FormidableKeyFieldAdmin();

	}

	static function getShort() {
		return self::$plugin_short;
	}

	static function getVersion() {
		return self::$version;
	}

	/**
	 * Translate string to main Domain
	 *
	 * @param $str
	 *
	 * @return string|void
	 */
	public static function t( $str ) {
		return __( $str, 'formidable_key_field-locale' );
	}

	/**
	 * Get WP option for date format
	 *
	 * @return mixed|void
	 */
	public static function getDateFormat() {
		return get_option( 'date_format' );
	}
}