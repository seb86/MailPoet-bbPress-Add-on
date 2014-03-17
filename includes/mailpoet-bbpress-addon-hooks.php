<?php
/**
 * MailPoet bbPress Add-on Hooks
 *
 * Hooks for various functions used.
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Add-on/Functions
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Actions
add_action( 'register_form', 'subscribe_me_to_mailpoet_field' );
add_action( 'user_register', 'save_user_register' );

?>