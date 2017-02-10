<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableStatusField {
	
	function __construct() {
		if ( class_exists( "FrmProAppController" ) ) {
			add_action( 'frm_pro_available_fields', array( $this, 'add_formidable_key_field' ) );
			add_action( 'frm_display_added_fields', array( $this, 'show_formidable_key_field_admin_field' ) );
			add_action( 'frm_form_fields', array( $this, 'show_formidable_key_field_front_field' ), 10, 2 );
			add_action( 'frm_display_value', array( $this, 'display_formidable_key_field_admin_field' ), 10, 3 );
			add_filter( 'frm_display_field_options', array( $this, 'add_formidable_key_field_display_options' ) );
			add_filter( 'frm_pre_create_entry', array( $this, 'after_formidable_key_field_create_entry' ) );
			add_filter( 'frm_validate_entry', array( $this, 'validate_field' ), 10, 3 );
			add_filter( 'frmpro_fields_replace_shortcodes', array( $this, 'replace_shortcode' ), 10, 4 );
		}
	}

	
	/**
	 * Add new field to formidable list of fields
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_formidable_key_field( $fields ) {
		$fields['key_used'] = FormidableKeyFieldManager::t( "Key Status" );
		
		return $fields;
	}

	/**
	 * Show the field placeholder in the admin area
	 *
	 * @param $field
	 */
	public function show_formidable_key_field_admin_field( $field ) {
		if ( $field['type'] != 'key_used' ) {
			return;
		}
		?>
		<div class="frm_html_field_placeholder">
			<div class="frm_html_field"><?= FormidableKeyFieldManager::t( "Show the status of key." ) ?> </div>
		</div>
	<?php
	}

	/**
	 * Validate entry. Set the existing value or default, before save.
	 *
	 * @param $errors
	 * @param $values
	 * @param $exclude
	 *
	 * @return mixed
	 */
	public function validate_field( $errors, $values, $exclude ) {
		global $frm_field;
		$field_statuses = $frm_field->get_all_types_in_form( $values['form_id'], "key_used" );

		foreach ( $field_statuses as $key => $item ) {
			$value = FrmEntryMeta::get_entry_meta_by_field( $values['id'], $key );
			if ( empty( $value ) ) {
				$value = '0';
			}
			$_POST["item_meta"][ $key ] = $value;
		}

		return $errors;
	}

	/**
	 * Add the HTML for the field on the front end
	 *
	 * @param $field
	 * @param $field_name
	 *
	 * @return mixed
	 */
	public function show_formidable_key_field_front_field( $field, $field_name ) {
		if ( $field['type'] != 'key_used' ) {
			return;
		}
		if ( empty( $field['value'] ) ) {
			$field['value'] = '0';
		}
		$field['value'] = stripslashes_deep( $field['value'] );
		if ( $field['value'] == '1' ) {
			?>
			<span class="dashicons dashicons-yes" style="color: #008000;"></span><?php
		} else {
			?><span class="dashicons dashicons-no-alt" style="color: #ff0000;"></span><?php
		}
	}


	/**
	 * Replace shortcode of the field. Set custom tags inside the field shortcode
	 *
	 * @param $value
	 * @param $tag
	 * @param $attr
	 * @param $field
	 *
	 * @return string
	 */
	public function replace_shortcode( $value, $tag, $attr, $field ) {
		if ( $field->type != 'key_used' ) {
			return $value;
		}

		$internal_attr = shortcode_atts( array(
			'show' => '',
		), $attr );

		if ( empty( $value ) ) {
			$value = '0';
		}

		if ( $internal_attr['show'] == 'icon' ) {
			$result = '<span class="dashicons dashicons-no-alt" style="color: #ff0000;"></span>';
			if ( $value == '1' ) {
				$result = '<span class="dashicons dashicons-yes"  style="color: #008000;"></span>';
			}

			return $result;
		}

		return $value;
	}
	
	/**
	 * Add the HTML to display the field in the admin area
	 *
	 * @param $value
	 * @param $field
	 * @param $atts
	 *
	 * @return string
	 */
	public function display_formidable_key_field_admin_field( $value, $field, $atts ) {
		if ( $field->type != 'key_used' ) {
			return $value;
		}

		if ( empty( $value ) ) {
			$value = '0';
		}

		if ( $value == '1' ) {
			$value = '<span class="dashicons dashicons-yes" style="color: #008000;"></span>';
		} else {
			$value = '<span class="dashicons dashicons-no-alt" style="color: #ff0000;"></span>';
		}
		
		return $value;
	}
	
	/**
	 * Set display option for the field
	 *
	 * @param $display
	 *
	 * @return mixed
	 */
	public function add_formidable_key_field_display_options( $display ) {
		if ( $display['type'] == 'key_used' ) {
			$display['unique']         = false;
			$display['required']       = false;
			$display['description']    = true;
			$display['options']        = true;
			$display['label_position'] = true;
			$display['css']            = true;
		}
		
		return $display;
	}

	/**
	 *
	 * @param $values
	 *
	 * @return
	 */
	public function after_formidable_key_field_create_entry( $values ) {
		foreach ( $values["item_meta"] as $key => $value ) {
			global $frm_field;
			if ( $frm_field->get_type( $key ) == "key_used" ) {
				if ( empty( $_POST["item_meta"][ $key ] ) ) {
					$values["item_meta"][ $key ] = '0';
					$_POST["item_meta"][ $key ]  = $values["item_meta"][ $key ];
				}
			}
		}

		return $values;
	}
}