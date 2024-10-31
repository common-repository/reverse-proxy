<?php
/**
* Plugin Name: Boost Site Performance with Reverse Proxy
* Description: Secure and protect your WP website from unauthorized access from web vulnerabilities, hence providing a secure connection between your internal services and external clients.
* Version: 1.1.5
* Author: miniOrange
* Author URI: https://www.miniorange.com
* License: MIT/Expat
* License URI: https://docs.miniorange.com/mit-license
*/

require_once('class-mo-reverse-proxy-loader.php');
require_once('class-mo-reverse-proxy-customer.php');

$mo_reverse_proxy_loader = new MO_Reverse_Proxy_Loader();

function mo_reverse_proxy_process_settings(){

	$utilities = new MO_Reverse_Proxy_Utility_Functions();

	if( isset($_POST['option']) ){ 

		if ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == "mo_reverse_proxy_register_customer" && isset($_POST["mo_reverse_proxy_customer_registration_form_nonce"]) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_reverse_proxy_customer_registration_form_nonce'] ) ), 'mo_reverse_proxy_customer_registration_form' ) ) {	
			//register the admin to miniOrange
			//validation and sanitization
			if ( mo_reverse_proxy_check_empty_or_null( $_POST['email'] ) || mo_reverse_proxy_check_empty_or_null( $_POST['password'] ) || mo_reverse_proxy_check_empty_or_null( $_POST['confirmPassword'] ) ) {
				update_option( 'mo_reverse_proxy_message', 'All the fields are required. Please enter valid entries.');
				mo_reverse_proxy_show_error_message();
							return;
			} elseif ( strlen( sanitize_text_field($_POST['password']) ) < 8 || strlen( sanitize_text_field($_POST['confirmPassword']) ) < 8) {
				update_option( 'mo_reverse_proxy_message', 'Choose a password with minimum length 8.');
				mo_reverse_proxy_show_error_message();
				return;
			} else {
				$email = sanitize_email( $_POST['email'] );
				$phone = sanitize_text_field( $_POST['phone'] );
				$password = sanitize_text_field( $_POST['password'] );
				$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
				$fname = sanitize_text_field( $_POST['fname'] );
				$lname = sanitize_text_field( $_POST['lname' ] );
				$company = sanitize_text_field( $_POST['company'] );
				$area_of_interest = sanitize_text_field( $_POST['area_of_interest'] );
				$usecase = sanitize_text_field( $_POST['usecase'] );
				

				if ( strcmp( $password, $confirmPassword) == 0 ) {
					$customer = new MO_Reverse_Proxy_Customer();
					$content = json_decode( $customer->check_customer( $email ), true );

					if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ) {
						$response = json_decode( $customer->create_customer( $email, $password, $fname, $lname, $phone, $company ), true );
						if ( strcasecmp( $response['status'], 'SUCCESS' ) != 0 ) {
							update_option( 'mo_reverse_proxy_message', 'Failed to create customer. Try again.' );
							mo_reverse_proxy_show_error_message();
						}else {
							update_option( 'mo_reverse_proxy_admin_email', $email );
							update_option( 'mo_reverse_proxy_admin_phone', $phone );
							update_option( 'mo_reverse_proxy_admin_fname', $fname );
							update_option( 'mo_reverse_proxy_admin_lname', $lname );
							update_option( 'mo_reverse_proxy_admin_company', $company );
							update_option( 'mo_reverse_proxy_login_requested', 'true');

							$subject = "WP Reverse Proxy New Customer Registered";
							$message = "<div>
											<br>First Name: ".$fname."
											<br><br>Last Name: ".$lname."
											<br><br>Customer: ".$email."
											<br><br>Domain: ".site_url()."
											<br><br>Company: ".$company."
											<br><br>Phone: ".$phone."
											<br><br>Area of Interest: ".$area_of_interest."
											<br><br>Use-Case: ".$usecase."
										</div>";
							$customer->mo_reverse_proxy_send_email_alert( $email, $message, $subject );
							update_option( 'mo_reverse_proxy_message', 'Your registration is successful. Please login.' );
							mo_reverse_proxy_show_success_message();
						}
						
					} elseif ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						update_option( 'mo_reverse_proxy_message', 'Account already exist. Please Login.' );
						mo_reverse_proxy_show_error_message();
					} else {
						update_option( 'mo_reverse_proxy_message', $content['status'] );
						mo_reverse_proxy_show_error_message();
					}
							
				} else {
					update_option( 'mo_reverse_proxy_message', 'Passwords do not match.');
					mo_reverse_proxy_show_error_message();
				}

			}
		}
		elseif(sanitize_text_field( wp_unslash( $_POST['option'] ) ) == "mo_reverse_proxy_verify_customer" && isset($_POST["mo_reverse_proxy_customer_login_form_nonce"]) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_reverse_proxy_customer_login_form_nonce'] ) ), 'mo_reverse_proxy_customer_login_form' )) {
			$email = '';
			$password = '';
			if( mo_reverse_proxy_check_empty_or_null( $_POST['email'] ) || mo_reverse_proxy_check_empty_or_null( $_POST['password'] ) ) {
				update_option( 'mo_reverse_proxy_message', 'All the fields are required. Please enter valid entries.');
				mo_reverse_proxy_show_error_message();
				return;
			} else{
				$email = sanitize_email( $_POST['email'] );
				$password = sanitize_text_field( $_POST['password'] );
			}

			$customer = new MO_Reverse_Proxy_Customer();
			$content = $customer->mo_reverse_proxy_get_customer_key( $email, $password );
			$customerKey = json_decode( $content, true );
			if( json_last_error() == JSON_ERROR_NONE ) {				

				if(!get_option('mo_reverse_proxy_admin_email')){
					$subject = "WP Reverse Proxy New Customer Login";
					$message = "<div>New customer has logged in.<br><br>Customer: ".$email."<br>Domain: ".site_url()."</div>";
					$new_customer_notify = $customer->mo_reverse_proxy_send_email_alert($email, $message, $subject);
				}
				update_option( 'mo_reverse_proxy_admin_email', $email );
				update_option( 'mo_reverse_proxy_admin_customer_key', $customerKey['id'] );
				update_option( 'mo_reverse_proxy_admin_api_key', $customerKey['apiKey'] );
				update_option( 'mo_reverse_proxy_customer_token', $customerKey['token'] );
				if( isset( $customerKey['phone'] ) )
					update_option( 'mo_reverse_proxy_admin_phone', $customerKey['phone'] );
				update_option( 'mo_reverse_proxy_message', 'Customer retrieved successfully');
				mo_reverse_proxy_show_success_message();

			} else {
				update_option( 'mo_reverse_proxy_message', 'Invalid username or password. Please try again.');
				mo_reverse_proxy_show_error_message();
			}
		}
		elseif ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == 'mo_reverse_proxy_contact_us' && isset($_REQUEST['mo_reverse_proxy_contact_us_field']) && wp_verify_nonce( sanitize_text_field( wp_unslash($_REQUEST['mo_reverse_proxy_contact_us_field'] )), 'mo_reverse_proxy_contact_us_form' ) ) {
			$email = isset( $_POST['mo_reverse_proxy_contact_us_email'] ) ? sanitize_email( $_POST['mo_reverse_proxy_contact_us_email'] ) : "";
			$phone = "+ ".preg_replace( '/[^0-9]/', '', sanitize_text_field($_POST['mo_reverse_proxy_contact_us_phone']) );
			$query = isset( $_POST['mo_reverse_proxy_contact_us_query'] ) ? sanitize_textarea_field( $_POST['mo_reverse_proxy_contact_us_query'] ) : "";
			if ( mo_reverse_proxy_check_empty_or_null( $email ) || mo_reverse_proxy_check_empty_or_null( $query ) ) {
				update_option( 'mo_reverse_proxy_message', 'Please fill up Email and Query fields to submit your query.' );
				mo_reverse_proxy_show_success_message();
			} else {
				$contact_us = new MO_Reverse_Proxy_Customer();
				$submited   = $contact_us->mo_reverse_proxy_contact_us( $email, $phone, $query );
				if ( $submited == false ) {
					update_option('mo_reverse_proxy_message', 'Your query could not be submitted. Please try again.');
					mo_reverse_proxy_show_error_message();
				} else {
					update_option( 'mo_reverse_proxy_message', 'Thanks for getting in touch! We shall get back to you shortly.' );
					mo_reverse_proxy_show_success_message();
				}
			}
		} elseif ( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == "mo_reverse_proxy_goto_login" ) {
			$utilities->deactivate();
			update_option( 'mo_reverse_proxy_login_requested', 'true');
		} elseif( sanitize_text_field( wp_unslash( $_POST['option'] ) ) == "mo_reverse_proxy_go_to_register" || sanitize_text_field( wp_unslash( $_POST['option'] ) ) == "mo_reverse_proxy_change_miniorange_account" ){
			$utilities->deactivate();
			delete_option( 'mo_reverse_proxy_login_requested');
		}
	}
}

function mo_reverse_proxy_show_success_message() {
	remove_action( 'admin_notices', 'mo_reverse_proxy_success_message' );
	add_action( 'admin_notices', 'mo_reverse_proxy_error_message' );
}

function mo_reverse_proxy_show_error_message() {
	remove_action( 'admin_notices', 'mo_reverse_proxy_error_message' );
	add_action( 'admin_notices', 'mo_reverse_proxy_success_message' );
}

function mo_reverse_proxy_success_message() {
	$class = "error";
	$message = get_option('mo_reverse_proxy_message');
	echo "<br><div class='" . esc_attr( $class ) . "'> <p>" . esc_attr( $message ) . "</p></div>";
}

function mo_reverse_proxy_error_message() {
	$class = "updated";
	$message = get_option('mo_reverse_proxy_message');
	echo "<br><div class='" . esc_attr( $class ) . "'><p>" . esc_attr( $message ) . "</p></div>";
}

function mo_reverse_proxy_check_empty_or_null( $value ) {
	if( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
}