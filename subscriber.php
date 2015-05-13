<?php
/**
* Plugin Name: Subscriber newsletter
* Description: Mailchimp newsletter settings
* Version: 1.0
* Author: Joakim & Mikael
**/

function my_plugin_subscriber_newsletter() {
	add_menu_page(
		'My Plugin Settings', 
		'Subscriber settings', 
		'administrator', 
		'my-subscriber-newsletter-settings-plugin', 
		'my_subscriber_newsletter_settings_page', 
		'dashicons-admin-generic'
	);
}

function my_subscriber_newsletter_settings_page(){

	require_once('src/Drewm/MailChimp.php');

	// Your Mailchimp API Key 
	$api = 'b08e782231147b0c03d797cd924f007d-us9'; 
	
	// Initializing the $MailChimp object
	$MailChimp = new \Drewm\MailChimp($api);
	$lists = $MailChimp->call('lists/list', array(
	  "filters" => array(),
	  "sort_dir" => "DESC"
	));

?>
	<div class="wrap">
	<h2>Subscriber Mailchimp settings</h2>
	 
	<form method="post" action="options.php">

	    <?php settings_fields( 'my-plugin-settings-group' ); ?>
	    <?php do_settings_sections( 'my-plugin-settings-group' ); ?>

	    <h3>Mailchimp options</h3>
	    <div style="margin-bottom:10px;">
		    <label for="subscriber_isactive_checkbox" style="padding-right:5px;">Subscriber Newsletter ON/OFF</label>
		    <input type="checkbox" 
		    	name="subscriber_isactive_checkbox" 
		    	id="subscriber_isactive_checkbox" 
		    	style="position:relative; top:2px"
		    	value="on"
		    	<?php echo get_option('subscriber_isactive_checkbox') == 'on' ? 'checked':''; ?> 
		    /> <br />
		</div>

		<label style="padding-right:5px;">Choose Mailchimp subscriberlist</label>
 		<select name="subscriber_select_list">
			<?php
			$selected = '';
			foreach ($lists['data'] as $list) {
				if($list['id'] == get_option('subscriber_select_list')):
					$selected = ' selected';
				else:
					$selected = '';
				endif;
	    		echo '<option value="'.$list['id'].'"'.$selected.'>'.$list['name'].'</option>';
			}
			?>
	    </select>

	    <hr />
	    <h3>Mailchimp subscription form options</h3>
	    <label for="subscriber_form_heading" style="padding-right:5px;">Subscriber form heading</label>
	    <input type="text" id="subscriber_form_heading" name="subscriber_form_heading" value="<?php echo ( get_option('subscriber_form_heading') ? get_option('subscriber_form_heading') : 'Subscribe to my newsletter'); ?>" />
	    <br />
	    <br />
	    <label for="subscriber_form_delay" style="padding-right:5px;">Subscriber form delay (ms)</label>
	   <input type="text" id="subscriber_form_delay" name="subscriber_form_delay" value="<?php echo (get_option('subscriber_form_delay') ? get_option('subscriber_form_delay') : 5000 ); ?>" />

	    <?php submit_button(); ?>
	 
	</form>
	</div>



<?php 
}

function my_plugin_settings() {
	register_setting( 'my-plugin-settings-group', 'subscriber_isactive_checkbox' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_heading' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_delay' );
}


add_action( 'admin_init', 'my_plugin_settings' );
add_action( 'admin_menu', 'my_plugin_subscriber_newsletter' );

// Ajax call
add_action( 'wp_ajax_ajax_subscribersubmit', 'ajax_subscribersubmit' );
add_action( 'wp_ajax_nopriv_ajax_subscribersubmit', 'ajax_subscribersubmit' );


function ajax_subscribersubmit(){

	// Check Mailchimp current settings
	$currentSubscriberListID = get_option('subscriber_select_list');

	require_once('src/Drewm/MailChimp.php');
	$MailChimp = new \Drewm\MailChimp('b08e782231147b0c03d797cd924f007d-us9');
	
	$result = $MailChimp->call('lists/subscribe', array(
	    'id'                => $currentSubscriberListID,
	    'email'             => array('email'=> $_POST['email']),
	    'merge_vars'        => array('FNAME'=> $_POST['name']),
	    'double_optin'      => false,
	    'update_existing'   => true,
	    'replace_interests' => false,
	    'send_welcome'      => false,
	));

	if( $result['leid'] ){

		// JSON Response
		header('Content-Type: application/json');
		echo json_encode($result);
	} else {
		header('Content-Type: application/json');
		echo json_encode(array('error' => 'true'));
	}

	die();
}

