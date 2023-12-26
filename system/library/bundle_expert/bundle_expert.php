<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class BundleExpert extends Controller{
    public  $OC_VERSION = '';
    protected $check_license;
    protected $kit_items_cache = array();
    protected $kit_items_info_cache = array();

    
    public $kit_products_cache = array();
    
    protected $kit_options_cache = array();

    public $product_as_kit_dinamic_price_cache = array();
    protected $product_as_kit_dinamic_price_le_cache = array();

    private $cache_to_file = true;
    public $cache_widgets = true;
    public $bundle_expert_settings = array();

    public $admin_api_mode = false;
    

    public $css_min_file = 'view/theme/default/stylesheet/bundle_expert_min.css';
    public $js_min_file = 'view/javascript/bundle-expert/bundle-expert-min.js';

    public $market_id = 0;
    public $script_version = 'v3.4_6';
    public $bundle_expert_version = '1.5.9.0';

    public function __construct($registry) {
        parent::__construct($registry);

        $this->OC_VERSION = $this->getOpencartVersion();

        $bundle_expert_settings = $this->config->get('bundle_expert_settings');

        
        if(isset($bundle_expert_settings) && !is_array($bundle_expert_settings)){
            $bundle_expert_settings = json_decode($bundle_expert_settings, true);
        }

        if (!isset($bundle_expert_settings['cart_items_limit'])) {
            $bundle_expert_settings['cart_items_limit'] = array('status' => 1, 'limit' => '200');
        }

        if (!isset($bundle_expert_settings['order_info_format'])) {
            $bundle_expert_settings['order_info_format'] = 'list';
            $bundle_expert_settings['order_info_format'] = 'one_item';
        }

        if (!isset($bundle_expert_settings['css_js_files'])) {
            $bundle_expert_settings['css_js_files'] = array(
                'magnific_popup' => 0,
                'moment' => 0,
                'datetimepicker' => 0,
            );
        }

        if (!isset($bundle_expert_settings['css_js_minify'])) {
            $bundle_expert_settings['css_js_minify'] = 0;
        }

        if (!isset($bundle_expert_settings['dynamic_update_product_as_kit_price_in_db'])) {
            $bundle_expert_settings['dynamic_update_product_as_kit_price_in_db'] = 1;
        }

        if (!isset($bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le'])) {
            $bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le'] = 1;
        }

        if (!isset($bundle_expert_settings['selectors']['product_page']['button_plus'])) {
            $bundle_expert_settings['selectors']['product_page']['button_plus'] = '';
        }

        if (!isset($bundle_expert_settings['selectors']['product_page']['button_minus'])) {
            $bundle_expert_settings['selectors']['product_page']['button_minus'] = '';
        }

        if (!isset($bundle_expert_settings['product_as_kit_add_from_category_enable'])) {
            $bundle_expert_settings['product_as_kit_add_from_category_enable'] = 0;
        }

        if (!isset($bundle_expert_settings['product_as_kit_product_page_animate_price'])) {
            $bundle_expert_settings['product_as_kit_product_page_animate_price'] = 1;
        }

        if (!isset($bundle_expert_settings['kit_discount_not_customer_group'])) {
            $bundle_expert_settings['kit_discount_not_customer_group'] = array();
        }

        if(!isset($bundle_expert_settings['css_js_files']['bootstrap_3'])) {
            $bundle_expert_settings['css_js_files']['bootstrap_3'] = false;
        }

        if(!isset($bundle_expert_settings['css_js_files']['bootstrap_3_js'])) {
            $bundle_expert_settings['css_js_files']['bootstrap_3_js'] = false;
        }

        if(!isset($bundle_expert_settings['css_js_files']['font_awesome'])) {
            $bundle_expert_settings['css_js_files']['font_awesome'] = false;
        }

        if(!isset($bundle_expert_settings['cart_show_option_in_product_as_kit'])) {
            $bundle_expert_settings['cart_show_option_in_product_as_kit'] = false;
        }

        if(!isset($bundle_expert_settings['cart_show_products_in_product_as_kit'])) {
            $bundle_expert_settings['cart_show_products_in_product_as_kit'] = true;
        }


        if(!isset($bundle_expert_settings['custom_header_cart_js_status'])) {
            $bundle_expert_settings['custom_header_cart_js_status'] = 0;
        }

        if(!isset($bundle_expert_settings['custom_header_cart_js'])) {
            $bundle_expert_settings['custom_header_cart_js'] = '';
        }

        if(!isset($bundle_expert_settings['smart_js'])) {
            $bundle_expert_settings['smart_js'] = false;
        }

        if(!isset($bundle_expert_settings['smart_js_2'])) {
            $bundle_expert_settings['smart_js_2'] = false;

        }

        if(!isset($bundle_expert_settings['cache_widgets'])) {
            $bundle_expert_settings['cache_widgets'] = false;
        }

        
        if(!isset($bundle_expert_settings['product_as_kit_tax_mode'])) {
            $bundle_expert_settings['product_as_kit_tax_mode'] ='by_main';
            $bundle_expert_settings['product_as_kit_tax_mode'] ='by_items';
        }

        if(!isset($bundle_expert_settings['optimized_owl_carousel'])) {
            $bundle_expert_settings['optimized_owl_carousel'] = false;
            $bundle_expert_settings['optimized_owl_carousel'] = true;
        }

        
        
        if(!isset($bundle_expert_settings['product_as_kit_use_reward'])) {
            $bundle_expert_settings['product_as_kit_use_reward'] = false;
        }
        

        if(!isset($bundle_expert_settings['js_defer'])) {
            $bundle_expert_settings['js_defer'] = false;

        }

        
        
        if(!isset($bundle_expert_settings['stock_out_products_show'])) {
            $bundle_expert_settings['stock_out_products_show'] = true;
        }
        if(!isset($bundle_expert_settings['stock_out_bundles_show'])) {
            $bundle_expert_settings['stock_out_bundles_show'] = true;
        }
        
        
        if(!isset($bundle_expert_settings['stock_out_bundles_add_to_cart'])) {
            $bundle_expert_settings['stock_out_bundles_add_to_cart'] = false;
        }

        if(!isset($bundle_expert_settings['product_as_kit_update_main_product_quantity'])) {
            $bundle_expert_settings['product_as_kit_update_main_product_quantity'] = false;
        }

        
        
        if(!isset($bundle_expert_settings['product_as_kit_use_price_cache_table'])) {
            $bundle_expert_settings['product_as_kit_use_price_cache_table'] = true;
        }
        

        
        
        if(!isset($bundle_expert_settings['product_page_widgets_direct_output_mode'])) {
            $bundle_expert_settings['product_page_widgets_direct_output_mode'] = false;
        }
        

        
        
        if(!isset($bundle_expert_settings['control_change_quantity_by_stock'])) {
            $bundle_expert_settings['control_change_quantity_by_stock'] = true;
        }
        
        if(!isset($bundle_expert_settings['show_stockout_options'])) {
            $bundle_expert_settings['show_stockout_options'] = true;
        }
        

        $this->cache_widgets = true;

        $this->bundle_expert_settings = $bundle_expert_settings;

        if(isset($this->request->get['route'])) {
            if (strpos($this->request->get['route'], 'api/') === 0) {
                $this->admin_api_mode = true;
            }
        }

        $this->check_license=$this->checkLicense();

    }

    private function getOpencartVersion(){
        $version = VERSION;

        
        $data = explode('.', $version);

        $data = array_slice($data, 0,4);

        $version = implode('.', $data);

        return $version;
    }

    
    public function isEnabled(){
        if($this->isAdminMode()){




            
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $module_status_name = "bundle_expert_status";
            }else{
                $module_status_name = "module_bundle_expert_status";
            }

            if ($this->config->get($module_status_name) && $this->isLicensed())
                $check = true;
            else
                $check = false;
        }else{
            if ($this->config->get('bundle_expert_status_for_customer') && $this->isLicensed())
                $check = true;
            else
                $check = false;

            
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $module_status_name = "bundle_expert_status";
            }else{
                $module_status_name = "module_bundle_expert_status";
            }

            if (!$this->config->get($module_status_name))
                $check = false;
        }

        return $check;

    }

    private function isAdminMode(){
        $is_admin = false;

        if(isset($this->request->get['token']) && !empty($this->request->get['token'])) {
            $is_admin = true;
        }
        
        if(isset($this->request->get['user_token']) && !empty($this->request->get['user_token'])) {
            $is_admin = true;
        }
        if(isset($this->request->get['route']) && strpos($this->request->get['route'], 'api/') === 0 ) {
            $is_admin = true;
        }

        return $is_admin;
    }

    
    public function getKitInfo($kit_id) {
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "bundle_expert k WHERE k.kit_id = '" . (int)$kit_id . "'";

        $query = $this->db->query($sql);

        return $query->row;

    }

    public function getKit($kit_id, $kit_unique_id = '', $get_kit_products_settings=array(), $filter_by_periods=false) {
        if(!$this->isEnabled()) return;



        if(!empty($kit_unique_id)) {
            $kit_info = $this->getKitInfoFromOrderHistory($kit_unique_id);
            if(!empty($kit_info)){
                
                $kit_info['product_discount_in_total'] = (isset($kit_info['product_discount_in_total'])) ? $kit_info['product_discount_in_total'] : true;
                $kit_info['kit_as_product'] = (isset($kit_info['kit_as_product'])) ? $kit_info['kit_as_product'] : false;
                $kit_info['kit_in_cart_as_product'] = (isset($kit_info['kit_in_cart_as_product'])) ? $kit_info['kit_in_cart_as_product'] : false;
                $kit_info['product_as_kit_cart_quantity_mode'] = (isset($kit_info['product_as_kit_cart_quantity_mode'])) ? $kit_info['product_as_kit_cart_quantity_mode'] : 'items_count';
                $kit_info['product_quantity_limit'] = (isset($kit_info['product_quantity_limit'])) ? $kit_info['product_quantity_limit'] : array('status' => 1, 'min' => '', 'max' => '');
                $kit_info['disbanded_bundle_clear'] = (isset($kit_info['disbanded_bundle_clear'])) ? $kit_info['disbanded_bundle_clear'] : 1;
                $kit_info['kit_as_product_light_mode'] = (isset($kit_info['kit_as_product_light_mode'])) ? $kit_info['kit_as_product_light_mode'] : false;
                $kit_info['enable_discount'] = (isset($kit_info['enable_discount'])) ? $kit_info['enable_discount'] : 0;
                $kit_info['kit_discount_by_product_count'] = (isset($kit_info['kit_discount_by_product_count'])) ? $kit_info['kit_discount_by_product_count'] : array('status' => 0, 'min' => '0', 'max' => '0');
                $kit_info['kit_as_product_main_product_use_default_discount'] = (isset($kit_info['kit_as_product_main_product_use_default_discount'])) ? $kit_info['kit_as_product_main_product_use_default_discount'] : 0;
                $kit_info['bundle_total_price_hide_special'] = (isset($kit_info['bundle_total_price_hide_special'])) ? $kit_info['bundle_total_price_hide_special'] : 0;
                $kit_info['import_template_id'] = (isset($kit_info['import_template_id'])) ? $kit_info['import_template_id'] : null;
                $kit_info['import_template_mode'] = (isset($kit_info['import_template_mode'])) ? $kit_info['import_template_mode'] : 0;
                $kit_info['show_default_specials_in_kit_discounts'] = (isset($kit_info['show_default_specials_in_kit_discounts'])) ? $kit_info['show_default_specials_in_kit_discounts'] : 0;
                $kit_info['link_products_combine_mode'] = (isset($kit_info['link_products_combine_mode'])) ? $kit_info['link_products_combine_mode'] : 'union';
                $kit_info['main_mode'] = (isset($kit_info['main_mode'])) ? $kit_info['main_mode'] : 'kit';
                $kit_info['custom_field_text'] = (isset($kit_info['custom_field_text'])) ? $kit_info['custom_field_text'] : '';
                $kit_info['custom_field_string'] = (isset($kit_info['custom_field_string'])) ? $kit_info['custom_field_string'] : '';
                $kit_info['custom_field_number'] = (isset($kit_info['custom_field_number'])) ? $kit_info['custom_field_number'] : '';
                $kit_info['auto_kit_in_cart'] = (isset($kit_info['auto_kit_in_cart'])) ? $kit_info['auto_kit_in_cart'] : false;
                $kit_info['add_to_cart_mode'] = (isset($kit_info['add_to_cart_mode'])) ? $kit_info['add_to_cart_mode'] : 'bundle';

                
                $kit_info['kit_price_mode_to_customer_groups'] = (isset($kit_info['kit_price_mode_to_customer_groups']) && !is_array($kit_info['kit_price_mode_to_customer_groups'])) ? json_decode($kit_info['kit_price_mode_to_customer_groups'], true) : array();
                $kit_info['kit_price_mode_to_customer_groups_status'] = (isset($kit_info['kit_price_mode_to_customer_groups_status'])) ? $kit_info['kit_price_mode_to_customer_groups'] : false;


                

                
                $kit_info['cart_items_limit'] = (isset($kit_info['cart_items_limit'])) ? $kit_info['cart_items_limit'] : array('status' => 1, 'limit' => '5');

            }
        }

        if(empty($kit_info)){
            $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "bundle_expert k LEFT JOIN " . DB_PREFIX . "bundle_expert_description kd2 ON (k.kit_id = kd2.kit_id) WHERE k.kit_id = '" . (int)$kit_id . "' AND kd2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND k.status = 1 ";

            $kit_info = array();

            $query = $this->db->query($sql);

            if($query->row){
                $kit_info = $query->row;
                $kit_info['kit_price_mode'] = json_decode($kit_info['kit_price_mode'], true);
                $kit_info['kit_quantity_mode'] = json_decode($kit_info['kit_quantity_mode'], true);
                $kit_info['kit_cart_limit_mode'] = json_decode($kit_info['kit_cart_limit_mode'], true);

                
                $kit_info['product_discount_in_total'] = (isset($kit_info['product_discount_in_total'])) ? $kit_info['product_discount_in_total'] : true;
                $kit_info['kit_as_product'] = (isset($kit_info['kit_as_product'])) ? $kit_info['kit_as_product'] : false;
                $kit_info['kit_in_cart_as_product'] = (isset($kit_info['kit_in_cart_as_product'])) ? $kit_info['kit_in_cart_as_product'] : false;
                $kit_info['product_as_kit_cart_quantity_mode'] = (isset($kit_info['product_as_kit_cart_quantity_mode'])) ? $kit_info['product_as_kit_cart_quantity_mode'] : 'items_count';
                $kit_info['product_quantity_limit'] = (isset($kit_info['product_quantity_limit'])) ? json_decode($kit_info['product_quantity_limit'], true) : array('status' => 1, 'min' => '', 'max' => '');
                $kit_info['disbanded_bundle_clear'] = (isset($kit_info['disbanded_bundle_clear'])) ? $kit_info['disbanded_bundle_clear'] : 1;
                $kit_info['kit_as_product_light_mode'] = (isset($kit_info['kit_as_product_light_mode'])) ? $kit_info['kit_as_product_light_mode'] : false;
                $kit_info['enable_discount'] = (isset($kit_info['enable_discount'])) ? $kit_info['enable_discount'] : 0;
                $kit_info['kit_as_product_main_product_use_default_discount'] = (isset($kit_info['kit_as_product_main_product_use_default_discount'])) ? $kit_info['kit_as_product_main_product_use_default_discount'] : 0;
                $kit_info['bundle_total_price_hide_special'] = (isset($kit_info['bundle_total_price_hide_special'])) ? $kit_info['bundle_total_price_hide_special'] : 0;
                $kit_info['kit_items'] = null;
                $kit_info['import_template_id'] = (isset($kit_info['import_template_id'])) ? $kit_info['import_template_id'] : null;
                $kit_info['import_template_mode'] = (isset($kit_info['import_template_mode'])) ? $kit_info['import_template_mode'] : 0;
                $kit_info['show_default_specials_in_kit_discounts'] = (isset($kit_info['show_default_specials_in_kit_discounts'])) ? $kit_info['show_default_specials_in_kit_discounts'] : 0;
                $kit_info['link_products_combine_mode'] = (isset($kit_info['link_products_combine_mode'])) ? $kit_info['link_products_combine_mode'] : 'union';

                $kit_info['kit_discount_by_product_count'] = (isset($kit_info['kit_discount_by_product_count'])) ? json_decode($kit_info['kit_discount_by_product_count'], true) : array('status' => 0, 'min' => '0', 'max' => '0');
                $kit_info['main_mode'] = (isset($kit_info['main_mode'])) ? $kit_info['main_mode'] : 'kit';
                
                $kit_info['cart_items_limit'] = (isset($kit_info['cart_items_limit'])) ? json_decode($kit_info['cart_items_limit'], true) : array('status' => 1, 'limit' => '5');

                $kit_info['custom_field_text'] = (isset($kit_info['custom_field_text'])) ? $kit_info['custom_field_text'] : '';
                $kit_info['custom_field_string'] = (isset($kit_info['custom_field_string'])) ? $kit_info['custom_field_string'] : '';
                $kit_info['custom_field_number'] = (isset($kit_info['custom_field_number'])) ? $kit_info['custom_field_number'] : '';

                $kit_info['auto_kit_in_cart'] = (isset($kit_info['auto_kit_in_cart'])) ? $kit_info['auto_kit_in_cart'] : false;
                $kit_info['add_to_cart_mode'] = (isset($kit_info['add_to_cart_mode'])) ? $kit_info['add_to_cart_mode'] : 'bundle';

                

                $kit_info['kit_price_mode_to_customer_groups'] = (isset($kit_info['kit_price_mode_to_customer_groups']) && !is_array($kit_info['kit_price_mode_to_customer_groups'])) ? json_decode($kit_info['kit_price_mode_to_customer_groups'], true) : array();

                $kit_info['kit_price_mode_to_customer_groups_status'] = (isset($kit_info['kit_price_mode_to_customer_groups_status'])) ? $kit_info['kit_price_mode_to_customer_groups_status'] : false;
                
            }

            if(!empty($kit_info)){
                if(!empty($get_kit_products_settings)){
                    $kit_info['kit_items'] = $this->getKitProducts($kit_id, $get_kit_products_settings);
                }
            }
        }

        if(!empty($kit_info)){

        }

        return $kit_info;
    }

    

    
    public function getWidgetKits($widget_id, $filter_linked_products, $widget) {

        if(!$this->isEnabled()) return array();

        $filter_by_periods = true;
        

        if($filter_by_periods){
            
            if(!empty($filter_linked_products)){
                if($widget['main_mode']!='product_from_kit'){
                    $sql = "SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE product_id IN (".$filter_linked_products.") GROUP BY kit_id";
                }else{
                    $sql = "SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE product_id IN (".$filter_linked_products.") GROUP BY kit_id";
                }
                $query = $this->db->query($sql);
                $kits = array_column ($query->rows, 'kit_id');

                
                
                if ($widget['main_mode']!='product_from_kit' || ($widget['main_mode']=='product_from_kit' && $widget['product_from_kit_mode_kits']=='linked_kits')) {
                    if (!empty($kits)) {
                        $kits_list = implode(',', $kits);

                        $sql = "SELECT bewk.kit_id FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (bewk.kit_id = be.kit_id) WHERE be.status = 1 AND bewk.widget_id = " . (int)$widget_id . " AND bewk.kit_id IN (" . $kits_list . ") GROUP BY kit_id";
                        $query = $this->db->query($sql);
                        $kits = array_column($query->rows, 'kit_id');
                    }
                }

                if($widget['main_mode']=='product_from_kit' && $widget['product_from_kit_mode_items']=='show_simple_kit'){

                }
            }else{
                
                $sql = "SELECT bewk.kit_id FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (bewk.kit_id = be.kit_id) WHERE be.status = 1 AND bewk.widget_id = " . (int)$widget_id . " GROUP BY kit_id";

                
                if($widget['display_mode']=='module_in_category'){
                    if(isset($this->request->get['route']) && $this->request->get['route'] == 'product/category'){
                        if(isset($this->request->get['path'])){
                            $parts = explode('_', (string)$this->request->get['path']);
                            if(count($parts)>0){
                                $current_category_id = $parts[count($parts)-1];
                                $sql = "SELECT bewk.kit_id FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (bewk.kit_id = be.kit_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_to_product betp ON (betp.kit_id = be.kit_id) WHERE be.status = 1 AND bewk.widget_id = '" . (int)$widget_id . "' AND betp.item_type='category' AND betp.item_id='".(int) $current_category_id."' GROUP BY kit_id";
                            }
                        }
                    }
                }
                $query = $this->db->query($sql);
                $kits = array_column ($query->rows, 'kit_id');
            }


            
            if(!empty($kits)){
                $kits_list = implode(',', $kits);

                $sql = "SELECT bekp.kit_id FROM " . DB_PREFIX . "bundle_expert_kit_period bekp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (bekp.kit_id = be.kit_id) WHERE bekp.kit_id IN (" . $kits_list . ") AND (bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR bekp.customer_group_id = -1000) AND (bekp.date_start = '0000-00-00' OR bekp.date_start <= CURDATE()) AND (bekp.date_end = '0000-00-00' OR bekp.date_end >= CURDATE()) ORDER BY be.sort_order";
                $query = $this->db->query($sql);
                $kits = array_column ($query->rows, 'kit_id');
            }

        }else{
        }

        $result = array();














        foreach ($kits as $kit_id){
            $kit_info = $this->getKit($kit_id);
            if($kit_info)
                $result[] = $kit_info;

        }

        return $result;
    }

    public function getWidgetKits_old2($widget_id, $filter_linked_products) {

        if(!$this->isEnabled()) return array();

        $filter_by_periods = true;
        

        if($filter_by_periods){
            $sql_01 = "SELECT COUNT(*) AS total1 FROM " . DB_PREFIX . "bundle_expert_kit_period bekp WHERE bekp.kit_id = bewk.kit_id AND bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'";
            $sql_02 = "SELECT COUNT(*) AS total2 FROM " . DB_PREFIX . "bundle_expert_kit_period bekp WHERE bekp.kit_id = bewk.kit_id AND bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (bekp.date_start = '0000-00-00' OR bekp.date_start <= CURDATE()) AND (bekp.date_end = '0000-00-00' OR bekp.date_end >= CURDATE()) ";

            if(empty($filter_linked_products)){
                $sql = "SELECT *, (".$sql_01.") AS kit_period_total, (".$sql_02.") AS kit_period_active FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) WHERE widget_id = '" . (int)$widget_id . "' ORDER BY be.sort_order";
            }else{
                $sql_03 = "SELECT COUNT(*) AS total3 FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products becklp WHERE becklp.kit_id = bewk.kit_id AND becklp.product_id IN (".$filter_linked_products.")";
                $sql_04 = "SELECT COUNT(*) AS total4 FROM " . DB_PREFIX . "bundle_expert_to_product betp WHERE betp.kit_id = bewk.kit_id";
                $sql = "SELECT *, (".$sql_01.") AS kit_period_total, (".$sql_02.") AS kit_period_active, (".$sql_03.") AS linked_products_count, (".$sql_04.") AS kit_to_product_count FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) WHERE widget_id = '" . (int)$widget_id . "' HAVING (kit_to_product_count = 0) OR (kit_to_product_count > 0 AND linked_products_count > 0)  ORDER BY be.sort_order";
            }

            $query = $this->db->query($sql);

            
            $sql_03 = "SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE product_id IN (".$filter_linked_products.")";
            $query = $this->db->query($sql_03);

            

            


        }else{
            if(empty($filter_linked_products)){
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) WHERE widget_id = '" . (int)$widget_id . "' ORDER BY be.sort_order");

            }else{
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_cache_kit_link_products becklp ON (be.kit_id = becklp.kit_id) WHERE widget_id = '" . (int)$widget_id . "' AND becklp.product_id IN (".$filter_linked_products.") GROUP BY bewk.kit_id ORDER BY be.sort_order ");

            }
        }


        $kits = array();

        foreach ($query->rows as $row){

            if($filter_by_periods) {
                if ($row['kit_period_total'] > 0 && $row['kit_period_active']==0 ) {
                    continue;
                }
            }


            $kit = $this->getKit($row['kit_id']);
            if($kit)
                $kits[] = $kit;

        }

        return $kits;
    }

    public function getWidgetKits_Old($widget_id) {

        if(!$this->isEnabled()) return array();

        $filter_by_periods = true;

        if($filter_by_periods){
            $sql_01 = "SELECT COUNT(*) AS total1 FROM " . DB_PREFIX . "bundle_expert_kit_period bekp WHERE bekp.kit_id = bewk.kit_id AND bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'";
            $sql_02 = "SELECT COUNT(*) AS total2 FROM " . DB_PREFIX . "bundle_expert_kit_period bekp WHERE bekp.kit_id = bewk.kit_id AND bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND (bekp.date_start = '0000-00-00' OR bekp.date_start <= CURDATE()) AND (bekp.date_end = '0000-00-00' OR bekp.date_end >= CURDATE()) ";

            $sql = "SELECT *, (".$sql_01.") AS kit_period_total, (".$sql_02.") AS kit_period_active FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) WHERE widget_id = '" . (int)$widget_id . "' ORDER BY be.sort_order";

            $query = $this->db->query($sql);
        }else{
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_kit bewk LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = bewk.kit_id) WHERE widget_id = '" . (int)$widget_id . "' ORDER BY be.sort_order");
        }


        $kits = array();

        foreach ($query->rows as $row){

            if($filter_by_periods) {
                if ($row['kit_period_total'] > 0 && $row['kit_period_active']==0 ) {
                    continue;
                }
            }

            $kit = $this->getKit($row['kit_id']);
            if($kit)
                $kits[] = $kit;
        }

        return $kits;
    }

    public function getWidgetDescriptions($widget_id){

        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "bundle_expert_widget_description WHERE widget_id = '" . (int)$widget_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getWidget($widget_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget WHERE widget_id = '" . (int)$widget_id . "'");

        $widget = $query->row;

        if($widget) {
            $widget['config_module'] = json_decode($widget['config_module'], true);
            $widget['config_product_page'] = json_decode($widget['config_product_page'], true);
            $widget['config_category_page'] = json_decode($widget['config_category_page'], true);
            $widget['config_custom_page'] = json_decode($widget['config_custom_page'], true);
            $widget['widget_width_mode'] = json_decode($widget['widget_width_mode'], true);
            $widget['table_mode_config'] = json_decode($widget['table_mode_config'], true);
            $widget['set_image_size_mode'] = json_decode($widget['set_image_size_mode'], true);
            $widget['background_image_size'] = json_decode($widget['background_image_size'], true);
            $widget['checkbox_mode'] = isset($widget['checkbox_mode'])? $widget['checkbox_mode']:0;
        }

        return $widget;
    }


    public function getCategoryWidgetKitsTotal($filter_data){

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_category_kits kts LEFT JOIN " . DB_PREFIX . "bundle_expert k ON (kts.kit_id = k.kit_id) WHERE kts.widget_id='" . (int)$filter_data['widget_id'] . "' AND kts.category_id='" . (int)$filter_data['category_id'] . "' AND k.status = 1 ORDER BY k.sort_order";

        $query = $this->db->query($sql);

        return $query->num_rows;

    }
    public function getCategoryWidgetKits($filter_data){

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_category_kits kts LEFT JOIN " . DB_PREFIX . "bundle_expert k ON (kts.kit_id = k.kit_id) WHERE kts.widget_id='" . (int)$filter_data['widget_id'] . "' AND kts.category_id='" . (int)$filter_data['category_id'] . "' AND k.status = 1 ORDER BY k.sort_order";

        if (isset($filter_data['start']) || isset($filter_data['limit'])) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;

    }

    public function getCategoryWidget($category_id){
        if(!$this->isEnabled()) return array();

        $widget = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_widget w LEFT JOIN " . DB_PREFIX . "bundle_expert_widget_to_store w2s ON (w.widget_id = w2s.widget_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_widget_to_category w2c ON (w.widget_id = w2c.widget_id) WHERE w.display_mode='category_list'  AND w2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND w2c.category_id = '" . (int)$category_id . "' AND w.status = 1 ORDER BY w.sort_order";

        $query = $this->db->query($sql);

        if($query->row){

            $widget = $query->row;
            $widget = $this->widgetInfoConvert($widget);

        }

        return $widget;

    }

    public function widgetInfoConvert($widget){
        $widget['config_module'] = json_decode($widget['config_module'], true);
        $widget['config_product_page'] = json_decode($widget['config_product_page'], true);
        $widget['config_category_page'] = json_decode($widget['config_category_page'], true);
        $widget['config_custom_page'] = json_decode($widget['config_custom_page'], true);
        $widget['widget_width_mode'] = json_decode($widget['widget_width_mode'], true);
        $widget['table_mode_config'] = isset($widget['table_mode_config'])? json_decode($widget['table_mode_config'], true):null;
        if($widget['table_mode_config'])
            $widget['table_mode_config']['free_product_table_mode'] = 1;
        $widget['set_image_size_mode'] = json_decode($widget['set_image_size_mode'], true);
        $widget['background_image_size'] = json_decode($widget['background_image_size'], true);
        $widget['slider_mode'] = isset($widget['slider_mode'])? $widget['slider_mode']:1;
        $widget['checkbox_mode'] = isset($widget['checkbox_mode'])? $widget['checkbox_mode']:0;
        $widget['product_click_mode'] = isset($widget['product_click_mode'])? $widget['product_click_mode']:'default';
        $widget['slider_autoplay_status'] = isset($widget['slider_autoplay_status'])? $widget['slider_autoplay_status']:0;
        $widget['slider_autoplay_time'] = isset($widget['slider_autoplay_time'])? $widget['slider_autoplay_time']:3000;

        $widget['main_mode'] = isset($widget['main_mode'])? $widget['main_mode']:'product_from_kit';
        $widget['main_mode'] = isset($widget['main_mode'])? $widget['main_mode']:'default';
        $widget['product_from_kit_mode_items'] = isset($widget['product_from_kit_mode_items'])? $widget['product_from_kit_mode_items']:'link_to_product_page';
        $widget['product_from_kit_mode_items'] = isset($widget['product_from_kit_mode_items'])? $widget['product_from_kit_mode_items']:'show_simple_kit';
        $widget['product_from_kit_mode_kits'] = isset($widget['product_from_kit_mode_kits'])? $widget['product_from_kit_mode_kits']:'all_kits';
        $widget['product_from_kit_mode_kits'] = isset($widget['product_from_kit_mode_kits'])? $widget['product_from_kit_mode_kits']:'linked_kits';
        $widget['product_from_kit_mode_product_source'] = isset($widget['product_from_kit_mode_product_source'])? $widget['product_from_kit_mode_product_source']:'from_page';


        return $widget;
    }

    
    
    
    public function getWidgetsByDisplayMode($display_mode, $data){

        if(!$this->isEnabled()) return array();

        if($display_mode!=="module_in_category"){
            $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_widget w LEFT JOIN " . DB_PREFIX . "bundle_expert_widget_to_store w2s ON (w.widget_id = w2s.widget_id) WHERE w.display_mode='". $this->db->escape($display_mode) ."'  AND w2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND w.status = 1 ORDER BY w.sort_order";
        }else{
            $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_widget w LEFT JOIN " . DB_PREFIX . "bundle_expert_widget_to_store w2s ON (w.widget_id = w2s.widget_id) WHERE w.display_mode='". $this->db->escape($display_mode) ."'  AND w2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND w.status = 1 AND w.widget_id = '".(int)$data['widget_id']."' ORDER BY w.sort_order";
        }


        $query = $this->db->query($sql);

        $widgets = array();

        foreach ($query->rows as $widget){
            $widget = $this->widgetInfoConvert($widget);

            
            if ($widget['display_mode'] == 'module' && isset($data['bundle_expert_page_module'])) {
                if ($widget['widget_id'] != $data['bundle_expert_page_module']['widget_id'])
                    continue;

            }
            if ($widget['display_mode'] == 'module_in_category' && isset($data['bundle_expert_page_module_in_category'])) {
                if ($widget['widget_id'] != $data['bundle_expert_page_module_in_category']['widget_id'])
                    continue;

            }
            if ($widget['display_mode'] == 'custom_page') {
                
                $check_url = $this->checkCustomPageUrl($widget);

                if (!$check_url) {
                    continue;
                }
            }

            $filter_linked_products_list = array();

            if($widget['widget_id']=='38'){
                $stop=1;
            }
            
            if($widget['main_mode'] !== 'product_from_kit'){
                foreach ($data['products'] as $product){
                    $filter_linked_products_list[$product['product_id']] = $product['product_id'];
                }
            }
            if($widget['main_mode'] == 'product_from_kit'){
                if( $widget['product_from_kit_mode_product_source']=='from_page') {
                    foreach ($data['products'] as $product) {
                        $filter_linked_products_list[$product['product_id']] = $product['product_id'];
                    }
                }else{
                    $linked_products_list = $this->dbCacheWidgetLinkProductsGet($widget['widget_id']);
                    foreach ($linked_products_list as $product_id){
                        $filter_linked_products_list[$product_id] = $product_id;
                        if($display_mode=='module' || $display_mode=='custom_page'){
                            $data['products'][$product_id] = array('product_id'=>$product_id);
                        }
                    }
                }
            }




            $filter_linked_products = implode(',', $filter_linked_products_list);







            $kits = $this->getWidgetKits($widget['widget_id'], $filter_linked_products, $widget);

            

            $get_kit_products_settings = array(
                'main_product_id' => '',
                'filter_kit_items' => true,
                'only_first' => false,
                'admin_mode' => false,
                
                
                
                
            );

            $ocmod_point_001 = 1;

            
            if ($widget['checkbox_mode']) {
                $get_kit_products_settings['limit_product_process_for_widget_checkbox_mode'] = true;
            }

            foreach ($kits as $kit_info) {
                if ($kit_info) {

                    
                    if($widget['main_mode']=='product_from_kit' && $widget['product_from_kit_mode_items']=='show_simple_kit'){
                        if($kit_info['kit_as_product'] || $kit_info['kit_as_product_light_mode']){
                            continue;
                        }
                    }

                    
                    if($widget['main_mode']=='product_from_kit' && $widget['product_from_kit_mode_items']!='show_simple_kit'){
                        $kit_info['kit_as_product'] = 0;
                        $kit_info['kit_as_product_light_mode'] = 0;
                    }


                    $widget['kit_info'] = $kit_info;
                    $widget['main_product_in_cart'] = false;
                    $widget['cart_merge_enable'] = false;

                    
                    
                    if($widget['display_mode']=='module_in_category'){

                    }else{
                        if($widget['main_mode']=='product_from_kit'){
                            $link_products = array();
                            $link_products = $filter_linked_products_list;
                        }else{
                            $link_products = $this->getKitLinkProducts2($kit_info['kit_id']);

                            
                            if($kit_info['kit_as_product'] && empty($link_products)) {
                                continue;
                            }
                        }
                    }









                    switch ($display_mode) {
                        case 'product_page':
                        case 'category_page':


                            
                            if ($link_products) {
                                foreach ($data['products'] as $product) {
                                    
                                    $is_link_product = in_array($product['product_id'],$link_products);

                                    if ($is_link_product) {
                                        $widget['unique_id'] = uniqid();
                                        $widget['main_product_id'] = $product['product_id'];
                                        $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                        if ($this->bundle_expert_settings['cache_widgets']) {
                                            $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                            if ($kit_enable_status_step_2){
                                                $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                                if ($kit_enable_status_step_1==-1) {
                                                    $widgets[] = $widget;
                                                }
                                            }
                                        }else{
                                            $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                                            $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                            if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                                $widgets[] = $widget;
                                        }


                                    }
                                }
                            } else {
                                $widget['unique_id'] = uniqid();
                                $widget['main_product_id'] = '';
                                
                                
                                if($kit_info['kit_mode']=='auto_list'){
                                    if (isset($this->request->get['product_id'])) {
                                        $main_product_id = (int)$this->request->get['product_id'];
                                        $widget['main_product_id'] = $main_product_id;
                                    }
                                }
                                

                                $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                if ($this->bundle_expert_settings['cache_widgets']) {
                                    $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                    if ($kit_enable_status_step_2){
                                        $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                        if ($kit_enable_status_step_1==-1) {
                                            $widgets[] = $widget;
                                        }
                                    }
                                }else {
                                    $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                                    $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                    if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                        $widgets[] = $widget;

                                }
                            }

                        
                        
                        if($kit_info['kit_mode']=='auto_list'){
                            $empty = true;
                            if(isset($widget['kit_info']['kit_items'])){
                                foreach ($widget['kit_info']['kit_items'] as $kit_item){
                                    if(!empty($kit_item['products'])){
                                        $empty = false;
                                        break;
                                    }
                                }
                            }
                            if($empty){
                                unset($widgets[count($widgets)-1]);
                            }
                        }
                        

                            break;

                        case 'custom_page':







                        case 'module':
                            
                            if ($link_products) {

                                $first_option_id = 'dc65e5e0764e444ec837';

                                
                                if (($widget['display_mode'] == "module" && $widget['config_module']['cart_free_product_mode'])
                                    || ($widget['display_mode'] == "custom_page" && $widget['config_module']['cart_free_product_mode'])) {
                                    foreach ($data['cart_free_products'] as $product) {
                                        

                                        $is_link_product = in_array($product['product_id'],$link_products);

                                        if ($is_link_product) {
                                            $widget['unique_id'] = uniqid();
                                            $widget['main_product_id'] = $product['product_id'];
                                            $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                            $widget['main_product_in_cart'] = true;
                                            $widget['cart_merge_enable'] = true;
                                            if ($this->bundle_expert_settings['cache_widgets']) {
                                                $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                                if ($kit_enable_status_step_2){
                                                    $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                                    if ($kit_enable_status_step_1==-1) {
                                                        $widgets[] = $widget;
                                                    }
                                                }
                                            }else {
                                                $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings, (int)$product['product_id']);
                                                $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id'], $widget['main_product_in_cart']);
                                                if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                                    $widgets[] = $widget;
                                            }
                                        }
                                    }
                                }else{
                                    foreach ($data['products'] as $product) {
                                        
                                        $is_link_product = in_array($product['product_id'],$link_products);

                                        if ($is_link_product) {
                                            $widget['unique_id'] = uniqid();
                                            $widget['main_product_id'] = $product['product_id'];
                                            $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                            if ($this->bundle_expert_settings['cache_widgets']) {
                                                $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                                if ($kit_enable_status_step_2){
                                                    $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                                    if ($kit_enable_status_step_1==-1) {
                                                        $widgets[] = $widget;
                                                    }
                                                }
                                            }else {
                                                $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                                                $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                                if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                                    $widgets[] = $widget;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $widget['unique_id'] = uniqid();
                                $widget['main_product_id'] = '';
                                $second_option_id = '70604031a6e68c8fa6be';
                                $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                if ($this->bundle_expert_settings['cache_widgets']) {
                                    $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                    if ($kit_enable_status_step_2){
                                        $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                        if ($kit_enable_status_step_1==-1) {
                                            $widgets[] = $widget;
                                        }
                                    }
                                }else {
                                    $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                                    $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                    if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                        $widgets[] = $widget;
                                }
                            }

                            break;
                        case 'module_in_category':
                            

                                $widget['unique_id'] = uniqid();
                                $widget['main_product_id'] = '';
                                $second_option_id = '70604031a6e68c8fa6be';
                                $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];
                                if ($this->bundle_expert_settings['cache_widgets']) {
                                    $kit_enable_status_step_2 = $this->getKitEnableStatusFromCache($widget, $get_kit_products_settings);
                                    if ($kit_enable_status_step_2){
                                        $kit_enable_status_step_1 = $this->getKitEnableStatus_step1($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                        if ($kit_enable_status_step_1==-1) {
                                            $widgets[] = $widget;
                                        }
                                    }
                                }else {
                                    $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
                                    $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
                                    if ($widget['kit_info']['kit_enable_status']['display_kit'])
                                        $widgets[] = $widget;
                                }





                            break;
                    }


                }
            }

        }

        if (isset($first_option_id) && isset($second_option_id))
            unset($second_option_id);


        foreach ($widgets as $widget){
            $this->addWidgetHistoryStatus($widget['widget_id'], 'success');
        }

        return $widgets;
    }

    public function getKitLinkCurrentCategory($kit_id){
        if(!$this->isEnabled()) return array();



        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "' AND item_type = 'category' ORDER BY id");

        $link_categories = $query->rows;

        return $link_categories;
    }
    
    

    public function getKitLinkProducts2($kit_id){
        if(!$this->isEnabled()) return array();

        $link_products = array();

        if($this->cache_to_file) {
            $link_products = $this->dbCacheKitLinkProductsGet($kit_id);
        }
        

        if(!$link_products){

            $kit_info = $this->getKitInfo($kit_id);

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "' ORDER BY id");

            foreach ($query->rows as $index1=>$item_info){
                $product_list = array();

                switch ($item_info['item_type']){
                    case 'product':
                        $product_list[$item_info['item_id']] = $item_info['item_id'];
                        break;
                    case 'category':
                        $filter_data = array(
                            'filter_category_id' => $item_info['item_id'],
                            'filter_filter' => '',
                            'sort' => '',
                            'order' => '',
                            'start' => 0,
                            'limit' => 100000
                        );
                        $products = $this->getProductsByCategory($filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }

                        break;
                    case 'filter':
                        $filter_data = array(
                            'filter_name' => '',
                        );
                        $products = $this->getProductsByFilter($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'manufacturer':
                        $filter_data = array(
                            'filter_name' => '',
                        );
                        $products = $this->getProductsByManufacturer($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'model':
                    case 'sku':
                    case 'upc':
                    case 'ean':
                    case 'jan':
                    case 'isbn':
                    case 'mpn':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByField($filter_data, $item_info['item_type']);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'group':
                    case 'group_kit':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByGroup($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    default:
                        break;
                }

                switch ($kit_info['link_products_combine_mode']){
                    case 'intersect':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            $link_products = array_intersect_key($link_products, $product_list);
                        }
                        break;
                    case 'union':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            foreach ($product_list as $product_id){
                                if(!isset($link_products[$product_id])){
                                    $link_products[$product_id] = $product_id;
                                }
                            }
                        }
                        break;
                    case 'subtract':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            foreach ($product_list as $product_id){
                                if(isset($link_products[$product_id])){
                                    unset($link_products[$product_id]);
                                }
                            }
                        }
                        break;
                    default:
                        break;
                }

            }



            $ocmod_point_009 = 1;

            if($this->cache_to_file) {
                $this->dbCacheKitLinkProductsAdd($kit_id, $kit_info, $link_products);
            }
            
        }




        return $link_products;
    }
    public function getWidgetLinkProducts($widget_id){
        if(!$this->isEnabled()) return array();

        $link_products = array();

        if($this->cache_to_file) {
            $link_products = $this->dbCacheWidgetLinkProductsGet($widget_id);
        }

        if(!$link_products){

            $widget = $this->getWidget($widget_id);

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_to_product WHERE widget_id = '" . (int)$widget_id . "' ORDER BY id");

            foreach ($query->rows as $index1=>$item_info){
                $product_list = array();

                switch ($item_info['item_type']){
                    case 'product':
                        $product_list[$item_info['item_id']] = $item_info['item_id'];
                        break;
                    case 'category':
                        $filter_data = array(
                            'filter_category_id' => $item_info['item_id'],
                            'filter_filter' => '',
                            'sort' => '',
                            'order' => '',
                            'start' => 0,
                            'limit' => 100000
                        );
                        $products = $this->getProductsByCategory($filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }

                        break;
                    case 'filter':
                        $filter_data = array(
                            'filter_name' => '',
                        );
                        $products = $this->getProductsByFilter($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'manufacturer':
                        $filter_data = array(
                            'filter_name' => '',
                        );
                        $products = $this->getProductsByManufacturer($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'model':
                    case 'sku':
                    case 'upc':
                    case 'ean':
                    case 'jan':
                    case 'isbn':
                    case 'mpn':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByField($filter_data, $item_info['item_type']);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    case 'group':
                    case 'group_kit':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByGroup($item_info['item_id'], $filter_data);
                        foreach ($products as $product){
                            $product_list[$product['product_id']] = $product['product_id'];
                        }
                        break;
                    default:
                        break;
                }

                switch ($widget['link_products_combine_mode']){
                    case 'intersect':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            $link_products = array_intersect_key($link_products, $product_list);
                        }
                        break;
                    case 'union':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            foreach ($product_list as $product_id){
                                if(!isset($link_products[$product_id])){
                                    $link_products[$product_id] = $product_id;
                                }
                            }
                        }
                        break;
                    case 'subtract':
                        if($index1 == 0){
                            $link_products = $product_list;
                        }else{
                            foreach ($product_list as $product_id){
                                if(isset($link_products[$product_id])){
                                    unset($link_products[$product_id]);
                                }
                            }
                        }
                        break;
                    default:
                        break;
                }

            }

            if($this->cache_to_file) {
                $this->dbCacheWidgetLinkProductsAdd($widget_id, $link_products);
            }
            
        }




        return $link_products;
    }

    public function getKitLinkProducts2_old($kit_id)
    {





















































































    }


    public function checkIsLinkProduct($product_id, $kit_id){
        $is_link_product = false;

        $product_categories = implode(',', $this->getProductCategories($product_id));
        $product_manufacturers = implode(',', $this->getProductManufacturers($product_id));
        $product_filters = implode(',', $this->getProductFilters($product_id));

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id='" . (int)$kit_id . "' AND ((item_type='product' AND item_id='" . (int)$product_id . "') ";

        if (!empty($product_categories)) {
            $sql .= "OR (item_type='category' AND item_id IN (" . $product_categories . ")) ";
        }

        if (!empty($product_manufacturers)) {
            $sql .= "OR (item_type='manufacturer' AND item_id IN (" . $product_manufacturers . ")) ";
        }

        if (!empty($product_filters)) {
            $sql .= "OR (item_type='filter' AND item_id IN (" . $product_filters . ")) ";
        }

        $sql .= " )";

        $query = $this->db->query($sql);
        if ($query->rows) {
            $is_link_product = true;
        }

        return $is_link_product;
    }

    
    public function checkCustomPageUrl($widget){
        $check = false;

        $this->load->model('design/layout');

        
        $config_url = $widget['config_custom_page']['custom_page_url'];
        $parse_url = parse_url($config_url);
        $config_url = $parse_url['path'];
        if (isset($parse_url['query'])) $config_url .= '?' . $parse_url['query'];
        $config_url = html_entity_decode($config_url);

        
        $current_url = htmlspecialchars_decode($this->request->server['REQUEST_URI']);

        $config_url = strtolower($config_url);
        $current_url = strtolower($current_url);

        if ($widget['config_custom_page']['custom_page_mode'] == 'url' && $config_url == $current_url) {
            $check = true;
        }

        
        if(isset($this->request->get['route']) && isset($this->request->get['information_id'])){
            $current_url = "/index.php?route=information/information&information_id=" . $this->request->get['information_id'];
        }
        $current_url = htmlspecialchars_decode($current_url);

        $config_url = strtolower($config_url);
        $current_url = strtolower($current_url);

        if ($widget['config_custom_page']['custom_page_mode'] == 'url' && strpos($config_url, $current_url, 0)!==false) {
            
            
        }

        
        if (isset($this->request->get['route'])) {
            $route = (string)$this->request->get['route'];
        } else {
            $route = 'common/home';
        }
        $layout_id = $this->model_design_layout->getLayout($route);
        if ($widget['config_custom_page']['custom_page_mode'] == 'layout' && $widget['config_custom_page']['custom_page_layout_id'] == $layout_id) {
            $check = true;
        }

        return $check;
    }



    public function getKitLinkProducts($kit_id) {

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_to_product WHERE kit_id = '" . (int)$kit_id . "'");

        return $query->rows;

    }


    private function getKitItemsCount($kit_id){
        $query = $this->db->query("SELECT item_position FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "' GROUP BY item_position");

        return $query->num_rows;
    }

    private function cacheKitProductsInfo($products){
        $products_list = array();
        foreach ($products as $product){
            if(!isset($this->kit_products_cache[$product['product_id']])){
                $products_list[] = $product['product_id'];
            }
        }

        if($products_list){
            $data_list = $this->getProductListInfoDefault($products_list);
            foreach ($data_list as $product){
                if(!isset($this->kit_products_cache[$product['product_id']])){
                    $this->kit_products_cache[$product['product_id']] = $product;
                }
            }
        }

    }

    public function checkProductInfo(&$item){
        if(!isset($item['product_info'])){
            $item['product_info'] = $this->getProductInfo($item['product_id']);

            $options = $this->getProductOptions($item['product_id']);;

            $item['product_info']['options'] = $options;
        }
    }

    public function getProductInfo($product_id){
        if(isset($this->kit_products_cache[$product_id])){
            $product_info = $this->kit_products_cache[$product_id];
        }else{
            $product_info = $this->getProductInfoDefault($product_id);
            $this->kit_products_cache[$product_id] = $product_info;
        }

        return $product_info;
    }

    private function cacheProductsOptions($products){
        $products_list = array();
        foreach ($products as $product){
            if(!isset($this->kit_options_cache[$product['product_id']])){
                $products_list[] = $product['product_id'];
            }
        }

        if($products_list){
            $data_list = $this->getProductListOptions($products_list);
            foreach ($data_list as $options){
                if(!isset($this->kit_options_cache[$product['product_id']])){
                    $this->kit_options_cache[$product['product_id']] = $options;
                }
            }
        }

    }

    public function getProductOptions($product_id){
        if(isset($this->kit_options_cache[$product_id])){
            $options = $this->kit_options_cache[$product_id];
        }else{
            $options = $this->model_catalog_product->getProductOptions($product_id);
            $this->kit_options_cache[$product_id] = $options;
        }

        return $options;
    }

    private function intersect_arrays($v1,$v2)
    {
        $new_array = array();

        foreach ($v2 as $item_2){
            foreach ($v1 as $item_1){
                if($item_1['product_id'] == $item_2['product_id']){
                    $new_array[] = $item_2;
                    break;
                }
            }
        }

        return $new_array;
    }



    public function getKitItems($kit_id, $filter_title='', $index_array=true){

        if (!$this->isEnabled()) return array();

        
        if (isset($this->kit_items_cache[$kit_id]) && $filter_title == '') {
            $kit_items = $this->kit_items_cache[$kit_id]['kit_items'];
        }else{
            if($this->cache_to_file){
                
                $kit_items = $this->dbCacheKitItemsGet($kit_id);
                if(!$kit_items) {
                    $kit_items = null;
                }else{
                    $cache_kit = true;
                    if(!$filter_title) {
                        if($cache_kit) {
                            $this->kit_items_cache[$kit_id] = array(
                                'kit_id' => $kit_id,
                                'kit_items' => $kit_items
                            );
                        }
                    }
                }
            }
        }

        if (!isset($kit_items)) {
            $kit_items = array();

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "' ORDER BY main DESC, item_position ASC, id");

            $items_data_array = array();

            foreach ($query->rows as $row) {
                $item_info = $row;

                $item_position = $item_info['item_position'];

                if(!isset($kit_items[$item_position]))
                    $kit_items[$item_position] = array();

                $products = array();

                
                
                
                switch ($item_info['item_type']){
                    case 'product':
                        $filter_data = array(
                            'filter_name' => $filter_title,
                        );
                        $products = $this->getProductsByProductId($item_info['item_id'], $filter_data);
                        break;
                    case 'category':
                        $filter_data = array(
                            'filter_category_id' => $item_info['item_id'],
                            'filter_filter' => '',
                            'filter_name' => $filter_title,
                            'sort' => '',
                            'order' => '',
                            'start' => 0,
                            'limit' => 100000
                        );
                        $products = $this->getProductsByCategory($filter_data);
                        break;
                    case 'manufacturer':
                        $filter_data = array(
                            'filter_name' => $filter_title,
                        );
                        $products = $this->getProductsByManufacturer($item_info['item_id'], $filter_data);
                        break;
                    case 'filter':
                        $filter_data = array(
                            'filter_name' => $filter_title,
                        );
                        $products = $this->getProductsByFilter($item_info['item_id'], $filter_data);
                        break;
                    case 'model':
                    case 'sku':
                    case 'upc':
                    case 'ean':
                    case 'jan':
                    case 'isbn':
                    case 'mpn':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByField($filter_data, $item_info['item_type']);
                        break;
                    case 'group':
                    case 'group_kit':

                        $filter_data = array(
                            'filter_name' => htmlspecialchars($item_info['item_value']),
                            'start' => 0,
                            'limit' => 1000000000
                        );
                        $products = $this->getProductsByGroup($item_info['item_id'], $filter_data);
                        break;

                        
                    case 'auto_attribute':
                        if (isset($this->request->get['product_id'])) {
                            $main_product_id = $this->request->get['product_id'];
                            $attribute_id = $item_info['item_id'];
                            $attribute_value = $this->getProductAttributeValue($main_product_id, $attribute_id);
                            if(isset($attribute_value)){
                                $filter_data = array(
                                    'attribute_id' => $attribute_id,
                                    'attribute_value' => $attribute_value,
                                    'limit' => 100000
                                );
                                $products = $this->getProductsByAttribute($filter_data);
                            }
                        }
                        break;
                    case 'auto_model':
                    case 'auto_sku':
                    case 'auto_upc':
                    case 'auto_ean':
                    case 'auto_jan':
                    case 'auto_isbn':
                    case 'auto_mpn':
                        if(isset($this->request->get['product_id'])){
                            $main_product_id = $this->request->get['product_id'];
                            $main_product_info = $this->getProductInfo($main_product_id);
                            $item_type = str_replace('auto_', '', $item_info['item_type']);

                            $item_value = $main_product_info[$item_type];
                            $filter_data = array(
                                'filter_name' => htmlspecialchars($item_value),
                                'start' => 0,
                                'limit' => 1000000000
                            );
                            $products = $this->getProductsByField($filter_data, $item_type);
                        }

                        break;
                        
                    case '':
                        break;
                }


                
                $item_info['hide_special_products'] = (isset($item_info['hide_special_products'])) ? $item_info['hide_special_products'] : false;

                $item_info['products_combine_mode'] = (isset($item_info['products_combine_mode'])) ? $item_info['products_combine_mode'] : 'union';

                $item_info['custom_field'] = (isset($item_info['custom_field'])) ? $item_info['custom_field'] : '';


                $item_info['item_info_unique_id'] = uniqid('', true);

                foreach ($products as $index=>$product){
                    $products[$index]['item_info'] = $item_info;
                }

                $items_data_array[$item_position]['products'][] = $products;
                $items_data_array[$item_position]['item_info'] = $item_info;

            }



            foreach ($items_data_array as $item_position=>$item_data_array) {
                $item_info = $item_data_array['item_info'];

                if($item_info['item_mode']=='fix_product'){
                    $products = array();
                    foreach ($item_data_array['products'] as $product_list){
                        $products = array_merge($products, $product_list);
                    }
                }else{
                    switch ($item_info['products_combine_mode']){
                        case 'intersect':
                            $products = array();
                            foreach ($item_data_array['products'] as $index1=>$product_list){
                                if($index1 == 0){
                                    $products = $product_list;
                                }else{
                                    $products = $this->intersect_arrays($products, $product_list);
                                }
                            }
                            break;
                        case 'union':
                            $products = array();
                            foreach ($item_data_array['products'] as $product_list){
                                $products = array_merge($products, $product_list);
                            }
                            break;
                        case 'subtract':
                            $products = array();
                            foreach ($item_data_array['products'] as $index1=>$product_list){
                                if($index1 == 0){
                                    $products = $product_list;
                                }else{
                                    foreach ($product_list as &$product_list_item){
                                        $product_id = $product_list_item['product_id'];
                                        foreach ($products as $ind=>$product){
                                            if($product['product_id']==$product_id){
                                                unset($products[$ind]);
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }

                $ocmod_point_002 = 1;

                foreach ($products as $product) {
                    $item_info = $product['item_info'];

                    $product_id = $product['product_id'];

                    
                    $show_product_in_kit = true;

                    if(($product['special'] && $item_info['hide_special_products']) && $item_info['item_mode']!='fix_product') {
                        $show_product_in_kit = false;
                    }

                    if($show_product_in_kit){

                        if(!isset($kit_items[$item_position][$product_id])) {

                            $item_data = array(
                                'product_id' => $product_id,
                                'quantity' => $item_info['quantity'],
                                'quantity_edit' => $item_info['quantity_edit'],
                                'price_mode' => $item_info['price_mode'],
                                'item_mode' => $item_info['item_mode'],
                                'price' => $item_info['price'],
                                'price_minus_sum' => $item_info['price_minus_sum'],
                                'price_minus_percent' => $item_info['price_minus_percent'],
                                
                                'price_mode_to_customer_groups' => isset($item_info['price_mode_to_customer_groups']) ? json_decode($item_info['price_mode_to_customer_groups'], true) : array(),
                                'price_mode_to_customer_groups_status' => isset($item_info['price_mode_to_customer_groups_status']) ? $item_info['price_mode_to_customer_groups_status'] : false,
                                
                                'main' => $item_info['main'],
                                'disable_options' => json_decode($item_info['disable_options'], 1),
                                'fixed_options' => json_decode($item_info['fixed_options'], 1),
                                'enabled_options' => isset($item_info['enabled_options']) && !empty($item_info['enabled_options']) ? json_decode($item_info['enabled_options'], 1) : array(),
                                'item_empty_mode' => isset($item_info['item_empty_mode']) && !empty($item_info['item_empty_mode']) ? json_decode($item_info['item_empty_mode'], 1) : array('enable' => 0, 'default_empty' => 0, 'title' => ''),
                                'is_free_product' => ($item_info['item_mode'] == "free_product") ? 1 : 0,
                                'free_product_default_in_kit' => isset($item_info['free_product_default_in_kit']) ? $item_info['free_product_default_in_kit'] : 1,
                                'hide_special_products' => $item_info['hide_special_products'],
                                'products_combine_mode' => $item_info['products_combine_mode'],
                                'randomize_select_product' => isset($item_info['randomize_select_product']) ? $item_info['randomize_select_product'] : 0,
                                'empty_group_image' => isset($item_info['empty_group_image']) ? $item_info['empty_group_image'] : '',
                                'custom_field' => isset($item_info['custom_field']) ? $item_info['custom_field'] : '',

                                'product_info' => null
                            );

                            
                            if(is_array($item_data['item_empty_mode']['title'])){
                                $item_data['item_empty_mode']['title_by_language_code'] = $item_data['item_empty_mode']['title'];
                            }
                            

                            if(is_array($item_data['item_empty_mode']['title'])){
                                $item_data['item_empty_mode']['title'] = $item_data['item_empty_mode']['title'][$this->config->get('config_language_id')];
                            }

                            
                            if(!isset($item_data['item_empty_mode']['not_empty_in_cart'])){
                                $item_data['item_empty_mode']['not_empty_in_cart'] = 0;
                            }
                            

                            if($item_info['item_mode']=="fix_product"){
                                $item_data['item_empty_mode']['enable'] = 0;
                            }

                            if($item_data['is_free_product'] && $item_data['free_product_default_in_kit']){
                                $item_data['free_product_in_kit'] = 1;
                            }else{
                                $item_data['free_product_in_kit'] = 0;
                            }

                            $kit_items[$item_position][$product_id]['product_id'] = $product_id;
                            $kit_items[$item_position][$product_id]['kit_id'] = $kit_id;
                            $kit_items[$item_position][$product_id]['item_info_unique_id'] = $item_info['item_info_unique_id'];
                            $kit_items[$item_position][$product_id]['hide_product'] = false;







                            $ocmod_point_003 = 1;

                            unset($item_data['product_id']);

                            if(!isset($this->kit_items_info_cache[$kit_id][$item_info['item_info_unique_id']])) {
                                $this->kit_items_info_cache[$kit_id][$item_info['item_info_unique_id']] = $item_data;
                            }
                        }
                    }

                }
            }

            
            
            


















            




            foreach ($kit_items as $index1=>$item){
                
            }














            
            
            foreach ($kit_items as $index1=>$item){
                foreach ($item as $index2=>$item_product){
                    $item_info = $this->kit_items_info_cache[$kit_id][$item_product['item_info_unique_id']];
                    if($item_info['item_mode']!="fix_product" && $item_info['main']!=1){
                        $product_info = $this->getProductInfo($item_product['product_id']);
                        if($product_info['quantity']<=0){
                            unset($kit_items[$index1][$index2]);
                            $kit_items[$index1][$item_product['product_id']] = $item_product;
                        }
                    }

                }
            }

            
            
            $cache_kit = true;
            if (!$filter_title) {
                $kit_info = $this->getKit($kit_id);
                if ($kit_info['kit_mode'] != 'auto_list') {
                    if ($cache_kit) {
                        $this->kit_items_cache[$kit_id] = array(
                            'kit_id' => $kit_id,
                            'kit_items' => $kit_items
                        );
                    }

                    if ($this->cache_to_file) {
                        $this->dbCacheKitItemsAdd($kit_id, $kit_items);
                        
                    }
                }

            }
        }



        
        foreach ($kit_items as $index1=>$item){
            if(count($item)>0){
                $first_item = reset($item);
                $item_info = $this->kit_items_info_cache[$first_item['kit_id']][$first_item['item_info_unique_id']];
                if($item_info['item_mode']=='select_product'){
                    if($item_info['randomize_select_product']){
                        shuffle($item);
                        $kit_items[$index1] = $item;
                    }
                }
            }
        }

        if($index_array){
            foreach ($kit_items as $index1=>$item){
                $kit_items[$index1] = array_values( $item);
            }
        }

        

        return $kit_items;

    }



    
    public function getFreeItemProducts($kit_id){
        if (!$this->isEnabled()) return array();

        $kit_items = $this->getKitItems($kit_id);

        $free_products = array();

        foreach ($kit_items as $item_position => $kit_item) {
            foreach ($kit_item as $item_product) {
                $product_id = $item_product['product_id'];

                $this->itemInfoCheck($item_product);
                $item_info = $item_product['item_info'];
                if ($item_info['is_free_product']) {
                    $product_info = $this->getProductInfo($product_id);
                    $item_data = $item_info;
                    if ($product_info) {

                        $item_data['product_info'] = $product_info;

                        $options = $this->getProductOptions($product_id);;

                        $item_data['product_info']['options'] = $this->filterProductOptions($item_data, $options);

                        $product_info['options'] = $item_data['product_info']['options'];
                        $item_data['product_info'] = $product_info;

                        $free_products[$item_data['product_id']] = $item_data;

                    }
                }
            }
        }

        $free_products = array_values($free_products);

        return $free_products;

    }


    private function isFreeItem($kit_id, $item_position){
        $is_free_item = false;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_items WHERE kit_id = '" . (int)$kit_id . "' AND item_position = '".$item_position."' LIMIT 1");

        if($query->num_rows){
            $item_info = $query->row;
            if($item_info['item_mode']=='free_product'){
                $is_free_item = true;
            }
        }

        return $is_free_item;
    }

    
    public function getKitItemProducts($kit_id, $settings, $item_position, &$products_quantity, &$options_quantity, &$filter = array(), $free_products_get_all=true, $free_products_skip=array()) {

        if (!$this->isEnabled()) return array();

        $kit_item = array();
        $kit_item['products'] = array();
        $kit_item['selectable'] = false;
        $kit_item['item_position'] = $item_position;

        $kit_item['is_free_product']  = $this->isFreeItem($kit_id, $item_position);

        $current_product_id = $settings['main_product_id'];
        $filter_kit_items = $settings['filter_kit_items'];
        $only_first = $settings['only_first'];
        $admin_mode = $settings['admin_mode'];

        if (isset($filter['filter_title']))
            $filter_title = $filter['filter_title'];
        else
            $filter_title = '';

        $kit_items = $this->getKitItems($kit_id, $filter_title, false);

        $products_list = $kit_items[$item_position];

        
        $kit_info = $this->getKit($kit_id);
        if(!empty($kit_info)){
            if($kit_info['main_mode']=="series" || $kit_info['main_mode']=="collection"){
                if($kit_info['series_mode']=='no_main'){
                    foreach ($products_list as $index1=>$item){
                        if($index1==$current_product_id){
                            unset($products_list[$index1]);
                        }

                    }
                }

            }
        }
        

        if (isset($filter['last_item_product_position']))
            $last_item_product_position = $filter['last_item_product_position'];
        else
            $last_item_product_position = 0;

        if (isset($filter['limit']))
            $limit = $filter['limit'];
        else
            $limit = count($products_list);


        
        
        if(isset($settings['limit_product_process_for_widget_checkbox_mode'])){
            $filter_limit = 100000;
        }else{
            $filter_limit = 10;
        }

        

        if ($filter_kit_items) {


        }

        
        
        if (count($products_list) > 0) {
            $first_item = reset($products_list);

            $this->itemInfoCheck($first_item);
            $item_info = $first_item['item_info'];
            if ($item_info['main'] && !$admin_mode) {
                $ocmod_point_005 = 1;
                $new_products_list = array();
                if (isset($products_list[$current_product_id])) {
                    $new_products_list[] = $products_list[$current_product_id];
                }
                $products_list = $new_products_list;
            }
        }

        $products_list = array_values($products_list);

        foreach ($products_list as $index => $item_product) {

            $ocmod_point_004 = 1;

            if(isset($settings['skip_hidden_product']) && $settings['skip_hidden_product']){
                if(isset($item_product['hide_product']) && $item_product['hide_product']){
                    continue;
                }
            }


            $this->itemInfoCheck($item_product);

            $kit_item['is_free_product'] = $item_product['item_info']['is_free_product'];

            
            if($item_product['item_info']['is_free_product'] && in_array($item_product['product_id'], $free_products_skip))
                continue;

            
            
            if (($last_item_product_position > $index || $limit<=0) && (!$item_product['item_info']['is_free_product'] || ($item_product['item_info']['is_free_product'] && !$free_products_get_all)))
                continue;

            $product_id = $item_product['product_id'];
            $item_info = $item_product['item_info'];

            if (count($products_list) > 1 || $item_info['item_mode'] !='fix_product')
                $kit_item['selectable'] = true;

            
            $kit_item['item_empty_mode_not_empty_in_cart'] = $item_product['item_info']['item_empty_mode']['not_empty_in_cart'];

            $kit_item['is_free_product'] = $item_product['item_info']['is_free_product'];

            if (($only_first && count($kit_item['products']) > 0) && !$item_product['item_info']['is_free_product'])
                break;

            
            
            if ($item_info['main'] && $product_id != $current_product_id && !$admin_mode)
                continue;


            $item_data = $item_info;
            if ($filter_kit_items && $filter_limit>0) {

                $product_info = $this->getProductInfo($product_id);
                

                if ($product_info) {

                    $item_data['product_info'] = $product_info;

                    $options = $this->getProductOptions($product_id);;

                    $item_data['product_info']['options'] = $this->filterProductOptions($item_data, $options);

                    
                    $products_quantity_prev = (new ArrayObject($products_quantity))->getArrayCopy();
                    $options_quantity_prev = (new ArrayObject($options_quantity))->getArrayCopy();
                    $quantity_data = array('products_quantity' => &$products_quantity, 'options_quantity' => &$options_quantity);
                    $stock = $this->checkKitItemProductStock($item_data, $quantity_data);

                    if ($stock) {
                        $product_info['options'] = $item_data['product_info']['options'];
                        $item_data['product_info'] = $product_info;

                        $kit_item['products'][$item_data['product_id']] = $item_data;

                        $limit--;
                        $filter['last_item_product_position']=$index;

                        
                        $filter_limit--;
                        
                    } else {
                        $products_quantity = $products_quantity_prev;
                        $options_quantity = $options_quantity_prev;
                    }


                }
            } else {
                $kit_item['products'][$item_data['product_id']] = $item_data;
                $limit--;
                $filter['last_item_product_position']=$index;
            }
        }

        $kit_item['products'] = array_values($kit_item['products']);

        return $kit_item;
    }

    public function itemInfoCheck(&$kitItemProduct){

        if (!isset($kitItemProduct['item_info'])) {
            $kitItemProduct['item_info'] = $this->kit_items_info_cache[$kitItemProduct['kit_id']][$kitItemProduct['item_info_unique_id']];

            
            if(isset($kitItemProduct['item_info']['item_empty_mode']['title_by_language_code'])){
                $config_language_id = $this->config->get('config_language_id');
                if(isset($kitItemProduct['item_info']['item_empty_mode']['title_by_language_code'][$config_language_id])){
                    $kitItemProduct['item_info']['item_empty_mode']['title'] = $kitItemProduct['item_info']['item_empty_mode']['title_by_language_code'][$config_language_id];
                }
            }
            

            $kitItemProduct['item_info']['product_id'] = $kitItemProduct['product_id'];

        }
    }

    public function getKitProducts($kit_id, $settings, $ignore_product_in_cart="") {

        if(!$this->isEnabled()) return array();

        $current_product_id = $settings['main_product_id'];
        $filter_kit_items = $settings['filter_kit_items'];
        $only_first = $settings['only_first'];
        $admin_mode = $settings['admin_mode'];

        $products_quantity_cart = array();
        $options_quantity_cart = array();

        if ($filter_kit_items) {
            $products_quantity_cart = $this->getCartProductsQuantity("", $ignore_product_in_cart);
            $options_quantity_cart = $this->getCartOptionsQuantity("", $ignore_product_in_cart);
        }

        $kit_items = array();

        $kit_items_count = $this->getKitItemsCount($kit_id);

        for($i = 0; $i<$kit_items_count; $i++){
            $products_quantity = $products_quantity_cart;
            $options_quantity = $options_quantity_cart;
            $kit_items[$i] = $this->getKitItemProducts($kit_id, $settings, $i, $products_quantity, $options_quantity);
        }

        $ocmod_point_006 = 1;

        
        foreach ($kit_items as $index1=>$item){
            if(isset($item['products'][0])){
                $first_item_product = $item['products'][0];

                if($first_item_product['main']) {
                    $kit_items[$index1]['selectable'] = false;
                }
                
                if($admin_mode && count($item['products'])>0 && $first_item_product['main']) {
                    $kit_items[$index1]['selectable'] = true;
                }

                $kit_items[$index1]['is_free_product'] = $first_item_product['is_free_product'];
                $kit_items[$index1]['item_position'] = $item['item_position'];
            }

        }

        
        
        $kit_info = $this->getKit($kit_id);
        if($kit_info){
            if($kit_info['main_mode']=='series'){
                if($kit_info['series_mode']=='no_main'){
                    foreach ($kit_items as $index1=>&$item){
                        foreach ($item['products'] as $index2=>&$item_product){
                            if($item_product['product_id']== $settings['main_product_id']){
                                unset($kit_items[$index1]['products'][$index2]);

                            }
                        }


                    }
                }

            }
        }


        return $kit_items;
    }





    public function getKitProduct($kit_id, $main_product_id = '', $item_position=-1, $product_id=-1, $admin_mode = false, $kit_unique_id='') {

        if(!$this->isEnabled()) return array();

        $get_kit_products_settings = array(
            'main_product_id' => $main_product_id,
            'filter_kit_items' => false,
            'only_first' => false,
            'admin_mode' => $admin_mode,
        );

        $kit_info = $this->getKit($kit_id, $kit_unique_id, $get_kit_products_settings);

        $kit_items = $kit_info['kit_items'];

        $kit_item = array();
        $kit_product = array();

        foreach ($kit_items[$item_position]['products'] as $kit_item_product){
            if($kit_item_product['product_id']==$product_id){
                if(!isset( $kit_item_product['product_info'])) {
                    $kit_item_product['product_info'] = $this->getProductInfo($product_id);;
                    $options = $this->getProductOptions($product_id);;;
                    $kit_item_product['product_info']['options'] = $this->filterProductOptions($kit_item_product, $options);
                }

                $kit_product = $kit_item_product;
                break;
            }
        }

        $selectable = $kit_items[$item_position]['selectable'];

        $is_free_product = $kit_items[$item_position]['is_free_product'];
        if($is_free_product)
            $selectable = false;

        if(!empty($kit_product)) {
            $kit_item = array(
                'products' => array($kit_product),
                'selectable' => $selectable,
                'is_free_product' => $is_free_product,
                'item_position' => $item_position,
            );
        }

        return $kit_item;
    }

    public function getProductCategories($product_id) {

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

        $categories = array();
        foreach ($query->rows as $row){
            $categories[] = $row['category_id'];
        }

        return $categories;
    }

    public function getProductManufacturers($product_id) {

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");

        $manufacturers = array();
        foreach ($query->rows as $row){
            $manufacturers[] = $row['manufacturer_id'];
        }

        return $manufacturers;
    }

    public function getProductFilters($product_id) {

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

        $filters = array();
        foreach ($query->rows as $row){
            $filters[] = $row['filter_id'];
        }

        return $filters;
    }

    public function getCategoryProducts($category_id) {

        if(!$this->isEnabled()) return array();

        $sql = "SELECT p2c.product_id";

        $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";

        $sql .= " AND cp.path_id = '" . (int)$category_id . "'";

        $query = $this->db->query($sql);

        return $query->rows;
    }


    public function findProductDataInKit($product_id, $kit_items, $item_position, $is_free_product=null, $kit_id=null){

        if(!$this->isEnabled()) return array();

        $product_data = array();









        foreach ($kit_items[$item_position]['products'] as $kit_item_product){

            if($kit_item_product['product_id'] == $product_id){

                $kit_item_product['product_info'] = $this->getProductInfo($product_id);;
                $options = $this->getProductOptions($product_id);;;
                $kit_item_product['product_info']['options'] = $this->filterProductOptions($kit_item_product, $options);

                $product_data = $kit_item_product;
                break;
            }

        }

        return $product_data;
    }


    public function checkKitDiscountEnableByCustomerGroup(){

        if (!$this->registry->has('customer')) {
            $customer_group_discount_enable = false;
        } else {
            if ($this->customer->isLogged()) {
                $customer_group_id = $this->customer->getGroupId();
            } else {
                $customer_group_id = -1;
            }

            $customer_group_discount_enable = in_array($customer_group_id, $this->bundle_expert_settings['kit_discount_not_customer_group']) ? false : true;
        }

        return $customer_group_discount_enable;

    }

    public function getKitProductPriceData($kit_info, $kit_product, $product_info, $options, $use_tax = true, $product_quantity=1){

        if(!$this->isEnabled()) return array();

        
        
        if($this->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le']) {
            if ($kit_info['kit_as_product_light_mode'] && $kit_product['main']) {
                $kit_info['enable_special'] = false;
            }
        }

        $customer_group_discount_enable = $this->checkKitDiscountEnableByCustomerGroup();

        
        $customer_group_id = $this->config->get('config_customer_group_id');
        if($kit_product['price_mode_to_customer_groups_status'] && isset($kit_product['price_mode_to_customer_groups'][$customer_group_id])){
            $new_price_mode = $kit_product['price_mode_to_customer_groups'][$customer_group_id];
            $kit_product['price_mode'] = $new_price_mode['price_mode'];
            switch ($kit_product['price_mode']){
                case 'product_price':
                    $kit_product['price'] = $new_price_mode['value'];
                    break;
                case 'product_price_minus_sum':
                    $kit_product['price_minus_sum'] = $new_price_mode['value'];
                    break;
                case 'product_price_minus_percent':
                    $kit_product['price_minus_percent'] = $new_price_mode['value'];
                    break;
                case 'fix_price':
                    break;
            }

        }
        

        if ($kit_product['price_mode'] == 'fix_price' && $customer_group_discount_enable) {
            $special =(float)$kit_product['price'];
            if(!$kit_info['enable_special']) {
                $price = $product_info['price'];
            }else{
                if($kit_info['show_default_specials_in_kit_discounts']){
                    $price = $product_info['price'];

                }else{
                    if ($product_info['special']) {
                        $price = $product_info['special'];
                    }else{
                        $price = $product_info['price'];
                    }
                }

            }


            if($options){
                $option_price = $this->calculateOptionsPrice($options, $product_info['product_id']);

                

                if($special){
                    $special = $special;
                    $price = $price + $option_price;

                }else{
                    $price = $price ;
                }

            }

            $ocmod_point_015 = 1;

            if ($this->config->get('config_tax')) {
                $tax = (float)$special ? $special : $price;
            } else {
                $tax = false;
            }

        } else {
            $price = $product_info['price'];
            $special = false;

            if($kit_info['enable_discount']){
                $discount_price = $this->getProductDiscount($product_info['product_id'], $product_quantity);
                if(!empty($discount_price)){
                    $price = $discount_price;
                }
            } else {

            }

            if(!$kit_info['enable_special']){


            } else {
                if ($product_info['special']) {
                    if($kit_info['show_default_specials_in_kit_discounts']){
                        $price = $product_info['price'];
                        $special = $product_info['special'];
                    }else{
                        $price = $product_info['special'];
                        $special = false;
                    }


                }else {


                }
            }



            if($options){
                $option_price = $this->calculateOptionsPrice($options, $product_info['product_id']);

                $price = $price + $option_price;

                if($special)
                    $special = $special + $option_price;
            }

            if ($this->config->get('config_tax')) {
                $tax = (float)$special ? $special : $price;
            } else {
                $tax = false;
            }












            if ($kit_product['price_mode'] == 'product_price') {
                
            }

            if ($kit_product['price_mode'] == 'product_price_minus_sum' && $customer_group_discount_enable) {
                if($special){
                    $special = $special - $kit_product['price_minus_sum'];
                }else{
                    $special = $price - $kit_product['price_minus_sum'];;
                }
            }

            if ($kit_product['price_mode'] == 'product_price_minus_percent' && $customer_group_discount_enable) {
                if($special){

                    $special = $special - ($special * $kit_product['price_minus_percent'] / 100);
                }else{

                    $special = $price - ($price * $kit_product['price_minus_percent'] / 100);
                }
            }

            if ($this->config->get('config_tax')) {
                $tax = (float)$special ? $special : $price;
            } else {
                $tax = false;
            }

        }

        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            
        } else {
            $price = false;
            $special = false;
        }

        if ($special)
            $special_no_tax = $special;
        else
            $special_no_tax = false;




        $price_no_tax = $price;
        if($use_tax) {
            $price = $this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax'));
            if ($special)
                $special = $this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax'));
        }



        $result = array(
            'price' => $price,

            'price_no_tax' => $price_no_tax,
            'special' => $special,
            'special_no_tax' => $special_no_tax,
            'tax' => $tax,
        );

        return $result;
    }

    
    public function calculateOptionsPrice($options, $product_id) {

        if(!$this->isEnabled()) return 0;

        $option_price = 0;
        $option_price_by_product = 0;
        $option_points = 0;
        $option_weight = 0;

        $option_data = array();

        $product_info = $this->getProductInfo($product_id);
        $price = $product_info['price'];
        if($product_info['special']){
            $price = $product_info['special'];
        }

        $ocmod_point_016 = 1;

        $default_price = $price;
        $option_by_product = false;



        foreach ($options as $product_option_id => $value) {

            $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($option_query->num_rows) {

                if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {

                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_value_query->num_rows) {

                        $ocmod_point_007 = 1;

                        if ($option_value_query->row['price_prefix'] == '+') {
                            $option_price += $option_value_query->row['price'];
                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                            $option_price -= $option_value_query->row['price'];
                        }

                        if ($option_value_query->row['points_prefix'] == '+') {
                            $option_points += $option_value_query->row['points'];
                        } elseif ($option_value_query->row['points_prefix'] == '-') {
                            $option_points -= $option_value_query->row['points'];
                        }

                        if ($option_value_query->row['weight_prefix'] == '+') {
                            $option_weight += $option_value_query->row['weight'];
                        } elseif ($option_value_query->row['weight_prefix'] == '-') {
                            $option_weight -= $option_value_query->row['weight'];
                        }

                        
                        
                        if ($option_value_query->row['price_prefix'] == '=') {
                            $option_price_by_product = $option_value_query->row['price'];
                            $price = $option_price_by_product;
                            $option_by_product = true;
                        }

                        if ($option_value_query->row['price_prefix'] == '*') {
                            $option_price_by_product = $price * $option_value_query->row['price'];
                            $price = $option_price_by_product;
                            $option_by_product = true;
                        }

                        if ($option_value_query->row['price_prefix'] == '/') {
                            if($option_value_query->row['price']!=0) {
                                $option_price_by_product = $price / $option_value_query->row['price'];
                                $price = $option_price_by_product;
                                $option_by_product = true;
                            }
                        }
                        
                        if ($option_value_query->row['price_prefix'] == 'u' || $option_value_query->row['price_prefix'] == '%' || $option_value_query->row['price_prefix'] == '+%') {
                            if($option_value_query->row['price']!=0) {
                                $option_price_by_product = $price + $price * $option_value_query->row['price']/100;
                                $price = $option_price_by_product;
                                $option_by_product = true;
                            }
                        }
                        
                        if ($option_value_query->row['price_prefix'] == 'd') {
                            if($option_value_query->row['price']!=0) {
                                $option_price_by_product = $price - $price * $option_value_query->row['price']/100;
                                $price = $option_price_by_product;
                                $option_by_product = true;
                            }
                        }
                        if ($option_value_query->row['price_prefix'] == '+') {
                            $option_price_by_product += $option_value_query->row['price'];
                            $price = $option_price_by_product;
                        } elseif ($option_value_query->row['price_prefix'] == '-') {
                            $option_price_by_product -= $option_value_query->row['price'];
                            $price = $option_price_by_product;
                        }
                        


                        $option_data[] = array(
                            'product_option_id'       => $product_option_id,
                            'product_option_value_id' => $value,
                            'option_id'               => $option_query->row['option_id'],
                            'option_value_id'         => $option_value_query->row['option_value_id'],
                            'name'                    => $option_query->row['name'],
                            'value'                   => $option_value_query->row['name'],
                            'type'                    => $option_query->row['type'],
                            'quantity'                => $option_value_query->row['quantity'],
                            'subtract'                => $option_value_query->row['subtract'],
                            'price'                   => $option_value_query->row['price'],
                            'price_prefix'            => $option_value_query->row['price_prefix'],
                            'points'                  => $option_value_query->row['points'],
                            'points_prefix'           => $option_value_query->row['points_prefix'],
                            'weight'                  => $option_value_query->row['weight'],
                            'weight_prefix'           => $option_value_query->row['weight_prefix']
                        );
                    }
                } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                    foreach ($value as $product_option_value_id) {
                        $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                        if ($option_value_query->num_rows) {

                            $ocmod_point_008 = 1;

                            if ($option_value_query->row['price_prefix'] == '+') {
                                $option_price += $option_value_query->row['price'];
                            } elseif ($option_value_query->row['price_prefix'] == '-') {
                                $option_price -= $option_value_query->row['price'];
                            }

                            if ($option_value_query->row['points_prefix'] == '+') {
                                $option_points += $option_value_query->row['points'];
                            } elseif ($option_value_query->row['points_prefix'] == '-') {
                                $option_points -= $option_value_query->row['points'];
                            }

                            if ($option_value_query->row['weight_prefix'] == '+') {
                                $option_weight += $option_value_query->row['weight'];
                            } elseif ($option_value_query->row['weight_prefix'] == '-') {
                                $option_weight -= $option_value_query->row['weight'];
                            }

                            
                            
                            if ($option_value_query->row['price_prefix'] == '=') {
                                $option_price_by_product = $option_value_query->row['price'];
                                $price = $option_price_by_product;
                                $option_by_product = true;
                            }

                            if ($option_value_query->row['price_prefix'] == '*') {
                                $option_price_by_product = $price * $option_value_query->row['price'];
                                $price = $option_price_by_product;
                                $option_by_product = true;
                            }

                            if ($option_value_query->row['price_prefix'] == '/') {
                                if($option_value_query->row['price']!=0) {
                                    $option_price_by_product = $price / $option_value_query->row['price'];
                                    $price = $option_price_by_product;
                                    $option_by_product = true;
                                }
                            }
                            
                            if ($option_value_query->row['price_prefix'] == 'u') {
                                if($option_value_query->row['price']!=0) {
                                    $option_price_by_product = $price + $price * $option_value_query->row['price']/100;
                                    $price = $option_price_by_product;
                                    $option_by_product = true;
                                }
                            }
                            
                            if ($option_value_query->row['price_prefix'] == 'd') {
                                if($option_value_query->row['price']!=0) {
                                    $option_price_by_product = $price - $price * $option_value_query->row['price']/100;
                                    $price = $option_price_by_product;
                                    $option_by_product = true;
                                }
                            }
                            if ($option_value_query->row['price_prefix'] == '+') {
                                $option_price_by_product += $option_value_query->row['price'];
                                $price = $option_price_by_product;
                            } elseif ($option_value_query->row['price_prefix'] == '-') {
                                $option_price_by_product -= $option_value_query->row['price'];
                                $price = $option_price_by_product;
                            }
                            

                            $option_data[] = array(
                                'product_option_id'       => $product_option_id,
                                'product_option_value_id' => $product_option_value_id,
                                'option_id'               => $option_query->row['option_id'],
                                'option_value_id'         => $option_value_query->row['option_value_id'],
                                'name'                    => $option_query->row['name'],
                                'value'                   => $option_value_query->row['name'],
                                'type'                    => $option_query->row['type'],
                                'quantity'                => $option_value_query->row['quantity'],
                                'subtract'                => $option_value_query->row['subtract'],
                                'price'                   => $option_value_query->row['price'],
                                'price_prefix'            => $option_value_query->row['price_prefix'],
                                'points'                  => $option_value_query->row['points'],
                                'points_prefix'           => $option_value_query->row['points_prefix'],
                                'weight'                  => $option_value_query->row['weight'],
                                'weight_prefix'           => $option_value_query->row['weight_prefix']
                            );
                        }
                    }
                } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                    $option_data[] = array(
                        'product_option_id'       => $product_option_id,
                        'product_option_value_id' => '',
                        'option_id'               => $option_query->row['option_id'],
                        'option_value_id'         => '',
                        'name'                    => $option_query->row['name'],
                        'value'                   => $value,
                        'type'                    => $option_query->row['type'],
                        'quantity'                => '',
                        'subtract'                => '',
                        'price'                   => '',
                        'price_prefix'            => '',
                        'points'                  => '',
                        'points_prefix'           => '',
                        'weight'                  => '',
                        'weight_prefix'           => ''
                    );
                }
            }
        }

        if($option_by_product){

            $option_price = ($option_price_by_product-$default_price);

        }


        return $option_price;
    }

    public function checkActiveKitDiscountByProductCount($kit_info, $total_product_count){
        $check = true;

        if ($kit_info['kit_discount_by_product_count']['status'] && ($total_product_count < $kit_info['kit_discount_by_product_count']['min'] || $total_product_count > $kit_info['kit_discount_by_product_count']['max'])) {
            $check = false;
        }

        return $check;
    }

    
    public function calculateKitTotal($kit_info, $kit_total, $kit_total_tax, $total_product_count=0, $product_as_kit_quantity=0, $use_tax=false, $main_product_id=''){
        if(!$this->isEnabled()) return 0;

        $kit_total_mode =$kit_info['kit_price_mode'];

        $kit_total_old = false;

        if(!$kit_total_tax){
            $kit_total_tax = $kit_total;
        }


        $customer_group_discount_enable = $this->checkKitDiscountEnableByCustomerGroup();

        
        $customer_group_id = $this->config->get('config_customer_group_id');
        if($kit_info['kit_price_mode_to_customer_groups_status'] && isset($kit_info['kit_price_mode_to_customer_groups'][$customer_group_id])){
            $group_price_mode = $kit_info['kit_price_mode_to_customer_groups'][$customer_group_id];
            $kit_total_mode['mode'] = $group_price_mode['kit_price_mode'];
            $kit_total_mode[$group_price_mode['kit_price_mode']] = $group_price_mode['value'];

        }
        
        switch ($kit_total_mode['mode']){
            case 'sum':
                $kit_total = $kit_total;
                $kit_total_tax = $kit_total_tax;
                break;
            case 'sum_minus_percent':
                if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }
                $kit_total_old = $kit_total;

                $kit_total = $kit_total - ($kit_total / 100 * $kit_total_mode['sum_minus_percent']);
                $kit_total_tax = $kit_total_tax - ($kit_total_tax / 100 * $kit_total_mode['sum_minus_percent']);

                break;
            case 'sum_minus_value':
                if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }

                
                
                
                $tax_percent = 0;
                if($kit_total_tax!=0){
                    $tax_value = $kit_total - $kit_total_tax;
                    $tax_percent = $tax_value / ($kit_total_tax/100);
                }

                $kit_total_old = $kit_total;

                $kit_total = $kit_total_tax - $kit_total_mode['sum_minus_value'];
                $kit_total_tax = $kit_total_tax - $kit_total_mode['sum_minus_value'];

                
                $kit_total = $kit_total + $tax_percent * ($kit_total/100);

                break;
            case 'sum_fix':
                if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                    break;
                }
                if(!$customer_group_discount_enable){
                    break;
                }

                $use_default_discount = false;
                if($kit_info['kit_as_product'] && $kit_info['kit_as_product_main_product_use_default_discount']){
                    $main_product = $this->getKitLinkProducts2($kit_info['kit_id']);
                    if ($product_as_kit_quantity)
                        $main_product_quantity = $product_as_kit_quantity;
                    else {
                        $main_product_quantity = 1;
                    }
                    if(!empty($main_product)){
                        $discount_price = $this->getProductDiscountByOpencartVersion(array_shift($main_product), $main_product_quantity);
                        if($discount_price!==false){
                            $use_default_discount = true;
                        }
                    }
                }

                
                
                
                $tax_percent = 0;
                if($kit_total_tax!=0){
                    $tax_value = $kit_total - $kit_total_tax;
                    $tax_percent = $tax_value / ($kit_total_tax/100);
                }

                if($use_default_discount){
                    $kit_total_old = $kit_total;
                    $kit_total = $discount_price;
                    $kit_total_tax = $discount_price;
                }else{
                    $kit_total_old = $kit_total;
                    $kit_total = $kit_total_mode['sum_fix'];
                    $kit_total_tax = $kit_total_mode['sum_fix'];
                }

                
                $kit_total = $kit_total + $tax_percent * ($kit_total/100);

                break;
        }

        
        if ($kit_info['kit_as_product']) {
            if ($use_tax) {
                    
                    switch ($this->bundle_expert_settings['product_as_kit_tax_mode']){
                        case "by_main":
                            $product_info = $this->getProductInfo($main_product_id);
                            if ($product_info) {
                                $kit_total = $this->tax->calculate($kit_total_tax, $product_info['tax_class_id'], $this->config->get('config_tax'));
                            }
                            break;
                        case "by_items":
                            $kit_total = $kit_total;
                            break;
                    }



            }
        }
        

        $kit_total_data = array(
            'total_kit_old'=>$kit_total_old,
            'total_kit'=>$kit_total,
            'total_kit_tax'=>$kit_total_tax,
        );

        return $kit_total_data;
    }


    public function getProductDiscountByOpencartVersion($product_id, $quantity){
        $price = false;

        $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

        if ($product_discount_query->num_rows) {
            $price = $product_discount_query->row['price'];
        }

        return $price;
    }

    public function getKitProductFixedOptionsData($product_options, $kit_product){

        if(!$this->isEnabled()) return array();

        $option = array();

        foreach ($product_options as $product_option){
            if($product_option['fixed_value']) {
                foreach ($product_option['product_option_value'] as $option_value) {
                    if ($this->isFixedOptionValue($product_option['product_option_id'], $option_value['product_option_value_id'], $kit_product['fixed_options'])) {
                        if($product_option['type']=='checkbox'){
                            $option[$product_option['product_option_id']][] = $option_value['product_option_value_id'];

                        }else{
                            $option[$product_option['product_option_id']] = $option_value['product_option_value_id'];

                        }
                    }
                }
            }
        }

        return $option;

    }

    public function convertProductOptionsToCartOptionsFormat($product_options){

        if(!$this->isEnabled()) return array();

        $cart_options = array();

        foreach ($product_options as $index1 => $option) {

            if (empty($option['product_option_value'])) {
                if (isset($option['fixed_value']) && $option['fixed_value']) {
                    $cart_options[$option['product_option_id']] = $option['value'];
                }
            } else {
                foreach ($option['product_option_value'] as $index2 => $option_value) {

                }
                if ($option['type'] == 'checkbox') {
                    foreach ($option['product_option_value'] as $index2 => $option_value) {
                        if (isset($option_value['preset_option_value']) && $option_value['preset_option_value']) {
                            $cart_options[$option['product_option_id']][] = $option_value['product_option_value_id'];
                        }
                    }

                } else {
                    foreach ($option['product_option_value'] as $index2 => $option_value) {
                        if (isset($option_value['preset_option_value']) && $option_value['preset_option_value']) {
                            $cart_options[$option['product_option_id']] = $option_value['product_option_value_id'];
                        }
                    }

                }
            }

        }

        return $cart_options;
    }

    
    public function filterProductOptions($kit_product, $product_options){

        if(!$this->isEnabled()) return array();

        
        foreach ($kit_product['disable_options'] as $index => $disable_option) {
            $option_in_product = $this->checkOptionInProduct($disable_option, $product_options);
            if (!$option_in_product) {
                unset($kit_product['disable_options'][$index]);
            }
        }
        foreach ($kit_product['fixed_options'] as $index => $fixed_option) {
            $option_in_product = $this->checkOptionInProduct($fixed_option, $product_options);
            if (!$option_in_product) {
                unset($kit_product['fixed_options'][$index]);
            }
        }

        
        if (isset($kit_product['enabled_options']) && $kit_product['enabled_options']) {
            $enabled_options = $kit_product['enabled_options'];
            foreach ($product_options as $index1 => $option) {
                $is_enable_option =  $this->isEnabledOption($option, $enabled_options);
                $is_enable_option_by_value =  $this->isEnabledOptionByValue($option, $enabled_options);
                if($is_enable_option || $is_enable_option_by_value){

                }else{
                    unset($product_options[$index1]);
                    continue;
                }








                if($is_enable_option_by_value){
                    foreach ($option['product_option_value'] as $index2 => $product_option_value) {
                        if (!$this->isEnabledOptionValue($product_option_value, $enabled_options)) {
                            unset($product_options[$index1]['product_option_value'][$index2]);

                        }
                    }
                }

            }

        }

        
        foreach ($product_options as $index1=>$option) {
            if($this->isDisableOption($option['product_option_id'], $kit_product['disable_options'])){
                unset($product_options[$index1]);
                continue;
            }

            foreach ($option['product_option_value'] as $index2=>$option_value) {
                if($this->isDisableOptionValue($option['product_option_id'], $option_value['product_option_value_id'], $kit_product['disable_options'])){
                    unset($product_options[$index1]['product_option_value'][$index2]);
                }
            }
        }

        
        $display_only_fixed_option_value = true;
        if($display_only_fixed_option_value) {
            foreach ($product_options as $index1 => $option) {

                $product_options[$index1]['fixed_value'] = false;

                if($option['type']=='checkbox'){
                    
                    $option_has_fixed = false;
                    foreach ($option['product_option_value'] as $index2 => $option_value) {
                        if ($this->isFixedOptionValue($option['product_option_id'], $option_value['product_option_value_id'], $kit_product['fixed_options'])) {
                            $option_has_fixed = true;
                            break;
                        }
                    }
                    if($option_has_fixed){
                        $product_options[$index1]['fixed_value'] = true;
                        foreach ($option['product_option_value'] as $index2 => $option_value) {
                            if (!$this->isFixedOptionValue($option['product_option_id'], $option_value['product_option_value_id'], $kit_product['fixed_options'])) {
                                unset($product_options[$index1]['product_option_value'][$index2]);
                            }else{
                                $product_options[$index1]['product_option_value'][$index2]['preset_option_value']=true;
                            }
                        }
                    }
                }else{
                    
                    foreach ($option['product_option_value'] as $index2 => $option_value) {
                        if ($this->isFixedOptionValue($option['product_option_id'], $option_value['product_option_value_id'], $kit_product['fixed_options'])) {

                            $product_options[$index1]['fixed_value'] = true;

                            $option['product_option_value'][$index2]['preset_option_value']=true;

                            $product_options[$index1]['product_option_value'] = array(
                                $option['product_option_value'][$index2]
                            );
                            break;
                        }
                    }
                }


            }
        }


        return $product_options;
    }

    public function isEnabledOption($option,  $enabled_options){

        if (!$this->isEnabled()) return;

        $is_enabled = false;

        foreach ($enabled_options as $enabled_option) {
            if ($enabled_option['item_type'] == 'option') {
                if($option['product_option_id']==$enabled_option['product_option_id']){
                    $is_enabled = true;
                    break;
                }
            }
        }

        return $is_enabled;
    }

    public function isEnabledOptionByValue($option,  $enabled_options){

        if (!$this->isEnabled()) return;

        $is_enabled = false;

        foreach ($enabled_options as $enabled_option) {

            if ($enabled_option['item_type'] == 'option_value') {
                if($option['product_option_id']==$enabled_option['product_option_id']){
                    $is_enabled = true;
                    break;
                }
            }
        }

        return $is_enabled;
    }

    public function isEnabledOptionValue($option_value,  $enabled_options){

        if (!$this->isEnabled()) return;

        $is_enabled = false;

        foreach ($enabled_options as $enabled_option) {

            if ($enabled_option['item_type'] == 'option_value') {
                if($option_value['product_option_value_id']==$enabled_option['product_option_value_id']){
                    $is_enabled = true;
                    break;
                }
            }
        }

        return $is_enabled;
    }

    public function isDisableOption($product_option_id,  $disable_options){

        if(!$this->isEnabled()) return;

        $is_disable = false;

        foreach ($disable_options as $disable_option) {
            if ($disable_option['item_type'] == 'option' && $disable_option['product_option_id'] == $product_option_id) {
                $is_disable = true;
                break;
            }
        }

        return $is_disable;
    }

    public function isDisableOptionValue($product_option_id, $product_option_value_id,  $disable_options){

        if(!$this->isEnabled()) return;

        $is_disable = false;

        foreach ($disable_options as $disable_option) {
            if ($disable_option['item_type'] == 'option_value' && $disable_option['product_option_id'] == $product_option_id && $disable_option['product_option_value_id'] == $product_option_value_id) {
                $is_disable = true;
                break;
            }
        }

        return $is_disable;
    }

    public function isFixedOptionValue($product_option_id, $product_option_value_id,  $fixed_options){

        if(!$this->isEnabled()) return;

        $is_fixed = false;

        foreach ($fixed_options as $fixed_option) {
            if ($fixed_option['item_type'] == 'option_value' && $fixed_option['product_option_id'] == $product_option_id && $fixed_option['product_option_value_id'] == $product_option_value_id) {
                $is_fixed = true;
                break;
            }
        }

        return $is_fixed;
    }

    public function getProductsByProductId($product_id, $data) {

        if(!$this->isEnabled()) return array();



        $sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW()";

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

    public function getProductsByCategory($data = array()) {

        $sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special ";

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

        if(!$this->isEnabled()) return array();


        $sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

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

        if(!$this->isEnabled()) return array();

        $sql = "SELECT pf.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = pf.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product_filter pf LEFT JOIN  " . DB_PREFIX . "product p ON(p.product_id=pf.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pf.filter_id = '" . (int)$filter_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

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

    
    public function getProductAttributeValue($product_id, $attribute_id) {
        if (!$this->isEnabled()) return array();

        $sql = "SELECT * FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '".(int)$attribute_id."' AND language_id = '" . (int)$this->config->get('config_language_id') . "' AND product_id = '".(int)$product_id."'";

        $query = $this->db->query($sql);

        if(!empty($query->row)){
            $value = $query->row['text'];
        }else{
            $value = null;
        }

        return $value;

    }
    public function getProductsByAttribute($filter_data) {
        if (!$this->isEnabled()) return array();

        $attribute_id = $filter_data['attribute_id'];
        $attribute_value = $filter_data['attribute_value'];



        $sql = "SELECT pa.*, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = pa.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "product p ON (pa.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (pa.product_id = p2s.product_id) WHERE pa.attribute_id = '".(int)$attribute_id."' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.text = '".$this->db->escape($attribute_value)."' AND p.status='1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        $query = $this->db->query($sql);

        return $query->rows;

    }
    

    public function getProductsByField($data, $field_key) {

        if (!$this->isEnabled()) return array();

        $sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW()";
        $sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (p." . $field_key . " LIKE '" . $this->db->escape($data['filter_name']) . "'";
            $sql .= " )";
        }

        $sql .= " AND p." . $field_key . "<>''";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getGroup($group_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_product_group g WHERE g.group_id = '" . (int)$group_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getGroupItems($group_id){
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_product_group_item WHERE group_id = '" . (int)($group_id) . "' ORDER BY sort_order";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getGroupProducts($group_id) {

        $products = array();

        $group = $this->getGroup($group_id);

        $group_items = $this->getGroupItems($group_id);

        if ($group) {
            foreach ($group_items as $index => $group_item) {
                $item_products = $this->getGroupItemProduct($group_item);
                if ($index == 0) {
                    $products = $item_products;
                } else {
                    switch ($group_item['combine_mode']) {
                        case 'intersect':
                            $products = array_intersect_key($products, $item_products);
                            break;
                        case 'union':
                            foreach ($item_products as $product_id => $item_product) {
                                if (!isset($products[$product_id])) {
                                    $products[$product_id] = $item_product;
                                }
                            }
                            break;
                        case 'subtract':
                            foreach ($item_products as $product_id => $item_product) {
                                if (isset($products[$product_id])) {
                                    unset($products[$product_id]);
                                }
                            }
                            break;
                        default:
                            break;
                    }

                }
            }


        }

        return $products;
    }

    public function getGroupItemProduct($group_item){
        $products = array();

        if(substr($group_item['source'], 0, strlen('auto_')) == 'auto_'){
            $autocomplete_mode = true;
        }else{
            $autocomplete_mode = false;
        }

        if($autocomplete_mode){
            $source_key = str_replace('auto_', '', $group_item['source']);
            $results = array();
            switch ($source_key){
                case 'product':
                    $filter_data = array(
                        'filter_name' => '',
                    );
                    $results = $this->getProductsByProductId($group_item['value'], $filter_data);
                    break;
                case 'category':
                    $filter_data = array(
                        'filter_category_id' => $group_item['value'],
                        'filter_filter' => '',
                        'filter_name' => '',
                        'sort' => '',
                        'order' => '',
                        'start' => 0,
                        'limit' => 100000
                    );
                    $results = $this->getProductsByCategory($filter_data);
                    break;
                case 'manufacturer':
                    $filter_data = array(
                        'filter_name' => '',
                    );
                    $results = $this->getProductsByManufacturer($group_item['value'], $filter_data);
                    break;


            }
            foreach ($results as $result){
                $product_info = $this->getProductInfo($result['product_id']);
                if($product_info){
                    $products[$product_info['product_id']] = $product_info;
                }

            }
        }else{
            $source_key = '';

            switch ($group_item['source']){
                case "product_id":
                case "model":
                case "sku":
                case "upc":
                case "ean":
                case "jan":
                case "isbn":
                case "mpn":
                case "location":
                case "price":
                case "quantity":
                case "status":
                    $source_key = "p." . $group_item['source'] ;
                    break;
                case "name":
                case "description":
                    $source_key = "pd." . $group_item['source'];
                    break;
            }

            $operation_key = '';
            switch ($group_item['operation']){
                case "equal":
                    $operation_key = " = '" . $this->db->escape($group_item['value']) . "' ";
                    break;
                case "more_equal":
                    $operation_key = " >= '" . (int)$group_item['value'] . "' ";
                    break;
                case "equal_less":
                    $operation_key = " <= '" . (int)$group_item['value'] . "' ";
                    break;
                case "more":
                    $operation_key = " > '" . (int)$group_item['value'] . "' ";
                    break;
                case "less":
                    $operation_key = " < '" . (int)$group_item['value'] . "' ";
                    break;
                case "start_with":
                    $operation_key = " LIKE '" . $this->db->escape($group_item['value']) . "%' ";
                    break;
                case "contain":
                    $operation_key = " LIKE '%" . $this->db->escape($group_item['value']) . "%' ";
                    break;
                case "end_with":
                    $operation_key = " LIKE '%" . $this->db->escape($group_item['value']) . "' ";
                    break;
            }

            if(!empty($source_key) && !empty($operation_key) && !empty($group_item['value'])){

                $condition = $source_key . $operation_key;

                $sql = "SELECT p.*, pd.*,(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND " . $condition;

                $query = $this->db->query($sql);

                foreach ($query->rows as $row){
                    $products[$row['product_id']] = $row;
                }

            }
        }


        return $products;
    }

    public function getGroupProductsSelected($group_id) {
        $sql = "SELECT gsp.*, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "bundle_expert_product_group_selected_product gsp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = gsp.product_id)  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE gsp.group_id = '" . (int)$group_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1  AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";




        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getProductsByGroup($group_id, $data) {

        if(!$this->isEnabled()) return array();

        $products = array();

        $group_info = $this->getGroup($group_id);

        if($group_info){
            if($group_info['products_mode']=="all"){
                $products = $this->getGroupProducts($group_id);
            }
            if($group_info['products_mode']=="select"){
                $products = $this->getGroupProductsSelected($group_id);
            }
        }

        return $products;
    }



    public function filterKitItemProducts_checkProduct($kit_items, $only_first=true){

    }
    
    
    
    
    
    
    
    
    public function filterKitItemProducts($kit_items, $only_first=true){

        if(!$this->isEnabled()) return array();

        if($this->bundle_expert_settings['stock_out_products_show']){

        }else{
            if (!$this->config->get('config_stock_checkout')) {

                
                $products_quantity = $this->getCartProductsQuantity();
                $options_quantity = $this->getCartOptionsQuantity();


                
                foreach ($kit_items as $index1 => $kit_item) {

                    $kit_item_products = $kit_item['products'];

                    $remove_last_products = false;

                    foreach ($kit_item_products as $index2 => $kit_item_product) {

                        $products_quantity_prev = (new ArrayObject($products_quantity))->getArrayCopy();
                        $options_quantity_prev = (new ArrayObject($options_quantity))->getArrayCopy();

                        if ($remove_last_products) {
                            break;
                        }

                        $quantity_data = array('products_quantity'=>&$products_quantity,'options_quantity'=>&$options_quantity);

                        $stock = $this->checkKitItemProductStock($kit_item_product, $quantity_data);




                        if (!$stock) {
                            unset($kit_items[$index1]['products'][$index2]);

                            $products_quantity = $products_quantity_prev ;
                            $options_quantity = $options_quantity_prev ;
                        }else{
                            
                            if($only_first)
                                $remove_last_products = true;
                        }

                    }
                }
            }
        }



        if($only_first){
            
            foreach ($kit_items as $index=>$kit_item){
                if(!empty($kit_item['products'])) {
                    reset($kit_item['products']);
                    $first_key = key($kit_item['products']);

                    $kit_items[$index]['products'] = array();
                    $kit_items[$index]['products'][] = $kit_item['products'][$first_key];
                }
            }
        }


        return $kit_items;

    }



    
    
    public function checkOptionInProduct($option, $product_options){

        if(!$this->isEnabled()) return;

        $option_in_product = false;

        foreach ($product_options as  $product_option){

            if ($option['item_type'] == 'option') {
                if($product_option['product_option_id']==$option['product_option_id']){
                    $option_in_product = true;
                    break;
                }
            }else{
                foreach ($product_option['product_option_value'] as $product_option_value){
                    if($product_option_value['product_option_value_id']==$option['product_option_value_id']){
                        $option_in_product = true;
                        break;
                    }
                }
            }

        }

        return $option_in_product;
    }

    public function getOrderProductKitInfo($order_product_id){

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_product_id = '" . (int)$order_product_id . "'");

        if($query->row && isset($query->row['cart_kit_info'])){
            $cart_kit_info = json_decode($query->row['cart_kit_info'], true);
        }else{
            $cart_kit_info = array();
        }
        return $cart_kit_info;
    }

    public function getOrderProductAsKitData($order_product_id){

        $data = array();

        if(!$this->isEnabled()) return array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_product_id = '" . (int)$order_product_id . "'");

        if($query->row){
            if(isset($query->row['cart_kit_info'])){
                $data['cart_kit_info']= json_decode($query->row['cart_kit_info'], true);
            }
            if(isset($query->row['product_as_kit_info'])){
                $data['product_as_kit_info']= json_decode($query->row['product_as_kit_info'], true);
            }
        }else{
            $data = array();
        }
        return $data;
    }

    
    public function addWidgetHistoryStatus($widget_id, $widget_history_code){

        if ($widget_history_code == 'success')
            $success = true;
        else
            $success = false;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_history_status WHERE widget_id = '" . (int)$widget_id . "'");

        if (empty($query->row)) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_history_status SET widget_id ='" . (int)$widget_id . "', widget_history_code = '" . $this->db->escape($widget_history_code) . "', success = '" . (int)$success . "', date_modified = NOW()");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget_history_status SET widget_history_code = '" . $this->db->escape($widget_history_code) . "', success = '" . (int)$success . "', date_modified = NOW() WHERE widget_id = '" . (int)$widget_id . "'");
        }
    }





    
    
    
    
    public function checkKitActuality($kit_items, $kit_info, $kit_from_cart_unique_id=''){

        if(!$this->isEnabled()) return;

        $check_kit = true;

        
        if (!$kit_info || $kit_info['status'] == 0) {
            $check_kit = false;
        } else {
            
            $size_error = false;
            foreach ($kit_info['kit_items'] as $index => $kit_info_item) {
                if($kit_info_item['is_free_product'])
                    continue;

                $finded = false;
                foreach ($kit_items as $kit_item){
                    if($kit_item['item_position']==$index){
                        $finded=true;
                        break;
                    }
                }
                if(!$finded && !$kit_info_item['products'][0]['item_empty_mode']['enable'])
                    $size_error=true;
            }


            if ($size_error) {
                $check_kit = false;
            } else {
                
                foreach ($kit_items as $index => $kit_item_product) {
                    $item_position = $kit_item_product['item_position'];







                    $item_product_id = $kit_item_product['product_id'];

                    $kit_item_product_data = $this->findKitItemProduct($item_position, $item_product_id, $kit_info['kit_items']);

                    if (isset($kit_item_product_data)) {
                        
                        if(!$kit_item_product_data['quantity_edit']) {
                            
                            
                            if ($kit_item_product['quantity'] != $kit_item_product_data['quantity']) {
                                
                                

                            }
                        }
                        
                        if (isset($kit_item_product['option'])) {
                            
                            foreach ($kit_item_product['option'] as $product_option_id => $value) {
                                if(!empty($value)) {
                                    $second_option_id = '70604031a6e68c8fa6be';
                                    $has_option = $this->checkProductHasOption($item_product_id, $product_option_id, $value);
                                    if (!$has_option) {
                                        $check_kit = false;
                                        break;
                                    }
                                }
                            }
                            
                            foreach ($kit_item_product['option'] as $product_option_id => $value) {
                                if(!empty($value)) {
                                    $has_disabled_option = $this->checkCartProductHasDisabledOption($item_product_id, $product_option_id, $value, $kit_item_product_data['disable_options']);
                                    if ($has_disabled_option) {
                                        $check_kit = false;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if($kit_from_cart_unique_id!=='') {
                            foreach ($kit_item_product_data['fixed_options'] as $fixed_option) {
                                if (!empty($value)) {
                                    $has_fixed_option = $this->checkCartProductHasFixedOption($item_product_id, $kit_item_product['option'], $fixed_option);
                                    $first_option_id = 'dc65e5e0764e444ec837';

                                    if (!$has_fixed_option) {
                                        $check_kit = false;
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $check_kit = false;
                        break;
                    }
                }
            }
        }

        if (isset($first_option_id) && isset($second_option_id))
            unset($second_option_id);


        return $check_kit;
    }

    
    public function checkKitPeriodActuality($kit_id){
        $check_kit = true;

        $sql = "SELECT kit_id FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id IN (" . $kit_id . ") AND (customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR customer_group_id = -1000) AND (date_start = '0000-00-00' OR date_start <= CURDATE()) AND (date_end = '0000-00-00' OR date_end >= CURDATE()) ";
        $query = $this->db->query($sql);
        if($query->row){
            $check_kit = true;
        }else{
            $check_kit = false;
        }











        return $check_kit;
    }

    
    public function getKitFreeItemInfo($kit_id, $item_position){
        if (!$this->isEnabled()) return array();

        $kit_items = $this->getKitItems($kit_id);

        $free_item_position = -1;

        $empty_mode_info = array();

        foreach ($kit_items[$item_position] as  $item_product) {

            $this->itemInfoCheck($item_product);
            $item_info = $item_product['item_info'];
            if ($item_info['is_free_product']) {
                $empty_mode_info = $item_info;
                break;
            }
        }

        return $empty_mode_info;

    }

    public function getKitFreeItemPositions($kit_id){
        if (!$this->isEnabled()) return array();

        $kit_items = $this->getKitItems($kit_id);

        $free_item_positions = array();

        foreach ($kit_items as $item_position => $kit_item) {
            foreach ($kit_item as $item_product) {

                $this->itemInfoCheck($item_product);
                $item_info = $item_product['item_info'];
                if ($item_info['is_free_product']) {
                    if(!in_array($item_position, $free_item_positions)) {
                        $free_item_positions[] = $item_position;
                        break;
                    }
                }
            }
        }

        return $free_item_positions;
    }

    
    
    public function checkProductHasOption($product_id, $product_option_id, $value){

        $has_option = false;

        $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        if ($option_query->num_rows) {
            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                if ($option_value_query->num_rows) {
                    $has_option = true;
                }
            } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                foreach ($value as $product_option_value_id) {
                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_value_query->num_rows) {
                        $has_option = true;
                    }
                }
            } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                $has_option = true;
            }
        }

        return $has_option;
    }

    
    
    public function checkCartProductHasDisabledOption($product_id, $product_option_id, $value, $disable_options){
        $model_bundle_expert = $this->registry->get('model_module_bundle_expert');

        $has_disabled_option = false;

        $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        if ($option_query->num_rows) {
            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                if ($option_value_query->num_rows) {
                    if ($model_bundle_expert->isDisableOption($product_option_id, $disable_options) || $model_bundle_expert->isDisableOptionValue($product_option_id, $value, $disable_options)) {
                        $has_disabled_option = true;
                    }
                }
            } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                foreach ($value as $product_option_value_id) {
                    $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_value_query->num_rows) {
                        if ($model_bundle_expert->isDisableOption($product_option_id, $disable_options) || $model_bundle_expert->isDisableOptionValue($product_option_id, $product_option_value_id, $disable_options)) {
                            $has_disabled_option = true;
                        }
                    }
                }
            } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                if ($model_bundle_expert->isDisableOption($product_option_id, $disable_options) || $model_bundle_expert->isDisableOptionValue($product_option_id, $value, $disable_options)) {
                    $has_disabled_option = true;
                }
            }
        }

        return $has_disabled_option;
    }

    
    
    public function checkCartProductHasFixedOption($product_id, $options, $fixed_option){

        $has_fixed_option = false;

        foreach ($options as $product_option_id => $value) {

            $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($option_query->num_rows) {
                if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
                    if($product_option_id==$fixed_option['product_option_id']&&$fixed_option['product_option_value_id']==$value)
                        $has_fixed_option = true;
                } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                    foreach ($value as $product_option_value_id) {
                        if($product_option_id==$fixed_option['product_option_id']&&$product_option_value_id==$fixed_option['product_option_value_id'])
                            $has_fixed_option = true;
                    }
                } elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                    if($product_option_id==$fixed_option['product_option_id']&&$fixed_option['product_option_value_id']==$value)
                        $has_fixed_option = true;
                }
            }
        }


        return $has_fixed_option;
    }

    
    public function findKitItemProduct($item_position, $item_product_id, $kit_items){

        $product = null;

        foreach ($kit_items[$item_position]['products'] as $item_product) {
            if ($item_product['product_id'] == $item_product_id) {
                $product = $item_product;
                break;
            }
        }

        return $product;
    }


    
    

    public function getKitEnableStatus_step1($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart = false, $kit_from_cart_unique_id="", $status_log = true){
        $enable_status = array(
            'display_kit' => true,
            'add_to_cart_kit' => true,
            'kit_history_code' => 'success',
            'kit_history_data' => array(
                'position'=>'',
                'main_product_id'=>$main_product_id
            ),
            'text' => ''
        );

        
        
        if(!$this->bundle_expert->admin_api_mode) {
            $check_kit_period = $this->checkKitPeriodActuality($kit_info['kit_id']);
            if(!$check_kit_period){
                $enable_status['display_kit'] = false;
                $enable_status['add_to_cart_kit'] = false;
                $enable_status['text'] = $this->language->get('text_kit_not_active_more');

                return $enable_status;
            }
        }

        
        $cart_kit_count = $this->getCartKitCount($kit_info, $main_product_id);

        if ($kit_in_cart) {
            $cart_kit_count = $cart_kit_count - 1;
        }

        
        if ($kit_info['kit_quantity_mode']['limit'] && $kit_info['kit_quantity_mode']['value']==0) {
            $enable_status['display_kit'] = false;
            $enable_status['add_to_cart_kit'] = false;
            $enable_status['text'] = $this->language->get('text_kit_limit_at_store');

            $enable_status['kit_history_code'] = 'kit_limit_zero';
            $enable_status['kit_history_data'] = array(
                'position'=>'',
                'main_product_id'=>$main_product_id
            );

            if($status_log)
                $this->addKitHistoryStatus($kit_info, $enable_status);

            return $enable_status;

        }

        
        
        if ($kit_info['kit_quantity_mode']['limit'] && ($cart_kit_count + 1) > $kit_info['kit_quantity_mode']['value']) {
            $enable_status['display_kit'] = false;
            $enable_status['add_to_cart_kit'] = false;
            $enable_status['text'] = $this->language->get('text_kit_limit_at_store');

            return $enable_status;

        }

        
        
        if ($kit_info['kit_cart_limit_mode']['limit'] && ($cart_kit_count+1) > $kit_info['kit_cart_limit_mode']['value']) {
            $enable_status['display_kit'] = false;
            $enable_status['add_to_cart_kit'] = false;
            $enable_status['text'] = $this->language->get('text_you_add_max_kit_at_cart') ;

            return $enable_status;
        }

        $enable_status = -1;

        return $enable_status;

    }

    public function getKitEnableStatus_step2($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart = false, $kit_from_cart_unique_id="", $status_log = true)
    {
        $enable_status = array(
            'display_kit' => true,
            'add_to_cart_kit' => true,
            'stock_in_status' => true,
            'kit_history_code' => 'success',
            'kit_history_data' => array(
                'position' => '',
                'main_product_id' => $main_product_id
            ),
            'text' => ''
        );

        
        
        if ($kit_in_cart)
            $ignore_kit_unique_id = $kit_from_cart_unique_id;
        else
            $ignore_kit_unique_id = "";
        
        if ($main_product_in_cart)
            $ignore_product_id = (int)$main_product_id;
        else
            $ignore_product_id = '';

        $products_quantity = $this->getCartProductsQuantity($ignore_kit_unique_id, $ignore_product_id);
        $options_quantity = $this->getCartOptionsQuantity($ignore_kit_unique_id, $ignore_product_id);

        
        if($main_product_id){

                if(!$this->config->get('config_stock_checkout')) {
                    $product_info = $this->getProductInfoDefault($main_product_id);
                    if($product_info && $product_info['quantity']<=0){
                        $enable_status['display_kit'] = false;
                        $enable_status['add_to_cart_kit'] = false;
                        $enable_status['stock_in_status'] = false;
                        $enable_status['text'] = $this->language->get('text_have_no_some_products');
                        $enable_status['error_items'][] = 0;

                        if($this->bundle_expert_settings['stock_out_bundles_show']){
                            $enable_status['display_kit'] = true;
                        }
                        if($this->bundle_expert_settings['stock_out_bundles_add_to_cart']){
                            $enable_status['add_to_cart_kit'] = true;
                        }
                        return $enable_status;
                    }
                }


        }

        

        foreach ($kit_items as $index=>$kit_item) {
            $kit_item_products = $kit_item['products'];
            $stock = true;
            if (count($kit_item_products) == 0 && !$kit_item['is_free_product']) {
                $stock = false;
            }else{
                foreach ($kit_item_products as $kit_item_product){

                    if($kit_item_product['is_free_product']){
                        break;
                    }

                    $products_quantity_prev = (new ArrayObject($products_quantity))->getArrayCopy();
                    $options_quantity_prev = (new ArrayObject($options_quantity))->getArrayCopy();

                    $quantity_data = array('products_quantity'=>&$products_quantity,'options_quantity'=>&$options_quantity);

                    $stock = $this->checkKitItemProductStock($kit_item_product, $quantity_data, false);

                    if(!$stock) {
                        $products_quantity = $products_quantity_prev ;
                        $options_quantity = $options_quantity_prev ;
                    }else{
                        $products_quantity = array();
                        $options_quantity = array();
                        break;
                    }
                }
            }


            if(!$stock){
                $enable_status['display_kit'] = false;
                $enable_status['add_to_cart_kit'] = false;
                $enable_status['stock_in_status'] = false;
                $enable_status['text'] = $this->language->get('text_have_no_some_products');
                $enable_status['error_items'][] = $index;

                if($this->bundle_expert_settings['stock_out_bundles_show']){
                    $enable_status['display_kit'] = true;
                }
                if($this->bundle_expert_settings['stock_out_bundles_add_to_cart']){
                    $enable_status['add_to_cart_kit'] = true;
                }
                break;
            }


































































        }

        return $enable_status;
    }

    public function getKitEnableStatus($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart = false, $kit_from_cart_unique_id="", $status_log = true){

        if(!$this->isEnabled()) return array();

        $enable_status = array(
            'display_kit' => true,
            'add_to_cart_kit' => true,
            'kit_history_code' => 'success',
            'kit_history_data' => array(
                'position'=>'',
                'main_product_id'=>$main_product_id
            ),
            'text' => ''
        );

        $enable_status = $this->getKitEnableStatus_step1($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart, $kit_from_cart_unique_id, $status_log);

        if($enable_status!=-1){
            if($status_log)
                $this->addKitHistoryStatus($kit_info, $enable_status);

            return $enable_status;
        }

        $enable_status = $this->getKitEnableStatus_step2($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart, $kit_from_cart_unique_id, $status_log);

        if($enable_status!=-1){
            if($status_log)
                $this->addKitHistoryStatus($kit_info, $enable_status);

            return $enable_status;
        }

        if($status_log)
            $this->addKitHistoryStatus($kit_info, $enable_status);

        return $enable_status;

        





























































        





















































































        if($status_log)
            $this->addKitHistoryStatus($kit_info, $enable_status);

        return $enable_status;
    }



    
    
    public function getCartKitCount($kit_info, $main_product_id, $kits_in_cart = null){
        $count = 0;

        if(!isset($kits_in_cart)) {

            $cart_products = $this->bundle_expert_cart->getCartProductsData();

            $kits_in_cart = array();

            
            foreach ($cart_products as $key=>$quantity) {
                $cart_product = unserialize(base64_decode($key));
                if (isset($cart_product['cart_kit_info'])) {
                    $kits_in_cart[$cart_product['cart_kit_info']['kit_unique_id']] = $cart_product['cart_kit_info'];
                }
            }
        }

        
        foreach ($kits_in_cart as $kit_in_cart){
            if($kit_in_cart['kit_id']==$kit_info['kit_id']) {
                $count++;
            }
        }

        return $count;

    }

    public function getCartProductsQuantity($ignore_kit_unique_id="", $ignore_product_id=""){

        return array();

        if(!$this->isEnabled()) return array();

        $products_quantity = array();

        $cart_products = $this->bundle_expert_cart->getCartProductsData();

        foreach ($cart_products as $key=>$quantity) {
            $cart_product_info = unserialize(base64_decode($key));

            if(isset($cart_product_info['cart_kit_info']) && $cart_product_info['cart_kit_info']['kit_unique_id']==$ignore_kit_unique_id)
                continue;

            if(!isset($cart_product_info['cart_kit_info']) && $cart_product_info['product_id']==$ignore_product_id)
                continue;

            
            if (!isset($products_quantity[$cart_product_info['product_id']]))
                $products_quantity[$cart_product_info['product_id']] = 0;

            $products_quantity[$cart_product_info['product_id']] += $quantity;
        }

        return $products_quantity;
    }

    public function getCartOptionsQuantity($ignore_kit_unique_id="", $ignore_product_id=""){

        return array();

        if(!$this->isEnabled()) return array();

        $options_quantity = array();

        $cart_products = $this->cart->getProducts();

        foreach ($cart_products as $cart_product) {
            $cart_product_info = unserialize(base64_decode($cart_product['key']));

            if(isset($cart_product_info['cart_kit_info']) && $cart_product_info['cart_kit_info']['kit_unique_id']==$ignore_kit_unique_id)
                continue;

            if(!isset($cart_product_info['cart_kit_info']) && $cart_product_info['product_id']==$ignore_product_id)
                continue;

            
            foreach ($cart_product['option'] as $option) {

                if (!empty($option['product_option_value_id'])) {

                    if (!isset($options_quantity[$option['product_option_value_id']]))
                        $options_quantity[$option['product_option_value_id']] = 0;

                    $options_quantity[$option['product_option_value_id']] += $cart_product['quantity'];
                }
            }
        }

        return $options_quantity;
    }

    
    public function addProductsQuantityByOpenedKit($products, $item_position,$products_quantity){

        if (!$this->isEnabled()) return array();

        foreach ($products as $index => $product) {
            if ($index == $item_position)
                continue;

            
            if (!isset($products_quantity[$product['product_id']]))
                $products_quantity[$product['product_id']] = 0;

            $products_quantity[$product['product_id']] += $product['quantity'];
        }

        return $products_quantity;
    }

    
    public function addOptionsQuantityByOpenedKit($products, $item_position,$options_quantity){

        if (!$this->isEnabled()) return array();

        foreach ($products as $index => $product) {
            if ($index == $item_position)
                continue;
            
            if (isset($product['option'])) {
                foreach ($product['option'] as $product_option_id => $value) {

                    $option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                    if ($option_query->num_rows) {
                        if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
                            $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                            if ($option_value_query->num_rows) {
                                $product_option_value_id = $value;

                                if (!isset($options_quantity[$product_option_value_id]))
                                    $options_quantity[$product_option_value_id] = 0;

                                $options_quantity[$product_option_value_id] += $product['quantity'];

                            }
                        } elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                            foreach ($value as $product_option_value_id) {
                                $option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

                                if ($option_value_query->num_rows) {


                                    if (!isset($options_quantity[$product_option_value_id]))
                                        $options_quantity[$product_option_value_id] = 0;

                                    $options_quantity[$product_option_value_id] += $product['quantity'];


                                }
                            }
                        }
                    }

                }
            }
        }

        return $options_quantity;
    }

    
    
    public function correctCartQuantities($kit_items, $quantity_data){

        $products_quantity = $quantity_data['products_quantity'];
        $options_quantity = $quantity_data['options_quantity'];

        foreach ($kit_items as $kit_item) {
            $product_id = $kit_item['products'][0]['product_id'];
            $quantity = $kit_item['products'][0]['quantity'];

            if(isset($products_quantity[$product_id]))
                $products_quantity[$product_id] -= $quantity;

            $this->checkProductInfo($kit_item['products'][0]);

            $options = $kit_item['products'][0]['product_info']['options'];

            foreach ($options as $index1=>$option) {
                $product_option_id = $option['product_option_id'];

                foreach ($option['product_option_value'] as $index2 => $product_option_value) {
                    $product_option_value_id = $product_option_value['product_option_value_id'];
                    if(isset($options_quantity[$product_option_value_id]))
                        $options_quantity[$product_option_value_id]-=$quantity;
                }
            }
        }

        $quantity_data['products_quantity']=$products_quantity;
        $quantity_data['options_quantity']=$options_quantity;

        return $quantity_data;
    }

    public function checkKitItemProductStock($kit_item_product, $quantity_data, $enable_stock_out_products_show=true){

        if(!$this->isEnabled()) return;




        $stock = true;

        if($this->bundle_expert_settings['stock_out_products_show'] && $enable_stock_out_products_show){
            $stock = true;
        }else{
            if(!$this->config->get('config_stock_checkout')) {
                $product_id = $kit_item_product['product_id'];

                
                $this->checkProductInfo($kit_item_product);
                $options = $kit_item_product['product_info']['options'];

                foreach ($options as $option) {
                    if ($stock && $option['product_option_value']) {
                        $has_not_empty_options = false;
                        foreach ($option['product_option_value'] as $product_option_value) {

                            if (!isset($quantity_data['options_quantity'][$product_option_value['product_option_value_id']]))
                                $quantity_data['options_quantity'][$product_option_value['product_option_value_id']] = 0;


                            if ($option['required'] && $product_option_value['subtract'] && (!$product_option_value['quantity'] || ($product_option_value['quantity'] < ($quantity_data['options_quantity'][$product_option_value['product_option_value_id']] + $kit_item_product['quantity'])))) {


                                
                                

                            } else {
                                $quantity_data['options_quantity'][$product_option_value['product_option_value_id']] += $kit_item_product['quantity'];
                                $has_not_empty_options = true;
                            }
                        }
                        if(!$has_not_empty_options){
                            $stock = false;
                        }
                    }

                }

                
                if (!isset($quantity_data['products_quantity'][$product_id]))
                    $quantity_data['products_quantity'][$product_id] = 0;

                $this->checkProductInfo($kit_item_product);

                if ($quantity_data['products_quantity'][$product_id] + $kit_item_product['quantity'] > $kit_item_product['product_info']['quantity']) {
                    $stock = false;
                } else {
                    
                    $quantity_data['products_quantity'][$product_id] += $kit_item_product['quantity'];
                }

            }
        }


        return $stock;
    }


    
    public function getCartFreeProducts(){

        if(!$this->isEnabled()) return array();

        $products = array();


        $cart_products = $this->cart->getProducts();

        
        foreach ($cart_products as $cart_product) {
            if(isset($cart_product['key'])) {
                $cart_product_info = unserialize(base64_decode($cart_product['key']));
                if (!isset($cart_product_info['cart_kit_info'])) {
                    $products[] = $cart_product;
                }
            }
        }

        return $products;

    }

    public function getFreeMainProductInCart($product_id, $quantity) {

        if(!$this->isEnabled()) return array();

        $free_product = array();


        $cart_products = $this->bundle_expert_cart->getCartProductsData();

        $product_id = (int) $product_id;
        $quantity = (int) $quantity;
        foreach ($cart_products as $key=>$cart_quantity) {
            $cart_product = unserialize(base64_decode($key));
            if (!isset($cart_product['cart_kit_info'])) {
                if ($product_id == $cart_product['product_id'] && $quantity <= $cart_quantity) {
                    $free_product = $cart_product;;
                    break;
                }
            }
        }

        return $free_product;
    }

    public function setPresetOptions($target_options, $source_options){

        if(!$this->isEnabled()) return array();

        foreach ($target_options as $index1=>$target_option) {
            $product_option_id = $target_option['product_option_id'];
            if (!$target_option['product_option_value']) {
                if (isset($source_options[$product_option_id])){
                    $target_options[$index1]['value'] = $source_options[$product_option_id];
                }
            } else {
                foreach ($target_option['product_option_value'] as $index2 => $product_option_value) {
                    $product_option_value_id = $product_option_value['product_option_value_id'];
                    if (isset($source_options[$product_option_id])){
                        if(is_array($source_options[$product_option_id])){
                            if(in_array($product_option_value_id, $source_options[$product_option_id])){
                                $target_options[$index1]['product_option_value'][$index2]['preset_option_value'] = true;
                            }
                        }else{
                            if ($source_options[$product_option_id] == $product_option_value_id) {
                                $target_options[$index1]['product_option_value'][$index2]['preset_option_value'] = true;
                            }
                        }
                    }
                }
            }
        }

        return $target_options;
    }

    public function getCartKit($kit_unique_id, $admin_mode=false){

        if(!$this->isEnabled()) return array();

        $kit_items = array();
        $kit_items_free = array();

        $cart_products = $this->cart->getProductsDefault();
        



        $kit_id = '';

        $main_product_id='';

        foreach ($cart_products as $cart_product){
            $cart_product_info = unserialize(base64_decode($cart_product['key']));
            if (isset($cart_product_info['cart_kit_info'])) {
                if($cart_product_info['cart_kit_info']['kit_unique_id'] == $kit_unique_id){

                    





                    $kit_id = $cart_product_info['cart_kit_info']['kit_id'];
                    $main_product_id = $cart_product_info['cart_kit_info']['main_product_id'];
                    $item_position = $cart_product_info['cart_kit_info']['item_position'];
                    $product_id = $cart_product_info['product_id'];

                    $kit_product = $this->getKitProduct($kit_id, $main_product_id, $item_position, $product_id, $admin_mode, $kit_unique_id);

                    if(isset($cart_product_info['cart_kit_info']['has_product_as_kit_quantity'])){
                        $cart_product['quantity'] /= $cart_product_info['cart_kit_info']['has_product_as_kit_quantity'];
                        $cart_product['weight'] /= $cart_product_info['cart_kit_info']['has_product_as_kit_quantity'];
                    }

                    if($kit_product['products'][0]['quantity_edit']){
                        $kit_product['products'][0]['quantity'] = $cart_product['quantity'];
                    }



                    
                    $this->checkProductInfo($kit_product['products'][0]);

                    if(isset($cart_product_info['option']) && isset($kit_product['products'][0]['product_info']['options'])) {
                        $kit_item_product_options = $kit_product['products'][0]['product_info']['options'];
                        $cart_product_options = $cart_product_info['option'];

                        $kit_product['products'][0]['product_info']['options'] = $this->setPresetOptions($kit_item_product_options, $cart_product_options);
                    }

                    if(!$cart_product_info['cart_kit_info']['is_free_product']){
                        $kit_items[$kit_product['item_position']] = $kit_product;
                    }else{
                        $kit_items_free[] = $kit_product;
                    }

                }
            }
        }

        
        $kit_info_items = $this->getKitItems($kit_id);
        $get_kit_products_settings = array(
            'main_product_id' => $main_product_id,
            'filter_kit_items' => true,
            'only_first' => true,
            'admin_mode' => $admin_mode,
        );
        $kit_products = $this->getKitProducts($kit_id, $get_kit_products_settings);
        foreach ($kit_info_items as $index => $kit_info_item) {
            foreach ($kit_info_item as $index2 => $kit_info_item_product) {
                if(!isset($kit_items[$index])){

                    $this->itemInfoCheck($kit_info_item_product);
                    if($kit_info_item_product['item_info']['item_empty_mode']['enable']){
                        $kit_items[$index]=$kit_products[$index];
                    }
                }
                break;

            }

        }

        foreach ($kit_items_free as $kit_item_free){
            $kit_items[] = $kit_item_free;
        }

        ksort($kit_items);

        $kit_info = $this->getKit($kit_id, $kit_unique_id);

        if(!empty($kit_info))
            $kit_info['kit_items'] = $kit_items;


        return $kit_info;
    }

    public function getCartKitMainProduct($kit_unique_id){

        if(!$this->isEnabled()) return;

        $cart_products = $this->bundle_expert_cart->getCartProductsData();

        $main_product_id = '';

        foreach ($cart_products as $key=>$quantity) {
            $cart_product = unserialize(base64_decode($key));
            if (isset($cart_product['cart_kit_info'])) {
                if ($cart_product['cart_kit_info']['kit_unique_id'] == $kit_unique_id) {
                    $main_product_id = $cart_product['cart_kit_info']['main_product_id'];
                    break;
                }
            }
        }

        return $main_product_id;
    }

    public function getOrderKits($order_id){

        if(!$this->isEnabled()) return array();

        

        $order_kits = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "'");

        foreach ($query->rows as $row) {
            $cart_kit_info = json_decode($row['cart_kit_info'], true);

            if(isset($cart_kit_info) && !empty($cart_kit_info)){
                $kit_info = $this->getKit($cart_kit_info['kit_id'], $cart_kit_info['kit_unique_id']);
                if ($kit_info) {
                    $order_kits[$cart_kit_info['kit_unique_id']] = $kit_info;
                }
            }
        }

        return $order_kits;

    }

    public function updateKitQuantity($kit_id, $quantity, $kit_from_cart_unique_id=''){
        



        $kit_info = $this->getKit($kit_id, $kit_from_cart_unique_id);

        if($kit_info) {
            $kit_quantity_mode = $kit_info['kit_quantity_mode'];

            $kit_quantity_mode['value'] += $quantity;

            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert SET kit_quantity_mode = '" . $this->db->escape(json_encode($kit_quantity_mode)) . "' WHERE kit_id = '" . (int)$kit_id . "'");
        }

    }

    public function addOrderKitHistory($kit_id, $kit_unique_id, $main_product_id, $order_id){


        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order_kit_info WHERE kit_unique_id = '" . $this->db->escape($kit_unique_id) . "'");

        if ($query->num_rows == 0) {
            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => false,
                'only_first' => false,
                'admin_mode' => true,
            );

            $kit_info = $this->getKit($kit_id, $kit_unique_id, $get_kit_products_settings);


            foreach ($kit_info['kit_items'] as $index1=>$kit_item){
                foreach ($kit_item['products'] as $index2=>$kit_item_product){
                    unset($kit_info['kit_items'][$index1]['products'][$index2]['product_info']);
                }
            }

            $kit_info_serialize = base64_encode(serialize($kit_info));

            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_order_kit_info SET order_id ='" . (int)$order_id. "', kit_unique_id = '" . $this->db->escape($kit_unique_id) . "', kit_info = '" . $kit_info_serialize . "'");
        }

    }

    public function getKitInfoFromOrderHistory($kit_unique_id) {

        if(!$this->isEnabled()) return array();

        $this->load->model('catalog/product');
        

        $kit_info = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_order_kit_info WHERE kit_unique_id = '" . $this->db->escape($kit_unique_id) . "'";

        $query = $this->db->query($sql);

        if($query->row){
            $check_serialized = @unserialize(base64_decode($query->row['kit_info']));

            if($check_serialized){
                $kit_info =  unserialize(base64_decode($query->row['kit_info']));
            }else{
            }

            if($kit_info){
                foreach ($kit_info['kit_items'] as $index1=>$kit_item){
                    foreach ($kit_item['products'] as $index2=>$kit_item_product){
                        $product_info = $this->getProductInfo($kit_item_product['product_id']);;
                        $options = $this->getProductOptions($kit_item_product['product_id']);;;

                        $product_info['options'] = $this->filterProductOptions($kit_item_product, $options);

                        $this->checkProductInfo($kit_info['kit_items'][$index1]['products'][$index2]);

                        $kit_info['kit_items'][$index1]['products'][$index2]['product_info'] = $product_info;
                    }
                }
            }
        }

        return $kit_info;
    }

    
    public function addKitHistoryStatus($kit_info, $kit_enable_status){

        if (isset($kit_enable_status['kit_history_data']['main_product_id']))
            $main_product_id = $kit_enable_status['kit_history_data']['main_product_id'];
        else
            $main_product_id = '';

        if (isset($kit_enable_status['kit_history_data']['position']))
            $position = $kit_enable_status['kit_history_data']['position'];
        else
            $position = '';

        if ($kit_enable_status['kit_history_code'] == 'success')
            $success = true;
        else
            $success = false;

        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_kit_history_status WHERE kit_id = '" . (int)$kit_info['kit_id'] . "' AND main_product_id = '" . $this->db->escape($main_product_id) . "'");

        if (empty($query->row)) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_kit_history_status SET kit_id ='" . (int)$kit_info['kit_id'] . "', main_product_id = '" . $this->db->escape($main_product_id) . "', position = '" . $this->db->escape($position) . "', kit_history_code = '" . $this->db->escape($kit_enable_status['kit_history_code']) . "', success = '" . (int)$success . "', date_modified = NOW()");
        } else {
            if ($query->row['success'] != $success) {
                $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_kit_history_status SET main_product_id = '" . $this->db->escape($main_product_id) . "', position = '" . $this->db->escape($position) . "', kit_history_code = '" . $this->db->escape($kit_enable_status['kit_history_code']) . "', success = '" . (int)$success . "', date_modified = NOW() WHERE kit_id = '" . (int)$kit_info['kit_id'] . "' AND main_product_id = '" . $this->db->escape($main_product_id) . "'");
            }

        }


    }

    public function checkFreeMainProductInCart($main_product_id){
        $check = false;
        $free_products = $this->getCartFreeProducts();

        foreach ($free_products as $free_product){
            if($free_product['product_id'] == $main_product_id){
                $check = true;
                break;
            }
        }
        return $check;
    }


    public function updateHelpTutorial(){

        $this->load->model('setting/setting');

        $help_data = $this->model_setting_setting->getSetting('bundle_expert_help');

        
        $need_update = false;
        if (empty($help_data['bundle_expert_help_update'])) {
            $need_update = true;
        } else {
            $now = new DateTime();
            $date = DateTime::createFromFormat("Y-m-d H:i:s", $help_data['bundle_expert_help_update']); 
            $interval = $now->diff($date);
            if ($interval->d >= 10) {
                $need_update = true;
            }
        }

        if ($need_update) {
            $data['help'] = true;
            $data['domain'] = htmlspecialchars_decode($this->request->server['HTTP_HOST']);
            $data['market_id'] = $this->market_id;
            $result = $this->opencart_expert_api('help/help/getHelpTotorial', $data);

            if ($result && isset($result['help_data'])) {
                $help_data['bundle_expert_help_url'] = $result['help_data']['bundle_expert_help_url'];
                $help_data['bundle_expert_help_data'] = $result['help_data']['bundle_expert_help_data'];

            }

            $now = new DateTime();
            $help_data['bundle_expert_help_update'] = $now->format('Y-m-d H:i:s');;

            $this->model_setting_setting->editSetting('bundle_expert_help', $help_data);
        }

    }

    public function checkProductIsCollection($product_id) {
        $check = false;

        $this->load->model('catalog/product');

        

        $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "'  AND be.status = 1 AND betp.mode = 'collection' GROUP BY betp.kit_id");
        if($query->num_rows){
            $check = true;
            return $check;
        }

        return $check;
    }

    
    
    public function checkProductIsKit($product_id, $use_expire_data=false) {
        $check = false;

        $this->load->model('catalog/product');

        
        
        if(!$use_expire_data){
            $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "'  AND be.status = 1 AND betp.mode = 'kit_as_product' GROUP BY betp.kit_id");
        }else{
            $sql = "SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) LEFT JOIN " . DB_PREFIX . "bundle_expert_kit_period bekp ON (be.kit_id = bekp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "'  AND be.status = 1 AND betp.mode = 'kit_as_product' AND (bekp.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' OR bekp.customer_group_id = -1000) AND (bekp.date_start = '0000-00-00' OR bekp.date_start <= CURDATE()) AND (bekp.date_end = '0000-00-00' OR bekp.date_end >= CURDATE()) GROUP BY betp.kit_id";
            $query = $this->db->query($sql);

        }

        if($query->num_rows){
            $check = true;
            return $check;
        }

        return $check;

        
        $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'product' AND betp.item_id = '" . (int)$product_id . "'  AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id");
        if($query->num_rows){
            $check = true;
            return $check;
        }

        $ocmod_point_011 = 1;

        

        $categories = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $categories = $categories->rows;
        if(!empty($categories)) {
            $category_list = array();
            foreach ($categories as $category) {
                $category_list[] = $category['category_id'];
            }

            $category_list = implode(',', $category_list);

            $sql = "SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'category' AND betp.item_id IN (" . $this->db->escape($category_list) . ")  AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $check = true;
                return $check;
            }
        }

        
        if(!isset($manufacturer_id)) {
            $product_info = $this->getProductInfoDefault($product_id);
            $manufacturer_id = $product_info['manufacturer_id'];
        }
        if(!empty($manufacturer_id)){
            $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'manufacturer' AND betp.item_id = '" . (int)$manufacturer_id . "'  AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id");
            if($query->num_rows){
                $check = true;
                return $check;
            }
        }

        
        



        return $check;

    }


    
    
    public function checkProductIsKit_LE($product_id, $manufacturer_id=null) {
        $check = false;

        $this->load->model('catalog/product');

        
        $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "'  AND be.status = 1 AND betp.mode = 'kit_as_product_light_mode' GROUP BY betp.kit_id");
        if($query->num_rows){
            $check = true;
            return $check;
        }

        return $check;

        
        $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'product' AND betp.item_id = '" . (int)$product_id . "'  AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id");
        if($query->num_rows){
            $check = true;
            return $check;
        }

        

        $categories = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $categories = $categories->rows;
        if(!empty($categories)) {
            $category_list = array();
            foreach ($categories as $category) {
                $category_list[] = $category['category_id'];
            }

            $category_list = implode(',', $category_list);

            $sql = "SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'category' AND betp.item_id IN (" . $this->db->escape($category_list) . ")  AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $check = true;
                return $check;
            }
        }

        
        if(!isset($manufacturer_id)) {
            $product_info = $this->getProductInfoDefault($product_id);
            $manufacturer_id = $product_info['manufacturer_id'];
        }
        if(!empty($manufacturer_id)){
            $query = $this->db->query("SELECT betp.*, be.status FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'manufacturer' AND betp.item_id = '" . (int)$manufacturer_id . "'  AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id");
            if($query->num_rows){
                $check = true;
                return $check;
            }
        }

        
        



        return $check;

    }

    public function checkProductIsSeries($product_id, $manufacturer_id=null) {
        $kit_id = null;

        $this->load->model('catalog/product');

        $query = $this->db->query("SELECT belp.*, be.status FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products belp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = belp.kit_id) WHERE belp.product_id = '" . (int)$product_id . "'  AND be.status = 1 AND be.main_mode = 'series' ");
        if($query->num_rows){

            $kit_id = $query->row['kit_id'];

            return $kit_id;
        }

        return $kit_id;

    }

    public function getKitProductsMinMaxPrices($kit_id, $item_position=null) {
        $result = array(
            'min_price'=>'',
            'max_price'=>'',
            'min_special'=>'',
            'max_special'=>'',
            'min_value'=>'',
            'max_value'=>'',
        );

        $this->load->model('catalog/product');

        $sql = "SELECT bep.product_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_products bep WHERE bep.kit_id = '" . (int)$kit_id . "'";

        if(isset($item_position)){
            $sql .= " AND bep.item_position = ".(int)$item_position."";
        }

        $query_products = $this->db->query($sql);
        if($query_products->num_rows){
            $products_list = array();
            foreach ($query_products->rows as $row){
                $products_list[]=$row['product_id'];
            }
            $products_list = implode(',', $products_list);

            $query = $this->db->query("SELECT p.product_id, p.price FROM " . DB_PREFIX . "product p WHERE p.product_id IN (".$products_list.") ORDER BY p.price");

            if($query->num_rows){
                $min_price =$query->rows[0]['price'];
                $max_price =$query->rows[count($query->rows)-1]['price'];

                $result['min_price'] = $min_price;
                $result['max_price'] = $max_price;
            }

            $query = $this->db->query("SELECT ps.product_id, ps.price as special FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id IN (".$products_list.") ORDER BY ps.price");


            if($query->num_rows){
                $min_special =$query->rows[0]['special'];
                $max_special =$query->rows[count($query->rows)-1]['special'];

                $result['min_special'] = $min_special;
                $result['max_special'] = $max_special;
            }

            $r = array_diff($result, array(null));
            $min_value = min($r);
            $max_value = max($r);

            $result['min_value'] = $min_value;
            $result['max_value'] = $max_value;
        }

        return $result;

    }

    public function getProductAsKitKits($product_id, $manufacturer_id=null) {
        $kits = array();



        
        
        $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "' AND be.status = 1 AND betp.mode = 'kit_as_product' GROUP BY betp.kit_id");
        if($query->num_rows){
            $kits = $query->rows;

        }

        return $kits;

        $this->load->model('catalog/product');
        
        $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'product' AND betp.item_id = '" . (int)$product_id . "' AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id");
        if($query->num_rows){
            $kits = $query->rows;
            return $kits;
        }

        $ocmod_point_012 = 1;

        

        $categories = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $categories = $categories->rows;
        if(!empty($categories)) {
            $category_list = array();
            foreach ($categories as $category) {
                $category_list[] = $category['category_id'];
            }

            $category_list = implode(',', $category_list);

            $sql = "SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'category' AND betp.item_id IN (" . $this->db->escape($category_list) . ")  AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $kits = $query->rows;
                return $kits;
            }
        }

        
        if(!isset($manufacturer_id)) {
            $product_info = $this->getProductInfoDefault($product_id);
            $manufacturer_id = $product_info['manufacturer_id'];
        }
        if(!empty($manufacturer_id)){
            $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'manufacturer' AND betp.item_id = '" . (int)$manufacturer_id . "'  AND be.status = 1 AND be.kit_as_product = 1 GROUP BY betp.kit_id");
            if($query->num_rows){
                $kits = $query->rows;
                return $kits;
            }
        }

        
        



        return $kits;

    }

    public function getProductAsKitKits_LE($product_id, $manufacturer_id=null) {
        $kits = array();

        
        
        $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.product_id = '" . (int)$product_id . "' AND be.status = 1 AND betp.mode = 'kit_as_product_light_mode' GROUP BY betp.kit_id");
        if($query->num_rows){
            $kits = $query->rows;

        }
        return $kits;

        $this->load->model('catalog/product');

        
        $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'product' AND betp.item_id = '" . (int)$product_id . "' AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id");
        if($query->num_rows){
            $kits = $query->rows;
            return $kits;
        }

        

        $categories = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        $categories = $categories->rows;
        if(!empty($categories)) {
            $category_list = array();
            foreach ($categories as $category) {
                $category_list[] = $category['category_id'];
            }

            $category_list = implode(',', $category_list);

            $sql = "SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'category' AND betp.item_id IN (" . $this->db->escape($category_list) . ")  AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $kits = $query->rows;
                return $kits;
            }
        }

        
        if(!isset($manufacturer_id)) {
            $product_info = $this->getProductInfoDefault($product_id);
            $manufacturer_id = $product_info['manufacturer_id'];
        }
        if(!empty($manufacturer_id)){
            $query = $this->db->query("SELECT be.kit_id FROM " . DB_PREFIX . "bundle_expert_to_product betp LEFT JOIN " . DB_PREFIX . "bundle_expert be ON (be.kit_id = betp.kit_id) WHERE betp.item_type = 'manufacturer' AND betp.item_id = '" . (int)$manufacturer_id . "'  AND be.status = 1 AND be.kit_as_product_light_mode = 1 GROUP BY betp.kit_id");
            if($query->num_rows){
                $kits = $query->rows;
                return $kits;
            }
        }

        
        



        return $kits;

    }

    
    public function getDataKit($kit_id, $main_product_id = '', $kit_items = null, $filter_products = false, $only_first = false, $admin_mode = false, $kit_from_cart_unique_id='', $use_tax = true, $product_as_kit_quantity=0){
        
        if (!isset($kit_items)) {
            $get_kit_products_settings = array(
                'main_product_id' => $main_product_id,
                'filter_kit_items' => $filter_products,
                'only_first' => $only_first,
                'admin_mode' => $admin_mode,
            );
            $kit_items = $this->getKitProducts($kit_id, $get_kit_products_settings);
        }

        $kit_info = $this->getKit($kit_id, $kit_from_cart_unique_id);
        $kit_info['kit_items'] = $kit_items;

        $kit_edit = false;
        $kit_has_only_simple_options = false;
        $kit_has_require_options = false;
        $kit_has_select_products = false;

        $total_kit = 0;
        $total_kit_tax = 0;
        $total_default = 0;
        $total_default_no_tax = 0;
        $total_default_new = 0;

        $total_product_count = 0;

        $kit_weight = 0;

        $kit_items_data = array();

        $ocmod_point_014 = 1;

        $kit_has_select_products = false;

        foreach ($kit_items as $index1 => $kit_item) {

            $kit_items_data[$index1] = array('products' => array(), 'selectable' => false);

            $kit_items_data[$index1]['is_free_product'] = $kit_item['is_free_product'];
            $kit_items_data[$index1]['free_product_in_kit'] = isset($kit_item['free_product_in_kit'])?$kit_item['free_product_in_kit']:0;
            $kit_items_data[$index1]['item_position'] = $kit_item['item_position'];
            $kit_items_data[$index1]['empty_mode_item_is_empty'] = isset($kit_item['empty_mode_item_is_empty'])?$kit_item['empty_mode_item_is_empty']:0;
            
            
            
            if(isset($kit_item['empty_mode_item_is_empty'])){
                $kit_items_data[$index1]['empty_mode_item_is_empty'] = $kit_item['empty_mode_item_is_empty'];
            }else{
                if (isset($kit_item['products'][0]) && $kit_item['products'][0]['item_empty_mode']['enable'] && $kit_item['products'][0]['item_empty_mode']['default_empty']) {
                    $kit_items_data[$index1]['empty_mode_item_is_empty'] = 1;
                } else {
                    $kit_items_data[$index1]['empty_mode_item_is_empty'] = 0;
                }
            }
            

            if ($kit_item['selectable'])
                $kit_has_select_products = true;

            foreach ($kit_item['products'] as $index2 => $kit_product) {

                
                if (($only_first && $index2 > 0) && !$kit_product['is_free_product']) {
                    break;
                }
                

                
                
                if ((($only_first && $index2 == 0) || !$only_first) || $kit_product['is_free_product']) {

                    $kit_product_data = $this->getDataKitProduct($kit_info, $kit_product, $use_tax);

                    
                    

                    if($kit_from_cart_unique_id!='') {
                        
                        $is_item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_from_cart_unique_id, $kit_item['item_position']);
                    }else{
                        $is_item_in_cart = false;
                    }

                    if (($index2 == 0 && (!$kit_product['is_free_product'] || $is_item_in_cart))
                        || ($kit_product['is_free_product'] && $kit_product['free_product_in_kit'])) {
                        $item_in_cart = $this->bundle_expert_cart->checkKitItemInCart($kit_from_cart_unique_id, $index1);
                        if ($kit_product['item_empty_mode']['enable'] && $kit_product['item_empty_mode']['default_empty'] && !$item_in_cart) {
                        }else {
                            $this->checkProductInfo($kit_product_data);

                            $total_default += $kit_product_data['product_info']['price_value'] * $kit_product['quantity'];
                            $total_default_no_tax += $kit_product_data['product_info']['price_value_no_tax'] * $kit_product['quantity'];

                            if ($kit_product_data['product_info']['special_value']!==false)
                                $total_default_new += $kit_product_data['product_info']['special_value'] * $kit_product['quantity'];
                            else
                                $total_default_new += $kit_product_data['product_info']['price_value'] * $kit_product['quantity'];

                            if ($kit_product_data['product_info']['special']!==false) {
                                $total_kit += $kit_product_data['product_info']['special_value'] * $kit_product['quantity'];
                            } else {
                                $total_kit += $kit_product_data['product_info']['price_value'] * $kit_product['quantity'];
                            }

                            $total_kit_tax += $kit_product_data['product_info']['tax_value'];


                            
                            $product_info = $kit_product_data['product_info'];

                            $weight_class_id = $product_info['weight_class_id'];

                            $config_weight_class_id = $this->config->get('config_weight_class_id');

                            $product_weight = $product_info['weight'];

                            $product_weight = $this->weight->convert($product_weight, $weight_class_id, $config_weight_class_id);

                            $kit_weight += $product_weight* $kit_product['quantity'];



                            $total_product_count +=$kit_product['quantity'];
                        }
                    }



                    $kit_items_data[$index1]['selectable'] = $kit_item['selectable'];

                    if ($index2 == 0) {
                        $selected = true;
                    } else {
                        $selected = false;
                    }

                    if ($kit_product['is_free_product']) {
                        if(isset($kit_product['free_product_in_kit']) && $kit_product['free_product_in_kit']){
                            $selected = true;
                        }else{
                            $selected = false;
                        }
                    }else{
                        if ($kit_product['item_mode'] == 'select_product' && $kit_product['item_empty_mode']['enable'] && $kit_product['item_empty_mode']['default_empty']) {
                            $selected = false;
                        } else {
                            $selected = true;
                        }
                    }



                    $this->checkProductInfo($kit_product_data);

                    $kit_product_data['product_info']['selected'] = $selected;
                    $kit_product_data['product_info']['select_product'] = ($kit_items_data[$index1]['selectable'] == true) ? true : false;

                    $kit_items_data[$index1]['products'][$index2] = $kit_product_data;

                } else {
                    unset($kit_items[$index1]['products'][$index2]);
                }

                $this->checkProductInfo($kit_product);

                $product_options = $kit_product['product_info']['options'];

                foreach ($product_options as $product_option) {

                    if (!$product_option['required']) {
                        $kit_has_only_simple_options = true;
                    }
                    if (isset($product_option['fixed_value'])) {
                        
                        
                    }

                    if (isset($product_option['fixed_value'])){
                        if ($product_option['required'] && !$product_option['fixed_value']) {
                            $kit_has_require_options = true;
                            $kit_has_only_simple_options = false;
                            break;
                        }
                    }




                }

            }

        }

        
        
        $kit_total_data = $this->calculateKitTotal($kit_info, $total_kit, $total_kit_tax, $total_product_count, $product_as_kit_quantity, $use_tax, $main_product_id);
















        
        if($kit_info['bundle_total_price_hide_special']){
            $total_default = $kit_total_data['total_kit'];
            $total_default_new = $kit_total_data['total_kit'];
            $total_default_no_tax = $kit_total_data['total_kit'];
            $kit_total_data['total_kit_old'] = false;
        }
        

        
        $profit_value = abs($kit_total_data['total_kit'] - $total_default);
        $profit_price = $this->currency->format($profit_value, $this->session->data['currency']);;
        $percent = $total_default / 100;
        if ($percent == 0) {
            $profit_percent = 0;
        } else {
            $profit_percent = round($profit_value / $percent);
        }
        if ($kit_total_data['total_kit'] - $total_default >= 0) {
            $profit_prefix = '+';
        } else {
            $profit_prefix = '-';
        }

        $data = array(
            'kit_items' => $kit_items_data,
            'kit_edit' => $kit_edit,
            'kit_has_only_simple_options' => $kit_has_only_simple_options,
            'kit_has_require_options' => $kit_has_require_options,
            'kit_has_select_products' => $kit_has_select_products,
            'total_default_new' => $total_default_new,
            'total_default' => $total_default,
            'total_default_no_tax' => $total_default_no_tax,
            'total_kit_old' => $kit_total_data['total_kit_old'],
            'total_kit' =>  $kit_total_data['total_kit'],
            'total_kit_tax' =>  $kit_total_data['total_kit_tax'],
            'kit_weight' =>  $this->weight->format($kit_weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
            'kit_weight_value' =>  $kit_weight,

            'profit_value' =>  $profit_value,
            'profit_price' =>  $profit_price,
            'profit_percent' =>  $profit_percent,
            'profit_prefix' =>  $profit_prefix,
        );


        return $data;
    }

    public function getDataKitProduct($kit_info, $kit_product, $use_tax = true){
        $kit_product_data = array();
        $this->load->model('tool/image');

        if (empty($image_sizes)) {
            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $image_width = $this->config->get('config_image_category_width');
                $image_height = $this->config->get('config_image_category_height');
            } else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $image_width = $this->config->get($this->config->get('config_theme') . '_image_category_width');
                    $image_height = $this->config->get($this->config->get('config_theme') . '_image_category_height');
                } else {
                    $image_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
                    $image_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
                }
            }
        } else {
            $image_width = $image_sizes['width'];
            $image_height = $image_sizes['height'];
        }

        $image_width = 160;
        $image_height = 160;










        $this->checkProductInfo($kit_product);

        $result = $kit_product['product_info'];

        $product_options = $kit_product['product_info']['options'];

        if ($result['image']) {
            $image = $this->model_tool_image->resize($result['image'], $image_width, $image_height);
        } else {
            $image = $this->model_tool_image->resize('placeholder.png', $image_width, $image_height);
        }

        $options_cart_format = $this->convertProductOptionsToCartOptionsFormat($product_options);

        $price_data = $this->getKitProductPriceData($kit_info, $kit_product, $result, $options_cart_format, $use_tax, $kit_product['quantity']);

        $price = $price_data['price'];
        $price_no_tax = $price_data['price_no_tax'];

        if ($price_data['special']!==false)
            $special = $price_data['special'];
        else
            $special = false;

        if ($this->config->get('config_tax')) {
            $tax = $this->currency->format($price_data['tax'], $this->session->data['currency']);
        } else {
            $tax = false;
        }

        if ($this->config->get('config_review_status')) {
            $rating = $result['rating'];
        } else {
            $rating = false;
        }

        
        $price_discount_text = '';

        
        $customer_group_id = $this->config->get('config_customer_group_id');
        if($kit_product['price_mode_to_customer_groups_status'] && isset($kit_product['price_mode_to_customer_groups'][$customer_group_id])){
            $new_price_mode = $kit_product['price_mode_to_customer_groups'][$customer_group_id];
            $kit_product['price_mode'] = $new_price_mode['price_mode'];
            switch ($kit_product['price_mode']){
                case 'product_price':
                    $kit_product['price'] = $new_price_mode['value'];
                    break;
                case 'product_price_minus_sum':
                    $kit_product['price_minus_sum'] = $new_price_mode['value'];
                    break;
                case 'product_price_minus_percent':
                    $kit_product['price_minus_percent'] = $new_price_mode['value'];
                    break;
                case 'fix_price':
                    break;
            }

        }
        

        if ($special!==false) {
            switch ($kit_product['price_mode']) {
                case "product_price_minus_sum":
                    $price_minus_special = $price - $special;
                    $price_discount_text = '-' . $this->currency->format($price_minus_special, $this->session->data['currency']);
                    break;
                case "fix_price":
                    $price_discount_text = $this->language->get('text_fix_price');;
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


        $kit_product_data = $kit_product;

        $this->checkProductInfo($kit_product_data);

        $kit_product_data['product_info']['name'] = $result['name'];
        $kit_product_data['product_info']['price'] = $this->currency->format($price, $this->session->data['currency']);
        $kit_product_data['product_info']['price_value'] = $price;
        $kit_product_data['product_info']['price_value_no_tax'] = $price_no_tax;
        $kit_product_data['product_info']['price_total'] = $this->currency->format($price*$kit_product['quantity'], $this->session->data['currency']);
        $kit_product_data['product_info']['special'] = ($special!==false) ? $this->currency->format($special, $this->session->data['currency']) : false;
        $kit_product_data['product_info']['special_value'] = ($special!==false) ? $special : false;
        $kit_product_data['product_info']['special_total'] = ($special!==false) ? $this->currency->format($special*$kit_product['quantity'], $this->session->data['currency']) : false;
        $kit_product_data['product_info']['tax'] = $tax;
        $kit_product_data['product_info']['tax_value'] = $price_data['tax'];
        $kit_product_data['product_info']['rating'] = $rating;
        $kit_product_data['product_info']['href'] = $this->url->link('product/product', 'product_id=' . $result['product_id']);
        $kit_product_data['product_info']['thumb'] = $image;
        $kit_product_data['product_info']['price_discount_text'] = $price_discount_text;




        $this->checkProductInfo($kit_product);

        $product_info = $kit_product['product_info'];

        $product_options = $product_info['options'];

        $options_data = array();

        foreach ($product_options as $option) {
            $product_option_value_data = array();

            $option_has_fixed_value = false;

            foreach ($option['product_option_value'] as $option_value) {
                
                
                
                if ($this->bundle_expert_settings['show_stockout_options'] || ($option_value['quantity'] > 0 || $this->config->get('config_stock_checkout') || !$option_value['subtract'])) {
                    
                    if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                        if(isset($this->tax)){
                            $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                        }else{
                            $price = $this->currency->format($option_value['price'], $this->session->data['currency']);
                        }
                    } else {
                        $price = false;
                    }

                    
                    $fixed_option_value = false;
                    if ($this->isFixedOptionValue($option['product_option_id'], $option_value['product_option_value_id'], $kit_product['fixed_options'])) {
                        $fixed_option_value = true;
                        $option_has_fixed_value = true;
                    }

                    if(isset($option_value['image']) && $option_value['image']){
                        $option_image = $this->model_tool_image->resize($option_value['image'], 50, 50);
                    }else{
                        $option_image = null;
                    }

                    $option_value['price_text']=$option_value['price_prefix'] . $price;

                    
                    if ($option_value['price_prefix'] == "d") {
                        $option_value['price_text'] = "-" . (float)$option_value['price'] . "%";
                    }
                    if ($option_value['price_prefix'] == "u") {
                        $option_value['price_text'] = "+" . (float)$option_value['price'] . "%";;
                    }
                    if ($option_value['price_prefix'] == "/") {
                        $option_value['price_text'] = "/" . (float)$option_value['price'] . "";
                    }
                    if ($option_value['price_prefix'] == "*") {
                        $option_value['price_text'] = "*" . (float)$option_value['price'] . "";;
                    }
                    if ($option_value['price_prefix'] == "=") {
                        $option_value['price_text'] = "=" . $price . "";
                    }

                    $product_option_value_data[] = array(
                        'product_option_value_id' => $option_value['product_option_value_id'],
                        'option_value_id' => $option_value['option_value_id'],
                        'unique_id' => uniqid(),
                        'name' => isset($option_value['name'])?$option_value['name']:'',
                        'image' => $option_image,
                        'price' => $price,
                        'quantity' => $option_value['quantity'],
                        'subtract' => $option_value['subtract'],
                        'price_prefix' => $option_value['price_prefix'],
                        'price_text' => $option_value['price_text'],
                        
                        'fixed_option_value' => $fixed_option_value,
                        'preset_option_value' => isset($option_value['preset_option_value']) ? $option_value['preset_option_value'] : false
                    );
                }
            }

            if(!empty($product_option_value_data)){
                $options_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value' => $product_option_value_data,
                    'option_id' => $option['option_id'],
                    'unique_id' => uniqid(),
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'value' => $option['value'],
                    'required' => $option['required'],
                    'option_has_fixed_value' => $option_has_fixed_value,
                    'preset_option' => isset($option['preset_option']) ? $option['preset_option'] : ''

                );
            }
        }

        $this->checkProductInfo($kit_product_data);

        $kit_product_data['product_info']['options'] = $options_data;

        return $kit_product_data;
    }

    public function getProductAsKitPrice($product_id, $use_tax = true){
        $product_as_kit_price = array();

        if(isset($this->product_as_kit_dinamic_price_cache[$product_id])){
            $product_as_kit_price = $this->product_as_kit_dinamic_price_cache[$product_id];
        }else{
            if ($this->bundle_expert->checkProductIsKit($product_id)) {
                $kits = $this->bundle_expert->getProductAsKitKits($product_id);
                if (!empty($kits)) {
                    
                    foreach ($kits as $kit) {
                        $kit_id = $kit['kit_id'];

                        $kit_info = $this->getKit($kit_id);
                        if (!empty($kit_info)) {
                            $main_product_id = $product_id;
                            $main_product_in_cart = "";

                            $get_kit_products_settings = array(
                                'main_product_id' => $main_product_id,
                                'filter_kit_items' => true,
                                'only_first' => false,
                                'admin_mode' => false,
                            );

                            $ocmod_point_010 = 1;

                            $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings);

                            $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, true, false, '', $use_tax);

                            $kit_info['kit_items'] = $kit_items_data['kit_items'];

                            $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

                            if ($kit_enable_status['display_kit']) {
                                if($kit_items_data['total_default'] == $kit_items_data['total_kit'] ){
                                    $product_as_kit_price['price'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency']);
                                    $product_as_kit_price['price_value'] =$kit_items_data['total_default'];
                                    $product_as_kit_price['price_value_no_tax'] =$kit_items_data['total_default_no_tax'];
                                    $product_as_kit_price['special'] = false;
                                    $product_as_kit_price['special_value'] = false;
                                }else{
                                    $product_as_kit_price['price'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency']);
                                    $product_as_kit_price['price_value'] = $kit_items_data['total_default'];
                                    $product_as_kit_price['price_value_no_tax'] = $kit_items_data['total_default_no_tax'];
                                    $product_as_kit_price['special'] = $this->currency->format($kit_items_data['total_kit'], $this->session->data['currency']);
                                    $product_as_kit_price['special_value'] = $kit_items_data['total_kit'];
                                }

                                $this->product_as_kit_dinamic_price_cache[$product_id] = $product_as_kit_price;
                            }
                        }

                    }
                }
            }
        }


        return $product_as_kit_price;
    }


    
    public function getProductAsKitPriceFromCache($product_id, $use_tax=true){
        $product_as_kit_price = array();

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE product_id ='" . (int)$product_id. "' AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."'";
        $query = $this->db->query($sql);

        if($query->row){


            if($query->row['price']){
                $product_as_kit_price['price'] = $this->currency->format($query->row['price'], $this->session->data['currency']);
                $product_as_kit_price['price_value'] = $query->row['price'];
                $product_as_kit_price['price_value_no_tax'] = $query->row['price'];
            }else{
                $product_as_kit_price['price'] = false;
                $product_as_kit_price['price_value'] = false;
                $product_as_kit_price['price_value_no_tax'] = false;
            }
            if($query->row['special']){
                $product_as_kit_price['special'] = $this->currency->format($query->row['special'], $this->session->data['currency']);
                $product_as_kit_price['special_value'] = $query->row['special'];
            }else{
                $product_as_kit_price['special'] = false;
                $product_as_kit_price['special_value'] = false;
            }


        }

        return $product_as_kit_price;
    }
    

    public function getProductAsKitPriceFromCache_LE($product_id, $use_tax=true){
        $product_as_kit_price = array();

        
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE product_id ='" . (int)$product_id. "' AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."'";
        

        $query = $this->db->query($sql);

        if($query->row){


            if($query->row['price']){
                $product_as_kit_price['price'] = $this->currency->format($query->row['price'], $this->session->data['currency']);
                $product_as_kit_price['price_value'] = $query->row['price'];
                $product_as_kit_price['price_value_no_tax'] = $query->row['price'];
            }else{
                $product_as_kit_price['price'] = false;
                $product_as_kit_price['price_value'] = false;
                $product_as_kit_price['price_value_no_tax'] = false;
            }
            if($query->row['special']){
                $product_as_kit_price['special'] = $this->currency->format($query->row['special'], $this->session->data['currency']);
                $product_as_kit_price['special_value'] = $query->row['special'];
            }else{
                $product_as_kit_price['special'] = false;
                $product_as_kit_price['special_value'] = false;
            }


        }

        return $product_as_kit_price;
    }

    
    public function getProductAsKitPrice_LE($product_id, $use_tax=true, $require_kit_id=null){
        $product_as_kit_price = array();


        if(isset($this->product_as_kit_dinamic_price_le_cache[$product_id]) && !isset($require_kit_id)){
            $product_as_kit_price = $this->product_as_kit_dinamic_price_le_cache[$product_id];
        }else {
            if ($this->bundle_expert->checkProductIsKit_LE($product_id)) {
                $kits = $this->bundle_expert->getProductAsKitKits_LE($product_id);
                if (!empty($kits)) {
                    
                    foreach ($kits as $kit) {
                        $kit_id = $kit['kit_id'];

                        
                        if(isset($require_kit_id) and $require_kit_id!=$kit_id){
                            continue;
                        }

                        
                        $kit_info = $this->getKit($kit_id);
                        if (!empty($kit_info)) {
                            $main_product_id = $product_id;
                            $main_product_in_cart = "";

                            $get_kit_products_settings = array(
                                'main_product_id' => $main_product_id,
                                'filter_kit_items' => true,
                                'only_first' => false,
                                'admin_mode' => false,
                            );

                            $kit_items = $this->getKitProducts($kit_id, $get_kit_products_settings);

                            $kit_items_data = $this->getDataKit($kit_id, $main_product_id, $kit_items, true, true, false, '', $use_tax);

                            $kit_info['kit_items'] = $kit_items_data['kit_items'];

                            $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

                            if ($kit_enable_status['display_kit']) {
                                if ($kit_items_data['total_default'] == $kit_items_data['total_kit']) {
                                    $product_as_kit_price['price'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency']);
                                    $product_as_kit_price['price_value'] = $kit_items_data['total_default'];
                                    $product_as_kit_price['price_value_no_tax'] = $kit_items_data['total_default_no_tax'];
                                    $product_as_kit_price['special'] = false;
                                    $product_as_kit_price['special_value'] = false;
                                } else {
                                    $product_as_kit_price['price'] = $this->currency->format($kit_items_data['total_default'], $this->session->data['currency']);
                                    $product_as_kit_price['price_value'] = $kit_items_data['total_default'];
                                    $product_as_kit_price['price_value_no_tax'] = $kit_items_data['total_default_no_tax'];
                                    $product_as_kit_price['special'] = $this->currency->format($kit_items_data['total_kit'], $this->session->data['currency']);
                                    $product_as_kit_price['special_value'] = $kit_items_data['total_kit'];
                                }

                                $this->product_as_kit_dinamic_price_le_cache[$product_id] = $product_as_kit_price;
                            }
                        }

                    }
                }
            }
        }

        return $product_as_kit_price;
    }

    public function getProductAsKitWeight($product_id, $use_tax = true){
        $weight = 0;

        if ($this->bundle_expert->checkProductIsKit($product_id)) {
            $kits = $this->bundle_expert->getProductAsKitKits($product_id);
            if (!empty($kits)) {
                
                foreach ($kits as $kit) {
                    $kit_id = $kit['kit_id'];

                    $kit_info = $this->getKit($kit_id);
                    if (!empty($kit_info)) {
                        $main_product_id = $product_id;
                        $main_product_in_cart = "";

                        $get_kit_products_settings = array(
                            'main_product_id' => $main_product_id,
                            'filter_kit_items' => true,
                            'only_first' => false,
                            'admin_mode' => false,
                        );

                        $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings);

                        $kit_items_data = $this->getDataKit($kit_id, $main_product_id, $kit_items, true, true, false, '', $use_tax);

                        $kit_info['kit_items'] = $kit_items_data['kit_items'];

                        $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

                        if ($kit_enable_status['display_kit']) {
                            $weight = $kit_items_data['kit_weight_value'];
                        }
                    }

                }
            }
        }

        return $weight;
    }

    public function getProductAsKitWeight_LE($product_id, $use_tax=true){
        $weight = 0;

        if ($this->bundle_expert->checkProductIsKit_LE($product_id)) {
            $kits = $this->bundle_expert->getProductAsKitKits_LE($product_id);
            if (!empty($kits)) {
                
                foreach ($kits as $kit) {
                    $kit_id = $kit['kit_id'];

                    $kit_info = $this->getKit($kit_id);
                    if (!empty($kit_info)) {
                        $main_product_id = $product_id;
                        $main_product_in_cart = "";

                        $get_kit_products_settings = array(
                            'main_product_id' => $main_product_id,
                            'filter_kit_items' => true,
                            'only_first' => false,
                            'admin_mode' => false,
                        );

                        $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings);

                        $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, true, false, '', $use_tax);

                        $kit_info['kit_items'] = $kit_items_data['kit_items'];

                        $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

                        if ($kit_enable_status['display_kit']) {
                            $weight = $kit_items_data['kit_weight_value'];
                        }
                    }

                }
            }
        }

        return $weight;
    }

    
    public function getProductAsKitPriceDataForProductPage($product_id, &$data){
        if($this->bundle_expert->checkProductIsKit($product_id)) {
            if ($this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db']) {
                $this->getProductAsKitPrice($product_id, false);
                $this->updateProductAsKitPriceInDB();
                $product_info = $this->getProductInfo($product_id);
                $data['product_as_kit'] = true;
                if ($product_info['price']) {
                    $product_info['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $data['price'] = "<span class='product-price-container'>" . $product_info['price'] . "</span>";
                }
                if ($product_info['special']) {
                    $product_info['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $data['special'] = "<span class='product-special-container'>" . $product_info['special'] . "</span>";
                }
            }else{
                $product_as_kit_price = $this->bundle_expert->getProductAsKitPrice($product_id);
                if (!empty($product_as_kit_price)) {
                    $data['product_as_kit'] = true;
                    

                    if ($product_as_kit_price['price']) {

                        $data['price'] = "<span class='product-price-container'>" . $product_as_kit_price['price'] . "</span>";
                    }

                    if ($product_as_kit_price['special']) {

                        $data['special'] = "<span class='product-special-container'>" . $product_as_kit_price['special'] . "</span>";
                    }
                } else {
                    $data['product_as_kit'] = false;
                }
            }
            if ($data['special']) {
                $data['special_date_start'] = "";
                $data['special_date_end'] = "";
            }
        }else if($this->bundle_expert->checkProductIsKit_LE($product_id)) {
            if ($this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le']) {
                $this->getProductAsKitPrice_LE($product_id, false);
                $this->updateProductAsKitPriceInDB_LE();
                $product_info = $this->getProductInfo($product_id);
                $data['product_as_kit'] = true;
                if ($product_info['price']) {
                    $product_info['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $data['price'] = "<span class='product-price-container'>" . $product_info['price'] . "</span>";
                }
                if ($product_info['special']) {
                    $product_info['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $data['special'] = "<span class='product-special-container'>" . $product_info['special'] . "</span>";
                }
            }else {
                $product_as_kit_price = $this->bundle_expert->getProductAsKitPrice_LE($product_id);
                if (!empty($product_as_kit_price)) {
                    $data['product_as_kit'] = true;
                    if ($data['price']) {
                        $data['price'] = "<span class='product-price-container'>" . $data['price'] . "</span>";
                    }
                    if ($data['special']) {
                        $data['special'] = "<span class='product-special-container'>" . $data['special'] . "</span>";
                    }
                } else {
                    $data['product_as_kit'] = false;
                }
            }
            if ($data['special']) {
                $data['special_date_start'] = "";
                $data['special_date_end'] = "";
            }
        }else{
            $data['product_as_kit'] = false;
        }
    }

    public function getProductAsKitWeightDataForProductPage($product_id, &$data){
        $use_tax = false;
        if($this->checkProductIsKit($product_id)) {
            $product_as_kit_weight = $this->getProductAsKitWeight($product_id, $use_tax);
            if (!empty($product_as_kit_weight)) {
                $data['weight'] = $product_as_kit_weight;
            }
        }
    }

    
    public function getProductAsKitPriceDataForProductModel($product_id, &$data){
        $use_tax = false;
        if($this->checkProductIsKit($product_id)) {
            
            
            $product_as_kit_price = $this->getProductAsKitPriceFromCache($product_id, $use_tax);
            
            if(empty($product_as_kit_price)){
                $product_as_kit_price = $this->getProductAsKitPrice($product_id, $use_tax);
            }
            


            if (!empty($product_as_kit_price)) {
                $data['price'] = $product_as_kit_price['price_value'];
                $data[ 'special' ] = $product_as_kit_price['special_value'];
            }else{
                $data[ 'quantity' ] = 0;
            }
            $data['product_as_kit_mode'] = true;
        }
    }
    public function getProductAsKitPriceDataForProductModel_LE($product_id, &$data){
        $use_tax = false;
        if ($this->checkProductIsKit_LE($product_id)) {
            
            $product_as_kit_price = $this->getProductAsKitPriceFromCache_LE($product_id, $use_tax);
            
            if(empty($product_as_kit_price)){
                $product_as_kit_price = $this->getProductAsKitPrice_LE($product_id, $use_tax);
            }
            if (!empty($product_as_kit_price)) {
                $data['price'] = $product_as_kit_price['price_value'];
                $data['special'] = $product_as_kit_price['special_value'];
            } else {
                $data['quantity'] = 0;
            }
            $data['product_as_kit_le_mode'] = true;
        }
    }

    public function getProductAsKitWeightDataForProductModel($product_id, &$data){
        $use_tax = false;
        if($this->checkProductIsKit($product_id)) {
            $product_as_kit_weight = $this->getProductAsKitWeight($product_id, $use_tax);
            if (!empty($product_as_kit_weight)) {
                $data['weight'] = $product_as_kit_weight;
            }
        }
    }
    public function getProductAsKitWeightDataForProductModel_LE($product_id, &$data){
        $use_tax = false;
        if ($this->checkProductIsKit_LE($product_id)) {
            $product_as_kit_weight = $this->getProductAsKitWeight_LE($product_id, $use_tax);
            if (!empty($product_as_kit_weight)) {
                $data['weight'] = $product_as_kit_weight;
            }
        }
    }
    public function checkKitProductsQuantityLimit($kit_info, $kit_items, $product_as_kit_data)
    {
        $result = array();

        if ($kit_info['product_quantity_limit']['status']) {
            $total_quantity = 0;
            
            foreach ($kit_items as $kit_item) {
                if(isset($kit_item['cart_kit_info']) && isset($kit_item['cart_kit_info']['has_product_as_kit_quantity'])){
                    $total_quantity += $kit_item['quantity']/$kit_item['cart_kit_info']['has_product_as_kit_quantity'];
                }else{
                    $total_quantity += $kit_item['quantity'];
                }
            }





            $min = $kit_info['product_quantity_limit']['min'];
            $max = $kit_info['product_quantity_limit']['max'];

            
            if (!empty($min) && empty($max)) {
                if ($min > $total_quantity) {
                    $result['text_error'] = sprintf($this->language->get('text_error_kit_quantity_limit_1'), $min);
                }
            } 
            else if (!empty($min) && !empty($max)) {

                if ($min != $max) {
                    if ($min > $total_quantity || $max < $total_quantity) {
                        $result['text_error'] = sprintf($this->language->get('text_error_kit_quantity_limit_2'), $min, $max);
                    }
                } else {
                    if ($min != $total_quantity) {
                        $result['text_error'] = sprintf($this->language->get('text_error_kit_quantity_limit_3'), $min, $max);
                    }
                }

            } 
            else if (empty($min) && !empty($max)) {
                if ($max < $total_quantity) {
                    $result['text_error'] = sprintf($this->language->get('text_error_kit_quantity_limit_4'), $max);
                }
            } 
            else {

            }

        }

        return $result;
    }

    
    public function checkKitProductsItemEmptyModeNotEmptyInCart($kit_info, $kit_items, $product_as_kit_data)
    {
        $result = array();

        $error = false;
        $empty_position = 1;
        $item_empty_mode_data = array();

        foreach ($kit_info['kit_items'] as $kit_item) {
            if(isset($kit_item['products'])){
                $kit_item_product = array_shift($kit_item['products']);
                $item_empty_mode = $kit_item_product['item_empty_mode'];
                $item_position = $kit_item['item_position'];

                if($item_empty_mode['enable'] && $item_empty_mode['not_empty_in_cart']){
                    $in_kit = false;
                    foreach ($kit_items as $kit_item2){
                        if($kit_item2['item_position']==$item_position){
                            $in_kit = true;
                            break;
                        }
                    }


                    if(!$in_kit){
                        $error = true;
                        $empty_position = $item_position+1;
                        $item_empty_mode_data = $item_empty_mode;
                        break;
                    }
                }
            }

        }

        if($error){
            $result['text_error'] = sprintf($this->language->get('text_error_item_empty_mode_not_empty_in_cart'), $item_empty_mode_data['title']);

        }



        return $result;
    }

    
    
    public function checkCartItemsLimit($kit_info, $products_count, $kit_from_cart_unique_id='', $product_as_kit_new_quantity=1, $update_part_of_product_as_kit=false){
        $cart_items_limit = $this->bundle_expert_settings['cart_items_limit'];
        $limit = $cart_items_limit['limit'];

        $result = array(
            'text_error' => '',
            'product_as_kit_new_quantity' => 0);

        if ($cart_items_limit['status']) {
            
            $cart_products = $this->bundle_expert_cart->getCartProductsData();;
            $current_items_quantity = count($cart_products);

            if (!$kit_info['kit_as_product']) {
                if ($kit_from_cart_unique_id) {
                    $kit_in_cart = $this->getCartKit($kit_from_cart_unique_id);
                    $kit_items_in_cart = $kit_in_cart['kit_items'];
                    $current_items_quantity -= count($kit_items_in_cart);
                }
                $current_items_quantity += $products_count;

                if ($current_items_quantity > $limit) {
                    $result['text_error'] = $this->language->get('text_cart_items_limit');
                }

            } else {
                $product_as_kit_old_quantity_total = 0;

                $quantity = $product_as_kit_new_quantity;

                if ($kit_from_cart_unique_id) {
                    if($update_part_of_product_as_kit){
                        $quantity = 1;
                        $product_as_kit_old_quantity = 1;
                    }else{
                        $product_as_kit_data = $this->bundle_expert_cart->checkKitHasProductAsKit($kit_from_cart_unique_id);
                        if($product_as_kit_data){
                            $product_as_kit_old_quantity = $product_as_kit_data['quantity'];
                        }else{
                            $product_as_kit_old_quantity = 0;
                        }
                    }
                    $kit_in_cart = $this->getCartKit($kit_from_cart_unique_id);
                    if(isset($kit_in_cart['kit_items'])) {
                        $kit_items_in_cart = $kit_in_cart['kit_items'];
                        $product_as_kit_old_quantity_total = 1 + (count($kit_items_in_cart) * $product_as_kit_old_quantity);
                        $current_items_quantity -= $product_as_kit_old_quantity_total;
                    }
                }


                $product_as_kit_new_quantity_total = $products_count + 1;

                $current_items_quantity += $product_as_kit_new_quantity_total;

                if ($current_items_quantity > $limit) {

                    $available_items_quantity = $limit - count($cart_products) + $product_as_kit_old_quantity_total;
                    $new_quantity = (int)(($available_items_quantity - 1) / ($products_count));
                    if ($new_quantity > 0) {
                        $result['product_as_kit_new_quantity'] = $new_quantity;
                    } else {
                        $result['text_error'] = $this->language->get('text_cart_items_limit');
                    }
                }
            }

        }

        return $result;
    }

    public function getTotalModule(&$total_data, &$total, &$taxes) {

        if (!$this->bundle_expert->isEnabled()) return;

        $this->load->language('module/bundle_expert');

        $this->load->language('checkout/bundle_expert');
        
        $this->load->model('catalog/product');

        $cart_products = $this->cart->getProducts();

        $discount_total = 0;
        $discount_kit = 0;

        $kits_in_cart = array();
        $product_as_kit_list = array();

        $customer_group_discount_enable = $this->checkKitDiscountEnableByCustomerGroup();

        
        
        
        foreach ($cart_products as $key => $value) {

            if (version_compare($this->bundle_expert->OC_VERSION, '2.1.0.1', '<')) {
                $cart_product = unserialize(base64_decode($key));
            }else{
                $key = $this->bundle_expert_cart->getCartKitKey($value['cart_id']);
                if($key){
                    $cart_product = unserialize(base64_decode($key));
                }else{
                    $cart_product = array();
                }
            }

            if(isset($cart_product['cart_kit_info'])){
                $kit_id = $cart_product['cart_kit_info']['kit_id'];
                $kit_unique_id = $cart_product['cart_kit_info']['kit_unique_id'];
                $main_product_id = $cart_product['cart_kit_info']['main_product_id'];
                $item_position = $cart_product['cart_kit_info']['item_position'];

                $cart_product['key'] = $key;
                $cart_product['quantity'] = $value['quantity'];

                $kits_in_cart[$kit_unique_id]['kit_id'] = $kit_id;
                $kits_in_cart[$kit_unique_id]['kit_unique_id'] = $kit_unique_id;
                $kits_in_cart[$kit_unique_id]['main_product_id'] = $main_product_id;
                $kits_in_cart[$kit_unique_id]['kit_items'][$key] = $cart_product;
                $kits_in_cart[$kit_unique_id]['kit_items'][$key]['cart_product_info'] = $value;
            }

            if(isset($cart_product['product_as_kit'])){
                $value['cart_product_info'] = $cart_product;
                $product_as_kit_list[] = $value;
            }

        }

        
        
        foreach ($kits_in_cart as $kit_index=>$kit_in_cart){

            $tax_rates_bundle = array();
            $tax_rates_bundle_products = array();
            $discount_by_tax = array();

            $get_kit_products_settings = array(
                'main_product_id' => $kit_in_cart['main_product_id'],
                'filter_kit_items' => false,
                'only_first' => false,
                'admin_mode' => false,
            );

            $kit_info = $this->getKit($kit_in_cart['kit_id'], $kit_in_cart['kit_unique_id'], $get_kit_products_settings);

            $kit_total = 0;
            $total_default = 0;
            $total_product_count = 0;
            $total_product_count_by_tax = array();

            foreach ($kit_in_cart['kit_items'] as $index2 => $kit_product) {

                $item_position = $kit_product['cart_kit_info']['item_position'];

                $cart_price = $kit_product['cart_product_info']['price'];

                
                
                $cart_price = $this->tax->calculate($cart_price, $kit_product['cart_product_info']['tax_class_id'], $this->config->get('config_tax'));
                

                $cart_quantity = $kit_product['cart_product_info']['quantity'];

                $kit_product_info =  $this->bundle_expert->findProductDataInKit($kit_product['product_id'],$kit_info['kit_items'],$item_position, $kit_product['cart_kit_info']['is_free_product'],$kit_info['kit_id'] );

                
                $correcting_cart_price = 0;

                
                if($this->bundle_expert_cart->isProductAsKit($kit_product)){
                    $price = $cart_price;
                }else{
                    $product_discount_in_total = $kit_info['product_discount_in_total'];
                    if ($product_discount_in_total) {

                        
                        if($kit_info['show_default_specials_in_kit_discounts']){
                            if($kit_product_info['product_info']['special']){
                                $correcting_cart_price = ($kit_product_info['product_info']['price'] - $kit_product_info['product_info']['special']);
                                $cart_price -= $correcting_cart_price;
                            }
                        }


                        
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
                                $price = $cart_price;
                                break;
                            case 'product_price_minus_sum':
                                if(!$customer_group_discount_enable){
                                    $price = $cart_price;
                                    break;
                                }
                                $price = $cart_price - $kit_product_info['price_minus_sum'];
                                break;
                            case 'product_price_minus_percent':
                                if(!$customer_group_discount_enable){
                                    $price = $cart_price;
                                    break;
                                }

                                $price = $cart_price - ($cart_price * $kit_product_info['price_minus_percent'] / 100);
                                break;
                            case 'fix_price':
                                if(!$customer_group_discount_enable){
                                    $price = $cart_price;
                                    break;
                                }
                                $price = $kit_product_info['price'];
                                break;

                        }
                    }else{
                        $price = $cart_price;
                    }
                }

                $quantity = $cart_quantity;

                $kit_total += $price * $quantity;
                $total_product_count += $quantity;

                
                $cart_price += $correcting_cart_price;

                $total_default += $cart_price * $quantity;

                if ($kit_product['cart_product_info']['tax_class_id']) {











                    if(!isset($discount_by_tax[$kit_product['product_id']])){
                        $discount_by_tax[$kit_product['product_id']] = array(
                            'tax_class_id' => $kit_product['cart_product_info']['tax_class_id'],
                            'value' => ($cart_price - $price) * $quantity,
                            'quantity' => $quantity

                        );
                    }else{
                        $discount_by_tax[$kit_product['product_id']]['value'] +=($cart_price - $price) * $quantity;
                        $discount_by_tax[$kit_product['product_id']]['quantity'] += $quantity;
                    }



                }

            }

            $kit_total_mode = $kit_info['kit_price_mode'];

            
            $customer_group_id = $this->customer->getGroupId('config_customer_group_id');
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
                    if(!$customer_group_discount_enable){
                        $discount_kit = $total_default - ($kit_total);
                        break;
                    }
                    if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                        $discount_kit = $total_default - ($kit_total);
                    }else{
                        $discount_kit = $total_default - ($kit_total - ($kit_total / 100 * $kit_total_mode['sum_minus_percent']));
                    }
                    
                    break;
                case 'sum_minus_value':
                    if(!$customer_group_discount_enable){
                        $discount_kit = $total_default - ($kit_total);
                        break;
                    }
                    if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                        
                        $discount_kit =  $total_default - ($kit_total);
                    }else{
                        $discount_kit =  $total_default - ($kit_total - $kit_total_mode['sum_minus_value']);
                    }
                    
                    break;
                case 'sum_fix':
                    if(!$customer_group_discount_enable){
                        break;
                    }
                    if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                        break;
                    }
                    $discount_kit =  $total_default - $kit_total_mode['sum_fix'];
                    break;
            }


            $kit_count_index = '';
            if($kit_product['cart_kit_info']['kit_cart_index']>1) {
                $kit_count_index = ' - ' . $kit_product['cart_kit_info']['kit_cart_index'];
            }

            $discount_title = $kit_info['cart_title'] . $kit_count_index . ". " . $this->language->get('text_discount');;


            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                $code = 'bundle_expert_total_v';
                $name_sort_order = 'bundle_expert_total_v_sort_order';
            }else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $code = 'bundle_expert_total';
                    $name_sort_order = 'bundle_expert_total_sort_order';
                } else {
                    $code = 'bundle_expert_total';
                    $name_sort_order = 'total_bundle_expert_total_sort_order';
                }
            }

            
            if ($discount_kit > 0) {













                $discount_by_tax_sum = 0;
                $discount_by_tax_count = 0;
                foreach ($discount_by_tax as $product_id=>$tax_data){
                    $discount_by_tax_sum+=$tax_data['value'];
                    $discount_by_tax_count+=$tax_data['quantity'];
                }
                foreach ($discount_by_tax as $product_id=>$tax_data){
                    if($discount_by_tax_sum==0){
                        $coeff = $tax_data['quantity']/$discount_by_tax_count;

                    }else{
                        $coeff = $tax_data['value']/$discount_by_tax_sum;
                    }


                    $discount_value = $discount_kit * $coeff;
                    $tax_rates = $this->tax->getRates($discount_value, $tax_data['tax_class_id']);

                    foreach ($tax_rates as $tax_rate) {
                        $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                    }
                }
            }

            if($discount_kit>0) {

                $total_data[] = array(
                    'code' => $code,
                    'title' => $discount_title,
                    'value' => -$discount_kit,
                    'sort_order' => (float)((float)$this->config->get($name_sort_order) + (float)(0.01) * (float)($kit_product['cart_kit_info']['kit_cart_index']))
                );

            }

            $discount_total+=$discount_kit;
        }


        $total -= $discount_total;

        
        $discount_total = $this->getTotalModuleAutoKit($total_data,$total,$taxes);

        $total -= $discount_total;
    }


    public function autoKitCreateKitTemplate($kit_id) {
        $kit_items_count = $this->getKitItemsCount($kit_id);
        $kit_template = array(
            'kit_id' => $kit_id,
            'items' => array()
        );
        for ($i = 0; $i < $kit_items_count; $i++) {
            $kit_template['items'][$i] = array(
                'products' => array()
            );
        }

        return $kit_template;
    }
    public function getTotalModuleAutoKit(&$total_data, &$total, &$taxes) {

        
        
        

        
        $cart_products = $this->cart->getProducts();

        $cart_products = $this->addAutoKitData($cart_products);

        
        $cart_auto_bundles = array();

        foreach ($cart_products as $cart_product){

            if (isset($cart_product['key'])) {
                $cart_product_data = unserialize(base64_decode($cart_product['key']));
                if (isset($cart_product_data['auto_kit_kit_id']) ) {
                    $kit_id = $cart_product_data['auto_kit_kit_id'];
                    $item_position = $cart_product_data['item_position'];
                    $product_id = $cart_product_data['product_id'];


                    if (!isset($cart_auto_bundles[$kit_id])) {
                        $cart_auto_bundles[$kit_id][] = $this->autoKitCreateKitTemplate($kit_id);
                    }

                    $added = false;
                    foreach ($cart_auto_bundles[$kit_id] as $kit_index => $kit) {
                        if (!isset($cart_auto_bundles[$kit_id][$kit_index]['items'][$item_position]['products'][$product_id])) {
                            $cart_auto_bundles[$kit_id][$kit_index]['items'][$item_position]['products'][$product_id] = array(
                                'product_id' => $product_id,
                                'quantity' => $cart_product['quantity'],
                                'total' => $cart_product['total'],
                            );
                            $added = true;
                            break;
                        }
                    }
                    if (!$added) {
                        $cart_auto_bundles[$kit_id][] = $this->autoKitCreateKitTemplate($kit_id);
                        $kit_index = count($cart_auto_bundles[$kit_id]) - 1;
                        if (!isset($cart_auto_bundles[$kit_id][$kit_index]['items'][$item_position]['products'][$product_id])) {
                            $cart_auto_bundles[$kit_id][$kit_index]['items'][$item_position]['products'][$product_id] = array(
                                'product_id' => $product_id,
                                'quantity' => $cart_product['quantity'],
                                'total' => $cart_product['total'],
                            );
                        }
                    }


                }
            }
        }

        
        $discount_kit_all = 0;
        $discount_kit = 0;

        $new_cart_auto_bundles = array();
        foreach ($cart_auto_bundles as $auto_bundle) {
            foreach ($auto_bundle as $kit) {
                $new_cart_auto_bundles[] = $kit;
            }
        }
        $cart_auto_bundles = $new_cart_auto_bundles;
        $discount_by_tax = array();
        foreach ($cart_auto_bundles as $auto_bundle) {
            $tax_rates_bundle_products = array();

            $kit_info = $this->getKit($auto_bundle['kit_id']);
            if ($kit_info && $kit_info['auto_kit_in_cart']) {


                $get_kit_products_settings = array(
                    'main_product_id' => '',
                    'filter_kit_items' => true,
                    'only_first' => true,
                    'admin_mode' => false,
                );

                $kit_items = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);

                $total_default = 0;
                $kit_total = 0;
                $total_bundle_product_count = 0;

                foreach ($kit_items as $kit_item) {
                    foreach ($kit_item['products'] as $kit_item_product) {
                        $item_position = $kit_item['item_position'];
                        $product_id = $kit_item_product['product_id'];

                        $auto_kit_product = $auto_bundle['items'][$item_position]['products'][$product_id];
                        $quantity =$auto_kit_product['quantity'];
                        $quantity =$kit_item_product['quantity'];

                        $product_total = $auto_kit_product['total'];

                        $use_tax = true;
                        if ($use_tax) {

                        }

                        $total_default += $product_total;

                        $customer_group_discount_enable = $this->checkKitDiscountEnableByCustomerGroup();
                        $kit_product_info = $kit_item_product;
                        $cart_price = $product_total;

                        
                        $customer_group_id = $this->customer->getGroupId();
                        if(!isset($customer_group_id)){
                            $customer_group_id = $customer_group_id = $this->config->get('config_customer_group_id');
                        }
                        if($kit_item['price_mode_to_customer_groups_status'] && isset($kit_item['price_mode_to_customer_groups'][$customer_group_id])){
                            $new_price_mode = $kit_item['price_mode_to_customer_groups'][$customer_group_id];
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
                                $price = $cart_price;
                                break;
                            case 'product_price_minus_sum':
                                if (!$customer_group_discount_enable) {
                                    $price = $cart_price;
                                    break;
                                }
                                $price = $cart_price - $kit_product_info['price_minus_sum'];
                                break;
                            case 'product_price_minus_percent':
                                if (!$customer_group_discount_enable) {
                                    $price = $cart_price;
                                    break;
                                }
                                $price = $cart_price - ($cart_price * $kit_product_info['price_minus_percent'] / 100);
                                break;
                            case 'fix_price':
                                if (!$customer_group_discount_enable) {
                                    $price = $cart_price;
                                    break;
                                }
                                $price = $kit_product_info['price'];
                                break;

                        }

                        $kit_total += $price;
                        $total_bundle_product_count+=$quantity;

                        if ($kit_product_info['product_info']['tax_class_id']) {


                            if(!isset($discount_by_tax[$kit_product_info['product_info']['product_id']])){
                                $discount_by_tax[$kit_product_info['product_info']['product_id']] = array(
                                    'tax_class_id' => $kit_product_info['product_info']['tax_class_id'],
                                    'value' => ($cart_price - $price) * $quantity,
                                    'quantity' => $quantity

                                );
                            }else{
                                $discount_by_tax[$kit_product_info['product_info']['product_id']]['value'] +=($cart_price - $price) * $quantity;
                                $discount_by_tax[$kit_product_info['product_info']['product_id']]['quantity'] += $quantity;
                            }

                        }
                    }
                }

                $kit_total_mode = $kit_info['kit_price_mode'];

                $discount_kit = 0;
                $total_product_count = 0;

                
                $customer_group_id = $this->customer->getGroupId('config_customer_group_id');
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
                        if (!$customer_group_discount_enable) {
                            $discount_kit = $total_default - ($kit_total);
                            break;
                        }
                        if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                            $discount_kit = $total_default - ($kit_total);
                        } else {
                            $discount_kit = $total_default - ($kit_total - ($kit_total / 100 * $kit_total_mode['sum_minus_percent']));
                        }

                        break;
                    case 'sum_minus_value':
                        if (!$customer_group_discount_enable) {
                            $discount_kit = $total_default - ($kit_total);
                            break;
                        }
                        if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {

                            $discount_kit = $total_default - ($kit_total);
                        } else {
                            $discount_kit = $total_default - ($kit_total - $kit_total_mode['sum_minus_value']);
                        }

                        break;
                    case 'sum_fix':
                        if (!$customer_group_discount_enable) {
                            break;
                        }
                        if (!$this->checkActiveKitDiscountByProductCount($kit_info, $total_product_count)) {
                            break;
                        }
                        $discount_kit = $total_default - $kit_total_mode['sum_fix'];
                        break;
                }

                if ($discount_kit > 0) {
                    $discount_kit_all += $discount_kit;
                }

            }
        }

        
        if ($discount_kit > 0) {

            $discount_by_tax_sum = 0;
            $discount_by_tax_count = 0;
            foreach ($discount_by_tax as $product_id=>$tax_data){
                $discount_by_tax_sum+=$tax_data['value'];
                $discount_by_tax_count+=$tax_data['quantity'];
            }
            foreach ($discount_by_tax as $product_id=>$tax_data){
                if($discount_by_tax_sum==0){
                    $coeff = $tax_data['quantity']/$discount_by_tax_count;

                }else{
                    $coeff = $tax_data['value']/$discount_by_tax_sum;
                }


                $discount_value = $discount_kit * $coeff;
                $tax_rates = $this->tax->getRates($discount_value, $tax_data['tax_class_id']);

                foreach ($tax_rates as $tax_rate) {
                    

                    $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'] * $tax_data['quantity'];
                }
            }

        }

        if($discount_kit_all>0){

            $discount_title = $this->language->get('text_total_discount');


            if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
                $code = 'bundle_expert_total_v';
                $name_sort_order = 'bundle_expert_total_v_sort_order';
            }else {
                if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                    $code = 'bundle_expert_total';
                    $name_sort_order = 'bundle_expert_total_sort_order';
                } else {
                    $code = 'bundle_expert_total';
                    $name_sort_order = 'total_bundle_expert_total_sort_order';
                }
            }

            $total_data[] = array(
                'code' => $code,
                'title' => $discount_title,
                'value' => -$discount_kit_all,
                'sort_order' => $this->config->get($name_sort_order)
            );

        }


        return $discount_kit_all;




    }

    public function autoKitReplaceSimpleProduct($cart_products, $product_id, $kit_id, $kit_item_quantity, $item_position) {
        $new_cart_products = array();

        $replaced = false;

        while (!empty($cart_products) && !$replaced) {
            $cart_products = array_values($cart_products);
            $cart_product = $cart_products[0];

            if (isset($cart_product['key'])) {
                $cart_product_data = unserialize(base64_decode($cart_product['key']));
                if (!isset($cart_product_data['cart_kit_info']) && !isset($cart_product_data['product_as_kit'])) {
                    if ($cart_product['product_id'] == $product_id) {
                        if ($cart_product['quantity'] > $kit_item_quantity) {
                            $new_product = $cart_product;
                            $new_product['quantity'] = $kit_item_quantity;
                            $new_product['total'] = $new_product['price'] * $kit_item_quantity;

                            $cart_product_data['auto_kit_kit_id'] = $kit_id;
                            $cart_product_data['item_position'] = $item_position;
                            $new_product['key'] = base64_encode(serialize($cart_product_data));

                            $new_cart_products[] = $new_product;

                            $cart_products[0]['quantity'] -= $kit_item_quantity;
                            $replaced = true;

                        } else {
                            $new_product = $cart_product;
                            $new_product['quantity'] = $kit_item_quantity;
                            $new_product['total'] = $new_product['price'] * $kit_item_quantity;

                            $cart_product_data['auto_kit_kit_id'] = $kit_id;
                            $cart_product_data['item_position'] = $item_position;
                            $new_product['key'] = base64_encode(serialize($cart_product_data));


                            $new_cart_products[] = $new_product;

                            unset($cart_products[0]);
                        }
                    } else {
                        $new_cart_products[] = $cart_product;
                        unset($cart_products[0]);
                    }


                } else {
                    $new_cart_products[] = $cart_product;
                    unset($cart_products[0]);

                }
            } else {
                $new_cart_products[] = $cart_product;
                unset($cart_products[0]);
            }

        }

        foreach ($cart_products as $cart_product){
            $new_cart_products[] = $cart_product;
        }

        return $new_cart_products;

    }


    public function addAutoKitData($cart_products) {
        $default_products = array();

        $this->load->model('catalog/product');

        foreach ($cart_products as $product) {

            if (isset($product['key'])) {
                $cart_product_data = unserialize(base64_decode($product['key']));
                if (isset($cart_product_data['cart_kit_info']) || isset($cart_product_data['product_as_kit'])) {

                } else {
                    $default_products[] = $product;
                }
            }
        }


        $stop = false;

        $loop_count = 0;

        while ($stop == false) {

            $products = array();
            foreach ($default_products as $product) {
                $product_id = $product['product_id'];
                $quantity = $product['quantity'];
                if (!isset($products[$product_id])) {
                    $products[$product_id] = $quantity;

                } else {
                    $products[$product_id] += $quantity;
                }
            }


            $sql = "UPDATE " . DB_PREFIX . "bundle_expert_auto_kit_in_cart SET cart_quantity = '0'";

            $this->db->query($sql);

            foreach ($products as $product_id => $quantity) {
                $sql = "UPDATE " . DB_PREFIX . "bundle_expert_auto_kit_in_cart SET cart_quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$product_id . "'";

                $this->db->query($sql);
            }


            $sql = "SELECT kit_id, items_count, COUNT(*) as items_in_cart FROM " . DB_PREFIX . "bundle_expert_auto_kit_in_cart WHERE quantity <= cart_quantity  GROUP BY kit_id HAVING items_in_cart = items_count  ORDER BY kit_sort_order LIMIT 1";

            $query = $this->db->query($sql);


            $kit = $query->row;
            if ($kit) {
                $kit_info = $this->getKit($kit['kit_id']);
                if ($kit_info && $kit_info['auto_kit_in_cart']) {


                    $get_kit_products_settings = array(
                        'main_product_id' => '',
                        'filter_kit_items' => true,
                        'only_first' => true,
                        'admin_mode' => false,
                    );

                    $kit_items = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);

                    foreach ($kit_items as $kit_item) {
                        foreach ($kit_item['products'] as $kit_item_product) {
                            $product_id = $kit_item_product['product_id'];
                            $kit_item_quantity = $kit_item_product['quantity'];
                            $position_quantity_total = 0;

                            foreach ($default_products as $index1 => $cart_product) {
                                if ($cart_product['product_id'] == $product_id) {


                                    if ($cart_product['quantity'] > $kit_item_quantity) {
                                        $default_products[$index1]['quantity'] -= $kit_item_quantity;
                                        $position_quantity_total += $kit_item_quantity;

                                    } else {
                                        unset($default_products[$index1]);
                                        $position_quantity_total += $cart_product['quantity'];
                                    }


                                }
                                if ($position_quantity_total >= $kit_item_quantity) {
                                    $cart_products = $this->autoKitReplaceSimpleProduct($cart_products, $product_id, $kit['kit_id'], $kit_item_quantity, $kit_item['item_position']);
                                    break;
                                }
                            }

                        }
                    }

                } else {
                    $stop = true;
                }
            } else {
                $stop = true;
            }

            $loop_count++;
            if ($loop_count > 50) {
                $stop = true;
            }
        }

        return $cart_products;
    }


    
    
    public function getProductDiscount($product_id, $quantity){
        $discount_price = '';

        $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

        if ($product_discount_query->num_rows) {
            $discount_price = $product_discount_query->row['price'];
        }

        return $discount_price;
    }

    public function orderUpdateKitQuantity($order_info, $order_id, $order_status_id){
        if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
            if ($this->bundle_expert->isEnabled()) {
                $this->load->model('checkout/bundle_expert');
                $order_kits = $this->getOrderKits($order_id);
                foreach ($order_kits as $kit) {
                    if ($kit['kit_quantity_mode']['limit']) {
                        $this->updateKitQuantity($kit['kit_id'], -1);
                    }
                }
            }
        }
        if (in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && !in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
            if ($this->bundle_expert->isEnabled()) {
                $this->load->model('checkout/bundle_expert');
                $order_kits = $this->getOrderKits($order_id);
                foreach ($order_kits as $kit) {
                    if ($kit['kit_quantity_mode']['limit']) {
                        $this->updateKitQuantity($kit['kit_id'], 1);
                    }
                }
            }
        }
    }

    public function orderAddKitProductData($order_id, $order_product_id, $product){

        if (isset($product['cart_kit_info'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_order SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_id = '" . (int)$product['product_id'] . "', cart_kit_info = '" . $this->db->escape(json_encode($product['cart_kit_info'])) . "'");
            $this->addOrderKitHistory($product['cart_kit_info']['kit_id'], $product['cart_kit_info']['kit_unique_id'], $product['cart_kit_info']['main_product_id'], $order_id);
        }
        if (isset($product['product_as_kit'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_order SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_id = '" . (int)$product['product_id'] . "', product_as_kit_info = '" . $this->db->escape(json_encode($product['product_as_kit'])) . "'");
            $this->addOrderKitHistory($product['product_as_kit']['kit_id'], $product['product_as_kit']['kit_unique_id'], $product['product_as_kit']['main_product_id'], $order_id);
        }
    }

    
    public function optimizeOrdersKitHistory(){
        

        $orders_query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "bundle_expert_order` GROUP BY order_id");
        $orders = $orders_query->rows;
        $this->log->write("BE optimize orders total " . count($orders));
        foreach ($orders as $index=>$order){

            $this->log->write("BE optimize " . $index . "=>" . $order['order_id'] );
            $this->deleteOrderKitHistoryOldData($order['order_id']);
        }

    }
    public function deleteOrderKitHistoryOldData($order_id){

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "'");

        if ($query->row) {
            $order_products = array();

            $order_products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

            foreach ($order_products_query->rows as $order_product) {
                $order_products[] = "'" . $order_product['order_product_id'] . "'";
            }

            if($order_products){
                $order_products_list=implode(',', $order_products);

                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "' AND order_product_id NOT IN (" . $order_products_list . ")");

                $order_kits = array();

                $order_kit_products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "'");

                foreach ($order_kit_products_query->rows as $order_kit_product) {
                    $kit_unique_id = null;
                    if(isset($order_kit_product['cart_kit_info'])){
                        $cart_kit_info = json_decode($order_kit_product['cart_kit_info'], true);
                        $kit_unique_id = $cart_kit_info['kit_unique_id'];
                    }
                    if(isset($order_kit_product['product_as_kit_info'])){
                        $product_as_kit_info = json_decode($order_kit_product['product_as_kit_info'], true);
                        $kit_unique_id = $product_as_kit_info['kit_unique_id'];
                    }
                    if(isset($kit_unique_id)){
                        if(!in_array($kit_unique_id, $order_kits)){
                            $order_kits[] = "'" . $kit_unique_id . "'";
                        }
                    }

                }

                if ($order_kits) {
                    $order_kits_list = implode(',', $order_kits);

                    $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_order_kit_info WHERE order_id = '" . (int)$order_id . "' AND kit_unique_id NOT IN (" . $order_kits_list . ")");

                } else {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_order_kit_info WHERE order_id = '" . (int)$order_id . "'");
                }
            } else {
                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_order WHERE order_id = '" . (int)$order_id . "'");
            }
        }

    }
    


    public function orderProductListPrepare($products){
        $index = 0;

        $products_default = $this->cart->getProductsDefault();

        
        
        if(count($products_default) != count($products)){
            return $products;
        }

        foreach ($products_default as $product) {
            if (isset($products[$index]) && $products[$index]['product_id'] == $product['product_id']) {

                $cart_product_data = unserialize(base64_decode($product['key']));
                if (isset($cart_product_data['cart_kit_info'])) {
                    $products[$index]['cart_kit_info'] = $cart_product_data['cart_kit_info'];

                    
                    if (isset($cart_product_data['cart_kit_info']['has_product_as_kit_unique_id']) and !empty($cart_product_data['cart_kit_info']['has_product_as_kit_unique_id'])) {
                        $products[$index]['total'] = '';
                        
                        
                        
                    }

                    $products[$index]['name'] = str_replace('<br>', ' ', $products[$index]['name']);
                    $products[$index]['name'] = strip_tags($products[$index]['name']);
                }
                if (isset($cart_product_data['product_as_kit'])) {


                    $products[$index]['product_as_kit'] = $cart_product_data['product_as_kit'];
                    $product_as_kit_total = $this->bundle_expert_cart->getProductAsKitInCartTotal($cart_product_data['product_as_kit']['kit_unique_id']);
                    $product_as_kit_tax = $this->bundle_expert_cart->getProductAsKitInCartTax($cart_product_data['product_as_kit']['kit_unique_id']);
                    
                    $products[$index]['price'] = $product_as_kit_total;
                    $products[$index]['total'] = $product_as_kit_total * $products[$index]['quantity'];
                    

                    $products[$index]['tax'] = $product_as_kit_tax;





                }
            }
            $index++;
        }

        return $products;
    }


    private static function order_sort_products($a,$b) {
        return $a["order_product_id"] > $b["order_product_id"];
    }

    private function orderInfoSortProducts($products){

        usort($products, array($this,'order_sort_products'));

        return $products;

    }

    public function orderInfoProductListPrepare($products){

        
        $this->sortArray($products,'order_product_id');

        switch ($this->bundle_expert_settings['order_info_format']){
            case 'one_item':
                $new_products_list = $this->orderEmail_Format_2($products);
                break;
            case 'list':
                $new_products_list = $this->orderEmail_Format_1($products);
                break;
            default:
                $new_products_list = $products;
                break;
        }

        return $new_products_list;
    }

    
    
    
    
    
    public function orderEmailProductListPrepare($products){


        $this->sortArray($products,'order_product_id');

        switch ($this->bundle_expert_settings['order_info_format']){
            case 'one_item':
                $new_products_list = $this->orderEmail_Format_2($products);
                break;
            case 'list':
                $new_products_list = $this->orderEmail_Format_1($products);
                break;
            default:
                $new_products_list = $products;
                break;
        }

        return $new_products_list;
    }


    public function orderEmail_Format_1($products){
        $new_products_list = array();
        $product_as_kit_formatted = array();

        foreach ($products as $index => $product) {

            

            $order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);

            if (!empty($order_product_kit_info)) {

                if (isset($order_product_kit_info['product_as_kit_info'])) {
                    $product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];

                    $product['name'] = '<b style="font-size: 110%">' . $product['name'] . '</b>';
                    if(isset($product['price']))
                        $product['price'] = '<b style="font-size: 110%">' . $product['price'] . '</b>';
                    if(isset($product['total']))
                        $product['total'] = '<b style="font-size: 110%">' . $product['total'] . '</b>';
                    $product['model'] = '<b style="font-size: 110%">' . $product['model'] . '</b>';
                    $product['quantity_value'] = $product['quantity'] ;
                    $product['quantity'] = '<b style="font-size: 110%">' . $product['quantity'] . '</b>' ;

                    $product['product_as_kit'] = true;

                    $new_products_list[$product_as_kit_unique_id] = $product;

                    $product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];
                    $product_as_kit_formatted[$product_as_kit_unique_id] = array();



                } else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {
                    $product['total'] = '';

                    $has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];
                    $item_position = $order_product_kit_info['cart_kit_info']['item_position'];

                    $product['in_product_as_kit'] = true;

                    if(isset($product_as_kit_formatted[$has_product_as_kit_unique_id])){
                        if(!isset($product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']])) {
                            $product['quantity'] = $product['quantity'] / $new_products_list[$has_product_as_kit_unique_id]['quantity_value'] ;

                            $new_products_list[] = $product;



                            $product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']] = true;
                        }
                    } else {
                        $new_products_list[] = $product;
                    }
                }else {
                    $new_products_list[] = $product;
                }

            } else {
                $new_products_list[] = $product;
            }
        }
        foreach ($new_products_list as &$product){
            if(isset($product['product_as_kit'])){
                $product['weight'] = $this->orderCalculateProductAsKitWeight($product['order_product_id'], true);
            }
        }

        return $new_products_list;
    }

    public function orderEmail_Format_2($products){
        $new_products_list = array();

        $product_as_kit_formatted = array();

        foreach ($products as $index => $product) {

            

            $order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);

            if (!empty($order_product_kit_info)) {

                if (isset($order_product_kit_info['product_as_kit_info'])) {

                    $product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];

                    $product['product_as_kit'] = true;

                    $product_as_kit_formatted[$product_as_kit_unique_id] = array();
                    $new_products_list[$product_as_kit_unique_id] = $product;


                } else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {

                    $has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];
                    $item_position = $order_product_kit_info['cart_kit_info']['item_position'];

                    if(isset($product_as_kit_formatted[$has_product_as_kit_unique_id])){
                        if(!isset($product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']])) {



                            $product['quantity'] = $product['quantity'] / $new_products_list[$has_product_as_kit_unique_id]['quantity'] ;
                            $new_products_list[$has_product_as_kit_unique_id]['option'][] = array(
                                'name'  => $product['name'],
                                'type'  => '',
                                'model'  => '',
                                'product_source_product_id' => $product['product_id'],
                                'product_source_order_product_id' => $product['order_product_id'],
                                'value' => $product['quantity'] . ' x ' . $product['price']
                            );

                            if($this->bundle_expert->bundle_expert_settings['cart_show_option_in_product_as_kit']){
                                if(isset($product['option'])){
                                    foreach ($product['option'] as $option){








                                        
                                        $new_products_list[$has_product_as_kit_unique_id]['option'][] = array(
                                            'name' => $option['name'],
                                            'value' => $option['value'],
                                            'model'  => '',
                                            'product_source_product_id' => $product['product_id'],
                                            'product_source_order_product_id' => $product['order_product_id'],
                                            'type' => '',
                                        );
                                    }
                                }

                            }

                            $product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']] = true;
                        }
                    } else {
                        $new_products_list[] = $product;
                    }
                }else {
                    $new_products_list[] = $product;
                }

            } else {
                $new_products_list[] = $product;
            }
        }

        foreach ($new_products_list as &$product){
            if(isset($product['product_as_kit'])){
                $product['weight'] = $this->orderCalculateProductAsKitWeight($product['order_product_id'], true);
            }
        }

        return $new_products_list;
    }

    
    
    public function orderProductTotalPrepare($order_id){
        $total = 0;

        $order_info = $this->model_account_order->getOrder($order_id);

        $products = $this->model_account_order->getOrderProducts($order_id);

        $data = array();
        $data['products'] = array();

        foreach ($products as $product) {

            $data['products'][] = array(
                'name'     => $product['name'],
                'model'    => $product['model'],
                'option'   => array(),
                'product_id' => $product['product_id'],
                'order_product_id' => isset($product['order_product_id'])?$product['order_product_id']:null,
                'quantity' => $product['quantity'],
                'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
            );

        }

        $data['products'] = $this->bundle_expert->orderInfoProductListPrepare($data['products']);

        foreach ($data['products'] as $product){
            $total += $product['quantity'];
        }

        return $total;
    }

    public function orderCalculateProductAsKitWeight($order_product_id, $format = false){

        $this->load->model('catalog/product');

        $weight = 0;

        $product_as_kit_unique_id = '';

        $product_as_kit_info = array();

        $product_as_kit_quantity = 0;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");

        if($query->rows){
            $order_id = $query->row['order_id'];

            $products = $this->getOrderProducts($order_id);

            $this->sortArray($products, 'order_product_id');

            foreach ($products as $product) {
                $option_weight = 0;

                $product_info = $this->getProductInfoDefault($product['product_id']);

                $order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);

                if (isset($order_product_kit_info['product_as_kit_info'])) {

                    if ($product['order_product_id'] == $order_product_id) {

                        $product_as_kit_info = $product_info;

                        $product_as_kit_quantity = $product['quantity'];

                        $product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];

                        $product_weight = $product_info['weight'];

                        $options = $this->getOrderOptions($order_id, $product['order_product_id']);

                        foreach ($options as $option) {

                            $product_option_value_info = $this->getProductOptionValue($product['product_id'], $option['product_option_value_id']);

                            if ($product_option_value_info) {
                                if ($product_option_value_info['weight_prefix'] == '+') {
                                    $option_weight += $product_option_value_info['weight'];
                                } elseif ($product_option_value_info['weight_prefix'] == '-') {
                                    $option_weight -= $product_option_value_info['weight'];
                                }
                            }
                        }

                        $product_weight += $option_weight;

                        $product_weight = $this->weight->convert($product_weight, $product_info['weight_class_id'], $this->config->get('config_weight_class_id'));

                        $weight += $product_weight;
                    }


                } else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {

                    $has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];

                    if ($has_product_as_kit_unique_id == $product_as_kit_unique_id) {

                        $product_weight = $product_info['weight'];

                        $options = $this->getOrderOptions($order_id, $product['order_product_id']);

                        foreach ($options as $option) {

                            $product_option_value_info = $this->getProductOptionValue($product['product_id'], $option['product_option_value_id']);

                            if ($product_option_value_info) {
                                if ($product_option_value_info['weight_prefix'] == '+') {
                                    $option_weight += $product_option_value_info['weight'];
                                } elseif ($product_option_value_info['weight_prefix'] == '-') {
                                    $option_weight -= $product_option_value_info['weight'];
                                }
                            }
                        }

                        $product_weight += $option_weight;

                        $product_weight = $this->weight->convert($product_weight, $product_info['weight_class_id'], $this->config->get('config_weight_class_id'));

                        $weight += $product_weight * $product['quantity'];
                    }
                }


            }
        }

        $weight = $weight * $product_as_kit_quantity;

        if($product_as_kit_info){
            $weight = $this->weight->convert($weight, $this->config->get('config_weight_class_id'), $product_as_kit_info['weight_class_id']);
        }

        if ($format) {
            $weight = $this->weight->format($weight, $product_as_kit_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
        }

        return $weight;
    }

    public function accountCheckReorder(){
        $continue = true;

        if ($this->bundle_expert->isEnabled()) {
            $this->load->model('account/order');
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }
            if (isset($this->request->get['order_product_id'])) {
                $order_product_id = $this->request->get['order_product_id'];
            } else {
                $order_product_id = 0;
            }
            $order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);
            if ($order_product_info) {
                $this->load->language('module/bundle_expert');
                if($this->checkProductIsKit($order_product_info['product_id']) || $this->checkProductIsKit_LE($order_product_info['product_id']) ){
                    $href = $this->url->link('product/product', 'product_id=' . $order_product_info['product_id'] . '');
                    $this->session->data['error'] = sprintf($this->language->get('text_product_as_kit_only_product_page'), $href);
                    $this->response->redirect($this->url->link('account/order/info', 'order_id=' . $order_id));
                    $continue = false;
                };
            }
        }

        return $continue;
    }

    public function getProductStockQuantity($product_id){
        $product_info = $this->getProductInfoDefault($product_id);

        $quantity = $product_info['quantity'];

        return $quantity;

    }


    public function getOrderProductAsKitItems($order_id, $product_as_kit_unique_id, $product_as_kit_quantity){

        $order_products = $this->getOrderProductsByOpencartVersion($order_id);

        foreach ($order_products as $index=>$order_product){

            $cart_kit_info = $this->getOrderProductKitInfo($order_product['order_product_id']);
            
            
            if(isset($cart_kit_info['has_product_as_kit_unique_id']) && !empty($cart_kit_info['has_product_as_kit_unique_id']) && $cart_kit_info['has_product_as_kit_unique_id']==$product_as_kit_unique_id){
                $has_product_as_kit_unique_id = $cart_kit_info['has_product_as_kit_unique_id'];
                if(!isset($product_as_kit_just_first[$has_product_as_kit_unique_id])){
                    $product_as_kit_just_first[$has_product_as_kit_unique_id] = $cart_kit_info['kit_unique_id'];
                }

                if(isset($product_as_kit_just_first[$has_product_as_kit_unique_id]) && $product_as_kit_just_first[$has_product_as_kit_unique_id]==$cart_kit_info['kit_unique_id']){
                    if(!isset($cart_kit_info['has_product_as_kit_quantity'])) {
                        $order_products[$index]['quantity'] *= $product_as_kit_quantity;
                        $cart_kit_info['has_product_as_kit_quantity'] = $product_as_kit_quantity;
                    }
                    $order_products[$index]['cart_kit_info_encode'] = base64_encode(serialize($cart_kit_info));
                    $order_products[$index]['cart_kit_info'] = base64_encode(serialize($cart_kit_info));
                    $order_products[$index]['product_as_kit_info_encode'] = '';
                }else{
                    unset($order_products[$index]);
                }
                
            }else{
                unset($order_products[$index]);
            }
        }

        return $order_products;

    }

    public function getOrderProductsByOpencartVersion($order_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        foreach ($query->rows as $index => $row) {
            $query_option = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$row['order_product_id'] . "'");
            foreach ($query_option->rows as $row_option) {
                if ($row_option['type'] == "checkbox") {
                    $query->rows[$index]['option'][$row_option['product_option_id']][] = $row_option['product_option_value_id'];
                } else {
                    $query->rows[$index]['option'][$row_option['product_option_id']] = $row_option['product_option_value_id'];
                }
            }

            $query->rows[$index]['option'][] = $query_option->row;

        }

        $order_products = $query->rows;








        return $order_products;
    }


    public function updateAllProductAsKitPriceInDB() {
        $prices_data = array();

        
        
        

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_as_product = '1' AND status = '1'";
        $query = $this->db->query($sql);

        foreach ($query->rows as $row){
            $kit_id = $row['kit_id'];

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product){
                $price_data = $this->getProductAsKitPrice($linked_product, false);
                if(!empty($price_data)){
                    $prices_data[$linked_product] = $price_data;
                }
            }

        }

        if(!empty($prices_data)){
            $this->updateProductAsKitPricesInDB($prices_data);
        }

    }
    public function updateAllProductAsKitPriceInDB_LE() {
        $prices_data = array();

        
        
        

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_as_product_light_mode = '1' AND status = '1'";
        $query = $this->db->query($sql);

        foreach ($query->rows as $row){
            $kit_id = $row['kit_id'];

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product){
                $this->deleteProductSpecialByOpencartVersion($linked_product);
                $price_data = $this->getProductAsKitPrice_LE($linked_product, false);
                if(!empty($price_data)){
                    $prices_data[$linked_product] = $price_data;
                }
            }

        }

        if(!empty($prices_data)){
            $this->updateProductAsKitPricesInDB($prices_data);
        }

    }

    
    public function updateProductAsKitPriceInDB() {
        if(!empty($this->product_as_kit_dinamic_price_cache)){
            $this->updateProductAsKitPricesInDB($this->product_as_kit_dinamic_price_cache);
        }
    }
    public function updateProductAsKitPriceInDB_LE() {
        if(!empty($this->product_as_kit_dinamic_price_le_cache)){
            $this->updateProductAsKitPricesInDB($this->product_as_kit_dinamic_price_le_cache);
        }
    }

    

    public function updateProductAsKitPricesInDB($prices_data) {

        $products_special_id = array();
        $values = array();
        $values_specials = array();
        $product_ids = array();


        
        $customer_group_id = (int)$this->config->get('config_customer_group_id');

        foreach ($prices_data as $product_id=>$data){

            $product_ids[] = $product_id;

            if(isset($data['price_value_no_tax']) && $data['price_value_no_tax']){
                $price = $data['price_value_no_tax'];

                $values[] = '('.$product_id . ',' . $price.')';


            }

            if(isset($data['special_value']) && $data['special_value']) {
                $special = $data['special_value'];

                $values_specials[] = '('.$product_id . ',' . $special.','.$customer_group_id.')';

                $products_special_id[] = $product_id;
            }

        }

        if(!empty($values)){
            $sql = "INSERT INTO " . DB_PREFIX . "product (product_id,price) VALUES " . implode(',', $values);
            $sql .= " ON DUPLICATE KEY UPDATE price=VALUES(price)";

            $this->db->query($sql);
        }

        



        if(!empty($product_ids)){

            
            $sql = "DELETE FROM " . DB_PREFIX . "product_special WHERE product_id IN (" . implode(',', $product_ids). ") AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."'";
            $this->db->query($sql);
        }

        if(!empty($values_specials)){


            
            $sql = "INSERT INTO " . DB_PREFIX . "product_special (product_id,price,customer_group_id) VALUES " . implode(',', $values_specials);
            $this->db->query($sql);

        }

    }

    
    public function updateProductAsKitPricesCacheInDB($prices_data, $kit_id) {

        $values = array();
        $product_ids = array();

        foreach ($prices_data as $product_id => $data) {

            $product_ids[] = $product_id;

            if (isset($data['price_value_no_tax']) && $data['price_value_no_tax']) {
                $price = $data['price_value_no_tax'];

                $value = '(' . $product_id . ',' . $price;
            } else {
                $value = '(' . $product_id . ',' . '0';
                $price = 0;
            }

            if (isset($data['special_value']) && $data['special_value']) {
                $special = $data['special_value'];

                $value .= ',' . $special;

            } else {
                $value .= ',' . '0';
                $special = 0;
            }
            $value .= ")";

            $values[] = $value;

            if (!empty($product_ids)) {

                
                $sql = "DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE product_id ='" . (int)$product_id . "' AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."' AND kit_id='".(int)$kit_id."'";
                
                $this->db->query($sql);
            }



            
            $sql = "INSERT INTO " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price SET product_id = " . (int)$product_id . ", price= " . (float)$price . ", special= " . (float)$special . ", customer_group_id='".(int)$this->config->get('config_customer_group_id')."', kit_id='".(int)$kit_id."'";
            

            $this->db->query($sql);


        }















    }
    public function getProductListOptions($products) {
        $result = array();

        if(!empty($products)){
            $products_str = implode(',', $products);

            $query1 = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND po.product_id IN (".$products_str.") ORDER BY o.sort_order";
            

            $query = $this->db->query($query1);

            foreach ($query->rows as $row){
                $result[] = array(
                    'product_id'       => $row['product_id'],
                    'name'             => $row['name'],
                    'description'      => $row['description'],
                    'meta_title'       => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_keyword'     => $row['meta_keyword'],
                    'tag'              => $row['tag'],
                    'model'            => $row['model'],
                    'sku'              => $row['sku'],
                    'upc'              => $row['upc'],
                    'ean'              => $row['ean'],
                    'jan'              => $row['jan'],
                    'isbn'             => $row['isbn'],
                    'mpn'              => $row['mpn'],
                    'location'         => $row['location'],
                    'quantity'         => $row['quantity'],
                    'stock_status'     => $row['stock_status'],
                    'image'            => $row['image'],
                    'manufacturer_id'  => $row['manufacturer_id'],
                    'manufacturer'     => $row['manufacturer'],
                    'price'            => ($row['discount'] ? $row['discount'] : $row['price']),
                    'special'          => $row['special'],
                    'reward'           => $row['reward'],
                    'points'           => $row['points'],
                    'tax_class_id'     => $row['tax_class_id'],
                    'date_available'   => $row['date_available'],
                    'weight'           => $row['weight'],
                    'weight_class_id'  => $row['weight_class_id'],
                    'length'           => $row['length'],
                    'width'            => $row['width'],
                    'weight_text'      => $this->weight->format($row['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
                    'height'           => $row['height'],
                    'length_class_id'  => $row['length_class_id'],
                    'length_class'     => $row['length_class'],
                    'subtract'         => $row['subtract'],
                    'rating'           => round($row['rating']),
                    'reviews'          => $row['reviews'] ? $row['reviews'] : 0,
                    'minimum'          => $row['minimum'],
                    'sort_order'       => $row['sort_order'],
                    'status'           => $row['status'],
                    'date_added'       => $row['date_added'],
                    'date_modified'    => $row['date_modified'],
                    'viewed'           => $row['viewed']
                );
            }

        }

        return $result;

    }

    public function getProductListInfoDefault($products) {
        $result = array();

        if(!empty($products)){
            $products_str = implode(',', $products);

            $query1 = "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE  pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.product_id IN (".$products_str.")";

            $query = $this->db->query($query1);

            foreach ($query->rows as $row){
                $result[] = array(
                    'product_id'       => $row['product_id'],
                    'name'             => $row['name'],
                    'description'      => $row['description'],
                    'meta_title'       => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_keyword'     => $row['meta_keyword'],
                    'tag'              => $row['tag'],
                    'model'            => $row['model'],
                    'sku'              => $row['sku'],
                    'upc'              => $row['upc'],
                    'ean'              => $row['ean'],
                    'jan'              => $row['jan'],
                    'isbn'             => $row['isbn'],
                    'mpn'              => $row['mpn'],
                    'location'         => $row['location'],
                    'quantity'         => $row['quantity'],
                    'stock_status'     => $row['stock_status'],
                    'image'            => $row['image'],
                    'manufacturer_id'  => $row['manufacturer_id'],
                    'manufacturer'     => $row['manufacturer'],
                    'price'            => ($row['discount'] ? $row['discount'] : $row['price']),
                    'special'          => $row['special'],
                    'reward'           => $row['reward'],
                    'points'           => $row['points'],
                    'tax_class_id'     => $row['tax_class_id'],
                    'date_available'   => $row['date_available'],
                    'weight'           => $row['weight'],
                    'weight_class_id'  => $row['weight_class_id'],
                    'length'           => $row['length'],
                    'width'            => $row['width'],
                    'weight_text'      => $this->weight->format($row['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
                    'height'           => $row['height'],
                    'length_class_id'  => $row['length_class_id'],
                    'length_class'     => $row['length_class'],
                    'subtract'         => $row['subtract'],
                    'rating'           => round($row['rating']),
                    'reviews'          => $row['reviews'] ? $row['reviews'] : 0,
                    'minimum'          => $row['minimum'],
                    'sort_order'       => $row['sort_order'],
                    'status'           => $row['status'],
                    'date_added'       => $row['date_added'],
                    'date_modified'    => $row['date_modified'],
                    'viewed'           => $row['viewed']
                );
            }

        }

        return $result;

    }



    public function getProductInfoDefault($product_id) {
        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return array(
                'product_id'       => $query->row['product_id'],
                'name'             => $query->row['name'],
                'description'      => $query->row['description'],
                'meta_title'       => $query->row['meta_title'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword'     => $query->row['meta_keyword'],
                'tag'              => $query->row['tag'],
                'model'            => $query->row['model'],
                'sku'              => $query->row['sku'],
                'upc'              => $query->row['upc'],
                'ean'              => $query->row['ean'],
                'jan'              => $query->row['jan'],
                'isbn'             => $query->row['isbn'],
                'mpn'              => $query->row['mpn'],
                'location'         => $query->row['location'],
                'quantity'         => $query->row['quantity'],
                'stock_status'     => $query->row['stock_status'],
                'stock_status_id'     => $query->row['stock_status_id'],
                'stock_status_result'     => ($query->row['quantity']<=0)?$query->row['stock_status']:$this->language->get('text_instock'),
                'image'            => $query->row['image'],
                'manufacturer_id'  => $query->row['manufacturer_id'],
                'manufacturer'     => $query->row['manufacturer'],
                'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                'special'          => $query->row['special'],
                'reward'           => $query->row['reward'],
                'points'           => $query->row['points'],
                'tax_class_id'     => $query->row['tax_class_id'],
                'date_available'   => $query->row['date_available'],
                'weight'           => $query->row['weight'],
                'weight_class_id'  => $query->row['weight_class_id'],
                'length'           => $query->row['length'],
                'width'            => $query->row['width'],
                'weight_text'      => $this->weight->format($query->row['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
                'height'           => $query->row['height'],
                'length_class_id'  => $query->row['length_class_id'],
                'length_class'     => $query->row['length_class'],
                'subtract'         => $query->row['subtract'],
                'rating'           => isset($query->row['rating'])? round($query->row['rating']):0,
                'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
                'minimum'          => $query->row['minimum'],
                'sort_order'       => $query->row['sort_order'],
                'status'           => $query->row['status'],
                'date_added'       => $query->row['date_added'],
                'date_modified'    => $query->row['date_modified'],
                'viewed'           => $query->row['viewed']
            );
        } else {
            return false;
        }
    }

    private function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return ($a[$key] < $b[$key]) ? -1 : 1;
        };
    }

    public function sortArray(&$data, $key){
        if(!empty($data)) {
            usort($data, $this->build_sorter($key));
        }

    }

    public function appProductSpecialByOpencartVersion($product_id, $special){
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
        $product_special = array(
            'customer_group_id' => '',
            'priority' => '',
            'price' => $special,
            'date_start' => '',
            'date_end' => '',
        );
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
    }

    public function deleteProductSpecialByOpencartVersion($product_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
    }



    public function deleteMinifyJsCss(){
        $file_min_css = DIR_APPLICATION . 'view/theme/default/stylesheet/bundle_expert_min.css';
        $file_min_js = DIR_APPLICATION . 'view/javascript/bundle-expert/bundle-expert-min.js';
        if (file_exists($file_min_css)){
            unlink($file_min_css);
        }
        if (file_exists($file_min_js)){
            unlink($file_min_js);
        }
    }

    public function min_css($css_files) {
        $file_min_css = $this->css_min_file;
        if ($css_files && !file_exists($file_min_css)) {
            
            $all_css = '';
            foreach ($css_files as $css_file) {
                if ($css_file) {
                    $all_css .= file_get_contents(DIR_APPLICATION . $css_file);
                }
            }
            $all_css = str_replace('../fonts/', 'fonts/', $all_css);
            $all_css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $all_css);
            $all_css = str_replace(': ', ':', $all_css);
            $all_css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $all_css);
            $all_css = str_replace(array('  ', '    ', '    '), ' ', $all_css);
            file_put_contents(DIR_APPLICATION . $file_min_css, $all_css);
        }
    }
    public function min_js($js_files) {
        $file_min_js = $this->js_min_file;
        if ($js_files) {
            if (!file_exists($file_min_js)) {
                $all_js = '';
                foreach ($js_files as $js_file) {
                    if ($js_file) {
                        $all_js .= file_get_contents(DIR_APPLICATION . $js_file);
                        $all_js .= "\r\n\r\n";
                    }
                }
                $all_js = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $all_js);
                $all_js = str_replace(array("\r\n", "\r"), "\n", $all_js);
                $all_js = preg_replace('/[^\S\n]+/', ' ', $all_js);
                $all_js = str_replace(array(" \n", "\n "), "\n", $all_js);
                $all_js = preg_replace('/\n+/', "\n", $all_js);
                $all_js = str_replace(': ', ':', $all_js);
                $all_js = preg_replace(array('(( )+{)','({( )+)'), '{', $all_js);
                $all_js = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $all_js);
                $all_js = preg_replace(array('(;( )+)','(( )+;)'), ';', $all_js);
                $all_js = str_replace(array(' {',' }','{ ','; '),array('{','}','{',';'), $all_js);
                file_put_contents(DIR_APPLICATION .$file_min_js, $all_js);
            }
        }
    }

    public function dbCacheKitItemsGet($kit_id){
        $kit_items = null;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_items WHERE kit_id = '" . (int)$kit_id . "' ");
        if($query->row){
            $kit_items = json_decode($query->row['kit_items'], true);
        }
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_items_info WHERE kit_id = '" . (int)$kit_id . "' ");
        if($query->row){
            $this->kit_items_info_cache[$kit_id] = json_decode($query->row['kit_items_info'], true);
        }
        return $kit_items;
    }

    public function dbCacheKitItemsAdd($kit_id, $kit_items){

        $kit_items_str = json_encode($kit_items);

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items WHERE kit_id = '" . (int)$kit_id . "' ");

        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_items SET kit_id = '" . (int)$kit_id . "', kit_items = '" . $this->db->escape($kit_items_str) . "' ");

        if(isset($this->kit_items_info_cache[$kit_id])){
            $kit_items_info_str = json_encode($this->kit_items_info_cache[$kit_id]);

            $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items_info WHERE kit_id = '" . (int)$kit_id . "' ");

            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_items_info SET kit_id = '" . (int)$kit_id . "', kit_items_info = '" . $this->db->escape($kit_items_info_str) . "' ");
        }



        $values = array();

























        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE kit_id = '" . (int)$kit_id . "' ");

        foreach ($kit_items as $item_position=>$kit_item){
            $sort_order = 0;

            foreach ($kit_item as $product){
                $product_id =  $product['product_id'];

                    $product_info = $this->getProductInfo($product_id);
                    $stock_quantity = $product_info['quantity'];



                    $sql = "INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_products SET kit_id = " . (int)$kit_id .", item_position = " . (int)$item_position .", product_id = " . (int)$product_id .", stock_quantity = " . (int)$stock_quantity .", sort_order = " . (int)$sort_order . " ";

                    $this->db->query($sql);

                    $sort_order ++;



            }
        }








    }

    public function dbCacheKitItemsAdd_old($kit_id, $kit_items){

        $kit_items_str = json_encode($kit_items);

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items WHERE kit_id = '" . (int)$kit_id . "' ");

        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_items SET kit_id = '" . (int)$kit_id . "', kit_items = '" . $this->db->escape($kit_items_str) . "' ");

        if(isset($this->kit_items_info_cache[$kit_id])){
            $kit_items_info_str = json_encode($this->kit_items_info_cache[$kit_id]);

            $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items_info WHERE kit_id = '" . (int)$kit_id . "' ");

            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_items_info SET kit_id = '" . (int)$kit_id . "', kit_items_info = '" . $this->db->escape($kit_items_info_str) . "' ");
        }



        $values = array();

        foreach ($kit_items as $kit_item){
            foreach ($kit_item as $product){
                $product_id =  $product['product_id'];
                if(!isset($values[$product_id])){
                    $values[$product_id] = '('.$kit_id . ',' . $product['product_id'].')';
                }
            }
        }

        if(!empty($values)){
            $sql = "INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_products (kit_id, product_id) VALUES " . implode(',', $values);
            $sql .= " ON DUPLICATE KEY UPDATE product_id=VALUES(product_id)";

            $this->db->query($sql);
        }

    }

    public function dbCacheKitItemsClear(){
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_products");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items_info");
    }

    public function dbCacheKitLinkProductsGet($kit_id){
        $kit_link_products = null;
        $kit_link_products = array();

        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE kit_id = '" . (int)$kit_id . "' ");
        if($query->rows){
            $kit_link_products = array_column ($query->rows, 'product_id');



            
            
        }
        return $kit_link_products;
    }

    public function dbCacheWidgetLinkProductsClear(){
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_widget_link_products");
    }

    public function dbCacheWidgetLinkProductsGet($widget_id){
        $widget_link_products = null;
        $widget_link_products = array();

        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "bundle_expert_cache_widget_link_products WHERE widget_id = '" . (int)$widget_id . "' ");
        if($query->rows){
            $widget_link_products = array_column($query->rows, 'product_id');
        }

        return $widget_link_products;
    }

    public function dbCacheWidgetLinkProductsAdd($widget_id, $widget_link_products){

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_widget_link_products WHERE widget_id = '" . (int)$widget_id . "' ");

        if(!empty($widget_link_products)){
            foreach ($widget_link_products as $widget_link_product){
                $values[] = '('.$widget_id . ',' . $widget_link_product.')';
            }

            if(!empty($values)){
                $sql = "INSERT INTO " . DB_PREFIX . "bundle_expert_cache_widget_link_products (widget_id, product_id) VALUES " . implode(',', $values);
                $this->db->query($sql);
            }
        }

    }

    public function dbCacheKitLinkProductsAdd($kit_id, $kit_info, $kit_link_products){

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE kit_id = '" . (int)$kit_id . "' ");

        if(!empty($kit_link_products)){
            foreach ($kit_link_products as $kit_link_product){
                $product_mode = "default";
                if($kit_info['kit_as_product']){
                    $product_mode = "kit_as_product";
                }
                if($kit_info['kit_as_product_light_mode']){
                    $product_mode = "kit_as_product_light_mode";
                }
                if($kit_info['main_mode']=="collection"){
                    $product_mode = "collection";
                }
                if($kit_info['main_mode']=="series"){
                    $product_mode = "series";
                }

                $values[] = "(".$kit_id . "," . $kit_link_product.", '".$product_mode ."')";
            }

            if(!empty($values)){
                $sql = "INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_link_products (kit_id, product_id, mode) VALUES " . implode(',', $values);
                $this->db->query($sql);
            }
        }

        






    }

    public function dbCacheKitLinkProductsClear(){
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products");
    }

    public function dbCacheClear(){
        $this->dbCacheKitItemsClear();
        $this->dbCacheKitLinkProductsClear();
        $this->dbCacheWidgetLinkProductsClear();
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits");


        $cache_key = $this->bundle_expert->getCacheKey(array('mode' => 'all_cache'));
        $this->cache->delete($cache_key);
    }

    public function dbCacheKitClear($kit_id){
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_items_info WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE kit_id = '".(int)$kit_id."'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le WHERE kit_id = '".(int)$kit_id."'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE kit_id = '".(int)$kit_id."'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits WHERE kit_id = '".(int)$kit_id."'");


        
        $widgets = $this->getKitWidgets($kit_id);

        foreach ($widgets as $widget){
            $key_data = array(
                'mode' => 'widget_kits',
                'widget_id' => $widget['widget_id'],
                'kit_id' => $kit_id
            );
            $cache_key = $this->getCacheKey($key_data);
            $this->cache->delete($cache_key);
        }
    }

    
    public function dbCacheKitClearByProductId($product_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_products WHERE product_id = ".(int)$product_id."");

        foreach ($query->rows as $row){
            $this->dbCacheKitClear($row['kit_id']);
        }

    }

    public function getKitWidgets($kit_id)
    {

        $query = $this->db->query("SELECT wk.*, w.name FROM " . DB_PREFIX . "bundle_expert_widget_kit wk LEFT JOIN " . DB_PREFIX . "bundle_expert_widget w ON (w.widget_id=wk.widget_id) WHERE kit_id = '" . (int)$kit_id . "'");

        return $query->rows;
    }

    
    
    
    public function tryAddProductAsKitFromCategory($product_id, &$json){

        $redirect_to_main_page = true;

        $result = array();

        if($this->bundle_expert_settings['product_as_kit_add_from_category_enable']){

            
            $product_as_kit_kits = $this->getProductAsKitKits($product_id);

            if(empty($product_as_kit_kits)){
                $product_as_kit_kits = $this->getProductAsKitKits_LE($product_id);
            }

            if($product_as_kit_kits){
                $kit_id = $product_as_kit_kits[0]['kit_id'];

                $kit_info = $this->bundle_expert->getKit($kit_id);

                $main_product_id = $product_id;

                if (!empty($kit_info)) {

                    $admin_mode = false;
                    $ignore_product_in_cart = "";
                    $main_product_in_cart = false;

                    $get_kit_products_settings = array(
                        'main_product_id' => $main_product_id,
                        'filter_kit_items' => true,
                        'only_first' => false,
                        'admin_mode' => $admin_mode,
                    );

                    $kit_items = $this->bundle_expert->getKitProducts($kit_id, $get_kit_products_settings, $ignore_product_in_cart);

                    $kit_items_data = $this->bundle_expert->getDataKit($kit_id, $main_product_id, $kit_items, true, true, $admin_mode);

                    $kit_info['kit_items'] = $kit_items_data['kit_items'];

                    $kit_enable_status = $this->bundle_expert->getKitEnableStatus($kit_info, $kit_info['kit_items'], $main_product_id, $main_product_in_cart);

                    
                    if ($kit_enable_status['display_kit']) {

                        
                        $has_required_options = false;
                        foreach ($kit_info['kit_items'] as $kit_item) {
                            foreach ($kit_item['products'] as $kit_item_product) {
                                if (!$kit_item_product['is_free_product'] || ($kit_item_product['is_free_product'] && $kit_item_product['free_product_in_kit'])) {
                                    foreach ($kit_item_product['product_info']['options'] as $option) {
                                        if ($option['required'] && !$option['option_has_fixed_value']) {
                                            $has_required_options = true;
                                        }
                                    }
                                }
                            }
                        }

                        
                        if(!$has_required_options){
                            $redirect_to_main_page = false;
                            $result = $this->addProductAsKitFromCategory($kit_info, $main_product_id);
                            if(!isset($result['success'])){
                                $redirect_to_main_page = true;
                            }
                        }

                    }
                }
            }

        }

        if($redirect_to_main_page){
            $json['error']['product_as_kit'] = '';
            $json['error']['option'][-1] = "Bundle Redirect";
            $be_continue = true;
        }else{
            if (method_exists($this, 'return_to_ajax_add_to_cart_custom')){
                $this->return_to_ajax_add_to_cart_custom($result);
            } else {
                $this->return_to_ajax_add_to_cart($result);
            }
            $be_continue = false;
        }

        return $be_continue;
    }

    
    private function addProductAsKitFromCategory($kit_info, $main_product_id){

        $data = array();

        $quantity = 1;

        if(isset($this->request->post['quantity'])){
            $quantity = (int)$this->request->post['quantity'];
        }
        if(isset($this->request->get['quantity'])){
            $quantity = (int)$this->request->get['quantity'];
        }

        $data['kit_id'] = $kit_info['kit_id'];
        $data['main_product_id'] = $main_product_id;
        $data['product_as_kit_data'] = array(
            'quantity' => $quantity,
            'product_id' => $main_product_id,
        );
        $data['main_product_in_cart'] = 0;
        $data['cart_merge_confirm'] = false;
        $data['cart_merge_enable'] = 0;
        $data['kit_from_cart_unique_id'] = null;
        $data['admin_mode'] = false;

        if(isset($this->request->post['option'])){
            $data['product_as_kit_data']['option'] = $this->request->post['option'];
        }
        if(isset($this->request->get['option'])){
            $data['product_as_kit_data']['option'] = $this->request->get['option'];
        }
        if(isset($this->request->post['quantity'])){
            $data['product_as_kit_data']['quantity'] = $this->request->post['quantity'];
        }
        if(isset($this->request->get['quantity'])){
            $data['product_as_kit_data']['quantity'] = $this->request->get['quantity'];
        }

        $data['products'] = array();

        foreach ($kit_info['kit_items'] as $kit_item) {

            $item_position_free = 0;

            foreach ($kit_item['products'] as $kit_item_product) {

                if (!$kit_item_product['is_free_product'] || ($kit_item_product['is_free_product'] && $kit_item_product['free_product_in_kit'])) {

                    $kit_item_data = array(
                        'product_id' => $kit_item_product['product_id'],
                        'item_position' => $kit_item['item_position'],
                        'item_position_free' => $item_position_free,
                        'quantity' => $kit_item_product['quantity'],
                        'quantity_edit' => $kit_item_product['quantity_edit'],
                        'empty_mode_item_is_empty' => $kit_item['empty_mode_item_is_empty'],
                        'is_free_product' => $kit_item_product['is_free_product'],
                        'free_product_in_kit' => $kit_item_product['free_product_in_kit'],
                        'quantity_field' => $kit_item_product['quantity'],
                        'checkbox_in_kit' => 'on',

                    );

                    $data['products'][] = $kit_item_data;

                }

                if ($kit_item_product['is_free_product']) {
                    $item_position_free++;
                }
            }
        }

        $result = $this->load->controller('checkout/bundle_expert/add_kit_to_cart_from_category', $data);

        return $result;

    }

    
    private function return_to_ajax_add_to_cart($result){
        $json_kit = $result;

        $json_kit['total'] = $result['total'];

        $ocmod_point_001 = 17;



        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json_kit));
    }

    
    private function opencart_expert_api($api, $data){

        $this->load->language('sale/order');
        $this->load->model('setting/store');
        $url = 'http://api.opencart-expert.com/';

        
        $url_data = array();

        foreach ($data as $key => $value) {
            if ($key != 'route' && $key != 'token' && $key != 'store_id') {
                $url_data[$key] = $value;
            }
        }

        $url_data['language'] = $this->config->get('config_admin_language');

        $curl = curl_init();

        
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_PORT, 443);
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $api . ($url_data ? '&' . http_build_query($url_data) : ''));

        if(isset($data['help'])){
            curl_setopt($curl, CURLOPT_TIMEOUT_MS, 3000);
        }

        if ($this->request->post) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
        }


        curl_setopt($curl, CURLOPT_COOKIE, 'XDEBUG_SESSION=XDEBUG_ECLIPSE');

        $json = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            $result = array();
            $result['error'] = $error_msg;
        } else {
            $result = json_decode($json, true);
        }

        curl_close($curl);

        return $result;


    }

    
    private function checkLicense(){
        $check = false;

        







        if(isset($this->request->server['HTTP_HOST'])) {
            $domain_name = htmlspecialchars_decode($this->request->server['HTTP_HOST']);

            $bundle_expert_license = $this->config->get('bundle_expert_license');

            
            if (isset($bundle_expert_license) && !is_array($bundle_expert_license)) {
                $bundle_expert_license = json_decode($bundle_expert_license, true);
            }

            
            if (!empty($bundle_expert_license['license_domain'])) {
                if (stripos($domain_name, "." . $bundle_expert_license['license_domain']) !== false) {
                    $domain_name = $bundle_expert_license['license_domain'];
                }
            }

            $str_1 = (isset($bundle_expert_license['key']) ? $bundle_expert_license['key'] : '') . $domain_name;

            
            if (strpos($domain_name, 'www.') === false) {
                $domain_name_2 = 'www.' . $domain_name;
                $str_2 = (isset($bundle_expert_license['key']) ? $bundle_expert_license['key'] : '') . $domain_name_2;
            } else {
                $domain_name_2 = str_replace('www.', '', $domain_name);
                $str_2 = (isset($bundle_expert_license['key']) ? $bundle_expert_license['key'] : '') . $domain_name_2;
            }

            $license_app_1 = sha1($str_1);
            $license_app_2 = sha1($str_2);



            if (isset($bundle_expert_license['license_app']) && $bundle_expert_license['license_app'] == $license_app_1)
                $check = true;
            if (isset($bundle_expert_license['license_app']) && $bundle_expert_license['license_app'] == $license_app_2)
                $check = true;


        }else{
            $check = true;
        }

        return $check;
    }

    public function isLicensed(){

        return $this->check_license;
    }

    public function getLicenseKeyFromLicenseServer($domain_name, $order_number, $market_id){

        $data = array();

        $data['domain_name'] = $domain_name;
        $data['order_number'] = $order_number;
        $data['market_id'] = $market_id;

        $result = array(
            'error' => '',
            'license_key' => '',
        );

        $result = $this->opencart_expert_api('license/license/getLicenseKey', $data);

        return $result;
    }

    public function registerModuleByLicenseKeyAtLicenseServer($license_key){

        $data = array();

        $data['license_key'] = $license_key;

        $result = array(
            'error' => '',
            'license_app' => '',
            'key' =>  ''
        );

        $result = $this->opencart_expert_api('license/license/getLicenseApp', $data);

        return $result;
    }


    public function convertInputData(&$data){
        $t = 1;
        $new_data = array();

        foreach ($data as $index=>$item){
            if(strripos($index, '_kit_items_free')>0){
                $correct_index = substr($index, strripos($index, '_kit_items')+1);
                if(!isset($new_data[$correct_index])){
                    $new_data[$correct_index] = array();
                }
                foreach ($item as $free_item_index=>$free_item) {
                    foreach ($free_item as $item_index => $option) {
                        if (!isset($new_data[$correct_index][$free_item_index][$item_index]['option'])) {
                            $new_data[$correct_index][$free_item_index][$item_index]['option'] = array();
                        }
                        foreach ($option['option'] as $option_index => $option_value) {
                            $new_data[$correct_index][$free_item_index][$item_index]['option'][$option_index] = $option_value;
                        }
                    }
                }
            }else if(strripos($index, '_kit_items')>0){
                $correct_index = substr($index, strripos($index, '_kit_items')+1);
                if(!isset($new_data[$correct_index])){
                    $new_data[$correct_index] = array();
                }
                foreach ($item as $item_index=>$option){
                    if(!isset($new_data[$correct_index][$item_index]['option'])){
                        $new_data[$correct_index][$item_index]['option'] = array();
                    }
                    foreach ($option['option'] as $option_index=>$option_value){
                        $new_data[$correct_index][$item_index]['option'][$option_index] = $option_value;
                    }
                }

            }else{
                $new_data[$index] = $data[$index];
            }

        }
        $data = $new_data;

    }

    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }

    public function getProductOptionValue($product_id, $product_option_value_id) {
        $query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getCollectionPriceFormatted($price) {

        $this->load->language('module/bundle_expert');

        $price = sprintf($this->language->get('text_collection_price_formatted'), $price);

        return $price;
    }

    public function getKitPeriods($kit_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id = '" . (int)$kit_id . "' ORDER BY priority");

        return $query->rows;
    }

    public function initCurrencieSession(){
        
        if(!isset($this->session->data['currency'])) {
            $code = '';

            $this->load->model('localisation/currency');

            $currencies = $this->model_localisation_currency->getCurrencies();

            if (isset($this->session->data['currency'])) {
                $code = $this->session->data['currency'];
            }

            if (isset($this->request->cookie['currency']) && !array_key_exists($code, $currencies)) {
                $code = $this->request->cookie['currency'];
            }

            if (!array_key_exists($code, $currencies)) {
                $code = $this->config->get('config_currency');
            }

            if (!isset($this->session->data['currency']) || $this->session->data['currency'] != $code) {
                $this->session->data['currency'] = $code;
            }
        }
    }

    
    public function getKitWithProduct($product_id) {
        $kits = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_products  WHERE product_id = '" . (int)$product_id . "' GROUP BY kit_id");

        foreach ($query->rows as $row){
            $kits[]=$row['kit_id'];
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_link_products  WHERE product_id = '" . (int)$product_id . "' GROUP BY kit_id");

        foreach ($query->rows as $row){
            $kits[]=$row['kit_id'];
        }

        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_items  WHERE item_id = '" . (int)$product_id . "' AND item_type = 'product' GROUP BY kit_id");

        foreach ($query->rows as $row){
            $kits[]=$row['kit_id'];
        }
        return $kits;
    }

    
    
    
    
    public function dbCacheUpdateCategoryKits($kit_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits WHERE kit_id = '" . (int)$kit_id . "'");

        $kit_info = $this->bundle_expert->getKit($kit_id);

        if($kit_info){
            if(($kit_info['main_mode']=='kit' && !$kit_info['kit_as_product'] && !$kit_info['kit_as_product_light_mode']) || $kit_info['main_mode']=='series'){

                $kit_link_products = $this->getKitLinkProducts2($kit_id);

                if(empty($kit_link_products)){
                    $kit_link_products[] = -1;
                }

                foreach ($kit_link_products as $kit_link_product){
                    $main_product_id = $kit_link_product;

                    $kit_id = $kit_id;
                    $stock_in_status = 1;

                    
                    $widgets_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget  WHERE display_mode = 'category_list' AND link_category_filter_kits = 'all'");

                    foreach ($widgets_query->rows as $widget){
                        $link_category = $this->getWidgetLinkCategory($widget['widget_id']);

                        foreach ($link_category as $link_category_item){
                            $widget_id = $widget['widget_id'];
                            $category_id = $link_category_item['category_id'];

                            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_category_kits SET widget_id = '" . (int)$widget_id . "', category_id = '" . (int)$category_id . "', kit_id = '" . (int)$kit_id . "', main_product_id = '" . (int)$main_product_id . "', stock_in_status = '" . (int)$stock_in_status . "'");

                        }
                    }

                    
                    $widgets_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget  WHERE display_mode = 'category_list' AND link_category_filter_kits = 'selected'");

                    foreach ($widgets_query->rows as $widget){
                        $widgets_kit = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_kit  WHERE widget_id = '" . (int)$widget['widget_id'] . "' AND kit_id = '" . (int)$kit_id . "' LIMIT 1");

                        if($widgets_kit->row){
                            $link_category = $this->getWidgetLinkCategory($widget['widget_id']);

                            foreach ($link_category as $link_category_item){
                                $widget_id = $widget['widget_id'];
                                $category_id = $link_category_item['category_id'];

                                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_category_kits SET widget_id = '" . (int)$widget_id . "', category_id = '" . (int)$category_id . "', kit_id = '" . (int)$kit_id . "', main_product_id = '" . (int)$main_product_id . "', stock_in_status = '" . (int)$stock_in_status . "'");

                            }
                        }

                    }

                    
                    if($kit_info['kit_mode']='list_product'){
                        break;
                    }
                }


            }
        }

    }

    public function getWidgetLinkCategory($widget_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_to_category WHERE widget_id = '" . (int)$widget_id . "'");

        return $query->rows;
    }

    public function updateKitCache($kit_id) {
        
        $periods = $this->getKitPeriods($kit_id);
        if(empty($periods)){
            $this->setToKitDefaultPeriod($kit_id);
        }

        $this->bundle_expert->getKitItems($kit_id);
        $this->bundle_expert->getKitLinkProducts2($kit_id);

        
        $this->bundle_expert->dbCacheUpdateCategoryKits($kit_id);
    }

    
    public function updateKitAsProductMainProductDataCache($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product = '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                
                $update_data = array();
                $result = $this->getProductAsKitMainProductQuantity($kit_id, $linked_product);

                $main_product_id = $linked_product;

                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id = '" . (int)$main_product_id . "'");

                $quantity = $result['quantity'];
                $stock_in_status = $result['stock_in_status'];

                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data SET kit_id = '" . (int)$kit_id . "', main_product_id = '" . (int)$main_product_id . "', quantity = '" . (float)$quantity . "', stock_in_status = '" . (int)$stock_in_status . "'");


                if($this->bundle_expert_settings['product_as_kit_update_main_product_quantity']){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$main_product_id . "'");
                }
            }

        }
    }

    public function updateKitAsProductMainProductDataCache_LE($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product_light_mode= '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {

                $update_data = array();
                $result = $this->getProductAsKitMainProductQuantity($kit_id, $linked_product);

                $main_product_id = $linked_product;

                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id = '" . (int)$main_product_id . "'");

                $quantity = $result['quantity'];
                $stock_in_status = $result['stock_in_status'];

                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le SET kit_id = '" . (int)$kit_id . "', main_product_id = '" . (int)$main_product_id . "', quantity = '" . (float)$quantity . "', stock_in_status = '" . (int)$stock_in_status . "'");


                if($this->bundle_expert_settings['product_as_kit_update_main_product_quantity']){
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$main_product_id . "'");
                }

            }

        }
    }

    public function updateKitAsProductMainProductPrice($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product = '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                $price_data = $this->getProductAsKitPrice($linked_product, false);
                if (!empty($price_data)) {
                    $prices_data[$linked_product] = $price_data;
                }

            }

            if (!empty($prices_data)) {
                $this->updateProductAsKitPricesInDB($prices_data);
            }
        }
    }

    public function updateKitAsProductMainProductPrice_LE($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product_light_mode = '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                $price_data = $this->getProductAsKitPrice_LE($linked_product, false);
                if (!empty($price_data)) {
                    $prices_data[$linked_product] = $price_data;
                }
            }

            if (!empty($prices_data)) {
                $this->updateProductAsKitPricesInDB($prices_data);
            }
        }

    }

    
    public function clearKitAsProductMainProductPriceCache($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                $product_id = $linked_product;
                $sql = "DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE product_id ='" . (int)$product_id . "' AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."' AND kit_id='".(int)$kit_id."'";
                $this->db->query($sql);
            }


        }
    }

    public function updateKitAsProductMainProductPriceCache($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product = '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                $price_data = $this->getProductAsKitPrice($linked_product, false);
                if (!empty($price_data)) {
                    $prices_data[$linked_product] = $price_data;
                }
            }

            if (!empty($prices_data)) {
                $this->updateProductAsKitPricesCacheInDB($prices_data, $kit_id);
            }
        }
    }
    

    public function clearKitAsProductMainProductPriceCache_LE($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product_light_mode = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                $product_id = $linked_product;
                $sql = "DELETE FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_price WHERE product_id ='" . (int)$product_id . "' AND customer_group_id='".(int)$this->config->get('config_customer_group_id')."' AND kit_id='".(int)$kit_id."'";
                $this->db->query($sql);
            }


        }
    }

    public function updateKitAsProductMainProductPriceCache_LE($kit_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND kit_as_product_light_mode = '1' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            foreach ($linked_products as $linked_product) {
                
                $price_data = $this->getProductAsKitPrice_LE($linked_product, false,$kit_id);
                if (!empty($price_data)) {
                    $prices_data[$linked_product] = $price_data;
                }
            }

            if (!empty($prices_data)) {
                $this->updateProductAsKitPricesCacheInDB($prices_data, $kit_id);
            }
        }
    }
    public function getProductAsKitMainProductDataFromCache($main_product_id) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data WHERE main_product_id = '" . (int)$main_product_id . "'");

        $result = $query->row;

        return $result;
    }

    public function getProductAsKitMainProductDataFromCache_LE($main_product_id) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_product_as_kit_main_product_data_le WHERE main_product_id = '" . (int)$main_product_id . "'");

        $result = $query->row;

        return $result;
    }


    
    public function getProductAsKitMainProductQuantity($kit_id, $main_product_id) {

        $enable = true;

        $main_product_info = $this->getProductInfo($main_product_id);

        if($main_product_info){

            $main_product_quantity = $main_product_info['quantity'];

            $main_quantity_depends_items_quantity = true;

            if($main_quantity_depends_items_quantity){

                $kit_info = $this->getKit($kit_id);

                if($kit_info){

                    $get_kit_products_settings = array(
                        'main_product_id' => $main_product_id,
                        'filter_kit_items' => true,
                        'only_first' => false,
                        'admin_mode' => false,
                    );

                    $kit_items = $this->getKitProducts($kit_id, $get_kit_products_settings);


                    foreach ($kit_items as $index=>$kit_item) {
                        $kit_item_products = $kit_item['products'];
                        $stock = true;
                        if (count($kit_item_products) == 0 && !$kit_item['is_free_product']) {
                            $stock = false;
                        }else{
                            foreach ($kit_item_products as $kit_item_product){

                                if($kit_item_product['is_free_product']){
                                    break;
                                }

                                
                                if($kit_item['selectable']){
                                    if($kit_item_product['product_info']['quantity']>=$kit_item_product['quantity']){
                                        $stock = true;
                                        break;
                                    }
                                }

                                if($kit_item_product['product_info']['quantity']<$kit_item_product['quantity']){
                                    $stock = false;
                                }

                                
                                
                                foreach ($kit_item_product['product_info']['options'] as $option) {
                                    if ($option['fixed_value']) {
                                        foreach ($option['product_option_value'] as $product_option_value) {
                                            if ($product_option_value['quantity'] < $kit_item_product['quantity']) {
                                                $stock = false;
                                            }
                                        }
                                    }
                                }
                                


                            }
                        }

                        if(!$stock){
                            $enable = false;
                            break;

                        }

                    }

                }else{
                    $enable = false;
                }


            }
        }

        if(isset($main_product_quantity) && $main_product_quantity<=0){
            if($enable && $this->bundle_expert_settings['product_as_kit_update_main_product_quantity']){
                $main_product_quantity = 1000;
            }
        }

        if(isset($main_product_quantity) && $main_product_quantity<=0){
            $enable = false;
        }

        if(!$enable){
            $quantity = 0;
            $stock_in_status =false;
        }else{

            $stock_in_status =true;

            if(isset($main_product_quantity)){
                $quantity = $main_product_quantity;
            }else{
                $quantity = 1000;
            }

        }

        $result = array(
            'quantity'=>$quantity,
            'stock_in_status'=>$stock_in_status,
        );
        return $result;

    }

    public function getProductAsKitMainProductQuantity_old($kit_id, $main_product_id) {
        $quantity = 0;
        $stock_in_status = true;

        $main_product_info = $this->getProductInfo($main_product_id);

        if($main_product_info){
            $quantity = $main_product_info['quantity'];

            $main_quantity_depends_items_quantity = true;
            
            
            if($main_quantity_depends_items_quantity){
                $enable = false;

                $kit_info = $this->getKit($kit_id);

                if($kit_info){

                    $get_kit_products_settings = array(
                        'main_product_id' => $main_product_id,
                        'filter_kit_items' => true,
                        'only_first' => false,
                        'admin_mode' => false,
                    );

                    $kit_items = $this->getKitProducts($kit_id, $get_kit_products_settings);
                    $kit_info['kit_items'] = $kit_items;

                    $kit_enable_status = $this->getKitEnableStatus_step2($kit_info, $kit_info['kit_items'], $main_product_id);
                    if ($kit_enable_status['display_kit']){
                        $enable = true;
                    }

                    if(!$enable){
                        $quantity = 0;
                    }



                    $stock_in_status = $kit_enable_status['stock_in_status'];
                }


            }
        }

        $result = array(
            'quantity'=>$quantity,
            'stock_in_status'=>$stock_in_status,
        );
        return $result;

    }


    public function updateCollectionMainProductPrice($kit_id) {

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE kit_id='" . (int)$kit_id . "' AND main_mode = 'collection' AND status = '1'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {

            $linked_products = $this->getKitLinkProducts2($kit_id);

            $price_data = $this->getKitProductsMinMaxPrices($kit_id);
            $price_data['price_value'] = $price_data['min_value'];
            $price_data['price_value_no_tax'] = $price_data['min_value'];
            $price_data['special_value'] = false;

            foreach ($linked_products as $linked_product) {

                if (!empty($price_data)) {
                    $prices_data[$linked_product] = $price_data;
                }
            }

            if (!empty($prices_data)) {
                $this->updateProductAsKitPricesInDB($prices_data);
            }
        }
    }

    public function onChangeOrderStatus($order_id) {

        if(!isset($this->session->data['currency'])){
            $this->bundle_expert->initCurrencieSession();
        }

        $order_kits = $this->getOrderKits($order_id);

        foreach ($order_kits as $kit) {
            $kit_id = $kit['kit_id'];
            $kit_info = $this->getKit($kit_id);

            $this->dbCacheKitClear($kit_id);

            if($kit_info && $kit_info['status']){

                $this->updateKitCache($kit_id);

                $this->bundle_expert->updateProductAsKitPricesProcess($kit_info);
            }


        }







    }


    public function onEditProduct($product_id) {
        if(!isset($this->session->data['currency'])){
            $this->bundle_expert->initCurrencieSession();
        }

        $kits = $this->getKitWithProduct($product_id);

        foreach ($kits as $kit_id) {
            $kit_info = $this->getKit($kit_id);

            $this->dbCacheKitClear($kit_id);

            if($kit_info && $kit_info['status']){
                
                $this->updateKitCache($kit_id);

                
                $this->bundle_expert->updateProductAsKitPricesProcess($kit_info);
                

















            }


        }

    }


    public function setToKitDefaultPeriod($kit_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_kit_period WHERE kit_id = '" . (int)$kit_id . "'");

        $customer_group_id="-1000";
        $priority="-1000";
        $date_start="1990-01-01";
        $date_end="2090-12-31";
        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_kit_period SET kit_id = '" . (int)$kit_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '" . (int)$priority . "', date_start = '" . $this->db->escape($date_start) . "', date_end = '" . $this->db->escape($date_end) . "'");
    }

    public function getWidgetCacheUniqueId($widget, $current_product_id) {
        $result = '';

        $data = array(
            'mode' => 'widget_data',
            'widget_id' => $widget['widget_id'],
            'kit_id' => $widget['kit_info']['kit_id'],
            'link_product_id' => $current_product_id,
        );
        $cache_key_data = $this->bundle_expert->getCacheKey($data);

        $widget_cache_data = $this->cache->get($cache_key_data);
        if (!empty($widget_cache_data)) {
            $result = $widget_cache_data['unique_id'];
        }

        return $result;
    }
    public function getWidgetCacheUniqueIdLinkToMainProducts($widget, $current_product_id) {
        $result = '';

        $data = array(
            'mode' => 'widget_data',
            'widget_id' => $widget['widget_id'],
            'kit_id' => 'list',
            'link_product_id' => $current_product_id,
        );
        $cache_key_data = $this->bundle_expert->getCacheKey($data);

        $widget_cache_data = $this->cache->get($cache_key_data);
        if (!empty($widget_cache_data)) {
            $result = $widget_cache_data['unique_id'];
        }

        return $result;
    }
    public function createWidgetCacheLinkToMainProducts($widget, $current_product_id, $widgets) {

        $widget_html = '';

        $data = array(
            'mode' => 'widget_data',
            'widget_id' => $widget['widget_id'],
            'kit_id' => 'list',
            'link_product_id' => $current_product_id,
        );
        $cache_key_data = $this->bundle_expert->getCacheKey($data);

        $data = array(
            'mode' => 'widget_html',
            'widget_id' => $widget['widget_id'],
            'kit_id' => 'list',
            'link_product_id' => $current_product_id,
        );
        $cache_key_html = $this->bundle_expert->getCacheKey($data);

        $widget_cache_html = $this->cache->get($cache_key_html);


        if (empty($widget_cache_html)) {

            $get_kit_products_settings = array(
                'main_product_id' => '',
                'filter_kit_items' => true,
                'only_first' => false,
                'admin_mode' => false,
            );



            foreach ($widgets as $index=>$widget_item){

                
                if ($widget_item['checkbox_mode']) {
                    $get_kit_products_settings['limit_product_process_for_widget_checkbox_mode'] = true;
                }

                $get_kit_products_settings['main_product_id'] = $widget_item['main_product_id'];

                $kit_items = $this->getKitProducts($widget_item['kit_info']['kit_id'], $get_kit_products_settings);
                $widgets[$index]['kit_info']['kit_items'] = $kit_items;
                $widgets[$index]['kit_info']['kit_enable_status'] = $this->getKitEnableStatus_step2($widget_item['kit_info'], $kit_items, $widget_item['main_product_id']);
            }


            $widget_html = $this->load->controller('module/bundle_expert/_createWidgetLinkToMainProducts', array('widgets'=>$widgets,'current_product_id'=>$current_product_id));;

            if($this->bundle_expert_settings['cache_widgets']){
                $this->cache->set($cache_key_html, $widget_html);
                $this->cache->set($cache_key_data, $widget);

            }
        } else {
            $widget_html = $widget_cache_html;

        }

        return $widget_html;


    }
    public function createWidgetCache($widget, $current_product_id) {

        $widget_html = '';

        $data = array(
            'mode' => 'widget_data',
            'widget_id' => $widget['widget_id'],
            'kit_id' => $widget['kit_info']['kit_id'],
            'link_product_id' => $current_product_id,
        );
        $cache_key_data = $this->bundle_expert->getCacheKey($data);

        $data = array(
            'mode' => 'widget_html',
            'widget_id' => $widget['widget_id'],
            'kit_id' => $widget['kit_info']['kit_id'],
            'link_product_id' => $current_product_id,
        );
        $cache_key_html = $this->bundle_expert->getCacheKey($data);

        $widget_cache_html = $this->cache->get($cache_key_html);
        $widget_cache_data = $this->cache->get($cache_key_data);

        if (empty($widget_cache_html)) {

            $get_kit_products_settings = array(
                'main_product_id' => '',
                'filter_kit_items' => true,
                'only_first' => false,
                'admin_mode' => false,
            );

            
            if ($widget['checkbox_mode']) {
                $get_kit_products_settings['limit_product_process_for_widget_checkbox_mode'] = true;
            }

            $get_kit_products_settings['main_product_id'] = $widget['main_product_id'];

            $widget['kit_info']['kit_items'] = $this->getKitProducts($widget['kit_info']['kit_id'], $get_kit_products_settings);
            $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus_step2($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);

            $widget_html = $this->load->controller('module/bundle_expert/_createWidget', array('widget'=>$widget,'current_product_id'=>$current_product_id));;

            if($this->bundle_expert_settings['cache_widgets']){
                $this->cache->set($cache_key_html, $widget_html);
                $this->cache->set($cache_key_data, $widget);

            }
        } else {
            $widget_html = $widget_cache_html;
            $widget_data = $widget_cache_data;
        }

        return $widget_html;


    }

    public function getCacheKey($data){
        $key = '';

        switch ($data['mode']){
            case 'widget_edit':
                $widget_id = $data['widget_id'];
                $key = "bundle_expert.widget.widget_id_" . $widget_id . "_";
                break;
            case 'all_cache':
                $key = "bundle_expert";
                break;
            case 'widget_html':
                $key = "bundle_expert.widget.widget_id_" . $data['widget_id'] . "_.kit_id_" . $data['kit_id'] . "_.link_product_id_" . $data['link_product_id']. "_.html";
                break;
            case 'widget_data':
                $key = "bundle_expert.widget.widget_id_" . $data['widget_id'] . "_.kit_id_" . $data['kit_id'] . "_.link_product_id_" . $data['link_product_id']. "_.data";
                break;
            case 'widget_kits':
                $key = "bundle_expert.widget.widget_id_" . $data['widget_id'] . "_.kit_id_" . $data['kit_id'] . "_";
                break;
            case '':
                break;
        }

        return $key;

    }

    public function getKitEnableStatusFromCache($widget, $get_kit_products_settings){
        $enable = false;

        $kit_id = $widget['kit_info']['kit_id'];
        $main_product_id = $widget['main_product_id'];

        if(empty($main_product_id)){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id IS NULL");
        }else{
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id = '" . (int)$main_product_id . "'");
        }


        if ($query->row) {
            $enable = $query->row['enable'];
        }else{
            $kit_info = $widget['kit_info'];
            $widget['kit_info']['kit_items'] = $this->getKitProducts($kit_info['kit_id'], $get_kit_products_settings);
            
            $widget['kit_info']['kit_enable_status'] = $this->getKitEnableStatus_step2($widget['kit_info'], $widget['kit_info']['kit_items'], $widget['main_product_id']);
            if ($widget['kit_info']['kit_enable_status']['display_kit']){
                $enable = true;
            }

            
            if(empty($main_product_id)){
                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id IS NULL");
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_enable_status SET kit_id = '" . (int)$kit_id . "', enable = '" . (int)$enable . "'");
            }else{
                $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_kit_enable_status WHERE kit_id = '" . (int)$kit_id . "' AND main_product_id = '" . (int)$main_product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_cache_kit_enable_status SET kit_id = '" . (int)$kit_id . "', main_product_id = '" . (int)$main_product_id . "', enable = '" . (int)$enable . "'");
            }
        }


        return $enable;
    }
    

    public function isGroupsInstalled(){
        $installed = false;

        $groups_extension_file = DIR_APPLICATION . 'controller/catalog/bundle_expert_product_group.php';
        if (file_exists($groups_extension_file)) {
            $installed = true;
        }

        return $installed;
    }

    public function dbCacheAllKitsUpdate(){
        $this->load->model('setting/setting');


        $this->cache->delete('product.bundle_expert');
        $this->cache->delete('product.bundle_expert');
        $this->bundle_expert->dbCacheClear();
        $this->bundle_expert->deleteMinifyJsCss();

        if (!isset($this->session->data['currency'])) {
            $this->bundle_expert->initCurrencieSession();
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert ";

        $query = $this->db->query($sql);

        $results = $query->rows;

        foreach ($results as $result) {

            $this->bundle_expert->updateKitCache($result['kit_id']);

            
            $this->bundle_expert->updateProductAsKitPricesProcess($result);
            

















        }

        return count($results);


    }

    
    
    public function getTaxesDataForProductAsKit($product){
        $tax_rates_result = null;

        if(isset($product['product_as_kit_flag']) && $product['product_as_kit_flag']){
            switch ($this->bundle_expert_settings['product_as_kit_tax_mode']){
                case "by_main":

                    break;
                case "by_items":
                    foreach ($product['option'] as $option){
                        if(isset($option['product_as_kit_product_id'])){
                            $product_info = $this->getProductInfo($option['product_as_kit_product_id']);
                            if($product_info){
                                $tax_rates = $this->tax->getRates($option['price'], $product_info['tax_class_id']);

                                foreach ($tax_rates as $tax_rate) {
                                    $tax_rates_result[] = $tax_rate;
                                }

                            }
                        }
                    }
                    break;
            }

        }

        return $tax_rates_result;
    }
    
    
    


































    public function getCustomerGroups() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cg.sort_order ASC, cgd.name ASC");

        return $query->rows;
    }

    
    public function editProductInfoForModel($product_id, $query) {
        if ($query->num_rows) {
            $cache_price = array();
            



                if ($this->bundle_expert->bundle_expert_settings['product_as_kit_use_price_cache_table']) {
                    $cache_price = $this->getProductAsKitPriceFromCache($product_id);
                    if (!empty($cache_price)) {
                        $query->row['price'] = $cache_price['price_value'];
                        $query->row['special'] = $cache_price['special_value'];
                        if ($query->row['special'] == 0) {
                            $query->row['special'] = false;
                            $query->row['special'] = null;
                        }
                    }

                }









                $main_product_cache_data = $this->bundle_expert->getProductAsKitMainProductDataFromCache($product_id);
                if ($main_product_cache_data) {
                    if ($main_product_cache_data['stock_in_status']) {
                        $query->row['quantity'] = $main_product_cache_data['quantity'];
                    } else {
                        $query->row['quantity'] = 0;
                    }


                }
                $main_product_cache_data = $this->bundle_expert->getProductAsKitMainProductDataFromCache_LE($product_id);
                if ($main_product_cache_data) {
                    if ($main_product_cache_data['stock_in_status']) {
                        $query->row['quantity'] = $main_product_cache_data['quantity'];
                    } else {
                        $query->row['quantity'] = 0;
                    }

                }

                if($this->checkProductIsKit($product_id) && !$this->checkProductIsKit($product_id, true)){
                    $query->row['quantity'] = 0;
                    $query->row['price'] = 0;
                    $query->row['special'] = 0;
                };

        }

        return $query;
    }

    public function updateProductAsKitPricesProcess($kit_info) {
        
        $kit_id = $kit_info['kit_id'];
        if ($this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db']) {
            
            if($kit_info['status']){
                $current_customer_group = $this->config->get('config_customer_group_id');
                $customer_groups = $this->bundle_expert->getCustomerGroups();
                foreach ($customer_groups as $customer_group) {
                    $this->bundle_expert->product_as_kit_dinamic_price_cache = array();
                    $this->bundle_expert->kit_products_cache = array();
                    $this->config->set('config_customer_group_id', $customer_group['customer_group_id']);

                    $this->bundle_expert->updateKitAsProductMainProductPrice($kit_id);
                    $this->bundle_expert->updateKitAsProductMainProductDataCache($kit_id);
                    $this->bundle_expert->updateCollectionMainProductPrice($kit_id);
                }
                $this->config->set('config_customer_group_id', $current_customer_group);
            }

        }
        if ($this->bundle_expert->bundle_expert_settings['dynamic_update_product_as_kit_price_in_db_le']) {
            
            if($kit_info['status']) {
                $current_customer_group = $this->config->get('config_customer_group_id');
                $customer_groups = $this->bundle_expert->getCustomerGroups();
                foreach ($customer_groups as $customer_group) {
                    $this->bundle_expert->product_as_kit_dinamic_price_cache = array();
                    $this->bundle_expert->kit_products_cache = array();
                    $this->config->set('config_customer_group_id', $customer_group['customer_group_id']);


                    $this->bundle_expert->updateKitAsProductMainProductPrice_LE($kit_id);
                    $this->bundle_expert->updateKitAsProductMainProductDataCache_LE($kit_id);

                }
                $this->config->set('config_customer_group_id', $current_customer_group);
            }
        }

        
        
        if ($this->bundle_expert->bundle_expert_settings['product_as_kit_use_price_cache_table']) {
            if ($kit_info['kit_as_product']) {
                if($kit_info['status']) {
                    $current_customer_group = $this->config->get('config_customer_group_id');
                    $customer_groups = $this->bundle_expert->getCustomerGroups();
                    foreach ($customer_groups as $customer_group) {
                        $this->bundle_expert->product_as_kit_dinamic_price_cache = array();
                        $this->bundle_expert->kit_products_cache = array();
                        $this->config->set('config_customer_group_id', $customer_group['customer_group_id']);
                        $this->updateKitAsProductMainProductPriceCache($kit_id);
                    }
                    $this->config->set('config_customer_group_id', $current_customer_group);
                }else{
                    $this->clearKitAsProductMainProductPriceCache($kit_id);
                }
            }
        }
        

        
        if($kit_info['kit_as_product_light_mode']){
            if($kit_info['status']) {
                $this->updateKitAsProductMainProductPriceCache_LE($kit_id);
            }else{
                $this->clearKitAsProductMainProductPriceCache_LE($kit_id);
            }
        }
    }

    

    
    public function getProductMainCategory($product_id){


        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_to_category` LIKE 'main_category'");
        if($query->num_rows!=0) {
            $sql = "SELECT * FROM " . DB_PREFIX . "product_to_category ptc LEFT JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (ptc.category_id = c2s.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (ptc.category_id = c.category_id) WHERE ptc.product_id = '" . (int)$product_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1' AND ptc.main_category = '1' LIMIT 1";

            $query = $this->db->query($sql);





            return $query->row;

        }else{
            return array();
        }


    }

    public function getProductBottomCategory($product_id){


        $sql = "SELECT * FROM " . DB_PREFIX . "product_to_category ptc LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = ptc.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (ptc.category_id = c2s.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (ptc.category_id = c.category_id) WHERE ptc.product_id = '" . (int)$product_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1' ORDER BY cp.level DESC LIMIT 1";

        $query = $this->db->query($sql);





        return $query->row;
    }
    public function getProductTopCategory($product_id){


        $sql = "SELECT * FROM " . DB_PREFIX . "product_to_category ptc LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = ptc.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (ptc.category_id = c2s.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (ptc.category_id = c.category_id) WHERE ptc.product_id = '" . (int)$product_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1' ORDER BY cp.level ASC LIMIT 1";


        $query = $this->db->query($sql);





        return $query->row;
    }
    
}