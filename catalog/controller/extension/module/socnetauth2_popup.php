<?php
class ControllerExtensionModuleSocnetauth2popup extends Controller {
	private $data;
	
	public function index($setting) {
	
		$this->load->language('extension/module/socnetauth2');
		
		if ($this->customer->isLogged()) {
	  		return false;
    	}
		
		if( !$this->config->get('module_socnetauth2_status') ) return false;
		
		if( empty( $_COOKIE['show_socauth2_popup'] ) )
		{
			$this->data['show_socauth2_popup'] = 1;
		}
		else
		{
			$this->data['show_socauth2_popup'] = 0;
		}
		
		$this->data['socnetauth2_mobile_control'] = $this->config->get('module_socnetauth2_mobile_control');
		
		$this->load->model('extension/module/socnetauth2');
		$this->data['count_socnets'] = $this->model_extension_module_socnetauth2->getCountEnabledSocnets();
		$this->data['count_socnets_width'] = ceil( 92 / $this->data['count_socnets']);
		$this->data['socnetauth2_socnets'] = $this->model_extension_module_socnetauth2->getEnabledSocnets();
		
		if( $this->config->get('module_socnetauth2_shop_folder') ) 
			$this->data['socnetauth2_shop_folder'] = '/'.$this->config->get('module_socnetauth2_shop_folder');
		else
			$this->data['socnetauth2_shop_folder'] = '';
		
      	$this->data['widget_heading_title'] = $this->language->get('widget_heading_title_popup');
      	$this->data['widget_text_skip'] = $this->language->get('widget_text_skip');
		
		$this->load->model('tool/image');
		$socnetauth2_socnets_icons = $this->custom_unserialize( $this->config->get( 'module_socnetauth2_socnets_icons' ) );
		
		foreach($this->data['socnetauth2_socnets'] as $i=>$socnet)
		{
			$this->data['socnetauth2_socnets'][$i]['img_url'] = '';
			
			if( !empty($socnetauth2_socnets_icons[ $socnet['key'] ]['image']) )
			{
				$this->data['socnetauth2_socnets'][$i]['img_url'] = $this->model_tool_image->resize( 
						$socnetauth2_socnets_icons[ $socnet['key'] ]['image'], 
						75, 
						75 
				);
			}
			else
			{
				$this->data['socnetauth2_socnets'][$i]['img_url'] = '/socnetauth2/icons/'.$socnetauth2_socnets[$i]['short'].'45.png';
			} 
			
			
			if( $socnet['key'] == 'telegram' )
			{
				$rand = rand(1,1000000000);
				$this->data['socnetauth2_socnets'][$i]['is_close'] = false;
				
				$this->data['socnetauth2_socnets'][$i]['link'] = 'javascript: showTelegramWindow('.$rand.')';
						
				$socnetauth2_telegram_html = html_entity_decode($this->config->get("module_socnetauth2_telegram_html"), ENT_QUOTES, 'UTF-8');
				
				$socnetauth2_telegram_html = str_replace(
					'id="sna_telegram_popup"', 
					'id="sna_telegram_popup'.$rand.'"',
					$socnetauth2_telegram_html
				);
				
				$socnetauth2_telegram_html = str_replace("{rand}", $rand, $socnetauth2_telegram_html);
				
				$this->data['socnetauth2_socnets'][$i]['js_block'] = $socnetauth2_telegram_html;
				
			}
			else
			{
				$this->data['socnetauth2_socnets'][$i]['is_close'] = true;
				$this->data['socnetauth2_socnets'][$i]['js_block'] = '';
			}
		}
		
		return $this->load->view('extension/module/socnetauth2_popup', $this->data);
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
}
?>