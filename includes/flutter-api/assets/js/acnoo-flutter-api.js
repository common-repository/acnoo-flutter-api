jQuery(document).ready(
	function ($) {
		$(document).on(
			'click',
			'.maanstore-delete-json-file',
			function () {
				var id = $(this).data('id');
				var nonce = $(this).data('nonce');
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_delete_json_file',
							id: id,
							nonce: nonce
						},
						success: function (result) {
							if (result == 'success') {
								location.reload();
							}
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-limit-product',
			function () {
				var limit = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_limit_product',
							limit: limit
						},
						success: function (result) {
							if (result == 'success') {
							}
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-firebase-server-key',
			function () {
				var serverKey = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_firebase_server_key',
							serverKey: serverKey
						},
						success: function (result) {
							$('#success_message').fadeIn();
							$('#success_message').html('Successfully Saved');

							setTimeout(function () {
								$('#success_message').fadeOut("Slow");
							}, 2500);
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-new-order-title',
			function () {
				var title = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_new_order_title',
							title: title
						},
						success: function (result) {
							$('#success_message').fadeIn();
							$('#success_message').html('Successfully Saved');

							setTimeout(function () {
								$('#success_message').fadeOut("Slow");
							}, 2500);
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-new-order-message',
			function () {
				var message = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_new_order_message',
							message: message
						},
						success: function (result) {
							$('#success_message').fadeIn();
							$('#success_message').html('Successfully Saved');

							setTimeout(function () {
								$('#success_message').fadeOut("Slow");
							}, 2500);
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-status-order-title',
			function () {
				var title = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_status_order_title',
							title: title
						},
						success: function (result) {
							$('#success_message').fadeIn();
							$('#success_message').html('Successfully Saved');

							setTimeout(function () {
								$('#success_message').fadeOut("Slow");
							}, 2500);
						}
					}
				)
				return false;
			}
		)

		$(document).on(
			'blur',
			'.maanstore-update-status-order-message',
			function () {
				var message = $(this).val();
				$.ajax(
					{
						type: 'post',
						url: MyAjax.ajaxurl,
						data: {
							action: 'maanstore_update_status_order_message',
							message: message
						},
						success: function (result) {
							$('#success_message').fadeIn();
							$('#success_message').html('Successfully Saved');

							setTimeout(function () {
								$('#success_message').fadeOut("Slow");
							}, 2500);
						}
					}
				)
				return false;
			}
		)
	}
)