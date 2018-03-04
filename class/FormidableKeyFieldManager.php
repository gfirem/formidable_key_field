<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableKeyFieldManager {
	private static $plugin_slug = 'formidable_key_field';
	private static $plugin_short = 'FormidableKeyField';
	private static $version = '2.1.0';
	
	public function __construct() {
		require_once FKF_CLASS_PATH . 'FormidableKeyLogs.php';
		new FormidableKeyLogs();
		
		try {
			if ( class_exists( 'FrmAppHelper' ) && method_exists( 'FrmAppHelper', 'pro_is_installed' ) && FrmAppHelper::pro_is_installed() ) {
				$fs = $this->kg_fs();
				require_once FKF_CLASS_PATH . 'FormidableKeyFieldAdmin.php';
				new FormidableKeyFieldAdmin();
				if ( ! empty( $fs ) && $fs->is_paying() ) {
					//Load dependencies
					require_once FKF_CLASS_PATH . 'FormidableGeneratorField.php';
					new FormidableGeneratorField();
					
					require_once FKF_CLASS_PATH . 'FormidableValidatorField.php';
					new FormidableValidatorField();
					
					require_once FKF_CLASS_PATH . 'FormidableStatusField.php';
					new FormidableStatusField();
				}
			} else {
				add_action( 'admin_notices', array( $this, 'required_formidable_pro' ) );
			}
		} catch ( Exception $ex ) {
			FormidableKeyLogs::log( array(
				'action'         => get_class( $this ),
				'object_type'    => FormidableKeyFieldManager::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $ex->getMessage(),
			) );
		}
	}
	
	public function required_formidable_pro() {
		require FKF_VIEW_PATH . 'require_formidable.php';
	}
	
	// Create a helper function for easy SDK access.
	public function kg_fs() {
		global $kg_fs;
		
		if ( ! isset( $kg_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';
			try {
				$kg_fs = fs_dynamic_init( array(
					'id'               => '759',
					'slug'             => 'formidable_key_field',
					'type'             => 'plugin',
					'public_key'       => 'pk_cde28eaaa6a6193ba4f9aafcf1e6c',
					'is_premium'       => true,
					'is_premium_only'  => true,
					'has_addons'       => false,
					'has_paid_plans'   => true,
					'is_org_compliant' => false,
					'menu'             => array(
						'slug'           => 'formidable_key_field',
						'override_exact' => true,
						'first-path'     => 'admin.php?page=formidable_key_field',
						'support'        => false
					),
					'trial'            => array(
						'days'               => 14,
						'is_require_payment' => true,
					),
					// Set the SDK to work in a sandbox mode (for development & testing).
					// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
					'secret_key'       => 'sk_LDKUop!rDp[I7d$-xC$I}Yl-&>Z%_',
				) );
			} catch ( Exception $ex ) {
				FormidableKeyLogs::log( array(
					'action'         => get_class( $this ),
					'object_type'    => FormidableKeyFieldManager::getSlug(),
					'object_subtype' => 'Error loading Freemius',
					'object_name'    => $ex->getMessage(),
				) );
				$kg_fs = null;
			}
		}
		
		return $kg_fs;
	}
	
	static function getShort() {
		return self::$plugin_short;
	}
	
	static function getSlug() {
		return self::$plugin_slug;
	}
	
	static function getVersion() {
		return self::$version;
	}
	
	/**
	 * Translate string to main Domain
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function t( $str ) {
		return __( $str, 'formidable_key_field-locale' );
	}
	
	/**
	 * Get WP option for date format
	 *
	 * @return mixed
	 */
	public static function getDateFormat() {
		return get_option( 'date_format' );
	}
}
