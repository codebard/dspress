<?php


class codebard_p8_plugin extends codebard_p8_core
{
	public function plugin_construct()
	{

		add_action('init', array(&$this, 'init'));
		
		add_action('upgrader_process_complete', array(&$this, 'upgrade'),10, 2);
					
		register_activation_hook( __FILE__, array(&$this,'activate' ));
		
		register_deactivation_hook(__FILE__, array(&$this,'deactivate'));
		
		if(is_admin())
		{
			add_action('init', array(&$this, 'admin_init'));
		}
		else
		{
			add_action('init', array(&$this, 'frontend_init'),99);
		}
		add_action( 'wp_head', array( &$this, 'styles_in_head' ) );
		add_action( 'init', array( &$this, 'check_plugin_activation_date_for_existing_installs' ) );
		add_action('admin_init',array(&$this,'check_redirect_to_setup_wizard'),99);
		add_filter( 'cb_p8_filter_vars_before_save_settings', array( &$this, 'filter_settings' ),60);
		
	}
	public function add_admin_menus_p()
	{
		
		add_menu_page( $this->lang['admin_menu_label'], $this->lang['admin_menu_label'], 'administrator', 'settings_'.$this->internal['id'], array(&$this,'do_settings_pages'), $this->internal['plugin_url'].'images/admin_menu_icon.png', 86 );
		add_submenu_page( null, 'Buy Button & Widgets For DeSo Admin Message', 'Admin message', 'manage_options', $this->internal['id'] . 'admin_message', array( &$this, 'admin_message_page' ) );
		add_submenu_page( '', 'DSPress Setup Wizard', 'DSPress Setup Wizard', 'administrator', 'cb_p8_setup_wizard', array(&$this, 'do_setup_wizard') );
		
		
	}
	public function admin_init_p() {
		
		// add_action( 'admin_enqueue_scripts',  array(&$this, 'load_pointers' ) );
		// add_filter( $this->internal['prefix'].'admin_pointers-dashboard', array( &$this, 'widgets_pointer' ) );
		add_action( 'cb_p8_action_before_do_admin_page_tabs', array( &$this, 'pro_pitch' ) );
		add_action( 'wp_ajax_cb_p8_dismiss_admin_notice', array( $this, 'dismiss_admin_notice' ), 10, 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ), 10, 1 );
		
		
		

