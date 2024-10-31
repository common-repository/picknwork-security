<?php
// ################################################
// BUILD A RESPONSIVE LOGIN FORM
// ###############################################
add_shortcode('pnw_login_form', 'picknworksecurity_login_form');
function picknworksecurity_login_form() {
	global $leapid, $wpdb;
	
	$allsetting = pnw_get_all_setting();
	$redirect = !empty($allsetting['setting_login_redirect']) ? sanitize_text_field($allsetting['setting_login_redirect']) : '';
	$link = !empty($redirect) ? pnw_get_page_url('name', $redirect) : site_url() . '/wp-admin/';
	
	$query = 'none';
	$cancel = pnw_remove_query($query);
	
	$sessiontype = !empty($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
	$sessionmessage = !empty($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';
	$type = '';
	$message = '';
	$sendOtpClass = '';
	$sendOtpMessage = '';
	$loginSetting = '';


	$logo = get_theme_mod('custom_logo');
	$image =  wp_get_attachment_image_src($logo, 'full');
	$logourl = $image[0];

	//Get some varaible
	$postusername = (!empty($_POST['pnwLoginUsername'])) ? sanitize_text_field($_POST['pnwLoginUsername']) : '';
	$postpassword = (!empty($_POST['pnwLoginPassword'])) ? sanitize_text_field($_POST['pnwLoginPassword']) : '';
	$postotp = (!empty($_POST['pnwConfirmOtp'])) ? sanitize_text_field($_POST['pnwConfirmOtp']) : '';
	$posttemppassword = !empty($_POST['pnwTempPassword']) ? sanitize_text_field($_POST['pnwTempPassword']) : $postpassword;
	$postresendotp = !empty($_POST['pnwResendOtp']) ? sanitize_text_field($_POST['pnwResendOtp']) : '';
	$stage = !empty($_POST['stage']) ? sanitize_text_field($_POST['stage']) : '';

	// form fields
	$usernameform = '<div class="pnwlogintitleDiv">Username</div>
	<div class="pnwloginfieldDiv"><input type="text" name="pnwLoginUsername" value="'.esc_html($postusername).'" placeholder="Enter Your Username or email" id="pnwusername" class="pnwloginfield"></div>
	<input type="hidden" name="stage" value="stagelogin" id="pnw_submit_login" class="pnwloginbutton">';

	$passwordform = '<input type="hidden" name="pnwLoginUsername" value="'.esc_html($postusername).'">
	<div class="pnwlogintitleDiv">Password <span class="pnwforget"><a href="?resetpassword">Forget?</a></span></div>
	<div class="pnwloginfieldDiv"><input type="password" name="pnwLoginPassword" placeholder="Enter Your Password" id="pnwpassword" class="pnwloginfield"></div>
	<input type="hidden" name="stage" value="stagepassword" id="pnw_submit_login" class="pnwloginbutton">';

	$otpform = '<input type="hidden" name="pnwLoginUsername" value="'.esc_html($postusername).'">
	<div class="pnwlogintitleDiv">Enter OTP <span style="float: right; margin-right: 2%;"><input type="checkbox" name="pnwResendOtp" value="resend" id="pnwresendotp"> 
	<label for="pnwresendotp" class="resendotplabel">Resend OTP</label></span></div>
	<div class="pnwloginfieldDiv"><input type="text" name="pnwConfirmOtp" value="'.esc_html($postotp).'" placeholder="Enter OTP" id="pnwconfirmotp" class="pnwloginfield"></div>
	<input type="hidden" name="stage" value="stageotp" id="pnw_submit_login" class="pnwloginbutton">';
	
	$showform = $usernameform;


	/* Start full form process
	**************************/	
	if($_SERVER['REQUEST_METHOD'] === 'POST'){

		// Validations
		if(isset($_POST['pnwLoginUsername']) && empty($_POST['pnwLoginUsername'])){
		$type = 'pnw-error';
		$message = 'Please enter a user name or email address';
		$showform = $usernameform;

		}

		if(isset($_POST['pnwLoginPassword']) && empty($_POST['pnwLoginPassword'])){
		$type = 'pnw-error';
		$message = 'Please enter your password';
		$showform = $passwordform;

		}

		if(isset($_POST['pnwConfirmOtp']) && empty($_POST['pnwConfirmOtp']) && $postresendotp != 'resend'){
		$type = 'pnw-error';
		$message = 'Please enter your Authentication Code';
		$showform = $otpform;
		}


		// search the user option to know what next
		if(isset($_POST['pnwsubmitlogin']) || $postresendotp === 'resend'){
			$user = get_user_by('login', $postusername);
			$userid = !empty($user->ID) ? $user->ID : '';
			$foundEmail = !empty($user->user_email) ? $user->user_email : '';
			$foundPhone = !empty($user->pnwphonenumber) ? $user->pnwphonenumber : '';
			$foundPnwApp = !empty($user->pnwapp) ? $user->pnwapp : '';
			$foundRole = !empty($user->roles[0]) ? $user->roles[0] : '';
			$foundPassword = !empty($user->user_pass) ? $user->user_pass : '';
			$foundLoginSetting = 'pnwlogin_'.$foundRole;
			$foundTrustkey = !empty($user->pnwtrustkey) ? $user->pnwtrustkey : '';

			$loginSetting = !empty($allsetting[$foundLoginSetting]) ? $allsetting[$foundLoginSetting] : '';
		}



		/* Process login for unknown login setting as to protect users 
		name from being known if is correct or wrong 
		**************************************************************/
		$allowed = array(
		'Standard', 
		'StandardWithEmailOtp', 
		'StandardWithSmsOtp', 
		'StandardWithPnwAPP', 
		'ClickLoginWithEmail', 
		'ClickLoginWithSms', 
		'ClickLoginWithPnwAPP'
		);
		
		if(!in_array($loginSetting, $allowed) && isset($_POST['pnwLoginUsername']) && !empty($_POST['pnwLoginUsername'])) {

			$showform = $passwordform;

			if(isset($_POST['pnwLoginPassword'])){

				if(wp_check_password($postpassword, $foundPassword)){
				// sign user in and redirect to set page
					$cred = array ();
					$cred['user_login'] = $postusername;
					$cred['user_password'] = $postpassword;
					$logon = wp_signon($cred, false);
					exit(wp_redirect($link));

				}else{
					$type = 'pnw-error';
					$message = 'Incorrect username or password';
					$showform = $usernameform;

				}
			}
		}
		/* End of Process login for unknown login setting */


		/* Process login for standard setting 
		***************************************/
		if($loginSetting === 'Standard') {

			$showform = $passwordform;

			if(isset($_POST['pnwLoginPassword'])){

				if(wp_check_password($postpassword, $foundPassword)){
					// sign user in and redirect to set page
					$cred = array ();
					$cred['user_login'] = $postusername;
					$cred['user_password'] = $postpassword;
					$logon = wp_signon($cred, false);
					exit(wp_redirect($link));

				}else{
					$type = 'pnw-error';
					$message = 'Incorrect username or password';
					$showform = $usernameform;

				}	
			}
		}



		/* Process login for standard with Email Otp
		**********************************************/
		if($loginSetting === 'StandardWithEmailOtp'){
			$showform = $passwordform;

			if(isset($_POST['pnwLoginPassword'])){

				if(wp_check_password($postpassword, $foundPassword)){

					// update user trustkey
					$trustkey = pnw_generate_trustkey();
					update_user_meta($userid, 'pnwtrustkey', $trustkey);

					// Send otp to the user email and update opt base
					$more_data = array(
					'send_otp' => 'send_otp',
					'trustkey' => $trustkey,
					'mode' => 'email',
					'email' => $foundEmail,
					'comment' => 'login'
					);

					$run = pnw_push('otp', 'send_otp', $more_data);
					$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
					$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
					$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

					if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
					}
					if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
					}
					$showform = $otpform;

					// reverse back to login form if the user is blocked
					if($comment === 'blocked'){
					$showform = $usernameform; 
					}
				}else{
					$type = 'pnw-error';
					$message = 'Incorrect username or password';
					$showform = $usernameform;
				}
			}

			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';
				$showform = $otpform;

				if($replyComment === 'blocked'){
				$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){
					
					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);
					
					if($replyTrustkey === $usertrustkey){
						// Login back after 
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);
						exit(wp_redirect($link));
					}
				}

				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}
		}
		/* End of Process login for standard with Email Otp */



		/* Process login for standard with SMS otp 
		***************************************/
		if($loginSetting === 'StandardWithSmsOtp'){
			$showform = $passwordform;

			if(isset($_POST['pnwLoginPassword'])){

				if(wp_check_password($postpassword, $foundPassword)){

					// update user trustkey
					$trustkey = pnw_generate_trustkey();
					update_user_meta($userid, 'pnwtrustkey', $trustkey);

					// Send otp to the user email and update opt base
					$more_data = array(
					'send_otp' => 'send_otp',
					'trustkey' => $trustkey,
					'mode' => 'phone',
					'phone' => $foundPhone,
					'comment' => 'login'
					);

					$run = pnw_push('otp', 'send_otp', $more_data);
					$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
					$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
					$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

					if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
					}
					if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
					}
					$showform = $otpform;

					// reverse back to login form if the user is blocked
					if($comment === 'blocked'){
					$showform = $usernameform; 
					}
				}else{
					$type = 'pnw-error';
					$message = 'Incorrect username or password';
					$showform = $usernameform;
				}
			}

			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';
				$showform = $otpform;

				if($replyComment === 'blocked'){
				$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){

					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						// Login back after 
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);
						exit(wp_redirect($link)); 
					}
				}

				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}
		}
		/* End of Process login for standard with SMS otp */


		/* Process login for standard PnwApp 
		***************************************/
		if($loginSetting === 'StandardWithPnwAPP'){
			$showform = $passwordform;

			if(isset($_POST['pnwLoginPassword'])){

				if(wp_check_password($postpassword, $foundPassword)){

					// update user trustkey
					$trustkey = pnw_generate_trustkey();
					update_user_meta($userid, 'pnwtrustkey', $trustkey);

					// Send otp to the user email and update opt base
					$more_data = array(
					'send_otp' => 'send_otp',
					'trustkey' => $trustkey,
					'mode' => 'pnwapp',
					'pnwapp' => $foundPnwApp,
					'comment' => 'login'
					);

					$run = pnw_push('otp', 'send_otp', $more_data);
					$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
					$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
					$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

					if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
					}
					if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
					}
					$showform = $otpform;

					// reverse back to login form if the user is blocked
					if($comment === 'blocked'){
					$showform = $usernameform; 
					}
				}else{
					$type = 'pnw-error';
					$message = 'Incorrect username or password';
					$showform = $usernameform;
				}
			}

			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';
				$showform = $otpform;

				if($replyComment === 'blocked'){
				$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){

					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						// Login back after 
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);
						exit(wp_redirect($link)); 
					}
				}

				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}

		}
		/* End of Process login for standard PnwApp */


		/* Process login for 1 click login with email 
		***************************************/
		if($loginSetting === 'ClickLoginWithEmail'){

			if(isset($_POST['pnwLoginUsername']) && $stage === 'stagelogin' && $postresendotp != 'resend'){

				$showform = $otpform;
				// update user trustkey
				$trustkey = pnw_generate_trustkey();
				update_user_meta($userid, 'pnwtrustkey', $trustkey);

				// Send otp to the user email and update opt base
				$more_data = array(
				'send_otp' => 'send_otp',
				'trustkey' => $trustkey,
				'mode' => 'email',
				'email' => $foundEmail,
				'comment' => 'login',
				'extra' => 'yes'
				);

				$run = pnw_push('otp', 'send_otp', $more_data);
				$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
				$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
				$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

				if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
				}
				if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
				}

				$showform = $otpform;

				// reverse back to login form if the user is blocked
				if($comment === 'blocked'){
					$showform = $usernameform; 
				}
			}


			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				$sendOtpClass = '';
				$sendOtpMessage = '';

				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';

				$showform = $otpform;

				if($replyComment === 'blocked'){
					$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){

					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						// Login back after updating his old user password
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);

						exit(wp_redirect($link)); 
					}
				}
				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}	
		}
		/* End of Process login for 1 click login with email */



		/* Process login for 1 click login with SMS
		***************************************/
		if($loginSetting === 'ClickLoginWithSms'){
			if(isset($_POST['pnwLoginUsername']) && $stage === 'stagelogin' && $postresendotp != 'resend'){

				$showform = $otpform;
				// update user trustkey
				$trustkey = pnw_generate_trustkey();
				update_user_meta($userid, 'pnwtrustkey', $trustkey);

				// Send otp to the user email and update opt base
				$more_data = array(
				'send_otp' => 'send_otp',
				'trustkey' => $trustkey,
				'mode' => 'phone',
				'phone' => $foundPhone,
				'comment' => 'login',
				'extra' => 'yes'
				);

				$run = pnw_push('otp', 'send_otp', $more_data);
				$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
				$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
				$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

				if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
				}
				if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
				}

				$showform = $otpform;

				// reverse back to login form if the user is blocked
				if($comment === 'blocked'){
					$showform = $usernameform; 
				}
			}


			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				$sendOtpClass = '';
				$sendOtpMessage = '';

				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';

				$showform = $otpform;

				if($replyComment === 'blocked'){
					$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){

					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						// Login back after updating his old user password
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);

						exit(wp_redirect($link)); 
					}
				}
				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}
		}
		/* End of Process login for 1 click login with SMS */


		/* Process login for 1 clcik login with pnwapp
		***********************************************/
		if($loginSetting === 'ClickLoginWithPnwAPP'){
			if(isset($_POST['pnwLoginUsername']) && $stage === 'stagelogin' && $postresendotp != 'resend'){

				$showform = $otpform;
				// update user trustkey
				$trustkey = pnw_generate_trustkey();
				update_user_meta($userid, 'pnwtrustkey', $trustkey);

				// Send otp to the user email and update opt base
				$more_data = array(
				'send_otp' => 'send_otp',
				'trustkey' => $trustkey,
				'mode' => 'pnwapp',
				'pnwapp' => $foundPnwApp,
				'comment' => 'login',
				'extra' => 'yes'
				);

				$run = pnw_push('otp', 'send_otp', $more_data);
				$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
				$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
				$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

				if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
				}
				if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
				}

				$showform = $otpform;

				// reverse back to login form if the user is blocked
				if($comment === 'blocked'){
					$showform = $usernameform; 
				}
			}


			if(isset($_POST['pnwConfirmOtp']) && $stage === 'stageotp' && $postresendotp != 'resend'){
				$sendOtpClass = '';
				$sendOtpMessage = '';

				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => $foundTrustkey,
				'postotp' => $postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';

				$showform = $otpform;

				if($replyComment === 'blocked'){
					$showform = $usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'login'){

					$user = get_user_by('login', $postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						// Login back after updating his old user password
						wp_cache_delete($userid,'users');
						wp_cache_delete($userlogin,'userlogins');
						wp_logout();

						wp_set_current_user($userid, $userlogin);
						wp_set_auth_cookie($userid);
						do_action('wp_login', $userlogin);

						exit(wp_redirect($link)); 
					}
				}
				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			}
		}
		/* end of process login for iclick login with pnwapp */

		
	/* Process resend OTP code
	***************************/
		if($postresendotp === 'resend'){
			
			$array_extra = array('ClickLoginWithEmail', 'ClickLoginWithSms', 'ClickLoginWithPnwAPP');
			$extra = in_array($loginSetting, $array_extra) ? 'yes' : '';
			
			$more_data = array(
			'resend_otp' => 'resend_otp',
			'trustkey' => $foundTrustkey,
			'extra' => $extra
			); 

			$run = pnw_push('otp', 'resend_otp', $more_data);
			$replyType = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
			$replyMessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
			$replyComment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';

			if($replyType === 'pnw-success'){
				$sendOtpClass = 'sendOtpResponseDiv';
				$sendOtpMessage = $replyMessage;
			}else{
				$type = $replyType;
				$message = $replyMessage;		
			}
			$showform = $otpform;

			if($replyComment === 'blocked'){
				$showform = $usernameform;
			}
		}
		
	} 	/* end of if request is post */


	$lasttype = !empty($type) ? $type : $sessiontype;
	$lastmessage = !empty($message) ? $message : $sessionmessage;

	$form = '<div class="'.esc_html($lasttype).'">'.esc_html($lastmessage).'</div>
	<div class="pnwloginDiv">
	<div class="pnwloginlogo"><div class="pnwLoginLogoHolder"><img class="pnwloginimg" src="'.esc_html($logourl).'"></div></div>
	<div class="pnwloginformDiv">
	<p class="loginintrotext">Enter Login Details</p> <hr>
	<form action="" method="post" name="pnwloginform">

	'.$showform.'

	<input type="submit" name="pnwsubmitlogin" value="Login" id="pnw_submit_login" class="pnwloginbutton">
	</form>
	<div class="'.esc_html($sendOtpClass).'">'.esc_html($sendOtpMessage).'</div>
	</div>
	<div class="loginCancelDiv"><a class="logincancel" href="'.esc_html($cancel).'">Cancel</a></div>
	</div>';

	/* ############## END OF LOGIN WORKFLOW  ###################### 
	###############################################################*/
	
	
	/*  BEGIN RESET PASSWORD WORKFLOW */
	if(isset($_GET['resetpassword'])){
		
		$resetpasswordfile = realpath(PNWSEC_PATH . 'includes/reset-password.php');
		if(file_exists($resetpasswordfile)){
			include($resetpasswordfile);
			$form = $resetpasswordform;
		}
	}
	
	$_SESSION['pnw-type'] = '';
	$_SESSION['pnw-message'] = '';	
	return $form;
		
}
