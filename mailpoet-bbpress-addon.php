<?php
/*
 * Plugin Name: MailPoet bbPress Addon
 * Plugin URI: http://www.wordpress.org/plugins/mailpoet-bbpress-addon/
 * Description: Enables your new forum members to subscribe to a newsletter while registering on the forum. Requires the use of [bbp-register] shortcode.
 * Version: 1.0.0
 * Author: Sebs Studio
 * Author URI: http://www.sebs-studio.com
 * Author Email: sebastien@sebs-studio.com
 * Requires at least: 3.7.1
 * Tested up to: 3.8.1
 *
 * Text Domain: mailpoet_bbpress_addon
 * Domain Path: /languages/
 * Network: false
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'MailPoet_bbPress_Addon' ) ) {

/**
 * Main MailPoet bbPress Addon Class
 *
 * @class MailPoet_bbPress_Addon
 * @version 1.0.0
 */
final class MailPoet_bbPress_Addon {

	/**
	 * Constants
	 */

	// Slug
	const slug = 'mailpoet_bbpress_addon';

	// Text Domain
	const text_domain = 'mailpoet_bbpress_addon';

	/**
	 * Global Variables
	 */

	/**
	 * The Plug-in name.
	 *
	 * @var string
	 */
	public $name = "MailPoet bbPress Addon";

	/**
	 * The Plug-in version.
	 *
	 * @var string
	 */
	public $version = "1.0.0";

	/**
	 * The WordPress version the plugin requires minumum.
	 *
	 * @var string
	 */
	public $wp_version_min = "3.7.1";

	/**
	 * The single instance of the class
	 *
	 * @var 
	 */
	protected static $_instance = null;

	/**
	 * The Plug-in URL.
	 *
	 * @var string
	 */
	public $web_url = "http://www.wordpress.org/plugins/mailpoet-bbpress-addon/";

	/**
	 * The Plug-in documentation URL.
	 *
	 * @var string
	 */
	public $doc_url = "http://docs.sebs-studio.com/extension/mailpoet/mailpoet-bbpress-addon/";

