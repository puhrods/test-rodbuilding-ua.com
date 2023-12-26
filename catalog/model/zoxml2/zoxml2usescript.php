<?php
class ModelZoXml2ZoXml2UseScript extends Model {

public function doUserScript($output,$data,$index,$item,$product_id) {
  $parts = explode (';',$index);
  foreach ($parts as $next_index) {
    $parts2 = explode (':',$next_index);
    if (isset($parts2[1]))   $this->old_doUserScript($output,$data,trim($parts2[0]),$item,$product_id,trim($parts2[1]));
    else                     $this->old_doUserScript($output,$data,$next_index,$item,$product_id,'');
    }
  }
public function old_doUserScript($output,$data,$index,$item,$product_id,$param) {
if ($index=='stores_to_multistore') { 
  foreach ($item as $option_key => $value) {
    if (!empty($data['stores_to_multistore'][trim($option_key)])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_multistore SET `code` = '" . $this->db->escape($param) . "', product_id = '" . (int)$product_id . "', multistore_id = '" . (int)$data['stores_to_multistore'][trim($option_key)] . "', quantity = '" . (int)$value . "'");
      }
    }
  // СУММИРОВАНИЕ
  if ($param) {
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_to_multistore WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), " . $param . " = '" . $this->db->escape("присутствует") . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    }
  }
if ($index=='hpmodel_links') {
  if (!empty($output['ean'])) {
    $query = $this->db->query ("SELECT * FROM " . DB_PREFIX . "hpmodel_links WHERE product_id = '" . (int)$product_id . "'");
    if (!$query->row) {
      // ТОВАРА НЕТ В СВЯЗКАХ! ИЩЕМ ЕГО РОДИТЕЛЯ
       $query = $this->db->query ("SELECT * FROM " . DB_PREFIX . "hpmodel_links WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product WHERE ean = '" . $this->db->escape($output['ean']) . "')");
       if (!$query->row) {
        // ЭТО ПЕРВЫЙ ТОВАР В СВЯЗКЕ
        $this->db->query("INSERT INTO " . DB_PREFIX . "hpmodel_links SET product_id = '" . (int)$product_id . "', parent_id = '" . (int)$product_id . "'");
        }
      else {
        // ДОБАВЛЯЕМ ТОВАР В СВЯЗКУ
        $this->db->query("INSERT INTO " . DB_PREFIX . "hpmodel_links SET product_id = '" . (int)$product_id . "', parent_id = '" . (int)$query->row['parent_id'] . "'");
        }
      }
    }
  }
if ($index=='woodville_special') {
  if (isset($data['settings']['update_special'])) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
    // ИЩЕМ АКЦИЮ
    $special_price = 0;
    $special_start = '';
    $special_end   = '';
    if (isset($item->param))  {
      foreach ($item->param  as $item_value) {
        if (isset($item_value['name'])) {
          if ($item_value['name']=="Акция Цена")   { $special_price = trim($item_value); }
          if ($item_value['name']=="Акция начало") { $special_start = trim($item_value); }
          if ($item_value['name']=="Акция конец")  { $special_end   = trim($item_value); }
          }
        }
      }
    // СОХРАНЯЕМ АКЦИЮ
    if ($special_price>0) {
    // Нужен формат 2019-01-09
    // Приходит: 01.03.2019 0:00:00
$date = new DateTime($special_start);
$special_start = $date->format('Y-m-d');
$date = new DateTime($special_end);
$special_end = $date->format('Y-m-d');
//$outtext = "АКЦИЯ:" . $special_price . "," . $special_start . "," . $special_end;
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_output', data = '" . $this->db->escape($outtext) . "', user = '" . $this->db->escape($data['user']) . "'");    
      foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$special_price . "', date_start = '" . $this->db->escape($special_start) . "', date_end = '" . $this->db->escape($special_end) . "'");
        }
      }
    }
  }
