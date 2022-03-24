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


echo '<h2>'. esc_html($this->lang['admin_title_choose_reset_language']).'</h2>';

echo '<form action="admin.php?page=settings_'. sanitize_key( $this->internal['id'] ) .'&'. sanitize_key( $this->internal['prefix'] ).'tab=languages" name="" method="post" class="'. sanitize_key( $this->internal['prefix'] ).'inline_block_form">';

echo $this->do_admin_language_selector($this->opt['lang']);

echo '<input type="hidden" name="'. sanitize_key( $this->internal['prefix'] ).'action" value="choose_language">';
echo '<input type="hidden" name="cb_plugin" value="'.sanitize_key( $this->internal['id'] ).'">';
echo '<input type="hidden" name="'.sanitize_key( $this->internal['prefix'] ).'current_language" value="'. sanitize_text_field( $this->opt['lang'] ).'">';
echo '<input type="submit" value="'.esc_html($this->lang['set_language_button_label'] ).'" class="'. sanitize_key( $this->internal['prefix'] ).'admin_button">';

echo '</form>';


echo '<form action="admin.php?page=settings_'. sanitize_key( $this->internal['id'] ) .'&'. sanitize_key( $this->internal['prefix'] ).'tab=languages" name="" method="post" class="'. sanitize_key( $this->internal['prefix'] ).'inline_block_form">';


echo '<input type="hidden" name="'. sanitize_key( $this->internal['prefix'] ) .'action" value="reset_languages">';
echo '<input type="hidden" name="cb_plugin" value="'.sanitize_key( $this->internal['id'] ) .'">';
echo '<input type="submit" value="'.esc_html( $this->lang['reset_languages_button_label'] ) .'" class="'. sanitize_key( $this->internal['prefix'] ).'admin_button">';
echo '</form>';

if(wp_script_is('jquery')) {

   echo '<br><br>';
   echo '<button type="submit" class="'. sanitize_key( $this->internal['prefix'] ).'admin_toggle_button '. sanitize_key( $this->internal['prefix'] ).'admin_button" target="'. sanitize_key( $this->internal['prefix'] ).'language_translation_toggle">'. esc_html( $this->lang['toggle_to_view_edit_current_language'] ).'</button>';

   echo '<div id="'.$this->internal['prefix'].'language_translation_toggle" style="display:none;">';

}

echo '<h2>'.$this->lang['admin_title_modify_current_language'].'</h2>';

echo '<form action="admin.php?page=settings_'. sanitize_key( $this->internal['id'] ) .'&'. sanitize_key( $this->internal['prefix'] ) .'tab=languages" name="" method="post">';

foreach($this->lang as $key => $value)
{
	echo '<br>';
	echo sanitize_key( $key );
	echo '<br>';
	echo '<br>';
	echo '<textarea cols="50" rows="3" name="'. sanitize_key( $this->internal['prefix'] ).'lang_strings['. sanitize_key( $key ).']">'. esc_html( $this->lang[$key] ).'</textarea>';
	echo '<br>';
	
}

echo '<input type="hidden" name="'. sanitize_key( $this->internal['prefix'] ).'action" value="save_language">';
echo '<input type="hidden" name="cb_plugin" value="'. sanitize_key( $this->internal['id']  ).'">';
echo '<input type="hidden" name="'. sanitize_key( $this->internal['prefix']  ).'lang" value="'. sanitize_text_field( $this->opt['lang']  ).'">';
echo '<br>';
echo '<input type="submit" value="'. esc_html( $this->lang['set_language_button_label'] ).'" class="'. sanitize_key( $this->internal['prefix'] ).'admin_button">';

echo '</form>';

if(wp_script_is('jquery')) {

   echo '</div>';

}



?>