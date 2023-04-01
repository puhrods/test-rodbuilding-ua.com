<?php
class ModelExtensionModuleSocnetauth2 extends Model 
{
	public $socnets = array(
		"vkontakte" => array(
			"key" => "vkontakte",
			"short" => "vk",
			"name" => "ВКонтакте",
			"loginza_key" => "vkontakte"
		),
		"odnoklassniki" => array(
			"key" => "odnoklassniki",
			"short" => "od",
			"name" => "Одноклассники",
			"loginza_key" => "odnoklassniki"
		),
		"facebook" => array(
			"key" => "facebook",
			"short" => "fb",
			"name" => "FaceBook",
			"loginza_key" => "facebook"
		),
		"twitter" => array(
			"key" => "twitter",
			"short" => "tw",
			"name" => "Twitter",
			"loginza_key" => "twitter"
		),
		"gmail" => array(
			"key" => "gmail",
			"short" => "gm",
			"name" => "Gmail.com",
			"loginza_key" => "google"
		),
		"mailru" => array(
			"key" => "mailru",
			"short" => "mr",
			"name" => "Mail.ru",
			"loginza_key" => "mailru"
		),
		"steam" => array(
			"key" => "steam",
			"short" => "st",
			"name" => "Steam",
			"loginza_key" => "steam"
		),
		"yandex" => array(
			"key" => "yandex",
			"short" => "ya",
			"name" => "Яндекс",
			"loginza_key" => "yandex"
		),
		"telegram" => array(
			"key" => "telegram",
			"short" => "tg",
			"name" => "Telegram",
			"loginza_key" => "telegram"
		),
		"tinkoff" => array(
			"key" => "tinkoff",
			"short" => "tk",
			"name" => "Tinkoff",
			"loginza_key" => "tinkoff"
		),
	);
	
	
	/* start 3004 */
	public function getProtokol( )
	{
		if( $this->config->get('module_socnetauth2_protokol') == 'detect' )
		{
			if( 
				( isset($this->request->server['SERVER_PORT']) && $this->request->server['SERVER_PORT'] == '443' ) || 
				!empty($this->request->server['HTTPS']) 
				|| ( !empty($this->request->server['HTTP_CF_VISITOR']) && strstr($this->request->server['HTTP_CF_VISITOR'], "https") )	
			)
				return 'https://';
			else
				return 'http://';
		}
		else
			return $this->config->get('module_socnetauth2_protokol'). '://'; 
	} 
		
	public function getShopFolder($type = 'url')
	{
		$socnetauth2_shop_folder = $this->config->get('module_socnetauth2_shop_folder');
		if( $socnetauth2_shop_folder ) $socnetauth2_shop_folder = '/'.$socnetauth2_shop_folder;
		
		if( $this->config->get('module_socnetauth2_shop_folders') )
		{
			$socnetauth2_shop_folders = $this->custom_unserialize( 
				$this->config->get('module_socnetauth2_shop_folders') 
			);
			
			if( !empty($socnetauth2_shop_folders[$this->config->get('config_store_id')]) )
				$socnetauth2_shop_folder = '/'.$socnetauth2_shop_folders[$this->config->get('config_store_id')];
			//else
			//	$socnetauth2_shop_folder = '';
		}
		
		if( $this->config->get('module_socnetauth2_shop_folder_in_'.$type) )
			return $socnetauth2_shop_folder;
	}
	
	public function setLoginCookies($customer_id)
	{
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "' ");
		