if ($index=='avtomarketomsk_date_added') {
  if (!empty($item->Дата)) $this->db->query("UPDATE " . DB_PREFIX . "product SET date_added = '" . $this->db->escape($item->Дата) . "' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='Igor1987') {
	 $this->db->query("UPDATE " . DB_PREFIX . "product_description SET meta_description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='Denyelle') {
	 $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '" . $this->db->escape($output['jan']) . "', minimum_upakovka = '" . (int)$output['minimum_upakovka'] . "', minimum_promo = '" . (int)$output['minimum_promo'] . "', ean = '" . $this->db->escape($output['ean']) . "', barcode = '" . $this->db->escape($output['barcode']) . "', quantity_vputi = '" . (int)$output['quantity_vputi'] . "', quantity_pod_zakaz = '" . (int)$output['quantity_pod_zakaz'] . "',date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
	 $this->db->query("UPDATE " . DB_PREFIX . "product_description SET atribute_text = '" . $this->db->escape($output['atribute_text']) . "', description_full = '" . $this->db->escape($output['description_full']) . "' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='atributes_1st2upp') {
	$this->db->query("UPDATE " . DB_PREFIX . "product SET upc = '" . $this->db->escape($output['sku']) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
  $this->db->query("UPDATE " . DB_PREFIX . "product_attribute SET `text` = CONCAT(UCASE(LEFT(`text`, 1)), LCASE(SUBSTRING(`text`, 2))) WHERE product_id = '" . (int)$product_id . "'");
  $this->db->query("UPDATE " . DB_PREFIX . "product_special SET `date_end`=DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE product_id = '" . (int)$product_id . "'");
  // теперь добавляем все данные их RO в теги
  $tags = array();
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
  foreach ($query->rows as $ro) {
    if (!empty($ro['sku']))       $tags[trim($ro['sku'])] = TRUE;
    if (!empty($ro['model']))     $tags[trim($ro['model'])] = TRUE;
    if (!empty($ro['upc']))       $tags[trim($ro['upc'])] = TRUE;
    if (!empty($ro['ean']))       $tags[trim($ro['ean'])] = TRUE;
    if (!empty($ro['location']))  $tags[trim($ro['location'])] = TRUE;
    }
  $tag = '';
  foreach ($tags as $key => $value) $tag .= trim($key) . ',';
  $this->db->query("UPDATE " . DB_PREFIX . "product_description SET tag = '" . $this->db->escape($tag) . "' WHERE product_id = '" . (int)$product_id . "'");
  return;
  }
if ($index=='keep_original_url') {
  if (!empty($item->url)) {
    $output['url_tpl'] = array_shift(explode('.',array_pop(explode ('/',$item->url))));
    if (isset($data['module']['url_alias'])) $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
    if (isset($data['module']['seo_url']))   $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url   WHERE query = 'product_id=" . (int)$product_id. "'");
    $this->model_zoxml2_zoxml2->doSeoUrl($data,$output,$product_id);
    }
  return;
  }
if ($index=='sport-hunt') {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
  if ($query->row['quantity']<1 && (empty($query->row['image']) || $query->row['image']=="catalog/0-no-photo.png" )) {
    // ТОВАР БЕЗ ФОТО И ОСТАТКА - ОТКЛЮЧАЕМ!
	 $this->db->query("UPDATE " . DB_PREFIX . "product SET status = '0', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
    } 
  return;
  }
if ($index=='7km_shipper') {
  $shipper_id = 0; 
  if (!empty($item->vendor)) {
    $shipper = trim($item->vendor);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipper WHERE name = '" . $this->db->escape($shipper) . "'"); 
    if ($query->row) {// Такой shipper есть
      $shipper_id = $query->row['shipper_id'];
      }
    else { // Новый - добавляем!
			$this->db->query("INSERT INTO " . DB_PREFIX . "shipper SET name = '" . $this->db->escape($shipper) . "'");
		  $shipper_id = $this->db->getLastId();
			$this->db->query("INSERT INTO " . DB_PREFIX . "shipper_to_store SET shipper_id = '" . (int)$shipper_id . "', store_id = '0'");
      foreach ($data['languages'] as $language_id => $language_name) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "shipper_description SET shipper_id = '" . (int)$shipper_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($shipper) . "'");
        }
      }
    }
	$this->db->query("UPDATE " . DB_PREFIX . "product SET shipper_id = '" . (int)$shipper_id . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
  return;
  }
if ($index=='newvay.ru') {
  $variant     = array (
    'options'                   => array(),
    'sku'                       => $item['id'],
    'ean'                       => $output['ean'],
    'model'                     => htmlspecialchars(trim($item->name)),
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => '=',
    'price'                     => $output['price'],
    'stock_status_id'           => 5,
    );
  // РАЗБОР ОПЦИЙ
  if (isset($item->param))  {
    foreach ($item->param  as $item_value) {
      if (isset($item_value['name'])) {
        $option_key = 'param_'  . $item_value['name'];
        if($data['options'][$option_key]['dest_type'] != 'option') continue;
        if($data['options'][$option_key]['dest_id'] == 0) continue;
        if ($option_key == 'param_Справочник Цвет' || $option_key == 'param_Справочник Размер') {
          $variant['options'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => trim($item_value),
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      }
    }
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    // RO
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // ОПЦИИ
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', model = '" . $this->db->escape($variant['model']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price, MAX(price) as max_price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price, MAX(price) as max_price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }

if ($index=='zoloto') {
  $ro_model    = $output['model'];
  $variant     = array (
    'options'                   => $output['option'],
    'sku'                       => '',
    'ean'                       => '',
    'model'                     => $ro_model,
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => '=',
    'price'                     => 0,
    'stock_status_id'           => 5,
    );
  // РАЗБОР ОПЦИЙ
  $output['option'] = array();
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    // RO
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // ОПЦИИ
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', sku = '" . $this->db->escape($variant['sku']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
if ($index=='eroticfantasy') {
  if (isset($output['KeepStatus'])&&$output['KeepStatus']==0) {
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), status = '0' WHERE product_id = '" . (int)$product_id . "'");
    }
  $variant     = array (
    'options'                   => array(),
    'sku'                       => $output['model'],
    'ean'                       => $output['ean'],
    'model'                     => $output['model'],
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => '=',
    'price'                     => $output['price'],
    'stock_status_id'           => 5,
    );
  // РАЗБОР ОПЦИЙ
  if (isset($item->Цена_по_акции)) {
    $variant['special'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->Цена_по_акции);
    if (is_nan($variant['special'])) return NULL;
    }
  if (isset($item->Цвет)) {
    $option_key = 'Цвет';
    if($data['options'][$option_key]['dest_id']>0) {
      $variant['options'][] = array (
        'option_id'     => $data['options'][$option_key]['dest_id'], 
        'value'         => trim($item->Цвет),
        'required'      => 1,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 1,
        'quantity'      => $output['quantity'],
        );
      }
    }
  if (isset($item->Размер)) {
    $option_key = 'Размер';
    if($data['options'][$option_key]['dest_id']>0) {
      $variant['options'][] = array (
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => trim($item->Размер),
        'required'      => 1,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 1,
        'quantity'      => $output['quantity'],
        );
      }
    }
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    // RO
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'"); 
    if ($query->row) {
      $upc = $query->row['upc']==1?TRUE:FALSE;
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // АКЦИИ
      if (isset($variant['special'])&&isset($data['settings']['update_special'])) {
        foreach ($data['customer_groups'] as $customer_group_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_special SET   
            relatedoptions_id = '" . (int)$relatedoptions_id . "',      
            customer_group_id = '" . (int)$customer_group_id . "', 
            price           = '" . (float)$variant['special'] . "'");  
          }
        // Добавляем АКЦИЮ в стандартный контроллер
        $query = $this->db->query("SELECT MIN(price) as price FROM " . DB_PREFIX . "relatedoptions_special WHERE price!='0' AND relatedoptions_id = '" . (int)$relatedoptions_id . "'"); 
  	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
        foreach ($data['customer_groups'] as $customer_group_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$query->row['price'] . "'");
          }
        }
      // ОПЦИИ
      if (!$upc) $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      else { // ЗАПЛАТКА для кривых товаров - всем опциям указываем кол-во 1
        $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '1'  WHERE product_id = '" . (int)$product_id . "'");
        }
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', sku = '" . $this->db->escape($variant['sku']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price, MAX(price) as max_price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
      $mpn = $query->row['max_price']<3000?'':'Доставка: 0 ₽';
      if (isset($variant['special'])&&$variant['special']>0&&$variant['special']<3000) $mpn ='';
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), mpn = '" . $this->db->escape($mpn) . "', price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // АКЦИИ
    if (isset($variant['special'])&&isset($data['settings']['update_special'])) {
      foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_special SET   
          relatedoptions_id = '" . (int)$relatedoptions_id . "',      
          customer_group_id = '" . (int)$customer_group_id . "', 
          price           = '" . (float)$variant['special'] . "'");  
        }
      // Добавляем АКЦИЮ в стандартный контроллер
      $query = $this->db->query("SELECT MIN(price) as price FROM " . DB_PREFIX . "relatedoptions_special WHERE price!='0' AND relatedoptions_id = '" . (int)$relatedoptions_id . "'"); 
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
      foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$query->row['price'] . "'");
        }
      }
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price, MAX(price) as max_price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
    $mpn = $query->row['max_price']<3000?'':'Доставка: 0 ₽';
    if (isset($variant['special'])&&$variant['special']>0&&$variant['special']<3000) $mpn ='';
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), mpn = '" . $this->db->escape($mpn) . "', price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  return;
  }



if ($index=='level99') {
  $relatedoptions_variant_id = 6;
  $variant     = array (
    'options'                   => array(),
    'sku'                       => (string)isset($item['id'])?$item['id']:'',
    'model'                     => '',
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'price_prefix'              => '+',
    'price'                     => $output['price'],
    'stock_status_id'           => 5,
    );
  // РАЗБОР ОПЦИЙ
  if (isset($item->weight)) {
    $option_key = 'weight';
    if($data['options'][$option_key]['dest_id']>0) {
      $variant['options'][] = array (
        'option_id'     => 22, 
        'value'         => trim($item->weight),
        'required'      => 1,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 1,
        'quantity'      => $output['quantity'],
        );
      }
    }
  if (isset($item->taste)) {
    $option_key = 'taste';
    if($data['options'][$option_key]['dest_id']>0) {
      $variant['options'][] = array (
        'option_id'     => 16,
        'value'         => trim($item->taste),
        'required'      => 1,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 1,
        'quantity'      => $output['quantity'],
        );
      }
    }
  if (count($variant['options'])==2) {
    // RO
//    $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value           WHERE product_id = '" . (int)$product_id . "'");
//  	$this->db->query("DELETE FROM " . DB_PREFIX . "product_option                 WHERE product_id = '" . (int)$product_id . "'");

//    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "'");        
//    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_option          WHERE product_id = '" . (int)$product_id . "'");        
//    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions                 WHERE product_id = '" . (int)$product_id . "'");  

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим
      $this->saveOptions ($variant['options'],$product_id, $data, $query->row['relatedoptions_id']); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "', 
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
        // Сохранение опций
    $relatedoptions_id = $this->db->getLastId();
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
if ($index=='b2b.tyrrussia.ru') {
  $relatedoptions_variant_id = 5;
  $variant     = array (
    'options'                   => array(),
    'sku'                       => (string)isset($item['id'])?$item['id']:'',
    'model'                     => '',
    'location'                  => '',
    'quantity'                  => $data['settings']['quantity'],
    'price_prefix'              => '+',
    'price'                     => $output['price'],
    'stock_status_id'           => 5,
    );
  // РАЗБОР ОПЦИЙ
  if (isset($item->param ))  {
    foreach ($item->param  as $param ) {
      if ($param['name']=="Размер") {
        $variant['options'][] = array (
          'option_id'     => 13, 
          'value'         => (string)$param,
          'required'      => 1,
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => 1,
          'quantity'      => $data['settings']['quantity'],
          );
        }
      if ($param['name']=="Цвет") {
        $variant['options'][] = array (
          'option_id'     => 14, 
          'value'         => (string)$param,
          'required'      => 1,
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => 1,
          'quantity'      => $data['settings']['quantity'],
          );
        }
      }
    }
  if (count($variant['options'])==2) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим
      $this->saveOptions ($variant['options'],$product_id, $data, $query->row['relatedoptions_id']); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . (int)$variant['price'] . "', quantity = '" . (int)$variant['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
      if ($query->row['total']<1&&$data['settings']['hide']) $status = 0;
      else                                              $status = 1;
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', status = '" . (int)$status . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "', 
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
        // Сохранение опций
    $relatedoptions_id = $this->db->getLastId();
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
    if ($query->row['total']<1&&$data['settings']['hide']) $status = 0;
    else                                              $status = 1;
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', status = '" . (int)$status . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
if ($index=='colourtex') {
  $this->db->query("DELETE FROM " . DB_PREFIX . "otp_option_value WHERE product_id = '" . (int)$product_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "otp_data         WHERE product_id = '" . (int)$product_id . "'");
	if (isset($data['settings']['update_image'])) $this->db->query("DELETE FROM " . DB_PREFIX . "otp_image        WHERE product_id = '" . (int)$product_id . "'");
  if (isset($output['roption'])) {
    $firstImage = TRUE;
    $arrContextOptions=array(
      "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ),
    );
    
    $folder = "catalog/";
    $url_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$output['manufacturer_id'] . "'");
    if ($url_query->row) $folder .= $url_query->row['keyword'];
    else                 $folder .= (string)$output['manufacturer_id'];
    if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
    $folder .= '/';

    $url_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$output['category_id'] . "'");
    if ($url_query->row) $folder .= $url_query->row['keyword'];
    else                 $folder .= (string)$output['category_id'];
    if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
    $folder .= '/';

    $folder .= str_replace ('/', '_', $this->model_zoxml2_zoxml2->myUrlEncode ($output['model']));
    if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
    $folder .= '/';

    if (isset($data['module']['HARD_DEBUG'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("folder: " . $folder)     . "', user = '" . $this->db->escape($data['user']) . "'");
      }

    foreach ($output['roption'] as $offer) { 
      // ОПЦИЯ РАЗМЕР
      $SIZES_ID = 15;
      $SIZES_VALUE_ID = 0;
      if (isset($offer['options'][$SIZES_ID])) {
        $option = $offer['options'][$SIZES_ID];
        $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($option['value']) . "' AND option_id = '" . (int)$SIZES_ID . "'");
        if ($option_ids->row) $SIZES_VALUE_ID = $option_ids->row['option_value_id'];
        else {
          $img = '';
    			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$SIZES_ID . "', image = '" . $this->db->escape($img) . "'");
    			$SIZES_VALUE_ID = $this->db->getLastId();
          foreach ($data['languages'] as $language_id => $language_name) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$SIZES_VALUE_ID . "', name = '" . $this->db->escape($option['value']) . "', option_id = '" . (int)$SIZES_ID . "', language_id = '" . (int)$language_id . "'");
            }
          }
        }
      else $SIZES_ID = 0;
      // ОПЦИЯ ЦВЕТ
      $COLOR_ID = 13;
      $COLOR_VALUE_ID = 0;
      if (isset($offer['options'][$COLOR_ID])) {
        $option = $offer['options'][$COLOR_ID];
        $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($option['value']) . "' AND option_id = '" . (int)$COLOR_ID . "'");
        if ($option_ids->row) $COLOR_VALUE_ID = $option_ids->row['option_value_id'];
        else {
          $img = '';
    			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$COLOR_ID . "', image = '" . $this->db->escape($img) . "'");
    			$COLOR_VALUE_ID = $this->db->getLastId();
          foreach ($data['languages'] as $language_id => $language_name) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$COLOR_VALUE_ID . "', name = '" . $this->db->escape($option['value']) . "', option_id = '" . (int)$COLOR_ID . "', language_id = '" . (int)$language_id . "'");
            }
          }
        }
      else $COLOR_ID = 0; 
      // otp_option_value
		  $this->db->query("INSERT " . DB_PREFIX . "otp_option_value SET 	
      product_id                 = '" . (int)$product_id . "', 
      parent_option_id           = '" . (int)$COLOR_ID . "',  
      child_option_id            = '" . (int)$SIZES_ID . "', 
      grandchild_option_id       = '0', 
      parent_option_value_id     = '" . (int)$COLOR_VALUE_ID . "', 
      child_option_value_id      = '" . (int)$SIZES_VALUE_ID . "', 
      grandchild_option_value_id = '0'");
     $otp_id = $this->db->getLastId();
      // otp_data 
		  $this->db->query("INSERT " . DB_PREFIX . "otp_data SET 	
      otp_id        = '" . (int)$otp_id . "', 
      product_id    = '" . (int)$product_id . "', 
      model         = '" . $this->db->escape($offer['model']) . "', 
      extra         = '" . $this->db->escape($offer['extra']) . "', 
      quantity      = '" . (int)$offer['quantity'] . "', 
      quantity_eu   = '" . (int)$offer['quantity_eu'] . "', 
      subtract      = '1', 
      price_prefix  = '" . $this->db->escape($offer['price_prefix']) . "', 
      price         = '" . $this->db->escape($offer['price']) . "', 
      special       = '0', 
      weight_prefix = '" . $this->db->escape($offer['weight_prefix']) . "', 
      weight        = '" . $this->db->escape($offer['weight']) . "'");
      // IMAGES   
      $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "otp_image WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$COLOR_ID . "' AND option_value_id   = '" . (int)$COLOR_VALUE_ID . "'");
      if (!$option_ids->row) {
        // КАРТИНКА ВАРИАНТА
        $img                = '';
        $data['dest_image'] = '';
        $ext                = 'jpg';
        if (!empty($offer['image'])&& isset($data['settings']['update_image'])) {
          $img = $this->model_zoxml2_zoxml2->myUrlEncode ($offer['image']);
          $src = file_get_contents ($img, false, stream_context_create($arrContextOptions));    
          if (isset($http_response_header)) {
            foreach ($http_response_header as $value) {
              if (strpos ($value,'image/png')!==FALSE) $ext = 'png';
              if (strpos ($value,'image/gif')!==FALSE) $ext = 'gif';
              }
            }
          if ($src) {
            $dest = 'IMG_' . md5 ($img) . "." . $ext;
            if (file_put_contents (DIR_IMAGE . $folder .  $dest, $src )===FALSE) $data['dest_image'] = '';
            else                                                                 $data['dest_image'] = $folder . $dest; 
            } 
          }
  		  if ($data['dest_image']) {
          $this->db->query("INSERT " . DB_PREFIX . "otp_image SET 	
          product_id        = '" . (int)$product_id . "', 
          option_id         = '" . (int)$COLOR_ID . "',  
          option_value_id   = '" . (int)$COLOR_VALUE_ID . "', 
          image             = '" . $this->db->escape($data['dest_image']) . "', 
          sort_order        = '0'");
          if ($firstImage) {
            $firstImage = FALSE;
		        $this->db->query("UPDATE " . DB_PREFIX . "product SET image  = '" . $this->db->escape($data['dest_image']) . "' WHERE product_id = '" . (int)$product_id . "'");
            }
          }
        }
      }
    }
  }
if ($index=='Tango' || $index=='saveOptions') {  // Пример пользовательского скрипта  - обработка опции "размер" для http://www.textilgroup.ru/upload/yml_csv/export.yml
  $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "product_option       WHERE product_id = '" . (int)$product_id . "'");
  if (count($output['option'])) {
    // Делаем сохранение опций
    $this->model_zoxml2_zoxml2->saveOptions ($output['option'],$product_id, $data);
    }
  }
