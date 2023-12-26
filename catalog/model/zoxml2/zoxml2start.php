<?php
class ModelZoXml2ZoXml2Start extends Model {

public function doUserStart($data,$xml) {
  $user_start = explode (';',$data['settings']['user_start']);
  foreach ($user_start as $next_user_start) {
    $parts = explode (':',$next_user_start);
    if (!isset($parts[1])) $parts[1] = '';
    $data = $this->old_doUserStart(trim($parts[0]),$data,$xml,trim($parts[1]));
    if (!is_array($data)) return NULL;
    }      
  return $data;
  }
public function old_doUserStart($index,$data,$xml,$param) {
  $session_key = $data['session_key'];
  $user        = $data['user'];

if ($index=='exit') return NULL;
if ($index=='log_data_and_exit') {
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape(json_encode($data)) . "', user = '" . $this->db->escape($data['user']) . "'");
  return NULL;
  }
if ($index=='log_and_exit') {
  foreach ($xml->shop->offers->offer as $item) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("Object: " . json_encode($item)) . "', user = '" . $this->db->escape($data['user']) . "'");
    foreach($item->attributes() as $key => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = '" . $this->db->escape(json_encode($key)) . "', data = '" . $this->db->escape(json_encode((string)$value)) . "', user = '" . $this->db->escape($data['user']) . "'");
      }
    break;
    }
  return NULL;
  }
if ($index=='kill_hpmodel_links') {
  $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "hpmodel_links");
  return $data;
  }
// samsonopt_use_type:pzk  
if ($index=='samsonopt_use_type') {
  if (empty($param)) $param = "min_opt";
  $data['samsonopt_use_type'] = $param;
  return $data;
  }
if ($index=='stores_to_multistore') {
  $data['stores_to_multistore'] = array();
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stores_to_multistore");
  foreach ($query->rows as $row) {
    $data['stores_to_multistore'][trim($row['store_name'])] = trim($row['multistore_id']);
    }
  if ($param) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_multistore WHERE `code` = '" . $this->db->escape($param) . "'");
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), " . $param . " = ''");
    }
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_stores', data = '" . $this->db->escape(json_encode($data['stores_to_multistore'])) . "', user = '" . $this->db->escape($data['user']) . "'");
  return $data;
  }
if ($index=='netlab_attriutes') {
  $data['netlab_properties'] = array();
  foreach($xml->properties->property as $item) {
    $data['netlab_properties'][trim($item['id'])] = trim($item);
    }
  return $data;
  }
if ($index=='killAllUpcValues') {
  $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), upc = ''");
  return $data;
  }
if ($index=='upc_65') {
  $this->db->query("ALTER TABLE " . DB_PREFIX . "product CHANGE `upc` `upc` VARCHAR(65)");
  $this->db->query("ALTER TABLE " . DB_PREFIX . "product_option_value CHANGE `upc` `upc` VARCHAR(65)");
  return $data;
  }
if ($index=='ean_65') {
  $this->db->query("ALTER TABLE " . DB_PREFIX . "product CHANGE `ean` `ean` VARCHAR(65)");
  return $data;
  }
if ($index=='sku_75') {
  $this->db->query("ALTER TABLE " . DB_PREFIX . "product CHANGE `sku` `sku` VARCHAR(75)");
  return $data;
  }

if ($index=='erosklad') {
  // Добавляем выбор опций
  if (!isset($data['options']['ro_color'])) {
    $data['options']['ro_color']['dest_id'] = 13;
    $data['options']['ro_color']['dest_type'] = 'option';
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'ro_color', `dest_type` = 'option', `data` = 'ro_color'");
    }  
  if (!isset($data['options']['ro_size'])) {
    $data['options']['ro_size']['dest_id'] = 11;
    $data['options']['ro_size']['dest_type'] = 'option';
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'ro_size', `dest_type` = 'option', `data` = 'ro_size'");
    }  

  return $data;
  }
  
  
if ($index=='USD_get_rate_from_CB_RF') {
  $valutes = $this->__getCBR ();
  if (isset($valutes['USD'])) $data['settings']['mul_after'] = (float)$valutes['USD'];
  return $data;
  }
if ($index=='ProteinPlus.pro') {
  if (isset($xml->currency)) $data['settings']['mul_after'] = (float)$xml->currency['rate'];
  return $data;
  }