function display_form() {

	add_thickbox(); ?>

	<script>
		function createCookie(name,value,days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			} else { 
				var expires = "";
				document.cookie = name+"="+value+expires+"; path=/";
			}
		}

		function readCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		}

		jQuery(document).ready(function() {

			// Check cookie
			if(readCookie('SubsriberForm-AshleyCooper') || readCookie('AshleyCooper-VisitorSubsribed')) {
				var displayForm = false;
			} else {
				var displayForm = true;
				createCookie('SubsriberForm-AshleyCooper', 'session');
			}

			// Popup timer
			if( displayForm ){
				setTimeout(function() {
					var t 	= 'Subscribe to my newsletter';
					var a 	= "#TB_inline?width=625&amp;height=250&amp;inlineId=newsletter-thickbox";
					var g 	= false;
					tb_show(t, a, g);
				}, <?php echo get_option('subscriber_form_delay'); ?>);
			}

			// Handle form submission
			jQuery('form').submit(function( event ){

				// Prevent submision
				event.preventDefault();

				// Collect vars
				var form 	= jQuery(this);
				var action 	= form.attr('action');
				var name 	= jQuery('input[name=name]', form).val();
				var email 	= jQuery('input[name=email]', form).val();
				var formData = {
					action:  'ajax_subscribersubmit',
					name: 	name,
					email: 	email
				};

				// Validate email
				var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if( !regex.test( email ) ){
					jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
					jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
					return false;
				}

				jQuery.ajax({
					url: action,
					data: formData,
					method: 'post',
					success: function( response ) {
						if( response.leid ){

							// Thank you message
							jQuery('#newsletter-thickbox-content .form').hide();
							jQuery('#newsletter-thickbox-content .thank-you .email-placeholder').text( email );
							jQuery('#newsletter-thickbox-content .thank-you').show();

							// Set permanent cookie
							createCookie('AshleyCooper-VisitorSubsribed', 'set', 3650);
						} else {
							
							// Show error message
							jQuery('#newsletter-thickbox-content .form').hide();
							jQuery('#newsletter-thickbox-content .error').show();
						}
					}
				});
			});
		});
	</script>
	
	<div id="newsletter-thickbox" style="display: none;">
		<style>
			#newsletter-thickbox-content .left {
				float: 		left;
			}
			#newsletter-thickbox-content .text-input-container {
				width: 		238px;
				padding-right: 20px;
			}
			#newsletter-thickbox-content .text-input-container input {
				border:		1px solid #D2D2D2;
				padding: 	8px 2%;
				width: 		100%;
			}
			#TB_ajaxWindowTitle {
				visibility: none;
				display: none;
			}
			#newsletter-thickbox-content .alert {
				border: 			1px solid #99B7DB;
				background-color: 	#C1E0FF;
				padding: 			10px;
			}
			#newsletter-thickbox-content .alert p {
				margin-top: 0;
			}
			#newsletter-thickbox-content .thank-you {
				text-align: center;
			}
		</style>
		<div id="newsletter-thickbox-content">

			<!-- Form -->
			<div class="form">
		 	<h1><?php echo get_option('subscriber_form_heading'); ?></h1>

		 	<!-- Alert box -->
		 	<div class="alert" style="display: none;"><p></p></div>

		 	<form action="/wp-admin/admin-ajax.php" method="post" id="mailchimp-subscribe">
				<input type="hidden" name="register">

				<!-- Name -->
				<div class="left text-input-container">
					<label for="name">Name </label><br>
					<input type="text" name="name" placeholder="Your name" class="text-input">
				</div>

				<!-- Email -->
				<div class="left text-input-container">
					<label for="email">Email</label><br>
					<input type="email" name="email" placeholder="Your email" class="text-input">
				</div>
				<input type="submit" value="Send" id="mailchimp-submit-button" class="left button large button darkgray fusion-button button-flat button-square button-large button-darkgray button-2 buttonshadow-0">
			</form>
		</div>

		 <!-- Thank you note -->
		<div class="thank-you" style="display: none;">
			<h1>Thank you!</h1>
			<p><span class="email-placeholder">[Email]</span> successfuly added to subsriber list.</p>
		</div>

		 <!-- Error note -->
		<div class="error" style="display: none;">
			<h1>Oops!</h1>
			<p>There was a technical error. Try again later.</p>
		</div>
		</div>
	</div>

	<?php
}

