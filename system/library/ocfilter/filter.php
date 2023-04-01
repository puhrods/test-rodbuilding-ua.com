<?php
  
namespace OCFilter;

class Filter extends Factory {
  const IMG_WIDTH = 20;
  const IMG_HEIGHT = 20;
   
  // Auto columns 
  const VALUE_WIDTH = 260; 
  const SIGN_WIDTH = 8; 
   
  private $counters = [];

  public function setValuesCounter($filter_data = null) {   
    if (is_null($filter_data)) {
      $filter_data = $this->getFilterModelData();
    }
   
    $this->counters = $this->opencart->model_extension_module_ocfilter->getCounters($filter_data);   
  }

  public function getValueProductTotal($filter_key, $value_id) {
    if (isset($this->counters[$filter_key][$value_id])) {
      return (int)$this->counters[$filter_key][$value_id];
    }      
    
    return 0;
  }

  public function formatFilter($filter_data) {
    $selected = $this->params->has($filter_data['filter_key']);
    
    if ($selected) {
      $discard = $this->seo->link($this->params->encode($this->params->getWithout($filter_data['filter_key']))); 
    } else {
      $discard = false;
    }
    
    return [
      'filter_key'          => $filter_data['filter_key'],
      'id'                  => str_replace('.', '-', $filter_data['filter_key']),
      'name'                => strip_tags(html_entity_decode($filter_data['name'], ENT_QUOTES, 'UTF-8')),
      'dropdown'            => $filter_data['dropdown'],
      'color'               => $filter_data['color'],
      'image'               => $filter_data['image'],
      'prefix'              => strip_tags(html_entity_decode($filter_data['prefix'], ENT_QUOTES, 'UTF-8')),
      'suffix'              => strip_tags(html_entity_decode($filter_data['suffix'], ENT_QUOTES, 'UTF-8')),
      'description'         => strip_tags(html_entity_decode($filter_data['description'], ENT_QUOTES, 'UTF-8')),
      'type'                => $filter_data['type'] ? $filter_data['type'] : 'checkbox',
      'selected'            => $selected,
      'sort_order'          => $filter_data['sort_order'],
      'chart'               => [],
      'min'                 => 0,
      'max'                 => 0,
      'min_request'         => 0,
      'max_request'         => 0,
      'slider_enabled'      => true,
      'values'              => [],
      'hidden_values'       => [],
      'columns'             => 1,
      'button_show_more_values' => '',
      'text_selected'       => '',
      'discard'             => $discard,
    ];
  }
  
  public function sortFilters(&$filters_data) {
    uasort($filters_data, function($a, $b) {
      if (($a['sort_order'] == 'begin') != ($b['sort_order'] == 'begin')) {        
        return ($a['sort_order'] == 'begin') < ($b['sort_order'] == 'begin');
      }
      
      if (($a['sort_order'] == 'after') != ($b['sort_order'] == 'after')) {        
        return ($a['sort_order'] == 'after') > ($b['sort_order'] == 'after');
      }      
      
      if ($a['sort_order'] != $b['sort_order']) {
        return $a['sort_order'] > $b['sort_order'];
      }      
      
      return $b['name'] < $a['name'];
    });   
    
    $filters_data = array_values($filters_data);
  }  
    
  public function setFilterSlider(&$filter_item) {
    $range = $this->opencart->model_extension_module_ocfilter->getFilterSliderRange($filter_item['filter_key'], $this->getFilterModelData($filter_item['filter_key']));

    if ($range['min'] == $range['max']) {           
      return false;
    }  
    
    $filter_item['min'] = $range['min'];
    $filter_item['max'] = $range['max'];

    $filter_item['min_request'] = $range['min'];
    $filter_item['max_request'] = $range['max'];
    
    if (false !== ($value = $this->params->getSelectedFilter($filter_item['filter_key'])) && is_string($value)) {
      list($min, $max) = $this->params->parseRange($value);

      $filter_item['min_request'] = $min;
      $filter_item['max_request'] = $max;
    }
    
    $filter_item['slider_enabled'] = $this->params->isEnabledSlider($filter_item['filter_key']);
        
    return true;
  }  
  
  public function setFilterChart(&$filter_item) {
    $chart_data = $this->opencart->model_extension_module_ocfilter->getFilterChartData($filter_item['filter_key'], $filter_item['min'], $filter_item['max'], $this->getFilterModelData($filter_item['filter_key']));
    
    if ($chart_data) {
      $filter_item['chart'] = $chart_data;
      
      return true;
    }  

    return false;
  }    
  
