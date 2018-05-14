<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="tml tml-login" id="theme-my-login<?php $template->the_instance(); ?>">
<h2>Welcome back, friend!</h2>
	<?php $template->the_errors(); ?>
	<form name="loginform" id="loginform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'login', 'login_post' ); ?>" method="post">
		<p class="tml-user-login-wrap">
			<input type="text" placeholder="Username or email" name="log" id="user_login<?php $template->the_instance(); ?>" class="input inputLogin" value="<?php $template->the_posted_value( 'log' ); ?>" size="20" />
		</p>
		<p class="tml-user-pass-wrap">
			<input type="password" placeholder="Password" name="pwd" id="user_pass<?php $template->the_instance(); ?>" class="input inputLogin" value="" size="20" autocomplete="off" />
		</p>

		<?php do_action( 'login_form' ); ?>


			<p class="tml-rememberme-wrap">
				<input name="rememberme" type="checkbox" id="rememberme<?php $template->the_instance(); ?>" value="forever" />
				<label for="rememberme<?php $template->the_instance(); ?>"><?php esc_attr_e( 'Remember Me', 'theme-my-login' ); ?></label>
			</p>

				<input type="submit" class="defaultBtn yellowBtn" name="wp-submit" id="wp-submit<?php $template->the_instance(); ?>" value="<?php esc_attr_e( 'Log In', 'theme-my-login' ); ?>" />
				<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'login' ); ?>" />
				<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
				<input type="hidden" name="action" value="login" />
			</p>

	</form>
	<?php $template->the_action_links( array( 'login' => false ) ); ?>
</div>
