<?php

namespace OCFilter;

class Seo extends Factory {
  const CURRENCY_PATTERN = '/\{c:(\d+)\|(.+?)\}/s';
  
  private $disable_route = [
    'api/',   'checkout/', 'ebay/',
    'event/', 'startup/',  'tool/',
  ];

  private $seo_url_enabled = null;

  private $route;
  private $path;
  private $category_id;
  private $product_id;
  private $manufacturer_id;
  private $search;

  private $filter_params = '';

  private $filter_title = null;
  private $page_id = 0;
  private $page_info = [];
  private $rewrite_cache = [];

  public function startup() {               
    // Set Search Keyword
    if (isset($this->opencart->request->get['search'])) {
      $this->search = html_entity_decode($this->opencart->request->get['search'], ENT_QUOTES, 'UTF-8');
    }

    // Set OCFilter Selected Params
    if (isset($this->opencart->request->get[$this->params->getIndex()])) {
      $this->filter_params = $this->opencart->request->get[$this->params->getIndex()];
    }

    // Set Category Path
    if (isset($this->opencart->request->get['path'])) {
      $this->path = $this->opencart->request->get['path'];
    } else if (isset($this->opencart->request->get['ocf_path'])) {
      $this->path = $this->opencart->request->get['ocf_path'];
    }

    // Set Category Id
    if ($this->path) {
      $parts = array_filter(explode('_', $this->path), 'strlen');

      $this->category_id = (int)end($parts);
    } else if (isset($this->opencart->request->get['filter_category_id'])) {
      $this->category_id = (int)$this->opencart->request->get['filter_category_id'];
    } else if (isset($this->opencart->request->get['category_id'])) {
      $this->category_id = (int)$this->opencart->request->get['category_id'];
    }
    
    if (isset($this->opencart->request->get['product_id'])) {
      $this->product_id = $this->opencart->request->get['product_id'];
    }

    // Set Manufacturer
    if (isset($this->opencart->request->get['manufacturer_id']) && $this->opencart->request->get['manufacturer_id'] > 0) {
      $this->manufacturer_id = $this->opencart->request->get['manufacturer_id'];
    } else if (isset($this->opencart->request->get['ocf_manufacturer_id']) && $this->opencart->request->get['ocf_manufacturer_id'] > 0) {
      $this->manufacturer_id = $this->opencart->request->get['ocf_manufacturer_id'];
    }

    // Set Route
    if (isset($this->opencart->request->get['ocf_route'])) {
      $this->route = $this->opencart->request->get['ocf_route'];
    } else {
      if (isset($this->opencart->request->get['route'])) {
        $this->route = $this->opencart->request->get['route'];
      } else if ($this->path) {
        $this->route = 'product/category';
      } else if ($this->manufacturer_id) {
        $this->route = 'product/manufacturer/info';
      } else if ($this->search) {
        $this->route = 'product/search';
      }
    }
    
    if (!$this->route) {
      $this->route = 'common/home';
    }

    // Set page and params
    if ($this->placement->isCategory()) {
      if (isset($this->opencart->request->get['ocfilter_page_id'])) {
        $this->page_id = $this->opencart->request->get['ocfilter_page_id'];
      }   

      if ($this->page_id && !$this->filter_params) {
        $this->page_info = $this->opencart->model_extension_module_ocfilter->getPage($this->page_id, $this->getCategoryId());

        if ($this->page_info) {
          $params = json_decode($this->page_info['params'], true);
          
          if (!empty($params)) {
            $this->filter_params = $this->params->encode($params);
          } else {
            $this->page_info = [];
          }            
        }
      } else if ($this->filter_params && !$this->page_id) {
        $this->page_info = $this->opencart->model_extension_module_ocfilter->getPageByParams($this->getCategoryId(), $this->params->decode($this->getParams()));      
      } else if (!is_null($this->manufacturer_id) && $this->manufacturer_id) {
        // Search by manufacturer
        $this->page_info = $this->opencart->model_extension_module_ocfilter->getPageByManufacturer($this->manufacturer_id, $this->getCategoryId());

        if ($this->page_info) {
          $params = json_decode($this->page_info['params'], true);
          
          $this->filter_params = $this->params->encode($params);
          
          if (isset($this->opencart->request->get['manufacturer_id'])) {
            unset($this->opencart->request->get['manufacturer_id']);
          }
        }
      }      

      if (!$this->page_info && isset($this->opencart->request->get['ocfilter_page_id'])) {
        unset($this->opencart->request->get['ocfilter_page_id']);  
      }
    }  
      
    $this->params->set($this->getParams());
        
    if ($this->isNoIndex()) {
      header('X-Robots-Tag: noindex, nofollow', true); 
    } 
  }
  
