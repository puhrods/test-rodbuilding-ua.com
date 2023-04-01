<?php  
class ControllerExtensionModulesocnetauth2 extends Controller { 
	private $error = array();
	private $data = array();
	private $redirect_url = '';
	
	public function authTinkoff()
	{
		$this->request->get['socnet'] = 'tinkoff'; 
		$this->dispatch();
	}
	
	public function authLoginza()
	{
		$this->request->get['socnet'] = 'loginza'; 
		$this->dispatch(); 
	}
	
	public function authVkontakte()
	{
		$this->request->get['socnet'] = 'vkontakte'; 
		$this->dispatch(); 
	}
	
	public function authTelegram()
	{
		$this->request->get['socnet'] = 'telegram'; 
		$this->dispatch();
	}
	
	public function authYandex()
	{
		$this->request->get['socnet'] = 'yandex'; 
		$this->dispatch();
	}
	
	public function authSteam()
	{
		$this->request->get['socnet'] = 'steam'; 
		$this->dispatch();
	}
	
	public function authMailru()
	{
		$this->request->get['socnet'] = 'mailru'; 
		$this->dispatch();
	}
	
	public function authGmail()
	{
		$this->request->get['socnet'] = 'gmail'; 
		$this->dispatch();
	}
	
	public function authTwitter()
	{
		$this->request->get['socnet'] = 'twitter'; 
		$this->dispatch();
	} 
	 
	public function authFacebook()
	{
		$this->request->get['socnet'] = 'facebook'; 
		$this->dispatch();
	}
	
	public function authOdnoklassniki()
	{
		$this->request->get['socnet'] = 'odnoklassniki'; 
		$this->dispatch();
	}
	
	public function index() { 
		
		if( !$this->config->get('module_socnetauth2_design_widget_status') )
			return;
		
		if( $this->customer->isLogged() && $this->config->get('module_socnetauth2_widget_after')=='hide' ) return;
		
		$this->language->load('extension/module/socnetauth2');
		
		/* start 1505 */
		$this->load->model('extension/module/socnetauth2');
		
		
		$socnetauth2_widget_name = $this->model_extension_module_socnetauth2->custom_unserialize( 
			$this->config->get('module_socnetauth2_widget_name') 
		);		
		
    	$this->data['widget_heading_title'] = $socnetauth2_widget_name[ $this->config->get('config_language_id') ];
		
		
		/* end 1505 */
		
		$this->data['widget_entry_email'] = $this->language->get('widget_entry_email');		
		$this->data['widget_entry_password'] = $this->language->get('widget_entry_password');		
		$this->data['widget_text_register'] = $this->language->get('widget_text_register');		
		$this->data['widget_text_forgotten'] = $this->language->get('widget_text_forgotten');		
		
		$this->data['widget_text_register'] = $this->language->get('widget_text_register');
    	$this->data['widget_text_login'] = $this->language->get('widget_text_login');
		$this->data['widget_text_logout'] = $this->language->get('widget_text_logout');
		$this->data['widget_text_forgotten'] = $this->language->get('widget_text_forgotten');
		$this->data['widget_text_account'] = $this->language->get('widget_text_account');
		$this->data['widget_text_edit'] = $this->language->get('widget_text_edit');
		$this->data['widget_text_password'] = $this->language->get('widget_text_password');
		$this->data['widget_text_wishlist'] = $this->language->get('widget_text_wishlist');
		$this->data['widget_text_order'] = $this->language->get('widget_text_order');
		$this->data['widget_text_download'] = $this->language->get('widget_text_download');
		$this->data['widget_text_return'] = $this->language->get('widget_text_return');
		$this->data['widget_text_transaction'] = $this->language->get('widget_text_transaction');
		$this->data['widget_text_newsletter'] = $this->language->get('widget_text_newsletter');
		
		$this->data['register'] = $this->url->link('account/register', '', 'SSL');
    	$this->data['login'] = $this->url->link('account/login', '', 'SSL');
		$this->data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
		$this->data['password'] = $this->url->link('account/password', '', 'SSL');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['order'] = $this->url->link('account/order', '', 'SSL');
		$this->data['download'] = $this->url->link('account/download', '', 'SSL');
		$this->data['return'] = $this->url->link('account/return', '', 'SSL');
		$this->data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$this->data['action'] = $this->url->link('account/login', '', 'SSL');
		
		$this->data['socnetauth2_widget_format'] = $this->config->get('module_socnetauth2_widget_format');	
		
		$this->data['logged'] = $this->customer->isLogged();
		
		
		$this->data['socnetauth2_widget_css_socnetauthbox'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox');
		$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthhead'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthhead');
		 
		$this->data['socnetauth2_widget_css_socnetauthbox_h3'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox_h3');
		$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthbody'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthbody');
		$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthform');
		$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'] = $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthform_td');
		
		$this->data['SOCNETAUTH2_DATA_BUTTONS'] = $this->showcode( 
			array(
				  "buttons_only"=>1, 
				  'str' => 'widget', 
				  'format' => $this->config->get('module_socnetauth2_widget_format') 
			) 
		);
			 
		
		