function display_footer_form(){ 

	// Register and enqueue Bootstrap
	wp_register_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js', 'jquery');
	wp_enqueue_script( 'bootstrap' );
?>

<script>
	
	//console.log('JS');
	
	//jQuery('[data-toggle="tooltip"]').tooltip('show');
	//jQuery('[data-toggle="tooltip"]').tooltip('toggle');

	jQuery(document).ready(function(){
		//console.log('jQuery');
		
		jQuery('#jm-mailchimp-subscribe form').submit(function( event ){
			
			// Hide form
			
			// Show spinner

			// Prevent submision
			event.preventDefault();

			// Collect vars
			var form 	= jQuery(this);
			var action 	= form.attr('action');
			var name 	= jQuery('input[name=name]', form).val();
			var email 	= jQuery('input[name=email]', form).val();
			var formData = {
				action: 'ajax_subscribersubmit',
				name: name,
				email: email
			};

			// Validate name
			if(name.length < 2){
				//jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
				//jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
				
				// Remove spinner
				
				// Show form
				
				console.log('Please enter your name');
				
				return false;
			}

			// Validate email
			var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if( !regex.test( email ) ){
				//jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
				//jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
				
				//Remove spinner
				
				// Show form
				
				console.log('Please enter a valid email address');
				
				return false;
			}

			// Send form
			jQuery.ajax({
				url: action,
				data: formData,
				method: 'post',
				success: function( response ) {
					if( response.leid ){

						// Remove spinner
						
						// Show thank you note

						console.log('Thank you!');

						// Thank you message
						//jQuery('#newsletter-thickbox-content .form').hide();
						//jQuery('#newsletter-thickbox-content .thank-you .email-placeholder').text( email );
						//jQuery('#newsletter-thickbox-content .thank-you').show();
					} else {

						// Remove spinne
						
						// Show error note

						console.log('Could not send form. Try agian later.');
						
						// Show error message
						//jQuery('#newsletter-thickbox-content .form').hide();
						//jQuery('#newsletter-thickbox-content .error').show();
					}
				}
			});
		});
	});
</script>

<style>
	#jm-mailchimp-subscribe { 
		background-color: 	#82F5D0;
		color: 				#FFF;
		padding: 			20px 0;
		font-family: 		"Antic Slab", Arial, Helvetica, sans-serif;
		position: 			relative;
		top: 				12px;
		z-index: 			1;
	}
	#jm-mailchimp-subscribe h1 {
		font-weight: 	normal;
		margin: 		0;
		line-height: 	37px;
	}
	#jm-mailchimp-subscribe .text-input,
	#jm-mailchimp-subscribe .text-input:focus,
	#jm-mailchimp-subscribe .submit-button,
	#jm-mailchimp-subscribe .submit-button:focus {
		width: 			100%;
		border: 		1px solid #FFF;
		background: 	transparent;
		color: 			#FFF;
		margin: 		0;
		padding: 		10px 15px;
		height: 		37px;
		line-height: 	15px;
		box-shadow: 	none;
		outline: 		none;
	}
	#jm-mailchimp-subscribe .text-input,
	#jm-mailchimp-subscribe .text-input:focus {
		border-right: none;
	}
	#jm-mailchimp-subscribe .submit-button {
		text-transform: uppercase;
		cursor: 		pointer;
	}
	#jm-mailchimp-subscribe .col-sm-5,
	#jm-mailchimp-subscribe .col-sm-2{
		padding: 0;
	}

	/* Placeholder style */
	/*::-webkit-input-placeholder,
	:-moz-placeholder,
	::-moz-placeholder,
	:-ms-input-placeholder {
	   color: #FFF;
	}*/
	::-webkit-input-placeholder {
	   color: #FFF;
	}
</style>

<div id="jm-mailchimp-subscribe">
	<div class="container">
		<div class="row">
		
			<?php // Form ?>
			<form action="/wp-admin/admin-ajax.php" method="post">
				<input type="hidden" name="register">
				
				<?php // Heading ?>
				<div class="col-md-5"><h1><?php echo get_option('subscriber_form_heading'); ?></h1></div>

				<div class="col-md-7">
					<div class="row">

						<?php // Name ?>
						<div class="col-sm-5">
							<input type="text" name="name" placeholder="Name" class="text-input" data-toggle="tooltip" data-placement="top" title="Please enter your name!">
						</div>

						<?php // Email ?>
						<div class="col-sm-5">
							<input type="email" name="email" placeholder="Email" class="text-input">
						</div>

						<?php // Button ?>
						<div class="col-sm-2">
							<input type="submit" value="Send" class="submit-button">
						</div>
					</div>
				</div>
			</form>

			<?php // Thank you note ?>
			<div class="thank-you" style="display: none;">
				<h1>Thank you!</h1>
				<p><span class="email-placeholder">[email]</span> successfuly added to subsriber list.</p>
			</div>

			<?php // Error note ?>
			<div class="error" style="display: none;">
				<h1>Oops!</h1>
				<p>There was a technical error. Try again later.</p>
			</div>
		</div>
	</div>
</div>

<?php
}

// Display form if plugin is activated in settings
if(get_option('subscriber_isactive_checkbox') == 'on'){
	//add_action('wp_footer', 'display_form');
	add_action('wp_footer', 'display_footer_form');
}
?>