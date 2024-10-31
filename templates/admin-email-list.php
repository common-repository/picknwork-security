<?php

$message = '';
$type = '';

if(isset($_POST['submit_delete_emails'])){
	
	$emails = (!empty($_POST['selectedemails'])) ? array_map('sanitize_text_field', $_POST['selectedemails']) : '';
	
	$more_data = array(
	'delete_selected_emails' => $emails
	);
	
	$run = pnw_push('email', 'delete_emails', $more_data);
	$type = !empty($run['type']) ? $run['type'] : '';
	$message = !empty($run['message']) ? $run['message'] : '';
	
}


// Get all the emails lists
 $allemails = pnw_get_all_emails();
 $extract = (is_array($allemails) && !empty($allemails)) ? $allemails : array();
 
 $listgroup = array();
 foreach($extract as $list){
	$listgroup[] = "<option>".(!empty($list['groups']) ? $list['groups'] : '')."</option>";
 }
	 
 
 $byshowall = '';
 $bygroups = '';
 $byemails = '';

 
 
 if(isset($_POST['submit_filter_by_email'])){
	 
	$bykey = !empty($_POST['filter_by_email']) ? sanitize_text_field($_POST['filter_by_email']) : '';
	
	$allemails = filter_by_key_and_value($allemails, 'email', $bykey);
 }
 
 
 if(isset($_POST['submit_filter_by_group'])){
	 
	$setgroup = !empty($_POST['filter_by_group']) ? sanitize_text_field($_POST['filter_by_group']) : '';
	
	if($setgroup === 'showall'){
		$byshowall = 'selected';
		$allemails = $allemails;
	}
	else{
		$allemails = filter_by_key_and_value($allemails, 'groups', $setgroup);
	}
	
 }
 
 
 if(isset($_POST['submit_clear_filter_emails'])){
	$allemails = $allemails;
 }
?>
<div class="wrap">
	<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<div class="pnwadmin_nav"><a href="?page=picknworksecurity-email-list"><button class="pnwadminNavButton">Email List</button></a></div>
<hr>
		
<div class="<?php echo esc_html_e($type);?>"><?php echo esc_html_e($message);?></div>

<br>			
<!-- EMAILS LIST PAGES STARTS HERE AND GOES -->
	<div class="pnwPaginationSearchDiv"><form action="" method="post">
	<select name="filter_by_group" class="pnw_searchtype filterline">
	<option value="" selected disabled>Select Filter Group</option>
	<option value="showall" <?php esc_html_e($byshowall);?>>Show All</option>
	<?php echo implode('', array_unique($listgroup));?>
	</select>
	<input type="submit" name="submit_filter_by_group" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="text" name="filter_by_email" value="<?php esc_html_e($byemails); ?>" placeholder="Search By Email" class="pnw_searchvalue filterline">
	<input type="submit" name="submit_filter_by_email" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="submit" name="submit_clear_filter_emails" value="Clear Filter" class="pnw_submit_search filterline">
	</form><div style="clear: both;"></div></div>

    <div class="dashboard_list_table_tips"> MANAGE EMAIL LISTS</div>
			
<?php
		
	if(!empty($allemails) && is_array($allemails)){

		echo '<div class="tableDiv">
		<table class="pnw-list-table">
		<th class="pc-role-head" style="width: 5%;">Select</th>
		<th class="pc-role-option-head" style="width: 30%;">Email</th>
		<th class="pc-role-head" style="width: 15%;">Group</th>
		<th class="pc-role-option-head" style="width: 30%;">Remark</th>
		<th class="pc-role-option-head" style="width: 15%;">Date</th>
		<form action="" method="post">';
		
		foreach($allemails as $row){
			
			$id = !empty($row['id']) ? $row['id'] : '';
			$email = !empty($row['email']) ? $row['email'] : '';
			$group = !empty($row['groups']) ? $row['groups'] : '';
			$remark = !empty($row['remark']) ? $row['remark'] : '';
			$date = !empty($row['ontime']) ? $row['ontime'] : '';
			
			echo '<tr class="pc-list-row pnwpaginate">
				<td class="pc-role-col"><input type="checkbox" name="selectedemails[]" value="'.esc_html($id).'" class="checklist-pc"></td>
				<td class="pc-role-col">'. esc_html($email) . '</td>
				<td class="pc-role-col">' . esc_html($group) . '</td>
				<td class="pc-role-col">'. esc_html($remark) .'</td>
				<td class="pc-role-col">'. esc_html($date) . '</td>
				</tr>';
								
		}
		echo '</table><input type="submit" name="submit_delete_emails" value="Delete Selected Emails" class="pnwbutton-big"></form></div><div class="paginationdiv"><span id="showingpageof"></span><ul id="pagin"></ul></div>';
	}else{
		echo '<div class="pnw_nothingfound">No Records Found!</div>';
	}
?>
		
		
	</div>

