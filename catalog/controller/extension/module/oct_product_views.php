<?php
/**********************************************************/
/*	@copyright	OCTemplates 2015-2019.					  */
/*	@support		https://octemplates.net/			  */
/*	@license		LICENSE.txt							  */
/**********************************************************/

class ControllerExtensionModuleOctProductViews extends Controller {
    public function index($setting) {
        static $module = 0;

		$this->load->language('octemplates/module/oct_product_views');

        $data['heading_title'] = (isset($setting['heading'][(int)$this->config->get('config_language_id')]) && !empty($setting['heading'][(int)$this->config->get('config_language_id')])) ? $setting['heading'][(int)$this->config->get('config_language_id')] : $this->language->get('heading_title');

        $data['position'] = isset($setting['position']) ? $setting['position'] : '';

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

        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $data['products'] = [];
        $products = [];

		if (isset($this->request->cookie['oct_product_views'])) {
            $products = explode(',', $this->request->cookie['oct_product_views']);
        } elseif (isset($this->session->data['oct_product_views'])) {
            $products = $this->session->data['oct_product_views'];
        }

        if (isset($this->request->cookie['viewed'])) {
            $products = array_merge($products, explode(',', $this->request->cookie['viewed']));
        } elseif (isset($this->session->data['viewed'])) {
            $products = array_merge($products, $this->session->data['viewed']);
        }

		$products = array_slice($products, 0, (int)$setting['limit']);

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

					$data['products'][] = [
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
                        'width'       => $width,
                        'height'      => $height,
						'name'        => $product_info['name'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
                        'stock'       => $stock,
            			'can_buy'     => $can_buy,
            			'oct_grayscale'  => $oct_grayscale,
						'tax'         => $tax,
						'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					];
                }
            }
        }

        $data['module'] = $module++;

        if ($data['products']) {
            $data['module_name'] = "pv";

            return $this->load->view('octemplates/module/oct_products_modules', $data);
        }
    }
}
