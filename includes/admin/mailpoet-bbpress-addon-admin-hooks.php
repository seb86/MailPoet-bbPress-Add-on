<?php
/**
 * MailPoet bbPress Add-on Admin Hooks
 *
 * Hooks for various functions used in the admin.
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Add-on/Admin
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Filters
add_filter( 'bbp_admin_get_settings_sections', 'mailpoet_bbpress_admin_add_setting_section' );
add_filter( 'bbp_admin_get_settings_fields', 'mailpoet_bbpress_admin_settings' );
//add_filter( 'bbp_slug_conflict_check', 'mailpoet_bbpress_conflict_check', 10, 2 );
add_filter( 'bbp_map_settings_meta_caps', 'mailpoet_bbpress_admin_add_permissions', 10, 4 );

?>