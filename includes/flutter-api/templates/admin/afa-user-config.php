<p>You can use the generated token for user based API actions. E.g - viewing orders, updating information etc.</p>
<form action="" method="post">
		<?php
		if ( isset( $_POST['but_generate'] ) ) {
			$user   = wp_get_current_user();
			$cookie = generateCookieByUserId( $user->ID );
			?>
				<div class="form-group" style="margin-top:10px;margin-bottom:10px">
					<textarea class="maanstore_input" style="height: 150px"><?php echo esc_attr( $cookie ); ?></textarea>
				</div>
				<?php
		}
		?>
		<button type="submit" class="maanstore_button" name='but_generate'>Generate Token</button>
</form>