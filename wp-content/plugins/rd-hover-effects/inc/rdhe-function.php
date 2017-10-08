<?php

class Rdhe_functions {
	public function __construct() {
		self::settings_options_panel();

		// create Custom Post with This Hook
		add_action( 'init', array( $this, 'rd_hover_effects_custom_post' ) );
	}

	//Option Settings Functions
	public static function settings_options_panel() {
		include( RDHE_DIR . "/options/codestar-framework.php" );

		if ( class_exists( 'CSF' ) ) {
			require_once( RDHE_DIR . '/options/samples/metabox.sample.php' );
		}
	}
	// Register Post Type
		public function rd_hover_effects_custom_post() {
			register_post_type( 'rd-hover', array(
				'labels'        => array(
					'name'          => __( 'RD Hov Effect', 'rdhov' ),
					'singular_name' => __( 'Hover Effect', 'rdhov' ),
					'add_new_item'  => __( 'Add new hover item', 'rdhov'),
				),
				'public'        => true,
				'supports'      => array( 'title' ),
				'has_archive'   => true,
				'rewrite'       => array( 'slug' => 'hover-effects' ),
				'menu_icon'     => 'dashicons-format-image',
				'menu_position' => 20,
			) );
		}


	}
	new Rdhe_functions();

//RD Hov Function
include( "shortcode.php" );

// RD shortcode Button
include ( "shortcode-button.php");

