<?php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'functions/index.php'; ?>

<div id="maanstore-api-settings-container">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1> <br/>
	<?php
	// if ( get_option( 'acnoo_themeforet_activation' ) ) {
	// 	echo load_template( dirname( __FILE__ ) . '/admin/afa-admin-navigation.php' );
	// } else {
	// 	echo load_template( dirname( __FILE__ ) . '/admin/afa-admin-activation.php' );
	// }

	echo load_template( dirname( __FILE__ ) . '/admin/afa-admin-navigation.php' );

	?>
</div>