if ($index=='mobilux.lv') {
  if (isset($data['settings']['update_description'])) { 
    if (isset($item->description_lv)) { 
      $output['description'] = (string)$item->description_lv;
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '2'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '2'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "', product_id = '" . (int)$product_id . "', language_id = '2'");
      // В английское описание записываем латышское!
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '1'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '1'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "', product_id = '" . (int)$product_id . "', language_id = '1'");
      }
    if (isset($item->description_ru)) { 
      $output['description'] = (string)$item->description_ru;
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '3'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '3'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "', product_id = '" . (int)$product_id . "', language_id = '3'");
      }
    }
  if (isset($data['settings']['update_name'])) { 
    if (!empty($output['name'])) { 
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '2'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) ."' WHERE product_id = '" . (int)$product_id . "' AND language_id = '2'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) . "', product_id = '" . (int)$product_id . "', language_id = '2'");
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '3'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) ."' WHERE product_id = '" . (int)$product_id . "' AND language_id = '3'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) . "', product_id = '" . (int)$product_id . "', language_id = '3'");
		  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '1'");
		  if ($query->row) $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) ."' WHERE product_id = '" . (int)$product_id . "' AND language_id = '1'");
		  else             $this->db->query("INSERT " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) . "', product_id = '" . (int)$product_id . "', language_id = '1'");
      }
    }
  }
