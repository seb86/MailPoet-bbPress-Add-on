<?php
/**
 * MailPoet bbPress Add-on Functions
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Add-on/Functions
 * @version 	1.0.0
 */

/*
 * Adds a checkbox field to the register form that 
 * is displayed using the bbPress shortcode [bbp-register] 
 */
function subscribe_me_to_mailpoet_field() {
?>
	<div class="bbp-remember-me mailpoet">
		<input type="checkbox" name="user_subscribe_to_mailpoet" value="yes" id="user_subscribe_to_mailpoet" tabindex="<?php bbp_tab_index(); ?>" />
		<label for="user_subscribe_to_mailpoet"><?php _e( 'Yes, please subscribe me to your newsletter.', 'mailpoet_bbpress_addon' ); ?></label>
	</div>
<?php
}

?>