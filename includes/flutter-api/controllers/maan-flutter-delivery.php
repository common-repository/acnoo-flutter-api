<?php
require_once __DIR__ . '/flutter-base.php';
require_once __DIR__ . '/helpers/delivery-wcfm-helper.php';
require_once __DIR__ . '/helpers/delivery-woo-helper.php';

/*
 * Base REST Controller for flutter
 *
 * @since 1.4.0
 *
 * @package home
*/
class FlutterDelivery extends FlutterBaseController {
	/**
	 * Endpoint namespace
	 *
	 * @var string
	 */
	protected $namespace = 'maan-delivery';

	/**
	 * Register all routes releated with stores
	 *
	 * @return void
	 */
	public function __construct() {
		add_action(
			'rest_api_init',
			array(
				$this,
				'register_flutter_delivery_routes',
			)
		);
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_flutter_delivery_routes() {
		// Get notification
		register_rest_route(
			$this->namespace,
			'/notifications',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array(
						$this,
						'get_notification',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/profile',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_delivery_profile',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/profile',
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array(
						$this,
						'update_delivery_profile',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/orders',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_delivery_orders',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/stores',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_delivery_stores',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/orders/(?P<id>[\d]+)/',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_delivery_order',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/order-update',
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array(
						$this,
						'update_delivery_order',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/stat',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_delivery_stat',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/offtime',
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array(
						$this,
						'set_off_time',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		// Create river note.
		register_rest_route(
			$this->namespace,
			'/driver-note',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array(
						$this,
						'set_driver_note',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		// Get a list of driver notes
		register_rest_route(
			$this->namespace,
			'/driver-notes',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array(
						$this,
						'get_all_drivers_notes',
					),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		// Get pickup location.
		register_rest_route(
			$this->namespace,
			'/order-pickup-location',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_pickup_location' ),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		// Get driver picture
		register_rest_route(
			$this->namespace,
			'/driver-picture',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_driver_picture' ),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);

		// Get driver picture
		register_rest_route(
			$this->namespace,
			'/driver-picture',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set_driver_picture' ),
					'permission_callback' => function () {
						return parent::checkApiPermission();
					},
				),
			)
		);
	}

	function get_delivery_orders( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}

		return $helper->get_delivery_orders( $user_id, $request );
	}

	function get_delivery_stores( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->get_delivery_stores( $user_id, $request );
	}

	function get_delivery_order( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->get_delivery_order( $user_id, $request );
	}

	function get_delivery_stat( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->get_delivery_stat( $user_id );
	}

	function get_notification( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->get_notification( $request, $user_id );
	}

	public function update_delivery_profile( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->update_delivery_profile( $request, $user_id );
	}

	public function update_delivery_order( $request ) {

		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}

		return $helper->update_delivery_order( $request['order_id'], $request['delivery_status'] );
	}

	public function get_delivery_profile( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->get_delivery_profile( $user_id );
	}

	public function set_off_time( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		$helper = new DeliveryWCFMHelper();
		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}
		return $helper->set_off_time( $user_id, sanitize_text_field( $request['is_available'] ) );
	}


	protected function authorize_user( $token ) {
		$token = sanitize_text_field( $token );
		if ( isset( $token ) ) {
			$cookie = $token;
		} else {
			return parent::sendError( 'unauthorized', 'You are not allowed to do this', 401 );
		}

		$user_id = validateCookieLogin( $cookie );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		return apply_filters( 'authorize_user', $user_id, $token );
	}

	public function set_driver_note( $request ) {
		$user_id = $this->authorize_user( sanitize_text_field( $request['token'] ) );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$helper = new DeliveryWCFMHelper();

		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}

		return $helper->set_driver_note( sanitize_text_field( $request['note'] ), sanitize_text_field( $request['order_id'] ) );
	}

	public function get_all_drivers_notes( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$helper = new DeliveryWCFMHelper();

		if ( $request['platform'] == 'woo' || $request['platform'] == 'dokan' ) {
			$helper = new DeliveryWooHelper();
		}

		return $helper->get_all_drivers_notes( sanitize_text_field( $request['order_id'] ) );
	}

	function get_pickup_location( $request ) {
		$token = $request['token'];

		if ( isset( $token ) ) {
			$cookie = sanitize_text_field( $token );
		} else {
			return parent::sendError( 'unauthorized', 'You are not allowed to do this', 401 );
		}
		$user_id = validateCookieLogin( $cookie );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$user_meta         = get_userdata( $user_id );
		$current_user_role = $user_meta->roles;
		$allowed_roles     = array( 'administrator', 'shop_manager', 'driver' );

		if ( ! array_intersect( $current_user_role, $allowed_roles ) ) {
			return parent::sendError( 'unauthorized', 'You are not allowed to do this', 401 );
		}

		$order_id              = sanitize_text_field( $request['order_id'] );
		$order_pickup_location = get_post_meta( $order_id, 'product_pickup_location', true );

		return new WP_REST_Response(
			array(
				'status'   => 'success',
				'response' => $order_pickup_location,
			)
		);
	}

	function get_driver_picture( $request ) {
		$token = $request['token'];

		if ( isset( $token ) ) {
			$cookie = sanitize_text_field( $token );
		} else {
			return parent::sendError( 'unauthorized', 'You are not allowed to do this', 401 );
		}
		$user_id = validateCookieLogin( $cookie );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$pic = get_user_meta( $user_id, 'ddwc_driver_picture', true );

		if ( ! isset( $pic ) || empty( $pic ) || is_null( $pic ) ) {
			return parent::sendError( 'not_found', 'No driver profile picture found', 404 );
		}

		return new WP_REST_Response(
			array(
				'status'   => 'success',
				'response' => $pic,
			)
		);
	}

	function set_driver_picture( $request ) {
		$token = $request['token'];

		if ( isset( $token ) ) {
			$cookie = sanitize_text_field( $token );
		} else {
			return parent::sendError( 'unauthorized', 'You are not allowed to do this', 401 );
		}
		$user_id = validateCookieLogin( $cookie );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}


		if ( isset( $request['url'] ) ) {
			$url        = esc_url_raw( $request['url'] );
			$image_id   = attachment_url_to_postid( $url );
			$image_path = wp_get_original_image_path( $image_id );
			$mime       = wp_get_image_mime( $image_path );
		} else {
			return parent::sendError( 'url_not_found', 'No image url supplied', 404 );
		}

		$pic = array(
			'file' => $image_path,
			'url'  => $url,
			'type' => $mime,
		);

		update_user_meta( $user_id, 'ddwc_driver_picture', $pic );
		$current_picture = get_user_meta( $user_id, 'ddwc_driver_picture', true );

		return new WP_REST_Response(
			array(
				'status'   => 'success',
				'response' => $current_picture,
			)
		);
	}
}

new FlutterDelivery();
