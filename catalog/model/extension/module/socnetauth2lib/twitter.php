<?php
class ModelExtensionModuleSocnetauth2LibTwitter extends Model {
	
	private $CONSUMER_KEY = '';
	private $CONSUMER_SECRET = '';
	private $SOCNET = 'twitter';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		require_once(DIR_SYSTEM.'library/socnetauth2/twitter/twitteroauth.php');

		$this->CONSUMER_KEY = $this->config_get('socnetauth2_twitter_consumer_key'); 
		$this->CONSUMER_SECRET = $this->config_get('socnetauth2_twitter_consumer_secret'); 
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
		
		if( empty($this->request->get['oauth_token']) && empty($this->request->post['oauth_token']))
		{		
			define("CONSUMER_KEY", $this->CONSUMER_KEY);
			define("CONSUMER_SECRET", $this->CONSUMER_SECRET);
			define("CALLBACK_URL", $REDIRECT_URI);
			
			$twitteroauth = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET);    
			$request_token = $twitteroauth->getRequestToken($REDIRECT_URI);
			
			$this->session->data['oauth_token'] = $request_token['oauth_token'];  
			$this->session->data['oauth_token_secret'] = $request_token['oauth_token_secret'];  
			
			#$STATE = md5($request_token['oauth_token'].$request_token['oauth_token_secret']);
			#setcookie($this->SOCNET."_state", $STATE, time()+3600, "/" );
			
			
			if($twitteroauth->http_code==200)
			{  
				$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);  

				if($IS_DEBUG)
				{
					if( strstr($url, "?") )
					{
						$url .= '&error=2';
					}
					else
					{
						$url .= '?error=2';
					}
				}
				
				return $url;
			}
		}
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		return !empty($this->request->cookie[$this->SOCNET.'_state'] ) ? 
			$this->request->cookie[$this->SOCNET.'_state'] : ''; 
	}
	
	public function checkReturn($recordData, $REQUISITES = array())
	{
		return $recordData ? true : false;
	}
	
	public function getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init($REQUISITES);
		
		$twitteroauth = new TwitterOAuth(
			$this->CONSUMER_KEY, 
			$this->CONSUMER_SECRET, 
			$this->session->data['oauth_token'], 
			$this->session->data['oauth_token_secret']
		);
		
		$access_token = $twitteroauth->getAccessToken($this->request->get['oauth_verifier']);      
		$this->session->data['access_token'] = $access_token; 
		
		$params = array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');
		$user_data = $twitteroauth->get('account/verify_credentials', $params );
		
		return $user_data;
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		$ar1 = explode(" ", $user_data->name);
		$first_name = $ar1[0];
		$last_name = $ar1[1];
		
		$arr = array(
				'identity' => $user_data->id,
				'firstname' => $ar1[0],
				'lastname'  => $ar1[1],
				'email'     => isset( $user_data->email ) ? $user_data->email : '',
				'telephone'	=> '',
				'link'		=> "https://twitter.com/".$user_data->screen_name
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
		$user_data = json_decode(json_encode($user_data), true);
		
		$user_data =  (array)$user_data;
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