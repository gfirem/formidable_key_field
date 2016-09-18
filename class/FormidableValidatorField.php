<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'Helper.php';

class FormidableValidatorField {
	
	function __construct() {


		if ( class_exists( "FrmProAppController" ) ) {
			add_action( 'frm_pro_available_fields', array( $this, 'add_formidable_key_field' ) );
		} else {
			add_action( 'frm_available_fields', array( $this, 'add_formidable_key_field' ) );
		}
		add_action( 'frm_before_field_created', array( $this, 'set_formidable_key_field_options' ) );
		add_action( 'frm_display_added_fields', array( $this, 'show_formidable_key_field_admin_field' ) );
		add_action( 'frm_field_options_form', array( $this, 'field_formidable_key_field_option_form' ), 10, 3 );
		add_action( 'frm_update_field_options', array( $this, 'update_formidable_key_field_options' ), 10, 3 );
		add_action( 'frm_form_fields', array( $this, 'show_formidable_key_field_front_field' ), 10, 2 );
		add_action( 'frm_display_value', array( $this, 'display_formidable_key_field_admin_field' ), 10, 3 );
		add_filter( 'frm_display_field_options', array( $this, 'add_formidable_key_field_display_options' ) );
		add_filter( "frm_validate_field_entry", array( $this, "validate_frm_entry" ), 10, 3 );
	}

	
	/**
	 * Add new field to formidable list of fields
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_formidable_key_field( $fields ) {
		$fields['key_validator'] = FormidableKeyFieldManager::t( "Key validator" );
		
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
		if ( $fieldData['type'] == 'key_validator' ) {
			$fieldData['name'] = FormidableKeyFieldManager::t( "Key validator" );
			
			$defaults = array(
				'key_validator_form_target' => '',
				'key_validator_invalid_msj' => '',
				'key_validator_exist'       => '',
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
		if ( $field['type'] != 'key_validator' ) {
			return;
		}
		?>
		<div class="frm_html_field_placeholder">
			<div class="frm_html_field"><?= FormidableKeyFieldManager::t( "Validate key generated in the set target form." ) ?> </div>
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
		if ( $field['type'] != 'key_validator' ) {
			return;
		}
		
		$defaults = array(
			'key_validator_form_target' => '',
			'key_validator_invalid_msj' => '',
			'key_validator_exist'       => '',
		);
		
		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $field[ $k ] ) ) {
				$field[ $k ] = $v;
			}
		}

		$exist_key = "";
		if ( $field['key_validator_exist'] == "1" ) {
			$exist_key = "checked='checked'";
		}

		global $frm_form, $frm_field;
		$fields       = $frm_field->getAll( array( "type" => "key_generator" ) );
		$form_options = '';
		foreach ( $fields as $item ) {
			$form   = $frm_form->getOne( $item->form_id );
			$select = "";
			if ( $field['key_validator_form_target'] == $form->id ) {
				$select = 'selected="selected"';
			}
			$form_options .= "<option " . $select . " value='" . $form->id . "'>" . $form->name . "</option>";
		}
		?>
		<tr>
			<td>
				<label for="field_options[key_validator_form_target_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Select target form" ) ?></label>
				<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "Select the form where you generate key with field key generator." ) ?>"></span>
			</td>
			<td>
				<select name="field_options[key_validator_form_target_<?php echo $field['id'] ?>]" id="field_options[key_validator_form_target_<?php echo $field['id'] ?>]">
					<option value=""></option>
					<?php echo "$form_options"; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="field_options[key_validator_exist_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Validate if exist" ) ?></label>
				<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "If this options checked, the validation message will show when not exist the key, otherwise show message if the key exist. " ) ?>"></span>
			</td>
			<td>
				<input type="checkbox" <?= $exist_key ?> name="field_options[key_validator_exist_<?php echo $field['id'] ?>]" id="field_options[key_validator_exist_<?php echo $field['id'] ?>]" value="1"/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="field_options[key_validator_invalid_msj_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Error message" ) ?></label>
				<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "This is the message show by trhe form is validation not pass." ) ?>"></span>
			</td>
			<td>
				<input type="text" class="frm_classes frm_long_input" name="field_options[key_validator_invalid_msj_<?php echo $field['id'] ?>]" id="field_options[key_validator_invalid_msj_<?php echo $field['id'] ?>]" value="<?php echo $field['key_validator_invalid_msj'] ?>"/>
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
		if ( $field->type != 'key_validator' ) {
			return $field_options;
		}
		
		$defaults = array(
			'key_validator_form_target' => '',
			'key_validator_invalid_msj' => '',
			'key_validator_exist'       => '',
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
		if ( $field['type'] != 'key_validator' ) {
			return;
		}
		$field['value'] = stripslashes_deep( $field['value'] );

		?>
		<input type="text" id='field_<?= $field['field_key'] ?>' name='item_meta[<?= $field['id'] ?>]' value="<?php echo esc_attr( $field['value'] ) ?>"/>
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
		if ( $field->type != 'key_validator' || empty( $value ) ) {
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
		if ( $display['type'] == 'key_validator' ) {
			$display['unique']         = true;
			$display['required']       = true;
			$display['description']    = true;
			$display['options']        = true;
			$display['label_position'] = true;
			$display['css']            = true;
			$display['conf_field']     = true;
		}
		
		return $display;
	}

	/**
	 * Validate if exist the key in the form target
	 *
	 *
	 * @param $errors
	 * @param $posted_field
	 * @param $posted_value
	 *
	 * @return mixed
	 */
	public function validate_frm_entry( $errors, $posted_field, $posted_value ) {
		global $frm_field;
		if(!empty($posted_value)) {
			if ( $posted_field->type == "key_validator" ) {
				$target = $frm_field->get_option( $posted_field, "key_validator_form_target" );
				$msj    = $frm_field->get_option( $posted_field, "key_validator_invalid_msj" );
				$exist  = (bool) $frm_field->get_option( $posted_field, "key_validator_exist" );
				if ( ! empty( $target ) ) {
					$targets_fields = $frm_field->get_all_types_in_form( $target, 'key_generator' );
					foreach ( $targets_fields as $field ) {
						if ( $this->value_exists( $field->id, $posted_value ) == $exist ) {
							if ( empty( $msj ) ) {
								$msj = FrmFieldsHelper::get_error_msg( $field, 'invalid' );
							}
							$errors = array_merge( $errors, array( 'field' . $posted_field->id => $msj ) );

							return $errors;
						}
					}
				}

			}
		}
		else{
			$errors = array_merge( $errors, array( 'field' . $posted_field->id => FrmFieldsHelper::get_error_msg( $posted_field, 'blank' ) ) );
		}

		return $errors;
	}

	/**
	 * Check if the key value exist is unique
	 *
	 * @param $field_id
	 * @param $value
	 *
	 * @return mixed
	 */
	public function value_exists( $field_id, $value ) {
		global $wpdb;
		$table        = $wpdb->prefix . "frm_item_metas";
		$count_result = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE field_id = '" . $field_id . "' AND meta_value = '" . $value . "'" );

		return $count_result > 0;
	}
}