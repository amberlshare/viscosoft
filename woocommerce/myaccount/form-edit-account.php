<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_edit_account_form' ); ?>

<?php wc_print_notices(); ?>
<div class="kameleon-woo-form kameleon-woo-center-form center-content">
	<h2><?php echo esc_html('Edit Account','kameleon'); ?></h2>
	<form class="woocommerce-EditAccountForm edit-account" action="" method="post">
		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
		<input type="text" class="input-text" placeholder="First name" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
		<input type="text" class="input-text" placeholder="Last name" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
		<input type="email" class="input-text" placeholder="Email address" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
		<input type="password" class="input-text" placeholder="<?php echo esc_html( 'Current Password (leave blank to leave unchanged)', 'kameleon' ); ?>" name="password_current" id="password_current" />
		<input type="password" class="input-text" placeholder="<?php echo esc_html( 'New Password (leave blank to leave unchanged)', 'kameleon' ); ?>" name="password_1" id="password_1" />
		<input type="password" class="input-text" placeholder="<?php echo esc_html( 'Confirm New Password', 'kameleon' ); ?>" name="password_2" id="password_2" />
		<?php do_action( 'woocommerce_edit_account_form' ); ?>
		<div>
			<?php wp_nonce_field( 'save_account_details' ); ?>
			<input type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'kameleon' ); ?>" />
			<input type="hidden" name="action" value="save_account_details" />		
		</div>	
		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

	</form>
</div>