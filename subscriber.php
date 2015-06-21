<?php
/**
* Plugin Name: Subscriber newsletter
* Description: Mailchimp newsletter settings
* Version: 1.0
* Author: Joakim & Mikael
**/


/* --------------------------------

Plugin settings page

---------------------------------- */

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
	)); ?>
	
	<div class="wrap">
		
		<div class="header">
			<h2>Subscriber Mailchimp settings</h2>
		</div>

		<div class="content">
			<form method="post" action="options.php">

		    	<?php settings_fields( 'my-plugin-settings-group' ); ?>
		    	<?php do_settings_sections( 'my-plugin-settings-group' ); ?>

			    <h3>Options</h3>

			    <p>Shortcode: [subscriber_shortcode]</p>
				
				<?php /* Form heading */ ?>
		    	<p>
		    		<label for="subscriber_form_heading" style="padding-right:5px;">Subscriber form heading</label>
		    		<input type="text" id="subscriber_form_heading" name="subscriber_form_heading" value="<?php echo ( get_option('subscriber_form_heading') ? get_option('subscriber_form_heading') : '' ); ?>" style="width: 300px;" />
		    	</p>
				
				<?php // Background color ?>
		    	<p>
		    		<label for="subscriber_background_color">Background color: </label>
		    		<input type="text" id="subscriper_background_color" name="subscriber_background_color" style="width: 150px;" value="<?php echo ( get_option('subscriber_background_color') ? get_option('subscriber_background_color') : '' ); ?>" />
		    	</p>
				
				<?php // Background image ?>
		    	<p>
		    		<label for="subscriber_side_image">Side image: </label>
		    		<input type="text" id="subscriper_side_image" name="subscriber_side_image" style="width: 300px;" value="<?php echo ( get_option('subscriber_side_image') ? get_option('subscriber_side_image') : '' ); ?>" />
		    	</p>
			    
			    <?php // Enable footer form checkbox ?>
			    <p>
				    <label for="subscriber_isactive_checkbox" style="padding-right:5px;">Enable footer signup</label>
				    <input type="checkbox" 
				    	name="subscriber_isactive_checkbox" 
				    	id="subscriber_isactive_checkbox" 
				    	style="position:relative; top:2px"
				    	value="on"
				    	<?php echo get_option('subscriber_isactive_checkbox') == 'on' ? 'checked':''; ?> 
				    />
				</p>
			    
			    <?php // Enable popup form checkbox ?>
			    <?php /*<p>
				    <label for="subscriber_popup_isactive_checkbox" style="padding-right:5px;">Enable popup signup</label>
				    <input type="checkbox" 
				    	name="subscriber_popup_isactive_checkbox" 
				    	id="subscriber_popup_isactive_checkbox" 
				    	style="position:relative; top:2px"
				    	value="on"
				    	<?php echo get_option('subscriber_popup_isactive_checkbox') == 'on' ? 'checked':''; ?> 
				    />
				</p>

				<hr>*/ ?>

		    	<hr>
				
				<?php // Signup form #1 ?>
				<p>
					<label style="padding-right:5px;" for="subscriber_select_list_1">Choose Mailchimp subscriberlist #1</label>
			 		<select name="subscriber_select_list_1" id="subscriber_select_list_1">
			 			<option value="">- None -</option>
						<?php
							$selected = '';
							foreach ($lists['data'] as $list) {
								if($list['id'] == get_option('subscriber_select_list_1')):
									$selected = ' selected';
								else:
									$selected = '';
								endif;
					    		echo '<option value="'.$list['id'].'"'.$selected.'>'.$list['name'].'</option>';
							}
						?>
				    </select>
				    <i>If only this list is selected, no checkboxes will show in signup form.</i>
			    <p>
				
				<?php // Signup form #2 ?>
				<p>
					<label style="padding-right:5px;" for="subscriber_select_list_2">Choose Mailchimp subscriberlist #2</label>
					<select name="subscriber_select_list_2" id="subscriber_select_list_2">
						<option value="">- None -</option>
						<?php
							$selected = '';
							foreach ($lists['data'] as $list) {
								if($list['id'] == get_option('subscriber_select_list_2')):
									$selected = ' selected';
								else:
									$selected = '';
								endif;
					    		echo '<option value="'.$list['id'].'"'.$selected.'>'.$list['name'].'</option>';
							}
						?>
				    </select>
				</p>
				
				<?php // Signup form #3 ?>
				<p>
					<label style="padding-right:5px;" for="subscriber_select_list_3">Choose Mailchimp subscriberlist #3</label>
					<select name="subscriber_select_list_3" id="subscriber_select_list_3">
						<option value="">- None -</option>
						<?php
							$selected = '';
							foreach ($lists['data'] as $list) {
								if($list['id'] == get_option('subscriber_select_list_3')):
									$selected = ' selected';
								else:
									$selected = '';
								endif;
					    		echo '<option value="'.$list['id'].'"'.$selected.'>'.$list['name'].'</option>';
							}
						?>
				    </select>
				</p>

				<hr>
		    	
		    	<?php // Popup delay ?>
		    	<?php /*<label for="subscriber_form_delay" style="padding-right:5px;">Subscriber form delay (ms)</label>
		   		<input type="text" id="subscriber_form_delay" name="subscriber_form_delay" value="<?php echo (get_option('subscriber_form_delay') ? get_option('subscriber_form_delay') : 5000 ); ?>" style="width: 75px;" />
		   	
		   		<hr>*/ ?>

		    	<?php submit_button(); ?>
		 
			</form>
		</div>
	</div>

