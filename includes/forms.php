<?php
/* CREATE UPDATE PHONE FORM FUNCTION
############################################# */

add_shortcode('pnw_update_phone', 'picknworksecurity_update_phone');
function picknworksecurity_update_phone() {
	global $leapid, $wpdb;
	$query = 'none';
	$cancel = pnw_remove_query($query);
	
	$sessiontype = !empty($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
	$sessionmessage = !empty($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';
	
	$type = '';
	$message = '';
	$sendOtpClass = '';
	$sendOtpMessage = '';
	
	
	if(is_user_logged_in()){
		$user = wp_get_current_user();
		$userid = $user->ID;
		$username = $user->user_login;
		$phone = $user->pnwphonenumber;
		$email = $user->user_email;
		$password = $user->user_pass;
		$usertrustkey = $user->pnwtrustkey;
	}else{
		$userid = '';
		$username = '';
		$phone = '';
		$email = '';
		$password = '';
		$usertrustkey = '';
	}

	
	$postphonenumber = !empty($_POST['pnwPhoneNumber']) ? sanitize_text_field($_POST['pnwPhoneNumber']) : '';
	$postupdatephonepassword = !empty($_POST['pnwUpdatePhonePassword']) ? sanitize_text_field($_POST['pnwUpdatePhonePassword']) : '';
	$postotp = !empty($_POST['pnwUpdatePhoneConfirmOtp']) ? sanitize_text_field($_POST['pnwUpdatePhoneConfirmOtp']) : '';
	
	
	$phoneform = '<div style="text-align: center;">Your current phone number: '.esc_html($phone).'</div><hr><br>
	<div class="pnwupdatephonetitleDiv">New Phone Number</div>
	<div class="pnwupdatephonefieldDiv"><input type="text" name="pnwPhoneNumber" value="'.esc_html($postphonenumber).'" placeholder="e.g +447099889976" id="pnwphonenumber" class="pnwupdatephonefield"></div>
	<div class="pnwupdatephonetitleDiv">Confirm Password</div>
	<div class="pnwupdatephonefieldDiv"><input type="password" name="pnwUpdatePhonePassword" value="'.esc_html($postupdatephonepassword).'" placeholder="Validate Your Password" id="pnwphonenumber" class="pnwupdatephonefield"></div>';

	$otpform = '<div class="pnwupdatephonetitleDiv">Enter OTP</div>
	<div class="pnwupdatephonefieldDiv"><input type="text" name="pnwUpdatePhoneConfirmOtp" value="'.esc_html($postotp).'" placeholder="Enter OTP" id="pnwconfirmotp" class="pnwupdatephonefield"></div>';
	
	$showform = $phoneform;
	
	
	// START PROCESSING FORM HERE
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['pnwsubmitupdatephone'])){
			
			// search if user already has same number
			$table = $wpdb->prefix . 'usermeta';
			$search = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM $table WHERE meta_key = 'pnwphonenumber' AND meta_value = '%s'", $postphonenumber));
			$foundphone = !empty($search[0]->meta_value) ? $search[0]->meta_value : '';
			
			if(isset($_POST['pnwPhoneNumber']) && empty($_POST['pnwPhoneNumber'])){
				$type = 'pnw-error';
				$message = 'Please enter a phone number';
			}
			elseif(isset($_POST['pnwPhoneNumber']) && !preg_match('/^[0-9+]*$/', $postphonenumber)){
				$type = 'pnw-error';
				$message = 'Invalid Phone number format';
			}
			elseif(isset($_POST['pnwUpdatePhonePassword']) && empty($_POST['pnwUpdatePhonePassword'])){
				$type = 'pnw-error';
				$message = 'Please enter your password';
			}
			elseif(isset($_POST['pnwUpdatePhonePassword']) && !wp_check_password($postupdatephonepassword, $password)){
				$type = 'pnw-error';
				$message = 'Incorrect Password!';
			}
			elseif(isset($_POST['pnwPhoneNumber']) && !empty($foundphone)){
				$type = 'pnw-error';
				$message = 'Phone number already in use';
			}elseif(!isset($_POST['pnwUpdatePhoneConfirmOtp'])){
				
				// update user trustkey
				$trustkey = pnw_generate_trustkey();
				update_user_meta($userid, 'pnwtrustkey', $trustkey.$postphonenumber);
				
				// Send otp to the user email and update opt base
					$more_data = array(
					'send_otp' => 'send_otp',
					'trustkey' => $trustkey.$postphonenumber,
					'mode' => 'phone',
					'phone' => $postphonenumber,
					'comment' => 'updatephone'
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
					$showform = $phoneform; 
					}
				}else{
					
					$nothing = 'nothing';
				}
				
				
				
				// if confirmotp is set
				if(isset($_POST['pnwUpdatePhoneConfirmOtp'])){
					
					// confirm otp
					$more_data = array(
					'verify_otp' => 'verify_otp',
					'trustkey' => $usertrustkey,
					'postotp' => $postotp,
					'username' => $username
					);

					$confirmedotp = pnw_push('otp', 'verify_otp', $more_data);
					$replyType = !empty($confirmedotp['type']) ? sanitize_text_field($confirmedotp['type']) : '';
					$replyMessage = !empty($confirmedotp['message']) ? sanitize_text_field($confirmedotp['message']) : '';
					$replyComment = !empty($confirmedotp['comment']) ? sanitize_text_field($confirmedotp['comment']) : '';
					$replyTrustkey = !empty($confirmedotp['trustkey']) ? sanitize_text_field($confirmedotp['trustkey']) : '';
					$showform = $otpform;


					if($replyComment === 'blocked'){
					$showform = $phoneform;
					}

					if($replyMessage === 'correct' && $replyComment === 'updatephone'){

						if($replyTrustkey === $usertrustkey){
							// Update his phone
							$newphone = substr($usertrustkey, 31);
							update_user_meta($userid, 'pnwphonenumber', $newphone);
							update_user_meta($userid, 'pnwphonestatus', 'verified');
							$_SESSION['pnw-type'] = 'pnw-success';
							$_SESSION['pnw-message'] = 'Your phone number have been succesfully updated';
							exit(wp_redirect($cancel)); 
						}
					}
					
					$type = !empty($replyType) ? $replyType : 'pnw-error';
					$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
				
				} /* end of If confirm otp is set */
				
		
		} /* End of if pnwsubmitupdatephone isset */
	}
	/* End of if request is post */
	
	$lasttype = !empty($type) ? $type : $sessiontype;
	$lastmessage = !empty($message) ? $message : $sessionmessage;
	
	$form = '<div class="'.esc_html($lasttype).'">'.esc_html($lastmessage).'</div>
	<div class="pnwupdatephoneDiv">
	<div class="pnwupdatephoneformDiv">
	<p class="updatephoneintrotext">Update Phone Number</p> <hr>
	<form action="" method="post" name="pnwupdatephoneform">

	'.$showform.'

	<input type="submit" name="pnwsubmitupdatephone" value="Update Phone" id="pnw_submit_updatephone" class="pnwupdatephonebutton">
	</form>
	<div class="'.$sendOtpClass.'">'.esc_html($sendOtpMessage).'</div>
	</div>
	<div class="updatephoneCancelDiv"><a class="updatephonecancel" href="'.esc_url($cancel).'">Cancel</a></div>
	</div>';
	
	$_SESSION['pnw-type'] = '';
    $_SESSION['pnw-message'] = '';
	
	return $form;

}

