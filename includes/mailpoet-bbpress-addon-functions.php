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
	$prechecked = get_option('_bbp_precheck_mailpoet_checkbox');
?>
	<div class="bbp-remember-me mailpoet">
		<input type="checkbox" name="user_subscribe_to_mailpoet" value="1" id="user_subscribe_to_mailpoet" tabindex="<?php bbp_tab_index(); ?>"<?php if($prechecked == 'yes') echo ' checked="checked"'; ?> />
		<label for="user_subscribe_to_mailpoet"><?php _e( 'Yes, please subscribe me to your newsletter.', 'mailpoet_bbpress_addon' ); ?></label>
	</div>
<?php
}

/*
 * Saves the additional field and subscribes the user if ticked.
 */
function save_user_register ($user_id) {
	if ( isset( $_POST['user_subscribe_to_mailpoet'] ) || !empty( $_POST[ 'bbpress_user_subscribe_to_mailpoet' ] ) ) {
		update_user_meta( $user_id, 'bbpress_user_subscribe_to_mailpoet', mailpoet_bbpress_add_on_clean($_POST['user_subscribe_to_mailpoet']) );

		$mailpoet_lists = get_option('_bbp_mailpoet_lists');

		$user_data = array(
			'email' 	=> $_POST['user_email'],
			'firstname' => $_POST['user_login'],
			'lastname' 	=> ''
		);

		$data_subscriber = array(
			'user' 		=> $user_data,
			'user_list' => array('list_ids' => $mailpoet_lists)
		);

		$userHelper = &WYSIJA::get('user','helper');
		$userHelper->addSubscriber($data_subscriber);
	}
}
?>