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
		$this->username = "helloworld@test.com";
	}

	function getAccessToken(){

		// set up request for access token
		$data		   = array();
		$client_id     = 'jtv'; // Client ID
		$client_secret = '***********'; //Client Secret
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

class Getprofile extends Container
{
	
	function __construct(){

		parent::__construct();
		
		// Get Profile Details Url
		$this->profileUrl = "/sms/2.0/ui/subscriber/get/details"; 		
		
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

	function getprofile(){

		$url = $this->domain . $this->profileUrl . "/" . $this->username;		
		
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


$r = new Getprofile();
$resp = $r->getprofile();

print_r($resp);
?>