if ($index=='add_field_updated_by') {
  $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='updated_by'");
  if (!$query->num_rows) $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `updated_by` VARCHAR(63) NOT NULL");
  return $data;
  }
if ($index=='gifts.ru_colors_loader') {
  // http://gifts.opencart.zone/colors.php
  $data['gifts_ru_colors'] = array ();
  $data['gifts_ru_memory'] = array ();
  $data['gifts_ru_names'] = array ();
  $xml = simplexml_load_file("http://gifts.opencart.zone/colors.php","SimpleXMLElement");
  if ($xml) {
//    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'XML', data = '" . $this->db->escape("Загружено: " . count($xml->item)) . "', user = '" . $this->db->escape($data['user']) . "'");
    foreach ($xml->item as $item) {
      $data['gifts_ru_colors'][trim($item->product_id)] = trim($item->color);
      $data['gifts_ru_memory'][trim($item->product_id)] = trim($item->memory);
      $data['gifts_ru_names'][trim($item->product_id)] = trim($item->zo_name);
      }
//    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'colors', data = '" . $this->db->escape("Загружено: " . count($data['gifts_ru_colors'])) . "', user = '" . $this->db->escape($data['user']) . "'");
    }
  else {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'colors', data = '" . $this->db->escape("Ошибка загрузки XML! ") . "', user = '" . $this->db->escape($data['user']) . "'");
    return NULL;
    }
  return $data;
  }
if ($index=='bau_catalog_brands_loader') {
  $data['bau_catalog_brands'] = array ();
  foreach ($xml->groupProduct as $groupProduct) {
    if (isset($groupProduct->collection_list)) {
      foreach ($groupProduct->collection_list->collection as $collection) {
        if (isset($collection->brand)) $data['bau_catalog_brands'][trim($collection['id'])] = trim($collection->brand);
        }
      }
    }
  return $data;
  }
if ($index=='mebeloptom_colors_loader') {
$this->db->query("ALTER TABLE " . DB_PREFIX . "product CHANGE `ean` `ean` VARCHAR(65)");

/*
<colors>
<color>
<id>29</id>
<name>Анегри</name>
<picture>https://mebeloptom.com/uploads/colors/large/color_57ade2a3031b9.jpg</picture>
<picture_medium>https://mebeloptom.com/uploads/colors/medium/color_57ade2a3031b9.jpg</picture_medium>
<picture_small>https://mebeloptom.com/uploads/colors/thumbnail/color_57ade2a3031b9.jpg</picture_small>
</color>
*/
  $data['mebeloptom_colors_id_to_name'] = array ();
  if (!empty($data['options']['color_id']['dest_id'])) {
    foreach ($xml->colors->color as $item) {
      $data['mebeloptom_colors_id_to_name'][(int)trim($item->id)] = trim($item->name);
      $option_id = $data['options']['color_id']['dest_id'];
      $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($item->name) . "' AND option_id = '" . (int)$option_id . "'");
      if ($option_ids->row) $option_value_id = $option_ids->row['option_value_id'];
      else {
        $img = $this->model_zoxml2_zoxml2->loadOptionImage ($item->picture, $data, $option_id); 
  			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', sort_order = '0', image = '" . $this->db->escape($img) . "'");
  			$option_value_id = $this->db->getLastId();
        foreach ($data['languages'] as $language_id => $language_name) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', name = '" . $this->db->escape($item->name) . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "'");
          }
        }
      }
    }   
  return $data;
  }
