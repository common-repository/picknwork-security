<?php
global $allsetting, $wpdb;


$type = isset($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
$message = isset($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';

$noquery = 'donothing';
$reload = pnw_remove_query($noquery);

$roles = pnw_get_all_roles();
$rolesNoAdmin = $roles;
$find = array_search('administrator', $rolesNoAdmin);
unset($rolesNoAdmin[$find]);

$rolesWithAdmin = $roles;


$pnwalloptionWp = pnw_get_all_option();

// Get list of wordpress roles
$pages = get_pages();
foreach($pages as $page){
	
	// For login redirect from API
	$loginredirect = !empty($allsetting['enable_pnwlogin_redirect']) ? $allsetting['enable_pnwlogin_redirect'] : 'Select Page';
	$loginredirect_selected = ($page->post_name === $loginredirect) ? 'selected' : '';
	$loginredirect_pageoption[] = '<option value="'.$page->post_name.'"'.$loginredirect_selected.'>'.$page->post_name.' </option>';
	
	// For dashboard lock redirect
	$dashredirect = !empty($pnwalloptionWp['setting_dashlock_redirect']) ? $pnwalloptionWp['setting_dashlock_redirect'] : 'Select Page';
	$dashredirect_selected = ($page->post_name === $dashredirect) ? 'selected' : '';
	$dashredirect_pageoption[] = '<option value="'.$page->post_name.'"'.$dashredirect_selected.'>'.$page->post_name.' </option>';
	
	// For Forced Phone redirect
	$forcedphoneredirect = !empty($pnwalloptionWp['enable_forcedphoneverify_redirect']) ? $pnwalloptionWp['enable_forcedphoneverify_redirect'] : 'Select Page';
	$forcedphoneredirect_selected = ($page->post_name === $forcedphoneredirect) ? 'selected' : '';
	$forcedphoneredirect_pageoption[] = '<option value="'.$page->post_name.'"'.$forcedphoneredirect_selected.'>'.$page->post_name.' </option>';
}

		
#####################################
// BLOCK WORDPRESS ADMIN DASHBOARD FOR NON ADMINS 
####################################### 
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enabledashboardlock'])){

	unset($_POST['submit_enabledashboardlock']);
	
	$_POST['setting_enable_dashboard_lock'] = (!empty($_POST['setting_enable_dashboard_lock'])) ? sanitize_text_field($_POST['setting_enable_dashboard_lock']) : 'off';
	
	if(!empty($_POST)){
		foreach($_POST as $key => $value){	
			pnw_update_option($key, sanitize_text_field($value));	
		}
	}

	$_SESSION['pnw-type'] = 'pnw-success';
	$_SESSION['pnw-message'] = 'Dashboard lock setting updated successfully';
	
	exit(wp_redirect($reload));
	
}	


$enabledDashboardLock = !empty($pnwalloptionWp['setting_enable_dashboard_lock']) ? $pnwalloptionWp['setting_enable_dashboard_lock'] : '';
$checkedDashboardLock = ($enabledDashboardLock === 'on') ? 'checked' : '';
	
?>

<hr>
<div class="<?php echo esc_html_e($type);?>"><?php echo esc_html_e($message);?></div>
<br>
<br>
<form action="" method="post">
<h3>Wordpress Admin Dashboard Restriction Settings </h3>
Block access to wordpress dashboard backend<br>
<br>
		

		
<div class="pc-admin-left">ENABLE WORDPRESS DASHBOARD LOCK </div><div class="pc-admin-right"><input type="checkbox" name="setting_enable_dashboard_lock" value="on" <?php esc_html_e($checkedDashboardLock);?> id="pnw_enabledashboardlock" class="pnwcheckbox"> 
<label for="pnw_enabledashboardlock" class="pnwtoggle"><p><span class="ontext">ON</span><span class="offtext">OFF</span></p></label></div>
<div style="clear: both;"></div>
<br>

<div class="pc-admin-left">REDIRECT PAGE</div><div class="pc-admin-right"><select name="setting_dashlock_redirect" class="pc-admin-select-field"><?php echo implode('', $dashredirect_pageoption); ?></select></div>
<div style="clear: both;"></div>
<br>
<br>

	

<!-- CHOOSE BLOCK WORDPRESS ADMIN DASHBOARD BASED ON USER ROLE SETTING -->

<div class="dashboard_list_table_tips"> If You Enabled Dashboard Lock, you can set lock features by role</div>
<div class="tableDiv">
<table class="pnw-list-roles">
<th class="pc-role-head">Roles</th>

<th class="pc-role-option-head">Action</th>		
<?php
	foreach($rolesNoAdmin as $listrole1){
				
		$pullchosen = !empty($pnwalloptionWp['dashlock_'.$listrole1]) ? $pnwalloptionWp['dashlock_'.$listrole1] : 'off';
		$selectedon = ($pullchosen === 'on') ? 'selected' : '';
		$selectedoff = ($pullchosen === 'off') ? 'selected' : '';
		
	echo '<tr class="pc-list-row"><td class="pc-role-col">' . ucfirst($listrole1) . '</td><td class="pc-role-action-head"><select name="dashlock_'.$listrole1.'"><option value="off" '.$selectedoff.'>OFF</option><option value="on" '.$selectedon.'>ON</option></select></td></tr>';
							
	}
?>
	</tr>
	</table>
	</div>
	
	<br><input type="submit" name="submit_enabledashboardlock" value="Update Dashboard Lock" class="pnwbutton"> 
</form>	
<!-- END OF WORDPRESS DASHBOARD LOCK SETTING -->			
		
		
			
		
<!-- 
#####################################		
ENABLE PICKNWORK LOGIN SECURITY 
#####################################
-->
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enable_pnwlogin'])){
	
	$enablepnwLogin = (!empty($_POST['enable_pnwlogin'])) ? sanitize_text_field($_POST['enable_pnwlogin']) : 'off';
	
	$loginRedirect = (!empty($_POST['enable_pnwlogin_redirect'])) ? sanitize_text_field($_POST['enable_pnwlogin_redirect']) : '';
	
	$more_data = array(
	'setting_enable_pnwlogin' => $enablepnwLogin,
	'full' => $_POST
	);

	$run = pnw_push('setting', 'setting_enable_pnwlogin', $more_data);
	
	$_SESSION['pnw-type'] = !empty($run['type']) ? $run['type'] : '';
	$_SESSION['pnw-message'] = !empty($run['message']) ? $run['message'] : '';
	exit(wp_redirect($reload));
	
	
}			
	$setenablepnwLogin = !empty($allsetting['enable_pnwlogin']) ? $allsetting['enable_pnwlogin'] : 'off';
	$checkedLogin = ($setenablepnwLogin === 'on') ? 'checked' : '';
	
	$setloginRedirect = !empty($allsetting['enable_pnwlogin_redirect']) ? $allsetting['enable_pnwlogin_redirect'] : '';
	