	/**
	 * Main MailPoet bbPress Addon Instance
	 *
	 * Ensures only one instance of  is loaded or can be loaded.
	 *
	 * @access public static
	 * @see MailPoet_bbPress_Addon()
	 * @return MailPoet bbPress Addon - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		// Define constants
		$this->define_constants();

		// Check plugin requirements
		$this->check_requirements();

		// Include required files
		$this->includes();

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'init', array( &$this, 'init_mailpoet_bbpress_addon' ), 0 );
	}

	/**
	 * Plugin action links.
	 *
	 * @access public
	 * @param mixed $links
	 * @return void
	 */
	public function action_links( $links ) {
		// List your action links
		if( current_user_can( 'manage_options' ) ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'options-general.php?page=bbpress' ) . '">' . __( 'Settings', 'mailpoet_bbpress_addon' ) . '</a>',
			);
		}

		return array_merge( $links, $plugin_links );
	}

	/**
	 * Plugin row meta links
	 *
	 * @access public
	 * @param array $input already defined meta links
	 * @param string $file plugin file path and name being processed
	 * @return array $input
	 */
	public function plugin_row_meta( $input, $file ) {
		if ( plugin_basename( __FILE__ ) !== $file ) {
			return $input;
		}

		$links = array(
			'<a href="' . esc_url( apply_filters( 'mailpoet_bbpress_addon_documentation_url', $this->doc_url ) ) . '">' . __( 'Documentation', 'mailpoet_bbpress_addon' ) . '</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}

	/**
	 * Define Constants
	 *
	 * @access private
	 */
	private function define_constants() {
		define( 'MAILPOET_BBPRESS_ADDON', $this->name );
		define( 'MAILPOET_BBPRESS_ADDON_FILE', __FILE__ );
		define( 'MAILPOET_BBPRESS_ADDON_VERSION', $this->version );
		define( 'MAILPOET_BBPRESS_ADDON_WP_VERSION_REQUIRE', $this->wp_version_min );

		define( 'MAILPOET_BBPRESS_ADDON_README_FILE', 'http://plugins.svn.wordpress.org/mailpoet_bbpress_addon/trunk/readme.txt' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		define( 'MAILPOET_BBPRESS_ADDON_SCRIPT_MODE', $suffix );

	}

	/**
	 * Checks that the WordPress setup meets the plugin requirements.
	 *
	 * @access private
	 * @global string $wp_version
	 * @return boolean
	 */
	private function check_requirements() {
		global $wp_version;

		if ( !version_compare( $wp_version, MAILPOET_BBPRESS_ADDON_WP_VERSION_REQUIRE, '>=' ) ) {
			add_action('admin_notices', array( &$this, 'display_req_notice' ) );
			return false;
		}

		if( !is_plugin_active( 'bbpress/bbpress.php' ) && !is_plugin_active( 'wysija-newsletters/index.php' ) ) {
			add_action('admin_notices', array( &$this, 'display_req_notice_mailpoet' ) );
			add_action('admin_notices', array( &$this, 'display_req_notice_bbpress' ) );
			return false;
		}

		if( !is_plugin_active( 'wysija-newsletters/index.php' ) ) {
			add_action('admin_notices', array( &$this, 'display_req_notice_mailpoet' ) );
			return false;
		}

		if( !is_plugin_active( 'bbpress/bbpress.php' ) ) {
			add_action('admin_notices', array( &$this, 'display_req_notice_bbpress' ) );
			return false;
		}

		return true;
	}

	/**
	 * Display the requirement notice for WordPress.
	 *
	 * @access static
	 */
	static function display_req_notice() {
		echo '<div id="message" class="error"><p>';
		echo sprintf( __('Sorry, <strong>%s</strong> requires WordPress ' . MAILPOET_PIWIK_ADDON_WP_VERSION_REQUIRE . ' or higher. Please <a href="%s">upgrade</a> your WordPress setup.', 'mailpoet_piwik_addon'), MAILPOET_PIWIK_ADDON, admin_url( 'update-core.php' ) );
		echo '</p></div>';
	}

	/**
	 * Display the requirement notice for bbPress.
	 *
	 * @access static
	 */
	static function display_req_notice_bbpress() {
		echo '<div id="message" class="error"><p>';
		echo sprintf( __('Sorry, <strong>%s</strong> requires bbPress for this plugin to work. Please install and activate <strong><a href="%s">bbPress</a></strong> first.', 'mailpoet_bbpress_addon'), MAILPOET_BBPRESS_ADDON, admin_url('plugin-install.php?tab=search&type=term&s=bbPress') );
		echo '</strong></p></div>';
	}

	/**
	 * Display the requirement notice for MailPoet.
	 *
	 * @access static
	 */
	static function display_req_notice_mailpoet() {
		echo '<div id="message" class="error"><p>';
		echo sprintf( __('Sorry, <strong>%s</strong> requires MailPoet Newsletters for this plugin to work. Please install and activate <strong><a href="%s">MailPoet Newsletters</a></strong> first.', 'mailpoet_bbpress_addon'), MAILPOET_BBPRESS_ADDON, admin_url('plugin-install.php?tab=search&type=term&s=MailPoet+Newsletters+%28formerly+Wysija%29') );
		echo '</p></div>';
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @access public
	 * @return void
	 */
	public function includes() {
		include_once( 'includes/mailpoet-bbpress-addon-core-functions.php' ); // Contains core functions for the front/back end.

		if ( is_admin() ) {
			$this->admin_includes();
		}

		if ( defined('DOING_AJAX') ) {
			$this->ajax_includes();
		}

		if ( ! is_admin() || defined('DOING_AJAX') ) {
			$this->frontend_includes();
		}

	}

	/**
	 * Include required admin files.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_includes() {
		include_once( 'includes/admin/mailpoet-bbpress-addon-admin-hooks.php' ); // Hooks used in the admin
		include_once( 'includes/admin/class-mailpoet-bbpress-addon-install.php' ); // Install plugin
		include_once( 'includes/admin/class-mailpoet-bbpress-addon-admin.php' ); // Admin section
	}

	/**
	 * Include required ajax files.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_includes() {
		//include_once( 'includes/mailpoet-bbpress-addon-ajax.php' ); // Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_includes() {
		include_once( 'includes/mailpoet-bbpress-addon-functions.php' ); // Contains functions for various front-end events
		include_once( 'includes/mailpoet-bbpress-addon-hooks.php' ); // Hooks used in the frontend
	}

	/**
	 * Runs when the plugin is initialized.
	 *
	 * @access public
	 */
	public function init_mailpoet_bbpress_addon() {
		// Set up localisation
		$this->load_plugin_textdomain();

		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

		// This will run on the frontend and for ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {

		}
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any 
	 * following ones if the same translation is present.
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'mailpoet_bbpress_addon' );

		// Admin Locale
		if ( is_admin() ) {
			load_textdomain( 'mailpoet_bbpress_addon', WP_LANG_DIR . "/mailpoet_bbpress_addon/mailpoet_bbpress_addon-admin-$locale.mo" );
			load_textdomain( 'mailpoet_bbpress_addon', $this->plugin_path() . "/languages/mailpoet_bbpress_addon-admin-$locale.mo" );
		}

		// Frontend Locale
		load_textdomain( 'mailpoet_bbpress_addon', WP_LANG_DIR . "/mailpoet_bbpress_addon/mailpoet_bbpress_addon-" . $locale . ".mo" );

		// Set Plugin Languages Directory
		// Plugin translations can be filed in the mailpoet-bbpress-addon/languages/ directory
		// Wordpress translations can be filed in the wp-content/languages/ directory
		load_plugin_textdomain( 'mailpoet_bbpress_addon', false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Registers and enqueues stylesheets and javascripts 
	 * for the administration panel and the front of the site.
	 *
	 * @access private
	 */
	private function register_scripts_and_styles() {
		global $wp_locale;

		if ( is_admin() ) {
			// Main Plugin Javascript
			//$this->load_file( self::slug . '_admin_script', '/assets/js/admin/mailpoet-bbpress-addon' . MAILPOET_BBPRESS_ADDON_SCRIPT_MODE . '.js', true, array('jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'), MailPoet_bbPress_Addon()->version );
			$this->load_file( self::slug . '_admin_script', '/assets/js/admin/mailpoet-bbpress-addon.js', true, array('jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'), MailPoet_bbPress_Addon()->version );

			// Variables for JS scripts
			wp_localize_script( self::slug . '_admin_script', 'mailpoet_bbpress_addon_admin_params', apply_filters( 'mailpoet_bbpress_addon_admin_params', array(
				'plugin_url' => $this->plugin_url(),
				)
			) );

			// Stylesheets
			$this->load_file( self::slug . '_admin_style', '/assets/css/admin/mailpoet-bbpress-addon.css' );
		}
		else {
			$this->load_file( self::slug . '-script', '/assets/js/frontend/mailpoet-bbpress-addon' . MAILPOET_BBPRESS_ADDON_SCRIPT_MODE . '.js', true );

			// Stylesheet
			$this->load_file( self::slug . '-style', '/assets/css/mailpoet-bbpress-addon.css' );

			// Variables for JS scripts
			wp_localize_script( self::slug . '-script', 'mailpoet_bbpress_addon_params', apply_filters( 'mailpoet_bbpress_addon_params', array(
				'plugin_url' => $this->plugin_url(),
				)
			) );

		} // end if/else
	} // end register_scripts_and_styles

	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 *
	 * @access private
	 */
	private function load_file( $name, $file_path, $is_script = false, $support = array(), $version = '' ) {
		$url = $this->plugin_url() . $file_path;
		$file = $this->plugin_path() . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, $support, $version );
				wp_enqueue_script( $name );
			}
			else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file

} // end class

} // end if class exists

/**
 * Returns the main instance of MailPoet_bbPress_Addon to prevent the need to use globals.
 *
 * @return 
 */
function MailPoet_bbPress_Addon() {
	return MailPoet_bbPress_Addon::instance();
}

// Global for backwards compatibility.
$GLOBALS['mailpoet_bbpress_addon'] = MailPoet_bbPress_Addon();

?>