if ($index=='dveri.com_colors_loader') {
  $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='avatara'");
  if (!$query->num_rows) $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `avatara` VARCHAR(128) NOT NULL");
//  $this->db->query("ALTER TABLE " . DB_PREFIX . "product CHANGE `avatara` `avatara` VARCHAR(128)");
//  $this->db->query ("UPDATE " . DB_PREFIX . "product SET avatara = '' ");
/*
<colors>
<color>
<id>11</id>
<title>Т-06 (Темный Лак)</title>
<picture>
https://dveri.com/uploads/colors/color_50c9060b92a58.jpg
</picture>
</color>
*/
  $data['dveri_com_colors_id_to_name'] = array ();
  $data['dveri_com_colors_id_avatara'] = array ();
  foreach ($xml->colors->color as $item) {
    $data['dveri_com_colors_id_to_name'][(int)trim($item->id)] = trim($item->title);
    $data['dveri_com_colors_id_avatara'][(int)trim($item->id)] = trim($item->picture);
    if (!empty($data['options']['color_id']['dest_id'])) {
      $option_id = $data['options']['color_id']['dest_id'];
      $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($item->title) . "' AND option_id = '" . (int)$option_id . "'");
      if ($option_ids->row) $option_value_id = $option_ids->row['option_value_id'];
      else {
        $img = $this->model_zoxml2_zoxml2->loadOptionImage ($item->picture, $data, $option_id); 
  			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', sort_order = '0', image = '" . $this->db->escape($img) . "'");
  			$option_value_id = $this->db->getLastId();
        foreach ($data['languages'] as $language_id => $language_name) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', name = '" . $this->db->escape($item->title) . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "'");
          }
        }
      }
    }   
/*
<accessory>
<id>13</id>
<group_id>6</group_id>
<name>Коробка 2060*70*40</name>
<price_opt>300</price_opt>
<price_rrc>420</price_rrc>
<discount_opt>0</discount_opt>
<discount_rrc>0</discount_rrc>
<code>025-0080</code>
<main>1</main>
<position>1</position>
<new>0</new>
<order>0</order>
<sale>0</sale>
<soon>0</soon>
<action>0</action>
<pictures>
<big>https://dveri.com/uploads/pogonazh/large/80306c2a-7687-11e2-9a78-00155de79509.jpg</big>
<small>https://dveri.com/uploads/pogonazh/thumbnail/80306c2a-7687-11e2-9a78-00155de79509.png</small>
</pictures>
</accessory>
<accessory>
*/
  $data['dveri_com_accessories'] = array();
  if (!empty($data['options']['accessories_group_id']['dest_id'])) {
    foreach ($xml->accessories->accessory as $item) {
      $price = (float)trim($item->price_rrc);
      if (trim($item->discount_rrc)>0)  $price *= (100 - trim($item->discount_rrc)) / 100;
      $data['dveri_com_accessories'][] = array (
        'accessories_group' => trim($item->group_id),
        'option_id'     => $data['options']['accessories_group_id']['dest_id'], 
        'value'         => trim($item->name),
        'sku'           => trim($item->code),
        'price'         => $price,
        'price_prefix'  => "+",
        'image'         => trim($item->pictures->big),
        'subtract'      => 0,
        'quantity'      => 1,
        'required'      => 0,
        );
      }
    }
  return $data;
  }
if ($index=='zero_after_loader') {
  $where = $this->model_zoxml2_zoxml2->getContext ($data);
  $hide = $data['settings']['hide']==1?", status = '0'":'';
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $hide . $where );
  // опции
  $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET price = '0', quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  return $data;
  }
if ($index=='HG_updateROptionsBySKU') {
  $session_key = $data['session_key'];
  $log_key     = $session_key;
  $parts = explode ('|', $data['settings']['before_mode']);
  $before_mode = 'supplier';
  if (!empty($parts[1])) $session_key = $parts[1];

  if ($before_mode=='all') $where='';
  else                                         {
    if ($data['settings']['supplier']=='location')  $where=" WHERE location='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where=" WHERE supplier='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where=" WHERE mpn='" .      $this->db->escape($session_key) . "'";
    }
  $hide = $data['settings']['hide']==1?", status = '0'":'';
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $hide . $where );
  // опции
  $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  // связанные опции
  if (isset($data['module']['ro2'])) {
    $this->db->query("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
    }


  if ($data['settings']['tag_shop']) {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offer'];
    }
  else {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_offer'];
    }
  $updated = 0;
  $products = array();
  $info_processed = 0;
  foreach ($offers as $item) {
    $info_processed ++;
    $quantity = 0;
    if (!empty($item->Свободный)) $quantity = trim($item->Свободный);
    if (!empty($item->ИД)) {
      $sku = trim($item->ИД);
      if ($sku) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($sku) . "'"); 
        if ($query->row) {
          // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
          $product_id             = $query->row['product_id'];   
          $relatedoptions_id      = $query->row['relatedoptions_id'];                                                                                                                               
          $products[$product_id]  = true;
          $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$quantity . "' WHERE relatedoptions_id = '" . (int)$relatedoptions_id . "'");
          $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
          $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
          $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
          $updated ++;
          }
        }
      }
    }
  // Завершающее сообщение
  $txt = "Обработано: " . $info_processed;
  $txt .= " Обновлено опций: " . $updated;
  $txt .= " в: " . count($products) . " товарах";
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
  $this->cache->delete('product');
  return NULL;
  }
