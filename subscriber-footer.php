<?php
function display_footer_form(){ ?>

<script type="text/javascript"></script>

<style></style>

<div id="jm-mailchimp-subscribe">
	
	<?php // Form ?>
	<form action="/wp-admin/admin-ajax.php" method="post">
		
	</form>

	<?php // Thank you note ?>
	<div class="thank-you" style="display: none;">
	<h1>Thank you!</h1>
		<p><span class="email-placeholder">[Email]</span> successfuly added to subsriber list.</p>
	</div>

	<?php // Error note ?>
	<div class="error" style="display: none;">
		<h1>Oops!</h1>
		<p>There was a technical error. Try again later.</p>
	</div>
</div>

<?php
}

if(get_option('subscriber_isactive_checkbox') == 'on'){
	add_action('wp_footer', 'display_footer_form');
}
?>