<?php
class ControllerZoXml2ZoXml2Yml extends Controller {
	public function index() {
    $json   = array();
    $json[] = "You do not have permission to access this page!";
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function add() {
    $json   = array();
    $json[] = "scan: You do not have permission to access this page!";
    $session_key = '0';

//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'post', data = '" . $this->db->escape(json_encode($this->request->post)) . "', user = 'add'");

    if ($this->request->server['REQUEST_METHOD'] != 'POST') {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = '" . $this->db->escape("Недопустимый метод: " . $this->request->server['REQUEST_METHOD']) . "', user = ''");
      return;
      }
    if (empty($this->request->post['url'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = 'YML - осутствует URL!', user = '" . $this->db->escape($this->request->post['user']) . "'");
      }
    else {
      $query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product WHERE field='supplier' ");
      if ($query->num_rows) $supplier = 'supplier';
      else                  $supplier = 'location';
      $data = array(
  			'module'       => $this->db->escape($this->request->post['module']),
  			'name'         => $this->db->escape($this->request->post['name']),
  			'url'          => $this->db->escape($this->request->post['url']),
  			'license'      => '',
        'supplier'     => $supplier,
        'before'       => 'zero',
        'images'       => 'main',
        'link'         => 'sku',
        'insert'       => 1,
        'update'       => 1,
        'add_before'   => 0,
        'mul_after'    => 1,
        'add_after'    => 0,
        'before_mode'  => 'supplier',
        );
        
      $salt = (string)time (); 
      $session_key    = 'YML' . md5 ($salt . $this->request->post['url']);
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_suppliers SET session_key = '" . $this->db->escape($session_key) . "', data = '" . $this->db->escape(json_encode($data)) ."'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('YML-файл добавлен в список поставщиков.') ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
      }


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function scan() {
    $json   = array();
    $json[] = "scan: You do not have permission to access this page!";

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if (isset($this->request->post['session_key'])) {
        if (empty($this->request->post['url'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'error', data = 'YML - осутствует URL!', user = '" . $this->db->escape($this->request->post['user']) . "'");
          }
        else {
          $this->load->model('zoxml2/zoxml2');
          $data = $this->model_zoxml2_zoxml2->getDefSettings ($this->request->post['session_key'],$this->request->post['user']);
          if (!empty($data['settings']['user_scan'])) $this->load->model('zoxml2/zoxml2scan');
          $this->do_scan($data);
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'end', data = '" . $this->db->escape('YML - сканирование завершено.') ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
          }
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = '" . $this->db->escape(json_encode($this->request->post)) . "', user = 'POST: unknown!'");
        }
      }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function do_scan($data) {
    $session_key = $data['session_key'];
    $user        = $data['user'];

    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('YML - начал сканировать фид ' . $data['settings']['url']) ."', user = '" . $this->db->escape($user) . "'");
    $xml = $this->model_zoxml2_zoxml2->getXML ($data);
    if (!$xml) return null;

    if ($data['settings']['tag_shop']&&!isset($xml->{$data['settings']['tag_shop']})) { 
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: файл не является YML-файлом! Тег <shop> не найден!') ."', user = '" . $this->db->escape($user) . "'");
      return null; 
      }
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '0'");
      }
    // ------
//    $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_categories WHERE `session_key` = '" . $this->db->escape($session_key) . "'");
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '0' WHERE `session_key` = '" . $this->db->escape($session_key) . "'");
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors    SET total = '0' WHERE `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!empty($data['settings']['user_scan'])) {
      $UserScan = $this->model_zoxml2_zoxml2scan->doUserScan($data,$xml);  
      if ($UserScan==NULL) return NULL; 
      }
    $values = array ();
    $categories = array();
    $params = array ();
    if ($data['settings']['tag_shop']) $info_total = count($xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});    
    else {
      if ($data['settings']['tag_offers']) $info_total = count($xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});
      else                                 $info_total = count($xml->{$data['settings']['tag_offer']});
      }
    $info_processed         = 0;    
    $info_progress          = 0;    
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
    // Категории
    $input_categories = $xml;
    if ($data['settings']['tag_shop'])        $input_categories = $input_categories->{$data['settings']['tag_shop']};
    if ($data['settings']['tag_categories'])  $input_categories = $input_categories->{$data['settings']['tag_categories']};
    if ($data['settings']['tag_category'])    $input_categories = $input_categories->{$data['settings']['tag_category']};
    if ($input_categories&&($data['settings']['tag_shop']||$data['settings']['tag_categories']||$data['settings']['tag_category'])) {
      $txt = "Найдено категорий (всего): " . count($input_categories);
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");

      $prohodov = 2;
      $prohod   = 0;
      while ($prohodov--) {
        $info_processed = 0;
        $prohod++;
        foreach ($input_categories as $item) {
          $info_processed ++;
          if (++$info_progress==$data['module']['step']) {
            $info_progress = 0; 
            $txt = "Проход: " . $prohod;
            $txt .= " Обработано категорий: " . $info_processed;
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
            }
          $item_id = 0;
          if (isset($item['id'])) $item_id = trim($item['id']); 
          if (isset($item->id))   $item_id = trim($item->id); 
          $item_name = '';
          if (isset($item->name))   $item_name = trim($item->name); 
          elseif (isset($item['title']))   $item_name = trim($item['title']); 
          elseif (isset($item['name']))    $item_name = trim($item['name']); 
          else                      $item_name = trim((string)$item);

          if ($item_id) {
            $parent_id    = 0;
            if (isset($item['parentId'])) $parent_id = trim($item['parentId']); 
            if (isset($item->parent_id))  $parent_id = trim($item->parent_id); 
            $categories[$item_id] = array( 
              'parent_id'    => $parent_id,  
              'total'        => 0,
              'name'         => $item_name,
              'data'         => $item_id,
              'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
              );
            }
          }
        }
      // Добавляем всех родителей в путь
      foreach ($categories as $key => $item) {
        $parent_id = $item['parent_id'];
        $name      = '';
        while ($parent_id) {
          if (!isset($categories[$parent_id])) break;
          $parent_item = $categories[$parent_id];
          if ($name)  $name = $parent_item['name'] . " / " . $name;
          else        $name = $parent_item['name'];
          $parent_id        = $parent_item['parent_id'];
          }
        $categories[$key]['path'] = $name;
        }
      $txt = "Обработано категорий (всего): " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    else {
      $txt = "Обработано категорий (всего): категории отсутствуют";
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    // Производители и атрибуты
    $info_processed         = 0;    
    $info_progress          = 0;    
    $params['host']      = true;

    if ($data['settings']['tag_shop']) {
      if ($data['settings']['tag_offers']) $offers = $xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
      else                                 $offers = $xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offer']};
      }
    else {          
      if ($data['settings']['tag_offers']) $offers = $xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
      else                                 $offers = $xml->{$data['settings']['tag_offer']};
      }
    foreach ($offers as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано товаров: " . $info_processed;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        if (isset($data['module']['HARD_DEBUG'])) {    
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape('3 - использовано памяти на данный момент: ' . memory_get_usage (TRUE)) . "', user = '" . $this->db->escape($data['user']) . "'");
//          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape('4 - использовано памяти на данный момент: ' . xmlMemUsed ()) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      foreach($item->attributes() as $key => $value) {
        if ($key=='available') $params['available'] = true;
        else $params["offer_" . $key] = true;
        }

      foreach ($item as $key => $value) if ($key!='params'&&$key!='param'&&$key!='specs'&&$key!='features') $params[$key] = true;
      if (!isset($data['settings']['auto_atributes'])) {
        if (isset($item->params->param))      foreach ($item->params->param as $item_value)       if (isset($item_value['name'])) $params['params_' .  trim($item_value['name'])] = true;
        if (isset($item->features->feature))  foreach ($item->features->feature as $item_value)   if (isset($item_value['name'])) $params['feature_' . trim($item_value['name'])] = true;
        if (isset($item->param))              foreach ($item->param  as $item_value)              if (isset($item_value['name'])) $params['param_'  .  trim($item_value['name'])] = true;
        if (isset($item->specs->spec))        foreach ($item->specs->spec as $item_value)         if (isset($item_value->name))   $params['spec_'   .  trim($item_value->name)]   = true;
        if (isset($item->ЗначенияРеквизитов->ЗначениеРеквизита))        foreach ($item->ЗначенияРеквизитов->ЗначениеРеквизита as $item_value)         if (isset($item_value->Наименование))   $params['Реквизит_'   .  trim($item_value->Наименование)]   = true;
  //      if (isset($item->attributes->attribute))  foreach ($item->attributes->attribute as $item_value) if (isset($item_value->name))   $params['attribute_'   . $item_value->name]   = true;
       }
      $cat_name     = '(категория не указана)';
      $par_cat_name = '';
      $vendor       = '(производитель не указан)';
      if (isset($item->vendor)) $vendor = trim($item->vendor);
      foreach ($item as $key => $value) {
        $option_key = $key;
        if ($key=='params' && !isset($data['settings']['auto_atributes'])) {
          if (isset($value['name'])) $option_key = 'params_' . $value['name']; 
          else {
            if (isset($item->params->param)) {      
              foreach ($item->params->param as $item_value) {
                if (isset($item_value['name'])) {
                    if (isset($item_value['value'])) $param_val = $item_value['value'];
                    else                             $param_val = $item_value;
                  $option_key = 'params_' .  trim($item_value['name']);
                  if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor')       $vendor       = trim($param_val);
                  if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='cat_name')     $cat_name     = trim($param_val);
                  if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='par_cat_name') $par_cat_name = trim($param_val);
                  }
                }
              }
            } 
          } 
        if ($key=='param'  && !isset($data['settings']['auto_atributes'])) if (isset($value['name'])) $option_key = 'param_'  . $value['name'];   
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor')       $vendor       = trim($value);
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='cat_name')     $cat_name     = trim($value);
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='par_cat_name') $par_cat_name = trim($value);
        if ($key=='specs' && !isset($data['settings']['auto_atributes']))  {
          foreach ($value->spec as $item_value) {
            $option_key = 'spec_';
            if (isset($item_value->name)) $option_key .= (string)$item_value->name;
            if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor') $vendor = (string)$item_value->value;
            }
          }
        if ($key=='ЗначенияРеквизитов' && !isset($data['settings']['auto_atributes']))  {
          foreach ($value->ЗначениеРеквизита as $item_value) {
            $option_key = 'Реквизит_';
            if (isset($item_value->Наименование)) $option_key .= (string)$item_value->Наименование;
            if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor') $vendor = (string)$item_value->Значение;
            }
          }
        }
      foreach($item->attributes() as $key => $value) {
        if ($key=='available') $option_key = 'available';
        else $option_key = "offer_" . $key;
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor')       $vendor       = trim($value);
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='cat_name')     $cat_name     = trim($value);
        if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='par_cat_name') $par_cat_name = trim($value);
        }
      if (!$vendor) $vendor = '(производитель не указан)';
      if ($vendor) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        if ($query->row) {
          $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
          }
        }
      // Обновляем псевдо-производителя HOST
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      // Категории
      if (isset($item->categoryId)||isset($item['categoryId'])) {
        $categoryId = (string)(isset($item->categoryId)?$item->categoryId:$item['categoryId']);
        if (isset($categories[$categoryId])) $categories[$categoryId]['total'] ++;
        else {
          // Добавляем несуществующую категорию и выводим предупреждение
          $categories[$categoryId] = array( 
            'parent_id'    => 0,  
            'total'        => 1,
            'name'         => (string)$categoryId,
            'data'         => (string)$categoryId,
            'parent'       => '',
            'path'         => '',
            ); 
          $offer_id         = isset($item['id'])?(string)$item['id']:0;
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Предупреждение: товар ' . $offer_id . ' ссылается на несуществующую категорию - ' . $categoryId) ."', user = '" . $this->db->escape($user) . "'");
          }
        }
      // Множественная привязка
      if (isset($data['settings']['link2category_ids']) && isset($item->categoryId)) {
        $i=0;
        foreach($item->categoryId as $categoryId) {
          if ($i++) {
            if (isset($categories[(string)$categoryId])) $categories[(string)$categoryId]['total'] ++;
            else {
              // Добавляем несуществующую категорию и выводим предупреждение
              $categories[$categoryId] = array( 
                'parent_id'    => 0,  
                'total'        => 1,
                'name'         => (string)$categoryId,
                'data'         => (string)$categoryId,
                'parent'       => '',
                'path'         => '',
                ); 
              }
            }
          }
        }
      if ($cat_name||$par_cat_name) {
        if (!$cat_name) {$cat_name=$par_cat_name; $par_cat_name='';}
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($cat_name) . "' AND parent = '" . $this->db->escape($par_cat_name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        if ($query->row) {
          $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE name = '" . $this->db->escape($cat_name) . "' AND parent = '" . $this->db->escape($par_cat_name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($cat_name) . "', `parent` = '" . $this->db->escape($par_cat_name) . "', `total` = '1'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $par_cat_name . ' -> ' . $cat_name) ."'");
          }
        }
    }
    $txt = "Обработано товаров (всего): " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
    // СБРОС В БАЗУ
    foreach ($categories as $item) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        if (!$query->row) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', parent_code = '" . $this->db->escape($item['parent_id']) . "', `path` = '" . $this->db->escape($item['path']) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['path'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
          }
        else {
          $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "', name = '" . $this->db->escape($item['name']) . "', parent = '" . $this->db->escape($item['parent']) . "', parent_code = '" . $this->db->escape($item['parent_id']) . "', `path` = '" . $this->db->escape($item['path']) . "'  WHERE `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
          }
        }
    foreach ($params as $key => $value) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
        }
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'end', data = '" . $this->db->escape('Сохранение данных в БД завершено!') . "', user = '" . $this->db->escape($user) . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Пиковый расход памяти: ' . memory_get_peak_usage(TRUE) ) . "', user = '" . $this->db->escape($user) . "'");
    }

	public function load() {
    $json   = array();
    $json[] = "load: You do not have permission to access this page!";

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if (isset($this->request->post['session_key'])) {
        $this->load->model('zoxml2/zoxml2');
        $data = $this->model_zoxml2_zoxml2->getDefSettings ($this->request->post['session_key'],$this->request->post['user']);
        if (!empty($data['settings']['user_start']))        $this->load->model('zoxml2/zoxml2start');
        if (!empty($data['settings']['user_filter']))       $this->load->model('zoxml2/zoxml2filters');
        if (!empty($data['settings']['user_pre']))          $this->load->model('zoxml2/zoxml2pre');
        if (!empty($data['settings']['update_use_script'])) $this->load->model('zoxml2/zoxml2usescript');
        if (!empty($data['settings']['insert_analyzer']))   $this->load->model('zoxml2/zoxml2insertanalyzer');
        if (!empty($data['settings']['update_analyzer']))   $this->load->model('zoxml2/zoxml2updateanalyzer');
        if (!empty($data['settings']['user_after']))        $this->load->model('zoxml2/zoxml2after');
        if (!empty($data['settings']['user_ro'])) {
                                                            $this->load->model('zoxml2/zoxml2rofilters');
                                                            $this->load->model('zoxml2/zoxml2ro');
          }
 //   ignore_user_abort(true);
//    ini_set('max_execution_time', 0);
        $this->model_zoxml2_zoxml2->before($this->request->post['session_key'],$data,$this->request->post['user']);
        usleep (1000);
        $query = $this->db->query("SELECT NOW() as start_time");
        usleep (1000);
        $data['process_start_time'] = $query->row['start_time'];
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'start_time', data = '" . $this->db->escape($data['process_start_time']) ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
        $this->do_load($data);
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'end', data = '" . $this->db->escape('YML - обработка завершена.') ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = '" . $this->db->escape(json_encode($this->request->post)) . "', user = 'POST: unknown!'");
        }
      }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function do_load($data) {
    $session_key = $data['session_key'];

    if (!$data['settings']['url']) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = 'YML - осутствует URL!', user = '" . $this->db->escape($data['user']) . "'");
      return null; 
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('YML - начал загружать фид ' . $data['settings']['url']) ."', user = '" . $this->db->escape($data['user']) . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('PID: ' . getmypid()) ."', user = '" . $this->db->escape($data['user']) . "'");

    if (!isset($data['settings']['update']) && !isset($data['settings']['insert'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('YML - загрузка фида пропушена') . "', user = '" . $this->db->escape($data['user']) . "'");
      if (!empty($data['settings']['user_after'])) {
        $this->model_zoxml2_zoxml2after->doUserAfter($data);
  	    $this->cache->delete('product');
        }
      return null; 
      }

    $xml = $this->model_zoxml2_zoxml2->getXML ($data);
    if (!$xml) return null;

    if ($data['settings']['tag_shop']&&!isset($xml->{$data['settings']['tag_shop']}))        { 
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: файл не является YML-файлом! Тег <shop> не найден!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return null; 
      }
    // ------
    if ($data['settings']['tag_shop']) {
      if ($data['settings']['tag_offers']) $info_total = count($xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});
      else                                 $info_total = count($xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offer']});
      }
    else {
      if ($data['settings']['tag_offers']) $info_total = count($xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});
      else                                 $info_total = count($xml->{$data['settings']['tag_offer']});
      }
     $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($data['user']) . "'");
  // currencies
    if (isset($xml->shop->currencies->currency)) {
      foreach ($xml->shop->currencies->currency as $currency) {
        if (isset($currency['id'])&&isset($currency['rate'])) $data['currencies'][(string)$currency['id']] = (string)$currency['rate']; 
        }
     }

  $data['info_insert']      = 0;
  $data['info_update']      = 0;
