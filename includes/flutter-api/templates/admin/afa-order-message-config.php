<div class="thanks">
	<h3>New Order Message</h3>
</div>

<form action="" method="post">
	<?php
	$newOrderTitle = get_option( 'maanstore_new_order_title' );

	if ( ! isset( $newOrderTitle ) || $newOrderTitle == false ) {
		$newOrderTitle = 'new Order';
	}

	$newOrderMsg = get_option( 'maanstore_new_order_message' );

	if ( ! isset( $newOrderMsg ) || $newOrderMsg == false ) {
		$newOrderMsg = 'Hi() {{name}}, Congratulations, you have received a new order() ! ';
	}
	?>
	<div class="form-group" style="margin-top:10px;">
		<input type="text" placeholder="Title" value="<?php echo esc_attr( $newOrderTitle ); ?>"
				class="maanstore-update-new-order-title maanstore_input">
	</div>
	<div class="form-group" style="margin-top:10px;margin-bottom:40px">
		<textarea placeholder="Message" class="maanstore-update-new-order-message maanstore_input"
				style="height: 120px"><?php echo esc_attr( $newOrderMsg ); ?></textarea>
	</div>
</form>

<div class="thanks">
	<h3>Order Status Changed Message</h3>
</div>

<form action="" method="post">
	<?php
	$statusOrderTitle = get_option( 'maanstore_status_order_title' );

	if ( ! isset( $statusOrderTitle ) || $statusOrderTitle == false ) {
		$statusOrderTitle = 'Order Status Changed';
	}

	$statusOrderMsg = get_option( 'maanstore_status_order_message' );

	if ( ! isset( $statusOrderMsg ) || $statusOrderMsg == false ) {
		$statusOrderMsg = 'Hi {{name}}, Your order: #{{orderId}} changed from {{prevStatus}} to {{nextStatus}}';
	}

	?>
	<div class="form-group" style="margin-top:10px;">
		<input type="text" placeholder="Title" value="<?php echo esc_attr( $statusOrderTitle ); ?>"
			class="maanstore-update-status-order-title maanstore_input">
	</div>
	<div class="form-group" style="margin-top:10px;margin-bottom:40px">
		<textarea placeholder="Message" class="maanstore-update-status-order-message maanstore_input"
				style="height: 120px"><?php echo esc_attr( $statusOrderMsg ); ?></textarea>
	</div>
</form>