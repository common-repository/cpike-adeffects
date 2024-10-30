<?php
/******
 create tables activating cPike
 ******/

function cpae_create_tables() {
	global $wpdb;

	$sql = "";
	$charset_collate = "";

	// add table prefix
	$table_name[0] = $wpdb->prefix . 'cpike_config';
	$table_name[1] = $wpdb->prefix . 'cpike_cv';
	$table_name[2] = $wpdb->prefix . 'cpike_cv_page';
	$table_name[3] = $wpdb->prefix . 'cpike_lp';

	// set charset
	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";

	// collating sequence - default : utf8_general_ci
	if ( !empty($wpdb->collate) )
		$charset_collate .= "COLLATE {$wpdb->collate}";

	// create tables
	// cpike_config
	$sql[0] = "CREATE TABLE {$table_name[0]} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			meta_key VARCHAR(255) NOT NULL,
			meta_value VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		) {$charset_collate};";

	// cpike_cv
	$sql[1] = "CREATE TABLE {$table_name[1]} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			cpike_key VARCHAR(255) NOT NULL,
			device_key VARCHAR(255) NOT NULL,
			device_kind VARCHAR(255) NOT NULL,
			cv_kind VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		) {$charset_collate};";

	// cpike_cv_page
	$sql[2] = "CREATE TABLE {$table_name[2]} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			cv_page_id BIGINT NOT NULL,
			is_deleted TINYINT NOT NULL,
			created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		) {$charset_collate};";

	// cpike_lp
	$sql[3] = "CREATE TABLE {$table_name[3]} (
			id BIGINT NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			kind VARCHAR(255) NOT NULL,
			landing_page_id BIGINT NOT NULL,
			cpike_key VARCHAR(255) NOT NULL,
			cat VARCHAR(255) NOT NULL,
			is_deleted TINYINT NOT NULL,
			created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id)
		) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// execute to create tables
	foreach ($sql as $val) {
		dbDelta($val);
	}

	///////////////////////////////////
	////////// insert initial values to config table
	///////////////////////////////////
	$now = current_time('mysql');
	$table_name = $wpdb->prefix . 'cpike_config';
	$metas['user_name'] = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8); // random 8 digits character string
	$metas['date_range'] = '6 months';

	foreach ($metas as $key => $value) {
		$result = $wpdb->query( $wpdb->prepare(
			"INSERT INTO {$table_name}
			(id, meta_key, meta_value, created, modified)
			VALUES (%d, %s, %s, %s, %s)",
			0, $key, $value, $now, $now
		));
	}
}
