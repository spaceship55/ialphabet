<?php
/*
Plugin Name: RD Hover Effects
Plugin URI: https://wordpress.org/plugins/rd-hover-effects/
Description: RD Hover Effects WordPress Plugin is an impressive hover effects powered by pure CSS3. Easy to use the beautiful amazing Image Hover Effects for your website.
Author: codecans
Author URI: http://codecans.com/
License: GPL2
Version: 4.7.9
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Rdhe_Default_func {
	public static function rdhe_def_func() {
		// Wp Version Check
		if ( ! function_exists( 'rdhe_wp_checkup' ) ) {
			function rdhe_wp_checkup() {
				$version = '3.8';
				global $wp_version;
				if ( version_compare( $wp_version, $version, '>=' ) ) {
					return "true";
				} else {
					return "false";
				}
			}
		}
		/* Define Constant */
		define( 'RDHE_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
		define( 'RDHE_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
	}
}


class RD_Hover_Effects_Panel extends Rdhe_Default_func {

	public function __construct() {

		// Default Method
		self::rdhe_def_func();

		// Admin Enqueue
		add_action( 'wp_enqueue_scripts', array( $this, 'rdhe_frontend_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'rdhe_admin_enqueue' ) );

		// Support For Widgets
		add_filter( 'widget_text', 'do_shortcode' );
	}

	public function rdhe_frontend_enqueue() {
		wp_enqueue_style( 'stylesheet_css', plugins_url( '/css/style_com.css', __FILE__ ) );
		wp_enqueue_style( 'rdhe-square', plugins_url( '/css/square.css', __FILE__ ) );
		wp_enqueue_style( 'rdhe-circle', plugins_url( '/css/circle.css', __FILE__ ) );
		wp_enqueue_style( 'rdhe-other', plugins_url( '/css/other.css', __FILE__ ) );
		wp_enqueue_style( 'rdresponsive_css', plugins_url( '/css/rdresponsive.css', __FILE__ ) );
		wp_enqueue_style( 'rdhe-square2', plugins_url( '/css/square2.css', __FILE__ ) );
		wp_enqueue_style( 'rdhe-caption', plugins_url( '/css/caption.css', __FILE__ ) );
		wp_enqueue_script( 'caption_modern', plugins_url( '/js/modernizr.custom.js', __FILE__ ) );
	}

	// admin Enqueue Script
	public function rdhe_admin_enqueue() {

		if ( is_admin() ) {
			wp_enqueue_style( "rdhe-copybutton", RDHE_URL . "/css/rdhe-copybutton.css", false, "1.0", "all" );
			//wp_enqueue_style( 'rdhe-master-fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', __FILE__ );
			wp_enqueue_style( "rdhe-admin-stylesheet", RDHE_URL . "/css/admin.css", false, "1.0", "all" );
			wp_enqueue_script( 'rdhe-admin-javascript', RDHE_URL . '/js/admin.js', false );
		}

	}
}

new RD_Hover_Effects_Panel;

//RD Hov Function
include "inc/rdhe-function.php";

// Define Shortcodes
rdhe_shortcode_method_define();

class Rdhe_Admin_notice {
	public function __construct() {
		/* Register activation hook. */
		register_activation_hook( __FILE__, array( $this, 'rdhe_admin_notice_example_activation_hook' ) );
		add_action( 'admin_notices', array( $this, 'rdhe_admin_notice_example_notice' ) );
	}

	public function rdhe_admin_notice_example_activation_hook() {

		/* Create transient data */
		set_transient( 'fx-admin-notice-example', true, 5 );
	}


	public function rdhe_admin_notice_example_notice() {
		/* Check transient, if available display notice */
		if ( get_transient( 'fx-admin-notice-example' ) ) {
			?>
			<div class="updated notice is-dismissible" id="afwp-wrap">
				<div class="awfp-profile-pic">
					<img src="http://codecans.com/images/codecans.jpg"
					     alt="codecans-profile">
				</div>
				<div class="afwp-content">
					<p>This is Rakibur Rahman Sagar From Codecans. I am a PHP
						Developer, I have been developed wordpress plugin since
						4 years. Please	rate this plugin to inspire me to make more amazing wordpress plugin</p>
					<ul>
						<li><a target="_blank" href="https://wordpress.org/support/plugin/rd-hover-effects/reviews/?filter=5">Rate This Plugin</a>
						<li id="afwp_click"><a href="">Remind Me later</a>
					</ul>
				</div>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient( 'fx-admin-notice-example' );
		}
	}
}
new Rdhe_Admin_notice;
?>