<?php

if(!isset($this->opt['lang']))
{
	$this->opt['lang']='en-US';
	
}

if(isset($_REQUEST[$this->internal['prefix'].'current_language']))
{
	$current_language=sanitize_text_field( $_REQUEST[$this->internal['prefix'].'current_language'] );
}
else
{
	$current_language=false;
}







echo '<h2>'.$this->lang['admin_title_choose_reset_language'].'</h2>';

echo '<form action="admin.php?page=settings_'. esc_attr( $this->internal['id'] ) .'&'. esc_attr( $this->internal['prefix'] ).'tab=languages" name="" method="post" class="'. esc_attr( $this->internal['prefix'] ).'inline_block_form">';

echo $this->do_admin_language_selector();

echo '<input type="hidden" name="'. esc_attr( $this->internal['prefix'] ).'action" value="choose_language">';
echo '<input type="hidden" name="cb_plugin" value="'.esc_attr( $this->internal['id'] ).'">';
echo '<input type="hidden" name="'.esc_attr( $this->internal['prefix'] ).'current_language" value="'. esc_attr( $this->opt['lang'] ).'">';
echo '<input type="submit" value="'.$this->lang['set_language_button_label'].'" class="'. esc_attr( $this->internal['prefix'] ).'admin_button">';

echo '</form>';


echo '<form action="admin.php?page=settings_'. esc_attr( $this->internal['id'] ) .'&'. esc_attr( $this->internal['prefix'] ).'tab=languages" name="" method="post" class="'. esc_attr( $this->internal['prefix'] ).'inline_block_form">';


echo '<input type="hidden" name="'. esc_attr( $this->internal['prefix'] ) .'action" value="reset_languages">';
echo '<input type="hidden" name="cb_plugin" value="'.esc_attr( $this->internal['id'] ) .'">';
echo '<input type="submit" value="'.esc_html( $this->lang['reset_languages_button_label'] ) .'" class="'. esc_attr( $this->internal['prefix'] ).'admin_button">';
echo '</form>';

if(wp_script_is('jquery')) {

   echo '<br><br>';
   echo '<button type="submit" class="'. esc_attr( $this->internal['prefix'] ).'admin_toggle_button '. esc_attr( $this->internal['prefix'] ).'admin_button" target="'. esc_attr( $this->internal['prefix'] ).'language_translation_toggle">'. esc_html( $this->lang['toggle_to_view_edit_current_language'] ).'</button>';

   echo '<div id="'.$this->internal['prefix'].'language_translation_toggle" style="display:none;">';

}

echo '<h2>'.$this->lang['admin_title_modify_current_language'].'</h2>';

echo '<form action="admin.php?page=settings_'. esc_attr( $this->internal['id'] ) .'&'. esc_attr( $this->internal['prefix'] ) .'tab=languages" name="" method="post">';

foreach($this->lang as $key => $value)
{
	echo '<br>';
	echo esc_attr( $key );
	echo '<br>';
	echo '<br>';
	echo '<textarea cols="50" rows="3" name="'. esc_attr( $this->internal['prefix'] ).'lang_strings['. esc_attr( $key ).']">'. esc_attr( $this->lang[$key] ).'</textarea>';
	echo '<br>';
	
}

echo '<input type="hidden" name="'. esc_attr( $this->internal['prefix'] ).'action" value="save_language">';
echo '<input type="hidden" name="cb_plugin" value="'. esc_attr( $this->internal['id']  ).'">';
echo '<input type="hidden" name="'. esc_attr( $this->internal['prefix']  ).'lang" value="'. esc_attr( $this->opt['lang']  ).'">';
echo '<br>';
echo '<input type="submit" value="'. esc_html( $this->lang['set_language_button_label'] ).'" class="'. esc_attr( $this->internal['prefix'] ).'admin_button">';

echo '</form>';

if(wp_script_is('jquery')) {

   echo '</div>';

}



?>