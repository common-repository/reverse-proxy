<?php

require 'class-mo-reverse-proxy-admin-display.php';
/**
 * The central class to define utility functions
 */
class MO_Reverse_Proxy_Utility_Functions
{
	function __construct() {}

	function miniorange_reverse_proxy_menu(){
		//Add miniOrange plugin to the menu
		$page = add_menu_page("Configuration", "Reverse Proxy","manage_options", "mo_reverse_proxy_settings",array($this, 'mo_reverse_proxy_menu_options') ,plugin_dir_url(__FILE__) . 'resources/images/miniorange.png' );
	}

	public function mo_reverse_proxy_menu_options(){
		
		global $wpdb;
		update_option('mo_reverse_proxy_host_name', 'https://login.xecurify.com');
		$currenttab = "";
		if( isset($_GET['tab']) )
			$currenttab = sanitize_text_field($_GET['tab']);

		$admin_menu = new MO_Reverse_Proxy_Admin_Display();
		$admin_menu->mo_reverse_proxy_show_menu( $currenttab );
	}

	public function enqueue_styles( $hook ){
		if( $hook != 'toplevel_page_mo_reverse_proxy_settings'){
			return;
		}

		wp_enqueue_style( 'mo_reverse_proxy_bootstrap_style', plugins_url( 'resources/css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'mo_reverse_proxy_phone_style', plugins_url( 'resources/css/phone.css', __FILE__ ) );
		wp_enqueue_style( 'mo_reverse_proxy_settings_style', plugins_url( 'resources/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'mo_reverse_proxy_fontawesome', plugins_url( 'resources/css/font-awesome.css', __FILE__ ) );
	}

	public function enqueue_scripts( $hook ) {
		if( $hook != 'toplevel_page_mo_reverse_proxy_settings' ) {
                return;
        }
		wp_enqueue_script( 'mo_reverse_proxy_bootstrap_script', plugins_url( 'resources/js/bootstrap.min.js', __FILE__) );
		wp_enqueue_script( 'mo_reverse_proxy_firebase_phone_script', plugins_url( 'resources/js/phone.js', __FILE__ ) );
	}

	function mo_reverse_proxy_is_customer_registered(){

		if( get_option('mo_reverse_proxy_admin_email') && get_option('mo_reverse_proxy_admin_customer_key') ){
			return true;
		}

		return false;
	}

	public function deactivate() {
		do_action( 'clear_os_cache' ); 
		delete_option( 'mo_reverse_proxy_host_name' );
		delete_option( 'mo_reverse_proxy_admin_customer_key' );
		delete_option( 'mo_reverse_proxy_admin_api_key' );
		delete_option( 'mo_reverse_proxy_customer_token' );
		delete_option( 'mo_reverse_proxy_message' );
	}
}
