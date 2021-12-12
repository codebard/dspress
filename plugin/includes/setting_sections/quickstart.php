<?php

$tab=$_REQUEST[$this->internal['prefix'].'tab'];


echo $this->do_admin_settings_form_header($tab);

		if(isset($_REQUEST[$this->internal['prefix'].'tab']))
		{
			
			$tab=$_REQUEST[$this->internal['prefix'].'tab'];
		}
		
		$open_new_window_checked_yes = '';
		$open_new_window_checked_no = '';
		$force_site_checked_yes = '';
		$force_site_checked_no = '';
		$use_old_patreon_button_yes = '';
		$use_old_patreon_button_no = '';
		$buy_button_checked = '';
		$follow_button_checked = '';
		$force_profile_checked_yes = '';
		$force_profile_checked_no = '';


		if(isset($this->opt[$tab]['button_type']) AND $this->opt[$tab]['force_profile_link']=='yes')
		{
		
			$force_profile_checked_yes=" CHECKED";
		
		}
		if(isset($this->opt[$tab]['button_type']) AND $this->opt[$tab]['force_profile_link']=='no')
		{
		
			$force_profile_checked_no=" CHECKED";
		
		}
		if(isset($this->opt[$tab]['button_type']) AND $this->opt[$tab]['button_type']=='buy_button')
		{
		
			$buy_button_checked=" CHECKED";
		
		}
		if(isset($this->opt[$tab]['button_type']) AND $this->opt[$tab]['button_type']=='follow_button')
		{
		
			$follow_button_checked=" CHECKED";
		
		}

		if(isset($this->opt[$tab]['open_new_window']) AND $this->opt[$tab]['open_new_window']=='yes')
		{
		
			$open_new_window_checked_yes=" CHECKED";
		
		}
		else
		{
			$open_new_window_checked_no=" CHECKED";	
		}	

		if(isset($this->opt[$tab]['force_site_button']) AND $this->opt[$tab]['force_site_button']=='yes')
		{
		
			$force_site_checked_yes=" CHECKED";
		
		}
		else
		{
			$force_site_checked_no=" CHECKED";	
		}
		
		
		if(isset($this->opt[$tab]['old_button']) AND $this->opt[$tab]['old_button']=='yes')
		{
		
			$use_old_patreon_button_yes=" CHECKED";
		
		}
		else
		{
			
			$use_old_patreon_button_no=" CHECKED";
			
		}
		
		if(!isset($this->opt[$tab]['custom_button']))
		{
		
			$this->opt[$tab]['custom_button']='';
		
		}
		if(!isset($this->opt[$tab]['custom_button_width']))
		{
		
			$this->opt[$tab]['custom_button_width']='';
		
		}
		
