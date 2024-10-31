<?php

$type = '';
$message = '';
	
$allphones = pnw_get_all_phones(); 
$extract = is_array($allphones) ? $allphones : array(); 

$showgroups = array();
foreach($extract as $row){
	$showgroups[] = '<option>'.(!empty($row['groups']) ? $row['groups'] : '').'</option>';
}


$group = !empty($_POST['phonegroup']) ? sanitize_text_field($_POST['phonegroup']) : '';
$subject = !empty($_POST['phonesubject']) ? sanitize_text_field($_POST['phonesubject']) : '';
$messagecontent = !empty($_POST['phonemessagecontent']) ? sanitize_text_field($_POST['phonemessagecontent']) : '';

	if(isset($_POST['submit_sms_users'])){
	 
		// Validations
		if(empty($group)){
			
			$type = 'pnw-error';
			$message = 'Please select a Group';
		}
		
		elseif(empty($subject)){
			
			$type = 'pnw-error';
			$message = 'Subject can not be empty';
		}
		
		elseif(empty($messagecontent)){
			
			$type = 'pnw-error';
			$message = 'Message content can not be empty';
				
		}else{
			
			$more_data = array(
			'sms_users' => 'sms_users',
			'group' => $group,
			'subject' => $subject,
			'messagecontent' => $messagecontent
			);
		
			$run = pnw_push('phone', 'sms_users', $more_data);
			$type = !empty($run['type']) ? sanitize_text_field($run['type']) : '';
			$message = !empty($run['message']) ? sanitize_text_field($run['message']) : '';
		
		}
		
	}			

?>
<div class="wrap">
<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<div class="pnwadmin_nav"><a href="?page=picknworksecurity-message-users-email"><button class="pnwadminNavButton">Email Users </button></a><a href="?page=picknworksecurity-message-users-sms"><button class="pnwadminNavButton">SMS Users</button></a></div>
<hr>
<div class="<?php esc_html_e($type);?>"><?php esc_html_e($message);?></div>	
<br>
<br>
			
<div class="message_tips"> Send Bulk SMS Messages To Your Site Users</div>
<div class="users_message_div">
<form action="" method="post">
<br>
<br>
		<div class="pnw-send-left">SELECT GROUPS</div><div class="pnw-send-right"><select name="phonegroup" class="pnw-send-field">
		<option selected disabled>Select Group</option>
		<option value="allgroup">All Group</option>
		<?php echo wp_kses(implode('', array_unique(array_map('sanitize_text_field', $showgroups))));?>
		</select></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left">SUBJECT</div><div class="pnw-send-right"><input type="text" name="phonesubject" value="<?php esc_html_e($subject);?>" class="pnw-send-field"></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left">MESSAGE</div><div class="pnw-send-right"><textarea name="phonemessagecontent" cols="90%" rows="10%" placeholder="Type your message" class="pnw-send-fieldtext"><?php esc_html_e($messagecontent);?></textarea></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left"></div><div class="pnw-send-right"><input type="submit" name="submit_sms_users" value="Send SMS" class="pnwbutton"></div>
		</form>
		</div>