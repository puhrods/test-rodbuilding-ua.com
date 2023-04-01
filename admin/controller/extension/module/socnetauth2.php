<?php
class ControllerExtensionModuleSocnetauth2 extends Controller {
	private $error = array(); 
	private $data = array(); 
	
	
	public function install()
	{
		$this->load->model('extension/module/socnetauth2');
		$this->model_extension_module_socnetauth2->addFields();
	}
	
	public function uninstall()
	{
		$this->load->model('setting/extension');
		$this->model_setting_extension->uninstall('module', 'socnetauth2_popup');
	}
	
	public function index() {   
		$this->load->language('extension/module/socnetauth2');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/socnetauth2');
		 
		$this->model_extension_module_socnetauth2->checkDB(); 
		
		$this->model_extension_module_socnetauth2->addFields();
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			foreach($this->request->post as $key=>$val)
			{
				if( is_array($val) && $key != 'socnetauth2_module' && $key != 'socnetauth2_popup_module'  )
				{
					foreach($val as $k=>$v)
					{
						$this->request->post[$key][$k] = $v;
					}
					
					$this->request->post[$key] = serialize($this->request->post[$key]);
				}
				
				if(  !is_array($this->request->post[$key]) )
					$this->request->post[$key] = trim($this->request->post[$key]);
			}
			
			$this->request->post['socnetauth2_vkontakte_appid'] 	 		 = trim( $this->request->post['socnetauth2_vkontakte_appid'] );
			$this->request->post['socnetauth2_vkontakte_appsecret'] 		 = trim( $this->request->post['socnetauth2_vkontakte_appsecret'] );
			$this->request->post['socnetauth2_facebook_appid'] 				 = trim( $this->request->post['socnetauth2_facebook_appid'] );
			$this->request->post['socnetauth2_facebook_appsecret'] 			 = trim( $this->request->post['socnetauth2_facebook_appsecret'] );
			$this->request->post['socnetauth2_twitter_consumer_key'] 		 = trim( $this->request->post['socnetauth2_twitter_consumer_key'] );
			$this->request->post['socnetauth2_twitter_consumer_secret'] 	 = trim( $this->request->post['socnetauth2_twitter_consumer_secret'] );
			$this->request->post['socnetauth2_odnoklassniki_application_id'] = trim( $this->request->post['socnetauth2_odnoklassniki_application_id'] );
			$this->request->post['socnetauth2_odnoklassniki_public_key'] 	 = trim( $this->request->post['socnetauth2_odnoklassniki_public_key'] );
			$this->request->post['socnetauth2_odnoklassniki_secret_key']  	 = trim( $this->request->post['socnetauth2_odnoklassniki_secret_key'] );
			$this->request->post['socnetauth2_gmail_client_id'] 			 = trim( $this->request->post['socnetauth2_gmail_client_id'] );
			$this->request->post['socnetauth2_gmail_client_secret'] 		 = trim( $this->request->post['socnetauth2_gmail_client_secret'] );
			$this->request->post['socnetauth2_mailru_id'] 					 = trim( $this->request->post['socnetauth2_mailru_id'] );
			$this->request->post['socnetauth2_mailru_private'] 				 = trim( $this->request->post['socnetauth2_mailru_private'] );
			$this->request->post['socnetauth2_mailru_secret'] 				 = trim( $this->request->post['socnetauth2_mailru_secret'] );
			$this->request->post['socnetauth2_steam_api_key'] 				 = trim( $this->request->post['socnetauth2_steam_api_key'] );
			$this->request->post['socnetauth2_yandex_client_id'] 				 = trim( $this->request->post['socnetauth2_yandex_client_id'] );
			$this->request->post['socnetauth2_yandex_client_secret'] 				 = trim( $this->request->post['socnetauth2_yandex_client_secret'] );
			
			foreach($this->request->post as $key=>$val)
			{
				if( preg_match("/^socnetauth2/", $key) )
				{
					$this->request->post[ 'module_'.$key ] = $val;
					unset($this->request->post[ $key ]);
				}
			}
			
			$this->model_setting_setting->editSetting('module_socnetauth2', $this->request->post);		
			
			
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			if( !empty($this->request->post['stay']) )
			{
				$tab = 'link-tab-general';
				
				if( !empty($this->request->post['tab']) )
				{
					$tab = $this->request->post['tab'];
				}
				
				$tab2 = 'general';
				
				if( !empty($this->request->post['tab2']) )
				{
					$tab2 = $this->request->post['tab2'];
				}
				
				$this->response->redirect($this->url->link('extension/module/socnetauth2', 'user_token=' . $this->session->data['user_token'].'&tab='.$tab.'&tab2='.$tab2, 'SSL'));
			}
			else
			{
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL'));			
			}
		}
				
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$socnets = $this->model_extension_module_socnetauth2->getAllSocnets();
		$this->data['version'] = $this->model_extension_module_socnetauth2->getVersions();
		
		$default_hash = array();
		
		foreach($this->data['languages'] as $i=>$lang)
		{
			$this->data['languages'][$i]['image'] = 'language/'.$lang['code'].'/'.$lang['code'].'.png';
			
			$directory = $lang['code'];
				
			$Lang = new Language( $directory );
			$Lang->load('extension/module/socnetauth2');
				
			$default_hash['default_label'][ $lang['language_id'] ] = $Lang->get('default_label');
			$default_hash['default_widget_name'][ $lang['language_id'] ] = $Lang->get('default_widget_name');
			$default_hash['default_facebook_title'][ $lang['language_id'] ] = $Lang->get('default_facebook_title');
			$default_hash['default_vkontakte_title'][ $lang['language_id'] ] = $Lang->get('default_vkontakte_title');
			$default_hash['default_twitter_title'][ $lang['language_id'] ] = $Lang->get('default_twitter_title');
			$default_hash['default_odnoklassniki_title'][ $lang['language_id'] ] = $Lang->get('default_odnoklassniki_title');
			$default_hash['default_gmail_title'][ $lang['language_id'] ] = $Lang->get('default_gmail_title');
			$default_hash['default_mailru_title'][ $lang['language_id'] ] = $Lang->get('default_mailru_title');
			$default_hash['default_yandex_title'][ $lang['language_id'] ] = $Lang->get('default_yandex_title');
			$default_hash['default_steam_title'][ $lang['language_id'] ] = $Lang->get('default_steam_title');
			$default_hash['default_telegram_title'][ $lang['language_id'] ] = $Lang->get('default_telegram_title');
			$default_hash['default_tinkoff_title'][ $lang['language_id'] ] = $Lang->get('default_tinkoff_title');
			
			$default_hash['default_header_name'][ $lang['language_id'] ] = $Lang->get('default_header_name');
			$default_hash['default_header_name_auth'][ $lang['language_id'] ] = $Lang->get('default_header_name_auth');
			$default_hash['default_header_name_reg'][ $lang['language_id'] ] = $Lang->get('default_header_name_reg');
			$default_hash['default_header_name_authreg'][ $lang['language_id'] ] = $Lang->get('default_header_name_authreg');
			
			
			$default_hash['default_newcustomer_mail_text'][ $lang['language_id'] ] = $Lang->get('default_newcustomer_mail_text');
			$default_hash['default_newcustomer_mail_subject'][ $lang['language_id'] ] = $Lang->get('default_newcustomer_mail_subject'); 
		}
		
		
		
