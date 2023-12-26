<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelCatalogBundleExpertWidget extends Model {
	public function addWidget($data) {


        
        $data['config_product_page']['selector'] = trim($data['config_product_page']['selector']);
        $data['config_category_page']['selector'] = trim($data['config_category_page']['selector']);
        $data['config_custom_page']['selector'] = trim($data['config_custom_page']['selector']);


        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget SET  name = ' '");

        $widget_id = $this->db->getLastId();

        $this->editWidget($widget_id, $data);

		$this->cache->delete('widget');



		return $widget_id;
	}

	public function editWidget($widget_id, $data) {


		
        $data['config_product_page']['selector'] = trim($data['config_product_page']['selector']);
        $data['config_category_page']['selector'] = trim($data['config_category_page']['selector']);
        $data['config_custom_page']['selector'] = trim($data['config_custom_page']['selector']);

        if($data['template']=='widget_template_5'){
            $data['checkbox_mode'] = 1;
        }else{
            if(strpos($data['template'], 'custom_template')===false ){
                $data['checkbox_mode'] = 0;
            }
        }



        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET  name = '" . $this->db->escape($data['name']) . "', display_mode = '" . $this->db->escape($data['display_mode']) . "', config_module = '" . $this->db->escape(json_encode($data['config_module'])) . "', config_product_page = '" . $this->db->escape(json_encode($data['config_product_page'])) . "', config_category_page = '" . $this->db->escape(json_encode($data['config_category_page'])) . "', config_custom_page = '" . $this->db->escape(json_encode($data['config_custom_page'])) . "', template = '" . $this->db->escape($data['template']) . "', table_mode_config = '" . $this->db->escape(json_encode($data['table_mode_config'])) . "', widget_width_mode = '" . $this->db->escape(json_encode($data['widget_width_mode'])) . "', set_image_size_mode = '" . $this->db->escape(json_encode($data['set_image_size_mode'])) . "', background_image_size = '" . $this->db->escape(json_encode($data['background_image_size'])) . "', status = '" . (int)($data['status']) . "', slider_mode = '" . (int)($data['slider_mode']) . "', slider_autoplay_status = '" . (int)($data['slider_autoplay_status']) . "', slider_autoplay_time = '" . (int)($data['slider_autoplay_time']) . "', checkbox_mode = '" . (int)($data['checkbox_mode']) . "', sort_order = '" . (int)($data['sort_order']) . "', product_click_mode = '" . $this->db->escape($data['product_click_mode']) . "', main_mode = '" . $this->db->escape($data['main_mode']) . "', product_from_kit_mode_items = '" . $this->db->escape($data['product_from_kit_mode_items']) . "', product_from_kit_mode_kits = '" . $this->db->escape($data['product_from_kit_mode_kits']) . "', product_from_kit_mode_product_source = '" . $this->db->escape($data['product_from_kit_mode_product_source']) . "' WHERE widget_id = '" . (int)$widget_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET link_products_combine_mode = '" . $this->db->escape($data['link_products_combine_mode']) . "' WHERE widget_id = '" . (int)$widget_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET html_element_action = '" . (int)($data['html_element_action']) . "' WHERE widget_id = '" . (int)$widget_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET html_element_action_selector = '" . $this->db->escape($data['html_element_action_selector']) . "' WHERE widget_id = '" . (int)$widget_id . "'");

        if (!isset($data['widget_kits'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE widget_id = '" . (int)$widget_id . "'");
        }

        if (isset($data['widget_kits']) && count($data['widget_kits']) < 100) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE widget_id = '" . (int)$widget_id . "'");

            if (isset($data['widget_kits'])) {
                foreach ($data['widget_kits'] as $widget_kit) {
                    if (!empty($widget_kit['kit_id']))
                        $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_kit SET widget_id = '" . (int)$widget_id . "', kit_id = '" . (int)($widget_kit['kit_id']) . "'");
                }
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_description WHERE widget_id = '" . (int)$widget_id . "'");

        foreach ($data['widget_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_description SET widget_id = '" . (int)$widget_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_store WHERE widget_id = '" . (int)$widget_id . "'");

        if (isset($data['widget_store'])) {
            foreach ($data['widget_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_to_store SET widget_id = '" . (int)$widget_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_product WHERE widget_id = '" . (int)$widget_id . "'");

        if (!empty($data['link_product'])) {
            foreach ($data['link_product'] as $value) {
                $item = $value;

                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_to_product SET widget_id = '" . (int)$widget_id . "', item_type = '" . $this->db->escape($item['item_type']) . "', item_id = '" . (int)$item['item_id'] . "', item_value = '" .  $this->db->escape($item['item_value']) . "'");
            }
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_category WHERE widget_id = '" . (int)$widget_id . "'");

        if (!empty($data['link_category'])) {
            foreach ($data['link_category'] as $value) {
                $item = $value;

                $this->db->query("INSERT INTO " . DB_PREFIX . "bundle_expert_widget_to_category SET widget_id = '" . (int)$widget_id . "', category_id = '" . (int)$item['category_id'] . "'");
            }
        }

        if (!empty($data['kits_per_category_page'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET kits_per_category_page = '" . (int)($data['kits_per_category_page']) . "' WHERE widget_id = '" . (int)$widget_id . "'");
        }

        if (!empty($data['link_category_filter_kits'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET link_category_filter_kits = '" . $this->db->escape($data['link_category_filter_kits']) . "' WHERE widget_id = '" . (int)$widget_id . "'");
        }

        
        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits WHERE widget_id = '" . (int)$widget_id . "'");

        if($data['display_mode']=="category_list"){
            if($data['link_category_filter_kits']=="selected"){
                $kits = $this->getWidgetKits($widget_id);
            }else{
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert WHERE import_template_mode = '0' AND status = '1'");

                $kits = $query->rows;
            }

            foreach ($kits as $kit){
                $this->bundle_expert->dbCacheUpdateCategoryKits($kit['kit_id']);
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_widget_link_products WHERE widget_id = '" . (int)$widget_id . "'");

        $link_product_list = $this->bundle_expert->getWidgetLinkProducts($widget_id);
        $this->bundle_expert->dbCacheWidgetLinkProductsAdd($widget_id, $link_product_list);

        if (version_compare(VERSION, '3.0.0.0', '<')) {
            $this->load->model('extension/module');
            $model_module = $this->model_extension_module;
        } else {
            $this->load->model('setting/module');
            $model_module = $this->model_setting_module;
        }


        
        if($data['display_mode']=='module' ||  $data['display_mode']=='module_in_category'){
            $module_data=array(
              'name' => $data['name'],
              'widget_id' => $widget_id,
              'display_mode' => $data['display_mode'],
              'status' => true,
            );
            if($data['config_module']['module_id']==-1){
                $model_module->addModule('bundle_expert', $module_data);
                
                $modules= $this->getModulesByCode('bundle_expert');
                $data['config_module']['module_id'] = $modules[0]['module_id'];
                $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET config_module = '" . $this->db->escape(json_encode($data['config_module'])) . "' WHERE widget_id = '" . (int)$widget_id . "'");
            }else{
                $model_module->editModule($data['config_module']['module_id'], $module_data);
            }
        }else{
            if($data['config_module']['module_id']!=-1) {
                $model_module->deleteModule($data['config_module']['module_id']);
                $data['config_module']['module_id'] = -1;
                $this->db->query("UPDATE " . DB_PREFIX . "bundle_expert_widget SET config_module = '" . $this->db->escape(json_encode($data['config_module'])) . "' WHERE widget_id = '" . (int)$widget_id . "'");
            }
        }

        $cache_key = $this->bundle_expert->getCacheKey(array('mode' => 'widget_edit', 'widget_id' => $widget_id));

		$this->cache->delete($cache_key);

	}

	public function deleteWidget($widget_id) {


		$widget_info = $this->getWidget($widget_id);

		if($widget_info['display_mode']=='module' || $widget_info['display_mode']=='module_in_category' ){
            if (version_compare(VERSION, '3.0.0.0', '<')) {
                $this->load->model('extension/module');
                $model_module = $this->model_extension_module;
            } else {
                $this->load->model('setting/module');
                $model_module = $this->model_setting_module;
            }
            $model_module->deleteModule($widget_info['config_module']['module_id']);
        }

		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_description WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_history_status WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_store WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_product WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_widget_link_products WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_cache_category_kits WHERE widget_id = '" . (int)$widget_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_expert_widget_to_category WHERE widget_id = '" . (int)$widget_id . "'");

		$this->cache->delete('widget');


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

	public function getWidgets($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "bundle_expert_widget";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
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

		return $query->rows;
	}

    public function getWidgetLinkProducts($widget_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_to_product WHERE widget_id = '" . (int)$widget_id . "' ORDER BY id");

        return $query->rows;
    }

    public function getWidgetLinkCategory($widget_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_to_category WHERE widget_id = '" . (int)$widget_id . "'");

        return $query->rows;
    }

    public function getTotalWidgets() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bundle_expert_widget");

        return $query->row['total'];
    }

    public function getTotalWidgetsByLayoutId($layout_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "widget_pro_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

        return $query->row['total'];
    }

	public function getWidgetStores($widget_id) {
		$widget_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_to_store WHERE widget_id = '" . (int)$widget_id . "'");

		foreach ($query->rows as $result) {
			$widget_store_data[] = $result['store_id'];
		}

		return $widget_store_data;
	}

	public function getWidgetKits($widget_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_kit WHERE widget_id = '" . (int)$widget_id . "'");

		return $query->rows;
	}

    public function getWidgetDescriptions($widget_id)
    {
        $widget_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_widget_description WHERE widget_id = '" . (int)$widget_id . "'");

        foreach ($query->rows as $result) {
            $widget_description_data[$result['language_id']] = array(
                'title' => $result['title'],
                'description' => $result['description'],
            );
        }

        return $widget_description_data;
    }

    public function getListDisplayMode() {

       $data = array(
           'product_page' => $this->language->get('text_config_product_page'),
           'category_page' => $this->language->get('text_config_category_page'),
           'category_list' => $this->language->get('text_config_category_list'),
           'module' => $this->language->get('text_config_module'),
           'module_in_category' => $this->language->get('text_config_module_in_category'),
           'custom_page' => $this->language->get('text_config_custom_page'),



       );

        return $data;
    }

    public function getListDisplayMethod() {

        $data = array(
            'block' => $this->language->get('text_config_block'),
            'popup' => $this->language->get('text_config_popup_window'),
        );

        return $data;
    }

    public function getListSpecificPageMode() {

        $data = array(
            'url' => $this->language->get('text_config_url'),
            

        );

        return $data;
    }

    public function getListDisplayTemplate() {











        $data = array(
            'widget_template_1' => array(
                'name'=>$this->language->get('text_config_widget_template_1'),
                'type'=>'kit',
            ),
            'widget_template_2' => array(
                'name'=>$this->language->get('text_config_widget_template_2'),
                'type'=>'kit',
            ),
            'widget_template_3' => array(
                'name'=>$this->language->get('text_config_widget_template_3'),
                'type'=>'kit',
            ),
            'widget_template_4' => array(
                'name'=>$this->language->get('text_config_widget_template_4'),
                'type'=>'series',
            ),
            'widget_template_5' => array(
                'name'=>$this->language->get('text_config_widget_template_5'),
                'type'=>'kit',
            ),
            'widget_template_6' => array(
                'name'=>$this->language->get('text_config_widget_template_6'),
                'type'=>'product_from_kit',
            ),


        );
        return $data;
    }

    public function getListSelectorMode() {

        $data = array(
            'before' => 'Before',
            'insert' => 'Insert',
            'replace' => 'Replace',
            'after' => 'After',
        );
        return $data;
    }

    public function getListPopupPosition() {

        $data = array(
            'top_left' => $this->language->get('text_config_top_left'),
            'top_center' => $this->language->get('text_config_top_center'),
            'top_right' => $this->language->get('text_config_top_right'),
            'center_left' => $this->language->get('text_config_center_left'),
            'center' => $this->language->get('text_config_center'),
            'center_right' => $this->language->get('text_config_center_right'),
            'bottom_left' => $this->language->get('text_config_bottom_left'),
            'bottom_center' => $this->language->get('text_config_bottom_center'),
            'bottom_right' => $this->language->get('text_config_bottom_right'),
        );

        return $data;
    }

    public function getListSelectorDefaultProductPage() {

        $template_config = new BundleExpertConfig($this->registry);

        $data = $template_config->getTemplateSelectors('opencart_default')['product_page']['positions'];

        return $data;
    }

    public function getListSelectorDefaultCategoryPage() {

        $template_config = new BundleExpertConfig($this->registry);

        $data = $template_config->getTemplateSelectors('opencart_default')['category_page']['positions'];

        return $data;
    }

    public function getListSelectorDefaultCartPage() {






    }

    public function getListSelectorDefaultCheckoutPage() {






    }

    public function getModulesByCode($code) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `module_id` DESC");

        return $query->rows;
    }

    public function editLayout($layout_id, $data) {


        $this->db->query("DELETE FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "'");

        if (isset($data['layout_module'])) {
            foreach ($data['layout_module'] as $layout_module) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape($layout_module['code']) . "', position = '" . $this->db->escape($layout_module['position']) . "', sort_order = '" . (int)$layout_module['sort_order'] . "'");
            }
        }


    }
}

class BundleExpertConfig {

    public function __construct($registry) {
        $this->language = $registry->get('language');

        $this->template_selectors = array(
            'opencart_default' => array(
                'template_title' => 'Opencart 2.0 default template',
                'product_page' =>array(
                    'positions' => array(
                        array(
                            'selector_title' =>  $this->language->get('text_position_product_page_before_tabs'),
                            'selector_value' => '.nav.nav-tabs',
                            'selector_mode' =>  'before',
                        ),
                    )
                ),
                'category_page' => array(
                    'positions' => array(
                        array(
                            'selector_title' => $this->language->get('text_position_category_page_between'),
                            'selector_value' => '.product-layout, .product-grid',
                            'selector_mode' => 'after',
                        ),
                    ),

                )
            ),
            'fastfood' => array(
                'template_title' => 'fastfood template',
                'product_page' =>array(
                    'positions' => array(
                        array(
                            'selector_title' =>  $this->language->get('text_position_product_page_before_tabs'),
                            'selector_value' => '.product-info',
                            'selector_mode' =>  'after',
                        ),
                    )
                ),
                'category_page' => array(
                    'positions' => array(
                        array(
                            'selector_title' => $this->language->get('text_position_category_page_between'),
                            'selector_value' => '.product-layout, .product-grid',
                            'selector_mode' => 'after',
                        ),
                    ),

                )
            )
        );

    }

    public function getTemplateSelectors($template_code){
        if(isset($this->template_selectors[$template_code]))
            return $this->template_selectors[$template_code];
        else
            return $this->template_selectors['opencart_default'];
    }


}
