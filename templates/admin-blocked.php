<?php
//############################
// PROCESS DELETE BLOCKED FROM BLOCK LIST
//############################	

$message = '';

	if(isset($_POST['submit_delete_blocked'])){
		
		$ids = (!empty($_POST['selectedblocked'])) ? array_map('sanitize_text_field', $_POST['selectedblocked']) : array();
	
		$more_data = array(
		'delete_selected_blocked' => $ids
		);
		
		$run = pnw_push('blocked', 'delete_blocked', $more_data);
		$type = !empty($run['type']) ? $run['type'] : '';
		$message = !empty($run['message']) ? $run['message'] : '';
		
	}

// Get all the blocked lists
 $allblocks = pnw_get_all_blocked();
 $value = '';
 
 $modeShowall = '';
 $modeUsername = '';
 $modeIp = '';
 $modeEmail = '';
 $modePhone = '';
 $modeAppid = '';
 
 if(isset($_POST['submit_filter_blocked_by_mode'])){
	 
	$setmode = !empty($_POST['filter_blocked_by_mode']) ? sanitize_text_field($_POST['filter_blocked_by_mode']) : '';
	
	
	if($setmode === 'showall'){
		$modeShowall = 'selected';
		$allblocks = $allblocks;
	}
	elseif($setmode === 'username'){
		$modeUsername = 'selected';
		$bykey = 'username';
		$allblocks = filter_by_key_and_value($allblocks, 'mode', $bykey);
		
	}elseif($setmode === 'ip'){
		$modeIp = 'selected';
		$bykey = 'ip';
		$allblocks = filter_by_key_and_value($allblocks, 'mode', $bykey);
		
	}elseif($setmode === 'email'){
		$modeEmail = 'selected';
		$bykey = 'email';
		$allblocks = filter_by_key_and_value($allblocks, 'mode', $bykey);
		
	}elseif($setmode === 'phone'){
		$modePhone = 'selected';
		$bykey = 'phone';
		$allblocks = filter_by_key_and_value($allblocks, 'mode', $bykey);
		
	}elseif($setmode === 'appid'){
		$modeAppid = 'selected';
		$bykey = 'appid';
		$allblocks = filter_by_key_and_value($allblocks, 'mode', $bykey);
	}else{
		$nothing = 'nothing';
	}
	
 }
 
 
 
 if(isset($_POST['submit_filter_blocked_by_value'])){
	$value = !empty($_POST['filter_blocked_by_value']) ? sanitize_text_field($_POST['filter_blocked_by_value']) : '';
	$allblocks = filter_by_value($allblocks, $value);
	
 }
 
 
 if(isset($_POST['submit_clear_filter_blocked'])){
	$allblocks = $allblocks;
 }
 
?>
<div class="wrap">
	<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<div class="pnwadmin_nav"><a href="?page=picknworksecurity-manage-blocks"><button class="pnwadminNavButton">Blocked List</button></a><a href="?page=picknworksecurity-add-blocks"><button class="pnwadminNavButton">Add To Block List</button></a></div>
<hr>
		
<div class="<?php echo esc_html_e($type);?>"><?php echo esc_html_e($message);?></div>

<br>			
<!-- BLOCKED PAGES STARTS HERE AND GOES -->
	<div class="pnwPaginationSearchDiv"><form action="" method="post">
	<select name="filter_blocked_by_mode" class="pnw_searchtype filterline">
	
	<option value="" selected disabled>Select Filter Mode</option>
	<option value="showall" <?php esc_html_e($modeShowall);?>>Show All</option>
	<option value="username" <?php esc_html_e($modeUsername);?>>Filter By Username</option>
	<option value="ip" <?php esc_html_e($modeIp);?>>Filter By Ip</option>
	<option value="email" <?php esc_html_e($modeEmail);?>>Filter By Email</option>
	<option value="phone" <?php esc_html_e($modePhone);?>>Filter By Phone</option>
	<option value="appid" <?php esc_html_e($modeAppid);?>>Filter By PNW APP ID</option>
	</select>
	<input type="submit" name="submit_filter_blocked_by_mode" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="text" name="filter_blocked_by_value" value="<?php esc_html_e($value); ?>" placeholder="Enter Search Keyword" class="pnw_searchvalue filterline">
	<input type="submit" name="submit_filter_blocked_by_value" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="submit" name="submit_clear_filter_blocked" value="Clear Filter" class="pnw_submit_search filterline">
	</form><div style="clear: both;"></div></div>

    <div class="dashboard_list_table_tips"> MANAGE BLOCKS LISTS</div>
			
<?php
		
	if(!empty($allblocks) && is_array($allblocks)){

		echo '<div class="tableDiv">
		<table class="pnw-list-table">
		<th class="pc-role-head" style="width: 5%;">Select</th>
		<th class="pc-role-option-head" style="width: 30%;">Blocked</th>
		<th class="pc-role-head" style="width: 15%;">Mode</th>
		<th class="pc-role-option-head" style="width: 30%;">Comment</th>
		<th class="pc-role-option-head" style="width: 15%;">Date</th>
		<form action="" method="post">';
		
		foreach($allblocks as $row){
			
			$id = !empty($row['id']) ? $row['id'] : '';
			$blocked = !empty($row['blocked']) ? $row['blocked'] : '';
			$mode = !empty($row['mode']) ? $row['mode'] : '';
			$comment = !empty($row['comment']) ? $row['comment'] : '';
			$date = !empty($row['ontime']) ? $row['ontime'] : '';
			
			echo '<tr class="pc-list-row pnwpaginate">
				<td class="pc-role-col"><input type="checkbox" name="selectedblocked[]" value="'.$id.'" class="checklist-pc"></td>
				<td class="pc-role-col">'. $blocked . '</td>
				<td class="pc-role-col">' . $mode . '</td>
				<td class="pc-role-col">'. $comment .'</td>
				<td class="pc-role-col">'. $date . '</td>
				</tr>';
								
		}
		echo '</table><input type="submit" name="submit_delete_blocked" value="Unblock Selected" class="pnwbutton-big"></form></div><div class="paginationdiv"><span id="showingpageof"></span><ul id="pagin"></ul></div>';
	}else{
		echo '<div class="pnw_nothingfound">No Records Found!</div>';
	}
?>
		
		
		
		