  public function isSecure() {
    $srv = $this->opencart->request->server;
    
    $secure = (isset($srv['HTTPS']) && (strtolower($srv['HTTPS']) == 'on' || (int)$srv['HTTPS'] > 0));
       
    if (!$secure) {
      $secure = (isset($srv['SERVER_PORT']) && $srv['SERVER_PORT'] == 443);
    }
    
    if (!$secure) {
      $secure = (isset($srv['HTTP_X_FORWARDED_PORT']) && $srv['HTTP_X_FORWARDED_PORT'] == 443);
    }    
    
    if (!$secure) {
      $secure = (isset($srv['HTTP_X_FORWARDED_PROTO']) && strtolower($srv['HTTP_X_FORWARDED_PROTO']) == 'https');
    }
    
    if (!$secure) {
      $secure = (substr(HTTPS_SERVER, 0, 5) == 'https');
    }
    
    return $secure;
  }  
  
  public function getHost() {
    $srv = $this->opencart->request->server;
    
    $host = '';
    
    if (!empty($srv['HTTP_HOST'])) {
      $host = $srv['HTTP_HOST'] . dirname($srv['PHP_SELF']);
    } else if (!empty($srv['SERVER_NAME'])) {
      $host = $srv['SERVER_NAME'] . dirname($srv['PHP_SELF']);
    }
    
    if ($host) {
      return ($this->isSecure() ? 'https://' : 'http://') . rtrim($host, '/.\\') . '/'; 
    } else {
      return $this->isSecure() ? HTTPS_SERVER : HTTP_SERVER;      
    }
  }                              
                                                                                                            
  public function isSeoUrlEnabled() { 
    $seo_url = true;
       
    if (isset($this->opencart->request->server['QUERY_STRING']) && (substr($this->opencart->request->server['QUERY_STRING'], 0, 8) != '_route_=')) {
      $seo_url = false;
    }

    if (!$seo_url && !empty($this->opencart->request->get['seo_url_enabled']) && $this->opencart->config->get('config_seo_url')) {
      $seo_url = true;
    }

    return $seo_url;                                                                             
  }
  
  public function isSeoProEnabled() {
    return ($this->opencart->config->get('config_seo_url_type') == 'seo_pro' || $this->opencart->config->get('config_seo_pro'));
  }
  
  public function getUrlSuffix($path) {
    $suffix = '';   
    
    if ($this->config('url_suffix')) {
      $suffix = $this->config('url_suffix');  
    } else if (false !== strpos($path, '.html')) {     
      $suffix = '.html';
    } else if ($this->isSeoProEnabled()) {
      if ($this->opencart->config->get('config_seo_url_postfix')) {
        $suffix = $this->opencart->config->get('config_seo_url_postfix');
      } else if ($this->opencart->config->get('config_page_postfix')) {
        $suffix = $this->opencart->config->get('config_page_postfix');
      } 
    } else if (substr($path, -1) == '/') {     
      $suffix = '/';       
    }

    return trim($suffix);
  }  

  public function isDisabledRoute() {
    if ($this->route) {
      if (false === strpos($this->route, '/') || in_array(substr($this->route, 0, strpos($this->route, '/')) . '/', $this->disable_route)) {
        return true;
      }
    }   

    return false;
  }
 
  public function isNoIndex() {
    return ($this->getParams() && !$this->getPageInfo());
  }

