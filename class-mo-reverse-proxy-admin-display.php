<?php

class MO_Reverse_Proxy_Admin_Display{

	public function mo_reverse_proxy_show_menu( $currenttab ){
		$utilities = new MO_Reverse_Proxy_Utility_Functions();
		?>
		 <div style="overflow:hidden">
			<div class="wrap">
				<div class="wrap">
					<div><img style="float:left;" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) ) ;?>resources/images/logo.png"></div>
				</div>
			       	<h1>
			            miniOrange Reverse Proxy&nbsp
			       	</h1>
	       	</div>
	       	<br>

			<div class="row">
			<div class="col-md-12">

				<?php

				?>	
				<div class="row mo_reverse_proxy_nav" style="border-bottom: 1px solid #cdcdcd">
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=proxysettings" class="nav-tab <?php if($currenttab === '' || $currenttab === 'proxysettings') echo 'nav-tab-active'; ?>">Proxy Settings</a>
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=iprestriction" class="nav-tab <?php if($currenttab === 'iprestriction') echo 'nav-tab-active'; ?>">IP Restriction</a>
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=cors" class="nav-tab <?php if($currenttab === 'cors') echo 'nav-tab-active'; ?>">CORS</a>
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=loadbalancing" class="nav-tab <?php if($currenttab === 'loadbalancing') echo 'nav-tab-active'; ?>">Load Balancing</a>
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=ratelimiting" class="nav-tab <?php if($currenttab === 'ratelimiting') echo 'nav-tab-active'; ?>">Rate Limiting</a>
					<a href="admin.php?page=mo_reverse_proxy_settings&tab=account" class="nav-tab <?php if($currenttab === 'account') echo 'nav-tab-active'; ?>">Account</a>
				</div>

				<table style="width: 100%;">
					<tr>
						<td style="vertical-align: top;" width="65%;">
				<?php 
					if( $utilities->mo_reverse_proxy_is_customer_registered() ) {	// load_current_view();
						$this->mo_reverse_proxy_show_tab( $currenttab );
					} elseif( get_option('mo_reverse_proxy_login_requested') == 'true' ){
						$this->mo_reverse_proxy_login_ui();
					}
					else {
						$this->mo_reverse_proxy_register_ui();
					}?></td><td style="vertical-align: top;">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="margin-top: 0;" >
					<h3 style="margin-bottom:10px 0;">Contact us</h3>
					<p>Need any help?<br>Just send us a query so we can help you.</p>
					<form action="" method="POST">
					<table class="mo_settings_table">
						<?php wp_nonce_field('mo_reverse_proxy_contact_us_form','mo_reverse_proxy_contact_us_field'); ?>
						<input type="hidden" name="option" value="mo_reverse_proxy_contact_us">
						<tr><td>
							<input style="width:95%;" type="email" placeholder="Enter email here" class="form-control" name="mo_reverse_proxy_contact_us_email" id="mo_reverse_proxy_contact_us_email" required>
						</td></tr><tr><td style="padding: 2px;"></td></tr>
						<tr><td>
							<input style="width:95%;" type="tel" id="mo_reverse_proxy_contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" placeholder="Enter phone here" class="form-control" name="mo_reverse_proxy_contact_us_phone" value="" required>
						</td></tr><tr><td style="padding: 2px;"></td></tr>
						<tr><td>
							<textarea style="width: 95%;" onkeypress="mo_reverse_proxy_contact_us_valid_query(this)" onkeyup="mo_reverse_proxy_contact_us_valid_query(this)" onblur="mo_reverse_proxy_contact_us_valid_query(this)"  name="mo_reverse_proxy_contact_us_query" placeholder="Enter query here" rows="5" id="mo_reverse_proxy_contact_us_query" required></textarea></td></tr>
						<tr><td>
						<input type="submit" class="button button-large button-primary" style="width:100px; margin: 10px 0;" value="Submit">	
						</td></tr>		
						<tr><td>					
					<p>If you want custom features in the plugin, just drop an email at <a href="mailto:info@xecurify.com">info@xecurify.com</a></p></td></tr></table></form>
		</div></div></table></div></div>
		<script>
			jQuery("#mo_reverse_proxy_contact_us_phone").intlTelInput();
			function mo_reverse_proxy_contact_us_valid_query(f) {
			    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
			        /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
			}
		</script>
	<?php

	}

	public function mo_reverse_proxy_show_tab( $currenttab ) {
		
		if( get_option('mo_reverse_proxy_admin_customer_key') != ''){
			if($currenttab == '' || $currenttab == 'proxysettings'){
				$this->mo_reverse_proxy_proxy_settings_ui();
			}elseif( $currenttab == 'account'){
				$this->mo_reverse_proxy_customer_account_ui();
			}elseif( $currenttab == 'iprestriction'){
				$this->mo_reverse_proxy_iprestriction_ui();
			}elseif( $currenttab == 'cors'){
				$this->mo_reverse_proxy_cors_ui();
			}elseif( $currenttab == 'loadbalancing'){
				$this->mo_reverse_proxy_loadbalancing_ui();
			}elseif( $currenttab == 'ratelimiting'){
				$this->mo_reverse_proxy_ratelimiting_ui();
			}
		} 
		else{
			$this->mo_reverse_proxy_login_ui();
		}
	}

	public function mo_reverse_proxy_login_ui(){
		?>
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_reverse_proxy_verify_customer" />
			<?php wp_nonce_field('mo_reverse_proxy_customer_login_form','mo_reverse_proxy_customer_login_form_nonce'); ?>
			<div class="mo_reverse_proxy_card" style="width:100%">
					<h3>Login with miniOrange</h3>
				<p style="font-size: 12px; font-weight: 550;">It seems you already have an account with miniOrange. Please enter your miniOrange email and password.<br/> <a href="#mo_reverse_proxy_forgot_password_link">Click here if you forgot your password?</a></p><br>

				<table class="mo_settings_table">
					<tr>
						<td><strong><font color="#FF0000">*</font>Email:</strong> </td>
						<td><input class="mo_table_textbox" type="email" name="email" required placeholder="person@example.com" value="<?php echo esc_attr( get_option('mo_reverse_proxy_admin_email') );?>" /></td>
					</tr>
						<td><strong><font color="#FF0000">*</font>Password:</strong></td>
						<td><input class="mo_table_textbox" required type="password" name="password" placeholder="Enter your password" /></td>
					</tr>
				</table>
				<br>
			<div>
			<input style="margin-left:30%;" type="submit" name="submit" value="Login" class="button button-primary button-large" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</form>
		<input style="margin-left:2%;" type="button" name="back-button" id="mo_reverse_proxy_back_button" onclick="document.getElementById('mo_reverse_proxy_go_to_register_form').submit();" value="Back" class="button button-primary button-large" /></div>
		<div style="margin: 10px;"></div></div>
					
		<form id="mo_reverse_proxy_go_to_register_form" method="post" action="">
			<input type="hidden" name="option" value="mo_reverse_proxy_go_to_register" />
		</form>
		<script>
			jQuery("a[href=\"#mo_reverse_proxy_forgot_password_link\"]").click(function(){
				window.open('https://login.xecurify.com/moas/idp/resetpassword');
			});
		</script>
		<?php
		
	}

	public function mo_reverse_proxy_register_ui() {
		$current_user = wp_get_current_user();
	?>
			<!--Register with miniOrange-->
		<form class="form" name="f" method="post" action="" novalidate>
			<input type="hidden" name="option" value="mo_reverse_proxy_register_customer" />
			<?php wp_nonce_field('mo_reverse_proxy_customer_registration_form','mo_reverse_proxy_customer_registration_form_nonce'); ?>
			
				<div class="mo_reverse_proxy_card" style="width:100%">
				<h3>Register with miniOrange</h3>
						
					<strong>Why should I register?</strong>
			        <div id="help_register_desc" style="background: aliceblue; padding: 10px; border-radius: 10px;font-size: small; margin: 10px 20px 0 0;">You should register so that in case you need help, we can help you with step by step instructions. <b>You will also need a miniOrange account to upgrade to the premium version of the plugins.</b> We do not store any information except the email that you will use to register with us.
			        </div>
					<br>
			        <table class="mo_settings_table">
					<tr>
							<td><strong><font color="#FF0000">*</font>&nbsp;&nbsp;Area of Interest:</strong></td>
							<td>
								<select style="max-width: 40rem;" class="form-control mo_table_textbox" name="area_of_interest" aria-label="authentication-select" required="true">
									<option value>Select Area of Interest</option>
									<option value="Access Restrictions (IP, Country, Device, Role Based, Media)">Access Restrictions (IP, Country, Device, Role Based, Media)</option> 
									<option value="Secure WordPress site (CORS, Rate Limiting)">Secure WordPress site (CORS, Rate Limiting)</option> 
									<option value="Authentication (SAML, OAuth, LDAP, RADIUS, Header Based)">Authentication (SAML, OAuth, LDAP, RADIUS, Header Based)</option> 
									<option value="Multi-Factor Authentication (MFA)">Multi-Factor Authentication (MFA)</option> 
									<option value="Network Traffic Analysis" >Network Traffic Analysis</option>
									<option value="Improve Site Performace (Caching, Load Balancing, URL Rewriting)" >Improve Site Performace (Caching, Load Balancing, URL Rewriting)</option>
									<option value="other">Other..</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><strong><font color="#FF0000">*</font>&nbsp;&nbsp;Email:</strong></td>
							<td><input class="form-control mo_table_textbox" onchange="checkEmailId(event)" onkeyup="checkEmailId(event)" type="email" id="email" name="email" required placeholder="person@example.com" value="<?php echo esc_attr( get_option('mo_reverse_proxy_admin_email') );?>" /></td>
						</tr>
						<tr class="hidden">
							<td><b><font color="#FF0000">*</font>Website/Company Name:</b></td>
							<td><input class="" type="text" name="company" required placeholder="Enter website or company name"
									value="<?php echo esc_attr( sanitize_text_field( $_SERVER['SERVER_NAME'] ) ); ?>"/></td>
						</tr>
						<tr  class="hidden">
							<td><b>&nbsp;&nbsp;First Name:</b></td>
							<td><input class="" type="text" name="fname" placeholder="Enter first name" value="<?php echo esc_attr( $current_user->user_firstname );?>" /></td>
						</tr>
						<tr class="hidden">
							<td><b>&nbsp;&nbsp;Last Name:</b></td>
							<td><input class="" type="text" name="lname" placeholder="Enter last name" value="<?php echo esc_attr( $current_user->user_lastname );?>" /></td>
						</tr>
						<tr>
							<td><strong><font color="#FF0000"></font>&nbsp;&nbsp;Phone number :</strong></td>
							 <td><input class="form-control mo_table_textbox" type="text" name="phone" pattern="[\+]?([0-9]{1,4})?\s?([0-9]{7,12})?" id="phone" title="Phone with country code eg. +1xxxxxxxxxx" placeholder="Phone with country code eg. +1xxxxxxxxxx" value="<?php echo esc_attr( get_option('mo_reverse_proxy_admin_phone') );?>" />
									<small> We will contact you only if you need support.<small></td>
									 
						</tr>
						<tr  class="hidden">
							<td></td>
							<td>We will call only if you need support.</td>
						</tr>
						<tr>
							<td><strong><font color="#FF0000">*</font>&nbsp;&nbsp;Password:</strong></td>
							<td><input class="form-control mo_table_textbox" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required onchange="checkPassword(event)" id="signup_password" onkeyup="checkPassword(event)" type="password" name="password" placeholder="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" /></td>
						</tr>
						<tr>
							<td><strong><font color="#FF0000">*</font>&nbsp;&nbsp;Confirm Password:</strong></td>
							<td><input class="form-control mo_table_textbox" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required type="password" onchange="checkPassword(event)" id="signup_confirmpassword" onkeyup="checkPassword(event)"  name="confirmPassword" placeholder="Confirm your password" /></td>
						</tr>
						<tr>
							<td><strong><font color="#FF0000">*</font>&nbsp;&nbsp;usecase :</strong></td>
							 <td>
								<textarea class="form-control mo_table_textbox" minlength="15" maxlength="100" type="text" name="usecase" id="usecase" title="Minimum 15 Characters are Required" placeholder="Write about your usecase" required></textarea>
							</td>			 
						</tr>
						<tr>
							<td>&nbsp;&nbsp;</td></tr>
							<tr><td>&nbsp;&nbsp;</td>
							<td><input style="margin-right: 15%;" type="submit" name="submit" value="Register" class="button button-primary button-large"/>
		                    <input type="button" name="mo_reverse_proxy_goto_login" id="mo_reverse_proxy_goto_login" value="Already have an account?" class="button button-primary button-large"/></td>
		                </tr>
		            </table>
				</div>
			</div>
		</form>
		<form name="f1" method="post" action="" id="mo_reverse_proxy_goto_login_form">
            <?php wp_nonce_field("mo_reverse_proxy_goto_login");?>
            <input type="hidden" name="option" value="mo_reverse_proxy_goto_login"/>
            </form>
            <script>
            	jQuery("#phone").intlTelInput();
                jQuery('#mo_reverse_proxy_goto_login').click(function () {
                    jQuery('#mo_reverse_proxy_goto_login_form').submit();
                } );

				(function () {
				'use strict'
				
				// Fetch all the forms we want to apply custom Bootstrap validation styles to
				var forms = document.querySelectorAll('.form')
				
				// Loop over them and prevent submission
				Array.prototype.slice.call(forms)
				.forEach(function (form) {
					form.addEventListener('submit', function (event) {
					if (!form.checkValidity()) {
						event.preventDefault()
						event.stopPropagation()
					}
					form.classList.add('was-validated')
					}, false)
				})
				})()

				checkPassword = (event) =>{
					if (document.getElementById('signup_password').value.toString() !=
						document.getElementById('signup_confirmpassword').value.toString()) {
							document.getElementById('signup_confirmpassword').setCustomValidity("Passwords Don't Match");
					}
					else{
						document.getElementById('signup_confirmpassword').setCustomValidity("");
					} 
				}

				checkEmailId = (event) => {
					var email = document.getElementById('email').value.toString();
					/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email) ?
					document.getElementById('email').setCustomValidity("") :
					document.getElementById('email').setCustomValidity("Invalid Email address")
				}
            </script>
		<?php
}

	public function mo_reverse_proxy_proxy_settings_ui(){
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="width: 100%">
				<div style="display: inline-block;">
				<h3>miniOrange Reverse Proxy Cloud Service</h3></div>
				<span class="mo_reverse_proxy_setup_guide_span"><a href="https://www.miniorange.com/reverse-proxy" target="_blank" rel="noopener" class="mo_reverse_proxy_setup_guide_style" style="text-decoration: none;"> Documentation</a></span>
				<div class="mo_reverse_proxy_feature_desc"><p>
					A reverse proxy server defends web servers against assaults while also improving security, performance and reliability. A reverse proxy is a server that is placed in front of the web servers and sends requests from clients to those servers. In most cases, reverse proxies are used to improve security, performance, and dependability. At the network's edge, a reverse proxy server serves as an intermediary connection point. It acts as the real endpoint and accepts first HTTP connection requests. The reverse proxy server, which acts as a gateway between users and your application origin server, is essentially your network's traffic cop.</p>
				</div>
				<div class="steps">
				<br><h6>Steps to setup Reverse Proxy for your WordPress site:</h6>
				<ol>
					<p>1. Visit <a href="https://proxy.miniorange.com" target="_blank" style="text-decoration: none;"> https://proxy.miniorange.com</a>. Login with your miniorange account and click on <b>Add Proxy</b> button to configure the new proxy.</p>
					<div><img style="width: 90%;" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/miniproxy1.png"></div>
					<br><br>
					<p>2. Give a name to your proxy application(e.g. TestProxy). Define a proxy path and enter your WordPress site URL.</p>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/miniproxy8.png"></div>
					<br><br>
					<p>3. Now, you can access your WordPress site with the proxy link configured as shown in the image below.</p>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/miniproxy9.png"></div>
				</ol>
				</div>
				</div>
			</div>

		</div>
		<?php
	}

	public function mo_reverse_proxy_iprestriction_ui(){
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="width: 100%">
				<div style="display: inline-block;">
				<h3>miniOrange Reverse Proxy Cloud Service- IP Restriction</h3></div>
				<span class="mo_reverse_proxy_setup_guide_span"><a href="https://www.miniorange.com/reverse-proxy/ip-restriction" target="_blank" rel="noopener" class="mo_reverse_proxy_setup_guide_style" style="text-decoration: none;"> Documentation</a></span>
				<div class="mo_reverse_proxy_feature_desc"><p>
					IP Restrictions allows you to give selective access to web servers based on the client’s IP address. The reverse proxy product sits right in front of the backend service. Thus, protecting it from direct access by clients spread throughout the world. When The IP restrictions is enabled, it allows the users to enter specific IP addresses that they want to either allow or restrict from accessing their server using the proxy link.</p>
				</div>
				<div class="steps">
				<br><h6>Steps to setup IP Restriction:</h6>
				<ol>
					<p>1. Switch on the toggle button for <b>Enable IP Restriction</b> and add an IP Address in the input box. Click on <b>Add IP Address</b> to add more and click on <b>Submit</b>.</br>
					2. Enable the <b>Restrict Below IP Addresses</b> if you want to restrict the access to all the configured IPs.</p><br>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/ip_restriction.png"></div>
					<br><br>
				</ol>
				</div>
				</div>
			</div>

		</div>

		<?php
	}

	public function mo_reverse_proxy_cors_ui(){
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="width: 100%">
				<h3>miniOrange Reverse Proxy Cloud Service- CORS</h3>
				<div class="mo_reverse_proxy_feature_desc"><p>
					CORS is an HTTP header-based method that allows a server to specify any origins (domain, scheme, or port) other than its own from which a browser should allow resources to be loaded. CORS is a security feature that is embedded into (almost) all modern web browsers. It essentially prevents any http requests from your front end to any API that does not have the same "Origin" (domain, protocol, and port—which is usually the case).</p>
				</div>
				<div class="steps">
				<br><h6>Steps to setup CORS:</h6>
				<ol>
					<p>1. Switch on the toggle button for <b>Enable CORS</b> and add Domain Name in the input box. Click on <b>Add Domain</b> to add more and click on <b>Submit</b>.<br>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/cors.png"></div>
					<br><br>
				</ol>
				</div>
				</div>
			</div>

		</div>

		<?php
	}

	public function mo_reverse_proxy_loadbalancing_ui(){
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="width: 100%">
				<div style="display: inline-block;">
				<h3>miniOrange Reverse Proxy Cloud Service- Load Balancing</h3></div>
				<span class="mo_reverse_proxy_setup_guide_span"><a href="https://www.miniorange.com/reverse-proxy/load-balancing" target="_blank" rel="noopener" class="mo_reverse_proxy_setup_guide_style" style="text-decoration: none;"> Documentation</a></span>
				<div class="mo_reverse_proxy_feature_desc"><p>
					Load balancing allows to distribute network traffic across multiple servers. Load balancing when enabled provides additional capabilities including application security. The miniOrange reverse proxy product sits right in front of the user’s servers and distributes the load amongst the server addresses provided by the user.</p>
				</div>
				<div class="steps">
				<br><h6>Steps to setup Load Balancing:</h6>
				<ol>
					<p>1. Switch on the toggle button for <b>Enable Load Balancing</b> and add an IP Address in the input box. Click on <b>Add Balancer Member</b> to add more and click on <b>Submit</b>.<br>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/load_balancing.png"></div>
					<br><br>
				</ol>
				</div>
				</div>
			</div>

		</div>

		<?php
	}

	public function mo_reverse_proxy_ratelimiting_ui(){
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="mo_reverse_proxy_card" style="width: 100%">
				<div style="display: inline-block;">
				<h3>miniOrange Reverse Proxy Cloud Service- Rate Limiting</h3></div>
				<span class="mo_reverse_proxy_setup_guide_span"><a href="https://www.miniorange.com/reverse-proxy/rate-limiting" target="_blank" rel="noopener" class="mo_reverse_proxy_setup_guide_style" style="text-decoration: none;"> Documentation</a></span>
				<div class="mo_reverse_proxy_feature_desc"><p>
					Rate limiting feature is essentially used to prevent the server from getting DoS attack, DDoS attack or Slowloris attack. It limits the number of requests a particular IP address can send in a specified amount of time. The reverse proxy server that sits right in front of the user’s server(s) receives all the requests. When in use, it doesn’t let any IP address make requests more than the permitted number.</p>
				</div>
				<div class="steps">
				<br><h6>Steps to setup Rate Limiting:</h6>
				<ol>
					<p>1. Switch on the toggle button for <b>Enable Rate Limiting</b> and configure the Maximum number of requests in given minutes. Click on <b>Submit</b>.<br>
					<div><img style="width: 90%" src="<?php echo esc_attr( plugin_dir_url( __FILE__ ) );?>resources/images/rate_limiting.png"></div>
					<br><br>					
				</ol>
				</div>
				</div>
			</div>

		</div>

		<?php
	}