/* End of Update Phone form 
*****************************/




/* CREATE NEWSLETTER FORM FUNCTION FOR EMAIL 
############################################# */
add_shortcode('pnw_newsletter_subscription', 'picknworksecurity_newsletter_subscription');
function picknworksecurity_newsletter_subscription($attr = []) {
	global $leapid, $wpdb;
	$query = 'none';
	$reload = pnw_remove_query($query);
	
	$sessiontype = !empty($_SESSION['pnw-type']) ? sanitize_text_field($_SESSION['pnw-type']) : '';
	$sessionmessage = !empty($_SESSION['pnw-message']) ? sanitize_text_field($_SESSION['pnw-message']) : '';
	
	$type = '';
	$message = '';
	$sendOtpClass = '';
	$sendOtpMessage = '';
	$fielderror = '';
	
	$postsubscription = !empty($_POST['pnwsubscription']) ? sanitize_text_field($_POST['pnwsubscription']) : '';
	$postsubscriptiontype = !empty($_POST['pnwsubscriptiontype']) ? sanitize_text_field($_POST['pnwsubscriptiontype']) : '';
	$postsubscriptiongroup = !empty($_POST['pnwsubscriptiongroup']) ? sanitize_text_field($_POST['pnwsubscriptiongroup']) : '';
	$postotp = !empty($_POST['pnwsubscriptionotp']) ? sanitize_text_field($_POST['pnwsubscriptionotp']) : '';
	$submitsubscription = !empty($_POST['pnwsubmitsubscription']) ? sanitize_text_field($_POST['pnwsubmitsubscription']) : '';


	$selectedemail = ($postsubscriptiontype === 'email') ? 'selected' : '';
	$selectedphone = ($postsubscriptiontype === 'phone') ? 'selected' : '';
	
	
	$attr_type = !empty($attr['type']) ? esc_html($attr['type']) : '';
	
	$attr_remark = !empty($attr['remark']) ? esc_html($attr['remark']) : '';
	
	$attr_groups = !empty($attr['groups']) ? esc_html($attr['groups']) : '';
	$grouplist = explode(',', $attr_groups);
	
	
	$groupoption = array();
	if(is_array($grouplist) && !empty($grouplist)){
		foreach($grouplist as $list){
			$newlist = ucwords(strtolower($list));
			$selectedgroup = ($postsubscriptiongroup === $newlist) ? 'selected' : '';
			$groupoption[] = '<option value="'.$newlist.'" '.$selectedgroup.'>'.$newlist.'</option>';
			
		}
	}
	
	// conditional subscription type
	if($attr_type === 'email'){
		$typeoptions = '
		<option value="email" '.esc_html($selectedemail).'>Email</option>';
	}elseif($attr_type === 'phone'){
		$typeoptions = '
		<option value="phone" '.esc_html($selectedphone).'>Phone</option>';
	}else{
		$typeoptions = '
		<option value="email" '.esc_html($selectedemail).'>Email</option>
		<option value="phone" '.esc_html($selectedphone).'>Phone</option>';
	}

	$subscribeform = '<br><div class="pnwsubscriptionDiv"><input type="text" name="pnwsubscription" value="'.esc_html($postsubscription).'" placeholder="enter your email or phone" class="pnwsubscription"></div>
	<div class="pnwsubscriptiontypeDiv"><select name="pnwsubscriptiontype" class="pnwsubscriptiontype">
	<option selected disabled>Select</option>
	'.$typeoptions.'
	</select></div>
	
	<div class="pnwsubscriptiongroupDiv">
	<select name="pnwsubscriptiongroup" class="pnwsubscriptiongroup">
	<option selected disabled>Select Group</option>
	'.implode('', $groupoption).'
	</select>
	</div><div style="clear: both;"></div>';
	
	$otpform = '<input type="hidden" name="pnwsubscription" value="'.esc_html($postsubscription).'">
	<br><div class="pnwsubscribeotpDiv"><input type="text" name="pnwsubscriptionotp" value="'.esc_html($postotp).'" placeholder="Enter OTP" class="pnwsubscriptionotp"></div>';
	
	$submitvalue = 'Subscribe';
	$showform = $subscribeform;
	
	
	// START PROCESSING FORM HERE
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if(isset($_POST['pnwsubmitsubscription']) && $submitsubscription === 'Subscribe'){
				
			if(empty($postsubscription)){
				$type = 'pnw-error';
				$message = 'Please enter a phone number or an email address';
				$fielderror = 'subscribefield';
			}
			elseif(empty($postsubscriptiontype)){
				$type = 'pnw-error';
				$message = 'Please select a subscription type';
				$fielderror = 'subscribetypefield';
			}
			elseif(empty($postsubscriptiongroup)){
				$type = 'pnw-error';
				$message = 'Please select a group';
				$fielderror = 'subscribegroupfield';
			}
			elseif($postsubscriptiontype === 'email' && !filter_var($postsubscription, FILTER_VALIDATE_EMAIL)){
				$type = 'pnw-error';
				$message = 'Please enter a valid email address';
				$fielderror = 'subscribefield';
			}
			elseif($postsubscriptiontype === 'phone' && !preg_match('/^[0-9+]*$/', $postsubscription)){
				$type = 'pnw-error';
				$message = 'Please enter a valid phone number';
				$fielderror = 'subscribefield';
			}else{
				// generate trustkey
				$trustkey = pnw_generate_trustkey();
				$_SESSION['pnwtrustkey'] = $trustkey.'*|*'.$postsubscription.'*|*'.$postsubscriptiongroup.'*|*'.$attr_remark;
				$fulltrustkey = $trustkey.'*|*'.$postsubscription.'*|*'.$postsubscriptiongroup.'*|*'.$attr_remark;
				
				if($postsubscriptiontype === 'email'){
					$modetype = 'email';
				}elseif($postsubscriptiontype === 'phone'){
					$modetype = 'phone';
				}else{
					$modetype = '';
				}
				
				// Send otp to the user
					$more_data = array(
					'send_subscription' => 'send_subscription',
					'trustkey' => $fulltrustkey,
					'mode' => $modetype,
					$modetype => $postsubscription,
					'comment' => 'newslettersubscription',
					'group' => $postsubscriptiongroup
					);
				

					$run = pnw_push('subscription', 'send_subscription', $more_data);
					$sendtype = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
					$sendmessage = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
					$comment = !empty($run['comment']) ? sanitize_text_field($run['comment']) : '';
					
					$submitvalue = 'Confirm Subscription';
					$showform = $otpform;
					
					if($sendtype === 'pnw-success'){
					$sendOtpClass = 'sendOtpResponseDiv';
					$sendOtpMessage = $sendmessage;
					$submitvalue = 'Confirm Subscription';
					$showform = $otpform;
					}
					if($sendtype === 'pnw-error'){
					$type = $sendtype;
					$message = $sendmessage;
					$showform = $subscribeform;
					$submitvalue = 'Subscribe';
					}
					
					// reverse back form if the user is blocked
					if($comment === 'blocked'){
					$showform = $subscribeform;
					$submitvalue = 'Subscribe';
					}	
			   }
		   } /* End of if pnwsubmitsubscription isset */
				
				
			// if confirm subscription is set
			if(isset($_POST['pnwsubmitsubscription']) && $submitsubscription === 'Confirm Subscription'){
				
				$usertrustkey = !empty($_SESSION['pnwtrustkey']) ? sanitize_text_field($_SESSION['pnwtrustkey']) : '';
				
				// confirm subscription
				$more_data = array(
				'confirm_subscription' => 'confirm_subscription',
				'trustkey' => $usertrustkey,
				'postotp' => $postotp
				);

				$confirmotp = pnw_push('subscription', 'confirm_subscription', $more_data);
				$replyType = !empty($confirmotp['type']) ? sanitize_text_field($confirmotp['type']) : '';
				$replyMessage = !empty($confirmotp['message']) ? sanitize_text_field($confirmotp['message']) : '';
				$replyComment = !empty($confirmotp['comment']) ? sanitize_text_field($confirmotp['comment']) : '';
				$replyTrustkey = !empty($confirmotp['trustkey']) ? sanitize_text_field($confirmotp['trustkey']) : '';
				
				$submitvalue = 'Confirm Subscription';
				$showform = $otpform;

				if($replyComment === 'blocked'){
				$showform = $subscribeform;
				}
			
				if($replyMessage === 'correct' && $replyComment === 'newslettersubscription'){

						$_SESSION['pnw-type'] = 'pnw-success';
						$_SESSION['pnw-message'] = 'You have been succesfully added to our newsletter';
						exit(wp_redirect($reload)); 
				}
				
				$type = !empty($replyType) ? $replyType : 'pnw-error';
				$message = !empty($replyMessage) ? $replyMessage : 'Unexpected Error Ocurred';
			
			} /* If confirm subscription is set */	
	}
	/* End of if request is post */
	
	$lasttype = !empty($type) ? $type : $sessiontype;
	$lastmessage = !empty($message) ? $message : $sessionmessage;
	
	$form = '
	<div class="pnwsubscriptionBase">
	<div class="pnwsubscriptionformBase">
	<div class="'.esc_html($lasttype).'">'.esc_html($lastmessage).'<span style="font-size: 0px; display: none;" class="pnwfielderror">'.$fielderror.'</span></div>
	<form action="" method="post" name="pnwsubscribeemailform">

	'.$showform.'

	<div style="text-align: center;"><input type="submit" name="pnwsubmitsubscription" value="'.esc_html($submitvalue).'" class="pnwsubcribebutton"></div>
	</form>
	<div class="'.esc_html($sendOtpClass).'">'.esc_html($sendOtpMessage).'</div>
	</div>
	</div>';
	
	$_SESSION['pnw-type'] = '';
	$_SESSION['pnw-message'] = '';
	
	return $form;

	
}
/* End of Newsletter form 
*****************************/