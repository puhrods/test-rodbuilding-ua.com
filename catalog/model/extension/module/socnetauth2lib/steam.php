<?php
class ModelExtensionModuleSocnetauth2LibSteam extends Model {
	
	private $API_KEY = ''; 
	private $SOCNET = 'steam';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		require_once(DIR_SYSTEM.'library/socnetauth2/steam/openid.php');
		
		$this->API_KEY = $this->config_get('socnetauth2_steam_api_key');   
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
		
		$openid = new LightOpenID($this->request->server['SERVER_NAME']);
			
		if(!$openid->mode) 
		{
			$openid->identity = 'http://steamcommunity.com/openid';
			return $openid->authUrl();
		}
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		if( !empty($this->request->cookie[$this->SOCNET.'_state'] ) )
		{
			return $this->request->cookie[$this->SOCNET.'_state'];
		}
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		return $recordData ? true : false;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$openid = new LightOpenID($this->request->server['SERVER_NAME']);
		 
		if( $openid->data['openid_identity'] && $openid->data['openid_mode'] && $openid->data['openid_mode'] != 'cancel' && 
			$openid->validate() )
		{
			$ptn = "/^https:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
			preg_match($ptn, $openid->data['openid_identity'], $matches);
			$STEAM_ID = $matches[1];
			
			$response = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".
			$this->API_KEY."&steamids=".$STEAM_ID); 
			
			if( $IS_DEBUG ) echo "M4: ".$response."<hr>";
			
			$content = json_decode($response, true);
			
			return array(
				"content" => $content,
				"openid" => $openid
			);
		}
		else
		{
			if( $IS_DEBUG ) echo "M4: ".print_r($openid, 1)."<hr>";
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
			
		$arr = array(
			'identity' => $user_data['content']['response']['players'][0]['steamid'],
			'firstname' => !empty( $user_data['content']['response']['players'][0]['realname'] ) ? $user_data['content']['response']['players'][0]['realname'] : '',
			'lastname'  => '',
			'email'     => '',
			'telephone'	=> ''
		);
			
 
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> $user_data['openid']->identity,
			'firstname' => $arr['firstname'],
			'lastname'  => '',
			'email'     => '',
			'telephone'	=> '',
			'data'		=> serialize($arr),
			'provider'  => $this->SOCNET
		); 
		
		
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
		$user_data = $user_data['content'];
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