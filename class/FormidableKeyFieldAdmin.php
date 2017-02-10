<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableKeyFieldAdmin {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'fs_plugin_icon_' . FormidableKeyFieldManager::getSlug(), array( $this, 'handle_plugin_icon' ), 10, 1 );
	}
	
	/**
	 * Site menu
	 */
	public function admin_menu() {
		add_menu_page( FormidableKeyFieldManager::t( "Key Generator" ), FormidableKeyFieldManager::t( "Key Generator" ), 'frm_view_forms', FormidableKeyFieldManager::getSlug(), array( $this, 'addManagerMenuPage' ), FKF_IMAGE_PATH . "icon-20-gary.png" );
	}
	
	public function handle_plugin_icon( $ico_path ) {
		return FormidableKeyFieldManager::getSlug() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'icon-24.png';
	}

	function addManagerMenuPage() {
		global $frm_form, $frm_field;
		$fields = $frm_field->getAll( array( "type" => "key_generator" ) );
		if ( isset( $_POST["form_target"] ) && ! empty( $_POST["form_target"] ) ) {
			$cycles = 1;
			if ( isset( $_POST["cycle_target"] ) && ! empty( $_POST["cycle_target"] ) ) {
				$cycles = esc_attr( $_POST["cycle_target"] );
			}

			$target    = esc_attr( $_POST["form_target"] );

			$statuses = array();
			$field_statuses = $frm_field->get_all_types_in_form($target, "key_used");
			if(!empty($field_statuses)){
				foreach ( $field_statuses as $key => $status_field ) {
					$statuses[$status_field->id] = '0';
				}
			}

			foreach ( $fields as $item ) {
				if ( $item->form_id == $target ) {
					for ( $i = 0; $i < $cycles; $i ++ ) {
						$frm_metas = array();
						$frm_metas[$item->id] =  FormidableGeneratorField::generate_key( $item->id );
						if(!empty($statuses)){
							$frm_metas = $frm_metas + $statuses;
						}
						FrmEntry::create( array(
							'form_id'     => $target,
							'item_key'    => 'bulk_generated_' . $i,
							'frm_user_id' => get_current_user_id(),
							'item_meta'   => $frm_metas,
						) );
					}
					break;
				}
			}
		}

		$form_options = '';
		foreach ( $fields as $item ) {
			$form = $frm_form->getOne( $item->form_id );
			if ( $form->status != "published" ) {
				continue;
			}
			$form_options .= "<option value='" . $form->id . "'>" . $form->name . "</option>";
		}
		?>
		<div id="tab-tables" class="lkg-card card pressthis">
			<h2><?= FormidableKeyFieldManager::t( "Generate Key into a form" ) ?></h2>

			<p><?= FormidableKeyFieldManager::t( "Select a form target and how many key you want to generate." ) ?></p>

			<form enctype="multipart/form-data" method="post" name="lkg_commands" id="lkg_commands">
				<table class="form-table">
					<tbody>
					<tr class="form-field form-required">
						<td>
							<select name="form_target" id="form_target">
								<option value=""></option>
								<?php echo "$form_options"; ?>
							</select>

						</td>
						<td>
							<input type="number" name="cycle_target" id="cycle_target">
						</td>
					</tr>

					<tr class="form-field">
						<td colspan="2" style="text-align:left">
							<input type="submit" style="text-align:left" value="<?= FormidableKeyFieldManager::t( "Generate" ) ?>" class="button button-primary" id="lkg_data_submit" name="lkg_data_submit">
						</td>
					</tr>
					</tbody>
				</table>
				<input type="hidden" id="lkg_action" name="lkg_action" value="generate_keys">
			</form>

		</div>
	<?php
	}
}