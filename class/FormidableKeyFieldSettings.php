<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableKeyFieldSettings {

	public static function route() {

		$action = isset( $_REQUEST['frm_action'] ) ? 'frm_action' : 'action';
		$action = FrmAppHelper::get_param( $action );
		if ( $action == 'process-form' ) {
			return self::process_form();
		} else {
			return self::display_form();
		}
	}

	/**
	 * @internal var gManager GManager_1_0
	 */
	public static function display_form() {
		$gManager = GManagerFactory::buildManager( 'FormidableKeyFieldManager', 'formidable_key_field', FormidableKeyFieldManager::getShort() );
		$key      = get_option( FormidableKeyFieldManager::getShort() . 'licence_key' );
		?>
		<h3 class="frm_first_h3"><?= FormidableKeyFieldManager::t( "Licence Data for Key Field" ) ?></h3>
		<table class="form-table">
			<tr>
				<td width="150px"><?= FormidableKeyFieldManager::t( "Version: " ) ?></td>
				<td>
					<span><?= FormidableKeyFieldManager::getVersion() ?></span>
				</td>
			</tr>
			<tr class="form-field" valign="top">
				<td width="150px">
					<label for="key"><?= FormidableKeyFieldManager::t( "Order Key: " ) ?></label>
					<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableKeyFieldManager::t( "Order key send to you with order confirmation, to get updates." ) ?>"></span>
				</td>
				<td><input type="text" name="<?= FormidableKeyFieldManager::getShort() ?>_key" id="<?= FormidableKeyFieldManager::getShort() ?>_key" value="<?= $key ?>"/></td>
			</tr>
			<tr class="form-field" valign="top">
				<td width="150px"><?= FormidableKeyFieldManager::t( "Key status: " ) ?></label></td>
				<td><?= $gManager->getStatus() ?></td>
			</tr>
		</table>
	<?php
	}

	public static function process_form() {
		if ( isset( $_POST[ FormidableKeyFieldManager::getShort() . '_key' ] ) && ! empty( $_POST[ FormidableKeyFieldManager::getShort() . '_key' ] ) ) {
			$gManager = GManagerFactory::buildManager( 'FormidableKeyFieldManager', 'formidable_key_field', FormidableKeyFieldManager::getShort() );
			$gManager->activate( $_POST[ FormidableKeyFieldManager::getShort() . '_key' ] );
			update_option( FormidableKeyFieldManager::getShort() . 'licence_key', $_POST[ FormidableKeyFieldManager::getShort() . '_key' ] );
		}
		else{
			delete_option(FormidableKeyFieldManager::getShort() . 'licence_key');
		}
		self::display_form();
	}
}