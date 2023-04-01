<?php
class ModelExtensionModuleSocnetauth2LibFacebook extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'facebook';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_facebook_appid');
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_facebook_appsecret');
	}
	
	private function config_get($key)
	{
		$key = preg_replace("/^module\_/", "", $key);
		
		if( isset($this->REQUISITES[$key]) )
			return $this->REQUISITES[$key];
		else
			return $this->config->get((version_compare(VERSION, '3.0.0.0') >= 0 ? 'module_' : "").$key);
	}
	
	public function getStartURL($REQUISITES, $REDIRECT_URI, $STATE, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		return 'https://www.facebook.com/dialog/oauth?'.
				'client_id='.$this->CLIENT_ID.
				'&redirect_uri='.urlencode($REDIRECT_URI).
				'&scope=public_profile,email&state='.$STATE;
	}
	
	
	public function getRecordKey($REDIRECT_URI)
	{
		return !empty($this->request->cookie[$this->SOCNET.'_state'] ) ? 
			$this->request->cookie[$this->SOCNET.'_state'] : '';
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		return empty($_GET['error']) && $recordData && !empty( $this->request->get['code'] ) ? true : false;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$CODE = $this->request->get['code'];
	
		$url = "https://graph.facebook.com/oauth/access_token?".
					   "client_id=".$this->CLIENT_ID.
					   "&redirect_uri=".$REDIRECT_URI.
					   "&client_secret=".$this->CLIENT_SECRET.
					   "&code=".$CODE;
		
		if( $IS_DEBUG ) echo "M4: ".$url."<hr>";
		
		if( extension_loaded('curl') )
		{
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_VERBOSE, true);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($c);
			curl_close($c);
		}
		else
		{
			$response = file_get_contents($url);
		}
		
		if( $IS_DEBUG ) echo "M5: ".$response."<hr>";
		$data = json_decode($response, 1);	
		
		if( !empty($data['access_token']) )
		{
			$graph_url = "https://graph.facebook.com/me?access_token=". $data['access_token'].
			"&fields=first_name,last_name,email,link";
			if( $IS_DEBUG ) echo "M6: ".$graph_url."<hr>";
			
			if( extension_loaded('curl') )
			{
				$c = curl_init($graph_url);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_VERBOSE, true);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$json = curl_exec($c);
				curl_close($c);
			}
			else
			{
				$json = file_get_contents($graph_url);
			}
			
			if( $IS_DEBUG ) 
				echo "M7: ".$json."<hr>";
			
			$user_data = json_decode($json, TRUE);
			
			return $user_data;
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$arr = array(
			'identity'  => $user_data['id'],
			'firstname' => $user_data['first_name'],
			'lastname'  => $user_data['last_name'],
			'email'     => isset( $user_data['email'] ) ? $user_data['email'] : '',
			'link'      => isset( $user_data['link'] ) ? $user_data['link'] : '',
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> $arr['link'],
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
			$data['telephone'] = $arr['telephone'];
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