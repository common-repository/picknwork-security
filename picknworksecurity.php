<?php
/**
 * @package Picknwork Security
 * @version 1.0.1
 */
/*
Plugin Name: Picknwork Security
Plugin URI: http://wordpress.org/plugins/picknwork-security/
Description: This is the picknwork security authentication layer for wordpress, woocommerce, gravity form, buddypress, and contact form 7.
Author: Picknwork LLC
Version: 1.0.1
Author URI: https://www.picknwork.com/
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


// ################################
// DEFINES THE PATH TO THE MAIN PLUGIN FILE.
// ################################

	define( 'PNWSEC_FILE', __FILE__ );

	// Defines the path to be used for includes.
	define( 'PNWSEC_PATH', plugin_dir_path( __FILE__ ) );

	// Defines the URL to the plugin.
	define( 'PNWSEC_URL', plugin_dir_url( __FILE__ ) );

	// Defines the current version of the plugin.
	define( 'PNWSEC_VERSION', '1.0.1' );
	


// ################################
// ADD THE PLUGIN CSS FRONTEND
// ################################
function picknworksecurity_add_styles() {
	
	// add css styles
	wp_enqueue_style( 'pnw_style', PNWSEC_URL . 'assets/css/picknworksecurity-wp-style.css');
	wp_enqueue_style( 'pnw_pagination_style', PNWSEC_URL . 'assets/css/paginate.css');

}
add_action( 'wp_enqueue_scripts', 'picknworksecurity_add_styles' );



// ################################
// ADD ADMIN DASHBOARD CSS
// ################################
function picknworksecurity_add_dashboard_styles() {
	
	// add dashboard css
	wp_enqueue_style('pnw_dashboard_style', PNWSEC_URL . 'assets/css/picknworksecurity-wp-dashboard.css');
	wp_enqueue_style( 'pnw_pagination_style', PNWSEC_URL . 'assets/css/paginate.css');

}
add_action( 'admin_enqueue_scripts', 'picknworksecurity_add_dashboard_styles' );



// ################################
// ADD GENERAL FRONTEND JS
// ################################
function picknworksecurity_general_js() {
 
	wp_enqueue_script( 'pnw_js_paginate_front',  PNWSEC_URL . 'assets/js/paginate.js', array( 'jquery' ) );
	wp_enqueue_script( 'pnw_js_front',  PNWSEC_URL . 'assets/js/pickworksecurity-wp-frontend.js', array() );
}
add_action('wp_enqueue_scripts', 'picknworksecurity_general_js');


// ################################
// ADD ADMIN DASHBOARD JS
// ################################ 
function picknworksecurity_admin_js() {

	wp_enqueue_script( 'pnw_js_paginate_back',  PNWSEC_URL . 'assets/js/paginate.js', array( 'jquery' ) );
 	wp_enqueue_script( 'pnw_js_back', PNWSEC_URL . 'assets/js/picknworksecurity-wp-dashboard.js', array( 'jquery' ) );
	
}
add_action( 'admin_enqueue_scripts', 'picknworksecurity_admin_js' );



// ################################
// INCLUDE SOME FUNCTIONS THAT IS IN ANOTHER FILE
// ################################

include(PNWSEC_PATH . 'includes/function.php');
include(PNWSEC_PATH . 'includes/login.php');
include(PNWSEC_PATH . 'includes/forms.php');


//##########################
// CREATE TABLES ON PLUGIN ACTIVATION IF TABLE DOES NOT EXIST
//##########################

register_activation_hook( __FILE__, 'picknwork_create_table' );
function picknwork_create_table(){
	
	global $wpdb;
	
	// CREATE PNW_AUTHENTICATION TABLE
	
	$charset_collate = $wpdb->get_charset_collate();
	$tablename1 = $wpdb->prefix . 'picknworksecurity_authentication';
	$tablename2 = $wpdb->prefix . 'picknworksecurity_options';

	$sql1 = "CREATE TABLE IF NOT EXISTS $tablename1 (
	 id int(10) NOT NULL AUTO_INCREMENT,
	 apikey varchar(255) DEFAULT NULL,
	 apisecret varchar(255) DEFAULT NULL,
	 passphrase varchar(255) DEFAULT NULL,
	 action varchar(255) NOT NULL DEFAULT 'act',
	 PRIMARY KEY (`id`)
	)$charset_collate;";

	
	
	// CREATE PNW_OPTIONS TABLE
	$sql2 = "CREATE TABLE IF NOT EXISTS $tablename2 (
	`setting` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `settingvalue` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
     PRIMARY KEY (`setting`)
    ) $charset_collate;";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql1);
	dbDelta($sql2);

}




function wp_picknworksecurity_settings_page() {
	
	$getpage = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
	
	add_menu_page(
		__('Picknworksecurity Settings Panel', 'wppicknworksecurity'),
		__('Picknworksecurity Settings', 'wppicknworksecurity'),
		'manage_options',
		'picknworksecurity-settings',
		'wppicknworksecurity_settings_page',
		'dashicons-admin-tools',
		100
	);
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Activation', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-awards"></span> Activation', 'wppicknworksecurity'),
		'manage_options',
		'picknworksecurity-activation',
		'wppicknworksecurity_settings_activation_subpage'
	);
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Picknworksecurity Authentication Keys Settings', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-admin-network"></span> Authentication', 'wppicknworksecurity'),
		'manage_options',
		'picknworksecurity-authentication-keys',
		'wppicknworksecurity_settings_authentication_keys_subpage'
	);
	
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Manage Email List ', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-email"></span> Email List', 'wppicknworksecurity'),
		'manage_options',
		'picknworksecurity-email-list',
		'wppicknworksecurity_settings_email_list_subpage'
	);
	
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Manage Phone List', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-phone"></span> Phone List', 'wppicknworksecurity'),
		'manage_options',
		'picknworksecurity-phone-list',
		'wppicknworksecurity_settings_phone_list_subpage'
	);
	
	
	
	// Add Message users links
	if($getpage === 'picknworksecurity-message-users-sms'){
		$messageuserslink = 'picknworksecurity-message-users-sms';
	}else{
		$messageuserslink = 'picknworksecurity-message-users-email';
	}
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Message Your Site Users', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-format-chat"></span> Message Users', 'wppicknworksecurity'),
		'manage_options',
		$messageuserslink,
		'wppicknworksecurity_settings_message_users_subpage'
	);
	
	
	// Add blocked links
	if($getpage === 'picknworksecurity-add-blocks'){
		$blocklink = 'picknworksecurity-add-blocks';
	}else{
		$blocklink = 'picknworksecurity-manage-blocks';
	}
	
	add_submenu_page(
		__('picknworksecurity-settings', 'wppicknworksecurity'),
		__('Manage Blocks', 'wppicknworksecurity'),
		__('<span class="dashicons dashicons-dismiss"></span> Manage Blocks', 'wppicknworksecurity'),
		'manage_options',
		$blocklink,
		'wppicknworksecurity_settings_blocked_subpage'
	);
	
}
add_action('admin_menu', 'wp_picknworksecurity_settings_page');


// LINK TO SETTINGS PAGE FROM PLUGINS SCREEN
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wppicknworksecurity_add_settings_links' );
function wppicknworksecurity_add_settings_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'admin.php?page=picknworksecurity-settings' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
}


function wppicknworksecurity_settings_page() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	include('templates/admin-settings.php');
}



function wppicknworksecurity_settings_activation_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	include('templates/admin-activation.php');
}


function wppicknworksecurity_settings_authentication_keys_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	include('templates/admin-authentication.php');
}



function wppicknworksecurity_settings_email_list_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	include('templates/admin-email-list.php');
}


function wppicknworksecurity_settings_phone_list_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	include('templates/admin-phone-list.php');
}


function wppicknworksecurity_settings_message_users_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	$getpage = isset($_GET['page']) ? esc_url_raw($_GET['page']) : '';
	if($getpage === 'picknworksecurity-message-users-sms'){
		
		include('templates/admin-message-users-sms.php');
	}else{
		include('templates/admin-message-users-email.php');
	}
	
}


function wppicknworksecurity_settings_blocked_subpage() {
	
	// Double check user capabilities
	if (!current_user_can('manage_options') ){
		return;
	}
	
	
	$getpage = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
	if($getpage === 'picknworksecurity-add-blocks'){
		
		include('templates/admin-add-blocked.php');
	}else{
		include('templates/admin-blocked.php');
	}
	
}


// DECLARE LEAPID CODE HERE
global $leapid;
// Generate leapid Keys
$code1 = substr(str_shuffle('0123456789abcdefghijklmnopABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
$code2 =  strtotime('NOW');
$code3 = substr(str_shuffle('0123456789abcdefghijklmnopABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
$code4 = rand(111111, 999999);
$code5 = substr(str_shuffle('0123456789abcdefghijklmnopABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);

$key = $code1 . $code2 . $code3 . $code4 . $code5;

if(empty($_SESSION['leapid'])) { 
$_SESSION['leapid'] = $key;

$leapid = !empty($_SESSION['leapid']) ? sanitize_text_field($_SESSION['leapid']) : '';

}
