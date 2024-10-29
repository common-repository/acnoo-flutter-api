<div>
		<p>The server key firebase is used to push notification when order status changed.</p>
		<p>(Firebase project -> Project Settings -> Cloud Messaging -> Server key)</p>
	</div>

	<form action="" method="post">
		<?php
		$serverKey = get_option( 'maanstore_firebase_server_key' );
		?>
		<div class="form-group" style="margin-top:10px;margin-bottom:40px">
			<textarea class="maanstore-update-firebase-server-key maanstore_input"
					style="height: 120px"><?php echo esc_attr( $serverKey ); ?></textarea>
		</div>
	</form>