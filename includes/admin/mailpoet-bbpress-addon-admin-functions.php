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

	return array(
		'toplevel_page_' . $mailpoet_bbpress_addon_screen_id,
	);
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
		'page' 					=> 'bbpress',
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
			'args'              => array()
		),

		'_bbp_mailpoet_checkbox_label' => array(
			'title' 			=> __( 'Checkbox label text', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_checkbox_label',
			'sanitize_callback' => 'esc_sql',
			'args'              => array()
		),

		'_bbp_precheck_mailpoet_checkbox' => array(
			'title' 			=> __( 'Pre-check the checkbox?', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_checkbox_pre_checked',
			'sanitize_callback' => 'sanitize_text_field',
			'args'              => array()
		),

		'_bbp_mailpoet_lists' => array(
			'title' 			=> __( 'Lists', 'mailpoet_bbpress_addon' ),
			'callback' 			=> 'mailpoet_bbpress_admin_setting_lists',
			'sanitize_callback' => 'esc_sql',
			'args'              => array()
		),

	);

	return $settings;
}

/**
 * This displays a checkbox field setting to enable 
 * the user to subscribe during registration.
 */
function mailpoet_bbpress_admin_setting_checkbox_registration( ) {
	$value = check_enabled_field( '_bbp_enable_mailpoet_checkbox_on_registration', false );
?>
	<input id="_bbp_enable_mailpoet_checkbox_on_registration" type="checkbox" name="_bbp_enable_mailpoet_checkbox_on_registration" value="1"<?php checked( $value ); bbp_maybe_admin_setting_disabled( '_bbp_enable_mailpoet_checkbox_on_registration' ); ?> />
	<label for="_bbp_enable_mailpoet_checkbox_on_registration"><?php esc_html_e( 'Enable users to subscriber to MailPoet Newsletters during registration?', 'mailpoet_bbpress_addon' ); ?></label>
<?php
}

/**
 * This displays a input[type="text"] field setting to 
 * all the admin to display the message they want next 
 * to the checkbox on the registration form.
 */
function mailpoet_bbpress_admin_setting_checkbox_label() {
	$default = __( 'Yes, please subscribe me to your newsletters.', 'mailpoet_bbpress_addon' );
	$value = bbp_get_form_option( '_bbp_mailpoet_checkbox_label', $default );
?>
	<input id="_bbp_mailpoet_checkbox_label" name="_bbp_mailpoet_checkbox_label" type="text" value="<?php echo esc_html($value); ?>" required class="regular-text code"<?php bbp_maybe_admin_setting_disabled( '_bbp_mailpoet_checkbox_label' ); ?> />
	<p class="description"><?php _e( 'HTML tags like <code>&lt;strong&gt;</code> and <code>&lt;em&gt;</code> are allowed in the label text.', 'mailpoet_bbpress_addon' ); ?></p>
<?php
}

/**
 * This displays a checkbox field setting to enable 
 * the user to subscribe during registration.
 */
function mailpoet_bbpress_admin_setting_checkbox_pre_checked() {
	// Current setting
	$show_pre_checked = bbp_get_form_option('_bbp_precheck_mailpoet_checkbox');

	// Options for pre checked checkbox
	$options = array(
		'yes' => array(
			'name' => __( 'Yes', 'mailpoet_bbpress_addon' )
		),
		'no' => array(
			'name' => __( 'No', 'mailpoet_bbpress_addon' )
		)
	); ?>

	<select name="_bbp_precheck_mailpoet_checkbox" id="_bbp_precheck_mailpoet_checkbox" <?php bbp_maybe_admin_setting_disabled( '_bbp_precheck_mailpoet_checkbox' ); ?>>

		<?php foreach ( $options as $option_id => $details ) { ?>

			<option <?php selected( $show_pre_checked, $option_id ); ?> value="<?php echo esc_attr( $option_id ); ?>"><?php echo esc_html( $details['name'] ); ?></option>

		<?php } ?>

	</select>
<?php
}

// List all enabled lists to select.
function mailpoet_bbpress_admin_setting_lists() {
	?>
	<p><?php _e( 'Here you can assign the customer to the lists you enable when they subscribe. Simply tick the lists you want your customers to subscribe to and press "Save Changes".', 'mailpoet_bbpress_add_on' ); ?></p>
	<table class="widefat">
		<thead>
			<tr valign="top">
				<td class="forminp" colspan="2">
					<table class="mailpoet widefat" cellspacing="0">
						<thead>
							<tr>
								<th width="1%"><?php _e('Enabled', 'mailpoet_bbpress_add_on'); ?></th>
								<th><?php _e('Lists', 'mailpoet_bbpress_add_on'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$checkout_lists = get_option('_bbp_mailpoet_lists');
						foreach(mailpoet_lists() as $key => $list){
							$list_id = $list['list_id'];
							$checked = '';
							if(isset($checkout_lists) && !empty($checkout_lists)){
								if(in_array($list_id, $checkout_lists)){ $checked = ' checked="checked"'; }
							}
							echo '<tr>
								<td width="1%" class="checkbox">
									<input type="checkbox" name="_bbp_mailpoet_lists[]" value="'.esc_attr($list_id).'"'.$checked.' />
								</td>
								<td>
									<p><strong>'.$list['name'].'</strong></p>
								</td>
							</tr>';
						}
						?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/* Register bbPress fields for MailPoet. */
function register_bbpress_mailpoet_settings() {
	register_setting( 'bbpress', '_bbp_enable_mailpoet_checkbox_on_registration' );
	register_setting( 'bbpress', '_bbp_mailpoet_checkbox_label' );
	register_setting( 'bbpress', '_bbp_precheck_mailpoet_checkbox' );
	register_setting( 'bbpress', '_bbp_mailpoet_lists' );
} 

/**
 * This checks the value of a checkbox.
 *
 * @return string
 */
function check_enabled_field( $value, $default = true ) {
	return (bool) get_option( $value, $default );
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