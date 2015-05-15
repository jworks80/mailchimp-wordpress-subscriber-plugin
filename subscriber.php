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
				
				<?php /* Form heading */ ?>
		    	<p>
		    		<label for="subscriber_form_heading" style="padding-right:5px;">Subscriber form heading</label>
		    		<input type="text" id="subscriber_form_heading" name="subscriber_form_heading" value="<?php echo ( get_option('subscriber_form_heading') ? get_option('subscriber_form_heading') : 'Subscribe to my newsletter'); ?>" style="width: 300px;" />
		    	</p>

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
	register_setting( 'my-plugin-settings-group', 'subscriber_popup_isactive_checkbox' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_1' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_2' );
	register_setting( 'my-plugin-settings-group', 'subscriber_select_list_3' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_heading' );
	register_setting( 'my-plugin-settings-group', 'subscriber_form_delay' );
}


add_action( 'admin_init', 'my_plugin_settings' );
add_action( 'admin_menu', 'my_plugin_subscriber_newsletter' );

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
	//$success = false;
	$subscriptions 		= $_POST['subscriptions'];
	//$success_counter 	= 0;
	foreach( $subscriptions as $subscription ){

		// Make the call
		$result = $MailChimp->call('lists/subscribe', array(
		    //'id'                => $currentSubscriberListID,
		    //'id'                => $subscription['value'],
		    'id'                => $subscription,
		    'email'             => array('email'=> $_POST['email']),
		    'merge_vars'        => array('FNAME'=> $_POST['name']),
		    'double_optin'      => false,
		    'update_existing'   => true,
		    'replace_interests' => false,
		    'send_welcome'      => false,
		));

		if( $result['leid'] ){
			//$success = true;
			//$success_counter++;
			$results[] = $result;
		}
	}
	
	// Set JSON headers
	header( 'Content-Type: application/json' );

	// Echo response
	//if( $result['leid'] ){
	//if( $success ){
	//if( $success_counter > 0 ){
	if( count($results) > 0 ){
		//echo json_encode( $result );
		echo json_encode(array( 'success' => true, 'response' => $results ));
	} else {
		echo json_encode(array( 'success' => false, 'response' => $results ));
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
?>

<script>
	jQuery(document).ready(function(){

		// Trigger checkboxes
		jQuery('#jm-mailchimp-subscribe .checkboxes input:checkbox').click(function(){
			//console.log('click');
			//var val = jQuery(this).val();
			//console.log(val);
			//jQuery(this).parent('li').toggleClass('active');
			jQuery(this).closest('li').toggleClass('active');
			//jQuery(this).toggleClass('active');
		});

		// Close validation message
		jQuery('.close-validate-message').click(function(){
			//console.log('click')
			jQuery('#jm-mailchimp-subscribe form').show();
			jQuery('#jm-mailchimp-subscribe .validate').hide();
		});

		//jQuery('#jm-mailchimp-subscribe form').hide();
		//jQuery('#jm-mailchimp-subscribe .thank-you').show();
		//jQuery('#jm-mailchimp-subscribe .error').show();
		//jQuery('#jm-mailchimp-subscribe .validate').show();

		jQuery('#jm-mailchimp-subscribe form').submit(function( event ){


			var validate_note = jQuery('#jm-mailchimp-subscribe .validate');
			var thank_you = jQuery('#jm-mailchimp-subscribe .thank-you');
			var error = jQuery('#jm-mailchimp-subscribe .error');
			
			// Hide form
			
			// Show spinner

			// Prevent submission
			event.preventDefault();

			subscriptions = [];
			jQuery('input:checkbox[name=subscriptions]:checked, input:hidden[name=subscriptions]', form).each(function( index ){
				if( jQuery(this).val() != '' ){
					subscriptions.push( jQuery(this).val() );
				}
			});

			//console.log(subscriptions);
			//return false;

			// Collect vars
			var form 			= jQuery(this);
			var action 			= form.attr('action');
			//var subscriptions 	= jQuery('input[name=subscriptions]:checked', form);
			//var subscriptions 	= [{value: '2192225b1d'}, {value: '096671b37c'}, {value: '06d9378dc0'}];
			var name 			= jQuery('input[name=name]', form).val();
			var email 			= jQuery('input[name=email]', form).val();
			var formData 		= {
				action: 'ajax_subscribersubmit',
				subscriptions: subscriptions,
				name: name,
				email: email
			};
			
			//return false;

			//console.log( jQuery('input:checked', form).val() );
			//console.log( jQuery('input[name=subscriptions]:checked', form).length );
			//console.log(subscriptions);
			//return false;
			
			// Validate subscriptions
			if( subscriptions.length < 1 ){
				//console.log( 'Please choose at least one subscription list' );
				//console.log( subscriptions.length );
				form.hide();
				validate_note.show();
				return false;
			}

			// Validate name
			if(name.length < 2){
				//jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
				//jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
				
				// Remove spinner
				
				// Show form
				
				//console.log('Please enter your name');
				
				form.hide();
				validate_note.show();
				
				return false;
			}

			// Validate email
			var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if( !regex.test( email ) ){
				//jQuery('#newsletter-thickbox-content .alert p').text('Please enter a valid email address.');
				//jQuery('#newsletter-thickbox-content .alert').show().delay(3000).fadeOut(2000);
				
				//Remove spinner
				
				// Show form
				
				//console.log('Please enter a valid email address');
				//
				form.hide();
				validate_note.show();
				
				return false;
			}
			
			// Change submit button text
			jQuery('.submit-button', form).val('Sending...');

			// Send form
			jQuery.ajax({
				url: action,
				data: formData,
				method: 'post',
				success: function( response ) {
					//if( response.leid ){
					if( response.success === true ){

						// Remove spinner
						
						// Show thank you note

						//console.log( 'Thank you!' );
						//console.log( response );

						// Thank you message
						//jQuery('#newsletter-thickbox-content .form').hide();
						//jQuery('#newsletter-thickbox-content .thank-you .email-placeholder').text( email );
						//jQuery('#newsletter-thickbox-content .thank-you').show();
						
						form.hide();
						jQuery('.email-placeholder', thank_you).text(email);
						thank_you.show();
					} else {

						// Remove spinner
						
						// Show error note

						//console.log( 'Could not send form. Try agian later.' );
						//console.log( response );

						// Change submit button text
						//jQuery('.submit-button', form).val('Send');
						
						// Show error message
						//jQuery('#newsletter-thickbox-content .form').hide();
						//jQuery('#newsletter-thickbox-content .error').show();
						
						form.hide();
						error.show();
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
	#jm-mailchimp-subscribe .submit-button:focus
	/*#jm-mailchimp-subscribe .close-validate-message,
	#jm-mailchimp-subscribe .close-validate-message:focus*/ {
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
	#jm-mailchimp-subscribe .submit-button, 
	#jm-mailchimp-subscribe .close-validate-message {
		text-transform: uppercase;
		cursor: 		pointer;
	}

	/* Checkboxes */
	#jm-mailchimp-subscribe .checkboxes {
		list-style: none;
		padding-left: 0;
	}
	#jm-mailchimp-subscribe .checkboxes li {
		display: inline-block;
		height:  15px;
		position: relative;
		margin-right: 31px;
		text-transform: uppercase;
		font-size: 16px;
	}
	#jm-mailchimp-subscribe .checkboxes li:before {
		height: 15px;
		width: 15px;
		border: 1px solid #FFF;
		content: '';
		display: inline-block;
		position: absolute;
		left: 0px;
		top: 0px;
		margin-right: 25px;
		cursor: pointer;
	}
	#jm-mailchimp-subscribe .checkboxes li.active:before {
		background: #FFF;
	}
	#jm-mailchimp-subscribe .checkboxes li label {
		margin-left: 25px;
		cursor: pointer;
	}
	#jm-mailchimp-subscribe .checkboxes input {
		display: none;
	}

	/* Columns */
	#jm-mailchimp-subscribe .col-sm-5,
	#jm-mailchimp-subscribe .col-sm-2{
		padding: 0;
	}

	#jm-mailchimp-subscribe .close-validate-message{
		display: inline-block;
		width: 67px;
		margin-left: 10px;
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
				<div class="col-lg-12">
					<h1><?php echo get_option('subscriber_form_heading'); ?></h1>
				</div>

				<div class="col-lg-6">

					<?php
						$list1_id = get_option( 'subscriber_select_list_1' );
						$list2_id = get_option( 'subscriber_select_list_2' );
						$list3_id = get_option( 'subscriber_select_list_3' );
					?>
					
					<?php // Only first list selected; show no checkboxes! ?>
					<?php if( $list1_id && ( !$list2_id && !$list3_id ) ){ ?>
							<?php /*<input type="hidden" name="list1_id" id="list1_id" value="<?php echo get_option('subscriber_select_list_1'); ?>">*/ ?>
							<input type="hidden" name="subscriptions" id="list1_id" value="<?php echo get_option('subscriber_select_list_1'); ?>">
					<?php } else { ?>
						<ul class="checkboxes">
								
							<?php // List #1 ?>
							<?php if(get_option( 'subscriber_select_list_1' )){ ?>
							<li>
								<label for="list1_id">
									<?php /*<input type="checkbox" name="list1_id" id="list1_id" value="<?php echo get_option('subscriber_select_list_1'); ?>"> */?>
									<input type="checkbox" name="subscriptions" id="list1_id" value="<?php echo get_option('subscriber_select_list_1'); ?>" class="checkbox">
									<?php echo getListName(get_option( 'subscriber_select_list_1' )); ?>
								</label>
							</li>
							<?php } ?>

							<?php // List #2 ?>
							<?php if(get_option( 'subscriber_select_list_2' )){ ?>
							<li>
								<label for="list2_id">
									<?php /*<input type="checkbox" name="list2_id" id="list2_id" value="<?php echo get_option('subscriber_select_list_2'); ?>">*/ ?>
									<input type="checkbox" name="subscriptions" id="list2_id" value="<?php echo get_option('subscriber_select_list_2'); ?>" class="checkbox">
									<?php echo getListName(get_option( 'subscriber_select_list_2' )); ?>
								</label>
							</li>
							<?php } ?>
					
							<?php // List #3 ?>
							<?php if(get_option( 'subscriber_select_list_3' )){ ?>
							<li>
								<label for="list3_id">
									<?php /*<input type="checkbox" name="list3_id" id="list3_id" value="<?php echo get_option('subscriber_select_list_3'); ?>">*/ ?>
									<input type="checkbox" name="subscriptions" id="list3_id" value="<?php echo get_option('subscriber_select_list_3'); ?>" class="checkbox">
									<?php echo getListName(get_option( 'subscriber_select_list_3' )); ?>
								</label>
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

<?php
}

// Display form if plugin is activated in settings
if(get_option('subscriber_isactive_checkbox') == 'on'){
	//add_action('wp_footer', 'display_form');
	add_action('wp_footer', 'display_footer_form');
}
?>