//  $data['info_processed']   = 0;
//  $data['info_progress']    = 0;
    $info_processed         = 0;    
    $info_progress          = 0;    

    if ($data['settings']['tag_shop']) {
      if ($data['settings']['tag_offers']) $offers = $xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
      else                                 $offers = $xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offer']};
      }
    else {
      if ($data['settings']['tag_offers']) $offers = $xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
      else                                 $offers = $xml->{$data['settings']['tag_offer']};
      }
  // Кешируем поиск категории
  $data['cache_category'] = array();
  // Кешируем список обновленных изображений
  $cache_main_image = array(); // записываем ID товара у которого обновлены изображения

  // ПОЛЬЗОВАТЕЛЬСКИЙ СТАРТ
  if (!empty($data['settings']['user_start'])) {
    $DEBUG = isset($data['module']['DEBUG']);
    $user  = $data['user'];
    $data  = $this->model_zoxml2_zoxml2start->doUserStart($data,$xml);
    if (!$data) {
      if ($DEBUG) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Пропуск. Пользовательский START") . "', user = '" . $this->db->escape($user) . "'");
      return null; 
      } 
    }



    foreach ($offers as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано: " . $info_processed;
        $txt .= "  Добавлено: " . $data['info_insert'];
        $txt .= "  Обновлено: " . $data['info_update'];
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
     // ОПЦИИ
      $output = $this->model_zoxml2_zoxml2->getDefOutput ($data);
      $output['sku']            = isset($item['id'])?trim($item['id']):'';
      $output['model']          = isset($item->model)?trim($item->model):''; 
      $output['price']          = isset($item->price)?(float)trim($item->price):''; 
      $output['name']           = isset($item->name)?trim($item->name):''; 
      $output['vendor']         = isset($item->vendor)?trim($item->vendor):''; 
      $output['cat_id']         = '';
      if (isset($item->categoryId))   $output['cat_id'] = trim($item->categoryId);
      if (isset($item['categoryId'])) $output['cat_id'] = trim($item['categoryId']);
      $status                   = 1;
      // ПОЛЬЗОВАТЕЛЬСКИЙ ПРЕПРОЦЕССОР
      if (!empty($data['settings']['user_pre'])) {
        $output_sku = $output['sku'];
        $output = $this->model_zoxml2_zoxml2pre->doUserPre($output,$data,$item);
        if (!is_array($output)) {
          $data['info_update'] += $output;
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output_sku . ". Пропуск. Пользовательский фильтр")     . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          } 
        }
      //  param_name & param_value  
      foreach ($item as $option_key => $value) {
        if ($option_key=='params') {
            if (isset($value['name'])) {
              if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('params_' . trim($value['name']),$output,$data,$value);
              else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($value['name']))] = trim($value);
              }
            else {
              if (isset($value->param)) {
                foreach ($value->param as $param) {
                  if (isset($param['name'])) {
                    if (isset($param['value'])) $param_val = $param['value'];
                    else                        $param_val = $param;
                    if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('params_' . trim($param['name']),$output,$data,$param_val);
                    else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($param['name']))] = trim($param_val);
                    }
                  }
                } 
              }
          continue;
          } 
        if ($option_key=='param') {
            if (isset($value['name'])) {
              if (isset($value['value'])) $param_val = $value['value'];
              else                        $param_val = $value;
              if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('param_' . trim($value['name']),$output,$data,$param_val);
              else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($value['name']))] = trim($param_val);
              }
          continue;
          } 
        if ($option_key=='specs') {
          foreach ($value->spec as $item_value) {
            if (isset($item_value->name)) {
              if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('spec_'  . trim($item_value->name),$output,$data,$item_value->value);
              else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($item_value->name))] = trim($item_value->value);
              }
            }
          continue;
          }
        if ($option_key=='ЗначенияРеквизитов') {
//          continue;
          foreach ($value->ЗначениеРеквизита as $item_value) {
            if (isset($item_value->Наименование)) {
              if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('Реквизит_'  . trim($item_value->Наименование),$output,$data,$item_value->Значение);
              else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($item_value->Наименование))] = trim($item_value->Значение);
              }
            }
          continue;
          }
        if ($option_key=='features') {
          foreach ($value->feature as $item_value) {
            if (isset($item_value['name'])) {
              if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams('feature_'  . trim($item_value['name']),$output,$data,$item_value);
              else                              $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($item_value['name']))] = trim($item_value);
              }
            }
          continue;
          }
        $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$value);
        }
      foreach($item->attributes() as $key => $value) {
        if ($key=='available') $output = $this->model_zoxml2_zoxml2->doParams('available',     $output,$data,$item['available']);
        else                   $output = $this->model_zoxml2_zoxml2->doParams("offer_" . $key, $output,$data,$item[$key]);
        }
      // Произодитель
      if (empty($output['vendor'])) $output['vendor'] = '(производитель не указан)';
      $output = $this->model_zoxml2_zoxml2->doParams('host',  $output,$data,'host');
      $data['manufacturer_info'] = $this->model_zoxml2_zoxml2->getVendorAndMargin($data,$output['vendor']);
      $output['manufacturer_id'] = $data['manufacturer_info']?$data['manufacturer_info']['manufacturer_id']:0;
      // Категория
      if (empty($output['cat_id']) && empty($output['cat_name']) && empty($output['par_cat_name'])) {
        $output['cat_name'] = '(категория не указана)';
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'Warning', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Категория не указана. Используем правило для (категория не указана)") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      $data['category_info'] = $this->model_zoxml2_zoxml2->getCategoryAndMarginByID($session_key,$output['cat_id']);
      $output['category_id'] = $data['category_info']?$data['category_info']['category_id']:0;
      if (empty($output['category_id'])) {
        if (!empty($output['cat_name']))          $output['category_id']     = $this->model_zoxml2_zoxml2->getCategory($session_key,$output['cat_name'],$output['par_cat_name']);
        if (empty($output['category_id']) && !empty($output['par_cat_name'])) $output['category_id']     = $this->model_zoxml2_zoxml2->getCategory($session_key,$output['par_cat_name']);
        }
      // ОГРАНИЧИТЕЛЬ
      if (!isset($data['cache_category'][$output['category_id']])) {
        if (defined('MAX_PRODUCTS_PER_CATEGORY')) $data['cache_category'][$output['category_id']] = 1;
        }

      if (!empty($data['cache_category'][$output['category_id']])) {
        if ($data['cache_category'][$output['category_id']]++ > MAX_PRODUCTS_PER_CATEGORY) $output['category_id'] = 0;
        }
      // ОБРАБОТКА ОПЦИЙ ПО УМОЛЧАНИЮ - делаем после обработки тегов, но до ПОДСТАНОВОК
      foreach ($item as $option_key => $value) {
        if ($option_key=='params') {
            if (isset($value['name'])) {
              $output = $this->model_zoxml2_zoxml2->doDefaultOption('params_' . $value['name'],$output,$data,$value);
              }
            else {
              if (isset($value->param)) {
                foreach ($value->param as $param) {
                  if (isset($param['name'])) $output = $this->model_zoxml2_zoxml2->doDefaultOption('param_' . trim($param['name']),$output,$data,$param);
                  }
                } 
              }
          continue;
          } 
        if ($option_key=='param') {
            if (isset($value['name'])) {
              $output = $this->model_zoxml2_zoxml2->doDefaultOption('param_' . trim($value['name']),$output,$data,$value);
              }
          continue;
          } 
        if ($option_key=='specs') {
          foreach ($value->spec as $item_value) {
            if (isset($item_value->name)) {
              $output = $this->model_zoxml2_zoxml2->doDefaultOption('spec_'  . trim($item_value->name),$output,$data,$item_value->value);
              }
            }
          continue;
          }
        if ($option_key=='ЗначенияРеквизитов') {
//          continue;
          foreach ($value->ЗначениеРеквизита as $item_value) {
            if (isset($item_value->Наименование)) {
              $output = $this->model_zoxml2_zoxml2->doDefaultOption('Реквизит_'  . trim($item_value->Наименование),$output,$data,$item_value->Значение);
              }
            }
          continue;
          }
        $output = $this->model_zoxml2_zoxml2->doDefaultOption(trim($option_key),$output,$data,$value);
        }
      foreach($item->attributes() as $key => $value) {
        if ($key!=='available') $output = $this->model_zoxml2_zoxml2->doDefaultOption(trim("offer_" . $key), $output,$data,$value);
        }
      // --------------------------
      if (!empty($data['settings']['l_w_h_to_attr']) && empty($output['attr'][$data['settings']['l_w_h_to_attr']])) {
        if (!empty($output['length'])&&!empty($output['width'])&&!empty($output['height'])) {
          $l_w_h = str_replace ("{length}", (string)$output['length'], $data['settings']['l_w_h_template']);
          $l_w_h = str_replace ("{width}",  (string)$output['width'],  $l_w_h);
          $l_w_h = str_replace ("{height}", (string)$output['height'], $l_w_h);
          $output['attr'][$data['settings']['l_w_h_to_attr']] = $l_w_h;
          }
        }
      // --------------------------
      $output          = $this->model_zoxml2_zoxml2->doReplase($output,$data);
      // ЦЕНА
      if (!empty($output['price_use_rates'])) {
        if (isset($item->currencyId)) {
          $item_currencyId = trim($item->currencyId);   
          if (!empty($data['currencies'][$item_currencyId])) $output['price'] *= $data['currencies'][$item_currencyId];
          }
        }
      $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
      if (is_nan($output['price'])) {
        if (isset($data['module']['DEBUG'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Пропуск: фильтр цены") . "', user = '" . $this->db->escape($data['user']) . "'");
        continue;
        }
      if (isset($output['oldprice'])) {
        $output['oldprice'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['oldprice']);
        if (is_nan($output['oldprice'])) {
          if (isset($data['module']['DEBUG'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Пропуск: фильтр старой цены") . "', user = '" . $this->db->escape($data['user']) . "'");
          continue;
          }
        if ($output['oldprice'] > $output['price']) {
          $output['special']     = $output['price'];
          $output['price']       =  $output['oldprice'];
          }
        unset($output['oldprice']);
        }
      if (isset($data['settings']['price_table4mc']) && isset($output['mc_price'])) {
        $output['mc_price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['mc_price']);
        if (is_nan($output['mc_price'])) {
          if (isset($data['module']['DEBUG'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Пропуск: фильтр валютной цены") . "', user = '" . $this->db->escape($data['user']) . "'");
          continue;
          }
        }  
      $output = $this->model_zoxml2_zoxml2->doMeta($output,$data); // ШАБЛОНЫ
      // Блок проверок
      if (!$output['name']&&($data['settings']['link'] == 'name')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - name") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['sku']&&($data['settings']['link'] == 'sku')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - sku: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['mpn']&&($data['settings']['link'] == 'mpn')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - mpn: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['upc']&&($data['settings']['link'] == 'upc')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - upc: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['ean']&&($data['settings']['link'] == 'ean')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - ean: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['jan']&&($data['settings']['link'] == 'jan')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - jan: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['isbn']&&($data['settings']['link'] == 'isbn')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - isbn: ") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (!$output['model'] &&($data['settings']['link'] == 'model')) {
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Пропуск - model") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      // Обрабатываем запись
      usleep ($data['module']['sleep']);
      // ОТСЕЧКА
      if (isset($data['settings']['insert']) && (empty($output['manufacturer_id']) || empty($output['category_id']))) {
        if (isset($data['module']['DEBUG'])) {
          if (empty($output['manufacturer_id'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. manufacturer_id=0" . " Производитель: " . $output['vendor']) . "', user = '" . $this->db->escape($data['user']) . "'");
          if (empty($output['category_id']))     $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. category_id=0 (categoryId=" . $output['cat_id'] . ")(cat_name=" . $output['cat_name'] . ")")     . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      if (isset($data['settings']['update']) && 
        ((isset($data['settings']['update_category']) && empty($output['category_id'])) || 
         (isset($data['settings']['update_vendor']))  && empty($output['manufacturer_id'] ))) {
        if (isset($data['module']['DEBUG'])) {
          if (empty($output['manufacturer_id'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. manufacturer_id=0" . " Производитель: " . $output['vendor']) . "', user = '" . $this->db->escape($data['user']) . "'");
          if (empty($output['category_id']))     $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. category_id=0")     . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        continue; 
        }
      // ПОЛЬЗОВАТЕЛЬСКИЙ ФИЛЬТР
      if (!empty($data['settings']['user_filter'])) {
        $output_sku = $output['sku'];
        $output = $this->model_zoxml2_zoxml2filters->doUserFilter($output,$data,$data['settings']['user_filter'],$item);
        if (!is_array($output)) {
          $data['info_update'] += $output;
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output_sku . ". Пропуск. Пользовательский фильтр")     . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          } 
        }
      // СВЯЗАННЫЕ ОПЦИИ
      if (!empty($data['settings']['user_ro'])) {
        $output_sku = $output['sku'];
        $output = $this->model_zoxml2_zoxml2rofilters->doRoFilter($output,$data,$item);
        if (!$output) {
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output_sku . ". Пропуск. Связанные опции")     . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          } 
        }
      // Множественная привязка
      if (isset($data['settings']['link2category_ids']) && isset($item->categoryId)) {
        $i=0;
        foreach($item->categoryId as $value) {
          if ($i++) {
            $cat_id = $this->model_zoxml2_zoxml2->getCategoryByID($session_key,$value);
            if ($cat_id) $output['category_ids'][$cat_id] = $cat_id;
            }
          }
        }
      // Производитель и категория известны - обрабатываем товар
      $sql = $this->model_zoxml2_zoxml2->doSQL($output,$data,$output['manufacturer_id']);
      if (isset($data['module']['HARD_DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("doSQL: " . $this->db->escape($sql)) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
      $query = $this->db->query($sql);
      // ---------------------------------------------------------------------
      if ($query->row) { // Обновление продукта
        $product_id = $query->row['product_id'];
        if (!empty($query->row['zoxml2_permision_hold'])) { // ОБНОВЛЕНИЕ ПРОДУКТА ЗАПРЕЩЕНО!
          $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
          $sql .= " WHERE product_id = '" . (int)$product_id . "'";
  	      $this->db->query($sql);
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Запрещено обновление товара! product_id : " . (int)$product_id . " SKU: " . $query->row['sku']) . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          }
        $status     = $query->row['status'];
        if (count($query->rows)>1) {
//          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("sql запрос: " . $sql . " вернул более 1-й строки!") . "', user = '" . $this->db->escape($data['user']) . "'");
          $list_of_id = array();
          foreach ($query->rows as $next_row) $list_of_id[] = $next_row['product_id'];
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Список product_id: " . json_encode($list_of_id)) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        if (!isset($data['settings']['update'])) continue;
        if (isset($data['settings']['no_update'])&&$status==0) {
          if (isset($data['module']['DEBUG'])) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Пропуск: запрещено обновление отключенного товара") . "', user = '" . $this->db->escape($data['user']) . "'");
          continue;
          }
        // Обновление разрешено!
        // ПОЛЬЗОВАТЕЛЬСКИЙ АНАЛИЗАТОР
        if (!empty($data['settings']['update_analyzer'])) {
          $output = $this->model_zoxml2_zoxml2updateanalyzer->doUpdateAnalyzer($output,$data,$item,$query->row);
          if (!is_array($output)) {
            $data['info_update'] += $output;
            if (isset($data['module']['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. Пользовательский анализатор") . "', user = '" . $this->db->escape($data['user']) . "'");
              }
            continue;
            } 
          }
        // КАТЕГОРИЯ
        if (isset($data['settings']['update_category'])) {
          $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
          $this->model_zoxml2_zoxml2->link2category_id ($data['module']['engine'],$product_id,$output['category_id'],isset($data['module']['do_category_up']));
          if (isset($data['settings']['link2category_ids']) && count($output['category_ids'])) {
            $this->model_zoxml2_zoxml2->link2category_ids ($product_id,$output['category_ids'],isset($data['module']['do_category_up']));
            }
          }
        // ПРОДУКТ
        if ($data['settings']['hide']&&$output['quantity']<=0) $status=0;
        else                                                   $status=1;
        $sql = "UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW()";
        if (isset($data['settings']['update_price']))    {
          if (!empty($query->row['zoxml2_permision_price'])) { // ОБНОВЛЕНИЕ ЦЕНЫ ЗАПРЕЩЕНО
            if (isset($data['module']['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Запрещено обновление цены товара! product_id : " . (int)$product_id . " SKU: " . $query->row['sku']) . "', user = '" . $this->db->escape($data['user']) . "'");
              }
            }
          else {
            $sql .= ", price = '" . (float)$output['price'] . "'";
            if (isset($data['module']['costprice']) && isset($output['iprice']) ) { // Закупочная цена - поддержка только для https://extensions.myopencart.com/costprice-%D0%B7%D0%B0%D0%BA%D1%83%D0%BF%D0%BE%D1%87%D0%BD%D0%B0%D1%8F-%D1%86%D0%B5%D0%BD%D0%B0-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2-%D0%B2-opencart
              $sql .= ", cost = '" . $this->db->escape((float)$output['iprice']) . "'";
              }
            if (isset($data['module']['is_updated_by'])) {
              if (empty($output['updated_by'])) $sql .= ", updated_by = '" . $this->db->escape($output['supplier']) . "'"; 
              else                              $sql .= ", updated_by = '" . $this->db->escape($output['updated_by']) . "'"; 
              }
           // valuta_plus
            if (isset($data['module']['valuta_plus']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) { 
              $sql .= ", base_price = '" .          $this->db->escape((float)$output['mc_price']) . "'";
              $sql .= ", base_currency_code = '" .  $this->db->escape($output['mc_price_cur']) . "'";
              }
            }
          }
        if (isset($data['settings']['update_stock_status_id'])) $sql .= ", stock_status_id = '" . (int)$output['stock_status_id'] . "'";     
        if (isset($data['settings']['update_quantity']))        $sql .= ", quantity = '" . (int)$output['quantity'] . "'";     
        if (isset($data['settings']['update_ean']))       $sql .= ", ean = '" . $this->db->escape($output['ean']) . "'";
        if (isset($data['settings']['update_minimum']))   $sql .= ", minimum = '" . $this->db->escape($output['minimum']) . "'";
        if (isset($data['settings']['update_upc']))       $sql .= ", upc = '" . $this->db->escape($output['upc']) . "'";
        if (isset($data['settings']['update_jan']))       $sql .= ", jan = '" . $this->db->escape($output['jan']) . "'";
        if (isset($data['settings']['update_isbn']))      $sql .= ", isbn = '" . $this->db->escape($output['isbn']) . "'";
        if (isset($data['settings']['update_mpn']))       $sql .= ", mpn = '" . $this->db->escape($output['mpn']) . "'";

        if (isset($data['settings']['update_vendor']))   $sql .= ", manufacturer_id = '" . (int)$output['manufacturer_id'] . "'";
        if (isset($data['settings']['update_sku']))      $sql .= ", sku = '" . $this->db->escape($output['sku']) . "'";
        if (isset($data['settings']['update_model']))    $sql .= ", model = '" . $this->db->escape($output['model']) . "'";
        if (isset($data['settings']['update_weight']))   $sql .= ", weight_class_id = '" . $this->db->escape($output['weight_class_id']) . "', weight = '" . $this->db->escape($output['weight']) . "'";
        if (isset($data['settings']['update_l_w_h']))    $sql .= ", length_class_id = '" . $this->db->escape($output['length_class_id']) . "', length = '" . $this->db->escape($output['length']) . "', width = '" . $this->db->escape($output['width']) . "', height = '" . $this->db->escape($output['height']) . "'";

        $sql .= " WHERE product_id = '" . (int)$product_id . "'";
        if (isset($data['module']['HARD_DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("updateSQL: " . $this->db->escape($sql)) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
	      $this->db->query($sql);
        // Акции
        $special_id = array(); 
        if (isset($data['settings']['update_special'])) {
    	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
          if (isset($output['special'])) {
            foreach ($data['customer_groups'] as $customer_group_id) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
              $special_id[] = $this->db->getLastId(); 
              }
            }
          }
        // Цены
        if (isset($data['settings']['update_price'])) {
          if (!empty($query->row['zoxml2_permision_price'])) { // ОБНОВЛЕНИЕ ЦЕНЫ ЗАПРЕЩЕНО
            if (isset($data['module']['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Запрещено обновление цены товара! product_id : " . (int)$product_id . " SKU: " . $query->row['sku']) . "', user = '" . $this->db->escape($data['user']) . "'");
              }
            }
          else {
           // Валютная цена
          if ( isset($data['module']['mcg2']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) {
            if ($output['mc_price_cur']!=$this->config->get('config_currency')) { 
  		        $mc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['mc_price_cur']) . "'");
              if ($mc_query->row) {
                $currency_id   = $mc_query->row['currency_id'];
             		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr  WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr  SET   product_id = '" . (int)$product_id . "', price = '" .    (float)$output['mc_price'] . "', currency_id = '" . (int)$currency_id . "'");
                if (isset($output['special'])&&isset($data['settings']['update_special'])&&count($special_id)) {
            		  $this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr_special  WHERE product_id = '" . (int)$product_id . "'");
                  foreach ($special_id as $next_special_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr_special SET product_special_id = '" . (int)$next_special_id . "', product_id = '" . (int)$product_id . "', mc_price = '" . (float)$output['special'] . "', currency_id = '" . (int)$currency_id . "'");
                    }
                  }
                }
              }
            }
           // Закупочная цена
          if ( isset($data['module']['mcg2']) && isset($output['iprice'])) {
            if (!isset($output['iprice_cur'])) $output['iprice_cur'] = $this->config->get('config_currency');
  		        $mc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['iprice_cur']) . "'");
              if ($mc_query->row) {
                $currency_id   = $mc_query->row['currency_id'];
             		$this->db->query("DELETE FROM " . DB_PREFIX . "product_input_price  WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_input_price  SET   product_id = '" . (int)$product_id . "', iprice = '" .   (float)$output['iprice'] . "', currency_id = '" . (int)$currency_id . "'");
                }
              }
            }
          }
        // МЕТА
        if (isset($data['settings']['update_name']) && $data['settings']['language']!=0) {
		      $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) ."' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$data['settings']['language'] . "'");
          }
        if (isset($data['settings']['update_description']) && $data['settings']['language']!=0) {
		      $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$data['settings']['language'] . "'");
          }
        if ($data['settings']['images']!='nop' && isset($data['settings']['update_image']) && isset($output['image'][0]) && !isset($cache_main_image[$product_id])) {
          $cache_main_image[$product_id] = true; // записываем ID товара у которого обновлено главное изображение
          $data = $this->model_zoxml2_zoxml2->loadImage ($output['image'][0], $data, $output['category_id']);
		      $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), image  = '" . $this->db->escape($data['dest_image']) . "' WHERE product_id = '" . (int)$product_id . "'");
          if ($data['settings']['images']=='all') {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
            if (isset($output['image'][1])) {
              $order = 0;
              foreach ($output['image'] as $next_img) {
                if (defined('zoxml2_max_images') && $order==zoxml2_max_images) break; 
                if (!$order++) continue;
                $data = $this->model_zoxml2_zoxml2->loadImage ($next_img, $data, $output['category_id']);
                if ($data['dest_image']) $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($data['dest_image']) . "', sort_order = '" . (int)($order-1) . "'");
                }
              }
            }
          }
        if (isset($data['settings']['update_atributes']) && $data['settings']['language']!=0) {
          // ВАЖНО - удаляем только атрибуты указанного языка
          $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$data['settings']['language'] . "' AND product_id = '" . (int)$product_id . "'");
          foreach ($output['attr'] as $attribute_id => $text) {
            $text = trim($text); 
            if (!$text||!$attribute_id) continue;
  					$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
            }
          }
        $data['info_update'] ++;
        }
      else {             // Добавление продукта
        if (!isset($data['settings']['insert'])) continue;
        if ($output['quantity']<1&&isset($data['settings']['not_empty_only'])) continue;
        // Добавление разрешено!
        // ПОЛЬЗОВАТЕЛЬСКИЙ АНАЛИЗАТОР
        if (!empty($data['settings']['insert_analyzer'])) {
          $output = $this->model_zoxml2_zoxml2insertanalyzer->doInsertAnalyzer($output,$data,$item);
          if (!is_array($output)) {
            $data['info_insert'] += $output;
            if (isset($data['module']['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. Пользовательский анализатор") . "', user = '" . $this->db->escape($data['user']) . "'");
              }
            continue;
            } 
          }
        if (isset($data['WhiteListOfSku']) && !isset($data['WhiteListOfSku'][$output['sku']])) {
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск: WhiteListOfSku") . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          }
        if (isset($data['BlackListOfSku']) &&  isset($data['BlackListOfSku'][$output['sku']])) {
          if (isset($data['module']['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск: BlackListOfSku") . "', user = '" . $this->db->escape($data['user']) . "'");
            }
          continue;
          }
        // ---------------------------------  
        $data['dest_image'] = '';
        if ($data['settings']['images']!='nop' && isset($output['image'][0])) $data = $this->model_zoxml2_zoxml2->loadImage ($output['image'][0], $data, $output['category_id']);
        if ($output['quantity']<1&&$data['settings']['hide']) $status = 0;
        if (isset($data['settings']['hide_new'])) $status = 0;
        $noindex_new = '';
        if (isset($data['settings']['noindex_new']))   $noindex_new = "noindex = '1',"; 
        $supplier = '';
        if (isset($data['module']['is_supplier']))   $supplier = "supplier = '" . $this->db->escape($output['supplier']) . "',"; 
        if (isset($data['module']['is_updated_by'])) $supplier .= "updated_by = '" . $this->db->escape($output['supplier']) . "',"; 
        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET 
               model = '" .           $this->db->escape($output['model']) . "',
                        " .           $supplier . " 
                        " .           $noindex_new . " 
               sku = '" .             $this->db->escape($output['sku']) . "', 
               upc = '" .             $this->db->escape($output['upc']) . "', 
               ean = '" .             $this->db->escape($output['ean']) . "', 
               jan = '" .             $this->db->escape($output['jan']) . "', 
               isbn = '" .            $this->db->escape($output['isbn']) . "', 
               mpn = '" .             $this->db->escape($output['mpn']) . "', 
               location = '" .        $this->db->escape($output['location']) . "',
               quantity = '" .        (int)$output['quantity'] . "', 
               minimum = '" .         (int)$output['minimum'] . "', 
               subtract = '" .        (int)$output['subtract'] . "', 
               image  = '" .          $this->db->escape($data['dest_image']) . "',
               stock_status_id = '" . (int)$output['stock_status_id'] . "', 
               date_available = NOW(), 
               manufacturer_id = '" . (int)$output['manufacturer_id'] . "', 
               shipping =  '" .       (int)$output['shipping'] . "', 
               price = '" .           (float)$output['price'] . "', 
               points =   '" .        (int)$output['points'] . "', 
               weight = '" .          (float)$output['weight'] . "', 
               weight_class_id = '" . (int)$output['weight_class_id'] . "', 
               length = '" .          (float)$output['length'] . "', 
               width = '" .           (float)$output['width'] . "',  
               height = '" .          (float)$output['height'] . "', 
               length_class_id = '" . (int)$output['length_class_id'] . "',  
               status = '" .          (int)$status . "', 
               tax_class_id = '" .    (int)$output['tax_class_id'] . "',  
               sort_order = '0', 
               date_modified = NOW(),
               date_added = NOW()");
        $product_id = $this->db->getLastId();
        if ($data['dest_image']) $cache_main_image[$product_id] = true; // записываем ID товара у которого загружено главное изображение
        // - МУЛЬТИМЕРЧ
        if (isset($data['settings']['ms_seller'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "ms_product SET product_id = '" . (int)$product_id . "', seller_id = '" . (int)$data['settings']['ms_seller'] . "', product_status = '6', product_approved ='0' ");
          }
       // АКЦИИ
        $special_id = array();
        if (isset($output['special'])) {
          foreach ($data['customer_groups'] as $customer_group_id) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
            $special_id[] = $this->db->getLastId(); 
            }
          }
       // Закупочная цена (AlexDW)      
        if (isset($data['module']['costprice']) && isset($output['iprice']) ) { // Закупочная цена - поддержка для https://extensions.myopencart.com/costprice-%D0%B7%D0%B0%D0%BA%D1%83%D0%BF%D0%BE%D1%87%D0%BD%D0%B0%D1%8F-%D1%86%D0%B5%D0%BD%D0%B0-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2-%D0%B2-opencart
          $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
          $sql .= ", cost = '" . $this->db->escape((float)$output['iprice']) . "'";
          $sql .= " WHERE product_id = '" . (int)$product_id . "'";
	        $this->db->query($sql);
          }
       // valuta_plus                                           -
        if (isset($data['module']['valuta_plus']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) { 
          $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
          $sql .= ", base_price = '" .          $this->db->escape((float)$output['mc_price']) . "'";
          $sql .= ", base_currency_code = '" .  $this->db->escape($output['mc_price_cur']) . "'";
          $sql .= " WHERE product_id = '" . (int)$product_id . "'";
	        $this->db->query($sql);
          }
       // Валютная цена
        if ( isset($data['module']['mcg2']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) {
          if ($output['mc_price_cur']!=$this->config->get('config_currency')) {
		        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['mc_price_cur']) . "'");
            if ($query->row) {
              $currency_id   = $query->row['currency_id'];
//           		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr  WHERE product_id = '" . (int)$product_id . "'");
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr  SET   product_id = '" . (int)$product_id . "', price = '" .    (float)$output['mc_price'] . "', currency_id = '" . (int)$currency_id . "'");
              if (isset($output['special'])&&count($special_id)) {
//          		  $this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr_special  WHERE product_id = '" . (int)$product_id . "'");
                foreach ($special_id as $next_special_id) {
                  $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr_special SET product_special_id = '" . (int)$next_special_id . "', product_id = '" . (int)$product_id . "', mc_price = '" . (float)$output['special'] . "', currency_id = '" . (int)$currency_id . "'");
                  }
                }
              }
            }
          }
         // Закупочная цена
        if ( isset($data['module']['mcg2']) && isset($output['iprice'])) {
          if (!isset($output['iprice_cur'])) $output['iprice_cur'] = $this->config->get('config_currency');
		        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['iprice_cur']) . "'");
            if ($query->row) {
              $currency_id   = $query->row['currency_id'];
//           		$this->db->query("DELETE FROM " . DB_PREFIX . "product_input_price  WHERE product_id = '" . (int)$product_id . "'");
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_input_price  SET   product_id = '" . (int)$product_id . "', iprice = '" .   (float)$output['iprice'] . "', currency_id = '" . (int)$currency_id . "'");
              }
          }
        // Лог новых товаров
        if (isset($data['settings']['log_new'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new product', data = '" . $this->db->escape('ID: ' . $product_id . ' SKU: ' . $output['sku'] . ' Model: ' . $output['model'] ) ."'");
          }
        // Атрибуты
        foreach ($output['attr'] as $attribute_id => $text) {
          $text = trim($text); 
          if (!$text||!$attribute_id) continue;
          if ($data['settings']['language']!=0 && !isset($data['settings']['all_languages'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
            }
          if (isset($data['settings']['all_languages'])) {
            foreach ($data['languages'] as $language_id => $language_name) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($text) . "'");
              }
            }
          }
        // дополнительные изображения
        if ($data['settings']['images']=='all') {
          $order = 0;
          foreach ($output['image'] as $next_img) {
            if (defined('zoxml2_max_images') && $order==zoxml2_max_images) break; 
            if (!$order++) continue;
            $data = $this->model_zoxml2_zoxml2->loadImage ($next_img, $data, $output['category_id']);
            if ($data['dest_image']) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($data['dest_image']) . "', sort_order = '" . (int)($order-1) . "'");
              }
            }
          }
        // Описание товара
        if ($data['settings']['language']!=0 && !isset($data['settings']['all_languages'])) {
          $sql = "INSERT INTO " . DB_PREFIX . "product_description SET 
                  product_id = '" .       (int)$product_id . "', 
                  language_id = '" .      (int)$data['settings']['language'] . "', 
                  name = '" .             $this->db->escape($output['name']) . "',";
          if (isset($output['meta_keyword']))     $sql .= "meta_keyword = '" . $this->db->escape($output['meta_keyword']) . "',";         
          if (isset($output['meta_description'])) $sql .= "meta_description = '" . $this->db->escape($output['meta_description']) . "',";         
          if (isset($output['meta_title']))       $sql .= "meta_title = '" . $this->db->escape($output['meta_title']) . "',";         
          if (isset($output['meta_h1']))          $sql .= "meta_h1 = '" . $this->db->escape($output['meta_h1']) . "',";         
          if (isset($output['seo_title']))        $sql .= "seo_title = '" . $this->db->escape($output['seo_title']) . "',";         
          if (isset($output['seo_h1']))           $sql .= "seo_h1 = '" . $this->db->escape($output['seo_h1']) . "',";         
          if (isset($output['tag']))              $sql .= "tag = '" . $this->db->escape($output['tag']) . "',"; 
          $sql .= "description = '" .      $this->db->escape($output['description']) . "'";        
          $this->db->query($sql);
          }
        if (isset($data['settings']['all_languages'])) {
          foreach ($data['languages'] as $language_id => $language_name) {
            $sql = "INSERT INTO " . DB_PREFIX . "product_description SET 
                    product_id = '" .       (int)$product_id . "', 
                    language_id = '" .      (int)$language_id . "', 
                    name = '" .             $this->db->escape($output['name']) . "',";
            if (isset($output['meta_keyword']))     $sql .= "meta_keyword = '" . $this->db->escape($output['meta_keyword']) . "',";         
            if (isset($output['meta_description'])) $sql .= "meta_description = '" . $this->db->escape($output['meta_description']) . "',";         
            if (isset($output['meta_title']))       $sql .= "meta_title = '" . $this->db->escape($output['meta_title']) . "',";         
            if (isset($output['meta_h1']))          $sql .= "meta_h1 = '" . $this->db->escape($output['meta_h1']) . "',";         
            if (isset($output['tag']))              $sql .= "tag = '" . $this->db->escape($output['tag']) . "',"; 
            $sql .= "description = '" .      $this->db->escape($output['description']) . "'";        
            $this->db->query($sql);
            }
          }

        $stores = array ();
        if (!empty($data['settings']['stores'])) {
          if ($data['settings']['stores']=="all") $stores = $data['all_stores'];
          else $stores[$data['settings']['stores']] = $data['settings']['stores']; 
          }
        else $stores[0] = 0;

        foreach ($stores as $store_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET 
                product_id = '" .     (int)$product_id . "', 
                store_id = '" .       (int)$store_id . "'");
                }

        $this->model_zoxml2_zoxml2->link2category_id ($data['module']['engine'],$product_id,$output['category_id'],isset($data['module']['do_category_up'])?true:false);
        if (isset($data['settings']['link2category_ids']) && count($output['category_ids'])) {
          $this->model_zoxml2_zoxml2->link2category_ids ($product_id,$output['category_ids']);
          }
        // SEO URL
        $this->model_zoxml2_zoxml2->doSeoUrl($data,$output,$product_id);
        $data['info_insert'] ++;
        }
      // СТРАНА
      if (!empty($output['country']) && isset($data['module']['mcg2'])) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_country_of_origin      WHERE product_id = '" . (int)$product_id . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_country_of_origin SET product_id = '" . (int)$product_id . "', country = '" .  $this->db->escape($output['country']) . "'");
        }
      // ПОЛЬЗОВАТЕЛЬСКИЙ СКРИТП
      if (!empty($data['settings']['update_use_script'])) {
        $this->model_zoxml2_zoxml2usescript->doUserScript($output,$data,$data['settings']['update_use_script'],$item,$product_id);
        }
      if (!empty($data['settings']['user_ro'])) {
        $this->model_zoxml2_zoxml2ro->saveROptions ($output,$product_id, $data);
        }
      if (isset($data['module']['mf_plus']) && $this->config->get( 'mfilter_plus_version' )) Mfilter_Plus::getInstance($this)->updateProduct($product_id);
      }
    // Общие действия
    if (!empty($data['settings']['user_after'])) {
      $this->model_zoxml2_zoxml2after->doUserAfter($data);
      }
    // ПОСТ-ДЕЙСИТВИЯ  
    if ($data['settings']['hide']) {
      $sql = "UPDATE " . DB_PREFIX . "product SET status = '0'";
      $where = $this->model_zoxml2_zoxml2->getContext ($data);
      $where .= " AND quantity < '1'";
      $this->db->query ($sql . $where);
      }
    // - МУЛЬТИМЕРЧ
    if (isset($data['settings']['ms_seller'])) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET status = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "ms_product WHERE product_approved='0')");
      }
    if (!empty($data['settings']['hide_by_attribute'])) {
      $where = $this->model_zoxml2_zoxml2->getContext ($data);
      $where .= " AND product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$data['settings']['hide_by_attribute'] . "')";
      $sql = "UPDATE " . DB_PREFIX . "product SET status = '0', date_modified = NOW() " . $where;
      $this->db->query($sql);
      }
    if (isset($data['settings']['hide_missing']) && isset($data['process_start_time'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Отключаем пропущенные товары') ."', user = '" . $this->db->escape($data['user']) . "'");
      }
    if (isset($data['settings']['zero_missing']) && isset($data['process_start_time'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Обнуляем пропущенные товары') ."', user = '" . $this->db->escape($data['user']) . "'");
      }
    if ((isset($data['settings']['hide_missing']) || isset($data['settings']['zero_missing'])) && isset($data['process_start_time'])) {
      $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
      if (isset($data['settings']['hide_missing'])) $sql .= ", status = '0'"; 
      if (isset($data['settings']['zero_missing'])) $sql .= ", quantity = '0'"; 
      $where = $this->model_zoxml2_zoxml2->getContext ($data);
      $where .= " AND date_modified < '" . $this->db->escape($data['process_start_time']) . "'";
      $this->db->query ($sql . $where);
      }
    // Завершающее сообщение
    $txt = "Обработано: " . $info_processed;
    $txt .= "  Добавлено: " . $data['info_insert'];
    $txt .= "  Обновлено: " . $data['info_update'];
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
	  $this->cache->delete('ocfilter');
	  $this->cache->delete('product');
	  $this->cache->delete('seo_pro');
    // Общие действия - конец
    }

	public function link() {
    $json   = array();
    $json[] = "scan: You do not have permission to access this page!";

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if (isset($this->request->post['session_key'])) {
        if (empty($this->request->post['url'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'error', data = 'YML - осутствует URL!', user = '" . $this->db->escape($this->request->post['user']) . "'");
          }
        else {
          $url = $this->request->post['url']; 
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'info', data = '" . $this->db->escape('YML - начал загружать фид ' . $url) ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
          $this->load->model('zoxml2/zoxml2');
          $data = $this->model_zoxml2_zoxml2->getDefSettings ($this->request->post['session_key'],$this->request->post['user']);
          $this->do_link($data,$url,$this->request->post['session_key'],$this->request->post['user']);
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['session_key']) . "', type = 'end', data = '" . $this->db->escape('YML - загрузка завершена.') ."', user = '" . $this->db->escape($this->request->post['user']) . "'");
          }
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = '" . $this->db->escape(json_encode($this->request->post)) . "', user = 'POST: unknown!'");
        }
      }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

	public function do_link($data,$url,$session_key,$user) {
    libxml_use_internal_errors(true);
    $xml = $this->model_zoxml2_zoxml2->getXML ($data);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Создание XML-объекта завершено') ."', user = '" . $this->db->escape($user) . "'");
    if (!$xml) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной файл!') ."', user = '" . $this->db->escape($user) . "'");
      foreach(libxml_get_errors() as $error) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($user) . "'");
        }
      return null; 
      } 
    if ($data['settings']['tag_shop']&&!isset($xml->{$data['settings']['tag_shop']}))        { 
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: файл не является YML-файлом! Тег <shop> не найден!') ."', user = '" . $this->db->escape($user) . "'");
      return null; 
      }
    // ------
    if ($data['settings']['tag_shop']) $info_total = count($xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});    
    else {
      if ($data['settings']['tag_offers']) $info_total = count($xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']});
      else                                 $info_total = count($xml->{$data['settings']['tag_offer']});
      }

    $info_processed         = 0;    
    $info_progress          = 0;    
    $info_linked            = 0;    
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");

    if ($data['settings']['tag_shop']) $offers = $xml->{$data['settings']['tag_shop']}->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
    else {
      if ($data['settings']['tag_offers']) $offers = $xml->{$data['settings']['tag_offers']}->{$data['settings']['tag_offer']};
      else                                 $offers = $xml->{$data['settings']['tag_offer']};
      }
    foreach ($offers as $item) {
      if ($info_progress==$data['module']['step']) {
        set_time_limit(20);
        $info_progress = 0; 
        $txt = "Обработано: " . $info_processed;
        $txt .= "  Привязано: " . $info_linked;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        }
      // Обрабатываем запись
      usleep ($data['module']['sleep']);
      $info_processed ++;
      if (isset($data['module']['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("Товар №: " . $info_processed . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
        }
      $info_progress  ++;
      $sku                    = isset($item->vendorCode) ?(string)$item->vendorCode:'';  
      $model                  = isset($item->model) ?(string)$item->model:'';  
      $vendor                 = isset($item->vendor)?(string)$item->vendor:'';  
      $name                   = isset($item->name)  ?(string)$item->name:'';  
      //  param_name & param_value  
      foreach ($item as $key => $value) {
        $option_key = $key;
        if ($key=='params') if (isset($value['name'])) $option_key = 'params_' . $value['name'];   
        if ($key=='param')  if (isset($value['name'])) $option_key = 'param_'  . $value['name'];   
        if (!isset($data['options'][$option_key])) continue; 
        switch ($data['options'][$option_key]['dest_type']) {
          case 'vendor':        { $vendor = (string)$value; break; } 
          case 'model':         { $model  = (string)$value; break; } 
          case 'sku':           { $sku    = (string)$value; break; } 
          case 'name':          { $name   = (string)$value; break; } 
          case 'mpn':           { $mpn    = (string)$value; break; }                                    
          case 'upc':           { $upc    = (string)$value; break; } 
          case 'ean':           { $ean    = (string)$value; break; } 
          case 'jan':           { $jan    = (string)$value; break; } 
          case 'isbn':          { $isbn   = (string)$value; break; } 
          }
        }
      if (isset($data['options']['host'])&&$data['options']['host']['dest_type']=='vendor') $vendor = 'host';

      foreach($item->attributes() as $key => $value) {
        if ($key!=='available') {
          $option_key = "offer_" . trim($key);
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='sku')   $sku      = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='mpn')   $mpn      = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='upc')   $upc      = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='ean')   $ean      = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='jan')   $jan      = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='isbn')  $isbn     = isset($item['id'])?(string)$item['id']:0;
          if (isset($data['options']['offer_id'])&&$data['options'][$option_key]['dest_type']=='model') $model    = isset($item['id'])?(string)$item['id']:0;
          }
        }


      $manufacturer_id = $this->model_zoxml2_zoxml2->getVendor($session_key,$vendor);
     // Формируем поисковый запрос
      $where = '';
      $sql   = "SELECT * FROM " . DB_PREFIX . "product p ";
//      $sql .= " LEFT JOIN  " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
      if ($data['settings']['link']=='name') {
        $sql .= " LEFT JOIN  " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
        }
      if ($data['settings']['link'] == 'sku') {
        if (!$sku) continue;
        if ($where) $where .= " AND ";
//        $where .= "p.sku = '" . $this->db->escape($sku) . "'";
        $where .= "p.sku = '" . $this->db->escape(trim($sku)) . "'";
        }
      if ($data['settings']['link'] == 'mpn') {
        if (!$mpn) continue;
        if ($where) $where .= " AND ";
        $where .= "p.mpn = '" . $this->db->escape($mpn) . "'";
        }
      if ($data['settings']['link'] == 'upc') {
        if (!$upc) continue;
        if ($where) $where .= " AND ";
        $where .= "p.upc = '" . $this->db->escape($upc) . "'";
        }
      if ($data['settings']['link'] == 'ean') {
        if (!$ean) continue;
        if ($where) $where .= " AND ";
        $where .= "p.ean = '" . $this->db->escape($ean) . "'";
        }
      if ($data['settings']['link'] == 'jan') {
        if (!$jan) continue;
        if ($where) $where .= " AND ";
        $where .= "p.jan = '" . $this->db->escape($jan) . "'";
        }
      if ($data['settings']['link'] == 'isbn') {
        if (!$isbn) continue;
        if ($where) $where .= " AND ";
        $where .= "p.isbn = '" . $this->db->escape($isbn) . "'";
        }
      if ($data['settings']['link'] == 'name') {
        if (!$name) continue;
        if ($where) $where .= " AND ";
        $where .= "pd.name = '" . $this->db->escape($name) . "'";
        $where .= " AND pd.language_id = '" . (int)$data['settings']['language'] . "'";
        }
      if ($data['settings']['link'] == 'model') {
        if (!$model) continue;
        if ($where) $where .= " AND ";
        $where .= "p.model = '" . $this->db->escape($model) . "'";
        }
      if (isset($data['settings']['link_vendor'])) {
        if ($where) $where .= " AND ";
        $where .= "p.manufacturer_id = '" . $this->db->escape($manufacturer_id) . "'";
        }
      $sql .= " WHERE " . $where . " GROUP BY p.product_id";
      $query = $this->db->query($sql);
      // ---------------------------------------------------------------------
      if ($query->row) { // Привязка продукта
        if (count($query->rows)>1) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("sql запрос: " . $sql . " вернул более 1-й строки!") . "', user = '" . $this->db->escape($user) . "'");
          }
        foreach ($query->rows as $value) {
          $product_id = $value['product_id'];
          if ($data['settings']['supplier']=='location') {  
            if ($value['location']&&$value['location']!=$session_key) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("Товар: " . $name . " уже привязан к другому поставщику! пропуск...") . "', user = '" . $this->db->escape($user) . "'");
              }
            else {
  		        $this->db->query("UPDATE " . DB_PREFIX . "product SET location = '" . $this->db->escape($session_key) . "' WHERE product_id = '" . (int)$product_id . "'");
              $info_linked ++;
              }
            } 
          if ($data['settings']['supplier']=='mpn') {  
            if ($value['mpn']&&$value['mpn']!=$session_key) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("Товар: " . $name . " уже привязан к другому поставщику! пропуск...") . "', user = '" . $this->db->escape($user) . "'");
              }
            else {
  		        $this->db->query("UPDATE " . DB_PREFIX . "product SET mpn = '" . $this->db->escape($session_key) . "' WHERE product_id = '" . (int)$product_id . "'");
              $info_linked ++;
              }
            } 
          if ($data['settings']['supplier']=='supplier') {  
            if ($value['supplier']&&$value['supplier']!=$session_key) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("Товар: " . $name . " уже привязан к другому поставщику! пропуск...") . "', user = '" . $this->db->escape($user) . "'");
              }
            else {
  		        $this->db->query("UPDATE " . DB_PREFIX . "product SET supplier = '" . $this->db->escape($session_key) . "' WHERE product_id = '" . (int)$product_id . "'");
              $info_linked ++;
              }
            } 
          if ($data['settings']['supplier']=='mcg2') {
            // Доделать!
            } 
          }
        }
      else { // Артикул не найден
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("sql запрос: " . $sql . " вернул 0 строк!") . "', user = '" . $this->db->escape($user) . "'");
        }
      }
    // Завершающее сообщение
    $txt = "Обработано: " . $info_processed;
    $txt .= "  Привязано: " . $info_linked;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    }


}
?>