  public function getCategoryId() {
    return (int)$this->category_id;
  }
  
  public function getProductId() {
    return (int)$this->product_id;
  }  

  public function getManufacturerId() {
    return (int)$this->manufacturer_id;
  }

  public function getSearchKeyword() {
    return $this->search;
  }

  public function getPath() {
    return $this->path;
  }

  public function getParams() {
    return $this->filter_params;
  }

  public function getRoute() {
    return $this->route;
  }

  public function getPageInfo() {
    $page_info = $this->page_info;
    
    if ($page_info && !isset($page_info['formatted'])) {    
      foreach ($page_info as $k => $v) {        
        if (in_array($k, [ 'name', 'heading_title', 'meta_title', 'meta_keyword', 'meta_description', 'description_top', 'description_bottom' ])) {
          // Convert currency values          
          if (preg_match_all(self::CURRENCY_PATTERN, $v, $matches) && count($matches) == 3) {                       
            foreach (array_shift($matches) as $key => $search) {             
              $page_info[$k] = str_replace($search, $this->opencart->currency->convert($matches[0][$key], $matches[1][$key], $this->opencart->session->data['currency']), $page_info[$k]);
            }
          }

          // Add currency symbols
          $page_info[$k] = strtr($page_info[$k], [ 
            '{cb}' => $this->opencart->currency->getSymbolLeft($this->opencart->session->data['currency']), 
            '{ca}' => $this->opencart->currency->getSymbolRight($this->opencart->session->data['currency'])
          ]);
        }
      }
      
      $page_info['formatted'] = true;
      
      $this->page_info = $page_info;
    }
   
    return $page_info;
  }
    
  public function rewrite($link) {        
    $url_info = parse_url(str_replace('&amp;', '&', $link));

    if (!isset($url_info['query'])) {
      return $link;
    }
    
    $data = [];

    parse_str($url_info['query'], $data);

    if (!isset($data[$this->params->getIndex()]) && !isset($data['ocfilter_page_id'])) {
      return $link;
    }
    
    $cache_key = md5($link);
    
    if (isset($this->rewrite_cache[$cache_key])) {
      return $this->rewrite_cache[$cache_key];
    }
    
    // Startup from OCFilter\Core for page_info setting
    parent::startup();

    $page_info = [];

    // Get SEO Page by params
    if (isset($data[$this->params->getIndex()]) && $this->placement->isCategory()) {
      $page_info = $this->opencart->model_extension_module_ocfilter->getPageByParams($this->getCategoryId(), $this->params->decode($data[$this->params->getIndex()])); 
      
      if ($page_info) {
        if (!$page_info['keyword'] || !$this->isSeoUrlEnabled()) {
          $data['ocfilter_page_id'] = $page_info['page_id'];   
        }    
        
        unset($data[$this->params->getIndex()]); 
      }       
    } else if (isset($data['ocfilter_page_id']) && $this->page_info && $this->isSeoUrlEnabled()) {
      if ($this->page_info['language_id'] != $this->opencart->config->get('config_language_id')) {
        $this->page_info = $this->opencart->model_extension_module_ocfilter->getPage($data['ocfilter_page_id']); 
      }
      
      // For seo_pro validation
      $page_info = $this->page_info;
        
      if (trim($this->page_info['keyword'])) {
        unset($data['ocfilter_page_id']);  
      }      
    } else if (isset($data['ocfilter_page_id']) && !$this->page_info) {
      unset($data['ocfilter_page_id']);                          
    }           

    if (!$page_info) {
      $this->rewrite_cache[$cache_key] = $link;
      
      return $link;
    }

    // Create new SEO URL
    $url = $url_info['scheme'] . '://' . $url_info['host'];

    if (isset($url_info['port'])) {
      $url .= ':' . $url_info['port'];
    }
    
    if (isset($url_info['path'])) {
      if ($this->isSeoUrlEnabled()) {
        $url .= str_replace('/index.php', '', $url_info['path']);
      } else {
        $url .= $url_info['path'];
      }      
    }
        
    if ($this->isSeoUrlEnabled()) {
      // Set URL keyword and suffix
      $url = rtrim($url, '/') . '/' . $page_info['keyword'] . $this->getUrlSuffix($url);       
    } 

    $query = '';

    if ($data) {
      foreach ($data as $key => $value) {
        $query .= '&' . rawurlencode((string)$key) . '=' . (is_array($value) ? http_build_query($value) : (string)$value);
      }

      if ($query) {
        $query = '?' . str_replace('&', '&amp;', trim($query, '&'));
      }
    }

    $url .= $query;

    $this->rewrite_cache[$cache_key] = $url;

    return $url;
  }