?>
<hr>

<br>
<br>
<form action="" method="post">
<h3>Enable PNW Login Security </h3>
Turn on pnw login protection for more advanced login security, <br>
Note: once is turned on any other login operation that is not done through [pnw_login_form] will be declined and termianted immediately<br>
<br>
<br>

<div class="pc-admin-left">ENABLE PICKNWORK LOGIN SECURITY </div><div class="pc-admin-right"><input type="checkbox" name="enable_pnwlogin" value="on" <?php echo $checkedLogin;?> id="checkpnwlogin" class="pnwcheckbox"> 
<label for="checkpnwlogin" class="pnwtoggle"><p><span class="ontext">ON</span><span class="offtext">OFF</span></p></label></div> 
<div style="clear: both;"></div>
<br>
<div class="pc-admin-left">REDIRECT PAGE</div><div class="pc-admin-right"><select name="enable_pnwlogin_redirect" class="pc-admin-select-field"><?php echo implode('', $loginredirect_pageoption); ?></select></div>
<div style="clear: both;"></div>
<br>
<br>

<div class="dashboard_list_table_tips"> If You Enabled pnw Login Security, you can set lock features by role</div>
<div class="tableDiv">
<table class="pnw-list-roles">
<th class="pc-role-head">Roles</th>
<th class="pc-role-option-head">Action</th>

