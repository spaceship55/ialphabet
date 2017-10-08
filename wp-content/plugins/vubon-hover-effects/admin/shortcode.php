<?php


// Register Shortcode
function vubon_short_ganerator_eff($atts){
	extract( shortcode_atts( array(

		'category' => '',	
		'style' => '',		
		
	), $atts) );
	
	
	    $q = new WP_Query(
        array('posts_per_page' => -1, 'post_type' => 'amaz-hover', 'amaz_cat' => $category)
        );
	
		while($q->have_posts()) : $q->the_post();
		$id = get_the_ID();	
		
	
		$subtlemeta = vp_metabox('amzhovpck.amaz-hover', false);
		
	$i = 0;

		$output = '<div class="content"><div class="grid">';
		
		foreach ($subtlemeta as $info) {	

		if ($style == 'style1') {
		
		$output .= '
					<vubon_figure class="effect-lily" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<div>
								<h2>'.$info['title'].'</h2>
								<p>'.$info['desc'].'</p>
							</div>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		
		else if ($style == 'style2') {
		
		$output .= '
					<vubon_figure class="effect-sadie" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<h2>'.$info['title'].'</h2>
							<p>'.$info['desc'].'</p>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		else if ($style == 'style3') {
		
		$output .= '
					<vubon_figure class="effect-layla" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<h2>'.$info['title'].'</h2>
							<p>'.$info['desc'].'</p>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		else if ($style == 'style4') {
		
		$output .= '
					<vubon_figure class="effect-oscar" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<h2>'.$info['title'].'</h2>
							<p>'.$info['desc'].'</p>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		else if ($style == 'style5') {
		
		$output .= '
					<vubon_figure class="effect-marley" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<h2>'.$info['title'].'</h2>
							<p>'.$info['desc'].'</p>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		
		else if ($style == 'style6') {
		
		$output .= '
					<vubon_figure class="effect-ruby" id="vubon_bor_box">
						<img src="'.$info['imgup_1'].'"/>
						<vubon_figcaption>
							<h2>'.$info['title'].'</h2>
							<p>'.$info['desc'].'</p>
							<a href="#"></a>
						</vubon_figcaption>			
					</vubon_figure>
				';
		}
		else if ($style == 'style7' || 'style8' || 'style9' || 'style10' || 'style11' || 'style12' || 'style13' || 'style14' || 'style15' || 'style16' || 'style17' || 'style18' || 'style19' || 'style20' || 'style21' || 'style22') {
		$output .= '
<h2 style="color: red;">This Feature Only For Pro Version. If you want to buy Please <a style="color: blue;" href="http://codecans.com/items/vubon-hover-effects-pro/">Click Here</a></h2>
				';
		}				
		$i++;
	}
	endwhile;
	$output .='</div></div>';
	wp_reset_query();
	return $output;
}

add_shortcode('amzhover', 'vubon_short_ganerator_eff');

?>