  public function setFilterValues(&$filter_item, $from_callback = false) {
    $value_results = $this->opencart->model_extension_module_ocfilter->getFilterValues($filter_item['filter_key'], $this->getFilterModelData());

    $values_data = [];

    $disabled = true;

    foreach ($value_results as $value) {
      // Skip empty name values
      if (utf8_strlen(trim(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8'))) < 1) {
        continue;
      }
      
      $selected_value = ($filter_item['selected'] ? $this->params->has($filter_item['filter_key'], $value['value_id']) : false);

      if ($selected_value) {
        $disabled = false;
      }

      $count = $this->getValueProductTotal($filter_item['filter_key'], $value['value_id']);

      if ($count) {
        $count = $this->helper->number_abbr($count);
      }

      if ($count && $filter_item['selected'] && $filter_item['type'] == 'checkbox') {
        $count = '+' . $count;
      }

      if ($count || $selected_value || !$this->config('hide_empty_values')) {
        if ($filter_item['image'] && isset($value['image']) && $value['image'] && file_exists(DIR_IMAGE . $value['image'])) {
          $image = $this->opencart->model_tool_image->resize($value['image'], self::IMG_WIDTH, self::IMG_HEIGHT);
        } else {
          $image = false;
        }

        $values_data[] = [
          'value_id' => $value['value_id'],
          'id'       => $filter_item['id'] . '-' . $value['value_id'],
          'name'     => html_entity_decode($value['name'] . (isset($filter_item['suffix']) ? $filter_item['suffix'] : ''), ENT_QUOTES, 'UTF-8'),
          'color'    => (($filter_item['color'] && isset($value['color']) && $value['color']) ? $value['color'] : false),
          'image'    => $image,
          'count'    => $count,
          'selected' => $selected_value
        ];
      }
    } // foreach $value_results
       
    if ($disabled && $values_data) {
      $disabled = false;
    }
    
    if (!$disabled && !$from_callback && !$filter_item['selected']) {
      $disabled = (count($values_data) == 1 && $this->config('hide_single_value'));
      
      if ($disabled) {
        $disabled = !($this->params->key($filter_item['filter_key'])->is('stock') && $this->config('stock_status_method') != 'quantity');
      }    

      if ($disabled) {
        $disabled = !$this->params->key($filter_item['filter_key'])->is('discount');
      }

      if ($disabled) {        
        $disabled = !$this->params->key($filter_item['filter_key'])->is('newest');
      }     
    }    
    
    if ($disabled) {
      return false;
    }
    
    if ($this->config('values_auto_column')) {
      $values_max_length = max(array_map(function($v) { return utf8_strlen($v['name']); }, $values_data));
      
      if ($values_max_length) {
        $filter_item['columns'] = floor(self::VALUE_WIDTH / (self::SIGN_WIDTH * $values_max_length + 70));
      }      
    }
    
    $filter_item['values'] = $values_data;

    return true;
  }

  public function slicingValues(&$filter_item) {
    if ($this->config('show_values_limit') > 0 && $this->config('show_values_limit') < count($filter_item['values'])) {
      $hidden_values = array_splice($filter_item['values'], (int)$this->config('show_values_limit'));
      
      // Add selected values
      foreach ($hidden_values as $key => $value) {
        if ($value['selected']) {
          $filter_item['values'][] = $value;
          
          unset($hidden_values[$key]);
        }
      } 
      
      if ($hidden_values) {
        if (!$this->config('hidden_values_lazy_load')) {
          $filter_item['hidden_values'] = $hidden_values; 
        }            
        
        $filter_item['button_show_more_values'] = sprintf($this->opencart->language->get('button_show_more_values'), count($hidden_values));  
      }  
    }
  }

  public function getSelectedFilters() {
    $filters = [];

    $params = $this->params->get();

    foreach ($params as $filter_key => $values) {              
      $filter_info = $this->opencart->model_extension_module_ocfilter->getFilter($filter_key);

      if (!$filter_info) {
        continue;
      }

      $filter_values = [];

      foreach ($values as $value_id) {
        if ($filter_info['type'] == 'slide' || $filter_info['type'] == 'slide_dual') {
          list($min, $max) = $this->params->parseRange($value_id);
          
          if ($min != $max) {
            $name = sprintf($this->opencart->language->get('text_slider_selected_range'), $filter_info['prefix'], $min, $max, $filter_info['suffix']);
          } else {
            $name = $min . $filter_info['suffix'];
          }         
        } else {
          $name = $this->opencart->model_extension_module_ocfilter->getFilterValueName($filter_key, $value_id);
        }
        
        if (!$name) {
          continue;
        }
        
        if ($filter_info['type'] == 'checkbox') {
          $_params = $this->params->encode($this->params->getValueParams($filter_key, $value_id, $filter_info['type']));
        } else {
          $_params = $this->params->encode($this->params->getWithout($filter_key));
        }

        $filter_values[] = [
          'name' => html_entity_decode($name, ENT_QUOTES, 'UTF-8'),
          'href' => $this->seo->link($_params),
        ];
      }

      $filters[] = [
        'filter_key' => $filter_key,
        'name'   => html_entity_decode($filter_info['name'], ENT_QUOTES, 'UTF-8'),
        'values' => $filter_values
      ];
    }

    return $filters;
  }

  public function getFilterModelData($exclude_param = '', $with_params = true, $params = []) {
    $filter_data = [];

    if ($this->params->get() && $with_params) {
      if ($exclude_param) {
        $filter_data['filter_params'] = $this->params->getWithout($exclude_param);
      } else {
        $filter_data['filter_params'] = $this->params->get();
      }
    } else if ($params) {
      $filter_data['filter_params'] = $params;      
    }

    if ($this->seo->getCategoryId()) {
      $filter_data['filter_category_id'] = $this->seo->getCategoryId();
    }

    if ($this->placement->isManufacturer()) {
      $filter_data['filter_manufacturer_id'] = $this->seo->getManufacturerId();
    }

    if ($this->placement->isSpecial()) {
      $filter_data['filter_special'] = true;
    }

    if ($this->placement->isSearch() && $this->seo->getSearchKeyword()) {
      $filter_data['filter_search'] = $this->seo->getSearchKeyword();
    }

    if ($this->placement->isCustomPage()) {
      $filter_data['filter_key'] = $this->placement->getCustomPageFilters();
    }
    
    return $filter_data;
  }
}