<?php
foreach($rolesWithAdmin as $listrole2){
	
	$setLogin = !empty($allsetting['pnwlogin_'.$listrole2]) ? $allsetting['pnwlogin_'.$listrole2] : 'Standard';
	
	$Standard = ($setLogin === 'Standard') ? 'selected' : '';
	$StandardWithEmailOtp = ($setLogin === 'StandardWithEmailOtp') ? 'selected' : '';
	$StandardWithSmsOtp = ($setLogin === 'StandardWithSmsOtp') ? 'selected' : '';
	$StandardWithPnwAPP = ($setLogin === 'StandardWithPnwAPP') ? 'selected' : '';
	$ClickLoginWithEmail = ($setLogin === 'ClickLoginWithEmail') ? 'selected' : '';
	$ClickLoginWithSms = ($setLogin === 'ClickLoginWithSms') ? 'selected' : '';
	$ClickLoginWithPnwAPP = ($setLogin === 'ClickLoginWithPnwAPP') ? 'selected' : '';
	
	
	echo '<tr class="pc-list-row"><td class="pc-role-col">' . ucfirst($listrole2) . '</td>
	<td class="pc-role-action-head"><select name="pnwlogin_'.$listrole2.'">
	<option value="Standard" '.$Standard.'>Standard</option>
	<option value="StandardWithEmailOtp" '.$StandardWithEmailOtp.'>Standard with Email OTP</option>
	<option value="StandardWithSmsOtp" '.$StandardWithSmsOtp.'>Standard with SMS OTP</option>
	<option value="StandardWithPnwAPP" '.$StandardWithPnwAPP.'>Standard with pnw APP</option>
	<option value="ClickLoginWithEmail" '.$ClickLoginWithEmail.'>1Click Login with Email</option>
	<option value="ClickLoginWithSms" '.$ClickLoginWithSms.'>1Click Login with SMS</option>
	<option value="ClickLoginWithPnwAPP" '.$ClickLoginWithPnwAPP.'>1Click Login with pnw APP</option>
	</select>
	</td>
	</tr>';						
}
?>
</table>
</div>
<input type="submit" name="submit_enable_pnwlogin" value="Update Login Security" class="pnwbutton"> 
</form>
<hr>
<br>
<br>
<!-- END OF LOGIN LOCK SETTING -->			
		

	
	
	
<!-- 
###################################
ENABLE WOOCOMMERCE CHECKOUT OTP 
###################################
-->	
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enable_woocommerce_checkout_otp'])){
	
	$enableWoocheckout = (!empty($_POST['enable_woocommerce_checkout_otp'])) ? sanitize_text_field($_POST['enable_woocommerce_checkout_otp']) : 'Disabled';

	$more_data = array(
	'enable_woocommerce_checkout_otp' => $enableWoocheckout
	);

	$run = pnw_push('setting', 'setting_enable_woocommerce_otp', $more_data);
	
	$_SESSION['pnw-type'] = !empty($run['type']) ? $run['type'] : '';
	$_SESSION['pnw-message'] = !empty($run['message']) ? $run['message'] : '';
	exit(wp_redirect($reload));

}	

	$wooCheckout = !empty($allsetting['enable_woocommerce_checkout_otp']) ? $allsetting['enable_woocommerce_checkout_otp'] : '';
	$wooCheckoutDisabled = ($wooCheckout === 'disabled') ? 'selected' : '';
	$wooCheckoutEnabled = ($wooCheckout === 'enabled') ? 'selected' : '';
	
?>	
<form action="" method="post">
	<h3>Woocommerce Checkout Authentication Settings ***coming soon***</h3>
	

	Enable this option if you would want users to authencate their order purchase before checking out
	<br>
	<br>
	
	<div class="pc-admin-left">ENABLE WOOCOMMERCE CHECKOUT OTP </div><div class="pc-admin-right"><select name="enable_woocommerce_checkout_otp" class="pc-admin-select-field">
	<option value="disabled" <?php esc_html_e($wooCheckoutDisabled);?>>Disabled</option>
	<option value="enabled" <?php esc_html_e($wooCheckoutEnabled);?>>Enabled</option>
	</select></div>
	<div style="clear: both;"></div>
	
	<br><input type="submit" name="submit_enable_woocommerce_checkout_otp" value="Update Woocommerce Checkout" class="pnwbutton"> 
