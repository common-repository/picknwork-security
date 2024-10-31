<?php
session_start();
ob_start();
require_once(ABSPATH .'wp-includes/pluggable.php');  // just because of pluggable.php to use get_user_by() and other wp core function


// UTILITY FUNCTIONS SECTIONS
/* *********************************** 
This are utility functions that is a standalone functions that will not have any other functions 
outside php functions called inside them But they can be called inside other functions for faster coding.
************************************ */

/* CREATE DATE FUNCTION WITH TIME ZONE
************************************/
function pnwsecuritydate(){
	$timezone = 'Europe/London';
	$timestamp = time();
	$date = new DateTime("now", new DateTimeZone($timezone));
	$date->setTimestamp($timestamp);
	
	return $date->format('Y-m-d H:i:s');
	
}


/* CREATE CLOSE ENVELOPE - WITH BUILT IN TIME STAMP
*****************************************************/
function pnwsecurityenvelope($data){
	if(file_exists(PNWSEC_PATH . '/includes/cranekey.php')){
		include(PNWSEC_PATH . '/includes/cranekey.php');
	}
	
	$key = !empty($pnwserialkey) ? $pnwserialkey : str_shuffle('ABCDEFGHIJKLMNOP');
	
	$enkey = base64_decode($key);
	$cipher = 'AES-128-CTR';
	$length = openssl_cipher_iv_length($cipher);
	$iv = openssl_random_pseudo_bytes($length);
	
	$newdata = $data .'**::**'. pnwsecuritydate();
	$chain = openssl_encrypt($newdata, $cipher, $enkey, 0, $iv);
	
	return base64_encode($chain .'**::**'. $iv);
	
}



/* CREATE OPEN ENVELOPE - WITH BUILT IN TIME STAMP
****************************************************/
function pnwsecurityopenenvelope($data){
	if(file_exists(PNWSEC_PATH . '/includes/cranekey.php')){
		include(PNWSEC_PATH . '/includes/cranekey.php');
	}
	
	$key = !empty($pnwserialkey) ? $pnwserialkey : str_shuffle('ABCDEFGHIJKLMNOP');
	
	$enkey = base64_decode($key);
	$cipher = 'AES-128-CTR';
	$length = openssl_cipher_iv_length($cipher);
	
	$firstdata = explode('**::**', base64_decode($data));
	$encrypted = !empty($firstdata[0]) ? $firstdata[0] : '';
	$iv = !empty($firstdata[1]) ? $firstdata[1] : '';
	
	$unchain = openssl_decrypt($encrypted, $cipher, $enkey, 0, $iv) ;
	
	$seconddata = explode('**::**', $unchain);
	$encryptedLast = !empty($seconddata[0]) ? $seconddata[0] : '';
	$date = !empty($seconddata[1]) ? $seconddata[1] : '';
	$expired = date('Y-m-d H:i:s', strtotime(pnwsecuritydate() .'-1 minutes'));
	
	// ensure right date format is used
	$format = 'Y-m-d H:i:s';
	$d = DateTime::createFromFormat($format, $date);
	$correctdate = $d && $d->format($format) === $date;
	
	
	if($date > $expired && !empty($correctdate) && !empty($encryptedLast)){
		$response = $encryptedLast;
	}else{
		$response = '';
	}
	return $response;
	
}




/* CREATE GET DOMAIN NAME ONLY FUNCTION
*****************************************/
	function pnw_mydomain() {
	
		$url = site_url();
		$domain1 = str_ireplace('http://wwww.', '', $url);
		$domain2 = str_ireplace('https://www.', '', $domain1);
		$domain3 = str_ireplace('https://', '', $domain2);
		$domain4 = str_ireplace('http://', '', $domain3);
		$domain5 = str_ireplace('www.', '', $domain4);
		
		return $domain5;
		
	} 
	



/* CREATE SPACES REVOMAL FUNCTION
***********************************/
	function pnw_trim($value) {
	
		$value = str_ireplace(' ', '', $value);
		
		return $value;
	}




/* CREATE A VERY POWERFUL ARRAY FLATTENING FUNCTION FOR ALL YOUR MULTI COLUMN AND ROWS INSERT AND DELETE FUNCTIONS
*******************************************************************************************************************/
	function pnw_flatten_array(array $items, array $flattened = []) {
		foreach($items as $item) {
		if(is_array($item)) {
		$flattened = prime_flatten_array($item, $flattened);
		continue;
		}
		$flattened[] = $item;
		}
		return $flattened;
		} 	
		
	

