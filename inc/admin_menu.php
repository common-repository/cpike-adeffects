<?php
/*
 menu pages
 */
function cpae_menu() {
	// dashboard
	add_menu_page(
		esc_html__('cPike', 'cpike-adeffects'),
		esc_html__('cPike AdEffects', 'cpike-adeffects'),
		'manage_options',
		'cpae_setting',
		'cpae_options'
	);

	// CV Page
	add_submenu_page(
		'cpae_setting',
		esc_html__('add conversion page', 'cpike-adeffects'),
		esc_html__('Goal Pages', 'cpike-adeffects'),
		'manage_options',
		'cpae-conversion-pages',
		'cpae_conversion_pages'
	);

	// Config Page
	add_submenu_page(
		'cpae_setting',
		esc_html__('add config page', 'cpike-adeffects'),
		esc_html__('Configuration', 'cpike-adeffects'),
		'manage_options',
		'cpae-config-page',
		'cpae_config_page'
	);
}
add_action( 'admin_menu', 'cpae_menu' );

/*
 view and css
 */
// cpike top
function cpae_options() {
	include_once(dirname(__FILE__) . '/../views/admin_menu_view.php');
	wp_enqueue_style('cpike_css', plugins_url('../css/style.css', __FILE__));
	wp_enqueue_style('jquery-ui', plugins_url('../css/jquery-ui.css', __FILE__));
	wp_enqueue_script('jquery-ui-datepicker');
}

// conversion pages
function cpae_conversion_pages() {
	include_once(dirname(__FILE__) . '/../views/conversion_pages_view.php');
	wp_enqueue_style('cpike_css', plugins_url('../css/style.css', __FILE__));
}

// config pages
function cpae_config_page() {
	include_once(dirname(__FILE__) . '/../views/config_page_view.php');
	wp_enqueue_style('cpike_css', plugins_url('../css/style.css', __FILE__));
}