</form>
<hr>
<br>
<br>

<!-- END OF ENABLE WOOCOMERCE CHECKOUT SETTING -->




<!--
################################## 
ENABLE WOOCOMMERCE ORDER SMS 
##################################
-->
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enable_woocommerce_order_sms'])){
	
	$enableWoocommerceOrderSMS = (!empty($_POST['enable_woocommerce_order_sms'])) ? sanitize_text_field($_POST['enable_woocommerce_order_sms']) : 'off';

	$more_data = array(
	'enable_woocommerce_order_sms' => $enableWoocommerceOrderSMS
	);

	$run = pnw_push('setting', 'setting_enable_woocommerce_order_sms', $more_data);
	
	$_SESSION['pnw-type'] = !empty($run['type']) ? $run['type'] : '';
	$_SESSION['pnw-message'] = !empty($run['message']) ? $run['message'] : '';
	exit(wp_redirect($reload));

}	

	$chosenWoocommerceOrderSMS = !empty($allsetting['enable_woocommerce_order_sms']) ? $allsetting['enable_woocommerce_order_sms'] : '';
	$checkedOrderSMS = ($chosenWoocommerceOrderSMS == 'on') ? 'checked' : '';
?>	
<form action="" method="post">
	<h3>Woocommerce Order Notification SMS Settings ***coming soon***</h3>
	
	Enable this option if you would want send sms to customers who purchased item on your woocoommerce store
	<br>
	<br>

	<div class="pc-admin-left">ENABLE WOOCOMMERCE ORDER SMS </div><div class="pc-admin-right"><input type="checkbox" name="enable_woocommerce_order_sms" value="on" <?php echo $checkedOrderSMS;?> id="enablewoocomerceordersms" class="pnwcheckbox"> 
	<label for="enablewoocomerceordersms" class="pnwtoggle"><p><span class="ontext">ON</span><span class="offtext">OFF</span></p></label> </div>
	<div style="clear: both;"></div>
	<br><input type="submit" name="submit_enable_woocommerce_order_sms" value="Update Woocommerce Order SMS" class="pnwbutton"> 
</form>
<hr>
<br>
<br>		
<!-- END WOOCOMERCE ORDER SMS -->		
	



<!-- 
##################################
ENABLE COMMENT BOX PROTECTION 
##################################
-->	
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enable_comment_protection'])){
	
	$enableCommentBox = (!empty($_POST['enable_comment_protection'])) ? sanitize_text_field($_POST['enable_comment_protection']) : 'off';

	$more_data = array(
	'enable_comment_protection' => $enableCommentBox
	);
	
	$run = pnw_push('setting', 'setting_enable_comment_protection', $more_data);
	
	$_SESSION['pnw-type'] = !empty($run['type']) ? $run['type'] : '';
	$_SESSION['pnw-message'] = !empty($run['message']) ? $run['message'] : '';
	exit(wp_redirect($reload));

}	
	$enabledComment = !empty($allsetting['enable_comment_protection']) ? $allsetting['enable_comment_protection'] : ''; 
	$commentDisbaled = ($enabledComment === 'disabled') ? 'selected' : '';
	$commentWithSms = ($enabledComment === 'enabledwithsms') ? 'selected' : '';
	$commentWithEmail = ($enabledComment === 'enabledwithemail') ? 'selected' : '';
	$commentWithPNWApp = ($enabledComment === 'enabledwithpnwapp') ? 'selected' : '';
