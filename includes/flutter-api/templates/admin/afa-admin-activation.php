
<div class="afa-verification">
<div class="afa-card">
	<p>Thank you for installing Acnoo Flutter API plugin. Please enter your license key to activate the plugin.</p>

<?php
if ( isset( $_POST['afa_license'] ) ) {
	$afa_license = sanitize_text_field( $_POST['afa_license'] );
	update_option( 'acnoo_themeforet_license', $afa_license );

	// TODO: Send the license using wp_remote_post function
	$url      = 'https://maanstoreapi.acnoo.com/verify-purchase/verify-afa-license';
	$response = wp_remote_post(
		$url,
		array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'pc'       => $afa_license,
				'site_url' => get_home_url(),
			),
			'cookies'     => array(),
		)
	);

	$response_body       = wp_remote_retrieve_body( $response );
	$response_body_array = json_decode( $response_body, true );

	update_option( 'afa_verify_response', $response_body_array );
}

$license_key = get_option( 'acnoo_themeforet_license' );
if ( ! is_null( $license_key ) ) {
	$substr             = substr( $license_key, 0, 12 );
	$show_purchase_code = $substr . '-******';
}
?>

<form action="" class="afa-verification-form" method="post">
	<input type="text" name="afa_license" id="afa_license_text" placeholder="Your purchase code here" value="<?php echo esc_html( $show_purchase_code ); ?>">
	<input type="submit" value="Activate" name="afa_license_submit" id="afa_license_submit">
</form>
</div>

<div class="afa-card">
	<h3> What is purchase code, and why do I need it? </h3>
	<p>A purchase code is a unique identifier. It is a gibberish looking series of text, which is understood by our system. It is used to verify the purchase from Envato marketplace.</p>

	<p>One purchase code is used for one website only.</p>

	<p>You must verify your purchase and activate this plugin, in order to use it.</p>
</div>

<div class="afa-card">
	<h3>How can I get my purchase code?</h3>

	<ol>
		<li>Log into your Envato Market account.</li> 
		<li>Hover the mouse over your username at the top of the screen.</li> 
		<li>Click ‘Downloads’ from the drop-down menu.</li> 
		<li> Click ‘License certificate & purchase code’ (available as PDF or text file).</li> 
	</ol>
</div>

<p>If you need a detailed information about it, you can follow the tutorial <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code">Here</a></p>
</div>