<?php
//############################
// PROCESS AUTHENTICATION SETTINGS
//############################

if(isset($_POST['submit_activation'])){
	
	$serialno = !empty($_POST['pnw_serialno']) ? sanitize_text_field($_POST['pnw_serialno']) : '';
$filedata = "<?php
if ( !function_exists( 'add_action' ) ) {
	echo 'Direct access denied';
	exit;
}

$"."pnwserialkey = '".strip_tags(filter_var($serialno, FILTER_SANITIZE_STRING))."';
";
	
	if(!empty($serialno)){
		$myfile = fopen(PNWSEC_PATH . 'includes/cranekey.php', "w");
		fwrite($myfile, $filedata);
		fclose($myfile);
	}else{
		$type = 'pnw-error';
		$message = 'Serial number can not be empty';
	}
	
} /* end of if submit_activation is set */


if(isset($_POST['submit_removeactivation'])){
	if(file_exists(PNWSEC_PATH . 'includes/cranekey.php')){
		unlink(PNWSEC_PATH . 'includes/cranekey.php');
	}
}
	
	
?>


<div class="wrap">
	<h1><?php esc_html_e(get_admin_page_title() );?></h1>
</div>
<hr>
		
		<div class="<?php if(!empty($type)){echo esc_html($type);}?>"><?php if(!empty($message)){echo esc_html($message);}?></div>
		
		<!-- CREATE A AUTHENTICATION PAGE	-->
		<div style="display: block; margin-left: auto; margin-right: auto; width: 60%; margin-top: 4%; background-color: white; padding: 4%; border-bottom: 20px solid blue; border-radius: 10px;">
			<form action="" method="post">
			<h3 style="text-align: center;">Activation  </h3><hr>
			<div style="text-align: center;">Enter the activation serial number you recieved upon purchase to get your picknworksecurity plugin activated</div>
			<br>
			<br>
			<?php 
			if(!file_exists(PNWSEC_PATH . 'includes/cranekey.php')){
			echo '<div class="pc-admin-left">SERIAL NO</div><div class="pc-admin-right"><input type="text" name="pnw_serialno" value="" class="pc-admin-select-field"></div>
			<div style="clear: both;"></div>
			<div style="text-align: center;"><input type="submit" name="submit_activation" value="Activate Now" class="pnwbutton"></div> 
			<div style="clear: both;"></div>';
			}else{
			echo '<div style="text-align: center; padding: 2%; font-size: 18px; margin-top: 2%; margin-border-bottom: 2%;">Your picknworksecurity Plugin is activated</div>
			<div style="clear: both;"></div>
			<div style="text-align: center;"><input type="submit" name="submit_removeactivation" value="Remove Activation" class="pnwbutton"></div> 
			<div style="clear: both;"></div>';
			}
			?>
			
			</form>
		</div>