<?php

class RD_shortcodes {

	public function __construct() {
		add_shortcode( 'hover', array( $this, 'rd_hover_effects_shortcode' ) );
	}

	// Register Shortcode
	public function rd_hover_effects_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => '',
		), $atts ) );
		$q = new WP_Query( array( 'posts_per_page' => - 1, 'post_type' => 'rd-hover', 'p' => $id ) );
		while ( $q->have_posts() ) : $q->the_post();
			$id = get_the_ID();

			$options = get_post_meta( $id, 'hover_options', true );
			$groups  = $options['hover_group_option'];

			// MetaBox Value
			include( 'metabox-value.php' );
			$output = '';
			$output .= '<style type="text/css">
						' . $custom_css . '
						</style>';
			if ( $layout == "square" || $layout == "circle" ) {
				$output .= '<div class="main-wrapper rd-container"><div class="rd-row"><div id="' . $style . '"><ul>';
			}

			//if Caption Effects Wrapper
			require "caption-wrapper.php";

			//$item = 0;
			$effects = ""; // Temporary Set to avoid Error
			if ( is_array( $groups ) && count( $groups ) >= 1 ):
				foreach ( $groups as $info ) {

					if ( isset( $info['image'] ) ) {
						$image = $info['image'];
						$image = wp_get_attachment_image_src( $image, 'full' );
					}

					if ( $image == "" ) {

						$image[0] = RDHE_URL . "/image/dummy-image.jpg";
					}

					$link_open = "";
					if ( isset( $info["link_open"] ) ) {
						$link_open = $info["link_open"];
					}

					// Button Set
					$is_button = "";
					$link_open = "";
					$link = "";

					/*
					if ( isset( $info["is_button"] ) ) {
						$is_button = $info["is_button"];
					}
					// Link Open
					$link_open == 1 ? $link_open = "_blank" : $link_open = "";
					$is_button == 1 ? $is_button = "true" : $is_button = "false";*/

					// Square Effects Shortcode
					require "square.php";

					// Square2 Effects Shortcode
					require "square2.php";

					/*
					if ( $is_button == "true" ) {
						$link = $info['link'];
						$link = ' target="' . $link_open . '" href="' . $link . '"';
					} else {
						$link = '';
					}*/

					//Circle Effects Shortcode
					require "circle.php";

					// Caption Effects Shortcode
					require "caption.php";

					//$item++;
				}
			else:
				echo "You Didn't Add Hover Items in Settings Panel Please Add Hover Items First.";
			endif;

			// Custom CSS Responsive
			//if ($item == 3){
			require "custom-responsive-css.php";
			//}

		endwhile;
		if ( $layout == "square" || $layout == "circle" ) {
			$output .= '</ul></div></div></div>';
		}
		if ( $layout == "caption" ) {
			$output .= '</div>';
		}
		wp_reset_query();

		return $output;
	}
}

// Shortocde Define
function rdhe_shortcode_method_define() {
	new RD_shortcodes;
}

?>