  public function link($params = '') {
    $url = '';

    if ($this->path) {
      $url .= '&path=' . (string)$this->path;
    } else if (isset($this->opencart->request->get['filter_category_id'])) {
      $url .= '&filter_category_id=' . (int)$this->opencart->request->get['filter_category_id'];
    } else if (isset($this->opencart->request->get['category_id'])) {
      $url .= '&category_id=' . (int)$this->opencart->request->get['category_id'];
    }

    if ($this->placement->isManufacturer()) {
      $url .= '&manufacturer_id=' . $this->manufacturer_id;
    }

    if ($this->placement->isSearch() && !$this->placement->isCustomPage()) {
      $url .= '&search=' . $this->getSearchKeyword();
    }

    if ($params) {
      $url .= '&' . $this->params->getIndex() . '=' . (string)$params;
    }

    if (isset($this->opencart->request->get['sort'])) {
      $url .= '&sort=' . (string)$this->opencart->request->get['sort'];
    }

    if (isset($this->opencart->request->get['order'])) {
      $url .= '&order=' . (string)$this->opencart->request->get['order'];
    }

    if (isset($this->opencart->request->get['limit'])) {
      $url .= '&limit=' . (int)$this->opencart->request->get['limit'];
    }

    if ($this->placement->isCustomPage()) {
      if ($params || !isset($this->opencart->request->get['ocf_custom_route'])) {
        $url .= '&ocf_layout_id=' . (int)$this->placement->getCustomPageLayoutId();
        $url .= '&ocf_custom_route=' . $this->placement->getCustomPageRoute();
        
        // Allow searching products in search.php controller
        $url .= '&tag=';  
             

        $link = $this->opencart->url->link('product/search', $url, 'SSL');        
      } else if (isset($this->opencart->request->get['ocf_custom_route'])) {
        $link = $this->opencart->url->link($this->opencart->request->get['ocf_custom_route'], '', 'SSL');   
      }
    } else if ($this->route) {
      $link = $this->opencart->url->link($this->route, $url, 'SSL');
    } else {
      $link = $this->opencart->url->link('product/category', $url, 'SSL');
    }
    
    return addslashes(str_replace('&amp;', '&', $link));
  }

  public function getPageBreadcrumb() {
    $name = $this->getSelectedsFilterTitle();
    
    $page_info = $this->getPageInfo();

    if ($page_info) {
      $name = $page_info['name'];
    } else if (!$name && $this->getProductId() && !empty($this->opencart->session->data['ocfilter_breadcrumb'])) {
      $breadcrumb = $this->opencart->session->data['ocfilter_breadcrumb'];

      $erase = (isset($breadcrumb['product_id']) && $breadcrumb['product_id'] != $this->getProductId());
  
      if (!$erase && isset($breadcrumb['category_id'])) {
        $query = $this->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$this->getProductId() . "' AND category_id = '" . (int)$breadcrumb['category_id'] . "'");
        
        $erase = !$query->num_rows;    
      }                        
      
      if ($erase) {
        unset($this->opencart->session->data['ocfilter_breadcrumb']);
        
        return false;
      }
      
      $this->opencart->session->data['ocfilter_breadcrumb']['product_id'] = $this->getProductId();
           
      return $breadcrumb;
    }

    if (!$name) {
      return false;
    }

    if (utf8_strlen($name) > 40) {
      $name = utf8_substr($name, 0, 40) . '..';
    }

    $name = $this->helper->utf8_ucfirst($name);
       
