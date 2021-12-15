<div class="<?php echo esc_attr( $this->internal['prefix'] );?>setup_modal" <?php if(!isset($_REQUEST['setup_stage'])){ ?> id="<?php echo esc_attr( $this->internal['prefix'] );?>setup_modal" <?php } ?>>

		<div style="font-size:24px;font-weight:bold;margin-top:30px;display:flex;align-items: center;width:100%;"><img src="<?php echo esc_attr( $this->internal['plugin_url'] ).'/images/DSPress-Logo-In-Text.png'?>" style="width:115px; height: auto;" /> for BitClout, Diamond and DeSo is almost ready!</div>

	
	<div style="display:block;font-size:20px;font-weight:bold;margin-top:30px;margin-bottom:10px;display:inline-table;width:100%;">Just one thing - you need to fill in your BitClout/Diamond/DeSo node profile url name below:</div>
	<div style="display:block;font-size:18px;margin-top:5px;margin-bottom:10px;display:inline-table;width:100%;">(This will be used in buttons and widgets to send your visitors to your BitClout/Diamond/DeSo Node profile so that they can buy your coins)</div>

	<?php 

	
		if(isset($this->error) AND count($this->error)>0)
		{
			foreach($this->error as $key => $value)
			{
		
				echo html_entity_decode('<div style="display:block;clear:both;margin-top: 5px; margin-bottom:15px;">'. esc_html( $this->error[$key] ).'</div>' );

			
			}
		
		}
		else {
		if ( $this->opt['last_profile_url_was_incorrect'] ) {
			
			echo html_entity_decode('<div style="display:block;clear:both;margin-top: 5px; margin-bottom:15px;">'. esc_html(  $this->lang['profile_url_not_right'] ).'</div>' );
		}
		}
		
		if(!isset($_REQUEST['site_account']))
		{
			$_REQUEST['site_account']=$this->opt['quickstart']['site_account'];
			
		}
	?>
	
	<form method="POST" action="<?php echo esc_url( $this->internal['admin_url'] ).'admin.php?page=cb_p8_setup_wizard&setup_stage=1'; ?>">
	
		<input type="text" style="max-width : 700px;width:100%;font-size:150%;" name="site_account" id="site_account_setup" value="<?php echo esc_html( $_REQUEST['site_account'] ); ?>"  onfocus="if(this.value == '<?php echo esc_html( $_REQUEST['site_account'] ) ?>') {this.value=''}" onblur="if(this.value == ''){this.value ='<?php echo esc_html( $_REQUEST['site_account'] ) ?>'}">
		<input type="submit" style="font-size:150%;" value="	Save!	">


	<input type="hidden" name="<?php echo esc_attr( $this->internal['id'] );?>_action" value="dud">
	</form>

	<div style="font-size:125%;font-weight:bold;margin-top:20px;margin-bottom:15px;display:inline-table;width:100%;">If you don't know how to find your profile url, <a href="https://codebard.com/how-to-find-out-your-bitclout-diamond-or-other-deso-node-profile-link" target="_blank">click here to read the guide</a> - its easy!</div>

	</div>
