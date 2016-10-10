<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableKeyFieldAdmin {

	function __construct() {
		require_once 'GManagerFactory.php';

		add_filter( 'frm_add_settings_section', array( $this, 'add_formidable_key_field_SettingPage' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_formidable_key_field_setting_link' ), 9, 2 );

		add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
	}

	public function addAdminMenu() {
		add_submenu_page( 'formidable', FormidableKeyFieldManager::t( 'Licence Key Generator' ), FormidableKeyFieldManager::t( 'L. Key Generator' ), 'frm_view_forms', 'formidable-key-generator', array( $this, 'addManagerMenuPage' ), 'dashicons-admin-generic' );
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

	/**
	 * Add setting page to global formidable settings
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function add_formidable_key_field_SettingPage( $sections ) {
		$sections['licences_key'] = array(
			'name'     => FormidableKeyFieldManager::t( "License Key Generator" ),
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
			$link = sprintf( '<a href="%s">%s</a>', esc_attr( admin_url( 'admin.php?page=formidable-settings&t=licences_key_settings' ) ), FormidableKeyFieldManager::t( "Settings" ) );
			array_unshift( $links, $link );
		}

		return $links;
	}
}