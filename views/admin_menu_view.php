<?php
	global $wpdb;
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cpike-adeffects' ) );
	}

	$date_ranges = cpae_get_date_range();

	if(isset($_GET['action']) && $_GET['action']) :
		$action = esc_html($_GET['action']);
	else :
		$action = '';
	endif;

	// query for settting landing pages form POSTS
	$query_arg = array(
		'post_type'=>'post',
		'post_status'=>'publish'
	);
	$the_query = new WP_Query( $query_arg );
	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		$posts_arr[get_the_ID()] = get_the_title();
	endwhile;
	wp_reset_postdata();

	// query for settting landing pages form PAGES
	$query_arg = array(
		'post_type'=>'page',
		'post_status'=>'publish'
	);
	$the_query = new WP_Query( $query_arg );
	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		$posts_arr[get_the_ID()] = get_the_title();
	endwhile;
	wp_reset_postdata();

	// conditional branch - isset cpike_id
	isset($_GET['cpike_id']) ? $cpike_id = esc_html($_GET['cpike_id']) : $cpike_id = '';
	if( $action == 'edit' && $cpike_id ) : // edit cpike_lp
		$table_name = $wpdb->prefix . "cpike_lp";
		$prepared_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $cpike_id);
		$cpike_lp_arr = $wpdb->get_row($prepared_sql);
?>
	<div class="wrap" id="cpike">
		<h1>Edit AD</h1>
	</div>

	<form id="cpae-submenu-form" action="">
		<?php wp_nonce_field('cpae-nonce-key', 'cpae-nonce'); ?>
		<label for="name"><?php esc_html_e('AD title', 'cpike-adeffects'); ?> : </label>
		<input type="text" name="name" value="<?php if(isset($cpike_lp_arr)) { echo $cpike_lp_arr->name; } ?>">
		<label for="name"><?php esc_html_e('Link to the landing page', 'cpike-adeffects'); ?> : </label>
		<select name="landing_page">
		<?php
			foreach ($posts_arr as $key => $val) {
		?>
			<option value="<?php echo esc_attr($key); ?>"<?php if($key == $cpike_lp_arr->landing_page_id) { echo " selected"; } ?>><?php echo esc_attr($val); ?></option>
		<?php
			}
		?>
		</select>
		<input type="hidden" name="cpike_lp_id" value="<?php echo $cpike_lp_arr->id; ?>">
		<input type="submit" value="<?php echo esc_attr( esc_html__( 'save', 'cpike-adeffects') ); ?>" class="button button-primary button-large">
	</form>

<?php else: ?>

	<div class="wrap" id="cpike">
	<h1>cPike AdEffects</h1>
	<h2><?php esc_html_e('List of ADs', 'cpike-adeffects'); ?></h2>
	<?php
		////// jquery date picker //////
		// get date
		if(isset($_GET['date_from']) && $_GET['date_from']):
			$date_from = esc_html($_GET['date_from']);
			$formatted_date_from = date('Y-m-d H:i:s', $date_from);
		else :
			$formatted_date_from = $date_ranges['from'];
		endif;
		if(isset($_GET['date_to']) && $_GET['date_to']):
			$date_to = esc_html($_GET['date_to']);
			$formatted_date_to = date('Y-m-d 23:59:59', $date_to);
		else :
			$formatted_date_to = $date_ranges['to'];
		endif;
	?>
	<form action="" class="date_range">
		<?php wp_nonce_field('cpae-lp-period-key', 'cpae-lp-period'); ?>
		Date Range :
		<input type="text" class="term-datepicker" id="date_from" name="date_from" value="<?php echo date("M d Y", strtotime($formatted_date_from)); ?>"> -
		<input type="text" class="term-datepicker" id="date_to" name="date_to" value="<?php echo date("M d Y", strtotime($formatted_date_to)); ?>">
		<button type="submit" class="button button-primary"><?php esc_html_e('Apply', 'cpike-adeffects'); ?></button>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="admin.php?page=cpae-config-page" class="button"><?php esc_html_e('Change default date range', 'cpike-adeffects'); ?></a>
	</form>

	<form action="">
		<?php wp_nonce_field('cpae-delete-nonce-key', 'cpae-delete-nonce'); ?>
		<table>
		<tr><th><?php esc_html_e('AD title', 'cpike-adeffects'); ?></th><th><?php esc_html_e('Landing Page title', 'cpike-adeffects'); ?></th><th><?php esc_html_e('cPike key', 'cpike-adeffects'); ?></th><th><?php esc_html_e('Generated URL', 'cpike-adeffects'); ?></th><th><?php esc_html_e('Clicked Through', 'cpike-adeffects'); ?></th><th><?php esc_html_e('Reached to Goal', 'cpike-adeffects'); ?></th><th>CVR</th><th><?php esc_html_e('operation', 'cpike-adeffects'); ?></th></tr>
