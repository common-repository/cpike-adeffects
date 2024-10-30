<?php
	global $wpdb;
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cpike-adeffects' ) );
	}

	$query_arg = array(
		'post_type'=>array('post','page'),
		'post_status'=>'publish'
	);
	$the_query = new WP_Query( $query_arg );

	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		$posts_arr[get_the_ID()] = get_the_title();
	endwhile;

	wp_reset_postdata();
?>

<div class="wrap" id="cpike">

	<div class="wrap" id="cpike">
	<h1><?php esc_html_e('List of Goal Pages', 'cpike-adeffects'); ?></h1>
	<h2><?php esc_html_e('Goal Pages', 'cpike-adeffects'); ?></h2>
	<form action="">
		<?php wp_nonce_field('cpae-delete-cv-nonce-key', 'cpae-delete-cv-nonce'); ?>

		<table>
		<tr><th><?php esc_html_e('Goal Page ID', 'cpike-adeffects'); ?></th><th><?php esc_html_e('title', 'cpike-adeffects'); ?></th><th><?php esc_html_e('URL', 'cpike-adeffects'); ?></th><th><?php esc_html_e('operation', 'cpike-adeffects'); ?></th></tr>
	<?php
		$results = cpae_get_database('cpike_cv_page', 'is_deleted', 1, 'not_equal');
		foreach ($results as $key => $val) {

			$cv_post_type = cpae_get_post_type($val->cv_page_id);
			$query_arg = array(
				$cv_post_type => esc_html($val->cv_page_id),
				'post_status'=>'publish'
			);
			$the_query = new WP_Query( $query_arg );

			while ( $the_query->have_posts() ) :
				$the_query->the_post();
	?>
		<tr>
			<td><?php echo esc_html($val->cv_page_id); ?></td>
			<td><?php echo get_the_title(); ?></td>
			<td><?php the_permalink(); ?></td>
			<td class="center nowrap">
				<button type="submit" name="id" value="<?php echo esc_html($val->id); ?>" class="button button-large" onclick='return confirm("Are you sure you want to delete this record?");'>Delete</button>
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
		<h1><?php esc_html_e('Add Goal Page', 'cpike-adeffects'); ?></h1>
	</div>

	<form id="cpae-submenu-form" action="">
		<?php wp_nonce_field('cpae-nonce-key', 'cpae-cv-nonce'); ?>
		<label for="name"><?php esc_html_e('Goal Page', 'cpike-adeffects'); ?> : </label>
		<select name="cv_page">
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

</div>
