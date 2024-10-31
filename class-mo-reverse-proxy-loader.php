<?php

require_once('class-mo-reverse-proxy-utility-functions.php');
/**
 * The central class to call all actions & filters.
 */
class MO_Reverse_Proxy_Loader 
{	
	function __construct()
	{
		$this->mo_reverse_proxy_call_wp_hooks();
	}

	/**
	 * 
	 * Function to call all hooks
	 * 
	 */
	private function mo_reverse_proxy_call_wp_hooks(){

		$utilities = new MO_Reverse_Proxy_Utility_Functions();
		add_action( 'admin_menu', array( $utilities, 'miniorange_reverse_proxy_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $utilities, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $utilities, 'enqueue_scripts') );
		add_action( 'admin_init', 'mo_reverse_proxy_process_settings');
	}
}