<?php
/**
 * Upload images using REST API.
 */
class MaanApi {
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {

		// Media upload.
		register_rest_route(
			'maanapi',
			'/media_upload',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'media_upload' ),
				'permission_callback' => array( $this, 'media_permission_callback' ),
			)
		);
	}

	function media_permission_callback() {
		return current_user_can( 'upload_files' );
	}

	public function media_upload() {
		$files = $_FILES;
		return $this->upload_file();
	}

	public function upload_file() {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		// Upload only images and files with the following extensions.
		$file_extension_type = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tiff', 'tif', 'ico', 'zip', 'pdf', 'docx' );
		$file_extension      = strtolower( pathinfo( $_FILES['async-upload']['name'], PATHINFO_EXTENSION ) );

		if ( ! in_array( $file_extension, $file_extension_type ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message'  => __( 'The uploaded file is not a valid file. Please try again.' ),
						'filename' => sanitize_file_name( $_FILES['async-upload']['name'] ),
					),
				)
			);
		}

		$attachment_id = media_handle_upload( 'async-upload', null, array() );

		if ( is_wp_error( $attachment_id ) ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message'  => $attachment_id->get_error_message(),
						'filename' => sanitize_file_name( $_FILES['async-upload']['name'] ),
					),
				)
			);
		}

		if ( isset( $post_data['context'] ) && isset( $post_data['theme'] ) ) {
			if ( 'custom-background' === $post_data['context'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', $post_data['theme'] );
			}

			if ( 'custom-header' === $post_data['context'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', $post_data['theme'] );
			}
		}

		$attachment = wp_prepare_attachment_for_js( $attachment_id );

		if ( ! $attachment ) {
			return wp_send_json(
				array(
					'success' => false,
					'data'    => array(
						'message'  => __( 'Image cannot be uploaded.' ),
						'filename' => sanitize_file_name( $_FILES['async-upload']['name'] ),
					),
				)
			);
		}

		return wp_send_json(
			array(
				'success' => true,
				'data'    => $attachment,
			)
		);
	}
}

$media_uploader = new MaanApi();
$media_uploader->init();