/* CREATE GET USER REAL IP ADDRESS
************************************/
function pnw_ip(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;	
}





/* CREATE GET URL WITH QUERY CLEANER
**************************************/
	function pnw_remove_query($query) {
	
		$site = admin_url();
		$find = substr($site, 0, 5);
		$protocol = ($find === 'https') ? 'https://' : 'http://';
		
		$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$value = isset($_GET[$query]) ? sanitize_text_field($_GET[$query]) : ''; 
		$off1 = '&&'.$query.'='.$value;
		$off2 = '&'.$query.'='.$value;
		$off3 = '?'.$query.'='.$value;
		$off4 = '&&'.$query;
		$off5 = '&'.$query;
		$off6 = '?'.$query;
		
		if(!empty(stripos($url, $off1))){
			$cleanurl = str_ireplace($off1, '', $url);	
		}elseif(!empty(stripos($url, $off2))){
			$cleanurl = str_ireplace($off2, '', $url);
		}elseif(!empty(stripos($url, $off3))){
			$cleanurl = str_ireplace($off3, '', $url);
		}elseif(!empty(stripos($url, $off4))){
			$cleanurl = str_ireplace($off4, '', $url);
		}elseif(!empty(stripos($url, $off5))){
			$cleanurl = str_ireplace($off5, '', $url);
		}elseif(!empty(stripos($url, $off6))){
			$cleanurl = str_ireplace($off6, '', $url);
		}else{
			$cleanurl = $url;
		}
	
		return $cleanurl;
		
}



/* CREATE FILTER ARRAY BY KEY
*******************************/
function filter_by_key($array, $find){
	
	$newarray = [];

	if(is_array($array)){
	 $getfirst = reset($array);
		if(is_array($getfirst)){	
			foreach($array as $key => $value){
				
				$temp = isset($array[$key][$find]) ? 'yes' : 'no';	
		
				if($temp === 'yes'){	
					$newarray[] = $array[$key];	
				}
			}
		}else{
			$temp = isset($array[$find]) ? 'yes' : 'no';
			if($temp === 'yes'){
				$newarray = $array;	
			}
		}
	}
	
	return $newarray;
}




/* CREATE FILTER ARRAY BY KEY AND VALUE
******************************************/
	function filter_by_key_and_value($array, $searchkey, $searchvalue){
		
		$newarray = [];
		if(is_array($array)){
			$getfirst = reset($array);
			
			if(is_array($getfirst)){
				foreach($array as $key => $value){
					
					foreach($value as $newkey => $newvalue){
						
						if($newkey === $searchkey && $newvalue === $searchvalue){
							$newarray[] = $array[$key];
						}
					}
				}
				
			}else{
				foreach($array as $key => $value){
					
					if($key === $searchkey && $value === $searchvalue){
						$newarray = $array;
					}
				}				
			}
		}
		return $newarray;
	}	





/* CREATE FILTER ARRAY BY VALUE
*********************************/
function filter_by_value($array, $searchvalue){
	
	$newarray = [];
	if(is_array($array)){
		$getfirst = reset($array);
		
		if(is_array($getfirst)){
			foreach($array as $key => $value){
				
				foreach($value as $newkey => $newvalue){
					
					if($newvalue === $searchvalue){
						$newarray[] = $array[$key];
					}
				}
			}
			
		}else{
			foreach($array as $key => $value){
				
				if($value === $searchvalue){
					$newarray = $array;
				}
			}				
		}
	}
	return $newarray;
}	




/* CREATE FILTER ARRAY BY VALUE FOR SINGLE ARRAY
***************************************************/
	function filter_by_value_single($array, $searchvalue){
		
		$newarray = [];
		if(is_array($array)){
			$getfirst = reset($array);
			
			if(is_array($getfirst)){
				foreach($array as $key => $value){
					
					foreach($value as $newkey => $newvalue){
						
						if($newvalue === $searchvalue){
							$temp[$key][$newkey] = $newvalue;
						}
					}
				}
				$newarray = (isset($temp) && is_array($temp)) ? array_values($temp) : '';
			}else{
				foreach($array as $key => $value){
					
					if($value === $searchvalue){
						$newarray[$key] = $value;
					}
				}				
			}
		}
		return $newarray;
	}
	
	
/* ******************Utility Functions Ends Here ******************* */	
/* ******************############################******************* */	



