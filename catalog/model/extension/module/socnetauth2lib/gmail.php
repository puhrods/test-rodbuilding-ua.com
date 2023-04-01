<?php
class ModelExtensionModuleSocnetauth2LibGmail extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'gmail';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Client.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Config.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Auth/Abstract.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Auth/OAuth2.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Service.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Exception.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Auth/Exception.php'); 
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Model.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Utils.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/IO/Abstract.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/IO/Curl.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Http/Request.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Http/CacheParser.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Service/Resource.php');
		require_once(DIR_SYSTEM.'library/socnetauth2/gmail/src/Google/Service/Oauth2.php');
		
		$this->CLIENT_ID = $this->config_get('socnetauth2_gmail_client_id');
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_gmail_client_secret'); 
	}
	
	private function config_get($key)
	{
		$key = preg_replace("/^module\_/", "", $key);
		
		if( isset($this->REQUISITES[$key]) )
			return $this->REQUISITES[$key];
		else
			return $this->config->get((version_compare(VERSION, '3.0.0.0') >= 0 ? 'module_' : "").$key);
	}
	
	public function getStartURL($REQUISITES, $REDIRECT_URI, $STATE)
	{
		$this->init($REQUISITES);
		
		$client = new Google_Client();
		$client->setClientId($this->CLIENT_ID);
		$client->setClientSecret($this->CLIENT_SECRET);
		$client->setRedirectUri($REDIRECT_URI);
		#$client->addScope("https://www.googleapis.com/auth/urlshortener");
		$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");
				
		return $client->createAuthUrl();
	}
	
	
	public function getRecordKey($REDIRECT_URI)
	{
		return !empty($this->request->cookie[$this->SOCNET.'_state'] ) ? 
			$this->request->cookie[$this->SOCNET.'_state'] : '';
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		return $recordData && !empty( $this->request->get['code'] ) ? true : false;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$client = new Google_Client();
		$client->setClientId($this->CLIENT_ID);
		$client->setClientSecret($this->CLIENT_SECRET);
		$client->setRedirectUri($REDIRECT_URI);
		#$client->addScope("https://www.googleapis.com/auth/urlshortener");
		$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");

		$service = new Google_Service_Oauth2($client);
		
		$client->authenticate($this->request->get['code']); 
		
		if( !$client->getAccessToken() ) exit('error1');
		
		$data = json_decode($client->getAccessToken(), 1);
		
		if( $IS_DEBUG ) echo "M4<hr>";
		
		if( !empty($data['access_token']) )
		{	
			$graph_url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token='.$data['access_token'];
			
			if( $IS_DEBUG ) echo "M5: ".$graph_url."<hr>";
			
			if( extension_loaded('curl') )
			{
				$c = curl_init($graph_url);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$json = curl_exec($c);
				curl_close($c);
			}
			else
			{
				$json = file_get_contents($graph_url);
			}
			
				/*				
					id=>100000402380563
					name=>Petrov Konstantin
					first_name=>Petrov
					last_name=>Konstantin
					link=>http://www.gmail.com/petrov.konstantin
					username=>petrov.konstantin
					email=>kin208@gmail.com
					timezone=>6
					locale=>en_US
					verified=>1
					updated_time=>2012-02-11T12:39:00+0000
				*/
			
			if( $IS_DEBUG ) echo "M7: ".$json."<hr>";
		
			return json_decode($json, TRUE);
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		 
		$arr = array(
			'identity' => $user_data['id'],
			'firstname' => $user_data['given_name'],
			'lastname'  => isset( $user_data['family_name'] ) ? $user_data['family_name'] : '',
			'email'     => $user_data['email'],
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> 'https://plus.google.com/'.$arr['identity'],
			'firstname' => '',
			'lastname'  => '',
			'email'     => '',
			'telephone'	=> '',
			'data'		=> serialize($arr),
			'provider'  => $this->SOCNET
		);
		
		if( !empty( $arr['firstname'] ) )
		{
			$data['firstname'] = $arr['firstname'];
		}
		
		if( !empty( $arr['lastname'] ) )
		{
			$data['lastname'] = $arr['lastname'];
		}
		
		if( !empty( $arr['email'] ) )
		{
			$data['email'] = $arr['email'];
		}
		
		if( !empty( $arr['telephone'] ) )
		{
			$data['telephone'] = $ar['telephone'];
		}
		
		$data['company'] = '';
		$data['address_1'] = '';
		$data['postcode'] = '';
		$data['city'] = '';
		$data['zone'] = '';
		$data['country'] = '';
		
		return $data;
	}
	
	public function prepareUserData($user_data)
	{
		return $user_data;
	}
}