<?php

if(isset($_REQUEST['site_account']) AND ($_REQUEST['site_account']=='' OR $_REQUEST['site_account']=='Delete this and enter your BitClout/Diamond/DeSo profile link here'))
{
	// Error!!!

	$this->error[]=$this->lang['error_wizard_empty_profile_name'];
	$this->opt['setup_is_being_done']=true;
	$this->update_opt();
	require(realpath(dirname(__FILE__)).'/setup_0.php');
	return;
}

$user_name = '';

$profile_url = parse_url( filter_var($_REQUEST['site_account'], FILTER_SANITIZE_URL ) );

if ( $profile_url AND isset($profile_url['path']) AND isset($profile_url['host']) ) {
	$user_name = str_replace('/u/', '', $profile_url['path']);
}
// Check for any slashes in the name. If there are, then path is wrong

if ( $user_name == '' OR strpos($user_name,'/') !== false ) {

	// Error!!!

	$this->error[]=$this->lang['profile_url_not_right'];

	require(realpath(dirname(__FILE__)).'/setup_0.php');
	return;
}

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

	$this->opt['quickstart']['site_account']=$url;
	$this->opt['quickstart']['user_name']=$user_name;
	$this->opt['quickstart']['node_name']=$node_name;
	$this->opt['quickstart']['node_url']=$profile_url['scheme']. '://' . $profile_url['host'];
		
	$this->opt['queue_modal']=false;
	$this->opt['setup_done'] = true;
	$this->opt['pro_pitch_done'] = true;
	$this->opt['setup_is_being_done'] = false;
	$this->update_opt();
	
?>

<div class="<?php echo esc_attr( $this->internal['prefix']); ?>settings">


	<div style="font-size:24px;font-weight:bold;margin-top:30px;display:flex;align-items: center;width:100%;"><img src="<?php echo esc_url( $this->internal['plugin_url'] ) .'/images/DSPress-Logo-In-Text.png'?>" style="width:115px; height: auto;" /> for BitClout, Diamond and DeSo is now ready!</div>

	
	<div style="font-size:18px;font-weight:normal;margin-top:30px;margin-bottom:15px;display:inline-table;width:100%;">Now if you wish, you can: 
<br><br> <a href="https://codebard.com/how-to-place-dspress-bitclout-diamond-deso-widgets-in-your-wordpress-sites-sidebars-or-footer" target="_blank" style="font-weight:bold;">Put Buy Widgets</a> to your Sidebar
<br><br> <a href="<?php echo esc_url( $this->internal['admin_url'] ) .'admin.php?page=settings_'. esc_attr( $this->internal['id'] ); ?>" target="_blank" style="font-weight:bold;">Customize Your Buttons, Button Messages</a> and their features<br><br> <a href="https://bitclout.com/u/CodeBard" target="_blank" style="font-weight:bold;">Follow CodeBard</a> at BitClout and <a href="https://bitclout.com/u/CodeBard/buy?tab=posts" target="_blank" style="font-weight:bold;">Buy Codebard Coin</a> to support DeSo plugin development and prosper together</div>

	<div style="font-size:20px;font-weight:bold;margin-top:10px;margin-bottom:15px;display:block;width:100%;line-height: 1;">And, subscribe to BitClout / DiamondApp / DeSo WordPress Plugins Newsletter!</div>
	<div style="font-size:18px;font-weight:normal;margin-top:0px;margin-bottom:15px;display:block;width:100%;line-height: 1;">
	Keep up-to-date with BitClout / DiamondApp / DeSo related WordPress news, new DeSo plugins, and receive tips & tricks to boost your DeSo profile!
	</div>

<!-- Begin Mailchimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7_dtp.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; width:100%;}
	/* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup" style="max-width:500px; width: 100%; background-color: transparent;padding-left: 0px;display:block; margin-left: 0px;">
<form action="https://codebard.us9.list-manage.com/subscribe/post?u=5afbc1be9f2ed76070f4b64fd&amp;id=6dd31e714b" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll" style="padding-left: 0px;">
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required  style="margin-left: 0px;">
    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_5afbc1be9f2ed76070f4b64fd_6dd31e714b" tabindex="-1" value=""></div>
        <div class="clear foot">
           <input type="submit" value="Subscribe Now!" name="subscribe" id="mc-embedded-subscribe" class="button" style="background-color: #479900;">
        </div>
    </div>
</form>
</div>

<!--End mc_embed_signup-->

