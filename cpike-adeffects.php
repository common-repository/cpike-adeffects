<?php
/*
Plugin Name: cPike AdEffects
Plugin URI: https://cpike.co/
Description: Simplest Marketing Plugin. So easy to identify where users come from to the website.
Version: 0.2.7
Author: cPike
Author URI: https://cpike.co/
License: GPL2
Text Domain: cpike-adeffects
Domain Path: /languages
*/

/*
 initialize
 */
$plugin_path = plugin_dir_path(__FILE__);

require_once($plugin_path . '/settings.php');

// create / drop tables
register_activation_hook(__FILE__, 'cpae_create_tables');
register_deactivation_hook(__FILE__, 'cpae_uninstall');

// this plugin's directory
function cpae_dir() {
	$cpae_dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	return $cpae_dir;
}

/*
 statics : cpike_key and conversion aquisition
 */
function cpae_stats() {
	global $wpdb;

	$cv_kind = 99999; // default CV = NaN

	// cpike_key acquisition
	if(isset($_GET['cpike']) && $_GET['cpike']) :
		$cpike_key = htmlspecialchars($_GET['cpike']);
	endif;

	if(isset($cpike_key) && $cpike_key):
		$results = cpae_get_database('cpike_lp');
		foreach ($results as $key => $val) :
			if($cpike_key == $val->cpike_key) :
				$cv_kind = 1; // CV = Landing Page(1)
			endif;
		endforeach;
		setcookie('cpike_key', $cpike_key, time() + (180*(24*60*60)), '/'); // lifetime : 180 days
		setcookie('cpike_first_access', time(), time() + (180*(24*60*60)), '/'); // lifetime : 180 days
	endif;

	// CV processing - branching posts/pages
	$now_page_id = get_the_ID();
	$results = cpae_get_database('cpike_cv_page');

	foreach ($results as $key => $val) :
		if($now_page_id == $val->cv_page_id) :
			$cv_kind = 0; // CV = Goal Page(0)

			if(isset($_COOKIE['cpike_key']) && $_COOKIE['cpike_key']) :
				$cpike_key = htmlspecialchars($_COOKIE['cpike_key']);
			endif;

		endif;
	endforeach;

	// processing write DB
	if($cv_kind != 99999 && isset($cpike_key) && $cpike_key) :
		$table_name = $wpdb->prefix . "cpike_cv";
		$now = current_time('mysql');
		$wpdb->insert( $table_name, array(
			'id' => '',
			'cpike_key' => $cpike_key,
			'cv_kind' => $cv_kind, // Goal Page(0) or Landing page(1)
			'created' => $now,
			'modified' => $now
		), array('%d', '%s', '%s', '%s', '%s') );
	endif;

}
add_action('get_header', 'cpae_stats');

/*
 i18n
 */
$cpae = new Cpike_Adeffects();
$cpae->register();

class Cpike_Adeffects {
	public function register() {
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
	}

	public function Plugins_loaded() {
		load_plugin_textdomain(
			'cpike-adeffects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
}