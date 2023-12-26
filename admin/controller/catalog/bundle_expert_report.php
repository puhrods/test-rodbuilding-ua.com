<?php
class ControllerCatalogBundleExpertReport extends Controller {

    private $token_name = array();
    private $token_value = array();
    private $path_extension_module = '';

    public function __construct($registry) {

        parent::__construct($registry);

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->token_name = 'token=';
            $this->token_value = $this->session->data['token'];
        }else{
            $this->token_name = 'user_token=';
            $this->token_value = $this->session->data['user_token'];
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
            $this->path_extension_module = 'extension/module';
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $this->path_extension_module = 'extension/extension';
            } else {
                $this->path_extension_module = 'marketplace/extension';
            }
        }
    }

    public function get_bundle_products($order_id, &$order_total_sum, &$order_tax, $url='') {
        $order_products = $this->model_sale_order->getOrderProducts($order_id);

        $products = array();

        $order_total_sum = 0;
        $order_tax = 0;

        $order_info = $this->model_sale_order->getOrder($order_id);

        //Оставляем только товары из комплектов
        foreach ($order_products as $order_product){
            $cart_kit_info = $this->model_catalog_bundle_expert_kit->getOrderProductKitInfo($order_product['order_id'], $order_product['order_product_id'], $order_product['product_id']);
            $product_as_kit_info = $this->model_catalog_bundle_expert_kit->getOrderProductAsKitInfo($order_product['order_id'], $order_product['order_product_id'], $order_product['product_id']);

            if($cart_kit_info){
                $order_product['cart_kit_info'] = $cart_kit_info;
                $products[] = $order_product;
            }
            if($product_as_kit_info){
                $order_product['product_as_kit_info'] = $product_as_kit_info;
                $products[] = $order_product;
            }
        }
        foreach ($products as $index=>$product){
//            $order_total_sum += $product['total'];
//            $order_tax += $product['tax'];


            $products[$index]['price'] = $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
            $products[$index]['price_value'] = ($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0));
//            $products[$index]['price'] = ($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0));
            $products[$index]['total'] = $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
//            $products[$index]['total'] = ($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0));
//            $products[$index]['price'] = $this->currency->format($product['price'], $this->config->get('config_currency'));
        }

        $products = $this->bundle_expert->orderInfoProductListPrepare($products);

        foreach ($products as $index=>$product){
            $order_total_sum += (int)$product['price_value'];
            $order_tax += $product['tax'];

            $products[$index]['href'] = $this->url->link('catalog/product/edit', $this->token_name . $this->token_value . '&product_id=' . $product['product_id'] . $url, true);

//            $products[$index]['price'] = $this->currency->format($product['price'], $this->config->get('config_currency'));

            $products[$index]['name_2'] = $products[$index]['name'] . ", " . $products[$index]['price'] ." x " . $products[$index]['quantity'];

            $option_data = array();

            $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

            foreach ($options as $option) {
                if ($option['type'] != 'file') {
                    $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => $option['value'],
                        'type'  => $option['type']
                    );
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                    if ($upload_info) {
                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => $upload_info['name'],
                            'type'  => $option['type'],
                            'href'  => $this->url->link('tool/upload/download', $this->token_name . $this->token_value . '&code=' . $upload_info['code'], true)
                        );
                    }
                }
            }

            if(isset($product['product_as_kit'])) {
                if ($product['product_as_kit']) {
                    if(isset($product['option'])){
                        foreach ($product['option'] as $option) {
                            if ($option['type'] != 'file') {
                                $option_data[] = array(
                                    'name' => $option['name'],
                                    'value' => $option['value'],
                                    'type' => $option['type']
                                );
                            } else {
                                $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                                if ($upload_info) {
                                    $option_data[] = array(
                                        'name' => $option['name'],
                                        'value' => $upload_info['name'],
                                        'type' => $option['type'],
                                        'href' => $this->url->link('tool/upload/download', $this->token_name . $this->token_value . '&code=' . $upload_info['code'], true)
                                    );
                                }
                            }
                        }
                    }

                }
            }

            $products[$index]['option'] = $option_data;

        }

        return $products;
    }

    public function index() {
        $this->load->language('catalog/bundle_expert_report');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
        }

        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } else {
            $filter_date_end = date('Y-m-d');
        }

//		if (isset($this->request->get['filter_group'])) {
//			$filter_group = $this->request->get['filter_group'];
//		} else {
//			$filter_group = 'week';
//		}

        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = 0;
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

//		if (isset($this->request->get['filter_group'])) {
//			$url .= '&filter_group=' . $this->request->get['filter_group'];
//		}

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/bundle_expert_report', $this->token_name . $this->token_value . $url, true)
        );

        $data['invoice'] = $this->url->link('catalog/bundle_expert_report/invoice', $this->token_name . $this->token_value , true);

        $this->load->model('catalog/bundle_expert_report');
        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('sale/order');
        $this->load->model('tool/upload');

        $data['orders'] = array();
        $limit = $this->config->get('config_limit_admin');