		return $this->load->view('extension/module/socnetauth2', $this->data);
	}
	
	public function sortMethods($socnetauth2_methods)
	{
		$sortable_arr = array();
		
		foreach($socnetauth2_methods as $key=>$val)
		{
			$val['k'] = $key;
			$sortable_arr[] = $val;
		}
		
		usort($sortable_arr, array($this, "cmp"));
		
		$sorted_socnetauth2_methods = array();
		
		foreach($sortable_arr as $key=>$val)
		{
			$sorted_socnetauth2_methods[ $val['k'] ] = $val;
		}
		
		return $sorted_socnetauth2_methods;
	}
	
	protected function cmp($a, $b)
	{
		if($a['sort'] == $b['sort']) {
			return 0;
		}
	
		return ($a['sort'] < $b['sort']) ? -1 : 1;
	}
	
	public function country() 
	{
		$json = array();
		
		$this->load->model('localisation/country');

    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		
		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}
		
		$this->response->setOutput(json_encode($json));
	} 
	
	private function custom_unserialize($s)
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
	
	/* start 0105 */
	
	public function dispatch()
	{
		$this->load->model('extension/module/socnetauth2');
		
		if( !empty($this->request->get['first']) && empty($this->request->get['openid_ns']) )
			$this->startOutsideAuth();
		else
			$this->endOutsideAuth();
	}
	
	public function startOutsideAuth()
	{ 
		$this->load->model('extension/module/socnetauth2');
		
		$SOCNET = $this->request->get['socnet'];
		$IS_DEBUG = $this->config->get('module_socnetauth2_'.$SOCNET.'_debug');
		
		if( !$this->config->get('module_socnetauth2_'.$SOCNET.'_status') )
		{
			$url = !empty($this->request->server['HTTP_REFERER']) ? $this->request->server['HTTP_REFERER'] : '/';
			
			if($IS_DEBUG)
			{
				if( strstr($url, "?") )
				{
					$url .= '&error=1';
				}
				else
				{
					$url .= '?error=1';
				}
			}
			
			$this->response->redirect($url);
			exit();  
		}
		
		$STATE = $SOCNET.'_socnetauth2_'.rand(); 
		$CURRENT_URI = !empty($this->request->server['HTTP_REFERER']) ? $this->request->server['HTTP_REFERER'] : '/';
			
		if( strstr($CURRENT_URI, 'logout') )
		{
			$CURRENT_URI = preg_replace("/index\.php\?route\=account\/logout$/", 
											"", $CURRENT_URI);
				
			$CURRENT_URI = str_replace("logout", "", $CURRENT_URI);
			$CURRENT_URI = preg_replace("/\/+$/", "/", $CURRENT_URI);
		}
			
		$CURRENT_URI = str_replace("?socnetauth2close=1", "", $CURRENT_URI);
		$CURRENT_URI = str_replace("&socnetauth2close=1", "", $CURRENT_URI);
		
		
		$REDIRECT_URI = $this->getRedirectURL($SOCNET);
		 
		setcookie("socnetauth2_from_page", $this->request->get['first'], time()+3600, "/" );
		setcookie($SOCNET."_state", $STATE, time()+3600, "/" );
		
		$this->model_extension_module_socnetauth2->setRecord($STATE, $CURRENT_URI);
			
		$this->load->model('extension/module/socnetauth2lib/'.$SOCNET);
			
		$REQUISITES = $this->model_extension_module_socnetauth2->getRequisites(); 
		$url = $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->getStartURL($REQUISITES, $REDIRECT_URI, $STATE, $IS_DEBUG); 
		
		if( empty($url) )
		{
			if( $IS_DEBUG )
				exit('empty auth url');
			else
				$url = '/?error=no_auth_url';
		}
		
		header("Location: ".$url); 
	}
			
	
	public function endOutsideAuth()
	{
		
		$SOCNET = $this->request->get['socnet'];
		$IS_DEBUG = $this->config->get('module_socnetauth2_'.$SOCNET.'_debug');

		$REDIRECT_URI = $this->getRedirectURL($SOCNET);
		
		###
		#$IS_DEBUG = 1;
		###
		 
		 
		$this->load->model('extension/module/socnetauth2lib/'.$SOCNET);
		
		$recordData = array();
		$CURRENT_URI = '';
		$REQUISITES = $this->model_extension_module_socnetauth2->getRequisites(); 
		
		if( $SOCNET != 'loginza' )
		{
			$recordKey = $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->getRecordKey($REDIRECT_URI);
			
			if( !$recordKey )
			{
				$this->stopAuth('empty_recordKey', 'empty recordKey', $IS_DEBUG);
			}
			
			$recordData = $this->model_extension_module_socnetauth2->getRecord( $recordKey );
			
			if( empty($recordData) && $IS_DEBUG )
			{
				$this->stopAuth('empty_recordData', 'empty recordData', $IS_DEBUG);
			} 
			
			$CURRENT_URI = $recordData['redirect'];
			$CURRENT_URI = str_replace("&socnetauth2close=1", "", $CURRENT_URI);
			$CURRENT_URI = str_replace("?socnetauth2close=1", "", $CURRENT_URI);
		}
		else
		{
			$CURRENT_URI = $this->request->cookie['socnetauth2_lastlink']; 
		}
		
		if( $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->checkReturn( $recordData, $REQUISITES ) )
		{
			$user_data = $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->getUserData($REQUISITES, $REDIRECT_URI, $IS_DEBUG); 
			
			if( $user_data )
				$result_data = $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->getResultData($REQUISITES, $user_data); 
			else
			{
				$this->stopAuth('empty_user_data', 'empty user_data', $IS_DEBUG);
			}
				
			if( $result_data )	
			{
				$user_data = $this->{'model_extension_module_socnetauth2lib_' . $SOCNET}->prepareUserData($user_data); 
				
				$redirect = $this->model_extension_module_socnetauth2->finishAuth(
					$SOCNET, $result_data, $user_data, $CURRENT_URI, $IS_DEBUG
				);
				
				if( !empty($this->session->data['customer_id']) )
				{
					$this->model_extension_module_socnetauth2->setLoginCookies( $this->session->data['customer_id'] );
					$this->model_extension_module_socnetauth2->restoreCart($this->session->data['customer_id']);
				}
				
				$redirect = str_replace("&socnetauth2close=1", "", $redirect);
				$redirect = str_replace("?socnetauth2close=1", "", $redirect);
				
				if( $redirect )
					$this->response->redirect($redirect);
				else
					$this->stopAuth('empty_redirect', 'empty redirect', $IS_DEBUG); 
					
			}
			else
			{
				$this->stopAuth('empty_result_data', 'empty result_data', $IS_DEBUG); 
			}
		}
		else
		{
			$this->stopAuth('error_return_params', 'error return params', $IS_DEBUG);  
		}
	}
	
	private function getRedirectURL($SOCNET)
	{
		$this->load->model('extension/module/socnetauth2');
		$REDIRECT_URI = '';
		$protokol = $this->model_extension_module_socnetauth2->getProtokol(); 
		$socnetauth2_shop_folder = $this->model_extension_module_socnetauth2->getShopFolder(); 
		
		if( !file_exists( preg_replace("/system\/?$/", "", DIR_SYSTEM).'socnetauth2/'.$SOCNET.'.php' ) ||
			$this->config->get('module_socnetauth2_'.$SOCNET.'_redirect_type') == 'new'
		)
			$REDIRECT_URI = $protokol.$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.'/sna-'.$SOCNET;
		else
			$REDIRECT_URI = $protokol.$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.'/socnetauth2/'.$SOCNET.'.php';
	
		return $REDIRECT_URI;
	}
	
	private function stopAuth($key, $message, $IS_DEBUG)
	{
		if($IS_DEBUG)
			exit($message);
		else
		{
			$url = !empty($this->request->server['HTTP_REFERER']) ? $this->request->server['HTTP_REFERER'] : '/';
			
			if( strstr($url, "?") )
				$url .= '&error='.$key;
			else
				$url .= '?error='.$key;
				
			$this->response->redirect( $url );
			exit();
		}
	}
	
	public function closeWindow()
	{
		$this->setParam('socnetauth2_confirmdata_show', 0); 
		setcookie('socnetauth2_confirmdata_show', 0, time()+3600, '/'); 
		
		exit('OK');
	}
	 
	public function frame()
	{
		$this->response->setOutput( $this->getFrame() );
	}
	
	private function getFrame()
	{
		$this->data['is_noiframe'] = $this->config->get('module_socnetauth2_is_noiframe');
		 
		$this->language->load('extension/module/socnetauth2');
		$this->load->model('extension/module/socnetauth2');
		$this->load->model('account/customer'); 
		
		$socnetauth_data = $this->getParam('socnetauth2_confirmdata');
		$socnetauth_data2 = $socnetauth_data;
				
		if( empty($socnetauth_data2) )
			return;
		
		foreach($socnetauth_data2 as $key=>$val)
		{
			if( is_array( $val ) ) unset($socnetauth_data2[$key]);
		}
		
		
		if( strstr( implode(',', $socnetauth_data2), '1,2,3,4') )
		{
			return $this->getConfirmform(array("email" => $socnetauth_data[4],
									 "identity" => $socnetauth_data[5],
									 "link" => $socnetauth_data[6],
									 "provider" => $socnetauth_data[7],
									 "data" => serialize($socnetauth_data[8]) ) );
		
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && 
			$data = $this->validate($socnetauth_data['data'])) 
		{
			if( !empty($data['email']) 
				&& $this->config->get('module_socnetauth2_email_auth') == 'confirm' 
				&& !$this->model_extension_module_socnetauth2->checkUniqEmail( $data['email'] ) 
			)
			{				
				$this->setParam('controlled_email', $data['email']);
				$this->model_extension_module_socnetauth2->sendConfirmEmail( $data );
				
				return $this->getConfirmform($data);
			}
		
			$this->redirect_url = !empty( $socnetauth_data['redirect_url'] ) ? $socnetauth_data['redirect_url'] : '';
			$this->setParam('socnetauth2_confirmdata', '');
			$customer_id = $this->model_extension_module_socnetauth2->addCustomer( $data );
			
			if( $customer_id && $this->getParam('socnetauth2_confirmdata_rawdata') )
			{
				$this->model_extension_module_socnetauth2->addCustomerComment( 
					$customer_id, 
					$data['provider'],
					$this->getParam('socnetauth2_confirmdata_rawdata')
				);
			}
			
			
			
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer( $customer_id );
			
			if( !empty($customer_info['status']) )
				$this->session->data['customer_id'] = $customer_id;
			
			return $this->getSuccess($customer_id);
		}
		
		$this->data['action'] = $this->url->link('extension/module/socnetauth2/frame', '', 'SSL');
		
		$this->data['header'] = $this->language->get('header');
		$this->data['header_notice'] = $this->language->get('header_notice');
		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_telephone'] = $this->language->get('entry_telephone');
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['text_none'] = $this->language->get('text_none');
		
		/* kin insert metka: c1 */
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_country'] = $this->language->get('entry_country');
		
		$this->data['text_select'] = $this->language->get('text_select');
		
		$this->data['firstname_required'] = $this->config->get('module_socnetauth2_confirm_firstname_required');
		$this->data['lastname_required']  = $this->config->get('module_socnetauth2_confirm_lastname_required');
		$this->data['email_required']     = $this->config->get('module_socnetauth2_confirm_email_required');
		$this->data['telephone_required'] = $this->config->get('module_socnetauth2_confirm_telephone_required');
		$this->data['company_required']   = $this->config->get('module_socnetauth2_confirm_company_required');
		$this->data['postcode_required']  = $this->config->get('module_socnetauth2_confirm_postcode_required');
		$this->data['country_required']   = $this->config->get('module_socnetauth2_confirm_country_required');
		$this->data['zone_required']      =	$this->config->get('module_socnetauth2_confirm_zone_required');
		$this->data['city_required']      = $this->config->get('module_socnetauth2_confirm_city_required');
		$this->data['address_1_required'] = $this->config->get('module_socnetauth2_confirm_address_1_required');
		
		$this->data['username_required'] = $this->config->get('module_socnetauth2_confirm_username_required');
		$old_dobor = array();
		
		#if( $this->config->get('module_socnetauth2_dobortype') == 'every' )
		#{
			$old_dobor = $this->model_extension_module_socnetauth2->getOldDoborData( $socnetauth_data );
		#}
		
		/* start 1303 */
		$this->data['entry_agree'] = $this->language->get('entry_agree');
		$this->data['agree_required'] = $this->config->get('module_socnetauth2_confirm_agree_required');
		
		if( $this->config->get('module_socnetauth2_confirm_agree_status') )
		{
			$this->data['is_agree'] = 1;
		}
		else
		{
			$this->data['is_agree'] = 0;
		}
		
		
		$this->load->model('catalog/information');

		$this->data['inf'] = $this->model_catalog_information->getInformation( $this->config->get('module_socnetauth2_confirm_agree_status') );
		
		if( $this->data['inf'] )
			$this->data['inf']['href'] = $this->url->link( 'information/information', 
				'information_id='.$this->config->get('module_socnetauth2_confirm_agree_status') 
			);
		
		$this->data['inf2'] = $this->model_catalog_information->getInformation( $this->config->get('module_socnetauth2_confirm_agree2_status') );
		
		if( $this->data['inf2'] )
			$this->data['inf2']['href'] = $this->url->link( 'information/information', 
				'information_id='.$this->config->get('module_socnetauth2_confirm_agree2_status') 
			);
		$this->data['inf3'] = $this->model_catalog_information->getInformation( $this->config->get('module_socnetauth2_confirm_agree3_status') );
		
		if( $this->data['inf3'] )
			$this->data['inf3']['href'] = $this->url->link( 'information/information', 
				'information_id='.$this->config->get('module_socnetauth2_confirm_agree3_status') 
			);
		
		
		$this->data['entry_agrees'] = $this->language->get('entry_agrees');
		
		if( $this->config->get('module_socnetauth2_confirm_agree2_status') )
			$this->data['is_agree2'] = 1;
		else
			$this->data['is_agree2'] = 0;
		
		if( $this->config->get('module_socnetauth2_confirm_agree3_status') )
			$this->data['is_agree3'] = 1;
		else
			$this->data['is_agree3'] = 0;
		
		$this->data['count_agrees'] = 0;
		if( $this->data['is_agree'] )
			$this->data['count_agrees']++;
		if( $this->data['is_agree2'] )
			$this->data['count_agrees']++;
		if( $this->data['is_agree3'] )
			$this->data['count_agrees']++;
		
		if( $this->data['count_agrees'] > 1 )
		{
			$this->data['is_many_agree'] = 1;
			$this->data['entry_agree'] = $this->language->get('entry_agrees');
		}
		else
			$this->data['is_many_agree'] = 0;
	
		if( !empty( $this->error['agree2'] ) )
			$this->data['error_agree2'] = $this->language->get('error_agree');
		else
			$this->data['error_agree2'] = '';
		
		if( !empty( $this->error['agree3'] ) )
			$this->data['error_agree3'] = $this->language->get('error_agree');
		else
			$this->data['error_agree3'] = '';
		
		if( !empty( $this->error['agree'] ) )
		{
			$this->data['error_agree'] = $this->error['agree'];
		}
		else
		{
			$this->data['error_agree'] = '';
		}
		
		/* end 1303 */
		/* start 0102 */
		$this->data['entry_group'] = $this->language->get('entry_group');
		$this->data['group_required'] = $this->config->get('module_socnetauth2_confirm_group_required');
		
		$this->load->model('account/customer_group');
		/* start 0502 */
		$groups = $this->model_account_customer_group->getCustomerGroups();
		
		$group_display = $this->config->get('config_customer_group_display');
		$this->data['groups'] = array();
		if( $group_display )
		{
			foreach( $groups as $group )
			{
				if( in_array( $group['customer_group_id'], $group_display) )
				{
					$this->data['groups'][] = $group;
				}
			}
		}
		/* end 0502 */
		
		
		if( isset($this->request->post['group']) )
		{
			$this->data['group'] = $this->request->post['group'];
		}
		elseif( !empty( $old_dobor['group'] ) )
		{
			$this->data['group'] = $old_dobor['group'];
		}
		else
		{
			$this->data['group'] = '';
		}
		
		/* start 1007  */
		if( !empty($old_dobor['group']) && 
			$this->config->get('module_socnetauth2_confirm_agree_hideifhas') )
		{
			$this->data['is_group'] = 0;
		} else
		/* end 1007 */
		if( $this->config->get('module_socnetauth2_confirm_group_status') )
		{
			$this->data['is_group'] = 1;
		}
		else
		{
			$this->data['is_group'] = 0;
		}
		/* end 0102 */
		
		
		$this->load->model('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();

		if( isset($this->request->post['company']) )
		{
			$this->data['company'] = $this->request->post['company'];
		}
		elseif( !empty( $old_dobor['company'] ) )
		{
			$this->data['company'] = $old_dobor['company'];
		}
		else
		{
			$this->data['company'] = '';
		}
		
		if( isset($socnetauth_data['company']) )
		{
			$this->data['is_company'] = 1;
		}
		else
		{
			$this->data['is_company'] = 0;
		}
		
		if( isset($this->request->post['address_1']) )
		{
			$this->data['address_1'] = $this->request->post['address_1'];
		}
		elseif( !empty( $old_dobor['address_1'] ) )
		{
			$this->data['address_1'] = $old_dobor['address_1'];
		}
		else
		{
			$this->data['address_1'] = '';
		}
		
		if( isset($socnetauth_data['address_1']) )
		{
			$this->data['is_address_1'] = 1;
		}
		else
		{
			$this->data['is_address_1'] = 0;
		}
		
		if( isset($this->request->post['postcode']) )
		{
			$this->data['postcode'] = $this->request->post['postcode'];
		}
		elseif( !empty( $old_dobor['postcode'] ) )
		{
			$this->data['postcode'] = $old_dobor['postcode'];
		}
		else
		{
			$this->data['postcode'] = '';
		}
		
		if( isset($socnetauth_data['postcode']) )
		{
			$this->data['is_postcode'] = 1;
		}
		else
		{
			$this->data['is_postcode'] = 0;
		}
		
		if( isset($this->request->post['city']) )
		{
			$this->data['city'] = $this->request->post['city'];
		}
		elseif( !empty( $old_dobor['city'] ) )
		{
			$this->data['city'] = $old_dobor['city'];
		}
		else
		{
			$this->data['city'] = '';
		}
		
		if( isset($socnetauth_data['city']) )
		{
			$this->data['is_city'] = 1;
		}
		else
		{
			$this->data['is_city'] = 0;
		}
		
		if( isset($this->request->post['zone']) )
		{
			$this->data['zone'] = $this->request->post['zone'];
		}
		elseif( !empty( $old_dobor['zone'] ) )
		{
			$this->data['zone'] = $old_dobor['zone'];
		}
		else
		{
			$this->data['zone'] = '';
		}
		
		if( isset($socnetauth_data['zone']) )
		{
			$this->data['is_zone'] = 1;
		}
		else
		{
			$this->data['is_zone'] = 0;
		}
		
		/* start 2507 */
		$this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
		
		if( $this->config->get('module_socnetauth2_confirm_newsletter') )
		{
			$this->data['is_newsletter'] = 1;
		}
		else
		{
			$this->data['is_newsletter'] = 0;
		}
		
		if( isset($this->request->post['newsletter']) )
		{
			$this->data['newsletter'] = $this->request->post['newsletter'];
		}
		elseif( !empty( $old_dobor['newsletter'] ) )
		{
			$this->data['newsletter'] = $old_dobor['newsletter'];
		}
		else
		{
			$this->data['newsletter'] = '';
		}
		/* end 2507 */
		
		
		if( isset($this->request->post['country']) )
		{
			$this->data['country'] = $this->request->post['country'];
		}
		elseif( !empty( $old_dobor['country'] ) )
		{
			$this->data['country'] = $old_dobor['country'];
		}
		else
		{
			$this->data['country'] = $this->config->get('config_country_id');
		}
		
		if( isset($socnetauth_data['country']) )
		{
			$this->data['is_country'] = 1;
		}
		else
		{
			$this->data['is_country'] = 0;
		}
		
		if( !empty( $this->error['company'] ) )
		{
			$this->data['error_company'] = $this->error['company'];
		}
		else
		{
			$this->data['error_company'] = '';
		}
		
		if( !empty( $this->error['address_1'] ) )
		{
			$this->data['error_address_1'] = $this->error['address_1'];
		}
		else
		{
			$this->data['error_address_1'] = '';
		}
		
		if( !empty( $this->error['postcode'] ) )
		{
			$this->data['error_postcode'] = $this->error['postcode'];
		}
		else
		{
			$this->data['error_postcode'] = '';
		}
		
		if( !empty( $this->error['city'] ) )
		{
			$this->data['error_city'] = $this->error['city'];
		}
		else
		{
			$this->data['error_city'] = '';
		}
		
		if( !empty( $this->error['zone'] ) )
		{
			$this->data['error_zone'] = $this->error['zone'];
		}
		else
		{
			$this->data['error_zone'] = '';
		}
		
		if( !empty( $this->error['country'] ) )
		{
			$this->data['error_country'] = $this->error['country'];
		}
		else
		{
			$this->data['error_country'] = '';
		}
		
		/* 
		company
		address_1
		address_2
		postcode
		city
		zone_id
		country_id
		end kin metka: c1 */
		
		$this->data['text_submit'] = $this->language->get('text_submit');
		
		
		if( !empty($this->request->post['username']) )
		{
			$this->data['username'] = $this->request->post['username'];
		}
		elseif( !empty($socnetauth_data['username']) )
		{
			$this->data['username'] = $socnetauth_data['username'];
		}
		elseif( !empty( $old_dobor['username'] ) )
		{
			$this->data['username'] = $old_dobor['username'];
		}
		else
		{
			$this->data['username'] = '';
		}
		
		if( isset($socnetauth_data['username']) )
		{
			$this->data['is_username'] = 1;
		}
		else
		{
			$this->data['is_username'] = 0;
		}
		
		if( !empty($this->request->post['firstname']) )
		{
			$this->data['firstname'] = $this->request->post['firstname'];
		}
		elseif( !empty($socnetauth_data['firstname']) )
		{
			$this->data['firstname'] = $socnetauth_data['firstname'];
		}
		elseif( !empty( $old_dobor['firstname'] ) )
		{
			$this->data['firstname'] = $old_dobor['firstname'];
		}
		else
		{
			$this->data['firstname'] = '';
		}
		
		if( isset($socnetauth_data['firstname']) )
		{
			$this->data['is_firstname'] = 1;
		}
		else
		{
			$this->data['is_firstname'] = 0;
		}
		
		if( !empty($this->request->post['lastname']) )
		{
			$this->data['lastname'] = $this->request->post['lastname'];
		}
		elseif( !empty($socnetauth_data['lastname']) )
		{
			$this->data['lastname'] = $socnetauth_data['lastname'];
		}
		elseif( !empty( $old_dobor['lastname'] ) )
		{
			$this->data['lastname'] = $old_dobor['lastname'];
		}
		else
		{
			$this->data['lastname'] = '';
		}
		
		if( isset($socnetauth_data['lastname']) )
		{
			$this->data['is_lastname'] = 1;
		}
		else
		{
			$this->data['is_lastname'] = 0;
		}
		
		if( !empty($this->request->post['email']) )
		{
			$this->data['email'] = $this->request->post['email'];
		}
		elseif( !empty($socnetauth_data['email']) )
		{
			$this->data['email'] = $socnetauth_data['email'];
		}
		elseif( !empty( $old_dobor['email'] ) )
		{
			$this->data['email'] = $old_dobor['email'];
		}
		else
		{
			$this->data['email'] = '';
		}
		
		if( isset($socnetauth_data['email']) )
		{
			$this->data['is_email'] = 1;
		}
		else
		{
			$this->data['is_email'] = 0;
		}
		
		if( !empty($this->request->post['telephone']) )
		{
			$this->data['telephone'] = $this->request->post['telephone'];
		}
		elseif( !empty($socnetauth_data['telephone']) )
		{
			$this->data['telephone'] = $socnetauth_data['telephone'];
		}
		elseif( !empty( $old_dobor['telephone'] ) )
		{
			$this->data['telephone'] = $old_dobor['telephone'];
		}
		else
		{
			$this->data['telephone'] = '';
		}
		
		/* start 1409 */
		$this->data['telephone_mask'] = $this->config->get('module_socnetauth2_telephone_mask');
		/* end 1409 */
		if( isset($socnetauth_data['telephone']) )
		{
			$this->data['is_telephone'] = 1;
		}
		else
		{
			$this->data['is_telephone'] = 0;
		}
		
		if( !empty( $this->error['username'] ) )
		{
			$this->data['error_username'] = $this->error['username'];
		}
		else
		{
			$this->data['error_username'] = '';
		}
		
		if( !empty( $this->error['firstname'] ) )
		{
			$this->data['error_firstname'] = $this->error['firstname'];
		}
		else
		{
			$this->data['error_firstname'] = '';
		}
		
		if( !empty( $this->error['lastname'] ) )
		{
			$this->data['error_lastname'] = $this->error['lastname'];
		}
		else
		{
			$this->data['error_lastname'] = '';
		}
		
		if( !empty( $this->error['email'] ) )
		{
			$this->data['error_email'] = $this->error['email'];
		}
		else
		{
			$this->data['error_email'] = '';
		}
		
		if( !empty( $this->error['telephone'] ) )
		{
			$this->data['error_telephone'] = $this->error['telephone'];
		}
		else
		{
			$this->data['error_telephone'] = '';
		}
		
		//-------------------------
		
		if(  version_compare(VERSION, '2.2.0.0') >= 0 )
		{
			return $this->load->view('extension/module/socnetauth2/socnetauth2_frame', $this->data);
		}
		else
		{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame.tpl', $this->data);
			} else {
				return $this->load->view('default/template/extension/module/socnetauth2/socnetauth2_frame.tpl', $this->data);
			}
		}
		
		
	}
	
	/* start 1601 */
	public function success($customer_id)
	{
		$this->response->setOutput( $this->getSuccess($customer_id) );
	} 
	
	private function getSuccess($customer_id)
	{
		setcookie('socnetauth2_confirmdata_show', 0, time()+3600, '/');
		
		$socnetauth_data = $this->getParam('socnetauth2_confirmdata');
		
		if( !empty($socnetauth_data['redirect_url']) )
		{
			$this->data['redirect_url'] = $socnetauth_data['redirect_url'];
		}
		elseif( !empty($this->redirect_url) )
		{
			$this->data['redirect_url'] = $this->redirect_url;
		}
		else
		{
			$this->data['redirect_url'] = '';
		}
		
		
		$this->language->load('extension/module/socnetauth2');
		$this->data['header'] = $this->language->get('header');
		$this->load->model('account/customer');
		$customer_info = $this->model_account_customer->getCustomer( $this->session->data['customer_id'] );
		
		$this->data['header'] = $this->language->get('header');
			
		if( empty($customer_info['status']) )
		{
			$this->data['success'] = $this->language->get('success_needapprove');
			$this->data['timeout'] =  10000; // миллисекунды
		}
		else
		{ 
			$this->data['success'] = $this->language->get('success');
			$this->data['timeout'] =  2000; // миллисекунды
		}
		
		if(  version_compare(VERSION, '2.2.0.0') >= 0 )
		{
			return $this->load->view('extension/module/socnetauth2/socnetauth2_frame_success', $this->data);
		}
		else
		{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame_success.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame_success.tpl', $this->data);
			} else {
				return $this->load->view('default/template/extension/module/socnetauth2/socnetauth2_frame_success.tpl', $this->data);
			}
			
		}
		
		
	}
	
	
  	private function validate($data) {
    	
		if( isset( $this->request->post['username'] ) && 
			empty( $this->request->post['username'] ) &&
			$this->config->get('module_socnetauth2_confirm_username_required') 
		)
		{
			$this->error['username'] = $this->language->get('error_username');
		}
		
		
		if( isset( $this->request->post['firstname'] ) && 
			empty( $this->request->post['firstname'] ) &&
			$this->config->get('module_socnetauth2_confirm_firstname_required') 
		)
		{
			$this->error['firstname'] = $this->language->get('error_firstname');
		}
		
		if( isset( $this->request->post['lastname'] ) && 
			empty( $this->request->post['lastname'] ) &&
			$this->config->get('module_socnetauth2_confirm_lastname_required')  
		)
		{
			$this->error['lastname'] = $this->language->get('error_lastname');
		}
		
		if( isset( $this->request->post['email'] ) && 
			empty( $this->request->post['email'] ) &&
			$this->config->get('module_socnetauth2_confirm_email_required') 
		)
		{
			$this->error['email'] = $this->language->get('error_email');
		}
		elseif( 
			!empty( $this->request->post['email'] ) && 
			!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'] ) &&
			$this->config->get('module_socnetauth2_confirm_email_required') 
		)
		{
			$this->error['email'] = $this->language->get('error_email2');
		}
		
		
		if( $this->config->get('module_socnetauth2_confirm_username_required')  )
		{
			if( (!isset($this->request->post['username']) || utf8_strlen($this->request->post['username']) < 3 || 	utf8_strlen($this->request->post['username']) > 64) 
			)
			{
				$this->error['username'] = 'Логин должен быть от 3 до 64 символов';
			}
			elseif (preg_match('/[^a-zа-яёй0-9_\-\s]/iu', $this->request->post['username'])) {
				$this->error['username'] = 'Логин должен содержать только цифры и буквы';
			} elseif ($this->model_account_customer->getTotalCustomersByUserName($this->request->post['username'])) {
				$this->error['username'] = 'Пользователь с таким логином уже существует';
			} else {
				
				$prohibited_usernames = array('root','администратор','administrator','administratоr','админ','aдмин','адмиh','адмиN','admin','модератор','moderator');
				
				foreach ($prohibited_usernames as $username) {
					if (utf8_stripos($username, $this->request->post['username']) !== false || utf8_stripos($this->request->post['username'], $username) !== false) {
							$this->error['username'] = 'Недопустимый логин';

					  break;
					}
				}
			}
		}
		
		if( isset( $this->request->post['telephone'] ) && 
			empty( $this->request->post['telephone'] ) &&
			$this->config->get('module_socnetauth2_confirm_telephone_required') )
		{
			$this->error['telephone'] = $this->language->get('error_telephone');
		}
		/* start 1809 */
		elseif( isset( $this->request->post['telephone'] ) && 
			!empty( $this->request->post['telephone'] ) &&
			strlen( preg_replace( "/[^\d]/", "", $this->request->post['telephone'] ) ) != 10 &&
			strlen( preg_replace( "/[^\d]/", "", $this->request->post['telephone'] ) ) != 11 &&
			strlen( preg_replace( "/[^\d]/", "", $this->request->post['telephone'] ) ) != 12 &&
			strlen( preg_replace( "/[^\d]/", "", $this->request->post['telephone'] ) ) != 13
		)
		{
			$this->error['telephone'] = $this->language->get('error_telephone2');
		}
		/* end 1809 */
		
		if( isset( $this->request->post['company'] ) && 
			empty( $this->request->post['company'] ) &&
			$this->config->get('module_socnetauth2_confirm_company_required') )
		{
			$this->error['company'] = $this->language->get('error_company');
		}
		
		/* start 1303 */
		if( $this->config->get('module_socnetauth2_confirm_agree_status') && 
			empty( $this->request->post['agree'] ) &&
			$this->config->get('module_socnetauth2_confirm_agree_required') )
		{
			$this->error['agree'] = $this->language->get('error_agree');
		}
		/* end 1303 */
		if( $this->config->get('module_socnetauth2_confirm_agree2_status') && 
			empty( $this->request->post['agree2'] ) &&
			$this->config->get('module_socnetauth2_confirm_agree2_required') )
		{
			$this->error['agree2'] = $this->language->get('error_agree');
		}
		
		if( $this->config->get('module_socnetauth2_confirm_agree3_status') && 
			empty( $this->request->post['agree3'] ) &&
			$this->config->get('module_socnetauth2_confirm_agree3_required') )
		{
			$this->error['agree3'] = $this->language->get('error_agree');
		}
		
		if( isset( $this->request->post['address_1'] ) && 
			empty( $this->request->post['address_1'] ) &&
			$this->config->get('module_socnetauth2_confirm_address_1_required') )
		{
			$this->error['address_1'] = $this->language->get('error_address_1');
		}
		
		if( isset( $this->request->post['postcode'] ) && 
			empty( $this->request->post['postcode'] ) &&
			$this->config->get('module_socnetauth2_confirm_postcode_required') )
		{
			$this->error['postcode'] = $this->language->get('error_postcode');
		}
		
		if( isset( $this->request->post['city'] ) && 
			empty( $this->request->post['city'] ) &&
			$this->config->get('module_socnetauth2_confirm_city_required') )
		{
			$this->error['city'] = $this->language->get('error_city');
		}
		
		if( isset( $this->request->post['zone'] ) && 
			empty( $this->request->post['zone'] ) &&
			$this->config->get('module_socnetauth2_confirm_zone_required') )
		{
			$this->error['zone'] = $this->language->get('error_zone');
		}
		
		if( isset( $this->request->post['country'] ) && 
			empty( $this->request->post['country'] ) &&
			$this->config->get('module_socnetauth2_confirm_country_required') )
		{
			$this->error['country'] = $this->language->get('error_country');
		}
		
    	if (!$this->error) {
			if( !empty($this->request->post['firstname']) )
			{
				$data['firstname'] = $this->request->post['firstname'];
			}
			
			if( !empty($this->request->post['username']) )
			{
				$data['username'] = $this->request->post['username'];
			}
			if( !empty($this->request->post['lastname']) )
			{
				$data['lastname']  = $this->request->post['lastname'];
			}
						
			if( !empty($this->request->post['email']) )
			{
				$data['email']  = $this->request->post['email'];
			}
			
			/* start 0102 */
			if( !empty($this->request->post['group']) )
			{
				$data['group']  = $this->request->post['group'];
			}
			elseif( !empty($data['provider']) && 
					$this->config->get('module_socnetauth2_'.$data['provider'].'_customer_group_id') )
			{
				$data['group']  = $this->config->get('module_socnetauth2_'.$data['provider'].'_customer_group_id');
			}
			/* end 0102 */
						
			if( !empty($this->request->post['telephone']) )
			{
				$data['telephone']  = $this->request->post['telephone'];
			}
			
			if( !empty($this->request->post['company']) )
			{
				$data['company']  = $this->request->post['company'];
			}
			
			if( !empty($this->request->post['address_1']) )
			{
				$data['address_1']  = $this->request->post['address_1'];
			}
			
			if( !empty($this->request->post['postcode']) )
			{
				$data['postcode']  = $this->request->post['postcode'];
			}
			
			if( !empty($this->request->post['city']) )
			{
				$data['city']  = $this->request->post['city'];
			}
			
			if( !empty($this->request->post['zone']) )
			{
				$data['zone']  = $this->request->post['zone'];
			}
			
			if( !empty($this->request->post['country']) )
			{
				$data['country']  = $this->request->post['country'];
			}
			
			/* start 2507 */
			if( !empty($this->request->post['newsletter']) )
			{
				$data['newsletter']  = $this->request->post['newsletter'];
			}
			else
			{
				$data['newsletter']  = 0;
			}
			/* end 2507 */
			
      		return $data;
    	} else {
      		return false;
    	}  	
  	}
	 
	/* start 1911 */
	public function getConfirmCode($ar=array())
	{
		if( !$this->getParam('socnetauth2_confirmdata') ||
			!$this->getParam('socnetauth2_confirmdata_show') 
			 || 
			(
				!empty( $this->request->get['lastlink'] ) &&  
				strstr($this->request->get['lastlink'], "information/information")
			)
			 ||
			(
				!empty( $this->request->get['lastroute'] ) &&  
				strstr($this->request->get['lastroute'], "information/information")
			)
		)
			exit();
		
		$socnetauth2_confirmdata = $this->getParam('socnetauth2_confirmdata');
		
		$dop = 0;
		
		if( $this->config->get('module_socnetauth2_confirm_agree_status') )
		{
			$dop += 40;
		}
		
		
		if( !empty($socnetauth2_confirmdata['agree']) )
			$dop = 20;
		if( !empty($socnetauth2_confirmdata['agree2']) )
			$dop += 20;
		if( !empty($socnetauth2_confirmdata['agree3']) )
			$dop += 20;
		
			
		$tmp_data = array(
			"divframe_height" => $dop+(340-(32*(5-(count($this->getParam('socnetauth2_confirmdata')))))),
			"frame_height"    => $dop+(360-(32*(5-(count($this->getParam('socnetauth2_confirmdata')))))),
			"lastlink"        => isset($this->request->get['lastlink']) ? urldecode($this->request->get['lastlink']) : '',
			"lastroute"        => isset($this->request->get['route']) ? urldecode($this->request->get['route']) : '',
			"socnetauth2_is_close_disabled" => $this->config->get('module_socnetauth2_is_close_disabled'),
			"frame_url"		  => $this->url->link( 'extension/module/socnetauth2/frame', '', 'SSL' )
		);
	
		
		if( !$this->config->get('module_socnetauth2_is_noiframe') )
		{
			$tmp_data['is_iframe'] = 1;
		}
		else
		{
			$tmp_data['noframe'] = $this->getFrame();
			$tmp_data['is_iframe'] = 0;
		}
		
		$this->setParam('socnetauth2_confirmdata_show', 0); 
		
		$socnetauth2_confirm_block = '';
		if(  version_compare(VERSION, '2.2.0.0') >= 0 )
		{
			$socnetauth2_confirm_block = $this->load->view('extension/module/socnetauth2/socnetauth2_confirm', $tmp_data);
		}
		else
		{
			$socnetauth2_confirm_block = $this->load->view('default/template/extension/module/socnetauth2/socnetauth2_confirm.tpl', $tmp_data);
		}
		
		exit($socnetauth2_confirm_block); 
	}
	/* start 1911 */
	/* start 1505 */
	public function showcode($ar=array())
	{
		$this->load->model('extension/module/socnetauth2');
			
		if( empty($this->session->data['customer_id']) && 
			!empty($_COOKIE['socnetauth2_authcode']) &&
			$customer_id = $this->model_extension_module_socnetauth2->getCustomerIdByAuthCode( 
				$_COOKIE['socnetauth2_authcode'] 
			)
		)
		{
			$this->session->data['customer_id'] = $customer_id;
			setcookie('socnetauth2_authcode', '', time()+3600, '/');
				
			header("Location: ".$this->request->server['REQUEST_URI']);
			exit();
		}
		
		$this->model_extension_module_socnetauth2->clearAuthCode();
		
		$SOCNETAUTH2_DATA = array();
		$SOCNETAUTH2_DATA['code'] = '';
		
		if( !$this->config->get('module_socnetauth2_status') ) return $SOCNETAUTH2_DATA;
		if( $this->customer->isLogged() ) return $SOCNETAUTH2_DATA;
		
		$STR = 'account';
		
		if( !empty($this->request->get['route']) )
		{
			if( $this->request->get['route'] == 'checkout/login' ) $STR = 'checkout';
			elseif( $this->request->get['route'] == 'account/register' ) $STR = 'reg';
			elseif( $this->request->get['route'] == 'checkout/simplecheckout' ) $STR = 'checkout';
			elseif( $this->request->get['route'] == 'account/simpleregister' ) $STR = 'reg';
			elseif( strstr($this->request->get['route'], 'checkout' ) ) $STR = 'checkout';
			elseif( strstr($this->request->get['route'], 'fastorder' ) ) $STR = 'checkout';
		}
		
		if( !empty( $ar['str'] ) )
		{
			$STR = $ar['str'];
		}
		
		$SOCNETAUTH2_DATA['format'] = $this->config->get('module_socnetauth2_'.$STR.'_format');
		
		if( !empty($ar['format']) ) $SOCNETAUTH2_DATA['format'] = $ar['format'];
		
		if( !empty($this->request->get['socnetauth2close']) )
		{
			$this->setParam('socnetauth2_confirmdata_show', 0);
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
		/* start 2106 */
		if( !strstr($this->request->server['REQUEST_URI'], 'common/cart/info') 
			&& 
			!strstr($this->request->server['REQUEST_URI'], 'revolution/revpopuplogin') 
		)
		{
			$lastlink = $http.$this->request->server['HTTP_HOST'].$this->request->server['REQUEST_URI'];
			$lastlink = str_replace("checkout/login", "checkout/checkout", $lastlink);
			$lastlink = str_replace("&socnetauth2close=1", "", $lastlink);
			$lastlink = str_replace("?socnetauth2close=1", "", $lastlink);
			
			$this->setParam('socnetauth2_lastlink', $lastlink);
			/* start 3004 */
			setcookie("socnetauth2_lastlink", $lastlink, time()+3600, "/" );
			/* end 3004 */
		}
		
		$this->load->model('localisation/language');
		$lang = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		$this->setParam('socnetauth2_lastlang', $lang['code']);

		
		if( !empty($ar['dobor_only']) )
		{
			if( strstr($lastlink, "?") )
				$lastlink .= '&socnetauth2close=1';
			else
				$lastlink .= '?socnetauth2close=1';
				
			$tmp_data = array(
				"lastlink"        => isset($this->request->get['lastlink']) ? urldecode($this->request->get['lastlink']) : '',
				"lastroute"        => isset($this->request->get['route']) ? urldecode($this->request->get['route']) : '',
			);
			
			$requisites = $this->model_extension_module_socnetauth2->getRequisites();
			
			if( $this->config->get('module_socnetauth2_telegram_status') && 
				$this->config->get('module_socnetauth2_telegram_bot_username') && 
				$this->config->get('module_socnetauth2_telegram_bot_token')
			)
			{ 
				$tmp_data['telegram_code'] = $this->config->get('module_socnetauth2_telegram_code');
				
				$socnetauth2_shop_folder = '';
				if( $this->config->get('module_socnetauth2_shop_folder') )
					$socnetauth2_shop_folder .= '/'.$this->config->get('module_socnetauth2_shop_folder');
				
				$redirect_url = $http.$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.'/sna-telegram';
				
				$tmp_data['telegram_code'] = str_replace("{redirect_url}", $redirect_url, $tmp_data['telegram_code']);
				
				$tmp_data['telegram_code'] = str_replace("{bot_username}", $requisites['socnetauth2_telegram_bot_username'], $tmp_data['telegram_code']);
			}
			else
				$tmp_data['telegram_code'] = '';
			
			$socnetauth2_confirm_block = $this->load->view('extension/module/socnetauth2/socnetauth2_confirm_js', $tmp_data);
			 
			if( !$this->config->get('config_maintenance') || !empty($this->session->data['user_id']) )
			$SOCNETAUTH2_DATA['code'] .= $socnetauth2_confirm_block;
		}
		
		$this->load->model('extension/module/socnetauth2');
		
		if( empty($ar['dobor_only']) && 
			$this->config->get('module_socnetauth2_design_'.$STR.'_status' ) )
		{
			$socnetauth2_code = $this->config->get('module_socnetauth2_'.$STR.'_code_'.$SOCNETAUTH2_DATA['format']);
			
			$socnetauth2_code = $this->custom_unserialize($socnetauth2_code);
			
			if( !empty($socnetauth2_code[ $this->config->get('config_language_id') ]) )
			{
				$socnetauth2_code = $socnetauth2_code[ $this->config->get('config_language_id') ];
				
				$socnetauth2_shop_folder = '';
				if( $this->config->get('module_socnetauth2_shop_folder') )
					$socnetauth2_shop_folder .= '/'.$this->config->get('module_socnetauth2_shop_folder');
				
				if( $this->config->get('module_socnetauth2_shop_folders') && 
					$this->config->get('config_store_id') )
				{
					$socnetauth2_shop_folders = $this->custom_unserialize( $this->config->get('module_socnetauth2_shop_folders') );
					if( !empty($socnetauth2_shop_folders[$this->config->get('config_store_id')]) )
						$socnetauth2_shop_folder = '/'.$socnetauth2_shop_folders[$this->config->get('config_store_id')];
					else
						$socnetauth2_shop_folder = '';
				}
				
				$socnetauth2_code = str_replace("{shop_folder}", $socnetauth2_shop_folder, $socnetauth2_code);
					
				/* start 0305 */
				$link = '';
				
				if( !file_exists( preg_replace("/system\/?$/", "", DIR_SYSTEM).'socnetauth2/lib/socnetauth2.php' ) )
					$link = '/sna-loginza';
				else
					$link = '/socnetauth2/loginza.php';
			
				
				$redirect_url = '';
				if( $this->config->get('module_socnetauth2_shop_folder_in_redirect') )
					$redirect_url = $http.$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.$link;
				else
					$redirect_url = $http.$this->request->server['HTTP_HOST'].$link;
				/* end 0305 */
				
				$redirect_url = urlencode($redirect_url);
				
				$rand = rand(1,1000000000);
				$socnetauth2_code = str_replace("{rand}", $rand, $socnetauth2_code);
				
				$this->model_extension_module_socnetauth2->setStartCookie('telegram', $STR);
				
				$lang = 'ru'; 
				
				if( isset($this->request->cookie['language']) )
				{
					if( strstr($this->request->cookie['language'], '-') )
					{
						$ar = explode("-", $this->request->cookie['language']);
						$lang = $ar[0];
					}
					else
					{
						$lang = $this->request->cookie['language']; 
					} 
				}
				
				$socnetauth2_code = str_replace("{lang}", $lang, $socnetauth2_code);
				
				
				$socnetauth2_code = str_replace("#redirect_url#", $redirect_url, $socnetauth2_code);
				
				$SOCNETAUTH2_DATA['code'] .= $socnetauth2_code;
			}
		}
		 
		$socnets = $this->model_extension_module_socnetauth2->getSocnets();
		foreach( $socnets as $socnet )
		{
			if( $socnet['short'] == 'od' )
				$socnet['short'] = 'ok';	
			
			if( strstr($SOCNETAUTH2_DATA['code'], "{title_".$socnet['short']."}") )
			{
				$title = $this->custom_unserialize( $this->config->get('module_socnetauth2_'.$socnet['key'].'_title') );
				
				if( isset( $title[ $this->config->get('config_language_id') ] ) )
					$title = $title[ $this->config->get('config_language_id') ]; 
				else
					$title = '';
				
				$SOCNETAUTH2_DATA['code'] = str_replace(
					"{title_".$socnet['short']."}", 
					$title,
					$SOCNETAUTH2_DATA['code']
				);
			}
		}
		/* end 2408 */
		
		$SOCNETAUTH2_DATA['code'] = str_replace("first=1", "first=".$STR, $SOCNETAUTH2_DATA['code']); 
		
		$this->load->model('tool/image');
		$ar = array();
		preg_match_all("/\{img([^\}]+)\}/", $SOCNETAUTH2_DATA['code'], $ar);
		
		
		
		foreach($ar[1] as $i=>$img_tpl)
		{
			$ar2 = explode("|", $img_tpl);
			$img = $this->model_tool_image->resize( $ar2[1], $ar2[2], $ar2[3] );
			
			$SOCNETAUTH2_DATA['code'] = str_replace($ar[0][$i], $img, $SOCNETAUTH2_DATA['code']);
		}
		
		
		$SOCNETAUTH2_DATA['code'] = html_entity_decode($SOCNETAUTH2_DATA['code'], 
													   ENT_QUOTES, 'UTF-8');
			
		return $SOCNETAUTH2_DATA;
	}
	
	public function showcode2($ar=array())
	{
		return $this->showcode2($ar);
	}
	 
	public function confirmform($data = array() )
	{
		$this->response->setOutput( $this->getConfirmform($data) );
	}
	
	private function getConfirmform($data = array() )
	{
		$this->data['is_noiframe'] = $this->config->get('module_socnetauth2_is_noiframe');
			
		$this->language->load('extension/module/socnetauth2');
		$this->data['confirmform_header'] = $this->language->get('confirmform_header');
		$this->data['confirmform_entry_code'] = $this->language->get('confirmform_entry_code');
		$this->data['confirmform_message'] = $this->language->get('confirmform_message');
		$this->data['confirmform_button'] = $this->language->get('confirmform_button');
		
		
		$this->data['action'] = $this->url->link('extension/module/socnetauth2/confirmation', '', 'SSL');
		
		$this->data['error_code'] = '';
		
		if( !empty( $this->error['error_code'] ) )
		$this->data['error_code'] = $this->error['error_code'];
		
		if( !empty($this->request->post['data']) )
		{
			$this->data['data'] = $this->request->post['data'];
		}
		else
		{
			$this->data['data'] = $data;
		}
		
		foreach($this->data['data'] as $key=>$val)
		{
			$this->data['data'][ $key ] = str_replace("'", "\'", $this->data['data'][ $key ]);
			$this->data['data'][ $key ] = preg_replace("/[\\\]+\'/", "\'", $this->data['data'][ $key ]);
		}
		
		if(  version_compare(VERSION, '2.2.0.0') >= 0 )
		{
			return $this->load->view('extension/module/socnetauth2/socnetauth2_frame_confirmform', $this->data);
		}
		else
		{
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame_confirmform.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/extension/module/socnetauth2/socnetauth2_frame_confirmform.tpl', $this->data);
			} else {
				return $this->load->view('default/template/extension/module/socnetauth2/socnetauth2_frame_confirmform.tpl', $this->data);
			}
		}
	}
	
	public function confirmation()
	{
		$this->language->load('extension/module/socnetauth2');
		$this->load->model('extension/module/socnetauth2');
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateConfirm() )
		{
			$socnetauth_data = $this->getParam('socnetauth2_confirmdata');
			$this->redirect_url = !empty( $socnetauth_data['redirect_url'] ) ? $socnetauth_data['redirect_url'] : '';
			
			/* start 0609 */
			$this->request->post['data']['email'] = $this->getParam('controlled_email');
			$this->setParam('socnetauth2_confirmdata', '');
			//$this->setParam('controlled_email', '');
			/* end 0609 */
			
			
			$customer_id = $this->model_extension_module_socnetauth2->addCustomerAfterConfirm( $this->request->post['data'] );
			
			if( $customer_id && $this->getParam('socnetauth2_confirmdata_rawdata') )
			{
				$this->model_extension_module_socnetauth2->addCustomerComment( 
					$customer_id, 
					$this->request->post['data']['provider'], 
					$this->getParam('socnetauth2_confirmdata_rawdata')
				);
			}
			
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer( $customer_id );
			
			if( !empty($customer_info['status']) )
				$this->session->data['customer_id'] = $customer_id;
			 
			$this->success($customer_id);
			
			return;
		}
		
		$this->confirmform();
	}
	
  	private function validateConfirm() 
	{
    	if( empty($this->request->post['code']) )
		{
			$this->error['error_code'] = $this->language->get('error_code_empty');
		}
		elseif( !$this->model_extension_module_socnetauth2->checkConfirmCode( 
					$this->request->post['data']['identity'], 
					$this->request->post['code'] ) )
		{
			$this->error['error_code'] = $this->language->get('error_code_invalid');
		}
		elseif( !$this->getParam('controlled_email') )
		{
		//	exit('error_email');
		}
		
		if( $this->error ) return false;
		else return true;
	}
	
	
	private function getCookie($key)
	{
		if( !empty($this->request->cookie[$key]) )
		{
			return $this->custom_unserialize( 
				html_entity_decode($this->request->cookie[ $key ])
			);
		}
	}
	
	private function getParam($key)
	{
		if( !empty($this->session->data[ $key ]) )
		{
			return $this->custom_unserialize( $this->session->data[ $key ] );
		} 
	}
	
	private function setParam($key, $value)
	{
		$this->session->data[ $key ] = $value;
		// $this->request->cookie[ $key ] = '';
		#setcookie($key, $value, time()+3600, '/');
	}
	
	/* end 0105 */
}
?>