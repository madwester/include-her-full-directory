<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="tml tml-lostpassword" id="theme-my-login<?php $template->the_instance(); ?>">
	<h2>Have you lost your password? Don't worry - we'll sort you out.</h2>
	<p>Please enter your username or email address. You will receive a link to create a new password via email.</p>
	<?php //$template->the_action_template_message( 'lostpassword' ); ?>
	<?php $template->the_errors(); ?>
	<form name="lostpasswordform" id="lostpasswordform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'lostpassword', 'login_post' ); ?>" method="post">
		<p class="tml-user-login-wrap">
			<label for="user_login<?php $template->the_instance(); ?>"><?php
			if ( 'email' == $theme_my_login->get_option( 'login_type' ) ) {
				_e( 'E-mail:', 'theme-my-login' );
			} else {
				_e( 'Username or E-mail:', 'theme-my-login' );
			} ?></label>
			<input type="text" name="user_login" id="user_login<?php $template->the_instance(); ?>" class="input" value="<?php $template->the_posted_value( 'user_login' ); ?>" size="20" />
		</p>

		<?php do_action( 'lostpassword_form' ); ?>

		<p class="tml-submit-wrap">
			<input type="submit" class="defaultBtn pinkBtn" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Get New Password', 'theme-my-login' ); ?>" />
			<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'lostpassword' ); ?>" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
			<input type="hidden" name="action" value="lostpassword" />
		</p>
	</form>
	<?php $template->the_action_links( array( 'lostpassword' => false ) ); ?>
</div>
