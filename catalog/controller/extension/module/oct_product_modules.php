<?php
/**********************************************************/
/*	@copyright	OCTemplates 2015-2021.					  */
/*	@support		https://octemplates.net/			  */
/*	@license		LICENSE.txt							  */
/**********************************************************/

class ControllerExtensionModuleOctProductModules extends Controller {
    public function index($setting) {
        static $module = 0;

		$this->load->language('octemplates/module/oct_product_modules');

        $data['heading_title'] = (isset($setting['heading'][(int)$this->config->get('config_language_id')]) && !empty($setting['heading'][(int)$this->config->get('config_language_id')])) ? $setting['heading'][(int)$this->config->get('config_language_id')] : $this->language->get('heading_title');
        $data['link']          = (isset($setting['link'][(int)$this->config->get('config_language_id')]) && !empty($setting['link'][(int)$this->config->get('config_language_id')])) ? $setting['link'][(int)$this->config->get('config_language_id')] : false;

        $data['position'] = isset($setting['position']) ? $setting['position'] : '';
        $data['show_type'] = isset($setting['show_type']) ? $setting['show_type'] : '';
        $data['module_id'] = isset($setting['module_id']) ? $setting['module_id'] : false;
        $data['page'] = isset($setting['page']) ? $setting['page'] : 1;

        $this->load->model('catalog/product');
        $this->load->model('octemplates/module/oct_product_modules');
        $this->load->model('tool/image');

        $data['oct_path'] = 0;

        if (isset($this->request->get['path'])) {
            $data['oct_path'] = $this->request->get['path'];
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = [];
        }

        if (isset($setting['oct_path']) && $setting['oct_path']) {
            $parts = explode('_', (string)$setting['oct_path']);
        }

        $category_id = (int)array_pop($parts);

        $products = [];

        if (empty($setting['limit'])) {
            $setting['limit'] = '10/6/6';
        }

        $limit = (isset($setting['limit']) && !empty($setting['limit'])) ? explode('/', $setting['limit']) : explode('/', '10/6/6');

        if (count($limit) == 1) {
            $limit = explode('/', '10/6/6');
        }

        if (isset($data['oct_isMobile'])) {
            $setting['limit'] = (isset($limit[2]) && !empty($limit[2])) ? trim($limit[2]) : trim($setting['limit']);
        } elseif (isset($data['oct_isTablet'])) {
            $setting['limit'] = (isset($limit[1]) && !empty($limit[1])) ? trim($limit[1]) : trim($setting['limit']);
        } else {
            $setting['limit'] = (isset($limit[0]) && !empty($limit[0])) ? trim($limit[0]) : trim($setting['limit']);
        }

        $filter_data = [
            'filter_category_id' 	=> isset($setting['module_categories']) && $setting['module_categories'] ? $setting['module_categories'] : [],
            'filter_sub_category' 	=> true,
            'sort'               	=> (isset($setting['sort']) && !empty($setting['sort'])) ? $setting['sort'] : 'pd.name',
			'order'              	=> (isset($setting['order']) && !empty($setting['order'])) ? $setting['order'] : 'ASC',
			'quantity_view'         => (isset($setting['quantity_view']) && !empty($setting['quantity_view'])) ? false : true,
			'only_specials'         => (isset($setting['only_specials']) && !empty($setting['only_specials'])) ? true : false,
			'product_ids'           => (isset($setting['product']) && !empty($setting['product'])) ? $setting['product'] : [],
            'start'					=> ((int)$data['page'] - 1) * (int)$setting['limit'],
            'limit'					=> (int)$setting['limit']
        ];

        $filter_data_pages = [
            'filter_category_id' 	=> isset($setting['module_categories']) && $setting['module_categories'] ? $setting['module_categories'] : [],
            'filter_sub_category' 	=> true,
            'sort'               	=> (isset($setting['sort']) && !empty($setting['sort'])) ? $setting['sort'] : 'pd.name',
			'order'              	=> (isset($setting['order']) && !empty($setting['order'])) ? $setting['order'] : 'ASC',
			'quantity_view'         => (isset($setting['quantity_view']) && !empty($setting['quantity_view'])) ? false : true,
			'only_specials'         => (isset($setting['only_specials']) && !empty($setting['only_specials'])) ? true : false,
			'product_ids'           => (isset($setting['product']) && !empty($setting['product'])) ? $setting['product'] : [],
            'start'					=> ((int)$data['page']) * (int)$setting['limit'],
            'limit'					=> (int)$setting['limit']
        ];

		if (isset($setting['module_show_in_categories']) && $setting['module_show_in_categories']) {
            if (in_array($category_id, $setting['module_show_in_categories'])) {
                $products = $this->model_octemplates_module_oct_product_modules->getProducts($filter_data);

                $product_total = count($this->model_octemplates_module_oct_product_modules->getProducts($filter_data_pages));
            }
        } else {
            $products = $this->model_octemplates_module_oct_product_modules->getProducts($filter_data);

            $product_total = count($this->model_octemplates_module_oct_product_modules->getProducts($filter_data_pages));
        }

        $data['products'] = [];

        if (!empty($products)) {
            foreach ($products as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);

                if ($product_info) {
	                $width = (isset($setting['width']) && !empty($setting['width'])) ? $setting['width'] : $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
	                $height = (isset($setting['height']) && !empty($setting['height'])) ? $setting['height'] : $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');

                    if ($product_info['image'] && file_exists(DIR_IMAGE.$product_info['image'])) {
						$image = $this->model_tool_image->resize($product_info['image'], $width, $height);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $width, $height);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = (int)$product_info['rating'];
					} else {
						$rating = false;
					}

                    if ($product_info['quantity'] <= 0) {
        				$stock = $product_info['stock_status'];
        			} else {
        				$stock = false;
        			}

        			$can_buy = true;

        			if ($product_info['quantity'] <= 0 && !$this->config->get('config_stock_checkout')) {
        				$can_buy = false;
        			} elseif ($product_info['quantity'] <= 0 && $this->config->get('config_stock_checkout')) {
        				$can_buy = true;
        			}

        			$oct_grayscale = ($this->config->get('theme_oct_ultrastore_no_quantity_grayscale') && !$can_buy) ? true : false;

                    $oct_atributes = false;

                    if (isset($setting['product_atributes']) && $setting['product_atributes']) {

                        $limit_attr  = $setting['product_atributes_limit'] ? $setting['product_atributes_limit'] : 5;
                        $oct_atributes = $this->model_catalog_product->getOctProductAttributes($product_info['product_id'], $limit_attr);
                    }

					$data['products'][] = [
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
                        'width'       => $setting['width'],
                        'height'      => $setting['height'],
						'name'        => $product_info['name'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
                        'oct_atributes'   => $oct_atributes,
						'tax'         => $tax,
                        'stock'     => $stock,
            			'can_buy'   => $can_buy,
            			'oct_grayscale'  => $oct_grayscale,
						'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'      => $rating,
                        'reviews'	  => $product_info['reviews'],
                        'quantity'	  => ($product_info['quantity'] <= 0 || !isset($setting['quantity_show'])) ? true : false,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					];
                }
            }
        }

        $data['module'] = $module++;

        if ($data['products']) {
            $data['module_name'] = "mp";

            $data['show_more'] = isset($setting['show_more']) ? (isset($product_total) && $product_total) ? $setting['show_more'] : false : false;

            return (isset($setting['oct_ajax']) && $setting['oct_ajax']) ? $this->response->setOutput($this->load->view('octemplates/module/oct_products_modules', $data)) : $this->load->view('octemplates/module/oct_products_modules', $data);
        }
    }

    public function modulePage() {
        if (isset($this->request->post['module_id']) && isset($this->request->post['page']) && isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->load->model('setting/module');

            $module_id = (int)$this->request->post['module_id'];
            $page = (int)$this->request->post['page'];
            $path = (string)$this->request->post['oct_path'];

            $setting_info = $this->model_setting_module->getModule($module_id);

			if ($setting_info && isset($setting_info['status']) && $setting_info['status']) {
				$setting_info['module_id'] = $module_id;
				$setting_info['page'] = $page;
				$setting_info['oct_ajax'] = true;
				$setting_info['oct_path'] = $path;

				$output = $this->index($setting_info);

				if ($output) {
					return $output;
				}
			}
        }
    }
}
