<?php

$message = '';
$type = '';

if(isset($_POST['submit_delete_phone'])){
	
	$phone = (!empty($_POST['selectedphone'])) ? array_map('sanitize_text_field', $_POST['selectedphone']) : array();
	
	$more_data = array(
	'delete_selected_phone' => $phone
	);
	
	$run = pnw_push('phone', 'delete_phone', $more_data);
	$type = !empty($run['type']) ? $run['type'] : '';
	$message = !empty($run['message']) ? $run['message'] : '';
	
}


// Get all the phone lists
 $allphone = pnw_get_all_phones();
 $extract = (is_array($allphone) && !empty($allphone)) ? $allphone : array();
 
 $listgroup = array();
 foreach($extract as $list){
	$listgroup[] = "<option>".(!empty($list['groups']) ? $list['groups'] : '')."</option>";
 }
	 
 
 $byshowall = '';
 $bygroups = '';
 $byphone = '';
 
 
 if(isset($_POST['submit_filter_by_phone'])){
	 
	$bykey = !empty($_POST['filter_by_phone']) ? sanitize_text_field($_POST['filter_by_phone']) : '';
	
	$allphone = filter_by_key_and_value($allphone, 'phone', $bykey);
 }
 
 
 if(isset($_POST['submit_filter_by_group'])){
	 
	$setgroup = !empty($_POST['filter_by_group']) ? sanitize_text_field($_POST['filter_by_group']) : '';
	
	if($setgroup === 'showall'){
		$byshowall = 'selected';
		$allphone = $allphone;
	}
	else{
		$allphone = filter_by_key_and_value($allphone, 'groups', $setgroup);
	}
	
 }
 
 
 if(isset($_POST['submit_clear_filter_phone'])){
	$allphone = $allphone;
 }
 
?>
<div class="wrap">
	<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<div class="pnwadmin_nav"><a href="?page=picknworksecurity-phone-list"><button class="pnwadminNavButton">Phone List</button></a></div>
<hr>
		
<div class="<?php echo esc_html_e($type);?>"><?php echo esc_html_e($message);?></div>

<br>			
<!-- phone LIST PAGES STARTS HERE AND GOES -->
	<div class="pnwPaginationSearchDiv"><form action="" method="post">
	<select name="filter_by_group" class="pnw_searchtype filterline">
	<option value="" selected disabled>Select Filter Group</option>
	<option value="showall" <?php esc_html_e($byshowall);?>>Show All</option>
	<?php echo implode('', array_unique($listgroup));?>
	</select>
	<input type="submit" name="submit_filter_by_group" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="text" name="filter_by_phone" value="<?php esc_html_e($byphone); ?>" placeholder="Search By Phone" class="pnw_searchvalue filterline">
	<input type="submit" name="submit_filter_by_phone" value="Filter" class="pnw_submit_search filterline"> 
	</form>
	<form action="" method="post">
	<input type="submit" name="submit_clear_filter_phone" value="Clear Filter" class="pnw_submit_search filterline">
	</form><div style="clear: both;"></div></div>

    <div class="dashboard_list_table_tips"> MANAGE PHONE LISTS</div>
			
<?php
		
	if(!empty($allphone) && is_array($allphone)){

		echo '<div class="tableDiv">
		<table class="pnw-list-table">
		<th class="pc-role-head" style="width: 5%;">Select</th>
		<th class="pc-role-option-head" style="width: 30%;">phone</th>
		<th class="pc-role-head" style="width: 15%;">Group</th>
		<th class="pc-role-option-head" style="width: 30%;">Remark</th>
		<th class="pc-role-option-head" style="width: 15%;">Date</th>
		<form action="" method="post">';
		
		foreach($allphone as $row){
			
			$id = !empty($row['id']) ? $row['id'] : '';
			$phone = !empty($row['phone']) ? $row['phone'] : '';
			$group = !empty($row['groups']) ? $row['groups'] : '';
			$remark = !empty($row['remark']) ? $row['remark'] : '';
			$date = !empty($row['ontime']) ? $row['ontime'] : '';
			
			echo '<tr class="pc-list-row pnwpaginate">
				<td class="pc-role-col"><input type="checkbox" name="selectedphone[]" value="'.$id.'" class="checklist-pc"></td>
				<td class="pc-role-col">'. esc_html($phone) . '</td>
				<td class="pc-role-col">' . esc_html($group) . '</td>
				<td class="pc-role-col">'. esc_html($remark) .'</td>
				<td class="pc-role-col">'. esc_html($date) . '</td>
				</tr>';
								
		}
		echo '</table><input type="submit" name="submit_delete_phone" value="Delete Selected Phone" class="pnwbutton-big"></form></div><div class="paginationdiv"><span id="showingpageof"></span><ul id="pagin"></ul></div>';
	}else{
		echo '<div class="pnw_nothingfound">No Records Found!</div>';
	}
?>
		
	</div>