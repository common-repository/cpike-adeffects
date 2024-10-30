<?php
/******
 プラグイン無効化の際にDB削除
 ******/
function cpae_uninstall() {
	global $wpdb;

	$table_name[0] = $wpdb->prefix . 'cpike_config';
	$table_name[1] = $wpdb->prefix . 'cpike_cv';
	$table_name[2] = $wpdb->prefix . 'cpike_cv_page';
	$table_name[3] = $wpdb->prefix . 'cpike_lp';

	foreach ($table_name as $val) {
		$wpdb->query("DROP TABLE IF EXISTS $val");
	}
}
