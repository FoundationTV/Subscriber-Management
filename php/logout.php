<?php
/**
 * JunctionTV Subscriber Management with access token
 *
 */
class Container
{

	function __construct(){
		
		// Domain
		$this->domain = "http://__JTV__HOST__"; 
		
		$this->token = "";
		$this->device_type = "web"; 
		$this->username = "helloworld@test.com";
		$this->password = "123456";		
		$this->firstname = "Hello";
		$this->lastname = "World";
		$this->device_num = "web";
		$this->device_name = "Web Browser";
	}

	function getAccessToken(){

		// set up request for access token
		$data		   = array();
		$client_id     = 'jtv'; // Client ID
		$client_secret = '**********'; //Client Secret
		$auth_string   = "{$client_id}:{$client_secret}";
		$request       = "https://cloud.junctiontv.net/ums/2.0/oauth/";
		$ch            = curl_init($request);
		curl_setopt_array($ch, array(
			CURLOPT_POST           => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_USERPWD        => $auth_string,
			CURLOPT_HTTPHEADER     => array(
				'Content-type: application/x-www-form-urlencoded'
			),
			CURLOPT_POSTFIELDS => $data
		));

		$response = curl_exec($ch);		
		
		// Check for errors
		if (!curl_errno($ch)) {
		  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}else{
			die(curl_error($ch));
		}

		curl_close($ch);

		if(isset($httpcode) && $httpcode == 200){
			return $response;
		}else{
			return false;
		}		
		
	}
}

class Logout extends Container
{
	
	function __construct(){

		parent::__construct();
		
		// Logout Url
		$this->logoutUrl = "/sms/2.0/user/logout"; 	

		$this->device_num = "5877569bd5d11617ab216e16"; // Get this value from login success parameter value "device_num"
		
		
		$tok = $this->getAccessToken();

		if($tok){
			$tok = json_decode($tok);
			if(isset($tok->access_token)){
				$this->token = $tok->access_token;
			}else{
				die("Unauthorize");
			}
		}else{
			die("Unauthorize");
		}
	}

	function logout(){

		$url = $this->domain . $this->logoutUrl . "/" . $this->device_type . "/" . $this->device_num . "/" . $this->username;
		
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER     => array(
				"Authorization: Bearer " . $this->token,
			)
		));
       
		//execute
        $result = curl_exec($ch);

        // Check for errors
		if (!curl_errno($ch)) {
		  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}else{
			die(curl_error($ch));
		}

        //close connection
        curl_close($ch);

		if(isset($httpcode) && $httpcode == 200){
			return $result;
		}else{
			return "HTTP Status Code ". $httpcode;
		}	
	}
}

$r = new Logout();
$resp = $r->logout();

print_r($resp);
?>