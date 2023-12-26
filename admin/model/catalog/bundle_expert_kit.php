<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelCatalogBundleExpertKit extends Model
{
    private $token_name = array();
    private $token_value = array();


    public function __construct($registry) {

        parent::__construct($registry);

        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $this->token_name = 'token=';
            $this->token_value = $this->session->data['token'];
        }else{
            $this->token_name = 'user_token=';
            $this->token_value = $this->session->data['user_token'];
        }
    }

    public function addKit($data, $copy_mode = false, $import_template_id = -1, $import_template_mode = false, $import_kit_key = '')
    {


        if(!isset($data['kit_mode'])){
            $data['kit_mode'] = 'list_product';
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert SET name = '" . $this->db->escape($data['name']) . "', main_mode = '" . $this->db->escape($data['main_mode']) . "', kit_mode = '" . $this->db->escape($data['kit_mode']) . "', kit_price_mode = '" . $this->db->escape(json_encode($data['kit_price_mode'])) . "', product_quantity_limit = '" . $this->db->escape(json_encode($data['product_quantity_limit'])) . "', kit_discount_by_product_count = '" . $this->db->escape(json_encode($data['kit_discount_by_product_count'])) . "', kit_as_product_main_product_use_default_discount = '" . (int)$data['kit_as_product_main_product_use_default_discount'] . "', bundle_total_price_hide_special = '" . (int)$data['bundle_total_price_hide_special'] . "', disbanded_bundle_clear = '" . (int)$data['disbanded_bundle_clear'] . "', kit_quantity_mode = '" . $this->db->escape(json_encode($data['kit_quantity_mode'])) . "', kit_cart_limit_mode = '" . $this->db->escape(json_encode($data['kit_cart_limit_mode'])) . "', product_discount_in_total = '" . (int)$data['product_discount_in_total'] . "', kit_as_product = '" . (int)$data['kit_as_product'] . "', save_kit_as_product_total = '" . (int)$data['save_kit_as_product_total'] . "', kit_in_cart_as_product = '" . (int)$data['kit_in_cart_as_product'] . "', enable_discount = '" . (int)$data['enable_discount'] . "', enable_special = '" . (int)$data['enable_special'] . "', show_default_specials_in_kit_discounts = '" . (int)$data['show_default_specials_in_kit_discounts'] . "', quantity_control = '" . (int)$data['quantity_control'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "',date_modified = NOW(), date_added = NOW()");

        $kit_id = $this->db->getLastId();

        $this->editKit($kit_id, $data, $copy_mode, $import_template_id, $import_template_mode, $import_kit_key);

        $this->cache->delete('kit');



        return $kit_id;
    }

    public function editKit($kit_id, $data, $copy_mode = false, $import_template_id = -1, $import_template_mode = false, $import_kit_key='')
    {


        
        if($data['main_mode']=='series' || $data['main_mode']=='collection' ){
            $data['kit_as_product'] = 0;
            $data['enable_special'] =1;
            $data['show_default_specials_in_kit_discounts'] =1;
        }

        

        if($data['kit_as_product']){
            $data['product_discount_in_total']=0;
        }

        if($data['kit_as_product'] ){
            $data['kit_mode']='list_product';
            $data['kit_as_product_light_mode']=0;
            $data['kit_in_cart_as_product']=1;
        }

        if(!isset($data['kit_mode'])){
            $data['kit_mode'] = 'list_product';
        }

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET name = '" . $this->db->escape($data['name']) . "', main_mode = '" . $this->db->escape($data['main_mode']) . "', kit_mode = '" . $this->db->escape($data['kit_mode']) . "', kit_price_mode = '" . $this->db->escape(json_encode($data['kit_price_mode'])) . "', product_quantity_limit = '" . $this->db->escape(json_encode($data['product_quantity_limit'])) . "', kit_discount_by_product_count = '" . $this->db->escape(json_encode($data['kit_discount_by_product_count'])) . "', kit_as_product_main_product_use_default_discount = '" . (int)$data['kit_as_product_main_product_use_default_discount'] . "', bundle_total_price_hide_special = '" . (int)$data['bundle_total_price_hide_special'] . "', disbanded_bundle_clear = '" . (int)$data['disbanded_bundle_clear'] . "', kit_quantity_mode = '" . $this->db->escape(json_encode($data['kit_quantity_mode'])) . "', kit_cart_limit_mode = '" . $this->db->escape(json_encode($data['kit_cart_limit_mode'])) . "', product_discount_in_total = '" . (int)$data['product_discount_in_total'] . "', kit_as_product = '" . (int)$data['kit_as_product'] . "', save_kit_as_product_total = '" . (int)$data['save_kit_as_product_total'] . "', kit_in_cart_as_product = '" . (int)$data['kit_in_cart_as_product'] . "', enable_discount = '" . (int)$data['enable_discount'] . "', enable_special = '" . (int)$data['enable_special'] . "', show_default_specials_in_kit_discounts = '" . (int)$data['show_default_specials_in_kit_discounts'] . "', quantity_control = '" . (int)$data['quantity_control'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', kit_as_product_light_mode = '" . (int)$data['kit_as_product_light_mode'] . "', date_modified = NOW() WHERE kit_id = '" . (int)$kit_id . "'");

        if ($import_template_id >= 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET import_template_id = '" . (int)$import_template_id . "' WHERE kit_id = '" . (int)$kit_id . "'");
        }

        if ($import_template_mode >= 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET import_template_mode = '" . (int)$import_template_mode . "' WHERE kit_id = '" . (int)$kit_id . "'");
        }

        if ($import_kit_key != '') {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET import_kit_key = '" . $this->db->escape($import_kit_key) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        }

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET series_mode = '" . $this->db->escape($data['series_mode']) . "' WHERE kit_id = '" . (int)$kit_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET link_products_combine_mode = '" . $this->db->escape($data['link_products_combine_mode']) . "' WHERE kit_id = '" . (int)$kit_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET image = '" . $this->db->escape($data['image']) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_description WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_history_status WHERE kit_id = '" . (int)$kit_id . "'");


        foreach ($data['kit_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_description SET kit_id = '" . (int)$kit_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', cart_title = '" . $this->db->escape($value['cart_title']) . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "'");


        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET custom_field_text = '" . $this->db->escape($data['custom_field_text']) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET custom_field_string = '" . $this->db->escape($data['custom_field_string']) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET custom_field_number = '" . (int)($data['custom_field_number']) . "' WHERE kit_id = '" . (int)$kit_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET auto_kit_in_cart = '" . (int)($data['auto_kit_in_cart']) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET add_to_cart_mode = '" . $this->db->escape($data['add_to_cart_mode']) . "' WHERE kit_id = '" . (int)$kit_id . "'");

        
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET kit_mode_auto_list_grouping = '" . $this->db->escape($data['kit_mode_auto_list_grouping']) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        

        
        if(isset($data['kit_price_mode_to_customer_groups_status'])){
            $kit_price_mode_to_customer_groups_status = true;
        }else{
            $kit_price_mode_to_customer_groups_status = false;
        }
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET kit_price_mode_to_customer_groups_status = '" . (int)$kit_price_mode_to_customer_groups_status . "' WHERE kit_id = '" . (int)$kit_id . "'");

        if(isset($data['kit_price_mode_to_customer_groups'])){
            $kit_price_mode_to_customer_groups = array();
            foreach ($data['kit_price_mode_to_customer_groups'] as $kit_price_mode_to_customer_group){
                $customer_group_id = $kit_price_mode_to_customer_group['customer_group_id'];
                $kit_price_mode_to_customer_groups[$customer_group_id] = $kit_price_mode_to_customer_group;
            }
        }else{
            $kit_price_mode_to_customer_groups = array();
        }
        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET kit_price_mode_to_customer_groups = '" . $this->db->escape(json_encode($kit_price_mode_to_customer_groups)) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        

        if (!empty($data['link_product'])) {
            foreach ($data['link_product'] as $value) {
                $item = $value;

                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_to_product SET kit_id = '" . (int)$kit_id . "', item_type = '" . $this->db->escape($item['item_type']) . "', item_id = '" . (int)$item['item_id'] . "', item_value = '" .  $this->db->escape($item['item_value']) . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "'");

        $item_position = -1;
        $prev_item_position = -1;
        if (isset($data['list_product']) && !empty($data['list_product'])) {
            
            foreach ($data['list_product'] as $index => $value) {

            }

            foreach ($data['list_product'] as $value) {
                $item = $value;

                
                if($data['kit_as_product'] && $item['main'])
                    continue;

                if($item['item_mode']=="free_product")
                    continue;



                if (!empty($item['item_id']) && !$item['remove_item_product']) {

                    if ($item['item_position'] != $prev_item_position) {
                        $item_position++;
                    }

                    if ($item['main']) {
                        $item_position = 0;
                    }

                    $disable_options = array();
                    if (isset($item['disable_options']))
                        $disable_options = array_values($item['disable_options']);

                    $enabled_options = array();
                    if (isset($item['enabled_options']))
                        $enabled_options = array_values($item['enabled_options']);

                    $fixed_options = array();
                    if (isset($item['fixed_options']))
                        $fixed_options = array_values($item['fixed_options']);

                    if (!isset($item['item_empty_mode']))
                        $item_empty_mode = null;
                    else {
                        if($item['item_mode']=="fix_product"){
                            $item_empty_mode = null;
                        }else{
                            if(!$copy_mode){
                                $item['item_empty_mode']['enable'] = isset($item['item_empty_mode']['enable']) ? 1 : 0;
                                $item['item_empty_mode']['default_empty'] = isset($item['item_empty_mode']['default_empty']) ? 1 : 0;
                                $item['item_empty_mode']['not_empty_in_cart'] = isset($item['item_empty_mode']['not_empty_in_cart']) ? 1 : 0;
                            }

                            $item_empty_mode = json_encode($item['item_empty_mode']);
                        }
                    }

                    
                    if(isset($item['price_mode_to_customer_groups_status'])){
                        $price_mode_to_customer_groups_status = true;
                    }else{
                        $price_mode_to_customer_groups_status = false;
                    }

                    $price_mode_to_customer_groups = array();
                    if(isset($item['price_mode_to_customer_groups'])){
                        $price_mode_to_customer_groups = array();
                        foreach ($item['price_mode_to_customer_groups'] as $price_mode_to_customer_group){
                            $customer_group_id = $price_mode_to_customer_group['customer_group_id'];
                            $price_mode_to_customer_groups[$customer_group_id] = $price_mode_to_customer_group;
                        }
                    }else{
                        $price_mode_to_customer_groups = array();
                    }
                    $price_mode_to_customer_groups = json_encode($price_mode_to_customer_groups);
                    

                    if(!$copy_mode) {
                        $quantity_edit = isset($item['quantity_edit']) ? 1 : 0;
                        $free_product_default_in_kit = isset($item['free_product_default_in_kit']) ? 1 : 0;
                        $hide_special_products = isset($item['hide_special_products']) ? 1 : 0;
                        $randomize_select_product = isset($item['randomize_select_product']) ? 1 : 0;

                        $products_combine_mode = $item['products_combine_mode'];
                    }else{
                        $quantity_edit = $item['quantity_edit'];
                        $free_product_default_in_kit = $item['free_product_default_in_kit'];
                        $hide_special_products = $item['hide_special_products'];
                        $randomize_select_product = $item['randomize_select_product'];
                        $products_combine_mode = $item['products_combine_mode'];
                    }

                    $empty_group_image = $item['empty_group_image'];
                    $custom_field = $item['custom_field'];

                    $item['quantity'] = (int)$item['quantity'];
                    if($item['quantity']<=0)
                        $item['quantity'] = 1;



                    $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_items SET kit_id = '" . (int)$kit_id . "', item_position = '" . (int)$item_position . "', item_mode = '" . $this->db->escape($item['item_mode']) . "', item_type = '" . $this->db->escape($item['item_type']) . "', item_id = '" . (int)$item['item_id'] . "', item_value = '" . $this->db->escape($item['item_value']) . "', quantity = '" . (int)$item['quantity'] . "', quantity_edit = '" . (int)$quantity_edit . "', free_product_default_in_kit = '" . (int)$free_product_default_in_kit . "', price_mode = '" . $this->db->escape($item['price_mode']) . "', price = '" . (float)$item['price'] . "', price_minus_sum = '" . (float)$item['price_minus_sum'] . "', price_minus_percent = '" . (float)$item['price_minus_percent'] . "', main = '" . (int)$item['main'] . "', disable_options='" . $this->db->escape(json_encode($disable_options)) . "', enabled_options='" . $this->db->escape(json_encode($enabled_options)) . "', fixed_options='" . $this->db->escape(json_encode($fixed_options)) . "', item_empty_mode='" . $this->db->escape($item_empty_mode) . "', hide_special_products='" . (int)($hide_special_products) . "', randomize_select_product='" . (int)($randomize_select_product) . "', products_combine_mode='" . $this->db->escape($products_combine_mode) . "', empty_group_image = '".$this->db->escape($empty_group_image)."', custom_field = '".$this->db->escape($custom_field)."', price_mode_to_customer_groups_status = '".(int)$price_mode_to_customer_groups_status."', price_mode_to_customer_groups = '". $price_mode_to_customer_groups."'");

                    $prev_item_position = $item['item_position'];
                }

            }

            
            foreach ($data['list_product'] as $value) {
                $item = $value;

                if($item['item_mode']!="free_product")
                    continue;

                if (!empty($item['item_id']) && !$item['remove_item_product']) {

                    if ($item['item_position'] != $prev_item_position) {
                        $item_position++;
                    }

                    if ($item['main']) {
                        $item_position = 0;
                    }

                    $disable_options = array();
                    if (isset($item['disable_options']))
                        $disable_options = array_values($item['disable_options']);

                    $enabled_options = array();
                    if (isset($item['enabled_options']))
                        $enabled_options = array_values($item['enabled_options']);

                    $fixed_options = array();
                    if (isset($item['fixed_options']))
                        $fixed_options = array_values($item['fixed_options']);

                    if (!isset($item['item_empty_mode'])) {
                        $item_empty_mode = null;
                        $item['item_empty_mode']['enable'] = 0;
                        $item['item_empty_mode']['default_empty'] = 0;
                        $item['item_empty_mode']['not_empty_in_cart'] = 0;
                    } else {


                        $item['item_empty_mode']['enable'] = 0;
                        $item['item_empty_mode']['default_empty'] = 0;
                        $item['item_empty_mode']['not_empty_in_cart'] = 0;
                        $item_empty_mode = json_encode($item['item_empty_mode']);
                    }

                    if(!$copy_mode) {
                        $quantity_edit = isset($item['quantity_edit']) ? 1 : 0;
                        $free_product_default_in_kit = isset($item['free_product_default_in_kit']) ? 1 : 0;
                        $hide_special_products = isset($item['hide_special_products']) ? 1 : 0;

                        $products_combine_mode = $item['products_combine_mode'];
                    }else{
                        $quantity_edit = $item['quantity_edit'];
                        $free_product_default_in_kit = $item['free_product_default_in_kit'];
                        $hide_special_products = $item['hide_special_products'];
                        $products_combine_mode = $item['products_combine_mode'];
                    }

                    $empty_group_image = $item['empty_group_image'];
                    $custom_field = $item['custom_field'];

                    $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_items SET kit_id = '" . (int)$kit_id . "', item_position = '" . (int)$item_position . "', item_mode = '" . $this->db->escape($item['item_mode']) . "', item_type = '" . $this->db->escape($item['item_type']) . "', item_id = '" . (int)$item['item_id'] . "', item_value = '" . $this->db->escape($item['item_value']) . "', quantity = '" . (int)$item['quantity'] . "', quantity_edit = '" . (int)$quantity_edit . "', free_product_default_in_kit = '" . (int)$free_product_default_in_kit . "', price_mode = '" . $this->db->escape($item['price_mode']) . "', price = '" . (float)$item['price'] . "', price_minus_sum = '" . (float)$item['price_minus_sum'] . "', price_minus_percent = '" . (float)$item['price_minus_percent'] . "', main = '" . (int)$item['main'] . "', disable_options='" . $this->db->escape(json_encode($disable_options)) . "', enabled_options='" . $this->db->escape(json_encode($enabled_options)) . "', fixed_options='" . $this->db->escape(json_encode($fixed_options)) . "', item_empty_mode='" . $this->db->escape($item_empty_mode) . "', hide_special_products='" . (int)($hide_special_products) . "', products_combine_mode='" . $this->db->escape($products_combine_mode) . "', empty_group_image = '".$this->db->escape($empty_group_image)."', custom_field = '".$this->db->escape($custom_field)."'");

                    $prev_item_position = $item['item_position'];
                }

            }
        }

        
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_auto_kit_in_cart WHERE kit_id = '" . (int)$kit_id . "'");
        if (isset($data['auto_kit_in_cart']) && $data['auto_kit_in_cart']) {
            $item_position = 0;

            foreach ($data['list_product'] as $item) {
                $items_count = count($data['list_product'] );
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_auto_kit_in_cart SET kit_id = '" . (int)$kit_id . "', position = '" . (int)$item_position . "', product_id = '" . (int)$item['item_id'] . "', quantity = '" . (int)$item['quantity'] . "', cart_quantity = '" . (int)0 . "', items_count = '" . (int)$items_count. "', kit_sort_order = '" . (int)$data['sort_order'] . "'");
                $item_position++;
            }
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE kit_id = '" . (int)$kit_id . "'");
        if (isset($data['kit_widgets'])) {
            foreach ($data['kit_widgets'] as $kit_widget){
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_kit SET kit_id = '" . (int)$kit_id . "', widget_id = '" . (int)$kit_widget['widget_id'] . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id = '" . (int)$kit_id . "'");


        if (isset($data['kit_period'])) {
            foreach ($data['kit_period'] as $kit_period) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_kit_period SET kit_id = '" . (int)$kit_id . "', customer_group_id = '" . (int)$kit_period['customer_group_id'] . "', priority = '" . (int)$kit_period['priority'] . "', date_start = '" . $this->db->escape($kit_period['date_start']) . "', date_end = '" . $this->db->escape($kit_period['date_end']) . "'");
            }
        }else{
           
           $this->bundle_expert->setToKitDefaultPeriod($kit_id);
        }

        if($this->bundle_expert->isGroupsInstalled()){
            $this->load->model('catalog/bundle_expert_product_group');
            $this->model_catalog_bundle_expert_product_group->deleteGroupsByKit($kit_id);

            if (isset($data['group'])) {

                $sort_order = 0;
                foreach ($data['group'] as $group){
                    $group['sort_order'] = $sort_order;
                    $this->model_catalog_bundle_expert_product_group->addGroup($group, $kit_id);
                    $sort_order++;
                }
            }
        }

        
        $this->cache->delete('kit');
        $this->cache->delete('product.bundle_expert.kit.' . $kit_id . "");



        $this->bundle_expert->dbCacheKitClear($kit_id);
        $this->bundle_expert->updateKitCache($kit_id);



        $this->bundle_expert->initCurrencieSession();

        
        $this->bundle_expert->updateProductAsKitPricesProcess($data);
        



        
        
        
        if(($data['kit_as_product'] || $data['kit_as_product_light_mode']) && $data['save_kit_as_product_total']){
            if($data['status']){
                if(!isset($this->session->data['currency'])){

                    $this->bundle_expert->initCurrencieSession();
                }

                
                $linked_products = $this->getAllLinkedProducts($kit_id);
                foreach ($linked_products as $linked_product){
                    if($data['kit_as_product']){
                        $price_data = $this->bundle_expert->getProductAsKitPrice($linked_product);
                    }
                    if($data['kit_as_product_light_mode']){
                        $this->bundle_expert->deleteProductSpecialByOpencartVersion($linked_product);
                        $price_data = $this->bundle_expert->getProductAsKitPrice_LE($linked_product);
                    }

                    $prices_data = array();
                    $prices_data[$linked_product] = $price_data;
                    $this->bundle_expert->updateProductAsKitPricesInDB($prices_data);








                }
            }


        }

        return $kit_id;






    }


























    public function copyKit($kit_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "bundle_expert k LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd2 ON (k.kit_id = kd2.kit_id) WHERE k.kit_id = '" . (int)$kit_id . "' AND kd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        if ($query->num_rows) {
            $data = $query->row;

            $data['kit_description'] = $this->getKitDescriptions($kit_id);
            $data['link_product'] = $this->getKitLinkProducts($kit_id);
            $data['list_product'] = $this->getKitProducts($kit_id);
            $data['kit_period'] = $this->getKitPeriods($kit_id);
            $data['kit_widgets'] = $this->getKitWidgets($kit_id);
            if($this->bundle_expert->isGroupsInstalled()) {
                $this->load->model('catalog/bundle_expert_product_group');

                $groups = $this->model_catalog_bundle_expert_product_group->getKitGroups($kit_id);

                foreach ($groups as $index=>$group){
                    unset($groups[$index]['group_id']);
                    $groups[$index]['kit_id'] = '';
                    foreach ($group['items'] as $index2=>$item){
                        $groups[$index]['items'][$index2]['kit_id'] = '';
                        $groups[$index]['items'][$index2]['item_id'] = '';
                        $groups[$index]['items'][$index2]['group_id'] = '';
                    }
                }

                $data['group'] = $groups;

            }

            $data['kit_price_mode'] = json_decode($data['kit_price_mode'], true);
            $data['kit_quantity_mode'] = json_decode($data['kit_quantity_mode'], true);
            $data['kit_cart_limit_mode'] = json_decode($data['kit_cart_limit_mode'], true);
            $data['product_quantity_limit'] = json_decode($data['product_quantity_limit'], true);
            $data['kit_discount_by_product_count'] = json_decode($data['kit_discount_by_product_count'], true);

            $data['name'] = $this->replaceKitNameCopyIndex($data);



            $this->addKit($data, true);
        }
    }

    private function replaceKitNameCopyIndex($data){
        $name_template = $data['name'];

        $char_pos = mb_strrpos($name_template, '#');

        if ($char_pos !== false) {
            $search_template = trim(mb_substr($name_template, 0, $char_pos-1));
            $name_template = mb_substr($name_template, 0, $char_pos+1);
        } else {
            $search_template = trim($name_template);
            $name_template .= ' #';
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE name LIKE '" . $this->db->escape($search_template) . "%'");

        $max_index = 2;

        foreach ($query->rows as $row) {
            $curent_index = (int) str_replace($name_template, '', $row['name']);
            if ($curent_index >= $max_index) {
                $max_index = $curent_index + 1;
            }
        }

        if($max_index>1){
            $name = $search_template .' #' . $max_index;
        }else{
            $name = $search_template;
        }

        return $name;
    }

    private function getAllLinkedProducts($kit_id){

        $linked_products = array();

        $links = $this->getKitLinkProducts($kit_id);

        foreach ($links as $item) {

            if($item['item_type']=='product'){
                $products = array(array('product_id'=>$item['item_id']));
            }

            if ($item['item_type'] == 'category') {
                $filter_data = array(
                    'filter_category_id' => $item['item_id'],
                    'filter_filter' => '',
                    'filter_name' => '',
                    'sort' => '',
                    'order' => '',
                    'start' => 0,
                    'limit' => 1000
                );
                $products = $this->getProductsByCategory($filter_data);

            }

            if ($item['item_type'] == 'manufacturer') {
                $filter_data = array(
                    'filter_name' => '',
                );
                $products = $this->getProductsByManufacturer($item['item_id'], $filter_data);
            }

            if ($item['item_type'] == 'filter') {
                $filter_data = array(
                    'filter_name' => '',
                );
                $products = $this->getProductsByFilter($item['item_id'], $filter_data);

            }

            foreach ($products as $product) {
                $product_id = $product['product_id'];

                if(!isset($linked_products[$product_id])) {
                    $linked_products[$product_id] = $product_id;
                }
            }
        }

        return $linked_products;
    }

    public function deleteAllKits(){
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_description");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_items ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_to_product ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_history_status ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_products");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_period ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group_item ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group_selected_product ");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits ");

        $this->cache->delete('kit');
        $this->cache->delete('product.bundle_expert.kit.');
    }

    public function deleteKit($kit_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_description WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_history_status WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group_item WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_product_group_selected_product WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_auto_kit_in_cart WHERE kit_id = '" . (int)$kit_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits WHERE kit_id = '" . (int)$kit_id . "'");

        $this->cache->delete('kit');
        $this->cache->delete('product.bundle_expert.kit.' . $kit_id . "");

    }

    public function repairKits($parent_id = 0)
    {

    }

    public function getKit($kit_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "bundle_expert k LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd2 ON (k.kit_id = kd2.kit_id) WHERE k.kit_id = '" . (int)$kit_id . "' AND kd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        if(!empty($query->row)){
            $query->row['kit_price_mode_to_customer_groups'] = (isset($query->row['kit_price_mode_to_customer_groups'])) ? json_decode($query->row['kit_price_mode_to_customer_groups'], true) : array();
            $query->row['kit_price_mode_to_customer_groups_status'] = (isset($query->row['kit_price_mode_to_customer_groups_status'])) ? $query->row['kit_price_mode_to_customer_groups_status'] : false;

        }


        return $query->row;
    }

    public function getKits($data = array())
    {
        $kits_filter = '';

        if(isset($data['filter_product_id']) && !empty($data['filter_product_id'])){
            $kits_filter = '-1';
            $query = $this->db->query("SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE product_id = ".(int)$data['filter_product_id']." GROUP BY kit_id");
            if($query->rows) {
                $kits_filter = array();
                foreach ($query->rows as $row) {
                    $kits_filter[] = $row['kit_id'];
                }
                $kits_filter = implode(',', $kits_filter);
            }
        }


        $sql = "SELECT k.kit_id AS kit_id, k.*, kd1.title AS title FROM " . DB_PREFIX . "bundle_expert k LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd1 ON (k.kit_id = kd1.kit_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd2 ON (k.kit_id = kd2.kit_id) WHERE kd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND kd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND k.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if($kits_filter){
            $sql .= " AND k.kit_id IN (" . $this->db->escape($kits_filter) . ")";
        }

        if (isset($data['import_template_mode']) && $data['import_template_mode'] == true) {
            $sql .= " AND k.import_template_mode = 1";
        }else{
            $sql .= " AND k.import_template_mode = 0";
        }

        $sql .= " GROUP BY k.kit_id";

        $sort_data = array(
            'name',
            'title',
            'quantity',
            'status',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        $kits = array();

        foreach ($query->rows as $row) {
            $row['kit_price_mode'] = json_decode($row['kit_price_mode'], true);
            $row['kit_quantity_mode'] = json_decode($row['kit_quantity_mode'], true);
            $row['kit_cart_limit_mode'] = json_decode($row['kit_cart_limit_mode'], true);
            $row['main_mode'] = isset($row['main_mode']) ? $row['main_mode'] : 'kit';

            $kits[] = $row;
        }
        return $kits;
    }

    public function getKitProducts($kit_id)
    {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "' ORDER BY id ASC");

        $kit_products = array();

        foreach ($query->rows as $row) {
            $row['item_empty_mode'] = json_decode($row['item_empty_mode'], true);
            $row['disable_options'] = json_decode($row['disable_options'], true);
            $row['enabled_options'] = json_decode($row['enabled_options'], true);
            $row['fixed_options'] = json_decode($row['fixed_options'], true);
            
            $row['price_mode_to_customer_groups'] = json_decode($row['price_mode_to_customer_groups'], true);
            
            $row['remove_item_product'] = false;

            if(!isset($row['item_empty_mode']['not_empty_in_cart'])){
                $row['item_empty_mode']['not_empty_in_cart'] = 0;
            }


            $kit_products[] = $row;
        }


        return $kit_products;

    }

    public function getKitLinkProducts($kit_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "' ORDER BY id");

        return $query->rows;

    }

    public function getKitDescriptions($kit_id)
    {
        $kit_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_description WHERE kit_id = '" . (int)$kit_id . "'");

        foreach ($query->rows as $result) {
            $kit_description_data[$result['language_id']] = array(
                'title' => $result['title'],
                'description' => $result['description'],
                'cart_title' => $result['cart_title']
            );
        }

        return $kit_description_data;
    }

    public function getKitPeriods($kit_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id = '" . (int)$kit_id . "' ORDER BY priority");

        return $query->rows;
    }

    public function getKitFilters($kit_id)
    {
        $kit_filter_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_filter WHERE kit_id = '" . (int)$kit_id . "'");

        foreach ($query->rows as $result) {
            $kit_filter_data[] = $result['filter_id'];
        }

        return $kit_filter_data;
    }


    public function getKitWidgets($kit_id)
    {

        $query = $this->db->query("SELECT wk.*, w.name FROM " . DB_PREFIX . "bundle_expert_widget_kit wk LEFT JOIN " . DB_PREFIX . "bundle_expert_widget w ON (w.widget_id=wk.widget_id) WHERE kit_id = '" . (int)$kit_id . "'");

        return $query->rows;
    }



    public function getTotalKits($data)
    {
        $kits_filter = '';

        if(isset($data['filter_product_id']) && !empty($data['filter_product_id'])){
            $kits_filter = '-1';
            $query = $this->db->query("SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE product_id = ".(int)$data['filter_product_id']." GROUP BY kit_id");
            if($query->rows) {
                $kits_filter = array();
                foreach ($query->rows as $row) {
                    $kits_filter[] = $row['kit_id'];
                }
                $kits_filter = implode(',', $kits_filter);
            }
        }

        $sql = "SELECT COUNT(DISTINCT k.kit_id) AS total FROM " . DB_PREFIX . "bundle_expert k LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd1 ON (k.kit_id = kd1.kit_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd2 ON (k.kit_id = kd2.kit_id) WHERE kd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND kd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND k.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if($kits_filter){
            $sql .= " AND k.kit_id IN (" . $this->db->escape($kits_filter) . ")";
        }

        if (isset($data['import_template_mode']) && $data['import_template_mode'] == true) {
            $sql .= " AND k.import_template_mode = 1";
        }else{
            $sql .= " AND k.import_template_mode = 0";
        }

        $query = $this->db->query($sql);




        return $query->row['total'];
    }

    public function getTotalKitsByLayoutId($layout_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bundle_expert_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

        return $query->row['total'];
    }

    public function getOptionValues($option_id)
    {
        $option_value_data = array();

        $option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int)$option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order, ovd.name");

        foreach ($option_value_query->rows as $option_value) {
            $option_value_data[$option_value['option_value_id']] = array(
                'option_value_id' => $option_value['option_value_id'],
                'name' => $option_value['name'],
                'image' => $option_value['image'],
                'sort_order' => $option_value['sort_order']
            );
        }

        return $option_value_data;
    }

    public function getOrderProductKitInfo($order_id, $order_product_id, $product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "' AND product_id = '" . (int)$product_id . "'");

        if($query->row && isset($query->row['cart_kit_info'])){
            $cart_kit_info = json_decode($query->row['cart_kit_info'], true);
        } else {
            $cart_kit_info = array();
        }
        return $cart_kit_info;
    }

    public function getOrderProductAsKitInfo($order_id, $order_product_id, $product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "' AND product_id = '" . (int)$product_id . "'");

        if($query->row && isset($query->row['product_as_kit_info'])){
            $product_as_kit_info = json_decode($query->row['product_as_kit_info'], true);
        } else {
            $product_as_kit_info = array();
        }
        return $product_as_kit_info;
    }

    public function getKitsByProduct($product_id){

        $kits = array();

        
        

        
        $product_info = $this->bundle_expert->getProductInfoDefault($product_id);

        if($product_info){
            $categories = $this->model_catalog_product->getProductCategories($product_id);
            $manufacturer = $product_info['manufacturer_id'];
            $filters = $this->model_catalog_product->getProductFilters($product_id);

            $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE (item_type = 'product' AND item_id = " . $product_id . ") ";

            if ($categories) {
                $categories = implode(',', $categories);
                $sql .= " OR (item_type='category' AND item_id IN (" . $categories . "))";
            }

            if ($manufacturer) {
                $sql .= " OR (item_type='manufacturer' AND item_id = " . $manufacturer . ")";
            }

            if ($filters) {
                $filters = implode(',', $filters);
                $sql .= " OR (item_type='category' AND item_id IN (" . $filters . "))";
            }

            $sql .= "GROUP BY kit_id";


            $query = $this->db->query($sql);

            foreach ($query->rows as $row) {
                $kit_info = $this->getKit($row['kit_id']);
                if ($kit_info) {
                    $kits[] = $kit_info;
                }
            }
        }

        return $kits;
    }

    public function getKitHistoryStatus($kit_id)
    {
        $this->load->model('catalog/product');

        $kit_history_status = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_kit_history_status WHERE kit_id = '" . (int)$kit_id . "' ORDER BY success");

        
        $success_added = false;

        foreach ($query->rows as $row) {

            $kit_history_code = $row['kit_history_code'];

            $status = array();

            switch ($kit_history_code) {
                case 'success':
                    $status['text'] = $this->language->get('text_kit_history_status_success');
                    $status['status'] = 'success';
                    break;
                case 'empty_kit_item':
                    $position = $row['position'];

                    $status['text'] = sprintf($this->language->get('text_kit_history_status_empty_kit_item'), $position + 1);
                    $status['status'] = 'warning';
                    break;
                case 'empty_kit_main_item':
                    $product_id = $row['main_product_id'];
                    $product_info = $this->bundle_expert->getProductInfoDefault($product_id);

                    $product_url = $this->url->link('catalog/product/edit', $this->token_name . $this->token_value . '&product_id=' . $product_id, 'SSL');
                    $status['text'] = sprintf($this->language->get('text_kit_history_status_empty_kit_main_item'), $product_url, $product_info['name']);
                    $status['status'] = 'warning';
                    break;
                case 'kit_limit_zero':
                    $status['text'] = sprintf($this->language->get('text_kit_history_status_kit_limit_zero'));
                    $status['status'] = 'warning';
                    break;
            }


            if (!$row['success'] || !$success_added) {
                $kit_history_status[] = $status;
            }

            if ($row['success']) {
                $success_added = true;
            }

        }


        return $kit_history_status;
    }

    
    public function getKitHistoryStatusInfo($kit_id)
    {
        $status_info = array(
            'text' => '',
            'status' => 'empty'
        );

        $kit_history_status = $this->getKitHistoryStatus($kit_id);

        $has_warning = false;
        $has_success = false;

        foreach ($kit_history_status as $kit_history) {
            if ($kit_history['status'] == 'success') {
                $has_success = true;
            }

            if ($kit_history['status'] == 'warning') {
                $has_warning = true;
            }
        }

        if ($has_warning && $has_success) {
            $status_info['text'] = $this->language->get('text_kit_history_status_warning_sometimes');
            $status_info['status'] = 'warning';
        }

        if ($has_warning && !$has_success) {
            $status_info['text'] = $this->language->get('text_kit_history_status_warning');
            $status_info['status'] = 'warning';
        }

        if (!$has_warning && $has_success) {
            $status_info['text'] = $this->language->get('text_kit_history_status_success');
            $status_info['status'] = 'success';
        }
        return $status_info;
    }

    public function getManufacturers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getProductsByCategory($data = array()) {
        $sql = "SELECT p.product_id ";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
            } else {
                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
            }

            if (!empty($data['filter_filter'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            } else {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
            }
        } else {
            $sql .= " FROM " . DB_PREFIX . "product p";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
            } else {
                $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
            }

            if (!empty($data['filter_filter'])) {
                $implode = array();

                $filters = explode(',', $data['filter_filter']);

                foreach ($filters as $filter_id) {
                    $implode[] = (int)$filter_id;
                }

                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
            }
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

            }

            if (!empty($data['filter_name'])) {
                $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
            }

            $sql .= ")";
        }

        if (!empty($data['filter_manufacturer_id'])) {
            $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.quantity',
            'p.price',
            'rating',
            'p.viewed',
            'p.sort_order',
            'p.date_added',
            'special'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } elseif ($data['sort'] == 'p.price') {
                $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, p.quantity DESC, LCASE(pd.name) ASC";
        } else {
            $sql .= " ASC, p.quantity DESC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        $products = $query->rows;

        return $products;

    }


    public function getProductsByManufacturer($manufacturer_id, $data) {

        if(!$this->bundle_expert->isEnabled()) return array();

        $sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

            }

            if (!empty($data['filter_name'])) {
                $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
            }

            $sql .= ")";
        }

        $query = $this->db->query($sql);


        return $query->rows;
    }

    public function getProductsByFilter($filter_id, $data) {

        if(!$this->bundle_expert->isEnabled()) return array();

        $sql = "SELECT pf.product_id FROM " . DB_PREFIX . "product_filter pf LEFT JOIN  " . DB_PREFIX . "product p ON(p.product_id=pf.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pf.filter_id = '" . (int)$filter_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

            }

            if (!empty($data['filter_name'])) {
                $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
            }

            $sql .= ")";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    
    private function calculateKitTotal($data){

        $kit_price = 0;

        if(isset($data['list_product'])){
            $list_product = $data['list_product'];
        }else{
            $list_product = array();
        }

        $this->load->model('catalog/product');

        $position_in_total = array();

        
        foreach ($list_product as $item) {
            if (!in_array($item['item_position'], $position_in_total) || $item['item_mode']=='free_product') {
                if($item['item_mode']=='free_product' && !$item['item_mode']=='free_product_default_in_kit' ){
                    continue;
                }

                $item_type = $item['item_type'];
                $item_id = $item['item_id'];
                if ($item_type == "product") {
                    $product_id = $item_id;
                } else {
                    $product_id = $this->findProductByItemType($item_type, $item_id);
                }
                $product_info = $this->bundle_expert->getProductInfoDefault($product_id);
                if ($product_info) {
                    $product_price = $product_info['price'];
                    
                    if (isset($item['fixed_options']) && !empty($item['fixed_options'])) {
                        $fixed_options_price = $this->calculateProductOptionsTotal($item['fixed_options'], $product_id);
                    } else {
                        $fixed_options_price = 0;
                    }

                    $product_price += $fixed_options_price;
                    $kit_price += $product_price * $item['quantity'];

                    $position_in_total[] = $item['item_position'];
                }

            }
        }

        return $kit_price;
    }

    private function calculateProductOptionsTotal($fixed_options, $product_id){
        $options_price_total = 0;

        foreach ($fixed_options as $fixed_option) {
            $product_option_id = $fixed_option['product_option_id'];
            $value = $fixed_option['product_option_value_id'];
            $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
            $option_price = 0;
            if ($option_query->num_rows) {
                if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_value_query->num_rows) {
                        if ($option_value_query->row['price_prefix'] == '+') {
                            $option_price += $option_value_query->row['price'];
                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                            $option_price -= $option_value_query->row['price'];
                        }

                        $options_price_total += $option_price;

                    }

                } elseif ($option_query->row['type'] == 'checkbox') {

                    $product_option_value_id = $fixed_option['product_option_value_id'];
                    $option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_value_query->num_rows) {
                        if ($option_value_query->row['price_prefix'] == '+') {
                            $option_price += $option_value_query->row['price'];
                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                            $option_price -= $option_value_query->row['price'];
                        }

                        $options_price_total += $option_price;


                    }

                } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {

                }
            }
        }

        return $options_price_total;
    }


    private function findProductByItemType($item_type, $item_id){

        $product_id = '';

        switch ($item_type){
            case 'category':
                $filter = array(
                    'filter_category_id' => $item_id
                );
                $products = $this->getProductsByCategory($filter);
                break;
            case 'manufacturer':
                $filter = array(
                );
                $products = $this->getProductsByManufacturer($item_id, $filter);
                break;
            case 'filter':
                $filter = array(
                );
                $products = $this->getProductsByFilter($item_id, $filter);
                break;
        }

        foreach ($products as $product){
            $product_info = $this->bundle_expert->getProductInfoDefault($product['product_id']);
            if($product_info && $product_info['status']){
                $product_id = $product['product_id'];
                break;
            }
        }

        return $product_id;

    }

    public function getProductsByField($data, $filter_field_key) {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (p.".$filter_field_key." LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
            $sql .= " )";
        }

        $sql .= " AND p.".$filter_field_key."<>''";

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;

    }

    public function getProducts($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND ( pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
            $sql .= " OR p.sku LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
            $sql .= " OR p.model LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
            $sql .= " )";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_sku']) && !empty($data['filter_sku'])) {
            $sql .= " AND p.sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }


}