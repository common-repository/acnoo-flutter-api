<?php
class CUSTOM_WC_REST_Customers_Controller extends WC_REST_Customers_Controller {

	/**
	 * Endpoint namespace
	 *
	 * @var string
	 */
	protected $namespace = 'maan-customer';

	/**
	 * Register all routes related with stores
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_flutter_woo_routes' ) );
	}

	public function register_flutter_woo_routes() {

		// Delete account.
		register_rest_route(
			$this->namespace,
			'/delete_account',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_account' ),
					'permission_callback' => array( $this, 'custom_delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// add customer note.
		register_rest_route(
			$this->namespace,
			'/customer-note',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'add_customer_note' ),
					'permission_callback' => array( $this, 'customer_adding_note_permission_check' ),
				),
			)
		);

		// add customer note.
		register_rest_route(
			$this->namespace,
			'/order-notes',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_all_order_notes' ),
					'permission_callback' => array( $this, 'customer_adding_note_permission_check' ),
				),
			)
		);
	}

	/**
	 * Check if customer has the permission to delete the account
	 *
	 * @param mixed $request
	 *
	 * @return boolean
	 */
	public function custom_delete_item_permissions_check( $request ) {
		$cookie = $request->get_header( 'User-Cookie' );
		if ( isset( $cookie ) && $cookie != null ) {
			$user_id = validateCookieLogin( $cookie );
			if ( is_wp_error( $user_id ) ) {
				return false;
			}
			$request['force'] = true;
			$request['id']    = $user_id;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Delete account by customer
	 *
	 * @param mixed $request
	 */
	function delete_account( $request ) {
		if ( checkWhiteListAccounts( $request['id'] ) ) {
			return new WP_Error( 'invalid_account', "This account can't delete", array( 'status' => 400 ) );
		} else {
			return $this->delete_item( $request );
		}
	}

	/**
	 * Add customer note
	 *
	 * @param mixed $request
	 */
	public function add_customer_note( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$order_id = sanitize_text_field( $request['order_id'] );

		$current_order = wc_get_order( $order_id );
		$customer_note = 'Customer Note: ' . sanitize_text_field( $request['note'] );

		$data        = $current_order->get_data(); // The Order data.
		$customer_id = sanitize_text_field( $data['customer_id'] );

		if ( $customer_id != $user_id ) {
			return new WP_Error( 'invalid_customer_id', 'Not a valid customer', array( 'status' => 401 ) );
		}

		if ( $current_order ) {
			$order_note_id = $current_order->add_order_note( $customer_note );

			$current_order->save();

			return new WP_REST_Response(
				array(
					'status'   => 'success',
					'response' => array(
						'note_id' => $order_note_id,
					),
				),
			);
		}

		return new WP_REST_Response(
			array(
				'status'   => 'Order not found . ',
				'response' => '',
			),
			400
		);
	}

	public function get_all_order_notes( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$order_id      = sanitize_text_field( $request['order_id'] );
		$current_order = wc_get_order( $order_id );

		$data        = $current_order->get_data(); // The Order data.
		$customer_id = sanitize_text_field( $data['customer_id'] );

		if ( $customer_id != $user_id ) {
			return new WP_Error( 'invalid_customer_id', 'Not a valid customer', array( 'status' => 401 ) );
		}

		if ( $current_order ) {
			$args = array(
				'post_id' => $order_id,
				'orderby' => 'comment_ID',
				'order'   => 'DESC',
				'approve' => 'approve',
				'type'    => 'order_note',
			);
			
			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
			
			$notes = get_comments( $args );
			
			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			return new WP_REST_Response(
				array(
					'status'   => 'success',
					'response' => $notes,
				),
				200
			);
		}

		return new WP_REST_Response(
			array(
				'status'   => 'Order not found . ',
				'response' => '',
			),
			400
		);
	}

	/**
	 * Check if customer has the permission to add note
	 *
	 * @param mixed $request
	 */
	public function customer_adding_note_permission_check( $request ) {
		$user_id = $this->authorize_user( $request['token'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		if ( wc_user_has_role( $user_id, 'customer' ) ) {
			return true;
		} else {
			return false;
		}
	}

	protected function authorize_user( $token ) {
		$token = sanitize_text_field( $token );
		if ( isset( $token ) ) {
			$cookie = $token;
		} else {
			return new WP_Error( 'unauthorized', 'You are not allowed to do this', array( 'status' => 401 ) );
		}

		$user_id = validateCookieLogin( $cookie );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		return apply_filters( 'authorize_user', $user_id, $token );
	}
}

new CUSTOM_WC_REST_Customers_Controller();
