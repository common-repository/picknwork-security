<?php
//############################
// PROCESS AUTHENTICATION SETTINGS
//############################
if(isset($_POST['submit_authentication'])){
	
	$apikey = !empty($_POST['pnw_apikey']) ? sanitize_text_field($_POST['pnw_apikey']) : '';
	$apisecret = !empty($_POST['pnw_apisecret']) ? sanitize_text_field($_POST['pnw_apisecret']) : '';
	$passphrase = !empty($_POST['pnw_passphrase']) ? sanitize_text_field($_POST['pnw_passphrase']) : '';
	
	$data = array(
	'apikey' => $apikey,
	'apisecret' => $apisecret,
	'passphrase' => $passphrase
	);
	
	$run = pnw_update_keys($data);
	$_SESSION['pnw-type'] = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
	$_SESSION['pnw-message'] = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
	
}

	$key = pnw_get_keys();
	$chosenAuthID = !empty($key['apikey']) ? sanitize_text_field($key['apikey']) : '';
	$chosenapisecret = !empty($key['apisecret']) ? sanitize_text_field($key['apisecret']) : '';
	$chosenpassphrase = !empty($key['passphrase']) ? sanitize_text_field($key['passphrase']) : '';
	
	$type = !empty($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
	$message = !empty($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';
?>


<div class="wrap">
	<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<hr>
		
		<div class="<?php if(!empty($type)){echo esc_html($type);}?>"><?php if(!empty($message)){echo esc_html($message);}?></div>
		
		<!-- CREATE A AUTHENTICATION PAGE	-->
		<div style="display: block; margin-left: auto; margin-right: auto; width: 60%; margin-top: 4%; background-color: white; padding: 4%; border-bottom: 20px solid blue; border-radius: 10px;">
			<form action="" method="post">
			<h3 style="text-align: center;">Authentication Settings  </h3><hr>
			<div style="text-align: center;">Add the authentication details you got from your picknworksecurity account or you can click here to <br> <a href="https://www.picknwork.com/myaccount/api-setting">access your keys.</a></div>
			<br>
			<br>
			<div class="pc-admin-left">API KEY</div><div class="pc-admin-right"><input type="text" name="pnw_apikey" value="<?php echo __($chosenAuthID);?>" class="pc-admin-select-field"></div>
			<div style="clear: both;"></div>
			<div class="pc-admin-left">API SECRET</div><div class="pc-admin-right"><input type="text" name="pnw_apisecret" value="<?php echo __($chosenapisecret);?>" class="pc-admin-select-field"></div>
			<div style="clear: both;"></div>
			<div class="pc-admin-left">PASSPHRASE</div><div class="pc-admin-right"><input type="password" name="pnw_passphrase" value="" class="pc-admin-select-field"></div>
			<div class="pc-admin-left"></div>
			<div class="pc-admin-right"><input type="submit" name="submit_authentication" value="Update Authentication" class="pnwbutton"></div> 
			<div style="clear: both;"></div>
			</form>
			

		</div>
<?php 
$_SESSION['pnw-type'] = '';
$_SESSION['pnw-message'] = '';
?>