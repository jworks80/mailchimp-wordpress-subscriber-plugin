<?php
/**
* Plugin Name: Subscriber newsletter
* Description: Mailchimp newsletter settings
* Version: 1.0
* Author: Joakim & Mikael
**/

echo 'Newsletter is active: '.get_option('subscriber_isactive_checkbox');
echo '<br /><br />';
echo 'Selected list id: '.get_option('subscriber_select_list');
function my_plugin_subscriber_newsletter() {
	add_menu_page('My Plugin Settings', 'Subscriber settings', 'administrator', 'my-subscriber-newsletter-settings-plugin', 'my_subscriber_newsletter_settings_page', 'dashicons-admin-generic');
}

function my_subscriber_newsletter_settings_page(){
  //
	// Register the Custom Music Review Post Type

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
		    	<?php echo get_option('subscriber_isactive_checkbox') == 'on' ? 'checked':''; ?> /> <br />
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
	    <input type="text" id="subscriber_form_heading" name="subscriber_form_heading" value="<?php echo get_option('subscriber_form_heading'); ?>" />
	    <br />
	    <br />
	    <label for="subscriber_form_delay" style="padding-right:5px;">Subscriber form delay</label>
	   <input type="text" id="subscriber_form_delay" name="subscriber_form_delay" value="<?php echo get_option('subscriber_form_delay'); ?>" />

	    
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
add_action('admin_menu', 'my_plugin_subscriber_newsletter');

// Ajax call

add_action( 'wp_ajax_ajax-subscribersubmit', 'myajax_subscriber_submit_func' );
add_action( 'wp_ajax_nopriv_ajax-subscribersubmit', 'myajax_subscriber_submit_func' );


function myajax_subscriber_submit_func(){


	// Check Mailchimp current settings
	if(get_option('subscriber_isactive_checkbox') == 'on'){

		$currentSubscriberListID = get_option('subscriber_select_list');

	}
}

function display_form() {

	add_thickbox();

	?>

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
				}, 1000);
			}

			// Handle form submission
			jQuery('form#mailchimp-subscribe').submit(function( event ){

				// Prevent submision
				event.preventDefault();

				// Disable submit button
				jQuery('#mailchimp-submit-button').disable();

				// Collect vars
				var form 	= jQuery(this);
				var action 	= form.attr('action');
				var name 	= jQuery('input[name=name]', form).val();
				var email 	= jQuery('input[name=email]', form).val();
				var formData = {
					'action': 	'myajax_subscriber_submit_func',
					'name': 	name,
					'email': 	email
				};

				// Validate email
				var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if( !regex.test( email ) ){
					jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
					jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
					return false;
				}

				// Make Ajax call
				jQuery.post( action, formData, function( data ){
					if( data.status == 'error' ){

						// Enable submit button
						jQuery('#mailchimp-submit-button').enable();
						
						// Show error message
						jQuery('#newsletter-thickbox-content .form').hide();
						jQuery('#newsletter-thickbox-content .error').show();
					} else {

						// Thank you message
						jQuery('#newsletter-thickbox-content .form').hide();
						jQuery('#newsletter-thickbox-content .thank-you .email-placeholder').text( email );
						jQuery('#newsletter-thickbox-content .thank-you').show();

						// Set permanent cookie
						createCookie('AshleyCooper-VisitorSubsribed', 'set', 3650);
					}
				}, "json");
			});
		});
	</script>
	
	<div id="newsletter-thickbox">
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
		     	<h1>Subscribe to my monthly newsletter!</h1>

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

add_action('wp_footer', 'display_form');

?>
