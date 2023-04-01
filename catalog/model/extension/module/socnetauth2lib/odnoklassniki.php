<?php
class ModelExtensionModuleSocnetauth2LibOdnoklassniki extends Model {
	
	private $APPLICATION_ID = '';
	private $CLIENT_SECRET = '';
	private $CLIENT_PUBLIC = '';
	private $SOCNET = 'odnoklassniki';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->APPLICATION_ID = $this->config_get('socnetauth2_odnoklassniki_application_id'); 
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_odnoklassniki_secret_key'); 
		$this->CLIENT_PUBLIC = $this->config_get('socnetauth2_odnoklassniki_public_key');  
	}
	
	private function config_get($key)
	{
		$key = preg_replace("/^module\_/", "", $key);
		
		if( isset($this->REQUISITES[$key]) )
			return $this->REQUISITES[$key];
		else
			return $this->config->get((version_compare(VERSION, '3.0.0.0') >= 0 ? 'module_' : "").$key);
	}
	
	public function getCountryIdByCode($code)
	{	
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` 
						WHERE iso_code_2='".$this->escape($code)."'");
		
		if( !empty($result['country_id']) )
			return $result['country_id'];
	}
	
	public function getStartURL($REQUISITES, $REDIRECT_URI, $STATE, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		return 'https://connect.ok.ru/oauth/authorize?client_id='.
			$this->APPLICATION_ID.'&scope=VALUABLE_ACCESS;LONG_ACCESS_TOKEN;GET_EMAIL'.
			'&response_type=code&GET_EMAIL&redirect_uri='.urlencode($REDIRECT_URI);
			
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
		$CODE = $_GET['code'];
		
		$POSTURL  = 'https://api.ok.ru/oauth/token.do';
		$POSTVARS = 'code='.$CODE.'&redirect_uri='.$REDIRECT_URI.'&grant_type=authorization_code'.
		'&client_id='.$this->APPLICATION_ID.'&client_secret='.$this->CLIENT_SECRET;
		
		
		$ch = curl_init($POSTURL);
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $POSTVARS);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$response = curl_exec($ch);
		curl_close($ch);
	 
		if( $IS_DEBUG ) echo "M4: ".$response."<hr>";
		
		$data = json_decode($response, true);
		
		if( !empty($data['access_token']) )
		{
			$SIGN = md5('application_key='.$this->CLIENT_PUBLIC.'fields=email,first_name,last_name,name,location,localemethod=users.getCurrentUser'.md5($data['access_token'].$this->CLIENT_SECRET));
			#application_key=CBABMBEMEBABABABAformat=jsonmethod=users.getCurrentUser5d57e71d18282133cffd428f9720e88a
			$graph_url = "https://api.odnoklassniki.ru/fb.do?method=users.getCurrentUser".
			"&access_token=".$data['access_token'].
			"&application_key=".$this->CLIENT_PUBLIC.
			"&sig=".$SIGN."&fields=email,first_name,last_name,name,location,locale";
			
			
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
			
			if( $IS_DEBUG ) echo "M6: ".$json."<hr>";
			
			return json_decode($json, TRUE);
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$arr = array(
			'identity' => $user_data['uid'],
			'firstname' => $user_data['first_name'],
			'lastname'  => $user_data['last_name'],
			'email'     => isset($user_data['email']) ? $user_data['email'] : '',
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> "http://ok.ru/profile/".$arr['identity'],
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
		$data['iso_code_2'] = '';
		
		if( !empty( $arr['countryCode'] ) )
		{
			$data['country'] = $this->getCountryIdByCode($arr['countryCode']);
		}
		
		if( !empty( $arr['city'] ) )
		{
			$data['city'] = $ar['city'];
		}
		
		return $data;
	}
	
	public function prepareUserData($user_data)
	{
		return $user_data;
	}
}