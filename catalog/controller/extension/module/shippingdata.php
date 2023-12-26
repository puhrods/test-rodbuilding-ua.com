<?php
class ControllerModuleShippingData extends Controller {
	public function getShippingData() {
		$json = array();

        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->model('extension/module/shippingdata');

            $model_name = 'model_extension_module_shippingdata';
        } else {
            $this->load->model('module/shippingdata');

            $model_name = 'model_module_shippingdata';
        }

		$shipping = $this->$model_name->getShippingMethod();

		if (isset($this->request->post['action'])) {
			$action = $this->request->post['action'];
		} else {
			$action = '';
		}
		
		if (isset($this->request->post['filter'])) {
			$filter = trim($this->request->post['filter']);
		} else {
			$filter = '';
		}
		
		if (isset($this->request->post['search'])) {
			$search = trim($this->request->post['search']);
		} else {
			$search = '';
		}

		if ($shipping['method'] == 'novaposhta') {
            if ($action == 'getCities') {
                if ($shipping['sub_method'] == 'department' || $shipping['sub_method'] == 'poshtomat') {
                    $json = $this->$model_name->getNovaPoshtaCities($filter, $search);
                } elseif ($shipping['sub_method'] == 'doors') {
                    $json = $this->$model_name->getNovaPoshtaSettlements($filter, $search);
                }
            } elseif ($action == 'getDepartments') {
                if ($shipping['sub_method'] == 'department') {
                    $json = $this->$model_name->getNovaPoshtaDepartments($filter, $search);
                } elseif ($shipping['sub_method'] == 'poshtomat') {
                    $json = $this->$model_name->getNovaPoshtaPoshtomats($filter, $search);
                }
            } elseif ($action == 'getStreets') {
                $json = $this->$model_name->getNovaPoshtaStreets($filter, $search);
            }
        } elseif ($shipping['method'] == 'ukrposhta') {
            if ($action == 'getCities') {
                $json = $this->$model_name->getUkrPoshtaCities($filter, $search);
            } elseif ($action == 'getDepartments') {
                $json = $this->$model_name->getUkrPoshtaDepartments($filter, $search, $shipping['sub_method']);
            } elseif ($action == 'getPostCodes') {
                $json = $this->$model_name->getUkrPoshtaPostCodes($search, $shipping['sub_method']);
            }
        } elseif ($shipping['method'] == 'justin') {
            if ($action == 'getCities') {
                $json = $this->$model_name->getJustinCities($filter, $search);
            } elseif ($action == 'getDepartments') {
                $json = $this->$model_name->getJustinDepartments($filter, $search);
            }
        }
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

class ControllerExtensionModuleShippingData extends ControllerModuleShippingData {

}