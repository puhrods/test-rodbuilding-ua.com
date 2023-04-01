<?php

namespace OCFilter;

class Api extends Factory {
  public $view;
  
  private static $temp = [];
  
  public function version($int = false) {
    if ($int) {
      return (int)str_pad(substr(str_replace('.', '', OCF_VERSION), 0, 2), 2, '0');
    } else {
      return OCF_VERSION; 
    }       
  }
  
  public function startup() {
    // Start public API in view template
    $this->view = new Dynamic();
        
    $this->view->page = function($page_id) {
      if (isset(self::$temp['view.getPage.' . $page_id])) {
        return self::$temp['view.getPage.' . $page_id];
      }
      
      $page_info = $this->opencart->model_extension_module_ocfilter->getPage($page_id);
      
      if ($page_info) {
        $format_page = $this->seo->formatPage($page_info);
        
        $page_data = (object)$format_page;
      } else {
        $page_data = (object)[ 'href' => '', 'name' => '', 'selected' => '', 'params' => '' ];
      }
      
      self::$temp['view.getPage.' . $page_id] = $page_data;
      
      return $page_data;
    };
  }
  
  public function useSubCategory() {
    return $this->config('category_visibility') == 'parent';
  }
  
  public function onlyLastLevel() {
    return $this->config('category_visibility') == 'last_level';
  }  
  
  public function isSelected() {
    return (bool)$this->params->get();
  }
  
  public function getParamsArray() {
    return $this->params->get();    
  }  
  
  public function getParamsString() {
    return $this->seo->getParams();    
  }  

  public function getParamsIndex() {
    return $this->params->getIndex();    
  }    
  
  public function isSeoPage() {
    return (bool)$this->seo->getPageInfo();    
  }
  
  public function getSeoPage() {
    return $this->seo->getPageInfo();    
  }      
  
  public function getSeoPageLayoutId() {
    return $this->placement->getPageLayoutId();    
  }     
  
  public function setProductSQL($fn, &$sql = '') {
    if ($fn != 'getProducts' && $fn != 'getTotalProducts' && $fn != 'getProductSpecials') {
      return;
    }
        
    $sql_key = md5($sql);
    
    if (isset(self::$temp['product.sql.' . $sql_key])) {
      $sql = self::$temp['product.sql.' . $sql_key];

      return;
    }
    
    $search_product_join = "LEFT JOIN " . DB_PREFIX . "product_description pd";
    $search_product_where = "p.status = '1'";
    
    $product_sql = $this->opencart->model_extension_module_ocfilter->getProductSearchSQL($this->params->get(), true);
        
    if ($product_sql['join']) {
      $sql = str_replace($search_product_join, $product_sql['join'] . " " . $search_product_join, $sql);
    }

    if ($product_sql['where']) {
      $sql = str_replace($search_product_where, $product_sql['where'] . " AND " . $search_product_where, $sql);
    }    
    
    if ($this->config('only_instock') && !preg_match('/p\.quantity\s?\>\s?(\'0\'|0)/i', $sql)) {
      $sql = str_replace($search_product_where, "p.quantity > 0 AND " . $search_product_where, $sql);
    }   

    if ($this->config('use_kj_series')) {
      $sql = str_replace("LEFT JOIN " . DB_PREFIX . "kjseries_product_hidden kph ON (p.product_id = kph.pid) WHERE kph.pid IS NULL AND ", " WHERE ", $sql); 
    }   
    
    /* HPM serach by single products
    if ($this->config('use_hpmodel')) {
      $sql = str_replace("COUNT(DISTINCT IF(hpl.parent_id IS NOT NULL AND h2s.store_id IS NOT NULL AND hph.pid IS NOT NULL, hpl.parent_id, p.product_id)) AS total", "COUNT(DISTINCT p.product_id) AS total", $sql);
      $sql = str_replace("IF(hpl.parent_id IS NOT NULL AND h2s.store_id IS NOT NULL AND hph.pid IS NOT NULL, hpl.parent_id, p.product_id) AS product_id", "p.product_id", $sql);
      $sql = str_replace("GROUP BY IF(hpl.parent_id IS NOT NULL AND h2s.store_id IS NOT NULL AND hph.pid IS NOT NULL, hpl.parent_id, p.product_id)", "GROUP BY p.product_id", $sql);
      $sql = str_replace([
        "LEFT JOIN " . DB_PREFIX . "hpmodel_product_hidden hph ON (p.product_id = hph.pid)",
        "LEFT JOIN " . DB_PREFIX . "hpmodel_links hpl ON (p.product_id = hpl.product_id)",
        "LEFT JOIN " . DB_PREFIX . "hpmodel_to_store h2s ON (hpl.type_id = h2s.type_id AND h2s.store_id = '" . (int)$this->opencart->config->get('config_store_id') . "')",
        "AND IF(hpl.parent_id IS NOT NULL AND h2s.store_id IS NOT NULL AND hph.pid IS NOT NULL, hpl.parent_id, p.product_id) = p.product_id",        
      ], "", $sql);
    }
    */

    self::$temp['product.sql.' . $sql_key] = $sql;
  }  
  
