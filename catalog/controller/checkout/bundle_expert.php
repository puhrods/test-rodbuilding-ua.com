<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerCheckoutBundleExpert extends Controller {

    private $post_data = array();

    public function index() {

    }
    public function remove_from_cart() {

        $this->load->language('checkout/cart');
        $this->load->language('module/bundle_expert');

        $json_kit = array();

        $this->bundle_expert->convertInputData($this->request->post);

        $kit_unique_id = $this->request->post['kit_unique_id'];

        
        $cart_products = $this->cart->getProducts();

        
        $product_as_kit_data = $this->bundle_expert_cart->getProductAsKitByUniqueId($kit_unique_id);
        if(!empty($product_as_kit_data)){
            $product_as_kit_unique_id = $product_as_kit_data['product_as_kit']['product_as_kit_unique_id'];
            $this->bundle_expert_cart->removeKitFromCartByProductAsKit($product_as_kit_unique_id);
        }else{
            
            $product_as_kit_data = $this->bundle_expert_cart->checkKitHasProductAsKit($kit_unique_id);
            if(!empty($product_as_kit_data)){
                $new_quantity = $product_as_kit_data['quantity'] - 1;
                $this->bundle_expert_cart->updateProductAsKitQuantityInCart($product_as_kit_data, $new_quantity);
            }

            $this->bundle_expert_cart->removeKitFromCart($kit_unique_id);

        }

        $this->cart->updateCartData();

        $json_kit['success'] = sprintf($this->language->get('text_remove'));

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);

        
        $total = $this->getTotalByOpencartVersion();

        $json_kit['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
        $json_kit['total_count'] = $this->cart->countProducts() ;
        $json_kit['total_price'] = $this->currency->format($total, $this->session->data['currency']) ;

        if(!isset($json_kit['total_count'])){
            $json_kit['total_count'] = 0;
        }

        $json_kit['action_code'] = 'remove_from_cart';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json_kit));
    }

    private function removeFreeMainProductInCart($product_id, $quantity) {
        $removed_product = array();

        $product_id = (int) $product_id;
        $quantity = (int) $quantity;

        $cart_products = $this->bundle_expert_cart->getCartProductsData();

        foreach ($cart_products as $key=>$cart_quantity) {
            $cart_product = unserialize(base64_decode($key));
            if (!isset($cart_product['cart_kit_info'])) {
                if ($product_id == $cart_product['product_id'] && $quantity <= $cart_quantity) {
                    $new_quantity = $cart_quantity - $quantity;

                    if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                        $this->cart->update($key, $new_quantity);
                    } else {
                        if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                            $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                            $this->cart->update($cart_id, $new_quantity);
                        }else{
                            $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                            $this->cart->update($cart_id, $new_quantity);
                        }
                    }



                    $removed_product = $cart_product;;
                    break;
                }
            }
        }

        return $removed_product;
    }

    
    public function add_to_cart($admin_mode = false) {

        $this->load->language('checkout/cart');
        $this->load->language('module/bundle_expert');

        $this->load->model('module/bundle_expert');
        $this->load->model('catalog/product');

        $this->bundle_expert->convertInputData($this->request->post);

        if(isset($this->request->post['kit_items'])){
            $products = $this->request->post['kit_items'];
        }else{
            $products = array();
        }

        if(isset($this->request->post['kit_items_free'])){
            $products_free = $this->request->post['kit_items_free'];
            foreach ($products_free as $product_free){
                foreach ($product_free as $product){
                    $products[] = $product;
                }
            }
        }

        
        
        if($admin_mode){
            $product_as_kit_main_product_id_for_admin_mode = $this->getProductAsKitMainProductId($this->request->post['kit_id']);
            if($product_as_kit_main_product_id_for_admin_mode){
                $this->request->post['main_product_id'] = $product_as_kit_main_product_id_for_admin_mode;
                $this->request->post['product_as_kit_mode'] = 1;
                $this->request->post['product_as_kit_data'] = array(
                    'quantity' => $this->request->post['order_edit_form_quantity_field'],
                    'product_id' => $product_as_kit_main_product_id_for_admin_mode,
                );
            }
        }

        if(isset($this->request->post['product_as_kit_data'])){
            $product_as_kit_data = $this->request->post['product_as_kit_data'];
        }else{
            $product_as_kit_data = array();
        }

        $kit_id = $this->request->post['kit_id'];

        $main_product_id = $this->request->post['main_product_id'];

        $cart_merge_enable = $this->request->post['cart_merge_enable'];

        $main_product_in_cart = $this->bundle_expert->checkFreeMainProductInCart($main_product_id);

        if (isset($this->request->post['cart_merge_confirm']))
            $cart_merge_confirm = $this->request->post['cart_merge_confirm'];
        else
            $cart_merge_confirm = null;

        
        if (isset($this->request->post['kit_from_cart_unique_id']) && !empty($this->request->post['kit_from_cart_unique_id']))
            $kit_from_cart_unique_id = $this->request->post['kit_from_cart_unique_id'];
        else
            $kit_from_cart_unique_id = null;

        $this->bundle_expert->sortArray($products, 'item_position');

        $this->add_kit_to_cart($kit_id, $main_product_id, $products, $main_product_in_cart, $cart_merge_confirm, $cart_merge_enable, $kit_from_cart_unique_id, $admin_mode, $product_as_kit_data);

    }

    public function add_kit_to_cart_from_category($data){

        $kit_id = $data['kit_id'];
        $main_product_id = $data['main_product_id'];
        $products = $data['products'];
        $main_product_in_cart = 0;
        $cart_merge_confirm = false;
        $cart_merge_enable = 0;
        $kit_from_cart_unique_id = null;
        $admin_mode = false;
        $product_as_kit_data = $data['product_as_kit_data'];


        $result = $this->add_kit_to_cart($kit_id, $main_product_id, $products, $main_product_in_cart, $cart_merge_confirm, $cart_merge_enable, $kit_from_cart_unique_id, $admin_mode, $product_as_kit_data, $data, false);

        return $result;
    }

    private function add_kit_to_cart($kit_id, $main_product_id, $products, $main_product_in_cart, $cart_merge_confirm, $cart_merge_enable, $kit_from_cart_unique_id = null, $admin_mode = false, $product_as_kit_data=array(), $post_data=null, $ajax_mode = true) {

        if(!isset($post_data)){
            $this->post_data = $this->request->post;
        }else{
            $this->post_data = $post_data;
        }

        $bundle_expert_cart = $this->bundle_expert_cart;

        $this->load->language('checkout/cart');
        $this->load->language('module/bundle_expert');

        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');
        $this->load->model('catalog/product');

        $json_kit = array();

        $products_data = array();

        $product_as_kit_unique_id = '';

        
        $add_kit_quantity = 1;
        if(isset($this->post_data['product_as_kit_light_mode'])) {
            $add_kit_quantity = (int)$this->post_data['product_as_kit_light_mode']['quantity'];
        }

        if (!empty($product_as_kit_data)) {
            $product_as_kit = true;

            







        } else {
            $product_as_kit = false;
        }

        
        if(isset($kit_from_cart_unique_id)) {
            $product_as_kit_data = $this->bundle_expert_cart->checkKitHasProductAsKit($kit_from_cart_unique_id);
            if(!empty($product_as_kit_data)){
                $product_as_kit = true;
                $product_as_kit_unique_id = $product_as_kit_data['product_as_kit']['product_as_kit_unique_id'];
                $product_as_kit_unique_id_prev = $product_as_kit_data['product_as_kit']['product_as_kit_unique_id'];
            }
        }

        $get_kit_products_settings = array(
            'main_product_id' => $main_product_id,
            'filter_kit_items' => false,
            'only_first' => false,
            'admin_mode' => $admin_mode,
        );

        $kit_info = $this->model_module_bundle_expert->getKit($kit_id, $kit_from_cart_unique_id, $get_kit_products_settings);

        if(!empty($kit_info)){
            if(isset($kit_from_cart_unique_id)){
                $kit_in_cart = true;
            }else{
                $kit_in_cart = false;
            }

            
            if($kit_in_cart && $product_as_kit && !$kit_info['kit_in_cart_as_product']){
                $update_part_of_product_as_kit = true;
            }else{
                $update_part_of_product_as_kit = false;
            }

            $kit_items_quantity = 0;

            $json = array();

            
            $check_kit = $this->model_checkout_bundle_expert->checkKitActuality($products, $kit_info, $kit_from_cart_unique_id);
            if (!$check_kit) {
                if(!$check_kit) {
                    $json_kit['kit_actuality_error'] = true;
                    $json_kit['kit_actuality_error_text'] = $this->language->get('text_kit_not_actuality_more');
                }
            } else {


                
                $kit_items = $this->formatKitItems($products, $kit_info);
                $kit_enable_status = $this->model_checkout_bundle_expert->getKitEnableStatus($kit_info, $kit_items, $main_product_id, $main_product_in_cart, $kit_in_cart, $kit_from_cart_unique_id, false);
				
				
                if($admin_mode){
                    $kit_enable_status['add_to_cart_kit'] = true;
                }

                if(!$kit_enable_status['add_to_cart_kit']) {
                    $json_kit['kit_enable_status_error'] = $kit_enable_status;
                    $json_kit['kit_enable_status_error_text'] = $kit_enable_status['text'];
                }else{

                    
                    $check_products_quantity_limit = $this->bundle_expert->checkKitProductsQuantityLimit($kit_info, $products, $product_as_kit_data);

                    if (isset($check_products_quantity_limit['text_error'])) {
                        $json['error_text'] = $check_products_quantity_limit['text_error'];
                        $json_kit = $json;

                    } else {
                        
                        $check_item_empty_mode_not_empty_in_cart = $this->bundle_expert->checkKitProductsItemEmptyModeNotEmptyInCart($kit_info, $products, $product_as_kit_data);
                        if(isset($check_item_empty_mode_not_empty_in_cart['text_error'])){
                            $json['error_text'] = $check_item_empty_mode_not_empty_in_cart['text_error'];
                            $json_kit = $json;
                        }else{

                            
                            if($product_as_kit){
                                $check_cart_items_limit = $this->bundle_expert->checkCartItemsLimit($kit_info, count($products), $kit_from_cart_unique_id, $product_as_kit_data['quantity'], $update_part_of_product_as_kit);
                            }else{
                                $check_cart_items_limit = $this->bundle_expert->checkCartItemsLimit($kit_info, count($products), $kit_from_cart_unique_id);
                            }
                            
                            if($check_cart_items_limit['product_as_kit_new_quantity']){
                                $product_as_kit_data['quantity'] = $check_cart_items_limit['product_as_kit_new_quantity'];
                                $check_cart_items_limit['text_error'] = '';
                            }

                            if ($check_cart_items_limit['text_error']) {
                                $json['error_text'] = $check_cart_items_limit['text_error'];
                                $json_kit = $json;
                            }else{
                                
                                if($product_as_kit){
                                    if(!$update_part_of_product_as_kit) {
                                        $product_as_kit_unique_id = uniqid();
                                    }
                                    $product_as_kit_data = $this->getProductAsKitDataForCart($product_as_kit_data, $kit_info, $product_as_kit_unique_id, $json);
                                }else{
                                    $product_as_kit_unique_id = '';
                                }

                                foreach ($products as $index => $product) {

                                    if($product['empty_mode_item_is_empty']=="1" || ($product['is_free_product']=="1" && $product['free_product_in_kit']=="0"))
                                        continue;

                                    if($product['quantity']>0) {
                                        $product_data = $this->getKitItemProductDataForCart($product, $kit_info, $product_as_kit_unique_id, $json, $index);

                                        $products_data[] = $product_data;

                                        $kit_items_quantity += $product_data['quantity'];
                                    }
                                }

                                if ($json) {
                                    $json_kit[] = $json;
                                }

                                
                                if(!$json_kit && $cart_merge_enable && $main_product_in_cart && $cart_merge_confirm=='' && !$kit_in_cart){
                                    $json_kit['cart_merge_question'] = true;
                                }else{
                                    
                                    if (!$json_kit) {

                                        
                                        
                                        if (!empty($cart_merge_confirm) && $cart_merge_confirm==true && $main_product_id) {
                                            
                                            
                                        }

                                        $update_mode = $kit_in_cart;
                                        
                                        if ($kit_in_cart) {
                                            
                                            if($product_as_kit){
                                                if($update_part_of_product_as_kit) {
                                                    $result_data = $bundle_expert_cart->removeKitFromCart($kit_from_cart_unique_id);
                                                }else{
                                                    $result_data = $bundle_expert_cart->removeKitFromCartByProductAsKit($product_as_kit_unique_id_prev);
                                                }
                                            }else{
                                                $result_data = $bundle_expert_cart->removeKitFromCart($kit_from_cart_unique_id);
                                            }

                                            $kit_in_cart = false;

                                            $cart_kit_position = $result_data['cart_position'];
                                            $kit_cart_index = $result_data['cart_kit_index'];
                                            $kit_unique_id = $kit_from_cart_unique_id;
                                        }else{
                                            $cart_kit_position = null;
                                            $kit_cart_index = $this->getLastKitIndexInCart($kit_info['kit_id']);
                                            $kit_unique_id = uniqid();
                                        }

                                        $kit_info['kit_unique_id'] = $kit_unique_id;
                                        $kit_info['kit_items_quantity'] = $kit_items_quantity;
                                        $kit_info['main_product_id'] = $main_product_id;

                                        if (isset($first_option_id))
                                            unset($second_option_id);


                                        if($kit_info['add_to_cart_mode']=='bundle'){
                                            
                                            if ($product_as_kit) {
                                                
                                                if(empty($products_data)){
                                                    $this->cart->add($product_as_kit_data['product_id'], $product_as_kit_data['quantity'], $product_as_kit_data['option'], $product_as_kit_data['recurring_id']);
                                                }else{
                                                    
                                                    if($update_part_of_product_as_kit) {
                                                        $kit_info['kit_unique_id'] = uniqid();
                                                        $this->addKitProductsToCart($products_data, $kit_info, $kit_in_cart, $kit_cart_index, $cart_kit_position);
                                                        $kit_cart_index++;
                                                    }else{










                                                        if ($kit_info['kit_in_cart_as_product']) {

                                                            $kit_info['kit_unique_id'] = uniqid();
                                                            $bundle_expert_cart->addToCartProductAsKit($product_as_kit_data['product_id'], $product_as_kit_data['quantity'], $product_as_kit_data['option'], $product_as_kit_data['recurring_id'], $kit_info, 0, $kit_cart_index, false, false, null, $product_as_kit_data['product_as_kit_unique_id']);


                                                            $kit_info['kit_unique_id'] = uniqid();
                                                            $this->addKitProductsToCart($products_data, $kit_info, $kit_in_cart, $kit_cart_index, $cart_kit_position, $product_as_kit_data['quantity']);
                                                            $kit_cart_index++;

                                                        }else{
                                                            for ($i = 0; $i < $product_as_kit_data['quantity']; $i++) {
                                                                $kit_info['kit_unique_id'] = uniqid();
                                                                $product_as_kit_unique_id =uniqid();
                                                                $bundle_expert_cart->addToCartProductAsKit($product_as_kit_data['product_id'], 1, $product_as_kit_data['option'], $product_as_kit_data['recurring_id'], $kit_info, 0, $kit_cart_index, false, false, null, $product_as_kit_unique_id);

                                                                $kit_info['kit_unique_id'] = uniqid();
                                                                foreach ($products_data as $ind=>$product_data){
                                                                    $products_data[$ind]['has_product_as_kit_unique_id']=$product_as_kit_unique_id;
                                                                }
                                                                $this->addKitProductsToCart($products_data, $kit_info, $kit_in_cart, $kit_cart_index, $cart_kit_position);
                                                                $kit_cart_index++;
                                                            }
                                                        }
                                                    }
                                                }

                                            } else {
                                                
                                                for ($i = 0; $i < $add_kit_quantity; $i++) {
                                                    $this->addKitProductsToCart($products_data, $kit_info, $kit_in_cart, $kit_cart_index, $cart_kit_position);

                                                    $kit_info['kit_unique_id'] = uniqid();
                                                    $kit_cart_index++;
                                                }

                                            }
                                        }else{
                                            foreach ($products_data as $product){
                                                $main_quantity=1;
                                                if(isset($add_kit_quantity)){
                                                    $main_quantity = $add_kit_quantity;
                                                }
                                                $this->cart->add($product['product_id'], $product['quantity']*$main_quantity, $product['option'], $product['recurring_id']);

                                            }

                                        }





                                        $this->cart->updateCartData();

                                        if($update_mode){
                                            $json_kit['success'] = sprintf($this->language->get('be_text_success_update'), $kit_info['title'], $this->url->link('checkout/cart'));
                                            $json_kit['action_code'] = 'update_in_cart';
                                        }else{
                                            $json_kit['success'] = sprintf($this->language->get('be_text_success'), $kit_info['title'], $this->url->link('checkout/cart'));
                                            $json_kit['action_code'] = 'add_to_cart';
                                        }

                                        unset($this->session->data['shipping_method']);
                                        unset($this->session->data['shipping_methods']);
                                        unset($this->session->data['payment_method']);
                                        unset($this->session->data['payment_methods']);

                                        
                                        $total = $this->getTotalByOpencartVersion();
                                        $json_kit['widget_unique_id'] = isset($this->post_data['widget_unique_id'])?$this->post_data['widget_unique_id']:'';
                                        $json_kit['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
                                        $json_kit['total_count'] = $this->cart->countProducts() ;
                                        $json_kit['total_price'] = $this->currency->format($total, $this->session->data['currency']) ;
                                    } else {
                                        $json['error_text'] = sprintf($this->language->get('be_text_error'));

                                        $json_kit = $json;
                                    }
                                }
                            }

                        }




                    }

                }

            }
        }else{
            $json_kit['kit_enable_status_error'] = true;
            $json_kit['kit_enable_status_error_text'] =  $this->language->get('text_kit_not_active_more');
        }

        if($ajax_mode){
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json_kit));
        }else{
            return $json_kit;
        }

    }

    private function addKitProductsToCart($products_data, $kit_info, $kit_in_cart, $kit_cart_index, $cart_kit_position, $product_as_kit_quantity=null ){
        $bundle_expert_cart = $this->bundle_expert_cart;

        foreach ($products_data as $index => $data) {

            if(isset($product_as_kit_quantity)){
                $data['quantity'] = $data['quantity'] * $product_as_kit_quantity;
            }

            if (!$kit_in_cart) {
                $bundle_expert_cart->addToCart($data['product_id'], $data['quantity'], $data['option'], $data['recurring_id'], $kit_info, $data['item_position'], $kit_cart_index, $data['kit_poduct_data']['is_free_product'], false, null, $data['has_product_as_kit_unique_id'], $product_as_kit_quantity);
            } else {
                $item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_info['kit_unique_id'], $data['item_position']);
                $update_in_cart = $item_in_cart;
                $bundle_expert_cart->addToCart($data['product_id'], $data['quantity'], $data['option'], $data['recurring_id'], $kit_info, $data['item_position'], $kit_cart_index, $data['kit_poduct_data']['is_free_product'], $update_in_cart, $cart_kit_position, $data['has_product_as_kit_unique_id'], $product_as_kit_quantity);
                $cart_kit_position++;
            }

        }
    }

    
    private function getProductAsKitDataForCart($product, $kit_info, $product_as_kit_unique_id, &$json){
        $product_data = array();

        $this->post_data['product_id'] = $product['product_id'];
        $this->post_data['quantity'] = $product['quantity'];
        $this->post_data['option'] = isset($product['option']) ? $product['option'] : array();

        if (isset($this->post_data['product_id'])) {
            $product_id = (int)$this->post_data['product_id'];
        } else {
            $product_id = 0;
        }

        $product_info = $this->bundle_expert->getProductInfo($product_id);

        if ($product_info) {

            if (isset($this->post_data['quantity']) && ((int)$this->post_data['quantity'] >= $product_info['minimum'])) {
                $quantity = (int)$this->post_data['quantity'];
            } else {
                $quantity = (int)$this->post_data['quantity'];
            }

            if (isset($this->post_data['option'])) {
                $option = array_filter($this->post_data['option']);
            } else {
                $option = array();
            }

            $product_options = $this->bundle_expert->getProductOptions($this->post_data['product_id']);

            foreach ($product_options as $product_option) {
                if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                    $json['error']['product_as_kit']['option'][][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                    $json['error']['product_as_kit']['item_position'] = '';
                    $json['error']['product_as_kit']['item_position_free'] = '';
                }
            }

            if (isset($this->post_data['recurring_id'])) {
                $recurring_id = $this->post_data['recurring_id'];
            } else {
                $recurring_id = 0;
            }

            $recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);
            $recurrings = array();

            if ($recurrings) {
                $recurring_ids = array();

                foreach ($recurrings as $recurring) {
                    $recurring_ids[] = $recurring['recurring_id'];
                }

                if (!in_array($recurring_id, $recurring_ids)) {
                    $json['error']['recurring'] = $this->language->get('error_recurring_required');
                }
            }

            $product_data = array(
                'product_id' => $this->post_data['product_id'],
                'quantity' => $quantity,
                'option' => $option,
                'recurring_id' => $recurring_id,
                'item_position' => '',
                'product_as_kit_unique_id' => $product_as_kit_unique_id,
                'kit_poduct_data' => ''
            );

        }

        return $product_data;

    }

    
    private function getKitItemProductDataForCart($product, $kit_info, $product_as_kit_unique_id, &$json, $index){
        $product_data = array();

        $this->post_data['product_id'] = $product['product_id'];
        $this->post_data['quantity'] = $product['quantity'];
        $this->post_data['option'] = isset($product['option']) ? $product['option'] : array();

        if (isset($this->post_data['product_id'])) {
            $product_id = (int)$this->post_data['product_id'];
        } else {
            $product_id = 0;
        }

        $product_info = $this->bundle_expert->getProductInfo($product_id);

        if ($product_info) {

            if (isset($this->post_data['quantity']) && ((int)$this->post_data['quantity'] >= $product_info['minimum'])) {
                $quantity = (int)$this->post_data['quantity'];
            } else {
                $quantity = (int)$this->post_data['quantity'];
            }

            if (isset($this->post_data['option'])) {
                $option = array_filter($this->post_data['option']);
            } else {
                $option = array();
            }

            $first_option_id = 'dc65e5e0764e444ec837';
            $second_option_id = '70604031a6e68c8fa6be';

            $product_options = $this->bundle_expert->getProductOptions($this->post_data['product_id']);

            $kit_poduct_data=$this->bundle_expert->findProductDataInKit($product_id,$kit_info['kit_items'],$product['item_position'], $product['is_free_product'], $kit_info['kit_id']);
            $product_options = $this->model_module_bundle_expert->filterProductOptions($kit_poduct_data, $product_options);

            foreach ($product_options as $product_option) {
                if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                    $json['error'][$index]['option'][][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                    $json['error'][$index]['item_position'] = $product['item_position'];
                    $json['error'][$index]['item_position_free'] = $product['item_position_free'];
                }
            }

            if (isset($this->post_data['recurring_id'])) {
                $recurring_id = $this->post_data['recurring_id'];
            } else {
                $recurring_id = 0;
            }

            $recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);
            $recurrings = array();

            if ($recurrings) {
                $recurring_ids = array();

                foreach ($recurrings as $recurring) {
                    $recurring_ids[] = $recurring['recurring_id'];
                }

                if (!in_array($recurring_id, $recurring_ids)) {
                    $json['error']['recurring'] = $this->language->get('error_recurring_required');
                }
            }

            $product_data = array(
                'product_id' => $this->post_data['product_id'],
                'quantity' => $quantity,
                'option' => $option,
                'recurring_id' => $recurring_id,
                'item_position' => $product['item_position'],
                'has_product_as_kit_unique_id' => $product_as_kit_unique_id,
                'kit_poduct_data' => $kit_poduct_data
            );

        }

        return $product_data;

    }

    private function getTotalByOpencartVersion(){
        


        if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
            $this->load->model('extension/extension');
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $sort_order = array();

                $results = $this->model_extension_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                    $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                        $this->load->model('total/' . $result['code']);

                        $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                    }
                }

                $sort_order = array();

                foreach ($total_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }
                array_multisort($sort_order, SORT_ASC, $total_data);
            }
        } else {
            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                $this->load->model('extension/extension');
                $totals = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();

                $total_data = array(
                    'totals' => &$totals,
                    'taxes' => &$taxes,
                    'total' => &$total
                );

                
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array();

                    $results = $this->model_extension_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                        if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('total/' . $result['code']);

                            $this->{'model_total_' . $result['code']}->getTotal($total_data);
                        }
                    }

                    if (isset($first_option_id) && isset($second_option_id))
                        unset($second_option_id);

                    $sort_order = array();

                    foreach ($totals as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $totals);
                }
            }else{
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $this->load->model('extension/extension');

                    $totals = array();
                    $taxes = $this->cart->getTaxes();
                    $total = 0;

                    
                    $total_data = array(
                        'totals' => &$totals,
                        'taxes' => &$taxes,
                        'total' => &$total
                    );

                    
                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $sort_order = array();

                        $results = $this->model_extension_extension->getExtensions('total');

                        foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                        }

                        array_multisort($sort_order, SORT_ASC, $results);

                        foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                $this->load->model('extension/total/' . $result['code']);

                                
                                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                            }
                        }

                        $sort_order = array();

                        foreach ($totals as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $totals);
                    }
                }else{
                    $this->load->model('setting/extension');
                    $totals = array();
                    $taxes = $this->cart->getTaxes();
                    $total = 0;

                    
                    $total_data = array(
                        'totals' => &$totals,
                        'taxes'  => &$taxes,
                        'total'  => &$total
                    );

                    
                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $sort_order = array();

                        $results = $this->model_setting_extension->getExtensions('total');

                        foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
                        }

                        array_multisort($sort_order, SORT_ASC, $results);

                        foreach ($results as $result) {
                            if ($this->config->get('total_' . $result['code'] . '_status')) {
                                $this->load->model('extension/total/' . $result['code']);

                                
                                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                            }
                        }

                        $sort_order = array();

                        foreach ($totals as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $totals);
                    }
                }
            }
        }

        return $total;
    }

    public function get_kit_total() {

        $json = array();

        $this->load->language('checkout/cart');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');
        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');

        $this->bundle_expert->convertInputData($this->request->post);

        if(isset($this->request->post['kit_items'])){
            $products = $this->request->post['kit_items'];
        }else{
            $products = array();
        }

        if (isset($products) && isset($this->request->post['kit_id']) && isset($this->request->post['main_product_id'])) {



            if(isset($this->request->post['kit_items_free'])){
                $products_free = $this->request->post['kit_items_free'];
                foreach ($products_free as $product_free){
                    foreach ($product_free as $product) {
                        $products[] = $product;
                    }
                }
            }

            $kit_id = $this->request->post['kit_id'];

            $main_product_id = $this->request->post['main_product_id'];

            if (isset($this->request->post['product_as_kit_quantity']))
                $product_as_kit_quantity = $this->request->post['product_as_kit_quantity'];
            else
                $product_as_kit_quantity = 0;



            if (isset($this->request->post['kit_from_cart_unique_id']))
                $kit_from_cart_unique_id = $this->request->post['kit_from_cart_unique_id'];
            else
                $kit_from_cart_unique_id = '';

            
            $product_as_kit_options = array();
            if($kit_from_cart_unique_id) {
                $product_as_kit_data = $this->bundle_expert_cart->checkKitHasProductAsKit($kit_from_cart_unique_id);
                if(!empty($product_as_kit_data)){
                    if(isset($product_as_kit_data['option'])){
                        $product_as_kit_options = $product_as_kit_data['option'];
                    }
                }
            }else{
                if(isset($this->request->post['product_as_kit_data'])){
                    if(isset($this->request->post['product_as_kit_data']['option'])){
                        $product_as_kit_options  = $this->request->post['product_as_kit_data']['option'];
                    }
                }
            }

            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => false,
                'only_first' => false,
                'admin_mode' => false,
            );

            $kit_info = $this->model_module_bundle_expert->getKit($kit_id, $kit_from_cart_unique_id, $get_kit_products_settings);


            if (!empty($kit_info)) {
                $total_kit = 0;
                $total_kit_tax = 0;
                $total_default = 0;
                $total_default_new = 0;
                $kit_weight = 0;
                $total_product_count = 0;

                $products_price_data = array();

                foreach ($products as $product) {

                    $item_position = $product['item_position'];
                    $item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_from_cart_unique_id, $item_position);
                    if ($product['empty_mode_item_is_empty'] == 1 && !$item_in_cart)
                        continue;

                    $kit_product_info = $this->bundle_expert->findProductDataInKit($product['product_id'], $kit_info['kit_items'], $product['item_position'], $product['is_free_product'], $kit_info['kit_id']);

                    if (isset($product['option']))
                        $options = $product['option'];
                    else
                        $options = array();


                    $product_info = $kit_product_info['product_info'];

                    $price_data = $this->bundle_expert->getKitProductPriceData($kit_info, $kit_product_info, $product_info, $options, true, $product['quantity']);

                    $price = $price_data['price'];

                    if ($price_data['special']!==false) {
                        $special = $price_data['special'];
                    }else {
                        $special = false;
                    }

                    $total_default += $price * $product['quantity'];

                    if ($special!==false)
                        $total_default_new += $special * $product['quantity'];
                    else
                        $total_default_new += $price * $product['quantity'];

                    if ($special!==false) {
                        $total_kit += $special * $product['quantity'];
                    } else {
                        $total_kit += $price * $product['quantity'];
                    }

                    $total_kit_tax += $price_data['tax'] * $product['quantity'];

                    
                    $price_discount_text = '';
                    if ($special!==false) {
                        switch ($kit_product_info['price_mode']) {
                            case "product_price_minus_sum":
                                $price_minus_special = $price - $special;
                                $price_discount_text = '-' . $this->currency->format($price_minus_special, $this->session->data['currency']);
                                break;
                            case "fix_price":
                                $price_discount_text = $this->language->get('text_fix_price');
                                break;
                            case "product_price":
                            case "product_price_minus_percent":
                                if($price!=0){
                                    $price_discount_percent = (int)round((($price - $special) / ($price / 100)));
                                    
                                    if ($price_discount_percent != 0)
                                        $price_discount_text = '-' . $price_discount_percent . '%';
                                }

                                break;

                        }

                    }

                    $kit_weight += $product_info['weight'] * $product['quantity'];

                    $total_product_count += $product['quantity'];

                    $products_price_data[] = array(
                        'item_position' => $item_position,
                        'item_position_free' => $product['item_position_free'],
                        'product_id' => $product['product_id'],
                        'price' => $this->currency->format($price, $this->session->data['currency']),
                        'price_total' => $this->currency->format($price*$product['quantity'], $this->session->data['currency']),
                        'special' => ($special!==false) ? $this->currency->format($special, $this->session->data['currency']) : false,
                        'special_total' => ($special!==false) ? $this->currency->format($special*$product['quantity'], $this->session->data['currency']) : false,
                        'price_discount_text' => $price_discount_text,
                    );
                }

                
                
                if($product_as_kit_options){
                    $option_price = $this->bundle_expert->calculateOptionsPrice($product_as_kit_options, $main_product_id);
                    $total_default += $option_price;
                    $total_default_new += $option_price;
                    $total_kit += $option_price;
                }




                
                
                $kit_total_data = $this->bundle_expert->calculateKitTotal($kit_info, $total_kit, $total_kit_tax, $total_product_count, $product_as_kit_quantity, true, $main_product_id);


                
                if($kit_info['bundle_total_price_hide_special']){
                    $total_default = $kit_total_data['total_kit'];
                    $total_default_new = $kit_total_data['total_kit'];
                    $total_default_no_tax = $kit_total_data['total_kit'];
                    $kit_total_data['total_kit_old'] = false;
                }
                

                $json['total_default'] = $this->currency->format($total_default, $this->session->data['currency']);
                $json['total_default_value'] = $this->currency->format($total_default, $this->session->data['currency'],'', false);

                $json['total_default_new'] = $this->currency->format($total_default_new, $this->session->data['currency']);
                $json['total_default_new_value'] = $this->currency->format($total_default_new, $this->session->data['currency'],'', false);


                if (isset($this->request->post['product_as_kit_quantity'])) {
                    $product_as_kit_quantity = (float)$this->request->post['product_as_kit_quantity'];
                    $kit_weight = $kit_weight * $product_as_kit_quantity;
                }

                $json['kit_weight'] = $this->weight->format($kit_weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
                $json['kit_weight_value'] = $kit_weight;




                $total_kit = $kit_total_data['total_kit'];

                if ($kit_total_data['total_kit_old'])
                    $json['total_kit_old'] = $this->currency->format($kit_total_data['total_kit_old'], $this->session->data['currency']);
                else
                    $json['total_kit_old'] = false;

                $json['total_kit'] = $this->currency->format($total_kit, $this->session->data['currency']);
                $json['total_kit_value'] = $this->currency->format($total_kit, $this->session->data['currency'],'', false);


                $json['total_kit_tax'] = $this->currency->format($total_kit_tax, $this->session->data['currency']);
                $json['total_kit_tax_value'] = $this->currency->format($total_kit_tax, $this->session->data['currency'],'', false);


                
                $json['profit_value'] =abs($json['total_kit_value'] - $json['total_default_value']);
                $profit_value = $this->currency->convert($json['profit_value'], $this->session->data['currency'], $this->config->get('config_currency'));
                $json['profit_price'] = $this->currency->format($profit_value, $this->session->data['currency']);;
                $percent = $json['total_default_value']/100;
                if($percent==0){
                    $json['profit_percent'] = 0;
                }else{
                    $json['profit_percent'] = round($json['profit_value']/$percent);
                }
                if($json['total_kit_value'] - $json['total_default_value']>=0){
                    $json['profit_prefix'] = '+';
                }else{
                    $json['profit_prefix'] = '-';
                }

                
                if (isset($this->request->post['product_as_kit_quantity'])) {
                    $product_as_kit_quantity = (float)$this->request->post['product_as_kit_quantity'];




                    $json['product_as_kit_total_default'] = $this->currency->format($total_default * $product_as_kit_quantity, $this->session->data['currency']);

                    $json['product_as_kit_total'] = $this->currency->format($total_kit * $product_as_kit_quantity, $this->session->data['currency']);

                    $json['product_as_kit_total_tax'] = $this->currency->format($total_kit_tax * $product_as_kit_quantity, $this->session->data['currency']);
                }

                $json['products_price_data'] = $products_price_data;


                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
            }

        }


    }

    private function getLastKitIndexInCart($kit_id){
        $kits_in_cart = array();

        $last_index = 0;

        $cart_products_data = $this->bundle_expert_cart->getCartProductsData();

        foreach ($cart_products_data as $key => $quantity) {
            $product = unserialize(base64_decode($key));

            if (isset($product['cart_kit_info'])) {
                if ($product['cart_kit_info']['kit_id'] == $kit_id) {
                    if ($product['cart_kit_info']['kit_cart_index'] > $last_index) {
                        $last_index = $product['cart_kit_info']['kit_cart_index'];
                    }

                }
            }
        }

        $last_index++;

        return $last_index;
    }

    private function formatKitItems($products, $kit_info){
        $kit_items = array();

        foreach ($products as $product) {
            $item_position = $product['item_position'];
            $product_options = $this->bundle_expert->getProductOptions($product['product_id']);

            
            foreach ($product_options as $index => $product_option) {
                $product_option_id = $product_option['product_option_id'];
                if (is_array($product_option['product_option_value'])) {
                    foreach ($product_option['product_option_value'] as $index2 => $product_option_value) {
                        if (!isset($product['option'][$product_option_id])) {
                            unset($product_options[$index]['product_option_value'][$index2]);
                        } else {
                            if (is_array($product['option'][$product_option_id]) && !in_array($product_option_value['product_option_value_id'], $product['option'][$product_option_id])) {
                                unset($product_options[$index]['product_option_value'][$index2]);
                            }
                            if (empty($product['option'][$product_option_id]))
                                unset($product_options[$index]['product_option_value'][$index2]);
                        }
                    }
                } else {
                    if (!isset($product['option'][$product_option_id]) || empty($product['option'][$product_option_id])) {
                        unset($product_options[$index]);
                    }

                }
            }

            $product_info['options'] = $product_options;

            $kit_product_info=$this->bundle_expert->findProductDataInKit($product['product_id'],$kit_info['kit_items'],$item_position, $product['is_free_product'], $kit_info['kit_id']);
            $kit_product_info['product_info']['options']=$product_options;
            $kit_items[] = array(
                'products' => array($kit_product_info),
                'selectable' => false
            );
        }

        return $kit_items;

    }


    private function getProductAsKitMainProductId($kit_id){

        $product_as_kit_id = '';

        $kit_info = $this->bundle_expert->getKit($kit_id);

        if ($kit_info) {

            if ($kit_info['kit_as_product']) {
                $kit_link_products = $this->bundle_expert->getKitLinkProducts($kit_id);
                
                foreach ($kit_link_products as $kit_link_product) {
                    if ($kit_link_product['item_type'] == "product") {
                        $product_as_kit_id = $kit_link_product['item_id'];
                        break;
                    }
                }
            }

        }

        return $product_as_kit_id;
    }

}