?>		
<form action="" method="post">
	<h3>Comment Box Protection Settings ***coming soon***</h3>
	
	Enable this option if you would to stop spam commenters on your site with ultra modern spam fighter
	<br>
	<br>
		
	<div class="pc-admin-left">ENABLE COMMENT BOX PROTECTION </div><div class="pc-admin-right"><select name="enable_comment_protection" class="pc-admin-select-field">
	<option value="disabled" <?php esc_html_e($commentDisbaled);?>>Disabled</option>
	<option value="enabledwithsms" <?php esc_html_e($commentWithSms);?>>Enabled with SMS</option>
	<option value="enabledwithemail" <?php esc_html_e($commentWithEmail);?>>Enabled with Email</option>
	<option value="enabledwithpnwapp" <?php esc_html_e($commentWithPNWApp);?>>Enabled with pnw APP</option>
	</select></div>
	<div style="clear: both;"></div>
	<br><input type="submit" name="submit_enable_comment_protection" value="Update Comment Protection" class="pnwbutton"> 
</form>
<hr>
<br>
<br>
<!-- END OF COMMENT BOX SETTING -->




<!-- 
###############################
ENABLE FORCE PHONE VERIFICATION 
###############################
-->
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_enable_forcephoneverify'])){
	
	unset($_POST['submit_enable_forcephoneverify']);
	
	$_POST['enable_forcedphoneverify'] = (!empty($_POST['enable_forcedphoneverify'])) ? sanitize_text_field($_POST['enable_forcedphoneverify']) : 'off';
	
	if(!empty($_POST)){
		foreach($_POST as $key => $value){	
			pnw_update_option($key, sanitize_text_field($value));	
		}
	}

	$_SESSION['pnw-type'] = 'pnw-success';
	$_SESSION['pnw-message'] = 'Forced phone verification setting updated successfully';
	
	exit(wp_redirect($reload));

}	


$enabledForcedPhoneVerify = !empty($pnwalloptionWp['enable_forcedphoneverify']) ? $pnwalloptionWp['enable_forcedphoneverify'] : '';
$checkedForcedPhoneVerify = ($enabledForcedPhoneVerify === 'on') ? 'checked' : '';
	
?>
<br>
<br>
<form action="" method="post">
<div class="pc-admin-left">ENABLE FORCE PHONE VERIFY </div><div class="pc-admin-right"><input type="checkbox" name="enable_forcedphoneverify" value="on" <?php esc_html_e($checkedForcedPhoneVerify);?> id="pnw_enableforcedphoneverify" class="pnwcheckbox"> 
<label for="pnw_enableforcedphoneverify" class="pnwtoggle"><p><span class="ontext">ON</span><span class="offtext">OFF</span></p></label></div>
<div style="clear: both;"></div>
<br>

<div class="pc-admin-left">REDIRECT PAGE</div><div class="pc-admin-right"><select name="enable_forcedphoneverify_redirect" class="pc-admin-select-field"><?php echo implode('', $forcedphoneredirect_pageoption); ?></select></div>
<div style="clear: both;"></div>
<br>
<br>

	

<!-- CHOOSE FORCE PHONE VERIFY BASED ON USER ROLE SETTING -->

<div class="dashboard_list_table_tips"> If You Enabled Forced Phone Verify, you can set lock features by role</div>
<div class="tableDiv">
<table class="pnw-list-roles">
<th class="pc-role-head">Roles</th>

<th class="pc-role-option-head">Action</th>		
<?php
	foreach($rolesNoAdmin as $listrole1){
				
		$pullchosen = !empty($pnwalloptionWp['forcephoneverify_'.$listrole1]) ? $pnwalloptionWp['forcephoneverify_'.$listrole1] : 'off';
		$selectedon = ($pullchosen === 'on') ? 'selected' : '';
		$selectedoff = ($pullchosen === 'off') ? 'selected' : '';
		
	echo '<tr class="pc-list-row"><td class="pc-role-col">' . ucfirst($listrole1) . '</td><td class="pc-role-action-head"><select name="forcephoneverify_'.$listrole1.'"><option value="off" '.$selectedoff.'>OFF</option><option value="on" '.$selectedon.'>ON</option></select></td></tr>';
							
	}
?>
	</tr>
	</table>
	</div>
	
	<br><input type="submit" name="submit_enable_forcephoneverify" value="Update Forced Phone Verify" class="pnwbutton"> 
</form>
<hr>
<br>
<br>
<?php
	$_SESSION['pnw-type'] = '';
	$_SESSION['pnw-message'] = '';
?>