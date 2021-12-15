<?php

class cb_p8_dud_language_object {
	
	public $internal = array(

	// Holds internal and generated vars. Never saved.

	);
	public $opt = array(

	// Holds internal and generated vars. Never saved.

	);
	public $hardcoded = array(

	// Holds hardcoded vars. Never saved.

	);
	public $lang = array(

	// Holds hardcoded vars. Never saved.

	);
	
			
	public function __construct() 
	{
	


	
	}
	public function load_language($v1=false)
	{

		// Loads language from db.
		
		if(isset($this->opt['lang']))
		{
			$lang = $this->opt['lang'];
		}
		else
		{
			$lang = 'en-US';
		}		
		
		$lang_file = __DIR__ . '/../../plugin/includes/languages/'.$lang.'.php';
		
		if(!file_exists($lang_file))
		{
			$lang='en-US';
			$this->opt['lang']='en-US';
			$this->update_opt();
			$lang_file = __DIR__ . '/../../plugin/includes/languages/'.$lang.'.php';
			
		}
		
		 
		// Get saved values in db:
		
		$language_values = get_option($this->internal['prefix'].'lang_'.$lang);
		
		include($lang_file);
		$this->lang=$lang;
			
		if(!is_array($language_values))
		{
			// Get the language from language file:

			
			$language_values = $this->lang;
			
		}
		

		
		$language_values = array_replace_recursive(
		
							$lang,
							$language_values
		);
	
		return array_map('stripslashes', $language_values);
			
	}
	public function update_opt()
	{
		// Does nothing but wrap update_options for options:
		
		return update_option($this->internal['prefix'].'options',$this->opt);

		
	}
	public function load_internal_vars()
	{
		require_once(__DIR__ . '/../../core/includes/default_internal_vars.php');
		require_once(__DIR__ . '/../../plugin/includes/default_internal_vars.php');
		require_once(__DIR__ . '/../../plugin/includes/hardcoded_vars.php');
		
	}
}


class cb_p8_sidebar_site_widget extends WP_Widget {
 
 
    /** constructor -- name this the same as the class above */
    public function __construct() {
		
		global $cb_p8;
		
		if( !isset( $cb_p8 ) ) {
			// If plugin is not initialized, we may be in wp-cli or some other tool that accessed this file without initiating this plugin. Use a dud object to replace it:
			
			$this->cb_p8 = new cb_p8_dud_language_object();
			// Get options 
			$this->cb_p8->internal['prefix']='cb_p8';
			$this->cb_p8->opt=get_option($this->cb_p8->internal['prefix'].'options');
			$this->cb_p8->internal = $this->cb_p8->load_internal_vars();
		}
		else {
			$this->cb_p8=$cb_p8;
		}
		
		
		// Load language from db
		$this->cb_p8->lang = $this->cb_p8->load_language();
		
        parent::__construct(
            'deso_sidebar_site_widget', // Base ID
             $this->cb_p8->lang['sidebar_site_widget_name'], // Name
            array( 'description' => $this->cb_p8->lang['sidebar_site_widget_desc'] ) // Args
        );
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) 
	{	
		global $cb_p8;
		
		
	    if( $this->cb_p8->opt['sidebar_widgets']['hide_site_widget_on_single_post_page']=='yes' AND is_singular('post'))
		{
			// Dont show the site widget on single post page 
			return;
			
		}
		
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $message 	= $instance['message'];
        ?>
              <?php echo html_entity_decode( esc_html( $before_widget) ); ?>
                  <?php if ( $title )
                        echo html_entity_decode( esc_html( $before_title . $title . $after_title ) ); ?>
						
								<?php if($message!='')
								{
								?>
								<div style="text-align: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['insert_text_align'] ); ?> !important;font-size: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_font_size'] ); ?>;margin-top: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_over_post_button_margin'] ); ?>;margin-bottom: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_over_post_button_margin'] ); ?>;"><?php echo esc_html( $this->cb_p8->site_sidebar_widget_message($message) ); ?></div>
								<?php } ?>
							
          <?php echo html_entity_decode( esc_html( $this->cb_p8->site_sidebar_widget() ) ); ?>
     
						
              <?php echo html_entity_decode( esc_html( $after_widget ) ); ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
		global $cb_p8;
		
		$button_message = $this->cb_p8->lang['sidebar_site_widget_message'];
		if ($this->cb_p8->opt['quickstart']['button_type'] == 'follow_button') {
			$button_message = $this->cb_p8->lang['sidebar_site_widget_message_follow'];
		}
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'message'=>$button_message ) );
        $title 		= esc_attr($instance['title']);
        $message	= esc_attr($instance['message']);
        ?>
         <p>
          <label for="<?php echo esc_html( $this->get_field_id('title') ); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_html( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_html( $title ); ?>" />
        </p>
		<p>
          <label for="<?php echo esc_textarea( $this->get_field_id('message') ); ?>"><?php echo esc_textarea( $cb_p8->lang['message_over_button'] ) ?></label> 
          <input class="widefat" id="<?php echo esc_textarea( $this->get_field_id('message') ); ?>" name="<?php echo esc_textarea( $this->get_field_name('message') ); ?>" type="text" value="<?php echo esc_textarea( $message ) ?>" />
        </p>
		<p>
          <?php echo html_entity_decode( esc_html( $this->cb_p8->site_sidebar_widget() ) ); ?>
        </p>		
		
        <?php 
    }
	

}




