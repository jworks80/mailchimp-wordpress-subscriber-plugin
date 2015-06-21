jQuery(document).ready(function(){

	// Trigger checkboxes
	jQuery('.jm-mailchimp-subscribe .checkboxes input:checkbox').on('click',function(){
		jQuery(this).parent('li').toggleClass('active');
	});

	// Close validation message
	jQuery('.close-validate-message').click(function(){
		jQuery('.jm-mailchimp-subscribe form').show();
		jQuery('.jm-mailchimp-subscribe .validate').hide();
	});
	
	// submit button
	jQuery('.jm-mailchimp-subscribe .submit-button').on('click', function( event ){
		jQuery(event.currentTarget).closest('form').submit();
		return false;
	});	

	jQuery('.jm-mailchimp-subscribe form').on('submit', function( event ){
	
		var currentTarget = event.currentTarget;
		var validate_note = jQuery(currentTarget).closest('.jm-mailchimp-subscribe').find('.validate');
		var thank_you = jQuery(currentTarget).closest('.jm-mailchimp-subscribe').find('.thank-you');
		var error = jQuery(currentTarget).closest('.jm-mailchimp-subscribe').find('.error');
		
		// Hide form
		
		// Show spinner

		// Prevent submission
		event.preventDefault();

		subscriptions = [];
		jQuery(currentTarget).find('input:checkbox[name=subscriptions]:checked, input:hidden[name=subscriptions]', form).each(function( index ){
			if( jQuery(this).val() != '' ){
				subscriptions.push( jQuery(this).val() );
			}
		});

		// Collect vars
		var form 			= jQuery(this);
		var action 			= form.attr('action');
		var name 			= jQuery(currentTarget).find('input[name=name]', form).val();
		var email 			= jQuery(currentTarget).find('input[name=email]', form).val();
		var formData 		= {
			action: 'ajax_subscribersubmit',
			subscriptions: subscriptions,
			name: name,
			email: email
		};
		
		// Validate subscriptions
		if( subscriptions.length < 1 ){
			form.hide();
			validate_note.show();
			return false;
		}

		// Validate name
		if(name.length < 2){
			
			// Remove spinner
			
			// Show form
			
			form.hide();
			validate_note.show();
			return false;
		}

		// Validate email
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if( !regex.test( email ) ){
			
			//Remove spinner
			
			// Show form
			
			form.hide();
			validate_note.show();
			return false;
		}
		
		// Change submit button text
		jQuery(currentTarget).find('.submit-button', form).val('Sending...');

		// Send form
		jQuery.ajax({
			url: action,
			data: formData,
			method: 'post',
			success: function( response ) {
				if( response.success === true ){
					// Remove spinner

					// Thank you message
					form.hide();
					jQuery(currentTarget).closest('.jm-mailchimp-subscribe').find('.email-placeholder', thank_you).text(email);
					thank_you.show();
				} else {
					// Remove spinner
					
					// Show error note
					form.hide();
					error.show();
				}
			}
		});
		return false;
	});
});