		if( isset( $this->request->post['socnetauth2_widget_name'] ) )
		{
			$this->data['socnetauth2_widget_name'] = $this->request->post['socnetauth2_widget_name'];
		}
		elseif( $this->config->has('module_socnetauth2_widget_name') )
		{
			if( !is_array($this->config->get('module_socnetauth2_widget_name')) && 
				stristr($this->config->get('module_socnetauth2_label'), '{' ) != false &&
				stristr($this->config->get('module_socnetauth2_label'), '}' ) != false &&
				stristr($this->config->get('module_socnetauth2_label'), ';' ) != false &&
				stristr($this->config->get('module_socnetauth2_label'), ':' ) != false
			)
			{
				$this->data['socnetauth2_widget_name'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_name') );
			}
			else
			$this->data['socnetauth2_widget_name'] = $default_hash['default_widget_name'];
		}
		else
		{
			
			$this->data['socnetauth2_widget_name'] = $default_hash['default_widget_name'];
		}
		
		if( !empty($this->request->get['tab']) )
		{
			$this->data['tab'] = $this->request->get['tab'];
		}
		else
		{
			$this->data['tab'] = 'link-tab-general';
		}
		
		if( !empty($this->request->get['tab2']) )
		{
			$this->data['tab2'] = $this->request->get['tab2'];
		}
		else
		{
			$this->data['tab2'] = 'general';
		}
		
		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores(); 
		
		$this->data['config_name'] = $this->config->get('config_name');
		$this->data['entry_shop_folders_col_storename'] = $this->language->get('entry_shop_folders_col_storename');
		$this->data['entry_shop_folders_col_storefolder'] = $this->language->get('entry_shop_folders_col_storefolder');
		$this->data['entry_shop_folder_in_img'] = $this->language->get('entry_shop_folder_in_img');
		
		$this->data['entry_shop_folder_in_redirect'] = $this->language->get('entry_shop_folder_in_redirect');
		$this->data['entry_shop_folder_in_url'] = $this->language->get('entry_shop_folder_in_url');
		
		if (isset($this->request->post['socnetauth2_shop_folder_in_redirect'])) 
		{
			$this->data['socnetauth2_shop_folder_in_redirect'] = $this->request->post['socnetauth2_shop_folder_in_redirect'];
		} 
		elseif( $this->config->has('module_socnetauth2_shop_folder_in_redirect') )
		{ 
			$this->data['socnetauth2_shop_folder_in_redirect'] = $this->config->get('module_socnetauth2_shop_folder_in_redirect');
		}
		else
		{
			$this->data['socnetauth2_shop_folder_in_redirect'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_shop_folder_in_url'])) 
		{
			$this->data['socnetauth2_shop_folder_in_url'] = $this->request->post['socnetauth2_shop_folder_in_url'];
		} 
		elseif( $this->config->has('module_socnetauth2_shop_folder_in_url') )
		{ 
			$this->data['socnetauth2_shop_folder_in_url'] = $this->config->get('module_socnetauth2_shop_folder_in_url');
		}
		else
		{
			$this->data['socnetauth2_shop_folder_in_url'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_shop_folder_in_img'])) 
		{
			$this->data['socnetauth2_shop_folder_in_img'] = $this->request->post['socnetauth2_shop_folder_in_img'];
		} 
		elseif( $this->config->has('module_socnetauth2_shop_folder_in_img') )
		{ 
			$this->data['socnetauth2_shop_folder_in_img'] = $this->config->get('module_socnetauth2_shop_folder_in_img');
		}
		else
		{
			$this->data['socnetauth2_shop_folder_in_img'] = '';
		}
		
		
		if (isset($this->request->post['socnetauth2_shop_folders'])) 
		{
			$this->data['socnetauth2_shop_folders'] = $this->request->post['socnetauth2_shop_folders'];
		} 
		elseif( $this->config->has('module_socnetauth2_shop_folders') )
		{ 
			$this->data['socnetauth2_shop_folders'] =  $this->custom_unserialize( 
				$this->config->get('module_socnetauth2_shop_folders')
			);
		}
		else
		{
			$this->data['socnetauth2_shop_folders'] = array();
		}
		
		if( $this->data['stores'] )
		{
			foreach($this->data['stores'] as $i=>$store)
			{
				if( !empty($this->data['socnetauth2_shop_folders'][$store['store_id']]) )
					$this->data['stores'][$i]['folder'] = $this->data['socnetauth2_shop_folders'][$store['store_id']];
				else
					$this->data['stores'][$i]['folder'] = '';
			}
		}
		
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['entry_protokol_detect'] = $this->language->get('entry_protokol_detect');
		
		$this->data['entry_show_standart_auth'] = $this->language->get('entry_show_standart_auth');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		
		$this->data['tab_design_account'] = $this->language->get('tab_design_account');
		$this->data['tab_design_checkout'] = $this->language->get('tab_design_checkout');
		$this->data['tab_design_reg'] = $this->language->get('tab_design_reg');
		$this->data['tab_design_icons'] = $this->language->get('tab_design_icons');
		$this->data['tab_design_popup'] = $this->language->get('tab_design_popup');
		
		$this->data['entry_design_css'] = $this->language->get('entry_design_css');
		$this->data['entry_design_header'] = $this->language->get('entry_design_header');
		$this->data['entry_design_format'] = $this->language->get('entry_design_format');
		$this->data['entry_design_html'] = $this->language->get('entry_design_html');
		$this->data['entry_design_html_tags'] = $this->language->get('entry_design_html_tags');
		$this->data['text_count_icons'] = $this->language->get('text_count_icons');
		$this->data['entry_design_general_css'] = $this->language->get('entry_design_general_css');
		$this->data['tab_design_general'] = $this->language->get('tab_design_general');
		$this->data['text_count_icons_notice'] = $this->language->get('text_count_icons_notice');
		$this->data['text_current_count_icons'] = $this->language->get('text_current_count_icons');
		$this->data['entry_design_htmlcode'] = $this->language->get('entry_design_htmlcode');
		$this->data['entry_design_notice'] = $this->language->get('entry_design_notice');
		$this->data['entry_design_notice_text'] = $this->language->get('entry_design_notice_text');
		$this->data['tab_design_widget'] = $this->language->get('tab_design_widget');
		$this->data['entry_widget_notice'] = $this->language->get('entry_widget_notice');
		$this->data['entry_widget_notice_text'] = $this->language->get('entry_widget_notice_text');
		$this->data['entry_design_widget_format'] = $this->language->get('entry_design_widget_format');
		
		$this->data['entry_popup_notice'] = $this->language->get('entry_popup_notice');
		$this->data['entry_popup_notice_text'] = $this->language->get('entry_popup_notice_text');
		
		$this->data['entry_mobile_control'] = $this->language->get('entry_mobile_control');
		
		$this->data['entry_is_noiframe'] = $this->language->get('entry_is_noiframe');
		$this->data['tab_design_confirm'] = $this->language->get('tab_design_confirm');
		$this->data['entry_is_noiframe_notice'] = $this->language->get('entry_is_noiframe_notice');
		
		$this->data['entry_redirect_url'] = $this->language->get('entry_redirect_url');
		$this->data['entry_redirect_url_account'] = $this->language->get('entry_redirect_url_account');
		$this->data['entry_redirect_url_account_default'] = $this->language->get('entry_redirect_url_account_default');
		$this->data['entry_redirect_url_checkout'] = $this->language->get('entry_redirect_url_checkout');
		$this->data['entry_redirect_url_checkout_default'] = $this->language->get('entry_redirect_url_checkout_default');
		$this->data['entry_redirect_url_reg'] = $this->language->get('entry_redirect_url_reg');
		$this->data['entry_redirect_url_reg_default'] = $this->language->get('entry_redirect_url_reg_default');
		$this->data['entry_redirect_url_set'] = $this->language->get('entry_redirect_url_set');
		$this->data['entry_dobor_pages'] = $this->language->get('entry_dobor_pages');
		 
		
		if (isset($this->request->post['socnetauth2_redirect_url_account'])) 
		{
			$this->data['socnetauth2_redirect_url_account'] = $this->request->post['socnetauth2_redirect_url_account'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_account') )
		{ 
			$this->data['socnetauth2_redirect_url_account'] = $this->config->get('module_socnetauth2_redirect_url_account');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_account'] = 'default';
		}
		
		if (isset($this->request->post['socnetauth2_redirect_url_account_set'])) 
		{
			$this->data['socnetauth2_redirect_url_account_set'] = $this->request->post['socnetauth2_redirect_url_account_set'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_account_set') )
		{ 
			$this->data['socnetauth2_redirect_url_account_set'] = $this->config->get('module_socnetauth2_redirect_url_account_set');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_account_set'] = '';
		}
				
				
		
		if (isset($this->request->post['socnetauth2_redirect_url_reg'])) 
		{
			$this->data['socnetauth2_redirect_url_reg'] = $this->request->post['socnetauth2_redirect_url_reg'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_reg') )
		{ 
			$this->data['socnetauth2_redirect_url_reg'] = $this->config->get('module_socnetauth2_redirect_url_reg');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_reg'] = 'default';
		}
		
		if (isset($this->request->post['socnetauth2_redirect_url_reg_set'])) 
		{
			$this->data['socnetauth2_redirect_url_reg_set'] = $this->request->post['socnetauth2_redirect_url_reg_set'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_reg_set') )
		{ 
			$this->data['socnetauth2_redirect_url_reg_set'] = $this->config->get('module_socnetauth2_redirect_url_reg_set');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_reg_set'] = '';
		}
					
				
		
		if (isset($this->request->post['socnetauth2_redirect_url_checkout'])) 
		{
			$this->data['socnetauth2_redirect_url_checkout'] = $this->request->post['socnetauth2_redirect_url_checkout'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_checkout') )
		{ 
			$this->data['socnetauth2_redirect_url_checkout'] = $this->config->get('module_socnetauth2_redirect_url_checkout');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_checkout'] = 'default';
		}
		
		if (isset($this->request->post['socnetauth2_redirect_url_checkout_set'])) 
		{
			$this->data['socnetauth2_redirect_url_checkout_set'] = $this->request->post['socnetauth2_redirect_url_checkout_set'];
		} 
		elseif( $this->config->has('module_socnetauth2_redirect_url_checkout_set') )
		{ 
			$this->data['socnetauth2_redirect_url_checkout_set'] = $this->config->get('module_socnetauth2_redirect_url_checkout_set');
		}
		else
		{
			$this->data['socnetauth2_redirect_url_checkout_set'] = '';
		}
		
		
		if (isset($this->request->post['socnetauth2_dobor_page_account'])) 
		{
			$this->data['socnetauth2_dobor_page_account'] = $this->request->post['socnetauth2_dobor_page_account'];
		} 
		elseif( $this->config->has('module_socnetauth2_dobor_page_account') )
		{ 
			$this->data['socnetauth2_dobor_page_account'] = $this->config->get('module_socnetauth2_dobor_page_account');
		}
		else
		{
			$this->data['socnetauth2_dobor_page_account'] = 1;
		}
		
		
		if (isset($this->request->post['socnetauth2_dobor_page_reg'])) 
		{
			$this->data['socnetauth2_dobor_page_reg'] = $this->request->post['socnetauth2_dobor_page_reg'];
		} 
		elseif( $this->config->has('module_socnetauth2_dobor_page_reg') )
		{ 
			$this->data['socnetauth2_dobor_page_reg'] = $this->config->get('module_socnetauth2_dobor_page_reg');
		}
		else
		{
			$this->data['socnetauth2_dobor_page_reg'] = 1;
		}
		
		
		if (isset($this->request->post['socnetauth2_dobor_page_checkout'])) 
		{
			$this->data['socnetauth2_dobor_page_checkout'] = $this->request->post['socnetauth2_dobor_page_checkout'];
		} 
		elseif( $this->config->has('module_socnetauth2_dobor_page_checkout') )
		{ 
			$this->data['socnetauth2_dobor_page_checkout'] = $this->config->get('module_socnetauth2_dobor_page_checkout');
		}
		else
		{
			$this->data['socnetauth2_dobor_page_checkout'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_is_noiframe'])) 
		{
			$this->data['socnetauth2_is_noiframe'] = $this->request->post['socnetauth2_is_noiframe'];
		} 
		elseif( $this->config->has('module_socnetauth2_is_noiframe') )
		{ 
			$this->data['socnetauth2_is_noiframe'] = $this->config->get('module_socnetauth2_is_noiframe');
		}
		else
		{
			$this->data['socnetauth2_is_noiframe'] = 0;
		}
		
		$count_icons = $this->model_extension_module_socnetauth2->getCountEnabledSocnets();
		
		$this->data['count_icons'] = 1;
		if( $count_icons )
			$this->data['count_icons'] = $count_icons;
	
		if (isset($this->request->post['socnetauth2_bline_html'][$i])) 
		{
			$this->data['socnetauth2_bline_html'] = $this->request->post['socnetauth2_bline_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_bline_html') )
		{ 
			$this->data['socnetauth2_bline_html'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_bline_html') );
		}
			
		if (isset($this->request->post['socnetauth2_lline_html'][$i])) 
		{
			$this->data['socnetauth2_lline_html'] = $this->request->post['socnetauth2_lline_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_lline_html') )
		{ 
			$this->data['socnetauth2_lline_html'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_lline_html') );
		}
			
		if (isset($this->request->post['socnetauth2_kvadrat_html'][$i])) 
		{
			$this->data['socnetauth2_kvadrat_html'] = $this->request->post['socnetauth2_kvadrat_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_kvadrat_html') )
		{ 
			$this->data['socnetauth2_kvadrat_html'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_kvadrat_html') );
		}
			
		$this->data['entry_lline_html'] = array();
		$this->data['entry_bline_html'] = array(); 
		$this->data['entry_kvadrat_html'] = array(); 
		

		for($i=1; $i<=9; $i++)
		{
			$this->data['entry_lline_html'][$i] = str_replace( "{count}", $i, $this->language->get('entry_lline_html') ); 
			$this->data['entry_bline_html'][$i] = str_replace( "{count}", $i, $this->language->get('entry_bline_html') ); 
			$this->data['entry_kvadrat_html'][$i] = str_replace( "{count}", $i, $this->language->get('entry_kvadrat_html') ); 
			
			if( empty($this->data['socnetauth2_bline_html'][$i]) )
			{
				$html = "<ul class='sna_bline_ul'>\n "."\n";
				
				for($e = 1; $e<=$i; $e++)
				{
					$html .= "<li><a href='{link_".$e."}'><img src='{img_".$e."}' title='{title_".$e."}'  /></a></li>\n";
				}
				
				$html .= " </ul><br style='clear: both;'>\n";
				
				$this->data['socnetauth2_bline_html'][$i] = $html;
			}
			
			if( empty($this->data['socnetauth2_lline_html'][$i]) )
			{
				$html = "<ul class='sna_lline_ul'>"."\n";
				
				for($e = 1; $e<=$i; $e++)
				{
					$html .= "<li><a href='{link_".$e."}'><img src='{img_".$e."}' title='{title_".$e."}'  /></a></li>\n";
				}
				
				$html .= " </ul><br style='clear: both;'>\n";
				
				$this->data['socnetauth2_lline_html'][$i] = $html;
			}
			
			if( empty($this->data['socnetauth2_kvadrat_html'][$i]) )
			{
				$html = "<table class='sna_kvadrat_table'>\n<tr class='sna_kvadrat_line1'>"."\n";
				
				for($e = 1; $e<=$i; $e++)
				{
					$html .= "<td><a href='{link_".$e."}'><img src='{img_".$e."}' title='{title_".$e."}'  /></a></td>\n";
					
					if( ceil( $i / 2 ) == $e && $e!=$i )
					{
						$html .= "</tr><tr class='sna_kvadrat_line2'>\n";
					}
				}
				
				if( $i / 2 > floor($i / 2) )
				{
					$html .= "<td>&nbsp;</td>\n";
				}
				
				$html .= "</tr>\n</table>";
				
				$this->data['socnetauth2_kvadrat_html'][$i] = $html;
			}
		}
		
		// ---
		
		
		
		if (isset($this->request->post['socnetauth2_design_general_css'])) 
		{
			$this->data['socnetauth2_design_general_css'] = $this->request->post['socnetauth2_design_general_css'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_general_css') )
		{ 
			$this->data['socnetauth2_design_general_css'] = $this->config->get('module_socnetauth2_design_general_css');
		}
		else
		{
			
			$this->data['socnetauth2_design_general_css'] = '.sna_bline_table td a:hover img { opacity: 1;padding-right: 10px; }'."\n".
			'.sna_bline_table td a:hover img { opacity: 0.7;padding-right: 10px; }'."\n".
			'.sna_bline_ul li a:hover img { opacity: 1; }'."\n".
			'.sna_bline_ul li a img { opacity: 0.7; }'
			;
		}
		
		if (isset($this->request->post['socnetauth2_mobile_control'])) 
		{
			$this->data['socnetauth2_mobile_control'] = $this->request->post['socnetauth2_mobile_control'];
		} 
		elseif( $this->config->has('module_socnetauth2_mobile_control') )
		{ 
			$this->data['socnetauth2_mobile_control'] = $this->config->get('module_socnetauth2_mobile_control');
		}
		else
		{
			$this->data['socnetauth2_mobile_control'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_design_account_status'])) 
		{
			$this->data['socnetauth2_design_account_status'] = $this->request->post['socnetauth2_design_account_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_account_status') )
		{ 
			$this->data['socnetauth2_design_account_status'] = $this->config->get('module_socnetauth2_design_account_status');
		}
		else
		{
			$this->data['socnetauth2_design_account_status'] = 1;
		}
		
		
		if (isset($this->request->post['socnetauth2_design_checkout_status'])) 
		{
			$this->data['socnetauth2_design_checkout_status'] = $this->request->post['socnetauth2_design_checkout_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_checkout_status') )
		{ 
			$this->data['socnetauth2_design_checkout_status'] = $this->config->get('module_socnetauth2_design_checkout_status');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_status'] = 1;
		}
		
		
		if (isset($this->request->post['socnetauth2_design_reg_status'])) 
		{
			$this->data['socnetauth2_design_reg_status'] = $this->request->post['socnetauth2_design_reg_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_reg_status') )
		{ 
			$this->data['socnetauth2_design_reg_status'] = $this->config->get('module_socnetauth2_design_reg_status');
		}
		else
		{
			$this->data['socnetauth2_design_reg_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_design_widget_status'])) 
		{
			$this->data['socnetauth2_design_reg_status'] = $this->request->post['socnetauth2_design_reg_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_reg_status') )
		{ 
			$this->data['socnetauth2_design_reg_status'] = $this->config->get('module_socnetauth2_design_reg_status');
		}
		else
		{
			$this->data['socnetauth2_design_reg_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_design_account_html'])) 
		{
			$this->data['socnetauth2_design_account_html'] = $this->request->post['socnetauth2_design_account_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_account_html') )
		{ 
			$this->data['socnetauth2_design_account_html'] = $this->config->get('module_socnetauth2_design_account_html');
		}
		else
		{
			$this->data['socnetauth2_design_account_html'] = '<style>{style}</style>'."\n".'<div class="sna_header">{header}</div>'."\n".'<div class="sna_icons">{icons}</div>';
		}
		
		
		if (isset($this->request->post['socnetauth2_design_account_css'])) 
		{
			$this->data['socnetauth2_design_account_css'] = $this->request->post['socnetauth2_design_account_css'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_account_css') )
		{ 
			$this->data['socnetauth2_design_account_css'] = $this->config->get('module_socnetauth2_design_account_css');
		}
		else
		{
			$this->data['socnetauth2_design_account_css'] = '.sna_header { font-weight: bold;  padding-top: 10px; }'."\n".".sna_icons { }";
		}
		
		if (isset($this->request->post['socnetauth2_design_account_header'])) 
		{
			$this->data['socnetauth2_design_account_header'] = $this->request->post['socnetauth2_design_account_header'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_account_header') )
		{ 
			$this->data['socnetauth2_design_account_header'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_design_account_header') );
		}
		else
		{
			$this->data['socnetauth2_design_account_header'] = $default_hash['default_header_name_auth'];
		}
		// -----
		
		
		
		if (isset($this->request->post['socnetauth2_design_widget_status'])) 
		{
			$this->data['socnetauth2_design_widget_status'] = $this->request->post['socnetauth2_design_widget_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_widget_status') )
		{ 
			$this->data['socnetauth2_design_widget_status'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_design_widget_status') );
		}
		else
		{
			$this->data['socnetauth2_design_widget_status'] = 0;
		}
		
		
		
		// --------
		
		
		if (isset($this->request->post['socnetauth2_design_checkout_html'])) 
		{
			$this->data['socnetauth2_design_checkout_html'] = $this->request->post['socnetauth2_design_checkout_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_checkout_html') )
		{ 
			$this->data['socnetauth2_design_checkout_html'] = $this->config->get('module_socnetauth2_design_checkout_html');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_html'] = '<style>{style}</style>'."\n".'<div class="sna_header">{header}</div>'."\n".'<div class="sna_icons">{icons}</div>';
		}
		
		
		if (isset($this->request->post['socnetauth2_design_checkout_css'])) 
		{
			$this->data['socnetauth2_design_checkout_css'] = $this->request->post['socnetauth2_design_checkout_css'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_checkout_css') )
		{ 
			$this->data['socnetauth2_design_checkout_css'] = $this->config->get('module_socnetauth2_design_checkout_css');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_css'] = '.sna_header { font-weight: bold; }'."\n".".sna_icons { }";
		}
		
		if (isset($this->request->post['socnetauth2_design_checkout_header'])) 
		{
			$this->data['socnetauth2_design_checkout_header'] = $this->request->post['socnetauth2_design_checkout_header'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_checkout_header') )
		{ 
			$this->data['socnetauth2_design_checkout_header'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_design_checkout_header') );
		}
		else
		{
			$this->data['socnetauth2_design_checkout_header'] = $default_hash['default_header_name_authreg'];
		}
		
		// --------
		
		
		if (isset($this->request->post['socnetauth2_design_reg_html'])) 
		{
			$this->data['socnetauth2_design_reg_html'] = $this->request->post['socnetauth2_design_reg_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_reg_html') )
		{ 
			$this->data['socnetauth2_design_reg_html'] = $this->config->get('module_socnetauth2_design_reg_html');
		}
		else
		{
			$this->data['socnetauth2_design_reg_html'] = '<style>{style}</style>'."\n".'<div class="sna_header">{header}</div>'."\n".'<div class="sna_icons">{icons}</div><br>';
		}
		
		
		if (isset($this->request->post['socnetauth2_design_reg_css'])) 
		{
			$this->data['socnetauth2_design_reg_css'] = $this->request->post['socnetauth2_design_reg_css'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_reg_css') )
		{ 
			$this->data['socnetauth2_design_reg_css'] = $this->config->get('module_socnetauth2_design_reg_css');
		}
		else
		{
			$this->data['socnetauth2_design_reg_css'] = '.sna_header { font-weight: bold; }'."\n".'.sna_icons { }';
		}
		
		if (isset($this->request->post['socnetauth2_design_reg_header'])) 
		{
			$this->data['socnetauth2_design_reg_header'] = $this->request->post['socnetauth2_design_reg_header'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_reg_header') )
		{ 
			$this->data['socnetauth2_design_reg_header'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_design_reg_header') );
		}
		else
		{
			$this->data['socnetauth2_design_reg_header'] = $default_hash['default_header_name_reg'];
		}
		
		$this->data['entry_vkontakte_agent'] = $this->language->get('entry_vkontakte_agent');
		$this->data['entry_vkontakte_agent_extension'] = $this->language->get('entry_vkontakte_agent_extension');
		$this->data['entry_vkontakte_agent_loginza'] = $this->language->get('entry_vkontakte_agent_loginza');
		$this->data['entry_vkontakte_agent_notice'] = $this->language->get('entry_vkontakte_agent_notice');

		$this->data['entry_facebook_agent'] = $this->language->get('entry_facebook_agent');
		$this->data['entry_facebook_agent_extension'] = $this->language->get('entry_facebook_agent_extension');
		$this->data['entry_facebook_agent_loginza'] = $this->language->get('entry_facebook_agent_loginza');
		$this->data['entry_facebook_agent_notice'] = $this->language->get('entry_facebook_agent_notice');

		$this->data['entry_twitter_agent'] = $this->language->get('entry_twitter_agent');
		$this->data['entry_twitter_agent_extension'] = $this->language->get('entry_twitter_agent_extension');
		$this->data['entry_twitter_agent_loginza'] = $this->language->get('entry_twitter_agent_loginza');
		$this->data['entry_twitter_agent_notice'] = $this->language->get('entry_twitter_agent_notice');

		$this->data['entry_odnoklassniki_agent'] = $this->language->get('entry_odnoklassniki_agent');
		$this->data['entry_odnoklassniki_agent_extension'] = $this->language->get('entry_odnoklassniki_agent_extension');
		$this->data['entry_odnoklassniki_agent_loginza'] = $this->language->get('entry_odnoklassniki_agent_loginza');
		$this->data['entry_odnoklassniki_agent_notice'] = $this->language->get('entry_odnoklassniki_agent_notice');

		$this->data['entry_gmail_agent'] = $this->language->get('entry_gmail_agent');
		$this->data['entry_gmail_agent_extension'] = $this->language->get('entry_gmail_agent_extension');
		$this->data['entry_gmail_agent_loginza'] = $this->language->get('entry_gmail_agent_loginza');
		$this->data['entry_gmail_agent_notice'] = $this->language->get('entry_gmail_agent_notice');

		$this->data['entry_mailru_agent'] = $this->language->get('entry_mailru_agent');
		$this->data['entry_mailru_agent_extension'] = $this->language->get('entry_mailru_agent_extension');
		$this->data['entry_mailru_agent_loginza'] = $this->language->get('entry_mailru_agent_loginza');
		$this->data['entry_mailru_agent_notice'] = $this->language->get('entry_mailru_agent_notice');

		$this->data['entry_yandex_agent'] = $this->language->get('entry_yandex_agent');
		$this->data['entry_yandex_agent_extension'] = $this->language->get('entry_yandex_agent_extension');
		$this->data['entry_yandex_agent_loginza'] = $this->language->get('entry_yandex_agent_loginza');
		$this->data['entry_yandex_agent_notice'] = $this->language->get('entry_yandex_agent_notice');

		$this->data['entry_steam_agent'] = $this->language->get('entry_steam_agent');
		$this->data['entry_steam_agent_extension'] = $this->language->get('entry_steam_agent_extension');
		$this->data['entry_steam_agent_loginza'] = $this->language->get('entry_steam_agent_loginza');
		$this->data['entry_steam_agent_notice'] = $this->language->get('entry_steam_agent_notice');

		if (isset($this->request->post['socnetauth2_vkontakte_agent'])) 
		{
			$this->data['socnetauth2_vkontakte_agent'] = $this->request->post['socnetauth2_vkontakte_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_vkontakte_agent') )
		{ 
			$this->data['socnetauth2_vkontakte_agent'] = $this->config->get('module_socnetauth2_vkontakte_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_vkontakte_agent'] = 'extension';
			else
				$this->data['socnetauth2_vkontakte_agent'] = 'loginza';
		}
		 
		if (isset($this->request->post['socnetauth2_gmail_agent'])) 
		{
			$this->data['socnetauth2_gmail_agent'] = $this->request->post['socnetauth2_gmail_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_gmail_agent') )
		{ 
			$this->data['socnetauth2_gmail_agent'] = $this->config->get('module_socnetauth2_gmail_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_gmail_agent'] = 'extension';
			else
				$this->data['socnetauth2_gmail_agent'] = 'loginza';
		}

		if (isset($this->request->post['socnetauth2_odnoklassniki_agent'])) 
		{
			$this->data['socnetauth2_odnoklassniki_agent'] = $this->request->post['socnetauth2_odnoklassniki_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_odnoklassniki_agent') )
		{ 
			$this->data['socnetauth2_odnoklassniki_agent'] = $this->config->get('module_socnetauth2_odnoklassniki_agent');
		}
		else
		{
			$this->data['socnetauth2_odnoklassniki_agent'] = 'extension';
		}
		 

		if (isset($this->request->post['socnetauth2_facebook_agent'])) 
		{
			$this->data['socnetauth2_facebook_agent'] = $this->request->post['socnetauth2_facebook_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_facebook_agent') )
		{ 
			$this->data['socnetauth2_facebook_agent'] = $this->config->get('module_socnetauth2_facebook_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_facebook_agent'] = 'extension';
			else
				$this->data['socnetauth2_facebook_agent'] = 'loginza';
		}
		 

		if (isset($this->request->post['socnetauth2_twitter_agent'])) 
		{
			$this->data['socnetauth2_twitter_agent'] = $this->request->post['socnetauth2_twitter_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_twitter_agent') )
		{ 
			$this->data['socnetauth2_twitter_agent'] = $this->config->get('module_socnetauth2_twitter_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_twitter_agent'] = 'extension';
			else
				$this->data['socnetauth2_twitter_agent'] = 'loginza';
		}
		 

		if (isset($this->request->post['socnetauth2_mailru_agent'])) 
		{
			$this->data['socnetauth2_mailru_agent'] = $this->request->post['socnetauth2_mailru_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_mailru_agent') )
		{ 
			$this->data['socnetauth2_mailru_agent'] = $this->config->get('module_socnetauth2_mailru_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_mailru_agent'] = 'extension';
			else
				$this->data['socnetauth2_mailru_agent'] = 'loginza';
		}
		 

		if (isset($this->request->post['socnetauth2_yandex_agent'])) 
		{
			$this->data['socnetauth2_yandex_agent'] = $this->request->post['socnetauth2_yandex_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_yandex_agent') )
		{ 
			$this->data['socnetauth2_yandex_agent'] = $this->config->get('module_socnetauth2_yandex_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_yandex_agent'] = 'extension';
			else
				$this->data['socnetauth2_yandex_agent'] = 'loginza';
		}
		 

		if (isset($this->request->post['socnetauth2_steam_agent'])) 
		{
			$this->data['socnetauth2_steam_agent'] = $this->request->post['socnetauth2_steam_agent'];
		} 
		elseif( $this->config->has('module_socnetauth2_steam_agent') )
		{ 
			$this->data['socnetauth2_steam_agent'] = $this->config->get('module_socnetauth2_steam_agent');
		}
		else
		{
			if( $this->config->has('module_socnetauth2_status') )
				$this->data['socnetauth2_steam_agent'] = 'extension';
			else
				$this->data['socnetauth2_steam_agent'] = 'loginza';
		}
		$this->data['text_recomendation_code'] = $this->language->get('text_recomendation_code');
		$this->data['entry_design_widget_name'] = $this->language->get('entry_design_widget_name');
		
		$this->data['entry_socnet_title'] = $this->language->get('entry_socnet_title');
		
		if (isset($this->request->post['socnetauth2_twitter_title'])) 
			$this->data['socnetauth2_twitter_title'] = $this->request->post['socnetauth2_twitter_title'];
		elseif( $this->config->has('module_socnetauth2_twitter_title') )
			$this->data['socnetauth2_twitter_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_twitter_title') );
		else
			$this->data['socnetauth2_twitter_title'] = $default_hash['default_twitter_title'];
		
		if (isset($this->request->post['socnetauth2_facebook_title'])) 
			$this->data['socnetauth2_facebook_title'] = $this->request->post['socnetauth2_facebook_title'];
		elseif( $this->config->has('module_socnetauth2_facebook_title') )
			$this->data['socnetauth2_facebook_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_facebook_title'));
		else
			$this->data['socnetauth2_facebook_title'] = $default_hash['default_facebook_title'];
		
		if (isset($this->request->post['socnetauth2_vkontakte_title'])) 
			$this->data['socnetauth2_vkontakte_title'] = $this->request->post['socnetauth2_vkontakte_title'];
		elseif( $this->config->has('module_socnetauth2_vkontakte_title') )
			$this->data['socnetauth2_vkontakte_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_vkontakte_title'));
		else
			$this->data['socnetauth2_vkontakte_title'] = $default_hash['default_vkontakte_title'];
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_title'])) 
			$this->data['socnetauth2_odnoklassniki_title'] = $this->request->post['socnetauth2_odnoklassniki_title'];
		elseif( $this->config->has('module_socnetauth2_odnoklassniki_title') )
			$this->data['socnetauth2_odnoklassniki_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_odnoklassniki_title'));
		else
			$this->data['socnetauth2_odnoklassniki_title'] = $default_hash['default_odnoklassniki_title'];
		
		if (isset($this->request->post['socnetauth2_gmail_title'])) 
			$this->data['socnetauth2_gmail_title'] = $this->request->post['socnetauth2_gmail_title'];
		elseif( $this->config->has('module_socnetauth2_gmail_title') )
			$this->data['socnetauth2_gmail_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_gmail_title'));
		else
			$this->data['socnetauth2_gmail_title'] = $default_hash['default_gmail_title'];
		
		if (isset($this->request->post['socnetauth2_mailru_title'])) 
			$this->data['socnetauth2_mailru_title'] = $this->request->post['socnetauth2_mailru_title'];
		elseif( $this->config->has('module_socnetauth2_mailru_title') )
			$this->data['socnetauth2_mailru_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_mailru_title'));
		else
			$this->data['socnetauth2_mailru_title'] = $default_hash['default_mailru_title'];
		
		if (isset($this->request->post['socnetauth2_yandex_title'])) 
			$this->data['socnetauth2_yandex_title'] = $this->request->post['socnetauth2_yandex_title'];
		elseif( $this->config->has('module_socnetauth2_yandex_title') )
			$this->data['socnetauth2_yandex_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_yandex_title'));
		else
			$this->data['socnetauth2_yandex_title'] = $default_hash['default_yandex_title'];
		
		if (isset($this->request->post['socnetauth2_steam_title'])) 
			$this->data['socnetauth2_steam_title'] = $this->request->post['socnetauth2_steam_title'];
		elseif( $this->config->has('module_socnetauth2_steam_title') )
			$this->data['socnetauth2_steam_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_steam_title') );
		else
			$this->data['socnetauth2_steam_title'] = $default_hash['default_steam_title'];
		
		if (isset($this->request->post['socnetauth2_telegram_title'])) 
			$this->data['socnetauth2_telegram_title'] = $this->request->post['socnetauth2_telegram_title'];
		elseif( $this->config->has('module_socnetauth2_telegram_title') )
			$this->data['socnetauth2_telegram_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_telegram_title') );
		else
			$this->data['socnetauth2_telegram_title'] = $default_hash['default_telegram_title'];
		
		if (isset($this->request->post['socnetauth2_tinkoff_title'])) 
			$this->data['socnetauth2_tinkoff_title'] = $this->request->post['socnetauth2_tinkoff_title'];
		elseif( $this->config->has('module_socnetauth2_tinkoff_title') )
			$this->data['socnetauth2_tinkoff_title'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_tinkoff_title') );
		else
			$this->data['socnetauth2_tinkoff_title'] = $default_hash['default_tinkoff_title'];
		
		$this->data['entry_debug'] = $this->language->get('entry_debug');
		
		if (isset($this->request->post['socnetauth2_vkontakte_debug'])) 
		{
			$this->data['socnetauth2_vkontakte_debug'] = $this->request->post['socnetauth2_vkontakte_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_vkontakte_debug'] = $this->config->get('module_socnetauth2_vkontakte_debug');
		}
		
		if (isset($this->request->post['socnetauth2_facebook_debug'])) 
		{
			$this->data['socnetauth2_facebook_debug'] = $this->request->post['socnetauth2_facebook_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_facebook_debug'] = $this->config->get('module_socnetauth2_facebook_debug');
		}
		
		if (isset($this->request->post['socnetauth2_twitter_debug'])) 
		{
			$this->data['socnetauth2_twitter_debug'] = $this->request->post['socnetauth2_twitter_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_twitter_debug'] = $this->config->get('module_socnetauth2_twitter_debug');
		}
		
		if (isset($this->request->post['socnetauth2_gmail_debug'])) 
		{
			$this->data['socnetauth2_gmail_debug'] = $this->request->post['socnetauth2_gmail_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_gmail_debug'] = $this->config->get('module_socnetauth2_gmail_debug');
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_debug'])) 
		{
			$this->data['socnetauth2_odnoklassniki_debug'] = $this->request->post['socnetauth2_odnoklassniki_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_odnoklassniki_debug'] = $this->config->get('module_socnetauth2_odnoklassniki_debug');
		}
		
		if (isset($this->request->post['socnetauth2_mailru_debug'])) 
		{
			$this->data['socnetauth2_mailru_debug'] = $this->request->post['socnetauth2_mailru_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_mailru_debug'] = $this->config->get('module_socnetauth2_mailru_debug');
		}
		
		$this->data['tab_yandex'] = $this->language->get('tab_yandex');
		$this->data['entry_yandex_status'] = $this->language->get('entry_yandex_status');
		$this->data['entry_yandex'] = $this->language->get('entry_yandex');
		$this->data['entry_yandex_client_id'] = $this->language->get('entry_yandex_client_id');
		$this->data['entry_yandex_client_secret'] = $this->language->get('entry_yandex_client_secret');
		$this->data['entry_yandex_customer_group_id'] = $this->language->get('entry_yandex_customer_group_id');
		$this->data['entry_yandex_rights'] = $this->language->get('entry_yandex_rights');
		$this->data['entry_yandex_rights_email'] = $this->language->get('entry_yandex_rights_email');
		$this->data['entry_yandex_rights_info'] = $this->language->get('entry_yandex_rights_info');
		
		$this->data['tab_steam'] = $this->language->get('tab_steam');
		$this->data['entry_steam_status'] = $this->language->get('entry_steam_status');
		$this->data['entry_steam'] = $this->language->get('entry_steam');
		$this->data['entry_steam_api_key'] = $this->language->get('entry_steam_api_key');
		$this->data['entry_steam_link'] = $this->language->get('entry_steam_link');
		$this->data['entry_steam_customer_group_id'] = $this->language->get('entry_steam_customer_group_id');
		
		$this->data['entry_socnets_sort_order'] = $this->language->get('entry_socnets_sort_order');
		
		$socnets = $this->model_extension_module_socnetauth2->getAllSocnets();
			
		$socnetauth2_socnets_sort_order = array();
			
		if (isset($this->request->post['socnetauth2_socnets_sort_order'])) 
		{
			$socnetauth2_socnets_sort_order = $this->request->post['socnetauth2_socnets_sort_order'];
		} 
		elseif( $this->config->get('module_socnetauth2_socnets_sort_order') )
		{ 
			$socnetauth2_socnets_sort_order = $this->custom_unserialize( 
				$this->config->get('module_socnetauth2_socnets_sort_order')
			);
		}
		else
		{
			$i = 1;
			foreach($socnets as $key=>$socnet)
			{
				$socnetauth2_socnets_sort_order[$key] = $i;
				$i++;
			}
		}
		
		$socnetauth2_socnets_icons = array();
		
		if (isset($this->request->post['socnetauth2_socnets_icons'])) {
			$socnetauth2_socnets_icons = $this->request->post['socnetauth2_socnets_icons'];
		} elseif( $this->config->get('module_socnetauth2_socnets_icons') ) { 
			$socnetauth2_socnets_icons = $this->custom_unserialize( $this->config->get('module_socnetauth2_socnets_icons') );
		} else {
			$socnetauth2_socnets_icons = array();
		}
		
		$this->data['entry_socnets_icons'] = $this->language->get('entry_socnets_icons');
		$this->data['entry_socnets_icons_name'] = $this->language->get('entry_socnets_icons_name');
		$this->data['entry_socnets_icons_image'] = $this->language->get('entry_socnets_icons_image'); 
		$this->data['entry_socnets_icons_sort_order'] = $this->language->get('entry_socnets_icons_sort_order');
		$this->data['entry_design_row_html'] = $this->language->get('entry_design_row_html');
		$this->data['entry_design_html_row_tags'] = $this->language->get('entry_design_html_row_tags');
		
		
		$this->data['entry_checkout_imagesize'] = $this->language->get('entry_checkout_imagesize');
		$this->data['entry_reg_imagesize'] = $this->language->get('entry_reg_imagesize');
		$this->data['entry_account_imagesize'] = $this->language->get('entry_account_imagesize');

		$this->load->model('tool/image');
		$this->data['socnetauth2_socnets_icons'] = array();
		
		foreach($socnets as $i=>$socnet)
		{ 
			$sort_order = 0; 
			
			if( isset($socnetauth2_socnets_icons[$socnet['key']]['sort_order']) )
				$sort_order = $socnetauth2_socnets_icons[$socnet['key']]['sort_order'];
			elseif( isset($socnetauth2_socnets_sort_order[$socnet['key']]) )
				$sort_order = $socnetauth2_socnets_sort_order[$socnet['key']]; 
			
			$dir_image = DIR_IMAGE.'catalog/socnetauth2_icons/';
			
			$thumb = '';
			$image = '';
			if( 
				isset($socnetauth2_socnets_icons[$socnet['key']]['image']) && 
				is_file(DIR_IMAGE . $socnetauth2_socnets_icons[$socnet['key']]['image']) 
			)
			{
				$dir_image = DIR_IMAGE;
				$image = $socnetauth2_socnets_icons[$socnet['key']]['image'];
			} 
			elseif( 
				is_file($dir_image . $socnet['short'].'.png') 
			)
			{
				$image = 'catalog/socnetauth2_icons/'.$socnet['short'].'.png';
			}
			 
			if( $image )
				$thumb = $this->model_tool_image->resize( $image, 70, 70);
			
			
			
			$this->data['socnetauth2_socnets_icons'][$socnet['key']] = array(
				"name" 			=> $socnet['name'],
				"sort_order" 	=> $sort_order, 
				"image"         => $image,
				"thumb"         => $thumb,
				"key"         => $socnet['key']
			);
		}
		
		usort($this->data['socnetauth2_socnets_icons'], array($this, "cmp"));
		
		if (isset($this->request->post['socnetauth2_checkout_imagesize_width'])) 
		{
			$this->data['socnetauth2_checkout_imagesize_width'] = $this->request->post['socnetauth2_checkout_imagesize_width'];
		} 
		elseif( $this->config->get('module_socnetauth2_checkout_imagesize_width') )
		{ 
			$this->data['socnetauth2_checkout_imagesize_width'] = $this->config->get('module_socnetauth2_checkout_imagesize_width');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_checkout_format') == 'lline' )
				$this->data['socnetauth2_checkout_imagesize_width'] = 16;
			else
				$this->data['socnetauth2_checkout_imagesize_width'] = 45;
		}
		
		if (isset($this->request->post['socnetauth2_checkout_imagesize_height'])) 
		{
			$this->data['socnetauth2_checkout_imagesize_height'] = $this->request->post['socnetauth2_checkout_imagesize_height'];
		} 
		elseif( $this->config->get('module_socnetauth2_checkout_imagesize_height') )
		{ 
			$this->data['socnetauth2_checkout_imagesize_height'] = $this->config->get('module_socnetauth2_checkout_imagesize_height');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_checkout_format') == 'lline' )
				$this->data['socnetauth2_checkout_imagesize_height'] = 16;
			else
				$this->data['socnetauth2_checkout_imagesize_height'] = 45;
		}
		
		// ---
		
		if (isset($this->request->post['socnetauth2_account_imagesize_width'])) 
		{
			$this->data['socnetauth2_account_imagesize_width'] = $this->request->post['socnetauth2_account_imagesize_width'];
		} 
		elseif( $this->config->get('module_socnetauth2_account_imagesize_width') )
		{ 
			$this->data['socnetauth2_account_imagesize_width'] = $this->config->get('module_socnetauth2_account_imagesize_width');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_account_format') == 'lline' )
				$this->data['socnetauth2_account_imagesize_width'] = 16;
			else
				$this->data['socnetauth2_account_imagesize_width'] = 45;
		}
		
		if (isset($this->request->post['socnetauth2_account_imagesize_height'])) 
		{
			$this->data['socnetauth2_account_imagesize_height'] = $this->request->post['socnetauth2_account_imagesize_height'];
		} 
		elseif( $this->config->get('module_socnetauth2_account_imagesize_height') )
		{ 
			$this->data['socnetauth2_account_imagesize_height'] = $this->config->get('module_socnetauth2_account_imagesize_height');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_account_format') == 'lline' )
				$this->data['socnetauth2_account_imagesize_height'] = 16;
			else
				$this->data['socnetauth2_account_imagesize_height'] = 45;
		}
		
		// ----
		
		
		if (isset($this->request->post['socnetauth2_widget_imagesize_width'])) 
		{
			$this->data['socnetauth2_widget_imagesize_width'] = $this->request->post['socnetauth2_widget_imagesize_width'];
		} 
		elseif( $this->config->get('module_socnetauth2_widget_imagesize_width') )
		{ 
			$this->data['socnetauth2_widget_imagesize_width'] = $this->config->get('module_socnetauth2_widget_imagesize_width');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_widget_format') == 'lline' )
				$this->data['socnetauth2_widget_imagesize_width'] = 16;
			else
				$this->data['socnetauth2_widget_imagesize_width'] = 45;
		}
		
		if (isset($this->request->post['socnetauth2_widget_imagesize_height'])) 
		{
			$this->data['socnetauth2_widget_imagesize_height'] = $this->request->post['socnetauth2_widget_imagesize_height'];
		} 
		elseif( $this->config->get('module_socnetauth2_widget_imagesize_height') )
		{ 
			$this->data['socnetauth2_widget_imagesize_height'] = $this->config->get('module_socnetauth2_widget_imagesize_height');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_widget_format') == 'lline' )
				$this->data['socnetauth2_widget_imagesize_height'] = 16;
			else
				$this->data['socnetauth2_widget_imagesize_height'] = 45;
		}
		
		if (isset($this->request->post['socnetauth2_reg_imagesize_width'])) 
		{
			$this->data['socnetauth2_reg_imagesize_width'] = $this->request->post['socnetauth2_reg_imagesize_width'];
		} 
		elseif( $this->config->get('module_socnetauth2_reg_imagesize_width') )
		{ 
			$this->data['socnetauth2_reg_imagesize_width'] = $this->config->get('module_socnetauth2_reg_imagesize_width');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_reg_format') == 'lline' )
				$this->data['socnetauth2_reg_imagesize_width'] = 16;
			else
				$this->data['socnetauth2_reg_imagesize_width'] = 45;
		}
		
		if (isset($this->request->post['socnetauth2_reg_imagesize_height'])) 
		{
			$this->data['socnetauth2_reg_imagesize_height'] = $this->request->post['socnetauth2_reg_imagesize_height'];
		} 
		elseif( $this->config->get('module_socnetauth2_reg_imagesize_height') )
		{ 
			$this->data['socnetauth2_reg_imagesize_height'] = $this->config->get('module_socnetauth2_reg_imagesize_height');
		}
		else
		{
			if( $this->config->get('module_socnetauth2_reg_format') == 'lline' )
				$this->data['socnetauth2_reg_imagesize_height'] = 16;
			else
				$this->data['socnetauth2_reg_imagesize_height'] = 45;
		}
		
		// --
		
		if (isset($this->request->post['socnetauth2_design_reg_bline_html'])) 
		{
			$this->data['socnetauth2_design_reg_bline_html'] = $this->request->post['socnetauth2_design_reg_bline_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_reg_bline_html') )
		{ 
			$this->data['socnetauth2_design_reg_bline_html'] = $this->config->get('module_socnetauth2_design_reg_bline_html');
		}
		else
		{
			$this->data['socnetauth2_design_reg_bline_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><ul class=\'sna_bline_ul\' style="padding-left: 0px;">{rows}</ul><br style=\'clear: both;\'></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_reg_bline_row_html'])) 
		{
			$this->data['socnetauth2_design_reg_bline_row_html'] = $this->request->post['socnetauth2_design_reg_bline_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_reg_bline_row_html') )
		{ 
			$this->data['socnetauth2_design_reg_bline_row_html'] = $this->config->get('module_socnetauth2_design_reg_bline_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_reg_bline_row_html'] = '<li style="padding-right: 10px; padding-bottom: 10px; float: left; list-style: none;"><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></li>';
		}	
			
		
		if (isset($this->request->post['socnetauth2_design_reg_kvadrat_html'])) 
		{
			$this->data['socnetauth2_design_reg_kvadrat_html'] = $this->request->post['socnetauth2_design_reg_kvadrat_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_reg_kvadrat_html') )
		{ 
			$this->data['socnetauth2_design_reg_kvadrat_html'] = $this->config->get('module_socnetauth2_design_reg_kvadrat_html');
		}
		else
		{
			$this->data['socnetauth2_design_reg_kvadrat_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><table class=\'sna_kvadrat_table\'>
<tr class=\'sna_kvadrat_line1\'>{rows}</tr>
<tr class=\'sna_kvadrat_line2\'>{rows}</tr></table></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_reg_kvadrat_row_html'])) 
		{
			$this->data['socnetauth2_design_reg_kvadrat_row_html'] = $this->request->post['socnetauth2_design_reg_kvadrat_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_reg_kvadrat_row_html') )
		{ 
			$this->data['socnetauth2_design_reg_kvadrat_row_html'] = $this->config->get('module_socnetauth2_design_reg_kvadrat_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_reg_kvadrat_row_html'] = '<td><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></td>';
		}
		
		// --
		
		if (isset($this->request->post['socnetauth2_design_account_bline_html'])) 
		{
			$this->data['socnetauth2_design_account_bline_html'] = $this->request->post['socnetauth2_design_account_bline_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_account_bline_html') )
		{ 
			$this->data['socnetauth2_design_account_bline_html'] = $this->config->get('module_socnetauth2_design_account_bline_html');
		}
		else
		{
			$this->data['socnetauth2_design_account_bline_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><ul class=\'sna_bline_ul\' style="padding-left: 0px;">{rows}</ul><br style=\'clear: both;\'></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_account_bline_row_html'])) 
		{
			$this->data['socnetauth2_design_account_bline_row_html'] = $this->request->post['socnetauth2_design_account_bline_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_account_bline_row_html') )
		{ 
			$this->data['socnetauth2_design_account_bline_row_html'] = $this->config->get('module_socnetauth2_design_account_bline_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_account_bline_row_html'] = '<li style="padding-right: 10px; padding-bottom: 10px; float: left; list-style: none;"><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></li>';
		}	
			
		
		if (isset($this->request->post['socnetauth2_design_account_kvadrat_html'])) 
		{
			$this->data['socnetauth2_design_account_kvadrat_html'] = $this->request->post['socnetauth2_design_account_kvadrat_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_account_kvadrat_html') )
		{ 
			$this->data['socnetauth2_design_account_kvadrat_html'] = $this->config->get('module_socnetauth2_design_account_kvadrat_html');
		}
		else
		{
			$this->data['socnetauth2_design_account_kvadrat_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><table class=\'sna_kvadrat_table\'>
<tr class=\'sna_kvadrat_line1\'>{rows}</tr>
<tr class=\'sna_kvadrat_line2\'>{rows}</tr></table></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_account_kvadrat_row_html'])) 
		{
			$this->data['socnetauth2_design_account_kvadrat_row_html'] = $this->request->post['socnetauth2_design_account_kvadrat_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_account_kvadrat_row_html') )
		{ 
			$this->data['socnetauth2_design_account_kvadrat_row_html'] = $this->config->get('module_socnetauth2_design_account_kvadrat_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_account_kvadrat_row_html'] = '<td><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></td>';
		}
		
		// --
		
		if (isset($this->request->post['socnetauth2_design_checkout_bline_html'])) 
		{
			$this->data['socnetauth2_design_checkout_bline_html'] = $this->request->post['socnetauth2_design_checkout_bline_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_checkout_bline_html') )
		{ 
			$this->data['socnetauth2_design_checkout_bline_html'] = $this->config->get('module_socnetauth2_design_checkout_bline_html');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_bline_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><ul class=\'sna_bline_ul\' style="padding-left: 0px;">{rows}</ul><br style=\'clear: both;\'></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_checkout_bline_row_html'])) 
		{
			$this->data['socnetauth2_design_checkout_bline_row_html'] = $this->request->post['socnetauth2_design_checkout_bline_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_checkout_bline_row_html') )
		{ 
			$this->data['socnetauth2_design_checkout_bline_row_html'] = $this->config->get('module_socnetauth2_design_checkout_bline_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_bline_row_html'] = '<li style="padding-right: 10px; padding-bottom: 10px; float: left; list-style: none;"><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></li>';
		}	
			
		
		if (isset($this->request->post['socnetauth2_design_checkout_kvadrat_html'])) 
		{
			$this->data['socnetauth2_design_checkout_kvadrat_html'] = $this->request->post['socnetauth2_design_checkout_kvadrat_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_checkout_kvadrat_html') )
		{ 
			$this->data['socnetauth2_design_checkout_kvadrat_html'] = $this->config->get('module_socnetauth2_design_checkout_kvadrat_html');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_kvadrat_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><table class=\'sna_kvadrat_table\'>
<tr class=\'sna_kvadrat_line1\'>{rows}</tr>
<tr class=\'sna_kvadrat_line2\'>{rows}</tr></table></div>';
		}
				
		
		if (isset($this->request->post['socnetauth2_design_checkout_kvadrat_row_html'])) 
		{
			$this->data['socnetauth2_design_checkout_kvadrat_row_html'] = $this->request->post['socnetauth2_design_checkout_kvadrat_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_checkout_kvadrat_row_html') )
		{ 
			$this->data['socnetauth2_design_checkout_kvadrat_row_html'] = $this->config->get('module_socnetauth2_design_checkout_kvadrat_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_checkout_kvadrat_row_html'] = '<td><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></td>';
		}
		
		if (isset($this->request->post['socnetauth2_steam_debug'])) 
		{
			$this->data['socnetauth2_steam_debug'] = $this->request->post['socnetauth2_steam_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_steam_debug'] = $this->config->get('module_socnetauth2_steam_debug');
		}
		
		if (isset($this->request->post['socnetauth2_steam_status'])) 
		{
			$this->data['socnetauth2_steam_status'] = $this->request->post['socnetauth2_steam_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_steam_status') )
		{ 
			$this->data['socnetauth2_steam_status'] = $this->config->get('module_socnetauth2_steam_status');
		}
		else
		{
			$this->data['socnetauth2_steam_status'] = 1;
		}
		 
		if (isset($this->request->post['socnetauth2_steam_api_key'])) 
		{
			$this->data['socnetauth2_steam_api_key'] = $this->request->post['socnetauth2_steam_api_key'];
		} 
		else
		{ 
			$this->data['socnetauth2_steam_api_key'] = $this->config->get('module_socnetauth2_steam_api_key');
		}
		
		if (isset($this->request->post['socnetauth2_steam_customer_group_id'])) {
			$this->data['socnetauth2_steam_customer_group_id'] = $this->request->post['socnetauth2_steam_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_steam_customer_group_id')) { 
			$this->data['socnetauth2_steam_customer_group_id'] = $this->config->get('module_socnetauth2_steam_customer_group_id');
		} else {
			$this->data['socnetauth2_steam_customer_group_id'] = '';
		}
		
		// ----
		
		if (isset($this->request->post['socnetauth2_yandex_debug'])) 
		{
			$this->data['socnetauth2_yandex_debug'] = $this->request->post['socnetauth2_yandex_debug'];
		} 
		else
		{ 
			$this->data['socnetauth2_yandex_debug'] = $this->config->get('module_socnetauth2_yandex_debug');
		}
		
		if (isset($this->request->post['socnetauth2_yandex_status'])) 
		{
			$this->data['socnetauth2_yandex_status'] = $this->request->post['socnetauth2_yandex_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_yandex_status') )
		{ 
			$this->data['socnetauth2_yandex_status'] = $this->config->get('module_socnetauth2_yandex_status');
		}
		else
		{
			$this->data['socnetauth2_yandex_status'] = 1;
		}
		 
		if (isset($this->request->post['socnetauth2_yandex_client_id'])) 
		{
			$this->data['socnetauth2_yandex_client_id'] = $this->request->post['socnetauth2_yandex_client_id'];
		} 
		else
		{ 
			$this->data['socnetauth2_yandex_client_id'] = $this->config->get('module_socnetauth2_yandex_client_id');
		}
		 
		if (isset($this->request->post['socnetauth2_yandex_client_secret'])) 
		{
			$this->data['socnetauth2_yandex_client_secret'] = $this->request->post['socnetauth2_yandex_client_secret'];
		} 
		else
		{ 
			$this->data['socnetauth2_yandex_client_secret'] = $this->config->get('module_socnetauth2_yandex_client_secret');
		}
		
		if (isset($this->request->post['socnetauth2_yandex_customer_group_id'])) {
			$this->data['socnetauth2_yandex_customer_group_id'] = $this->request->post['socnetauth2_yandex_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_yandex_customer_group_id')) { 
			$this->data['socnetauth2_yandex_customer_group_id'] = $this->config->get('module_socnetauth2_yandex_customer_group_id');
		} else {
			$this->data['socnetauth2_yandex_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_yandex_rights_email'])) {
			$this->data['socnetauth2_yandex_rights_email'] = $this->request->post['socnetauth2_yandex_rights_email'];
		} elseif ($this->config->get('module_socnetauth2_yandex_rights_email')) { 
			$this->data['socnetauth2_yandex_rights_email'] = $this->config->get('module_socnetauth2_yandex_rights_email');
		} else {
			$this->data['socnetauth2_yandex_rights_email'] = '';
		}
		
		
		if (isset($this->request->post['socnetauth2_yandex_rights_info'])) {
			$this->data['socnetauth2_yandex_rights_info'] = $this->request->post['socnetauth2_yandex_rights_info'];
		} elseif ($this->config->get('module_socnetauth2_yandex_rights_info')) { 
			$this->data['socnetauth2_yandex_rights_info'] = $this->config->get('module_socnetauth2_yandex_rights_info');
		} else {
			$this->data['socnetauth2_yandex_rights_info'] = '';
		}
		
		$this->data['entry_protokol'] = $this->language->get('entry_protokol');
		
		if (isset($this->request->post['socnetauth2_protokol'])) 
		{
			$this->data['socnetauth2_protokol'] = $this->request->post['socnetauth2_protokol'];
		}
		elseif( $this->config->has('module_socnetauth2_protokol') )
		{
			$this->data['socnetauth2_protokol'] = $this->config->get('module_socnetauth2_protokol');
		}
		else
		{ 
			$this->data['socnetauth2_protokol'] = 'https';
		}
		
		$this->data['text_download_link'] = $this->language->get('text_download_link');
		$this->data['entry_vkontakte_retargeting'] = $this->language->get('entry_vkontakte_retargeting');
		$this->data['entry_facebook_retargeting'] = $this->language->get('entry_facebook_retargeting');

		$this->data['vkontakte_retargeting'] = $this->url->link('extension/module/socnetauth2/vkontakte_retargeting', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$this->data['facebook_retargeting'] = $this->url->link('extension/module/socnetauth2/facebook_retargeting', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		$this->data['entry_show_source_in_order'] = $this->language->get('entry_show_source_in_order');
		$this->data['entry_show_source_in_customer'] = $this->language->get('entry_show_source_in_customer');
		
		if (isset($this->request->post['socnetauth2_show_source_in_order'])) 
		{
			$this->data['socnetauth2_show_source_in_order'] = $this->request->post['socnetauth2_show_source_in_order'];
		} 
		elseif( $this->config->has('module_socnetauth2_show_source_in_order') )
		{ 
			$this->data['socnetauth2_show_source_in_order'] = $this->config->get('module_socnetauth2_show_source_in_order');
		}
		else
		{
			$this->data['socnetauth2_show_source_in_order'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_show_source_in_customer'])) 
		{
			$this->data['socnetauth2_show_source_in_customer'] = $this->request->post['socnetauth2_show_source_in_customer'];
		} 
		elseif( $this->config->has('module_socnetauth2_show_source_in_customer') )
		{ 
			$this->data['socnetauth2_show_source_in_customer'] = $this->config->get('module_socnetauth2_show_source_in_customer');
		}
		else
		{
			$this->data['socnetauth2_show_source_in_customer'] = 1;
		}
		
		$this->data['entry_telephone_mask'] = $this->language->get('entry_telephone_mask');
		$this->data['entry_telephone_mask_notice'] = $this->language->get('entry_telephone_mask_notice');
		$this->data['entry_is_close_disabled'] = $this->language->get('entry_is_close_disabled');
		
		if (isset($this->request->post['socnetauth2_telephone_mask'])) 
		{
			$this->data['socnetauth2_telephone_mask'] = $this->request->post['socnetauth2_telephone_mask'];
		} 
		elseif(  $this->config->has('module_socnetauth2_telephone_mask') )
		{ 
			$this->data['socnetauth2_telephone_mask'] = $this->config->get('module_socnetauth2_telephone_mask');
		}
		else
		{
			$this->data['socnetauth2_telephone_mask'] = '+7 (999) 999-99-99';
		}
		
		if (isset($this->request->post['socnetauth2_is_close_disabled'])) 
		{
			$this->data['socnetauth2_is_close_disabled'] = $this->request->post['socnetauth2_is_close_disabled'];
		} 
		elseif(  $this->config->has('module_socnetauth2_is_close_disabled') )
		{ 
			$this->data['socnetauth2_is_close_disabled'] = $this->config->get('module_socnetauth2_is_close_disabled');
		}
		else
		{
			$this->data['socnetauth2_is_close_disabled'] = 0;
		}
		/* end 1409 */
		
		$this->data['text_design_row_socnetauth2_reg'] = $this->language->get('text_design_row_socnetauth2_reg');
		
		$this->data['text_format_account_page'] = $this->language->get('text_format_account_page');
		$this->data['text_format_checkout_page'] = $this->language->get('text_format_checkout_page');
		$this->data['text_format_simple_page'] = $this->language->get('text_format_simple_page');
		$this->data['text_format_simplereg_page'] = $this->language->get('text_format_simplereg_page');
		$this->data['text_format_reg_page'] = $this->language->get('text_format_reg_page');
		
		
		$this->data['text_showtype_notice'] = $this->language->get('text_showtype_notice');
		
		$this->data['entry_shop_folder'] = $this->language->get('entry_shop_folder');
		
		$this->data['tab_vkontakte'] = $this->language->get('tab_vkontakte');
		$this->data['tab_facebook'] = $this->language->get('tab_facebook');
		$this->data['tab_twitter'] = $this->language->get('tab_twitter');
		$this->data['tab_odnoklassniki'] = $this->language->get('tab_odnoklassniki');
		
		$this->data['entry_version'] = $this->language->get('entry_version');
		
		$this->data['text_debug_notice'] = $this->language->get('text_debug_notice');
		
		$this->data['entry_vkontakte_status'] = $this->language->get('entry_vkontakte_status');
		$this->data['entry_vkontakte'] = $this->language->get('entry_vkontakte');
		$this->data['entry_vkontakte_appid'] = $this->language->get('entry_vkontakte_appid');
		$this->data['entry_vkontakte_appsecret'] = $this->language->get('entry_vkontakte_appsecret');
		
		$this->data['entry_twitter_status'] = $this->language->get('entry_twitter_status');
		$this->data['entry_twitter'] = $this->language->get('entry_twitter');
		$this->data['entry_twitter_consumer_key'] = $this->language->get('entry_twitter_consumer_key');
		$this->data['entry_twitter_consumer_secret'] = $this->language->get('entry_twitter_consumer_secret');
		$this->data['entry_twitter_callback'] = $this->language->get('entry_twitter_callback');
		$this->data['entry_twitter_website'] = $this->language->get('entry_twitter_website');
		
		$this->data['entry_facebook_status'] = $this->language->get('entry_facebook_status');
		$this->data['entry_facebook'] = $this->language->get('entry_facebook');
		$this->data['entry_facebook_appid'] = $this->language->get('entry_facebook_appid');
		$this->data['entry_facebook_appsecret'] = $this->language->get('entry_facebook_appsecret');
		$this->data['entry_facebook_link'] = $this->language->get('entry_facebook_link');
		
		$this->data['entry_odnoklassniki_status'] = $this->language->get('entry_odnoklassniki_status');
		$this->data['entry_odnoklassniki'] = $this->language->get('entry_odnoklassniki');
		$this->data['entry_odnoklassniki_application_id'] = $this->language->get('entry_odnoklassniki_application_id');
		$this->data['entry_odnoklassniki_public_key'] = $this->language->get('entry_odnoklassniki_public_key');
		$this->data['entry_odnoklassniki_secret_key'] = $this->language->get('entry_odnoklassniki_secret_key');
		
		
		$this->data['entry_dobortype'] = $this->language->get('entry_dobortype');
		$this->data['entry_dobortype_one'] = $this->language->get('entry_dobortype_one');
		$this->data['entry_dobortype_every'] = $this->language->get('entry_dobortype_every');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_dobor'] = $this->language->get('tab_dobor');
		$this->data['tab_socseti'] = $this->language->get('tab_socseti');
		$this->data['tab_widget'] = $this->language->get('tab_widget');
		$this->data['tab_support'] = $this->language->get('tab_support');
		
		$this->data['tab_design'] = $this->language->get('tab_design');
		$this->data['text_design_notice2'] = $this->language->get('text_design_notice2');
		
		
		$this->data['text_design_col_element'] = $this->language->get('text_design_col_element');
		$this->data['text_design_col_file'] = $this->language->get('text_design_col_file');
		$this->data['text_design_col_comment'] = $this->language->get('text_design_col_comment');
		$this->data['text_design_row_socnetauth2_account'] = $this->language->get('text_design_row_socnetauth2_account');
		$this->data['text_design_notice'] = $this->language->get('text_design_notice');
		$this->data['text_design_row_socnetauth2_checkout'] = $this->language->get('text_design_row_socnetauth2_checkout');
		$this->data['text_design_row_socnetauth2_simple'] = $this->language->get('text_design_row_socnetauth2_simple');
		$this->data['text_design_row_socnetauth2_simplereg'] = $this->language->get('text_design_row_socnetauth2_simplereg');
		$this->data['text_design_row_socnetauth2_popup'] = $this->language->get('text_design_row_socnetauth2_popup');
		$this->data['text_design_row_socnetauth2_confirm'] = $this->language->get('text_design_row_socnetauth2_confirm');
		$this->data['text_design_row_socnetauth2_frame'] = $this->language->get('text_design_row_socnetauth2_frame');
		$this->data['text_design_row_socnetauth2_frame_success'] = $this->language->get('text_design_row_socnetauth2_frame_success');
		$this->data['text_design_row_module_socnetauth2'] = $this->language->get('text_design_row_module_socnetauth2');
		
		$this->data['entry_confirm_data'] = $this->language->get('entry_confirm_data');
		$this->data['entry_confirm_data_notice'] = $this->language->get('entry_confirm_data_notice');
		$this->data['entry_confirm_firstname'] = $this->language->get('entry_confirm_firstname');
		$this->data['entry_confirm_lastname']  = $this->language->get('entry_confirm_lastname');
		$this->data['entry_confirm_email']     = $this->language->get('entry_confirm_email');
		$this->data['entry_confirm_phone']     = $this->language->get('entry_confirm_phone');
		$this->data['text_confirm_disable']    = $this->language->get('text_confirm_disable');
		$this->data['text_confirm_none']       = $this->language->get('text_confirm_none');
		$this->data['text_confirm_allways']    = $this->language->get('text_confirm_allways');
		
		$this->data['entry_admin'] = $this->language->get('entry_admin');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['entry_widget_name'] = $this->language->get('entry_widget_name');
		
		$this->data['entry_popup_name']	= $this->language->get('entry_popup_name');
		$this->data['tab_popup']	= $this->language->get('tab_popup');
		$this->data['text_popup_notice']	= $this->language->get('text_popup_notice');
		
		$this->data['entry_format']	= $this->language->get('entry_format');
		$this->data['entry_label']	= $this->language->get('entry_label');
		
		$this->data['entry_save_to_addr']	= $this->language->get('entry_save_to_addr');
		$this->data['text_customer_addr']	= $this->language->get('text_customer_addr');
		$this->data['text_customer_only']	= $this->language->get('text_customer_only');
		
		$this->data['entry_admin_header']	= $this->language->get('entry_admin_header');
		$this->data['entry_admin_customer']	= $this->language->get('entry_admin_customer');
		$this->data['entry_admin_customer_list']	= $this->language->get('entry_admin_customer_list');
		$this->data['entry_admin_order']	= $this->language->get('entry_admin_order');
		$this->data['entry_admin_order_list']	= $this->language->get('entry_admin_order_list');
		
		$this->data['text_format_kvadrat']	= $this->language->get('text_format_kvadrat');
		$this->data['text_format_bline']	= $this->language->get('text_format_bline');
		$this->data['text_format_lline']	= $this->language->get('text_format_lline');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		
		$this->data['entry_widget_layout'] = $this->language->get('entry_widget_layout');
		$this->data['entry_widget_position'] = $this->language->get('entry_widget_position');
		$this->data['entry_widget_status'] = $this->language->get('entry_widget_status');
		$this->data['entry_widget_sort_order'] = $this->language->get('entry_widget_sort_order');
		
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_country'] = $this->language->get('text_country');
		$this->data['text_regions'] = $this->language->get('text_regions');
		
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		$this->data['entry_showtype'] = $this->language->get('entry_showtype');
		$this->data['entry_widget_showtype'] = $this->language->get('entry_widget_showtype');
		$this->data['text_showtype_window'] = $this->language->get('text_showtype_window');
		$this->data['text_showtype_redirect'] = $this->language->get('text_showtype_redirect');
		
		$this->data['text_frame'] = $this->language->get('text_frame');
		$this->data['text_contact'] = $this->language->get('text_contact');
		
		$this->data['entry_widget_after'] = $this->language->get('entry_widget_after');
		$this->data['text_widget_after_show'] = $this->language->get('text_widget_after_show');
		$this->data['text_widget_after_hide'] = $this->language->get('text_widget_after_hide');
		
		$this->data['entry_widget_format'] = $this->language->get('entry_widget_format');
		 
		$this->data['col_enable'] = $this->language->get('col_enable');
		
		$this->data['col_sort_order'] = $this->language->get('col_sort_order');
		
		$this->data['tab_gmail'] = $this->language->get('tab_gmail');
		$this->data['entry_gmail_status'] = $this->language->get('entry_gmail_status');
		$this->data['entry_gmail_client_id'] = $this->language->get('entry_gmail_client_id');
		$this->data['entry_gmail_client_secret'] = $this->language->get('entry_gmail_client_secret');
		
		
		$this->data['entry_email_auth'] = $this->language->get('entry_email_auth');
		$this->data['entry_email_auth_none'] = $this->language->get('entry_email_auth_none');
		$this->data['entry_email_auth_confirm'] = $this->language->get('entry_email_auth_confirm');
		$this->data['entry_email_auth_noconfirm'] = $this->language->get('entry_email_auth_noconfirm');
		
		$this->data['tab_mailru'] = $this->language->get('tab_mailru');
		$this->data['entry_mailru_status'] = $this->language->get('entry_mailru_status');
		$this->data['entry_mailru_id'] = $this->language->get('entry_mailru_id');
		$this->data['entry_mailru_private'] = $this->language->get('entry_mailru_private');
		$this->data['entry_mailru_secret'] = $this->language->get('entry_mailru_secret');
		
		$this->data['tab_tinkoff'] = $this->language->get('tab_tinkoff');
		$this->data['entry_tinkoff_status'] = $this->language->get('entry_tinkoff_status'); 
		$this->data['entry_tinkoff_customer_group_id'] = $this->language->get('entry_tinkoff_customer_group_id');
		$this->data['entry_tinkoff_client_id'] = $this->language->get('entry_tinkoff_client_id');
		$this->data['entry_tinkoff_client_secret'] = $this->language->get('entry_tinkoff_client_secret');
		$this->data['entry_redirect_url_tinkoff'] = $this->language->get('entry_redirect_url_tinkoff'); 
		 
		if (isset($this->request->post['socnetauth2_tinkoff_client_id'])) 
			$this->data['socnetauth2_tinkoff_client_id'] = $this->request->post['socnetauth2_tinkoff_client_id'];
		else
			$this->data['socnetauth2_tinkoff_client_id'] = $this->config->get('module_socnetauth2_tinkoff_client_id');
		
		if (isset($this->request->post['socnetauth2_tinkoff_client_secret'])) 
			$this->data['socnetauth2_tinkoff_client_secret'] = $this->request->post['socnetauth2_tinkoff_client_secret'];
		else
			$this->data['socnetauth2_tinkoff_client_secret'] = $this->config->get('module_socnetauth2_tinkoff_client_secret');
		
		if (isset($this->request->post['socnetauth2_tinkoff_customer_group_id'])) 
			$this->data['socnetauth2_tinkoff_customer_group_id'] = $this->request->post['socnetauth2_tinkoff_customer_group_id'];
		else
			$this->data['socnetauth2_tinkoff_customer_group_id'] = $this->config->get('module_socnetauth2_tinkoff_customer_group_id');
		
		if (isset($this->request->post['socnetauth2_tinkoff_status'])) 
		{
			$this->data['socnetauth2_tinkoff_status'] = $this->request->post['socnetauth2_tinkoff_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_tinkoff_status') )
		{ 
			$this->data['socnetauth2_tinkoff_status'] = $this->config->get('module_socnetauth2_tinkoff_status');
		}
		else
		{
			$this->data['socnetauth2_tinkoff_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_tinkoff_debug'])) 
		{
			$this->data['socnetauth2_tinkoff_debug'] = $this->request->post['socnetauth2_tinkoff_debug'];
		} 
		elseif( $this->config->has('module_socnetauth2_tinkoff_debug') )
		{ 
			$this->data['socnetauth2_tinkoff_debug'] = $this->config->get('module_socnetauth2_tinkoff_debug');
		}
		else
		{
			$this->data['socnetauth2_tinkoff_debug'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_tinkoff_req'])) {
			$this->data['socnetauth2_tinkoff_req'] = $this->request->post['socnetauth2_tinkoff_req'];
		} elseif( $this->config->get('module_socnetauth2_tinkoff_req') ) { 
			$this->data['socnetauth2_tinkoff_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_tinkoff_req') );
		} else {
			$this->data['socnetauth2_tinkoff_req'] = array();
		}
		
		
		$this->data['tab_telegram'] = $this->language->get('tab_telegram');
		$this->data['entry_telegram_status'] = $this->language->get('entry_telegram_status'); 
		$this->data['entry_telegram_customer_group_id'] = $this->language->get('entry_telegram_customer_group_id');
		$this->data['entry_telegram_bot_token'] = $this->language->get('entry_telegram_bot_token');
		$this->data['entry_telegram_bot_username'] = $this->language->get('entry_telegram_bot_username'); 
		$this->data['entry_redirect_url_telegram'] = $this->language->get('entry_redirect_url_telegram'); 
		$this->data['entry_telegram_html'] = $this->language->get('entry_telegram_html'); 
		
		if (isset($this->request->post['socnetauth2_telegram_bot_token'])) 
			$this->data['socnetauth2_telegram_bot_token'] = $this->request->post['socnetauth2_telegram_bot_token'];
		else
			$this->data['socnetauth2_telegram_bot_token'] = $this->config->get('module_socnetauth2_telegram_bot_token');
		
		if (isset($this->request->post['socnetauth2_telegram_bot_username'])) 
			$this->data['socnetauth2_telegram_bot_username'] = $this->request->post['socnetauth2_telegram_bot_username'];
		else
			$this->data['socnetauth2_telegram_bot_username'] = $this->config->get('module_socnetauth2_telegram_bot_username');
		
		if (isset($this->request->post['socnetauth2_telegram_customer_group_id'])) 
			$this->data['socnetauth2_telegram_customer_group_id'] = $this->request->post['socnetauth2_telegram_customer_group_id'];
		else
			$this->data['socnetauth2_telegram_customer_group_id'] = $this->config->get('module_socnetauth2_telegram_customer_group_id');
		
		if (isset($this->request->post['socnetauth2_telegram_status'])) 
		{
			$this->data['socnetauth2_telegram_status'] = $this->request->post['socnetauth2_telegram_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_telegram_status') )
		{ 
			$this->data['socnetauth2_telegram_status'] = $this->config->get('module_socnetauth2_telegram_status');
		}
		else
		{
			$this->data['socnetauth2_telegram_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_telegram_debug'])) 
		{
			$this->data['socnetauth2_telegram_debug'] = $this->request->post['socnetauth2_telegram_debug'];
		} 
		elseif( $this->config->has('module_socnetauth2_telegram_debug') )
		{ 
			$this->data['socnetauth2_telegram_debug'] = $this->config->get('module_socnetauth2_telegram_debug');
		}
		else
		{
			$this->data['socnetauth2_telegram_debug'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_telegram_req'])) {
			$this->data['socnetauth2_telegram_req'] = $this->request->post['socnetauth2_telegram_req'];
		} elseif( $this->config->get('module_socnetauth2_telegram_req') ) { 
			$this->data['socnetauth2_telegram_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_telegram_req') );
		} else {
			$this->data['socnetauth2_telegram_req'] = array();
		}
		
		
		if (isset($this->request->post['socnetauth2_telegram_html'])) 
		{
			$this->data['socnetauth2_telegram_html'] = $this->request->post['socnetauth2_telegram_html'];
		} 
		elseif( $this->config->has('module_socnetauth2_telegram_html') )
		{ 
			$this->data['socnetauth2_telegram_html'] = $this->config->get('module_socnetauth2_telegram_html');
		}
		else
		{
			$this->data['socnetauth2_telegram_html'] = '<div  id="sna_telegram_popup" class="sna_telegram_popup" style="display: none; position: absolute; top: -80px;  left: -40px;  background: #fff;  border-radius: 10px; padding: 15px; border: 1px #54a9eb solid; z-index: 999999999;"></div>';
		}
		
		$this->data['entry_telegram_code'] = $this->language->get('entry_telegram_code'); 
		
		if (isset($this->request->post['socnetauth2_telegram_code'])) 
		{
			$this->data['socnetauth2_telegram_code'] = $this->request->post['socnetauth2_telegram_code'];
		} 
		elseif( $this->config->has('module_socnetauth2_telegram_code') )
		{ 
			$this->data['socnetauth2_telegram_code'] = $this->config->get('module_socnetauth2_telegram_code');
		}
		else
		{
			$this->data['socnetauth2_telegram_code'] = '<script async src="https://telegram.org/js/telegram-widget.js?19" data-telegram-login="{bot_username}" data-size="large" data-auth-url="{redirect_url}" data-request-access="write" data-userpic="false"></script>';
		}
		
		$this->data['col_domain'] = $this->language->get('col_domain');
		$this->data['text_default_domain'] = $this->language->get('text_default_domain');
		$this->data['button_add'] = $this->language->get('button_add');
		
		if (isset($this->request->post['socnetauth2_vkontakte_req'])) {
			$this->data['socnetauth2_vkontakte_req'] = $this->request->post['socnetauth2_vkontakte_req'];
		} elseif( $this->config->get('module_socnetauth2_vkontakte_req') ) { 
			$this->data['socnetauth2_vkontakte_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_vkontakte_req') );
		} else {
			$this->data['socnetauth2_vkontakte_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_facebook_req'])) {
			$this->data['socnetauth2_facebook_req'] = $this->request->post['socnetauth2_facebook_req'];
		} elseif( $this->config->get('module_socnetauth2_facebook_req') ) { 
			$this->data['socnetauth2_facebook_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_facebook_req') );
		} else {
			$this->data['socnetauth2_facebook_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_twitter_req'])) {
			$this->data['socnetauth2_twitter_req'] = $this->request->post['socnetauth2_twitter_req'];
		} elseif( $this->config->get('module_socnetauth2_twitter_req') ) { 
			$this->data['socnetauth2_twitter_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_twitter_req') );
		} else {
			$this->data['socnetauth2_twitter_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_req'])) {
			$this->data['socnetauth2_odnoklassniki_req'] = $this->request->post['socnetauth2_odnoklassniki_req'];
		} elseif( $this->config->get('module_socnetauth2_odnoklassniki_req') ) { 
			$this->data['socnetauth2_odnoklassniki_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_odnoklassniki_req') );
		} else {
			$this->data['socnetauth2_odnoklassniki_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_gmail_req'])) {
			$this->data['socnetauth2_gmail_req'] = $this->request->post['socnetauth2_gmail_req'];
		} elseif( $this->config->get('module_socnetauth2_gmail_req') ) { 
			$this->data['socnetauth2_gmail_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_gmail_req') );
		} else {
			$this->data['socnetauth2_gmail_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_mailru_req'])) {
			$this->data['socnetauth2_mailru_req'] = $this->request->post['socnetauth2_mailru_req'];
		} elseif( $this->config->get('module_socnetauth2_mailru_req') ) { 
			$this->data['socnetauth2_mailru_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_mailru_req') );
		} else {
			$this->data['socnetauth2_mailru_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_yandex_req'])) {
			$this->data['socnetauth2_yandex_req'] = $this->request->post['socnetauth2_yandex_req'];
		} elseif( $this->config->get('module_socnetauth2_yandex_req') ) { 
			$this->data['socnetauth2_yandex_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_yandex_req') );
		} else {
			$this->data['socnetauth2_yandex_req'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_steam_req'])) {
			$this->data['socnetauth2_steam_req'] = $this->request->post['socnetauth2_steam_req'];
		} elseif( $this->config->get('module_socnetauth2_steam_req') ) { 
			$this->data['socnetauth2_steam_req'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_steam_req') );
		} else {
			$this->data['socnetauth2_steam_req'] = array();
		}
		
		$this->data['entry_vkontakte_customer_group_id'] = $this->language->get('entry_vkontakte_customer_group_id');
		$this->data['entry_facebook_customer_group_id'] = $this->language->get('entry_facebook_customer_group_id');
		$this->data['entry_odnoklassniki_customer_group_id'] = $this->language->get('entry_odnoklassniki_customer_group_id');
		$this->data['entry_gmail_customer_group_id'] = $this->language->get('entry_gmail_customer_group_id');
		$this->data['entry_mailru_customer_group_id'] = $this->language->get('entry_mailru_customer_group_id');
		$this->data['entry_twitter_customer_group_id'] = $this->language->get('entry_twitter_customer_group_id');
		
		$this->data['entry_mobile_control'] = $this->language->get('entry_mobile_control');
		
		$this->data['button_save_and_go'] = $this->language->get('button_save_and_go');
		$this->data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
		
		if( !empty( $this->session->data['success'] ) )
		{
			$this->session->data['success'] = 0;
			$this->data['success'] = $this->language->get('text_success');
		}
		else
		{
			$this->data['success'] = '';
		}
		
		$this->load->model('catalog/information');

		$this->data['informations'] = $this->model_catalog_information->getInformations();
		
		$this->data['entry_agree'] = $this->language->get('entry_agree');
		$this->data['entry_agree_no'] = $this->language->get('entry_agree_no');
		
		if (isset($this->request->post['socnetauth2_confirm_agree_status'])) 
		{
			$this->data['socnetauth2_confirm_agree_status'] = $this->request->post['socnetauth2_confirm_agree_status'];
		} 
		else
		{ 
			$this->data['socnetauth2_confirm_agree_status'] = $this->config->get('module_socnetauth2_confirm_agree_status');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_agree_required'])) {
			$this->data['socnetauth2_confirm_agree_required'] = $this->request->post['socnetauth2_confirm_agree_required'];
		} else { 
			$this->data['socnetauth2_confirm_agree_required'] = $this->config->get('module_socnetauth2_confirm_agree_required');
		}
		
		/* end 1303 */
		
		$this->data['entry_agree2'] = $this->language->get('entry_agree2');
		$this->data['entry_agree3'] = $this->language->get('entry_agree3');
		
		if (isset($this->request->post['socnetauth2_confirm_agree2_status'])) 
		{
			$this->data['socnetauth2_confirm_agree2_status'] = $this->request->post['socnetauth2_confirm_agree2_status'];
		} 
		else
		{ 
			$this->data['socnetauth2_confirm_agree2_status'] = $this->config->get('module_socnetauth2_confirm_agree2_status');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_agree2_required'])) {
			$this->data['socnetauth2_confirm_agree2_required'] = $this->request->post['socnetauth2_confirm_agree2_required'];
		} else { 
			$this->data['socnetauth2_confirm_agree2_required'] = $this->config->get('module_socnetauth2_confirm_agree2_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_agree3_status'])) 
		{
			$this->data['socnetauth2_confirm_agree3_status'] = $this->request->post['socnetauth2_confirm_agree3_status'];
		} 
		else
		{ 
			$this->data['socnetauth2_confirm_agree3_status'] = $this->config->get('module_socnetauth2_confirm_agree3_status');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_agree3_required'])) {
			$this->data['socnetauth2_confirm_agree3_required'] = $this->request->post['socnetauth2_confirm_agree3_required'];
		} else { 
			$this->data['socnetauth2_confirm_agree3_required'] = $this->config->get('module_socnetauth2_confirm_agree3_required');
		}
		
		$this->data['entry_confirm_group'] = $this->language->get('entry_confirm_group');
		
		if (isset($this->request->post['socnetauth2_confirm_group_status'])) 
		{
			$this->data['socnetauth2_confirm_group_status'] = $this->request->post['socnetauth2_confirm_group_status'];
		} 
		else
		{ 
			$this->data['socnetauth2_confirm_group_status'] = $this->config->get('module_socnetauth2_confirm_group_status');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_group_required'])) {
			$this->data['socnetauth2_confirm_group_required'] = $this->request->post['socnetauth2_confirm_group_required'];
		} else { 
			$this->data['socnetauth2_confirm_group_required'] = $this->config->get('module_socnetauth2_confirm_group_required');
		}
		
		/* end 0102 */
		
		
		if (isset($this->request->post['socnetauth2_vkontakte_status'])) 
		{
			$this->data['socnetauth2_vkontakte_status'] = $this->request->post['socnetauth2_vkontakte_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_vkontakte_status') )
		{ 
			$this->data['socnetauth2_vkontakte_status'] = $this->config->get('module_socnetauth2_vkontakte_status');
		}
		else
		{
			$this->data['socnetauth2_vkontakte_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_facebook_status'])) 
		{
			$this->data['socnetauth2_facebook_status'] = $this->request->post['socnetauth2_facebook_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_facebook_status') )
		{ 
			$this->data['socnetauth2_facebook_status'] = $this->config->get('module_socnetauth2_facebook_status');
		}
		else
		{
			$this->data['socnetauth2_facebook_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_twitter_status'])) 
		{
			$this->data['socnetauth2_twitter_status'] = $this->request->post['socnetauth2_twitter_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_twitter_status') )
		{ 
			$this->data['socnetauth2_twitter_status'] = $this->config->get('module_socnetauth2_twitter_status');
		}
		else
		{
			$this->data['socnetauth2_twitter_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_status'])) 
		{
			$this->data['socnetauth2_odnoklassniki_status'] = $this->request->post['socnetauth2_odnoklassniki_status'];
		} 
		else
		{ 
			$this->data['socnetauth2_odnoklassniki_status'] = $this->config->get('module_socnetauth2_odnoklassniki_status');
		}
		
		
		
		
		if (isset($this->request->post['socnetauth2_popup_status'])) {
			$this->data['socnetauth2_popup_status'] = $this->request->post['socnetauth2_popup_status'];
		} elseif ($this->config->get('module_socnetauth2_popup_status')) { 
			$this->data['socnetauth2_popup_status'] = $this->config->get('module_socnetauth2_popup_status');
		} else {
			$this->data['socnetauth2_popup_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_gmail_status'])) 
		{
			$this->data['socnetauth2_gmail_status'] = $this->request->post['socnetauth2_gmail_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_gmail_status') )
		{ 
			$this->data['socnetauth2_gmail_status'] = $this->config->get('module_socnetauth2_gmail_status');
		}
		else
		{
			$this->data['socnetauth2_gmail_status'] = 1;
		}
		
		
		if (isset($this->request->post['socnetauth2_gmail_client_id'])) 
		{
			$this->data['socnetauth2_gmail_client_id'] = $this->request->post['socnetauth2_gmail_client_id'];
		} 
		else
		{ 
			$this->data['socnetauth2_gmail_client_id'] = $this->config->get('module_socnetauth2_gmail_client_id');
		}
		
		if (isset($this->request->post['socnetauth2_gmail_client_secret'])) 
		{
			$this->data['socnetauth2_gmail_client_secret'] = $this->request->post['socnetauth2_gmail_client_secret'];
		} 
		else
		{ 
			$this->data['socnetauth2_gmail_client_secret'] = $this->config->get('module_socnetauth2_gmail_client_secret');
		}
		
		if (isset($this->request->post['socnetauth2_vkontakte_customer_group_id'])) {
			$this->data['socnetauth2_vkontakte_customer_group_id'] = $this->request->post['socnetauth2_vkontakte_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_vkontakte_customer_group_id')) { 
			$this->data['socnetauth2_vkontakte_customer_group_id'] = $this->config->get('module_socnetauth2_vkontakte_customer_group_id');
		} else {
			$this->data['socnetauth2_vkontakte_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_facebook_customer_group_id'])) {
			$this->data['socnetauth2_facebook_customer_group_id'] = $this->request->post['socnetauth2_facebook_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_facebook_customer_group_id')) { 
			$this->data['socnetauth2_facebook_customer_group_id'] = $this->config->get('module_socnetauth2_facebook_customer_group_id');
		} else {
			$this->data['socnetauth2_facebook_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_gmail_customer_group_id'])) {
			$this->data['socnetauth2_gmail_customer_group_id'] = $this->request->post['socnetauth2_gmail_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_gmail_customer_group_id')) { 
			$this->data['socnetauth2_gmail_customer_group_id'] = $this->config->get('module_socnetauth2_gmail_customer_group_id');
		} else {
			$this->data['socnetauth2_gmail_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_twitter_customer_group_id'])) {
			$this->data['socnetauth2_twitter_customer_group_id'] = $this->request->post['socnetauth2_twitter_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_twitter_customer_group_id')) { 
			$this->data['socnetauth2_twitter_customer_group_id'] = $this->config->get('module_socnetauth2_twitter_customer_group_id');
		} else {
			$this->data['socnetauth2_twitter_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_customer_group_id'])) {
			$this->data['socnetauth2_odnoklassniki_customer_group_id'] = $this->request->post['socnetauth2_odnoklassniki_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_odnoklassniki_customer_group_id')) { 
			$this->data['socnetauth2_odnoklassniki_customer_group_id'] = $this->config->get('module_socnetauth2_odnoklassniki_customer_group_id');
		} else {
			$this->data['socnetauth2_odnoklassniki_customer_group_id'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_mailru_customer_group_id'])) {
			$this->data['socnetauth2_mailru_customer_group_id'] = $this->request->post['socnetauth2_mailru_customer_group_id'];
		} elseif ($this->config->get('module_socnetauth2_mailru_customer_group_id')) { 
			$this->data['socnetauth2_mailru_customer_group_id'] = $this->config->get('module_socnetauth2_mailru_customer_group_id');
		} else {
			$this->data['socnetauth2_mailru_customer_group_id'] = '';
		}
		
		$this->data['entry_mailru_api_version'] = $this->language->get('entry_mailru_api_version');
		$this->data['entry_mailru_api_version_old'] = $this->language->get('entry_mailru_api_version_old');
		$this->data['entry_mailru_api_version_new'] = $this->language->get('entry_mailru_api_version_new');
		$this->data['entry_mailru_api_version_notice'] = $this->language->get('entry_mailru_api_version_notice');
		
		if (isset($this->request->post['socnetauth2_mailru_api_version'])) {
			$this->data['socnetauth2_mailru_api_version'] = $this->request->post['socnetauth2_mailru_api_version'];
		} elseif ($this->config->get('module_socnetauth2_mailru_api_version')) { 
			$this->data['socnetauth2_mailru_api_version'] = $this->config->get('module_socnetauth2_mailru_api_version');
		} else {
			
			if( $this->config->has('module_socnetauth2_vkontakte_status') )
				$this->data['socnetauth2_mailru_api_version'] = 'old';
			else
				$this->data['socnetauth2_mailru_api_version'] = 'new';
		}
		
		
		if (isset($this->request->post['socnetauth2_mailru_status'])) 
		{
			$this->data['socnetauth2_mailru_status'] = $this->request->post['socnetauth2_mailru_status'];
		} 
		elseif( $this->config->has('module_socnetauth2_mailru_status') )
		{ 
			$this->data['socnetauth2_mailru_status'] = $this->config->get('module_socnetauth2_mailru_status');
		}
		else
		{
			$this->data['socnetauth2_mailru_status'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_mailru_id'])) 
		{
			$this->data['socnetauth2_mailru_id'] = $this->request->post['socnetauth2_mailru_id'];
		} 
		else
		{ 
			$this->data['socnetauth2_mailru_id'] = $this->config->get('module_socnetauth2_mailru_id');
		}
		
		if (isset($this->request->post['socnetauth2_mailru_private'])) 
		{
			$this->data['socnetauth2_mailru_private'] = $this->request->post['socnetauth2_mailru_private'];
		} 
		else
		{ 
			$this->data['socnetauth2_mailru_private'] = $this->config->get('module_socnetauth2_mailru_private');
		}
		
		if (isset($this->request->post['socnetauth2_mailru_secret'])) 
		{
			$this->data['socnetauth2_mailru_secret'] = $this->request->post['socnetauth2_mailru_secret'];
		} 
		else
		{ 
			$this->data['socnetauth2_mailru_secret'] = $this->config->get('module_socnetauth2_mailru_secret');
		}
		
		
		if (isset($this->request->post['socnetauth2_email_auth'])) 
		{
			$this->data['socnetauth2_email_auth'] = $this->request->post['socnetauth2_email_auth'];
		} 
		elseif( $this->config->has('module_socnetauth2_email_auth') )
		{ 
			$this->data['socnetauth2_email_auth'] = $this->config->get('module_socnetauth2_email_auth');
		}
		else
		{
			$this->data['socnetauth2_email_auth'] = 'confirm';
		}		
		
		$this->data['entry_addpass'] = $this->language->get('entry_addpass');
		$this->data['entry_addpass_notice'] = $this->language->get('entry_addpass_notice');
		
		if (isset($this->request->post['socnetauth2_addpass'])) 
		{
			$this->data['socnetauth2_addpass'] = $this->request->post['socnetauth2_addpass'];
		} 
		else
		{ 
			$this->data['socnetauth2_addpass'] = $this->config->get('module_socnetauth2_addpass');
		}
		
		$this->data['entry_newcustomer_mail_type'] = $this->language->get('entry_newcustomer_mail_type');
		$this->data['entry_newcustomer_mail_type_common'] = $this->language->get('entry_newcustomer_mail_type_common');
		$this->data['entry_newcustomer_mail_type_custom'] = $this->language->get('entry_newcustomer_mail_type_custom');
		$this->data['entry_newcustomer_mail_subject'] = $this->language->get('entry_newcustomer_mail_subject');
		$this->data['entry_newcustomer_mail_text'] = $this->language->get('entry_newcustomer_mail_text');
		
		if (isset($this->request->post['socnetauth2_newcustomer_mail_type'])) 
		{
			$this->data['socnetauth2_newcustomer_mail_type'] = $this->request->post['socnetauth2_newcustomer_mail_type'];
		} 
		elseif( $this->config->has('module_socnetauth2_newcustomer_mail_type') )
		{ 
			$this->data['socnetauth2_newcustomer_mail_type'] = $this->config->get('module_socnetauth2_newcustomer_mail_type');
		}
		else
		{
			$this->data['socnetauth2_newcustomer_mail_type'] = 'common';
		}
		
		if (isset($this->request->post['socnetauth2_newcustomer_mail_subject'])) 
		{
			$this->data['socnetauth2_newcustomer_mail_subject'] = $this->request->post['socnetauth2_newcustomer_mail_subject'];
		} 
		elseif( $this->config->has('module_socnetauth2_newcustomer_mail_subject') )
		{ 
			$this->data['socnetauth2_newcustomer_mail_subject'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_newcustomer_mail_subject') );
		}
		else
		{
			$this->data['socnetauth2_newcustomer_mail_subject'] = $default_hash['default_newcustomer_mail_subject'];
		}
		
		if (isset($this->request->post['socnetauth2_newcustomer_mail_text'])) 
		{
			$this->data['socnetauth2_newcustomer_mail_text'] = $this->request->post['socnetauth2_newcustomer_mail_text'];
		} 
		elseif( $this->config->has('module_socnetauth2_newcustomer_mail_text') )
		{ 
			$this->data['socnetauth2_newcustomer_mail_text'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_newcustomer_mail_text') );
		}
		else
		{
			$this->data['socnetauth2_newcustomer_mail_text'] = $default_hash['default_newcustomer_mail_text'];
		}
		
		$this->data['entry_add_param'] = $this->language->get('entry_add_param');
		$this->data['entry_add_param_notice'] = $this->language->get('entry_add_param_notice');
		if (isset($this->request->post['socnetauth2_add_param'])) 
		{
			$this->data['socnetauth2_add_param'] = $this->request->post['socnetauth2_add_param'];
		} 
		else
		{ 
			$this->data['socnetauth2_add_param'] = $this->config->get('module_socnetauth2_add_param');
		}
		
		
		if (isset($this->request->post['socnetauth2_shop_folder'])) 
		{
			$this->data['socnetauth2_shop_folder'] = $this->request->post['socnetauth2_shop_folder'];
		} 
		else
		{ 
			$this->data['socnetauth2_shop_folder'] = $this->config->get('module_socnetauth2_shop_folder');
		}
		
		$this->data['entry_redirect_url'] = $this->language->get('entry_redirect_url');
		
		$this->data['entry_redirect_url_vkontakte'] = $this->language->get('entry_redirect_url_vkontakte');
		$this->data['entry_redirect_url_odnoklassniki'] = $this->language->get('entry_redirect_url_odnoklassniki');
		$this->data['entry_redirect_url_facebook'] = $this->language->get('entry_redirect_url_facebook');
		$this->data['entry_redirect_url_twitter'] = $this->language->get('entry_redirect_url_twitter');
		$this->data['entry_redirect_url_gmail'] = $this->language->get('entry_redirect_url_gmail');
		$this->data['entry_redirect_url_mailru'] = $this->language->get('entry_redirect_url_mailru');
		$this->data['entry_redirect_url_steam'] = $this->language->get('entry_redirect_url_steam');
		$this->data['entry_redirect_url_yandex'] = $this->language->get('entry_redirect_url_yandex'); 
		
		$this->data['entry_redirect_url_new'] = $this->language->get('entry_redirect_url_new');
		$this->data['entry_redirect_url_old'] = $this->language->get('entry_redirect_url_old');
		$this->data['entry_redirect_url_old_notice'] = $this->language->get('entry_redirect_url_old_notice');
		$this->data['text_copy'] = $this->language->get('text_copy'); 
		
		$socnets = $this->model_extension_module_socnetauth2->getAllSocnets();
		
		$socnetauth2_shop_folder = '';
		if( $this->data['socnetauth2_shop_folder'] )
			$socnetauth2_shop_folder = '/'.$this->data['socnetauth2_shop_folder'];
		
		if( file_exists( preg_replace("/system\/?$/", "", DIR_SYSTEM).'socnetauth2/lib/socnetauth2.php' ) )
			$this->data['is_show_old_redirect'] = 1;
		else
			$this->data['is_show_old_redirect'] = 0;
		
		foreach($socnets as $key=>$val)
		{
			$this->data['entry_redirect_url_'.$key.'_notice'] = $this->language->get('entry_redirect_url_notice');
			$this->data['entry_redirect_url_'.$key.'_notice'] = str_replace("{key}", $key, $this->data['entry_redirect_url_'.$key.'_notice']);
			
			$this->data[$key.'_redirect_url_new'] = $this->data['socnetauth2_protokol'].'://'.
				$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.'/sna-'.$key;
				
			$this->data[$key.'_redirect_url_old'] = $this->data['socnetauth2_protokol'].'://'.
				$this->request->server['HTTP_HOST'].$socnetauth2_shop_folder.'/socnetauth2/'.$key.'.php';
			
			if( $this->config->get('module_socnetauth2_'.$key.'_redirect_type') )
			{
				$this->data['socnetauth2_'.$key.'_redirect_type'] = $this->config->get('module_socnetauth2_'.$key.'_redirect_type');
			}
			elseif( !$this->config->has('module_socnetauth2_status') 
				||
				!$this->data['is_show_old_redirect']
			)
			{
				$this->data['socnetauth2_'.$key.'_redirect_type'] = 'new';
			}
			elseif( !$this->config->get('module_socnetauth2_'.$key.'_agent') || 
					$this->config->get('module_socnetauth2_'.$key.'_agent') == 'loginza' )
			{
				$this->data['socnetauth2_'.$key.'_redirect_type'] = 'old';
			}
			else
			{
				if( 
					($key == 'vkontakte' && $this->config->get('module_socnetauth2_vkontakte_appid')) ||
					($key == 'facebook' && $this->config->get('module_socnetauth2_facebook_appid')) ||
					($key == 'twitter' && $this->config->get('module_socnetauth2_twitter_consumer_key')) ||
					($key == 'odnoklassniki' && $this->config->get('module_socnetauth2_odnoklassniki_application_id')) ||
					($key == 'gmail' && $this->config->get('module_socnetauth2_gmail_client_id')) ||
					($key == 'mailru' && $this->config->get('module_socnetauth2_mailru_id')) ||
					($key == 'yandex' && $this->config->get('module_socnetauth2_yandex_client_id')) ||
					($key == 'steam' && $this->config->get('module_socnetauth2_steam_api_key')) 
				
				)
				{  
					$this->data['socnetauth2_'.$key.'_redirect_type'] = 'old';
				}
				else
				{
					$this->data['socnetauth2_'.$key.'_redirect_type'] = 'new';
				}
			}
			
		}
		
		if (isset($this->request->post['socnetauth2_vkontakte_appid'])) 
		{
			$this->data['socnetauth2_vkontakte_appid'] = $this->request->post['socnetauth2_vkontakte_appid'];
		} 
		else
		{ 
			$this->data['socnetauth2_vkontakte_appid'] = $this->config->get('module_socnetauth2_vkontakte_appid');
		}
		
		if (isset($this->request->post['socnetauth2_vkontakte_appsecret'])) 
		{
			$this->data['socnetauth2_vkontakte_appsecret'] = $this->request->post['socnetauth2_vkontakte_appsecret'];
		} 
		else
		{ 
			$this->data['socnetauth2_vkontakte_appsecret'] = $this->config->get('module_socnetauth2_vkontakte_appsecret');
		}
		
		
		if (isset($this->request->post['socnetauth2_facebook_appid'])) 
		{
			$this->data['socnetauth2_facebook_appid'] = $this->request->post['socnetauth2_facebook_appid'];
		} 
		else
		{ 
			$this->data['socnetauth2_facebook_appid'] = $this->config->get('module_socnetauth2_facebook_appid');
		}
		
		if (isset($this->request->post['socnetauth2_facebook_appsecret'])) 
		{
			$this->data['socnetauth2_facebook_appsecret'] = $this->request->post['socnetauth2_facebook_appsecret'];
		} 
		else
		{ 
			$this->data['socnetauth2_facebook_appsecret'] = $this->config->get('module_socnetauth2_facebook_appsecret');
		}
		
		if (isset($this->request->post['socnetauth2_twitter_consumer_key'])) 
		{
			$this->data['socnetauth2_twitter_consumer_key'] = $this->request->post['socnetauth2_twitter_consumer_key'];
		} 
		else
		{ 
			$this->data['socnetauth2_twitter_consumer_key'] = $this->config->get('module_socnetauth2_twitter_consumer_key');
		}
		
		if (isset($this->request->post['socnetauth2_twitter_consumer_secret'])) 
		{
			$this->data['socnetauth2_twitter_consumer_secret'] = $this->request->post['socnetauth2_twitter_consumer_secret'];
		} 
		else
		{ 
			$this->data['socnetauth2_twitter_consumer_secret'] = $this->config->get('module_socnetauth2_twitter_consumer_secret');
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_application_id'])) 
		{
			$this->data['socnetauth2_odnoklassniki_application_id'] = $this->request->post['socnetauth2_odnoklassniki_application_id'];
		} 
		else
		{ 
			$this->data['socnetauth2_odnoklassniki_application_id'] = $this->config->get('module_socnetauth2_odnoklassniki_application_id');
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_public_key'])) 
		{
			$this->data['socnetauth2_odnoklassniki_public_key'] = $this->request->post['socnetauth2_odnoklassniki_public_key'];
		} 
		else
		{ 
			$this->data['socnetauth2_odnoklassniki_public_key'] = $this->config->get('module_socnetauth2_odnoklassniki_public_key');
		}
		
		if (isset($this->request->post['socnetauth2_odnoklassniki_secret_key'])) 
		{
			$this->data['socnetauth2_odnoklassniki_secret_key'] = $this->request->post['socnetauth2_odnoklassniki_secret_key'];
		} 
		else
		{ 
			$this->data['socnetauth2_odnoklassniki_secret_key'] = $this->config->get('module_socnetauth2_odnoklassniki_secret_key');
		}
		
		
		
		
		
		
		if (isset($this->request->post['socnetauth2_status'])) {
			$this->data['socnetauth2_status'] = $this->request->post['socnetauth2_status'];
		} elseif ($this->config->get('module_socnetauth2_status')) { 
			$this->data['socnetauth2_status'] = $this->config->get('module_socnetauth2_status');
		} else {
			$this->data['socnetauth2_status'] = 0;
		}
		
		// ----------------------
		
		if (isset($this->request->post['socnetauth2_account_format'])) {
			$this->data['socnetauth2_account_format'] = $this->request->post['socnetauth2_account_format'];
		} elseif ($this->config->get('module_socnetauth2_account_format')) { 
			$this->data['socnetauth2_account_format'] = $this->config->get('module_socnetauth2_account_format');
		} else {
			$this->data['socnetauth2_account_format'] = 'bline';
		}
		
		if( $this->data['socnetauth2_account_format'] == 'lline' )
			$this->data['socnetauth2_account_format'] = 'bline'; 
		
		if (isset($this->request->post['socnetauth2_checkout_format'])) {
			$this->data['socnetauth2_checkout_format'] = $this->request->post['socnetauth2_checkout_format'];
		} elseif ($this->config->get('module_socnetauth2_checkout_format')) { 
			$this->data['socnetauth2_checkout_format'] = $this->config->get('module_socnetauth2_checkout_format');
		} else {
			$this->data['socnetauth2_checkout_format'] = 'bline';
		}
		
		if( $this->data['socnetauth2_checkout_format'] == 'lline' )
			$this->data['socnetauth2_checkout_format'] = 'bline'; 
		
		if (isset($this->request->post['socnetauth2_reg_format'])) {
			$this->data['socnetauth2_reg_format'] = $this->request->post['socnetauth2_reg_format'];
		} elseif ($this->config->get('module_socnetauth2_reg_format')) { 
			$this->data['socnetauth2_reg_format'] = $this->config->get('module_socnetauth2_reg_format');
		} else {
			$this->data['socnetauth2_reg_format'] = 'bline';
		}
		
		if( $this->data['socnetauth2_reg_format'] == 'lline' )
			$this->data['socnetauth2_reg_format'] = 'bline'; 
		
		if( isset( $this->request->post['socnetauth2_popup_name'] ) )
		{
			$this->data['socnetauth2_popup_name'] = $this->request->post['socnetauth2_popup_name'];
		}
		elseif( $this->config->has('module_socnetauth2_popup_name') )
		{
			if( !is_array($this->config->get('module_socnetauth2_popup_name')) && 
				stristr($this->config->get('module_socnetauth2_popup_name'), '{' ) != false &&
				stristr($this->config->get('module_socnetauth2_popup_name'), '}' ) != false &&
				stristr($this->config->get('module_socnetauth2_popup_name'), ';' ) != false &&
				stristr($this->config->get('module_socnetauth2_popup_name'), ':' ) != false
			)
			{
				$this->data['socnetauth2_popup_name'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_popup_name') );
			}
			else
			$this->data['socnetauth2_popup_name'] = $this->config->get('module_socnetauth2_popup_name');
		}
		else
		{
			foreach($this->data['languages'] as $language)
			{
				if( isset($language['directory']) )
					$directory = $language['directory'];
				else
					$directory = $language['code'];
				
				$Lang = new Language( $directory );
				$Lang->load('extension/module/socnetauth2');
				
			
				$this->data['socnetauth2_popup_name'][$language['language_id']] = $Lang->get('socnetauth2_popup_name_default');
			}
		}
		
		
		if (isset($this->request->post['socnetauth2_popups'])) {
			$this->data['socnetauth2_popups'] = $this->request->post['socnetauth2_popups'];
		} elseif ($this->config->get('module_socnetauth2_popups')) { 
		
			if( !is_array($this->config->get('module_socnetauth2_popups')) && 
				stristr($this->config->get('module_socnetauth2_popups'), '{' ) != false &&
				stristr($this->config->get('module_socnetauth2_popups'), '}' ) != false &&
				stristr($this->config->get('module_socnetauth2_popups'), ';' ) != false &&
				stristr($this->config->get('module_socnetauth2_popups'), ':' ) != false
			)
			{
				$this->data['socnetauth2_popups'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_popups') );
			}
			else
			{
				$this->data['socnetauth2_popups'] = $this->config->get('module_socnetauth2_popups');
			}
			
		} else {
			$this->data['socnetauth2_popups'] = array();
		}
		
	   	   
		$this->data['entry_default_email'] = $this->language->get('entry_default_email');
		$this->data['entry_default_email_notice'] = $this->language->get('entry_default_email_notice');
		
		if (isset($this->request->post['socnetauth2_default_email'])) {
			$this->data['socnetauth2_default_email'] = $this->request->post['socnetauth2_default_email'];
		} elseif ($this->config->get('module_socnetauth2_default_email')) { 
			$this->data['socnetauth2_default_email'] = $this->config->get('module_socnetauth2_default_email');
		} else {
			$this->data['socnetauth2_default_email'] = $this->config->get('config_email');
		}
		
		   
		$this->data['entry_save_fullname_to_firstname'] = $this->language->get('entry_save_fullname_to_firstname');
		
		if (isset($this->request->post['socnetauth2_save_fullname_to_firstname'])) {
			$this->data['socnetauth2_save_fullname_to_firstname'] = $this->request->post['socnetauth2_save_fullname_to_firstname'];
		} elseif ($this->config->get('module_socnetauth2_save_fullname_to_firstname')) { 
			$this->data['socnetauth2_save_fullname_to_firstname'] = $this->config->get('module_socnetauth2_save_fullname_to_firstname');
		} else {
			$this->data['socnetauth2_save_fullname_to_firstname'] = 0;
		}
		
		$this->data['entry_confirm_username'] = $this->language->get('entry_confirm_username');
		
		if (isset($this->request->post['socnetauth2_confirm_username_required'])) {
			$this->data['socnetauth2_confirm_username_required'] = $this->request->post['socnetauth2_confirm_username_required'];
		} else { 
			$this->data['socnetauth2_confirm_username_required'] = $this->config->get('module_socnetauth2_confirm_username_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_username_status'])) {
			$this->data['socnetauth2_confirm_username_status'] = $this->request->post['socnetauth2_confirm_username_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_username_status')) { 
			$this->data['socnetauth2_confirm_username_status'] = $this->config->get('module_socnetauth2_confirm_username_status');
		} else {
			$this->data['socnetauth2_confirm_username_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_save_to_addr'])) {
			$this->data['socnetauth2_save_to_addr'] = $this->request->post['socnetauth2_save_to_addr'];
		} elseif ($this->config->get('module_socnetauth2_save_to_addr')) { 
			$this->data['socnetauth2_save_to_addr'] = $this->config->get('module_socnetauth2_save_to_addr');
		} else {
			$this->data['socnetauth2_save_to_addr'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_widget_format'])) {
			$this->data['socnetauth2_widget_format'] = $this->request->post['socnetauth2_widget_format'];
		} elseif ($this->config->get('module_socnetauth2_widget_format')) { 
			$this->data['socnetauth2_widget_format'] = $this->config->get('module_socnetauth2_widget_format');
		
		} else {
			$this->data['socnetauth2_widget_format'] = 'bline';
		}
		
		$this->data['entry_design_widget_form_html'] = $this->language->get('entry_design_widget_form_html');
		
		$this->data['entry_widget_css_socnetauthbox'] = $this->language->get('entry_widget_css_socnetauthbox');
		$this->data['entry_widget_css_socnetauthbox_h3'] = $this->language->get('entry_widget_css_socnetauthbox_h3');
		$this->data['entry_widget_css_socnetauthbox_socnetauthhead'] = $this->language->get('entry_widget_css_socnetauthbox_socnetauthhead');
		$this->data['entry_widget_css_socnetauthbox_socnetauthbody'] = $this->language->get('entry_widget_css_socnetauthbox_socnetauthbody');
		$this->data['entry_widget_css_socnetauthbox_socnetauthform'] = $this->language->get('entry_widget_css_socnetauthbox_socnetauthform');
		$this->data['entry_widget_css_socnetauthbox_socnetauthform_td'] = $this->language->get('entry_widget_css_socnetauthbox_socnetauthform_td');
		
		if( $this->data['socnetauth2_widget_format'] == 'lline' )
		{
			$this->data['socnetauth2_widget_format'] = 'bline';
		}  
		
		if (isset($this->request->post['socnetauth2_design_widget_kvadrat_html'])) 
		{
			$this->data['socnetauth2_design_widget_kvadrat_html'] = $this->request->post['socnetauth2_design_widget_kvadrat_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_widget_kvadrat_html') )
		{ 
			$this->data['socnetauth2_design_widget_kvadrat_html'] = $this->config->get('module_socnetauth2_design_widget_kvadrat_html');
		}
		else
		{
			$this->data['socnetauth2_design_widget_kvadrat_html'] = '<style>{style}</style>
<div class="sna_header" style="font-weight: bold;  padding-top: 10px;">{header}</div>
<div class="sna_icons"><table class=\'sna_kvadrat_table\' style=\'padding-bottom: 10px;\'>
<tr class=\'sna_kvadrat_line1\'>{rows}</tr>
<tr class=\'sna_kvadrat_line2\'>{rows}</tr></table></div>';
		}
		
		if (isset($this->request->post['socnetauth2_design_widget_bline_html'])) 
		{
			$this->data['socnetauth2_design_widget_bline_html'] = $this->request->post['socnetauth2_design_widget_bline_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_widget_bline_html') )
		{ 
			$this->data['socnetauth2_design_widget_bline_html'] = $this->config->get('module_socnetauth2_design_widget_bline_html');
		}
		else
		{
			$this->data['socnetauth2_design_widget_bline_html'] = "<style>{style}</style>
				<div class='sna_header' style='font-weight: bold;  padding-top: 10px;'>{header}</div>
				<div class='sna_icons'><ul class='sna_bline_ul' style='padding-left: 0px;'>{rows}</ul>
				<br style='clear: both;'></div>";
		}
		
		if (isset($this->request->post['socnetauth2_design_widget_bline_row_html'])) 
		{
			$this->data['socnetauth2_design_widget_bline_row_html'] = $this->request->post['socnetauth2_design_widget_bline_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_widget_bline_row_html') )
		{ 
			$this->data['socnetauth2_design_widget_bline_row_html'] = $this->config->get('module_socnetauth2_design_widget_bline_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_widget_bline_row_html'] = '<li style="padding-right: 10px; padding-bottom: 10px; float: left; list-style: none;"><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></li>';
		}	
		
		if (isset($this->request->post['socnetauth2_design_widget_kvadrat_row_html'])) 
		{
			$this->data['socnetauth2_design_widget_kvadrat_row_html'] = $this->request->post['socnetauth2_design_widget_kvadrat_row_html'];
		} 
		elseif( $this->config->get('module_socnetauth2_design_widget_kvadrat_row_html') )
		{ 
			$this->data['socnetauth2_design_widget_kvadrat_row_html'] = $this->config->get('module_socnetauth2_design_widget_kvadrat_row_html');
		}
		else
		{
			$this->data['socnetauth2_design_widget_kvadrat_row_html'] = '<td><a href=\'{link}\'><img src=\'{img}\' title=\'{title}\'  /></a></td>';
		}	
		
		
		if (isset($this->request->post['socnetauth2_design_widget_css'])) 
		{
			$this->data['socnetauth2_design_widget_css'] = $this->request->post['socnetauth2_design_widget_css'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_widget_css') )
		{ 
			$this->data['socnetauth2_design_widget_css'] = $this->config->get('module_socnetauth2_design_widget_css');
		}
		else
		{
			$this->data['socnetauth2_design_widget_css'] = '.sna_header { font-weight: bold;  padding-top: 10px; }'."\n".
			'.socnetauthbox { border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px 4px 4px 4px; }'."\n".
			'.socnetauthbox h3 { margin: 0px; color: #888888; font-size: 14px; }'."\n".
			'.socnetauthbox .socnetauthhead { border-radius: 4px 4px 0px 0px; background: none repeat scroll 0 0 #eeeeee; border-bottom: 1px solid #ddd;  padding-top: 10px; padding-bottom: 10px; padding-left: 20px; padding-right: 20px;}'."\n".
			'.socnetauthbox .socnetauthbody { padding-top: 10px; padding-bottom: 10px; padding-left: 20px; padding-right: 20px; }'."\n".
			'.socnetauthform td { padding-top: 5px; }';
		}
		
		if (isset($this->request->post['socnetauth2_design_widget_header'])) 
		{
			$this->data['socnetauth2_design_widget_header'] = $this->request->post['socnetauth2_design_widget_header'];
		} 
		elseif( $this->config->has('module_socnetauth2_design_widget_header') )
		{ 
			$this->data['socnetauth2_design_widget_header'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_design_widget_header') );
		}
		else
		{
			$this->data['socnetauth2_design_widget_header'] = $default_hash['default_header_name_auth'];
		}
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox'] = $this->request->post['socnetauth2_widget_css_socnetauthbox'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox'] = 'border: 1px solid #ddd; margin-bottom: 20px; border-radius: 4px 4px 4px 4px;';
		}
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox_h3'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_h3'] = $this->request->post['socnetauth2_widget_css_socnetauthbox_h3'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox_h3') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox_h3'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox_h3') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_h3'] = 'margin: 0px; color: #888888; font-size: 14px;';
		}
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthhead'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthhead'] = $this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthhead'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox_socnetauthhead') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthhead'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthhead') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthhead'] = 'border-radius: 4px 4px 0px 0px; background: none repeat scroll 0 0 #eeeeee; border-bottom: 1px solid #ddd;  padding-top: 10px; padding-bottom: 10px; padding-left: 20px; padding-right: 20px;';
		}
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthbody'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthbody'] = $this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthbody'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox_socnetauthbody') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthbody'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthbody') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthbody'] = 'padding-top: 10px; padding-bottom: 10px; padding-left: 20px; padding-right: 20px;';
		}
		
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthform'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform'] = $this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthform'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox_socnetauthform') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthform') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'])) 
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'] = $this->request->post['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'];
		} 
		elseif( $this->config->has('module_socnetauth2_widget_css_socnetauthbox_socnetauthform_td') )
		{ 
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_widget_css_socnetauthbox_socnetauthform_td') );
		}
		else
		{
			$this->data['socnetauth2_widget_css_socnetauthbox_socnetauthform_td'] = 'padding-top: 5px;';
		}
		
		
		
		if (isset($this->request->post['socnetauth2_show_standart_auth'])) {
			$this->data['socnetauth2_show_standart_auth'] = $this->request->post['socnetauth2_show_standart_auth'];
		} elseif ($this->config->has('module_socnetauth2_show_standart_auth')) { 
			$this->data['socnetauth2_show_standart_auth'] = $this->config->get('module_socnetauth2_show_standart_auth');
		} else {
			$this->data['socnetauth2_show_standart_auth'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_sort'])) {
			$this->data['socnetauth2_sort'] = $this->request->post['socnetauth2_sort'];
		} elseif ($this->config->has('module_socnetauth2_sort')) { 
			$this->data['socnetauth2_sort'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_sort') );
		} else {
			$this->data['socnetauth2_sort'] = array();
		}
		
		if (isset($this->request->post['socnetauth2_dobortype'])) {
			$this->data['socnetauth2_dobortype'] = $this->request->post['socnetauth2_dobortype'];
		} elseif ($this->config->has('module_socnetauth2_dobortype')) { 
			$this->data['socnetauth2_dobortype'] = $this->config->get('module_socnetauth2_dobortype');
		} else {
			$this->data['socnetauth2_dobortype'] = 'one';
		}
		
		
		if (isset($this->request->post['socnetauth2_modules'])) {
			$this->data['socnetauth2_modules'] = $this->request->post['socnetauth2_modules'];
		} elseif ($this->config->get('module_socnetauth2_modules')) { 
		
			if( !is_array($this->config->get('module_socnetauth2_modules')) && 
				stristr($this->config->get('module_socnetauth2_modules'), '{' ) != false &&
				stristr($this->config->get('module_socnetauth2_modules'), '}' ) != false &&
				stristr($this->config->get('module_socnetauth2_modules'), ';' ) != false &&
				stristr($this->config->get('module_socnetauth2_modules'), ':' ) != false
			)
			{
				$this->data['socnetauth2_modules'] = $this->custom_unserialize( $this->config->get('module_socnetauth2_modules') );
			}
			else
			{
				$this->data['socnetauth2_modules'] = $this->config->get('module_socnetauth2_modules');
			}
			
		} else {
			$this->data['socnetauth2_modules'] = array();
		}
		
		
		if (isset($this->request->post['socnetauth2_widget_after'])) {
			$this->data['socnetauth2_widget_after'] = $this->request->post['socnetauth2_widget_after'];
		} elseif ($this->config->get('module_socnetauth2_widget_after')) { 
			$this->data['socnetauth2_widget_after'] = $this->config->get('module_socnetauth2_widget_after');
		} else {
			$this->data['socnetauth2_widget_after'] = '';
		}
		
		if (isset($this->request->post['socnetauth2_confirm_firstname_status'])) {
			$this->data['socnetauth2_confirm_firstname_status'] = $this->request->post['socnetauth2_confirm_firstname_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_firstname_status')) { 
			$this->data['socnetauth2_confirm_firstname_status'] = $this->config->get('module_socnetauth2_confirm_firstname_status');
		} else {
			$this->data['socnetauth2_confirm_firstname_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_lastname_status'])) {
			$this->data['socnetauth2_confirm_lastname_status'] = $this->request->post['socnetauth2_confirm_lastname_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_lastname_status')) { 
			$this->data['socnetauth2_confirm_lastname_status'] = $this->config->get('module_socnetauth2_confirm_lastname_status');
		} else {
			$this->data['socnetauth2_confirm_lastname_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_email_status'])) {
			$this->data['socnetauth2_confirm_email_status'] = $this->request->post['socnetauth2_confirm_email_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_email_status')) { 
			$this->data['socnetauth2_confirm_email_status'] = $this->config->get('module_socnetauth2_confirm_email_status');
		} else {
			$this->data['socnetauth2_confirm_email_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_telephone_status'])) {
			$this->data['socnetauth2_confirm_telephone_status'] = $this->request->post['socnetauth2_confirm_telephone_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_telephone_status')) { 
			$this->data['socnetauth2_confirm_telephone_status'] = $this->config->get('module_socnetauth2_confirm_telephone_status');
		} else {
			$this->data['socnetauth2_confirm_telephone_status'] = 0;
		}
		
		$this->data['text_hideifhas'] = $this->language->get('text_hideifhas');
		
		$fields = array(
			"firstname", 
			"lastname",
			"email",
			"telephone",
			"company",
			"postcode",
			"country",
			"zone",
			"city",
			"address_1",
			"group",
			"agree",
			"username"
		);
		
		foreach($fields as $field)
		{
			if (isset($this->request->post['socnetauth2_confirm_'.$field.'_hideifhas'])) {
				$this->data['socnetauth2_confirm_'.$field.'_hideifhas'] = $this->request->post['socnetauth2_confirm_'.$field.'_hideifhas'];
			} elseif ($this->config->get('module_socnetauth2_confirm_'.$field.'_hideifhas')) { 
				$this->data['socnetauth2_confirm_'.$field.'_hideifhas'] = $this->config->get('module_socnetauth2_confirm_'.$field.'_hideifhas');
			} else {
				$this->data['socnetauth2_confirm_'.$field.'_hideifhas'] = 0;
			}
		}
		
		$this->data['entry_confirm_company'] = $this->language->get('entry_confirm_company');
		$this->data['entry_confirm_address_1'] = $this->language->get('entry_confirm_address_1');
		$this->data['entry_confirm_postcode'] = $this->language->get('entry_confirm_postcode');
		$this->data['entry_confirm_city'] = $this->language->get('entry_confirm_city');
		$this->data['entry_confirm_zone'] = $this->language->get('entry_confirm_zone');
		$this->data['entry_confirm_country'] = $this->language->get('entry_confirm_country');
		$this->data['text_required'] = $this->language->get('text_required');
		
		if (isset($this->request->post['socnetauth2_confirm_company_status'])) {
			$this->data['socnetauth2_confirm_company_status'] = $this->request->post['socnetauth2_confirm_company_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_company_status')) { 
			$this->data['socnetauth2_confirm_company_status'] = $this->config->get('module_socnetauth2_confirm_company_status');
		} else {
			$this->data['socnetauth2_confirm_company_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_address_1_status'])) {
			$this->data['socnetauth2_confirm_address_1_status'] = $this->request->post['socnetauth2_confirm_address_1_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_address_1_status')) { 
			$this->data['socnetauth2_confirm_address_1_status'] = $this->config->get('module_socnetauth2_confirm_address_1_status');
		} else {
			$this->data['socnetauth2_confirm_address_1_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_postcode_status'])) {
			$this->data['socnetauth2_confirm_postcode_status'] = $this->request->post['socnetauth2_confirm_postcode_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_postcode_status')) { 
			$this->data['socnetauth2_confirm_postcode_status'] = $this->config->get('module_socnetauth2_confirm_postcode_status');
		} else {
			$this->data['socnetauth2_confirm_postcode_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_city_status'])) {
			$this->data['socnetauth2_confirm_city_status'] = $this->request->post['socnetauth2_confirm_city_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_city_status')) { 
			$this->data['socnetauth2_confirm_city_status'] = $this->config->get('module_socnetauth2_confirm_city_status');
		} else {
			$this->data['socnetauth2_confirm_city_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_zone_status'])) {
			$this->data['socnetauth2_confirm_zone_status'] = $this->request->post['socnetauth2_confirm_zone_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_zone_status')) { 
			$this->data['socnetauth2_confirm_zone_status'] = $this->config->get('module_socnetauth2_confirm_zone_status');
		} else {
			$this->data['socnetauth2_confirm_zone_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_country_status'])) {
			$this->data['socnetauth2_confirm_country_status'] = $this->request->post['socnetauth2_confirm_country_status'];
		} elseif ($this->config->get('module_socnetauth2_confirm_country_status')) { 
			$this->data['socnetauth2_confirm_country_status'] = $this->config->get('module_socnetauth2_confirm_country_status');
		} else {
			$this->data['socnetauth2_confirm_country_status'] = 0;
		}
		
		if (isset($this->request->post['socnetauth2_confirm_firstname_required'])) {
			$this->data['socnetauth2_confirm_firstname_required'] = $this->request->post['socnetauth2_confirm_firstname_required'];
		} else { 
			$this->data['socnetauth2_confirm_firstname_required'] = $this->config->get('module_socnetauth2_confirm_firstname_required');
		}
		
		
		$this->data['data_nets'] = $this->data_nets;
		
		
		
		if (isset($this->request->post['socnetauth2_confirm_lastname_required'])) {
			$this->data['socnetauth2_confirm_lastname_required'] = $this->request->post['socnetauth2_confirm_lastname_required'];
		} else { 
			$this->data['socnetauth2_confirm_lastname_required'] = $this->config->get('module_socnetauth2_confirm_lastname_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_email_required'])) {
			$this->data['socnetauth2_confirm_email_required'] = $this->request->post['socnetauth2_confirm_email_required'];
		} else { 
			$this->data['socnetauth2_confirm_email_required'] = $this->config->get('module_socnetauth2_confirm_email_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_telephone_required'])) {
			$this->data['socnetauth2_confirm_telephone_required'] = $this->request->post['socnetauth2_confirm_telephone_required'];
		} else { 
			$this->data['socnetauth2_confirm_telephone_required'] = $this->config->get('module_socnetauth2_confirm_telephone_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_company_required'])) {
			$this->data['socnetauth2_confirm_company_required'] = $this->request->post['socnetauth2_confirm_company_required'];
		} else { 
			$this->data['socnetauth2_confirm_company_required'] = $this->config->get('module_socnetauth2_confirm_company_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_address_1_required'])) {
			$this->data['socnetauth2_confirm_address_1_required'] = $this->request->post['socnetauth2_confirm_address_1_required'];
		} else { 
			$this->data['socnetauth2_confirm_address_1_required'] = $this->config->get('module_socnetauth2_confirm_address_1_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_postcode_required'])) {
			$this->data['socnetauth2_confirm_postcode_required'] = $this->request->post['socnetauth2_confirm_postcode_required'];
		} else { 
			$this->data['socnetauth2_confirm_postcode_required'] = $this->config->get('module_socnetauth2_confirm_postcode_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_city_required'])) {
			$this->data['socnetauth2_confirm_city_required'] = $this->request->post['socnetauth2_confirm_city_required'];
		} else { 
			$this->data['socnetauth2_confirm_city_required'] = $this->config->get('module_socnetauth2_confirm_city_required');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_zone_required'])) {
			$this->data['socnetauth2_confirm_zone_required'] = $this->request->post['socnetauth2_confirm_zone_required'];
		} else { 
			$this->data['socnetauth2_confirm_zone_required'] = $this->config->get('module_socnetauth2_confirm_zone_required');
		}
		
		
		if (isset($this->request->post['socnetauth2_confirm_country_required'])) {
			$this->data['socnetauth2_confirm_country_required'] = $this->request->post['socnetauth2_confirm_country_required'];
		} else { 
			$this->data['socnetauth2_confirm_country_required'] = $this->config->get('module_socnetauth2_confirm_country_required');
		}
		
		/* 
		lastname
		email
		phone
		company
		address_1		
		postcode
		city
		zone_id
		country_id
		end update: c1 */
		
		
		
		
		if (isset($this->request->post['socnetauth2_format'])) {
			$this->data['socnetauth2_format'] = $this->request->post['socnetauth2_format'];
		} elseif ($this->config->get('module_socnetauth2_format')) { 
			$this->data['socnetauth2_format'] = $this->config->get('module_socnetauth2_format');
		} else {
			$this->data['socnetauth2_format'] = 'bline';
		}
		
		if (isset($this->request->post['socnetauth2_label'])) {
			$this->data['socnetauth2_label'] = $this->request->post['socnetauth2_label'];
		} elseif ($this->config->get('module_socnetauth2_label')) { 
			$this->data['socnetauth2_label'] = $this->custom_unserialize($this->config->get('module_socnetauth2_label'));
		} else {
			$this->data['socnetauth2_label'] = $default_hash['default_label'];
		}
		
		if (isset($this->request->post['socnetauth2_admin_customer'])) {
			$this->data['socnetauth2_admin_customer'] = $this->request->post['socnetauth2_admin_customer'];
		} elseif ($this->config->has('module_socnetauth2_admin_customer')) { 
			$this->data['socnetauth2_admin_customer'] = $this->config->get('module_socnetauth2_admin_customer');
		} else {
			$this->data['socnetauth2_admin_customer'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_admin_customer_list'])) {
			$this->data['socnetauth2_admin_customer_list'] = $this->request->post['socnetauth2_admin_customer_list'];
		} elseif ($this->config->has('module_socnetauth2_admin_customer_list')) { 
			$this->data['socnetauth2_admin_customer_list'] = $this->config->get('module_socnetauth2_admin_customer_list');
		} else {
			$this->data['socnetauth2_admin_customer_list'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_admin_order'])) {
			$this->data['socnetauth2_admin_order'] = $this->request->post['socnetauth2_admin_order'];
		} elseif ($this->config->has('module_socnetauth2_admin_order')) { 
			$this->data['socnetauth2_admin_order'] = $this->config->get('module_socnetauth2_admin_order');
		} else {
			$this->data['socnetauth2_admin_order'] = 1;
		}
		
		if (isset($this->request->post['socnetauth2_admin_order_list'])) {
			$this->data['socnetauth2_admin_order_list'] = $this->request->post['socnetauth2_admin_order_list'];
		} elseif ($this->config->has('module_socnetauth2_admin_order_list')) { 
			$this->data['socnetauth2_admin_order_list'] = $this->config->get('module_socnetauth2_admin_order_list');
		} else {
			$this->data['socnetauth2_admin_order_list'] = 1;
		}
		
		$this->data['entry_confirm_newsletter'] = $this->language->get('entry_confirm_newsletter');
		$this->data['entry_default_country'] = $this->language->get('entry_default_country');
		$this->data['text_no_country'] = $this->language->get('text_no_country');
		$this->data['entry_default_country_notice'] = $this->language->get('entry_default_country_notice');
		
		if (isset($this->request->post['socnetauth2_default_country'])) {
			$this->data['socnetauth2_default_country'] = $this->request->post['socnetauth2_default_country'];
		} elseif ($this->config->has('module_socnetauth2_default_country')) { 
			$this->data['socnetauth2_default_country'] = $this->config->get('module_socnetauth2_default_country');
		} else {
			$this->data['socnetauth2_default_country'] = $this->config->get('config_country_id');
		}
		
		if (isset($this->request->post['socnetauth2_confirm_newsletter'])) {
			$this->data['socnetauth2_confirm_newsletter'] = $this->request->post['socnetauth2_confirm_newsletter'];
		} else { 
			$this->data['socnetauth2_confirm_newsletter'] = $this->config->get('module_socnetauth2_confirm_newsletter');
		}
		
		$this->load->model('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		/* end 2507 */
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => ' :: '
   		);
		 
		
		$this->data['action'] = $this->url->link('extension/module/socnetauth2', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL');

		if (isset($this->request->post['socnetauth2_admin'])) {
			$this->data['socnetauth2_admin'] = $this->request->post['socnetauth2_admin'];
		} else {
			$this->data['socnetauth2_admin'] = $this->config->get('module_socnetauth2_admin');
		}	
		
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		if( substr(preg_replace("/[^0-9]/", "", VERSION) , 0, 4 ) >=2100 )
		{
			$this->load->model('customer/customer_group');
			$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups(array());
		}
		else
		{
			$this->load->model('sale/customer_group');
			$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(array());
		}
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/socnetauth2', $this->data));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/socnetauth2')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if( !empty( $this->request->post['socnetauth2_sort'] ) )
		{
			$this->request->post['socnetauth2_sort'] = serialize($this->request->post['socnetauth2_sort']);
		}
		
		
		
		if (!$this->error) 
		{
			
			if( !empty( $this->request->post['socnetauth2_modules'] ) )
			{
				$this->request->post['socnetauth2_module'] = $this->request->post['socnetauth2_modules'];
			}
			else
			{
				//$this->request->post['socnetauth2_module'] = '';
			}
			
			if( !empty( $this->request->post['socnetauth2_popups'] ) )
			{
				for($i=0; $i<count($this->request->post['socnetauth2_popups']); $i++ )
				{
					$this->request->post['socnetauth2_popups'][$i]['position']   = 'content_top';
					$this->request->post['socnetauth2_popups'][$i]['sort_order'] = 1;
				}
					
				$this->request->post['socnetauth2_popup_module'] = $this->request->post['socnetauth2_popups'];
			}
			else
			{
				//$this->request->post['socnetauth2_popup_module'] = '';
			}
			
			
			if( empty($this->request->post['socnetauth2_shop_folder_in_img']) )
			$this->request->post['socnetauth2_shop_folder_in_img'] = 0;
			
			
			if( empty($this->request->post['socnetauth2_shop_folder_in_url']) )
			$this->request->post['socnetauth2_shop_folder_in_url'] = 0;
		
			if( empty($this->request->post['socnetauth2_shop_folder_in_redirect']) )
			$this->request->post['socnetauth2_shop_folder_in_redirect'] = 0;
			
			
			if( empty($this->request->post['socnetauth2_admin_customer']) )
			$this->request->post['socnetauth2_admin_customer'] = 0;
			
			if( empty($this->request->post['socnetauth2_admin_customer_list']) )
			$this->request->post['socnetauth2_admin_customer_list'] = 0;
			
			if( empty($this->request->post['socnetauth2_admin_order']) )
			$this->request->post['socnetauth2_admin_order'] = 0;
			
			if( empty($this->request->post['socnetauth2_admin_order_list']) )
			$this->request->post['socnetauth2_admin_order_list'] = 0;
			
			if( empty($this->request->post['socnetauth2_show_standart_auth']) )
			$this->request->post['socnetauth2_show_standart_auth'] = 0;
			
			if( !empty($this->request->post['socnetauth2_label']) )
			$this->request->post['socnetauth2_label'] = serialize( $this->request->post['socnetauth2_label'] );
			
			$this->request->post = $this->model_extension_module_socnetauth2->makeCode($this->request->post);
			
			return true;
		} else {
			return false;
		}	
	}
	
	public function vkontakte_retargeting()
	{
		$this->load->model('extension/module/socnetauth2');
		$this->model_extension_module_socnetauth2->checkDB();
		
		$filename = 'vkontakte_retargeting.txt'; // of course find the exact filename....        
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false); // required for certain browsers 
		header('Content-Type: text/plain');

		header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
		header('Content-Transfer-Encoding: binary');
		#header('Content-Length: ' . filesize($filename));

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "socnetauth2_customer2account 
								   WHERE provider = 'vkontakte'");
		if( $query->rows )
		{
			foreach($query->rows as $row)
			{
				echo $row['identity']."\r\n";
			}
		}
		else
		{
			echo "empty list :(";
		}
		
		exit;
		
	}
	
	protected function cmp($a, $b)
	{
		if($a['sort_order'] == $b['sort_order']) {
			return 0;
		}
	
		return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
	}
	
	public function facebook_retargeting()
	{
		$this->load->model('extension/module/socnetauth2');
		$this->model_extension_module_socnetauth2->checkDB();
		
		$filename = 'facebook_retargeting.txt'; // of course find the exact filename....        
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false); // required for certain browsers 
		header('Content-Type: text/plain');

		header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
		header('Content-Transfer-Encoding: binary');
		#header('Content-Length: ' . filesize($filename));

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "socnetauth2_customer2account 
								   WHERE provider = 'facebook'");
		if( $query->rows )
		{
			foreach($query->rows as $row)
			{
				echo $row['identity']."\r\n";
			}
		}
		else
		{
			echo "empty :(";
		}
		
		exit;
	}
	/* end r1 */
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

}
?>