<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableKeyFieldAdmin {

	function __construct() {
		add_filter( 'frm_add_settings_section', array( $this, 'add_formidable_key_field_SettingPage' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_formidable_key_field_setting_link' ), 9, 2 );
	}

	/**
	 * Add setting page to global formidable settings
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_formidable_key_field_SettingPage( $sections ) {
		$sections['key_validator'] = array(
			'name'     => FormidableKeyFieldManager::t( "Key Validator" ),
			'class'    => 'FormidableKeyFieldSettings',
			'function' => 'route',
		);

		return $sections;
	}

	/**
	 * Add a "Settings" link to the plugin row in the "Plugins" page.
	 *
	 * @param $links
	 * @param string $pluginFile
	 *
	 * @return array
	 * @internal param array $pluginMeta Array of meta links.
	 */
	public function add_formidable_key_field_setting_link( $links, $pluginFile ) {
		if ( $pluginFile == 'formidable_key_field/formidable_key_field.php' ) {
			$link = sprintf( '<a href="%s">%s</a>', esc_attr( admin_url( 'admin.php?page=formidable-settings&t=pattern_settings' ) ), FormidableKeyFieldManager::t( "Settings" ) );
			array_unshift( $links, $link );
		}

		return $links;
	}
}