		$lifetime = time() + 60 * 60 * 24 * 365;
		$hash = md5($customer_query->row['password'] . $customer_query->row['salt']. $this->request->server['HTTP_USER_AGENT']);
		setcookie("customer_id", (int) $customer_query->row['customer_id'], $lifetime, '/');
		setcookie("hash", $hash, $lifetime, '/');
	}
	
	public function restoreCart($customer_id)
	{
		if( !$this->session->getId() )
			return;
		
		$check = $this->db->query("
			SELECT * FROM `" . DB_PREFIX . "cart` 
			WHERE session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY date_added DESC");
		
		if( empty($check->row['customer_id']) )
		{
			$this->db->query("UPDATE `" . DB_PREFIX . "cart` SET
					customer_id = '".(int)$customer_id."'
				WHERE session_id = '" . $this->db->escape($this->session->getId()) . "'");
		}
	} 
	
	
	public function finishAuth($provider, $data, $user_data, $CURRENT_URI, $IS_DEBUG)
	{
		$this->clearAuthCode();
		
		$CURRENT_URI = $this->prepareUrl($CURRENT_URI);
		
		$is_filter_passed = $this->isfilterPassed(); 
		
		if( $customer_id = $this->checkNew($data) ) // пользователь найден
		{
			if( $this->config->get('module_socnetauth2_dobortype') != 'every' )  // добор не нужен 
			{
				$this->session->data['customer_id'] = $customer_id; 
				$this->session->data['socnetauth2_confirmdata_show'] = 0; 
				
				$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
				
				if( $IS_DEBUG ) exit( "END-1 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
				
				return $CURRENT_URI;; 
			}
			else // добор нужен
			{
				$confirm_data = $this->isNeedConfirm( $data );
				
				if( !empty($confirm_data) && $is_filter_passed  ) // есть данные, добор не запрещен
				{
					//$data['customer_id'] = $customer_id;
					
					##$this->session->data['pre_customer_id'] = $customer_id;  
					
					$confirm_data['data'] = $data;
					
					$this->session->data['socnetauth2_confirmdata'] = serialize($confirm_data); 
					$this->session->data['socnetauth2_confirmdata_show'] = 1; 
					$this->session->data['socnetauth2_confirmdata_rawdata'] = serialize($user_data);  
					
					if( $IS_DEBUG ) exit( "END-2 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				else // нечего добирать или добор запрещен
				{
					$this->session->data['customer_id'] = $customer_id; 
					$this->session->data['socnetauth2_confirmdata'] = ''; 
					$this->session->data['socnetauth2_confirmdata_show'] = 0;  
					
					$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
					
					if( $IS_DEBUG ) exit( "END-3 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
			}
		}
		else // пользователь НЕ найден
		{
			$confirm_data = $this->isNeedConfirm( $data );
			
			if( !$this->config->get('module_socnetauth2_email_auth') || $this->config->get('module_socnetauth2_email_auth') == 'none' )  // даже при совпадении email нужно регать нового пользователя
			{
				if( $confirm_data && $is_filter_passed  ) // есть данные для добора и добор разрешен фильтром
				{
					$confirm_data['data'] = $data;
					
					$this->session->data['socnetauth2_confirmdata'] = serialize($confirm_data); 
					$this->session->data['socnetauth2_confirmdata_show'] = 1; 
					$this->session->data['socnetauth2_confirmdata_rawdata'] = serialize($user_data);   
					
					if( $IS_DEBUG ) exit( "END-4 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				else // нет данных для добора или добор запрещен фильтром
				{
					$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
					
					$this->session->data['socnetauth2_confirmdata'] = ''; 
					$this->session->data['socnetauth2_confirmdata_show'] = ''; 
				
					$customer_id = $this->addCustomer( $data );
					
					$this->session->data['customer_id'] = 1;  
					
					$this->addCustomerComment( $customer_id, $provider, $user_data );
					
					if( $IS_DEBUG ) exit( "END-5 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
			}
			elseif( $this->config->get('module_socnetauth2_email_auth') == 'confirm'  ) // нужно слать письмо с кодом.
			{
				// требуется добор данных
				if( $confirm_data && $is_filter_passed  )
				{
					$confirm_data['data'] = $data;
					
					$this->session->data['socnetauth2_confirmdata'] = serialize($confirm_data); 
					$this->session->data['socnetauth2_confirmdata_show'] = 1; 
					$this->session->data['socnetauth2_confirmdata_rawdata'] = serialize($user_data);   
					
					if( $IS_DEBUG ) exit( "END-6 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				// Получен E-mail и включено проверка email письмом
				elseif( !empty( $data['email'] ) && $this->checkByEmail($data, 0) )
				{
					$this->sendConfirmEmail($data);
					
					$this->session->data['socnetauth2_confirmdata'] = serialize(array(1, 2, 3, 4, $data['email'], $data['identity'], $data['link'], $data['provider'], $data)); 
					$this->session->data['socnetauth2_confirmdata_show'] = 1; 
					$this->session->data['controlled_email'] = $data['email']; 
					
					if( $IS_DEBUG ) exit( "END-7 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				//Получен e-mail и он уникальный
				elseif( empty( $data['email'] ) ||
					 ( !empty( $data['email'] ) && !$this->checkByEmail($data, 0) ) )
				{
					$this->session->data['socnetauth2_confirmdata'] = ''; 
					$this->session->data['socnetauth2_confirmdata_show'] = ''; 
					
					$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
				
					$customer_id = $this->addCustomer( $data );
					
					$this->session->data['customer_id'] = $customer_id; 
					
					$this->addCustomerComment( $customer_id, $provider, $user_data );
				
					
					if( $IS_DEBUG ) exit( "END-8 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI;  
				}
			}
			elseif( $this->config->get('module_socnetauth2_email_auth') == 'noconfirm'  )
			{
				// требуется добор данных
				if( $confirm_data && $is_filter_passed  )
				{
					$confirm_data['data'] = $data;
					
					$this->session->data['socnetauth2_confirmdata'] = serialize($confirm_data); 
					$this->session->data['socnetauth2_confirmdata_show'] = 1; 
					$this->session->data['socnetauth2_confirmdata_rawdata'] = serialize($user_data); 
					
					if( $IS_DEBUG ) exit( "END-9 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				// Получен E-mail 
				elseif( !empty( $data['email'] ) && $customer_id = $this->checkByEmail($data, 1) )
				{
					$this->session->data['socnetauth2_confirmdata'] = ''; 
					$this->session->data['socnetauth2_confirmdata_show'] = '';
					$this->session->data['customer_id'] = $customer_id; 
					
					$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
					
					if( $IS_DEBUG ) exit( "END-10 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}
				//Получен e-mail и он уникальный 
				elseif( empty( $data['email'] ) ||
					 ( !empty( $data['email'] ) && !$this->checkByEmail($data, 0) ) )
				{
					$this->session->data['socnetauth2_confirmdata'] = ''; 
					$this->session->data['socnetauth2_confirmdata_show'] = '';
					
					$CURRENT_URI = $this->getBackRedirectUrl($CURRENT_URI);
				
					$customer_id = $this->addCustomer( $data );
					
					$this->session->data['customer_id'] = $customer_id; 
					
					$this->addCustomerComment( $customer_id, $provider, $user_data );
					
					if( $IS_DEBUG ) exit( "END-11 ".$CURRENT_URI."<hr>Отладочные сообщения можно отключить во вкладке соответствующей соц.сети в настройках модуля");
					
					return $CURRENT_URI; 
				}	
			}
		}
		
		exit();
	}
	
	public function getRecord($state)
	{
		$result = $this->db->query("
			SELECT * FROM `" . DB_PREFIX . "socnetauth2_records` 
			WHERE state='".$this->db->escape($state)."'");
		
		return $result->row;
	}
	
	public function setRecord($state, $redirect)
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "socnetauth2_records` 
			WHERE DATE_ADD(cdate, INTERVAL 15 MINUTE)<NOW()");
			
		$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_records` 
			SET 
				`state` = '".$this->db->escape($state)."',
				`redirect` = '".$this->db->escape($redirect)."',
				`cdate` = NOW()");
	}
	
	public function checkByEmail($data, $is_add=0)
	{
		$result = $this->db->query("SELECT * 
									FROM `" . DB_PREFIX . "customer` 
									WHERE email='".$this->db->escape($data['email'])."'")->row;
		
		if( $result )
		{
			if( $is_add )
			{
				$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_customer2account`
								   SET 
								    `customer_id` = '".(int)$result['customer_id']."',
									`identity` = '".$this->db->escape($data['identity'])."',
									`link` = '".$this->db->escape($data['link'])."',
									`provider` = '".$this->db->escape($data['provider'])."',
									`data` = '".$this->db->escape($data['data'])."',
									`email` = '".$this->db->escape($data['email'])."'");
			}
			
			$this->addActivity($result['customer_id'], $data['identity'], 'login');
			
			return $result['customer_id'];
		}
		else
		{
			return false;
		}
	}
	
	private function addActivity($customer_id, $identity, $activity_key)
	{
		if($this->config->get('config_customer_activity') && !empty($customer_id) )
		{
			$query_customer = $this->db->query("SELECT * 
									   FROM `" . DB_PREFIX . "customer`
									   WHERE `customer_id`='".(int)$customer_id."'")->row;
				
			$query_account = $this->db->query("SELECT * FROM
					`" . DB_PREFIX . "socnetauth2_customer2account` 
					WHERE 
						identity = '" .$this->db->escape($identity) . "' AND
						`customer_id`='".(int)$customer_id."'
					")->row;
				
			$this->load->model('account/activity');

			$name = '';
			if( !empty($query_customer['firstname']) )
				$name .= $query_customer['firstname'];
			if( !empty($query_customer['lastname']) )
				$name .= ' '.$query_customer['lastname'];
			if( !empty( $query_account['provider'] ) )
				$name .= ' ('.strtoupper($this->socnets[ $query_account['provider'] ]['short']).')';
				 
			
			$name = trim($name);
					
			$activity_data = array(
					'customer_id' => $customer_id,
					'name'        => $name
			);

			$this->model_account_activity->addActivity($activity_key, $activity_data);
		}
	}
	
	public function isNeedConfirm($data)
	{
		$confirm_data = array();
		/* start 0102 */
		if( $this->config->get('module_socnetauth2_confirm_group_status') == 1 )
		{
			$confirm_data['group'] = isset($data['group']) ?  $data['group'] : '';
		}
		/* end 0102 */
		
		if( $this->config->get('module_socnetauth2_confirm_firstname_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_firstname_status') == 1 && empty($data['firstname'])
			) )
		{
			$confirm_data['firstname'] = $data['firstname'];
		}
		
		if( $this->config->get('module_socnetauth2_confirm_username_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_username_status') == 1 && empty($data['username'])
			) )
		{
			$confirm_data['username'] = isset($data['username']) ? $data['username'] : '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_lastname_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_lastname_status') == 1 && empty($data['lastname'])
		) )
		{
			$confirm_data['lastname'] = $data['lastname'];
		}
		
		if( $this->config->get('module_socnetauth2_confirm_email_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_email_status') == 1 && empty($data['email'])
			) )
		{
			$confirm_data['email'] = $data['email'];
		}
		
		if( $this->config->get('module_socnetauth2_confirm_telephone_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_telephone_status') == 1 && empty($data['telephone'])
		) )
		{
			$confirm_data['telephone'] = $data['telephone'];
		}
		
		/* kin insert metka: c1 */
		if( $this->config->get('module_socnetauth2_confirm_company_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_company_status') == 1 && empty($data['company'])
		) )
		{
			$confirm_data['company'] = '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_address_1_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_address_1_status') == 1 && empty($data['address_1'])
		) )
		{
			$confirm_data['address_1'] = '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_postcode_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_postcode_status') == 1 && empty($data['postcode'])
		) )
		{
			$confirm_data['postcode'] = '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_city_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_city_status') == 1 && empty($data['city'])
		) )
		{
			$confirm_data['city'] = '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_zone_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_zone_status') == 1 && empty($data['zone'])
		) )
		{
			$confirm_data['zone'] = '';
		}
		
		if( $this->config->get('module_socnetauth2_confirm_country_status') == 2 || (
			$this->config->get('module_socnetauth2_confirm_country_status') == 1 && empty($data['country'])
		) )
		{
			$confirm_data['country'] = '';
		}
		/* end kin metka: c1 */
		
		
		if( !$confirm_data )
		{	
			return false;
		}
		else
		{		
			return $confirm_data;
		}
	}
	
	public function getBackRedirectUrl( $redirect_url )
	{	
		if( !empty( $this->request->cookie['socnetauth2_from_page'] ) && 
			( $this->request->cookie['socnetauth2_from_page'] == 'account' || $this->request->cookie['socnetauth2_from_page'] == 'reg' || $this->request->cookie['socnetauth2_from_page'] == 'checkout' )
		)
		{
			if( $this->config->get('module_socnetauth2_redirect_url_'.$this->request->cookie['socnetauth2_from_page']) &&
				$this->config->get('module_socnetauth2_redirect_url_'.$this->request->cookie['socnetauth2_from_page']) != 'default' &&
				$this->config->get('module_socnetauth2_redirect_url_'.$this->request->cookie['socnetauth2_from_page'].'_set')
			)
			{
				$redirect_url = $this->config->get('module_socnetauth2_redirect_url_'.$this->request->cookie['socnetauth2_from_page'].'_set');  
			}
		}
		
		$redirect_url = htmlspecialchars_decode($redirect_url);
		 
		return $redirect_url;
	}
	
	public function isfilterPassed()
	{
		if( !empty( $this->request->cookie['socnetauth2_from_page'] ) && 
			( $this->request->cookie['socnetauth2_from_page'] == 'account' || $this->request->cookie['socnetauth2_from_page'] == 'reg' || $this->request->cookie['socnetauth2_from_page'] == 'checkout' )
			&&
			$this->config->has('module_socnetauth2_dobor_page_'.$this->request->cookie['socnetauth2_from_page']) &&
			!$this->config->get('module_socnetauth2_dobor_page_'.$this->request->cookie['socnetauth2_from_page'])
		)
		{
			return false;  
		}
		
		return true;
	}
	
	public function prepareUrl($url)
	{
		if( strstr($url, 'logout') )
		{
			$url = str_replace('logout', 'login', $url);
		}
		
		
		if( $this->config->get('module_socnetauth2_add_param') )
		{
			if( strstr($url, '?') )
				$url .= '&sna=1';
			else
				$url .= '?sna=1';
		}
		
		$url = str_replace("&amp;", "&", $url);
		
		return $url;
	}
	
	/* end 3004 */
	
	private function checkColumn($table, $column, $type)
	{
		$query = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX . $table."`");
		
		$hash = array();
		
		foreach($query->rows as $row)
		{
			$hash[ $row['Field'] ] = $row['Type'];
		}
		
		if( !isset($hash[ $column ]) )
		{
			$sql = "ALTER TABLE `" . DB_PREFIX . $table . "` 
			ADD `".$column."` ". $type ." NOT NULL";
			$this->db->query($sql);
		}
		elseif( strtoupper( $hash[ $column ] ) != strtoupper($type) )
		{
			if( strtoupper($type) == 'TEXT' &&  
				(
					strtoupper( $hash[ $column ] ) == 'LONGTEXT' ||
					strtoupper( $hash[ $column ] ) == 'MEDIUMTEXT' 
				)
			) 
			{
				// none
			}
			else
			{
				$this->db->query("ALTER TABLE  `" . DB_PREFIX . $table  . "` 
				CHANGE  `".$column."`  `".$column."` ".$type." NOT NULL");
			}
		}
	}
	
	/* staet 3004 */
	
	public function checkDB()
	{
	
		$res = $this->db->query("SHOW TABLES");
		$installed = 0;
		foreach($res->rows as $key=>$val)
		{
			foreach($val as $k=>$v)
			{
				if( $v == DB_PREFIX . 'socnetauth2_customer2account' )
				{
					$installed = 1;
				}
			}
		}
		
		if( $installed == 0 )
		{
			$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "socnetauth2_customer2account` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`customer_id` varchar(100) NOT NULL,
				`identity` varchar(300) NOT NULL,
				`link` varchar(300) NOT NULL,
				`provider` varchar(300) NOT NULL,
				`email` varchar(300) NOT NULL,
				`data` TEXT NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
				
			$this->db->query($sql);
			 
		}
		else
		{
			$todel = $this->db->query("SELECT sc.id, c.customer_id 
								  FROM `" . DB_PREFIX . "socnetauth2_customer2account` sc
								  LEFT JOIN `" . DB_PREFIX . "customer` c
								  ON sc.customer_id=c.customer_id
								  WHERE c.customer_id IS NULL")->rows;
			
			if( !empty($todel) )
			{
				foreach($todel as $item)
				{
					$this->db->query("DELETE FROM `" . DB_PREFIX . "socnetauth2_customer2account` 
								  WHERE id=".(int)$item['id'] );
				}
			}
			
		}
		
		$this->checkColumn("socnetauth2_customer2account", "mdate", "DATETIME");
		$this->checkColumn("socnetauth2_customer2account", "code", "VARCHAR(300)");
		 
		
		
		/* start 0205 */
		$this->load->model('localisation/language'); 
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach($this->socnets as $key=>$val)
		{
			foreach($languages as $lang)
			{
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` 
				WHERE `keyword` = 'sna-".$this->db->escape($key)."' AND language_id = ".(int)$lang['language_id']."");
				
				if( !$query->row  )
				{
					$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` 
						SET 
							language_id = ".(int)$lang['language_id'].",
							`query` = 'extension/module/socnetauth2/auth" . ucfirst($key) . "',
							`keyword` = 'sna-".$this->db->escape($key)."'");
				}
			}
		}
		/* end 0205 */
		
	}
	
	public function clearAuthCode()
	{
		$this->checkDB();
	/* end 3004 */
		
		$this->db->query("UPDATE `" . DB_PREFIX . "socnetauth2_customer2account` 
			SET 
				code = ''
			WHERE 
				DATE_ADD(mdate, INTERVAL 15 MINUTE)<NOW()");
	}
	
	public function getRequisites()
	{
		$hash = array(
			"socnetauth2_vkontakte_appid" => "",
			"socnetauth2_vkontakte_appsecret" => "",
			"socnetauth2_facebook_appid" => "",
			"socnetauth2_facebook_appsecret" => "",
			"socnetauth2_twitter_consumer_key" => "",
			"socnetauth2_twitter_consumer_secret" => "",
			"socnetauth2_odnoklassniki_application_id" => "",
			"socnetauth2_odnoklassniki_public_key" => "",
			"socnetauth2_odnoklassniki_secret_key" => "",
			"socnetauth2_gmail_client_id" => "",
			"socnetauth2_gmail_client_secret" => "",
			"socnetauth2_mailru_id" => "",
			"socnetauth2_mailru_private" => "",
			"socnetauth2_mailru_secret" => "",
			"socnetauth2_yandex_client_id" => "",
			"socnetauth2_yandex_client_secret" => "",
			"socnetauth2_steam_api_key" => "", 
			"socnetauth2_telegram_bot_username" => "",
			"socnetauth2_telegram_bot_token" => "",
			"socnetauth2_tinkoff_client_id" => "",
			"socnetauth2_tinkoff_client_secret" => "",
		);
		
		$domain = $this->getDomain( $this->request->server['HTTP_HOST'] );
		
		foreach($this->socnets as $key=>$val)
		{
			$req_list = $this->custom_unserialize( $this->config->get('module_socnetauth2_'.$key.'_req') );
			  
			foreach($hash as $config_key=>$value)
			{
				if( strstr($config_key, "socnetauth2_".$key) )
				{
					$param_key = str_replace("socnetauth2_".$key."_", "" , $config_key );
					
					$is_in = 0;
					if( !empty($req_list) )
					{
						foreach($req_list as $i=>$req)
						{
							if( $this->getDomain( $req['domain'] ) == $domain )
							{
								$is_in = 1;
								
								$hash[$config_key] = isset( $req[ $param_key ] ) ? $req[ $param_key ] : "";
							}
						} 
					}
					
					if( !$is_in )
					{
						$hash[$config_key] = $this->config->get('module_'.$config_key);
					}
				}
			}	
		}
		
		return $hash;
	}
	
	public function getDomain($domain)
	{
		$domain = preg_replace("/^http\:\/\//", "", $domain);
		$domain = preg_replace("/^https\:\/\//", "", $domain);
		#$domain = preg_replace("/^www\./", "", $domain);
		$domain = preg_replace("/\/$/", "", $domain);
		$domain = strtolower($domain);
		
		return $domain;
	}
	
	public function getCustomerIdByAuthCode($code)
	{
		$this->clearAuthCode();
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "socnetauth2_customer2account`
						  WHERE 
							`code` = '".$this->db->escape($code)."'");
		 
		return isset( $query->row['customer_id'] ) ? $query->row['customer_id'] : '';
	}
	
	public function checkNew($data)
	{
		if( empty($data['identity']) ) exit("EMPTY ID");
	
		$identitis = array();
		
		$identitis[] = $data['identity'];
		$identitis[] = str_replace("http://", "https://", $data['identity']);
		$identitis[] = str_replace("https://", "http://", $data['identity']);
		$identitis[] = str_replace("https://", "https://www.", $data['identity']);
		$identitis[] = str_replace("http://", "http://www.", $data['identity']);
		$identitis[] = str_replace("http://www.", "http://", $data['identity']);
		$identitis[] = str_replace("https://www.", "https://", $data['identity']);
		$identitis[] = str_replace("https://www.", "", $data['identity']);
		$identitis[] = str_replace("https://", "", $data['identity']);
		$identitis[] = str_replace("http://www.", "", $data['identity']);
		$identitis[] = str_replace("http://", "", $data['identity']);
		$identitis[] = str_replace("https://www.", "http://", $data['identity']);
		
		
		
		for($i=0; $i<count($identitis); $i++)
		{
			if( !empty($identitis[$i]) )
				$identitis[$i] = " sc.identity='".$this->db->escape($identitis[$i])."' ";
		}
		
		$wh = implode(" OR ", $identitis);
		
		$check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "socnetauth2_customer2account` sc
								   JOIN `" . DB_PREFIX . "customer` c
								   ON c.customer_id=sc.customer_id
								   WHERE ".$wh);
								   
		if( empty($check->rows) && $this->config->get('module_socnetauth2_dobortype') == 'one' )
		{
			return false;
		}
		elseif( !empty( $check->row ) )
		{
			$upd = '';
			
			if( !empty($data['firstname']) )
			{
				$upd .= " firstname = '".$this->db->escape($data['firstname'])."', ";
			}
			
			if( !empty($data['lastname']) )
			{
				$upd .= " lastname = '".$this->db->escape($data['lastname'])."', ";
			}
			
			if( !empty($data['telephone']) )
			{
				$upd .= " telephone = '".$this->db->escape($data['telephone'])."', ";
			}
			
			if( !empty($data['email']) )
			{
				$upd .= " email = '".$this->db->escape($data['email'])."', ";
			}
			
			/* start 2507 */
			if( isset($data['newsletter']) )
			{
				$upd .= " newsletter = '".(int)$data['newsletter']."', ";
			}
			/* end 2507 */
			 
			$this->db->query("UPDATE " . DB_PREFIX . "socnetauth2_customer2account 
							  SET
								data = '".$this->db->escape($data['data'])."'
							  WHERE
							    identity = '" .$this->db->escape($data['identity']) . "'");
			
			/* start 3004 */
			$customer_data = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer`
								   WHERE customer_id = '" .$this->db->escape($check->row['customer_id']) . "'");	
			/* end 3004 */

			
			if( $this->config->get('module_socnetauth2_save_to_addr')!='customer_only' &&
				( !empty( $data['city'] ) ||  !empty( $data['zone'] ) )
			)
			{
				$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "address 
								WHERE city = '" . $this->db->escape($data['city']) . "' AND 
									  zone_id = '" . (int)$data['zone'] . "' AND
									  country_id = '" . (int)$data['country'] . "' AND 
									  customer_id = '" .(int)$check->row['customer_id'] . "'");
					
				$address_id = 0;
					
				if( empty( $query2->row['address_id'] )  )
				{
					$this->db->query("INSERT INTO " . DB_PREFIX . "address 
						SET 
							customer_id = '" . (int)$check->row['customer_id'] . "', 
							firstname = '" . $this->db->escape($data['firstname']) . "', 
							lastname = '" . $this->db->escape($data['lastname']) . "', 
							company = '" . $this->db->escape($data['company']) . "', 
							address_1 = '" . $this->db->escape($data['address_1']) . "', 
							postcode = '" . $this->db->escape($data['postcode']) . "', 
							city = '" . $this->db->escape($data['city']) . "', 
							zone_id = '" . (int)$data['zone'] . "', 
							country_id = '" . (int)$data['country'] . "'");
				
					$address_id = $this->db->getLastId();
				}
				else
				{
					$address_id = $query2->row['address_id']; 
				}
									
				$this->db->query("UPDATE " . DB_PREFIX . "customer 
								  SET address_id = '" . (int)$address_id . "' 
								  WHERE customer_id = '" . (int)$check->row['customer_id'] . "'");
					
				if( isset( $this->session->data['simple']['shipping_address']['city'] ) )
				{
					$this->session->data['simple']['shipping_address']['city'] = $data['city'];
					$this->session->data['simple']['shipping_address']['zone_id'] = $data['zone'];
					$this->session->data['simple']['shipping_address']['country_id'] = $data['country'];
				}

				if( isset($this->session->data['guest']['shipping']['city']) )
				{
					$this->session->data['guest']['shipping']['city'] = $data['city'];
					$this->session->data['guest']['shipping']['zone_id'] = $data['zone'];
					$this->session->data['guest']['shipping']['country_id'] = $data['country'];
				}

				if( isset($this->session->data['guest']['payment']['city']) )
				{
					$this->session->data['guest']['payment']['city'] = $data['city'];
					$this->session->data['guest']['payment']['zone_id'] = $data['zone'];
					$this->session->data['guest']['payment']['country_id'] = $data['country'];
				}

				if( isset($this->session->data['payment_address']['city']) )
				{
					$this->session->data['payment_address']['city'] = $data['city'];
					$this->session->data['payment_address']['zone_id'] = $data['zone'];
					$this->session->data['payment_address']['country_id'] = $data['country'];
				}
					 
				if( isset($this->session->data['shipping_address']['city']) )
				{
					$this->session->data['shipping_address']['city'] = $data['city'];
					$this->session->data['shipping_address']['zone_id'] = $data['zone'];
					$this->session->data['shipping_address']['country_id'] = $data['country'];
				}
					   
					 
				if( isset($this->session->data['shipping_country_id']) )
				{
					$this->session->data['shipping_country_id'] = $data['country'];
				}
					
				if( isset($this->session->data['payment_country_id']) )
				{
					$this->session->data['payment_country_id'] = $data['country'];
				}
					
				if( isset($this->session->data['shipping_zone_id']) )
				{
					$this->session->data['shipping_zone_id'] = $data['zone'];
				}
					
				if( isset($this->session->data['payment_zone_id']) )
				{
					$this->session->data['payment_zone_id'] = $data['zone'];
				}
					
				if( isset($this->session->data['shipping_city']) )
				{
					$this->session->data['shipping_city'] = $data['city'];
				} 
					
				if( isset($this->session->data['payment_city']) )
				{
					$this->session->data['payment_city'] = $data['city'];
				} 
			}
			/* start specific block: system/library/customer.php */
			if( !empty($customer_data->row['cart']) && is_string($customer_data->row['cart']) ) {
				$cart = unserialize($customer_data->row['cart']);
				
				foreach ($cart as $key => $value) {
					if (!array_key_exists($key, $this->session->data['cart'])) {
						$this->session->data['cart'][$key] = $value;
					} else {
						$this->session->data['cart'][$key] += $value;
					}
				}			
			}

			if ( !empty($customer_data->row['wishlist']) && is_string($customer_data->row['wishlist'])) {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}
								
				$wishlist = unserialize($customer_data->row['wishlist']);
			
				foreach ($wishlist as $product_id) {
					if (!in_array($product_id, $this->session->data['wishlist'])) {
						$this->session->data['wishlist'][] = $product_id;
					}
				}			
			}
			/* end specific block */

			
			return $customer_data->row['customer_id'];
		}
		else
		{
			return false;
		}
	}

	public function addCustomerComment($customer_id, $provider, $data)
	{
		$comment_ar = array();
		foreach($data as $key=>$val)
		{
			if( !is_array($val) && strlen($val) > 150 )
				$val = substr($val, 0, 150).'...';
			elseif( is_array($val) )
				$val = substr(json_encode($val), 0, 150).'...';
			
			$comment_ar[] = $key.': '.$val;
		}
		$comment = strtoupper($provider).":<br>";
		$comment .= implode("<br>", $comment_ar); 
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_history` 
								SET
								 date_added = NOW(),
								 comment = '".$this->db->escape($comment )."',
								 customer_id = '".(int)$customer_id."'"); 
	}
	
	public function addCustomerAfterConfirm($data)
	{
		$data = $this->prepareData($data);
		$query = $this->db->query("SELECT * 
									   FROM `" . DB_PREFIX . "customer`
									   WHERE `email`='".$this->db->escape($data['email'])."'");
			
		if( !empty($query->row) )
		{
			$query_account = $this->db->query("SELECT * FROM
					`" . DB_PREFIX . "socnetauth2_customer2account` 
					WHERE 
						identity = '" .$this->db->escape($data['identity']) . "' AND
						provider = '".$this->db->escape($data['provider']) ."' AND 
						`customer_id`='".(int)$query->row['customer_id']."'
					")->row;
					
			if( !$query_account )	
			{
				$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_customer2account` 
								SET
								 identity = '" .$this->db->escape($data['identity']) . "',
								 provider = '".$this->db->escape($data['provider']) ."',
								 data = '".$this->db->escape($data['data'])."',
								 link = '".$this->db->escape($data['link'])."',
								 email = '".$this->db->escape($data['email'])."',
								 customer_id = '".(int)$query->row['customer_id']."'");
			}
			else
			{
				$this->db->query("UPDATE `" . DB_PREFIX . "socnetauth2_customer2account` 
								SET
									`data` = '".$this->db->escape($data['data'])."',
									`link` = '".$this->db->escape($data['link'])."'
								WHERE 
									identity = '" .$this->db->escape($data['identity']) . "' AND
									provider = '".$this->db->escape($data['provider']) ."' AND 
									`customer_id`='".(int)$query->row['customer_id']."'");
			}
			
			$data['customer_id'] = $query->row['customer_id'];
			
			$upd_ar_customer = array();
			$upd_ar_address = array();
			
			if( !empty($data['firstname']) || !empty($data['lastname']) )
			{
				$upd_ar_customer[] = "firstname = '".$this->db->escape($data['firstname'])."',";
				$upd_ar_address[]  = "firstname = '".$this->db->escape($data['firstname'])."',";
				
				$upd_ar_customer[] = "lastname = '".$this->db->escape($data['lastname'])."',";
				$upd_ar_address[]  = "lastname = '".$this->db->escape($data['lastname'])."',";
			}
			
			if( !empty($data['email']) )
				$upd_ar_customer[] = "email = '".$this->db->escape($data['email'])."',";
			
			if( !empty($data['newsletter']) )
				$upd_ar_customer[] = "newsletter = '".$this->db->escape($data['newsletter'])."',";
			
			if( !empty($data['telephone']) )
				$upd_ar_customer[] = "telephone = '".$this->db->escape($data['telephone'])."',";
			
			$this->db->query("UPDATE " . DB_PREFIX . "customer 
							  SET
								".implode(" ", $upd_ar_customer)."
							    ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
							  WHERE 
								customer_id='".(int)$data['customer_id']."'");					 
				
			if( $this->config->get('module_socnetauth2_save_to_addr')!='customer_only' )
			{
				$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer 
											   WHERE customer_id='".(int)$data['customer_id']."'");
				
				if( !empty( $query2->row['address_id'] ) )
				{
					$this->db->query("UPDATE " . DB_PREFIX . "address 
							SET  
								".implode(" ", $upd_ar_address)."
								company = '" . $this->db->escape($data['company']) . "', 
								address_1 = '" . $this->db->escape($data['address_1']) . "', 
								postcode = '" . $this->db->escape($data['postcode']) . "', 
								city = '" . $this->db->escape($data['city']) . "', 
								zone_id = '" . (int)$data['zone'] . "', 
								country_id = '" . (int)$data['country'] . "'
							WHERE
								address_id = '" . (int)$query2->row['address_id'] . "'");
				}
				else
				{
					$this->db->query("INSERT INTO " . DB_PREFIX . "address 
					SET 
					customer_id = '" . (int)$data['customer_id'] . "', 
					firstname = '" . $this->db->escape($data['firstname']) . "', 
					lastname = '" . $this->db->escape($data['lastname']) . "', 
					company = '" . $this->db->escape($data['company']) . "', 
					address_1 = '" . $this->db->escape($data['address_1']) . "', 
					postcode = '" . $this->db->escape($data['postcode']) . "', 
					city = '" . $this->db->escape($data['city']) . "', 
					zone_id = '" . (int)$data['zone'] . "', 
					country_id = '" . (int)$data['country'] . "'");
			
					$address_id = $this->db->getLastId();
			
					$this->db->query("UPDATE " . DB_PREFIX . "customer 
							  SET address_id = '" . (int)$address_id . "' 
							  WHERE customer_id = '" . (int)$data['customer_id'] . "'");
					
				}
			}
			
			/* start 3004 */
			if( !empty($data['customer_id']) )
			{
				$this->addActivity($data['customer_id'], $data['identity'], 'login');
			}
			/* end 2803 */
		}
		
		return $query->row['customer_id'];
	}
	
	public function prepareData($data)
	{
		$fields = array("firstname", "lastname", "email", "telephone", "company", "postcode",
		"country", "zone", "city", "address_1", "address_2", "link", "group" );
		
		foreach($fields as $field)
		{
			if( !isset($data[$field]) )
			{
				$data[$field] = '';
			}
			
			$data[$field] = trim($data[$field]);
		}
		
		$data['password'] = '';
		if( $this->config->get('module_socnetauth2_addpass') || version_compare(VERSION, '3.0.0.0') >= 0 )
		{
			$data['password'] = substr( md5(  rand() ), 0, 8 );
		}
			
		if( !isset($data['newsletter']) )
			$data['newsletter'] = 0;
				
		$shipping_address = array();
		
		if( !empty( $this->session->data['simple']['shipping_address'] ) )
		{
			$shipping_address = $this->session->data['simple']['shipping_address'];
		}
		elseif( !empty( $this->session->data['simple']['payment_address'] ) )
		{
			$shipping_address = $this->session->data['simple']['payment_address'];
		}
		elseif( !empty( $this->session->data['shipping_address'] ) )
		{
			$shipping_address = $this->session->data['shipping_address'];
		}
		elseif( !empty( $this->session->data['payment_address'] ) )
		{
			$shipping_address = $this->session->data['payment_address'];
		}
		elseif( !empty($this->session->data['prmn.city_manager']) )
		{
			$shipping_address = $this->session->data['prmn.city_manager'];
		}
		
		if( !empty($shipping_address['country_id']) )
		{
			$data['country'] = $shipping_address['country_id'];
		}
		
		if( !empty($shipping_address['postcode']) )
		{
			$data['postcode'] = $shipping_address['postcode'];
		}
		
		if( !empty($shipping_address['city']) )
		{
			$data['city'] = $shipping_address['city'];
		}
		
		if( !empty($shipping_address['zone_id']) )
		{
			$data['zone'] = $shipping_address['zone_id'];
		}
		
		if( !empty($data['data']) ) 
		{
			$data['data'] = preg_replace("/[\\\]+\'/", "'", $data['data']);			
		}
		
		$data['group'] = isset($data['group']) ? (int)$data['group'] : 0;
		
		if( empty($data['group']) && $this->config->get('module_socnetauth2_'.$data['provider'].'_customer_group_id') )
		{
			$data['group'] = $this->config->get('module_socnetauth2_'.$data['provider'].'_customer_group_id');
		}
		
		if( empty($data['group']) && $this->config->get('config_customer_group_id') )
		{
			$data['group'] = $this->config->get('config_customer_group_id');
		}
		
		return $data;
	}
	
	public function addCustomer($data)
	{
		
/*
	Op 3
	addCustomer
	
	customer_group_id
	firstname
	lastname
	email
	telephone
	custom_field (optional)
	password
	newsletter (optional)
	
	addAddress
	firstname
	lastname
	company (optional)
	company_id (optional)
	tax_id (optional)
	address_1
	address_2
	postcode
	city
	zone_id
	country_id
	
	
	Op 2.3, 2.2, 2.1, 2.0, 1.5
	addCustomer
	customer_group_id
	firstname
	lastname
	email
	telephone
	fax
	custom_field (optional)
	password
	newsletter (optional)
	company
	address_1
	address_2
	postcode
	city
	zone_id
	country_id
		
		
*/	
		/* start 0605 */
		
		$data = $this->prepareData($data);
		
		$is_empty_email = empty($data['email']) ? 1 : 0;
		
		$customer_add_data = array(
			"customer_group_id" => $data['group'],
			"firstname" => $data['firstname'],
			"lastname" => $data['lastname'],
			"email" => $data['email'] ? $data['email'] : 
				( 
					$this->config->get('module_socnetauth2_default_email') ? $this->config->get('module_socnetauth2_default_email') : 
					$this->config->get('config_email')
				),
			"telephone" => $data['telephone'],
			"custom_field" => array(),
			"password" => $data['password'],
			"newsletter" => (int)$data['newsletter'],
			"company" => $data['company'],
			"company_id" => 0,
			"tax_id" => 0,
			"username" => isset($data['username']) ? $data['username'] : '',
			"fax" => '',
			"address_1" => $data['address_1'],
			"address_2" => $data['address_2'],
			"postcode" => $data['postcode'],
			"city" => $data['city'],
			"zone_id" => $data['zone'],
			"country_id" => !empty( $data['country'] ) ? (int)$data['country'] : (int)$this->config->get('module_socnetauth2_default_country'),
			'is_sna' => 1
		);
		
		if( $this->config->get('module_socnetauth2_save_fullname_to_firstname') )
		{
			$customer_add_data['firstname'] = $customer_add_data['firstname'].' '.$customer_add_data['lastname'];
			$customer_add_data['lastname'] = '';
		}
		// --------
		
		$this->load->model('account/customer');
		$this->load->model('account/address');
		
		$customer_id = 0;
		$is_customer_added = 0;
		$activity_key = '';
		
		if( $customer_id = $this->checkNew($data) )
		{
			$this->addActivity($customer_id, $data['identity'], 'login'); 
			return $customer_id;
		}
		elseif( $this->config->get('module_socnetauth2_email_auth') == 'noconfirm' && 
			!empty( $data['email'] ) )
		{
			$query = $this->db->query("SELECT * 
									   FROM `" . DB_PREFIX . "customer`
									   WHERE `email`='".$this->db->escape($data['email'])."'");
			
			if( !empty($query->row) )
			{
				$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_customer2account` 
								SET
								 identity = '" .$this->db->escape($data['identity']) . "',
								 provider = '".$this->db->escape($data['provider']) ."',
								 data = '".$this->db->escape($data['data'])."',
								 link = '".$this->db->escape($data['link'])."',
								 email = '".$this->db->escape($data['email'])."',
								 customer_id = '".(int)$query->row['customer_id']."'");
					
				$customer_id = $query->row['customer_id'];
				
				$this->addActivity($data['customer_id'], $data['identity'], 'login'); 
			}
			else
			{
				$customer_id = $this->model_account_customer->addCustomer($customer_add_data);
				$is_customer_added = 1;
				
				if( !$customer_id )
					$customer_id = $this->db->getLastId();
				
				if( $customer_id && version_compare(VERSION, '3.0.0.0') >= 0 )
				{
					$this->customer->login($customer_add_data['email'], $customer_add_data['password'] );
				}
				
				if( $is_empty_email && $customer_id )
				{
					$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET email = ''
										  WHERE customer_id = ".(int)$customer_id );
				}
				
				$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_customer2account` 
							  SET
								 identity = '" .$this->db->escape($data['identity']) . "',
								 provider = '".$this->db->escape($data['provider']) ."',
								 data = '".$this->db->escape($data['data'])."',
								 link = '".$this->db->escape($data['link'])."',
								 email = '".$this->db->escape($data['email'])."',
								 customer_id = '".(int)$customer_id."'"); 
			}
		}
		else
		{
			$customer_id = $this->model_account_customer->addCustomer($customer_add_data);
			$is_customer_added = 1;
				
			if( !$customer_id )
				$customer_id = $this->db->getLastId();
			
			if( $customer_id && version_compare(VERSION, '3.0.0.0') >= 0 )
			{
				$this->customer->login($customer_add_data['email'], $customer_add_data['password'] );
			}
				
			if( $is_empty_email && $customer_id )
			{
				$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET email = ''
									  WHERE customer_id = ".(int)$customer_id );
			}
			
			
			$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_customer2account` 
							  SET
								 identity = '" .$this->db->escape($data['identity']) . "',
								 provider = '".$this->db->escape($data['provider']) ."',
								 data = '".$this->db->escape($data['data'])."',
								 link = '".$this->db->escape($data['link'])."',
								 email = '".$this->db->escape($data['email'])."',
								 customer_id = '".(int)$customer_id."'");
		}
		
		if( $customer_id )
		{
			if( $this->config->get('module_socnetauth2_save_to_addr') == 'customer_only' )
			{
				if( $is_customer_added )
				{
					$query = $this->db->query("SELECT * 
											   FROM `" . DB_PREFIX . "customer`
											   WHERE `customer_id`='".(int)$customer_id."'");
					
					if( !empty($query->row['address_id']) )
					{
						$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET address_id = 0 WHERE customer_id = '".(int)$customer_id."'");
						$this->db->query("DELETE FROM `" . DB_PREFIX . "address` WHERE address_id = '".(int)$query->row['address_id']."'");
					}
				}
			}
			else
			{
				if( version_compare(VERSION, '3.0.0.0') >= 0 )
				{
					$customer_add_data['default'] = 1;
					$this->model_account_address->addAddress($customer_id, $customer_add_data);
				}
			}
		}
		
		return $customer_id;
	}
	
	public function checkUniqEmail($email)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE email='".$this->db->escape($email)."'");
		
		if( $query->row ) 
			return false;
		else 
			return true;
	}
	
	public function getOldDoborData($loginza_data)
	{
		$RES = array(
			"firstname" => "", 
			"lastname" => "", 
			"email" => "", 
			"telephone" => "",
		
			/* start 2505 */
			"newsletter" => "",
			/* end 2505 */
			
			/* start 0102 */
			"group" => "",
			/* end 0102 */
			"company" => "", 
			"address_1" => "", 
			"postcode" => "", 
			"city" => "", 
			"zone" => "", 
			"country" => ""
		);
		
		
		/* start 1007 */ 
		
		$dobor_customer_id = 0;
		
		if( !empty( $loginza_data['data']['customer_id'] ) )
		{
			$dobor_customer_id = $loginza_data['data']['customer_id'];
		}
		else 
		{
			$query = $this->db->query("SELECT * 
								   FROM `" . DB_PREFIX . "customer` c 
								   JOIN `" . DB_PREFIX . "socnetauth2_customer2account` sc
								   ON c.customer_id=sc.customer_id
								   WHERE 
									sc.identity='".$this->db->escape($loginza_data['data']['identity'])."'
								");
			if( !empty( $query->row['customer_id'] ) )
				$dobor_customer_id = $query->row['customer_id'];
			elseif( $this->config->get('module_socnetauth2_email_auth') == 'noconfirm' 
					&& !empty($loginza_data['data']['email'])
			)
			{
				$query = $this->db->query("SELECT * 
								   FROM `" . DB_PREFIX . "customer` c  
								   WHERE 
									c.email ='".$this->db->escape($loginza_data['data']['email'])."'
								");
				if( !empty( $query->row['customer_id'] ) )
					$dobor_customer_id = $query->row['customer_id'];
			}
		}
		 
		if( empty($dobor_customer_id) ) return;
		 
		$query = $this->db->query("SELECT * 
								   FROM `" . DB_PREFIX . "customer` c  
								   WHERE 
									c.customer_id = '".(int)$dobor_customer_id."'");
		
		if( empty($query->row) ) return;
		$RES['dobor_customer_id'] = $dobor_customer_id;
		/* end 1007 */ 
		
		$RES['telephone'] = $query->row['telephone'];
		$RES['email'] = $query->row['email'];
		$RES['firstname'] = $query->row['firstname'];
		$RES['lastname'] = $query->row['lastname'];
		/* start 0102 */
		$RES['group'] = $query->row['customer_group_id'];
		/* end 0102 */
		
		if( !empty($query->row['address_id']) )
		{
			$query_address = $this->db->query("SELECT * 
								   FROM `" . DB_PREFIX . "address` 
								   WHERE 
									address_id='".(int)$query->row['address_id']."'
								");
			
			if( !empty($query_address->row) )
			{
				
				$RES['company'] = $query_address->row['company'];
				$RES['address_1'] = $query_address->row['address_1'];
				$RES['postcode'] = $query_address->row['postcode'];
				
				$RES['city'] = $query_address->row['city'];
				$RES['zone'] = $query_address->row['zone_id'];
				$RES['country'] = $query_address->row['country_id'];
			}
		}
		
		return $RES;
	}
	
	
	public function sendConfirmEmail($data)
	{
		$res = $this->db->query("SHOW TABLES");
		$installed = 0;
		foreach($res->rows as $key=>$val)
		{
			foreach($val as $k=>$v)
			{
				if( $v == DB_PREFIX . 'socnetauth2_precode' )
				{
					$installed = 1;
				}
			}
		}
		
		if( $installed == 0 )
		{		
			$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "socnetauth2_precode` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`identity` varchar(300) NOT NULL,
				`code` varchar(300) NOT NULL,
				`cdate` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			$this->db->query($sql);
			
		}
				
		$code = substr(rand(0, 100000), 0, 7);
		$this->db->query("INSERT INTO `" . DB_PREFIX . "socnetauth2_precode`
						  SET 
							`identity` = '".$this->db->escape($data['identity'])."',
							`code` = '".$this->db->escape($code)."',
							`cdate`=NOW()");
		
		
		$this->language->load('extension/module/socnetauth2');
		$subject = $this->language->get('text_mail_subject');
		$message = $this->language->get('text_mail_body');
		$message = str_replace("%", $code, $message);
		
		$mail = new Mail();
		
		
		
		
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			
		$mail->setTo($data['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($message);
		$mail->send();
		
		return $code;
	}
	
	public function checkConfirmCode($identity, $code)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "socnetauth2_precode` 
								   WHERE identity='".$this->db->escape($identity)."' 
								   AND code='".$this->db->escape($code)."'");
		
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "socnetauth2_precode` 
						  WHERE DATE_ADD(cdate, INTERVAL 1 DAY) < NOW() ");
		
		if( $query->row ) 
			return true;
		else 
			return false;
	}
	
	/* start 1505 */
	public function custom_unserialize($s)
	{
		if( is_array($s) ) return $s;
	
		if(
			stristr($s, '{' ) != false &&
			stristr($s, '}' ) != false &&
			stristr($s, ';' ) != false &&
			stristr($s, ':' ) != false
		){
			return unserialize($s);
		}else{
			return $s;
		}

	}
	
	public function getConfigPostValue($key)
	{
		return $this->config->get("module_".$key);
	}
	
	public function checkIsSocnetAvailable($key)
	{
		if( !$this->getConfigPostValue('socnetauth2_'.$key.'_status') )
			return false;
		
		if( $this->getConfigPostValue('socnetauth2_'.$key.'_agent') == 'loginza' )
			return true;
		
		if( $key == 'vkontakte' )
		{
			if( $this->getConfigPostValue('socnetauth2_vkontakte_appid') && 
				$this->getConfigPostValue('socnetauth2_vkontakte_appsecret')
			)
			{
				return true;
			}
		}
		elseif( $key == 'facebook' )
		{
			if( $this->getConfigPostValue('socnetauth2_facebook_appid') && 
				$this->getConfigPostValue('socnetauth2_facebook_appsecret')
			)
			{
				return true;
			}
		}
		elseif( $key == 'twitter' )
		{
			if( $this->getConfigPostValue('socnetauth2_twitter_consumer_key') && 
				$this->getConfigPostValue('socnetauth2_twitter_consumer_secret')
			)
			{
				return true;
			}
		}
		elseif( $key == 'odnoklassniki' )
		{
			if( $this->getConfigPostValue('socnetauth2_odnoklassniki_application_id') && 
				$this->getConfigPostValue('socnetauth2_odnoklassniki_public_key') && 
				$this->getConfigPostValue('socnetauth2_odnoklassniki_secret_key')
			)
			{
				return true;
			}
		}
		elseif( $key == 'gmail' )
		{
			if( $this->getConfigPostValue('socnetauth2_gmail_client_id') && 
				$this->getConfigPostValue('socnetauth2_gmail_client_secret')
			)
			{
				return true;
			}
		}
		elseif( $key == 'mailru' )
		{
			if( $this->getConfigPostValue('socnetauth2_mailru_id') && 
				//$this->getConfigPostValue('socnetauth2_mailru_private') && 
				$this->getConfigPostValue('socnetauth2_mailru_secret')
			)
			{
				return true;
			}
		}
		elseif( $key == 'yandex' )
		{
			if( $this->getConfigPostValue('socnetauth2_yandex_client_id') && 
				$this->getConfigPostValue('socnetauth2_yandex_client_secret') && 
				(
					$this->getConfigPostValue('socnetauth2_yandex_rights_email') ||
					$this->getConfigPostValue('socnetauth2_yandex_rights_info')
				)
			)
			{
				return true;
			}
		}
		elseif( $key == 'steam' )
		{
			if( $this->getConfigPostValue('socnetauth2_steam_api_key') )
			{
				return true;
			}
		}
		elseif( $key == 'telegram' )
		{
			if( $this->getConfigPostValue('socnetauth2_telegram_bot_username') &&
				$this->getConfigPostValue('socnetauth2_telegram_bot_token')  
			)
			{
				return true;
			}
		}
		elseif( $key == 'tinkoff' )
		{
			if( $this->getConfigPostValue('socnetauth2_tinkoff_client_id') &&
				$this->getConfigPostValue('socnetauth2_tinkoff_client_secret')  
			)
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function setStartCookie($SOCNET, $page)
	{
		$STATE = $SOCNET.'_socnetauth2_'.rand(); 
		$CURRENT_URI = !empty($this->request->server['REQUEST_URI']) ? $this->request->server['REQUEST_URI'] : '/';
		
		if( strstr($CURRENT_URI, 'logout') )
		{
			$CURRENT_URI = preg_replace("/index\.php\?route\=account\/logout$/", 
											"", $CURRENT_URI);
				
			$CURRENT_URI = str_replace("logout", "", $CURRENT_URI);
			$CURRENT_URI = preg_replace("/\/+$/", "/", $CURRENT_URI);
		}
		elseif( strstr($CURRENT_URI, 'checkout/login') )
		{
			$CURRENT_URI = str_replace('checkout/login', 'checkout/checkout', $CURRENT_URI);
		}
		
		$CURRENT_URI = str_replace("?socnetauth2close=1", "", $CURRENT_URI);
		$CURRENT_URI = str_replace("&socnetauth2close=1", "", $CURRENT_URI);
		
		setcookie("socnetauth2_from_page", $page, time()+3600, "/" );
		
		setcookie($SOCNET."_state", $STATE, time()+3600, "/" );
		
		$this->setRecord($STATE, $CURRENT_URI);
	}
	
	public function getLoginzaLangCode($code)
	{
		$lang_hash = array(
				"ru"=>"ru",
				"uk"=>"uk",
				"ua"=>"uk",
				"be"=>"be",
				"fr"=>"fr",
				"en"=>"en"
		);
			
		if( isset($lang_hash[ $code ]) )
		{
			return $lang_hash[ $code ];
		}
		elseif( isset($lang_hash[ $this->config->get('config_language') ]) )
		{
			return $lang_hash[ $this->config->get('config_language') ];
		}
		else
		{
			return 'ru';
		}
	}
	
	public function getSocnets()
	{
		$socnetauth2_socnets_sort_order = $this->custom_unserialize( 
			$this->config->get('module_socnetauth2_socnets_sort_order')
		);
		
		$result = array();
		foreach( $this->socnets as $key=>$socnet )
		{
			$socnet['sort'] = isset( $socnetauth2_socnets_sort_order[$key] ) ? 
					$socnetauth2_socnets_sort_order[$key] : 100;
					
			$result[] = $socnet;
		}
		
		usort($result, array($this, "cmp"));
		
		return $result;
	}
	
	
	protected function cmp($a, $b)
	{
		if($a['sort'] == $b['sort']) {
			return 0;
		}
	
		return ($a['sort'] < $b['sort']) ? -1 : 1;
	}
	
	
	public function getEnabledSocnets()
	{
		$socnetauth2_shop_folder = '';
		
		
		if( $this->config->get('module_socnetauth2_shop_folder') ) 
			$socnetauth2_shop_folder = '/'.$this->config->get('module_socnetauth2_shop_folder');
		
		if( $this->config->get('module_ocnetauth2_shop_folders') && 
			$this->config->get('config_store_id') )
		{
			$socnetauth2_shop_folders = $this->custom_unserialize( $this->config->get('module_socnetauth2_shop_folders') );
			if( !empty($socnetauth2_shop_folders[$this->config->get('config_store_id')]) )
				$socnetauth2_shop_folder = '/'.$socnetauth2_shop_folders[$this->config->get('config_store_id')];
			else
				$socnetauth2_shop_folder = '';
		}
					
		$http = 'http://';
		if( $this->config->get('module_socnetauth2_protokol') == 'detect' )
		{
			if( 
				( isset($this->request->server['SERVER_PORT']) && $this->request->server['SERVER_PORT'] == '443' ) || 
				!empty($this->request->server['HTTPS']) 
				|| ( !empty($this->request->server['HTTP_CF_VISITOR']) && strstr($this->request->server['HTTP_CF_VISITOR'], "https") )	
			)
				$http = 'https://';
			else
				$http = 'http://';
		}
		else
			$http = $this->config->get('module_socnetauth2_protokol'). '://';
		 
		$redirect_url = '';
		
		if( $this->config->get('module_socnetauth2_shop_folder_in_redirect') )
			$redirect_url = $http.
						$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.
						'/sna-loginza';
		else
			$redirect_url = $http.
						$this->request->server['HTTP_HOST'].
						'/sna-loginza'; 
						
		$redirect_url = urlencode($redirect_url);
		
		$lang = $this->getLoginzaLangCode( $this->config->get('config_language') );
		
		$result = array();
		$socnets = $this->getSocnets();
		
		foreach( $socnets as $socnet )
		{
			$key = $socnet['key'];
			if( $this->checkIsSocnetAvailable($key) )
			{
				$link = '';
				if( $this->getConfigPostValue('socnetauth2_'.$key.'_agent') != 'loginza' )
				{
					if( $this->config->get('module_socnetauth2_shop_folder_in_url') ) 
						$link = $socnetauth2_shop_folder.'/sna-'.$key.'?first=1';
					else
						$link = '/sna-'.$key.'?first=1';
				}
				else
				{
					$link = "https://loginza.ru/api/widget?token_url=".$redirect_url."&provider=".
					$socnet['loginza_key']."&lang=".$lang."&providers_set=vkontakte,facebook,twitter,odnoklassniki,google,mailru,steam,yandex";
				}
				
				$socnet['link'] = $link;
				
				$result[] = $socnet;
			}
		}
		
		return $result;
	}
	
	public function getCountEnabledSocnets()
	{
		$count = 0;
		foreach( $this->socnets as $key=>$socnet )
		{
			if( $this->checkIsSocnetAvailable($key) )
			{
				$count++;
			}
		}
		
		return $count;
	}
	/* end 1505 */
	
	private function formatData($text, $data)
	{
		if( is_array($text) )
		{
			$text = $text[ $this->config->get('config_language_id') ];
		}
		else
		{
			$text = $this->custom_unserialize( $this->config->get('module_'.$text) );
			$text = $text[ $this->config->get('config_language_id') ];
		}
		
		if( !empty($text) )
		{
			foreach($data as $key=>$val)
			{
				$text = str_replace("{".$key."}", $val, $text);
			}
		}
		
		return $text;
	}
	
	public function getNewCustomerMessage($customer_id, $system_subject, $system_message, $data)
	{
		$subject = '';
		$message = '';
		
		if( $this->config->get('module_socnetauth2_newcustomer_mail_type') != 'custom' )
		{
			return array($system_subject, $system_message);
		}
		
		
		$hash = array(
			"sitename" => html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'),
			"firstname" => isset($data['firstname']) ? $data['firstname'] : "",
			"lastname" => isset($data['lastname']) ? $data['lastname'] : "",
			"email" => isset($data['email']) ? $data['email'] : "",
			"telephone" => isset($data['telephone']) ? $data['telephone'] : "",
			"password" => isset($data['password']) ? $data['password'] : "",
			"account_link" => $this->url->link('account/login', '', true),
		); 
		
		$socnetauth2_newcustomer_mail_subject = $this->custom_unserialize( $this->config->get('module_socnetauth2_newcustomer_mail_subject'));
		if( !empty( $socnetauth2_newcustomer_mail_subject[$this->config->get('config_language_id')] ) )
		{
			$subject =  html_entity_decode($socnetauth2_newcustomer_mail_subject[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			
			foreach($hash as $key=>$val)
			{
				$subject = str_replace("{".$key."}", $val, $subject);
			}
		}
		else
		{
			$subject = $system_subject;
		}
		
		$socnetauth2_newcustomer_mail_text = $this->custom_unserialize( $this->config->get('module_socnetauth2_newcustomer_mail_text'));
		if( !empty( $socnetauth2_newcustomer_mail_text[$this->config->get('config_language_id')] ) )
		{
			$message =  html_entity_decode($socnetauth2_newcustomer_mail_text[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			
			foreach($hash as $key=>$val)
			{
				$message = str_replace("{".$key."}", $val, $message);
			}
		}
		else
		{
			$message = $system_subject;
		}
		
		return array($subject, $message);
	}
}

?>