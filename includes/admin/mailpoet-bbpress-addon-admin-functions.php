<?php
/**
 * MailPoet bbPress Add-on Admin Functions
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Add-on/Admin/Functions
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get all MailPoet bbPress Add-on screen ids
 *
 * @return array
 */
function mailpoet_bbpress_addon_get_screen_ids() {
	$mailpoet_bbpress_addon_screen_id = strtolower( str_replace ( ' ', '-', __( 'MailPoet bbPress Add-on', 'mailpoet_bbpress_addon' ) ) );

	return apply_filters( 'mailpoet_bbpress_addon_screen_ids', array(
		'toplevel_page_' . $mailpoet_bbpress_addon_screen_id,
	) );
}

/**
 * Gets MailPoet forum settings section.
 *
 * @return type
 */
function mailpoet_bbpress_admin_add_setting_section( $sections ) {
	$sections['bbp_settings_mailpoet'] = array(
		'title' 				=> __( 'MailPoet Integration', 'mailpoet_bbpress_addon' ),
		'callback' 				=> 'bbpress_admin_setting_callback_mailpoet_section',
		'page' 					=> 'discussion',
	);

	return $sections;
}

/** 
 * Settings Title
 */
function bbpress_admin_setting_callback_mailpoet_section( ) {
?>
	<p><?php esc_html_e( 'Forum Settings for MailPoet Newsletters', 'mailpoet_bbpress_addon' ); ?></p>
<?php
}

/** 
 * MailPoet Admin Settings
 */
function mailpoet_bbpress_admin_settings( $settings ) {

	$settings['bbp_settings_mailpoet'] = array(

		'_bbp_enable_mailpoet_checkbox_on_registration' => array(
			'title' 			=> __( 'Enable checkbox on Registration.', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_checkbox_registration',
			'sanitize_callback' => 'intval',
			'args' 				=> array()
		),

		'_bbp_mailpoet_checkbox_label' => array(
			'title' 			=> __( 'Checkbox label text', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_checkbox_label',
			'sanitize_callback' => 'intval',
			'args' 				=> array()
		),

		'_bbp_precheck_mailpoet_checkbox' => array(
			'title' 			=> __( 'Pre-check the checkbox?', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_checkbox_pre_checked',
			'sanitize_callback' => 'intval',
			'args' 				=> array()
		),

	);

	return $settings;
}

/** 
 * MailPoet Admin Settings
 */
function mailpoet_bbpress_conflict_check( $slug, $default ) {

	$slug['bbp_settings_mailpoet'] = array(

		'_bbp_enable_mailpoet_checkbox_on_registration' => array(
			'name' 		=> __( 'Enable checkbox on Registration.', 'mailpoet_bbpress_addon' ),
			'default' 	=> 'mailpoet_bbpress_admin_setting_checkbox_registration',
			'context' 	=> 'intval',
		),

		'_bbp_mailpoet_checkbox_label' => array(
			'name' 		=> __( 'Checkbox label text', 'mailpoet_bbpress_addon' ),
			'default' 	=> __( 'Yes, please subscribe me to your newsletters.', 'mailpoet_bbpress_addon' ),
			'context' 	=> 'bbPress',
		),

		'_bbp_precheck_mailpoet_checkbox' => array(
			'name' 		=> __( 'Pre-check the checkbox?', 'mailpoet_bbpress_addon' ),
			'default' 	=> 'no',
			'context' 	=> 'bbPress',
		),

	);

	return $slug;
}

/**
 * This displays a checkbox field setting to enable 
 * the user to subscribe during registration.
 */
function mailpoet_bbpress_admin_setting_checkbox_registration( ) {
?>
	<input id="_bbp_enable_mailpoet_checkbox_on_registration" type="checkbox" name="_bbp_enable_mailpoet_checkbox_on_registration" value="yes"<?php checked( get_option( '_bbp_enable_mailpoet_checkbox_on_registration', false ) ); bbp_maybe_admin_setting_disabled( '_bbp_enable_mailpoet_checkbox_on_registration' ); ?> />
	<label for="_bbp_enable_mailpoet_checkbox_on_registration"><?php esc_html_e( 'Enable users to subscriber to MailPoet Newsletters during registration?', 'mailpoet_bbpress_addon' ); ?></label>
<?php
}

/**
 * This displays a input[type="text"] field setting to 
 * all the admin to display the message they want next 
 * to the checkbox on the registration form.
 */
function mailpoet_bbpress_admin_setting_checkbox_label( ) {
	$default_label = __( 'Yes, please subscribe me to your newsletters.', 'mailpoet_bbpress_addon' );
?>
	<input id="_bbp_mailpoet_checkbox_label" name="_bbp_mailpoet_checkbox_label" type="text" value="<?php bbp_form_option( '_bbp_mailpoet_checkbox_label', $default_label ); ?>" pre="<?php echo $default_label; ?>" required class="regular-text code"<?php bbp_maybe_admin_setting_disabled( '_bbp_mailpoet_checkbox_label' ); ?> />
	<p class="description"><?php _e( 'HTML tags like <code>&lt;strong&gt;</code> and <code>&lt;em&gt;</code> are allowed in the label text.', 'mailpoet_bbpress_addon' ); ?></p>
<?php
}

/**
 * This displays a checkbox field setting to enable 
 * the user to subscribe during registration.
 */
function mailpoet_bbpress_admin_setting_checkbox_pre_checked( ) {
?>
	<label><input type="radio" name="_bbp_precheck_mailpoet_checkbox" value="1"<?php checked( bbp_get_form_option( '_bbp_precheck_mailpoet_checkbox', false ) ); bbp_maybe_admin_setting_disabled( '_bbp_precheck_mailpoet_checkbox' ); ?> /> <?php esc_html_e( 'Yes', 'mailpoet_bbpress_addon' ); ?></label> &nbsp; <label><input type="radio" name="_bbp_precheck_mailpoet_checkbox" value="0"<?php checked( bbp_get_form_option( '_bbp_precheck_mailpoet_checkbox', false ) ); bbp_maybe_admin_setting_disabled( '_bbp_precheck_mailpoet_checkbox' ); ?> /> <?php esc_html_e( 'No', 'mailpoet_bbpress_addon' ); ?></label>
<?php
}

/**
 * Adds bbPress permission for MailPoet settings.
 */
function mailpoet_bbpress_admin_add_permissions( $caps, $cap, $user_id, $args ) {
	if ($cap == 'bbp_settings_mailpoet') {
		if ( is_plugin_active( 'wysija-newsletters/index.php' ) ) {
			$caps = array( bbpress()->admin->minimum_capability );
		}
	}

	return $caps;
}

?>