if ($index=='inet.mondo') {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
  if (((int)$output['quantity'])>0 && ((float)$query->row['price']>(float)$output['price'])) {
    $sql   = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
    $sql .= ", price = '" . (float)$output['price'] . "'";
    $sql .= ", quantity = '" . (int)$output['quantity'] . "'";
    $sql .= " WHERE product_id = '" . (int)$product_id . "'";
    $this->db->query ($sql);
    }
  }
if ($index=='setStiker_updateOptions') {
  if (isset($output['KeepStatus'])&&$output['KeepStatus']==0) $KeepStatus = " status = '0',";
  else                                                        $KeepStatus = '';
  $mpn = $output['price']<3000?'':'Доставка: 0 ₽';
  $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW()," . $KeepStatus . " mpn = '" . $this->db->escape($mpn) . "' WHERE product_id = '" . (int)$product_id . "'");
  $index ='updateOptions';
  }
if ($index=='updateOptions') {
  if (isset($output['option']) && count($output['option'])) {
    // Делаем сохранение опций
    $this->model_zoxml2_zoxml2->saveOptions ($output['option'],$product_id, $data);
    }
  }
if ($index=='updateOptions_KeepStatus') {
  $query = $this->db->query("SELECT status FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'"); 
  $KeepStatus = " status = '" . (int)$query->row['status'] . "'";
  if (isset($output['option']) && count($output['option'])) {
    // Делаем сохранение опций
    $this->model_zoxml2_zoxml2->saveOptions ($output['option'],$product_id, $data);
    }
  $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW()," . $KeepStatus . " WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='updateOptionsScheme3') {
  if (isset($output['option']) && count($output['option'])) {
    // Делаем сохранение опций
    $this->model_zoxml2_zoxml2->saveOptions ($output['option'],$product_id, $data);
    $query = $this->db->query("SELECT MIN(price) as price FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
  }
if ($index=='priceF') {
  if (isset($item->priceF)) { 
    if (isset($data['currencies']['USD'])) {
      $price  = (float)$item->priceF;
      $price *= $data['currencies']['USD'];
		  $query  = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
      if (!empty($query->row['isbn'])) $price *= ((float)$query->row['isbn'])/100 + 1; 
      $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)round($price,0) . "' WHERE product_id = '" . (int)$product_id . "'");
      }    
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = 'Пользовательский скрпт: точка 2', user = '" . $this->db->escape($data['user']) . "'");
      if (isset($data['currencies']))$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = 'Пользовательский скрпт: точка 3', user = '" . $this->db->escape($data['user']) . "'");
      } 
    }
  else {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = 'Пользовательский скрпт: точка 1', user = '" . $this->db->escape($data['user']) . "'");
    } 
  }
