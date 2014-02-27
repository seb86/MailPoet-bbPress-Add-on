<?php
/**
 * MailPoet bbPress Add-on Uninstall
 *
 * Uninstalling MailPoet bbPress Add-on 
 * deletes profile fields and options.
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Add-on/Uninstaller
 * @version 	1.0.0
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();

// For Single site
if ( !is_multisite() ) {

	// Your uninstall code goes here.
	// List each option to delete here.
	delete_option( '_bbp_enable_mailpoet_checkbox_on_registration' );
	delete_option( '_bbp_mailpoet_checkbox_label' );
	delete_option( '_bbp_precheck_mailpoet_checkbox' );
} 
// For Multisite
else {
	global $wpdb;

	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );

		// Your uninstall code goes here.
		// List each option to delete here.
		delete_site_option( '_bbp_enable_mailpoet_checkbox_on_registration' );
		delete_site_option( '_bbp_mailpoet_checkbox_label' );
		delete_site_option( '_bbp_precheck_mailpoet_checkbox' );
	}

	switch_to_blog( $original_blog_id );
}
?>