<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerApiBundleExpert extends Controller {
	public function getBundleExpert() {
		$this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

		if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
            $json['bundle_expert'] = $this->load->controller('module/bundle_expert/getBundleExpert', true);
            $json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    public function getKitForm() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('module/bundle_expert/getKitForm', true);
        }
    }

    public function getKitFormFromCart() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('module/bundle_expert/getKitFormFromCart', true);
        }
    }

    public function getKitItemProducts() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('module/bundle_expert/getKitItemProducts', true);
        }

    }

    public function getKitFormProduct() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('module/bundle_expert/getKitFormProduct', true);
        }

    }

    public function getKitTotal() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('checkout/bundle_expert/get_kit_total', true);
        }

    }

    public function addToCart() {
        $this->load->language('api/cart');

        $json = array();

        $this->bundle_expert->admin_api_mode = true;

        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->controller('checkout/bundle_expert/add_to_cart', true);
        }

    }

}