class cb_p8_sidebar_author_widget extends WP_Widget {
 
 
    /** constructor -- name this the same as the class above */
    public function __construct() {
		
		global $cb_p8;
		
		if( !isset( $cb_p8 ) ) {
			// If plugin is not initialized, we may be in wp-cli or some other tool that accessed this file without initiating this plugin. Use a dud object to replace it:
			
			$this->cb_p8 = new cb_p8_dud_language_object();
			// Get options 
			$this->cb_p8->internal['prefix']='cb_p8';
			$this->cb_p8->opt=get_option($this->cb_p8->internal['prefix'].'options');
			$this->cb_p8->internal = $this->cb_p8->load_internal_vars();
		}
		else {
			$this->cb_p8=$cb_p8;
		}
		
		
		// Load language from db
		$this->cb_p8->lang = $this->cb_p8->load_language();
		
        parent::__construct(
            'deso_sidebar_author_widget', // Base ID
             $this->cb_p8->lang['sidebar_author_widget_name'], // Name
            array( 'description' => $this->cb_p8->lang['sidebar_author_widget_desc'] ) // Args
        );
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) 
	{
		global $cb_p8;
		
		
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $message 	= $instance['message'];
  
		echo html_entity_decode( esc_html( $before_widget) ); ?>
                  <?php if ( $title )
                        echo html_entity_decode( esc_html( $before_title . $title . $after_title ) ); ?>
						
								<?php if($message!='')
								{
								?>
								<div style="text-align: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['insert_text_align'] ); ?> !important;font-size: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_font_size'] ); ?>;margin-top: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_over_post_button_margin'] ); ?>;margin-bottom: <?php echo esc_html( $this->cb_p8->opt['sidebar_widgets']['message_over_post_button_margin'] ); ?>;"><?php echo esc_html( $this->cb_p8->author_sidebar_widget_message($message) ); ?></div>
								<?php } ?>
							
          <?php echo html_entity_decode( esc_html( $this->cb_p8->author_sidebar_widget() ) ); ?>
     
						
              <?php echo html_entity_decode( esc_html( $after_widget ) ); ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {
		global $cb_p8;
		
		$button_message = $this->cb_p8->lang['sidebar_author_widget_message'];
		if ($this->cb_p8->opt['quickstart']['button_type'] == 'follow_button') {
			$button_message = $this->cb_p8->lang['sidebar_author_widget_message_follow'];
		}
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'message'=>$button_message ) );
        $title 		= esc_attr($instance['title']);
        $message	= esc_attr($instance['message']);
        ?>
         <p>
          <label for="<?php echo esc_html( $this->get_field_id('title') ); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_html( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_html( $title ); ?>" />
        </p>
		<p>
          <label for="<?php echo esc_textarea( $this->get_field_id('message') ); ?>"><?php echo esc_textarea( $cb_p8->lang['message_over_button'] ) ?></label> 
          <input class="widefat" id="<?php echo esc_textarea( $this->get_field_id('message') ); ?>" name="<?php echo esc_textarea( $this->get_field_name('message') ); ?>" type="text" value="<?php echo esc_textarea( $message ) ?>" />
        </p>
		<p>
          <?php echo html_entity_decode( esc_html( $this->cb_p8->author_sidebar_widget() ) ); ?>
        </p>		
		
        <?php 
    }
	

}


function cb_p8_register_widgets()
{

	register_widget( 'cb_p8_sidebar_author_widget' );
	register_widget( 'cb_p8_sidebar_site_widget' );

}

add_action('widgets_init', 'cb_p8_register_widgets');


?>