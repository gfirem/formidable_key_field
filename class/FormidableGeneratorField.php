<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableGeneratorField {
	
	function __construct() {
		if ( class_exists( "FrmProAppController" ) ) {
			add_action( 'frm_pro_available_fields', array( $this, 'add_formidable_key_field' ) );
			add_action( 'frm_before_field_created', array( $this, 'set_formidable_key_field_options' ) );
			add_action( 'frm_display_added_fields', array( $this, 'show_formidable_key_field_admin_field' ) );
			add_action( 'frm_field_options_form', array( $this, 'field_formidable_key_field_option_form' ), 10, 3 );
			add_action( 'frm_update_field_options', array( $this, 'update_formidable_key_field_options' ), 10, 3 );
			add_action( 'frm_form_fields', array( $this, 'show_formidable_key_field_front_field' ), 10, 2 );
			add_action( 'frm_display_value', array( $this, 'display_formidable_key_field_admin_field' ), 10, 3 );
			add_filter( 'frm_display_field_options', array( $this, 'add_formidable_key_field_display_options' ) );
			add_filter( 'frm_pre_create_entry', array( $this, 'after_formidable_key_field_create_entry' ) );
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
		$fields['key_generator'] = FormidableKeyFieldManager::t( "Key generator" );
		
		return $fields;
	}
	
	/**
	 * Set the default options for the field
	 *
	 * @param $fieldData
	 *
	 * @return mixed
	 */
	public function set_formidable_key_field_options( $fieldData ) {
		if ( $fieldData['type'] == 'key_generator' ) {
			$fieldData['name'] = FormidableKeyFieldManager::t( "Key generator" );
			
			$defaults = array(
				'key_generator_length'         => '',
				'key_generator_allow_specials' => '',
			);
			
			foreach ( $defaults as $k => $v ) {
				$fieldData['field_options'][ $k ] = $v;
			}
		}
		
		return $fieldData;
	}
	
	/**
	 * Show the field placeholder in the admin area
	 *
	 * @param $field
	 */
	public function show_formidable_key_field_admin_field( $field ) {
		if ( $field['type'] != 'key_generator' ) {
			return;
		}
		?>
		<div class="frm_html_field_placeholder">
			<div class="frm_html_field"><?= FormidableKeyFieldManager::t( "Generate random key." ) ?> </div>
		</div>
	<?php
	}
	
	
	/**
	 * Display the additional options for the new field
	 *
	 * @param $field
	 * @param $display
	 * @param $values
	 */
	public function field_formidable_key_field_option_form( $field, $display, $values ) {
		if ( $field['type'] != 'key_generator' ) {
			return;
		}
		
		$defaults = array(
			'key_generator_length'         => '',
			'key_generator_allow_specials' => '',
		);
		
		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $field[ $k ] ) ) {
				$field[ $k ] = $v;
			}
		}
		
		$allow_specials = "";
		if ( $field['key_generator_allow_specials'] == "1" ) {
			$allow_specials = "checked='checked'";
		}
		?>
		<tr>
			<td>
				<label for="field_options[key_generator_length_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Length" ) ?></label>
				<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "Set the length for generate content, by default will be 20." ) ?>"></span>
			</td>
			<td>
				<input type="number" style="width: 10%;" pattern="\d*" max="50" min="5" maxlength="50" size="100" name="field_options[key_generator_length_<?php echo $field['id'] ?>]" id="field_options[key_generator_length_<?php echo $field['id'] ?>]" value="<?php echo $field['key_generator_length'] ?>"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="field_options[key_generator_allow_specials_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Special characters" ) ?></label>
				<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "Check if the generated content will be special characters. " ) ?>"></span>
			</td>
			<td>
				<input type="checkbox" <?= $allow_specials ?> name="field_options[key_generator_allow_specials_<?php echo $field['id'] ?>]" id="field_options[key_generator_allow_specials_<?php echo $field['id'] ?>]" value="1"/>
			</td>
		</tr>
	<?php
	}
	
	/**
	 * Update the field options from the admin area
	 *
	 * @param $field_options
	 * @param $field
	 * @param $values
	 *
	 * @return mixed
	 */
	public function update_formidable_key_field_options( $field_options, $field, $values ) {
		if ( $field->type != 'key_generator' ) {
			return $field_options;
		}
		
		$defaults = array(
			'key_generator_length'         => '',
			'key_generator_allow_specials' => '',
		);
		
		foreach ( $defaults as $opt => $default ) {
			$field_options[ $opt ] = isset( $values['field_options'][ $opt . '_' . $field->id ] ) ? $values['field_options'][ $opt . '_' . $field->id ] : $default;
		}
		
		return $field_options;
	}
	
	/**
	 * Add the HTML for the field on the front end
	 *
	 * @param $field
	 * @param $field_name
	 */
	public function show_formidable_key_field_front_field( $field, $field_name ) {
		if ( $field['type'] != 'key_generator' ) {
			return;
		}
		$field['value'] = stripslashes_deep( $field['value'] );
		$maxlength      = "";
		if ( ! empty( $field['key_generator_length'] ) ) {
			$maxlength = 'maxlength="' . $field['key_generator_length'] . '"';
		}
		?>
		<input type="text" <?php echo "$maxlength"; ?> id='field_<?= $field['field_key'] ?>' name='item_meta[<?= $field['id'] ?>]' value="<?php echo esc_attr( $field['value'] ) ?>"/>
	<?php
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
		if ( $field->type != 'key_generator' || empty( $value ) ) {
			return $value;
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
		if ( $display['type'] == 'key_generator' ) {
			$display['unique']         = true;
			$display['required']       = false;
			$display['description']    = true;
			$display['options']        = true;
			$display['label_position'] = true;
			$display['css']            = true;
		}
		
		return $display;
	}

	/**
	 * Set random value after create an entry, and avoid change when update
	 *
	 * @param $values
	 *
	 * @return
	 */
	public function after_formidable_key_field_create_entry( $values ) {
		foreach ( $values["item_meta"] as $key => $value ) {
			global $frm_field;
			if ( $frm_field->get_type( $key ) == "key_generator" ) {
				if ( empty( $_POST["item_meta"][ $key ] ) ) {
					$values["item_meta"][ $key ] = self::generate_key($key);
					$_POST["item_meta"][ $key ]  = $values["item_meta"][ $key ];
				}
			}
		}

		return $values;
	}

	/**
	 * Generate random key using configuration set.
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function generate_key($id) {
		global $frm_field;
		$field   = $frm_field->getOne( $id );
		$length  = 20;
		$special = false;
		if ( ! empty( $field ) && ! empty( $field->field_options ) ) {
			if ( ! empty( $field->field_options['key_generator_length'] ) && is_numeric( $field->field_options['key_generator_length'] ) ) {
				$length = $field->field_options['key_generator_length'];
			}
			if ( ! empty( $field->field_options['key_generator_allow_specials'] ) ) {
				$special = (bool) $field->field_options['key_generator_allow_specials'];
			}

		}
		return wp_generate_password( $length, $special, false );
	}
}