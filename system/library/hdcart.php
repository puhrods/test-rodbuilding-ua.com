<?php

class HdCart {
    private $c_accumulative_discounts = false;
    private $c_accumulative_editors = false;

    protected $products_cost;
    protected $products = array();
    protected $data_indexed;
    public $customer_group_id;
    public $customer_id;
    public $SecInDay  = 86400;
    public $DayInWeek = 7;
    private $hd_activate = false;
    private $global_type_discount = false;
    private $setting = [];

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        $this->tax = $registry->get('tax');
        $this->weight = $registry->get('weight');
        $this->currency = $registry->get('currency');
        $this->request = $registry->get('request');

        $this->customer_group_id = (int)$this->config->get('config_customer_group_id');
        $this->customer_id = (int)$this->customer->getId();
        
        if (isset($this->session->data['customer']) && isset($this->session->data['customer']['customer_group_id'])) {
            $this->customer_group_id = (int)$this->session->data['customer']['customer_group_id'];
        } else if ($this->customer->isLogged()) {
            $this->customer_group_id = (int)$this->customer->getGroupId();
        }
        
        // ordePro fix
        if (!empty($this->request->get['route']) && $this->request->get['route'] == 'checkout/recalculate') {
            if (!empty($this->request->post['customer_id'])) $this->customer_id = (int)$this->request->post['customer_id'];
            if (!empty($this->request->post['customer_group_id'])) $this->customer_group_id = (int)$this->request->post['customer_group_id'];
        }
        
