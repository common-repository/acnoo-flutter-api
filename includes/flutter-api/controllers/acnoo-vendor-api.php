<?php
/** Require the JWT library. */
use Tmeister\Firebase\JWT\JWT;
use Tmeister\Firebase\JWT\Key;

use WeDevs\Dokan\Vendor\Vendor;

/**
 * Upload images using REST API.
 */
class Acnoo_Dokan_API {
	public $namespace = 'maanapi';

	private array $supported_algorithms = array( 'HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512', 'ES256', 'ES384', 'ES512', 'PS256', 'PS384', 'PS512' );

	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {

		// Vendor profile registration
		register_rest_route(
			$this->namespace,
			'/vendor_registration',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'vendor_registration' ),
				'permission_callback' => array( $this, 'registration_permission_check' ),
			)
		);

		// Vendor profile update
		register_rest_route(
			$this->namespace,
			'/vendor_update',
			array(
				'methods'             => 'put',
				'callback'            => array( $this, 'vendor_update' ),
				'permission_callback' => array( $this, 'vendor_profile_update_permission_check' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/fb_login',
			array(
				array(
					'methods'             => 'post',
					'callback'            => array( $this, 'fb_social_login' ),
					'permission_callback' => array( $this, 'registration_permission_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/google_login',
			array(
				array(
					'methods'             => 'post',
					'callback'            => array( $this, 'google_social_login' ),
					'permission_callback' => array( $this, 'registration_permission_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/refresh_token',
			array(
				array(
					'methods'             => 'post',
					'callback'            => array( $this, 'refresh_token' ),
					'permission_callback' => array( $this, 'registration_permission_check' ),
				),
			)
		);
	}

	public function registration_permission_check() {
		return true;
	}

	public function vendor_profile_update_permission_check() {
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Register Dokan Vendor
	 *
	 * @param mixed $request
	 * @return void
	 */
	public function vendor_registration( $request ) {
		if ( ! isset( $request['username'] ) || ! isset( $request['email'] ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => __( 'Please fill all the required fields.' ),
					),
				)
			);
		}

		if ( isset( $request['username'] ) && username_exists( $request['username'] ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => __( 'Username already exists. Please try again.' ),
					),
				),
				403
			);
		}

		$password   = sanitize_text_field( $request['password'] ) ?? wp_generate_password( 12, true );
		$first_name = sanitize_text_field( $request['first_name'] ) ?? '';
		$last_name  = sanitize_text_field( $request['last_name'] ) ?? '';

		// Create a new user.
		$userdata = array(
			'user_login' => sanitize_text_field( $request['username'] ),
			'user_pass'  => $password,
			'user_email' => sanitize_email( $request['email'] ),
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => 'seller',
		);

		$user_id = wp_insert_user( $userdata );

		if ( is_wp_error( $user_id ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => $user_id->get_error_message(),
					),
				)
			);
		}

		// Get JWT Token
		$token = $this->generate_token( $request['username'], $password );

		$user_data        = get_userdata( $user_id );
		$user_data->token = $token;

		// Return wp rest response
		return new WP_REST_Response(
			array(
				'status' => 'success',
				'data'   => $user_data,
			),
			200
		);
	}

	/**
	 * Update Dokan Vendor profile data
	 * @param mixed $request
	 */
	public function vendor_update( $request ) {
		$user_id = $request['user_id'];

		if ( ! $user_id ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => __( 'Please fill all the required fields.' ),
					),
				)
			);
		}

		$userdata = get_userdata( $user_id );

		if ( ! $userdata ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => __( 'User not found.' ),
					),
				)
			);
		} else {
			$existing_email     = $user_data->user_email;
			$existing_firstname = $user_data->first_name;
			$existing_lastname  = $user_data->last_name;
		}

		$email = sanitize_email( $request['email'] ) ?? $existing_email;
		$fname = sanitize_text_field( $request['first_name'] ) ?? $existing_firstname;
		$lname = sanitize_text_field( $request['last_name'] ) ?? $existing_lastname;

		// Update user data
		$userdata = array(
			'ID'         => $user_id,
			'user_email' => $email,
			'first_name' => $fname,
			'last_name'  => $lname,
		);

		$user_id = wp_update_user( $userdata );

		if ( is_wp_error( $user_id ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message' => $user_id->get_error_message(),
					),
				)
			);
		}

		// Getting the vendor data
		$vendor = new Vendor( $user_id );

		// Updating vendor data.
		if ( isset( $request['store_name'] ) ) {
			$vendor->set_store_name( sanitize_text_field( $request['store_name'] ) );
		}

		if ( isset( $request['phone'] ) ) {
			$vendor->set_phone( $request['phone'] );
		}

		if ( isset( $request['address'] ) ) {
			$vendor->set_address( $request['address'] );
		}

		if ( isset( $request['banner_id'] ) ) {
			$vendor->set_banner_id( $request['banner_id'] );
		}

		if ( isset( $request['avatar_id'] ) ) {
			$vendor->set_gravatar_id( $request['avatar_id'] );
		}

		$vendor->save();

		// Return wp rest response
		return new WP_REST_Response(
			array(
				'status' => 'success',
				'data'   => $vendor,
			),
			200
		);
	}

	/**
	 * Generate authentication token
	 *
	 * @param string $username
	 * @param string $password
	 * @return void
	 */
	public function generate_token( $username, $password ) {
		$secret_key = defined( 'JWT_AUTH_SECRET_KEY' ) ? JWT_AUTH_SECRET_KEY : false;

		/** First thing, check the secret key if not exist return an error*/
		if ( ! $secret_key ) {
			return new WP_Error(
				'jwt_auth_bad_config',
				__( 'JWT is not configured properly, please contact the admin', 'wp-api-jwt-auth' ),
				array(
					'status' => 403,
				)
			);
		}

		// TODO: In order to generate token, get_userdata function should be used, to get the id, instead of this
		// TODO: authentication process

		/** Try to authenticate the user with the passed credentials*/
		$user = wp_authenticate( $username, $password );

		/** If the authentication fails return an error*/
		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return new WP_Error(
				'[jwt_auth] ' . $error_code,
				$user->get_error_message( $error_code ),
				array(
					'status' => 403,
				)
			);
		}

		/** Valid credentials, the user exists create the according Token */
		$issuedAt  = time();
		$notBefore = apply_filters( 'jwt_auth_not_before', $issuedAt, $issuedAt );
		$expire    = apply_filters( 'jwt_auth_expire', $issuedAt + ( DAY_IN_SECONDS * 30 ), $issuedAt );

		$token = array(
			'iss'  => get_bloginfo( 'url' ),
			'iat'  => $issuedAt,
			'nbf'  => $notBefore,
			'exp'  => $expire,
			'data' => array(
				'user' => array(
					'id' => $user->data->ID,
				),
			),
		);

		/** Let the user modify the token data before the sign. */
		$algorithm = $this->get_algorithm();

		if ( $algorithm === false ) {
			return new WP_Error(
				'jwt_auth_unsupported_algorithm',
				__( 'Algorithm not supported, see https://www.rfc-editor.org/rfc/rfc7518#section-3', 'wp-api-jwt-auth' ),
				array(
					'status' => 403,
				)
			);
		}

		$token = JWT::encode(
			apply_filters( 'jwt_auth_token_before_sign', $token, $user ),
			$secret_key,
			$algorithm
		);

		/** The token is signed, now create the object with no sensible user data to the client*/
		$data = array(
			'token'             => $token,
			'user_email'        => $user->data->user_email,
			'user_nicename'     => $user->data->user_nicename,
			'user_display_name' => $user->data->display_name,
		);

		/** Let the user modify the data before send it back */
		return apply_filters( 'jwt_auth_token_before_dispatch', $data, $user );
	}

	/**
	 * Get algorithm for creating JWT Token
	 *
	 * @return void
	 */
	private function get_algorithm() {
		$algorithm = apply_filters( 'jwt_auth_algorithm', 'HS256' );
		if ( ! in_array( $algorithm, $this->supported_algorithms ) ) {
			return false;
		}

		return $algorithm;
	}

	public function generate_token_from_user_id( $user_id ) {
		$secret_key = defined( 'JWT_AUTH_SECRET_KEY' ) ? JWT_AUTH_SECRET_KEY : false;

		/** First thing, check the secret key if not exist return an error*/
		if ( ! $secret_key ) {
			return new WP_Error(
				'jwt_auth_bad_config',
				__( 'JWT is not configured properly, please contact the admin', 'wp-api-jwt-auth' ),
				array(
					'status' => 403,
				)
			);
		}

		/** Try to authenticate the user with the passed credentials*/
		$user = get_userdata( $user_id );

		/** If the authentication fails return an error*/
		if ( is_wp_error( $user ) ) {
			$error_code = $user->get_error_code();

			return new WP_Error(
				'[jwt_auth] ' . $error_code,
				$user->get_error_message( $error_code ),
				array(
					'status' => 403,
				)
			);
		}

		/** Valid credentials, the user exists create the according Token */
		$issuedAt  = time();
		$notBefore = apply_filters( 'jwt_auth_not_before', $issuedAt, $issuedAt );
		$expire    = apply_filters( 'jwt_auth_expire', $issuedAt + ( DAY_IN_SECONDS * 7 ), $issuedAt );

		$token = array(
			'iss'  => get_bloginfo( 'url' ),
			'iat'  => $issuedAt,
			'nbf'  => $notBefore,
			'exp'  => $expire,
			'data' => array(
				'user' => array(
					'id' => $user->data->ID,
				),
			),
		);

		/** Let the user modify the token data before the sign. */
		$algorithm = $this->get_algorithm();

		if ( $algorithm === false ) {
			return new WP_Error(
				'jwt_auth_unsupported_algorithm',
				__( 'Algorithm not supported, see https://www.rfc-editor.org/rfc/rfc7518#section-3', 'wp-api-jwt-auth' ),
				array(
					'status' => 403,
				)
			);
		}

		$token = JWT::encode(
			apply_filters( 'jwt_auth_token_before_sign', $token, $user ),
			$secret_key,
			$algorithm
		);

		/** The token is signed, now create the object with no sensible user data to the client*/
		$data = array(
			'token'             => $token,
			'user_email'        => $user->data->user_email,
			'user_nicename'     => $user->data->user_nicename,
			'user_display_name' => $user->data->display_name,
		);

		/** Let the user modify the data before send it back */
		return apply_filters( 'jwt_auth_token_before_dispatch', $data, $user );
	}


	private function get_shipping_address( $userId ) {
		$shipping = array();

		$shipping['first_name'] = get_user_meta( $userId, 'shipping_first_name', true );
		$shipping['last_name']  = get_user_meta( $userId, 'shipping_last_name', true );
		$shipping['company']    = get_user_meta( $userId, 'shipping_company', true );
		$shipping['address_1']  = get_user_meta( $userId, 'shipping_address_1', true );
		$shipping['address_2']  = get_user_meta( $userId, 'shipping_address_2', true );
		$shipping['city']       = get_user_meta( $userId, 'shipping_city', true );
		$shipping['state']      = get_user_meta( $userId, 'shipping_state', true );
		$shipping['postcode']   = get_user_meta( $userId, 'shipping_postcode', true );
		$shipping['country']    = get_user_meta( $userId, 'shipping_country', true );
		$shipping['email']      = get_user_meta( $userId, 'shipping_email', true );
		$shipping['phone']      = get_user_meta( $userId, 'shipping_phone', true );

		if ( empty( $shipping['first_name'] ) && empty( $shipping['last_name'] ) && empty( $shipping['company'] ) && empty( $shipping['address_1'] ) && empty( $shipping['address_2'] ) && empty( $shipping['city'] ) && empty( $shipping['state'] ) && empty( $shipping['postcode'] ) && empty( $shipping['country'] ) && empty( $shipping['email'] ) && empty( $shipping['phone'] ) ) {
			return null;
		}
		return $shipping;
	}

	private function get_billing_address( $userId ) {
		$billing = array();

		$billing['first_name'] = get_user_meta( $userId, 'billing_first_name', true );
		$billing['last_name']  = get_user_meta( $userId, 'billing_last_name', true );
		$billing['company']    = get_user_meta( $userId, 'billing_company', true );
		$billing['address_1']  = get_user_meta( $userId, 'billing_address_1', true );
		$billing['address_2']  = get_user_meta( $userId, 'billing_address_2', true );
		$billing['city']       = get_user_meta( $userId, 'billing_city', true );
		$billing['state']      = get_user_meta( $userId, 'billing_state', true );
		$billing['postcode']   = get_user_meta( $userId, 'billing_postcode', true );
		$billing['country']    = get_user_meta( $userId, 'billing_country', true );
		$billing['email']      = get_user_meta( $userId, 'billing_email', true );
		$billing['phone']      = get_user_meta( $userId, 'billing_phone', true );

		if ( empty( $billing['first_name'] ) && empty( $billing['last_name'] ) && empty( $billing['company'] ) && empty( $billing['address_1'] ) && empty( $billing['address_2'] ) && empty( $billing['city'] ) && empty( $billing['state'] ) && empty( $billing['postcode'] ) && empty( $billing['country'] ) && empty( $billing['email'] ) && empty( $billing['phone'] ) ) {
			return null;
		}

		return $billing;
	}

	function getResponseUserInfo( $user ) {
		$shipping = $this->get_shipping_address( $user->ID );
		$billing  = $this->get_billing_address( $user->ID );
		$avatar   = get_user_meta( $user->ID, 'user_avatar', true );
		if ( ! isset( $avatar ) || $avatar == '' || is_bool( $avatar ) ) {
			$avatar = get_avatar_url( $user->ID );
		} else {
			$avatar = $avatar[0];
		}
		$is_driver_available = false;
		if ( is_plugin_active( 'delivery-drivers-for-woocommerce/delivery-drivers-for-woocommerce.php' ) ) {
			$is_driver_available = get_user_meta( $user->ID, 'ddwc_driver_availability', true );
		} else {
			$is_driver_available = in_array( 'administrator', $user->roles ) || in_array( 'wcfm_delivery_boy', $user->roles );
		}
		return array(
			'id'                   => $user->ID,
			'username'             => $user->user_login,
			'nicename'             => $user->user_nicename,
			'email'                => $user->user_email,
			'url'                  => $user->user_url,
			'registered'           => $user->user_registered,
			'displayname'          => $user->display_name,
			'firstname'            => $user->user_firstname,
			'lastname'             => $user->last_name,
			'nickname'             => $user->nickname,
			'description'          => $user->user_description,
			'capabilities'         => $user->wp_capabilities,
			'role'                 => $user->roles,
			'shipping'             => $shipping,
			'billing'              => $billing,
			'avatar'               => $avatar,
			'is_driver_available'  => $is_driver_available,
			'dokan_enable_selling' => $user->dokan_enable_selling,
		);
	}

	function create_social_account( $email, $name, $firstName, $lastName, $userName, $role ) {
		$email_exists = email_exists( $email );
		if ( $email_exists ) {
			$user    = get_user_by( 'email', $email );
			$user_id = $user->ID;
		} else {
			$i = 0;
			while ( username_exists( $userName ) ) {
				++$i;
				$userName = strtolower( $userName ) . '.' . $i;
			}
			$password = wp_generate_password( $length = 12 );
			$userdata = array(
				'user_login'   => $userName,
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $name,
				'first_name'   => $firstName,
				'last_name'    => $lastName,
				'role'         => $role,
			);
			$user_id  = wp_insert_user( $userdata );
		}

		$user  = get_userdata( $user_id );
		$token = $this->generate_token_from_user_id( $user_id );

		$response['wp_user_id'] = $user_id;
		$response['token']      = $token;
		$response['user_login'] = $user->user_login;
		$response['user']       = $this->getResponseUserInfo( $user );

		return $response;
	}

	public function fb_social_login( $request ) {
		$fields        = 'id,name,first_name,last_name,email';
		$enable_ssl    = true;
		$access_token  = $request['access_token'];
		$role          = $request['role'];
		$allowed_roles = array( 'seller', 'customer' );

		if ( ! in_array( $role, $allowed_roles ) ) {
			return new WP_Error( 'invalid_request', 'Role must be provided', array( 'status' => 400 ) );
		}

		if ( ! isset( $access_token ) ) {
			// return new error with code
			return new WP_Error( 'invalid_request', 'Access token must be provided', array( 'status' => 400 ) );
		}

		$url = 'https://graph.facebook.com/me/?fields=' . $fields . '&access_token=' . $access_token;

		$result = wp_remote_retrieve_body( wp_remote_get( $url ) );

		$result = json_decode( $result, true );

		if ( isset( $result['email'] ) ) {
			$user_name = strtolower( $result['first_name'] . '.' . $result['last_name'] );
			return $this->create_social_account( $result['email'], $result['name'], $result['first_name'], $result['last_name'], $user_name, $role );
		} else {
			return new WP_Error( 'invalid_login', "Your 'access_token' did not return email of the user. Without 'email' user can't be logged in or registered. Get user email extended permission while joining the Facebook app.", 400 );
		}
	}

	public function google_social_login( $request ) {
		$access_token = $request['access_token'];

		if ( ! isset( $access_token ) ) {
			return new WP_Error( 'invalid_request', 'Access token must be provided', array( 'status' => 400 ) );
		}

		$url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $access_token;

		$result = wp_remote_retrieve_body( wp_remote_get( $url ) );

		$result = json_decode( $result, true );

		$role          = $request['role'];
		$allowed_roles = array( 'seller', 'customer' );

		if ( ! in_array( $role, $allowed_roles ) ) {
			return new WP_Error( 'invalid_request', 'Role must be provided', array( 'status' => 400 ) );
		}

		if ( isset( $result['email'] ) ) {
			$firstName    = $result['given_name'];
			$lastName     = $result['family_name'];
			$email        = $result['email'];
			$display_name = $firstName . ' ' . $lastName;
			$user_name    = $firstName . '.' . $lastName;
			return $this->create_social_account( $email, $display_name, $firstName, $lastName, $user_name, $role );
		} else {
			return new WP_Error( 'invalid_login', "Your 'token' did not return email of the user. Without 'email' user can't be logged in or registered. Get user email extended permission while joining the Google app.", 400 );
		}
	}

	public function refresh_token( $request ) {
		$user_id = $this->get_user_from_token( $request );

		if( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$token   = $this->generate_token_from_user_id( $user_id );

		return new WP_REST_Response(
			array(
				'status' => 'success',
				'data'   => $token,
			),
			200
		);
	}

	private function get_user_from_token( WP_REST_Request $request ) {

		$auth_header = $request->get_header( 'Authorization' );

		if ( ! $auth_header ) {
			return new WP_Error(
				'jwt_auth_no_auth_header',
				'Authorization header not found.',
				array(
					'status' => 403,
				)
			);
		}

		/*
		 * Extract the authorization header.
		 */
		[ $token ] = sscanf( $auth_header, 'Bearer %s' );

		/**
		 * if the format is not valid return an error.
		 */
		if ( ! $token ) {
			return new WP_Error(
				'jwt_auth_bad_auth_header',
				'Authorization header malformed.',
				array(
					'status' => 403,
				)
			);
		}

		/** Get the Secret Key */
		$secret_key = defined( 'JWT_AUTH_SECRET_KEY' ) ? JWT_AUTH_SECRET_KEY : false;
		if ( ! $secret_key ) {
			return new WP_Error(
				'jwt_auth_bad_config',
				'JWT is not configured properly, please contact the admin',
				array(
					'status' => 403,
				)
			);
		}

		/** Try to decode the token */
		try {
			$algorithm = $this->get_algorithm();
			if ( $algorithm === false ) {
				return new WP_Error(
					'jwt_auth_unsupported_algorithm',
					__( 'Algorithm not supported, see https://www.rfc-editor.org/rfc/rfc7518#section-3', 'wp-api-jwt-auth' ),
					array(
						'status' => 403,
					)
				);
			}

			$token = JWT::decode( $token, new Key( $secret_key, $algorithm ) );

			/** The Token is decoded now validate the iss */
			if ( $token->iss !== get_bloginfo( 'url' ) ) {
				/** The iss do not match, return error */
				return new WP_Error(
					'jwt_auth_bad_iss',
					'The iss do not match with this server',
					array(
						'status' => 403,
					)
				);
			}

			/** So far so good, validate the user id in the token */
			if ( ! isset( $token->data->user->id ) ) {
				/** No user id in the token, abort!! */
				return new WP_Error(
					'jwt_auth_bad_request',
					'User ID not found in the token',
					array(
						'status' => 403,
					)
				);
			}

			// Return the user id
			if( $token){
				return $token->data->user->id;
			}

		} catch ( Exception $e ) {
			/** Something were wrong trying to decode the token, send back the error */
			return new WP_Error(
				'jwt_auth_invalid_token',
				$e->getMessage(),
				array(
					'status' => 403,
				)
			);
		}
	}
}

$vendor_registration = new Acnoo_Dokan_API();
$vendor_registration->init();
