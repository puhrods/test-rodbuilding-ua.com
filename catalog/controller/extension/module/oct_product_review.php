<?php
/**********************************************************/
/*	@copyright	OCTemplates 2015-2019					  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**********************************************************/

class ControllerExtensionModuleOctProductReview extends Controller {
    public function index($setting) {
        static $module = 0;

        $this->load->language('octemplates/module/oct_product_review');

        $data['heading_title'] = $this->language->get('heading_title');

        $this->load->model('catalog/product');
        $this->load->model('octemplates/module/oct_product_review');

        $this->load->model('tool/image');

        $data['reviews'] = [];
        $data['reviews_count'] = 2;

        if (!$setting['limit']) {
            $setting['limit'] = 4;
        }

        $filter_data = [
            'limit' => $setting['limit'],
            'start' => 0
        ];

        $data['position'] = isset($setting['position']) ? $setting['position'] : '';

        $reviews = $this->model_octemplates_module_oct_product_review->getAllReviews($filter_data);

        foreach ($reviews as $review) {
            if ($review['image']) {
                $image = $this->model_tool_image->resize($review['image'], $setting['width'], $setting['height']);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
            }

			$product_info = $this->model_catalog_product->getProduct($review['product_id']);

			if ((float)$product_info['special']) {
				$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$special = false;
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

            $data['reviews'][] = [
                'product_id'	=> $review['product_id'],
                'thumb'			=> $image,
                'width'         => $setting['width'],
                'height'        => $setting['height'],
                'name'			=> $review['name'],
                'author'		=> $review['author'],
                'special'		=> $special,
                'price'			=> $price,
                'date'			=> date($this->language->get('date_format_short'), strtotime($review['date_added'])),
                'text'			=> utf8_substr(strip_tags(html_entity_decode($review['text'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..',
                'rating'		=> $review['rating'],
                'href'			=> $this->url->link('product/product', 'product_id=' . $review['product_id'], true)
            ];
        }

        $data['module'] = $module++;

        if (!empty($data['reviews'])) {
	        $data['reviews_count'] = count($data['reviews']);

            return $this->load->view('octemplates/module/oct_product_review', $data);
        }
    }
}
