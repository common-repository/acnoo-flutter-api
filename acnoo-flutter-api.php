<?php
/**
 * Plugin Name: Acnoo Flutter API
 * Plugin URI: https://wordpress.org/plugins/acnoo-flutter-api
 * Description: The Acnoo Flutter API Plugin which is used for REST API configurations of mobile apps created by Acnoo.
 * Version: 1.0.5
 * Author: Acnoo
 * Author URI: https://profiles.wordpress.org/acnoo/
 * Text Domain: acnoo-flutter-api
 */

defined( 'ABSPATH' ) || wp_die( 'Can\'t access directly!' );

// Flutter API.
require plugin_dir_path( __FILE__ ) . 'includes/flutter-api/maan-flutter-config.php';

function my_plugin_activate() {

	$element_list = array(
		'media-upload'               => 'on',
		'vendor-registration-social' => 'on',
		'delivery-api'               => 'on',
		'user-api'                   => 'on',
		'booking'                    => 'on',
		'vendor-admin'               => 'on',
		'user-api'                   => 'on',
		'vendor-registration-social' => 'on',
		'woo'                        => 'on',
		'blog'                       => 'on',
		'paid-membership-pro'        => 'on',
		'woo_extra'                  => 'on',
	);

	update_option( 'afa_elements', $element_list );
}

register_activation_hook( __FILE__, 'my_plugin_activate' );