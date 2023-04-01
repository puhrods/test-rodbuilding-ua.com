<?php
class ControllerSaleOmproFooter extends Controller {
	public function index() {
		$data['strtoken'] = $this->ompro->strtoken;

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_close'] = $this->language->get('text_close');
		$data['text_alert_error'] = $this->language->get('text_alert_error');

		$data = array_merge($data, $this->load->language('sale/ompro'));

		$langCode = substr(strtolower($this->omproapi->getLanguageCode()), 0, 2);
		$data['langCode'] = $langCode == 'en' || $langCode == 'ru' ? $langCode : 'ru';

		$config_cur = $this->config->get('config_currency');
		$data['config_cur_decimals'] = $this->currency->getDecimalPlace($config_cur);
		$data['config_cur_decimal_point'] = $this->language->get('decimal_point');
		$data['config_cur_thousand_point'] = $this->language->get('thousand_point');
		$data['config_cur_sym_left'] = $this->currency->getSymbolLeft($config_cur);
		$data['config_cur_sym_right'] = $this->currency->getSymbolRight($config_cur);

		$action_urls = $this->omproapi->action_urls();
		foreach ($action_urls as $var => $url) {
			$data[$var] = $url;
		}

		$btn_module_statuses = $this->omproapi->btn_module_statuses();
		foreach ($btn_module_statuses as $var => $url) {
			$data[$var] = $url;
		}

		$data['catalog'] = $this->ompro->catalog;

		$action_list = array('change_status','create_invoiceno','edit_reward','edit_comission');
		foreach ($action_list as $value) {
			$data[$value.'_status'] = false;
		}

		$data['user_id'] = $this->session->data['user_id'];
		$access_actions = $this->ompro->getSettingGroupAccessActions($this->user->getGroupId());

		if ($access_actions) {
			foreach ($access_actions as $action) {
				$data[$action.'_status'] = true;
			}
		}

		$data['version'] = $this->omproapi->version;
		$data['ocversion'] = $this->ompro->ocversion;
		$ending = $data['ocversion'] >= 230 ? '' : '.tpl';
		return $this->load->view('sale/ompro/ompro_footer'.$ending, $data);
	}

}