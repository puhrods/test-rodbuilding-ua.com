<?php
class ControllerSaleOmproWidget extends Controller {
	private $error = array();

	public function init() {
		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}
	}

	// jvectormap
	public function map_html() {
		return '<div omprowidget="map" style="width: 100%; height: 100%;"></div>';
	}

	public function map_ru_mill_html() {
		return '<div omprowidget="map_ru_mill" style="width: 100%; height: 100%;"></div>';
	}

	public function map_europe_mill_html() {
		return '<div omprowidget="map_europe_mill" style="width: 100%; height: 100%;"></div>';
	}

	public function map_asia_mill_html() {
		return '<div omprowidget="map_asia_mill" style="width: 100%; height: 100%;"></div>';
	}

	public function map() {
		$this->getMap('world_mill_en');
	}

	public function mapRuMill() {
		$this->getMap('ru_mill');
	}

	public function mapEuropeMill() {
		$this->getMap('europe_mill');
	}

	public function mapAsiaMill() {
		$this->getMap('asia_mill');
	}

	public function getMap($map = 'world_mill_en') {
		$this->init();
		$json = array();
		$get_coutry_list_map = array('world_mill_en','europe_mill','asia_mill');

		if ($map == 'ru_mill') {
			$results = $this->ompro->getTotalOrdersByZone();
		} else {
			$results = $this->ompro->getTotalOrdersByCountry();
		}

		foreach ($results as $result) {
			$json[strtoupper($result['code'])] = array(
				'total'  => $result['total'],
				'amount' => $this->currency->format($result['amount'], $this->config->get('config_currency'))
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function donut_chart_html() {
		$strtoken = $this->strToken();
		$data['ompro_widget_donut_chart'] = $this->url->link('sale/ompro_widget/donut_chart', $strtoken, 'SSL');
		$data['donutData'] = $this->donut_chart();
		$ending = $this->getOcVersion() >= 230 ? '' : '.tpl';
		return $this->load->view('sale/ompro/ompro_widget_donut_chart'.$ending, $data);
	}

	public function donut_chart() {
		$this->init();

		$user_group_id = $this->user->getGroupId();
		$setting_user_group = $this->ompro->getSettingGroup($user_group_id);

		$user_statuses = array();
		if (!empty($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders']['order_statuses'])) {
			$user_statuses = $setting_user_group['select_orders']['order_statuses'];
		}

		$filter_data = array();
		$filter_data['filter_order_status'] = $user_statuses;
		$results = $this->ompro->getTotalOrdersByOrderStatusId($filter_data);
		$color_set = array();

		if (isset($setting_user_group['order_statuses'])) {
			$color_set = $setting_user_group['order_statuses'];
		}

		$donutData = array();
		foreach ($results as $result) {
			$donutData[] = array(
				'data'  => $result['total'],
				'label'  => $result['order_status_id'] == '0' ? 'Брошенные' : $result['order_status'],
				'bgcolor'  => isset($color_set[$result['order_status_id']]['text_color_id']) && !empty($color_set[$result['order_status_id']]['text_color_id']) ? '#'.$color_set[$result['order_status_id']]['text_color_id'] : '',
				'color'  => isset($color_set[$result['order_status_id']]['back_color_id']) && !empty($color_set[$result['order_status_id']]['back_color_id']) ? '#'.$color_set[$result['order_status_id']]['back_color_id'] : ''
			);
		}

		return json_encode($donutData);
	}

	public function getOcVersion() {
		$version = 0;
		$version = explode('.', VERSION);
		$ocVersion = floatval($version[0].$version[1].$version[2].'.'.(isset($version[3]) ? $version[3] : 0));
		return $ocVersion;
	}

	public function strToken() {
		if ($this->getOcVersion() >= 300) {
			return 'user_token='.$this->session->data['user_token'];
		} else {
			return 'token='.$this->session->data['token'];
		}
	}

}