if ($index=='price_uah') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET price_uah = '" . $this->db->escape($item->price2) . "' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_1') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '1' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_2') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '2' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_3') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '3' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_4') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '4' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_5') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '5' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_6') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '6' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_7') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '7' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_8') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '8' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_9') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '9' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_10') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = '10' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_rct') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'rct' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_sbx') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'sbx' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_brc') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'brc' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_dbx') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'dbx' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_shp') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'shp' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='set_JAN_to_ypp') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET jan = 'ypp' WHERE product_id = '" . (int)$product_id . "'");
  }
if ($index=='updateROptions_pure') {
  // Сохранение НЕ СВЯЗАННЫХ опций
  if (count($output['option'])) $this->model_zoxml2_zoxml2->saveOptions ($output['option'],$product_id, $data);
  // СОХРАНЕНИЕ ГОТОВЫХ ДАННЫХ
  foreach ($output['roption'] as $variant) { 
    if (count($variant['options'])>0) {
      // RO
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "' AND model = '" . $this->db->escape($variant['model']) . "'"); 
      if ($query->row) {
        // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
        $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
        // ОПЦИИ
        $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
        $extra = '';
        if (isset($data['settings']['update_quantity'])) $extra .=  "quantity = '" . (int)$variant['quantity'] . "',";  
        if (isset($data['settings']['update_price']))    $extra .=  "price = '" . $this->db->escape((float)$variant['price']) . "',";
        
        $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET " . $extra . " ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "' AND model = '" . $this->db->escape($variant['model']) . "'");
        if (isset($data['settings']['update_quantity'])) {
          $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
          $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
          }
        else {
          $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
          }
        continue;
        }
      // Такого варианта нет - добавляем!
      $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
      if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
          relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
          product_id = '" . (int)$product_id . "', 
          relatedoptions_use = '1'");        
        $relatedoptions_variant_product_id = $this->db->getLastId();
        } 
       // сохранение
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
        relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
        product_id      = '" . (int)$product_id . "', 
        quantity        = '" . (int)$variant['quantity'] . "', 
        weight          = '" . (float)$variant['weight'] . "', 
        model           = '" . $this->db->escape($variant['model']) . "', 
        sku             = '" . $this->db->escape($variant['sku']) . "', 
        ean             = '" . $this->db->escape($variant['ean']) . "', 
        location        = '" . $this->db->escape($variant['location']) . "', 
        stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
        price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
        price           = '" . (float)$variant['price'] . "'");  
      $relatedoptions_id = $this->db->getLastId();
      // Сохранение опций
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
        
      if (isset($data['settings']['update_quantity'])) {
        $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
        $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
        }
      else {
        $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
        }
      }
    }
  }
