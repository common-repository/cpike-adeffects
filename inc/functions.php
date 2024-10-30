<?php
/******
 add custom post type
 ******/
add_action('init', 'cpae_register_post_type_and_taxonomy_for_landing_pages');
function cpae_register_post_type_and_taxonomy_for_landing_pages() {
	/* landing_pages */
	register_post_type (
		'landing_pages', array(
			'labels' => array(
				'name' => 'Landing Page',
				'add_new_item' => 'Add Landing Page',
				'edit_item' => 'Edit Landing Page'
			),
			'public' => true,
			'hierarchical' => true,
			'has_archive' => true,
			'rewrite' => array('with_front' => false),
			'supports' => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail'
			)
		)
	);
	register_taxonomy(
		'landing_pages-tag',
		'landing_pages',
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => 'label',
			'singular_label' => 'label',
			'public' => true,
			'show_ui' => true,
			'rewrite' => array('with_front' => false)
		)
	);
}

/******
 acquisition data
 ******/
function cpae_get_database($table_last_name = 'cpike_config', $where_column = '', $where_val = '', $condition = '') {
	global $wpdb;
	$table_name = $wpdb->prefix . $table_last_name;

	if($condition == 'not_equal'):
		$condition_exp = '!=';
	else:
		$condition_exp = '==';
	endif;
	if($where_column):
		$prepared_sql = $wpdb->prepare("SELECT * FROM {$table_name} where {$where_column} {$condition_exp} %s", $where_val);
	else:
		$prepared_sql = "SELECT * FROM {$table_name}";
	endif;
	$results = $wpdb->get_results($prepared_sql);
	return($results);
}

function cpae_admin_notice() {
	if ( $messages = get_transient('cpae-custom-message')):
		echo '<div class="updated notice is-dismissible">';
		echo '<ul>';
		foreach( $messages as $message ):
			echo '<li>' . esc_html($message);
			echo '<button type="button" class="notice-dismiss">';
			echo '<span class="screen-reader-text">close this message</span>';
			echo '</button></li>';
		endforeach;
		echo '</ul>';
		echo '</div>';
	endif;
}
add_action( 'admin_notices', 'cpae_admin_notice' );

function cpae_get_config_data() {
	global $wpdb;

	$table_name = $wpdb->prefix . "cpike_config";
	$prepared_sql = $wpdb->prepare("SELECT * FROM {$table_name} where id != %d", 0);
	$config_arr = $wpdb->get_results($prepared_sql);

	foreach ($config_arr as $val) {
		$config[$val->meta_key] = $val->meta_value;
	}

	return $config;
}

function cpae_get_date_range() {
	$config = cpae_get_config_data();

	$now_date = date_i18n('Y-m-d H:i:s');
	$default_date_range = $config['date_range'];
	if($default_date_range == 'today') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date));
	elseif($default_date_range == 'yesterday') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -1 day"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	elseif($default_date_range == '7 days') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -8 days"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	elseif($default_date_range == '30 days') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -31 days"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	elseif($default_date_range == '3 months') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -3 months"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	elseif($default_date_range == '6 months') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -6 months"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	elseif($default_date_range == '1 year') :
		$default_date_from = date('Y-m-d 00:00:00', strtotime($now_date . " -1 year"));
		$default_date_to = date('Y-m-d 23:59:59', strtotime($now_date. " -1 day"));
	else :
		$default_date_from = '';
		$default_date_to = '';
	endif;

	$date_ranges = array(
		'from' => $default_date_from,
		'to' => $default_date_to
	);
	return $date_ranges;
}

function cpae_get_posts_by_id($param_id) {
	global $wpdb;
	if(isset($param_id) && $param_id) :
		$prepared_sql = $wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE id = %d", $param_id);
		$posts_arr = $wpdb->get_row($prepared_sql);
		return $posts_arr;
	else:
		return;
	endif;
}

function cpae_get_post_type($param_id) {
	$cv_p_arr = cpae_get_posts_by_id($param_id);
	if($cv_p_arr->post_type == 'post'):
		$cv_post_type = 'p';
	else:
		$cv_post_type = 'page_id';
	endif;
	return $cv_post_type;
}