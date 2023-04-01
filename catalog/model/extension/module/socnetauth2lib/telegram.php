<?php
class ModelExtensionModuleSocnetauth2LibTelegram extends Model {
	
	private $BOT_USERNAME = '';
	private $BOT_TOKEN = '';
	private $SOCNET = 'telegram';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->BOT_USERNAME = $REQUISITES['socnetauth2_telegram_bot_username']; 
		$this->BOT_TOKEN = $REQUISITES['socnetauth2_telegram_bot_token']; 
	}
	
	private function config_get($key)
	{
		$key = preg_replace("/^module\_/", "", $key);
		
		if( isset($this->REQUISITES[$key]) )
			return $this->REQUISITES[$key];
		else
			return $this->config->get($key);
	}
	
	public function getStartURL($REQUISITES, $REDIRECT_URI, $STATE, $IS_DEBUG = 0)
	{
		//none
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		return !empty($this->request->cookie[$this->SOCNET.'_state'] ) ? 
			$this->request->cookie[$this->SOCNET.'_state'] : '';
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		$this->init($REQUISITES);
		
		if( !$recordData || empty( $this->request->get['hash'] ) || !empty( $this->request->get['error'] ) )
			return false;
		
		
		$check_hash = $this->request->get['hash'];
		
		$auth_data = $this->request->get; 
		unset($auth_data['hash']);
		
		if( isset($auth_data['_route_']) )
			unset($auth_data['_route_']);
		
		if( isset($auth_data['route']) )
			unset($auth_data['route']);
		
		if( isset($auth_data['socnet']) )
			unset($auth_data['socnet']);
		
		$data_check_arr = [];
		foreach ($auth_data as $key => $value) {
			$data_check_arr[] = $key . '=' . $value;
		}
		sort($data_check_arr);
		$data_check_string = implode("\n", $data_check_arr);
		$secret_key = hash('sha256', $this->BOT_TOKEN, true);
		$hash = hash_hmac('sha256', $data_check_string, $secret_key);
		
		if (strcmp($hash, $check_hash) !== 0) {
			//throw new Exception('Data is NOT from Telegram');
			return false;
		}
		
		if ((time() - $auth_data['auth_date']) > 86400) {
			//throw new Exception('Data is outdated');
			return false;
		}
		
		return true;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$data = array(
			"id" => isset($this->request->get['id']) ? $this->request->get['id'] : "",
			"first_name" => isset($this->request->get['first_name']) ? $this->request->get['first_name'] : "",
			"last_name" => isset($this->request->get['last_name']) ? $this->request->get['last_name'] : "",
			"username" => isset($this->request->get['username']) ? $this->request->get['username'] : "",
			"photo_url" => isset($this->request->get['photo_url']) ? $this->request->get['photo_url'] : "",
			"auth_date" => isset($this->request->get['auth_date']) ? $this->request->get['auth_date'] : "",
			"hash" => isset($this->request->get['hash']) ? $this->request->get['hash'] : "",
		);
		return $data;
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
			'firstname' => !empty($user_data['first_name']) ? $user_data['first_name'] : '',
			'lastname'  => !empty($user_data['last_name']) ? $user_data['last_name'] : '',
			'email'     => !empty( $user_data['email'] ) ? $user_data['email'] : '',
			'link'      => !empty( $user_data['username'] ) ? '@'.$user_data['username'] : '',
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