<?php 
}

function my_plugin_settings() {
	register_setting( 'my-plugin-settings-group', 'subscriber_isactive_checkbox' );
	register_setting( 'my-plugin-settings-group', 'subscriber_background_color' );
	register_setting( 'my-plugin-settings-group', 'subscriber_side_image' );
	register_setting( 'my-plugin-settings-group', 'subscriber_popup_isactive_checkbox' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_1' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_2' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_3' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_heading' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_delay' );
}

add_action( 'admin_init', 'my_plugin_settings' );
add_action( 'admin_menu', 'my_plugin_subscriber_newsletter' );


/* --------------------------------

Handle AJAX request

---------------------------------- */

// Ajax call
add_action( 'wp_ajax_ajax_subscribersubmit', 'ajax_subscribersubmit' );
add_action( 'wp_ajax_nopriv_ajax_subscribersubmit', 'ajax_subscribersubmit' );


function ajax_subscribersubmit(){

	//echo json_encode(array( 'success' => false ));
	//die();

	// Check Mailchimp current settings
	//$currentSubscriberListID = get_option('subscriber_select_list_1');

	// Connect to the API
	require_once('src/Drewm/MailChimp.php');
	$MailChimp = new \Drewm\MailChimp('b08e782231147b0c03d797cd924f007d-us9');

	// Loop subscriptions
	$subscriptions 		= $_POST['subscriptions'];
	foreach( $subscriptions as $subscription ){

		// Make the call
		$result = $MailChimp->call('lists/subscribe', array(
		    'id'                => $subscription,
		    'email'             => array('email'=> $_POST['email']),
		    'merge_vars'        => array('FNAME'=> $_POST['name']),
		    'double_optin'      => false,
		    'update_existing'   => true,
		    'replace_interests' => false,
		    'send_welcome'      => false,
		));

		if( $result['leid'] ){
			$results[] = $result;
		}
	}
	
	// Set JSON headers
	header( 'Content-Type: application/json' );

	// Echo response
	if( count($results) > 0 ){
		echo json_encode(array( 'success' => true, 'response' => $results ));
	} else {
		echo json_encode(array( 'success' => false, 'response' => $results ));
	}

	die();
}

/*function display_form() {

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
}*/


/* --------------------------------

Register and mount CSS & Javascript

---------------------------------- */

function jm_mailchimp_register_script(){

	// Register
	wp_register_style( 'mailchimp-subscriber', plugins_url('mailchimp-wordpress-subscriber-plugin/subscriber.css'), array(), '1.3', 'all' );
	wp_register_script( 'mailchimp-subscriber', plugins_url('mailchimp-wordpress-subscriber-plugin/subscriber.js'), array('jquery'), '1.1', true );
	wp_register_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js', 'jquery', null, true);
	
	// Enqueue
	wp_enqueue_style( 'mailchimp-subscriber' );
	wp_enqueue_script( 'mailchimp-subscriber' );
	wp_enqueue_script( 'bootstrap' );
}
add_action('wp_enqueue_scripts', 'jm_mailchimp_register_script');


// Get list name from id
function getListName( $id ){
	require_once('src/Drewm/MailChimp.php');

	// Your Mailchimp API Key 
	$api = 'b08e782231147b0c03d797cd924f007d-us9';
	
	// Initializing the $MailChimp object
	$MailChimp = new \Drewm\MailChimp($api);
	$lists = $MailChimp->call('lists/list', array(
	  "filters" 	=> array(),
	  "sort_dir" 	=> "DESC"
	));

	$list_name = '';
	foreach( $lists['data'] as $list ){
		if($list['id'] == $id){
			$list_name = $list['name'];
		}
	}

	return $list_name;
}


function form($id){ ?>
<div class="fusion-fullwidth fullwidth-box" style="background-color: <?php echo get_option( 'subscriber_background_color' ); ?>">
	<div class="jm-mailchimp-subscribe">
		<div class="container" style="background-image: url('<?php echo get_option( "subscriber_side_image" ); ?>')" />
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
			
					<?php // Form ?>
					<form action="/wp-admin/admin-ajax.php" method="post">
						<input type="hidden" name="register">
						
						<?php // Heading ?>
						<div class="col-lg-12">
							<h1><?php echo get_option('subscriber_form_heading'); ?></h1>
						</div>

						<div class="row">
							<div class="col-lg-6">

								<?php
									$list1_id = get_option( 'subscriber_select_list_1' );
									$list2_id = get_option( 'subscriber_select_list_2' );
									$list3_id = get_option( 'subscriber_select_list_3' );
								?>
								
								<?php // Only first list selected; show no checkboxes! ?>
								<?php if( $list1_id && ( !$list2_id && !$list3_id ) ){ ?>
										<input type="hidden" name="subscriptions" id="list1_id_<?php echo $id; ?>" value="<?php echo get_option('subscriber_select_list_1'); ?>">
								<?php } else { ?>
									<ul class="checkboxes">
											
										<?php // List #1 ?>
										<?php if(get_option( 'subscriber_select_list_1' )){ ?>
										<li>
											<input type="checkbox" name="subscriptions" id="list1_id_<?php echo $id; ?>" value="<?php echo get_option('subscriber_select_list_1'); ?>" class="checkbox">
											<label for="list1_id_<?php echo $id; ?>"><?php echo getListName(get_option( 'subscriber_select_list_1' )); ?></label>
										</li>
										<?php } ?>

										<?php // List #2 ?>
										<?php if(get_option( 'subscriber_select_list_2' )){ ?>
										<li>
											<input type="checkbox" name="subscriptions" id="list2_id_<?php echo $id; ?>" value="<?php echo get_option('subscriber_select_list_2'); ?>" class="checkbox">
											<label for="list2_id_<?php echo $id; ?>"><?php echo getListName(get_option( 'subscriber_select_list_2' )); ?></label>
										</li>
										<?php } ?>
								
										<?php // List #3 ?>
										<?php if(get_option( 'subscriber_select_list_3' )){ ?>
										<li>
										<input type="checkbox" name="subscriptions" id="list3_id_<?php echo $id; ?>" value="<?php echo get_option('subscriber_select_list_3'); ?>" class="checkbox">
											<label for="list3_id_<?php echo $id; ?>"><?php echo getListName(get_option( 'subscriber_select_list_3' )); ?></label>
										</li>
										<?php } ?>

									</ul>
								<?php } ?>
							</div>

							<div class="col-lg-6">
								<div class="row">

									<?php // Name ?>
									<div class="col-sm-5">
										<input type="text" name="name" placeholder="Name" class="text-input" data-toggle="tooltip" data-placement="top" title="Please enter your name!">
									</div>

									<?php // Email ?>
									<div class="col-sm-5">
										<input type="email" name="email" placeholder="Email" class="text-input" data-toggle="tooltip" data-placement="top" title="Please enter your email!">
									</div>

									<?php // Button ?>
									<div class="col-sm-2">
										<input type="submit" value="Send" class="submit-button">
									</div>
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

					<?php // Validation note ?>
					<div class="validate" style="display: none;">
						<h1>Please fill in a subscription list, name and an email address!
						<a href="javascript:;" class="close-validate-message">OK</a></h1>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php // </div> ?>
</div>
<?php }

/* --------------------------------

Footer form HTML 

--------------------------------- */

function jm_mailchimp_subscribe_form(){
	
	// Display form if activated in admin
	if(get_option('subscriber_isactive_checkbox') == 'on'){
		form('id1');
	}
}

function jm_mailchimp_subscribe_form2(){	
	form('id2');
}

function init_shortcode_form(){
	ob_start();
	jm_mailchimp_subscribe_form2();
	return ob_get_clean();
}

function register_shortcodes(){
	add_shortcode('subscriber_shortcode', 'init_shortcode_form');
}

add_action('init', 'register_shortcodes');
?>