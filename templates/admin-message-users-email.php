<?php

$type = '';
$message = '';
	
// Get list of wordpress roles
$allemails = pnw_get_all_emails(); 
$extract = is_array($allemails) ? $allemails : array(); 

$showgroups = array();
foreach($extract as $row){
	$showgroups[] = '<option>'.(!empty($row['groups']) ? $row['groups'] : '').'</option>';
}


$group = !empty($_POST['emailgroup']) ? sanitize_text_field($_POST['emailgroup']) : '';
$subject = !empty($_POST['emailsubject']) ? sanitize_text_field($_POST['emailsubject']) : '';
$messagecontent = !empty($_POST['emailmessagecontent']) ? sanitize_text_field($_POST['emailmessagecontent']) : '';

	if(isset($_POST['submit_email_users'])){

	 
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
			'email_users' => 'email_users',
			'group' => $group,
			'subject' => $subject,
			'messagecontent' => $messagecontent
			);
		
			$run = pnw_push('email', 'email_users', $more_data);
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
			
<div class="message_tips"> Send Bulk Email Messages To Your Site Users</div>
<div class="users_message_div">
<form action="" method="post">
<br>
<br>
		<div class="pnw-send-left">SELECT GROUPS</div><div class="pnw-send-right"><select name="emailgroup" class="pnw-send-field">
		<option selected disabled>Select Group</option>
		<option value="allgroup">All Group</option>
		<?php echo implode('', array_unique($showgroups));?>
		</select></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left">SUBJECT</div><div class="pnw-send-right"><input type="text" name="emailsubject" value="<?php esc_html_e($subject);?>" class="pnw-send-field"></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left">MESSAGE</div><div class="pnw-send-right"><textarea name="emailmessagecontent" cols="90%" rows="10%" placeholder="Type your message" class="pnw-send-fieldtext"><?php esc_html_e($messagecontent);?></textarea></div>
		<div style="clear: both;"></div>
		<br>
		<br>
		<div class="pnw-send-left"></div><div class="pnw-send-right"><input type="submit" name="submit_email_users" value="Send Email" class="pnwbutton"></div>
		</form>
		</div>