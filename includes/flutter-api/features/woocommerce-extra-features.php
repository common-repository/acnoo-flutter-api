<?php
namespace MaanStoreAPI\Features;

class Woo_Extra_Features {
	public function __construct() {
		$this->init_metaboxes();
	}

	// Save the metabox.
	public function init_metaboxes() {
		add_action( 'add_meta_boxes', array( $this, 'add_product_metabox' ) );
		add_action( 'save_post', array( $this, 'save_woocommerce_meta' ) );
	}

	// Add the metabox.
	public function add_product_metabox() {
		add_meta_box(
			'product_pickup_location',
			'Product pickup Location',
			array( $this, 'create_product_pickup_location' ),
			'shop_order'
		);
	}

	// Display the metabox.
	public function create_product_pickup_location( $post ) {
		$pickup_location = get_post_meta( $post->ID, 'product_pickup_location', true );
		?>
		<label for="product_pickup_location">Type product pickup location.</label><br><br>
		<textarea id="product_pickup_location" class="" name="product_pickup_location" cols="30" rows="10"><?php echo esc_html( $pickup_location ); ?></textarea>
		<?php
	}

	// Save the metabox.
	public function save_woocommerce_meta( $post_id ) {
		if ( array_key_exists( 'product_pickup_location', $_POST ) ) {
			$product_pickup_location = sanitize_text_field( $_POST['product_pickup_location'] );

			update_post_meta(
				$post_id,
				'product_pickup_location',
				$product_pickup_location
			);
		}
	}
}

new Woo_Extra_Features();