  public function getTotalProductSpecials() {
    return $this->opencart->model_extension_module_ocfilter->getTotalProductSpecials($this->params->get());
  }
  
  public function setProductListControllerData(&$data, $product_total = null) {
    $this->opencart->load->language('extension/module/ocfilter');        
    
    $data['ocfilter_description_top'] = '';
    $data['ocfilter_description_bottom'] = '';
    
    $data['ocfilter_mobile_button'] = ($this->isEnabled() && !empty($this->config('mobile_button_position')) && ($this->config('mobile_button_position') == 'static' || $this->config('mobile_button_position') == 'both'));

    $mobile_button_text = $this->config('mobile_button_text');
    
    if ($mobile_button_text && isset($mobile_button_text[$this->opencart->config->get('config_language_id')])) {
      $data['button_ocfilter_mobile'] = $mobile_button_text[$this->opencart->config->get('config_language_id')];
    } else {
      $data['button_ocfilter_mobile'] = $this->opencart->language->get('button_ocfilter_mobile');
    }   

    // Meta
    $this->opencart->document->setTitle($this->seo->getPageMetaTitle($this->opencart->document->getTitle()));
    $this->opencart->document->setDescription($this->seo->getPageMetaDescription($this->opencart->document->getDescription()));
    $this->opencart->document->setKeywords($this->seo->getPageMetaKeywords($this->opencart->document->getKeywords()));      
    
    // Heading title
    if (!empty($data['real_heading_title'])) {
      $heading_title = $data['real_heading_title'];
    } else if (!empty($data['meta_h1'])) {
      $heading_title = $data['meta_h1'];
    } else if (!empty($data['seo_h1'])) {
      $heading_title = $data['seo_h1'];        
    } else if (!empty($data['heading_title'])) {
      $heading_title = $data['heading_title'];
    } else {
      $heading_title = '';
    }
    
    if (!$heading_title && !$this->placement->isCategory() && $this->opencart->language->get('heading_title') != 'heading_title') {
      $heading_title = $this->opencart->language->get('heading_title');
    }    
        
    if ($heading_title) {
      $data['heading_title'] = $data['meta_h1'] = $data['seo_h1'] = $data['real_heading_title'] = $this->seo->getPageHeadingTitle($heading_title);
    } 
    
    if ($this->params->get()) {
      // Description
      $data['ocfilter_description_top'] = $this->seo->getPageDescription('top');
      $data['ocfilter_description_bottom'] = $this->seo->getPageDescription('bottom');
      
      $data['description'] = $data['description_bottom'] = $data['description_top'] = $data['description_2'] = $data['ext_description'] = $data['description_dop'] = '';      
            
      // Breadcrumb   
      if ($this->config('category_breadcrumb')) {
        if (false !== ($breadcrumb = $this->seo->getPageBreadcrumb())) {
          $data['breadcrumbs'][] = $breadcrumb;
        }     
        
        $href = '';

        foreach ($data['breadcrumbs'] as $key => $breadcrumb) {
          if ($breadcrumb['href'] == $href) {
            unset($data['breadcrumbs'][$key]);
          }

          $href = $breadcrumb['href'];
        }
      }
        
      // Hide subcategories
      if ($this->config('hide_categories')) {
        $data['categories'] = [];
      }     

      if (isset($this->opencart->request->get['path']) && $this->isSeoPage()) {
        $this->opencart->document->ocfDeleteLink('canonical');
        $this->opencart->document->ocfDeleteLink('prev');
        $this->opencart->document->ocfDeleteLink('next');             
       
        if (isset($this->opencart->request->get['page'])) {
          $page = (int)$this->opencart->request->get['page'];
        } else {
          $page = 1;
        }
           
        if (isset($this->opencart->request->get['limit'])) {
          $limit = (int)$this->opencart->request->get['limit'];
        } else if ($this->opencart->version >= 30) {
          $limit = $this->opencart->config->get('theme_' . $this->opencart->config->get('config_theme') . '_product_limit');
        } else if ($this->opencart->version >= 22) {
          $limit = $this->opencart->config->get($this->opencart->config->get('config_theme') . '_product_limit');
        } else {
          $limit = $this->opencart->config->get('config_product_limit'); 
        }

        $this->opencart->document->addLink($this->opencart->url->link('product/category', 'path=' . $this->opencart->request->get['path'] . '&' . $this->params->getIndex() . '=' . $this->seo->getParams(), 'SSL'), 'canonical');
        
        if ($page > 2) {
          $this->opencart->document->addLink($this->opencart->url->link('product/category', 'path=' . $this->opencart->request->get['path'] . '&' . $this->params->getIndex() . '=' . $this->seo->getParams() . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
        }

        if (!is_null($product_total) && $limit && ceil($product_total / $limit) > $page) {
          $this->opencart->document->addLink($this->opencart->url->link('product/category', 'path=' . $this->opencart->request->get['path'] . '&' . $this->params->getIndex() . '=' . $this->seo->getParams() . '&page='. ($page + 1)), 'next');
        }        
      }
    }
    
    $data['ocfilter_pages'] = $this->seo->getCategoryPages();
  }
  
  public function setProductItemControllerData(&$data) {
    if (!isset($data['tags'])) {
      return;
    }
  
    $pages = $this->seo->getProductPages();

    if (!empty($data['attribute_groups'])) {
      foreach ($pages as $page_key => $page) {
        foreach ($page['params'] as $filter_key => $values) {          
          $this->params->key($filter_key)->expand($filter_id, $source);
          
          if (!$this->params->source($source)->is('attribute')) {
            continue;
          }
                                      
          foreach ($data['attribute_groups'] as $ag_key => $attribute_group) {
            foreach ($attribute_group['attribute'] as $a_key => $attribute) {
              if ($attribute['attribute_id'] == $filter_id) {
                if ($this->params->isRange($values[0])) {
                  $data['attribute_groups'][$ag_key]['attribute'][$a_key]['text'] = '<a href="' . $page['href'] . '" class="ocf-attribute-link">' . $attribute['text'] . '</a>';
                  
                  unset($pages[$page_key]);
                } else {
                  foreach ($values as $value_id) {
                    $value_name = $this->opencart->model_extension_module_ocfilter->getFilterValueName($filter_key, $value_id);
                    
                    if (preg_match('/' . preg_quote($value_name, '/') . '/iu', $attribute['text'], $match) && $match) {
                      $data['attribute_groups'][$ag_key]['attribute'][$a_key]['text'] = str_replace($match[0], '<a href="' . $page['href'] . '" class="ocf-attribute-link">' . $match[0] . '</a>', $data['attribute_groups'][$ag_key]['attribute'][$a_key]['text']);
                    } else {
                      $data['attribute_groups'][$ag_key]['attribute'][$a_key]['text'] = '<a href="' . $page['href'] . '" class="ocf-attribute-link">' . $attribute['text'] . '</a>';
                    }
                  }
                }
                                                  
                unset($pages[$page_key]);
                
                goto nextPage;
              } // if attribute founded by filter_id 
            } // foreach attributes
          } // foreach attribute_groups
        } // foreach page params
        
        nextPage: continue;
      } // foreach pages
    }
    
    foreach ($pages as $page) {
      $data['tags'][] = [
        'tag' => $page['name'],
        'href' => $page['href'],
      ];
    }  
    
    if ($this->config('product_breadcrumb') && false !== ($breadcrumb = $this->seo->getPageBreadcrumb())) {
      $product = array_pop($data['breadcrumbs']); 
      
      $data['breadcrumbs'][] = $breadcrumb;
      $data['breadcrumbs'][] = $product;
      
      $href = '';

      foreach ($data['breadcrumbs'] as $key => $crumb) {
        if ($crumb['href'] == $href) {
          unset($data['breadcrumbs'][$key]);
        }

        $href = $crumb['href'];
      }         
    }          
  }  
}

// https://www.php.net/manual/ru/reserved.classes.php#121622
class Dynamic extends \stdClass {
  public function __get($key) {
    return '';    
  }
  
  public function __call($key, $params) {
    if (!isset($this->{$key})) {
      throw new Exception("Call to undefined method " . __CLASS__ . "::" . $key . "()");
    }

    return $this->{$key}->__invoke(... $params);
  }
}