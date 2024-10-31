<?php
class MO_Reverse_Proxy_Customer{

	public $email;
	public $phone;

	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
	private $hostName = "https://login.xecurify.com";

	function create_customer( $email, $password, $firstName, $lastName, $phone, $company ){
		$url = $this->hostName . '/moas/rest/customer/add';
		$this->email 		= $email;
		$this->phone 		= $phone;
		
		$fields = array(
			'companyName' => $company,
			'areaOfInterest' => 'Reverse Proxy',
			'firstname'	=> $firstName,
			'lastname'	=> $lastName,
			'email'		=> $this->email,
			'phone'		=> $this->phone,
			'password'	=> $password
		);
		$field_string = json_encode($fields);
		$headers = array( 'Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic' );
		$args = array(
			'method' =>'POST',
			'body' => $field_string,
			'timeout' => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => $headers,
 
		);
		
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: ". esc_attr( $error_message );
			exit();
		}
		
		return wp_remote_retrieve_body($response);
	}

	function check_customer( $email ) {
		$url 	= $this->hostName . "/moas/rest/customer/check-if-exists";

		$fields = array(
			'email' 	=> $email,
		);
		$field_string = json_encode( $fields );
		$headers = array( 'Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic' );
		$args = array(
			'method' =>'POST',
			'body' => $field_string,
			'timeout' => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => $headers,
		);
			
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: ". esc_attr( $error_message );
			exit();
		}
			
		return wp_remote_retrieve_body($response);
	}

	function mo_reverse_proxy_get_customer_key( $email, $password ) {
		$url 	= $this->hostName . "/moas/rest/customer/key";
		
		$fields = array(
			'email' 	=> $email,
			'password' 	=> $password
		);
		$field_string = json_encode( $fields );
		
		$headers = array( 'Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic' );
		$args = array(
			'method' =>'POST',
			'body' => $field_string,
			'timeout' => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => $headers,
 
		);
		
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: ". esc_attr( $error_message );
			exit();
		}
		
		return wp_remote_retrieve_body($response);
	}

	function mo_reverse_proxy_contact_us( $email, $phone, $query ) {
		global $current_user;
		wp_get_current_user();
		if ( defined( 'MO_REVERSE_PROXY_VERSION' ) ) {
			$version = MO_REVERSE_PROXY_VERSION;
		} else {
			$version = '1.0.0';
		}
		$query = '[WP Reverse Proxy Plugin: '.$version.'] ' . $query;
		$fields = array(
			'firstName'			=> $current_user->user_firstname,
			'lastName'	 		=> $current_user->user_lastname,
			'company' 			=> site_url(),
			'email' 			=> $email,
			'ccEmail' 		    => 'proxysupport@xecurify.com',
			'phone'				=> $phone,
			'query'				=> $query
		);
		$field_string = json_encode( $fields );
		
		$url = 'https://login.xecurify.com/moas/rest/customer/contact-us';

		$headers = array( 
			'Content-Type'   => 'application/json', 
			'charset'        => 'UTF - 8', 
			'Authorization'  => 'Basic' 
		);
		$args = array(
			'method'          =>'POST',
			'body'            => $field_string,
			'timeout'         => '15',
			'redirection'     => '5',
			'httpversion'     => '1.0',
			'blocking'        => true,
			'headers'         => $headers,
		);

		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: ". esc_attr( $error_message );
			exit();
		}
		
		return true;
	}

	function mo_reverse_proxy_send_email_alert( $email, $message, $subject ) {
		if( ! $this->check_internet_connection() )
			return;

		$url                 = $this->hostName . '/moas/api/notify/send';
		$customerKey         = $this->defaultCustomerKey;
		$apiKey              = $this->defaultApiKey;
		$currentTimeInMillis = self::get_timestamp();
		$stringToHash 		 = $customerKey .  $currentTimeInMillis . $apiKey;
		$hashValue 			 = hash("sha512", $stringToHash);
		$customerKeyHeader 	 = "Customer-Key: " . $customerKey;
		$timestampHeader 	 = "Timestamp: " .  $currentTimeInMillis;
		$authorizationHeader = "Authorization: " . $hashValue;

		global $user;
		$user         = wp_get_current_user();

		$fields = array(
			'customerKey'	=> $customerKey,
			'sendEmail' 	=> true,
			'email' 		=> array(
				'customerKey' 	=> $customerKey,
				'fromEmail' 	=> $email,
				'fromName' 		=> 'miniOrange',
				'toEmail' 		=> 'proxysupport@xecurify.com',
				'toName' 		=> 'proxysupport@xecurify.com',
				'subject' 		=> $subject,
				'content' 		=> $message
			),
		);
		$field_string             = json_encode($fields);
		$headers                  = array( 'Content-Type' => 'application/json');
		$headers['Customer-Key']  = $customerKey;
		$headers['Timestamp']     = $currentTimeInMillis;
		$headers['Authorization'] = $hashValue;
		
		$args = array(
			'method'      =>'POST',
			'body'        => $field_string,
			'timeout'     => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: ". esc_attr( $error_message );
			exit();
		}
	}

	function check_internet_connection() {
		return (bool) @fsockopen('login.xecurify.com', 443, $iErrno, $sErrStr, 5);
	}

	public function get_timestamp() {
		    $url     = $this->hostName . '/moas/rest/mobile/get-timestamp';
		    $headers = array( 'Content-Type' => 'application/json', 'charset' => 'UTF - 8', 'Authorization' => 'Basic' );
			$args    = array(
				'method'      =>'POST',
				'body'        => array(),
				'timeout'     => '15',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);
			
			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: ". esc_attr( $error_message );
				exit();
			}
			
			return wp_remote_retrieve_body($response);
		}
}
?>