<?php
// table operation manual
// $wpdb->insert( $table, $data, $format );
// $wpdb->update( $table, $data, $where, $format = null, $where_format = null );
// $wpdb->delete( $table, $where, $where_format = null );

add_action('admin_init', 'cpae_write_landing_and_conversion_page');
add_action('admin_init', 'cpae_delete_landing_and_conversion_page');

function cpae_write_landing_and_conversion_page() {
	global $wpdb;
	// storing Landing Page
	if(isset($_GET['cpae-nonce']) && $_GET['cpae-nonce']) :
		if(check_admin_referer('cpae-nonce-key', 'cpae-nonce')) :
			$table_name = $wpdb->prefix . "cpike_lp";
			$now = current_time('mysql');

			// UPDATE
			if(isset($_GET['cpike_lp_id']) && $_GET['cpike_lp_id']) :
				$condition = array('id' => esc_html($_GET['cpike_lp_id']));
				$result = $wpdb->update( $table_name, array(
					'name' => esc_html($_GET['name']),
					'landing_page_id' => esc_html($_GET['landing_page']),
					'modified' => $now
				), $condition, array('%s', '%d', '%s'));
			else :
				// INSERT
				$cpike_key = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);
				$result = $wpdb->insert( $table_name, array(
					'id' => '',
					'name' => esc_html($_GET['name']),
					'kind' => 'post',
					'landing_page_id' => esc_html($_GET['landing_page']),
					'cpike_key' => $cpike_key,
					'cat' => '', // ex) GDN AD 01
					'is_deleted' => 0,
					'created' => $now,
					'modified' => $now
				), array('%d', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%s') );
			endif;
			wp_safe_redirect( menu_page_url('cpae_setting', false));
		endif;
	endif;

	// storing CV Page
	if(isset($_GET['cpae-cv-nonce']) && $_GET['cpae-cv-nonce']) :
		if(check_admin_referer('cpae-nonce-key', 'cpae-cv-nonce')) :
			$table_name = $wpdb->prefix . "cpike_cv_page";
			$cv_page_id = esc_html($_GET['cv_page']);

			$prepared_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE cv_page_id = %d AND is_deleted = 0", $cv_page_id);

			$cpike_cv_page_arr = $wpdb->get_row($prepared_sql);

			if(!empty($cpike_cv_page_arr)):
				$messages = array(esc_html__('you have already set the page as CV point', 'cpike-adeffects'));
				set_transient( 'cpae-custom-message', $messages, 10 );
				wp_safe_redirect( menu_page_url('cpae-conversion-pages', false));
				return;
			endif;

			$now = current_time('mysql');
			$result = $wpdb->insert( $table_name, array(
				'id' => '',
				'cv_page_id' => $cv_page_id,
				'created' => $now,
				'modified' => $now
			), array('%d', '%s', '%s', '%s') );
			wp_safe_redirect(menu_page_url('cpae-conversion-pages', false));
		endif;
	endif;

	// UPDATE Config Data
	if(isset($_GET['cpae-config-nonce']) && $_GET['cpae-config-nonce']) :
		if(check_admin_referer('cpae-config-nonce-key', 'cpae-config-nonce')) :
			$table_name = $wpdb->prefix . "cpike_config";
			$now = current_time('mysql');

			// UPDATE
			if(isset($_GET['date_range']) && $_GET['date_range']) :
				$condition = array('meta_key' => 'date_range');
				$result = $wpdb->update( $table_name, array(
					'meta_value' => esc_html($_GET['date_range']),
					'modified' => $now
				), $condition, array('%s', '%s'));
			endif;
			$messages = array(esc_html__('config datas have been saved', 'cpike-adeffects'));
			set_transient( 'cpae-custom-message', $messages, 10 );
			wp_safe_redirect( menu_page_url('cpae_setting', false));
		endif;
	endif;

}

function cpae_delete_landing_and_conversion_page() {
	global $wpdb;
	$now = current_time('mysql');

	// logical delete - Landing Page
	if(isset($_GET['cpae-delete-nonce']) && $_GET['cpae-delete-nonce']) :
		if(check_admin_referer('cpae-delete-nonce-key', 'cpae-delete-nonce')) :
			$lp_id = htmlspecialchars($_GET['lp_id']);
			// logical deletion
			$table_name = $wpdb->prefix . "cpike_lp";

			$result = $wpdb->update( $table_name,
				array(
					'is_deleted' => 1,
					'modified' => $now
				),
				array('id' => $lp_id),
				array('%d', '%s')
			);

			$messages = array(esc_html__('deleted', 'cpike-adeffects'));
			set_transient( 'cpae-custom-message', $messages, 10 );

			wp_safe_redirect( menu_page_url('cpae_setting', false));
		endif;
	endif;

	// logical delete - CV Page
	if(isset($_GET['cpae-delete-cv-nonce']) && $_GET['cpae-delete-cv-nonce']) :
		if(check_admin_referer('cpae-delete-cv-nonce-key', 'cpae-delete-cv-nonce')) :
			$id = htmlspecialchars($_GET['id']);
			// logical deletion
			$table_name = $wpdb->prefix . "cpike_cv_page";

			$result = $wpdb->update( $table_name,
				array(
					'is_deleted' => 1,
					'modified' => $now
				),
				array('id' => $id),
				array('%d', '%s')
			);

			$messages = array(esc_html__('deleted', 'cpike-adeffects'));
			set_transient( 'cpae-custom-message', $messages, 10 );

			wp_safe_redirect( menu_page_url('cpae-conversion-pages', false));
		endif;
	endif;
}