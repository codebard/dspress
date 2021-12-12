<?php


$this->opt = array_replace_recursive(

	$this->opt,

	array(
	
		'quickstart'=> array(
		
			'site_account' => 'Delete this and enter your BitClout/DiamondApp/DeSo profile link here',
			'redirect_url'=>'',	
			'site_support_message'=>'Buy {sitename} at {node}!',
			'open_new_window'=>'no',
			'button_type'=>'buy_button',
			'force_site_button'=>'no',
			'old_button'=>'no',
			'custom_button'=>'',
			'user_name'=>'',
			'node_name'=>'BitClout',
			'node_host'=>'bitclout.com',
			'force_profile_link'=>'no',
			'button_color'=>'#097efc',
			'button_hover_color'=>'#006bde',
			'button_text_color'=>'#ffffff',

		),
			
		'post_button'=> array(
			
			'show_button_under_posts'=>'yes',
			'append_to_content_order'=>'99',
			'show_message_over_post_button'=>'yes',
			'message_over_post_button_font_size'=>'24px',
			'insert_text_align'=>'center',
			'insert_margin'=>'15px',
			'message_over_post_button'=>'Liked it? Buy {coin} at {node} to support {authorname}!',
			'message_over_post_button_follow'=>'Liked it? Follow {coin} at {node} to support {authorname}!',
			'message_over_post_button_margin'=>'10px',
			'button_margin'=>'10px',
		),
		'sidebar_widgets'=> array(
			'hide_site_widget_on_single_post_page'=>'no',
			'insert_text_align'=>'center',
			'message_font_size'=>'18px',
			'message_over_post_button_margin'=>'10px',
			'button_margin'=>'10px',
		),
		'extras'=> array(
				
			'insert_text_align'=>'center',
		),
		'support'=> array(
				
			'insert_text_align'=>'center',
		),
			
		'template'=> 'default',
		'assign_tickets_to_admins'=> 'yes',
		'send_ticket_update_email_notification_to_users'=> 'yes',
		'send_ticket_update_email_notification_to_staff'=> 'yes',

		'setup_done' => false,
		'pro_pitch_done' => false,
		'setup_being_done' => false,
		'pro_pitch_being_done' => false,
		

)
);


?>