<?php



	// if uninstall.php is not called by WordPress, die
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			die;
		}
	

		global $wpdb;
	


		// Create dud object for loading options and internal vars:
		
		class codebard_p8_dud_object {
			
			public $internal = array(
	
			// Holds internal and generated vars. Never saved.

			);
			public $opt = array(
	
			// Holds internal and generated vars. Never saved.

			);
			public $hardcoded = array(
	
			// Holds hardcoded vars. Never saved.

			);
			
					
			public function __construct() 
			{
			
				require_once('core/includes/default_internal_vars.php');
				require_once('plugin/includes/default_internal_vars.php');
				require_once('plugin/includes/hardcoded_vars.php');
					
			
			}
		}	
		$codebard_p8 = new codebard_p8_dud_object;
		
		// Include internal vars from file:
		
		
		// Get options 
		
		$codebard_p8->opt=get_option($codebard_p8->internal['prefix'].'options');		

		if($codebard_p8->opt['delete_options_on_uninstall']=='yes')
		{
			$wpdb->query( "DELETE FROM ".$wpdb->options." WHERE option_name LIKE '".$codebard_p8->internal['id']."_%';");
		
		}
	
		if($codebard_p8->opt['delete_data_on_uninstall']=='yes')
		{
			
			foreach($codebard_p8->internal['tables'] as $key => $value)
			{
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix.$codebard_p8->internal['id']."_".$key.";");
				
			}
			foreach($codebard_p8->internal['meta_tables'] as $key => $value)
			{
				
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix.$codebard_p8->internal['id']."_".$key.";");
				
			}
			
			// Remove wordpress posts
			
			// Get posts first:
	
			$results = $wpdb->get_results( "SELECT ID FROM ".$wpdb->posts." WHERE post_type = '".$codebard_p8->internal['id']."_ticket';",ARRAY_A);
			
			foreach($results as $key => $value)
			{
				$post_id = $results[$key]['ID'];
				
				// Delete post meta
				
				$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE post_id = '".$post_id."';");	
				
				// Delete post 
				
				$wpdb->query( "DELETE FROM ".$wpdb->posts." WHERE ID = '".$post_id."';");	
				
				
			}
			
			// Delete custom taxonomy
						
			// Delete terms
			$wpdb->query( "
				DELETE FROM
				".$wpdb->terms."
				WHERE term_id IN
				( SELECT * FROM (
					SELECT ".$wpdb->terms.".term_id
					FROM ".$wpdb->terms."
					JOIN ".$wpdb->term_taxonomy."
					ON ".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id
					WHERE taxonomy = '".$codebard_p8->internal['id']."_support'
				) as T
				);
			" );

			// Delete taxonomies
			$wpdb->query( "DELETE FROM ".$wpdb->term_taxonomy." WHERE taxonomy = '".$codebard_p8->internal['id']."_support'" );

			
		}
		

?>