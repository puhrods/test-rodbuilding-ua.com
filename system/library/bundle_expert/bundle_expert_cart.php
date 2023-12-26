<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class BundleExpertCart extends Controller {
    private $session_id = null;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    private function initSessionId(){
        if(!isset($this->session_id)) {
            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                $this->session_id = -1;
            } else {
                $this->session_id = $this->session->getId();
            }
        }
    }

    public function setSessionIdCustom($sessionId){
        $this->session_id = $sessionId;
    }

    
    public function addToCartProductAsKit($product_id, $quantity = 1, $option = array(), $recurring_id = 0, $kit_info=array(), $item_position=-1, $kit_cart_index=-1, $is_free_product=-1, $update_in_cart = false, $cart_kit_position=null, $product_as_kit_unique_id=-1) {
        $this->initSessionId();

        $this->data = array();

        $product['product_id'] = (int)$product_id;

        if ($option) {
            foreach ($option as $index=>$value){
                if(is_array($value)){
                    $option[$index] = array_values($value);
                }
            }
            $product['option'] = $option;
        }

        if ($recurring_id) {
            $product['recurring_id'] = (int)$recurring_id;
        }

        $cart_kit_info = array(
            'product_as_kit' => true,
            'product_as_kit_unique_id' => $product_as_kit_unique_id,
            'kit_id' => $kit_info['kit_id'],
            'kit_unique_id' => $product_as_kit_unique_id,
            'main_product_id' => $kit_info['main_product_id'],
            'kit_cart_index' => $kit_cart_index,
            'item_position' => $item_position,
            'is_free_product'=>$is_free_product
        );

        $product['product_as_kit'] = $cart_kit_info;

        $key = base64_encode(serialize($product));

        if(!$update_in_cart) {
            
            $this->addToCartByOpencartVersion($product_id, $quantity, $option, $recurring_id, $key);
        }
    }


    public function addToCart($product_id, $quantity = 1, $option = array(), $recurring_id = 0, $kit_info=array(), $item_position=-1, $kit_cart_index=-1, $is_free_product=-1, $update_in_cart = false, $cart_kit_position=null, $has_product_as_kit_unique_id=-1, $has_product_as_kit_quantity=null) {
        $this->initSessionId();

        $this->data = array();

        $product['product_id'] = (int)$product_id;

        if ($option) {
            foreach ($option as $index=>$value){
                if(is_array($value)){
                    $option[$index] = array_values($value);
                }
            }
            $product['option'] = $option;
        }

        if ($recurring_id) {
            $product['recurring_id'] = (int)$recurring_id;
        }

        $cart_kit_info = array(
            'product_as_kit' => false,
            'kit_id' => $kit_info['kit_id'],
            'kit_unique_id' => $kit_info['kit_unique_id'],
            'has_product_as_kit_unique_id' => $has_product_as_kit_unique_id,
            'has_product_as_kit_quantity' => $has_product_as_kit_quantity,
            'main_product_id' => $kit_info['main_product_id'],
            'item_position' => $item_position,
            'kit_cart_index' => $kit_cart_index,
            'is_free_product'=>$is_free_product
        );

        $product['cart_kit_info'] = $cart_kit_info;

        $key = base64_encode(serialize($product));

        if(!$update_in_cart) {
            
            $this->addToCartByOpencartVersion($product_id, $quantity, $option, $recurring_id, $key);
        }
    }

    public function addToCartByOpencartVersion($product_id, $quantity, $option, $recurring_id, $key){
        $this->initSessionId();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            if ((int)$quantity && ((int)$quantity > 0)) {
                if (!isset($this->session->data['cart'][$key])) {
                    $this->session->data['cart'][$key] = (int)$quantity;
                } else {
                    $this->session->data['cart'][$key] += (int)$quantity;
                }
            }
        } else {
            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                $option['cart_key'] = $key;
                $this->db->query("INSERT " . DB_PREFIX . "cart SET customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session_id) . "', product_id = '" . (int)$product_id . "', recurring_id = '" . (int)$recurring_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', date_added = NOW()");
            }else{
                $option['cart_key'] = $key;
                $this->db->query("INSERT " . DB_PREFIX . "cart SET api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "', customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session_id) . "', product_id = '" . (int)$product_id . "', recurring_id = '" . (int)$recurring_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', date_added = NOW()");
            }
        }
    }

    
    public function removeKitFromCartByProductAsKit($product_as_kit_unique_id){
        $this->initSessionId();

        $kits_unique_id = $this->getProductAsKitKitsUniqueId($product_as_kit_unique_id);

        $result = $this->removeProductAsKitFromCart($product_as_kit_unique_id);

        foreach ($kits_unique_id as $kit_unique_id) {
            $this->removeKitFromCart($kit_unique_id);
        }

        return $result;
    }

    
    public function removeProductAsKitItemsFromCart($product_as_kit_items, $old_data){
        $this->initSessionId();

        foreach ($product_as_kit_items as $index2 => $kit_cart_product) {

            $new_data_product = $old_data[$kit_cart_product['key']];

            $this->removeFromCartByVersion($new_data_product);
        }
    }


    
    
    public function removeProductAsKitFromCart($product_as_kit_unique_id){
        $this->initSessionId();

        $first_cart_position = -1;
        $kit_cart_index = 0;


        $cart_products = $this->getCartProductsDataAdvanced();

        $cart_position = 0;
        foreach ($cart_products as $cart_product) {
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if (isset($cart_product_info['product_as_kit'])) {
                if ($cart_product_info['product_as_kit']['product_as_kit_unique_id'] == $product_as_kit_unique_id){
                    if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                        $key = $cart_product['key'];
                        unset($this->session->data['cart'][$key]);
                    } else if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
                        $cart_id = $cart_product['cart_id'];
                        $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
                    } else {
                        $cart_id = $cart_product['cart_id'];
                        $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
                    }

                    if (($first_cart_position < 0)) {
                        $first_cart_position = $cart_position;
                        $kit_cart_index = $cart_product_info['product_as_kit']['kit_cart_index'];
                    }
                }
            }
            $cart_position++;
        }

        $result_data = array(
            'cart_position' => $first_cart_position,
            'cart_kit_index' => $kit_cart_index,
        );

        return $result_data;
    }

    public function removeKitFromCart($kit_unique_id){
        $this->initSessionId();

        $first_cart_position = -1;
        $kit_cart_index = 0;

        
        $cart_products = $this->getCartProductsDataAdvanced();

        $cart_position = 0;
        foreach ($cart_products as $cart_product) {
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if (isset($cart_product_info['cart_kit_info'])) {
                
                if ($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id){
                    if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                        $key = $cart_product['key'];
                        unset($this->session->data['cart'][$key]);
                    } else if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
                        $cart_id = $cart_product['cart_id'];
                        $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
                    } else {
                        $cart_id = $cart_product['cart_id'];
                        $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
                    }

                    if (($first_cart_position < 0)) {
                        $first_cart_position = $cart_position;
                        $kit_cart_index = $cart_product_info['cart_kit_info']['kit_cart_index'];
                    }
                }
            }
            $cart_position++;
        }



        $result_data = array(
            'cart_position' => $first_cart_position,
            'cart_kit_index' => $kit_cart_index,
        );

        return $result_data;
    }

    private function clearKitInCart($kit_in_cart, &$old_data){
        $this->initSessionId();

        $kit_unique_id = $kit_in_cart['kit_unique_id'];

        $cart_products = $this->getCartProductsDataAdvanced();

        $has_product_as_kit_unique_id = '';

        foreach ($cart_products as $key=>$cart_product) {
            $cart_product_info = unserialize(base64_decode($key));

            if (isset($cart_product_info['cart_kit_info'])) {
                
                if ($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id){

                    $data_product = $old_data[$key];

                    $this->removeFromCartByVersion($data_product);

                    if(isset($cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'])){
                        $has_product_as_kit_unique_id = $cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'];
                    }

                    unset($old_data[$key]);

                }
            }
        }

        
        if(!empty($has_product_as_kit_unique_id)){
            $product_as_kit_data = $this->getProductAsKitByUniqueId($has_product_as_kit_unique_id);
            if($product_as_kit_data){
                $old_quantity = $product_as_kit_data['quantity'];
                $new_quantity = $old_quantity - 1;
                $this->updateProductAsKitQuantityInCart($product_as_kit_data, $new_quantity);
            }

        }

    }


    public function prepareCartData($old_data, &$new_data_all) {
        $this->initSessionId();

        
        $old_data = $this->convertByOpencartVersion($old_data);

        $new_data = array();

        $kits_in_cart = $this->getCartKits($old_data);

        $kits_in_cart = $this->sortKitsByCartIndex($kits_in_cart);

        
        
        foreach ($kits_in_cart as $kit_index => $kit_in_cart) {

            
            
            $get_kit_products_settings = array(
                'main_product_id' => $kit_in_cart['main_product_id'],
                'filter_kit_items' => false,
                'only_first' => false,
                'admin_mode' => false,
            );

            $kit_info = $this->bundle_expert->getKit($kit_in_cart['kit_id'], $kit_in_cart['kit_unique_id'], $get_kit_products_settings);
            if (!empty($kit_info)) {
                $kits_in_cart[$kit_index]['kit_info'] = $kit_info;

                $check_kit = true;

                
                
                if(!$this->bundle_expert->admin_api_mode) {
                    $check_kit = $this->bundle_expert->checkKitPeriodActuality($kit_info['kit_id']);
                }

                
                if($check_kit!=false) {
                    $check_kit = $this->bundle_expert->checkKitActuality($this->formatKitItems($kit_in_cart['kit_items']), $kit_info, $kit_in_cart['kit_unique_id']);

                    
                    if ($check_kit != false) {
                        $result_quantity_limit = $this->bundle_expert->checkKitProductsQuantityLimit($kit_info, $kit_in_cart['kit_items'], array());
                        if (isset($result_quantity_limit['text_error'])) {
                            $check_kit = false;
                        }
                    }
                }

            } else {
                $check_kit = false;
            }

            
            if (!$check_kit) {
                if(!empty($kit_info) && $kit_info['disbanded_bundle_clear']==true){
                    $this->clearKitInCart($kit_in_cart, $old_data);
                }else{
                    $this->disbandKitInCart($kit_in_cart, $old_data);
                }
                unset($kits_in_cart[$kit_index]);
            } else {

            }
        }

        $edit_by_click = true;

        
        $products_as_kit_list = array();
        $products_as_kit_items = array();
        $products_as_kit_list_help = array();

        foreach ($old_data as $cart_product) {
            $key = $cart_product['key'];

            $cart_product_data = unserialize(base64_decode($key));

            
            
            if (isset($cart_product_data['product_as_kit'])) {
                if(!$this->bundle_expert->bundle_expert_settings['product_as_kit_use_reward']){
                    $cart_product['reward'] = 0;
                }
            }
            

            if (isset($cart_product_data['product_as_kit'])) {
                $product_as_kit_unique_id = $cart_product_data['product_as_kit']['product_as_kit_unique_id'];
                $products_as_kit_list[$product_as_kit_unique_id] = array(
                    'product_id' => $cart_product['product_id'],
                    'key' => $key,
                    'product_as_kit_info' => $cart_product_data,
                    'cart_product_info' => $cart_product,
                    'quantity' => $cart_product['quantity']
                );
                $products_as_kit_items[$product_as_kit_unique_id] = array();
                unset($old_data[$key]);

                
                $products_as_kit_list_help[$product_as_kit_unique_id] = $cart_product;

            }
        }


        
        foreach ($kits_in_cart as $index1 => $kit_in_cart) {

            $kit_info = $kit_in_cart['kit_info'];

            $kit_in_cart['kit_items'] = $this->sortKitItemsByPosition($kit_in_cart['kit_items']);

            $item_position_free = -1;

            $kit_total_weight = 0;

            foreach ($kit_in_cart['kit_items'] as $index2 => $kit_cart_product) {

                $cart_kit_info = $kit_cart_product['cart_kit_info'];

                $new_data_product = $old_data[$kit_cart_product['key']];

                $product_info = $this->bundle_expert->getProductInfoDefault($new_data_product['product_id']);

                
                if (isset($kit_cart_product['cart_kit_info']['has_product_as_kit_unique_id'])
                    && !empty($kit_cart_product['cart_kit_info']['has_product_as_kit_unique_id'])) {
                    $has_product_as_kit_unique_id = $kit_cart_product['cart_kit_info']['has_product_as_kit_unique_id'];
                    if (!isset($products_as_kit_items[$has_product_as_kit_unique_id][$index1]))
                        $products_as_kit_items[$has_product_as_kit_unique_id][$index1] = array();
                } else {
                    $has_product_as_kit_unique_id = '';
                }

                
                
                if(!empty($has_product_as_kit_unique_id) && !isset($products_as_kit_list[$has_product_as_kit_unique_id])){



                    $this->removeProductAsKitItemsFromCart($kit_in_cart['kit_items'], $old_data);
                    unset($kits_in_cart[$index1]);
                    break;
                }

                if ($cart_kit_info['is_free_product']) {
                    $item_position_free++;
                }

                
                if(!empty($has_product_as_kit_unique_id) && $kit_info['kit_in_cart_as_product']){
                    $kit_text_html = '';
                }else{
                    $kit_text_html = $this->createCartKitTextHtml($kit_info, $cart_kit_info, $index2, $edit_by_click);
                }

                $new_data_product['name'] = htmlspecialchars(htmlspecialchars_decode($new_data_product['name']));

                $new_data_product['name'] .= $kit_text_html;

                
                $kit_product_info = $this->bundle_expert->findProductDataInKit($kit_cart_product['product_id'], $kit_in_cart['kit_info']['kit_items'], $kit_cart_product['cart_kit_info']['item_position'], $kit_cart_product['cart_kit_info']['is_free_product'], $kit_info['kit_id']);

                $new_data_product_for_all = $new_data_product;

                
                if($has_product_as_kit_unique_id && isset($products_as_kit_list[$has_product_as_kit_unique_id])){
                    $new_data_product['quantity'] = $new_data_product['quantity'] / $products_as_kit_list[$has_product_as_kit_unique_id]['quantity'];
                    $new_data_product['weight'] = $new_data_product['weight'] / $products_as_kit_list[$has_product_as_kit_unique_id]['quantity'];
                }else{
                    $new_data_product['quantity'] = $kit_cart_product['quantity'];
                }

                $kit_total_weight += $new_data_product['weight'];

                

                $options_price = 0;
                if (isset($kit_cart_product['option'])) {
                    $options = $kit_cart_product['option'];

                    $options_price = $this->bundle_expert->calculateOptionsPrice($options, $new_data_product['product_id']);

                    $first_option_id = 'dc65e5e0764e444ec837';
                }

                
                $new_data_product['price'] = $product_info['price'] + $options_price;
                $new_data_product_for_all['price'] = $product_info['price'] + $options_price;

                $discount_set = false;
                if ($kit_info['enable_discount']) {
                    $discount_price = $this->bundle_expert->getProductDiscount($product_info['product_id'], $kit_cart_product['quantity']);
                    if(!empty($discount_price)){
                        $new_data_product['price'] = $discount_price + $options_price;
                        $new_data_product_for_all['price'] = $discount_price + $options_price;
                        $discount_set = true;
                    }
                }

                
                
                
                if($this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le']) {
                    if ($kit_info['kit_as_product_light_mode'] && $kit_product_info['main']) {
                        $kit_info['enable_special'] = false;
                    }
                }

                if (!$discount_set) {
                    if (!$kit_info['enable_special']) {


                    } else {
                        if ($product_info['special']) {
                            if ($kit_info['product_discount_in_total'] && $kit_info['show_default_specials_in_kit_discounts']) {
                                $new_data_product['price'] = $product_info['price'] + $options_price;
                                $new_data_product_for_all['price'] = $product_info['price'] + $options_price;
                            } else {
                                $new_data_product['price'] = $product_info['special'] + $options_price;
                                $new_data_product_for_all['price'] = $product_info['special'] + $options_price;
                            }








                        } else {
                            $new_data_product['price'] = $product_info['price'] + $options_price;
                            $new_data_product_for_all['price'] = $product_info['price'] + $options_price;
                            $second_option_id = '70604031a6e68c8fa6be';
                        }
                    }
                }

                $ocmod_point_001 = 1;

                $customer_group_discount_enable = $this->bundle_expert->checkKitDiscountEnableByCustomerGroup();



                
                $product_discount_in_total = $kit_info['product_discount_in_total'];
                if (!$product_discount_in_total) {

                    
                    $customer_group_id = $this->customer->getGroupId();
                    if(!isset($customer_group_id)){
                        $customer_group_id = $customer_group_id = $this->config->get('config_customer_group_id');
                    }
                    if($kit_product_info['price_mode_to_customer_groups_status'] && isset($kit_product_info['price_mode_to_customer_groups'][$customer_group_id])){
                        $new_price_mode = $kit_product_info['price_mode_to_customer_groups'][$customer_group_id];
                        $kit_product_info['price_mode'] = $new_price_mode['price_mode'];
                        switch ($kit_product_info['price_mode']){
                            case 'product_price':
                                $kit_product_info['price'] = $new_price_mode['value'];
                                break;
                            case 'product_price_minus_sum':
                                $kit_product_info['price_minus_sum'] = $new_price_mode['value'];
                                break;
                            case 'product_price_minus_percent':
                                $kit_product_info['price_minus_percent'] = $new_price_mode['value'];
                                break;
                            case 'fix_price':
                                $kit_product_info['price'] = $new_price_mode['value'];
                                break;
                        }
                    }
                    

                    switch ($kit_product_info['price_mode']) {
                        case 'product_price':
                            break;
                        case 'product_price_minus_sum':
                            if(!$customer_group_discount_enable){
                                break;
                            }
                            $new_data_product['price'] = $new_data_product['price'] - $kit_product_info['price_minus_sum'];
                            $new_data_product_for_all['price'] = $new_data_product['price'];
                            break;
                        case 'product_price_minus_percent':
                            if(!$customer_group_discount_enable){
                                break;
                            }
                            $new_data_product['price'] = $new_data_product['price'] - ($new_data_product['price'] * $kit_product_info['price_minus_percent'] / 100);
                            $new_data_product_for_all['price'] = $new_data_product['price'];
                            break;
                        case 'fix_price':
                            if(!$customer_group_discount_enable){
                                break;
                            }
                            $new_data_product['price'] = $kit_product_info['price'];
                            $new_data_product_for_all['price'] = $kit_product_info['price'];
                            break;
                    }
                }

                $new_data_product['total'] = $new_data_product['price'] * $new_data_product['quantity'];
                $new_data_product_for_all['total'] = $new_data_product_for_all['price'] * $new_data_product_for_all['quantity'];

                
                
                if ($kit_info['kit_in_cart_as_product']) {
                    
                    if ($kit_info['kit_as_product']) {
                        if (!empty($has_product_as_kit_unique_id)) {
                            $products_as_kit_items[$has_product_as_kit_unique_id][$index1][$index2] = $new_data_product;
                        } else {
                            $new_data[$new_data_product['key']] = $new_data_product;
                        }
                    } else {
                        
                        if (!isset($kits_in_cart_as_product)) {
                            $kits_in_cart_as_product = array();
                        }

                        $kits_in_cart_as_product[$kit_in_cart['kit_unique_id']][] = $new_data_product;
                        if ($index2 == (count($kit_in_cart['kit_items']) - 1)) {
                            $new_data_product = $this->createKitAsProduct($kits_in_cart_as_product[$kit_in_cart['kit_unique_id']], $kit_info, $cart_kit_info);
                            $new_data[$new_data_product['key']] = $new_data_product;
                        }
                    }
                } else {
                    
                    
                    $new_data[$new_data_product['key']] = $new_data_product;
                }

                
                if (!empty($has_product_as_kit_unique_id) && isset($products_as_kit_list_help[$has_product_as_kit_unique_id])) {
                    $new_data_all[$products_as_kit_list_help[$has_product_as_kit_unique_id]['key']] = $products_as_kit_list_help[$has_product_as_kit_unique_id];
                    unset($products_as_kit_list_help[$has_product_as_kit_unique_id]);
                }

                $new_data_all[$new_data_product['key']] = $new_data_product_for_all;

                unset($old_data[$kit_cart_product['key']]);

                if (isset($first_option_id) && isset($second_option_id))
                    unset($second_option_id);
            }

            
            if ($kit_info['kit_as_product'] && $kit_info['kit_in_cart_as_product'] && isset($kits_in_cart[$index1])) {
                if ($kit_info['kit_in_cart_as_product']) {
                    $next_kit = isset($kits_in_cart[$index1 + 1]) ? $kits_in_cart[$index1 + 1] : null;
                    if (isset($next_kit['kit_items'][0]['cart_kit_info'])) {
                        $next_product_as_kit_unique_id = $this->getKitItemProductAsKitUniqueId($next_kit['kit_items'][0]['cart_kit_info']);
                    }
                    if (!empty($has_product_as_kit_unique_id))
                        $product_as_kit_unique_id = $has_product_as_kit_unique_id;
                    if (!empty($product_as_kit_unique_id)) {
                        if (!isset($next_kit) || $product_as_kit_unique_id != $next_product_as_kit_unique_id) {
                            $product_as_kit_new_item = $this->createProductAsKit($products_as_kit_list, $products_as_kit_items, $product_as_kit_unique_id, $kit_info);
                            $new_data[$product_as_kit_new_item['key']] = $product_as_kit_new_item;
                        }
                    }
                }
            }

        }

        
        foreach ($products_as_kit_list as $index=>$product_as_kit){
            if(empty($products_as_kit_items[$index])){
                $this->removeEmptyProductAsKit($product_as_kit);
            }
        }


        foreach ($old_data as $index => $value) {
            if ($value['quantity'] > 0) {
                $new_data[$value['key']] = $value;
                $new_data_all[$value['key']] = $value;
            }
        }


        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=')) {
            $new_data_2 = array();
            foreach ($new_data as $index => $cart_product) {
                $new_data_2[] = $cart_product;
            }
            $new_data = $new_data_2;
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=')) {
            $new_data_2 = array();
            foreach ($new_data_all as $index => $cart_product) {
                $new_data_2[] = $cart_product;
            }
            $new_data_all = $new_data_2;
        }

        return $new_data;

    }

    
    private function disbandKitInCart($kit_in_cart, &$old_data){
        $this->initSessionId();

        foreach ($kit_in_cart['kit_items'] as $kit_product) {
            $old_key = $kit_product['key'];

            try
            {
                $new_cart_product = unserialize(base64_decode($old_key));
            }
            catch(Exception $e)
            {
                return;
            }


            unset($new_cart_product['cart_kit_info']);

            $new_key = base64_encode(serialize($new_cart_product));

            if (isset($old_data[$new_key])) {
                $product_in_cart = true;
            } else {
                $product_in_cart = false;
            }

            if ($product_in_cart) {
                $kit_product['quantity'] += $old_data[$old_key]['quantity'];
                $old_data[$new_key]['quantity'] = $kit_product['quantity'];
            } else {
                $old_data[$new_key] = $old_data[$old_key];
                $old_data[$new_key]['key'] = $new_key;
            }

            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                if ($product_in_cart) {
                    $this->session->data['cart'][$new_key] += $old_data[$old_key]['quantity'];
                } else {
                    $this->session->data['cart'][$new_key] = $old_data[$old_key]['quantity'];
                }
            } else {
                if (!$product_in_cart) {
                    $add_product = array();
                    $add_product['product_id'] = (int)$kit_product['product_id'];
                    $add_product['quantity'] = (int)$kit_product['quantity'];
                    if (isset($kit_product['option'])) {
                        $add_product['option'] = $kit_product['option'];
                    } else {
                        $add_product['option'] = array();
                    }
                    if (isset($kit_product['recurring_id'])) {
                        $add_product['recurring_id'] = (int)$kit_product['recurring_id'];
                    } else {
                        $add_product['recurring_id'] = 0;
                    }

                    $this->cart->add($add_product['product_id'], $add_product['quantity'], $add_product['option'], $add_product['recurring_id']);

                } else {
                    $this->cart->update($old_data[$new_key]['cart_id'], $kit_product['quantity']);
                }

                $last_cart_id = $this->getCartLastId();
                $old_data[$new_key]['cart_id'] = $last_cart_id;
            }

            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                unset($this->session->data['cart'][$old_key]);
            }  else  if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$old_data[$old_key]['cart_id']  . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
            }else {
                $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$old_data[$old_key]['cart_id'] . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
            }

            
            if (isset($kit_product['cart_kit_info']['has_product_as_kit_unique_id'])
                && !empty($kit_product['cart_kit_info']['has_product_as_kit_unique_id'])) {
                $has_product_as_kit_unique_id = $kit_product['cart_kit_info']['has_product_as_kit_unique_id'];
                $products_as_kit_data = $this->getProductAsKitByUniqueId($has_product_as_kit_unique_id);
                if($products_as_kit_data){
                    $new_quantity = $products_as_kit_data['quantity'] - 1;
                    $this->updateProductAsKitQuantityInCart($products_as_kit_data, $new_quantity);
                }

            }

            unset($old_data[$old_key]);

        }
    }

    private function createCartKitTextHtml($kit_info, $cart_kit_info, $position, $edit_by_click, $show_kit_name = true){
        $this->initSessionId();

        $kit_text_html = '';
        $kit_count_index = '';

        if($cart_kit_info['kit_cart_index']>1)
            $kit_count_index =  '-'.$cart_kit_info['kit_cart_index'];

        if($edit_by_click){

            $kit_text_html = '<br><span class=\'eckb\'  uid=\''.$cart_kit_info['kit_unique_id'].'\' pos=\''.$position.'\'>';
            if($show_kit_name)
                $kit_text_html .= '(' . $kit_info['cart_title'] . $kit_count_index . ')';
            if($position==0)
                $kit_text_html .= '<b></b>';
            $kit_text_html .='</span>';
        }else{
            $kit_text_html = ' (' . $kit_info['cart_title'] .$kit_count_index. ')';
        }


        return $kit_text_html;
    }

    public function getKitItemProductAsKitUniqueId($cart_kit_info){
        $this->initSessionId();

        $product_as_kit_unique_id = '';
        if(isset($cart_kit_info['product_as_kit'])){
            if($cart_kit_info['product_as_kit']){
                $product_as_kit_unique_id = $cart_kit_info['product_as_kit_unique_id'];
            }else{
                $product_as_kit_unique_id = $cart_kit_info['has_product_as_kit_unique_id'];
            }
        }

        return $product_as_kit_unique_id;
    }

    
    private function createProductAsKit($products_as_kit_list, $products_as_kit_items, $product_as_kit_unique_id, $kit_info){
        $this->initSessionId();

        $total_price = 0;
        $total = 0;
        $total_weight = 0;
        $total_product_count = 0;

        $cart_product_info = $products_as_kit_list[$product_as_kit_unique_id]['cart_product_info'];
        $product_as_kit_info = $products_as_kit_list[$product_as_kit_unique_id]['product_as_kit_info'];

        $cart_product_info['name'].=$this->createCartKitTextHtml($kit_info, $product_as_kit_info['product_as_kit'],0,1,false);

        foreach ($products_as_kit_items[$product_as_kit_unique_id] as $index0 => $product_as_kit_items) {
            foreach ($product_as_kit_items as $kit_item) {
                $price = $kit_item['price'];









                $total_price += $price * $kit_item['quantity'];
                $total += $kit_item['total'];

                

                $item_weight = $kit_item['weight'];
                $item_weight = $this->weight->convert($item_weight, $kit_item['weight_class_id'], $this->config->get('config_weight_class_id'));
                $total_weight += $item_weight;

                $total_product_count += $kit_item['quantity'];
            }
            break;
        }

        
        $total_weight = $this->weight->convert($total_weight, $this->config->get('config_weight_class_id'), $cart_product_info['weight_class_id']);

        
        
        if(isset($products_as_kit_list[$product_as_kit_unique_id]['product_as_kit_info']['option'])) {
            $product_as_kit_options = $products_as_kit_list[$product_as_kit_unique_id]['product_as_kit_info']['option'];
            $option_price = $this->bundle_expert->calculateOptionsPrice($product_as_kit_options, $products_as_kit_list[$product_as_kit_unique_id]['product_id']);
            $total_price += $option_price;
        }


        
        $new_options = array();

        if($this->bundle_expert->bundle_expert_settings['cart_show_products_in_product_as_kit']){
            foreach ($products_as_kit_items[$product_as_kit_unique_id] as $index0 => $product_as_kit_items) {
                foreach ($product_as_kit_items as $index1=>$kit_item) {
                    $product_as_kit_option_stock = true;
                    $name = '' . $kit_item['name'];
                    $value = $kit_item['quantity'] . ' x ';
                    $price = $kit_item['price'];
                    if ($this->config->get('config_tax')) {
                        $price = $this->tax->calculate($price, $kit_item['tax_class_id'], $this->config->get('config_tax'));
                    }
                    
                    $price_tax = $this->tax->calculate($kit_item['price'], $kit_item['tax_class_id'], true);

                    $value .= $this->currency->format($price, $this->session->data['currency']);

                    if($index1==(count($product_as_kit_items)-1)){
                        
                    }

                    $new_option_data = array(
                        'product_option_id' => '',
                        'product_option_value_id' => '',
                        'option_id' => '',
                        'option_value_id' => '',
                        'name' => $name,
                        'value' => $value,
                        'type' => 'checkbox',
                        'quantity' => $kit_item['quantity'],
                        'subtract' => $kit_item['subtract'],
                        'price' => $kit_item['price'],
                        'price_tax' => $price_tax,
                        'price_prefix' => '',
                        'points' => $kit_item['points'],
                        'points_prefix' => '',
                        'weight' => $kit_item['weight'],
                        'product_as_kit_product_id' => $kit_item['product_id'],
                        'product_as_kit_product_quantity' => $kit_item['quantity'],
                        'model'  => '',
                        'weight_prefix' => ''
                    );
                    $new_options[] = $new_option_data;

                    if($this->bundle_expert->bundle_expert_settings['cart_show_option_in_product_as_kit']){
                        foreach ($kit_item['option'] as $option){
                            $new_option_data = array(
                                'product_option_id' => '',
                                'product_option_value_id' => '',
                                'option_id' => '',
                                'option_value_id' => '',
                                'name' => $option['name'],
                                'value' => $option['value'],
                                'type' => 'checkbox',
                                'quantity' =>'',
                                'subtract' => $option['subtract'],
                                'price' => '',
                                'price_prefix' => '',
                                'points' =>'' ,
                                'points_prefix' => '',
                                'weight' => 0,
                                'kit_as_product_sub_option' => true,
                                'model'  => '',


                                'weight_prefix' => ''
                            );
                            $new_options[] = $new_option_data;
                        }
                    }

                }
                break;

            }
        }




        $new_options = array_merge($new_options, $cart_product_info['option']);
        $cart_product_info['option'] = $new_options;

        
        $cart_kit_info = $product_as_kit_info['product_as_kit'];

        $get_kit_products_settings = array(
            'main_product_id' => $cart_kit_info['main_product_id'],
            'filter_kit_items' => false,
            'only_first' => false,
            'admin_mode' => false,
        );

        $kit_info = $this->bundle_expert->getKit($cart_kit_info['kit_id'], $cart_kit_info['kit_unique_id'], $get_kit_products_settings);

        $kit_total = $total_price;
        $total_default = $total_price;

        $kit_total_mode = $kit_info['kit_price_mode'];

        $discount_kit = 0;

        $customer_group_discount_enable = $this->bundle_expert->checkKitDiscountEnableByCustomerGroup();

        
        $customer_group_id = $this->customer->getGroupId('config_customer_group_id');
        if(!isset($customer_group_id)){
            $customer_group_id = $customer_group_id = $this->config->get('config_customer_group_id');
        }
        if($kit_info['kit_price_mode_to_customer_groups_status'] && isset($kit_info['kit_price_mode_to_customer_groups'][$customer_group_id])){
            $group_price_mode = $kit_info['kit_price_mode_to_customer_groups'][$customer_group_id];
            $kit_total_mode['mode'] = $group_price_mode['kit_price_mode'];
            $kit_total_mode[$group_price_mode['kit_price_mode']] = $group_price_mode['value'];

        }
        

        switch ($kit_total_mode['mode']) {
            case 'sum':
                $discount_kit = $total_default - $kit_total;
                break;
            case 'sum_minus_percent':
                if (!$this->bundle_expert->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }

                $discount_kit = $total_default - ($kit_total - ($kit_total / 100 * $kit_total_mode['sum_minus_percent']));
                break;
            case 'sum_minus_value':
                if (!$this->bundle_expert->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }
                $discount_kit =  $total_default - ($kit_total - $kit_total_mode['sum_minus_value']);
                break;
            case 'sum_fix':
                if (!$this->bundle_expert->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }
                
                $use_default_discount = false;
                if ($kit_info['kit_as_product'] && $kit_info['kit_as_product_main_product_use_default_discount']) {
                    $default_discount_price = $this->bundle_expert->getProductDiscountByOpencartVersion($cart_product_info['product_id'], $cart_product_info['quantity']);
                    if ($default_discount_price !== false) {
                        $use_default_discount = true;
                    }
                }

                if($use_default_discount){
                    $discount_kit =  0;
                    $total_price = $default_discount_price;
                }else{
                    $discount_kit =  $total_default - $kit_total_mode['sum_fix'];
                }

                break;
        }

        $total_price -= $discount_kit;
        $total = $total_price;


        $cart_product_info['product_as_kit_flag'] = 1;

        $cart_product_info['price'] = $total_price;
        $cart_product_info['total'] = $cart_product_info['quantity'] * $total;

        
        
        $cart_product_info['weight'] = $cart_product_info['weight']+ $total_weight*$cart_product_info['quantity'] ;


        return $cart_product_info;
    }

    private function createKitAsProduct($kit_in_cart_as_product, $kit_info, $cart_kit_info){
        $this->initSessionId();

        $product = array();
        $total_price = 0;
        $total = 0;

        
        
        
        foreach ($kit_in_cart_as_product as $index=>$kit_product){
            if ($index == 0) {
                $product = $kit_product;

                $product['name'] = $kit_info['cart_title'];
                $kit_text_html = $this->createCartKitTextHtml($kit_info, $cart_kit_info, $index, true, false);
                $product['name'] .=$kit_text_html;

            }

            $total_price += $kit_product['price'];
            $total += $kit_product['total'];

        }

        $product['price'] = $total_price;
        $product['total'] = $total;

        return $product;
    }

    private function sortKitsByCartIndex($kits_in_cart){
        $this->initSessionId();

        usort($kits_in_cart, function ($item1, $item2) {

            
            if (version_compare(PHP_VERSION, '7.0.0', '<')) {
                include(DIR_SYSTEM . 'library/bundle_expert/php_versions/usort1_php5.php');

            }else{
                include(DIR_SYSTEM . 'library/bundle_expert/php_versions/usort1_php7-8.php');

            }

        });

        return $kits_in_cart;
    }

    private function sortKitItemsByPosition($kit_items_cart){
        $this->initSessionId();

        $kit_items = array();
        $kit_items_free = array();

        foreach ($kit_items_cart as $kit_item){
            if(!$kit_item['cart_kit_info']['is_free_product']){
                $kit_items[] = $kit_item;
            }else{
                $kit_items_free[] = $kit_item;
            }
        }

        usort($kit_items, function ($item1, $item2) {

            
            if (version_compare(PHP_VERSION, '7.0.0', '<')) {
                include(DIR_SYSTEM . 'library/bundle_expert/php_versions/usort2_php5.php');

            }else{
                include(DIR_SYSTEM . 'library/bundle_expert/php_versions/usort2_php7-8.php');

            }


        });

        foreach ($kit_items_free as $kit_item_free){
            $kit_items[] = $kit_item_free;
        }

        return $kit_items;
    }

    
    public function prepareCartHasStockData($cart_products){
        $this->initSessionId();




        
        $cart_products = $this->convertByOpencartVersion($cart_products);

        $kits_in_cart = $this->getCartKits($cart_products);

        $kits_in_cart_count=array();
        $products_quantity=array();
        $options_quantity=array();

        
        foreach ($kits_in_cart as $index1 => $kit_in_cart) {

            $kit_key = base64_encode($kit_in_cart['kit_id'] . $kit_in_cart['main_product_id']);
            if (!isset($kits_in_cart_count[$kit_key]))
                $kits_in_cart_count[$kit_key] = 0;
            $kits_in_cart_count[$kit_key]++;

            $kit_info = $this->bundle_expert->getKit($kit_in_cart['kit_id'], $kit_in_cart['kit_unique_id']);

            
            $kit_stock = true;
            if ($kit_info['kit_quantity_mode']['limit']) {
                if ($kits_in_cart_count[$kit_key] > $kit_info['kit_quantity_mode']['value']) {
                    $kit_stock = false;
                }
            }
            if ($kit_info['kit_cart_limit_mode']['limit']) {
                if ($kits_in_cart_count[$kit_key] > $kit_info['kit_cart_limit_mode']['value']) {
                    $kit_stock = false;
                }
            }

            if (!$kit_stock) {
                foreach ($cart_products as $index2 => $cart_product) {
                    $cart_product_data = unserialize(base64_decode($cart_product['key']));

                    if (isset($cart_product_data['cart_kit_info'])) {
                        if ($cart_product_data['cart_kit_info']['kit_unique_id'] == $kit_in_cart['kit_unique_id'])
                            $cart_products[$index2]['stock'] = $kit_stock;
                    }
                }
            }

        }

        
        



            if (!$this->config->get('config_stock_checkout')) {
                foreach ($cart_products as $index2 => $cart_product) {
                    $stock = true;

                    $product_id = $cart_product['product_id'];

                    $product_info = $this->bundle_expert->getProductInfoDefault($product_id);

                    if (!isset($products_quantity[$product_id]))
                        $products_quantity[$product_id] = 0;

                    $products_quantity[$product_id] += $cart_product['quantity'];

                    if ($products_quantity[$product_id] > $product_info['quantity']) {
                        $stock = false;
                    }

                    foreach ($cart_product['option'] as $option_index => $option) {

                        if (!isset($option['product_as_kit_product_id'])) {
                            $product_option_value_id = $option['product_option_value_id'];

                            if ($product_option_value_id) {
                                if (!isset($options_quantity[$product_option_value_id]))
                                    $options_quantity[$product_option_value_id] = 0;

                                $options_quantity[$product_option_value_id] += $cart_product['quantity'];
                            }

                            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') {
                                if ($product_option_value_id) {
                                    if ($option['subtract'] && (!$option['quantity'] || ($option['quantity'] < $options_quantity[$product_option_value_id]))) {
                                        $stock = false;
                                    }
                                }
                            } elseif ($option['type'] == 'checkbox') {
                                if ($option['product_option_value_id']) {
                                    if ($option['subtract'] && (!$option['quantity'] || ($option['quantity'] < $options_quantity[$product_option_value_id]))) {
                                        $stock = false;
                                    }
                                }
                            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {

                            }
                        } else {
                            $product_id = $option['product_as_kit_product_id'];

                            $product_info = $this->bundle_expert->getProductInfoDefault($product_id);

                            if (!isset($products_quantity[$product_id]))
                                $products_quantity[$product_id] = 0;

                            $products_quantity[$product_id] += $cart_product['quantity'] * $option['product_as_kit_product_quantity'];

                            if ($products_quantity[$product_id] > $product_info['quantity']) {
                                if($this->bundle_expert->bundle_expert_settings['stock_out_bundles_show']){
                                    if (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')) {
                                        $cart_products[$index2]['option'][$option_index]['name'] = '<span class="be-text-danger">***</span> ' . $cart_products[$index2]['option'][$option_index]['name'];
                                        $cart_products[$index2]['option'][$option_index]['product_as_kit_option_stock'] = false;
                                    }
                                }else {
                                    if (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')) {
                                        $cart_products[$index2]['option'][$option_index]['name'] = '<span class="be-text-danger">***</span> ' . $cart_products[$index2]['option'][$option_index]['name'];
                                        $cart_products[$index2]['option'][$option_index]['product_as_kit_option_stock'] = false;
                                    }
                                }
                            }
                        }

                    }

                    if (!$stock) {
                        $cart_products[$index2]['stock'] = $stock;
                    }

                }
            }


        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=')) {
            $new_data_2 = array();
            foreach ($cart_products as $index => $cart_product) {

                $new_data_2[] = $cart_product;
            }
            $cart_products = $new_data_2;
        }

        return $cart_products;
    }

    private function getCartKits($cart_products){
        $this->initSessionId();

        $kits_in_cart = array();

        
        
        $cart_index_count = -1;
        foreach ($cart_products as $key => $cart_product_data) {

            $cart_product = unserialize(base64_decode($key));

            if(isset($cart_product['cart_kit_info']) ){
                $kit_id = $cart_product['cart_kit_info']['kit_id'];
                $kit_unique_id = $cart_product['cart_kit_info']['kit_unique_id'];
                $main_product_id = $cart_product['cart_kit_info']['main_product_id'];
                $item_position = $cart_product['cart_kit_info']['item_position'];

                $cart_product['key'] = $key;
                $cart_product['quantity'] = $cart_product_data['quantity'];

                if(!isset($kits_in_cart[$kit_unique_id])){
                    $cart_index_count++;
                }

                $kits_in_cart[$kit_unique_id]['kit_id'] = $kit_id;
                $kits_in_cart[$kit_unique_id]['cart_index'] = $cart_product['cart_kit_info']['kit_cart_index'];
                $kits_in_cart[$kit_unique_id]['kit_unique_id'] = $kit_unique_id;
                $kits_in_cart[$kit_unique_id]['main_product_id'] = $main_product_id;
                $kits_in_cart[$kit_unique_id]['kit_items'][] = $cart_product;
            }

        }

        return $kits_in_cart;
    }

    private function formatKitItems($kit_items){
        $this->initSessionId();

        $kit_items_new = array();

        foreach ($kit_items as $kit_item_product){
            $kit_items_new[] = array(
                'product_id' => $kit_item_product['product_id'],
                'quantity' => $kit_item_product['quantity'],
                'item_position' => $kit_item_product['cart_kit_info']['item_position'],
                'is_free_product' => $kit_item_product['cart_kit_info']['is_free_product'],
                'option' => isset($kit_item_product['cart_kit_info']['option'])?$kit_item_product['cart_kit_info']['option']:array(),
            );
        }
        return $kit_items_new;

    }

    private function convertByOpencartVersion($old_data){
        $this->initSessionId();

        $converted = array();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=')) {
            foreach ($old_data as $index => $cart_product) {
                $key = $this->getCartKitKey($cart_product['cart_id']);
                if (empty($key)) {
                    $key = '';

                    if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=') && version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_product['cart_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
                    } else {
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_product['cart_id'] . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "' ORDER BY cart_id");
                    }

                    if(!empty($query->row)){
                        $product_id = $query->row['product_id'];
                        $option = json_decode($query->row['option'], true);
                        $recurring_id = $query->row['recurring_id'];

                        $product['product_id'] = (int)$product_id;
                        if ($option) {
                            $product['option'] = $option;
                        }
                        if ($recurring_id) {
                            $product['recurring_id'] = (int)$recurring_id;
                        }

                        $key = base64_encode(serialize($product));
                    }

                }

                $converted[$key] = $cart_product;
                $converted[$key]['key'] = $key;
            }

        }else{
            $converted = $old_data;
        }

        return $converted;
    }

    public function getCartKitKey($cart_id){
        $this->initSessionId();


        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "'  ORDER BY cart_id");

        $key = '';

        if(!empty($query->row)){
            $option = json_decode($query->row['option'], true);
            if(isset($option['cart_key'])){
                $key = $option['cart_key'];
            }
        }

        return $key;
    }

    public function getCartIdByKey($key){
        $this->initSessionId();

        $cart_id='';



        $bundle_expert_product_data = $this->cart->bundle_expert_all_product_data;

        foreach ($bundle_expert_product_data as $product){
            if($product['key']==$key){
                $cart_id = $product['cart_id'];
                break;
            }
        }

        return $cart_id;
    }

    public function isProductAsKit($cart_product){
        $this->initSessionId();

        $is = false;
        if(isset($cart_product['product_as_kit']) && $cart_product['product_as_kit']==true) {
                $is = true;
        }

        return $is;
    }

    private function getKitItemsInCart($kit_unique_id){
        $this->initSessionId();

        $kit_items = array();

        
        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if (isset($cart_product_info['cart_kit_info'])) {
                if($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id){
                    $item_position = $cart_product_info['cart_kit_info']['item_position'];
                    $kit_items[$item_position] = $cart_product;

                }
            }
        }

        return $kit_items;
    }

    public function getKitItemInCart($kit_unique_id, $item_position){
        $this->initSessionId();

        $kit_in_cart = $this->getKitItemsInCart($kit_unique_id);

        $kit_item_in_cart = array();

        if(isset($kit_in_cart[$item_position]))
            $kit_item_in_cart = $kit_in_cart[$item_position];

        return $kit_item_in_cart;
    }

    public function checkKitItemInCart($kit_unique_id, $item_position){
        $this->initSessionId();

        $kit_in_cart = $this->getKitItemsInCart($kit_unique_id);
        $finded = false;

        if(isset($kit_in_cart[$item_position]))
            $finded = true;

        return $finded;
    }

    
    private function getProductAsKitProductsInCart($product_as_kit_unique_id){
        $this->initSessionId();

        $kit_items = array();

        
        $cart_products = $this->getCartProductsDataAdvanced();

        $kit_unique_id = '';

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if (isset($cart_product_info['cart_kit_info'])) {
                if(isset($cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'])
                    && $cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'] == $product_as_kit_unique_id){

                    if(empty($kit_unique_id) || $cart_product_info['cart_kit_info']['kit_unique_id']==$kit_unique_id) {
                        $kit_unique_id = $cart_product_info['cart_kit_info']['kit_unique_id'];
                        $kit_items[] = $cart_product;
                    }

                }
            }
        }

        return $kit_items;
    }


    public function getKitCartPosition($kit_unique_id){
        $this->initSessionId();

        $first_cart_position = -1;
        $kit_cart_index = 0;

        $cart_products = $this->getCartProductsData();

        $cart_position = 0;
        foreach ($cart_products as $key=>$quantity) {
            $cart_product_info = unserialize(base64_decode($key));
            if (isset($cart_product_info['cart_kit_info'])) {
                if ($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id) {

                    if(($first_cart_position<0)){
                        $first_cart_position = $cart_position;
                        $kit_cart_index = $cart_product_info['cart_kit_info']['kit_cart_index'];
                    }
                }
            }
            $cart_position++;
        }

        $result_data = array(
            'cart_position' =>$first_cart_position,
            'cart_kit_index' =>$kit_cart_index,
        );

        return $result_data;
    }

    
    private function getKitItemIndexInCartSession($kit_unique_id, $item_position){
        $this->initSessionId();

        $cart_key = '';

        foreach ($this->session->data['cart'] as $key => $quantity) {
            $product = unserialize(base64_decode($key));
            if (isset($product['cart_kit_info'])) {
                if ($product['cart_kit_info']['kit_unique_id'] == $kit_unique_id && $product['cart_kit_info']['item_position']==$item_position) {
                    $cart_key = $key;
                    break;
                }
            }
        }

        return $cart_key;
    }
    
    private function replaceKitItemInCartSession($replace_key, $new_key, $new_quantity){
        $this->initSessionId();

        $new_cart = array();

        foreach ($this->session->data['cart'] as $key => $quantity) {
            if ($key!=$replace_key) {
                $new_cart[$key] = $quantity;
            }else{
                $new_cart[$new_key] = $new_quantity;
            }
        }

        $this->session->data['cart'] = $new_cart;

    }



    public function getCartQueryFromDb($api_id = null, $customer_id = null){
        $this->initSessionId();

        if ($this->registry->has('customer')) {
            if (!isset($customer_id)) {
                $customer_id = $this->customer->getId();
            }
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '>=') && version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$customer_id . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
        } else {
            if(!isset($api_id))
                $api_id = (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0);

            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . $this->db->escape($api_id) . "' AND customer_id = '" . (int)$customer_id . "' AND session_id = '" . $this->db->escape($this->session_id) . "' ORDER BY cart_id");
        }

        return $cart_query;
    }

    public function sortCartQueryFromDb(&$data){

        $this->bundle_expert->sortArray($data, 'cart_id');

    }

    private function getCartLastId(){
        $this->initSessionId();

        $query = $this->db->query("SELECT max(cart_id) as last_cart_id FROM " . DB_PREFIX . "cart");

        return $query->row['last_cart_id'];
    }


    
    
    
    public function getCartProductsData(){
        $this->initSessionId();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $cart_products_data = $this->session->data['cart'];
        }else{
            $cart_products_data = array();

            $cart_query = $this->getCartQueryFromDb();

            foreach ($cart_query->rows as $row){
                $product_id = $row['product_id'];
                $option = json_decode($row['option'], true);
                $recurring_id = $row['recurring_id'];

                if (isset($option['cart_key'])) {
                    $key = $option['cart_key'];
                } else {
                    $product['product_id'] = (int)$product_id;
                    if ($option) {
                        $product['option'] = $option;
                    }
                    if ($recurring_id) {
                        $product['recurring_id'] = (int)$recurring_id;
                    }

                    $key = base64_encode(serialize($product));
                }



                $cart_products_data[$key] = $row['quantity'];
            }

        }

        return $cart_products_data;
    }

    public function getCartProductsDataAdvanced(){
        $this->initSessionId();

        $cart_products_data = array();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $cart_products = $this->session->data['cart'];
            foreach ($cart_products as $key => $quantity) {
                $cart_product_data = unserialize(base64_decode($key));

                $cart_products_data[$key] = array(
                    'cart_id' => '',
                    'product_id' => $cart_product_data['product_id'],
                    'option' => $cart_product_data['option'],
                    'quantity' => $cart_product_data['quantity'],
                    'key' => $key
                );
            }
        }else {
            $cart_query = $this->getCartQueryFromDb();

            foreach ($cart_query->rows as $row) {
                $product_id = $row['product_id'];
                $option = json_decode($row['option'], true);
                $recurring_id = $row['recurring_id'];
                $quantity = $row['quantity'];

                if (isset($option['cart_key'])) {
                    $key = $option['cart_key'];
                } else {
                    $product['product_id'] = (int)$product_id;
                    if ($option) {
                        $product['option'] = $option;
                    }
                    if ($recurring_id) {
                        $product['recurring_id'] = (int)$recurring_id;
                    }

                    $key = base64_encode(serialize($product));
                }


                $cart_products_data[$key] = array(
                    'cart_id' => $row['cart_id'],
                    'product_id' => $product_id,
                    'option' => $option,
                    'quantity' => $quantity,
                    'key' => $key
                );

            }

        }

        return $cart_products_data;
    }

    
    public function checkUpdateBeforeCartUpdate($key, $quantity){
        $this->initSessionId();

        $continue = true;

        $cart_product_data = unserialize(base64_decode($key));
        if (isset($cart_product_data['product_as_kit'])) {
            $this->bundle_expert_cart->updateProductAsKit($cart_product_data['product_as_kit']['product_as_kit_unique_id'], $quantity);
            $continue = false;
        } else {
            
            if (isset($cart_product_data['cart_kit_info'])) {
                $cart_kit_info = $cart_product_data['cart_kit_info'];
                $kit_items = $this->bundle_expert->getKitItems($cart_kit_info['kit_id']);
                $item_position = $cart_kit_info['item_position'];
                $product = null;

                foreach ($kit_items[$item_position] as $item_product) {
                    if ($item_product['product_id'] == $cart_product_data['product_id']) {

                        $this->bundle_expert->itemInfoCheck($item_product);
                        $kit_item_info = $item_product['item_info'];
                        break;
                    }
                }
                if(isset($kit_item_info)){
                    if($kit_item_info['quantity_edit']==0){
                        $continue = false;
                    }
                }
            }
        }

        return $continue;
    }

    
    public function checkRemoveBeforeCartRemove($key){
        $this->initSessionId();

        $continue = true;

        
        $cart_product_data = unserialize(base64_decode($key));
        if (isset($cart_product_data['product_as_kit'])) {
            $product_as_kit_unique_id = $cart_product_data['product_as_kit']['product_as_kit_unique_id'];
            $this->removeKitFromCartByProductAsKit($product_as_kit_unique_id);

            $this->cart->updateCartData();
            $continue = false;
        }

        return $continue;
    }

    
    
    
    public function apiAddBeforeClear($products){
        $this->initSessionId();

        $new_list = $products;

        foreach ($products as $index=>$product) {
            $product_as_kit = array();


            if (isset($product['key'])) {
                $key = unserialize(base64_decode($product['key']));
                if (isset($key['product_as_kit'])) {
                    $product_as_kit = $key['product_as_kit'];
                    $product_as_kit_items = $this->getProductAsKitProductsInCart($product_as_kit['product_as_kit_unique_id']);

                    $product_as_kit_data = $this->getProductAsKitByUniqueId($product_as_kit['product_as_kit_unique_id']);
                    if(!empty($product_as_kit_data)) {
                        $product_as_kit_old_quantity = $product_as_kit_data['quantity'];
                        $product_as_kit_new_quantity = $product['quantity'];
                        
                        if ($product_as_kit_old_quantity != $product_as_kit_new_quantity) {
                            $new_product_as_kit_unique_id = uniqid();
                            $key['product_as_kit']['kit_unique_id'] = $new_product_as_kit_unique_id;
                            $key['product_as_kit']['product_as_kit_unique_id'] = $new_product_as_kit_unique_id;
                            $new_key = base64_encode(serialize($key));
                            $new_list[$index]['key'] = $new_key;
                        }
                    }
                }
            }


            
            if (isset($product['product_as_kit']) && !empty($product['product_as_kit'])) {
                $product_as_kit = unserialize(base64_decode($product['product_as_kit']));
                $product_as_kit_items = $this->bundle_expert->getOrderProductAsKitItems($product['order_id'], $product_as_kit['product_as_kit_unique_id'], $product['quantity']);

                $product_as_kit_old_quantity = $product['quantity'];
                $product_as_kit_new_quantity = $product['quantity'];
            }

            if (!empty($product_as_kit)) {
                foreach ($product_as_kit_items as $product_as_kit_item) {
                    if ($product_as_kit_old_quantity != $product_as_kit_new_quantity) {
                        $product_new_quantity = $product_as_kit_item['quantity'] / $product_as_kit_old_quantity * $product_as_kit_new_quantity;
                        $product_as_kit_item['quantity'] = $product_new_quantity;

                        $key2 = unserialize(base64_decode($product_as_kit_item['key']));
                        if(isset($key2['cart_kit_info'])){
                            $key2['cart_kit_info']['has_product_as_kit_quantity'] = $product_as_kit_new_quantity;
                            $key2['cart_kit_info']['has_product_as_kit_unique_id'] = $new_product_as_kit_unique_id;
                            $new_key2 = base64_encode(serialize($key2));
                            $product_as_kit_item['key'] = $new_key2;
                        }

                    }
                    $new_list[] = $product_as_kit_item;
                }
            }

        }

        return $new_list;
    }

    
    public function apiAddBeforeDefault($product, $option){
        $this->initSessionId();

        $continue = $this->apiAddToCart($product, $option);

        if (isset($product['key'])) {
            $cart_product = unserialize(base64_decode($product['key']));
            if (isset($cart_product['product_as_kit'])) {
                $product_as_kit = $cart_product['product_as_kit'];
            }
        }
        if(isset($product['product_as_kit'])){
            $product_as_kit = unserialize(base64_decode($product['product_as_kit']));
        }

        if (!empty($product_as_kit)) {
            $this->addToCartProductAsKit($product['product_id'], $product['quantity'], $option, 0, $product_as_kit, 0, $product_as_kit['kit_cart_index'], $product_as_kit['is_free_product'], false, null, $product_as_kit['product_as_kit_unique_id']);

            $continue = false;
        }

        return $continue;
    }

    private function apiAddToCart($product, $option){
        $this->initSessionId();

        $continue = true;
        $cart_kit_info = array();
        if (!empty($product['cart_kit_info'])) {
            $cart_kit_info = unserialize(base64_decode($product['cart_kit_info']));
        }
        if (isset($product['key'])) {
            $cart_product = unserialize(base64_decode($product['key']));
            if (isset($cart_product['cart_kit_info'])) {
                $cart_kit_info = $cart_product['cart_kit_info'];
            }



        }
        if (!empty($cart_kit_info)) {
            if(isset($cart_kit_info['has_product_as_kit_unique_id'])){
                $has_product_as_kit_unique_id = $cart_kit_info['has_product_as_kit_unique_id'];
            }else{
                $has_product_as_kit_unique_id = '';
            }
            if(isset($cart_kit_info['has_product_as_kit_quantity'])){
                $has_product_as_kit_quantity = $cart_kit_info['has_product_as_kit_quantity'];
            }else{
                $has_product_as_kit_quantity = null;
            }
            $this->addToCart($product['product_id'], $product['quantity'], $option, 0, $cart_kit_info, $cart_kit_info['item_position'], $cart_kit_info['kit_cart_index'], $cart_kit_info['is_free_product'], false, null, $has_product_as_kit_unique_id, $has_product_as_kit_quantity);

            $continue = false;
        }

        return $continue;
    }



    public function checkProductAsKitInCart($kit_from_cart_unique_id){
        $this->initSessionId();

        $product_as_kit_data = array();

        
        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['product_as_kit'])) {
                if ($cart_product_info['product_as_kit']['kit_unique_id'] == $kit_from_cart_unique_id) {
                    $product_as_kit_data = $cart_product_info;
                    $product_as_kit_data['quantity'] = $cart_product['quantity'];
                    break;
                }
            }
        }

        return $product_as_kit_data;
    }

    public function getProductAsKitFirstItemKitUniqueId($product_as_kit_unique_id){
        $this->initSessionId();

        $kit_unique_id = '';

        
        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['cart_kit_info'])) {
                $cart_kit_info = $cart_product_info['cart_kit_info'];
                if (isset($cart_kit_info['has_product_as_kit_unique_id'])
                    && $cart_kit_info['has_product_as_kit_unique_id'] == $product_as_kit_unique_id) {
                    $kit_unique_id = $cart_kit_info['kit_unique_id'];
                    break;
                }
            }
        }

        return $kit_unique_id;
    }

    public function getProductAsKitKitsUniqueId($product_as_kit_unique_id){
        $this->initSessionId();

        $kits_unique_id = array();

        
        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['cart_kit_info'])) {
                $cart_kit_info = $cart_product_info['cart_kit_info'];
                if (isset($cart_kit_info['has_product_as_kit_unique_id'])
                    && $cart_kit_info['has_product_as_kit_unique_id'] == $product_as_kit_unique_id) {
                    $kit_unique_id = $cart_kit_info['kit_unique_id'];
                    $kits_unique_id[$kit_unique_id] = $kit_unique_id;
                }
            }
        }

        return $kits_unique_id;
    }

    public function checkKitHasProductAsKit($kit_unique_id){
        $this->initSessionId();

        $product_as_kit_data = array();

        
        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['cart_kit_info'])) {
                if ($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id) {
                    if(isset($cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'])){
                        $product_as_kit_data = $this->getProductAsKitByUniqueId($cart_product_info['cart_kit_info']['has_product_as_kit_unique_id']);
                        break;

                    }
                }
            }
        }

        return $product_as_kit_data;
    }

    
    public function getProductAsKitByUniqueId($product_as_kit_unique_id){
        $this->initSessionId();

        $product_as_kit_data = array();


        $cart_products = $this->getCartProductsDataAdvanced();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['product_as_kit'])) {
                if ($cart_product_info['product_as_kit']['product_as_kit_unique_id'] == $product_as_kit_unique_id) {
                    $product_as_kit_data = $cart_product_info;
                    $product_as_kit_data['quantity'] = $cart_product['quantity'];
                    $product_as_kit_data['key'] = $cart_product['key'];
                    break;
                }
            }
        }

        return $product_as_kit_data;
    }

    public function getProductAsKitInCartTotal($kit_unique_id){
        $this->initSessionId();

        $total=0;

        
        $cart_products = $this->cart->getProducts();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['product_as_kit'])) {
                if ($cart_product_info['product_as_kit']['product_as_kit_unique_id'] == $kit_unique_id) {
                    $total = $cart_product['price'];
                    break;
                }
            }








































        }


        return $total;

    }

    public function getProductAsKitInCartTax($kit_unique_id){
        $this->initSessionId();

        $total=0;

        $cart_products = $this->cart->getProducts();

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if(isset($cart_product_info['product_as_kit'])) {
                if ($cart_product_info['product_as_kit']['product_as_kit_unique_id'] == $kit_unique_id) {
                    $total = $this->tax->getTax($cart_product['price'], $cart_product['tax_class_id']);
                    break;
                }
            }
            
            
            


















        }


        return $total;

    }

    
    public function removeEmptyProductAsKit($product_as_kit_data){
        $this->initSessionId();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $key = $product_as_kit_data['key'];
            unset($this->session->data['cart'][$key]);
        } else if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
            $cart_id = $product_as_kit_data['cart_product_info']['cart_id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
        } else {
            $cart_id = $product_as_kit_data['cart_product_info']['cart_id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
        }

    }

    
    public function updateProductAsKitQuantityInCart($product_as_kit_data, $new_quantity){
        $this->initSessionId();


        $quantity = $new_quantity;

        $key = $product_as_kit_data['key'];

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $this->cart->update($key, $quantity);
            if ((int)$quantity && ((int)$quantity > 0) && isset($this->session->data['cart'][$key])) {
                $this->session->data['cart'][$key] = (int)$quantity;
            } else {
                unset($this->session->data['cart'][$key]);
            }
        } else {
            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
                $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
            }else{
                $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
            }
        }

    }

    
    public function updateProductAsKitItemQuantityInCart($kit_item_data, $new_quantity){
        $this->initSessionId();


        $quantity = $new_quantity;

        $key = $kit_item_data['key'];

        unset($kit_item_data['key']);
        $new_key = base64_encode(serialize($kit_item_data));

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $this->cart->update($key, $quantity);
            if ((int)$quantity && ((int)$quantity > 0) && isset($this->session->data['cart'][$key])) {

                $this->session->data['cart'][$new_key] = (int)$quantity;
            } else {
                unset($this->session->data['cart'][$key]);
            }
        } else {



            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
                $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");

                if (isset($kit_item_data['option'])) {
                    $option = $kit_item_data['option'];
                } else {
                    $option = array();
                }
                $option['cart_key'] = $new_key;
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET `option` = '" . $this->db->escape(json_encode($option)) . "' WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");

            }else{
                $cart_id = $this->bundle_expert_cart->getCartIdByKey($key);
                $this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");

                if (isset($kit_item_data['option'])) {
                    $option = $kit_item_data['option'];
                } else {
                    $option = array();
                }
                $option['cart_key'] = $new_key;

                $this->db->query("UPDATE " . DB_PREFIX . "cart SET `option` = '" . $this->db->escape(json_encode($option)) . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");

            }
        }

    }

    
    public function updateProductAsKit($product_as_kit_unique_id, $new_quantity){
        $this->initSessionId();

        $product_as_kit = $this->getProductAsKitByUniqueId($product_as_kit_unique_id);

        if (!empty($product_as_kit)) {

            $kit_info = $this->bundle_expert->getKit($product_as_kit['product_as_kit']['kit_id']);

            

            $kits_unique_id = $this->getProductAsKitKitsUniqueId($product_as_kit['product_as_kit']['kit_unique_id']);
            $kit_unique_id = reset($kits_unique_id);
            $kit_in_cart = $this->bundle_expert->getCartKit($kit_unique_id);
            if($kit_in_cart){
                $kit_items_in_cart = $kit_in_cart['kit_items'];
                $check_cart_items_limit = $this->bundle_expert->checkCartItemsLimit($kit_info, count($kit_items_in_cart), $kit_unique_id, $new_quantity, false);
                if($check_cart_items_limit['product_as_kit_new_quantity']){
                    $new_quantity = $check_cart_items_limit['product_as_kit_new_quantity'];
                    $check_cart_items_limit['text_error'] = '';
                }

                if ($check_cart_items_limit['text_error']) {

                }else{
                    $quantity_diff = $new_quantity - $product_as_kit['quantity'];

                    
                    if($quantity_diff!=0) {
                        $this->updateProductAsKitQuantityInCart($product_as_kit, $new_quantity);

                        $kits_in_cart = $this->getCartKits($this->getCartProductsDataAdvanced());
                        $kits_in_cart = $this->sortKitsByCartIndex($kits_in_cart);

                        $kit_unique_ids = $this->getProductAsKitKitsUniqueId($product_as_kit_unique_id);
                        $kit_unique_ids = array_values($kit_unique_ids);

                        foreach ($kits_in_cart as $kit_in_cart) {
                            if ($kit_in_cart['kit_unique_id'] == $kit_unique_ids[count($kit_unique_ids)-1]) {
                                    foreach ($kit_in_cart['kit_items'] as $cart_kit_item) {

                                        $cart_kit_item_new_quantity = $cart_kit_item['quantity']/$product_as_kit['quantity'] * $new_quantity;

                                        $cart_kit_item['quantity'] = $cart_kit_item_new_quantity;
                                        $cart_kit_item['cart_kit_info']['has_product_as_kit_quantity'] = $new_quantity;

                                        $this->updateProductAsKitItemQuantityInCart($cart_kit_item, $cart_kit_item_new_quantity);
                                    }
                            }
                        }

                    }

                }

            }




        }

    }

    public function getCartCountProducts(){
        $product_total = 0;

        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }

        return $product_total;
    }

    private function removeFromCartByVersion($cart_product){
        $this->initSessionId();

        if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
            $key = $cart_product['key'];
            unset($this->session->data['cart'][$key]);
        } else if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.1', '<')) {
            $cart_id = $cart_product['cart_id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
        } else {
            $cart_id = $cart_product['cart_id'];
            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session_id) . "'");
        }
    }

    
    
    public function addProductAsKitDefault($product_id, $quantity = 1, $option = array(), $recurring_id = 0){
        $this->initSessionId();

        $continue = true;

        $this->load->language('product/product');
        $this->load->language('module/bundle_expert');

        $this->load->model('catalog/product');
        $this->load->model('module/bundle_expert');
        $this->load->model('checkout/bundle_expert');
        $this->load->model('tool/image');

        $kits = $this->bundle_expert->getProductAsKitKits($product_id);

        if(!empty($kits)){
            $kit = $kits[0];
            $kit_id = $kit['kit_id'];
            $kit_info = $this->bundle_expert->getKit($kit_id);

            if (!empty($kit_info)) {
                $main_product_id = '';
                $admin_mode = false;

                $get_kit_products_settings = array(
                    'main_product_id' => $main_product_id,
                    'filter_kit_items' => true,
                    'only_first' => false,
                    'admin_mode' => $admin_mode,
                );

                $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings);

                
                $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, true, $admin_mode);

                $kit_info['kit_items'] = $kit_items_data['kit_items'];

                $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id);

                if($kit_enable_status['add_to_cart_kit']){
                    

                    $continue = false;
                }


            }

        }

        return $continue;
    }

    
    public function getCorrectProductsCount(){
        $this->initSessionId();

        $count = 0;

        $cart_products = $this->getCartProductsData();

        foreach ($cart_products as $key => $quantity) {
            $cart_product_info = unserialize(base64_decode($key));
            
            if (isset($cart_product_info['product_as_kit'])) {
                $kit_info = $this->bundle_expert->getKit($cart_product_info['product_as_kit']['kit_id']);

                if ($kit_info['product_as_kit_cart_quantity_mode'] != "items_count") {
                    $count += $quantity;
                }
            } else if (isset($cart_product_info['cart_kit_info']) && isset($cart_product_info['cart_kit_info']['has_product_as_kit_unique_id'])) {
                $kit_info = $this->bundle_expert->getKit($cart_product_info['cart_kit_info']['kit_id']);

                if ($kit_info['product_as_kit_cart_quantity_mode'] == "items_count") {
                    $count += $quantity;
                }
            } else {
                $count += $quantity;
            }
        }

        return $count;
    }

    public function checkCartHasBundles(){
        $has = false;

        $cart_products = $this->cart->getProducts();

        foreach ($cart_products as $cart_product){
            if(isset($cart_product['key'])){
                $cart_product_info = unserialize(base64_decode($cart_product['key']));
                if(isset($cart_product_info['cart_kit_info']) || isset($cart_product_info['product_as_kit'])){
                    $has = true;
                    break;
                }
            }

        }

        return $has;

    }


    public function checkHasStock(){
        $this->initSessionId();

        $cart_products = $this->cart->getProducts();

        foreach ($cart_products as $product) {
            if (!$product['stock']) {
                return false;
            }
            foreach ($product['option'] as $option) {
                if (isset($option['product_as_kit_option_stock'])) {
                    if (!$option['product_as_kit_option_stock']) {
                        return false;
                    }
                }
            }
        }
























        return true;
    }
}