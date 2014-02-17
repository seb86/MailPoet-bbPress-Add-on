<?php
/**
 * MailPoet bbPress Add-on Admin.
 *
 * @author 		Sebs Studio
 * @category 	Admin
 * @package 	MailPoet bbPress Add-on/Admin
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'MailPoet_bbPress_Addon_Admin' ) ) {

	class MailPoet_bbPress_Addon_Admin {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Actions
			add_action( 'init', array( &$this, 'includes' ) );
			add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		}

		/**
		 * Include any classes we need within admin.
		 */
		public function includes() {
			// Functions
			include( 'mailpoet-bbpress-addon-admin-functions.php' );
		}

		/**
		 * Include admin files conditionally
		 */
		public function conditonal_includes() {
			$screen = get_current_screen();

			switch ( $screen->id ) {
				case 'users' :
				case 'user' :
				case 'profile' :
				case 'user-edit' :
					include( 'class-mailpoet-bbpress-addon-admin-profile.php' );
				break;
			}
		}

	} // end class.

} // end if class exists.

return new MailPoet_bbPress_Addon_Admin();

?>