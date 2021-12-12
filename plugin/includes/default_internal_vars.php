<?php

$this->internal = array_replace_recursive(
	
	
	$this->internal,
	
	array(
		
		'id' => 'cb_p8',
		'plugin_id' => 'dspress',
		'prefix' => 'cb_p8_',
		'version' => '1.0.0',
		'plugin_name' => 'DSPress for BitClout, Diamond & DeSo',
		
		'callable_from_request' => array(
			
			'save_settings' => 1,
			'reset_languages' => 1,
			'save_language' => 1,
			'choose_language' => 1,
			'do_setup_wizard' => 1,
			
			'save_license' => 1,
		
			'ignore the ones after this line they were allowed for development!' => 1,
			
		),
			
		
		'do_log' => false,
		
		'calllimits' => array(
		
			'add_admin_menu'=>1,
		),		
		
		'callcount' => array(
		
		),		
		
		'tables'=> array(


		),	
		'data'=> array(


		),	
		

		'meta_tables'=> array(

						
		),	
		
		
		'admin_tabs' => array(
		
			'dashboard'=>array(
				
			),
			'quickstart'=>array(
				
				
			),
			'post_button'=>array(
				
				
			),
			'sidebar_widgets'=>array(
				
				
			),
			'languages'=>array(
				
				
			),
			'addons'=>array(
				
				
				
			),
			'support'=>array(
				
				
			),
		
		
		
		),
		
		'addons' => array(
		
		
		
		
		
		),
		'template_parts' => array(
			'content' => '',
		),
	
	)
	
);

?>