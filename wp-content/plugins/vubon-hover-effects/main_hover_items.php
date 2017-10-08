<?php
/*
Plugin Name: Vubon Hover Effects Wordpress
Plugin URI: https://wordpress.org/plugins/vubon-hover-effects/
Description: Vubon Hover Effects Wordpress allows you to add animation effects to images, when it hovered. Few single effects and more combinations to boost creativity. All effects simulate flash and javascript with pure CSS3 . Each effect on each css and html files.
Author: codecans
Author URI: http://codecans.com/
Version: 3.2.9
*/

// Loading VafPress Framework
if ( ! class_exists( 'VP_Amazimagehov' ) ) {
	defined( 'VP_AOVPACK_VERSION' ) or define( 'VP_AOVPACK_VERSION', '2.0' );
	defined( 'VP_AOVPACK_URL' ) or define( 'VP_AOVPACK_URL', plugin_dir_url( __FILE__ ) );
	defined( 'VP_AOVPACK_DIR' ) or define( 'VP_AOVPACK_DIR', plugin_dir_path( __FILE__ ) );
	defined( 'VP_AOVPACK_FILE' ) or define( 'VP_AOVPACK_FILE', __FILE__ );
// Looding Bootstrap
	require 'framework/bootstrap.php';
	
//Loading CSS
	function vubon_stylesheet_effects_csjs() {
		wp_enqueue_style( 'main_css_file', plugins_url( '/css/vub_dem.css', __FILE__ ) );
		wp_enqueue_style( 'main1_css_file', plugins_url( '/css/normalize.css', __FILE__ ) );
		wp_enqueue_style( 'main2_css_file', plugins_url( '/css/style_one.css', __FILE__ ) );
	}

	add_action( 'wp_enqueue_scripts', 'vubon_stylesheet_effects_csjs' );
	
// add widgets
	add_filter( 'widget_text', 'do_shortcode' );

}


function vubon_admin_enqueue_add_init() {
    if ( is_admin() ) {
		wp_enqueue_style( 'vubon_master_fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', __FILE__ );
    }
}
add_action( 'admin_enqueue_scripts', 'vubon_admin_enqueue_add_init' );


// Registering Custom Post type
function vubon_hov_effects_ecpt_rd() {
	register_post_type( 'amaz-hover',
		array(
			'labels'        => array(
				'name'          => __( 'Vubon Hov Effects' ),
				'singular_name' => __( 'Hover Effect' ),
				'add_new_item'  => __( 'Add new hover item' )
			),
			'public'        => true,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'hover-effects' ),
			'menu_icon'     => '',
			'menu_position' => 21,
		)
	);

}

add_action( 'init', 'vubon_hov_effects_ecpt_rd' );
// Registering Custom post's taxonomy category
function vubon_ect_category_sup_rd() {
	register_taxonomy(
		'amaz_cat',
		'amaz-hover',
		array(
			'hierarchical'      => true,
			'label'             => 'Vubon Category',
			'query_var'         => true,
			'show_admin_column' => true,
			'rewrite'           => array(
			'slug'       => 'vubon-category',
			'with_front' => true
			)
		)
	);
}
add_action( 'init', 'vubon_ect_category_sup_rd' );

require 'admin/icon.php';

// Loading Metaboxes
new VP_Metabox( array
(
	'id'       => 'amzhovpck',
	'types'    => array( 'amaz-hover' ),
	'title'    => __( 'Hover Information ', 'vp_textdomain' ),
	'priority' => 'high',
	'template' => VP_AOVPACK_DIR . '/admin/metabox/main.php'
) );

//calling Shortcode
require_once( VP_AOVPACK_DIR . 'admin/shortcode.php' );

//Shortcode Generator
function vubon_subtle_shortcodegenerator() {
	$vubtmpl_sg = array(
		'name'         => 'sg_2',
		'template'     => VP_AOVPACK_DIR . '/admin/shortcode_generator.php',
		'modal_title'  => __( 'Vubon Shortcode Setting', 'vp_textdomain' ),
		'button_title' => __( 'Vubon Shortcode', 'vp_textdomain' ),
		'types'        => array( 'post', 'page' ),
		'main_image'   => VP_AOVPACK_URL . 'images/icon_short.png',
		'sprite_image' => VP_AOVPACK_URL . 'images/icon_short.png',
	);
	$vubonsg = new VP_ShortcodeGenerator( $vubtmpl_sg );
}
add_action( 'after_setup_theme', 'vubon_subtle_shortcodegenerator' );
?>