    return [
      'text' => $name,
      'href' => $this->link($this->getParams()),
    ];
  }
  
  public function setLastBreadcrumb() {
    if (!$this->config('product_breadcrumb')) {
      return;
    }
    
    if ($this->getParams()) {     
      $breadcrumb = $this->getPageBreadcrumb();
      
      if (!$this->getPageInfo() && $this->getCategoryId()) {
        $category_info = $this->opencart->model_catalog_category->getCategory($this->getCategoryId());
        
        if ($category_info) {
          $breadcrumb['text'] = $category_info['name'] . ' ' . $breadcrumb['text'];
        }
      }
           
      if ($this->getCategoryId()) {
        $breadcrumb['category_id'] = $this->getCategoryId();
      }      
      
      $this->opencart->session->data['ocfilter_breadcrumb'] = $breadcrumb;
    } else if (isset($this->opencart->session->data['ocfilter_breadcrumb'])) {
      unset($this->opencart->session->data['ocfilter_breadcrumb']);
    }   
  }    

  public function getMetaText($text) {
    $filter_title = $this->getSelectedsFilterTitle();
    
    if ($this->config('add_meta') && $filter_title) {
      if (false !== strpos($text, '{filter}')) {
        $text = trim(str_replace('{filter}', $filter_title, $text));
      } else {
        $text .= ' ' . $filter_title;
      }
    } else {
      $text = trim(str_replace('{filter}', '', $text));
    }

    return $text;
  }

  public function getPageMetaTitle($meta_title) {
    $page_info = $this->getPageInfo();

    if ($page_info) {
      $meta_title = $page_info['meta_title'];
    } else {
      $meta_title = $this->getMetaText($meta_title);
    }

    return $meta_title;
  }

  public function getPageMetaDescription($meta_description) {
    $page_info = $this->getPageInfo();

    if ($page_info) {
      $meta_description = $page_info['meta_description'];
    } else if ($meta_description) {
      $meta_description = $this->getMetaText($meta_description, '. ');
    }

    return $meta_description;
  }

  public function getPageMetaKeywords($meta_keyword) {
    $page_info = $this->getPageInfo();

    if ($page_info) {
      $meta_keyword = $page_info['meta_keyword'];
    } else if ($meta_keyword) {
      $meta_keyword = $this->getMetaText($meta_keyword);
    }

    return $meta_keyword;
  }

  public function getPageHeadingTitle($heading_title) {
    $page_info = $this->getPageInfo();

    if ($page_info) {
      $heading_title = $page_info['heading_title'];
    } else {
      $heading_title = $this->getMetaText($heading_title);
    }

    return $heading_title;
  }

  public function getPageDescription($position = 'top') {
    $page_info = $this->getPageInfo();

    if ($position != 'top') {
      $position = 'bottom';
    }

    if ($page_info && trim(html_entity_decode($page_info['description_' . $position], ENT_QUOTES, 'UTF-8')) && 
      !isset($this->opencart->request->get['page']) && 
      !isset($this->opencart->request->get['sort']) && 
      !isset($this->opencart->request->get['order']) && 
      !isset($this->opencart->request->get['search']) && 
      !isset($this->opencart->request->get['limit'])
    ) {
      $description = html_entity_decode($page_info['description_' . $position], ENT_QUOTES, 'UTF-8');
    } else {
      $description = '';
    }

    return $description;
  }

  public function formatPage($result) {
    $cache_key = 'p.' . $result['category_id'];
    
    if (isset($this->rewrite_cache[$cache_key])) {
      $link = $this->rewrite_cache[$cache_key];
    } else {
      $link = $this->opencart->url->link('product/category', 'path=' . $result['path'], 'SSL');
      
      $this->rewrite_cache[$cache_key] = $link;
    }   
        
    $selected = ($this->page_info && $this->page_info['page_id'] == $result['page_id']);    
    
    if (!$selected) {
      $link = rtrim($link, '/');  
      
      if (false !== utf8_strpos($link, '.html')) {
        $link = utf8_substr($link, 0, utf8_strpos($link, '.html'));
      }
      
      if ($result['keyword'] && $this->isSeoUrlEnabled()) {
        $link .= '/' . $result['keyword'] . $this->getUrlSuffix($link);
      } else {
        $link .= (false === utf8_strpos($link, '?') ? '?' : '&') . 'ocfilter_page_id=' . $result['page_id'];
      }
    }
    
    return [
      'name' => $result['name'] ? html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8') : html_entity_decode($result['heading_title'], ENT_QUOTES, 'UTF-8'),
      'selected' => $selected,
      'href' => $link,
      'params_count' => (int)$result['params_count'],
      'params' => json_decode($result['params'], true)
    ];
  }

  public function formatPages($pages = [], $filter_data = []) {
    $common_params = [];     
   
    $format_pages = [];
   
    foreach ($pages as $page) {
      $visibled = true;

      $params = json_decode($page['params'], true);

      $has_slider = $this->params->hasParamsSlider($params);

      if (!$has_slider) {
        foreach ($params as $filter_key => $values) {
          $visibled_param = false;
          
          foreach ($values as $value_id) {   
            if ($this->filter->getValueProductTotal($filter_key, $value_id)) {
              $visibled_param = true;
              
              break;
            }         
          }
          
          if (!$visibled_param) {
            $visibled = false; 
            
            break;
          }        
        }         
      }

      if (!$visibled) {
        continue; 
      }  
      
      $cache_key = 'p.' . $page['category_id'];
      
      if (isset($this->rewrite_cache[$cache_key])) {
        $link = $this->rewrite_cache[$cache_key];
      } else {
        $link = $this->opencart->url->link('product/category', 'path=' . $page['path'], 'SSL');
        
        $this->rewrite_cache[$cache_key] = $link;
      }           
      
      $selected = ($this->page_info && $this->page_info['page_id'] == $page['page_id']);
      
      if (!$selected) {
        $link = rtrim($link, '/');
        
        if ($link && false !== utf8_strpos($link, '.html')) {
          $link = utf8_substr($link, 0, utf8_strpos($link, '.html'));
        }  
        
        if ($page['keyword'] && $this->isSeoUrlEnabled()) {
          $link .= '/' . $page['keyword'] . $this->getUrlSuffix($link);
        } else {
          $link .= (false === utf8_strpos($link, '?') ? '?' : '&') . 'ocfilter_page_id=' . $page['page_id'];
        }        
      }        
      
      $format_pages[] = [
        'name' => $page['name'] ? html_entity_decode($page['name'], ENT_QUOTES, 'UTF-8') : html_entity_decode($page['heading_title'], ENT_QUOTES, 'UTF-8'),
        'selected' => $selected,
        'href' => $link,
        'params_count' => (int)$page['params_count'],
        'params' => $params,
        'has_slider' => $has_slider
      ];
      
      if (!$has_slider) {
        foreach ($params as $filter_key => $values) {
          if (!isset($common_params[$filter_key])) {
            $common_params[$filter_key] = [];
          }
        
          $common_params[$filter_key] = array_unique(array_merge($common_params[$filter_key], $values));
        }        
      }
    } // end pages foreach
    
    // Sort by values count asc
    uasort($common_params, function($a, $b) {
      return count($a) - count($b);
    });
    
    $this->setPageVisibility($format_pages, $common_params, $filter_data);

    return $format_pages;
  }
  
  private function setPageVisibility(&$format_pages, $common_params, $filter_data) {
    if (count($common_params) < 2) {
      return;      
    }
        
    $param_key = array_keys($common_params)[0];          
        
    $setVisibility = function($params, $value_id = false) use (&$format_pages, $param_key, $filter_data) {
      $filter_data['filter_params'] = $params;
      
      $this->filter->setValuesCounter($filter_data);          
      
      foreach ($format_pages as $key => $page) {
        if ($page['has_slider'] || !isset($page['params'][$param_key])) {
          continue;
        }        

        if ($value_id && !in_array($value_id, $page['params'][$param_key])) {
          continue;                    
        }
        
        $visibled = true;          

        foreach ($page['params'] as $filter_key => $values) {
          if ($filter_key == $param_key) {
            continue;
          }
          
          $visibled_param = false;
          
          foreach ($values as $_value_id) {   
            if ($this->filter->getValueProductTotal($filter_key, $_value_id)) {
              $visibled_param = true;
              
              break;
            }         
          }
          
          if (!$visibled_param) {
            $visibled = false; 
            
            break;
          }        
        }
        
        if (!$visibled) {
          unset($format_pages[$key]);
        }
      }      
    };   
        
    $values = array_shift($common_params);
    
    if (count($values) > 1) {     
      $setVisibility([ $param_key => $values ]);
    } else { 
      foreach ($values as $value_id) {
        $setVisibility([ $param_key => [ $value_id ] ], $value_id);
      }  
    }     
    
    $this->setPageVisibility($format_pages, $common_params, $filter_data);
  }
  
  public function getCategoryPages() {
    $pages_data = [
      'top' => [],
      'bottom' => [],
    ];

    if (!$this->placement->isCategory() || !$this->config('page_category_link_status')) {
      return $pages_data;
    }    
    
    $results = $this->opencart->model_extension_module_ocfilter->getPages([ 
      'filter_category' => true,
      'filter_category_id' => $this->getCategoryId() 
    ]);
   
    $filter_data = [
      'filter_category_id' => $this->getCategoryId() 
    ];
   
    $this->filter->setValuesCounter($filter_data);    
        
    // Set groups
    $setGroups = function($pages) {
      $page_groups = [];
      
      foreach ($pages as $page) {
        if ($page['params_count'] > 1) {
          if (!isset($page_groups[0])) {
            $page_groups[0] = [ 'name' => '', 'pages' => [] ];
          }
          
          $page_groups[0]['pages'][] = $page;
        } else if ($page['params']) {
          $filter_key = array_keys($page['params'])[0];

          if (!isset($page_groups[$filter_key])) {
            $filter_info = $this->opencart->model_extension_module_ocfilter->getFilter($filter_key);
            
            if ($filter_info) {
              $page_groups[$filter_key] = [ 'name' => $filter_info['name'], 'pages' => [] ];
            } else {
              continue;
            }
          }
          
          $page_groups[$filter_key]['pages'][] = $page;                    
        }        
      }
      
      uasort($page_groups, function($a, $b) {
        return $a['name'] < $b['name'];
      });      

      return $page_groups;
    };    
    
    $pages = $this->formatPages($results, $filter_data);
    
    if ($this->config('page_category_link_position') == 'top') {
      $pages_data['top'] = $setGroups($pages);
    } else if ($this->config('page_category_link_position') == 'bottom') {
      $pages_data['bottom'] = $setGroups($pages);
    } else {
      $pages_data['top'] = $setGroups(array_splice($pages, 0, ceil(count($pages) / 2)));
      $pages_data['bottom'] = $setGroups($pages);
    }
        
    return $pages_data;
  }
  
  public function getModulePages() {
    if (!$this->placement->isCategory() || !$this->config('page_module_link_status')) {
      return [];
    }

    $results = $this->opencart->model_extension_module_ocfilter->getPages([
      'filter_module' => true,
      'filter_category_id' => $this->getCategoryId()
    ]);
      
    $filter_data = [
      'filter_category_id' => $this->getCategoryId()
    ];
   
    $this->filter->setValuesCounter($filter_data);
    
    return $this->formatPages($results, $filter_data);
  }  
   
  public function getProductPages() {
    if (!$this->config('page_product_link_status') || !$this->getProductId()) {
      return [];
    }             
             
    $product_params = [];
    
    $query = $this->opencart->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$this->getProductId() . "'");

    if ($query->num_rows) {
      $product_values = array_merge(
        $this->opencart->model_extension_module_ocfilter->getProductValues($this->getProductId()),
        $this->opencart->model_extension_module_ocfilter->getProductRangeValues($this->getProductId())
      );
      
      foreach ($product_values as $result) {       
        if (!isset($product_params[$result['filter_key']])) {
          $product_params[$result['filter_key']] = [];
        }
        
        if (isset($result['min']) && isset($result['max'])) {
          $product_params[$result['filter_key']] = [ 'min' => $result['min'], 'max' => $result['max'] ];
        } else {
          $product_params[$result['filter_key']][] = $result['value_id'];
        }
      }     
      
      if ($query->row['manufacturer_id']) {
        $product_params[$this->params->special('manufacturer')->key()] = [ $query->row['manufacturer_id'] ];
      }
      
      if ($query->row['stock_status_id']) {
        $product_params[$this->params->special('stock')->key()] = [ $query->row['stock_status_id'] ];
      }
      
      if ($query->row['price']) {
        $product_params[$this->params->special('price')->key()] = [
          'min' => $query->row['price'],
          'max' => $query->row['price'],
        ];          
      }
    }        
    
    $results = $this->opencart->model_extension_module_ocfilter->getPages([
      'filter_product' => true,
      'filter_product_id' => $this->getProductId() 
    ]);    
       
    // Product page visibility
    $complete_relation = ($this->config('page_product_link_relation_type') == 'complete');

    foreach ($results as $key => $result) {
      $visibled = false;
      
      $params = json_decode($result['params'], true);
      
      foreach ($params as $filter_key => $values) {
        if (!isset($product_params[$filter_key])) {
          if ($complete_relation) {
            $visibled = false;
            
            break;
          }

          continue;            
        }
        
        $product_values = $product_params[$filter_key];
        
        $has_param = false;
        
        if ($this->params->isRange($values[0])) {
          list($min, $max) = $this->params->parseRange(array_shift($values));
          
          $has_param = (
            isset($product_values['min']) && isset($product_values['max']) && (
              ($min <= $product_values['min'] && $max >= $product_values['max']) ||
              ($product_values['min'] <= $min && $product_values['max'] >= $max)
            )
          );
        } else {
          $has_param = array_intersect($values, $product_values);         
        }                  
        
        if ($complete_relation && !$has_param) {
          $visibled = false;
            
          break;
        } else if ($has_param) {
          $visibled = true;
        }
      }
      
      if (!$visibled) {
        unset($results[$key]);
      }
    }       
    
    if ($results) {      
      $categories_id = array_unique(array_column($results, 'category_id'));              
             
      $filter_data = [
        'filter_category_id' => $categories_id
      ];
             
      $this->filter->setValuesCounter($filter_data);    

      return $this->formatPages($results, $filter_data);  
    }
    
    return [];
  }    

  public function getSelectedsFilterTitle() {
    if (!is_null($this->filter_title)) {
      return $this->filter_title;
    }

    $filter_title = '';

    $filters = $this->filter->getSelectedFilters();

    $limit = (int)$this->config('add_meta_limit');

    foreach ($filters as $i => $filter) {
      if ($limit > 0 && $i == $limit) {
        break;
      }
      
      $special_filter = $this->params->key($filter['filter_key'])->special();    
      
      if ($filter_title) {
        if ($this->config('add_meta') == 'filter_value') {
          $filter_title .= $this->config('meta_filter_separator');
        } else {
          $filter_title .= $this->config('meta_value_separator');
        }
      }

      $name_items = array_column($filter['values'], 'name');

      if ($limit > 0 && count($name_items) > $limit) {
        $name_items = array_slice($name_items, 0, $limit);
        
        $name_items[] = '...';
      }

      $values_name = implode($this->config('meta_value_separator'), $name_items);

      if ($values_name) {
        if ($this->config('add_meta') == 'value' || $special_filter == 'manufacturer' || $special_filter == 'price' || $special_filter == 'discount' || $special_filter == 'newest' || $special_filter == 'stock') {
          $filter_title .= $values_name;
        } else {
          $filter_title .= $filter['name'] . ' ' . $values_name;
        }
      }
    }

    if ($filter_title && $this->config('meta_lowercase')) {
      $filter_title = utf8_strtolower($filter_title);
    }

    $this->filter_title = $filter_title;

    return $filter_title;
  }
}