<?php
class ModelExtensionModuleSocnetauth2LibYandex extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'yandex';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_yandex_client_id');
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_yandex_client_secret');
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
		
		$scopes = array();
		if( $this->config_get('socnetauth2_yandex_rights_email') )  $scopes[] = 'login:email';
		if( $this->config_get('socnetauth2_yandex_rights_info') )  $scopes[] = 'login:info';
			
		if(empty($scopes))
			exit('error: no rights selected');
			
		return 'https://oauth.yandex.ru/authorize?'.
					'response_type=code'.
					'&client_id='.$this->CLIENT_ID.
					'&scope='.implode("+", $scopes);
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
		
		$url = "https://oauth.yandex.ru/token";
		
		$params = "grant_type=authorization_code".
		"&code=".$_GET['code'].
		"&client_id=".$this->CLIENT_ID.
		"&client_secret=".$this->CLIENT_SECRET;
		
		if($IS_DEBUG)
		{
			echo "M3: ".$params."<hr>";
		}
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $params);
		#curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		#curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		#curl_setopt($ch, CURLOPT_USERPWD, $CLIENT_ID.":".$CLIENT_SECRET); //Your credentials goes here

		$response = curl_exec($ch);
		curl_close($ch);
		
	 
		if( $IS_DEBUG ) echo "M4: ".$response."<hr>";
		
		
		$data = json_decode($response, true);
		
		if( !empty($data['access_token']) )
		{
			$graph_url = "https://login.yandex.ru/info?format=json&with_openid_identity=1";
			
			if( $IS_DEBUG ) echo "M5: ".$graph_url."<hr>";
			
			$c = curl_init($graph_url);
				
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);     
			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2); 
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_HTTPHEADER, 
				array('Authorization: OAuth '.$data['access_token'])
			);

			$json = curl_exec($c);
			curl_close($c);
			
			if( $IS_DEBUG ) echo "M7: ".$json."<hr>";
			
			return json_decode($json, TRUE);
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$arr = array(
			'identity' => $user_data['id'],
			'firstname' => !empty($user_data['first_name']) ? $user_data['first_name'] : '',
			'lastname'  => !empty($user_data['last_name']) ? $user_data['last_name'] : '',
			'email'     => !empty($user_data['emails'][0]) ? $user_data['emails'][0] : '',
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> $user_data['openid_identities'][0],
			'firstname' => '',
			'lastname'  => '',
			'email'     => '',
			'telephone'	=> '',
			'data'		=> serialize($user_data),
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
		foreach($user_data as $key=>$val)
		{
			if( is_array($val) )
			{
				$user_data[$key] = print_r($val, 1);
			}
		}
		
		return $user_data;
	}
}