if ($index=='updateROptions') {
  $variant     = array (
    'options'                   => $output['option'],
    'sku'                       => isset($output['ro_sku'])?$output['ro_sku']:'',
    'ean'                       => '',
    'model'                     => isset($output['ro_model'])?$output['ro_model']:'',
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => '=',
    'price'                     => $output['price'],
    'stock_status_id'           => $data['settings']['stock_status_id'],
    );
  // РАЗБОР ОПЦИЙ
  $output['option'] = array();
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    // RO
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "' AND model = '" . $this->db->escape($variant['model']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // ОПЦИИ
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', sku = '" . $this->db->escape($variant['sku']) . "', model = '" . $this->db->escape($variant['model']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "' AND model = '" . $this->db->escape($variant['model']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
if ($index=='updateROptionsByModel') {
  $variant     = array (
    'options'                   => $output['option'],
    'sku'                       => isset($output['ro_sku'])?$output['ro_sku']:'',
    'ean'                       => '',
    'model'                     => isset($output['ro_model'])?$output['ro_model']:'',
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => '=',
    'price'                     => $output['price'],
    'stock_status_id'           => $data['settings']['stock_status_id'],
    );
  // РАЗБОР ОПЦИЙ
  $output['option'] = array();
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    // RO
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // ОПЦИИ
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', sku = '" . $this->db->escape($variant['sku']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($variant['model']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
if ($index=='updateROptions_pohodpro' || $index=='updateROptionsBySKU') {
  foreach($output['option'] as $key => $value) {
    $output['option'][$key]['quantity'] = $output['quantity']; 
    }
  $variant     = array (
    'options'                   => $output['option'],
    'sku'                       => isset($output['ro_sku'])?$output['ro_sku']:'',
    'ean'                       => '',
    'model'                     => isset($output['ro_model'])?$output['ro_model']:'',
    'location'                  => '',
    'quantity'                  => $output['quantity'],
    'weight'                    => $output['weight'],
    'price_prefix'              => $data['settings']['dov_price_prefix'],
    'price'                     => $data['settings']['dov_price_prefix']=='='?$output['price']:0,
    'stock_status_id'           => $data['settings']['stock_status_id'],
    );
  // РАЗБОР ОПЦИЙ
  $output['option'] = array();  
  // СОХРАНЕНИЕ ДАННЫХ
  if (count($variant['options'])>0) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'"); 
    if ($query->row) {
      // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
      $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
      // ОПЦИИ
      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$variant['price']) . "', quantity = '" . (int)$variant['quantity'] . "', model = '" . $this->db->escape($variant['model']) . "', ean = '" . $this->db->escape($variant['ean']) . "', weight = '" . (float)$variant['weight'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($variant['sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      if ($query->row['total']<1 && $data['settings']['hide']==1) $hide = "status = '0',";
      else                                                        $hide = "status = '1',";
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), " . $hide . "quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return;
      }
    // Такого варианта нет - добавляем!
    $relatedoptions_variant_id = $this->model_zoxml2_zoxml2->getVariantID ($variant);
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "' AND relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "'"); 
    if ($query->row) $relatedoptions_variant_product_id = $query->row['relatedoptions_variant_product_id'];
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_product SET   
        relatedoptions_variant_id         = '" . (int)$relatedoptions_variant_id . "', 
        product_id = '" . (int)$product_id . "', 
        relatedoptions_use = '1'");        
      $relatedoptions_variant_product_id = $this->db->getLastId();
      } 
     // сохранение
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions SET   
      relatedoptions_variant_product_id = '" . (int)$relatedoptions_variant_product_id . "',      
      product_id      = '" . (int)$product_id . "', 
      quantity        = '" . (int)$variant['quantity'] . "', 
      weight          = '" . (float)$variant['weight'] . "', 
      model           = '" . $this->db->escape($variant['model']) . "', 
      sku             = '" . $this->db->escape($variant['sku']) . "', 
      ean             = '" . $this->db->escape($variant['ean']) . "', 
      location        = '" . $this->db->escape($variant['location']) . "', 
      stock_status_id = '" . (int)$variant['stock_status_id'] . "', 
      price_prefix    = '" . $this->db->escape($variant['price_prefix']) . "', 
      price           = '" . (float)$variant['price'] . "'");  
    $relatedoptions_id = $this->db->getLastId();
    // Сохранение опций
    $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
      
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    if ($query->row['total']<1 && $data['settings']['hide']==1) $hide = "status = '0',";
    else                                                        $hide = "status = '1',";
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), " . $hide . "quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return;
    }
  }
}

