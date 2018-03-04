<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableKeyFieldAdmin {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'fs_plugin_icon_' . FormidableKeyFieldManager::getSlug(), array( $this, 'handle_plugin_icon' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'front_enqueue_style' ) );
	}
	
	public function front_enqueue_style() {
		$current_screen = get_current_screen();
		if ( 'toplevel_page_formidable' === $current_screen->id ) {
			wp_enqueue_style( 'formidable_key_field', FKF_CSS_PATH . 'formidable_key_field.css' );
		}
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
		$fields       = FrmField::getAll( array( "type" => "key_generator" ) );
		$form_target  = FrmAppHelper::get_post_param( 'form_target' );
		$cycle_target = FrmAppHelper::get_post_param( 'cycle_target' );
		if ( ! empty( $form_target ) ) {
			$cycles = 1;
			if ( ! empty( $cycle_target ) ) {
				$cycles = esc_attr( $cycle_target );
			}
			$target         = esc_attr( $form_target );
			$statuses       = array();
			$field_statuses = FrmField::get_all_types_in_form( $target, "key_used" );
			
			if ( ! empty( $field_statuses ) ) {
				foreach ( $field_statuses as $key => $status_field ) {
					$statuses[ $status_field->id ] = '0';
				}
			}
			
			foreach ( $fields as $item ) {
				if ( $item->form_id == $target ) {
					for ( $i = 0; $i < $cycles; $i ++ ) {
						$frm_metas              = array();
						$frm_metas[ $item->id ] = FormidableGeneratorField::generate_key( $item->id );
						if ( ! empty( $statuses ) ) {
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
			$form = FrmForm::getOne( $item->form_id );
			if ( ! empty( $form ) && $form->status != "published" ) {
				continue;
			}
			$form_options .= sprintf( "<option value='%s'>%s</option>", $form->id, $form->name );
		}
		
		require FKF_VIEW_PATH . 'admin.php';
	}
}
