<?php
class ModelExtensionModuleSocnetauth2LibVkontakte extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'vkontakte';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_vkontakte_appid'); 
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_vkontakte_appsecret');  
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
		
		return 'https://oauth.vk.com/authorize?client_id='. $this->CLIENT_ID .
				'&scope=SETTINGS,email'.
				'&redirect_uri='.urlencode($REDIRECT_URI).
				'&display=page&'.
				'response_type=code';
			
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
		
			
		$url = "https://oauth.vk.com/access_token?client_id=". $this->CLIENT_ID.
			   "&client_secret=".$this->CLIENT_SECRET.
			   "&code=".$CODE.'&redirect_uri='.$REDIRECT_URI.'&';
			   
		if($IS_DEBUG)
		{
			echo "M3: ".$url."<hr>";
		}
		
		if( extension_loaded('curl') )
		{
			$c = curl_init($url);
				
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);     
			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2); 
			  curl_setopt ($c, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($c);
			curl_close($c);
		}
		else
		{
			$response = file_get_contents($url);
		}
		
		if( $IS_DEBUG ) echo "M4: ".$response."<hr>";
	
		$data = json_decode($response, true);
		 
		if( !empty($data['access_token']) )
		{
			$graph_url = "https://api.vk.com/method/users.get?uids=".$data['user_id'].
			"&v=5.131&fields=uid,first_name,last_name,city,country&access_token=".$data['access_token'];
			
			if( $IS_DEBUG ) echo "M5: ".$graph_url."<hr>";
			
			if( extension_loaded('curl') )
			{
				$c = curl_init($graph_url);
				
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);     
				curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2); 
				
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$json = curl_exec($c);
				curl_close($c);
			}
			else
			{
			
				$json = file_get_contents($graph_url);
			}
			
			
			if( $IS_DEBUG ) echo "M7: ".$json."<hr>";
			
			return array(
				"responce1" => $data,
				"responce2" => json_decode($json, TRUE)
			);
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$arr = array(
			'identity' => $user_data['responce2']['response'][0]['id'],
			'firstname' => $user_data['responce2']['response'][0]['first_name'],
			'lastname'  => $user_data['responce2']['response'][0]['last_name'],
			'email'     => isset( $user_data['responce1']['email'] ) ?  $user_data['responce1']['email'] : (
				isset( $user_data['responce2']['response'][0]['email'] ) ? $user_data['responce2']['response'][0]['email'] : ""
			),
			'telephone'	=> ''
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> 'http://vk.com/id'.$arr['identity'],
			'firstname' => '',
			'lastname'  => '',
			'email'     => $arr['email'],
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
		#$user_data = $user_data['response2']['response'][0];
		
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