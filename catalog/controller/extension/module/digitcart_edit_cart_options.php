<?php
class ControllerExtensionModuleDigitCartEditCartOptions extends Controller {
	public function index() {
		$this->load->language('extension/module/digitcart_edit_cart_options');

		$this->load->language('product/product');

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$data['product_id'] = $product_id;

		if (isset($this->request->get['cart_id'])) {
			$cart_id = (int)$this->request->get['cart_id'];
		} else {
			$cart_id = 0;
		}

		$data['cart_id'] = $cart_id;

		$data['cart_option_values'] = $this->getProductOptionsInCart($this->request->get['cart_id']);

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$this->load->model('tool/image');

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			$this->response->setOutput($this->load->view('extension/module/digitcart_edit_cart_options', $data));
		} else {
			echo $this->language->get('text_not_found');
		}
	}

	protected function getProductOptionsInCart ($cart_id){
		$this->load->model('tool/upload');

		$products = $this->cart->getProducts();

		$product_options = array();

		foreach ($products as $product) {
			if($product['cart_id'] == $cart_id){
				foreach($product['option'] as $option){
					$upload_name = '';

					if ($option['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$upload_name = $upload_info['name'];
						}
					}

					$product_options[] = array(
						'product_option_id'			=> $option['product_option_id'],
						'product_option_value_id'	=> $option['product_option_value_id'],
						'option_id'					=> $option['option_id'],
						'option_value_id'			=> $option['option_value_id'],
						'name' 						=> $option['name'],
						'upload_name' 				=> $upload_name,
						'value' 					=> $option['value'],
						'type' 						=> $option['type'],
						'quantity' 					=> $option['quantity'],
						'subtract' 					=> $option['subtract'],
						'price' 					=> $option['price'],
						'price_prefix' 				=> $option['price_prefix'],
						'points' 					=> $option['points'],
						'points_prefix' 			=> $option['points_prefix'],
						'weight' 					=> $option['weight'],
						'weight_prefix' 			=> $option['weight_prefix']
					);
				}

				break;
			}
		}

		return $product_options;
	}
}