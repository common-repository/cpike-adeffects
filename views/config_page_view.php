<?php
	global $wpdb;
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cpike-adeffects' ) );
	}

	$config = cpae_get_config_data();
	$date_range_arr = array(
		'today' => esc_html__('Today', 'cpike-adeffects'),
		'yesterday' => esc_html__('Yesterday', 'cpike-adeffects'),
		'7 days' => esc_html__('Last 7 days', 'cpike-adeffects'),
		'30 days' => esc_html__('Last 30 days', 'cpike-adeffects'),
		'3 months' => esc_html__('Last 3 months', 'cpike-adeffects'),
		'6 months' => esc_html__('Last 6 months', 'cpike-adeffects'),
		'1 year' => esc_html__('Last 1 year', 'cpike-adeffects'),
	);
?>

<div class="wrap" id="cpike">
	<div class="wrap" id="cpike">
	<h1><?php esc_html_e('Configuration', 'cpike-adeffects'); ?></h1>
	<form id="cpae-submenu-form" action="">
		<?php wp_nonce_field('cpae-config-nonce-key', 'cpae-config-nonce'); ?>
		<label for="date_range"><?php esc_html_e('Date Range', 'cpike-adeffects'); ?></label>
		<select name="date_range">
		<?php
			foreach ($date_range_arr as $key => $val) {
		?>
			<option value="<?php echo esc_attr($key); ?>"<?php if($key == $config['date_range']){ echo ' selected'; } ?>><?php echo esc_attr($val); ?></option>
		<?php
			}
		?>
		</select>
		<input type="submit" value="<?php echo esc_attr( esc_html__( 'save', 'cpike-adeffects') ); ?>" class="button button-primary button-large">
	</form>
</div>
