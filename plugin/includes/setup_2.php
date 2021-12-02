<?php

if(isset($_REQUEST['site_account']) AND ($_REQUEST['site_account']=='' OR $_REQUEST['site_account']=='Delete this and enter your Site or your personal (admin) Patreon account here'))
{
	// Error!!!

	$this->error[]=$this->lang['error_wizard_empty_profile_name'];
	$this->opt['setup_is_being_done']=true;
	$this->update_opt();
	require($this->internal['plugin_path'].'plugin/includes/setup_modal.php');
	
}
else
{

	$this->opt['quickstart']['site_account']=$_REQUEST['site_account'];
		
	$this->opt['queue_modal']=false;
	$this->opt['setup_done'] = true;
	$this->opt['setup_done'] = true;
	$this->opt['pro_pitch_done'] = true;
	$this->opt['setup_is_being_done'] = false;
	$this->update_opt();
	
?>

<div class="<?php echo $this->internal['prefix'];?>settings">


	<div style="font-size:175%;font-weight:bold;margin-top:30px;display:inline-table;width:100%;">Great! Buy Button & Widgets For BitClout, DiamondApp and DeSo is now ready!</div>

	
	<div style="font-size:150%;font-weight:bold;margin-top:30px;margin-bottom:15px;display:inline-table;width:100%;">Now if you wish, you can: 
<br><br> <a href="<?php echo $this->internal['admin_url']; ?>widgets.php" target="_blank">Put Patreon Widgets to your Sidebar</a>
<br><br> <a href="<?php echo $this->internal['admin_url'].'admin.php?page=settings_'.$this->internal['id']; ?>" target="_blank">Customize Your Buttons, Button Messages and their features</a></div>

	<div style="font-size:150%;font-weight:bold;margin-top:10px;margin-bottom:15px;display:inline-table;width:100%;line-height: 1;">And, subscribe to BitClout / DiamondApp / DeSo WordPress Plugins Newsletter!</div>
	<div style="font-size:125%;font-weight:bold;margin-top:0px;margin-bottom:15px;display:inline-table;width:100%;line-height: 1;">
	Keep up-to-date with BitClout / DiamondApp / DeSo related WordPress news, new DeSo plugins, and receive tips & tricks to boost your DeSo profile!
	</div>
	<div style="font-size:150%;font-weight:bold;margin-top:0px;margin-bottom:15px;display:inline-table;width:100%;line-height: 1;">
<!-- Begin Mailchimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7_dtp.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; width:100%;}
	/* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup" style="max-width:500px; width: 100%;">
<form action="https://codebard.us9.list-manage.com/subscribe/post?u=5afbc1be9f2ed76070f4b64fd&amp;id=6dd31e714b" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<br><br>
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_5afbc1be9f2ed76070f4b64fd_6dd31e714b" tabindex="-1" value=""></div>
        <div class="clear foot">
           <input type="submit" value="Subscribe Now!" name="subscribe" id="mc-embedded-subscribe" class="button" style="background-color: #479900;">
        </div>
	<p><a href="http://eepurl.com/hO41f9" title="Mailchimp - email marketing made easy and fun"><img class="referralBadge" src="https://eep.io/mc-cdn-images/template_images/branding_logo_text_dark_dtp.svg"></a></p>
    </div>
</form>
</div>

<!--End mc_embed_signup-->
	</div>


<?php
}


?>