<?php
	$resultsX = cpae_get_database('cpike_lp', 'is_deleted', 1, 'not_equal');

	$cv_kind_arr = array(0, 1); // // Goal Page(0) or Landing page(1)
	foreach ($resultsX as $key => $val) {
		$query_arg = array(
			'p' => esc_html($val->landing_page_id),
			'post_type'=> array('post', 'page'),
			'post_status'=>'publish'
		);
		$the_query = new WP_Query( $query_arg );
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			// acquiring times of CV
			$table_cpike_cv = $wpdb->prefix . "cpike_cv";

			foreach ($cv_kind_arr as $val_cv):
				$prepared_sql = $wpdb->prepare("SELECT id, cpike_key FROM {$table_cpike_cv} WHERE cpike_key = %s AND cv_kind = %d AND modified BETWEEN %s AND %s", $val->cpike_key, $val_cv, $formatted_date_from, $formatted_date_to);
				$result_cv[$val_cv] = $wpdb->get_results($prepared_sql);
			endforeach;

			$num_ct = count($result_cv[1]);
			$num_cv = count($result_cv[0]);
			if(isset($num_ct) && $num_ct != 0) {
				$cvr = ($num_cv / $num_ct) * 100;
			} else {
				$cvr = 0;
			}
			$cv_post_type = cpae_get_post_type($val->landing_page_id);

?>
		<tr>
			<td><?php echo esc_html($val->name); ?></td>
			<td><?php echo get_the_title(); ?></td>
			<td><?php echo esc_html($val->cpike_key); ?></td>
			<td><?php echo get_bloginfo('url'); ?>?<?php echo $cv_post_type; ?>=<?php echo esc_html($val->landing_page_id); ?>&cpike=<?php echo esc_html($val->cpike_key); ?></td>
			<td class="center"><?php echo $num_ct; // clicked throughs ?></td>
			<td class="center"><?php echo $num_cv; // conversions ?></td>
			<td class="center nowrap"><?php echo number_format($cvr, 2),PHP_EOL; ?>%</td>
			<td class="center nowrap">
				<a href="admin.php?page=<?php echo esc_html($_GET['page']); ?>&action=edit&cpike_id=<?php echo esc_html($val->id); ?>" class="button button-primary button-large"><?php esc_html_e('Edit', 'cpike-adeffects'); ?></a>
				<button type="submit" name="lp_id" value="<?php echo esc_html($val->id); ?>" class="button button-large" onclick='return confirm("<?php esc_html_e('Are you sure you want to delete this record?', 'cpike-adeffects'); ?>");'><?php esc_html_e('Delete', 'cpike-adeffects'); ?></button>
			</td>
		</tr>
<?php
		endwhile;
		wp_reset_postdata();
	}
?>
		</table>
	</form>

	<div class="wrap" id="cpike">
		<h1><?php esc_html_e('Add AD', 'cpike-adeffects'); ?></h1>
	</div>

	<form id="cpae-submenu-form" action="">
		<?php wp_nonce_field('cpae-nonce-key', 'cpae-nonce'); ?>
		<label for="name"><?php esc_html_e('AD title', 'cpike-adeffects'); ?> : </label>
		<input type="text" name="name" value="<?php if(isset($cpike_lp_arr)) { echo $cpike_lp_arr->name; } ?>">
		<label for="name"><?php esc_html_e('Link to the landing page', 'cpike-adeffects'); ?> : </label>
		<select name="landing_page">
		<?php
			foreach ($posts_arr as $key => $val) {
		?>
			<option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($val); ?></option>
		<?php
			}
		?>
		</select>
		<input type="submit" value="<?php echo esc_attr( esc_html__( 'Save', 'cpike-adeffects') ); ?>" class="button button-primary button-large">
	</form>

<?php endif; ?>

	<div class="wrap" id="usage_link">
		<div class="link_btn">
			<a href="https://cpike.co?page_id=7&cpike=y9o7lk2v" target="_blank"><?php esc_html_e('Click to see the QUICK START of cPike AdEffects', 'cpike-adeffects'); ?></a>
		</div>
	</div>

<script>
	jQuery(document).ready(function($) {
		$('.term-datepicker').datepicker();
		$('.term-datepicker').datepicker("option", "dateFormat", "M dd yy");
		$(".term-datepicker#date_from").datepicker("setDate", "<?php echo date("M d Y", strtotime($formatted_date_from)); ?>");
		$(".term-datepicker#date_to").datepicker("setDate", "<?php echo date("M d Y", strtotime($formatted_date_to)); ?>");
});
</script>