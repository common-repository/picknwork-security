<?php
//############################
// PROCESS ADMIN BLOCK SOMEONE SETTINGS
//############################	
$type = '';
$message = '';


	if(isset($_POST['submit_admin_block'])){
		
		$method = (!empty($_POST['admin_block_method'])) ? sanitize_text_field($_POST['admin_block_method']) : '';
		$blockvalue = (!empty($_POST['admin_block_value'])) ? sanitize_text_field($_POST['admin_block_value']) : '';
		$reason = (!empty($_POST['admin_block_reason'])) ? sanitize_text_field($_POST['admin_block_reason']) : '';
		$date = pnwsecuritydate();
		
		$more_data = array(
		'add_blocked' => 'admin_add_block',
		'method' => $method,
		'blockvalue' => $blockvalue,
		'reason' => 'Admin:: ' .$reason,
		'date' => $date
		);
		
		$run = pnw_push('blocked', 'add_blocked', $more_data);
		$type = !empty($run['type']) ? $run['type'] : '';
		$message = !empty($run['message']) ? $run['message'] : '';

	}		


?>
<div class="pnwadmin_nav"><a href="?page=picknworksecurity-manage-blocks"><button class="pnwadminNavButton">Blocked List</button></a><a href="?page=picknworksecurity-add-blocks"><button class="pnwadminNavButton">Add To Block List</button></a></div>

<div class="<?php echo esc_html_e($type);?>"><?php echo esc_html_e($message);?></div>


<!-- CREATE A BLOCK USER INTERFACE FOR ADMIN TO USE AND BLOCK USERNAME, IP, EMAIL, PHONE AND APPID	-->
		<div style="display: block; margin-left: auto; margin-right: auto; width: 60%; margin-top: 4%; background-color: white; padding: 4%; border-bottom: 20px solid blue; border-radius: 10px;">
			<form action="" method="post">
			<h3 style="text-align: center;">Admin Blocking Interface </h3>
			You can block any email address, phone, Ipaddress, Username, Userid from accessing your website
			<br>
			<br>
			
			<div class="pc-admin-left">CHOOSE BLOCK METHOD</div><div class="pc-admin-right"><select name="admin_block_method" class="pc-admin-select-field">
			<option value="ip">Ip</option>
			<option value="username">Username</option>
			<option value="userid">Userid</option>
			<option value="email">Email</option>
			<option value="phone">Phone</option>
			<option value="appid">Picknworksecurity APP ID</option>
			</select></div>
			<div style="clear: both;"></div>

			<div class="pc-admin-left">ENTER LOCK VALUE </div><div class="pc-admin-right"><input type="text" name="admin_block_value" value="" class="pc-admin-select-field"></div>
			<div style="clear: both;"></div>
			
			<div class="pc-admin-left">REASON FOR BLOCKING</div><div class="pc-admin-right"><input type="text" name="admin_block_reason" value="" class="pc-admin-select-field"></div>
			<div style="clear: both;"></div>
			<div class="pc-admin-left"></div>
			<div class="pc-admin-right"><input type="submit" name="submit_admin_block" value="Add To Blocked List" class="pnwbutton"></div>
			<div style="clear: both;"></div>
			</form>
			</div>