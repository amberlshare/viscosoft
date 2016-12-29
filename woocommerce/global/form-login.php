<?php
/**
 * Login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() ) {
	return;
}

?>
<div class="km-login-inputs km-woo-section-f">
	<form method="post" id="km-login-woo">
		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>
			<div class="km-login-row">			
				<input type="text" class="input-text" name="username" id="username" placeholder="<?php esc_html_e( 'Username or email', 'kameleon' ); ?> " />
			</div>	

			<div class="km-login-row">	
				<input class="input-text" type="password" name="password" id="password" placeholder="<?php esc_html_e( 'Password', 'kameleon' ); ?>" />
			</div>
		
		<?php do_action( 'woocommerce_login_form' ); ?>

		<div class="km-login-row">
			<?php wp_nonce_field( 'woocommerce-login' ); ?>
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span class="rememberme-span">	<?php esc_html_e( 'Remember me', 'kameleon' ); ?></span>		
			<input type="submit" class="button" name="login" value="<?php esc_html_e( 'Login', 'kameleon' ); ?>" />
			<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />	
		</div>	
		<div class="km-login-row">	
			<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'kameleon' ); ?></a>
		</div>
		<?php do_action( 'woocommerce_login_form_end' ); ?>

	</form>
</div>