/* LIST ALL ROLES FUNCTION
****************************/
function pnw_get_all_roles(){
	global $wp_roles;
		
	$droles = $wp_roles->get_names();
	
	foreach($droles as $list){
	$convertcase[] = strtolower($list);   // Convert all returned list of roles into lower cases as to match with the get current user role
	}
	
	return $convertcase;
}



/* CREATE GENERATE PASSWORD
*****************************/
function pnw_generate_password(){
	$passOne = substr(str_shuffle('AabBPQRABCDCDcdefgmnEFGOSTUVWXhijklHIJKLMNOstuvwxyzPQRABCDEFGHIJKopqrstuvwxyzLMNYZ'), 0, 10);
	$passTwo = date('msi') . rand(11111, 99999);
	$passThree = substr(str_shuffle('!@#$%^&*'), 0, 5);
	$tempass = $passOne . $passTwo . $passThree;
	return $tempass;
}



/* CREATE GENERATE TRUSTKEY
*****************************/
function pnw_generate_trustkey(){
	$keyOne = substr(str_shuffle('AabBPQRABCDCDcdefgmnEFGOST012345UVWXhijklHIJKLMNOstuvw7890xyzPQRABCDEFGHIJKopqrstuvwxyzLMNYZ'), 0, 15);
	$keyTwo = date('msi') . rand(11111, 99999);
	$keyThree = substr(str_shuffle('@#%^&@#%^&*@#%^&**'), 0, 5);
	$trustkey = $keyOne . $keyTwo . $keyThree;
	return $trustkey;
}



/* LIST CURRENT USER ROLE FUNCTION
************************************/
function pnw_current_user_role(){
	
	$user = wp_get_current_user();
	$roles = $user->roles;
	$hisrole = $roles[0];

	return $hisrole;
	
}



/* CREATE GET PAGE URL FUNCTION
**********************************/
function pnw_get_page_url($type, $page){
global $wpdb;
$table = $wpdb->prefix . 'posts';

	if($type === 'name'){
		$search = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE post_name = '%s' AND post_type = 'page'", $page));
		$result = !empty($search[0]) ? $search[0] : '';
		$post_name = !empty($result->post_name) ? $result->post_name : '';
		$parent = !empty($result->post_parent) ? $result->post_parent : 0;
	}else{
		$search = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE ID = '%s' AND post_type = 'page'", $page));
		$result = !empty($search[0]) ? $search[0] : '';
		$post_name = !empty($result->post_name) ? $result->post_name : '';
		$parent = !empty($result->post_parent) ? $result->post_parent : 0;
	}

	if($parent != 0){
		$search2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE ID = '%s' AND post_type = 'page'", $parent));
		$result2 = !empty($search2[0]) ? $search2[0] : '';
		$post_name2 = !empty($result2->post_name) ? $result2->post_name : '';
		$link = site_url() . '/' .$post_name2. '/' . $post_name . '/';
	}else{
		$link = site_url() . '/' . $post_name . '/';	
	}

return $link;
}





/* CREATE A ROBUST GET USER BY FUNCTION
******************************************/
function pnw_get_user_by($by, $value){
	
	$user = get_user_by($by, $value);
	$userid = !empty($user->ID) ? $user->ID : '';
	$username = !empty($user->user_login) ? $user->user_login : '';
	$password = !empty($user->user_pass) ? $user->user_pass : '';
	$email = !empty($user->user_email) ? $user->user_email : '';
	$role = !empty($user->roles[0]) ? $user->roles[0] : '';
	$pnwphonenumber = !empty($user->pnwphonenumber) ? $user->pnwphonenumber : '';
	$pnwappid = !empty($user->pnwappid) ? $user->pnwappid : '';
	
	
	$build = array(
	'id' => $userid, 
	'username' => $username, 
	'password' => $password, 
	'email' => $email, 
	'role' => $role, 
	'pnwphonenumber' => $pnwphonenumber, 
	'pnwappid' => $pnwappid);

	return $build;
	
}


		

