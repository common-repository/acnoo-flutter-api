<?php
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'functions/index.php';

//Get the active tab from the $_GET param
$tab = $_GET['tab'] ?? 'general';
?>

<div class="wrap afa-wrap">
		<form method="post" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper afa-nav-tab-wrapper">
			<a href="<?php echo admin_url( 'admin.php?page=acnoo-flutter-api&tab=general' ); ?>" class="nav-tab 
								<?php
								if ( $tab === 'general' ) :
									?>
									nav-tab-active<?php endif; ?>">General</a>
			<a href="<?php echo admin_url( 'admin.php?page=acnoo-flutter-api&tab=firebase' ); ?>" class="nav-tab 
								<?php
								if ( $tab === 'firebase' ) :
									?>
				nav-tab-active<?php endif; ?>">Firebase Config</a>

			<a href="<?php echo admin_url( 'admin.php?page=acnoo-flutter-api&tab=user' ); ?>" class="nav-tab 
						<?php
						if ( $tab === 'user' ) :
							?>
			nav-tab-active<?php endif; ?>">User Information</a>

			<a href="<?php echo admin_url( 'admin.php?page=acnoo-flutter-api&tab=order' ); ?>" class="nav-tab 
								<?php
								if ( $tab === 'order' ) :
									?>
				 nav-tab-active<?php endif; ?>">Order Messages</a>
		</nav> 
</div>

<?php
switch ( $tab ) {
	case 'general':
		echo load_template( dirname( __FILE__ ) . '/afa-general-config.php' );
		break;
	case 'firebase':
		echo load_template( dirname( __FILE__ ) . '/afa-firebase-config.php' );
		break;
	case 'user':
		echo load_template( dirname( __FILE__ ) . '/afa-user-config.php' );
		break;
	case 'order':
		echo load_template( dirname( __FILE__ ) . '/afa-order-message-config.php' );
		break;
}
?>

<div id="success_message"></div>