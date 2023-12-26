<?php
class ModelZoXml2ZoXml2Pre extends Model {

public function doUserPre($output,$data,$item) {                

$session_key      = $data['session_key'];
$user             = $data['user'];


if ($data['settings']['user_pre']=='only17027') { 
  if ($item['id']!="10018528") return NULL;
  return $output;
  }
if ($data['settings']['user_pre']=='verda-m_ru') { 
  $item_product_id = trim($item->productId);
  if (!isset($data['verda_data'][$item_product_id])) return NULL;
  $output['cat_id'] = $data['verda_data'][$item_product_id]['categoryId'];
  $output['description'] = $data['verda_data'][$item_product_id]['description'];
  return $output;
  }
if ($data['settings']['user_pre']=='price_use_rates') { 
  $output['price_use_rates'] = TRUE;
  return $output;
  }
if ($data['settings']['user_pre']=='price_format_correction') { 
  if (isset($item->price)) $output['price'] = str_replace(",", "",  trim($item->price));
  return $output;
  }
if ($data['settings']['user_pre']=='gifts.ru4ImprovedOptions_v3') { 
  $item_product_id = trim($item->product_id);
  if (!empty($data['gifts_ru_colors'][$item_product_id])) $output['color']  = $data['gifts_ru_colors'][$item_product_id]; 
  if (!empty($data['gifts_ru_memory'][$item_product_id])) $output['memory'] = $data['gifts_ru_memory'][$item_product_id]; 
  return $output;
  }
if ($data['settings']['user_pre']=='sku_from_param') { 
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Артикул') {
        $output['sku'] = trim($value); 
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='color2name') { 
  if (empty($item->Цвет__COLOR)) return NULL;
  if (empty($item->Наименование_элемента)) return NULL;
  $output['name'] = trim($item->Наименование_элемента) . ", " . trim($item->Цвет__COLOR);
  return $output;
  }
if ($data['settings']['user_pre']=='images_from_ydisk') { 
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Ссылка на фото товара (ссылки через разделитель)') {
        $urls = explode (';', trim((string)$value));
        foreach ($urls as $url) $output['image'][] = $data['settings']['img_path'] . trim($url);
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='bau_catalog_brands') { 
  $output['vendor']         = isset($item->vendor)?trim($item->vendor):''; 
  if (isset($item->producer_brand)) {
    $output['vendor'] = trim($item->producer_brand);
    }
  else {
    if (isset($item->collection_list->collection)) {
      foreach ($item->collection_list->collection as $collection) {
        $output['vendor'] = "collection_" . $collection;
        if (isset($data['bau_catalog_brands'][trim($collection)])) {
          $output['vendor'] = $data['bau_catalog_brands'][trim($collection)];
          break;
          }
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='md5name2ean') { 
  $output['ean'] = md5 (trim($item->name) . "::" . $session_key);
  return $output;
  }
if ($data['settings']['user_pre']=='v4_vip_v2') { 
  $vendor = isset($item->vendor)?trim($item->vendor):'(производитель не указан)';
  $output['ean'] = md5 (trim($item->shortName) . "::" . $vendor . "::" . $session_key);
  $output['upc'] = md5 (trim($item->name));
  $output['model'] = trim($item->name);
  if ($output['weight_class_id']=="1") $output['weight_class_id_koeff'] = 0.001;
  if (!isset($item->vendorCode)) return NULL;
  $output['io_sku'] = trim($item->vendorCode); 
  if (empty($output['io_sku'])) return NULL;
// 33S0601S
// 31093163XL
// 11275.11 
//  
  // СНАЧАЛА ПРОВЕРЯЕМ НАЛИЧИЕ ТОЧКИ В АРТИКУЛЕ
  $parts = explode ('.', $output['io_sku']);
  if (isset($parts[1])) {
    $output['sku'] = $parts[0];
    }
  else {
    $output['sku'] = ''; // БУДЕТ ВЫЧИСЛЕН ПОЗЖЕ ПУТЕМ ОТРЕЗАИЯ РАЗМЕРА
    }
    
/*
<outlets>
<outlet id="000000029" instock="128"/> - Мосвка
<outlet id="1-0000052" instock="3560"/>  - Европа
</outlets>
*/  
  $item_mos = 0;
  $item_eur = 0;
  foreach ($item->outlets->outlet as $value) {
    if ($value['id'] == "000000029") $item_mos = (int)$value['instock'];
    if ($value['id'] == "1-0000052") $item_eur = (int)$value['instock'];
    }
  $output['quantity'] = $item_mos + $item_eur;

  $description = '';
  $description .= 'Всего: ' . $output['quantity'] .  ', ';
  $description .= 'Москва: ' . $item_mos .  ', ';
  $description .= 'Европа: ' . $item_eur;

  if ($data['options']['param_Метод нанесения']['dest_id']) {
    $output['option'][] = array (
      'option_id'     => $data['options']['param_Метод нанесения']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'sort_order'    => 1,
      );
    }
  foreach ($item->param as $value) {
    if ($value['name']=='Метод нанесения' && $data['options']['param_Метод нанесения']['dest_id']) {
      $output['option'][] = array (
        'option_id'     => $data['options']['param_Метод нанесения']['dest_id'], 
        'value'         => (string)$value,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'sort_order'    => 10,
        );
      }
    if ($value['name']=='Размер' && $data['options']['param_Размер']['dest_id']) {
      $optionValue = htmlspecialchars(trim($value));
      if ($optionValue) {
        if (!$output['sku']) $output['sku'] = substr_replace($output['io_sku'],'',-strlen($optionValue)); 
        //str_replace($optionValue, "", $output['io_sku']);
        if (defined('OASIS_VIP_SKU_PREFIX')) $output['io_sku'] = OASIS_VIP_SKU_PREFIX . $output['io_sku'];
        $output['option'][] = array(
        'option_id'     => $data['options']['param_Размер']['dest_id'],
        'value'         => $optionValue,
        'price_prefix'  => '+',
        'required'      => $data['settings']['dov_required'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $output['quantity'],
        'price'         => 0,
        'sku'           => $output['io_sku'],
        'model'         => $output['model'] . ", размер " . $optionValue,
        'description'   => $description,
        );
        }
      }
    }
  if (!$output['sku']) $output['sku'] = trim($item->vendorCode);
  if (defined('OASIS_VIP_SKU_PREFIX')) $output['sku'] = OASIS_VIP_SKU_PREFIX . $output['sku'];
  return $output;
  }
if ($data['settings']['user_pre']=='v4_biz_v2') { 
  $vendor = isset($item->vendor)?trim($item->vendor):'(производитель не указан)';
  $output['ean'] = md5 (trim($item->shortName) . "::" . $vendor . "::" . $session_key);
//  $output['upc'] = md5 (trim($item->name));
  $output['model'] = trim($item->name);
  if ($output['weight_class_id']=="1") $output['weight_class_id_koeff'] = 0.001;
  if (!isset($item->vendorCode)) return NULL;
  $output['io_sku'] = trim($item->vendorCode); 
  if (empty($output['io_sku'])) return NULL;
// 33S0601S
// 31093163XL
// 11275.11 
//  
  // СНАЧАЛА ПРОВЕРЯЕМ НАЛИЧИЕ ТОЧКИ В АРТИКУЛЕ
  $parts = explode ('.', $output['io_sku']);
  if (isset($parts[1])) {
    $output['sku'] = $parts[0];
    }
  else {
    $output['sku'] = ''; // БУДЕТ ВЫЧИСЛЕН ПОЗЖЕ ПУТЕМ ОТРЕЗАИЯ РАЗМЕРА
    }
    
/*
<outlets>
<outlet id="000000029" instock="128"/> - Мосвка
<outlet id="1-0000052" instock="3560"/>  - Европа
</outlets>
*/  
  $item_mos = 0;
  $item_eur = 0;
  $item_way = 0;
  foreach ($item->outlets->outlet as $value) {
    if ($value['id'] == "000000029") $item_mos = (int)$value['instock'];
    if ($value['id'] == "000000039") $item_way = (int)$value['instock'];
    if ($value['id'] == "1-0000052") $item_eur = (int)$value['instock'];
    }
  $output['quantity'] = $item_mos + $item_eur + $item_way;

  $description = '';
  $description .= 'Всего: ' . $output['quantity'] .  ', ';
  $description .= 'Москва: ' . $item_mos .  ', ';
  $description .= 'Европа: ' . $item_eur .  ', ';
  $description .= 'В пути: ' . $item_way;
  // Новый подсчет - сумма всех складов
//  $output['quantity'] = 0;
//  foreach ($item->outlets->outlet as $value) $output['quantity'] += (int)$value['instock'];


  if ($data['options']['param_Метод нанесения']['dest_id']) {
    $output['option'][] = array (
      'option_id'     => $data['options']['param_Метод нанесения']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'sort_order'    => 1,
      );
    }
  $output['upc'] = md5 (trim($item->name));
  if (!empty($item->size) && $data['options']['size']['dest_id']) {
    $optionValue = htmlspecialchars(trim($item->size));
    if ($optionValue) {
      if (!$output['sku']) $output['sku'] = substr_replace($output['io_sku'],'',-strlen($optionValue)); 
      //str_replace($optionValue, "", $output['io_sku']);
      if (defined('OASIS_BIZ_SKU_PREFIX')) $output['io_sku'] = OASIS_BIZ_SKU_PREFIX . $output['io_sku'];
      $output['option'][] = array(
      'option_id'     => $data['options']['size']['dest_id'],
      'value'         => $optionValue,
      'price_prefix'  => '+',
      'required'      => $data['settings']['dov_required'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $output['quantity'],
      'price'         => 0,
      'sku'           => $output['io_sku'],
      'model'         => $output['model'] . ", размер " . $optionValue,
      'description'   => $description,
      );
      }
    }
  foreach ($item->param as $value) {
    if ($value['name']=='Цвет товара') {
      $output['upc'] = md5 (trim($item->name) . trim($value));
      }
    if ($value['name']=='Метод нанесения' && $data['options']['param_Метод нанесения']['dest_id']) {
      $output['option'][] = array (
        'option_id'     => $data['options']['param_Метод нанесения']['dest_id'], 
        'value'         => (string)$value,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'sort_order'    => 10,
        );
      }
    if ($value['name']=='Размер' && $data['options']['param_Размер']['dest_id']) {
      $optionValue = htmlspecialchars(trim($value));
      if ($optionValue) {
        if (!$output['sku']) $output['sku'] = substr_replace($output['io_sku'],'',-strlen($optionValue)); 
        //str_replace($optionValue, "", $output['io_sku']);
        if (defined('OASIS_BIZ_SKU_PREFIX')) $output['io_sku'] = OASIS_BIZ_SKU_PREFIX . $output['io_sku'];
        $output['option'][] = array(
        'option_id'     => $data['options']['param_Размер']['dest_id'],
        'value'         => $optionValue,
        'price_prefix'  => '+',
        'required'      => $data['settings']['dov_required'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $output['quantity'],
        'price'         => 0,
        'sku'           => $output['io_sku'],
        'model'         => $output['model'] . ", размер " . $optionValue,
        'description'   => $description,
        );
        }
      }
    }
  if (!$output['sku']) $output['sku'] = trim($item->vendorCode);
  if (defined('OASIS_BIZ_SKU_PREFIX')) $output['sku'] = OASIS_BIZ_SKU_PREFIX . $output['sku'];
  return $output;
  }
if ($data['settings']['user_pre']=='strip_id') { 
  // <offer id="9228.01062">
  if (!empty($item["id"])) {
    $parts = explode ('.',trim($item["id"]));
    $output['model'] = $parts[0];
    if (isset($parts[2])) $output['model'] .= "." . $parts[1];
    $output['optsku'] = trim($item["id"]);
    }
  return $output;
  }
if ($data['settings']['user_pre']=='eklektika_category_id') { 
  $cats = array();
  if (isset($item->Группы->Ид)) {
    foreach ($item->Группы->Ид as $id) $cats[] = trim($id);
    }
  if (count($cats)) $output['cat_id'] = array_shift ($cats);
  if (isset($data['settings']['link2category_ids'])) {
    foreach($cats as $value) {
      $cat_id = $this->model_zoxml2_zoxml2->getCategoryByID($session_key,$value);
      if ($cat_id) $output['category_ids'][$cat_id] = $cat_id;
      }
    } 
  return $output;
  }
if ($data['settings']['user_pre']=='use_last_category_id') { 
  $cats = array();
  foreach ($item->categoryId as $category) $cats[] = trim($category);
  if (count($cats)) $output['cat_id'] = array_pop ($cats);
  return $output;
  }
if ($data['settings']['user_pre']=='api.nvprint.ru') { 
  foreach ($item->УсловияПродаж->Договор as $value) {
    if ($value['НомерДоговора']=="ТП-001768") {
      $output['price'] = trim($value->Цена);
      $output['quantity'] = trim($value->Наличие['Количество']);
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='optvideo_skip') { 
  if (isset($item->skip)) return NULL;
  return $output;
  }
if ($data['settings']['user_pre']=='urbanplanet_name') { 
  $output['name'] = trim($item->name);
  foreach ($item->param as $value) {
    if ($value['name']!='РАЗМЕР') continue;
    $output['name'] = str_replace ( " " . trim($value) . " ", "|", $output['name'] ); 
    $output['name'] = str_replace ( " " . trim($value) . "<", "|", $output['name'] ); 
    $parts = explode ('|',trim($output['name']));
    $output['name'] = trim($parts[0]);
    }
  }
if ($data['settings']['user_pre']=='ProteinPlus_Kremenchug') { 
  $output['quantity'] = 0;
  if ($item->available !="true") return NULL;
  if (isset($item->warehouses)) {
    foreach ($item->warehouses->warehouse as $value) {
      if ($value['id'] == 2) {
        if ($value->available == 'false') $output['quantity'] = 0;
        else                              $output['quantity'] = $data['settings']['quantity'];
        }
      }
    }
  if (!empty($item['group_id'])) $output['model'] = trim($item['group_id']);
  else {  
    $values = explode ('-',trim($item->vendorCode));
    $output['model'] = $values[0];
    }      
  $output['name'] = "";
  if (isset($item->name)) {
    $values = explode ('(',trim($item->name));
    $output['name'] = $values[0];
    foreach ($item->param as $value) {
      if ($value['name']!="Фасовка/размер") continue;
      $output['name'] .= " (" . trim($value) . ")"; 
      }
    }
  
  return $output;
  }
if ($data['settings']['user_pre']=='totalfit_name') { 
  $output['name'] = trim($item->name);
  foreach ($item->param as $value) {
    if ($value['name']!='Размер') continue;
    $output['name'] = str_replace ( " " . trim($value) . " ", " ", $output['name'] ); 
    $output['name'] = str_replace ( " " . trim($value) . "<", " ", $output['name'] ); 
    }
  }
if ($data['settings']['user_pre']=='skip_if_parent') {
  if (!empty($item['parent'])) return NULL;
  return $output;
  }
// ------ gifts.ru ----------
if ($data['settings']['user_pre']=='gifts.ru_name') {
  $item_product_id = trim($item->product_id);
  if (empty($data['gifts_ru_names'][$item_product_id])) return NULL;
  if (!empty($data['gifts_ru_memory'][$item_product_id])) $output['memory'] = $data['gifts_ru_memory'][$item_product_id]; 
  $output['name'] = $data['gifts_ru_names'][$item_product_id];
  return $output;
  }
if ($data['settings']['user_pre']=='gifts.ru_color_dump') {
  // ИЩЕМ ЦВЕТ В ФИЛЬТРАХ:  $data['color_filter']
  $color_from_filter = '';
  foreach ($item->filters->filter as $filter) {
    if ($data['color_filter']['filtertypeid'] ==(int)$filter->filtertypeid) {
      $color_from_filter = $data['color_filter']['colors'][trim($filter->filterid)];
      break;
      }
    }
  // ИЩЕМ ЦВЕТ В НАЗВАНИИ
  $color_from_name = '';
  $parts = explode (',',trim($item->name));
  if (isset($parts[1])) { // ЦВЕТ ЕСТЬ!
    $color_from_name = trim(array_pop($parts));
    }

  $color = 'не указан';
  if ($color_from_filter==$color_from_name) $color = $color_from_name;

  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gifts_colors WHERE product_id = '" . $this->db->escape($item->product_id) . "'");
  if (!$query->row) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "gifts_colors SET   product_id = '" . $this->db->escape($item->product_id) . "', 
                                                                      name = '" . $this->db->escape($item->name) . "',
                                                                      color_from_filter = '" . $this->db->escape($color_from_filter) . "',
                                                                      color_from_name = '" . $this->db->escape($color_from_name) . "',
                                                                      color = '" . $this->db->escape($color) . "',
                                                                      zo_name = '" . $this->db->escape($parts[0]) . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'gifts: new!', data = '" . $this->db->escape('Новый product_id: ' . $item->product_id) ."'");
    return NULL;
    }
  $output['name'] = $query->row['zo_name'];
  return $output;
  }
if ($data['settings']['user_pre']=='mg5_of_name_to_sku') { 
  $name = trim($item->name);
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Цвет') {
        $name  .= trim($value);
        }
      }
    }
  $output['sku'] = md5($name);
  return $output;
  }
  
if ($data['settings']['user_pre']=='OPT-STUFF') {
  foreach ($item->price_list as $price_list) {
    if (trim($price_list->type)=="contract")     $output['price']       = trim($price_list->value); 
    }
  foreach ($item->stock_list as $stock_list) {
    if (trim($stock_list->type)=="idp")          $output['quantity']    = trim($stock_list->value); 
    }
  return $output;
  }
if ($data['settings']['user_pre']=='options_with_price_prefix') { 
/*
<param name="Доступные размеры" unit="в скобках указана наценка">
42, 44, 46, 48, 50, 52, 54 (+60), 56 (+60), 58 (+60), 60 (+60), 62 (+85), 64 (+85), 66 (+85), 68 (+170), 70 (+170), 72 (+170), 74 (+170)
</param>
*/
  foreach ($item->param as $value) {
    if ($value['name']!='Доступные размеры') continue;
    $values = explode (',',trim($value));
    foreach ($values as $option_value) {
      $parts = explode (' ',trim($option_value));
      $plus  = 0;
      if (isset($parts[1])) {
        $entities = array('(', ')', '+');
        $replacements = array(' ', ' ', " ");
        $plus =  trim(str_replace($entities, $replacements, $parts[1]));
        }
      $output['option'][] = array(
      'option_id'     => $data['options']['param_Доступные размеры']['dest_id'],
      'value'         => htmlspecialchars(trim($parts[0])),
      'price_prefix'  => '+',
      'required'      => $data['settings']['dov_required'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $output['quantity'],
      'price'         => $plus,
      );
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='trade-city_USD2UAH') {  
  $output['price'] = 0;
  if (isset($item->base_price)) {
    $output['price'] = trim($item->base_price);
    }  
  if (isset($item->base_currency)) {
    $base_currency = trim($item->base_currency);
    if (isset($data['currencies'][$base_currency])) $output['price'] *= $data['currencies'][$base_currency];
    }
  return  $output;
  }
if ($data['settings']['user_pre']=='velles_rrc') {  
  $output['price'] = 0;
  if (isset($item->price)) {
    foreach ($item->price as $price) {
      if ($price['name']=="РРЦ")      $output['price']    = trim($price);
      if ($price['name']=="Оптовая")  $output['mc_price'] = trim($price);
      }
    }  
  if (isset($item->currencyId)) {
    foreach ($item->currencyId as $currencyId) {
      if ($currencyId['name']=="Оптовая")  $output['mc_price_cur'] = trim($currencyId);
      }
    }
  return  $output;
  }
if ($data['settings']['user_pre']=='export_data_eburg4io') { 
  // КАТЕГОРИЯ
//  $output['cat_id'] = '';
  $output['cat_name'] = "(категория не указана)";
  if (!isset($item->cats)) return NULL;
  $cats = array();
  foreach ($item->cats->item as $category) $cats[] = trim($category);
  if (empty($cats[1])) return NULL; // ЭТО МУСОР
  $output['cat_name'] = $cats[0];
  $output['par_cat_name'] = $cats[1];
  // ИЗОБРАЖЕНИЕ 
  $output['image'] = array();
  if (!empty($item->images->large->list->path)) $output['image'][] = $data['settings']['img_path'] . trim($item->images->large->list->path);
  return $output;
  }
if ($data['settings']['user_pre']=='nikart.com.ua') { 
/*
<categories>
<main id="12">Босоножки, весна-лето</main>
</categories>
*/
    $output['cat_id'] = trim($item->categories->main['id']);
/*
<photo original="http://nikart.com.ua/uploads/products/4019/33136-orig.jpg">
<large width="1024" height="1024">
http://nikart.com.ua/uploads/products/4019/33136-lg.jpg
</large>
<medium width="800" height="800">
http://nikart.com.ua/uploads/products/4019/33136-md.jpg
</medium>
<small width="640" height="640">
http://nikart.com.ua/uploads/products/4019/33136-sm.jpg
</small>
<extraSmall width="320" height="320">
http://nikart.com.ua/uploads/products/4019/33136-xs.jpg
</extraSmall>
</photo>
*/

  $output['image'] = array();
  $output['image'][] = $data['settings']['img_path'] . trim($item->photos->main->medium);
  foreach ($item->photos->photo as $image) $output['image'][] = $data['settings']['img_path'] . trim($image->medium);
  
/*
<price>
<wholesale currency="UAH">580.00</wholesale>
<retail currency="UAH">680.00</retail>
</price>
*/  
  $output['price'] = trim($item->price->retail);
  return $output;
  }
if ($data['settings']['user_pre']=='topol_qty') { 
  $output['quantity'] = 0;
  foreach ($item->quantity->store as $value) {
    if ($value['id']==2) {
      $output['quantity'] += (int)$value;
      }
    }
  return $output;
  }        
if ($data['settings']['user_pre']=='all_outlets') { 
  $output['quantity'] = 0;
  foreach ($item->outlets->outlet as $value) {
    $output['quantity'] += (int)$value['instock'];
    }
  return $output;
  }        
if ($data['settings']['user_pre']=='minerva-sewing.ru') {
  $output['sku'] = trim($item->vendor_code);
  if (empty($output['sku'])) $output['sku'] = $item['id'];
  return $output;
  }
if ($data['settings']['user_pre']=='novatour.ru') {
  if (!empty($item->inventory)) {
    foreach ($item->inventory as $stock) {
      if (isset($stock['code']) && $stock['code']=="amurskaya") $output['quantity'] = trim($stock);
      }
    }
/*
<price name="РРЦ" code="roznichnaya">1290</price>
<price name="БОЦ" code="boc">842</price>
<price name="Ваша цена" code="personal_price">826</price>
*/
  foreach ($item->price as $value) {
    if (isset($value['name']) && $value['name']=='РРЦ')   $output['price'] = $value;
    }
  return $output;
  }
if ($data['settings']['user_pre']=='js-company') {
  if (!empty($item->typePrefix)) {
    $output['name'] = trim($item->typePrefix); 
    }

  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']!='Цвет') continue;
      if (!empty($item->url)) {
        $value = htmlspecialchars(trim($value));
        if ($value && !empty($data['options']['param_Цвет']['dest_id'])) {
          $option = array(
          'option_id'     => $data['options']['param_Цвет']['dest_id'],
          'value'         => $value,
          'price_prefix'  => $data['settings']['dov_price_prefix'],
          'required'      => $data['settings']['dov_required'],
          'subtract'      => $data['settings']['dov_subtract'],
          'quantity'      => $output['quantity'],
          'price'         => 0,
          'optsku'        => $this->ParseJS(trim($item->url),$value),
          );
          $output['option'][] = $option;
          }
        }
      }
    }

  return $output;
  }
if ($data['settings']['user_pre']=='clear-fit_attributes') {
/*
<descr>
Обновленная версия популярной модели серии Eco ET20 - для интенсивных и продолжительных тренировок. Усиленная конструкция и новый дизайн дополнили концепцую топовой модели серии. Мощное основание этой модели совместно с просторной беговой поверхностью гарантируют высокий комфорт и продуктивность занятий спортом. Надежный двигатель 3 л.с. обеспечивает широкий диапазон скорости от 0.8 км/ч до 20 км/ч. Просторное беговое полотно&nbsp;Diamond&nbsp;шириной&nbsp;535 мм&nbsp;надежная дека 20 мм отвечают за комфорт и безопасность тренировок повышенной скорости. Возможность применения телеметрических датчиков пульса повышают эффективность кардиоваскулярных тренировок. Прочная рама, утолщенная дека, увеличенный диаметр роллеров 46 мм обеспечивают дополнительный ресурс работы тренажера. Усиленная конструкция рассчитана на тренировки пользователями&nbsp;весом до 160 кг. Новая консоль отличается подачей и легкостью восприятия информации о ходе тренировок. 5&nbsp;LCD дисплеев&nbsp; в постоянном режиме выводят на экран все основные параметры тренировки: время, скорость, дистанция, калории, пульс и полную информацию по выполнению программы, что обеспечивает полный контроль ежесекундно. В модели 20AI предусмотрены дополнительные программы для обеспечения эффективности и оптимизации занятий. Режим &quot;Нормализация&quot; позволяет правильно закончить аэробную тренировку и снять дополнительную нагрузку с кардио-системы. BMI позволяет наглядно оценивать эффективность тренировок от занятия к занятию, контролировать изменения организма. Русифицированное управление значительно упрощает и облегчает настройку программ и проведение тренировок. Система амортизации&nbsp;SensibleCushion&nbsp;из восьми эластомеров, сбалансированно расположенных по периметру деки, эффективно снижает нагрузку на позвоночник и бережет Ваши суставы. Данная система амортизации подходит для всех возрастных групп пользователей. На поручнях расположены клавиши &laquo;быстрого доступа&raquo;, таким образом, Вы легко и быстро можете изменять скорость и угол наклона во время движения. Складная конструкция облегчает процесс перемещения и хранения тренажера. Газовый амортизатор обеспечивает плавное и мягкое опускание бегового полотна, безопасно, легко и бесшумно. При разработке тренажеров компания Clear-Fit придерживается принципа всестороннего контроля качества &laquo;изнутри и снаружи&raquo;, поэтому используются&nbsp; только самые качественные комплектующие и новейшие разработки. Таким образом, достигается максимальный контроль качества, надежности, безопасности и обеспечивается повышенный комфорт при использовании тренажера. Предлагаем ознакомиться с&nbsp;программой ежедневных тренировок&nbsp;(с понедельника по субботу), составленной профессиональным тренером.
<h2>ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ</h2>
<table>
<tr>
<td>Мощность мотора, л/с</td>
<td>3.0</td>
</tr>
<tr>
<td>Уровень шума</td>
<td>низкий</td>
</tr>
*/
  if (!empty($item->descr)) {
    $block = html_entity_decode ($item->descr);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape($block) . "', user = '" . $this->db->escape($data['user']) . "'");

    $attribute = str_replace("<h2>",     "|",  $item->descr);
    $attribute = str_replace("</h2>",    "",   $attribute);
    $attribute = str_replace("</tr>",    "",   $attribute);
    $attribute = str_replace("</td>",    "",   $attribute);
    $attribute = str_replace("</table>", "",   $attribute);
    $blocks    = explode ('|', $attribute);
//    $blocks[0] - описание  
//    $blocks[1] - таблица ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ  
//    $blocks[2] - таблица КОНСТРУКТИВНЫЕ ОСОБЕННОСТИ 
    foreach ($blocks as $key => $block) {
      if ($key==0) continue;
      $block = trim(str_replace("<table>", "|",   $block));
      $parts = explode ('|', $block);
      $group_name = trim($parts[0]); // ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ  
      $group_data = trim($parts[1]); // <tr> <td>Мощность мотора, л/с<td>3.0 <tr> и тд
      $attributes = trim(str_replace("<tr>", "|",   $group_data)); // | <td>Мощность мотора, л/с<td>3.0 | и тд
      $attributes = explode ('|', $attributes);
      foreach ($attributes as $attribute) { // <td>Мощность мотора, л/с<td>3.0
        $attribute = trim(str_replace("<td>", "|",   $attribute)); // | Мощность мотора, л/с | 3.0
        $attribute = explode ('|', $attribute);
        if (!empty($attribute[1])) {
          $key  = $group_name . '_' . trim($attribute[1]);
          if (!empty($attribute[2])) $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$attribute[2]);
          } 
        }
      }
    }
  }
if ($data['settings']['user_pre']=='jorgen-svensson_attributes') {
  // <attrs>
  // <group name="Биомеханические свойства">
  // <attr name="Посадка">вертикальная</attr>
  if (!empty($item->attrs)) {
    foreach ($item->attrs->group as $group) {
      $key  = 'attrs_' . trim($group['name']) . '_';
      foreach ($group->attr as $attr) {
        $output = $this->model_zoxml2_zoxml2->doParams($key . trim($attr['name']),$output,$data,trim($attr));
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='sunstones_attributes_from_description') {
  // <description><![CDATA[Артикул: 047306 <br>Ширина: 7 мм.  <br>Бренд: XUPING    <br>Вес: 2,7 г.   <br>Длина: 18 см.
  if (!empty($item->description)) {
    $parts = explode ('|', str_replace("<br>","|",trim($item->description)));
    foreach ($parts as $part) {
      $attribute = explode (':', trim($part));
      if (!empty($attribute[1])) {
        $key  = 'atribut_' . trim($attribute[0]);
        if (!isset($data['zoAttributes'])) $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,trim($attribute[1]));
        else                               $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($attribute[0]))] = trim($attribute[1]);
        }
      }
    }
//  if (empty($output['vendor'])) $output['vendor'] = '(производитель не указан)';
  return $output;
  }
if ($data['settings']['user_pre']=='sunstones_attributes') {
  // <atribut>Ширина---13 мм.|Бренд---XUPING|Вес---1,9 г.|Камень---Фианит(нат.)|Качество---Не темнеет, не облазит|Материал---Медицинский сплав|Покрытие---родий|Цвет---Хрустальный|Цвет3---Серебряный</atribut>
  if (!empty($item->atribut)) {
    $parts = explode ('|', $item->atribut);
    foreach ($parts as $attribute) {
      $attribute = trim(str_replace("---","|",$attribute));
      $parts2 = explode ('|', $attribute);
      $key   = trim($parts2[0]);
      if (isset($parts2[1])) {
        $value = trim($parts2[1]);
        if ($key && $value) {
          $key  = 'atribut_' . $key;
          $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$value);
          }
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='zavodsporta_attributes') {
  // <Атрибуты>Вес: 85 кг | Габариты: 119 х 60 х 137 см | Max нагрузка: 160 кг | Цвет: цвет сиденья-черный | Особенности: вертикальный | Наличие: No</Атрибуты>
  if (!empty($item->Атрибуты)) {
    $parts = explode ('|', $item->Атрибуты);
    foreach ($parts as $attribute) {
      $parts2 = explode (':', $attribute);
      $key   = trim($parts2[0]);
      $value = trim($parts2[1]);
      if ($key && $value) {
        $key  = 'Атрибуты_' . $key;
        $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$value);
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='neotren_attributes') {
  if (!empty($item->attrs)) {
    foreach ($item->attrs->attr as $attribute) {
      if (!empty($attribute['name']))
      $key  = 'attr_' . trim($attribute['name']);
      $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,trim($attribute));
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='samsonopt_attributes') {
  if (!empty($item->Характеристики)) {
    foreach ($item->Характеристики->Характеристика as $attribute) {
      $parts = explode (':', $attribute);
      if (empty($parts[1])) continue;
      $key = trim($parts[0]);
      if (!$key) continue;
      $key  = 'Характеристика_' . $key;
      $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,trim($parts[1]));
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='timeofstyle') {
  if (!empty($item['id'])) {
    $parts = explode ('|', trim($item['id']));
    if (count($parts)<3) return NULL;
    $output['sku']       = (string)$parts[0];
    $output['model']     = (string)$parts[1];
    return $output;
    }
  return NULL;
  }
if ($data['settings']['user_pre']=='ebazaar') {
  if (!empty($item->category_id)) {
    $output['cat_id'] = trim($item->category_id);
    }
  // <articul>LXX65031-051</articul>
  if (!empty($item->articul)) {
    $parts = explode ('-', trim($item->articul));
    $output['ean'] = trim($parts[0]);
    if (isset($parts[2])) $output['ean'] .= "-" . trim($parts[1]);
    }  
  return $output;
  }
if ($data['settings']['user_pre']=='src2img') {
  if (isset($item->images->image)) {
    foreach ($item->images->image as $value) {
      $output['image'][] = $data['settings']['img_path'] . trim($value['src']);
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='maomag') {
  if (!empty($item->Изображения)) {
    $urls = explode ('|', (string)$item->Изображения);
    foreach ($urls as $value) {
      $output['image'][] = trim($value);
      }
    }
  if (!empty($item->Характеристики)) {
    $parts = explode ('|', trim($item->Характеристики));
    foreach ($parts as $value) {
      $keys = explode (':', trim($value));
      $key  = trim($keys[0]);
      if ($key) {
        $key  = 'Характеристики_' . $key;
        $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$keys[1]);
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='url3sku') {
  if (isset($item->url)) {
    $urls = explode ('?', (string)$item->url);
    $output['sku'] = md5(trim($urls[0]));
    }
  return $output;
  }
if ($data['settings']['user_pre']=='ignore_category') {
  $output['cat_id']   = '';
  return $output;
  }
if ($data['settings']['user_pre']=='moderni-boty-cz') {
  if (isset($item->categorys->categoryid)) $output['cat_id']  = (string)$item->categorys->categoryid;
  else                                     $output['cat_id']  = '';
  if (isset($item->images->generalimage))  {
    foreach ($item->images->generalimage as $value) {
      $output['image'][] = $data['settings']['img_path'] . trim((string)$value['link']);
      }
    }
  if (isset($item->images->otherimage)) {
    foreach ($item->images->otherimage as $value) {
      $output['image'][] = $data['settings']['img_path'] . trim((string)$value['link']);
      }
    }
  if (isset($item->stock->value)) {
    foreach ($item->stock->value as $value) {
      $output['option'][] = array(
        'option_id'     => $data['options']['stock']['dest_id'],
        'value'         => htmlspecialchars($value['size']),
        'price'         => 0,
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'required'      => $data['settings']['dov_required'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => (int)$value,
        );
    }
  }
}
if ($data['settings']['user_pre']=='lightstar.ru') {
  $output['cat_id']   = '';
  if (isset($item->Остаток['Остаток']))          $output['quantity'] = trim($item->Остаток['Остаток']);
  if (isset($item->Артикул['Артикул']))          $output['sku']      = trim($item->Артикул['Артикул']);
  if (isset($item->Цены['Розничная']))           $output['price']    = trim($item->Цены['Розничная']);
  return $output;
  }
if ($data['settings']['user_pre']=='alexika_ru') {
  $output['vendor'] = $data['alexika_brands'][(string)$item->brand];
  $output['cat_id'] = (string)$item->subcategory;
  foreach ($item->attributes->attribute as $value) {
    $key = $data['alexika_attributes'][(string)$value->id];
    $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$value->value);
    }
  foreach ($item->images->image as $value) {
    $output['image'][] = (string)$value->url;
    }
  foreach ($item->schemes->scheme as $value) {
    $output['image'][] = (string)$value->url;
    }
  $output['name']        .= ' ' . (string)$item->short_description; 
  $output['description'] .= ' ' . (string)$item->technology; 
  return $output;
  }
if ($data['settings']['user_pre']=='dveri_com') {
  if (isset($item->category_id)) $output['cat_id']   = (string)$item->category_id;
  return $output;
  }
if ($data['settings']['user_pre']=='galser.ru') {
  if (isset($item->Свойство)) {
    foreach ($item->Свойство as $item_value) {
      if (isset($item_value->Наименование)) {
        $key = 'Свойство_' . trim($item_value->Наименование);
        // Проверка наличия в базе
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        if (!$query->row) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
          }
        // ОБРАБОТКА
        $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$item_value->Значение);
        }
      }
    }
  if (isset($item->case->image)) $output['image'][] = (string)$item->case->image;
  return $output;
  }
if ($data['settings']['user_pre']=='mib01') {
  $output['image'][] = $data['settings']['img_path'] . trim((string)$item['Идентификатор']) . '.jpg';
  if (isset($item['Идентификатор']))    $output = $this->model_zoxml2_zoxml2->doParams('Идентификатор',$output,$data,$item['Идентификатор']);
  if (isset($item['Артикул']))          $output = $this->model_zoxml2_zoxml2->doParams('Артикул',$output,$data,$item['Артикул']);
  if (isset($item['Модель']))           $output = $this->model_zoxml2_zoxml2->doParams('Модель',$output,$data,$item['Модель']);
  if (isset($item['Наименование']))     $output = $this->model_zoxml2_zoxml2->doParams('Наименование',$output,$data,$item['Наименование']);
  if (isset($item['ОписаниеКороткое'])) $output = $this->model_zoxml2_zoxml2->doParams('ОписаниеКороткое',$output,$data,$item['ОписаниеКороткое']);
  if (isset($item['ОписаниеДлинное']))  $output = $this->model_zoxml2_zoxml2->doParams('ОписаниеДлинное',$output,$data,$item['ОписаниеДлинное']);
  $output['cat_id']   = '';
  if (isset($item->Цена)) {
    foreach ($item->Цена as $item_value) {
      if (isset($item_value['ТипЦены'])) {
        $output = $this->model_zoxml2_zoxml2->doParams('Цена_' . $item_value['ТипЦены'],$output,$data,$item_value['Цена']);
        }
      }
    }
  if (isset($item->Свойство)) {
    foreach ($item->Свойство as $item_value) {
      if (isset($item_value['Наименование'])) {
        $key = 'Свойство_' . $item_value['Наименование'];
        if ($item_value['Наименование']=='Производитель') {
          $output['vendor']   = (string)$item_value;
          $output['cat_name'] = (string)$item_value;
          }
        $output = $this->model_zoxml2_zoxml2->doParams($key,$output,$data,$item_value);
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='eroticfantasy') {
  // $item->Артикул - артикул опции. Загоняем в модель
  // $item->Группа  - артикул (связующее). Если оно не задано - используем модель
  if (!empty($item->Группа)) $output['sku']=trim($item->Группа);
  else if (!empty($item->Артикул)) $output['sku']=trim($item->Артикул);
  return $output;
  }
if ($data['settings']['user_pre']=='bumba.ru') {
  if (isset($item->url)) {
    $urls = explode ('?', (string)$item->url);
    $output['mpn'] = md5($urls[0]);
    }
  }
if ($data['settings']['user_pre']=='p5s') {
  if (isset($item['prodID'])) $output = $this->model_zoxml2_zoxml2->doParams('prodID',$output,$data,$item['prodID']);
  if (isset($item['vendorCode'])) $output = $this->model_zoxml2_zoxml2->doParams('vendorCode',$output,$data,$item['vendorCode']);
  if (isset($item['name'])) $output = $this->model_zoxml2_zoxml2->doParams('name',$output,$data,$item['name']);
  if (isset($item['vendor'])) $output = $this->model_zoxml2_zoxml2->doParams('vendor',$output,$data,$item['vendor']);
  if (isset($item['brutto'])) $output = $this->model_zoxml2_zoxml2->doParams('brutto',$output,$data,$item['brutto']);
  if (isset($item['batteries'])) $output = $this->model_zoxml2_zoxml2->doParams('batteries',$output,$data,$item['batteries']);
  if (isset($item['pack'])) $output = $this->model_zoxml2_zoxml2->doParams('pack',$output,$data,$item['pack']);
  if (isset($item['material'])) $output = $this->model_zoxml2_zoxml2->doParams('material',$output,$data,$item['material']);
  if (isset($item['lenght'])) $output = $this->model_zoxml2_zoxml2->doParams('lenght',$output,$data,$item['lenght']);
  if (isset($item['diameter'])) $output = $this->model_zoxml2_zoxml2->doParams('diameter',$output,$data,$item['diameter']);
  if (isset($item['CollectionName'])) $output = $this->model_zoxml2_zoxml2->doParams('CollectionName',$output,$data,$item['CollectionName']);

  if (isset($item->price['RetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_RetailPrice',$output,$data,$item->price['RetailPrice']);
  if (isset($item->price['BaseRetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseRetailPrice',$output,$data,$item->price['BaseRetailPrice']);
  if (isset($item->price['WholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_WholePrice',$output,$data,$item->price['WholePrice']);
  if (isset($item->price['BaseWholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseWholePrice',$output,$data,$item->price['BaseWholePrice']);
  if (isset($item->assortiment->assort['sklad'])) $output = $this->model_zoxml2_zoxml2->doParams('assortiment_sklad',$output,$data,$item->assortiment->assort['sklad']);
  if (isset($item->assortiment->assort['color'])) $output = $this->model_zoxml2_zoxml2->doParams('assortiment_color',$output,$data,$item->assortiment->assort['color']);
  if (isset($item->assortiment->assort['size'])) $output = $this->model_zoxml2_zoxml2->doParams('assortiment_size',$output,$data,$item->assortiment->assort['size']);
  if (isset($item->assortiment->assort['barcode'])) $output = $this->model_zoxml2_zoxml2->doParams('assortiment_barcode',$output,$data,$item->assortiment->assort['barcode']);
  
  if (isset($item->categories->category['Name']))     $output['par_cat_name'] = (string)$item->categories->category['Name'];
  if (isset($item->categories->category['subName']))  $output['cat_name']     = (string)$item->categories->category['subName'];
  $output['cat_id']   = '';
  if (isset($item->assortiment->assort)) {
    $option_key = 'assortiment_color_size';
    if($data['options'][$option_key]['dest_id']>0) {
      foreach ($item->assortiment->assort as $assort) {
          $ShippingDate = '';
          if (!empty($assort['ShippingDate'])) {
            $parts = explode (' ', $assort['ShippingDate']);
            if ($parts[0]) $ShippingDate = 'Товар может быть отправлен: ' . $parts[0];
            }
          $value = $assort['color'];
          if ($assort['color']!='' && $assort['size']!='') $value .= '/';
          $value .= $assort['size']; 
          if (isset($data['module']['io2'])) {
            $output['option'][] = array (
              'option_id'     => $data['options'][$option_key]['dest_id'], 
              'value'         => $value==''?'цвет и размер не указаны':$value,
              'price_prefix'  => $data['settings']['dov_price_prefix'],
              'required'      => $data['settings']['dov_required'],
              'subtract'      => $data['settings']['dov_subtract'],
              'price'         => 0,
              'quantity'      => $assort['sklad'],
              'sku'           => $assort['aID'],
              'description'   => $ShippingDate,
              );
            }
          else {
            $output['option'][] = array (
              'option_id'     => $data['options'][$option_key]['dest_id'], 
              'value'         => $value==''?'цвет и размер не указаны':$value,
              'price_prefix'  => $data['settings']['dov_price_prefix'],
              'required'      => $data['settings']['dov_required'],
              'subtract'      => $data['settings']['dov_subtract'],
              'price'         => 0,
              'quantity'      => $assort['sklad'],
              );
            }
        }
      }
    }
  return $output;
  }
if ($data['settings']['user_pre']=='p5s4RO') {
  if (isset($item['prodID'])) $output = $this->model_zoxml2_zoxml2->doParams('prodID',$output,$data,$item['prodID']);
  if (isset($item['vendorCode'])) $output = $this->model_zoxml2_zoxml2->doParams('vendorCode',$output,$data,$item['vendorCode']);
  if (isset($item['name'])) $output = $this->model_zoxml2_zoxml2->doParams('name',$output,$data,$item['name']);
  if (isset($item['vendor'])) $output = $this->model_zoxml2_zoxml2->doParams('vendor',$output,$data,$item['vendor']);
  if (isset($item['brutto'])) $output = $this->model_zoxml2_zoxml2->doParams('brutto',$output,$data,$item['brutto']);
  if (isset($item['batteries'])) $output = $this->model_zoxml2_zoxml2->doParams('batteries',$output,$data,$item['batteries']);
  if (isset($item['pack'])) $output = $this->model_zoxml2_zoxml2->doParams('pack',$output,$data,$item['pack']);
  if (isset($item['material'])) $output = $this->model_zoxml2_zoxml2->doParams('material',$output,$data,$item['material']);
  if (isset($item['lenght'])) $output = $this->model_zoxml2_zoxml2->doParams('lenght',$output,$data,$item['lenght']);
  if (isset($item['diameter'])) $output = $this->model_zoxml2_zoxml2->doParams('diameter',$output,$data,$item['diameter']);
  if (isset($item['CollectionName'])) $output = $this->model_zoxml2_zoxml2->doParams('CollectionName',$output,$data,$item['CollectionName']);

  if (isset($item->price['RetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_RetailPrice',$output,$data,$item->price['RetailPrice']);
  if (isset($item->price['BaseRetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseRetailPrice',$output,$data,$item->price['BaseRetailPrice']);
  if (isset($item->price['WholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_WholePrice',$output,$data,$item->price['WholePrice']);
  if (isset($item->price['BaseWholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseWholePrice',$output,$data,$item->price['BaseWholePrice']);
 
  if (isset($item->price['WholePrice']) && !empty($item->price['Discount']) && isset($data['settings']['update_special'])) $output['oldprice'] = trim((string)$item->price['WholePrice']); 
  
  if (isset($item->categories->category)) {
    foreach ($item->categories->category as $category) {
      if (isset($category['Name']))     $output['par_cat_name'] = trim($category['Name']);
      if (isset($category['subName']))  $output['cat_name']     = trim($category['subName']);
      }
    }
  $output['cat_id']   = '';
  return $output;
  }
if ($data['settings']['user_pre']=='p5s4RO_one_level') {
  if (isset($item['prodID'])) $output = $this->model_zoxml2_zoxml2->doParams('prodID',$output,$data,$item['prodID']);
  if (isset($item['vendorCode'])) $output = $this->model_zoxml2_zoxml2->doParams('vendorCode',$output,$data,$item['vendorCode']);
  if (isset($item['name'])) $output = $this->model_zoxml2_zoxml2->doParams('name',$output,$data,$item['name']);
  if (isset($item['vendor'])) $output = $this->model_zoxml2_zoxml2->doParams('vendor',$output,$data,$item['vendor']);
  if (isset($item['brutto'])) $output = $this->model_zoxml2_zoxml2->doParams('brutto',$output,$data,$item['brutto']);
  if (isset($item['batteries'])) $output = $this->model_zoxml2_zoxml2->doParams('batteries',$output,$data,$item['batteries']);
  if (isset($item['pack'])) $output = $this->model_zoxml2_zoxml2->doParams('pack',$output,$data,$item['pack']);
  if (isset($item['material'])) $output = $this->model_zoxml2_zoxml2->doParams('material',$output,$data,$item['material']);
  if (isset($item['lenght'])) $output = $this->model_zoxml2_zoxml2->doParams('lenght',$output,$data,$item['lenght']);
  if (isset($item['diameter'])) $output = $this->model_zoxml2_zoxml2->doParams('diameter',$output,$data,$item['diameter']);
  if (isset($item['CollectionName'])) $output = $this->model_zoxml2_zoxml2->doParams('CollectionName',$output,$data,$item['CollectionName']);

  if (isset($item->price['RetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_RetailPrice',$output,$data,$item->price['RetailPrice']);
  if (isset($item->price['BaseRetailPrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseRetailPrice',$output,$data,$item->price['BaseRetailPrice']);
  if (isset($item->price['WholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_WholePrice',$output,$data,$item->price['WholePrice']);
  if (isset($item->price['BaseWholePrice'])) $output = $this->model_zoxml2_zoxml2->doParams('price_BaseWholePrice',$output,$data,$item->price['BaseWholePrice']);
 
  if (isset($item->price['WholePrice']) && !empty($item->price['Discount']) && isset($data['settings']['update_special'])) $output['oldprice'] = trim((string)$item->price['WholePrice']); 
  
  if (isset($item->categories->category)) {
    $output['cat_names'] = array();
    foreach ($item->categories->category as $category) {
      if (isset($category['Name']))     {
        $output['cat_name'] = (string)$category['Name'];
        if (isset($category['subName']))  $output['cat_name']     .= " / " . (string)$category['subName'];
        $output['cat_names'][] = $output['cat_name'];
        }
      }
    $output['cat_name'] = array_shift ($output['cat_names']);
    // Множественная привязка
    if (isset($data['settings']['link2category_ids'])) {
      while (count($output['cat_names'])) {
        $cat_id = $this->model_zoxml2_zoxml2->getCategory($session_key,array_shift($output['cat_names']));
        if ($cat_id) $output['category_ids'][$cat_id] = $cat_id;
        }
      }
    }
  $output['cat_id']   = '';
  return $output;
  }
if ($data['settings']['user_pre']=='eroticfantasy_good_short') {
  if (!isset($item->scu))   return NULL;
  $scu = (string)$item->scu;
  if (!isset($item->price)) return NULL;
  $quantity = isset($item->quantity)?(int)$item->quantity:0;
  $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->price);
  if (is_nan($output['price'])) return NULL;
  $output['price']  = round ( $output['price']+5, 1, PHP_ROUND_HALF_UP);
  if (isset($item->oldprice)) {
    if ((float)$item->oldprice > $output['price']) {
        $output['special']  = $output['price'];
        $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->oldprice);
        if (is_nan($output['price'])) return NULL;
        $output['price']  = round ( $output['price']+5, 1, PHP_ROUND_HALF_UP);
        }
    }
  // ищем опцию
  $query = $this->db->query("SELECT pov.product_option_value_id,p.location,pov.product_id FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pov.product_id) WHERE pov.sku = '" . $this->db->escape($scu) . "'"); 
  if ($query->row) {
    if ($query->row['location']!='YML7d35847bbd26df7dc7d2546b2ae08a46') return NULL;
    // OPTION
    $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$quantity . "'  WHERE product_option_value_id = '" . (int)$query->row['product_option_value_id'] . "'");
    // PRODUCT
    $product_query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$query->row['product_id'] . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$product_query->row['total'] . "' WHERE product_id = '" . (int)$query->row['product_id'] . "'");
    // АКЦИЯ
    if (isset($data['settings']['update_special'])) {
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$query->row['product_id'] . "'");
      if (isset($output['special'])) {
        foreach ($data['customer_groups'] as $customer_group_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$query->row['product_id'] . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
          }
        }
      }
    return NULL;
    }
  // ищем товар с таким артикулом
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($scu) . "'"); 
  if ($query->row) {
    if ($query->row['location']!='YML7d35847bbd26df7dc7d2546b2ae08a46') return NULL;
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$query->row['product_id'] . "'");
    // АКЦИЯ
    if (isset($data['settings']['update_special'])) {
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$query->row['product_id'] . "'");
      if (isset($output['special'])) {
        foreach ($data['customer_groups'] as $customer_group_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$query->row['product_id'] . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
          }
        }
      }
    }
  return NULL;
  }
if ($data['settings']['user_pre']=='stan.su_roption') {
  $COLOR_ID = 13;
  $SIZES_ID = 15;
  if (isset($item->colors_of_model->color_of_model)) {
    $output['roption'] = array();
    foreach ($item->colors_of_model->color_of_model as $offer) {
      if (isset($offer->color)) {
        if (isset($offer->sizes->size)) {
          foreach ($offer->sizes->size as $size) {
            $variant = array (
              'options'         => array(),        // Позже
              'images'          => array(),        // Позже
              'sku'             => '',             // Не используется
              'model'           => '',             // Позже
              'extra'           => '',             // Позже
              'location'        => '',             // Не используется
              'quantity'        => 0,              // Позже
              'quantity_eu'     => 0,              // Позже
              'price'           => 0,              // Позже
              'price_prefix'    => '=',            // ВСЕГДА!
              'weight_prefix'   => '=',            // ВСЕГДА!
              'weight'          => '0',            // ВСЕГДА!
              );
            $variant['options'][$COLOR_ID] = array (
              'option_id'     => $COLOR_ID, 
              'value'         => (string)$offer->color->title,
              );
            $variant['options'][$SIZES_ID] = array (
              'option_id'     => $SIZES_ID, 
              'value'         => (string)$size->title,
              );
            if (isset($size->price))          $variant['price']     = (float)$size->price;
            if (isset($offer->color->rgb))    $variant['extra']     = (string)$offer->color->rgb;
            if (isset($offer->images->image)) $variant['image']     = (string)$offer->images->image;
            if (isset($size->summary))        $variant['model']     = (string)$size->summary;
            if (isset($size->quantity_on_storages->storage)) {
              foreach ($size->quantity_on_storages->storage as $storage) {
                if ($storage->title=='Москва')   $variant['quantity']    = (int)$storage->quantity;
                if ($storage->title=='Основной') $variant['quantity_eu'] = (int)$storage->quantity;
                }
              } 
            $output['roption'][] = $variant;  
            }
          }
        }
      }
    return $output;
    }
  else  return NULL;
  }
if ($data['settings']['user_pre']=='modniy-ostrov.com') {
  if (isset($item->categoryId)) {
    $output['cat_name'] = trim($item->categoryId);
    $output['cat_id']   = '';
    }
  return $output;
  }
if ($data['settings']['user_pre']=='SNT') {
  if (isset($item->category)) {
    $categoryIds = explode (',', (string)$item->category);
    if (isset($categoryIds[0])) {
      $output['cat_id'] = trim($categoryIds[0]);
      }
    else return NULL; 
    }  
  else return NULL;   
  $output['model'] = isset($item->sku)?(string)$item->sku:''; 
  if (isset($item->features))  {
    foreach ($item->features->feature as $item_value) {
      if (isset($item_value->name))   {
        $option_key = 'feature_' . trim($item_value->name);
        $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,trim($item_value->value));
        }
      }
    }
  $output['weight'] = (float)$output['weight'] / 1000;
  return $output;
  }
if ($data['settings']['user_pre']=='Tango_Комплектатор') {  
  $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->price);
  if (is_nan($output['price'])) return NULL; 
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => $output['price'],
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $item['available']=='true'?$output['quantity']:0,
            );
          }
        $output['price']    = 0;
        $output['quantity'] = $item['available']=='true'?$output['quantity']:0;
        return $output;
        }
      }
    }
  return NULL; // Блокируем загрузку товаров без опций
  }
if ($data['settings']['user_pre']=='ALSO') {
  foreach ($item->Qty as $Qty) {
    if ($Qty['WarehouseID']=='1') $output['quantity'] = (string)$Qty->QtyAvailable;
    }
  foreach ($item->Product->Grouping->GroupBy as $GroupBy) {
    if ($GroupBy['GroupID']=='VendorID') $output['vendor']   = (string)$GroupBy['Value'];
    if ($GroupBy['GroupID']=='ClassID')  $output['cat_name'] = (string)$GroupBy['Value'];
    }
  if (isset($item->Price->UnitPrice)) { //price
    $output['price'] = (string)$item->Price->UnitPrice;    
    }
  if (isset($item->Product->ProductID)) { //SKU
    $output['sku'] = (string)$item->Product->ProductID;    
    }
  if (isset($item->Product->PartNumber)) { //Model
    $output['model'] = (string)$item->Product->PartNumber;    
    }
  if (isset($item->Product->EANCode)) { //EAN
    $output['ean'] = (string)$item->Product->EANCode;    
    }
  if (isset($item->Product->Description)) { //Name
    $output['name'] = (string)$item->Product->Description;    
    }
  if (isset($item->Product->LongDesc)) { //Description
    $output['description'] = (string)$item->Product->LongDesc;    
    }
  if (isset($item->Product->PeriodofWarranty)) { //Attribute:Warranty
    $output = $this->model_zoxml2_zoxml2->doParams('PeriodofWarranty',$output,$data,(string)$item->Product->PeriodofWarranty);   
    }
  }
if ($data['settings']['user_pre']=='karnatextile_pure') { 
  $output['description']    = '';
  $output['sku']            = (string)$item["Артикул"];
  $output['model']          = (string)$item["Идентификатор"];
  $output['price']          = (string)$item["Цена"]; 
  $output['cat_name']       = (string)$item["Категория"];
  $output['quantity']       = (string)$item["ЕстьВНаличие"]=='да'?$data['settings']['quantity']:0;
  $output['name']           = htmlspecialchars($item["Наименование"],ENT_QUOTES); 
  $output['image'][]        = $data['settings']['img_path'] . (string)$item["Файл"];
  $output = $this->model_zoxml2_zoxml2->doParams('ХарактеристикаНоменклатуры',$output,$data,$value['ХарактеристикаНоменклатуры']);  
//  $output = $this->model_zoxml2_zoxml2->doParams('Идентификатор',$output,$data,$value['Идентификатор']);  
//  $output = $this->model_zoxml2_zoxml2->doParams('Артикул',$output,$data,$value['Артикул']);  
  $output['vendor']         = '(производитель не указан)'; 
  foreach ($item->Свойствы->Свойство as $value) {
    $option_key = 'Свойство_' . (string)$value['Имя'];
    if ($option_key=='Свойство_Изготовитель') $output['vendor'] = (string)$value['Значение'];
    else $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$value['Значение']);  
    }
  return $output; 
  }
if ($data['settings']['user_pre']=='karnatextile') { 
  $output['description']    = '';
  $output['sku']            = (string)$item["Артикул"];
  $output['model']          = (string)$item["Артикул"];
  $output['price']          = (string)$item["Цена"]; 
  $output['cat_name']       = (string)$item["Категория"];
  $output['name']           = htmlspecialchars($item["Наименование"],ENT_QUOTES); 
  $output['image'][]        = $data['settings']['img_path'] . (string)$item["Файл"];
  $poip = array();
  $poip[] = htmlspecialchars($data['settings']['img_path'] . (string)$item["Файл"],ENT_QUOTES);
  $output['vendor']         = '(производитель не указан)'; 
  foreach ($item->Свойствы->Свойство as $value) {
    $option_key = 'Свойство_' . (string)$value['Имя'];
    if ($option_key=='Свойство_Изготовитель') $output['vendor'] = (string)$value['Значение'];
    else $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$value['Значение']);  
    }
  $output['quantity']  = 0;
  switch (trim($item["ЕстьВНаличие"])) {
    case 'да':  { $output['quantity']  = $data['settings']['dov_quantity']; break; }
    case 'нет': { $output['quantity']  = 0; break; }
    default: $output['quantity']  = (int)$item["ЕстьВНаличие"];
    }
  if( !empty($data['options']['ХарактеристикаНоменклатуры']) && $data['options']['ХарактеристикаНоменклатуры']['dest_type']=='option' && $data['options']['ХарактеристикаНоменклатуры']['dest_id']>0) {
    
    $output['option'][] = array (
      'option_id'     => $data['options']['ХарактеристикаНоменклатуры']['dest_id'], 
      'value'         => (string)$item['ХарактеристикаНоменклатуры'],
      'required'      => 1,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => $output['quantity'] ,
      'poip'          => $poip,
      );
    }
  return $output; 
  }
if ($data['settings']['user_pre']=='karnatextile_комплектатор') { 
  $output['description']    = '';
  $output['sku']            = (string)$item["Артикул"];
  $output['model']          = (string)$item["Артикул"];
  $output['price']          = (string)$item["Цена"]; 
  $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$item["Цена"]);  
  $output['name']           = htmlspecialchars($item["Наименование"],ENT_QUOTES); 
  $output['cat_name']       = (string)$item["Категория"];
  $output['quantity']       = (string)$item["ЕстьВНаличие"]=='да'?10:0;
  $output['image'][]        = $data['settings']['img_path'] . (string)$item["Файл"];
  $output['vendor']         = '(производитель не указан)'; 
  foreach ($item->Свойствы->Свойство as $value) {
    $option_key = 'Свойство_' . (string)$value['Имя'];
    if ($option_key=='Свойство_Изготовитель') $output['vendor'] = (string)$value['Значение'];
    else $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,$value['Значение']);  
    $output = $this->model_zoxml2_zoxml2->doDefaultOption($option_key,$output,$data,$value['Значение']);
    }
  if( isset($data['options']['ХарактеристикаНоменклатуры']) && $data['options']['ХарактеристикаНоменклатуры']['dest_type']=='option' && $data['options']['ХарактеристикаНоменклатуры']['dest_id']>0) {
    $output['option'][] = array (
      'option_id'     => $data['options']['ХарактеристикаНоменклатуры']['dest_id'], 
      'value'         => (string)$item['ХарактеристикаНоменклатуры'],
      'required'      => $data['settings']['dov_required'],
      'price'         => 0,
      'price_prefix'  => $data['settings']['dov_price_prefix'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $item["ЕстьВНаличие"]=='да'?$data['settings']['dov_quantity']:0,
      );
    }
  return $output; 
  }
if ($data['settings']['user_pre']=='Номенклатура_Элемент') {
  $output['cat_id']   = (string)$item->ИДРодителя;
  return $output; 
  }
if ($data['settings']['user_pre']=='Номенклатура_Элемент_размеры') {
  $output['cat_id']   = (string)$item->ИДРодителя;
  // <Артикул>711386.301/L</Артикул>
  if (!empty($item->Артикул)) {
    $parts = explode ("/", trim($item->Артикул));
    $output['model'] = $parts[0];
    }
  return $output; 
  }
if ($data['settings']['user_pre']=='HappyGifts4io_v2') {
  $output['cat_id']   = trim($item->ИДРодителя);
  $output['name'] = str_replace('"', "'", trim($item->Описание));
  $output['ean']  = md5($output['name'] . "::" . $data['session_key']);
  $output['io_model'] = str_replace('"', "'", trim($item->НаименованиеПолное));
  $color = trim($item->Цвет);
  if ($color) {
    if (strpos ($output['name'],$color)===FALSE) $output['name'] .= ", " . $color; 
    }
  $output['model'] = $output['name'];
  $io_sku = trim($item->Артикул);
  if ($io_sku) {
    // ЕСЛИ АРТИКУЛ ОПЦИИ НЕ СОДЕРЖИТ ТОЧКУ, ТО ЭТО АРТИКУЛ ТОВАРА
    if (strpos ($io_sku,'.')===FALSE) {
      $output['sku'] = $io_sku;
      $output['upc'] = trim($item->ИД);
      return $output;
      }
    // <Артикул>613760.30/XS</Артикул> - УБИРАЕМ РАЗМЕР И ПОЛУЧАЕМ АРТИКУЛ ТОВАРА НУЖНОГО ЦВЕТА
    $parts = explode ('/', $io_sku);
    $output['sku'] = $parts[0];
    }
  return $output;
  }
if ($data['settings']['user_pre']=='Элемент') {  // Пример пользовательского препроцессора - обработка тега Элемент из Номенклатуры 1С
  $output['description']    = (string)$item["Описание"];
  $output['sku']            = (string)$item["Артикул"];
  $output['model']          = ''; 
  $output['price']          = (string)$item["Цена"]; 
  $output['name']           = (string)$item["Наименование"]; 
  $output['vendor']         = (string)$item["Производитель"]; 
  $output['cat_name']       = (string)$item["Категория"];
  return $output; 
  }
if ($data['settings']['user_pre']=='bemal_остатки') { 
  if (isset($item->ЗначенияСвойств->ЗначенияСвойства)) {
    foreach ($item->ЗначенияСвойств->ЗначенияСвойства as $attr) {
      if ($attr->Ид==84) {
        $sku      = isset($attr->Значение)?trim($attr->Значение):'';
        $quantity = isset($item->Количество)?$item->Количество:0;
        if ($sku) {
          // Сначала ищем в СВЯЗАННЫХ ОПЦИЯХ
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($sku) . "'"); 
          if ($query->row) {
            $product_id = $query->row['product_id'];
            $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($sku) . "'");
            $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
            $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
            return 1;
            }
          // Ищем в КОМПЛЕКТНЫХ ОПЦИЯХ
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE optsku = '" . $this->db->escape($sku) . "'");
          if ($query->row) {
            // Сздаем СВЯЗАННУЮ ОПЦИЮ
            $product_id = $query->row['product_id'];
            $option = array (
              'option_id'     => $product_id = $query->row['option_id'], 
              );
            $variant     = array (
              'options'                   => $option,
              'sku'                       => $sku,
              'ean'                       => '',
              'model'                     => '',
              'location'                  => '',
              'quantity'                  => $quantity,
              'weight'                    => 0,
              'price_prefix'              => '=',
              'price'                     => 0,
              'stock_status_id'           => 5,
              );
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
            $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
            $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
            return 1;
            }
          return 0;
          }
        }
      }
    }
  return 0; 
  }
if ($data['settings']['user_pre']=='driada-sport.ru') {
//  $output['description']    = (string)$item["Описание"];
  $output['sku']            = (string)$item["Артикул"];
  $output['model']          = ''; 
  $output['price']          = (string)$item["Цена"]; 
  $output['name']           = (string)$item["Наименование"]; 
//  $output['vendor']         = (string)$item["Производитель"]; 
  $output['cat_name']       = (string)$item["Уровень2"];
  $output['par_cat_name']   = (string)$item["Уровень1"];
  $output['quantity']       = (string)$item["Остаток"]; 
  return $output; 
  }
if ($data['settings']['user_pre']=='group_id') {
  $output['sku']            = isset($item['group_id'])?(string)$item['group_id']:'';
  return $output; 
  }

if ($data['settings']['user_pre']=='set_user_model_dg') {
  $output['model'] = 'SBN-'.mt_rand(9999999,99999999);
  return $output; 
  }
 
if ($data['settings']['user_pre']=='set_user_model_4sis') {
  $output['model'] = 'SIF-'.mt_rand(9999999,99999999);
  return $output; 
  }  

if ($data['settings']['user_pre']=='set_user_model_ROOMERS') {
  $output['model']        = 'ROM-'.mt_rand(9999999,99999999); 
  $output['description']  = isset($item->model)?(string)$item->model:$output['model'];
     
  return $output; 
  }    

return $output; 
}


public function ParseJS($url,$color) {  
  $url = html_entity_decode($url);
  $query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_colors_new WHERE name = '" . $this->db->escape($color) . "' AND supplier_id = '1'");
  if ($query1->row) return $query1->row['color']; 

//  $page = file_get_contents($url);
  
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  	$page = curl_exec($ch);

 	  $errNo = curl_errno($ch);
  	$err   = curl_error($ch);
  	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);  
  
$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = 'YMLe3cf75f47572d3a1a72f871a57bb0b35', type = 'log_page', data = '" . $this->db->escape(json_encode($url . " : " . $http_response_status)) . "', user = 'PARSER'");
  preg_match('~colors = (.*)ng-show~su', $page, $m);
  $a = explode('}],', $m[1]);


  foreach ($a as $k => $aa) {
      if ($k > 0) {
         $b = str_replace("'", "", $aa);
         $c = strpos($b, "},{");
         $d = substr($b, 0, $c);
         $e = explode(',', $d);
         $prev_res = array();
         if (count($e) > 1) {
             foreach ($e as $ee) {
                $i = strpos($ee, ':');
                $key = substr($ee, 0, $i);
                $value = substr($ee, $i + 1);
                $prev_res[$key] = $value;
                }
              }
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = 'YMLe3cf75f47572d3a1a72f871a57bb0b35', type = 'log_output', data = '" . $this->db->escape(json_encode($prev_res)) . "', user = 'PARSER'");
        if (isset($prev_res['name']) && isset($prev_res['bg'])) {
          $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_colors_new WHERE name = '" . $this->db->escape($prev_res['name']) . "' AND supplier_id = '1'");
          if (!$query2->row) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_colors_new SET name = '" . $this->db->escape($prev_res['name']) . "', color = '" . $this->db->escape($prev_res['bg']) . "', supplier_id = '1'");
            }
          }    
        }
    }

  $query3 = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_colors_new WHERE name = '" . $this->db->escape($color) . "' AND supplier_id = '1'");
  if ($query3->row) return $query3->row['color']; 
  return 'parse_error';
  }

}
?>