/* UPDATE AUTHENTICATION KEYS FUNCTION 
*****************************************/
function pnw_update_keys($data = '') {
	global $wpdb;
	
	$tablename = $wpdb->prefix . 'picknworksecurity_authentication';
	
	$apikey = !empty($data['apikey']) ? $data['apikey'] : '';
	$apisecret = !empty($data['apisecret']) ? $data['apisecret'] : '';
	$passphrase = !empty($data['passphrase']) ? password_hash($data['passphrase'], PASSWORD_DEFAULT) : '';
	
	// Search if act exist
	$search = $wpdb->get_results("SELECT action FROM $tablename WHERE action = 'act'");
	$result = !empty($search[0]->action) ? $search[0]->action : '';
	

	// inlcude the passphrase in your update or insert function if passphrase is not empty.
	if(!empty($passphrase)){
		if($result === 'act'){
			// Update
			$run = $wpdb->query($wpdb->prepare("UPDATE $tablename SET apikey = '%s', apisecret = '%s', passphrase = '%s' WHERE action = 'act'", $apikey, $apisecret, $passphrase));
			
		}else{
			// Insert New Record		
			$run = $wpdb->insert($tablename, 
			array(
			'apikey' => $apikey,
			'apisecret' => $apisecret,
			'passphrase' => $passphrase),
			array(
			'%s',
			'%s',
			'%s'
			));
		}
	}else{	
		if($result === 'act'){
			// Update
			$run = $wpdb->query($wpdb->prepare("UPDATE $tablename SET apikey = '%s', apisecret = '%s' WHERE action = 'act'", $apikey, $apisecret));
			
		}else{
			// Insert New Record		
			$run = $wpdb->insert($tablename, 
			array(
			'apikey' => $apikey,
			'apisecret' => $apisecret),
			array(
			'%s',
			'%s'
			));
		}
	}
	
	$done = array('type' => 'pnw-success', 'message' => 'Authentication Settings Updated Successfully');
	
	return $done;
} 			
	
	
	
	

/* GET SETTINGS DETAILS FUNCTION
******************************************************* */
function pnw_get_keys() {
	global $wpdb;
	
	$tablename = $wpdb->prefix . 'picknworksecurity_authentication';
	
	$search = $wpdb->get_results("SELECT * FROM $tablename");
	$apikey = !empty($search[0]->apikey) ? $search[0]->apikey : '';
	$apisecret = !empty($search[0]->apisecret) ? $search[0]->apisecret : '';
	$passphrase = !empty($search[0]->passphrase) ? $search[0]->passphrase : '';
	
	
	if(!empty($search)){
		$response = array('apikey' => $apikey, 'apisecret' => $apisecret, 'passphrase' => $passphrase);
	}else{
		$response = array('apikey' => '', 'apisecret' => '', 'passphrase' => '');
	}
	
	return $response;
} 			


	

/* CREATE PUSH GENERAL MULTIPUROSE FUNCTION
******************************************************* */
	function pnw_push($pushto, $process, $more_data) {
		global $leapid;
	
		// Get API KEY, and Credentials
		$key = pnw_get_keys();
		$apikey = !empty($key['apikey']) ? $key['apikey'] : '';
		$apisecret = !empty($key['apisecret']) ? $key['apisecret'] : '';
		$passphrase = !empty($key['passphrase']) ? $key['passphrase'] : '';
		
		
		// Sessionkey as user identification
		$leapid = !empty($_SESSION['leapid']) ? sanitize_text_field($_SESSION['leapid']) : '';
		$ip = pnw_ip();
		
		
		$base_data = array(
		'apikey' => $apikey,
		'apisecret' => pnwsecurityenvelope($apisecret),
		'passphrase' => pnwsecurityenvelope($passphrase),
		'leapid' => pnwsecurityenvelope($leapid),
		'ip' => pnwsecurityenvelope($ip),
		'pushto' => $pushto,
		'process' => pnwsecurityenvelope($process),
		'ontime' => pnwsecurityenvelope(pnwsecuritydate()),
		'load' => pnwsecurityenvelope(json_encode($more_data, true))
		);
		
		$form_data = $base_data;
		
		
		// Validations
		if(empty($pushto)){
		$returned = array('type' => 'pnw-error', 'message' => 'Please specify a pushto');
		}
		elseif(empty($process)){
		$returned = array('type' => 'pnw-error', 'message' => 'Please specify a process');
		}
		else{

		$apidata = array(
		'body' => $form_data,
		);
 		
		$postapi = wp_remote_post('https://api.picknwork.com/build/'.$pushto, $apidata);
		$responsebody = !empty($postapi['body']) ? $postapi['body'] : '';
		
		$returned = json_decode(pnwsecurityopenenvelope($responsebody), true);
		
		}
		return $returned;
	} 

	
	


/* CREATE UPDATE PNW WP OPTION
********************************/
function pnw_update_option($setting, $settingvalue){
	global $wpdb;
	$table = $wpdb->prefix . 'picknworksecurity_options';
	
	$search = $wpdb->get_results($wpdb->prepare("SELECT setting FROM $table WHERE setting = '%s'", $setting));
	if(!empty($search[0]->setting)){
		$update = $wpdb->query($wpdb->prepare("UPDATE $table SET settingvalue = '%s' WHERE setting = '%s'", $settingvalue, $setting));
	}else{
		$insert = $wpdb->query($wpdb->prepare("INSERT INTO $table (setting, settingvalue) VALUES('%s', '%s')", $setting, $settingvalue)); 
	}

}