if ($index=='HG_updateOptionsBySKU') {
  $session_key = $data['session_key'];
  $log_key     = $session_key;
  $parts = explode ('|', $data['settings']['before_mode']);
  $before_mode = 'supplier';
  if (!empty($parts[1])) $session_key = $parts[1];

  if ($before_mode=='all') $where='';
  else                                         {
    if ($data['settings']['supplier']=='location')  $where=" WHERE location='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where=" WHERE supplier='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where=" WHERE mpn='" .      $this->db->escape($session_key) . "'";
    }
  $hide = $data['settings']['hide']==1?", status = '0'":'';
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $hide . $where );
  // опции
  $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");

  if ($data['settings']['tag_shop']) {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offer'];
    }
  else {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_offer'];
    }
  $updated = 0;
  $products = array();
  $info_processed = 0;
  foreach ($offers as $item) {
    $info_processed ++;
    $quantity = 0;
    if (!empty($item->Свободный)) $quantity = trim($item->Свободный);
    if (!empty($item->ИД)) {
      $sku = trim($item->ИД);
      if ($sku) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE sku = '" . $this->db->escape($sku) . "'"); 
        if ($query->row) {
          // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
          $product_id                   = $query->row['product_id'];   
          $product_option_value_id      = $query->row['product_option_value_id'];                                                                                                                               
          $products[$product_id]        = true;
          $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$quantity . "' WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");
          $query2 = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'"); 
          $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query2->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
          $updated ++;
          }
        }
      }
    }
  // Завершающее сообщение
  $txt = "Обработано: " . $info_processed;
  $txt .= " Обновлено опций: " . $updated;
  $txt .= " в: " . count($products) . " товарах";
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
  $this->cache->delete('product');
  return NULL;
  }
if ($index=='LoadCatNames') {
  $data['LoadCatNames'] = array();
  foreach ($xml->shop->categories->category as $item) {
    $data['LoadCatNames'][trim($item['id'])] = trim($item); 
    }
  // Удаляем все опции и связанные опции
  
  $session_key = $data['session_key'];
  $log_key     = $session_key;
  $parts = explode ('|', $data['settings']['before_mode']);
  $before_mode = 'supplier';
  if (!empty($parts[1])) $session_key = $parts[1];

  if ($before_mode=='all') $where='';
  else                                         {
    if ($data['settings']['supplier']=='location')  $where=" WHERE location='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where=" WHERE supplier='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where=" WHERE mpn='" .      $this->db->escape($session_key) . "'";
    }
//  $hide = $data['settings']['hide']==1?", status = '0'":'';
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $where );
/*
  // опции
  $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  // связанные опции
  if (isset($data['module']['ro2'])) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_option WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
    $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
    }
 */


  return $data;
  }
if ($index=='KillOptionsAndDescriptions') {
  if ($data['settings']['before_mode']=='all') $where='';
  else                                         {
    if ($data['settings']['supplier']=='location')  $where=" WHERE location='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where=" WHERE supplier='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where=" WHERE mpn='" .      $this->db->escape($session_key) . "'";
    }
  $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  $this->db->query("UPDATE " . DB_PREFIX . "product_description  SET description = '' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product"  . $where . ")");
  return $data;
  }
if ($index=='LoadWhiteListOfSku') {
    $arrContextOptions=array(
      "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ),
    );
    
  $src = file_get_contents (HTTPS_SERVER . "zoxml2_whitelistofsku.txt", false, stream_context_create($arrContextOptions));  
  if ($src) {
    $data['WhiteListOfSku'] = array();
    $values = explode (',',(string)$src);
    foreach ($values as $sku) $data['WhiteListOfSku'][trim($sku)] = true;
    }
  else {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'Error', data = '" . $this->db->escape('Ошибка загрузки файла: ' . HTTPS_SERVER . "zoxml2_whitelistofsku.txt") . "', user = '" . $this->db->escape($data['user']) . "'");
    if (isset($http_response_header)) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("http_response_header: " . json_encode($http_response_header)) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
    }
  if (!isset($data['WhiteListOfSku']))                 {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'Error', data = '" . $this->db->escape('WhiteListOfSku отсутствует!') . "', user = '" . $this->db->escape($data['user']) . "'");
    return NULL;
    }
  return $data;
  }