protected function saveOptions ($options,$product_id, $data, $relatedoptions_id) {
  $query = $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_option WHERE product_id = '" . (int)$product_id . "'  AND relatedoptions_id = '" . (int)$relatedoptions_id . "'"); 
  foreach ($options as $option) {
    if (!$option['value']) continue;
    $option_id = $option['option_id'];
    // product_option
		$product_option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . $option_id . "'");
    if ($product_option_ids->row) $product_option_id = $product_option_ids->row['product_option_id'];
    else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', required = '" . (int)$option['required'] . "'");
			$product_option_id = $this->db->getLastId();
      }
    // option value
    $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($option['value']) . "' AND option_id = '" . (int)$option_id . "'");
    if ($option_ids->row) $option_value_id = $option_ids->row['option_value_id'];
    else {
      $img = '';
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', image = '" . $this->db->escape($img) . "'");
			$option_value_id = $this->db->getLastId();
      foreach ($data['languages'] as $language_id => $language_name) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', name = '" . $this->db->escape($option['value']) . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "'");
        }
      }
    // product_option_value 
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND option_value_id = '" . (int)$option_value_id . "'"); 
    $extra = '';
    if (isset($option['weight'])  ) $extra .= "weight   = '" . $this->db->escape($option['weight'])   . "', "; 
    if (isset($option['sku'])     ) $extra .= "sku      = '" . $this->db->escape($option['sku'])      . "', "; 
    if (isset($option['optsku'])  ) $extra .= "optsku   = '" . $this->db->escape($option['optsku'])   . "', "; 
    if (isset($option['upc'])     ) $extra .= "upc      = '" . $this->db->escape($option['upc'])      . "', "; 
    if (isset($option['jan'])     ) $extra .= "jan      = '" . $this->db->escape($option['jan'])      . "', "; 
    if (isset($option['optsku'])  ) $extra .= "optsku   = '" . $this->db->escape($option['optsku'])   . "', "; 
    if (isset($option['code'])    ) $extra .= "code     = '" . $this->db->escape($option['code'])     . "', "; 
    if (isset($option['location'])) $extra .= "location = '" . $this->db->escape($option['location']) . "', "; 