//		$limit = 1000;

        $filter_data = array(
            'filter_date_start'	     => $filter_date_start,
            'filter_date_end'	     => $filter_date_end,
//			'filter_group'           => $filter_group,
            'filter_order_status_id' => $filter_order_status_id,
            'start'                  => ($page - 1) * $limit,
            'limit'                  => $limit,
        );

        $order_total = $this->model_catalog_bundle_expert_report->getTotalOrders($filter_data);

        $results = $this->model_catalog_bundle_expert_report->getOrders($filter_data);

        foreach ($results as $result) {

            $order_info = $this->model_sale_order->getOrder($result['order_id']);

            $order_total_sum = 0;
            $order_tax = 0;

            $products = $this->get_bundle_products($result['order_id'], $order_total_sum, $order_tax, $url);


            $data['totals'] = array();

            $totals = $this->model_sale_order->getOrderTotals($order_info['order_id']);

            foreach ($totals as $total) {
                if($total['code']=='bundle_expert_total'){
                    $order_total_sum += $total['value'];
                }

            }
            $result = $order_info;

            if($products){
                $data['orders'][] = array(
                    'order_id'      => $result['order_id'],
                    'selected'      => in_array($result['order_id'], $data['selected'])?true:false,
                    'customer'      => $result['customer'],
                    'products'   => $products,
                    'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
                    'tax'        => $this->currency->format($order_tax, $this->config->get('config_currency')),
                    'total'      => $this->currency->format($order_total_sum, $this->config->get('config_currency')),
                    'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                    'shipping_code' => $result['shipping_code'],
                    'view'          => $this->url->link('sale/order/info', $this->token_name . $this->token_value  . '&order_id=' . $result['order_id'] . $url, true),
                    'edit'          => $this->url->link('sale/order/edit', $this->token_name . $this->token_value . '&order_id=' . $result['order_id'] . $url, true)
                );
            }else{
                $order_total--;
            }




        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_all_status'] = $this->language->get('text_all_status');

        $data['column_date_start'] = $this->language->get('column_date_start');
        $data['column_date_end'] = $this->language->get('column_date_end');
        $data['column_orders'] = $this->language->get('column_orders');
        $data['column_products'] = $this->language->get('column_products');
        $data['column_tax'] = $this->language->get('column_tax');
        $data['column_total'] = $this->language->get('column_total');

        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_group'] = $this->language->get('entry_group');
        $data['entry_status'] = $this->language->get('entry_status');





        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_invoice_print'] = $this->language->get('button_invoice_print');


        $data['column_order_product'] = $this->language->get('column_order_product');



        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->token_value;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['groups'] = array();

        $data['groups'][] = array(
            'text'  => $this->language->get('text_year'),
            'value' => 'year',
        );

        $data['groups'][] = array(
            'text'  => $this->language->get('text_month'),
            'value' => 'month',
        );

        $data['groups'][] = array(
            'text'  => $this->language->get('text_week'),
            'value' => 'week',
        );

        $data['groups'][] = array(
            'text'  => $this->language->get('text_day'),
            'value' => 'day',
        );

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        $url = '';

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

//		if (isset($this->request->get['filter_group'])) {
//			$url .= '&filter_group=' . $this->request->get['filter_group'];
//		}

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('catalog/bundle_expert_report', $this->token_name . $this->token_value . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($order_total - $limit)) ? $order_total : ((($page - 1) * $limit) + $limit), $order_total, ceil($order_total / $limit));

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
//		$data['filter_group'] = $filter_group;
        $data['filter_order_status_id'] = $filter_order_status_id;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_report.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_report', $data));
        }

