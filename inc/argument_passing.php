<?php

add_action('admin_init', 'cpae_argument_passing');

function cpae_argument_passing() {
	global $wpdb;
	$config = cpae_get_config_data();

	// storing Landing Page
	if(isset($_GET['cpae-lp-period']) && $_GET['cpae-lp-period']) :
		if(check_admin_referer('cpae-lp-period-key', 'cpae-lp-period')) :
			// get date
			$now_date = date_i18n('Y-m-d H:i:s');
			if(isset($_GET['date_to']) && $_GET['date_to']):
				$date_to = esc_html($_GET['date_to']);
				$formatted_date_to = $date_to;
			else :
				$formatted_date_to = $now_date;
			endif;

			if(isset($_GET['date_from']) && $_GET['date_from']):
				$date_from = esc_html($_GET['date_from']);
				$formatted_date_from = $date_from;
			else :
				$formatted_date_from = $formatted_date_to . " -" . $config['date_range'];
			endif;

			wp_safe_redirect( menu_page_url('cpae_setting', false) . '&date_from=' . strtotime($formatted_date_from) . '&date_to=' . strtotime($formatted_date_to));
		endif;
	endif;
}
