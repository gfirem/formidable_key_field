<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableValidatorField {
	
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
			add_filter( "frm_validate_field_entry", array( $this, "validate_frm_entry" ), 10, 3 );
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
			$form = $frm_form->getOne( $item->form_id );
			if ( $form->status != "published" ) {
				continue;
			}
			$form_options .= "<option value='" . $form->id . "'>" . $form->name . "</option>";
		}
		if ( ! empty( $field['key_validator_form_target'] ) ) {
			$fields_targets_obj = maybe_unserialize( $field['key_validator_form_target'] );
			$fields_targets     = json_encode( $fields_targets_obj );
		}
		?>
        <style>
            .key_target_icon {
                vertical-align: middle;
                text-decoration: none !important;
                font-weight: normal;
                text-shadow: none;
                font-family: dashicons;
                font-size: 20px;
            }

            .key_target_icon:hover {
                text-decoration: none;
            }
        </style>
        <tr class="frm_options_heading">
            <td colspan="2">
                <div class="menu-settings">
                    <h3 class="frm_no_bg"><?= FormidableKeyFieldManager::t( "Validate options" ) ?></h3>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label for="field_options[key_validator_form_target_<?php echo $field['id'] ?>]"><?= FormidableKeyFieldManager::t( "Select target form" ) ?></label>
                <span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "Select the form where you generate key with field key generator." ) ?>"></span>
            </td>
            <td id="target_td_container">
				<?php
				if ( ! empty( $fields_targets_obj ) ) {
					$i = 0;
					foreach ( json_decode( $fields_targets_obj ) as $key => $item ) {
						?>
                        <div id="key_validator_form_target_<?php echo $field['id'] ?>" class="field_target_container">
                            <select name="<?php echo $item->name ?>" id="<?php echo $item->name ?>" class="field_target_select">
                                <option value=""></option>
								<?php echo "$form_options"; ?>
                            </select>
                            <a class="dashicons-minus key_target_remove key_target_icon" href="javascript:void(0)"></a>
                            <a class="dashicons-plus key_target_add key_target_icon" href="javascript:void(0)"></a>
                        </div>
						<?php
						$i ++;
					}
				} else {
					?>
                    <div id="key_validator_form_target_<?php echo $field['id'] ?>" class="field_target_container">
                        <select name="field_target_0" id="field_target_0" class="field_target_select">
                            <option value=""></option>
							<?php echo "$form_options"; ?>
                        </select>
                        <a class="dashicons-minus key_target_remove key_target_icon" href="javascript:void(0)"></a>
                        <a class="dashicons-plus key_target_add key_target_icon" href="javascript:void(0)"></a>
                    </div>
				<?php } ?>
                <input type="hidden" name="field_options[key_validator_form_target_<?php echo $field['id'] ?>]" id="field_options[key_validator_form_target_<?php echo $field['id'] ?>]" value='<?php echo $field['key_validator_form_target'] ?>'>
            </td>
        </tr>
        <script>
			jQuery(document).ready(function ($) {
				var start_id = "field_target";
				var target_id = "field_options[key_validator_form_target_<?php echo $field['id'] ?>]";
				var serialized_targets = $("input[name='" + target_id + "']").val();
				if (serialized_targets) {
					serialized_targets = $.parseJSON(serialized_targets);
					jQuery.each(serialized_targets, function (i, item) {
						$("#" + item.name + ">option[value='" + item.value + "']").attr("selected", "selected");
					});
				}

				var onChange = function () {
					var serialized = $("select[id^='field_target_']").serializeArray();
					var jsonSer = JSON.stringify(serialized);
					$("input[name='" + target_id + "']").val(jsonSer);
				};

				var add_target = function () {
					$("#target_td_container").append($("#key_validator_form_target_<?php echo $field['id'] ?>").clone());
					var i = 0;
					$(".field_target_select").each(function () {
						$(this).attr("id", start_id + "_" + i);
						$(this).attr("name", start_id + "_" + i);
						$(this).change(onChange);
						i++;
					});
					$(".key_target_add").unbind("click").click(add_target);
					$(".key_target_remove").unbind("click").click(remove_target);
					onChange();
				};

				var remove_target = function () {
					var size = $(".field_target_select").size();
					if (size > 1) {
						$(this).parent().remove();
						$(".key_target_add").unbind("click").click(add_target);
						$(".key_target_remove").unbind("click").click(remove_target);
						onChange();
					}
				};

				$(".field_target_select").change(onChange);

				$(".key_target_add").click(add_target);
				$(".key_target_remove").click(remove_target);

			});
        </script>
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
                <span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "This is the message show by the form is validation not pass." ) ?>"></span>
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
			$display['read_only']      = false;
			$display['description']    = true;
			$display['options']        = true;
			$display['label_position'] = true;
			$display['css']            = true;
			$display['conf_field']     = false;
			$display['default_value']  = true;
			$display['visibility']     = true;
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
		global $frm_field, $frm_entry_meta, $frm_form;
		
		
		if ( $posted_field->type == "key_validator" ) {
			if ( empty( $posted_value ) && $posted_field->required ) {
				$errors = array_merge( $errors, array( 'field' . $posted_field->id => FrmFieldsHelper::get_error_msg( $posted_field, 'blank' ) ) );
				
				return $errors;
			}
			$target = $frm_field->get_option( $posted_field, "key_validator_form_target" );
			$msj    = $frm_field->get_option( $posted_field, "key_validator_invalid_msj" );
			$exist  = (bool) $frm_field->get_option( $posted_field, "key_validator_exist" );
			if ( ! empty( $target ) ) {
				if ( empty( $msj ) ) {
					$msj = FrmFieldsHelper::get_error_msg( $frm_field->getOne( $posted_field->id ), 'invalid' );
				}
				$fields_ids = array();
				$key_used   = array();
				foreach ( json_decode( $target ) as $key => $item ) {
					$targets_fields = $frm_field->get_all_types_in_form( $item->value, 'key_generator' );
					if ( ! empty( $targets_fields ) ) {
						foreach ( $targets_fields as $field ) {
							$fields_ids[] = $field->id;
						}
					}
					$targets_fields_used = $frm_field->get_all_types_in_form( $item->value, 'key_used' );
					if ( ! empty( $targets_fields_used ) ) {
						foreach ( $targets_fields_used as $field ) {
							$key_used[] = $field->id;
						}
					}
					
				}
				if ( ! empty( $fields_ids ) ) {
					$entry_id = $this->value_exists( $fields_ids, htmlentities( $posted_value ) );
					if ( empty( $entry_id ) != $exist ) {
						$errors = array_merge( $errors, array( 'field' . $posted_field->id => $msj ) );
						
						return $errors;
					} else {
						foreach ( $fields_ids as $key => $id ) {
							$exist = $this->value_exist( $id, htmlentities( $posted_value ) );
							if ( ! empty( $exist ) ) {
								$field          = $frm_field->getOne( $id );
								$field_statuses = $frm_field->get_all_types_in_form( $field->form_id, "key_used" );
								if ( ! empty( $field_statuses ) ) {
									foreach ( $field_statuses as $key_1 => $status_field ) {
										$value = FrmEntryMeta::get_entry_meta_by_field( $entry_id, $status_field->id );
										if ( empty( $value ) ) {
											$result = FrmEntryMeta::add_entry_meta( $entry_id, $status_field->id, null, '1' );
										} else {
											$result = FrmEntryMeta::update_entry_meta( $entry_id, $status_field->id, null, '1' );
										}
									}
									
								}
							}
						}
					}
				}
			}
		}
		
		
		return $errors;
	}
	
	/**
	 * Check if given ids have the key value
	 *
	 * @param $field_ids
	 * @param $value
	 *
	 * @return mixed
	 */
	public function value_exists( $field_ids, $value ) {
		global $wpdb;
		$table  = $wpdb->prefix . "frm_item_metas";
		$result = $wpdb->get_var( "SELECT item_id FROM $table WHERE field_id IN (" . join( ", ", $field_ids ) . ") AND meta_value = '" . $value . "'" );
		
		return $result;
	}
	
	/**
	 * Check if given id have the key value
	 *
	 * @param $field_id
	 * @param $value
	 *
	 * @return mixed
	 */
	public function value_exist( $field_id, $value ) {
		global $wpdb;
		$table  = $wpdb->prefix . "frm_item_metas";
		$result = $wpdb->get_var( "SELECT item_id FROM $table WHERE field_id = '" . $field_id . "' AND meta_value = '" . $value . "'" );
		
		return $result;
	}
}