if ($index=='LoadBlackListOfSku') {
    $arrContextOptions=array(
      "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ),
    );
    
  $src = file_get_contents (HTTPS_SERVER . "zoxml2_blacklistofsku.txt", false, stream_context_create($arrContextOptions));  
  if ($src) {
    $data['BlackListOfSku'] = array();
    $values = explode (',',(string)$src);
    foreach ($values as $sku) $data['BlackListOfSku'][trim($sku)] = true;
    }
  return $data;
  }
if ($index=='LoadBlackListOfSkuText') {
    $arrContextOptions=array(
      "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ),
    );
    
  $src = file_get_contents (HTTPS_SERVER . "zoxml2_blacklistofsku.text", false, stream_context_create($arrContextOptions));  
  if ($src) {
    $data['BlackListOfSku'] = array();
    $values = explode (',',(string)$src);
    foreach ($values as $sku) $data['BlackListOfSku'][trim($sku)] = true;
    }
  return $data;
  }
if ($index=='related4zoloto') {
  $total    = 0;
  $related  = 0;
  foreach ($xml->shop->offers->offer as $item) {
    if (isset($item->АртикулПоставщика)) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($item->АртикулПоставщика) . "'");        
      if ($query->row) { // ТОВАР С ТАКИМ АРТИКУЛОМ СУЩЕСТВУЕТ
        $total ++;
        if (isset($item->рекомендация)) {
          $product_id = $query->row['product_id'];
          $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
          $models = explode (',', $item->рекомендация);
          foreach ($models as $model) {
            $model_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape(trim($model)) . "'");        
            if ($model_query->row) {
              $related ++;
				      $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$model_query->row['product_id'] . "'");
              }
            }
          }
        }
      }
    }
  $txt = "Обработано: " . $total;
  $txt .= "  Рекомендованных: " . $related;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
  return NULL;
  }
if ($index=='JAN_shp') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE jan='shp'");
  return $data;
  }
if ($index=='JAN_brc') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE jan='brc'");
  return $data;
  }
if ($index=='JAN_dbx') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE jan='dbx'");
  return $data;
  }
if ($index=='JAN_sbx') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE jan='sbx'");
  return $data;
  }
if ($index=='JAN_rct') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE jan='rct'");
  return $data;
  }
if ($index=='inpo_ru') {
  $session_key            = $data['session_key'];
  $info_insert            = 0;    
  $info_update            = 0;    
  $info_processed         = 0;    
  $info_progress          = 0;    
  foreach ($xml->group as $root_category) {
   foreach ($root_category->group as $sub_category) {
       foreach ($sub_category->item as $item) {
        usleep ($data['module']['sleep']);
        $info_processed ++;
        if (++$info_progress==$data['module']['step']) {
          $info_progress = 0; 
          $txt = "Обработано: " . $info_processed;
          $txt .= "  Добавлено: " . $data['info_insert'];
          $txt .= "  Обновлено: " . $data['info_update'];
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        $output = $this->model_zoxml2_zoxml2->getDefOutput ($data);
        $output['cat_id'] = $sub_category['no'];
        foreach ($item as $option_key => $value) {
          $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$value);
          }
        $output = $this->model_zoxml2_zoxml2->doParams('host',$output,$data,'host');
        $output = $this->model_zoxml2_zoxml2->doReplase($output,$data);
        $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
        if (is_nan($output['price'])) continue;
        $output = $this->model_zoxml2_zoxml2->doMeta($output,$data); // ШАБЛОНЫ
        // Произодитель
        $output['manufacturer_id'] = $this->model_zoxml2_zoxml2->getVendor($session_key,$output['vendor']);
        // категория
        $output['category_id']     = $this->model_zoxml2_zoxml2->getCategoryByID($session_key,$output['cat_id']);
        if ($output['category_id']>0&&$output['manufacturer_id']>0) {
          $data  = $this->model_zoxml2_zoxml2->processOutput ($data,$output);
          $info_update ++;
          }
        }
      }
    }
  $txt = "Обработано: " . $info_processed;
  $txt .= "  Добавлено: " . $data['info_insert'];
  $txt .= "  Обновлено: " . $data['info_update'];
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'end', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
  return NULL;
  }