		/* Old Widget notice  - can be used to show new notices.
	   if(!isset($this->opt['widget_update_notice_shown']) AND !$this->opt['setup_is_being_done']) {
			$this->queue_notice($this->lang['updated_widgets_notice'],'info','widget_update_notice','perma',true);	
			$this->opt['widget_update_notice_shown']=true;
			$this->update_opt();
	   }
		*/
		// Do setup wizard if it was not done
		if( isset( $this->opt['setup_is_being_done'] ) AND $this->opt['setup_is_being_done'] )
		{
			add_action($this->internal['prefix'].'action_before_do_settings_pages',array(&$this,'do_setup_wizard'),99,1);
		}
		
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		
	}
	public function frontend_init_p()
	{
		
	}
	public function init_p()
	{
	
		// Below function checks the request in any way necessary, and queues any action/filter depending on request. This way, we avoid filtering content or putting any actions in pages or operations not relevant to plugin
				
		add_action( 'wp', array(&$this, 'route_request'));
		
		add_action( 'template_redirect', array(&$this, 'template_redirections'));
		
		$upload_dir = wp_upload_dir();
		
		$this->internal['attachments_dir'] = $upload_dir['basedir'] . '/'.$this->internal['prefix'].'ticket_attachments/';
	
		$this->internal['attachment_url'] =  $upload_dir['baseurl'] . '/'.$this->internal['prefix'].'ticket_attachments/';
		
		// Get relative attachment dir/url :
		
		$this->internal['attachment_relative_url']=substr(wp_make_link_relative($upload_dir['baseurl']),1).'/'.$this->internal['prefix'].'ticket_attachments/';
		
		
		$this->internal['plugin_update_url'] =  wp_nonce_url(get_admin_url().'update.php?action=upgrade-plugin&plugin='.$this->internal['plugin_id'].'/index.php','upgrade-plugin_'.$this->internal['plugin_id'].'/index.php');
		

		add_action( 'show_user_profile', array(&$this, 'add_custom_user_field') );
		add_action( 'edit_user_profile', array(&$this, 'add_custom_user_field') );

		add_action( 'personal_options_update', array(&$this, 'save_custom_user_field') );
		add_action( 'edit_user_profile_update', array(&$this, 'save_custom_user_field') );
		
		
		
	}
	public function load_options_p()
	{
		// Initialize and modify plugin related variables
		

		return $this->internal['core_return'];
		
	}

	public function title_filters_p($title)
	{
		global $post;

		
		return $title;
	}
	public function content_filters_p($wordpress_content)
	{
		global $post;
	
		
		if(is_singular() AND isset($this->opt['post_button']['show_button_under_posts']) AND $this->opt['post_button']['show_button_under_posts']=='yes')
		{
			
			$wordpress_content = $this->append_to_content($wordpress_content,$this->opt['post_button']['append_to_content_order']);
			return $wordpress_content;
		}
	
		return $wordpress_content;
	}
	public function template_redirections_p($link)
	{
		global $post;

		return $link;
	}
	public function setup_languages_p()
	{
		// Here we do plugin specific language procedures. 
		
		// Set up the custom post type and its taxonomy slug into options:
		
		$current_lang=get_option($this->internal['prefix'].'lang_'.$this->opt['lang']);
		
		// Get current options

		$current_options=get_option($this->internal['prefix'].'options');

		$current_options['ticket_post_type_slug']=$current_lang['ticket_post_type_slug'];
		$current_options['ticket_category_slug']=$current_lang['ticket_post_type_category_slug'];
		
		// Update options :
		
		update_option($this->internal['prefix'].'options',$current_options);
		
		// Set current options the same as well :
		
		$this->opt=$current_options;
		
	}
 	public function activate_p()
	{
		// Not setting the default return to 0 like the one in init check here because we dont want to overwrite the value 0 for installs existing at the date this code was implemented
		$plugin_first_activated   = get_option( 'cb_p8_first_activated', false );
				
		if ( !$plugin_first_activated ) {
			
			update_option( 'cb_p8_first_activated', time() );
		
		}
	}

	public function check_redirect_to_setup_wizard_p()
	{

		if(!is_user_logged_in())
		{
			return;
		}
		// If setup was not done, redirect to wizard
		if($this->opt['quickstart']['site_account']=='Delete this and enter your BitClout/Diamond/DeSo profile link here' OR (!$this->opt['setup_done'] AND !isset($_REQUEST['setup_stage'])) )
		{

			if ( $this->internal['redirected_to_setup_once'] ) {
				return;
			}
			$this->opt['setup_is_being_done']=true;
			$this->update_opt();
			
			// The below flag will allow hiding notices or avoiding recursive redirections 
			update_option( 'cb_p8_is_being_done', true );
			
			// Toggle redirect flag off. If the user skips the wizard we will just show a notice in admin and not force subsequent redirections
			update_option( 'cb_p8_redirect_to_setup_wizard', false );

			wp_redirect( admin_url( 'admin.php?page=cb_p8_setup_wizard&setup_stage=0') );
			
			
			return;
				
		}
		/*
		// If setup was not done, redirect to wizard
		if(!$this->opt['pro_pitch_done'] AND !isset($_REQUEST['setup_stage']))
		{
		
			$this->opt['setup_is_being_done']=true;
			$this->update_opt();
			
			// $this->queue_modal('pro_pitch');
			
		}
		*/
		
	}
	public function enqueue_frontend_styles_p()
	{
		wp_enqueue_style( $this->internal['id'].'-css-main', $this->internal['template_url'].'/'.$this->opt['template'].'/style.css' );
	}
	public function enqueue_admin_styles_p()
	{
		$current_screen=get_current_screen();

		if($current_screen->base=='toplevel_page_settings_'.$this->internal['id'] OR ( isset( $_REQUEST['page']) AND $_REQUEST['page']== 'cb_p8_install_pw' ) )
		{
			wp_enqueue_style( $this->internal['id'].'-css-admin', $this->internal['plugin_url'].'plugin/includes/css/admin.css' );
			
		}
		if( $_REQUEST['setup_stage'] == '1' )
		{
			wp_enqueue_style( $this->internal['id'].'-css-mailchimp', 'https://cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7_dtp.css' );
			
		}
	}
	public function enqueue_frontend_scripts_p()
	{
	
	
	
		
	}	
	public function enqueue_admin_scripts_p()
	{
	
		// This will enqueue the Media Uploader script
		wp_enqueue_media();	
		wp_enqueue_script( $this->internal['id'].'-js-admin', $this->internal['plugin_url'].'plugin/includes/scripts/admin.js' );	
		
		
	}	
	public function route_request_p()
	{
		global $post;
		
		$current_term = get_queried_object();
		$current_user = wp_get_current_user();
		
		// Placeholder queuer
		
		// Support desk main page. Queue content filter or any necessary function
		
		$this->queue_content_filters();

		
			
		
		
	}
	public function queue_title_filters_p()
	{
		// This function is a wrapper for queueing content filter
		
		if(!isset($this->internal['title_filter_queued']))
		{
			$this->internal['title_filter_queued']=true;
			add_filter('the_title', array(&$this, 'title_filters'));		
		}
	}
	public function queue_content_filters_p()
	{
		// This function is a wrapper for queueing content filter
		
		if(!isset($this->internal['content_filter_queued']))
		{
			$this->internal['content_filter_queued']=true;
			add_filter('the_content', array(&$this, 'content_filters'));
		}
	}
	public function choose_language_p($v1)
	{
		
		// Check if language was successfully changed and hook to create pages if necessary:
		if($this->internal['core_return'])
		{
			add_action( 'admin_init', array(&$this, 'check_create_pages'));
		}
	}
	public function do_setup_wizard_p($v1)
	{

			$this->internal['setup_is_being_done']=true;
			
			// No setup was done in this install. do setup
			if(isset($_REQUEST['setup_stage']) AND $_REQUEST['setup_stage']=='0')
			{
				$this->opt['redirected_to_setup_once'] = true;
				$this->update_opt();
				require($this->internal['plugin_path'].'plugin/includes/setup_0.php');
		
			}

			if(isset($_REQUEST['setup_stage']) AND $_REQUEST['setup_stage']=='1')
			{
				
				require($this->internal['plugin_path'].'plugin/includes/setup_1.php');
		
			}
			return;
	
		// Return left here for future modifications
		return;

	}
	public function pro_pitch_p() {
		// This function displays pro pitch after page admin header
		
		if($this->check_addon_exists('patron_plugin_pro')=='notinstalled')
		{	
			/* Pro upsell
			echo '<div class="cb_p8_pro_pitch">';
			echo esc_html( $this->lang['cb_p8_a1_addon_available_header'] );
			echo '</div>';
			*/
		}
		
	}
	public function display_addons_p()
	{
		// This function displays addons from internal vars
		echo '<div class="cb_addons_list">';
		foreach($this->internal['addons'] as $key => $value)
		{
			echo  esc_html( $this->display_addon($key) );
			
		}
		echo '</div>';
		
		
	}
	public function display_addon_p($v1)
	{
		$addon_key=$v1;
		
		$addon=$this->internal['addons'][$addon_key];
		
		// This function displays a particular addon
	
		echo '<div class="cb_addon_listing">';
		echo '<div class="cb_addon_icon"><a href="'.  esc_url( $this->internal['addons'][$addon_key]['link'] ) .'" target="_blank"><img src="'. esc_url( $this->internal['plugin_url'] ).'images/'. esc_html( $addon['icon'] ).'" /></a></div>';echo '<div class="cb_addon_title"><a href="'.esc_url( $this->internal['addons'][$addon_key]['link'] ).'" target="_blank">'. esc_html( $this->lang['addon_'.$addon_key.'_title'] ).'</a></div>';
		echo '<div class="cb_addon_status">'.  esc_html( $this->check_addon_status($addon_key) ).'</div>';
		echo '</div>';
		
	}
	public function wrapper_check_addon_license_p($v1)
	{
		// Wrapper solely for the purpose of letting addons check their licenses
		
	}
	public function check_addon_status_p($v1)
	{
		// Checks addon status, license, and provides links if inecessary
		
		$addon_key = $v1;
		
		// Check if addon is active:
		
		if ( is_plugin_active( $this->internal['addons'][$addon_key]['slug'] ) ) 
		{
			//plugin is active
			
			echo  esc_html( $this->wrapper_check_addon_license($addon_key) );
			
		}
		else
		{
			// Check if plugin exists:
		
			if(file_exists(WP_PLUGIN_DIR.'/'.$this->internal['addons'][$addon_key]['slug']))
			{
			
				return $this->lang['inactive']; 
				
			}
			else			
			{
		
				// Not installed. 
				return '<a href="'.$this->internal['addons'][$addon_key]['link'].'" class="cb_get_addon_link" target="_blank">'.$this->lang['get_this_addon'].'</a>';
				
			}
			
		}
		
		
	}
	public function check_addon_exists_p($v1)
	{
		// Checks addon status, license, and provides links if inecessary
		
		$addon_key = $v1;
		
		// Check if addon is active:
		
		if ( is_plugin_active( $this->internal['addons'][$addon_key]['slug'] ) ) 
		{
			//plugin is active
			
			return 'active';
			
		}
		else
		{
			// Check if plugin exists:
			
			if(file_exists(WP_PLUGIN_DIR.'/'.$this->internal['addons'][$addon_key]['slug']))
			{
				
				return 'notactive';
				
			}
			else			
			{
				// Not installed. 
				return 'notinstalled';
				
			}
			
		}
		
		
	}
	public function add_custom_user_field_p($v1)
	{
		$user=$v1;
		?>
					
					<table class="form-table">
						<tr><th>
							<label for="address"><?php _e('Your BitClout/Diamond/DeSo profile', $this->internal['id']); ?>
							</label></th>
							<td>
								<input type="text" name="<?php echo  esc_attr( $this->internal['id'] );?>_bitclout_profile" id="<?php echo esc_attr( $this->internal['id'] );?>_bitclout_profile" value="<?php echo esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $user->ID ) ); ?>" class="regular-text" /><br />
									<span class="description"><?php _e('Enter your BitClout/Diamond/DeSo profile link here', esc_attr( $this->internal['id'] )); ?></span>
							</td>
						</tr>
					</table>
						
		<?php		
		
	}
	
	


	public function save_custom_user_field_p($v1)
	{
		$user_id=$v1;

		if ( !current_user_can( 'edit_user', $user_id ) ) $return = FALSE;
		
				

		$user_name = '';

		$profile_url = parse_url( filter_var($_POST[$this->internal['prefix'].'bitclout_profile'], FILTER_SANITIZE_URL ) );

		if ( $profile_url AND isset($profile_url['path']) AND isset($profile_url['host']) ) {
			$user_name = str_replace('/u/', '', $profile_url['path']);
		}
		// Check for any slashes in the name. If there are, then path is wrong


		$node_name = $profile_url['host'];

		if ( $profile_url['host']=='bitclout.com') {
			$node_name = 'BitClout';
		}
		if ( $profile_url['host']=='diamondapp.com') {
			$node_name = 'Diamond';
		}

		// Reconstruct url without req params
		if ( $profile_url['host'] != '') {
			$url = 'https://'. $profile_url['host'] . '/u/' . $user_name;
		}

		update_user_meta( $user_id, $this->internal['prefix'].'bitclout_profile', $url );
		update_user_meta( $user_id, $this->internal['prefix'].'user_name', $user_name );
		update_user_meta( $user_id, $this->internal['prefix'].'node_name', $node_name );
		update_user_meta( $user_id, $this->internal['prefix'].'node_url', $profile_url['scheme']. '://' . $profile_url['host'] );
		update_user_meta( $user_id, $this->internal['prefix'].'node_url', $profile_url['scheme']. '://' . $profile_url['host'] );
		
	}

	public function site_sidebar_widget_p($v1)
	{
		//************************Site Sidebar Widget**********************//
		$content=$v1;
		//if(in_array('get_the_excerpt', $GLOBALS['wp_current_filter']) OR 'post' !== get_post_type()) $return = $content;
			
		global $post;
		
		$get_url=get_permalink();
		$append = '';
		$append.='<div class="'.$this->internal['prefix'].'bitclout_site_widget" style="text-align:'.$this->opt['sidebar_widgets']['insert_text_align'].' !important;">';
		

		if($this->opt['quickstart']['redirect_url']=='')
		{
			$redirect=$get_url;
		
		}
		else
		{
			$redirect=$this->opt['quickstart']['redirect_url'];
		
		}	

		$user=$this->opt['quickstart']['site_account'];

		$profile_url = $this->make_bitclout_profile_url( false, 'site_sidebar_widget' );

		// Lets shove in the target=_blank if open in new window is set :
		
		if($this->opt['quickstart']['open_new_window']=='yes')
		{
			$new_window=' target="_blank"';
		
		}
	
		
		$button=$this->internal['plugin_url'].'images/'."become_a_patron_button.png";
		$max_width = 'min-content';
		
		
		if($this->opt['quickstart']['custom_button']!='')
		{
			$button=$this->opt['quickstart']['custom_button'];
			
			if($this->opt['quickstart']['custom_button_width']!='')
			{
				$max_width = $this->opt['quickstart']['custom_button_width'];
				
			}
			else
			{
				$max_width = '200';
			}
			
		}

		if($this->opt['quickstart']['open_new_window']=='yes')
		{
			$new_window=true;
		}
		else
		{
			$new_window=false;
		}

		if (filter_var($profile_url, FILTER_VALIDATE_URL) === FALSE) {
			if ( current_user_can( 'manage_options' ) ) {
				$button = 'BitClout/Diamond/DeSo button is not appearing because you have not saved your BitClout/Diamond/DeSo profile url in Button settings - click <a href="'. admin_url( 'admin.php?page=settings_cb_p8&cb_p8_tab=quickstart' ) .'">here</a> to save it. Only you as an admin can see this message.';
			}
			else {
				$button = '';
			}
		}
		else {
			
			if ( $this->opt['quickstart']['button_type']=='buy_button') {
				$button = $this->make_buy_button(false, $this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
			}
			if ( $this->opt['quickstart']['button_type']=='follow_button') {
				$button = $this->make_follow_button(false, $this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
				
			}
			
		}
		
		
		$append.=$button;
		
		$append.='</div>';

		return $append;


		//************************Site Sidebar Widget EOF*********************************//
		
	}


	public function return_plugin_name_p($v1)
	{
		// Wrapper to modify the plugin name when shown anywhere
		
		return $this->lang['plugin_name']; 
		
	}
	public function append_to_content_p($v1,$v2)
	{

		//************************APPEND TO CONTENT*********************************//
		
		$content=$v1;
		$append_to_content_order=$v2;
	
				
		if(in_array('get_the_excerpt', $GLOBALS['wp_current_filter']) OR 'page' == get_post_type() OR !is_singular() ) {
			
			return $content;
		
		}

		global $post;
			
		$get_url=get_permalink();
			

		// form array of items set to 1
		$append='<div class="'.$this->internal['prefix'].'bitclout_button" style="text-align:'.$this->opt['post_button']['insert_text_align'].' !important;margin-top:'.$this->opt['post_button']['insert_margin'].';margin-bottom:'.$this->opt['post_button']['insert_margin'].';">';
			

		if($this->opt['post_button']['show_message_over_post_button']=='yes')
		{
			$author_name=get_the_author_meta('display_name');
			

		$author_id=get_the_author_meta('ID');
		
			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'bitclout_profile', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );			
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );

			if($this->opt['quickstart']['force_site_button']=='yes')
			{
				$author_name = get_bloginfo( 'name', 'raw' );
			}

			$insert_message=str_replace('{authorname}',$author_name,$this->opt['post_button']['message_over_post_button']);
			$insert_message=str_replace('{coin}',$user_name,$insert_message);
			$insert_message=str_replace('{node}',$node_name,$insert_message);

			$append.='<div class="'.$this->internal['prefix'].'message_over_post_button" style="font-size:'.$this->opt['post_button']['message_over_post_button_font_size'].';margin-top:'.$this->opt['post_button']['message_over_post_button_margin'].';margin-bottom:'.$this->opt['post_button']['message_over_post_button_margin'].';">'.$insert_message.'</div>';
				
			
		}
		

		
		
		if($this->opt['quickstart']['force_site_button']=='yes' OR $user=='')
		{
			$user=$this->opt['quickstart']['site_account'];
		}
	
		
		$url = $this->make_bitclout_profile_url( $author_id, 'author_sidebar_widget' );


		if(isset($this->opt['quickstart']['custom_button']) AND $this->opt['quickstart']['custom_button']!='')
		{	
			$button=$this->opt['quickstart']['custom_button'];
			if($this->opt['quickstart']['custom_button_width']!='')
			{
				$max_width = $this->opt['quickstart']['custom_button_width'];
				
			}
			else
			{
				$max_width = '200';
			}
			
		}
		if(@$this->opt['quickstart']['open_new_window']=='yes')
		{
			$new_window=' target="_blank"';
			
		}
		else
		{
			$new_window='';
		}
		

		if($this->opt['quickstart']['force_site_button']=='yes')
		{
			$url = $this->make_bitclout_profile_url( false, 'author_sidebar_widget' );

			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				if ( current_user_can( 'manage_options' ) ) {
					$append.= 'BitClout/Diamond/DeSo button is not appearing because you have not saved your profile url in Button settings - click <a href="'. admin_url( 'admin.php?page=settings_cb_p8&cb_p8_tab=quickstart' ) .'">here</a> to save it. Only you as an admin can see this message.';
				}
				else {
					$append.= '';
				}
			}
			else {
				if ( $this->opt['quickstart']['button_type']=='buy_button') {
					$append .= $this->make_buy_button(false,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
				}
				if ( $this->opt['quickstart']['button_type']=='follow_button') {
					$append .= $this->make_follow_button(false,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
					
				}
			}
			
		}
		else
		{

			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				if ( current_user_can( 'manage_options' ) ) {
					$append.= 'This author has to set his or her BitClout/Diamond/DeSo profile url in his profile before widget can link to his profile. Additionally we can\'t show the site BitClout/Diamond/DeSo profile link in its place either because have not saved site profile url in Button settings - either one of them must be saved for the widget to show the link - click <a href="'. admin_url( 'admin.php?page=settings_cb_p8&cb_p8_tab=quickstart' ) .'">here</a> to save site profile name for site\'s BitClout/Diamond/DeSo. Only you as an admin can see this message.';
				}
				else {
					$append.= '';
				}
			}
			else {
				
				
				if ( $this->opt['quickstart']['button_type']=='buy_button') {
					$append .= $this->make_buy_button($author_id,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
				}
				if ( $this->opt['quickstart']['button_type']=='follow_button') {
					$append .= $this->make_follow_button($author_id,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
					
				}
			}

		}

		$append.='</div>';
		
		return $content.$append;

	}
	public function author_sidebar_widget_p($v1)
	{
		

		$content=$v1;
				
		//if(in_array('get_the_excerpt', $GLOBALS['wp_current_filter']) OR 'post' !== get_post_type()) $return = $content;
	
		global $post;
		
		$get_url=get_permalink();
		$append='';
		$append.='<div class="'.$this->internal['prefix'].'bitclout_author_widget" style="text-align:'.$this->opt['sidebar_widgets']['insert_text_align'].' !important;">';
		

		$author_id=get_the_author_meta('ID');

		if($this->opt['quickstart']['force_site_button']=='yes' OR $author_id=='' OR $author_id == 0 OR $author_id == false)
		{
			$author_id=false;
			
		}
		
		$url = $this->make_bitclout_profile_url( $author_id, 'author_sidebar_widget' );


		$button=$this->internal['plugin_url'].'images/'."become_a_patron_button.png";
		$max_width = 'min-content';
		
		if($this->opt['quickstart']['custom_button']!='')
		{
			$button=$this->opt['quickstart']['custom_button'];
			if($this->opt['quickstart']['custom_button_width']!='')
			{
				$max_width = $this->opt['quickstart']['custom_button_width'];
				
			}
			else
			{
				$max_width = '200';
			}
			
		}
		if($this->opt['quickstart']['open_new_window']=='yes')
		{
			$new_window=true;
		}
		else
		{
			$new_window=false;
		}

		if($this->opt['quickstart']['force_site_button']=='yes')
		{
			$url = $this->make_bitclout_profile_url( false, 'author_sidebar_widget' );

			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				if ( current_user_can( 'manage_options' ) ) {
					$button = 'BitClout/Diamond/DeSo button is not appearing because you have not saved your profile url in Button settings - click <a href="'. admin_url( 'admin.php?page=settings_cb_p8&cb_p8_tab=quickstart' ) .'">here</a> to save it. Only you as an admin can see this message.';
				}
				else {
					$button = '';
				}
			}
			else {
			
				if ( $this->opt['quickstart']['button_type']=='buy_button') {
					$button = $this->make_buy_button(false,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
				}
				if ( $this->opt['quickstart']['button_type']=='follow_button') {
					$button = $this->make_follow_button(false,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
					
				}
			}
		
		}
		else
		{

			if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
				if ( current_user_can( 'manage_options' ) ) {
					$button = 'This author has to set his or her BitClout/Diamond/DeSo vanity profile name in his profile before widget can link to his profile. Additionally we can\'t show the site BitClout/Diamond/DeSo profile link in its place either because have not saved site profile url in Button settings - either one of them must be saved for the widget to show the link - click <a href="'. admin_url( 'admin.php?page=settings_cb_p8&cb_p8_tab=quickstart' ) .'">here</a> to save site profile name for site\'s Patreon. Only you as an admin can see this message.';
				}
				else {
					$button = '';
				}
			}
			else {
					
				
				if ( $this->opt['quickstart']['button_type']=='buy_button') {
					$button = $this->make_buy_button($author_id,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
				}
				if ( $this->opt['quickstart']['button_type']=='follow_button') {
					$button = $this->make_follow_button($author_id,$this->opt['sidebar_widgets']['button_margin'],$max_width,$new_window);
					
				}
			}

		}
		
	
		$append.=$button;
		
		$append.='</div>';

		return $append;
		
	}
	public function author_sidebar_widget_message_p($message) {
	
		global $post;
		

		$author_id=get_the_author_meta('ID');

		if($this->opt['quickstart']['force_site_button']=='yes' OR !$author_id)
		{
			$site_name=$bloginfo = get_bloginfo( 'name', 'raw' );
			$message=str_replace('{authorname}',$site_name,$message);
			$message=str_replace('{node}',$this->opt['quickstart']['node_name'],$message);
			$message=str_replace('{coin}','$'.$this->opt['quickstart']['user_name'],$message);
			
		}
		else
		{
			$author_name=get_the_author_meta('display_name');

		
			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'bitclout_profile', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			
			
			$message=str_replace('{authorname}',$author_name,$message);
			$message=str_replace('{node}',$node_name,$message);
			$message=str_replace('{coin}','$'.$user_name,$message);
		}
		return $message;
		
	}
	
	public function site_sidebar_widget_message_p($message)
	{
	
		$site_name=$bloginfo = get_bloginfo( 'name', 'raw' );
		$message=str_replace('{sitename}',$site_name,$message);
		$message=str_replace('{node}',$this->opt['quickstart']['node_name'],$message);
		$message=str_replace('{coin}','$'.$this->opt['quickstart']['user_name'],$message);
		
		
		return $message;
		
	}
	
	public function make_to_bitclout_link_p($url, $button, $margin=10, $max_width=200, $new_window=false)
	{
		if($new_window)
		{
			$new_window = ' target="_blank"';
			
		}
		else
		{
			$new_window='';
		
		}
				
		
		if($this->opt['quickstart']['custom_button']!='') {
			
			return '<a rel="nofollow"'.$new_window.' href="'.$url.'"><img style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;width:100%;height:auto;" src="'.$button.'"></a>';
			
		}
		
		$buy_button_message = str_replace('{coin}','$'.$this->opt['quickstart']['user_name'],$this->lang['buy_button_label']);
		
		
		return '<a rel="nofollow"'.$new_window.' href="'.$url.'"><button class="cb_p8_buy_button" style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;">'.$buy_button_message.'</button></a>';
		
		
	}
	public function make_buy_button_p($author_id=false, $margin=10, $max_width=200, $new_window=false)
	{
		if($new_window)
		{
			$new_window = ' target="_blank"';
			
		}
		else
		{
			$new_window='';
		
		}
		
		$buy_url = $this->make_bitclout_buy_url( $author_id, 'dspress_buy_button', 'site_sidebar_widget' );
		$profile_url = $this->make_bitclout_profile_url( $author_id, 'dspress_buy_button', 'site_sidebar_widget' );
		
		if ($author_id) {
			

			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			
		}
		else {
			
			$bitclout_profile = $this->opt['quickstart']['user_name'];
			$node_name = $this->opt['quickstart']['node_name'];
			$node_url = $this->opt['quickstart']['node_url'];
		}
		
		
		if($this->opt['quickstart']['custom_button']!='') {
			
			return '<a rel="nofollow"'.$new_window.' href="'.$buy_url.'"><img style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;width:100%;height:auto;" src="'.$button.'"></a>';
			
		}
		
		$buy_button_message = str_replace('{coin}','$'.$bitclout_profile,$this->lang['buy_button_label']);
		$follow_link_message = str_replace('{coin}',''.$bitclout_profile,$this->lang['follow_instead_label']);
		
		
		return '<a rel="nofollow"'.$new_window.' href="'.$buy_url.'/buy"><button class="cb_p8_buy_button" onClick="window.location.href=\''.$buy_url.'\';" style="background-color: '.$this->opt['quickstart']['button_color'].';color: '.$this->opt['quickstart']['button_text_color'].';margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;">'.$buy_button_message.'</button></a><br /><a href="'.$profile_url.'">'.$follow_link_message.'</a>';
		
		
	}
	public function make_follow_button_p($author_id=false, $margin=10, $max_width=200, $new_window=false)
	{
		if($new_window)
		{
			$new_window = ' target="_blank"';
			
		}
		else
		{
			$new_window='';
		
		}
		
		$buy_url = $this->make_bitclout_buy_url( $author_id, 'dspress_follow_button', 'site_sidebar_widget' );
		$profile_url = $this->make_bitclout_profile_url( $author_id, 'dspress_follow_button', 'site_sidebar_widget' );
		

		if ($author_id) {
			

			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'bitclout_profile', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
		}
		else {
			
			$bitclout_profile = $this->opt['quickstart']['user_name'];
			$node_name = $this->opt['quickstart']['node_name'];
			$node_url = $this->opt['quickstart']['node_url'];
			$user_name = $this->opt['quickstart']['user_name'];
		}
		
		
		if($this->opt['quickstart']['custom_button']!='') {
			
			return '<a rel="nofollow"'.$new_window.' href="'.$profile_url.'"><img style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;width:100%;height:auto;" src="'.$button.'"></a>';
			
		}		

		
		$buy_message = str_replace('{coin}','$'.$this->opt['quickstart']['user_name'],$this->lang['buy_button_label']);
		$follow_button_message = str_replace('{coin}',''.$user_name,$this->lang['follow_label']);
		
		
		return '<a rel="nofollow"'.$new_window.' href="'.$profile_url.'"><button class="cb_p8_buy_button" onClick="window.location.href=\''.$profile_url.'\';" style="background-color: '.$this->opt['quickstart']['button_color'].';color: '.$this->opt['quickstart']['button_text_color'].';margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;">'.$follow_button_message.'</button></a><br /><a'.$new_window.' href="'.$buy_url.'">'.$buy_message.'</a>';
		
		
	}
	public function make_to_bitclout_link_to_profile_p($url, $button, $margin=10, $max_width=200, $new_window=false)
	{
		if($new_window)
		{
			$new_window = ' target="_blank"';
			
		}
		else
		{
			$new_window='';
		
		}
		
		return '<a rel="nofollow"'.$new_window.' href="'.$url.'"><img style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:'.$max_width.'px;width:100%;height:auto;" src="'.$button.'"></a>';
		
		
	}
	public function make_bitclout_buy_url_p( $author_id, $utm_content )
	{
		// wrapper to add some params and filter the url.
		if ($author_id) {
			

			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			
		}
		else {
			
			$bitclout_profile = $this->opt['quickstart']['user_name'];
			$node_name = $this->opt['quickstart']['node_name'];
			$node_url = $this->opt['quickstart']['node_url'];
		}
		
		

		// Lets check if what is saved is an url
		if(substr($bitclout_profile,0,4)=='http') {
			// It is! Load the value to url value
			$url = $bitclout_profile;
		}
		else {
			
			// Check if an int user id was dropped in
			if ( is_numeric( $bitclout_profile ) ) {
				// This is a user name/slug. Make the url :
				$url = $node_url .'/u/'.$bitclout_profile;
				
			}
			else {
				// This is a user. Make relevant link.
				
				$url = $node_url . '/u/'.$bitclout_profile;
			}
			
		}
		
		
		// Diamondapp doesn't support /buy url.

		if (  $node_url != 'https://diamondapp.com' AND $this->opt['quickstart']['force_profile_link'] != 'yes') {
			
			// Add utm params
			$url.='/buy';
		
		}
		
		
		$utm_source_url = site_url();
		
		// Check if this is a post.
		
		global $post;
		
		if ( $post ) {
			// Override with content url if there is content
			$utm_source_url =  get_permalink( $post );
		}
		
		$utm_params = 'utm_content=' . $utm_content . '&utm_medium=dspress&utm_campaign=' . get_site_url() .'&utm_term=&utm_source=' . $utm_source_url;
		
		// Simple check to see if creator url has ? (for non vanity urls)
		$append_with = '?';
		if ( strpos( $url, '?' ) !== false ) {
			$append_with = '&';
		}

		$url .= $append_with . $utm_params;
		
		return $url;
		
		
	}
	public function make_bitclout_profile_url_p( $author_id, $utm_content )
	{
		// wrapper to add some params and filter the url.
		if ($author_id) {
			

			$bitclout_profile=esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );
			$node_name=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_name', $author_id ) );
			$node_url=esc_attr( get_the_author_meta( $this->internal['prefix'].'node_url', $author_id ) );
			$user_name = esc_attr( get_the_author_meta( $this->internal['prefix'].'user_name', $author_id ) );

		}
		else {
			
			$bitclout_profile = $this->opt['quickstart']['user_name'];
			$node_name = $this->opt['quickstart']['node_name'];
			$node_url = $this->opt['quickstart']['node_url'];
		}

		// Lets check if what is saved is an url

		
		$url = $node_url . '/u/'.$bitclout_profile;


		// Add utm params

		$utm_source_url = site_url();
		
		// Check if this is a post.
		
		global $post;
		
		if ( $post ) {
			// Override with content url if there is content
			$utm_source_url =  get_permalink( $post );
		}
		
		$utm_params = 'utm_content=' . $utm_content . '&utm_medium=dspress&utm_campaign=' . get_site_url() .'&utm_term=&utm_source=' . $utm_source_url;
		
		// Simple check to see if creator url has ? (for non vanity urls)
		$append_with = '?';
		if ( strpos( $url, '?' ) !== false ) {
			$append_with = '&';
		}

		$url .= $append_with . $utm_params;
		
		return $url;
		
		
	}
	public function queue_modal_p($modal)
	{
		// This function queues modals as necessary. 

		// We want our styles in even if we arent on our own admin page:
	
		wp_enqueue_style( $this->internal['id'].'-css-admin', $this->internal['plugin_url'].'plugin/includes/css/admin.css' );
		
		add_action('admin_footer',array(&$this,'queue_footer_modal'));
	
		$this->internal['queue_modal']=$modal;
		$this->opt['queue_modal']=$modal;
		$this->update_opt();

		wp_enqueue_script( 'jquery-ui-dialog' ); 
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		
		wp_enqueue_script( $this->internal['id'].'-js-'.$modal, $this->internal['plugin_url'].'plugin/includes/scripts/'.$modal.'_modal.js' );
	
	}
	public function queue_footer_modal_p($v1)
	{
	
		include($this->internal['plugin_path'].'plugin/includes/'.$this->internal['queue_modal'].'_modal.php');
		
		$this->internal['setup_is_being_done']=true;
		
		if($this->internal['queue_modal']=='pro_pitch')
		{
			$this->opt['pro_pitch_done']=true;
		}
		$this->opt['queue_modal']=false;
		$this->update_opt();
	
	}
	// Plugin installer bloc taken from wpreset tutorial https://wpreset.com/programmatically-automatically-download-install-activate-wordpress-plugins/
	// Removed per plugin review of WP repo
	// Place here any code to install dependencies when an admin triggers & confirms installation
	
	// Plugin installer block EOF
	
    public function admin_message_page_p() {
		
		if(!current_user_can('manage_options')) {
			
			echo 'Sorry, you need to be an admin to view this page';
			return;
		}
		
		echo '<div id="cb_p8_admin_message_screen">';
	
			// Put some defaults so sites with warnings on will be fine
			$heading = $this->lang['admin_message_default_title'];
			$content = $this->lang['admin_message_default_content'];
			
			if ( isset( $_REQUEST['cb_p8_admin_message_title'] ) ) {
				$heading = $this->lang[ sanitize_text_field( $_REQUEST['cb_p8_admin_message_title'] ) ];
			}
			if ( isset( $_REQUEST['cb_p8_admin_message_content'] ) ) {
				$content = $this->lang[  sanitize_text_field( $_REQUEST['cb_p8_admin_message_content'] ) ];
			}
			
			echo '<div id="cb_p8_admin_message_page"><h1 style="margin-top: 0px;">' . esc_html($heading) . '</h1><div id="cb_p8_admin_message_content">' . esc_html($content) . '</div></div>';
		
			echo '</div>';
		
    }
	public function get_plugin_version_p( $slug ) {
		
		$plugin_data = get_plugin_data( $slug );
		return $plugin_data['Version'];
		
	}
	public function load_pointers_p( $hook_suffix ) {

		// Taken from wptuts tutorial
			 
		$screen = get_current_screen();
		$screen_id = $screen->id;
		
		// Get pointers for this screen
		$pointers = apply_filters( 'cb_p8_admin_pointers-' . $screen_id, array() );
		 
		if ( ! $pointers || ! is_array( $pointers ) ) {
			return;
		}
	 
		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$valid_pointers =array();
	 
		// Check pointers and remove dismissed ones.
		foreach ( $pointers as $pointer_id => $pointer ) {
	 
			// Sanity check
			if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
				continue;
			}
				
			$pointer['pointer_id'] = $pointer_id;
	 
			// Add the pointer to $valid_pointers array
			$valid_pointers['pointers'][] =  $pointer;
		}
			
		// No valid pointers? Stop here.
		if ( empty( $valid_pointers ) ) {
			return;
		}

		// Add pointers style to queue.
		wp_enqueue_style( 'wp-pointer' );
	 
		// Add pointers script to queue. Add custom script.
		wp_enqueue_script( $this->internal['id'] . '-pointer', $this->internal['plugin_url'] . 'plugin/includes/scripts/pointers.js', array( 'wp-pointer' ) );
	 
		// Add pointer options to script.
		wp_localize_script( $this->internal['id'] . '-pointer', 'cbp6Pointer', $valid_pointers );
				
	}

	public function widgets_pointer_p( $p ) {
		
		$widget_pointer_message = $this->lang['new_bitclout_widget_pointer_message'];
		
		if ( is_plugin_active( 'patron-plugin-pro/index.php' ) ) {
			$widget_pointer_message = $this->lang['new_bitclout_widget_pointer_message_for_pp_users'];		
		}
		
		$p['xyz140'] = array(
			'target' => '#menu-appearance',
			'options' => array(
				'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
					$this->lang['new_bitclout_widget_pointer_title'],
					$widget_pointer_message
				),
				'position' => array( 'edge' => 'top', 'align' => 'middle' )
			)
		);
		return $p;
	}
	
	public static function check_plugin_activation_date_for_existing_installs_p() {
	
		// Checks if plugin first activation date is saved for existing installs. Its here for backwards compatibility for existing installs before this version (2.1.1), and in case this meta info is lost in the db for any reason
		
		$plugin_first_activated = get_option( 'cb_p8_first_activated', 'NONE' );

		if ( $plugin_first_activated == 'NONE' ) {
			// If no date was set, set it to 0. This will mark existing installs
			update_option( 'cb_p8_first_activated', 0 );
		}
		
	}
	
	
	public function check_days_after_last_non_system_notice_p( $days ) {
		// Calculates if $days many days passed after last non system notice was showed. Used in deciding if and when to show admin wide notices
		
		$last_non_system_notice_shown_date = get_option( 'cb_p8_last_non_system_notice_shown_date', 0 );
		
		// Calculate if $days days passed since last notice was shown
		if ( ( time() - $last_non_system_notice_shown_date ) > ( $days * 24 * 3600 ) ) {
			// More than $days days. Set flag
			return true;
		}

		return false;
		
	}
	
	
	public function check_days_after_last_system_notice_p( $days ) {
		// Calculates if $days many days passed after last non system notice was showed. Used in deciding if and when to show admin wide notices
		
		$last_non_system_notice_shown_date = get_option( 'cb_p8_last_system_notice_shown_date', 0 );
		
		// Calculate if $days days passed since last notice was shown
		if ( ( time() - $last_non_system_notice_shown_date ) > ( $days * 24 * 3600 ) ) {
			// More than $days days. Set flag
			return true;
		}

		return false;
		
	}

	public function calculate_days_after_first_activation_p( $days ) {
		
		// Used to calculate days passed after first plugin activation. 
		
		$plugin_first_activated   = get_option( 'cb_p8_first_activated', 0 );
				
		// Calculate if $days days passed since last notice was shown		
		if ( ( time() - $plugin_first_activated ) > ( $days * 24 * 3600 ) ) {
			// More than $days days. Set flag
			return true;
		}

		return false;
			
	}
	
	public function admin_notices_p() {
		
		
		if ( $this->opt['setup_is_being_done'] ) {
			return;
		}
		/* Pro upsells 
		// Wp org wants non-error / non-functionality related notices to be shown infrequently and one per admin-wide page load, and be dismissable permanently. 		

		$patron_content_manager_pitch_shown = get_option( 'patron_content_manager_pitch_shown', false );

		$already_showed_non_system_notice = false;
		$current_screen = get_current_screen();
		
		// The addon upsell must be admin wide, permanently dismissable, and must not appear in plugin manager page in admin
		
		if( !$patron_content_manager_pitch_shown AND !$this->check_plugin_exists('patron-content-manager') AND $current_screen->id != 'plugins' AND ( ($this->check_days_after_last_non_system_notice( 7 ) AND $this->calculate_days_after_first_activation( 30 ) ) ) AND !$already_showed_non_system_notice AND !isset($GLOBALS['patron_content_manager_pitch_being_shown'])) {

			?>
				<div class="notice notice-success is-dismissible cb_p8_notice" id="cb_p8_patron_content_manager_pitch"><p><div style="display: flex; flex-wrap: wrap; flex-direction: row;"><a href="https://codebard.com/patron-content-manager?utm_source=<?php urlencode( site_url() ) ?>&utm_medium=cb_p8&utm_campaign=&utm_content=cb_p8_addon_upsell_notice_patron_content_manager&utm_term=" target="_blank"><img class="addon_upsell" src="<?php echo $this->internal['plugin_url']."images/Easily-manage-gated-posts.jpg"?>" style="width:200px; height:106px;margin: 10px; border: 1px solid #000000; margin-right: 20px;" alt="Patron Content Manager" /></a><div style="max-width: 700px; width: 100%;"><div style="max-width:500px; width: auto; float:left; display:inline-box"><h2 style="margin-top: 0px; font-size: 150%; font-weight: bold;">Easily manage your patron only content with Patron Content Manager</h2></div><div style="width:100%; font-size: 125% !important;clear:both; ">Get new <a href="https://codebard.com/patron-content-manager?utm_source=<?php urlencode( site_url() ) ?>&utm_medium=cb_p8&utm_campaign=&utm_content=cb_p8_addon_upsell_notice_patron_content_manager&utm_term=" target="_blank">Patron Content Manager</a> plugin for Patreon and easily re-gate content, gate old content, use detailed locking options, use content locking wizard to manage your patron only content & increase your patrons and pledges.<br /><br /><a href="https://codebard.com/patron-content-manager?utm_source=<?php urlencode( site_url() ) ?>&utm_medium=cb_p8&utm_campaign=&utm_content=cb_p8_addon_upsell_notice_patron_content_manager&utm_term=" target="_blank">Check out all features here</a></div></div></div></p>
				</div>
			<?php	
			
			$already_showed_non_system_notice = true;
			$GLOBALS['patron_content_manager_pitch_being_shown'] = true;
			
		}
		*/
	}

	public function check_plugin_exists_p( $plugin_dir ) {

		// Simple function to check if a plugin is installed (may be active, or not active) in the WP instalation
		
		// Plugin dir is the wp's plugin dir together with the plugin's dir

		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_dir ) ) {
			return true;
		}
	}	
	public function dismiss_admin_notice_p() {
		
		if( !( is_admin() && current_user_can( 'manage_options' ) ) ) {
			return;
		}
		
		// Mapping what comes from REQUEST to a given value avoids potential security problems and allows custom actions depending on notice

		if ( $_REQUEST['notice_id'] == 'cb_p8_patron_content_manager_pitch' ) {
			
			update_option( 'patron_content_manager_pitch_shown', true);
			
			// Set the last notice shown date
			$this->set_last_non_system_notice_shown_date();
		}
		
		
	}
	
	public function set_last_non_system_notice_shown_date_p() {
		
		// Sets the last non system notice shown date to now whenever called. Used for decicing when to show admin wide notices that are not related to functionality. 
		
		update_option( 'cb_p8_last_non_system_notice_shown_date', time() );
			
	}
	public function set_last_system_notice_shown_date_p() {
		
		// Sets the last non system notice shown date to now whenever called. Used for decicing when to show admin wide notices that are not related to functionality. 
		
		update_option( 'cb_p8_system_notice_shown_date', time() );
			
	}
	
	public function enqueue_color_picker_p( $hook_suffix ) {
		// first check that $hook_suffix is appropriate for your admin page
		if ( $hook_suffix != 'toplevel_page_settings_cb_p8' OR !isset($_REQUEST['cb_p8_tab']) OR $_REQUEST['cb_p8_tab'] != 'quickstart' ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		wp_enqueue_script( 'cp-active', plugins_url('/includes/scripts/cb_p8_color_picker.js', __FILE__), array('jquery'), '', true );
		
	}

	public function styles_in_head_p() {

	echo '
	<style>
	.cb_p8_buy_button:hover {
		
		background-color: '. esc_html($this->opt['quickstart']['button_hover_color']).' !important;
	}
	</style>

';


	}
	public function filter_settings_p($v1)
	{

		$request = $v1;
		
		if ( isset( $request['opt']['quickstart']['site_account'] ) ) {
			
			
			$user_name = '';

			$profile_url = parse_url( filter_var($request['opt']['quickstart']['site_account'], FILTER_SANITIZE_URL ) );

			if ( $profile_url AND isset($profile_url['path']) AND isset($profile_url['host']) ) {
				$user_name = str_replace('/u/', '', $profile_url['path']);
			}
			// Check for any slashes in the name. If there are, then path is wrong

			if ( $user_name == '' OR strpos($user_name,'/') !== false ) {

				// Error!!!
				$this->opt['last_profile_url_was_incorrect'] = true;
				$this->update_opt();
				wp_redirect( admin_url( 'admin.php?page=cb_p8_setup_wizard&setup_stage=0&error=profile_url_not_right') );

				exit;
			}
			$this->opt['last_profile_url_was_incorrect'] = false;
			$this->update_opt();
			$node_name = $profile_url['host'];

			if ( $profile_url['host']=='bitclout.com') {
				$node_name = 'BitClout';
			}
			if ( $profile_url['host']=='diamondapp.com') {
				$node_name = 'Diamond';
			}

				// Reconstruct url without req params
				if ( $profile_url['host'] != '') {
					$url = 'https://'. $profile_url['host'] . '/u/' . $user_name;
				}

				$request['opt']['quickstart']['site_account']=$url;
				$request['opt']['quickstart']['user_name']=$user_name;
				$request['opt']['quickstart']['node_name']=$node_name;
				$request['opt']['quickstart']['node_url']=$profile_url['scheme']. '://' . $profile_url['host'];
				
			
		}

		return $request;
	}

}

$codebard_p8 = codebard_p8_plugin::get_instance();

function codebard_p8_get()
{

	// This function allows any plugin to easily retieve this plugin object
	return codebard_p8_plugin::get_instance();

}

?>