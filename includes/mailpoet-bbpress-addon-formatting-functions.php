<?php
/**
 * MailPoet bbPress Addon Formatting
 *
 * Functions for formatting data.
 *
 * @author 		Sebs Studio
 * @category 	Core
 * @package 	MailPoet bbPress Addon/Functions
 * @version 	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Clean variables
 *
 * @access public
 * @param string $var
 * @return string
 */
function mailpoet_bbpress_add_on_clean( $var ) {
	return sanitize_text_field( $var );
}

?>