//		$this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_report', $data));
    }

    public function invoice() {
//        $this->load->language('sale/order');
        $this->load->language('catalog/bundle_expert_report');

        $data['title'] = $this->language->get('text_invoice');

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');

        $data['text_invoice'] = $this->language->get('text_invoice');
        $data['text_order_detail'] = $this->language->get('text_order_detail');
        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_invoice_no'] = $this->language->get('text_invoice_no');
        $data['text_invoice_date'] = $this->language->get('text_invoice_date');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_website'] = $this->language->get('text_website');
        $data['text_payment_address'] = $this->language->get('text_payment_address');
        $data['text_shipping_address'] = $this->language->get('text_shipping_address');
        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');
        $data['text_comment'] = $this->language->get('text_comment');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_action'] = $this->language->get('column_action');

        $data['column_order_product'] = $this->language->get('column_order_product');
        $data['text_total'] = $this->language->get('text_total');

        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_invoice_print'] = $this->language->get('button_invoice_print');

        $this->load->model('sale/order');

        $this->load->model('setting/setting');
        $this->load->model('catalog/bundle_expert_report');
        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('sale/order');
        $this->load->model('tool/upload');

        $data['orders'] = array();

        $orders = array();

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        $total_all = 0;

        foreach ($orders as $order_id) {
            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {
                $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

                if ($store_info) {
                    $store_address = $store_info['config_address'];
                    $store_email = $store_info['config_email'];
                    $store_telephone = $store_info['config_telephone'];
                    $store_fax = $store_info['config_fax'];
                } else {
                    $store_address = $this->config->get('config_address');
                    $store_email = $this->config->get('config_email');
                    $store_telephone = $this->config->get('config_telephone');
                    $store_fax = $this->config->get('config_fax');
                }

                if ($order_info['invoice_no']) {
                    $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $invoice_no = '';
                }

                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname'  => $order_info['payment_lastname'],
                    'company'   => $order_info['payment_company'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city'      => $order_info['payment_city'],
                    'postcode'  => $order_info['payment_postcode'],
                    'zone'      => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country'   => $order_info['payment_country']
                );

                $payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname'  => $order_info['shipping_lastname'],
                    'company'   => $order_info['shipping_company'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city'      => $order_info['shipping_city'],
                    'postcode'  => $order_info['shipping_postcode'],
                    'zone'      => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country'   => $order_info['shipping_country']
                );

                $shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $this->load->model('tool/upload');

                $product_data = array();

                $order_total_sum = 0;
                $order_tax = 0;

                $products = $this->get_bundle_products($order_id, $order_total_sum, $order_tax);

                $totals = $this->model_sale_order->getOrderTotals($order_info['order_id']);

                foreach ($totals as $total) {
                    if($total['code']=='bundle_expert_total'){
                        $order_total_sum += $total['value'];
                    }

                }

                $total_all += $order_total_sum;

//                $products = $this->model_sale_order->getOrderProducts($order_id);
//                $products = $this->get_bundle_products($order_id);

//                foreach ($products as $product) {
//                    $option_data = array();
//
//                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
//
//                    foreach ($options as $option) {
//                        if ($option['type'] != 'file') {
//                            $value = $option['value'];
//                        } else {
//                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
//
//                            if ($upload_info) {
//                                $value = $upload_info['name'];
//                            } else {
//                                $value = '';
//                            }
//                        }
//
//                        $option_data[] = array(
//                            'name'  => $option['name'],
//                            'value' => $value
//                        );
//                    }
//
//                    $product_data[] = array(
//                        'name'     => $product['name'],
//                        'model'    => $product['model'],
//                        'option'   => $option_data,
//                        'quantity' => $product['quantity'],
//                        'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
//                        'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
//                    );
//                }
//
//                $voucher_data = array();
//
//                $vouchers = $this->model_sale_order->getOrderVouchers($order_id);
//
//                foreach ($vouchers as $voucher) {
//                    $voucher_data[] = array(
//                        'description' => $voucher['description'],
//                        'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
//                    );
//                }

//                $total_data = array();
//
//                $totals = $this->model_sale_order->getOrderTotals($order_id);
//
//                foreach ($totals as $total) {
//                    $total_data[] = array(
//                        'title' => $total['title'],
//                        'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
//                    );
//                }

                $data['orders'][] = array(
                    'order_id'	       => $order_id,
                    'invoice_no'       => $invoice_no,
                    'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                    'date_modified'       => date($this->language->get('date_format_short'), strtotime($order_info['date_modified'])),
                    'store_name'       => $order_info['store_name'],
                    'store_url'        => rtrim($order_info['store_url'], '/'),
                    'store_address'    => nl2br($store_address),
                    'store_email'      => $store_email,
                    'store_telephone'  => $store_telephone,
                    'store_fax'        => $store_fax,
                    'email'            => $order_info['email'],
                    'telephone'        => $order_info['telephone'],
                    'shipping_address' => $shipping_address,
                    'shipping_method'  => $order_info['shipping_method'],
                    'payment_address'  => $payment_address,
                    'payment_method'   => $order_info['payment_method'],
                    'products'          => $products,
                    'order_status'  => $order_info['order_status'] ? $order_info['order_status'] : $this->language->get('text_missing'),
                    'tax'        => $this->currency->format($order_tax, $this->config->get('config_currency')),
                    'total'      => $this->currency->format($order_total_sum, $this->config->get('config_currency')),
//                    'voucher'          => $voucher_data,
//                    'total'            => $total_data,
                    'comment'          => nl2br($order_info['comment'])
                );
            }
        }

        $data['total_all'] =$this->currency->format($total_all, $this->config->get('config_currency'))  ;

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_report_invoice.tpl', $data));

        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_report_invoice', $data));
        }

    }
}