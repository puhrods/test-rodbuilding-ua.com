<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerModuleBundleExpert extends Controller {
    private $checkbox_enable=false;


	public function index($setting) {
	    $bundle_expert = $this->bundle_expert;
        if(isset($bundle_expert)) {
            if ($this->config->get('bundle_expert_status_for_customer')) {
                if ($setting) {
                    return $this->initKitsModule($setting);
                }
            }
        }
	}

    public function addPageProducts($products) {
        if($this->registry->has('bundle_expert_page_products')){
            $bundle_expert_page_products = $this->registry->get('bundle_expert_page_products');
        }else{
            $bundle_expert_page_products = array();
        }

        foreach ($products as $product){
            if(isset($product['product_id'])) {
                $bundle_expert_page_products[$product['product_id']] = $product;
            }
        }

        $this->bundle_expert_page_products = $bundle_expert_page_products;
    }

    public function addPageWidgets($widget) {
        if($this->registry->has('bundle_expert_page_widgets')){
            $bundle_expert_page_widgets = $this->registry->get('bundle_expert_page_widgets');
        }else{
            $bundle_expert_page_widgets = array();
        }

        $bundle_expert_page_widgets[] = $widget;

        $this->bundle_expert_page_widgets = $bundle_expert_page_widgets;
    }

    
    public function addCartFreeProducts() {
        if($this->registry->has('bundle_expert_cart_free_products')){
            $bundle_expert_cart_free_products = $this->registry->get('bundle_expert_cart_free_products');
        }else{
            $bundle_expert_cart_free_products = array();
        }

        $cart_free_products = $this->model_checkout_bundle_expert->getCartFreeProducts();

        foreach ($cart_free_products as $product){
            $bundle_expert_cart_free_products[] = $product['product_id'];
        }

        $this->bundle_expert_cart_free_products = $cart_free_products;
    }



    public function getBundleExpert($admin_mode=false) {

        if ($this->checkConfigMainteance()) {
            return;
        }

        if(!$this->bundle_expert->isEnabled()) return '';

        $this->load->language('module/bundle_expert');

        $data['add_bundle_expert_custom_js']= false;
























        $data['custom_style_file'] = '';
        $data['moment_library_file'] = '';

        if (!$admin_mode) {
            $custom_style_file = DIR_APPLICATION . 'view/theme/default/stylesheet/bundle_expert_custom.css';
            if(file_exists($custom_style_file)) {
                $data['custom_style_file'] = 'catalog/view/theme/default/stylesheet/bundle_expert_custom.css';
            }
            $custom_js_file = DIR_APPLICATION . 'view/javascript/bundle-expert/bundle-expert-custom.js';
            if(file_exists($custom_js_file)) {
                $data['add_bundle_expert_custom_js']= true;
            }else{
                $data['add_bundle_expert_custom_js']= false;
            }
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $data['moment_library_file'] = 'catalog/view/javascript/jquery/datetimepicker/moment.js';
            }else{
                $data['moment_library_file'] = 'catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js';
            }
        }

        $data['https_catalog'] = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;

        $this->load->model('catalog/product');
        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');
        $this->load->model('tool/image');

        $bundle_expert_settings = $this->config->get('bundle_expert_settings');

        $data['text_cart_merge_message'] = $this->language->get('text_cart_merge_message');
        $data['text_to_merge'] = $this->language->get('text_to_merge');
        $data['text_to_add'] = $this->language->get('text_to_add');
        $data['text_cancel'] = $this->language->get('text_cancel');
        $data['text_close'] = $this->language->get('text_close');
        $data['text_add_kit_to_cart'] = $this->language->get('text_add_kit_to_cart');
        $data['text_bundle_expert_name'] = $this->language->get('text_bundle_expert_name');
        $data['text_setup_kit'] = $this->language->get('text_setup_kit');
        $data['text_setup_kit_message'] = $this->language->get('text_setup_kit_message');
        $data['text_remove_kit_from_cart_question'] = $this->language->get('text_remove_kit_from_cart_question');
        $data['button_edit_kit'] = $this->language->get('button_edit_kit');
        $data['button_remove_kit'] = $this->language->get('button_remove_kit');
        $data['text_kit'] = $this->language->get('text_kit');

        $data['text_'] = 'text';

        
        if (isset($this->request->get['route']) && isset($this->request->get['product_id']) && $this->request->get['route'] == 'product/product') {

            $product_id = (int)$this->request->get['product_id'];
            $product = $this->bundle_expert->getProductInfo($product_id);;
            $this->addPageProducts(array($product));
        }

        
        

        
        $this->addCartFreeProducts();

        
        if (!$admin_mode) {
            $widgets = $this->initKits();
            $data['widgets_data'] = $widgets['widgets_data'];
            $data['widgets_html'] = $widgets['widgets_html'];
        } else {
            $data['widgets_data'] = array();
            $data['widgets_html'] = array();
        }

        
        
        $json_array = array();
        foreach ($data['widgets_data'] as $index=>$widgets_data){
            unset($widgets_data['kit_items_data']);
            unset($widgets_data['kit_info']);
            $json_array[] = $widgets_data;
        }
        $data['widgets_data_json'] = json_encode($json_array);


        if($this->registry->has('bundle_expert_page_products')){
            $bundle_expert_page_products = $this->registry->get('bundle_expert_page_products');
        }else{
            $bundle_expert_page_products = array();
        }

        
        if($this->bundle_expert->bundle_expert_settings['control_change_quantity_by_stock']){
            $data['control_change_quantity_by_stock'] = 'true';
        }else{
            $data['control_change_quantity_by_stock'] = 'false';
        }
        

        $data['category_products_data_json'] = json_encode(array());

        if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/category'){
            if(!empty($bundle_expert_page_products)){
                $category_products = array();
                foreach ($bundle_expert_page_products as $bundle_expert_page_product){
                    $category_products[] = $bundle_expert_page_product['product_id'];
                }
                $data['category_products_data_json'] = json_encode($category_products);
            }
        }


        
        $data['reload_item_products'] = $this->config->get('config_stock_checkout') ? 0 : 1;

        
        $data['is_some_checkout_page'] =(isset($this->request->get['route'])
            && in_array($this->request->get['route'], $bundle_expert_settings['checkout_pages']))? 'true' : 'false';








        
        if(isset($this->request->get['route']) && ($this->request->get['route']=='checkout/checkout' || $this->request->get['route']=='account/order/info'))
            $data['disable_cart_kit_edit_button'] = 'true' ;
        else
            $data['disable_cart_kit_edit_button'] = 'false';



        
        $data['bundle_expert_settings'] = $this->bundle_expert->bundle_expert_settings;

        $selectors = isset( $data['bundle_expert_settings']['selectors']) ?  $data['bundle_expert_settings']['selectors'] : array();




        $data['selectors_data_json'] = json_encode($selectors);

        if($this->bundle_expert->bundle_expert_settings['product_as_kit_product_page_animate_price']){
            $data['animate_price'] = 'true';
        }else{
            $data['animate_price'] = 'false';
        }

        $currency_data = array(
            'symbol_left'   => strip_tags($this->currency->getSymbolLeft($this->session->data['currency'])),
            'symbol_right'   => strip_tags($this->currency->getSymbolRight($this->session->data['currency'])),
            'decimal_place'   => $this->currency->getDecimalPlace($this->session->data['currency']),
            'decimal_point'   => strip_tags($this->language->get('decimal_point')),
            'thousand_point'   => strip_tags($this->language->get('thousand_point')),
        );

        
        

        
        $dynamic_update_product_as_kit_price_in_db = $this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db'];
        if($dynamic_update_product_as_kit_price_in_db){
            
        }
        $dynamic_update_product_as_kit_price_in_db_le = $this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le'];
        if($dynamic_update_product_as_kit_price_in_db_le){
            
        }

        $data['currency_data_json'] = (json_encode($currency_data));

        $data['admin_mode'] = $admin_mode;
        $data['script_version'] = $this->bundle_expert->script_version;

        if($admin_mode) {
            $data['bundle_expert_settings']['smart_js'] = false;
        }

        $add_js_css = false;
        $css_files = array();
        $js_files = array();

        if($data['bundle_expert_settings']['optimized_owl_carousel']){
            $add_owl_carowsel = $this->checkOwlCarouselForWidgets($data['widgets_data']);
        }else{
            $add_owl_carowsel = true;
        }



        
        if($data['bundle_expert_settings']['smart_js']){
            $cart_has_bundles = $this->bundle_expert_cart->checkCartHasBundles();
            if(!empty($data['widgets_html']) || $cart_has_bundles){
                $add_js_css = true;
            }

            if($this->getCategoryWidget()){
                $add_js_css = true;
                $add_owl_carowsel = true;
            }


            if($add_js_css){
                $css_files = $this->getCssFiles($add_owl_carowsel);

                $bundle_expert_settings = $this->bundle_expert->bundle_expert_settings;

                $minimize_css = $minimize_js = $bundle_expert_settings['css_js_minify'];

                if($minimize_css){
                    $this->bundle_expert->min_css($css_files);
                    $css_files = array();
                    $css_files[] = 'catalog/'.$this->bundle_expert->css_min_file;
                }else{
                    foreach ($css_files as &$css_file){
                        $css_file = 'catalog/' .$css_file;
                    }

                }

                $js_files = $this->getJsFiles($add_owl_carowsel);

                if ($minimize_js) {
                    $this->bundle_expert->min_js($js_files);
                    $js_files = array();
                    $js_files[] = 'catalog/' . $this->bundle_expert->js_min_file;
                }else{
                    foreach ($js_files as &$js_file){
                        $js_file = 'catalog/' .$js_file;
                    }
                }
            }

        }else{

        }



        $data['add_js_css'] = $add_js_css;
        $data['css_files'] = $css_files;
        $data['js_files'] = $js_files;


        $bundle_expert_settings = $this->bundle_expert->bundle_expert_settings;
        $data['js_defer'] = $bundle_expert_settings['js_defer'];











        $add_bundle_expert_to_page = true;

        if($data['bundle_expert_settings']['smart_js']){
            if($add_js_css){
                $add_bundle_expert_to_page = true;
            }else{
                $add_bundle_expert_to_page = false;
            }
        }


        if($add_bundle_expert_to_page){
            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                return $this->load->view('default/template/module/bundle_expert/bundle_expert.tpl', $data);
            }else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    return $this->load->view('module/bundle_expert/bundle_expert.tpl', $data);
                }else{
                    return $this->load->view('module/bundle_expert/bundle_expert', $data);
                }

            }
        }else{
            return '';
        }


    }

    public function getBundleExpertHeaderStyle($admin_mode = false) {

        if ($this->checkConfigMainteance()) {
            return;
        }

        if(!$this->bundle_expert->isEnabled()) return '';

        $bundle_expert_settings = $this->bundle_expert->bundle_expert_settings;

        $data['cart_color_handler_1'] = $bundle_expert_settings['cart_color_handler_1'];
        $data['cart_color_handler_2'] = $bundle_expert_settings['cart_color_handler_2'];

        $data['add_bundle_expert_custom_js']= false;

        $data['custom_style_file'] = '';
        $data['moment_library_file'] = '';

        if($this->registry->has('bundle_expert_page_widgets')){
            $bundle_expert_page_widgets = $this->registry->get('bundle_expert_page_widgets');
        }else{
            $bundle_expert_page_widgets = array();
        }

        $minimize_css = $minimize_js = $bundle_expert_settings['css_js_minify'];
        if(!$minimize_css){
            $this->bundle_expert->deleteMinifyJsCss();
        }

        
        if(!$bundle_expert_settings['smart_js']){
            if (!$admin_mode) {
                $css_files = $this->getCssFiles();


                if(!$minimize_css){
                    foreach ($css_files as $css_file){
                        if(!empty($css_file)){
                            $this->document->addStyle('catalog/'.$css_file);
                        }
                    }
                }else{
                    $this->bundle_expert->min_css($css_files);
                    $this->document->addStyle('catalog/'.$this->bundle_expert->css_min_file);
                }

            }
        }

        
        if(!$bundle_expert_settings['smart_js']) {
            if (!$admin_mode) {

                $js_files = array();
                $js_files = $this->getJsFiles();

                if (!$minimize_js) {
                    foreach ($js_files as $js_file) {
                        if (!empty($js_file)) {
                            $this->document->addScript('catalog/' . $js_file);
                        }
                    }
                } else {
                    $this->bundle_expert->min_js($js_files);
                    $this->document->addScript('catalog/' . $this->bundle_expert->js_min_file);
                }
            }
        }

        $data['hide_elements_selector'] = '';
        $hide_elements = array();
        foreach ($bundle_expert_page_widgets as $widget){
            if($widget['html_element_action']!=0) {
                $hide_elements[] = $widget['html_element_action_selector'];
            }
        }
        $data['hide_elements_selector'] = implode(', ' , $hide_elements);

        $data['https_catalog'] = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;

        $data['admin_mode'] = $admin_mode;
        $data['script_version'] = $this->bundle_expert->script_version;

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            return $this->load->view('default/template/module/bundle_expert/bundle_expert_header_style.tpl', $data);
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                return $this->load->view('module/bundle_expert/bundle_expert_header_style.tpl', $data);
            }else{
                return $this->load->view('module/bundle_expert/bundle_expert_header_style', $data);
            }

        }

    }

    private function getCssFiles($add_owl_carowsel = true){
        $css_files = array();

        $bundle_expert_settings = $this->bundle_expert->bundle_expert_settings;

        $minimize_css = $minimize_js = $bundle_expert_settings['css_js_minify'];

        $data = array();
        $data['custom_style_file'] = '';
        $data['moment_library_file'] = '';

        $custom_style_file = DIR_APPLICATION . 'view/theme/default/stylesheet/bundle_expert_custom.css';
        if(file_exists($custom_style_file)) {
            if(!$minimize_css){

                $data['custom_style_file'] = 'view/theme/default/stylesheet/bundle_expert_custom.css';
            }else{
                $data['custom_style_file'] = 'view/theme/default/stylesheet/bundle_expert_custom.css';
            }

        }

        
        $css_files = array();
        if(!$minimize_css){

            $css_files[] = 'view/theme/default/stylesheet/bundle_expert.css';
        }else{
            $css_files[] = 'view/theme/default/stylesheet/bundle_expert.css';
        }
        if(!empty($data['custom_style_file'])){
            $css_files[] = $data['custom_style_file'];

        }
        if($bundle_expert_settings['css_js_files']['magnific_popup']) {
            $css_files[] = 'view/javascript/jquery/magnific/magnific-popup.css';
        }
        if($bundle_expert_settings['css_js_files']['datetimepicker']) {
            $css_files[] = 'view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css';
        }
        if($bundle_expert_settings['css_js_files']['bootstrap_3']) {
            $css_files[] = 'view/javascript/bundle-expert/bootstrap/css/bootstrap.min.css';
        }
        if($bundle_expert_settings['css_js_files']['font_awesome']) {
            $css_files[] = 'view/javascript/bundle-expert/font-awesome/css/font-awesome.min.css';
        }
        if($add_owl_carowsel){
            $css_files[] = 'view/javascript/bundle-expert/owl.carousel.min.css';
            $css_files[] = 'view/javascript/bundle-expert/owl.theme.default.min.css';
        }



        return $css_files;
    }
    private function getJsFiles($add_owl_carowsel = true){
        $js_files = array();

        $bundle_expert_settings = $this->bundle_expert->bundle_expert_settings;

        $data = array();

        $minimize_css = $minimize_js = $bundle_expert_settings['css_js_minify'];

        $custom_js_file = DIR_APPLICATION . 'view/javascript/bundle-expert/bundle-expert-custom.js';
        if (file_exists($custom_js_file)) {
            $data['add_bundle_expert_custom_js'] = true;
        } else {
            $data['add_bundle_expert_custom_js'] = false;
        }
        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $data['moment_library_file'] = 'view/javascript/jquery/datetimepicker/moment.js';
        } else {
            $data['moment_library_file'] = 'view/javascript/jquery/datetimepicker/moment/moment.min.js';
        }

        if ($bundle_expert_settings['css_js_files']['magnific_popup']) {
            $js_files[] = 'view/javascript/jquery/magnific/jquery.magnific-popup.min.js';
        }
        if ($bundle_expert_settings['css_js_files']['moment']) {
            $js_files[] = $data['moment_library_file'];
        }
        if ($bundle_expert_settings['css_js_files']['datetimepicker']) {
            $js_files[] = 'view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js';
        }
        if ($bundle_expert_settings['css_js_files']['bootstrap_3_js']) {
            $js_files[] = 'view/javascript/bundle-expert/bootstrap/js/bootstrap.min.js';
        }
        if($add_owl_carowsel){
            $js_files[] = 'view/javascript/bundle-expert/owl.carousel.js';
        }
        if ($data['add_bundle_expert_custom_js']) {
            if (!$minimize_js) {

                $js_files[] = 'view/javascript/bundle-expert/bundle-expert-custom.js';
            } else {
                $js_files[] = 'view/javascript/bundle-expert/bundle-expert-custom.js';
            }
        }

        if ($bundle_expert_settings['smart_js_2']) {
            $js_files[] = 'view/javascript/bundle-expert/bundle-expert-mini.js';
        }else {
            if (!$minimize_js) {

                $js_files[] = 'view/javascript/bundle-expert/bundle-expert.js';
            } else {
                $js_files[] = 'view/javascript/bundle-expert/bundle-expert.js';
            }
        }

        if ($bundle_expert_settings['custom_header_cart_js_status']) {
            $custom_header_cart_js_file = DIR_APPLICATION . 'view/javascript/bundle-expert/bundle-expert-custom-header-cart.js';
            if (file_exists($custom_header_cart_js_file)) {
                $js_files[] = 'view/javascript/bundle-expert/bundle-expert-custom-header-cart.js';
            }
        }

        return $js_files;
    }

    public function getCategoryWidget() {
        $widget_info = array();

        if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/category'){
            if (isset($this->request->get['path'])) {

                $parts = explode('_', (string)$this->request->get['path']);

                $category_id = (int)array_pop($parts);


            } else {
                $category_id = 0;
            }

            if($category_id){



                $widget_info = $this->bundle_expert->getCategoryWidget($category_id);

                if($widget_info){
                    $widget_info['category_id'] = $category_id;

                }

            }
        }

        return $widget_info;
    }

    
    
    public function getWidgetsDirectOutputMode($source_data) {

        if($this->bundle_expert->bundle_expert_settings['product_page_widgets_direct_output_mode']){
            $widgets = $this->initKits(true);


            if (!empty($widgets['widgets_html'])) {
                $data['widgets_html'] = $widgets['widgets_html'];
                $data['widgets_data'] = $widgets['widgets_data'];
                if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $html = $this->load->view('default/template/module/bundle_expert/product_page_direct_output.tpl', $data);
                } else {
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $html = $this->load->view('module/bundle_expert/product_page_direct_output.tpl', $data);
                    } else {
                        $html = $this->load->view('module/bundle_expert/product_page_direct_output', $data);
                    }
                }

                $source_data['bundle_widgets'] = $html;

            } else {
                $source_data['bundle_widgets'] = '';
            }

        }

        return $source_data;

    }
    

    public function getCategoryKits($source_data) {



        $html = '';

        $widget_info = $this->getCategoryWidget();

        if($widget_info){

            $category_id = $widget_info['category_id'];

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            if (isset($this->request->get['limit'])) {
                $limit = (int)$this->request->get['limit'];
            } else {
                $limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
                $limit = $widget_info['kits_per_category_page'];
            }

            $filter_data = array(
                'category_id' => $category_id,
                'widget_id' => $widget_info['widget_id'],
                'start'              => ($page - 1) * $limit,
                'limit'              => $limit
            );

            $kit_total = $this->bundle_expert->getCategoryWidgetKitsTotal($filter_data);
            $kits = $this->bundle_expert->getCategoryWidgetKits($filter_data);
            $widgets_html = array();

            foreach ($kits as $kit) {
                $kit_info = $this->bundle_expert->getKit($kit['kit_id']);

                if($kit_info){
                    $main_product_id = $kit['main_product_id'];

                    $get_kit_products_settings = array(
                        'main_product_id' => $main_product_id,
                        'filter_kit_items' => true,
                        'only_first' => false,
                        'admin_mode' => false,
                    );

                    $widget_info['main_product_id'] = $main_product_id;
                    $widget_info['kit_info'] = $kit_info;
                    $widget_info['kit_info']['kit_items'] = $this->bundle_expert->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                    $widget_info['unique_id'] = $this->bundle_expert->getWidgetCacheUniqueId($widget_info, $main_product_id);
                    $widget_info['unique_id'] = uniqid();
                    $widget_info['main_product_in_cart'] = false;
                    $widget_info['cart_merge_enable'] = false;

                    if ($this->bundle_expert->bundle_expert_settings['cache_widgets']) {
                        $widgets_html[] = $this->bundle_expert->createWidgetCache($widget_info, $main_product_id);
                    } else {
                        $widgets_html[] = $this->createWidget($widget_info, $main_product_id);
                    }

                }

            }

            $data['widgets_html'] = $widgets_html;
            $data['widget_info'] = $widget_info;

            $url="";

            $pagination = new Pagination();
            $pagination->total = $kit_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

            $data['pagination'] = $pagination->render();

            $data['results'] = sprintf($this->language->get('text_pagination'), ($kit_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($kit_total - $limit)) ? $kit_total : ((($page - 1) * $limit) + $limit), $kit_total, ceil($kit_total / $limit));

            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $html = $this->load->view('default/template/module/bundle_expert/category_kits.tpl', $data);
            } else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $html = $this->load->view('module/bundle_expert/category_kits.tpl', $data);
                } else {
                    $html = $this->load->view('module/bundle_expert/category_kits', $data);
                }
            }
        }


        if(isset($widgets_html) && !empty($widgets_html)){
            $source_data['products'] = array();
            $source_data['kits_list_html'] = $html;
        }else{
            $source_data['kits_list_html'] = '';
        }



        return $source_data;

    }

    
    public function initKits($direct_output_mode = false) {
        if($this->registry->has('bundle_expert_page_products')){
            $bundle_expert_page_products = $this->registry->get('bundle_expert_page_products');
        }else{
            $bundle_expert_page_products = array();
        }

        if($this->registry->has('bundle_expert_cart_free_products')){
            $bundle_expert_cart_free_products = $this->registry->get('bundle_expert_cart_free_products');
        }else{
            $bundle_expert_cart_free_products = array();
        }

        if($this->registry->has('bundle_expert_page_modules')){
            $bundle_expert_page_modules = $this->registry->get('bundle_expert_page_modules');
        }else{
            $bundle_expert_page_modules = array();
        }

        if($this->registry->has('bundle_expert_page_modules_in_category')){
            $bundle_expert_page_modules_in_category = $this->registry->get('bundle_expert_page_modules_in_category');
        }else{
            $bundle_expert_page_modules_in_category = array();
        }

        $data['widgets_data'] = array();
        $data['widgets_html'] = array();

        $widgets = array();

        
        if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/product'){
            if(!$this->bundle_expert->bundle_expert_settings['product_page_widgets_direct_output_mode'] || $direct_output_mode) {
                $widgets_product_page = $this->bundle_expert->getWidgetsByDisplayMode('product_page', array('products' => $bundle_expert_page_products));
                $widgets = array_merge($widgets, $widgets_product_page);
            }
        }

        
        
        if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/product'){
            if(!$this->bundle_expert->bundle_expert_settings['product_page_widgets_direct_output_mode'] || $direct_output_mode) {
                $widgets_product_page = $this->bundle_expert->getWidgetsByDisplayMode('product_page', array('products' => array(), 'product_auto_list' => true));
                $widgets = array_merge($widgets, $widgets_product_page);
            }
        }

        
        if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/category'){
            $widgets_category_page = $this->model_module_bundle_expert->getWidgetsByDisplayMode('category_page', array('products'=>$bundle_expert_page_products));
            $widgets = array_merge($widgets, $widgets_category_page);
        }

        
        $widgets_custom_page = $this->model_module_bundle_expert->getWidgetsByDisplayMode('custom_page',  array('products'=>$bundle_expert_page_products, 'cart_free_products'=>$bundle_expert_cart_free_products));
        $widgets = array_merge($widgets, $widgets_custom_page);

        
        foreach ($bundle_expert_page_modules as $bundle_expert_page_module) {
            $widgets_module = $this->model_module_bundle_expert->getWidgetsByDisplayMode('module', array('products' => array(), 'cart_free_products' => $bundle_expert_cart_free_products, 'bundle_expert_page_module' => $bundle_expert_page_module));
            foreach ($widgets_module as &$widget){
                $widget['config_module']['module_unique_id'] = $bundle_expert_page_module['module_unique_id'];
                $widgets[] = $widget;
            }
        }

        
        foreach ($bundle_expert_page_modules_in_category as $bundle_expert_page_module) {
            $widgets_module = $this->model_module_bundle_expert->getWidgetsByDisplayMode('module_in_category', array('products' => array(), 'cart_free_products' => $bundle_expert_cart_free_products, 'bundle_expert_page_module' => $bundle_expert_page_module, 'widget_id'=>$bundle_expert_page_module['widget_id']));
            foreach ($widgets_module as &$widget){
                $widget['config_module']['module_unique_id'] = $bundle_expert_page_module['module_unique_id'];
                $widgets[] = $widget;
            }
        }

        while (!empty($widgets)) {
            $widget = $widgets[0];

            if ($widget['main_mode']=='product_from_kit' && $widget['product_from_kit_mode_items']=='link_to_product_page') {

                if($this->bundle_expert->bundle_expert_settings['cache_widgets']){
                    $data['widgets_html'][] = $this->bundle_expert->createWidgetCacheLinkToMainProducts($widget, $widget['main_product_id'], $widgets);
                    $widget['unique_id'] = $this->bundle_expert->getWidgetCacheUniqueIdLinkToMainProducts($widget, $widget['main_product_id']);
                }else{
                    $data['widgets_html'][] = $this->createWidgetLinkToMainProducts($widgets, $widget['main_product_id']);
                }

                
                foreach ($widgets as $index=>$widget_item){
                    if ($widget_item['widget_id'] == $widget['widget_id'] && $widget_item['main_mode'] == 'product_from_kit' && $widget_item['product_from_kit_mode_items'] == 'link_to_product_page') {
                        unset($widgets[$index]);
                    }
                }

            }else{
                if ($this->bundle_expert->bundle_expert_settings['cache_widgets']) {
                    $data['widgets_html'][] = $this->bundle_expert->createWidgetCache($widget, $widget['main_product_id']);
                    $widget['unique_id'] = $this->bundle_expert->getWidgetCacheUniqueId($widget, $widget['main_product_id']);
                } else {
                    $data['widgets_html'][] = $this->createWidget($widget, $widget['main_product_id']);
                }
                unset($widgets[0]);
            }

            $data['widgets_data'][] = array(
                'widget_id' => $widget['widget_id'],
                'display_mode' => $widget['display_mode'],
                'main_product_id' => $widget['main_product_id'],
                'kit_id' => $widget['kit_info']['kit_id'],
                'kit_info' => $widget['kit_info'],
                'kit_items_data' => isset($widget['kit_items_data'])? $widget['kit_items_data']: array(),
                'config_product_page' => $widget['config_product_page'],
                'config_custom_page' => $widget['config_custom_page'],
                'config_category_page' => $widget['config_category_page'],
                'config_module' => $widget['config_module'],
                'unique_id' => $widget['unique_id'],
                'slider_mode' => $widget['slider_mode'],
                'slider_autoplay_status' => $widget['slider_autoplay_status'],
                'slider_autoplay_time' => $widget['slider_autoplay_time'],
                'main_mode' => $widget['kit_info']['main_mode'],
                'kit_as_product' => $widget['kit_info']['kit_as_product'],
                'kit_as_product_light_mode' => $widget['kit_info']['kit_as_product_light_mode'],
                'template' => $widget['template'],
                'html_element_action' => $widget['html_element_action'],
                'html_element_action_selector' => $widget['html_element_action_selector'],
            );

            $widgets = array_values($widgets);

            $this->addPageWidgets($widget);

        }

























































        return $data;
    }

























































































































































































































    public function initKitsModule($setting) {
        if($this->registry->has('bundle_expert_page_modules')){
            $bundle_expert_page_modules = $this->registry->get('bundle_expert_page_modules');
        }else{
            $bundle_expert_page_modules = array();
        }

        if($this->registry->has('bundle_expert_page_modules_in_category')){
            $bundle_expert_page_modules_in_category = $this->registry->get('bundle_expert_page_modules_in_category');
        }else{
            $bundle_expert_page_modules_in_category = array();
        }

        $widget_id = $setting['widget_id'];

        $module_unique_id = uniqid();

        if(!isset($setting['display_mode'])){
            $setting['display_mode']="module";
        }
        if($setting['display_mode']=="module"){
            $bundle_expert_page_modules[] = array('widget_id'=>$widget_id, 'module_unique_id' =>$module_unique_id);

            $this->bundle_expert_page_modules = $bundle_expert_page_modules;
        }else{
            $bundle_expert_page_modules_in_category[] = array('widget_id'=>$widget_id, 'module_unique_id' =>$module_unique_id);

            $this->bundle_expert_page_modules_in_category = $bundle_expert_page_modules_in_category;
        }


        $data['widget_id'] = $widget_id;
        $data['module_unique_id'] = $module_unique_id;

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            return $this->load->view('default/template/module/bundle_expert/bundle_expert_module.tpl', $data);
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                return $this->load->view('module/bundle_expert/bundle_expert_module.tpl', $data);
            }else{
                return $this->load->view('module/bundle_expert/bundle_expert_module', $data);
            }

        }
    }


    private function createFreeProductAddButton($widget_template, $item_position, $title, $display_options_input=0, $last_item=-1, $free_item=-1, $widget_product_click_mode=-1){
        $kit_item_data = array();
        $kit_item_data['item_product'] = '';
        $kit_item_data['item_position'] = $item_position;
        $kit_item_data['item_product_position'] = 0;
        $kit_item_data['selectable'] = 0;
        $kit_item_data['last_item'] = $last_item;

        $kit_item_data['text_product_for_select'] = $this->language->get('text_product_for_select');
        $kit_item_data['text_add_product'] = $this->language->get('text_add_product');
        $kit_item_data['text_remove_from_kit'] = $this->language->get('text_remove_from_kit');
        $kit_item_data['text_item_not_selected'] = $this->language->get('text_item_not_selected');


        if (1){
            $kit_item_data['item_default_empty'] = false;
            $kit_item_data['empty_mode_item_is_empty'] = false;
            $kit_item_data['item_empty_title'] = '';

            $kit_item_data['text_product_for_select'] = $this->language->get('text_add_product');
            $data['kit_has_free_products'] = true;
        }


        if (1){
            $kit_item_data['item_position_free'] = 0;
        }else{
            $kit_item_data['item_position_free'] = -1;
        }

        $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-add-free-product-2.png', 80, 80);

        if(!empty($free_item['empty_group_image'])){
            $kit_item_data['empty_image_name'] = $this->model_tool_image->resize($free_item['empty_group_image'], 80, 80);
        }else{
            $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-add-free-product-2.png', 80, 80);
        }

        $kit_item_data['free_product_in_kit'] = 0;

        $kit_item_data['free_item_title'] = $title;

        $kit_item_data['display_options_input'] = $display_options_input;

        $kit_item_data['product_click_mode'] = $widget_product_click_mode;

        $html ='';


        if(strripos($widget_template, "custom_template_") !== false){
            $file_name = 'custom_widget/' . $widget_template . '_kit_item_add_free';
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.tpl';
            } else {
                $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.twig';
            }
            if(!(file_exists($check_file_name))){
                $file_name = 'kit_item_template_1_add_free';
            }
        }else {
            if ($widget_template == 'widget_template_1') {
                $file_name = 'kit_item_template_1_add_free';
            } else {
                $file_name = 'kit_item_template_2_add_free';
            }
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            $html = $this->load->view('default/template/module/bundle_expert/'.$file_name.'.tpl', $kit_item_data);
        } else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $html = $this->load->view('module/bundle_expert/'.$file_name.'.tpl', $kit_item_data);
            } else {
                $html = $this->load->view('module/bundle_expert/'.$file_name.'', $kit_item_data);
            }
        }

























        return $html;
    }
    public function _createWidgetLinkToMainProducts($data){
        $widgets = $data['widgets'];
        $current_product_id = $data['current_product_id'];
        $html = $this->createWidgetLinkToMainProducts($widgets, $current_product_id);
        return $html;
    }

    private function createWidgetLinkToMainProducts($widgets, $current_product_id){
	    $html = '';

	    if(!empty($widgets)){
	        $widget = $widgets[0];

	        $products=array();


	        foreach ($widgets as $widget_item){
	            $kit_info = $widget_item['kit_info'];

	            $linked_products = $this->bundle_expert->getKitLinkProducts2($kit_info['kit_id']);

                $products = array_merge($products,$linked_products);

            }

	        if(!empty($products)){
	            $kit_item_clone = $widget['kit_info']['kit_items'][0];
	            $product_clone = $kit_item_clone['products'][0];

                $product_clone['is_free_product'] = 0;
                $product_clone['free_product_default_in_kit'] = 1;
                $product_clone['free_product_in_kit'] = 1;
                $product_clone['item_empty_mode']['enable'] = 0;

                $kit_item_clone['products'] = array();


                foreach ($products as $product_id){
                    $product_info = $this->bundle_expert->getProductInfo($product_id);

                    if($product_info){
                        $product_clone['product_info'] = $product_info;
                        $product_clone['product_info']['options'] = array();
                        $product_clone['product_id'] = $product_info['product_id'];

                        $kit_item_clone['products'][] = $product_clone;
                    }

                }

                $widget['kit_info']['kit_items'] = array();
                $widget['kit_info']['kit_items'][] = $kit_item_clone;
                $widget['kit_info']['kit_mode'] = 'list_product';
                $widget['kit_info']['kit_as_product'] = 0;
                $widget['kit_info']['main_mode'] = 'series';
                $widget['kit_info']['kit_as_product_light_mode'] = 0;
                $widget['kit_info']['series_mode'] = 'default';
                $widget['kit_info']['title'] = '';
                $widget['kit_info']['kit_as_product'] = 0;

                $widget_description = $this->bundle_expert->getWidgetDescriptions($widget['widget_id']);

                if($widget_description){
                    $widget['kit_info']['title'] = $widget_description['title'];
                    $widget['kit_info']['description'] = $widget_description['description'];
                }


            }
        }

        $html = $this->createWidget($widget, $current_product_id);
	    return $html;

    }

    public function _createWidget($data){
        $widget = $data['widget'];
        $current_product_id = $data['current_product_id'];
        $html = $this->createWidget($widget, $current_product_id);
        return $html;
    }

    private function createWidget(&$widget, $current_product_id){
	    $data = array();

        $data['text_tax'] = $this->language->get('text_tax');

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_continue'] = $this->language->get('button_continue');

        $data['text_products_total'] = $this->language->get('text_products_total');
        $data['text_bundle_total'] = $this->language->get('text_bundle_total');
        $data['text_add_kit_to_cart'] = $this->language->get('text_add_kit_to_cart');
        $data['text_more'] = $this->language->get('text_more');
        $data['text_you_can_add_to_kit_next_products'] = $this->language->get('text_you_can_add_to_kit_next_products');
        $data['text_free_products_list_empty'] = $this->language->get('text_free_products_list_empty');
        $data['text_price'] = $this->language->get('text_price');
        $data['text_quantity'] = $this->language->get('text_quantity');
        $data['text_weight_total'] = $this->language->get('text_weight_total');
        $data['text_clean'] = $this->language->get('text_clean');
        $data['text_tax'] = $this->language->get('text_tax');
        $data['text_kit_settings_link'] = $this->language->get('text_kit_settings_link');
        $data['text_widget_settings_link'] = $this->language->get('text_widget_settings_link');
        $data['text_product_select_header'] = $this->language->get('text_product_select_header');
        $data['text_bundle_profit_price'] = $this->language->get('text_bundle_profit_price');
        $data['text_add_free_group_to_kit'] = $this->language->get('text_add_free_group_to_kit');
        $data['text_remove_free_group_to_kit'] = $this->language->get('text_remove_free_group_to_kit');



        $data['text_select'] = $this->language->get('text_select');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['button_upload'] = $this->language->get('button_upload');



        $data['text_'] = 'text';


        $data['widget'] = $widget;



        if($widget['set_image_size_mode']['mode']){
            $image_sizes = array('width'=>$widget['set_image_size_mode']['width'], 'height'=>$widget['set_image_size_mode']['height']);
        }else{
            $image_sizes = array('width'=>'80', 'height'=>'80');
        }

        $data['image_width'] = $image_sizes['width'];

        $only_first = !$widget['checkbox_mode'];

        $kit_items_data = $this->bundle_expert->getDataKit($widget['kit_info']['kit_id'], $current_product_id, $widget['kit_info']['kit_items'],true, $only_first, false);

        $kit_items =  $kit_items_data['kit_items'];

        $widget['kit_items_data'] = $kit_items_data;

        $data['kit_has_free_products'] = false;

        if(isset($widget['table_mode_config']) && $widget['table_mode_config']['free_product_table_mode']==1){
            $data['free_products_table_mode'] = true;
        }else{
            $data['free_products_table_mode'] = false;
        }


        foreach ($kit_items as $index1=>$kit_item){
            $item_position_free = 0;
            $kit_items[$index1]['has_free_product_in_kit'] = false;

            $kit_items[$index1]['item_title'] = '';

            foreach ($kit_item['products'] as $index2=>$kit_item_product) {
                
                if ($kit_item_product['is_free_product'] && !$data['free_products_table_mode'] && !$kit_item_product['free_product_in_kit'] ){
                    $data['kit_has_free_products'] = true;
                    continue;
                }

                $kit_item_data = array();
                $kit_item_data['item_product'] = $kit_item_product;
                $kit_item_data['item_position'] = $index1;
                $kit_item_data['item_product_position'] = 0;
                $kit_item_data['selectable'] = $kit_item['selectable'];
                if($kit_item_product['item_mode']=='select_product'){
                    $kit_item_data['selectable'] = 1;
                }
                $kit_item_data['last_item'] = ($index1 == (count($kit_items) - 1)) ? true : false;

                $kit_item_data['text_product_for_select'] = $this->language->get('text_product_for_select');
                $kit_item_data['text_select'] = $this->language->get('text_select');
                $kit_item_data['text_add_product'] = $this->language->get('text_add_product');
                $kit_item_data['text_remove_from_kit'] = $this->language->get('text_remove_from_kit');
                $kit_item_data['text_item_not_selected'] = $this->language->get('text_item_not_selected');
                $kit_item_data['text_loading'] = $this->language->get('text_loading');
                $kit_item_data['button_upload'] = $this->language->get('button_upload');
                $kit_item_data['button_cart'] = $this->language->get('button_cart');
                $kit_item_data['button_wishlist'] = $this->language->get('button_wishlist');
                $kit_item_data['button_compare'] = $this->language->get('button_compare');
                $kit_item_data['text_tax'] = $this->language->get('text_tax');

                
                $kit_item_data['text_manufacturer'] = $this->language->get('text_manufacturer');
                $kit_item_data['text_model'] = $this->language->get('text_model');
                $kit_item_data['text_sku'] = $this->language->get('text_sku');
                $kit_item_data['text_reward'] = $this->language->get('text_reward');
                $kit_item_data['text_points'] = $this->language->get('text_points');
                $kit_item_data['text_stock'] = $this->language->get('text_stock');
                $kit_item_data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $kit_item_product['product_info']['manufacturer_id']);
                


                if ($kit_item_product['item_empty_mode']['enable'] && $kit_item_product['item_empty_mode']['default_empty']) {
                    $kit_item_data['show_empty'] = true;
                    $kit_item_data['empty_mode_item_is_empty'] = true;
                } else {
                    $kit_item_data['show_empty'] = false;
                    $kit_item_data['empty_mode_item_is_empty'] = false;
                    $kit_item_data['item_empty_title'] = '';
                }

                $kit_items[$index1]['item_title'] = '';

                if ($kit_item_product['is_free_product']){
                    $kit_item_data['item_default_empty'] = false;
                    $kit_item_data['empty_mode_item_is_empty'] = false;
                    $kit_item_data['item_empty_title'] = '';

                    $kit_item_data['text_product_for_select'] = $this->language->get('text_add_product');
                    $data['kit_has_free_products'] = true;

                    $kit_items[$index1]['item_title'] = $kit_item_product['item_empty_mode']['title'];
                }

                if ($widget['checkbox_mode'] && $kit_item_product['item_mode']=='select_product') {
                    $kit_items[$index1]['item_title'] = $kit_item_product['item_empty_mode']['title'];
                }

                if ($kit_item_product['is_free_product']){
                    $kit_item_data['item_position_free'] = $item_position_free;
                }else{
                    $kit_item_data['item_position_free'] = -1;
                }

                if(!empty($kit_item_product['empty_group_image'])){
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize($kit_item_product['empty_group_image'], 80, 80);
                }else{
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-empty-item-2.png', 80, 80);
                }

                $first_option_id = 'dc65e5e0764e444ec837';
                $kit_item_data['free_product_in_kit'] = $kit_item_product['free_product_in_kit'];

                $kit_item_data['display_options_input'] = $widget['table_mode_config']['display_options_input'];

                $checkbox_enable = $this->checkbox_enable;
                $kit_item_data['checkbox_in_kit_show'] = $checkbox_enable;
                if($checkbox_enable && ($kit_item_product['is_free_product'] ||  $kit_item_data['item_default_empty'])){
                    $kit_item_data['checkbox_in_kit_checked'] = false;
                }else{
                    $kit_item_data['checkbox_in_kit_checked'] = true;
                }
                if($kit_item_product['item_empty_mode']['enable'] || $kit_item_product['is_free_product']){
                    $kit_item_data['checkbox_in_kit_enable'] = true;
                }else{
                    $kit_item_data['checkbox_in_kit_enable'] = false;
                }

                $kit_item_data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($kit_item_product['product_id']);

                if($kit_item_product['is_free_product'] && $kit_item_product['free_product_in_kit']){
                    $kit_item_data['last_item'] = false;
                    $kit_items[$index1]['has_free_product_in_kit'] = true;
                }

                $kit_item_data['item_product']['product_info']['description'] = html_entity_decode($kit_item_product['product_info']['description'], ENT_QUOTES, 'UTF-8');
                $kit_item_data['item_product']['product_info']['short_description'] = $this->getShortDescriptionByOCV($kit_item_product['product_info']['description']);;

                if ($kit_item_product['product_info']['image']) {
                    $popup_image = $kit_item_product['product_info']['image'];
                }else {
                    $popup_image = 'placeholder.png';
                }

                if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $kit_item_data['item_product']['product_info']['popup_image'] = $this->model_tool_image->resize($popup_image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                } else {
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $kit_item_data['item_product']['product_info']['popup_image'] = $this->model_tool_image->resize($popup_image, $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
                    } else {
                        $kit_item_data['item_product']['product_info']['popup_image'] = $this->model_tool_image->resize($popup_image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
                    }
                }

                $kit_item_data['item_product']['product_info']['popup_images'] = array();

                $enable_pop_up_images = false;

                if ($enable_pop_up_images) {
                    if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                        $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                    } else {
                        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                            $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image, $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
                        } else {
                            $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
                        }
                    }

                    $popup_images = $this->model_catalog_product->getProductImages($kit_item_product['product_info']['product_id']);

                    foreach ($popup_images as $popup_image) {
                        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                            $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_width'));
                        } else {
                            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                                $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
                            } else {
                                $kit_item_data['item_product']['product_info']['popup_images'][] = $this->model_tool_image->resize($popup_image['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
                            }
                        }
                    }
                }


                if($kit_item_product['is_free_product'] && ($kit_item_product['free_product_in_kit'] )){
                    $input_name_prefix = 'kit_items_free_w['.$kit_item['item_position'].']['.$item_position_free.']';
                    $item_position_free++;
                }else{
                    $input_name_prefix = 'kit_items_w['.$kit_item['item_position'].']';
                }

                $kit_item_data['input_name_prefix'] = $input_name_prefix;


                
                $kit_item_data['kit_item_checkbox_checked'] = true;
                $kit_item_data['kit_item_checkbox_enable'] = true;
                $kit_item_data['empty_mode_can_be_empty'] = false;

                switch ($kit_item_product['item_mode']){
                    case 'fix_product':
                        $kit_item_data['kit_item_checkbox_checked'] = true;
                        $kit_item_data['kit_item_checkbox_enable'] = false;
                        break;
                        case 'select_product':
                            if($kit_item_data['empty_mode_item_is_empty']){
                                $kit_item_data['kit_item_checkbox_checked'] = false;
                            }else{
                                if( $index2==0){
                                    $kit_item_data['kit_item_checkbox_checked'] = true;
                                }else{
                                    $kit_item_data['kit_item_checkbox_checked'] = false;
                                }
                            }
                            if($kit_item_product['item_empty_mode']['enable'] && $kit_item_product['item_empty_mode']['default_empty']){
                                $kit_item_data['item_product']['product_info']['selected'] = false;
                            }
                            if($index2>0){
                                $kit_item_data['item_product']['product_info']['selected'] = false;
                            }
                            if($kit_item_product['item_empty_mode']['enable']){
                                $kit_item_data['empty_mode_can_be_empty'] = true;
                            }

                        break;
                        case 'free_product':
                            if($kit_item_product['free_product_default_in_kit']){
                                $kit_item_data['kit_item_checkbox_checked'] = true;
                            }else{
                                $kit_item_data['kit_item_checkbox_checked'] = false;
                            }
                        break;
                }

                $ocmod_point_005 = 1;


                $kit_item_data['product_click_mode'] = $widget['product_click_mode'];

                if(strripos($widget['template'], "custom_template_") !== false){
                    $file_name = 'custom_widget/' . $widget['template'] . '_kit_item';
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.tpl';
                    } else {
                        $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.twig';
                    }
                    if(!(file_exists($check_file_name))){
                        $file_name = 'kit_item_template_1';
                    }
                }else {
                    switch ($widget['template']){
                        case 'widget_template_1':
                            $file_name = 'kit_item_template_1';
                            break;
                        case 'widget_template_2':
                            $file_name = 'kit_item_template_2';
                            break;
                        case 'widget_template_3':
                            $file_name = 'kit_item_template_2';
                            break;
                        case 'widget_template_4':
                            $file_name = 'kit_item_template_4';
                            break;
                        case 'widget_template_5':
                            $file_name = 'kit_item_template_5';
                            break;
                        case 'widget_template_6':
                            $file_name = 'kit_item_template_6';
                            break;
                        default:
                            $file_name = 'kit_item_template_2';
                            break;
                    }

                }

                if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $kit_items[$index1]['item_html'][$index2] = $this->load->view('default/template/module/bundle_expert/'.$file_name.'.tpl', $kit_item_data);
                } else {
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $kit_items[$index1]['item_html'][$index2] = $this->load->view('module/bundle_expert/'.$file_name.'.tpl', $kit_item_data);
                    } else {
                        $kit_items[$index1]['item_html'][$index2] = $this->load->view('module/bundle_expert/'.$file_name.'', $kit_item_data);
                    }
                }

            }

        }


        
        if ($data['kit_has_free_products']){
            $free_item_positons = $this->bundle_expert->getKitFreeItemPositions($widget['kit_info']['kit_id']);
            foreach ($free_item_positons as $index_pos=>$free_item_positon) {
                $free_item = $this->bundle_expert->getKitFreeItemInfo($widget['kit_info']['kit_id'], $free_item_positon);
                $last_item=($index_pos==(count($free_item_positons)-1))?true:false;
                $data['free_products_add_button_html'][$free_item_positon] = $this->createFreeProductAddButton($widget['template'], $free_item_positon, $free_item['item_empty_mode']['title'], $widget['table_mode_config']['display_options_input'], $last_item, $free_item, $widget['product_click_mode']);
            }
        }else{
            $data['free_products_add_button_html'] = array();
        }

        $data['default_hidden_free_products_container'] = true;
        foreach ($kit_items as $kit_item){
            foreach ($kit_item['products'] as $kit_item_product){
                if($kit_item_product['is_free_product'] && !$kit_item_product['free_product_in_kit']){
                    $data['default_hidden_free_products_container'] = false;
                }
                if(!$data['default_hidden_free_products_container']){
                    break;
                }
            }
            if(!$data['default_hidden_free_products_container']){
                break;
            }
        }

        foreach ($kit_items as $index=>$kit_item){
            $free_item_all_products_in_kit = true;
            foreach ($kit_item['products'] as $kit_item_product){
                if($kit_item_product['is_free_product'] && !$kit_item_product['free_product_in_kit']){
                    $free_item_all_products_in_kit = false;
                    break;
                }
            }

            $kit_items[$index]['free_item_all_products_in_kit'] = $free_item_all_products_in_kit;

        }

        
        $new_kit_items = array();
        if ($widget['kit_info']['kit_mode'] == 'auto_list' && $widget['kit_info']['kit_mode_auto_list_grouping'] != 'none') {
            switch ($widget['kit_info']['kit_mode_auto_list_grouping'] ) {
                case 'by_category':
                case 'by_category_top':
                case 'by_category_bottom':
                    
                    foreach ($kit_items as $index1=>$kit_item){
                        foreach ($kit_item['products'] as $index2=>$kit_item_product){
                            $product_info = $kit_item_product['product_info'];
                            switch ($widget['kit_info']['kit_mode_auto_list_grouping']){
                                case 'by_category':
                                    $category = $this->bundle_expert->getProductMainCategory($product_info['product_id']);
                                    break;
                                case 'by_category_top':







                                    $category = $this->bundle_expert->getProductTopCategory($product_info['product_id']);
                                    break;
                                case 'by_category_bottom':







                                    $category = $this->bundle_expert->getProductBottomCategory($product_info['product_id']);
                                    break;
                            }
                            if(!empty($category)) {

                                $category_id = $category['category_id'];
                                if (!isset($new_kit_items[$category_id])) {
                                    $kit_item['item_title'] = $category['name'];
                                    $new_kit_items[$category_id] = $kit_item;
                                    $new_kit_items[$category_id]['products'] = array();
                                    $new_kit_items[$category_id]['item_html'] = array();

                                }

                                $new_kit_items[$category_id]['products'][] = $kit_item_product;
                                $new_kit_items[$category_id]['item_html'][] = $kit_items[$index1]['item_html'][$index2];

                            }


                        }

                    }
                    foreach ($new_kit_items as $index=>$kit_item){
                        $new_kit_items[$index]['item_position'] = $index;
                    }
                    if(!empty($new_kit_items)){
                        $kit_items = $new_kit_items;
                    }

                    break;
            }
        }
        

        $data['kit_items'] = $kit_items;

        $data['kit_edit'] = $kit_items_data['kit_edit'];

        if ($widget['kit_info']['image']) {
            $data['background_image'] = $this->config->get('config_ssl') . 'image/' . $widget['kit_info']['image'];
        } else {
            $data['background_image'] = '';
        }

        if($widget['background_image_size']['mode']){
            $background_image_sizes = array('width'=>$widget['background_image_size']['width'], 'height'=>$widget['background_image_size']['height']);
            if($background_image_sizes['width']!='auto')
                $background_image_sizes['width'] .= 'px';
            if($background_image_sizes['height']!='auto')
                $background_image_sizes['height'] .= 'px';
        }else{
            $background_image_sizes = array('width'=>'auto', 'height'=>'200px');
        }

        $data['background_image_sizes'] = $background_image_sizes;

        $data['add_to_cart_mode'] = 'simple';

        if($kit_items_data['kit_has_only_simple_options'] || $kit_items_data['kit_has_select_products'])
            $data['add_to_cart_mode'] = 'question';

        if($kit_items_data['kit_has_require_options']) {
            $data['add_to_cart_mode'] = 'open_form';
            $second_option_id = '70604031a6e68c8fa6be';
        }

        $data['kit_info_description_text'] = html_entity_decode($data['widget']['kit_info']['description']);
        $data['kit_info_custom_field_text'] = html_entity_decode($data['widget']['kit_info']['custom_field_text']);
        $data['kit_info_custom_field_string'] = ($data['widget']['kit_info']['custom_field_string']);
        $data['kit_info_custom_field_number'] = ($data['widget']['kit_info']['custom_field_number']);

        if (isset($first_option_id) && isset($second_option_id))
            unset($second_option_id);

        $data['checkbox_in_kit_show'] = $checkbox_enable = $this->checkbox_enable;;

        if($kit_items_data['total_kit_old']) {
            $data['total_kit_old'] = $this->currency->format($kit_items_data['total_kit_old'], $this->session->data['currency']);
            $data['total_kit_old_value'] = $this->currency->format($kit_items_data['total_kit_old'], $this->session->data['currency'],'', false);
        }else {
            $data['total_kit_old'] = false;
            $data['total_kit_old_value'] = '';
        }

        $data['total_kit'] = $this->currency->format($kit_items_data['total_kit'], $this->session->data['currency']);
        $data['total_kit_value'] = $this->currency->format($kit_items_data['total_kit'], $this->session->data['currency'],'', false);

        if($kit_items_data['total_default'] != $kit_items_data['total_default_new']){
            $data['total_default_new'] = $this->currency->format($kit_items_data['total_default_new'], $this->session->data['currency']);;
            $data['total_default_new_value'] = $this->currency->format($kit_items_data['total_default_new'], $this->session->data['currency'],'', false);;
        }else{
            $data['total_default_new'] = false;
            $data['total_default_new_value'] = '';
        }

        $data['kit_weight'] = $kit_items_data['kit_weight'];

	    $data['total_default'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency']);
	    $data['total_default_value'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency'],'', false);

        


        $data['profit_value'] =  $kit_items_data['profit_value'];
        $data['profit_price'] = $kit_items_data['profit_price'];
        $data['profit_percent'] = $kit_items_data['profit_percent'];
        $data['profit_prefix'] = $kit_items_data['profit_prefix'];

        
        if ($data['total_default_value'] == $data['total_kit_value']) {
            $data['display_total_default'] = false;
        } else {
            $data['display_total_default'] = true;
        }

        $data['display_options_input'] = $widget['table_mode_config']['display_options_input'];

        $data['product_as_kit_mode'] = $widget['kit_info']['kit_as_product'];
        $data['product_as_kit_mode_light_mode'] = $widget['kit_info']['kit_as_product_light_mode'];

        if(isset($this->session->data['token']) || isset($this->session->data['user_token'])){
            if(isset($this->session->data['token'])){
                $data['show_settings_icon'] = true;
                $data['kit_settings_link'] = $this->link('catalog/bundle_expert_kit/edit', 'token=' . $this->session->data['token'] . '&kit_id=' . $widget['kit_info']['kit_id'], true);
                $data['widget_settings_link'] = $this->link('catalog/bundle_expert_widget/edit', 'token=' . $this->session->data['token'] . '&widget_id=' . $widget['widget_id'], true);
            }
            if(isset($this->session->data['user_token'])){
                $data['show_settings_icon'] = true;
                $data['kit_settings_link'] = $this->link('catalog/bundle_expert_kit/edit', 'user_token=' . $this->session->data['user_token'] . '&kit_id=' . $widget['kit_info']['kit_id'], true);
                $data['widget_settings_link'] = $this->link('catalog/bundle_expert_widget/edit', 'user_token=' . $this->session->data['user_token'] . '&widget_id=' . $widget['widget_id'], true);
            }
        }else{
            $data['show_settings_icon'] = false;

        }

        
        if($this->bundle_expert->bundle_expert_settings['cache_widgets']){
            $data['show_settings_icon'] = false;

        }

        $ocmod_point_004 = 1;

        if(strripos($widget['template'], "custom_template_") !== false){
            $file_name = 'custom_widget/' . $widget['template'] . '_widget';
        }else {
            $file_name = $widget['template'];
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.tpl';
        } else {
            $check_file_name = DIR_APPLICATION . 'view/theme/default/template/module/bundle_expert/'.$file_name.'.twig';
        }
        if(!(file_exists($check_file_name))){
            $file_name = 'widget_template_2';
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            return $this->load->view('default/template/module/bundle_expert/' . $file_name . '.tpl', $data);
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                return $this->load->view('module/bundle_expert/' . $file_name . '.tpl', $data);
            }else{
                return $this->load->view('module/bundle_expert/' . $file_name. '', $data);
            }

        }

    }

    public function getCartColorHandler($settings){

	    $color_handler = array();

	    $products = $settings['products'];
        $prev_kit_unique_id = -1;
        $kit_even = true ;
	    foreach ($products as $product){

            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                $key = $product['key'];
            }else{
                $key = $this->bundle_expert_cart->getCartKitKey($product['cart_id']);
            }

            $d = unserialize(base64_decode($key));

            $kit_unique_id = (isset($d['cart_kit_info'])&& isset($d['cart_kit_info']['kit_unique_id']))?$d['cart_kit_info']['kit_unique_id']:'';
            $kit_even = ($kit_unique_id != $prev_kit_unique_id) ? $kit_even = !$kit_even : $kit_even;
            $prev_kit_unique_id = $kit_unique_id;

            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                $handler_index = $key;
            } else {
                $handler_index = $product['cart_id'];
            }

            if (!empty($kit_unique_id)) {
                $color_handler[$handler_index] = '<tr class="kit-color-handler-' . (int)$kit_even . '"><td></td></tr>';
            } else {
                $color_handler[$handler_index] = '';
            }
        }

        return $color_handler;
    }

    private function createFreeProductProductForm($item_position, $title, $free_item){
        $html_form_product = '';

        $data = array();

        $data['item_position'] = $item_position;
        $data['item_position_free'] = -1;

        $data['text_add_product'] = $this->language->get('text_add_product');
        $data['text_select_product'] = $this->language->get('text_select_product');
        $data['text_item_not_selected'] = $this->language->get('text_item_not_selected');
        $data['text_item_not_selected'] = $this->language->get('text_item_not_selected');

        $data['item_default_empty'] = false;
        $data['empty_mode_item_is_empty'] = false;
        $data['item_empty_title'] = '';

        $data['free_item_title'] = $title;

        $data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-add-free-product-2.png', 80, 80);

        if(!empty($free_item['empty_group_image'])){
            $data['empty_image_name'] = $this->model_tool_image->resize($free_item['empty_group_image'], 80, 80);
        }else{
            $data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-add-free-product-2.png', 80, 80);
        }

        $data['product_form_unique_id'] = uniqid();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            $html_form_product = $this->load->view('default/template/module/bundle_expert/kit_form_product_free_product.tpl', $data);
        }else{
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $html_form_product = $this->load->view('module/bundle_expert/kit_form_product_free_product.tpl', $data);
            }else{
                $html_form_product = $this->load->view('module/bundle_expert/kit_form_product_free_product', $data);
            }

        }

        return $html_form_product;
    }




    public function createKitForm($kit_info, $admin_mode, $main_product_id='', $kit_items_data=array(), $kit_from_cart = false, $kit_unique_id = '', $add_owl_carousel=false){

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');
        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');

        $kit_data['kit_id'] = $kit_info['kit_id'];
        $kit_data['main_product_id'] = $main_product_id;
        $kit_data['title'] = $kit_info['title'];

        $kit_data['kit_from_cart'] = $kit_from_cart;
        $kit_data['kit_unique_id'] = $kit_unique_id;

        $kit_data['kit_items'] = $kit_items_data['kit_items'];
        $kit_data['total_kit'] = strip_tags($this->currency->format($kit_items_data['total_kit'], $this->session->data['currency']));
        $kit_data['total_kit_old'] = strip_tags($this->currency->format($kit_items_data['total_kit_old'], $this->session->data['currency']));
        $kit_data['total_default'] = strip_tags($this->currency->format($kit_items_data['total_default'], $this->session->data['currency']));
        $kit_data['total_default_new'] = strip_tags($this->currency->format($kit_items_data['total_default_new'], $this->session->data['currency']));

        $kit_data['products_form'] = array();

        $kit_data['text_select_product_process'] = $this->language->get('text_select_product_process');;
        $kit_data['text_to_select'] = $this->language->get('text_to_select');
        $kit_data['text_cancel'] = $this->language->get('text_cancel');
        $kit_data['text_close'] = $this->language->get('text_close');
        $kit_data['text_total'] = $this->language->get('text_total');
        $kit_data['text_add_kit_to_cart'] = $this->language->get('text_add_kit_to_cart');
        $kit_data['text_update_kit_at_cart'] = $this->language->get('text_update_kit_at_cart');

        $kit_data['text_select'] = $this->language->get('text_select');

        
        $kit_data['free_products_add_button_html'] = array();
        $kit_data['free_products_product_form_html'] = array();
        $free_item_positons = $this->bundle_expert->getKitFreeItemPositions($kit_info['kit_id']);
        foreach ($free_item_positons as $index_pos=>$free_item_positon){
            if ($free_item_positon >= 0) {
                $free_item = $this->bundle_expert->getKitFreeItemInfo($kit_info['kit_id'], $free_item_positon);
                $last_item=($index_pos==(count($free_item_positons)-1))?true:false;
                $kit_data['free_products_add_button_html'][$free_item_positon] = $this->createFreeProductAddButton('widget_template_1', $free_item_positon, $free_item['item_empty_mode']['title'],0,$last_item, $free_item, 'default');
                $kit_data['free_products_product_form_html'][$free_item_positon] = $this->createFreeProductProductForm($free_item_positon, $free_item['item_empty_mode']['title'], $free_item);
            }else{

            }
        }

        $item_position = 0;
        $item_position_free = -1;

        $kit_items =  $kit_items_data['kit_items'];

        $kit_data['free_products_table_mode'] = false;

        $prev_free_product_position = -1;
        foreach ($kit_items as $index1=>$kit_item){

            if(isset($kit_item['products'][0])) {
                $kit_item_product = $kit_item['products'][0];

                $item_position = $kit_item['item_position'];

                if ($kit_item_product['is_free_product']) {
                    if ($item_position != $prev_free_product_position) {
                        $item_position_free = 0;
                    } else {
                        $item_position_free++;
                    }
                    $prev_free_product_position = $item_position;
                }

                $kit_item_product['empty_mode_item_is_empty'] = $kit_item['empty_mode_item_is_empty'];
                
                $html_form_product = $this->createProductForm($kit_info, $kit_item_product, $item_position, $item_position_free, true, $kit_from_cart, $kit_unique_id);

                $kit_data['products_form'][] = array(
                    'product_id' => $kit_item_product['product_id'],
                    'item_position' => $item_position,
                    'html' => $html_form_product,
                );


                $kit_item_data = array();
                $kit_item_data['item_product'] = $kit_item_product;
                $kit_item_data['item_position'] = $item_position;
                $kit_item_data['item_product_position'] = 0;
                $kit_item_data['selectable'] = $kit_item['selectable'];
                if($kit_item_product['item_mode']=='select_product'){
                    $kit_item_data['selectable'] = 1;
                }

                $kit_item_data['last_item'] = ($index1 == (count($kit_items) - 1)) ? true : false;
                if(!empty($kit_data['free_products_add_button_html'])){
                    $kit_item_data['last_item'] = false;
                }

                $kit_item_data['text_product_for_select'] = $this->language->get('text_select_product');

                $kit_item_data['text_add_product'] = $this->language->get('text_add_product');
                $kit_item_data['text_item_not_selected'] = $this->language->get('text_item_not_selected');
                $kit_item_data['text_loading'] = $this->language->get('text_loading');
                $kit_item_data['button_upload'] = $this->language->get('button_upload');
                $kit_item_data['text_select'] = $this->language->get('text_select');


                if(!empty($kit_item_product['empty_group_image'])){
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize($kit_item_product['empty_group_image'], 80, 80);
                }else{
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-empty-item-2.png', 80, 80);
                }


                $item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_unique_id, $index1);

                if (($kit_from_cart && $kit_item_product['item_empty_mode']['enable'] && !$item_in_cart)
                        || ($kit_item_product['empty_mode_item_is_empty']) ) {
                    $kit_item_data['empty_mode_item_is_empty'] = true;
                    $kit_item_data['show_empty'] = true;
                    
                    $kit_item_data['item_product']['product_id'] = -1;
                } else {
                    $kit_item_data['empty_mode_item_is_empty'] = false;
                    $kit_item_data['item_empty_title'] = '';
                    $kit_item_data['show_empty'] = false;
                }

                if ($kit_item_product['is_free_product']){
                    $kit_item_data['item_position_free'] = $item_position_free;
                }else{
                    $kit_item_data['item_position_free'] = -1;
                }

                $kit_item_data['free_product_in_kit'] = isset($kit_item_product['free_product_in_kit'])?$kit_item_product['free_product_in_kit']:0;

                
                $checkbox_enable = $this->checkbox_enable;
                $kit_item_data['checkbox_in_kit_show'] = $checkbox_enable;
                if($checkbox_enable){
                    $kit_item_data['checkbox_in_kit_checked'] = true;
                }else{
                    $kit_item_data['checkbox_in_kit_checked'] = false;
                }
                if($kit_item_product['item_empty_mode']['enable'] || $kit_item_product['is_free_product']){
                    $kit_item_data['checkbox_in_kit_enable'] = true;
                }else{
                    $kit_item_data['checkbox_in_kit_enable'] = false;
                }

                if($kit_item_product['is_free_product'] && $kit_item_product['free_product_in_kit']){
                    $kit_item_data['last_item'] = false;
                }

                if($kit_item_product['is_free_product'] && $kit_item_product['free_product_in_kit']){
                    
                    $input_name_prefix = 'kit_items_free_wf['.$kit_item['item_position'].']['.$item_position_free.']';
                    
                    
                }else{
                    
                    $input_name_prefix = 'kit_items_wf['.$kit_item['item_position'].']';

                }
                $kit_item_data['input_name_prefix'] = $input_name_prefix;

                $kit_item_data['product_click_mode'] = 'default';

                if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $kit_items[$index1]['item_html'] = $this->load->view('default/template/module/bundle_expert/kit_item_template_1.tpl', $kit_item_data);
                } else {
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $kit_items[$index1]['item_html'] = $this->load->view('module/bundle_expert/kit_item_template_1.tpl', $kit_item_data);
                    } else {
                        $kit_items[$index1]['item_html'] = $this->load->view('module/bundle_expert/kit_item_template_1', $kit_item_data);
                    }

                }

            }else{
                $kit_items[$index1]['item_html']='';
            }
            $item_position++;
        }



        
        $products_form_html = array();
        foreach ($kit_data['products_form'] as $product_id => $product_form){
            $item_positon = $product_form['item_position'];
            $products_form_html[$item_positon][] = $product_form;
        }
        foreach ($kit_data['free_products_product_form_html'] as $item_position => $product_form){
            $products_form_html[$item_position][] = array(
                'product_id' => -1,
                'item_position' => $item_position,
                'html' => $product_form,
            );
        }
        $kit_data['products_form_html'] = $products_form_html;

        $kit_data['kit_items'] = $kit_items;

        if($admin_mode){
            $kit_data['enable_search_filter']=true;
        }else{
            $kit_data['enable_search_filter']=false;
        }
        $kit_data['admin_mode'] = $admin_mode;

        
        
        if(isset($kit_items[0]['products'][0]) && $kit_items[0]['products'][0]['main']){
            $kit_data['main_product_id'] = $kit_items[0]['products'][0]['product_id'];
        }

        $kit_data['add_owl_carousel'] = $add_owl_carousel;

        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            $html = $this->load->view('default/template/module/bundle_expert/kit_form.tpl', $kit_data);
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $html = $this->load->view('module/bundle_expert/kit_form.tpl', $kit_data);
            }else{
                $html = $this->load->view('module/bundle_expert/kit_form', $kit_data);
            }

        }

        return $html;
    }

    private function createProductForm($kit_info, $kit_product, $item_position, $item_position_free, $show_empty_product=true, $kit_from_cart=false, $kit_from_cart_unique_id=''){

        $html_form_product = '';

        $data = array();

        $product = $kit_product;

        $data['item_position'] = $item_position;
        $data['item_position_free'] = $item_position_free;
        $data['product_id'] = $product['product_id'];
        $data['fixed_options'] = $kit_product['fixed_options'];



        $product_info = $kit_product['product_info'];

        if ($product_info) {

            $data['text_select'] = $this->language->get('text_select');
            $data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $data['text_model'] = $this->language->get('text_model');
            $data['text_sku'] = $this->language->get('text_sku');
            $data['text_reward'] = $this->language->get('text_reward');
            $data['text_points'] = $this->language->get('text_points');
            $data['text_stock'] = $this->language->get('text_stock');
            $data['text_discount'] = $this->language->get('text_discount');
            $data['text_default_discount'] = $this->language->get('text_default_discount');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['text_option'] = $this->language->get('text_option');
            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $data['text_write'] = $this->language->get('text_write');
            $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
            $data['text_note'] = $this->language->get('text_note');
            $data['text_tags'] = $this->language->get('text_tags');
            $data['text_related'] = $this->language->get('text_related');
            $data['text_loading'] = $this->language->get('text_loading');
            $data['text_category'] = $this->language->get('text_category');

            $data['entry_qty'] = $this->language->get('entry_qty');
            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_review'] = $this->language->get('entry_review');
            $data['entry_rating'] = $this->language->get('entry_rating');
            $data['entry_good'] = $this->language->get('entry_good');
            $data['entry_bad'] = $this->language->get('entry_bad');

            $data['button_cart'] = $this->language->get('button_cart');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');
            $data['button_upload'] = $this->language->get('button_upload');
            $data['button_continue'] = $this->language->get('button_continue');


            $data['heading_title'] = $product_info['name'];

            $this->load->model('catalog/review');

            $data['tab_main'] = $this->language->get('tab_main');
            $data['tab_description'] = $this->language->get('tab_description');
            $data['tab_attribute'] = $this->language->get('tab_attribute');
            $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

            $data['product_id'] = (int)$product['product_id'];
            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $data['product_url'] = $this->url->link('product/product', 'product_id=' . $product_info['product_id']);
            $data['model'] = $product_info['model'];
            $data['sku'] = $product_info['sku'];
            $data['reward'] = $product_info['reward'];
            $data['points'] = $product_info['points'];


            $this->load->model('catalog/product');
            $this->load->model('catalog/category');
            $data['product_category'] = array();
            $product_categories = $this->model_catalog_product->getCategories($product_info['product_id']);
            foreach ($product_categories as $product_category){
                $category_info = $this->model_catalog_category->getCategory($product_category['category_id']);

                if ($category_info) {
                    $data['product_category'] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $product_category['category_id'])
                    );
                }
            }


            if ($product_info['quantity'] <= 0) {
                $data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
                $data['stock'] = $product_info['quantity'];
            } else {
                $data['stock'] = $this->language->get('text_instock');
            }


            $data['stock_status_result'] = $product_info['stock_status_result'];

            $this->load->model('tool/image');

            $data['quantity'] = $kit_product['quantity'];

            if ($product_info['image']) {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
                    }else{
                        $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
                    }
                }
            } else {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
                    }else{
                        $data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
                    }
                }
            }

            if ($product_info['image']) {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
                    }else{
                        $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
                    }
                }
            } else {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
                    }else{
                        $data['thumb'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
                    }
                }
            }


            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($product['product_id']);

            if ($product_info['image'] && !empty($results)) {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['images'][] = array(
                        'popup' => $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
                        'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                    );
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['images'][] = array(
                            'popup' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height')),
                            'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
                        );
                    }else{
                        $data['images'][] = array(
                            'popup' => $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
                            'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
                        );
                    }

                }
            }


            $data['product_small_thumb'] = $this->model_tool_image->resize($product_info['image'], 160, 160);


            foreach ($results as $result) {
                if(version_compare ($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $data['images'][] = array(
                        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_width')),
                        'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                    );
                }else{
                    if(version_compare ($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $data['images'][] = array(
                            'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height')),
                            'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
                        );
                    }else{
                        $data['images'][] = array(
                            'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
                            'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
                        );
                    }

                }
            }


            
            $product_info_1 = $this->bundle_expert->getProductInfo($product['product_id']);;
            $price_data = $this->bundle_expert->getKitProductPriceData($kit_info, $kit_product, $product_info_1, array(), true, $product['quantity']);

            
            
            $data['price'] = $product_info['price'];


            
            if($product_info['special']!==false)
                $data['special'] = $product_info['special'];
            else
                $data['special'] = $product_info['special'];





            $data['options'] = array();
            $data['options'] = $product_info['options'];

            
            $data['options'] = $this->createOptionsNameForProductForm($data['options'], $kit_product, $item_position, $item_position_free);
            $data = $this->createInputsNameForProductForm($data, $kit_product, $item_position, $item_position_free);

            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format($price_data['tax'], $this->session->data['currency']);
            } else {
                $data['tax'] = false;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($product['product_id']);

            $data['discounts'] = array();

            foreach ($discounts as $discount) {
                $data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
                );
            }

            if(!$kit_info['enable_discount']){
                $data['discounts'] = array();
            }

            if ($product_info['minimum']) {
                $data['minimum'] = $product_info['minimum'];
            } else {
                $data['minimum'] = 1;
            }

            $data['minimum'] = 1;

            $data['review_status'] = $this->config->get('config_review_status');

            if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
                $data['review_guest'] = true;
            } else {
                $data['review_guest'] = false;
            }

            if ($this->customer->isLogged()) {
                $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
            } else {
                $data['customer_name'] = '';
            }

            $data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
            $data['rating'] = (int)$product_info['rating'];
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product['product_id']);

            $data['tags'] = array();
            $data['tags_count'] = 0;

            if ($product_info['tag']) {
                $tags = explode(',', $product_info['tag']);

                foreach ($tags as $tag) {
                    $data['tags'][] = array(
                        'tag'  => trim($tag),
                        'href' => $this->url->link('product/search', 'tag=' . trim($tag))
                    );
                }

                $data['tags_count'] = count($tags);
            }

            $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
            $data['recurrings'] = $this->model_catalog_product->getProfiles($product['product_id']);
            $data['recurrings'] = array();

            $this->model_catalog_product->updateViewed($product['product_id']);

            if ($this->config->get('config_google_captcha_status')) {
                $this->document->addScript('https://www.google.com/recaptcha/api.js');

                $data['site_key'] = $this->config->get('config_google_captcha_public');
            } else {
                $data['site_key'] = '';
            }

            $data['text_add_product'] = $this->language->get('text_add_product');
            $data['text_select_product'] = $this->language->get('text_select_product');
            $data['text_item_not_selected'] = $this->language->get('text_item_not_selected');
            $data['text_item_not_selected'] = $this->language->get('text_item_not_selected');

            if(!empty($kit_product['empty_group_image'])){
                $kit_item_data['empty_image_name'] = $this->model_tool_image->resize($kit_product['empty_group_image'], 80, 80);
            }else{
                $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-empty-item-2.png', 80, 80);
            }



            $item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_from_cart_unique_id, $item_position);

            if (($kit_from_cart && $kit_product['item_empty_mode']['enable'] && !$item_in_cart)
            || ($kit_product['empty_mode_item_is_empty']) ) {
                $data['empty_mode_item_is_empty'] = true;
                $data['show_empty'] = true;
                
                $data['product_id'] = '-1';
            } else {
                $data['empty_mode_item_is_empty'] = false;
                $data['item_empty_title'] = '';
                $data['show_empty'] = false;
            }

            if ($kit_product['is_free_product']){
                $data['item_position_free'] = $item_position_free;
            }else{
                $data['item_position_free'] = -1;
            }
            if (isset($kit_product['free_product_in_kit'])){
                $data['free_product_in_kit'] = $kit_product['free_product_in_kit'];
            }else{
                $data['free_product_in_kit'] = -1;
            }

            if(!empty($kit_product['empty_group_image'])){
                $data['empty_image_name'] = $this->model_tool_image->resize($kit_product['empty_group_image'], 80, 80);
            }else{
                $data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-empty-item-2.png', 80, 80);
            }



            $data['item_product'] = $kit_product;

            $data['product_form_unique_id'] = uniqid();

            $ocmod_point_002 = 1;

            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $html_form_product = $this->load->view('default/template/module/bundle_expert/kit_form_product.tpl', $data);
            }else{
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $html_form_product = $this->load->view('module/bundle_expert/kit_form_product.tpl', $data);
                }else{
                    $html_form_product = $this->load->view('module/bundle_expert/kit_form_product', $data);
                }

            }

        }

        return $html_form_product;
    }

    private function checkOwlCarouselForWidgets($widgets){
	    $result = false;

	    
        
        $carousel_widgetstemplates = array(
            'widget_template_1',
            'custom_template_1',
        );

        foreach ($widgets as $widget){
            if(in_array($widget['template'], $carousel_widgetstemplates )){
                $result = true;
                return $result;
            }
        }

        
        $widgets_with_sliders = array();
        foreach ($widgets as $widget){

            if(!isset($widgets_with_sliders[$widget['widget_id']])){
                $widgets_with_sliders[$widget['widget_id']] = 1;
            }else{
                if($widget['slider_mode']){
                    $result = true;
                    return $result;
                }
            }

        }

	    return $result;
    }

    private function checkOwlCarouselActiveForKitForm(){

	    $add = false;

        if(isset($this->request->post['owl_carousel_status'])){
            $owl_carousel_status = $this->request->post['owl_carousel_status'];
        }else{
            $owl_carousel_status = true;
        }

        if($owl_carousel_status){
            $add = false;
        }else{
            $add = true;
        }

        return $add;
    }

    public function getKitForm($admin_mode = false){
        $json = array();

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');

        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');

        $this->load->model('tool/image');

        $this->bundle_expert->convertInputData($this->request->post);

        if(!isset($this->request->post['kit_id'])){
            return;
        }

        if(isset($this->request->post['kit_items'])){
            $kit_items_request = $this->request->post['kit_items'];
        }else{
            $kit_items_request = array();
        }


        if(isset($this->request->post['kit_items_free'])){
            $kit_items_free_request = $this->request->post['kit_items_free'];
        }else{
            $kit_items_free_request = array();
            $free_products = array();
        }

        foreach ($kit_items_free_request as $kit_item_free){
            foreach ($kit_item_free as $kit_item) {
                if ($kit_item['is_free_product']) {
                    $kit_items_request[] = $kit_item;
                    $free_products[] = $kit_item;
                }
            }
        }

        $kit_id = $this->request->post['kit_id'];

        $main_product_id = $this->request->post['main_product_id'];

        $main_product_in_cart = $this->bundle_expert->checkFreeMainProductInCart($main_product_id);

        $kit_info = $this->model_module_bundle_expert->getKit($kit_id);

        if (!empty($kit_info)) {

            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => true,
                'only_first' => false,
                'admin_mode' => $admin_mode,
            );

            if($main_product_in_cart)
                $ignore_product_in_cart =(int) $main_product_id;
            else
                $ignore_product_in_cart = "";

            $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings, $ignore_product_in_cart);


            
            foreach ($kit_items_request as $index=>$kit_item_request){

                if($kit_item_request['is_free_product']!=="1"){
                    $item_position = $kit_item_request['item_position'];
                    $product_id = $kit_item_request['product_id'];
                    $item_product=$this->bundle_expert->findProductDataInKit($product_id, $kit_items,$item_position);
                    $kit_items[$item_position]['products'][0] = $item_product;
                    $kit_items[$item_position]['empty_mode_item_is_empty'] = $kit_item_request['empty_mode_item_is_empty'];
                    if($kit_item_request['empty_mode_item_is_empty']){
                        $kit_items[$item_position]['products'][0]['item_empty_mode']['default_empty'] = 1;
                    }
                }

            }
            
            foreach ($kit_items as $index=>$kit_item){
                if(isset($kit_item['products'][0])){
                    if($kit_item['products'][0]['item_mode']=='select_product'){
                        $empty=true;
                        foreach ($kit_items_request as $kit_item_request){
                            if($kit_item_request['item_position']==$kit_item['item_position']){
                                $empty=false;
                                break;
                            }
                        }
                        
                        if($empty && !$admin_mode){
                        
                            $kit_items[$index]['empty_mode_item_is_empty']=1;
                        }
                    }
                }
            }


            
            $free_item_positons = $this->bundle_expert->getKitFreeItemPositions($kit_id);
            $kit_items2 = $kit_items;
            foreach ($free_item_positons as $free_item_positon) {
                if ($free_item_positon >= 0) {

                    unset($kit_items[$free_item_positon]);
                }
            }
            $first_position = count($kit_items);


                    $index = 0;
                    foreach ($free_products as $free_product) {
                        $item_position = $free_product['item_position'];
                        $free_product_data = $this->bundle_expert->findProductDataInKit($free_product['product_id'], $kit_items2, $item_position, $free_product['is_free_product'], $kit_id);
                        if(!empty($free_product_data)) {
                            $free_product_data['quantity'] = $free_product['quantity'];
                            $free_product_data['free_product_in_kit'] = $free_product['free_product_in_kit'];
                            $kit_items[$first_position + $index] = array('products' => array($free_product_data), 'selectable' => false, 'is_free_product' => $free_product['is_free_product'], 'item_position' => $free_product['item_position']);

                            $index++;
                        }
                    }



            
            foreach ($kit_items_request as $index0 => $kit_item_request) {
                $item_position = $kit_item_request['item_position'];

                
                $finded_index = -1;
                foreach ($kit_items as $index1 => $kit_item) {
                    if ($kit_item['item_position'] == $item_position) {
                        $finded_index = $index1;
                    }
                }

                if ($finded_index >= 0) {
                    if (!empty($kit_item_request['option'])) {
                        $product_info_options = $kit_items[$finded_index]['products'][0]['product_info']['options'];
                        $options = $this->model_checkout_bundle_expert->setPresetOptions($product_info_options, $kit_item_request['option']);
                        $kit_items[$finded_index]['products'][0]['product_info']['options'] = $options;
                    }

                    if ($kit_items[$finded_index]['products'][0]['quantity_edit']) {
                        $kit_items[$finded_index]['products'][0]['quantity'] = $kit_item_request['quantity'];
                    }
                }

            }


            $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, true, $admin_mode);

            $kit_info['kit_items'] = $kit_items_data['kit_items'];

            $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

            
            













            $add_owl_carousel = false;

            
            if ($kit_enable_status['display_kit']) {
                $html = $this->createKitForm($kit_info, $admin_mode, $main_product_id, $kit_items_data, false, '', $add_owl_carousel);
                $json['html'] = $html;
            } else {
                $json['kit_error'] = $kit_enable_status['text'];
            }
        } else {
            $json['kit_error'] = $this->language->get('text_kit_not_active_more');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }



    public function getKitFormFromCart($admin_mode = false){
        $json = array();

        if(!isset($this->request->post['kit_unique_id'])){
            return;
        }

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');

        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');

        $this->load->model('tool/image');

        $this->bundle_expert->convertInputData($this->request->post);

        $kit_unique_id = $this->request->post['kit_unique_id'];

        
        $product_as_kit_data = $this->bundle_expert_cart->checkProductAsKitInCart($kit_unique_id);
        if(!empty($product_as_kit_data)){
            $kit_unique_id = $this->bundle_expert_cart->getProductAsKitFirstItemKitUniqueId($product_as_kit_data['product_as_kit']['product_as_kit_unique_id']);
        }
        if(!empty($product_as_kit_data)){
            $product_as_kit_quantity = $product_as_kit_data['quantity'];
        }else{
            $product_as_kit_quantity = 0;
        }

        
        $product_as_kit_options = array();
        if(!empty($product_as_kit_data)){
            if(isset($product_as_kit_data['option'])) {
                $product_as_kit_options = $product_as_kit_data['option'];
            }
        }

        $kit_info = $this->model_checkout_bundle_expert->getCartKit($kit_unique_id, $admin_mode);

        if(!empty($kit_info)) {
            $main_product_id = $this->model_checkout_bundle_expert->getCartKitMainProduct($kit_unique_id);

            $kit_id = $kit_info['kit_id'];

            $main_product_in_cart = false;

            
            foreach ($kit_info['kit_items'] as $index=>$kit_item){
                if($kit_item['products'][0]['item_empty_mode']['enable']){
                    $item_in_cart = $this->bundle_expert_cart->getKitItemInCart($kit_unique_id, $index);
                    if(empty($item_in_cart)){
                        $kit_info['kit_items'][$index]['empty_mode_item_is_empty']=1;
                    }else{
                        $kit_info['kit_items'][$index]['empty_mode_item_is_empty']=0;
                    }
                }else{
                    $kit_info['kit_items'][$index]['empty_mode_item_is_empty']=0;
                }
                if( $kit_item['is_free_product']) {
                    $kit_info['kit_items'][$index]['products'][0]['free_product_in_kit'] = 1;
                    $kit_info['kit_items'][$index]['free_product_in_kit'] = 1;
                }
            }

            $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_info['kit_items'], true, true, $admin_mode, $kit_unique_id, true, $product_as_kit_quantity);

            
            if ($main_product_id && $main_product_in_cart) {
                $kit_main_product = $kit_items_data['kit_items'][0]['products'][0];
                $free_main_product = $this->model_checkout_bundle_expert->getFreeMainProductInCart($main_product_id, $kit_main_product['quantity']);

                if ($free_main_product && isset($free_main_product['option']) && isset($kit_main_product['product_info']['options'])) {
                    $kit_main_product_options = $kit_main_product['product_info']['options'];
                    $free_main_product_options = $free_main_product['option'];
                    $kit_main_product_options = $this->model_checkout_bundle_expert->setPresetOptions($kit_main_product_options, $free_main_product_options);
                    $kit_items_data['kit_items'][0]['products'][0]['product_info']['options'] = $kit_main_product_options;
                }
            }


            $add_owl_carousel = false;

            
            $html = $this->createKitForm($kit_info, $admin_mode, $main_product_id, $kit_items_data, true, $kit_unique_id, $add_owl_carousel);

            $json['html'] = $html;
        }else{
            $json['kit_error'] = $this->language->get('text_kit_not_active_more');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getKitFormProduct($admin_mode = false){

        $json = array();

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');
        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');
        $this->load->model('tool/image');

        $this->bundle_expert->convertInputData($this->request->post);

        $kit_id = $this->request->post['kit_id'];
        $main_product_id = $this->request->post['main_product_id'];
        $item_position = $this->request->post['item_position'];
        $item_position_free = $this->request->post['item_position_free'];

        $product_id = $this->request->post['product_id'];
        $is_free_product = $this->request->post['is_free_product'];

        if (isset($this->request->post['kit_from_cart_unique_id']))
            $kit_from_cart_unique_id = $this->request->post['kit_from_cart_unique_id'];
        else
            $kit_from_cart_unique_id = '';

        $kit_info = $this->model_module_bundle_expert->getKit($kit_id, $kit_from_cart_unique_id);

        if(!empty($kit_info)) {

            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => false,
                'only_first' => false,
                'admin_mode' => $admin_mode,
            );

            $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings);

            $kit_product = $this->bundle_expert->findProductDataInKit($product_id, $kit_items, $item_position,$is_free_product, $kit_id);

            $kit_product_data = $this->bundle_expert->getDataKitProduct($kit_info, $kit_product);

            $kit_product_data['empty_mode_item_is_empty'] = 0;
            $kit_product_data['free_product_in_kit'] = 0;

            
            $html = $this->createProductForm($kit_info, $kit_product_data, $item_position, $item_position_free, false);

            $json['html'] = $html;
        } else {
            $json['kit_error'] = $this->language->get('text_kit_not_active_more');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getKitItemProducts($admin_mode = false){

        $json = array();

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');

        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');

        $this->load->model('tool/image');

        $this->bundle_expert->convertInputData($this->request->post);

        $kit_id = $this->request->post['kit_id'];
        $main_product_id = $this->request->post['main_product_id'];
        $item_position = $this->request->post['item_position'];
        $last_item_product_position = $this->request->post['last_item_product_position'];

        if(isset($this->request->post['kit_items'])){
            $products = $this->request->post['kit_items'];
        }else{
            $products = array();
        }

        if(isset($this->request->post['kit_items_free'])){
            $free_products = $this->request->post['kit_items_free'];
        }else{
            $free_products = array();
        }

        if (isset($this->request->post['filter_title']))
            $filter_title = $this->request->post['filter_title'];
        else
            $filter_title = '';

        if (isset($this->request->post['kit_from_cart_unique_id']))
            $kit_from_cart_unique_id = $this->request->post['kit_from_cart_unique_id'];
        else
            $kit_from_cart_unique_id = '';

        $kit_info = $this->model_module_bundle_expert->getKit($kit_id, $kit_from_cart_unique_id);

        if (!empty($kit_info)) {

            $products_quantity = $this->model_checkout_bundle_expert->getCartProductsQuantity();
            $options_quantity = $this->model_checkout_bundle_expert->getCartOptionsQuantity();

            
            $products_quantity = $this->bundle_expert->addProductsQuantityByOpenedKit($products, $item_position,$products_quantity);
            $options_quantity = $this->bundle_expert->addOptionsQuantityByOpenedKit($products, $item_position,$options_quantity);

            
            $free_products_skip = array();
            foreach ($free_products as $position=>$free_product_item) {
                if($position==$item_position) {
                    foreach ($free_product_item as $free_product) {
                        $free_products_skip [] = $free_product['product_id'];
                    }
                }
            }
            $free_products_get_all = false;

            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => true,
                'only_first' => false,
                'admin_mode' => $admin_mode,
            );

            $limit = 5;

            $filter = array(
                'filter_title' => $filter_title,
                'limit' => $limit+1,
                'last_item_product_position' => $last_item_product_position,
            );

            $ocmod_point_001 = 1;

            $kit_item_products = $this->bundle_expert->getKitItemProducts($kit_id, $get_kit_products_settings, $item_position, $products_quantity, $options_quantity, $filter, $free_products_get_all, $free_products_skip);

            $ocmod_point_003 = 1;

            $kit_item_products['empty_mode_item_is_empty'] = 0;



            
            if (count($kit_item_products['products']) > $limit) {
                $data['enable_more_button'] = true;
                $kit_item_products['products'] = array_slice($kit_item_products['products'], 0, ($limit));
                $filter['last_item_product_position']--;
            } else {
                $data['enable_more_button'] = false;

            }

            








            $kit_items[$item_position] = $kit_item_products;

            $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, false, $admin_mode, $kit_from_cart_unique_id);

            $kit_items = $kit_items_data['kit_items'];

            $kit_item_products = array();

            foreach ($kit_items[$item_position]['products'] as $index1 => $kit_item_product) {

                $kit_item_data = array();

                $kit_item_data['text_select'] = $this->language->get('text_select');
                $kit_item_data['text_loading'] = $this->language->get('text_loading');
                $kit_item_data['button_upload'] = $this->language->get('button_upload');

                $kit_item_data['item_product'] = $kit_item_product;
                $kit_item_data['item_position'] = $item_position;
                if(!$kit_item_product['is_free_product'])
                    $kit_item_data['item_position_free'] = -1;
                else
                    $kit_item_data['item_position_free'] = 0;
                $kit_item_data['item_product_position'] =$last_item_product_position+ $index1;

                $kit_item_data['selectable'] = 1;
                $kit_item_data['last_item'] = true;

                $kit_item_data['item_product']['product_info']['select_product'] = false;

                $kit_item_data['text_add_product'] = $this->language->get('text_add_product');
                $kit_item_data['text_item_not_selected'] = $this->language->get('text_item_not_selected');


                if(!empty($kit_item_product['empty_group_image'])){
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize($kit_item_product['empty_group_image'], 80, 80);
                }else{
                    $kit_item_data['empty_image_name'] = $this->model_tool_image->resize('catalog/be-empty-item-2.png', 80, 80);
                }




                $kit_item_data['text_product_for_select'] = $this->language->get('text_select_product');
                $kit_item_data['item_default_empty'] = false;
                $kit_item_data['empty_mode_item_is_empty'] = false;
                $kit_item_data['item_empty_title'] = '';
                $kit_item_data['free_product_in_kit'] = 0;

                if($kit_item_product['is_free_product'])
                    $kit_item_data['selectable'] = false;

                $kit_item_data['checkbox_in_kit_show'] = false;
                $kit_item_data['checkbox_in_kit_checked'] = true;
                $kit_item_data['checkbox_in_kit_enable'] = true;

                $kit_item_data['show_empty'] = false;


                if($kit_item_product['is_free_product']){
                    $input_name_prefix = 'kit_items_free['.$kit_item_data['item_position'].']['.'0'.']';
                }else{
                    $input_name_prefix = 'kit_items['.$kit_item_data['item_position'].']';
                }
                $kit_item_data['input_name_prefix'] = $input_name_prefix;

                $kit_item_data['product_click_mode'] = 'default';

                if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                    $kit_item_products[]['html'] = $this->load->view('default/template/module/bundle_expert/kit_item_template_1.tpl', $kit_item_data);
                } else {
                    if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                        $kit_item_products[]['html'] = $this->load->view('module/bundle_expert/kit_item_template_1.tpl', $kit_item_data);
                    } else {
                        $kit_item_products[]['html'] = $this->load->view('module/bundle_expert/kit_item_template_1', $kit_item_data);
                    }

                }

            }

            $data['text_load_more'] = $this->language->get('text_load_more');
            $data['text_empty_list'] = $this->language->get('text_empty_list');


            $data['kit_item_products'] = $kit_item_products;

            $data['item_products'] = $kit_item_products;
            $data['item_position'] = $item_position;

            $last_item_product_position = $filter['last_item_product_position'];

            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $html = $this->load->view('default/template/module/bundle_expert/kit_form_item_products.tpl', $data);
            } else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $html = $this->load->view('module/bundle_expert/kit_form_item_products.tpl', $data);
                } else {
                    $html = $this->load->view('module/bundle_expert/kit_form_item_products', $data);
                }

            }

            $json['html'] = $html;
            $json['last_item_product_position'] = $last_item_product_position;
        } else {
            $json['kit_error'] = $this->language->get('text_kit_not_active_more');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function getShortDescriptionByOCV($description){
        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            $description = utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..';
        } else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $description = utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..';
            } else {
                $description = utf8_substr(trim(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
            }

        }

        return $description;
    }

    private function checkConfigMainteance(){
	    $check = false;
        if (version_compare($this->bundle_expert->OC_VERSION, '2.0.1.1', '<=')) {
            if ($this->config->get('config_maintenance')) {
                $route = '';

                if (isset($this->request->get['route'])) {
                    $part = explode('/', $this->request->get['route']);

                    if (isset($part[0])) {
                        $route .= $part[0];
                    }
                }

                
                $this->load->library('user');

                $this->user = new User($this->registry);

                if (($route != 'payment') && !$this->user->isLogged()) {
                    $check = true;
                }else{
                    $check = false;
                }
            }
        }else{
            if (version_compare($this->bundle_expert->OC_VERSION, '2.0.3.1', '<=')) {
                if ($this->config->get('config_maintenance')) {
                    $route = '';

                    if (isset($this->request->get['route'])) {
                        $part = explode('/', $this->request->get['route']);

                        if (isset($part[0])) {
                            $route .= $part[0];
                        }
                    }

                    
                    $this->load->library('user');

                    $this->user = new User($this->registry);

                    if (($route != 'payment' && $route != 'api') && !$this->user->isLogged()) {
                        $check = true;
                    }else{
                        $check = false;
                    }
                }
            }else{
                if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.2', '<=')) {
                    if ($this->config->get('config_maintenance')) {
                        $route = '';

                        if (isset($this->request->get['route'])) {
                            $part = explode('/', $this->request->get['route']);

                            if (isset($part[0])) {
                                $route .= $part[0];
                            }
                        }

                        
                        $this->user = new User($this->registry);

                        if (($route != 'payment' && $route != 'api') && !$this->user->isLogged()) {
                            $check = true;
                        }else{
                            $check = false;
                        }
                    }
                }else{
                    if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<=')) {
                        if ($this->config->get('config_maintenance')) {
                            $route = '';

                            if (isset($this->request->get['route'])) {
                                $part = explode('/', $this->request->get['route']);

                                if (isset($part[0])) {
                                    $route .= $part[0];
                                }
                            }

                            
                            $this->user = new Cart\User($this->registry);

                            if (($route != 'payment' && $route != 'api') && !$this->user->isLogged()) {
                                $check = true;
                            }else{
                                $check = false;
                            }
                        }
                    }else{
                        if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<=')) {
                            if ($this->config->get('config_maintenance')) {
                                
                                if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
                                    $route = $this->request->get['route'];
                                } else {
                                    $route = $this->config->get('action_default');
                                }

                                $ignore = array(
                                    'common/language/language',
                                    'common/currency/currency'
                                );

                                
                                $this->user = new Cart\User($this->registry);

                                if ((substr($route, 0, 7) != 'payment' && substr($route, 0, 3) != 'api') && !in_array($route, $ignore) && !$this->user->isLogged()) {
                                    $check = true;
                                }else{
                                    $check = false;
                                }
                            }
                        }else{
                            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.3.2', '<=')) {
                                if ($this->config->get('config_maintenance')) {
                                    
                                    if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
                                        $route = $this->request->get['route'];
                                    } else {
                                        $route = $this->config->get('action_default');
                                    }

                                    $ignore = array(
                                        'common/language/language',
                                        'common/currency/currency'
                                    );

                                    
                                    $this->user = new Cart\User($this->registry);

                                    if ((substr($route, 0, 17) != 'extension/payment' && substr($route, 0, 3) != 'api') && !in_array($route, $ignore) && !$this->user->isLogged()) {
                                        $check = true;
                                    }else{
                                        $check = false;
                                    }
                                }
                            }else{

                            }
                        }
                    }
                }
            }
        }

        return $check;

    }

    public function link($route, $args = '', $secure = false) {
	    $admin_dir = 'admin';
        if ($this->ssl && $secure) {
            $url = HTTP_SERVER . $admin_dir. '/index.php?route=' . $route;
        } else {
            $url = HTTPS_SERVER . $admin_dir .'/index.php?route=' . $route;
        }

        if ($args) {
            if (is_array($args)) {
                $url .= '&amp;' . http_build_query($args);
            } else {
                $url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
            }
        }

        return $url;
    }

    private function createOptionsNameForProductForm($options, $kit_item, $item_position, $item_position_free){

	    foreach ($options as &$option){
            if ($option['type'] == 'checkbox') {
                foreach ($option['product_option_value'] as $index=>&$option_value){

                    if (!$kit_item['is_free_product']) {
                        $html_element_name = 'kit_items[' . $item_position . ']' . '[option][' . $option['product_option_id'] .'][' . $index . ']';
                    } else {
                        $html_element_name = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[option][' . $option['product_option_id'] .'][' . $index . ']';
                    }

                    $option_value['html_element_name'] = $html_element_name;

                }
            }else{
                if (!$kit_item['is_free_product']) {
                    $html_element_name = 'kit_items[' . $item_position . ']' . '[option][' . $option['product_option_id'] .']';
                } else {
                    $html_element_name = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[option][' . $option['product_option_id'] .']';
                }

                $option['html_element_name'] = $html_element_name;

            }

        }

        return $options;

    }
    private function createInputsNameForProductForm($data, $kit_item, $item_position, $item_position_free){

        if (!$kit_item['is_free_product']) {
            $data['html_element_name_item_position'] = 'kit_items[' . $item_position . ']' . '[item_position]';
            $data['html_element_name_item_position_free'] = 'kit_items[' . $item_position . ']' . '[item_position_free]';
            $data['html_element_name_empty_mode_item_is_empty'] = 'kit_items[' . $item_position . ']' . '[empty_mode_item_is_empty]';
            $data['html_element_name_is_free_product'] = 'kit_items[' . $item_position . ']' . '[is_free_product]';
            $data['html_element_name_free_product_in_kit'] = 'kit_items[' . $item_position . ']' . '[free_product_in_kit]';
            $data['html_element_name_quantity'] = 'kit_items[' . $item_position . ']' . '[quantity]';
            $data['html_element_name_product_id'] = 'kit_items[' . $item_position . ']' . '[product_id]';
        } else {
            $data['html_element_name_item_position'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[item_position]';
            $data['html_element_name_item_position_free'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[item_position_free]';
            $data['html_element_name_empty_mode_item_is_empty'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[empty_mode_item_is_empty]';
            $data['html_element_name_is_free_product'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[is_free_product]';
            $data['html_element_name_free_product_in_kit'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[free_product_in_kit]';
            $data['html_element_name_quantity'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[quantity]';
            $data['html_element_name_product_id'] = 'kit_items_free[' . $item_position . '][' . $item_position_free . ']' . '[product_id]';
        }

        return $data;

    }

}