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

?>
