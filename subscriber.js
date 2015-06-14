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