//  improvedoptions support
    if (isset($option['default_select'])) $extra .= "default_select = '" . (int)$option['default_select'] . "', "; 
    if (isset($option['description']))    $extra .= "description = '" . $this->db->escape($option['description']) . "', "; 
    if (isset($option['model']))          $extra .= "model = '" .       $this->db->escape($option['model']) . "', "; 
    if ($query->row) {
      // UPDATE
      $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET " . $extra . " quantity =  (quantity + " . $option['quantity'] . ")  WHERE product_option_value_id = '" . (int)$query->row['product_option_value_id'] . "'");
      $product_option_value_id = $query->row['product_option_value_id']; 
      }
    else {
      // INSERT
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET " . $extra . " product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', quantity = '" . $this->db->escape($option['quantity']) . "', subtract = '" . $this->db->escape($option['subtract']) . "', price = '" . (float)$option['price'] . "', price_prefix = '" . $this->db->escape($option['price_prefix']) . "'");        
      $product_option_value_id = $this->db->getLastId(); 
      }
    if (isset($data['module']['poip'])&&isset($option['poip'])&&count($option['poip'])) {
      $poip_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "poip_option_image WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");        
      if (!$poip_image_query->row) {
        foreach ($option['poip'] as $sort_order => $file) {
          $img = $this->model_zoxml2_zoxml2->loadOptionImage ($file, $data, $option_id);
          $this->db->query("INSERT INTO " . DB_PREFIX . "poip_option_image SET product_id = '" . (int)$product_id . "', product_option_id = '" . (int)$product_option_id . "', product_option_value_id = '" . (int)$product_option_value_id . "', 	image = '" . $this->db->escape($img) . "', sort_order = '" . (int)$sort_order . "'");        
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'OptionImage', data = '" . $this->db->escape($file)     . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_option SET   
      relatedoptions_id   = '" . (int)$relatedoptions_id . "', 
      product_id          = '" . (int)$product_id . "', 
      option_id           = '" . (int)$option_id . "', 
      option_value_id     = '" . (int)$option_value_id . "'");
    }
  }

}
?>