?>
			<h3>Site's BitClout / Diamond / DeSo Node profile</h3>
			If you chose not to use profile of Authors for each author, or an Author does not have any profile saved in his/her author profile page, this profile will be used for Buttons for users in any single post. This affects both the <b>Buttons under Posts</b>, and the <b>Author sidebar widget</b>.<br><br>
			<input type="text" style="width : 500px" name="opt[<?php echo $tab; ?>][site_account]" value="<?php echo $this->opt[$tab]['site_account']; ?>"><br><br>
			
			<h3>Button background color</h3>
			The background color of buttons used in widgets and call to actions. The default color is 'DeSo blue'. But if this doesn't suit your site, pick a new color that does. Just click inside the input box below and a color picker will appear.<br><br>
			<input id="cb_p8_button_color" class="cb_p8-color-picker" name="opt[<?php echo $tab; ?>][button_color]" type="text" value="<?php echo $this->opt[$tab]['button_color']; ?>" /><br><br>
			
			<h3>Button hover color</h3>
			If you changed the button background color, you should change the hover color for the button so it will fit the new color you have set for the button. Just click inside the input box below and a color picker will appear.<br><br>
			<input id="cb_p8_button_color" class="cb_p8-color-picker" name="opt[<?php echo $tab; ?>][button_hover_color]" type="text" value="<?php echo $this->opt[$tab]['button_color']; ?>" /><br><br>
			
			<h3>Button text color</h3>
			The text color of buttons used in widgets and call to actions. The default color is white. Just click inside the input box below and a color picker will appear.<br><br>
			<input id="cb_p8_button_color" class="cb_p8-color-picker" name="opt[<?php echo $tab; ?>][button_text_color]" type="text" value="<?php echo $this->opt[$tab]['button_text_color']; ?>" /><br><br>
			
			
			<h3>Use Buy buttons or Follow buttons</h3>
			If you choose Buy buttons, the buttons and messages will prioritize sending your users directly to buying your token. And they will have a smaler follow link down below them. If you choose Follow buttons, the buttons will send your users to your profile. In this case they will have a small buy link down below them. If you change this, revisit your Appearance -> Widgets menu and update the label text in the widgets that you have already added. (ie, 'Buy {coin} at BitClout...' to 'Follow {coin} at BitClout')
			
			<br><br>
			Buy buttons <input type="radio" name="opt[<?php echo $tab; ?>][button_type]" value="buy_button"<?php echo $buy_button_checked; ?>>
			Follow buttons <input type="radio" name="opt[<?php echo $tab; ?>][button_type]" value="follow_button"<?php echo $follow_button_checked; ?>>
			<br><br>
			
			
			<h3>Make all buttons go to profile</h3>
			If you use a DeSo node/app like Diamond which do not support a /buy url to send your users directly to buy your coin, choose 'yes' so that both Buy and Follow links will go to the profile instead of Buy buttons not working.
			
			<br><br>
			
			Yes <input type="radio" name="opt[<?php echo $tab; ?>][force_profile_link]" value="yes"<?php echo $force_profile_checked_yes; ?>>
			No <input type="radio" name="opt[<?php echo $tab; ?>][force_profile_link]" value="no"<?php echo $force_profile_checked_no; ?>>
			<br><br>
			
			
			<h3>Open pages in new window?</h3>
			If 'Yes', links like Buy and Follow links will be opened in a new window when users click them.
			
			<br><br>
			Yes <input type="radio" name="opt[<?php echo $tab; ?>][open_new_window]" value="yes"<?php echo $open_new_window_checked_yes; ?>>
			No <input type="radio" name="opt[<?php echo $tab; ?>][open_new_window]" value="no"<?php echo $open_new_window_checked_no; ?>>
			<br><br>		
			
			
			<h3>Force Site Button instead of Author</h3>
			If 'Yes', Site's own profile will be used for <b>Buttons under Posts</b> and <b>Author Buy sidebar widget</b> instead of Authors' own profile.
			
			<br><br>
			Yes <input type="radio" name="opt[<?php echo $tab; ?>][force_site_button]" value="yes"<?php echo $force_site_checked_yes; ?>>
			No <input type="radio" name="opt[<?php echo $tab; ?>][force_site_button]" value="no"<?php echo $force_site_checked_no; ?>>
			<br><br>
			
		
			<h3>Use a custom Button</h3>
			You can use a custom image for your button! Just click on below field to be taken to your WordPress media library to select your button or upload a new button and select that one. After selecting your button, save options and your new custom button will be made active.
			
			<br><br>			
			 <input class="cb_p8_file_upload" type="text" id="opt[<?php echo $tab; ?>]_custom_button" size="36" name="opt[<?php echo $tab; ?>][custom_button]" value="<?php echo $this->opt[$tab]['custom_button']; ?>" /> <a href="" class="cb_p8_clear_prevfield">Clear</a>
		<br><br>
		Current custom button :
		<br>
		<?php
			if($this->opt[$tab]['custom_button']!='')
			{
				echo '<a rel="nofollow"'.@$new_window.' href="'.@$url.'"><img style="margin-top: '.$this->opt['sidebar_widgets']['button_margin'].';margin-bottom: '.$this->opt['sidebar_widgets']['button_margin'].';max-width:50px;width:100%;height:auto;" src="'.$this->opt[$tab]['custom_button'].'"></a>';
				
			}
		?>
			<h3>Width for your custom button</h3>
			You can set the width for your custom button if you want to have it display larger or smaller. Height will be adjusted automatically. If you leave this empty, default width of 200px will be used - something close to the default button. Experiment with this value if you think your custom button is larger/smaller than you wish. 
			<br><br>
			<input type="text" style="width : 50px" name="opt[<?php echo $tab; ?>][custom_button_width]" value="<?php echo $this->opt[$tab]['custom_button_width']; ?>">
		
		<br><br>
		

<?php


$this->do_setting_section_additional_settings($tab);

echo $this->do_admin_settings_form_footer($tab);

?>