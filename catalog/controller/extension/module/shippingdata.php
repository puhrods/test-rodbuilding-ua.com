<?php
class ControllerModuleShippingData extends Controller {
	public function getShippingData() {
		$json = array();

		if (isset($this->request->post['shipping'])) {
			$shipping = $this->request->post['shipping'];
		} else {
			$shipping = '';
		}
		
		if (isset($this->request->post['action'])) {
			$action = $this->request->post['action'];
		} else {
			$action = '';
		}
		
		if (isset($this->request->post['filter'])) {
			$filter = $this->request->post['filter'];
		} else {
			$filter = '';
		}
		
		if (isset($this->request->post['search'])) {
			$search = $this->request->post['search'];
		} else {
			$search = '';
		}

        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->model('extension/module/shippingdata');

            $model_name = 'model_extension_module_shippingdata';
        } else {
            $this->load->model('module/shippingdata');

            $model_name = 'model_module_shippingdata';
        }

		if (strpos($shipping, 'novaposhta.') !== false) {
            require_once(DIR_SYSTEM . 'helper/novaposhta.php');

            $novaposhta = new NovaPoshta($this->registry);

            if ($action == 'getCities') {
                if ($filter) {
                    $this->load->model('localisation/zone');

                    $zone_info = $this->model_localisation_zone->getZone($filter);

                    if ($zone_info) {
                        $filter = $novaposhta->getAreaRef($zone_info['name']);
                    }
                }

                $json = $this->$model_name->getNovaPoshtaCities($filter, $search);
            } elseif ($action == 'getWarehouses') {
                $json = $this->$model_name->getNovaPoshtaWarehouses($filter, $search);
            }
        }
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

class ControllerExtensionModuleShippingData extends ControllerModuleShippingData {

}