if ($index=='verda-m_ru') {
  $data['verda_data'] = array ();
  foreach ($xml->products->product as $item) {
    $data['verda_data'][trim($item['id'])] = array (
      'cover' => trim($item->cover),
      'categoryId' => trim($item->categoryId),
      'description' => isset($item->description)?trim($item->description):'',
      );
    }  
  return $data;
  }
if ($index=='alexika_ru') {
  // ПРОИЗВОДИТЕЛИ
  $data['alexika_brands'] = array ();
  foreach ($xml->brands->brand as $item) {
    $data['alexika_brands'][(string)$item->id] = (string)$item->name;
    }  
  //  АТРИБУТЫ
  $data['alexika_attributes'] = array ();
  foreach ($xml->attributes->attribute as $item) {
    $data['alexika_attributes'][(string)$item->id] = 'attribute_' . (string)$item->name;
    }
  return $data;
  }
if ($index=='kill_ro_special') {
  if (isset($data['settings']['update_special'])) {
    if (isset($data['module']['ro2'])) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_special WHERE relatedoptions_id IN (SELECT relatedoptions_id FROM " . DB_PREFIX . "relatedoptions WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product WHERE location = 'YML33c6c23c2e77e5c726a32d2c5cb41232'))");
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_special        WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product WHERE location = 'YML33c6c23c2e77e5c726a32d2c5cb41232')");
      }
    }
  return $data;
  }

if ($index=='YML72e0a0090980452fd365a47c1ff83283') {
  $info_update            = 0;    
  $info_processed         = 0;    
  $info_progress          = 0;    
  foreach ($xml->product as $item) {
    usleep ($data['module']['sleep']);
    if (isset($item->price['RetailPrice']))     $output = $this->model_zoxml2_zoxml2->doParams('price_RetailPrice',$output,$data,$item->price['RetailPrice']);
    if (isset($item->price['BaseRetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseRetailPrice',$output,$data,$item->price['BaseRetailPrice']);
    if (isset($item->price['WholePrice']))      $output = $this->model_zoxml2_zoxml2->doParams('price_WholePrice',$output,$data,$item->price['WholePrice']);
    if (isset($item->price['BaseWholePrice']))  $output = $this->model_zoxml2_zoxml2->doParams('price_BaseWholePrice',$output,$data,$item->price['BaseWholePrice']);
    // ------------
    if (isset($item->assortiment->assort)) {
      $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
      if (is_nan($output['price'])) continue;
      $product_id = 0;
      foreach ($item->assortiment->assort as $assort) {
$info_processed ++;
if (++$info_progress==$data['module']['step']) {
  $info_progress = 0; 
  $txt = "Обработано опций: " . $info_processed;
  $txt .= "  Обновлено: " . $info_update;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
  }
        $quantity       = $assort['sklad'];
        $sku            = $assort['aID'];
        // product_option_value 
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE sku = '" . (int)$sku . "'"); 
        if ($query->row) {
          // UPDATE
          $info_update ++;
          $product_id = $query->row['product_id'];
          $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$quantity . "'  WHERE product_option_value_id = '" . (int)$query->row['product_option_value_id'] . "'");
          }
        }
      if ($product_id) {
        $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'"); 
        $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
        }
      }
    }
  return NULL;
  }

  return $data;
  }

public function __getCBR () { // ПОЛУЧЕНИЕ КУРСОВ ИЗ ЦБ РФ
/*
<Valute ID="R01720">
<NumCode>980</NumCode>
<CharCode>UAH</CharCode>
<Nominal>10</Nominal>
<Name>Украинских гривен</Name>
<Value>24,2739</Value>
</Valute>
*/
  $xml = $this->__getXmlFrom ('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date("d/m/Y"));
  if (!$xml) return NULL;
  $valutes = array();
  foreach ($xml->Valute as $item) {
    $valutes[trim($item->CharCode)] = (float)str_replace(",",".",trim($item->Value)) / (int)trim($item->Nominal); 
    }
	return $valutes;
  }
public function __getXmlFrom ($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $src = curl_exec($ch);
  curl_close ($ch);
  if (!$src) return NULL;
  return simplexml_load_string ($src);
  }


}
?>