function mo_reverse_proxy_customer_account_ui() {
	?>
	<div class="mo_reverse_proxy_card" style="width:100%">
		<h6 style="margin: 20px 0;">Thank you for registering with miniOrange.</h6>

		<table border="1"
		   style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0; margin:20px 2px 0; width:85%">
		<tr>
			<td style="width:45%; padding: 10px;"><strong>miniOrange Account Email</strong></td>
			<td style="width:55%; padding: 10px; font-size: 14px;"><?php echo esc_attr( get_option( 'mo_reverse_proxy_admin_email' ) ); ?></td>
		</tr>
		<tr>
			<td style="width:45%; padding: 10px;"><strong>Customer ID</strong></td>
			<td style="width:55%; padding: 10px; font-size: 14px;"><?php echo esc_attr( get_option( 'mo_reverse_proxy_admin_customer_key' ) ) ?></td>
		</tr>
		</table>
		<br />

	<table>
	<tr>
	<td>
	<form name="f1" method="post" action="" id="mo_reverse_proxy_goto_login_form">
		<input type="hidden" value="mo_reverse_proxy_change_miniorange_account" name="option"/>
		<input type="submit" value="Change Email Address" class="button button-primary button-large"/>
	</form>
	</td><td>
	</td>
	</tr>
	</table>
	</div>

	<?php
	}
}