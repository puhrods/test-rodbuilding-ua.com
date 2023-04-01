<?php
class ModelExtensionModuleSocnetauth2LibTinkoff extends Model {
	
	private $CLIENT_ID = '';
	private $CLIENT_SECRET = '';
	private $SOCNET = 'tinkoff';
	private $REQUISITES = array();
	
	public function init($REQUISITES = array() )
	{
		$this->REQUISITES = $REQUISITES;
		$this->CLIENT_ID = $this->config_get('socnetauth2_tinkoff_client_id'); 
		$this->CLIENT_SECRET = $this->config_get('socnetauth2_tinkoff_client_secret');  
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
		
		$state = str_replace("tinkoff_socnetauth2_", "", $STATE);
		
		return 'https://id.tinkoff.ru/auth/authorize?client_id='. $this->CLIENT_ID .
		'&redirect_uri='.urlencode($REDIRECT_URI) .
		'&state='.$state .
		'&response_type=code';
	}
	
	public function getRecordKey($REDIRECT_URI)
	{
		return "tinkoff_socnetauth2_".$this->request->get['state'];
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
		$state = $this->request->get['state'];
		$session_state = $this->request->get['session_state'];
		
		$POSTURL  = 'https://id.tinkoff.ru/auth/token';
		$POSTVARS = 'grant_type=authorization_code&redirect_uri='.urlencode($REDIRECT_URI).'&code='.$CODE;
		
	 
		if( $IS_DEBUG ) echo "M3: POSTVARS: ".$POSTVARS."<hr>";
		
		$ch = curl_init($POSTURL);
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    , $POSTVARS);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array('Authorization: Basic '.base64_encode($this->CLIENT_ID . ':' . $this->CLIENT_SECRET),
				'Content-Type: application/x-www-form-urlencoded'
			)
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$response = curl_exec($ch);
		curl_close($ch);
		
	 
		if( $IS_DEBUG ) echo "M4: ".$response."<hr>";
		
		$data = json_decode($response, true);
		
		/*
		$data = array(
			"access_token" => "t.mzucgRIDwalXXX_at2Y2kmJB6yhNAQlWMNucp3w45xMcKknxWyl_XXXXkp5_3Nq8i_UvddDroJvd3elz-QH5hQ",
			"token_type" => "Bearer",
			"expires_in" => 1791, // Время жизни токена в секундах
			"refresh_token" => "LShO9uuXXXXqozxO8WP2eGsJpXXXX-3QBGYUeNzUv4LTtjudU6zPofXbiXXXoznuCOLv6XXXCJn04fsLvsYH2Q"
		);
		
		{
			"access_token": "t.mzucgRIDwalXXX_at2Y2kmJB6yhNAQlWMNucp3w45xMcKknxWyl_XXXXkp5_3Nq8i_UvddDroJvd3elz-QH5hQ",
			"token_type": "Bearer",
			"expires_in": 1791, // Время жизни токена в секундах
			"refresh_token": "LShO9uuXXXXqozxO8WP2eGsJpXXXX-3QBGYUeNzUv4LTtjudU6zPofXbiXXXoznuCOLv6XXXCJn04fsLvsYH2Q"
		}
		*/ 
		if( !empty($data['access_token']) )
		{
			$graph_url = "https://id.tinkoff.ru/userinfo/userinfo";
				
			$POSTVARS = 'client_id='.$this->CLIENT_ID.'&client_secret='.$this->CLIENT_SECRET;
		
			$ch = curl_init($graph_url);
			curl_setopt($ch, CURLOPT_POST      ,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS    , $POSTVARS);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, 
				array('Authorization: Bearer '.$data['access_token'],
					'Content-Type: application/x-www-form-urlencoded'
				)
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
			$json = curl_exec($ch);
			curl_close($ch);
			
				
			if( $IS_DEBUG ) echo "M5: ".$json."<hr>"; 
			
			/*
			$json = '{"email": "tinkoff@mail.ru","family_name": "Иванов","birthdate": "2000-01-01","sub": "923d4812-148c-45v4-a56b-eed15cdd2857","name": "Иванов Олег","gender": "male","phone_number": "+79998887766","middle_name": "Юрьевич","given_name": "Олег"}';
			*/

			return array(
				"responce1" => $json,
				"responce2" => json_decode($json, TRUE)
			);
		}
		
	}
	
	public function getResultData($REQUISITES, $user_data)
	{
		$this->init($REQUISITES);
		
		/*
		{
		 "email": "tinkoff@mail.ru",
		 "family_name": "Иванов",
		 "birthdate": "2000-01-01",
		 "sub": "923d4812-148c-45v4-a56b-eed15cdd2857",
		 "name": "Иванов Олег",
		 "gender": "male"
		 "phone_number": "+79998887766",
		 "middle_name": "Юрьевич",
		 "given_name": "Олег",
		}
		*/

		$arr = array(
			'identity' => $user_data['responce2']['sub'],
			'firstname' => trim((isset($user_data['responce2']['given_name']) ? $user_data['responce2']['given_name'] : '').' '.(isset($user_data['responce2']['middle_name']) ? $user_data['responce2']['middle_name'] : '')),
			'lastname'  => isset($user_data['responce2']['family_name']) ? $user_data['responce2']['family_name'] : '',
			'email'     => isset($user_data['responce2']['email']) ? $user_data['responce2']['email'] : '',
			'telephone'	=> isset($user_data['responce2']['phone_number']) ? $user_data['responce2']['phone_number'] : '',
		);
		
		$data = array(
			'identity'  => $arr['identity'],
			'link' 		=> '',
			'firstname' => '',
			'lastname'  => '',
			'email'     => $arr['email'],
			'telephone'	=> $arr['telephone'],
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