<h3>
	<?php _e( 'Welcome to Acnoo Flutter API', 'acnoo-flutter-api' ); ?>
</h3>
<p>
	<?php _e( 'This plugin is intended to be used with other mobile applications created by the Acnoo Team.', 'acnoo-flutter-api' ); ?>
</p>
<p>
	<?php _e( 'To understand how the plugin should work. Follow this', 'acnoo-flutter-api' ); ?>
	<a href="<?php echo esc_url( 'https://maanstoreapi.acnoo.com/documentation/' ); ?>"><?php _e( 'documentation.', 'acnoo-flutter-api' ); ?></a>
</p>

<div class="afa-plugin-features-panel">
	<h2 class="afa-feat-title">
		<?php _e( 'Turn on the APIs you need.', 'acnoo-flutter-api' ); ?>
	</h2>

	<p class="afa-feat-desc">
		<?php _e( 'Enable/Disable the elements anytime you want from Essential Addons Dashboard', 'acnoo-flutter-api' ); ?>
	</p>
</div>

<?php
if ( isset( $_POST['afa_element'] ) && wp_verify_nonce( $_POST['afa_feat_nonce'], 'afa_feat' ) ) {
	$elements = $_POST['afa_element'];
	update_option( 'afa_elements', $elements );
}
?>

<form method="post">
	<div class="afa-feat-body">
		<div class="afa-item-panel">
			<?php
			foreach ( get_element_list() as $element ) :
				$preferences = $checked = '';
				if ( isset( $element['preferences'] ) ) {
					$preferences = $element['preferences'];
					if ( $element['preferences'] == 'basic' ) {
						$checked = 'checked';
					}

					$saved_data = get_option( 'afa_elements' ) ?? array();

					if ( is_array( $saved_data ) ) {
						if ( null != $saved_data && array_key_exists( $element['key'], $saved_data ) ) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
					}
				}
				?>
				<div class="afa-api-item">
					<label class="afa-api-checkbox">
						<h4><?php echo esc_html( $element['title'] ); ?></h4>
						<input data-preferences="<?php echo esc_attr( $preferences ); ?>" type="checkbox"
								class="afa-element" id="<?php echo esc_attr( $element['key'] ); ?>"
								name="afa_element[<?php echo esc_attr( $element['key'] ); ?>]"
							<?php echo esc_attr( $checked ); ?> >
					</label>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="afa-submit-btn">
		<?php wp_nonce_field( 'afa_feat', 'afa_feat_nonce' ); ?>
		<button type="submit" class="button button-primary afa-save-btn">
			<?php esc_html_e( 'Save Changes', 'acnoo-flutter-api' ); ?>
		</button>
	</div>
</form>

<?php
function get_element_list() {
	return array(
		array(
			'key'         => 'media-upload',
			'title'       => __( 'Media Upload API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'vendor-registration-social',
			'title'       => __( 'Vendor registration & Social Login', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'vendor-admin',
			'title'       => __( 'Vendor Admin API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'delivery-api',
			'title'       => __( 'Delivery Man API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'user-api',
			'title'       => __( 'User API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'booking',
			'title'       => __( 'Booking', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'paid-membership-pro',
			'title'       => __( 'Paid Membership Pro', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'blog',
			'title'       => __( 'Blog', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'woo',
			'title'       => __( 'Custom WooCommerce API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
		array(
			'key'         => 'woo_extra',
			'title'       => __( 'WooCommerce Extra Features API', 'acnoo-flutter-api' ),
			'preferences' => 'basic',
		),
	);
}

register_activation_hook( __FILE__, 'afa_activate' );

function afa_activate() {
	$new_array_list = array();

	foreach ( get_element_list() as $element ) :
		$new_array_list[ $element['key'] ] = 'on';
	endforeach;

	update_option( 'afa_elements', $new_array_list );
}
