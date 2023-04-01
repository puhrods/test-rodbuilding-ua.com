<?php
class ModelExtensionModuleSocnetauth2LibMailru extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $CLIENT_PRIVATE = '';
	private $SOCNET = 'mailru';
	private $VERSION = '';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() ) 
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_mailru_id'); 
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_mailru_secret'); 
		$this->CLIENT_PRIVATE = $this->config_get('socnetauth2_mailru_private');  
		$this->VERSION = $this->config_get('socnetauth2_mailru_api_version') ? 
			$this->config_get('socnetauth2_mailru_api_version') : 'old'; 
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
		
		if( $this->VERSION == 'new' )
			return 'https://oauth.mail.ru/login?client_id='.$this->CLIENT_ID.
				'&response_type=code&scope=userinfo&redirect_uri='.urlencode($REDIRECT_URI).'&state='.$STATE;
		else
			return	'https://connect.mail.ru/oauth/authorize?client_id='.$this->CLIENT_ID.
				'&response_type=code&scope=&redirect_uri='.urlencode($REDIRECT_URI);
			
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
		$url = '';
		$fields = array();
		
		if( $this->VERSION == 'new' )
		{
			$url = 'https://oauth.mail.ru/token';
			$fields = array( 
				'client_id' => $this->CLIENT_ID, 
				'grant_type' => 'authorization_code',
				'code' => urlencode($this->request->get['code']),
				'redirect_uri' => urlencode($REDIRECT_URI)
			);
		}
		else
		{
			$url = 'https://connect.mail.ru/oauth/token';
			$fields = array(
							'client_id' => urlencode($this->CLIENT_ID),
							'client_secret' => urlencode($this->CLIENT_SECRET),
							'grant_type' => 'authorization_code',
							'code' => urlencode($this->request->get['code']),
							'redirect_uri' => urlencode($REDIRECT_URI)
					);
			
		}

					
					 
		##if( $IS_DEBUG ) echo "M3: ".print_r($fields, 1)."<hr>";
		
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		$fields_string = rtrim($fields_string, '&');
		 

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);

		curl_setopt($ch, CURLOPT_USERPWD, $this->CLIENT_ID.':'.$this->CLIENT_SECRET);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);
		
		
		$data = json_decode($result, 1);
		 
		if( $IS_DEBUG ) echo "M4: ".$result."<hr>";
		
		if( !empty($data['access_token']) )
		{	  
			$graph_url = '';
			
			if( $this->VERSION == 'new' )
			{
				$graph_url = 'https://oauth.mail.ru/userinfo?access_token='.$data['access_token'];
			}
			else
			{
				$request_params = array(
					'app_id' => $this->CLIENT_ID,
					'method' => 'users.getInfo',
					'secure' => 1,
					'session_key' => $data['access_token'],
					'uids' => $data['x_mailru_vid'],
				);
		 
				$params = '';
				
				foreach ($request_params as $key => $value)
					$params .= "$key=$value";
		 
				
				$graph_url = 'http://www.appsmail.ru/platform/api'.'?'.http_build_query($request_params).
					   '&sig='.md5($params.$this->CLIENT_SECRET);
				
			}
			
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
			
			if( $IS_DEBUG ) echo "M7: ".$json."<hr>";
			
			return json_decode($json, TRUE);
		}
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$arr = array();
		
		if( $this->VERSION == 'new' )
		{
			$arr = array(
				'identity' => $user_data['id'],
				'firstname' => $user_data['first_name'],
				'lastname'  => $user_data['last_name'],
				'email'     => $user_data['email'],
				'telephone'	=> '',
			);
		}
		else
		{
			$arr = array(
				'identity' => $user_data[0]['uid'],
				'firstname' => $user_data[0]['first_name'],
				'lastname'  => $user_data[0]['last_name'],
				'email'     => isset($user_data[0]['email']) ? $user_data[0]['email'] : "",
				'telephone'	=> '',
			);
		}
		
		$link = '';
		if( !empty($arr['email']) )
		{
			$ar = explode("@", $arr['email']);
			$link = 'https://my.mail.ru/mail/'.$ar[0].'/';
		}
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> $link,
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