<?php
class ModelExtensionModuleSocnetauth2LibInstagram extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'instagram';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_instagram_client_id'); 
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_instagram_client_secret'); 
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
		
		return 'https://api.instagram.com/oauth/authorize/?client_id='. $this->CLIENT_ID.
					'&redirect_uri='.urlencode($REDIRECT_URI).
					'&response_type=code'.
					'&scope=basic'.
					'&state='.$STATE.'&hl=en';
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		return !empty($this->request->cookie[$this->SOCNET.'_state'] ) ? 
			$this->request->cookie[$this->SOCNET.'_state'] : '';
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		return $recordData && !empty( $this->request->get['code'] ) && empty( $this->request->get['error'] ) 
			? true : false;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$CODE = $this->request->get['code'];
		
		$url = "https://api.instagram.com/oauth/access_token";
		$POSTVARS = "client_id=".$this->CLIENT_ID.
					   "&client_secret=".$this->CLIENT_SECRET.
					   "&grant_type=authorization_code".
					   "&redirect_uri=".urlencode($REDIRECT_URI).
					   "&code=".$CODE;
					   
		if( $IS_DEBUG ) echo "M4: ".$url."<hr>";
		if( $IS_DEBUG ) echo "M4.1: ".$POSTVARS."<hr>";
		
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $POSTVARS);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$response = curl_exec($ch);
		curl_close($ch);
	 
		if( $IS_DEBUG ) echo "M5: ".$response."<hr>";
		$data = json_decode($response, 1);	
		
		if( !empty($data['access_token']) && !empty($data['user']) )
		{
			return $data['user'];
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		 
		if( !empty($user_data['full_name']) )
		{
			$ar = explode(" ", $user_data['full_name']);
			$user_data['first_name'] = $ar[0];
			$user_data['last_name'] = isset($ar[1]) ? $ar[1] : '';
		}
		
		$arr = array(
			'identity'  => $user_data['id'],
			'firstname' => isset($user_data['first_name']) ? $user_data['first_name'] : '',
			'lastname'  => isset($user_data['last_name']) ? $user_data['last_name'] : '',
			'email'     => isset( $user_data['email'] ) ? $user_data['email'] : '',
			'link'      => 'https://www.instagram.com/'.$user_data['username'].'/',
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> $arr['link'],
			'firstname' => $arr['firstname'],
			'lastname'  => $arr['lastname'],
			'email'     => '',
			'telephone'	=> '',
			'data'		=> serialize($arr),
			'provider'  => $provider
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