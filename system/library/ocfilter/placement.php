<?php

namespace OCFilter;

class Placement extends Factory {
  private $layout_id = null;

  private $is_custom_page = null;
  private $is_module_in_layout = null;
  private $custom_layout_id = null;
  private $custom_filters = array();

  public function isManufacturer() {
    return ($this->seo->getRoute() == 'product/manufacturer/info' && $this->seo->getManufacturerId() > 0 && $this->isModuleInLayout());
  }

  public function isSearch() {
    return ($this->seo->getRoute() == 'product/search' && ($this->seo->getSearchKeyword() || $this->seo->getParams()) && $this->isModuleInLayout());
  }

  public function isCategory() {
    return ($this->seo->getCategoryId() && $this->isModuleInLayout());
  }
  
  public function isProduct() {
    return (bool)$this->seo->getProductId();    
  }

  public function isLastChildCategory() {
    $query = $this->opencart->db->query("SELECT c.category_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$this->seo->getCategoryId() . "' AND c2s.store_id = '" . (int)$this->opencart->config->get('config_store_id') . "' LIMIT 1");
    
    return (bool)!$query->num_rows;
  }

  public function isSpecial() {
    return ($this->seo->getRoute() == 'product/special' && $this->isModuleInLayout());
  }

  public function isCustomPage() {
    if (!is_null($this->is_custom_page)) {
    	 return $this->is_custom_page;
    }

    $this->is_custom_page = false;

    $placements = $this->config('placement_layout');

    if ($placements) {
      $layout_id = $this->getLayoutId();

      foreach ($placements as $placement) {
        if ($layout_id == $placement['layout_id'] && !empty($placement['filters']) && is_array($placement['filters'])) {
        	 $this->custom_filters = $placement['filters'];
          $this->custom_layout_id = $layout_id;

          $this->is_custom_page = true;
        }
      }
    }

    return $this->is_custom_page;
  }

  public function getPlace() {
    if ($this->isSpecial()) {
    	 return 'special';
    } else if ($this->isManufacturer()) {
    	 return 'manufacturer';
    } else if ($this->isSearch()) {
    	 return 'search';
    } else if ($this->isCustomPage()) {
    	 return 'custom';
    } else if ($this->isCategory()) {
    	 return 'category';
    }

    return '';
  }
  
  public function getPlaceSign($data = []) {
    $sign = $this->getPlace();
    
    if (isset($data['filter_category_id'])) {
      $sign .= '.' . (int)$data['filter_category_id'];
    } else if (isset($data['filter_manufacturer_id'])) {
      $sign .= '.' . (int)$data['filter_manufacturer_id'];
    } else {
      $sign .= '.0';
    }
    
    return $sign;
  }

  public function getCustomPageLayoutId() {
    return $this->custom_layout_id;
  }
  
  public function getCustomPageRoute() {
    if (isset($this->opencart->request->get['ocf_custom_route'])) {
      return $this->opencart->request->get['ocf_custom_route'];
    } else {
      return $this->seo->getRoute();
    }    
  }  
  
  public function getCustomPageFilters() {
    return $this->custom_filters;
  }
  
  public function isModuleInLayout() {
    if (!is_null($this->is_module_in_layout)) {
    	 return $this->is_module_in_layout;
    }
    
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$this->getLayoutId() . "' AND `code` = 'ocfilter'");

    $this->is_module_in_layout = (bool)$query->num_rows;

    return $this->is_module_in_layout;
  }

	public function getLayoutId() {
    if (!is_null($this->layout_id)) {
      return $this->layout_id;
    }
    
    if (isset($this->opencart->request->get['ocf_layout_id'])) {
      $this->layout_id = (int)$this->opencart->request->get['ocf_layout_id'];
      
      return $this->layout_id;
    }
      
		$this->opencart->load->model('design/layout');

		if ($this->seo->getRoute()) {
			$route = $this->seo->getRoute();
    } else if (isset($this->opencart->request->get['route'])) {
			$route = (string)$this->opencart->request->get['route'];
    } else {
			$route = 'common/home';
		}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->opencart->request->get['path'])) {
			$this->opencart->load->model('catalog/category');

			$path = explode('_', (string)$this->opencart->request->get['path']);

			$layout_id = $this->opencart->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->opencart->request->get['product_id'])) {
			$this->opencart->load->model('catalog/product');

			$layout_id = $this->opencart->model_catalog_product->getProductLayoutId($this->opencart->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->opencart->request->get['information_id'])) {
			$this->opencart->load->model('catalog/information');

			$layout_id = $this->opencart->model_catalog_information->getInformationLayoutId($this->opencart->request->get['information_id']);
		}
    
		if ($route == 'product/category' && isset($this->opencart->request->get['ocfilter_page_id'])) {
			$layout_id = $this->getPageLayoutId();
		}     

		if (!$layout_id) {
			$layout_id = $this->opencart->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->opencart->config->get('config_layout_id');
		}

    $this->layout_id = $layout_id;

    return $this->layout_id;
	}
  
  public function getPageLayoutId() {
    $layout_id = 0;
    
		if (isset($this->opencart->request->get['ocfilter_page_id'])) {
			$this->opencart->load->model('extension/module/ocfilter');

			$layout_id = $this->opencart->model_extension_module_ocfilter->getPageLayoutId($this->opencart->request->get['ocfilter_page_id']);
		}     
    
    return $layout_id;
  }
}