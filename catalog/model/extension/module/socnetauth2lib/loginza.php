<?php
class ModelExtensionModuleSocnetauth2LibLoginza extends Model {
	
	private $TOKEN = '';
	private $SOCNET = 'loginza';
	
	private function init()
	{
		$this->TOKEN = !empty($this->request->get['loginza_token']) ? $this->request->get['loginza_token'] : (
			 !empty($this->request->post['token']) ? $this->request->post['token'] : ""
		);
	}
	
	public function getStartURL($REDIRECT_URI, $STATE, $IS_DEBUG = 0)
	{
		if( empty($this->request->post['token']) ) 
		{
			if( !empty($this->request->server['HTTP_REFERER']) )
				$url = "Location: ".$this->request->server['HTTP_REFERER'];
			else 
				$url = '/';
			
			return $url;
		}
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		
	}
	
	public function checkReturn($recordData)
	{
		if( empty($this->request->get['loginza_token']) && empty($this->request->post['token']) ) 
		{
			return false;
		}
		else
			return true;
	}
	
	public function getUserData($REDIRECT_URI, $IS_DEBUG = 0)
	{
		$this->init();
		
		$url = "http://loginza.ru/api/authinfo?token=". $this->TOKEN;
				
		if( extension_loaded('curl') )
		{
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			$page = curl_exec($c);
			curl_close($c);
		}
		else
		{
			$page = file_get_contents($url);
		}
				
		if( empty($page) )
		{
			exit('error connection');
		}
				
		return json_decode($page, true);
	}
	
	public function getResultData($arr)
	{
		$this->init();
				
		$provider = '';
				
		if( strstr($arr['provider'], "facebook") )
			$provider = "facebook";
		elseif( strstr($arr['provider'], "vk.com") )
			$provider = "vkontakte";
		elseif( strstr($arr['provider'], "google") )
			$provider = "gmail";
		elseif( strstr($arr['provider'], "twitter") )
			$provider = "twitter";
		elseif( strstr($arr['provider'], "odnoklassniki") )
			$provider = "odnoklassniki";
		elseif( strstr($arr['provider'], "mail.ru") )
			$provider = "mailru";
		elseif( strstr($arr['provider'], "yandex") )
			$provider = "yandex";
		elseif( strstr($arr['provider'], "steam") )
			$provider = "steam";
			
		$data = array(
			'identity'  => !empty($arr['uid']) ? $arr['uid'] : $arr['identity'],		
			'link' 		=> '',
			'firstname' => '',
			'lastname'  => '',
			'email'     => '',
			'telephone'	=> '',
			'data'		=> serialize($arr),
			'provider'  => $provider
		);

		if( $provider == 'steam' )
		{
			$data['identity'] = str_replace("https://steamcommunity.com/openid/id/", "", $data['identity']);
		}

		if( !empty( $arr['name']['first_name'] ) || !empty( $arr['name']['last_name'] ) )
		{
			if( !empty( $arr['name']['first_name'] ) )
				$data['firstname'] = $arr['name']['first_name'];
			
			if( !empty( $arr['name']['last_name'] ) )
				$data['lastname'] = $arr['name']['last_name'];
		}
		elseif( !empty( $arr['name']['full_name'] ) )
		{
			$ar = explode(' ', $arr['name']['full_name']);
			 
			if( !empty($ar[0]) )
				$data['firstname'] = $ar[0];

			if( !empty($ar[1]) )
				$data['lastname'] = $ar[1];
		}

		if( !empty($arr['email']) )
			$data['email'] = $arr['email'];

		$data['link'] = $this->getLink($data, $arr);
				
		$data['company'] = '';
		$data['address_1'] = '';
		$data['postcode'] = '';
		$data['city'] = '';
		$data['zone'] = '';
		$data['country'] = '';
		
		return $data;
	}
	
	public function getLink($data, $arr)
	{
		$link = '';
		
		$identity_list = array(
			#"mailru"=>1,
			"vkontakte"=>1,
			"facebook"=>1,
			"livejournal"=>1,
			"twitter"=>1,
			"linkedin"=>1,
			"wmkeeper.com"=>1,
			"last.fm"=>1,
			"aol.com"=>1,
			"gmail" => 1
		);
		
		if( !empty($identity_list[ $data['provider'] ]) )
		{
			$link = $data['identity'];
		}
		
		if( $data['provider'] == 'mailru' && !empty($data['identity']) )
		{
			if( strstr($data['identity'], 'http://openid.mail.ru/mail/') )
			{
				$key = str_replace('http://openid.mail.ru/mail/', "", $data['identity']);
				$link = 'https://my.mail.ru/mail/'.$key.'/';
			}
			else
			{
				$link = $data['identity'];
			}
		}
		elseif( $data['provider'] == 'odnoklasssniki' && !empty($data['identity']) )
		{
			$link = "http://ok.ru/profile/".$arr['identity'];
		}
		elseif( $data['provider'] == 'yandex' && !empty($arr['openid_identities'][0]) )
		{
			$link = $arr['openid_identities'][0];
		}
		elseif( $data['provider'] == 'gmail' && !empty($data['identity']) )
		{
			$link = 'https://plus.google.com/'.$data['identity'];
		}
		elseif( $data['provider'] == 'twitter' && !empty($arr['identity']) )
		{
			$link = $arr['identity'];
		}
		elseif( $data['provider'] == 'facebook' && !empty($arr['identity']) )
		{
			$link = $arr['identity'];
		}
		elseif( $data['provider'] == 'vkontakte' && !empty($arr['identity']) )
		{
			$link = $arr['identity'];
		}
		
		
		return $link;
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