<?php



return array(
	array(
		'type'      => 'group',
		'repeating' => true,
		'sortable'  => true,
		'name'      => 'amaz-hover',
		'priority'  => 'high',
		'title'     => __('Vubon Hover Item', 'vp_textdomain'),
		'fields'    => array(
		
						array(
			'type' => 'notebox',
			'name' => 'nb_1',
			'label' => __('Author Comment', 'vp_textdomain'),
			'description' => __('If you want to enable awesome features, you need to buy pro version from here <a target="_blank" href="http://codecans.com/items/vubon-hover-effects-pro/">Vubon Hover Effects Pro</a>', 'vp_textdomain'),
			'status' => 'error',
				),
		
			 array(
				'type' => 'upload',
				'name' => 'imgup_1',
				'label' => __('Upload Image', 'vp_textdomain'),
			),
		
		 array(
			'type' => 'textbox',
			'name' => 'title',
			'label' => __('Enter your title', 'vp_textdomain'),
			//'description' => __('Only **alphabets** allowed here.', 'vp_textdomain'),
			'default' => 'Title Here',
			),

			array(
			'type' => 'textarea',
			'name' => 'desc',
			'label' => __('Enter your description', 'vp_textdomain'),
			//'description' => __('Only **alphabets** allowed here.', 'vp_textdomain'),
			'default' => 'Enter your Description',
			),
			
			array(
			'type' => 'textbox',
			'name' => 'link',
			'label' => __('Enter Image URL <br/><a href="http://codecans.com/items/vubon-hover-effects-pro/" style="color: red;">Pro Only</a>', 'vp_textdomain'),
			//'description' => __('Only **alphabets** allowed here.', 'vp_textdomain'),
			'default' => '#',
			),	
			

		),
	),
);

/**
 * EOF
 */