        $result = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "hd_setting'");
        if ($result->num_rows == 0) {
            return;
        }

        $query = $this->db->query("SELECT `key`, `value` FROM `" . DB_PREFIX . "hd_setting`");
        foreach ($query->rows as $row) {
            $this->setting[$row['key']] = $row['value'];
        }
        
        $this->hd_activate = (int)$this->setting['status'];
        $this->global_type_discount = $this->setting['setting_variants'];
    }
    
    public function getCache() {
        if ($this->products) {
            return $this->products;
        }
        return false;
    }
  
    public function clearCache() {
        $this->products = false;
    }
    
    public function getProducts($products) {
        if ($this->products) {
            return $this->products;
        }
        
        $this->products = $products;
    
        unset($this->session->data['hyper_discount']);
        
        $this->setDataIndexed();

        if ($this->hd_activate && $products) {
            if ($this->global_type_discount == 'most_profitable_type') {
                $this->products = $this->mostProfitableType();
            } else if ($this->global_type_discount == 'most_profitable_for_products') {
                $this->products = $this->mostProfitableForProducts();
            }
        }

        if (!empty($this->request->get['hz'])) {
            $this->debug();
        }
        
        return $this->products;
    }

    public function mostProfitableForProducts() {
        $all_ds   = array();
        $products = $this->products;
        $users_ds = $this->getTotalUsers();
        $acc_ds   = $this->getTotalAccumulative();
        $quant_ds = $this->getTotalQuantitative();
        $kit_ds   = $this->getTotalKit();
        $subtotal = 0;
        if (isset($users_ds) && is_array($users_ds) && count($users_ds)) {
            $all_ds = array_merge($users_ds, $all_ds);
        }

        if (isset($acc_ds) && is_array($acc_ds) && count($acc_ds)) {
            $all_ds = array_merge($acc_ds, $all_ds);
        }

        if (isset($quant_ds) && is_array($quant_ds) && count($quant_ds)) {
            $all_ds = array_merge($quant_ds, $all_ds);
        }

        if (isset($kit_ds) && is_array($kit_ds) && count($kit_ds)) {
            $all_ds = array_merge($kit_ds, $all_ds);
        }
                        
        $best_profit = false; 
        $best_key   = false;
        foreach ($all_ds as $key => $d) {        
            if ($d['profit'] > $best_profit || $best_profit === false) {
                $best_profit = $d['profit'];
                $best_key = $key;
            }
        }        
            
        if ($best_key !== false) {
            $title = $all_ds[$best_key]['title'];
            
            foreach ($products as $key => $p) {
                if (!isset($all_ds[$best_key]['products'][$key])) continue;
                
                $products[$key]['hd_profit'] = $all_ds[$best_key]['products'][$key]['profit'];
                
                if ($all_ds[$best_key]['products'][$key]['profit'] > 0) {
                    if (isset($this->session->data['hyper_discount'][$title])) {
                        $this->session->data['hyper_discount'][$title] += $all_ds[$best_key]['products'][$key]['profit'];
                    } else {
                        $this->session->data['hyper_discount'][$title] = $all_ds[$best_key]['products'][$key]['profit'];
                    }
                }
            }
        }
        
        return $products;
    }

    public function mostProfitableType() {
        $all_ds   = array();
        $users_ds = $this->getTotalUsers();
        $acc_ds   = $this->getTotalAccumulative();
        $quant_ds = $this->getTotalQuantitative();
        $kit_ds   = $this->getTotalKit();
        $products = $this->products;
        if (isset($users_ds) && is_array($users_ds) && count($users_ds)) {
            $all_ds[] = $this->getSummDiscount($users_ds);
        }

        if (isset($acc_ds) && is_array($acc_ds) && count($acc_ds)) {
            $all_ds[] = $this->getSummDiscount($acc_ds);
        }

        if (isset($quant_ds) && is_array($quant_ds) && count($quant_ds)) {
            $all_ds[] = $this->getSummDiscount($quant_ds);
        }

        if (isset($kit_ds) && is_array($kit_ds) && count($kit_ds)) {
            $all_ds[] = $this->getSummDiscount($kit_ds);
        }

        if (count($all_ds)) {
            foreach ($all_ds as $key => $d) {
                $this->session->data['hyper_discount'][$all_ds[$key]['title']] = $all_ds[$key]['profit'];
                
                foreach ($products as $pkey => $p) {
                    if (!empty($product['orderpro'])) {
                        continue;
                    }

                    if (isset($d['products'][$pkey])) {
                        if (!isset($products[$pkey]['hd_profit'])) $products[$pkey]['hd_profit'] = 0;
                        $products[$pkey]['hd_profit'] += $d['products'][$pkey]['profit'];
                    }
                }
            }
        }

        return $products;
    }

    public function getSummDiscount($ds) {
        $total         = array();
        $best_products = array();
        if (count($ds) > 1) {
            foreach ($ds as $key => $d) {
                if (isset($d['products']) && is_array($d['products'])) {
                    foreach ($d['products'] as $key2 => $p) {
                        $p['dkey'] = $key;
                        if (isset($best_products[$key2])) {
                            if ($best_products[$key2]['final_total'] > $p['final_total']) {
                                $best_products[$key2] = $p;
                            }

                        } else {
                            $best_products[$key2] = $p;
                        }

                    }
                }

            }

            if (count($best_products) == 0) {
                return;
            }

            $ntotal_profit = 0;
            $ntotal        = 0;
            foreach ($this->products as $key => $p) {
                if (isset($best_products[$key])) {
                    $ntotal += $best_products[$key]['total'];
                }

            }

            foreach ($best_products as $p) {
                $ntotal_profit += $p['profit'];
            }

            $all_titles = array();
            foreach ($best_products as $p) {

                if ($p['profit']) {
                    $all_titles[] = $ds[$p['dkey']]['title'];
                }

            }

            $all_titles           = array_unique($all_titles);
            $total['title']       = implode(' / ', $all_titles);
            $total['products']    = $best_products;
            $total['total']       = $ntotal;
            $total['profit']      = $ntotal_profit;
            $total['final_total'] = $ntotal - $ntotal_profit;
            return $total;
        } else {
            $d = array_pop($ds);
            return $d;
        }
    }

    
    public function getAccumulativeInfo($customer_id = false) {
        if (!$customer_id) {
            $customer_id = $this->customer_id;
        }
        
        if (!$customer_id) {
            return false;
        }
        
        if ($this->c_accumulative_discounts === false) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_accumulative WHERE status = '1'");
            $this->c_accumulative_discounts = array();
            foreach ($query->rows as $discount) {
                $this->c_accumulative_discounts[$discount['id']] = $discount;
            }
        }
        
        if ($this->c_accumulative_editors === false) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_accumulative_discount_editor WHERE status = '1' ORDER BY id");        
            $this->c_accumulative_editors = array();
            foreach ($query->rows as $editor) {
                $this->c_accumulative_editors[$editor['discount_id']][$editor['editor_id']] = $editor;
            }
        }
        
        $discounts = &$this->c_accumulative_discounts;
        $editors = &$this->c_accumulative_editors;
            
        $language_id = $this->config->get('config_language_id');        
        $data = array();
        $current_cache = array();
                
        foreach ($discounts as $discount) {
            if ($this->checkShop($discount)) continue;
            if (!$this->checkCustomer($discount)) continue;
            
            $discount_id = $discount['id'];            
            if (!isset($editors[$discount_id])) continue;
            
            $discount_data = array();
            
            $names = json_decode($discount['name'], true);            
            $discount_data['info'] = array(
                'discount_id' => $discount_id,
                'name'        => isset($names[$language_id]) ? $names[$language_id] : '',
            );
            
            $order_statuses = json_decode($discount['order_statuses'], true);            
            if (!$order_statuses) continue;
            $order_statuses = array_map(function($value){return (int)$value;}, $order_statuses);
            
            $sql = "SELECT SUM(op.quantity) AS products, SUM(op.quantity * op.price) AS product_total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id IN (" . implode(',', $order_statuses) . ")";
            if ($discount['start_date']) {
                $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($discount['start_date']) . "'";
            }
            if ($discount['end_date']) {
                $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($discount['end_date']) . "' ";
            }            
            $hash = md5($sql);
            if (!empty($current_cache[$hash])) {
                $current_products = $current_cache[$hash];
            } else {
                $current_products = $this->db->query($sql)->row;
                $current_cache[$hash] = $current_products;
            }                 
            
            $sql = "SELECT SUM(o.total) AS total, COUNT(o.order_id) AS orders FROM `" . DB_PREFIX . "order` o WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id IN (" . implode(',', $order_statuses) . ")";
            if ($discount['start_date']) {
                $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($discount['start_date']) . "'";
            }
            if ($discount['end_date']) {
                $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($discount['end_date']) . "' ";
            }            
            $hash = md5($sql);
            if (!empty($current_cache[$hash])) {
                $current_orders = $current_cache[$hash];
            } else {
                $current_orders = $this->db->query($sql)->row;
                $current_cache[$hash] = $current_orders;
            }                 
            
            $discount_data['current'] = array(
                'orders'          => (int)$current_orders['orders'],
                'total'           => (float)$current_orders['total'],
                'products'        => (int)$current_products['products'],
                'product_total'   => (float)$current_products['product_total'],
            );
            
            $discount_data['discount'] = array();
            
            foreach ($editors[$discount_id] as $editor) {            
                if ($this->checkDate($editor)) continue;
                
                $discount_info = array(
                    'id'              => $editor['id'],
                    'value'           => $editor['discount_percent'],
                    'type'            => $editor['discount_type'],
                    'active'          => false,
                    'condition_type'  => $editor['discount_function'] == 'protect_all' ? 'all' : 'one',
                    'condition' => array(
                        'orders'    => $editor['discount_function_orders'],
                        'products'  => $editor['discount_function_products'],
                        'total'     => $editor['discount_function_sum'],
                    ),
                );
                
                $condition_count = 0;
                
                foreach ($discount_info['condition'] as $key => $value) {
                    if ($value == '') continue;
                    if ($discount_data['current'][$key] >= $value) {
                        $condition_count++;
                    }
                }
                
                if (($discount_info['condition_type'] != 'all' && $condition_count > 0) || $condition_count == count($discount_info['condition'])) {
                    $discount_info['active'] = true;
                }

                $discount_data['discount'][] = $discount_info;
            }
            
            $data[] = $discount_data;
        }
        
        return $data;
    }
    
    protected function getTotalAccumulative() {
        $discounts = $this->getAccumulativeInfo();
        
        if (!$discounts) return array();
        
        $data = array();
      
        foreach ($discounts as $discount) {
            $discount_id = $discount['info']['discount_id'];
            $discount_settings = &$this->c_accumulative_discounts[$discount_id];
            
            foreach ($discount['discount'] as $discount_info) {
                if (!$discount_info['active']) {
                    continue;
                }
            
                $editor_settings = &$this->c_accumulative_editors[$discount_id][$discount_info['id']];
        
                if (!$discount_settings['products_all']) {
                    $discount_products = json_decode($discount_settings['products'], true);
                } else {
                    $discount_products = array();
                }

                $disc_prd_price = array();
                
                foreach ($this->products as $key => $product) {
                    $profit = false;
                    
                    if ($discount_settings['products_all']) {
                        $profit = true;
                    } else if ($discount_products) {
                        $profit = in_array($product['product_id'], $discount_products);
                    } else {
                        $profit = $this->filter($product, $discount_settings['products_filter_url']);
                    }
                    
                    if (!$profit) {
                        continue;
                    }
                    
                    if (isset($this->data_indexed[$key])) {
                        $disc_prd_price['discount_variant']           = $discount_settings['discount_variant_discount'];
                        $disc_prd_price['special_variant']            = $discount_settings['discount_variant_specials'];
                        $disc_prd_price['discount_percent']           = $discount_info['value'];
                        $disc_prd_price['discount_type']              = $discount_info['type'];
                        
                        $this->addProduct($this->data_indexed[$key], $disc_prd_price, $profit, $discount_settings['correction']);
                    }
                }

                if ($disc_prd_price) {
                    $discount_result = $this->setDiscountValue($disc_prd_price);
                    
                    if ($discount_result) {
                        $discount_result['title'] = $discount['info']['name'];
                        $data[] = $discount_result;
                    }
                }
            }
        }
        
        return $data;
    }

    
    protected function setDiscountValue($disc_prd_price) {
        $total_profit   = 0;
        $result         = array();
        $total          = 0;
        $total_qty      = 0;

        if (isset($disc_prd_price['products'])) {
            foreach ($disc_prd_price['products'] as $product_id => $value) {
                $total_qty += $value['product_quantity'];
            }
        }

        $dp = $disc_prd_price['discount_percent'];

        if (isset($disc_prd_price['products'])) {
            foreach ($disc_prd_price['products'] as $product_id => $value) {
                /*
                echo 'value'."\n";
                print_r($value);                 
                */
              
                if (!$value['profit']) {
                    continue;
                }

                $special = $value['special_price'] > 0; 
                $discount = $value['discount_price'] > 0;
                $dprofit = 0;

                if ($value['correction']) {
                    $price_for_discount = $value['nominal_product_price'];
                } else {
                    $price_for_discount = $value['current_product_price'];
                }
                
                if ($special) {
                    $base_product_price = $value['current_product_price'] * $value['nominal_product_price'] / $value['special_price'];
                } else if ($discount) {
                    $base_product_price = $value['current_product_price'] * $value['nominal_product_price'] / $value['discount_price'];
                } else {
                    $base_product_price = $value['current_product_price'];
                }

                $cart_price = $value['current_product_price'];
                
                if ($disc_prd_price['discount_type'] == '%') {
                    $my_discount = ($price_for_discount / 100) * $dp;                        
                } else if ($disc_prd_price['discount_type'] == 'full fix') {
                    $my_discount = $dp / $total_qty;
                } else {
                    $my_discount = $dp;
                }
                $dprofit = $my_discount;

                if ($special || $discount) {
                    $variant_key = $special ? 'special_variant' : 'discount_variant';
                    
                    switch ($disc_prd_price[$variant_key]) {                      
                        case 'protect_active':
                            $cart_discount = $base_product_price - $value['current_product_price'];
                            $my_discount -= $cart_discount;
                            if ($my_discount < 0) {
                                $my_discount = 0;
                            }
                            $dprofit = $my_discount;
                            break;
                        case 'protect_summary':
                            break;
                    }
                }
                
                if ($special && $disc_prd_price['special_variant'] == 'protect_ignore') {
                    $dprofit = 0;
                }

                if ($discount && $disc_prd_price['discount_variant'] == 'protect_ignore') {
                    $dprofit = 0;
                }

                $dprofit *= $value['profit_quantity'];
                $total_profit += $dprofit;
                $total += $cart_price * $value['product_quantity'];

                $result['products'][$product_id]['total']       = $cart_price * $value['product_quantity'];
                $result['products'][$product_id]['final_total'] = $cart_price * $value['product_quantity'] - $dprofit;
                $result['products'][$product_id]['profit']      = $dprofit;
                $result['products'][$product_id]['price']       = $cart_price;
            }
        }

        $result['total'] = $total;
        $result['profit'] = $total_profit;
        $result['final_total'] = $total - $total_profit;
        
        // print_r($result);
        
        return $result;
    }

    public function f1($op, $ap, $q)
    {
        return ($op - $ap) * $q;
    }

    public function getTotalQuantitative()
    {
        $discounts = array();
        $query     = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_quantitative WHERE `status` = '1';");
        if ($query->rows) {
            $query_ude = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_quantitative_discount_editor ");
            if ($query_ude->rows) {
                foreach ($query_ude->rows as $key => $editor_value) {
                    $discount_editorz[$editor_value['discount_id']][$editor_value['editor_id']] = $editor_value;
                }
            }

            $quantitative_discounts = $query->rows;
            foreach ($quantitative_discounts as $quantitative_discount) {
                $discount_id  = $quantitative_discount['id'];
                $guests       = $quantitative_discount['guests'];
                $customer_ids = $quantitative_discount['customers'] ? json_decode($quantitative_discount['customers'], true) : array();

                if ($this->checkShop($quantitative_discount)) {
                    continue;
                }

                if (!$this->checkCustomer($quantitative_discount)) {
                    continue;
                }

                $all_sum   = 0;
                $all_count = 0;
                foreach ($this->products as $product) {
                    $all_sum += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
                    $all_count += $product['quantity'];
                }

                if (isset($discount_editorz[$discount_id])) {
                    foreach ($discount_editorz[$discount_id] as $key => $dev) {
                        $discount_percent = $dev['discount_percent'];
                        if (($discount_percent != 0 && isset($dev['status']) && $dev['status'] == 1) == false) {
                            continue;
                        }

                        if ($this->checkDate($dev)) {
                            continue;
                        }

                        if ($dev['discount_function'] == 'protect_all') {
                            if (empty($dev['discount_count_products']) && empty($dev['discount_accumulation_sum'])) {
                                continue;
                            }

                            if (!empty($dev['discount_count_products']) && (int) $dev['discount_count_products'] > $all_count) {
                                continue;
                            }

                            if (!empty($dev['discount_accumulation_sum']) && (int) $dev['discount_accumulation_sum'] > $all_sum) {
                                continue;
                            }

                        } else
                        if ($dev['discount_function'] == 'protect_custom') {
                            $res = array();
                            if (!empty($dev['discount_count_products'])) {
                                $res[] = (int) $dev['discount_count_products'] <= $all_count;
                            }

                            if (!empty($dev['discount_accumulation_sum'])) {
                                $res[] = (int) $dev['discount_accumulation_sum'] <= $all_sum;
                            }

                            if (!$res || in_array(true, $res) === false) {
                                continue;
                            }

                        }

                        $disc_prd_price    = [];
                        $discount_type     = $dev['discount_type'];
                        $profit_all        = false;
                        $filter            = false;
                        $discount_products = array();
                        if ($dev['products_all'] == 1) {
                            $profit_all = true;
                        } else {
                            if (!empty($dev['products'])) {
                                $discount_products = json_decode($dev['products'], true);
                            }

                            if (!$discount_products && $dev['products_filter_url']) {
                                $filter = true;
                            }
                        }

                        $disc_prd_price['discount_variant']           = $dev['discount_variant_discount'];
                        $disc_prd_price['special_variant']            = $dev['discount_variant_specials'];
                        $disc_prd_price['discount_percent']           = $discount_percent;
                        $disc_prd_price['discount_type']              = $discount_type;
                            
                        $one_profit = false;

                        foreach ($this->products as $key2 => $product_val) {
                            $key    = (floatval(VERSION) > 2.0) ? $key2 : $product_val['key'];
                            $profit = false;
                            if ($profit_all) {
                                $profit = true;
                            } else
                            if ($discount_products) {
                                $profit = in_array($product_val['product_id'], $discount_products);
                            } else
                            if ($filter) {
                                $profit = $this->filter($product_val, $dev['products_filter_url']);
                            }
                            if ($profit && !$one_profit) {
                                $one_profit = true;
                            }

                            if (isset($this->data_indexed[$key])) {
                                $this->addProduct($this->data_indexed[$key], $disc_prd_price, $profit, $quantitative_discount['correction']);
                            }

                        }
                        if (!$one_profit) {
                            continue;
                        }

                        $discount = $this->setDiscountValue($disc_prd_price);
                        if ($discount) {
                            $discount['title'] = json_decode($quantitative_discount['name'], true)[$this->config->get('config_language_id')];

                            $discounts[] = $discount;
                        }
                    }
                }

            }
        }

        return $discounts;
    }

    public function getTotalKit()
    {
        $discounts = array();
        $query_kit = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_kit WHERE `status` = '1'");
        $query_ude = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_kit_discount_editor ");
        if ($query_kit->rows) {
            if ($query_ude->rows) {
                foreach ($query_ude->rows as $key => $editor_value) {
                    $discount_editorz[$editor_value['discount_id']][$editor_value['editor_id']] = $editor_value;
                }
            }

            $kit_discounts = $query_kit->rows;
            foreach ($kit_discounts as $kit_discount) {
                $discount_id  = $kit_discount['id'];
                $guests       = $kit_discount['guests'];
                $customer_ids = $kit_discount['customers'] ? json_decode($kit_discount['customers'], true) : array();

                if ($this->checkShop($kit_discount)) {
                    continue;
                }

                if (!$this->checkCustomer($kit_discount)) {
                    continue;
                }

                if (isset($discount_editorz[$discount_id])) {
                    foreach ($discount_editorz[$discount_id] as $key => $dev) {
                        $discount_percent = $dev['discount_percent'];
                        if (($discount_percent != 0 && isset($dev['status']) && $dev['status'] == 1) == false) {
                            continue;
                        }

                        if ($this->checkDate($dev)) {
                            continue;
                        }

                        $disc_prd_price     = [];
                        $discount_type      = $dev['discount_type'];
                        $profit_all         = false;
                        $filter             = false;
                        $filter2            = false;
                        $discount_products  = array();
                        $discount_products2 = array();
                        $check              = false;
                        $copy               = $dev['products_all'] == 1;
                        if (!empty($dev['products2'])) {
                            $discount_products2 = json_decode($dev['products2'], true);
                        }

                        if (!$discount_products2 && $dev['products_filter_url2']) {
                            $filter2 = true;
                        }

                        if (!empty($dev['products'])) {
                            $discount_products = json_decode($dev['products'], true);
                        }

                        if (!$discount_products && $dev['products_filter_url2']) {
                            $filter = true;
                        }

                        if ($copy) {
                            $discount_products          = $discount_products2;
                            $filter                     = $filter2;
                            $dev['products_filter_url'] = $dev['products_filter_url2'];
                            $dev['products']            = $dev['products2'];
                            // $dev['discount_count_products'] = $dev['discount_count_products2'];

                        }

                        $flag2 = true;
                        $flag  = true;

                        $type_qty  = $dev['type_qty'] == '1';
                        $need_qty  = (!empty($dev['discount_count_products2'])) ? (int) $dev['discount_count_products2'] : 1;
                        $need_qty2 = (!empty($dev['discount_count_products'])) ? (int) $dev['discount_count_products'] : 99999;

                        $all_p = (strpos($need_qty, '!') !== false);

                        $count_exist_pr = 0;
                        $need_count2    = 0;

                        foreach ($this->products as $key2 => $product_val) {
                            $this->products[$key2]['filter1'] = true;
                        }

                        foreach ($this->products as $key2 => $product_val) {
                            if ($discount_products2) {
                                $lflag = in_array($product_val['product_id'], $discount_products2);
                            } else
                            if ($filter2) {
                                $lflag = $this->filter($product_val, $dev['products_filter_url2']);
                            } else {
                                $lflag = false;
                            }

                            if ($lflag) {
                                if ($type_qty) {
                                    if ($need_qty <= (int) $product_val['quantity']) {
                                        $count_exist_pr += $product_val['quantity'];
                                    } else {
                                        $this->products[$key2]['filter1'] = false;
                                    }

                                } else {
                                    $count_exist_pr += $product_val['quantity'];
                                }
                            }
                            else
                                $this->products[$key2]['filter1'] = false;

                        }

                        $flag2 = $count_exist_pr >= $need_qty;

                        if (!$flag2) {
                            continue;
                        }

                        $disc_qty = (!empty($dev['discount_count_products'])) ? (int) $dev['discount_count_products'] : 1;

                        $one_profit = false;

                        foreach ($this->products as $key => $p) {
                            $sort_price[$key] = $p['price'];
                        }

                        asort($sort_price);

                        $disc_prd_price['discount_variant']           = $dev['discount_variant_discount'];
                        $disc_prd_price['special_variant']            = $dev['discount_variant_specials'];
                        $disc_prd_price['discount_percent']           = $discount_percent;
                        $disc_prd_price['discount_type']              = $discount_type;
                            
                        foreach ($sort_price as $key2 => $price) {

                            $product_val = $this->products[$key2];

                            $key    = (floatval(VERSION) > 2.0) ? $key2 : $product_val['key'];
                            $profit = false;

                            if ($discount_products) {
                                $profit = in_array($product_val['product_id'], $discount_products);
                            } else
                            if ($filter) {
                                $profit = $this->filter($product_val, $dev['products_filter_url']);
                            }

                            if ($copy) {
                                $profit = $profit && $this->products[$key2]['filter1'];
                            }

                            if ($profit && !$one_profit) {
                                $one_profit = true;
                            }

                            if (isset($this->data_indexed[$key])) {
                                $q = 0;
                                if ($profit) {
                                    if ($copy && $type_qty) {
                                        if ($need_qty <= $product_val['quantity'] &&
                                            $product_val['quantity'] <= $need_qty2) {
                                            $q = $product_val['quantity'];
                                        } else if ($product_val['quantity'] > $need_qty2) {
                                            $q = $need_qty2;
                                        }

                                    } else if ($disc_qty > 0) {
                                        if ($disc_qty >= (int) $product_val['quantity']) {
                                            $q = (int) $product_val['quantity'];
                                        } else {
                                            $q = $disc_qty;
                                        }
                                        $disc_qty -= $q;
                                    }
                                }


                                $this->addProduct($this->data_indexed[$key], $disc_prd_price, $profit, $kit_discount['correction'], $q);
                            }
                        }
                        if (!$one_profit) {
                            continue;
                        }

                        
                        $discount = $this->setDiscountValue($disc_prd_price);
                        if ($discount) {
                            $discount['title'] = json_decode($kit_discount['name'], true)[$this->config->get('config_language_id')];
                            $discounts[]       = $discount;
                        }
                    }
                }

            }
        }

        return $discounts;
    }

    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    public function filter($product, $filter)
    {
        parse_str($filter, $filter);
        foreach ($filter as $key => $val) {
            $filter[$key] = explode(',', $val);
        }

        if (isset($filter['filter_manufacturer'])) {
            $manufac = $this->getProductManufacturer($product['product_id']);
            if ($manufac) {
                if (in_array($manufac['manufacturer_id'], $filter['filter_manufacturer']) === false) {
                    return false;
                }

            } else {
                return false;
            }

        }

        if (isset($filter['filter_category'])) {
            $product_cats = $this->getProductCategories($product['product_id']);
            $cats         = array();
            if ($product_cats) {
                foreach ($product_cats as $cat) {
                    $cats[] = $cat['category_id'];
                }

                $childs = $this->getCategoriesParent($cats);
                foreach ($childs as $child) {
                    $cats[] = $child['path_id'];
                }

                $cats = array_unique($cats);
                $diff = array_diff($cats, $filter['filter_category']);
                
                if (count($diff) === count($cats)) {
                    return false;
                }

            } else {
                return false;
            }

        }

        return true;
    }

    public function getProductManufacturer($id)
    {
        $query = $this->db->query("SELECT `manufacturer_id` FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$id . "'");
        return $query->row;
    }

    public function getProductCategories($id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$id . "'");
        return $query->rows;
    }

    public function getCategoriesParent($ids = 0)
    {
        $path_ids = implode("','", $ids);
        $query    = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE `category_id` IN('$path_ids');");
        return $query->rows;
    }


    public function getDiscountPeriod($per)
    {
        switch ($per) {
            case '2_week':
                return 2;
                break;

            case '3_week':
                return 3;
                break;

            case '4_week':
                return 4;
                break;

            case '5_week':
                return 5;
                break;

            case '6_week':
                return 6;
                break;

            case '7_week':
                return 7;
                break;

            case '8_week':
                return 8;
                break;

            case '9_week':
                return 9;
                break;

            case '10_week':
                return 10;
                break;

            case '11_week':
                return 11;
                break;
        }
    }

    public function checkPeriod($dcp)
    {

        if (!isset($dcp['discount_period']) || $dcp['discount_period'] == null || empty($dcp['discount_period'])) {
            return true;
        }

        $time = time() - (int) $dcp['save_time'];
        $week = ($this->DayInWeek * $this->SecInDay);
        $per  = $this->getDiscountPeriod($dcp['discount_period']);
        $res  = (((int) ($time / $week)) % $per) == 0;

        return $res;
    }

    public function checkWeek($dcp)
    {

        if (!isset($dcp['discount_week']) || $dcp['discount_week'] == null || $dcp['discount_week'] == '[]') {
            return true;
        }

        $weeks = json_decode($dcp['discount_week'], true);
        if (!$weeks) {
            return false;
        }

        return (in_array($this->getDay(), $weeks) !== false);
    }

    public function checkTime($dcp)
    {
        if (!isset($dcp['discount_time']) || $dcp['discount_time'] == null || $dcp['discount_time'] == '[]') {
            return true;
        }

        $times = json_decode($dcp['discount_time'], true);
        if (!$times) {
            return false;
        }

        return (in_array($this->getTime(), $times) !== false);
    }

    public function checkDate($dev) {
        $day = 60 * 60 * 24;
        $sd  = $dev['start_date'];
        $ed  = $dev['end_date'];

        if (
            (strtotime($sd) < time() && strtotime($ed) + $day > time())
            ||
            ($sd == false && $ed == false)
            ||
            (strtotime($sd) < time() && $ed == false)
            ||
            (strtotime($ed) + $day > time() && $sd == false)
        ) {

            return !true;
        }

        return !false;
    }
    
    public function countOrderByCustomerId($cid) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE `customer_id` = '" . (int)$cid . "'");
        return $query->row['total'];
    }

    public function checkCustomer($disc) {
        if ($this->customer_id) {
            if (!empty($disc['customers_all'])) {
                return true;
            } else {
                $customers_ids = json_decode($disc['customers'], true);
                
                if ($customers_ids) {
                    if (in_array($this->customer_id, $customers_ids)) {
                        return true;
                    }
                } else {
                    if ($disc['customers_filter_url']) {
                        parse_str($disc['customers_filter_url'], $cf_url);
                        if (isset($cf_url['filter_customer_group_id']) && is_array($cf_url['filter_customer_group_id'])) {
                            if (in_array($this->customer_group_id, $cf_url['filter_customer_group_id']) !== false) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        
        if (!empty($disc['guests'])) {
            if (!$this->customer_id) {
                return true;
            } else {
                if ($this->countOrderByCustomerId($this->customer_id) == 0) {
                    return true;
                }
            }
        }
        
        return false;
    }

    public function checkShop($quantitative_discount)
    {
        $current_shop_id = $this->config->get('config_store_id');
        $shops_list      = $quantitative_discount['shops'] ? json_decode($quantitative_discount['shops'], true) : array();
        if (($quantitative_discount['shops_all'] == '1' || in_array($current_shop_id, $shops_list)) == false) {
            return true;
        }
        return false;
    }

    public function addProduct($iproduct, &$disc_prd_price, $profit, $correction = false, $profit_qunatity = false) {
        $pkey = $iproduct['key'];

        $t                          = &$disc_prd_price['products'][$pkey];
        $t['id']                    = $iproduct['id'];
        $t['profit']                = $profit;
        $t['correction']            = $correction;
        $t['discount_price']        = isset($this->products[$pkey]['hdp_dp']) && $this->products[$pkey]['hdp_dp'] > 0 ? $this->products[$pkey]['hdp_dp'] : false;
        $t['special_price']         = isset($this->products[$pkey]['hdp_sp']) && $this->products[$pkey]['hdp_sp'] > 0 ? $this->products[$pkey]['hdp_sp'] : false;
        $t['nominal_product_price'] = $iproduct['price'];
        $t['current_product_price'] = $this->products[$pkey]['price'];
        $t['product_quantity']      = $this->products[$pkey]['quantity'];
        if ($profit_qunatity === false) {
            $t['profit_quantity'] = $this->products[$pkey]['quantity'];
        } else {
            $t['profit_quantity'] = $profit_qunatity;
        }
        
    }

    public function setDataIndexed() {
        $this->data_indexed = array();
        $cart_products_ids  = array();
        foreach ($this->products as $p) {
            $cart_products_ids[] = (int)$p['product_id'];
        }

        if (count($cart_products_ids) == 0) {
            return;
        }

        $product_query = $this->db->query("SELECT product_id, price FROM " . DB_PREFIX . "product WHERE product_id IN (" . implode(',', $cart_products_ids) . ")");
        if ($product_query->rows) {
            foreach ($this->products as $tmp_key => $tmp_value) {
                foreach ($product_query->rows as $product_info) {
                    if ($tmp_value['product_id'] == $product_info['product_id']) {
                        if (floatval(VERSION) > 2.0) {
                            $this->data_indexed[$tmp_key]['key'] = $tmp_key;
                        } else {
                            $this->data_indexed[$tmp_key]['key'] = $tmp_value['key'];
                        }

                        $this->data_indexed[$tmp_key]['id']        = $product_info['product_id'];
                        $this->data_indexed[$tmp_key]['cart_id']   = $tmp_value['cart_id'];
                        $this->data_indexed[$tmp_key]['price']     = $product_info['price'];
                    }
                }
            }
        } else {
            return false;
        }
    }

    public function getTotalUsers() {
        $discounts       = array();
        $current_shop_id = $this->config->get('config_store_id');

        // Инициализация массива скидок $discount_data[group][proc]

        $query_users = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_users WHERE status = 1");
        if ($query_users->rows) {
            $user_discounts = $query_users->rows;
            $query_ude      = $this->db->query("SELECT * FROM " . DB_PREFIX . "hd_users_discount_editor WHERE status = 1");
            
            if ($query_ude->rows) {
                foreach ($query_ude->rows as $editor_value) {
                    $discount_editorz[$editor_value['discount_id']][$editor_value['editor_id']] = $editor_value;
                }
            }

            foreach ($user_discounts as $user_discount) {
                $discount_id       = (int) $user_discount['id'];
                $discount_settings = isset($user_discount['shops_all']) ? $user_discount['shops_all'] : false;
                $shops_list        = json_decode($user_discount['shops'], true);
                if ((isset($discount_settings) || in_array($current_shop_id, $shops_list)) == false) {
                    continue;
                }

                if (!$this->checkCustomer($user_discount)) {
                    continue;
                }

                if (isset($discount_editorz[$discount_id])) {
                    foreach ($discount_editorz[$discount_id] as $discount_editor) {
                        $discount_products = !empty($discount_editor['products']) ? json_decode($discount_editor['products'], true) : array();
                        $disc_prd_price    = false;
                        $discount_percent  = $discount_editor['discount_percent'];
                        if ($discount_percent <= 0) {
                            continue;
                        }

                        if ($this->checkDate($discount_editor)) {
                            continue;
                        }

                        $profit_all        = false;
                        $filter            = false;
                        $discount_products = array();

                        if ($discount_editor['products_all'] == 1) {
                            $profit_all = true;
                        } else {
                            if (!empty($discount_editor['products'])) {
                                $discount_products = json_decode($discount_editor['products'], true);
                            }

                            if (!$discount_products && $discount_editor['products_filter_url']) {
                                $filter = true;
                            }
                        }

                        $disc_prd_price['discount_variant']           = $discount_editor['discount_variant_discount'];
                        $disc_prd_price['special_variant']            = $discount_editor['discount_variant_specials'];
                        $disc_prd_price['discount_percent']           = $discount_editor['discount_percent'] ? (int)$discount_editor['discount_percent'] : 0;
                        $disc_prd_price['discount_type']              = $discount_editor['discount_type'];
                            
                        $one_profit = false;
                        foreach ($this->products as $key2 => $product_val) {
                            $profit = false;
                            
                            if ($profit_all) {
                                $profit = true;
                            } else if ($discount_products) {
                                $profit = in_array($product_val['product_id'], $discount_products);
                            } else if ($filter) {
                                $profit = $this->filter($product_val, $discount_editor['products_filter_url']);
                            }
                            
                            if ($profit && !$one_profit) {
                                $one_profit = true;
                            }

                            $key                                          = (floatval(VERSION) > 2.0) ? $key2 : $product_val['key'];
                            if (isset($this->data_indexed[$key])) {
                                $this->addProduct($this->data_indexed[$key], $disc_prd_price, $profit, $user_discount['correction']);
                            }

                        }
                        if (!$one_profit) {
                            continue;
                        }

                        $discount = $this->setDiscountValue($disc_prd_price);

                        if ($discount) {
                            $discount['title'] = json_decode($user_discount['name'], true)[$this->config->get('config_language_id')];
                            $discounts[]       = $discount;
                        }

                    }
                }

            }
        }

        // $this->debug($discounts);

        return $discounts;
    }
    
    
    public function getSqlSpecialDateEnd()
    {
        if (!empty($this->setting['special_counter_one_day'])) {
            return ', DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AS date_end';
        } else {
            return '';
        }
    }

    public function getSqlSpecialCart($tprefix = '')
    {
        $d          = $this->getDay();
        $hours      = $this->getTime();
        $secsPassed = "(UNIX_TIMESTAMP(NOW())-" . $tprefix . "save_time)";
        $fullDay    = "FLOOR($secsPassed/" . $this->SecInDay . ")";
        $case       = "(CASE " . $tprefix . "discount_period
        WHEN '2_week' THEN 1
        WHEN '3_week' THEN 2
        WHEN '4_week' THEN 3
        WHEN '5_week' THEN 4
        WHEN '6_week' THEN 5
        WHEN '7_week' THEN 6
        WHEN '8_week' THEN 7
        WHEN '9_week' THEN 8
        WHEN '10_week' THEN 9
        WHEN '11_week' THEN 10
        ELSE 0 END)";
        $period   = "($case*" . $this->DayInWeek . ")";
        $fullWeek = "FLOOR($fullDay/$period)";
        $total    = "$fullWeek%" . $tprefix . "discount_period";

        $ds = $tprefix . "date_start";
        $de = $tprefix . "date_end";
        $dp = $tprefix . "discount_period";
        $dw = $tprefix . "discount_week";
        $dt = $tprefix . "discount_time";

        $mysql = "
        (
            (($ds = '0000-00-00' OR $ds < NOW()) AND ($de = '0000-00-00' OR $de > NOW()))
            AND
            ($dp IS NULL OR $dp = '' OR $total=0)
        ) AND
        (
            (($dw IS NULL) OR ($dw = '') OR ($dw LIKE '[]') OR ($dw LIKE '%\"$d\"%'))
            AND
            (($dt IS NULL) OR ($dw = '') OR ($dt LIKE '[]') OR ($dt LIKE '%\"$hours\"%'))
        )";
        return $mysql;
    }

    public function getDay()
    {
        $dowMap = array(
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        );
        $dow_numeric = date('w');
        return $dowMap[$dow_numeric];
    }

    public function getTime()
    {
        $hours = date('G', time());
        $time  = array(
            'time_a',
            'time_b',
            'time_c',
            'time_d',
            'time_e',
            'time_f',
            'time_g',
            'time_h',
            'time_i',
            'time_j',
            'time_k',
            'time_l',
            'time_m',
            'time_n',
            'time_o',
            'time_p',
            'time_q',
            'time_r',
            'time_s',
            'time_t',
            'time_u',
            'time_v',
            'time_w',
            'time_x',
        );
        return $time[$hours];
    }
    
    private function debug($data = array()) {
        $all_ds   = array();
        $products = $this->products;
        $users_ds = $this->getTotalUsers();
        $acc_ds   = $this->getTotalAccumulative();
        $quant_ds = $this->getTotalQuantitative();
        $kit_ds   = $this->getTotalKit();
        $subtotal = 0;
        if (isset($users_ds) && is_array($users_ds) && count($users_ds)) {
            $all_ds = array_merge($users_ds, $all_ds);
        }

        if (isset($acc_ds) && is_array($acc_ds) && count($acc_ds)) {
            $all_ds = array_merge($acc_ds, $all_ds);
        }

        if (isset($quant_ds) && is_array($quant_ds) && count($quant_ds)) {
            $all_ds = array_merge($quant_ds, $all_ds);
        }

        if (isset($kit_ds) && is_array($kit_ds) && count($kit_ds)) {
            $all_ds = array_merge($kit_ds, $all_ds);
        }
        $data = $all_ds;
        
        header('Content-Type: text/html; charset=utf-8');
        $result = "<table>";
        $result .= "<thead>";

        // var_dump($data);
        $result .= "<tr>";
        $result .= "<td>Название</td>";
        $result .= "<td>Тотал</td>";
        $result .= "<td>Профит</td>";
        $result .= "<td>Финал тотал</td>";
        $result .= "<td>Товары</td>";
        $result .= "</tr>";

        $result .= "</thead>";
        foreach ($data as $d) {
            //var_dump($d);
            $result .= "<tr>";
            $result .= "<td>" . $d['title'] . "</td>";
            $result .= "<td>" . $d['total'] . "</td>";
            $result .= "<td>" . $d['profit'] . "</td>";
            $result .= "<td>" . $d['final_total'] . "</td>";
            $result .= "<td><table>";
            $result .= "<thead>";

            // var_dump($data);
            $result .= "<tr>";
            $result .= "<td>Цена</td>";
            $result .= "<td>Тотал</td>";
            $result .= "<td>Профит</td>";
            $result .= "<td>Финал тотал</td>";
            $result .= "</tr>";
            $result .= "</thead>";
            foreach ($d['products'] as $p) {
                $result .= "<tr>";
                $result .= "<td>" . $p['price'] . "</td>";
                $result .= "<td>" . $p['total'] . "</td>";
                $result .= "<td>" . $p['profit'] . "</td>";
                $result .= "<td>" . $p['final_total'] . "</td>";
                $result .= "</tr>";
            }
            $result .= "</table></td>";
            $result .= "</tr>";
        }
        $result .= "</table>
        <style>
            table { border-collapse: collapse; }
            td {
                padding: 5px 10px;
                border: 1px solid black;
                border-collapse: collapse;
            }
        </style>";
        echo $result;
        
        echo '<pre>' . var_export($this->products, true) . '</pre>';
        
        die();
    }
}
