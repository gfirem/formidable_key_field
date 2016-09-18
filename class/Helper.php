<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Helper' ) ) :

	class Helper {

		/**
		 * Function to get key of the Formidable field from field id
		 *
		 * @uses globals $frmdb, $wpdb
		 *
		 * @param $field_id - Integer with field ID
		 *
		 * @return string
		 */
		function get_key_from_id_field( $field_id ) {
			$key_from_id = '';
			global $frmdb, $wpdb;
			if ( ! empty( $field_id ) && is_numeric( $field_id ) ) {
				$key_from_id = $wpdb->get_var( $wpdb->prepare( "SELECT field_key from $frmdb->fields WHERE id=%s", $field_id ) );
			}

			return $key_from_id;
		}

		/**
		 * Function to get type of the Formidable field from field id
		 *
		 * @uses globals $frmdb, $wpdb
		 *
		 * @param $field_id - Integer with field ID
		 *
		 * @return string
		 */
		function get_field_type( $field_id ) {
			$type = '';
			global $frmdb, $wpdb;
			if ( ! empty( $field_id ) && is_numeric( $field_id ) ) {
				$type = $wpdb->get_var( $wpdb->prepare( "SELECT type from $frmdb->fields WHERE id=%s", $field_id ) );
			}

			return $type;
		}

		/**
		 * Get data from formidable table for given userId
		 *
		 * @param $form_id
		 * @param $user_id
		 *
		 * @return bool|mixed
		 */
		function get_fmr_data_from_table( $form_id, $user_id ) {
			$where = array( 'it.form_id' => $form_id );
			if ( $user_id ) {
				$where['user_id'] = $user_id;
			}
			if ( isset( $new_ids ) && empty( $new_ids ) ) {
				$entries = false;
			} else {
				$entries = FrmEntry::getAll( $where, '', '', true, false );
			}

			return $entries;
		}
	}

endif;