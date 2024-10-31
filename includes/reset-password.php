<?php

	$sessiontype = !empty($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
	$sessionmessage = !empty($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';

	$cancelquery = 'resetpassword';
	$cancel = pnw_remove_query($cancelquery);
	
	$rp_postusername = !empty($_POST['pnwRPusername']) ? sanitize_text_field($_POST['pnwRPusername']) : '';
	$rp_postotp = !empty($_POST['pnwRPconfirmOtp']) ? sanitize_text_field($_POST['pnwRPconfirmOtp']) : '';
	
	$rp_postnewpassword = !empty($_POST['pnwRPnewpassword']) ? sanitize_text_field($_POST['pnwRPnewpassword']) : '';
	$rp_postretypepassword = !empty($_POST['pnwRPretypepassword']) ? sanitize_text_field($_POST['pnwRPretypepassword']) : '';
	
	$rp_postrptrustkey = !empty($_POST['pnwRPtrustkey']) ? sanitize_text_field($_POST['pnwRPtrustkey']) : '';
	$rp_posthiddentrustkey = !empty($_POST['pnwhiddentrustkey']) ? sanitize_text_field($_POST['pnwhiddentrustkey']) : $rp_postrptrustkey;
	
	

	// form fields
	$rp_usernameform = '<div class="pnwlogintitleDiv">Username</div>
	<div class="pnwloginfieldDiv"><input type="text" name="pnwRPusername" value="'.esc_html($rp_postusername).'" placeholder="Enter Your Username or email" id="pnwusername" class="pnwloginfield"></div>';

	$rp_otpform = '<input type="hidden" name="pnwRPusername" value="'.esc_html($rp_postusername).'">
	<div class="pnwlogintitleDiv">Enter OTP </div>
	<div class="pnwloginfieldDiv"><input type="text" name="pnwRPconfirmOtp" value="'.esc_html($rp_postotp).'" placeholder="Enter OTP" id="pnwconfirmotp" class="pnwloginfield"></div>';
	
	$rp_newpasswordform = '
	<input type="hidden" name="pnwhiddentrustkey" value="'.esc_html($rp_posthiddentrustkey).'">
	<input type="hidden" name="pnwRPusername" value="'.esc_html($rp_postusername).'">
	<div class="pnwlogintitleDiv">New Password </div>
	<div class="pnwloginfieldDiv"><input type="password" name="pnwRPnewpassword" value="'.esc_html($rp_postnewpassword).'" placeholder="Create New Password" id="pnwconfirmotp" class="pnwloginfield">
	<div class="pnwlogintitleDiv">Retype Password </div>
	<div class="pnwloginfieldDiv"><input type="password" name="pnwRPretypepassword" value="'.esc_html($rp_postretypepassword).'" placeholder="Re-enter New Password" id="pnwconfirmotp" class="pnwloginfield"></div>';
	
	$rpshowform = $rp_usernameform;
	
	
	/* Start processing the reset passsword form
	**************************/	
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['pnwsubmitresetpassword'])){


			// Validations
			if(empty($_POST['pnwRPusername']) && !isset($_POST['pnwRPconfirmOtp'])){
				$type = 'pnw-error';
				$message = 'Please enter a user name or email address';
				$rpshowform = $rp_usernameform;
			}

			if(isset($_POST['pnwRPconfirmOtp']) && empty($_POST['pnwRPconfirmOtp'])){
				$type = 'pnw-error';
				$message = 'Please enter your Authentication Code';
				$rpshowform = $rp_otpform;
			}


			if(filter_var($rp_postusername, FILTER_VALIDATE_EMAIL)){
				$rpuser = get_user_by('email', $rp_postusername);
			}else{
				$rpuser = get_user_by('login', $rp_postusername);
			}

			$rp_userid = !empty($rpuser->ID) ? $rpuser->ID : '';
			$rp_foundEmail = !empty($rpuser->user_email) ? $rpuser->user_email : '';
			$rp_foundPhone = !empty($rpuser->pnwphonenumber) ? $rpuser->pnwphonenumber : '';
			$rp_foundPnwApp = !empty($rpuser->pnwapp) ? $rpuser->pnwapp : '';
			$rp_foundRole = !empty($rpuser->roles[0]) ? $rpuser->roles[0] : '';
			$rp_foundPassword = !empty($rpuser->user_pass) ? $rpuser->user_pass : '';
			$rp_foundTrustkey = !empty($rpuser->pnwtrustkey) ? $rpuser->pnwtrustkey : '';



			if(isset($_POST['pnwRPusername']) && !isset($_POST['pnwRPconfirmOtp']) && !isset($_POST['pnwRPnewpassword'])){

				$rpshowform = $rp_otpform;

				// ONLY RUN IF THE USER WAS FOUND
				if(!empty($rp_foundEmail)){

					// update user trustkey
					$rp_trustkey = pnw_generate_trustkey();
					update_user_meta($rp_userid, 'pnwtrustkey', $rp_trustkey);

					// Send otp to the user email and update opt base
					$more_data = array(
					'send_otp' => 'send_otp',
					'trustkey' => $rp_trustkey,
					'mode' => 'email',
					'email' => $rp_foundEmail,
					'comment' => 'resetpassword',
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

					$rpshowform = $rp_otpform;

					// reverse back to login form if the user is blocked
					if($comment === 'blocked'){
						$rpshowform = $rp_usernameform; 
					}
				}
			} /* End of if pnwusername isset */



			if(isset($_POST['pnwRPconfirmOtp']) && !isset($_POST['pnwRPnewpassword'])){
				$sendOtpClass = '';
				$sendOtpMessage = '';
				
				// confirm otp
				$more_data = array(
				'verify_otp' => 'verify_otp',
				'trustkey' => !empty($rp_foundTrustkey) ? $rp_foundTrustkey : 'unknownuser',
				'postotp' => $rp_postotp
				);

				$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
				$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
				$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
				$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
				$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';

				$rpshowform = $rp_otpform;

				if($replyComment === 'blocked'){
					$rpshowform = $rp_usernameform;
				}

				if($replyMessage === 'correct' && $replyComment === 'resetpassword'){

					$user = get_user_by('login', $rp_postusername);
					$userid = $user->ID;
					$userlogin = $user->user_login;

					$usertrustkey = get_user_meta($userid, 'pnwtrustkey', true);

					if($replyTrustkey === $usertrustkey){
						$rpshowform = '<input type="hidden" name="pnwRPtrustkey" value="'.$replyTrustkey.'">' . $rp_newpasswordform;
					}
				}
				$type = (!empty($replyType) && $replyType != 'pnw-success') ? $replyType : '';
				$message = (!empty($replyMessage) && $replyType != 'pnw-success') ? $replyMessage : '';
			}
			/* End of if confirmedotp is set */
			
			
			if(isset($_POST['pnwRPnewpassword'])){
				
				$rpshowform = $rp_newpasswordform;
				
				// validate new password
				if(empty($rp_postnewpassword)){
					$type = 'pnw-error';
					$message = 'Please enter new password';
					$rpshowform = $rp_newpasswordform;
				}elseif($rp_postnewpassword != $rp_postretypepassword){
					$type = 'pnw-error';
					$message = 'Password did not match';
					$rpshowform = $rp_newpasswordform;
				}elseif(strlen($rp_postnewpassword) < 8){
					$type = 'pnw-error';
					$message = 'Password must contain uppercase, lowercase, digits and must be greater than 8 characters';
					$rpshowform = $rp_newpasswordform;
				}elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/', $rp_postnewpassword)){
					$type = 'pnw-error';
					$message = 'Password must contain uppercase, lowercase, digits, and must be greater than 8 characters';
					$rpshowform = $rp_newpasswordform;
				}else{
					
					if($rp_posthiddentrustkey === $rp_foundTrustkey){
						wp_set_password($rp_postnewpassword, $rp_userid);
						$_SESSION['pnw-type'] = 'pnw-success';
						$_SESSION['pnw-message'] = 'passsword was successfully changed';
						$rpshowform = $rp_newpasswordform;
						
						$query = 'resetpassword';
						$backtologin = pnw_remove_query($query);
						exit(wp_redirect($backtologin));
						
					}else{
						$type = 'pnw-error';
						$message = 'Internal error 115';
						$rpshowform = $rp_usernameform;
					}
				} 
				
			}
			/* End of if enter new password form is set */
			
		}
	}
	
	
	$lasttype = !empty($type) ? $type : $sessiontype;
	$lastmessage = !empty($message) ? $message : $sessionmessage;
	
	$resetpasswordform = '<div class="'.esc_html($lasttype).'">'.esc_html($lastmessage).'</div>
	<div class="pnwloginDiv">
	<div class="pnwloginlogo"><div class="pnwLoginLogoHolder"><img class="pnwloginimg" src="'.esc_html($logourl).'"></div></div>
	<div class="pnwloginformDiv">
	<p class="loginintrotext">Enter Reset Password Details</p> <hr>
	<form action="" method="post" name="pnwresetpasswordform">

	'.$rpshowform.'

	<input type="submit" name="pnwsubmitresetpassword" value="Reset Password" id="pnw_submit_login" class="pnwloginbutton">
	</form>
	<div class="'.esc_html($sendOtpClass).'">'.esc_html($sendOtpMessage).'</div>
	</div>
	<div class="loginCancelDiv"><a class="logincancel" href="'.esc_html($cancel).'">Cancel</a></div>
	</div>';
	
	
	