/* CREATE A GET OPTION FUNCTION FOR PNW WP OPTION
***************************************************/
function pnw_get_option($setting){
	global $wpdb;
	$table = $wpdb->prefix . 'picknworksecurity_options';	
	$search = $wpdb->get_results($wpdb->prepare("SELECT settingvalue FROM $table WHERE setting = '%s'", $setting));
	$result = !empty($search[0]->settingvalue) ? $search[0]->settingvalue : '';
	return $result;
}



/* CREATE GET ALL OPTION FOR PNW WP OPTION
********************************************/
function pnw_get_all_option(){
	global $wpdb;
	$table = $wpdb->prefix . 'picknworksecurity_options';	
	$search = $wpdb->get_results("SELECT * FROM $table");
	
	$data = array();
	if(!empty($search) && is_array($search)){
		foreach($search as $row){
			$key = $row->setting;
			$data[$key] = $row->settingvalue;
		}
	}
	return $data;
}
	
	



/* CREATE GET ALL SETTING FUNCTION	
************************************/
function pnw_get_all_setting(){
	
	$run = pnw_push('allsetting', 'get_all_setting', array());

	return $run;

}

$allsetting = pnw_get_all_setting();
global $allsetting;




/* CREATE GET ALL BLOCKED FUNCTION	
************************************/
function pnw_get_all_blocked(){
	
	$run = pnw_push('allblocked', 'get_all_blocked', array());

	return $run;

}



/* CREATE GET ALL EMAIL FUNCTION	
************************************/
function pnw_get_all_emails(){
	
	$run = pnw_push('allemails', 'get_all_emails', array());

	return $run;

}



/* CREATE GET ALL PHONE FUNCTION	
************************************/
function pnw_get_all_phones(){
	
	$run = pnw_push('allphone', 'get_all_phone', array());

	return $run;

}


/* CREATE EXECUTE DASHBOARD LOCK
************************************/
add_action( 'admin_init', 'pnw_execute_dashboard_lock', 100 );
function pnw_execute_dashboard_lock(){
	 
	$alloptions = pnw_get_all_option();
	$enabled = !empty($alloptions['setting_enable_dashboard_lock']) ? $alloptions['setting_enable_dashboard_lock'] : '';
	$redirect = !empty($alloptions['setting_dashlock_redirect']) ? $alloptions['setting_dashlock_redirect'] : '';
	$link = pnw_get_page_url('name', $redirect);
	
	if(is_user_logged_in()){
		
		$userid = get_current_user_id();
		$user = get_user_by('ID', $userid);
		$role = $user->roles[0];
		$fill = 'dashlock_'.$role;
		$dashrule = !empty($alloptions[$fill]) ? $alloptions[$fill] : '';
		
		if($enabled === 'on' && $dashrule === 'on' && is_admin()){
			exit(wp_redirect($link));
		}
	}		
}



/* CREATE EXECUTE FORCED PHONE
************************************/
add_action( 'init', 'pnw_execute_force_phone_verify');
function pnw_execute_force_phone_verify(){
		
	$alloptions = pnw_get_all_option();
	$enabled = !empty($alloptions['setting_enable_forcedphoneverify']) ? $alloptions['setting_enable_forcedphoneverify'] : '';
	$redirect = !empty($alloptions['setting_forcedphone_redirect']) ? $alloptions['setting_forcedphone_redirect'] : '';
	$link = pnw_get_page_url('name', $redirect);
	
	$bluff = 'donothing';
	$moveto = pnw_remove_query($bluff);
	
	if(is_user_logged_in()){
		
		$userid = get_current_user_id();
		$user = get_user_by('ID', $userid);
		$role = !empty($user->roles[0]) ? $user->roles[0] : '';
		$phoneStatus = !empty($user->pnw_phoneverifystatus) ? $user->pnw_phoneverifystatus : '';
		$fill = 'forcephoneverify_'.$role;
		$forcerule = !empty($alloptions[$fill]) ? $alloptions[$fill] : '';
		
		if($enabled === 'on' && $forcerule === 'on' && $phoneStatus != 'verified' && $moveto != $link){
			exit(wp_redirect($link));
		}
	}		
}


ob_flush();		