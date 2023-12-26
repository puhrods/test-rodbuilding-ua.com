<?php
class ModelZoXml2ZoXml2Filters extends Model {

public function doUserFilter($output,$data,$index,$item) {
  $indexes = explode (';',$index);
  foreach ($indexes as $next_index) {
    $parts = explode (':',$next_index);
    if (!isset($parts[1])) $parts[1] = '';
    $output = $this->oldUserFilter($output,$data,trim($parts[0]),$item,trim($parts[1]));
    if (!is_array($output)) return $output;
    }      
  return $output;
  }
public function oldUserFilter($output,$data,$index,$item,$param) {
$session_key      = $data['session_key'];
$user             = $data['user'];
                                      
if ($index=='nl2br') { 
  $output['description'] = preg_replace("/[\r\n]+/m", "<br>", $output['description']);
//  $output['description'] = nl2br ($output['description'],false); 
  return $output;
  }
if ($index=='relef_Минимальная') { 
  foreach ($item->pack_units->pack_unit as $value) {
    if ($value["ТипУпаковки"] == "Минимальная") {
      $output['length'] = trim($value->length);
      $output['width']  = trim($value->width);
      $output['height'] = trim($value->height);
      $output['weight'] = trim($value->weight); 
      $output['volume'] = trim($value->volume); 
      } 
    }
  return $output;
  }
if ($index=='netlab_attriutes') { 
  foreach ($item as $option_key => $value) {
    if (isset($data['netlab_properties'][trim($option_key)])) {
      if ($data['netlab_properties'][trim($option_key)]=="Описание") $output['description'] = trim($value);
      else $output['attr'][$data['zoAttributes']->getProductAtributeId($data['settings']['default_atribute_group_name'], $data['settings']['default_atribute_group'], trim($data['netlab_properties'][trim($option_key)]))] = trim($value);
      }
    }
  return $output;
  }
if ($index=='mctrade_quantity') { 
  switch ($output['quantity']) {
    case '+': {
      $output['quantity'] = 1;
      $output['stock_status_id'] = 9;
      break;
      }
    case 'Под заказ': {
      $output['quantity'] = 0;
      $output['stock_status_id'] = 11;
      break;
      }
    default: {
      $output['quantity'] = 0;
      $output['stock_status_id'] = 10;
      }
    }
  return $output;
  }
  
if ($index=='dominanta') { 
  $parts = explode ('/',$output['sku']);
  $output['ean'] = md5 ( $parts[0] . "::" . $session_key);
  return $output;
  }
if ($index=='verda-m_ru') { 
  if (count($output['image'])==0) {
    $item_product_id = trim($item->productId);
    $output['image'][] = $data['verda_data'][$item_product_id]['cover'];
    }
  $output['ean'] = md5 ( $output['ean'] . "::" . $session_key);
  if (isset($item->params->param)) {
    foreach ($item->params->param as $value) {
      if ($value['name']=='Элемент') {
        $option_key = 'params_Элемент';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => trim($value['title']),
            'required'      => 0,
            'price'         => trim($value['price']),
            'price_prefix'  => '=',
            'subtract'      => 0,
            'quantity'      => $value['available']=="true"?1:0,
            );
          }
        }
      }
    }
  return $output;
  }
if ($index=='cat_id2location') { 
  $output['location'] = trim($item->cat_id);
  return $output;
  }
if ($index=='BikiniTop_colors') { 
  if (empty($output['sku'])) return NULL;
  if (empty($item->Цвет__COLOR)) return NULL;
  $output['ean'] = md5 ( $output['sku'] . "::" . $session_key);
  $output['sku'] = $output['sku'] . "::" . trim($item->Цвет__COLOR);
  return $output;
  }
if ($index=='tngtoys_action') { 
  if (isset($item->action)) {
    if ($item->action==1) $output['price'] *= 1.3;
    else                  $output['price'] *= 1.1;
    }
  return $output;
  }
if ($index=='UZ_ARMS') { 
  $list_of_bad_sku = array ("VSA", "AR063.1х2200");
  if (in_array ($output['sku'],$list_of_bad_sku)) $output['sku'] .= "-" . $item['id'];
  return $output;
  }
if ($index=='MoscowPlusKupavna') { 
  $output['quantity'] = 0;
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Склад Москва')  $output['quantity'] += trim($value);
      if ($value['name']=='Склад Купавна') $output['quantity'] += trim($value);
      }
    }      
  return $output;
  }
if ($index=='flashki_optom_md5_ean') {
  $output['ean'] = '';
  if (isset($item->artnumber)) {
    $ean = trim ($item->artnumber);
     if ($ean) {
         $output['ean'] = md5($ean . "::" . $session_key);
     }
     /*
      if ($ean  && isset($item->body_color)) {
        $body_color = trim ($item->body_color);
        if ($body_color) $output['ean'] = md5 ( $ean . "::" . $body_color . "::" . $session_key);
        }
     */
    } 
  return $output;
  }
if ($index=='novatour_md5_ean_50') {
  $output['ean'] = $output['sku'];
  if (!empty($output['attr'][50])) $output['ean'] .= "::" . $output['attr'][50];
  $output['ean'] = md5 ($output['ean'] . "::" . $session_key);
  return $output;
  }
if ($index=='portobello_md5_ean') {
  $output['ean'] = '';
  // <articul>LXX65031-051</articul> 1-ю часть ариткула в EAN
  if (isset($item->articul)) {
    $ean = trim ($item->articul);
    if ($ean) {
      $parts = explode ('-', $ean);
      if (!empty($parts[0])) $output['ean'] = md5 ($parts[0] . "::" . $session_key);
      }
    } 
  return $output;
  }
if ($index=='md5_ean') { 
  if (!empty($output['ean'])) $output['ean'] = md5 ($output['ean'] . "::" . $session_key);
  return $output;
  }
if ($index=='md5name2ean') { 
  $output['ean'] = md5 (trim($item->name) . "::" . $session_key);
  return $output;
  }
if ($index=='export_data_eburg4io') { 
  $prices = array();
  $output['quantity'] = 0;
  // ОПЦИИ
  /*
  <prices>
    <p392782718>
      <price>1591</price>
      <count>9</count>
      <param_size>1249</param_size>
      <param>1.5 спальный</param>
      <store>Склад Екатеринбург</store>
    </p392782718>
  */
  if (isset($item->prices) && !empty($data['options']['prices']['dest_id'])) {
    foreach ($item->prices as $option1) foreach ($option1 as $sku => $option){
      $description = '';
      if (isset($option->pillowcase)) {
        $val = trim($option->pillowcase);
        if ($val) $description  .= '<p><b>Наволочка: </b> ' . $val . '</p>';
        }  
      if (isset($option->sheet)) {
        $val = trim($option->sheet);
        if ($val) $description  .= '<p><b>Простыня: </b> ' . $val . '</p>';
        }  
      if (isset($option->duvet)) {
        $val = trim($option->duvet);
        if ($val) $description  .= '<p><b>Пододеяльник: </b> ' . $val . '</p>';
        }  
      $weight = 0;
      if (isset($option->weight)) {
        $weight = trim($option->weight);
        $parts = preg_split ("/[^\d.]+/",$weight);
        $weight = $parts[0];
        if (isset($parts[1])) $weight .= "." . $parts[1];  
        } 
      if ($weight) {
        $description  .= '<p><b>Вес: </b> ' . $weight . ' кг</p>';
        }
      if (isset($option->store)) {
        $val = trim($option->store);
        if ($val) $description  .= '<p><b>'. $val . ': </b> ' . trim($option->count) . ' шт</p>';
        }  
      if (defined('export_data_eburg_price')&&export_data_eburg_price=='opt') $plus = $this->model_zoxml2_zoxml2->doPrice  ($data,trim($option->price));
      else  $plus = $this->model_zoxml2_zoxml2->doPrice  ($data,trim($option->rprice));
      if (is_nan($plus)) continue;
      $quantity = trim($option->count);
      if ($quantity==0 && isset($data['settings']['not_empty_only'])) continue;
      $output['quantity'] += $quantity;
      $prices[trim($sku)] = $plus;
      $output['option'][trim($sku)] = array(
          'option_id'     => $data['options']['prices']['dest_id'],
          'value'         => trim($option->param),
          'price_prefix'  => $data['settings']['dov_price_prefix'],
          'required'      => $data['settings']['dov_required'],
          'subtract'      => $data['settings']['dov_subtract'],
          'quantity'      => $quantity,
          'price'         => $plus,
          'sku'           => trim($sku),
          'description'   => $description,
          'weight_prefix' => "+",
          'weight'        => $weight,
          );
      }  
    }
  // БЛОКИРОВКА ПО ОСТАТКУ
  if ($output['quantity']==0 && isset($data['settings']['not_empty_only'])) return NULL;
  // ТЕПЕРЬ НАЙТИ В $prices минимальную цену
  if (count($prices)) {
    $output['price'] = min ($prices);
    if ($data['settings']['dov_price_prefix']=="+") {
      foreach ($prices as $sku => $price) {
        $output['option'][$sku]['price'] -= $output['price'];
        }
      }
    return $output;
    }
  $output['price'] = 999999;
  return NULL; // ТОВАР ИМЕЕТ ПУСТОЙ БЛОК <prices/>
  }
if ($index=='Запрет_скидки') { 
  if (isset($item->Запрет_скидки)) {
    if (trim($item->Запрет_скидки)=="Да") $output['price'] *= 1.35; 
    else                                  $output['price'] *= 1.15; 
    }
  return $output;
  }
// ------ masterangel ----------
if ($index=='masterangel') { 
  if (isset($item->OPTIONS)) {
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'masterangel', data = '" . $this->db->escape("Старт: " . json_encode($output)) . "', user = '" . $this->db->escape($data['user']) . "'");
    $values = explode ('|',trim($item->OPTIONS));
    foreach ($values as $option_value) {
      $parts = explode ('(',trim(str_replace(')', ' ', $option_value)));
      $plus  = 0;
      if (isset($parts[1])) $plus =  trim($parts[1]);
      $plus = $this->model_zoxml2_zoxml2->doPrice  ($data,$plus);
      if (is_nan($plus)) continue;
      $output['option'][] = array(
      'option_id'     => $data['options']['OPTIONS']['dest_id'],
      'value'         => htmlspecialchars(trim($parts[0])),
      'price_prefix'  => '=',
      'required'      => $data['settings']['dov_required'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $output['quantity'],
      'price'         => $plus,
      );
      }
    }
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'masterangel', data = '" . $this->db->escape("Конец: " . json_encode($output)) . "', user = '" . $this->db->escape($data['user']) . "'");
  return $output;
  }  
if ($index=='use_RetailPrice_if_not_zero') {   
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='RetailPrice') {
        if (trim($value)>0) $output['price'] = trim($value);
        }
      }
    }      
  return  $output;
  }
if ($index=='extra_description_domovito') {  
  $output['description'] .= "<br />Купить " . $output['name'] . " в интернет магазине Домовито 24";
  }
if ($index=='sort_pictures') {  
  $output['image'] = array(); 
  if (isset($item->pictures->picture)) {
    foreach ($item->pictures->picture as $value) {
      if ($value['main']=='yes') {
        $output['image'][] = trim($value);
        }
      }
    foreach ($item->pictures->picture as $value) {
      if ($value['main']!='yes') {
        $output['image'][] = trim($value);
        }
      }
    }      
  return  $output;
  }
if ($index=='novatour_h1') {   
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='H1') {
        $output['name'] = trim($value);
        }
      }
    }      
  return  $output;
  }
if ($index=='price2special') {   
  if (!empty($item->price)) {  
    $output['special'] = trim($item->price);
    }      
  return  $output;
  }
if ($index=='ProteinPlus_no_ptions') {   
  if (empty($item['group_id'])) {  
    $values = explode ('-',trim($item->vendorCode));
    $output['model'] = $values[0];
    }      
  return  $output;
  }
if ($index=='sku_ltrim') {          
	$output['sku'] = ltrim ($output['sku'],"0");
  return  $output;
  }
if ($index=='Igor1987') {
	$output['meta_description'] = $output['description'];
  return  $output;
  }
if ($index=='video2description') {  
  if (isset($item->video)) {
    $output['description'] = $item->video . $output['description'];
    }  
  return  $output;
  }
if ($index=='velles_rrc') {  
  if (isset($item->price)) {
    foreach ($item->price as $price) {
      if ($price['name']=="РРЦ")      {
        $output['price']    = trim($price);
        unset ($output['mc_price']);
        }
      }
    }  
  return  $output;
  }
if ($index=='add_partner_id_to_images') {
  foreach ($output['image'] as $key => $value) {
    $output['image'][$key] = rtrim ($value, "0") . '100202146';
    } 
  return $output;
  }
if ($index=='Avigal') {
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Ткань') {
        $output['description']  .= '<br /><p><b>' . trim($value['name']) . ':</b> ' . trim($value) . '</p>';
        }
      if ($value['name']=='Состав') {
        $output['description']  .= '<br /><p><b>' . trim($value['name']) . ':</b> ' . trim($value) . '</p>';
        }
      if ($value['name']=='Цвет') {
        $output['description']  .= '<br /><p><b>' . trim($value['name']) . ':</b> ' . trim($value) . '</p>';
        }
      }
    }
  return $output;
  }
// ------ gifts.ru ----------
if ($index=='gifts.ru_color') {
  if (!isset($item->group)) $output['sku'] = trim($item->code);

  $item_product_id = trim($item->product_id);
  if (empty($data['gifts_ru_colors'][$item_product_id])) return NULL;
  $color =  $data['gifts_ru_colors'][$item_product_id];
  
  // ОПЦИЯ ЦВЕТ
  $variant_color = array ();
  // ФОРМИРУЕМ ОПИСАНИЕ ОПЦИИ
  $description = "";
  if (isset($item->matherial)) $description .= "<p><b>Материал: </b>" . trim($item->matherial) . "</p>";
  if (isset($item->pack)) {
    $description .= "<p><b>Транспортная упаковка: </b></p>";
    if (isset($item->pack->amount)) $description .= "<p>Кол-во в упаковке: " . trim($item->pack->amount) . "</p>";
    if (isset($item->pack->weight)) {
      $weight = (float)$item->pack->weight;
      $weight_unit = " гр";
      if (defined('GIFTS_RU_WEIGHT2KG')) {
        $weight /=1000;
        $weight_unit = " кг";
        }
      $description .= "<p>Вес одной упаковки: " . $weight . $weight_unit . "</p>";
      }
    if (isset($item->pack->volume)) {
      $volume = (float)$item->pack->volume;
      $volume_unit = " см. куб.";
      if (defined('GIFTS_RU_VOLUME2LITR')) {
        $volume /=1000;
        $volume_unit = " литров";
        }
      if (defined('GIFTS_RU_VOLUME2KUB'))  {
        $volume /=1000000;
        $volume_unit = " м. куб.";
        }
      $description .= "<p>Объем упаковки: " . $volume . $volume_unit . "</p>";
      }
    if (isset($item->pack->sizex))  $description .= "<p>Длина: " . trim($item->pack->sizex) . "</p>"; 
    if (isset($item->pack->sizey))  $description .= "<p>Ширина: " . trim($item->pack->sizey) . "</p>";
    if (isset($item->pack->sizez))  $description .= "<p>Высота: " . trim($item->pack->sizez) . "</p>";
    }  
  
  if (!empty($data['options']['color']['dest_id'])) {
    $variant_color = array (
      'option_id'     => $data['options']['color']['dest_id'], 
      'value'         => $color,
      'required'      => $data['settings']['dov_required'],
      'price'         => 0,
      'price_prefix'  => $data['settings']['dov_price_prefix'],
      'image'         => '',
        'poip'          => $output['image'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $data['settings']['dov_quantity'],
      'description'   => $description,
      );
    }
  
  
  
  $output['roption']   = array();
  if (isset($item->product)) {
    foreach ($item->product as $offer) {
      $offer_weight = (float)$offer->weight;
      if (defined('GIFTS_RU_WEIGHT2KG')) $offer_weight /=1000;
      $variant = array (
        'options'  => array(),
        'sku'      => isset($offer->code)?(string)$offer->code:'',
        'model'    => isset($offer->name)?(string)$offer->name:'',
        'weight'   => $offer_weight,
        'ean'      => '',
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'location' => '',
        'quantity' => $data['settings']['dov_quantity'],
        'price'    => isset($offer->price->price)?(float)$offer->price->price:0,
        'stock_status_id' => 5,
        );
      // ОПЦИЯ РАЗМЕР
      if (!empty($data['options']['product']['dest_id'])) {
        $variant['options'][] = array (
          'option_id'     => $data['options']['product']['dest_id'], 
          'value'         => trim($offer->size_code),
          'required'      => $data['settings']['dov_required'],
          'price'         => 0,
          'price_prefix'  => $data['settings']['dov_price_prefix'],
          'image'         => '',
          'subtract'      => $data['settings']['dov_subtract'],
          'quantity'      => $data['settings']['dov_quantity'],
          );
        }
      $variant['options'][] = $variant_color;
      $output['roption'][]  = $variant;
      }
    }
  else {  // ТОВАРЫ БЕЗ РАЗМЕРОВ
      $variant = array (
        'options'  => array(),
        'sku'      => isset($item->code)?(string)$item->code:'',
        'model'    => isset($item->name)?(string)$item->name:'',
        'weight'   => isset($item->weight)?(string)$item->weight:'',
        'ean'      => '',
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'location' => '',
        'quantity' => $data['settings']['dov_quantity'],
        'price'    => isset($item->price->price)?(float)$item->price->price:0,
        'stock_status_id' => 5,
        );
      $variant['options'][] = $variant_color;
      $output['roption'][]  = $variant;
      }
  if (isset($item->print) && !empty($data['options']['print']['dest_id'])) {
    if ($data['options']['print']['dest_type']=="attr") {
      $output['attr'][$data['options']['print']['dest_id']] = '';
       foreach ($item->print as $offer) {
          if (!empty($output['attr'][$data['options']['print']['dest_id']])) $output['attr'][$data['options']['print']['dest_id']] .= ", "; 
          $output['attr'][$data['options']['print']['dest_id']] .= trim($offer->name);
          if (defined('GIFTS_RU_EXTRA_DESCRIPTION')) {
            $output['attr'][$data['options']['print']['dest_id']] .= " (" . trim($offer->description) . ")";
            }

          }
     }
    if ($data['options']['print']['dest_type']=="option") {
      $output['option'][] = array (
        'option_id'     => $data['options']['print']['dest_id'], 
        'value'         => 'Без нанесения',
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'description'   => '',
//        'default_select' => 1,
        'sort_order'    => 1,
        );
      foreach ($item->print as $offer) {
        $output['option'][] = array (
          'option_id'     => $data['options']['print']['dest_id'], 
          'value'         => (string)$offer->name,
          'required'      => 0,
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => 0,
          'quantity'      => 0,
          'description'   => isset($offer->description)?(string)$offer->description:'',
          'sort_order'    => 10,
          );
        }
      }
    }
  if (!empty($output['memory']) && !empty($data['options']['memory']['dest_id'])) {
    $output['attr'][$data['options']['memory']['dest_id']] = $output['memory'];
    }
  return $output;
  }
if ($index=='options_with_price_prefix') { 
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
if ($index=='Denyelle') {
  $output['atribute_text'] = '';
  if (isset($item->characteristic_list))     {
     foreach ($item->characteristic_list as $characteristic_list) {
      $output['atribute_text'] .= trim($characteristic_list) . "|";
      }
    }
  $output['jan'] = '';
  $glubina = 0;
  foreach ($item->facet_list as $facet_list) {
//    if (trim($facet_list->name)=="Гарантия")  $output['jan']          = $facet_list->value; //array_shift(explode (' ',$facet_list->value)); 
    if (trim($facet_list->name)=="Длина")     $output['length']       = str_replace (",", ".", trim($facet_list->value)) / 10; 
    if (trim($facet_list->name)=="Ширина")    $output['width']        = str_replace (",", ".", trim($facet_list->value)) / 10; 
    if (trim($facet_list->name)=="Высота")    $output['height']       = str_replace (",", ".", trim($facet_list->value)) / 10; 
    if (trim($facet_list->name)=="Глубина")   $glubina                = str_replace (",", ".", trim($facet_list->value)) / 10; 
    }  
  if ($output['length']==0) $output['length'] = $glubina;

  $output['ean'] /= 1000000;    // ОБЪЕМ В КУБОМЕТРАХ
  $output['quantity_vputi'] = 0;
  $output['quantity_pod_zakaz'] = 0;
  if (isset($item->stock_list))     {
     foreach ($item->stock_list as $stock) {
      if ($stock->type=='transit')                $output['quantity_vputi'] = trim($stock->value);
      if ($stock->type=='distribution_warehouse') $output['quantity_pod_zakaz'] = trim($stock->value);
      }
    }
  $output['description_full'] = '';
  if (isset($item->description_ext)) $output['description_full'] = $item->description_ext;
  $output['barcode'] = '';
  if (isset($item->barcode)) $output['barcode'] = $item->barcode;
  $output['minimum_upakovka'] = 0;
  $output['minimum_promo'] = 0;
  if (isset($item->package_list))     {
     foreach ($item->package_list as $stock) {
      if ($stock->type=='transport')              $output['minimum_upakovka'] = trim($stock->value);
      if ($stock->type=='intermediate')           $output['minimum_promo'] = trim($stock->value);
      }
    }
  return $output;
  }

if ($index=='item2log') {
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_output', data = '" . $this->db->escape(json_encode($item)) . "', user = '" . $this->db->escape($data['user']) . "'");
  return $output;
  }

if ($index=='weekend-billiard-brutto') {
  foreach ($item->param as $value) {
    if ($value['name']=='Размер упаковки') {
      // Габаритные размеры и вес, перечисленные через любой разделитель  <param name="Размер упаковки">126 х 64 х 13 см, 24 кг</param>
      $value = str_replace ("см,", "см", trim($value));
      $value = str_replace (",", ".", trim($value));
      if (empty($value)) return $output;
      $parts = preg_split ("/[^\d.]+/",$value); 
      if (count($parts)<4) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'Warning', data = '" . $this->db->escape("SKU: " . $output['sku'] . " вес брутто отсутствует: " . $value . " Technical information: " . json_encode($parts) ) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
      else $output['weight'] = trim($parts[3]);
      }
    }
  return $output;
  }
if ($index=='mb-catalog.ru') {
  if (!empty($item->param)) {
    $output['description']  .= '<br /><p><b>Характеристики:</b><br />';
    $output['description']  .= trim($item->param);
    $output['description']  .= '</p>';
    }
  if (count($output['image'])>1) array_shift ($output['image']);
  return $output;
  }
if ($index=='mb-catalog-images') {
  if (count($output['image'])>1) array_shift ($output['image']);
  return $output;
  }
if ($index=='ursus_quantity_control') {
  if ($output['quantity']<2) $output['quantity']=0;
  return $output;
  }

if ($index=='minerva-sewing.ru') {
  foreach ($output['image'] as $key => $value) {
    $value = str_replace (" ", "%20", $value);
    $output['image'][$key] = $value;
    } 
  return $output;
  }
if ($index=='keep_original_url') {
  if (!empty($item->url)) {
    $output['url_tpl'] = array_shift(explode('.',array_pop(explode ('/',$item->url))));
    }
  return $output;
  }
if ($index=='brightawards_image_big') {
  $output['image'] = array();
  if (isset($item->files->image_big)) $output['image'][] = trim($item->files->image_big);
  return $output;
  }
if ($index=='shop.tetis.ru_options') {
  if (isset($item->articules) && !empty($data['options']['articules']['dest_id'])) {
    foreach ($item->articules->articul as $option) {
      $output['option'][] = array(
      'option_id'     => $data['options']['articules']['dest_id'],
      'value'         => htmlspecialchars(trim($option->artname)),
      'price_prefix'  => '+',
      'required'      => $data['settings']['dov_required'],
      'subtract'      => $data['settings']['dov_subtract'],
      'quantity'      => $data['settings']['dov_quantity'],
      'price'         => (float)trim($option->artprice) - (float)$output['price'],
      'sku'           => trim($option->art1c)
      );
      }
    }
  return $output;
  }
if ($index=='barcelonadesign2artnode') {
  foreach ($output['image'] as $key => $value) {
    $value = str_replace ("http://img.barcelonadesign.ru/", DIR_IMAGE . "catalog/barcelona/", $value);
    $output['image'][$key] = $value;
    } 
  return $output;
  }
if ($index=='size_from_description') {
  if (!empty($item->description)) {
    $parts = explode ('.', trim($item->description));
    foreach ($parts as $part) {
      // Размеры - S, M, L.    - игнрируеи
      // Размеры: S-M, M-L.
      $value = explode (':', trim($part));
      if (trim($value[0])=="Размеры" && isset($value[1])) {
        $values = explode (',',trim($value[1]));
          foreach ($values as $option_value) {
            $output['option'][] = array(
            'option_id'     => 13,
            'value'         => htmlspecialchars(trim($option_value)),
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'required'      => $data['settings']['dov_required'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $output['quantity'],
            'price'         => 0,
            );
            }
        }
      }
    }
  return $output;
  }
if ($index=='js-company_description') {
  foreach ($item->param as $value) {
    if ($value['name']=='Состав') {
      $output['description']  .= '<br /><p><b>Состав:</b><br />';
      $output['description']  .= trim($value);
      $output['description']  .= '</p>';
      }
    }
  foreach ($output['attr'] as $attribute_id => $text) {
    $parts = explode (',', trim($text) );
    $output['attr'][$attribute_id] = '';
    foreach ($parts as $part) {
      $sostav = explode (' ', trim($part) );
      if ($output['attr'][$attribute_id]) $output['attr'][$attribute_id] .=  ';'; 
      $output['attr'][$attribute_id] .=  trim($sostav[0]); 
      }
    }
  $output['weight'] = 100; 
  $output['price']  = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->wholesalePrice);
  if (is_nan($output['price'])) return 0;
  if (isset($item['available']) && $item['available']=='false') $output['quantity'] = 0; 
  if ($output['quantity'] == 0) return 0;
  return $output;
  }
if ($index=='delmare2irmol') {
  foreach ($output['image'] as $key => $value) {
    $value = str_replace ("http://delmare-opt.ru/image/cache", "https://irmol.ru", $value);
    $output['image'][$key] = $value;
    } 
  return $output;
  }
if ($index=='p5s4ImprovedOptions_UpdateOptions') {
  $query = $this->db->query ("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
  if (!$query->row) return 0;
  $product_id = $query->row['product_id'];
  if (isset($data['settings']['update_quantity'])) {
    foreach ($item->assortiment->assort as $option) {
      $ShippingDate = '';
      if (!empty($option['ShippingDate'])) {
        $parts = explode (' ', $option['ShippingDate']);
        if ($parts[0]) $ShippingDate = 'Товар может быть отправлен: ' . $parts[0];
        }
      $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET description = '" . $this->db->escape($ShippingDate) . "', quantity = '" . (int)$option['sklad'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($option['aID']) . "'");
      }
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    }
  if (isset($data['settings']['update_price'])) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->price['BaseRetailPrice']);
    if (is_nan($output['price'])) return 0;
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "' WHERE product_id = '" . (int)$product_id . "'");
    }
  return 1;
  }
if ($index=='attr2description') {
  $output['description']  = '';
  foreach ($output['attr'] as $attribute_id => $text) {
    $text = trim($text); 
    if (!$text) continue;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_id = '" . (int)$attribute_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
    if ($query->row) {
      $output['description']  .= '<p>';
      $output['description']  .= '<b>';
      $output['description']  .= $query->row['name'];
      $output['description']  .= ':</b> ';
      $output['description']  .= $text;
      $output['description']  .= '</p>';
      }
    }
  return $output;
  }
if ($index=='sunstones') {
  // <r1>16=2;17,5=2;17=1;16,5=1</r1>
  if (isset($item->r1) && !empty($data['options']['r1']['dest_id'])) {
    $variants = explode (';', trim($item->r1));
    foreach ($variants as $variant_data) {
      $option = explode ('=', trim($variant_data));
      if (!empty($option[1])) {
        $output['option'][] = array (
          'option_id'     => $data['options']['r1']['dest_id'], 
          'value'         => trim($option[0]),
          'required'      => $data['settings']['dov_required'],
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => $data['settings']['dov_subtract'],
          'quantity'      => (int)$option[1],
          );
        }
      }
    }
  // <picture>https://sunstones.ua/photo/big/016374.jpg,https://sunstones.ua/photo/big/016374_2.jpg,https://sunstones.ua/photo/big/016374_3.jpg</picture>
  $output['image'] = array();
  if (isset($item->picture)) {
    $urls = explode (',', (string)$item->picture);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = $value;
    }
  return $output;
  }

if ($index=='outmaxshop_options') {
  if (isset($item->options) && !empty($data['options']['options']['dest_id'])) {
    $variants = explode (';', trim($item->options));
    foreach ($variants as $variant_data) {
      $option = explode ('-', trim($variant_data));
      if (isset($option[1])) {
        $output['option'][] = array (
          'option_id'     => $data['options']['options']['dest_id'], 
          'value'         => $option[0],
          'required'      => $data['settings']['dov_required'],
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => $data['settings']['dov_subtract'],
          'quantity'      => $option[1],
          );
        }
      }
    }
  return $output;
  }
if ($index=='ebazaar_giftskrd') {
  if (!empty($item->color)) {
    $output['attr'][64] = trim($item->color);
    }
  return $output;
  }
  if ($index=='maomag') {
    if (!empty($output['ean'])) {
      $output['sku'] = $output['ean'];
      }
    return $output;
    }
  if ($index=='sbx_updateOptions') {
    if (empty($output['upc'])) return NULL;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE location ='YMLd07e943edbaa1d04674bd2bb56e34477' AND upc = '" . $this->db->escape($output['upc']) . "'");        
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $price = $output['price'];
      if (!empty($output['special'])) $price = $output['special'];
      $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET jan='sbx', price = '" . $this->db->escape((float)$price) . "', quantity = '" . (int)$output['quantity'] . "' WHERE location ='YMLd07e943edbaa1d04674bd2bb56e34477' AND upc = '" . $this->db->escape($output['upc']) . "'");         
      $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "product_option_value WHERE quantity != '0' AND product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "', price = '" . $this->db->escape((float)$query->row['price']) . "' WHERE product_id = '" . (int)$product_id . "'");
     return 1;
      }
    return $output;
    }
if ($index=='model2mpn') {
  $output['mpn'] = $output['model'];
  return $output;
  }
if ($index=='torgovaya-tochka') {
  if (!empty($output['length']) && !empty($output['width']) && !empty($output['height'])) {
    $output['attr'][54] = (string)$output['length'] . 'x' . (string)$output['width'] . 'x' . (string)$output['height']; 
    }
  return $output;
  }

if ($index=='gifts.ru4ImprovedOptions_UpdateOptions') {
  if (isset($data['module']['io2']) && isset($item->code) && isset($item->amount)) {
    $price = $output['price'];
    if ($data['settings']['dov_price_prefix']=='+') {
      $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_option_value WHERE model = '" . $this->db->escape(trim($item->code)) . "'");        
      if (!empty($query->row['product_id'])) {
        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$query->row['product_id'] . "'");        
        $price = $output['price'] - $query->row['price'];
        }
      }
    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET price = '" . $this->db->escape((float)$price) . "', quantity = '" . (int)$item->amount . "' WHERE model = '" . $this->db->escape($item->code) . "'");        
    }
  return $output;
  }
if ($index=='gifts.ru4ImprovedOptions_UpdateOptions_v2') {
  if (isset($data['module']['io2']) && isset($item->code)) {
    $price = $output['price'];
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE sku = '" . $this->db->escape(trim($item->code)) . "'");        
    if (empty($query->row['product_id'])) return $output;
    $option_id = $query->row['option_id'];
    $product_id = $query->row['product_id'];
    if ($data['settings']['dov_price_prefix']=='+') {
      $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");        
      $price = $output['price'] - $query->row['price'];
      }
    $description = '';
    $description .= 'Свободный остаток: ' . trim($item->free) .  ', ';
    $description .= 'Общий остаток: ' . trim($item->amount) .  ', ';
    $description .= 'В пути: ' . trim($item->inwayamount);
    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET description = '" . $this->db->escape($description) . "', price = '" . $this->db->escape((float)$price) . "', quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape(trim($item->code)) . "'");        

    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$option_id . "'");        
    $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");        
    return 1;
    }
  return $output;
  }

if ($index=='happygifts.ru4ImprovedOptions_UpdateOptions_v2') {
  if (isset($data['module']['io2'])) {
    $description = '';
    $description .= 'Свободный остаток: ' . $output['quantity'] .  ', ';
    $description .= 'Общий остаток: ' . ($output['quantity']+trim($item->Занятый)) .  ', ';
    $description .= 'В пути: ' . (trim($item->СвободныйВПути)+trim($item->ЗанятыйВПути));
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE upc = '" . $this->db->escape($output['upc']) . "'"); 
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET description = '" . $this->db->escape($description) . "', quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND upc = '" . $this->db->escape($output['upc']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_output', data = '" . $this->db->escape($product_id . ": " . json_encode($output)) . "', user = '" . $this->db->escape($data['user']) . "'");
      return 1;
      }
    }
  return $output;
  }
if ($index=='gifts.ru4ImprovedOptions') {
  if (isset($item->product) && !empty($data['options']['product']['dest_id'])) {
    foreach ($item->product as $offer) {
      $price = 0;
      if (isset($offer->price->price)) {
        $price = $this->model_zoxml2_zoxml2->doPrice  ($data,(string)$offer->price->price);
        if (is_nan($price)) continue;
        }
      if ($data['settings']['dov_price_prefix']=='+') $price -= $output['price'];
      $output['option'][] = array (
        'option_id'     => $data['options']['product']['dest_id'], 
        'value'         => (string)$offer->size_code,
        'required'      => $data['settings']['dov_required'],
        'price'         => $price,
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $data['settings']['dov_quantity']=='{quantity}'?$output['quantity']:$data['settings']['dov_quantity'],
        'description'   => isset($offer->name)?(string)$offer->name:'',
        'sku'           => isset($offer->product_id)?(string)$offer->product_id:'',
        'weight'        => isset($offer->weight)?(float)$offer->weight-$output['weight']:'',
        'model'         => isset($offer->code)?(string)$offer->code:'',
        );
      }
    }
  if (isset($item->print) && !empty($data['options']['print']['dest_id'])) {
    $output['option'][] = array (
      'option_id'     => $data['options']['print']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'description'   => '',
//      'default_select' => 1,
      'sort_order'    => 1,
      );
    foreach ($item->print as $offer) {
      $output['option'][] = array (
        'option_id'     => $data['options']['print']['dest_id'], 
        'value'         => (string)$offer->name,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'description'   => isset($offer->description)?(string)$offer->description:'',
        'sort_order'    => 10,
        );
      }
    }
  return $output;
  }
if ($index=='gifts.ru4ImprovedOptions_v2') {
  $ean = '';
  if (isset($item->group)) $ean = trim($item->group);
  if ($ean) $output['ean'] = $ean . "::" . md5($ean . "::" . $data['session_key']);
  if (isset($item->product_size)) {
      $value = str_replace (",", ".", trim($item->product_size));
      if (!empty($value)) {
        $parts = preg_split ("/[^\d.]+/",$value); 
        if (count($parts)==3) {
          $output['length']       = trim($parts[0]);
          $output['width']        = trim($parts[1]);
          $output['height']       = trim($parts[2]);
          }
        }
      }
  if (isset($item->product) && !empty($data['options']['product']['dest_id'])) {
    foreach ($item->product as $offer) {
      $price = 0;
      if (isset($offer->price->price)) {
        $price = $this->model_zoxml2_zoxml2->doPrice  ($data,(string)$offer->price->price);
        if (is_nan($price)) continue;
        }
      if ($data['settings']['dov_price_prefix']=='+') $price -= $output['price'];
      $offer_weight = (float)$offer->weight;
      if (defined('GIFTS_RU_WEIGHT2KG')) $offer_weight /=1000;
      $output['option'][] = array (
        'option_id'     => $data['options']['product']['dest_id'], 
        'value'         => (string)$offer->size_code,
        'required'      => $data['settings']['dov_required'],
        'price'         => $price,
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $data['settings']['dov_quantity']=='{quantity}'?$output['quantity']:$data['settings']['dov_quantity'],
        'model'         => isset($offer->name)?(string)$offer->name:'',
        'sku'           => isset($offer->code)?(string)$offer->code:'',
        'weight'        => $offer_weight-$output['weight'],
        );
      }
    }
// GIFTS_RU_ADDLOGOOPTION
    if (defined('GIFTS_RU_ADDLOGOOPTION')&& !empty($data['options']['ADDLOGOOPTION']['dest_id'])) { 
    $output['option'][] = array (
      'option_id'     => $data['options']['ADDLOGOOPTION']['dest_id'], 
      'value'         => 'Нет',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
//      'default_select' => 1,
      'sort_order'    => 1,
      );
    $output['option'][] = array (
      'option_id'     => $data['options']['ADDLOGOOPTION']['dest_id'], 
      'value'         => 'Да',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'description'   => 'Отпраьте логотип на почту: ____',
      'sort_order'    => 10,
      );
      }

// ВИДЫ НАНЕСЕНИЯ    
  if (isset($item->print) && !empty($data['options']['print']['dest_id'])) {
    $output['option'][] = array (
      'option_id'     => $data['options']['print']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'description'   => '',
//      'default_select' => 1,
      'sort_order'    => 1,
      );
    foreach ($item->print as $offer) {
      $offer_name = trim($offer->name);
      if (isset($offer->description)) {
        $offer_description = trim($offer->description);
//        if ($offer_description) $offer_name .= " (" . $offer_description . ")";
        }
      $output['option'][] = array (
        'option_id'     => $data['options']['print']['dest_id'], 
        'value'         => $offer_name,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'description'   => isset($offer->description)?" - " . trim($offer->description):'',
//        'description'   => '',
        'sort_order'    => 10,
        );
      }
    }
  // АТРИБУТЫ
  if (!empty($output['color']) && !empty($data['options']['color']['dest_id'])) {
    $output['attr'][$data['options']['color']['dest_id']] = $output['color'];
    }
  if (!empty($output['memory']) && !empty($data['options']['memory']['dest_id'])) {
    $output['attr'][$data['options']['memory']['dest_id']] = $output['memory'];
    }
  return $output;
  }
if ($index=='gifts.ru_print') {
  if (isset($item->print) && !empty($data['options']['print']['dest_id'])) {
    if ($data['options']['print']['dest_type']=="attr") {
      $output['attr'][$data['options']['print']['dest_id']] = '';
       foreach ($item->print as $offer) {
          if (empty($output['attr'][$data['options']['print']['dest_id']])) $output['attr'][$data['options']['print']['dest_id']] = trim($offer->name);
          else $output['attr'][$data['options']['print']['dest_id']] .= ", " . trim($offer->name);
          }
     }
    if ($data['options']['print']['dest_type']=="option") {
      $output['option'][] = array (
        'option_id'     => $data['options']['print']['dest_id'], 
        'value'         => 'Без нанесения',
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'description'   => '',
//        'default_select' => 1,
        'sort_order'    => 1,
        );
      foreach ($item->print as $offer) {
        $output['option'][] = array (
          'option_id'     => $data['options']['print']['dest_id'], 
          'value'         => (string)$offer->name,
          'required'      => 0,
          'price'         => 0,
          'price_prefix'  => '+',
          'subtract'      => 0,
          'quantity'      => 0,
          'description'   => isset($offer->description)?(string)$offer->description:'',
          'sort_order'    => 10,
          );
        }
      }
    }
  return $output;
  }
if ($index=='gifts.ru_SimpleOptionsWithSKU') {
  if (isset($item->product) && !empty($data['options']['product']['dest_id'])) {
    foreach ($item->product as $offer) {
      $price = 0;
      if (isset($offer->price->price)) {
        $price = $this->model_zoxml2_zoxml2->doPrice  ($data,(string)$offer->price->price);
        if (is_nan($price)) continue;
        }
      if ($data['settings']['dov_price_prefix']=='+') $price -= $output['price'];
      $output['option'][] = array (
        'option_id'     => $data['options']['product']['dest_id'], 
        'value'         => (string)$offer->size_code,
        'required'      => $data['settings']['dov_required'],
        'price'         => $price,
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $data['settings']['dov_quantity']=='{quantity}'?$output['quantity']:$data['settings']['dov_quantity'],
        'weight'        => isset($offer->weight)?(float)$offer->weight-$output['weight']:'',
        'sku'           => isset($offer->product_id)?(string)$offer->product_id:'',
        );
      }
    }
  if (isset($item->print) && !empty($data['options']['print']['dest_id'])) {
    $output['option'][] = array (
      'option_id'     => $data['options']['print']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'sort_order'    => 1,
      );
    foreach ($item->print as $offer) {
      $output['option'][] = array (
        'option_id'     => $data['options']['print']['dest_id'], 
        'value'         => (string)$offer->name,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'sort_order'    => 10,
        );
      }
    }
  return $output;
  }
if ($index=='UpdateQuantity4ROptionsByMODEL') {
  if (!empty($output['ro_model'])) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE model = '" . $this->db->escape($output['ro_model']) . "'"); 
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($output['ro_model']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return 1;
      }
    }
  return 0;
  }
if ($index=='UpdateQuantity4ROptionsBySKU') {
  if (!empty($output['ro_sku'])) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($output['ro_sku']) . "'"); 
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($output['ro_sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return 1;
      }
    }
  return 0;
  }
if ($index=='HappyGiftsUpdateQuantity4ROptions') {
  if (!empty($output['sku'])) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($output['sku']) . "'"); 
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($output['sku']) . "'");
      $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return 1;
      }
    }
  return 0;
  }
if ($index=='modellos') {
  foreach ($output['attr'] as $attribute_id => $text) {
    if ($attribute_id ==327) {
      $output['attr'][343] = $text;
      $parts = explode ('+', (string)$text);
      $output['attr'][327] = trim($parts[0]);
      }
    }
  return $output;
  }
if ($index=='wisell') {
  foreach ($output['attr'] as $attribute_id => $text) {
    if ($attribute_id ==327) {
      $parts = explode (',', (string)$text);
      $output['attr'][327] = trim($parts[0]);
      if (isset($parts[1])) $output['attr'][343] = $text;
      }
    }
  return $output;
  }
if ($index=='noindex4description') {
  $output['description'] = "<!--noindex-->" . $output['description'] . "<!--/noindex-->"; 
  return $output;
  }
if ($index=='sizetable2description') {
  if (!empty($item->sizetable)) $output['description'] .= (string)$item->sizetable; 
  if (!empty($item->description_short)) $output['description'] .= (string)$item->description_short; 
  return $output;
  }
if ($index=='oasiscatalog_ro_update_use_msk_stock') {
  if (trim($item->warehouse_id) != "000000029") return 0;  // ЗАГРУДАЕМ ТОЛЬКО МОСКОВСКИЙ СКЛАД
  $query = $this->db->query ("SELECT *  FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($output['sku']) . "'");
  if ($query->row) {
    $product_id = $query->row['product_id'];
    $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($output['sku']) . "'");
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return 1;
    }
  else {
    $query = $this->db->query ("SELECT *  FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return 1;
      }
    }
  return 0;
  }
if ($index=='gifts.ru_ro_update') {
  $query = $this->db->query ("SELECT *  FROM " . DB_PREFIX . "relatedoptions WHERE sku = '" . $this->db->escape($output['sku']) . "'");
  if ($query->row) {
    $product_id = $query->row['product_id'];
    $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND sku = '" . $this->db->escape($output['sku']) . "'");
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return 1;
    }
  else {
    $query = $this->db->query ("SELECT *  FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
    if ($query->row) {
      $product_id = $query->row['product_id'];
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "'");
      $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "'");
      return 1;
      }
    }
  return 0;
  }
if ($index=='HappyGiftsPrint') {
  if (isset($item->ТипыНанесения) && !empty($data['options']['ТипыНанесения']['dest_id'])) {
    $output['option'][] = array (
      'option_id'     => $data['options']['ТипыНанесения']['dest_id'], 
      'value'         => 'Без нанесения',
      'required'      => 0,
      'price'         => 0,
      'price_prefix'  => '+',
      'subtract'      => 0,
      'quantity'      => 0,
      'description'   => '',
 //     'default_select' => 1,
      'sort_order'    => 1,
      );
    foreach ($item->ТипыНанесения->ТипНанесения as $offer) {
      $output['option'][] = array (
        'option_id'     => $data['options']['ТипыНанесения']['dest_id'], 
        'value'         => trim($offer),
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 0,
        'description'   => '',
        'sort_order'    => 10,
        );
      }
    }
  return $output; 
  }
if ($index=='HappyGiftsImages') {
  if (isset($item->ИД)) {
    $output['image'][] = $data['settings']['img_path'] . (string)$item->ИД . '.jpg';
    foreach ($output['option'] as $key => $option) {
      if ($option['value']=="-") unset($output['option'][$key]);
      }
    if (isset($data['module']['poip'])) {
      if (is_array($output['option'])) {
        foreach ($output['option'] as $key => $option) {
          if ($option['option_id']==$data['options']['Цвет']['dest_id']) {
            $output['option'][$key]['poip'] = $output['image'];
            }
          }
        }
      }
    }
  return $output; 
  }
if ($index=='sport-hunt') {
  if (isset($item->prices)) {
    foreach ($item->prices->price as $value) {
      if ($value->order==2) $output['price'] = (float)$value->value;
      }
    }
  return $output;
  }
if ($index=='model2subcategory') {
  if (!empty($item->model)) {
    $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_description WHERE name = '" . $this->db->escape(trim($item->model)) . "'");
    foreach ($query->rows as $row) {
      $query2 = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$row['category_id'] . "' AND parent_id = '" . (int)$output['category_id'] . "'");
      if ($query2->row) {
        $output['category_id'] = $row['category_id'];
        return $output;
        }
      }   
    }
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new category', data = '" . $this->db->escape('Необходимо создать подкатегорию  ' . (string)$item->model . ' для товара ' . $item->model . '(vendorCode:' . (string)$item->vendorCode . ')') ."'");
  return $output;
  }
if ($index=='dveri_com') {
  if (isset($item->color)) {
    $output['name'] .= ' ' . (string)$item->color;
    }
  if (isset($item->params->param)) {
    foreach ($item->params->param as $item_value)      {
      if (isset($item_value['name'])) {
        $option_key = 'param_' . $item_value['name'];
        $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,(string)$item_value);
        }
      }
    }
  if (isset($item->options->option)) {
    $option_key = 'options';
    foreach ($item->options->option as $value) {   
      $price    = $this->model_zoxml2_zoxml2->doPrice  ($data,$value['price-rrc']);
      if (is_nan($price)) continue;
      $output['option'][] = array(
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => htmlspecialchars($value),
        'price'         => $price,
        'price_prefix'  => '=',
//        'code'          => $value['code'],
        'required'      => 1,
        'subtract'      => 1,
        'quantity'      => 1,
        );
      }
    }
  if (isset($item->accessories->accessory)) {
    $option_key = 'accessories';
    foreach ($item->accessories->accessory as $value) {
      $price    = $this->model_zoxml2_zoxml2->doPrice  ($data,$value['price-rrc']);
      if (is_nan($price)) continue;
      $output['option'][] = array(
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => (string)$value,
        'price'         => $price,
        'price_prefix'  => '+',
//        'code'          => '',
        'required'      => 0,
        'subtract'      => 1,
        'quantity'      => 1,
        );
      }
    }
  $output['image'] = array();
  if (isset($item->pictures->picture)) {
    foreach ($item->pictures->picture as $value) {
      $output['image'][] = $this->model_zoxml2_zoxml2->myUrlEncode2($value);
      }
    }
  return $output;
  }
if ($index=='mebeloptom_colors') {
  $output['tag'] = $output['name'];
  $output['ean'] = md5($output['name'] . "::" . $data['session_key']);
  if (!empty($item->color_id)) {
    if (!empty($data['mebeloptom_colors_id_to_name'][(int)trim($item->color_id)])) {
      $output['name'] .= ", цвет: " . $data['mebeloptom_colors_id_to_name'][(int)trim($item->color_id)]; 
      if (isset($output['meta_h1']))    $output['meta_h1']    = $output['name'];
      if (isset($output['meta_title'])) $output['meta_title'] = $output['name'];
      if (!empty($data['options']['color_id']['dest_id'])) {
        if ($data['options']['color_id']['dest_type']=='option') {
          $output['option'][] = array(
            'option_id'     => $data['options']['color_id']['dest_id'],
            'value'         => $data['mebeloptom_colors_id_to_name'][(int)trim($item->color_id)],
            'price'         => 0,
            'price_prefix'  => '+',
            'required'      => 1,
            'subtract'      => 0,
            'quantity'      => 100,
            );
          }
        if ($data['options']['color_id']['dest_type']=='attr') {
          $output['attr'][$data['options']['color_id']['dest_id']] = $data['mebeloptom_colors_id_to_name'][(int)trim($item->color_id)];
          }
        }
      }
    }
  if (isset($item->options->option)) {
    $option_key = 'options';
    foreach ($item->options->option as $value) {   
      $option_price = (float)trim($value->price_rrc);
      $option_price = $this->model_zoxml2_zoxml2->doPrice  ($data,$option_price);
      if (is_nan($option_price)) continue;
      $output['option'][] = array(
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => trim($value->name),
        'price'         => $option_price - $output['price'],
        'price_prefix'  => '+',
//        'sku'           => trim($value->code),
        'required'      => 1,
        'subtract'      => 0,
        'quantity'      => 100,
        );
      }
    }
  return $output;
  }
if ($index=='dveri_com_msk') {
  $output['tag'] = $output['name'];
  $output['ean'] = $output['name'];
  if (!empty($item->accessories_group_id) && !empty($data['options']['accessories_group_id']['dest_id'])) {
    foreach ($data['dveri_com_accessories'] as $accessory) {
      if (trim($accessory['accessories_group'])!=trim($item->accessories_group_id)) continue;
      $output['option'][] = $accessory;
      }
    }
  if (!empty($item->color_id)) {
    // NEW! AVATARA !
    if (isset($data['dveri_com_colors_id_avatara'][(int)trim($item->color_id)])) $output['avatara'] = $data['dveri_com_colors_id_avatara'][(int)trim($item->color_id)];
    // END OF AVATARA
    if (!empty($data['dveri_com_colors_id_to_name'][(int)trim($item->color_id)])) {
      $output['name'] .= ", цвет: " . $data['dveri_com_colors_id_to_name'][(int)trim($item->color_id)]; 
      if (isset($output['meta_h1']))    $output['meta_h1']    = $output['name'];
      if (isset($output['meta_title'])) $output['meta_title'] = $output['name'];
      if (!empty($data['options']['color_id']['dest_id'])) {
        if ($data['options']['color_id']['dest_type']=='option') {
          $output['option'][] = array(
            'option_id'     => $data['options']['color_id']['dest_id'],
            'value'         => $data['dveri_com_colors_id_to_name'][(int)trim($item->color_id)],
            'price'         => 0,
            'price_prefix'  => '+',
            'required'      => 0,
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        if ($data['options']['color_id']['dest_type']=='attr') {
          $output['attr'][$data['options']['color_id']['dest_id']] = $data['dveri_com_colors_id_to_name'][(int)trim($item->color_id)];
          }
        }
      }
    }
  if (isset($item->components->component)) {
    foreach ($item->components->component as $item_value)      {
      if (isset($item_value->name)) {
        $option_key = 'component_' . $item_value->name;
        $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,(string)$item_value->value);
        }
      }
    }
  if (isset($item->params->param)) {
    foreach ($item->params->param as $item_value)      {
      if (isset($item_value->name)) {
        $option_key = 'param_' . $item_value->name;
        $output = $this->model_zoxml2_zoxml2->doParams($option_key,$output,$data,(string)$item_value->value);
        }
      }
    }
  $price   = (float)trim($item->price_rrc);
  $special = $price;
  if (trim($item->discount_rrc)>0)  $special *= (100 - (float)trim($item->discount_rrc)) / 100;
  
  $price = $this->model_zoxml2_zoxml2->doPrice  ($data,$price);
  if (is_nan($price)) return NULL;
  $special = $this->model_zoxml2_zoxml2->doPrice  ($data,$special);
  if (is_nan($special)) return NULL;

  if (isset($item->options->option)) {
    $option_key = 'options';
    foreach ($item->options->option as $value) {   

      $option_price = (float)trim($value->price_rrc);
      if (trim($value->discount_rrc)>0)  $option_price *= (100 - trim($value->discount_rrc)) / 100;
      $option_price = $this->model_zoxml2_zoxml2->doPrice  ($data,$option_price);
      if (is_nan($option_price)) continue;

      $output['option'][] = array(
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => htmlspecialchars($value->name),
        'price'         => $option_price - $special,
        'price_prefix'  => '+',
        'sku'           => trim($value->code),
        'required'      => 1,
        'subtract'      => 1,
        'quantity'      => 1,
        );
      }
    }
  $output['price'] = $price;
  if ($price>$special) {
    $output['special'] = $special;
    }
    
  $output['image'] = array();
  if (isset($item->pictures->picture)) {
    foreach ($item->pictures->picture as $value) {
      $output['image'][] = $this->model_zoxml2_zoxml2->myUrlEncode2($value);
      }
    }
  return $output;
  }
if ($index=='eroticfantasy_short') {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions WHERE model = '" . $this->db->escape($output['model']) . "'"); 
  if ($query->row) {
    $product_id = $query->row['product_id'];
    // Такой вариант есть - делаем обновление кол-ва, цены и уходим 
    // $item->Цена_по_акции
    $relatedoptions_id = $query->row['relatedoptions_id'];                                                                                                                               
    // АКЦИИ
    if (isset($item->Цена_по_акции)&&isset($data['settings']['update_special'])) {
      $special    = $this->model_zoxml2_zoxml2->doPrice  ($data,(float)$item->Цена_по_акции);
      if (!is_nan($special)) {
        foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_special SET   
          relatedoptions_id = '" . (int)$relatedoptions_id . "',      
          customer_group_id = '" . (int)$customer_group_id . "', 
          price           = '" . (float)$special . "'");  
        }
      }
      // Добавляем АКЦИЮ в стандартный контроллер
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
      $query = $this->db->query("SELECT MIN(price) as price FROM " . DB_PREFIX . "relatedoptions_special WHERE price!='0' AND relatedoptions_id = '" . (int)$relatedoptions_id . "'"); 
      foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$query->row['price'] . "'");
        }
      }
    // ОПЦИИ
//      $this->saveOptions ($variant['options'],$product_id, $data, $relatedoptions_id); 
    $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET price = '" . $this->db->escape((float)$output['price']) . "', quantity = '" . (int)$output['quantity'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($output['model']) . "'");
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price, MAX(price) as max_price FROM " . DB_PREFIX . "relatedoptions WHERE price!='0' AND product_id = '" . (int)$product_id . "'"); 
    $mpn = $query->row['max_price']<3000?'':'Доставка: 0 ₽';
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), mpn = '" . $this->db->escape($mpn) . "', price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    return 1;
    }
  return 0;
  }

if ($index=='eroticfantasy') {
  // $item->Артикул - артикул опции. Загоняем в модель
  // $item->Группа  - артикул (связующее). Если оно не задано - используем модель
  if ($output['sku']=='') $output['sku']=$output['model']; 
  $output['mpn'] = $output['price']<3000?'':'Доставка: 0 ₽';
  $output['name'] = htmlspecialchars($output['name'],ENT_QUOTES);
  return $output;
  }
if ($index=='kvimol') {
  foreach ($output['image'] as $key => $value) {
    $value = str_replace ("/cache/", "/", $value);
    $value = str_replace ("-100x100.jpg", ".jpg", $value);
    $output['image'][$key] = $value;
    } 
  return $output;
  }
if ($index=='TTango_габариты') {  
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Габариты упаковки') {
        $parts = explode ('x', (string)$value);
        if (isset($parts[0])) $output['length']       = trim((string)$parts[0]);
        if (isset($parts[1])) $output['width']        = trim((string)$parts[1]);
        if (isset($parts[2])) $output['height']       = trim((string)$parts[2]);
        }
      }
    }
  return $output;
  }
if ($index=='prime-sport.ru') {  
  if ($output['price']==0 && isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Цена Серебро') {
        $output['price'] = (float)$value * 1.33;
        }
      }
    }
  return $output;
  }
if ($index=='images_over_comma') {  
  $output['image'] = array();
  if (isset($item->images)) {
    $urls = explode (',', (string)$item->images);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars(trim($value),ENT_QUOTES);
    }
  return $output;
  }
if ($index=='pictures_over_comma') {  
  $output['image'] = array();
  if (isset($item->picture)) {
    $urls = explode (',', (string)$item->picture);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars(trim($value),ENT_QUOTES);
    }
  return $output;
  }
if ($index=='IK_TovarAll') {  
  $output['image'] = array();
  if (isset($item->images)) {
    $urls = explode (',', (string)$item->images);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars($value,ENT_QUOTES);
    }
  if (isset($output['iprice'])) {
    $output['iprice'] = str_replace(",",".",$output['iprice']);
    $output['price']  = $this->model_zoxml2_zoxml2->doPrice  ($data,(float)$output['iprice']);
    if (is_nan($output['price'])) return NULL;
    $output['price']  = round ( $output['price']+5, 1, PHP_ROUND_HALF_UP);
    }
  $output['name']     = trim(str_replace($output['model'],"",$output['name']));
  $output['name']     = htmlspecialchars($output['name'],ENT_QUOTES);
  return $output;
  }
if ($index=='runlab') {
  if (isset($item->stock)) {
    $output['quantity'] = 0;
    foreach ($item->stock as $value) {
      $output['quantity'] += (int)$value;
      }
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размеры') {
        $option_key = 'param_Размеры';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      if ($value['name']=='Вкус') {
        $option_key = 'param_Вкус';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
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
  return $output;
  }
if ($index=='htmlspecialchars_for_name') {
  $output['name'] = htmlspecialchars($output['name'],ENT_QUOTES);
  return $output;
  }
if ($index=='staix') {
  if (isset($item->picture)) {
    $urls = explode ('|', $item->picture);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars($value,ENT_QUOTES);
    }
  if (isset($item->meta_title)) {
    $output['meta_title'] = (string)$item->meta_title;
    }
  if (isset($item->meta_keyword)) {
    $output['meta_keyword'] = (string)$item->meta_keyword;
    }
  return $output;
  }
if ($index=='skip_no_name') {
  $output['name'] = str_replace("\n","",trim($output['name']));
  $output['name'] = str_replace("\r","",trim($output['name']));
  $output['name'] = htmlspecialchars($output['name'],ENT_QUOTES);
  return $output['name']==''?NULL:$output;
  }
if ($index=='rimari_дропшипинг') {
  if (isset($item['price'])) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item['price']);
    if (is_nan($output['price'])) return NULL;
    }
  if (isset($item->desc)) {
    $output['description'] = (string)$item->desc;
    }
  if (isset($item->picture)) {
    $str  = str_replace("\n","|",(string)$item->picture);
    $urls = explode ('|', $str);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars($value,ENT_QUOTES);
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 0,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      }
    }
  return $output;
  }
if ($index=='rimari') {
  if (isset($item['price'])) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item['price']);
    if (is_nan($output['price'])) return NULL;
   }
  if (isset($item->desc)) {
//    $output['description'] = (string)$item->desc;
    }
  if (isset($item->picture)) {
    $str  = str_replace("\n","|",(string)$item->picture);
    $urls = explode ('|', $str);
    foreach ($urls as $value) if(strlen($value)>5) $output['image'][] = htmlspecialchars($value,ENT_QUOTES);
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 0,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      }
    foreach ($item->param as $value) {
      if ($value['name']=='Цвет') {
        $output['model'] = $output['model'] . '/'. (string)$value;
        $output['name']  = $output['name']  . '. '. (string)$value . ' (' . $output['model'] . ')';
        }
      }
    }
  return $output;
  }
if ($index=='Дриада') {
  $output['model'] .= (string)'-9';
  return $output;
  }
if ($index=='garda') {
  if (isset($item->name) && isset($item->vendorCode)) {
    $output['name'] = trim(str_replace ( (string)$item->vendorCode, '', (string)$item->name ));                     
    } 
  return $output;
  }
if ($index=='WhiteBear_UpdateByOptsku') { 
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE optsku = '" . $this->db->escape($output['sku']) . "'");
  if ($query->row) {
    $product_id = $query->row['product_id'];
    $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '" . (int)$output['quantity'] . "' WHERE product_option_value_id = '" . (int)$query->row['product_option_value_id'] . "'");
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$query->row['total'] . "', supplier = 'YML2bfa7d222f68d4b0f6d171f0f28a2e93' WHERE product_id = '" . (int)$product_id . "'");
    return 1;  
    }
  return $output;
  }
if ($index=='Skip_if_SKU_exist') { 
  if (!$output['sku']) return NULL;
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
  if ($query->row) {
    }
  return $output;
  }
if ($index=='EmptyOnly') { 
  if ($output['quantity']) return NULL;
  return $output;
  }
if ($index=='Onmedia') { 
  $output['quantity'] = $item->outlets->outlet['instock'];
  return $output;
  }
if ($index=='neocube') { 
  foreach ($item->outlets->outlet as $value) {
    if (isset($value['id'])&&isset($value['instock'])) {
      if ($value['id']==1) $output['quantity'] = $value['instock'];
      }
    }
  return $output;
  }
if ($index=='neocube_merge_stocks') { 
  $q1 = 0;
  $q2 = 0;
  foreach ($item->outlets->outlet as $value) {
    // stock_status_id = '" . (int)$output['stock_status_id'] . "', 
    if (isset($value['id'])&&isset($value['instock'])) {
      if ($value['id']==1) $q1 = trim($value['instock']);
      if ($value['id']==2) $q2 = trim($value['instock']);
      }
    }
  $output['quantity'] = $q1;
  if ($q1<1&&$q2>0) $output['stock_status_id'] = 6; // Ожидание 2-3 дня
  return $output;
  }
if ($index=='neocube_all_outlets') { 
  $output['name'] = str_replace ('"', "'", $output['name']);
  $output['quantity'] = 0;
  foreach ($item->outlets->outlet as $value) {
    $output['quantity'] += (int)$value['instock'];
    }
  return $output;
  }
if ($index=='all_outlets') { 
  $output['quantity'] = 0;
  foreach ($item->outlets->outlet as $value) {
    $output['quantity'] += (int)$value['instock'];
    }
  return $output;
  }
if ($index=='karapuzik_megarion') { 
  $price = 0;
  $opt   = 0;
  foreach ($item->param as $value) {
    if ($value['name']=='МРЦ')      $price = (float)$value;
    if ($value['name']=='Цена опт') $opt = (float)$value;
    }
  if ($price>0) { $output['price'] = $price; return $output; }
  if ($opt>0)   {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$opt);
    if (is_nan($output['price'])) return NULL;
    return $output;
    }
  $output['price']    = 0;
  $output['quantity'] = 0;
  return $output;
  }
if ($index=='karapuzik_vsekroham') { 
  $price = $item->minimal_price;
  $opt   = $item->price;
  if ($price>0) { $output['price'] = $price; return $output; }
  if ($opt>0)   {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$opt);
    if (is_nan($output['price'])) return NULL;
    return $output;
    }
  $output['price']    = 0;
  $output['quantity'] = 0;
  return $output;
  }
if ($index=='DG_only') { 
  if (strncmp($output['sku'],'DG',2)) return NULL; // Пропускаем товар если артикул не начинается с префикса DG 
  }
if ($index=='instructionUrl') {  // Пример пользовательского фильтра - обработка тега instructionUrl
  if (isset($item->instructionUrl)) $output['description'] .= '<br><a href="' . (string)$item->instructionUrl . '" target="_blank" rel="nofollow">Скачать инструкцию</a>';  
  }
if ($index=='ДопДанные') {  // Пример пользовательского фильтра - обработка тега ДопДанные из Номенклатуры 1С 
  if (isset($item->ДопДанные->Цены->РЦ)) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,(string)$item->ДопДанные->Цены->РЦ);
    if (is_nan($output['price'])) return NULL;
    }
  }
if ($index=='Элемент') {  // Пример пользовательского фильтра - обработка тега Элемент из Номенклатуры 1С  
  }
if ($index=='mobilux.lv') {  
  if ((float)$output['price']<90) return NULL;
  if (trim((string)$item->quantity)=='>3') $output['quantity'] = 5;
  }
if ($index=='evelatusplus_ean') {
  if ((float)$output['price']<90) return NULL;
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($output['sku']) . "'");
  if (!empty($query->row['ean'])) {
    $output['sku'] = $query->row['ean'];
    if (((int)$output['quantity'])>0 && ((float)$query->row['price']>(float)$output['price'])) {
      $sql   = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
      $sql .= ", price = '" . (float)$output['price'] . "'";
      $sql .= ", quantity = '" . (int)$output['quantity'] . "'";
      $sql .= ", status = '1'";
      $sql .= " WHERE  sku = '" . $this->db->escape($output['sku']) . "'";
      $this->db->query ($sql);
      return NULL;
      }
    }  
  }
if ($index=='sumki.net_description') {  // Пример пользовательского фильтра - сборное описание товара
  $output['description'] = '';
  foreach ($item->param as $value) {
    if ($value['name']=='tx_outs')  $output['description'] .= (string)$value;
    if ($value['name']=='tx_ins')   $output['description'] .= (string)$value;
    if ($value['name']=='text')     $output['description'] .= (string)$value;
    if ($value['name']=='tx_dop')   $output['description'] .= (string)$value;
    }
  }
if ($index=='sumki.net') {  // Пример пользовательского фильтра - сборное описание товара
  $output['description'] = '';
  foreach ($item->param as $value) {
    if ($value['name']=='tx_outs')  $output['description'] .= (string)$value;
    if ($value['name']=='tx_ins')   $output['description'] .= (string)$value;
    if ($value['name']=='text')     $output['description'] .= (string)$value;
    if ($value['name']=='tx_dop')   $output['description'] .= (string)$value;
    }
  if (isset($item->param)) {
    $shirina = '';
    $vysota = '';
    $tolshina_glubina = '';
    foreach ($item->param as $value) {
      if ($value['name']=='shirina'||$value['name']=='width')           $shirina          = (string)$value;
      if ($value['name']=='vysota'||$value['name']=='height')           $vysota           = (string)$value;
      if ($value['name']=='tolshina_glubina'||$value['name']=='length') $tolshina_glubina = (string)$value;
      if ($value['name']=='color') {
        $option_key = 'param_color';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    $razmer = $shirina . ' x ' . $vysota. ' x ' . $tolshina_glubina;
    $output['attr'][2] = (string)$razmer;
    }
  }
if ($index=='sumki.net4') {  // Пример пользовательского фильтра - сборное описание товара
  $output['description'] = '';
  foreach ($item->param as $value) {
    if ($value['name']=='tx_outs')  $output['description'] .= (string)$value;
    if ($value['name']=='tx_ins')   $output['description'] .= (string)$value;
    if ($value['name']=='text')     $output['description'] .= (string)$value;
    if ($value['name']=='tx_dop')   $output['description'] .= (string)$value;
    }
  if (isset($item->param)) {
    $shirina = '';
    $vysota = '';
    $tolshina_glubina = '';
    foreach ($item->param as $value) {
      if ($value['name']=='shirina'||$value['name']=='width')           $shirina          = (string)$value;
      if ($value['name']=='vysota'||$value['name']=='height')           $vysota           = (string)$value;
      if ($value['name']=='tolshina_glubina'||$value['name']=='length') $tolshina_glubina = (string)$value;
      if ($value['name']=='color') {
        $option_key = 'param_color';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    $razmer = $shirina . ' x ' . $vysota. ' x ' . $tolshina_glubina;
    $output['attr'][4] = (string)$razmer;
    }
  }
if ($index=='eurogalant') {
  if (isset($item->param)) {
    $shirina = '';
    $vysota = '';
    $tolshina_glubina = '';
    foreach ($item->param as $value) {
      if ($value['name']=='shirina'||$value['name']=='width')           $shirina          = (string)$value;
      if ($value['name']=='vysota'||$value['name']=='height')           $vysota           = (string)$value;
      if ($value['name']=='tolshina_glubina'||$value['name']=='length') $tolshina_glubina = (string)$value;
      }
    $output['attr'][2] = (string)$shirina . ' x ' . $vysota. ' x ' . $tolshina_glubina;;
    }
  return $output; 
  }
if ($index=='Tango') {  // Пример пользовательского фильтра - обработка опции "размер" для http://www.textilgroup.ru/upload/yml_csv/export.yml
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
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
  }
if ($index=='Tango_Комплектатор') {  // Пример пользовательского фильтра - обработка опции "размер" для http://www.textilgroup.ru/upload/yml_csv/export.yml
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
            'quantity'      => $output['quantity'],
            );
          }
        $output['price'] = 0;
        return $output;
        }
      }
    }
  return NULL; // Блокируем загрузку товаров без опций
  }
if ($index=='Ormanix') {
  if (isset($item->price)) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->price);
    if (is_nan($output['price'])) return NULL;
    $output['price']  = round ( $output['price']+5, 1, PHP_ROUND_HALF_UP);
    if (isset($item->oldprice)) {
      if ($item->oldprice > $output['price']) {
          $output['special']  = $output['price'];
          $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->oldprice);
          if (is_nan($output['price'])) return NULL;
          $output['price']  = round ( $output['price']+5, 1, PHP_ROUND_HALF_UP);
          }
      }
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'sku'           => $item->scu,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $item->quantity,
            );
          }
        }
      }
    }
  }
if ($index=='hatsandcaps') {
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      if ($value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    }
  }
if ($index=='voltmarket') {  // Пример пользовательского фильтра - дублирование веса в поле "вес" и в атрибуты
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Вес брутто (кг)') $output['weight'] = (string)$value; 
      }
    }
  }
if ($index=='vkostume.ru') {  // Пример пользовательского фильтра - обработка опции "Цвет" для vkostume.ru
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if (isset($value['name'])&&$value['name']=='Русский размер') {
        $option_key = 'param_Русский размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      if (isset($value['name'])&&$value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
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
  }
if ($index=='rimari.ua') {  // Пример пользовательского фильтра - обработка опции "размер" для http://rimari.ua/
  if (isset($item['price'])) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item['price']);
    if (is_nan($output['price'])) return NULL;
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
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
  }
if ($index=='enigma') {
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            );
          }
        }
      if ($value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 0,
            );
          }
        }
      }
    }
  }
if ($index=='Размеры') {  // Пример пользовательского фильтра - обработка опции "размер" где размеры перечисленны через запятую
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Размер') {
        $option_key = 'param_Размер';
        if($data['options'][$option_key]['dest_id']>0) {
          $values = explode (',',(string)$value);
          foreach ($values as $option_value) { 
            $output['option'][] = array (
              'option_id'     => $data['options'][$option_key]['dest_id'], 
              'value'         => trim($option_value),
              'required'      => 1,
              'price'         => 0,
              'price_prefix'  => '+',
              'subtract'      => 0,
              'quantity'      => 1,
              );
            }
          }
        }
      }
    }
  }
if ($index=='tm-modus') { 
  if (isset($item->avail->size)) {
    foreach ($item->avail->size as $value) {
      if ($value) {
        $option_key = 'avail';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => $data['settings']['dov_required'],
            'price'         => 0,
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $value['quantity'],
            );
          }
        }
      }
    }
  }
if ($index=='param_Цвет материала') {  // Пример пользовательского фильтра - обработка опции "Цвет материала" http://www.petek-1855.ru/bitrix/catalog_export/catalog_petek.php
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Цвет материала') {
        $option_key = 'param_Цвет материала';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    }
  }
if ($index=='bumba.ru') {  // Пример пользовательского фильтра - обработка опции "Цвет" 
  // Подчищаем название
  $tmp = explode ('(', $output['name']);
  $output['name'] = trim($tmp[0]);
  // $output['price'] содержит оптовую цену со сделанными преобразованиями
  // $output['model'] содержит артикул опции
  $poip = array();
  if (isset($item->picture)) {
    foreach ($item->picture as $value) {
      $poip[] = htmlspecialchars((string)$value,ENT_QUOTES);
      }
    }
  if (isset($item->price)) {
    if ($item->price > $output['price']) $output['price'] = $item->price;
    }
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Опт 100') {
        $output['iprice'] = (float)$value;
        }
      if ($value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => $output['quantity'],
            'poip'          => $poip,
            'optsku'        => $output['model'],
            );
          $output['model'] = $output['sku'];
          }
        }
      }
    }
  }
if ($index=='param_Цвет') {  // Пример пользовательского фильтра - обработка опции "Цвет" 
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='Цвет') {
        $option_key = 'param_Цвет';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    }
  }
if ($index=='param_color') {  // Пример пользовательского фильтра - обработка опции "color" 
  if (isset($item->param)) {
    foreach ($item->param as $value) {
      if ($value['name']=='color') {
        $option_key = 'param_color';
        if($data['options'][$option_key]['dest_id']>0) {
          $output['option'][] = array (
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => (string)$value,
            'required'      => 1,
            'price'         => 0,
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
          }
        }
      }
    }
  }
if ($index=='sizes_size') {
  if (isset($item->sizes) && isset($data['options']['sizes'])) {
    if ($data['options']['sizes']['dest_type']=='option' && $data['options']['sizes']['dest_id']) {
      foreach ($item->sizes->size as $option_value) {
          $output['option'][] = array(
            'option_id'     => $data['options']['sizes']['dest_id'], 
            'value'         => (string)$option_value['name'],
            'required'      => 1,
            'price'         => '',
            'price_prefix'  => '+',
            'subtract'      => 1,
            'quantity'      => (int)$option_value,
            );
        }
      }
    }
  return $output;
  }
if ($index=='trade-city.ua') {  // Пример пользовательского фильтра - обработка тега "sizes" для http://trade-city.ua/export/partner_new.xml
  if (isset($item->base_price) && isset($item->base_currency) && isset($data['currencies'][(string)$item->base_currency]) ) {
    $output['price']    = (float)$item->base_price; 
    $output['price']   *= (float)$data['currencies'][(string)$item->base_currency]; 
    $output['price']    = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
    if (is_nan($output['price'])) return NULL;
    }
  if (isset($item->sizes) && isset($data['options']['sizes'])) {
    if ($data['options']['sizes']['dest_type']=='option' && $data['options']['sizes']['dest_id']) {
      foreach ($item->sizes->size as $option_value) {
          $output['option'][] = array(
            'option_id'     => $data['options'][$option_key]['dest_id'], 
            'value'         => htmlspecialchars($option_value),
            'required'      => 1,
            'price'         => '',
            'price_prefix'  => '+',
            'subtract'      => 0,
            'quantity'      => 1,
            );
        }
      }
    }
  }
if ($index=='glem.com.ua') {  // Пример пользовательского фильтра - обработка тега "avail" для http://www.glem.com.ua/eshop/xml.php
  $option_key = 'avail';
  if (isset($item->avail->size)&&$data['options'][$option_key]['dest_id']>0) {
    foreach ($item->avail->size as $value) {
      $output['option'][] = array (
        'option_id'     => $data['options'][$option_key]['dest_id'], 
        'value'         => (string)$value,
        'required'      => 0,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => $value['quantity'],
        );
      }
    }
  }
if ($index=='gepur.com') {  // Пример пользовательского фильтра - обработка тега "product_sizes" для http://gepur.com/
  if (isset($item->product_prices->price_uah)) {
    $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->product_prices->price_uah);
    if (is_nan($output['price'])) return NULL;
    }
  $option_key = 'product_sizes';
  if (isset($item->product_sizes->size)&&$data['options'][$option_key]['dest_id']>0) {
    foreach ($item->product_sizes->size as $value) {
      $output['option'][] = array (
        'option_id'     => $data['options'][$option_key]['dest_id'], 
        'value'         => (string)$value,
        'required'      => 1,
        'price'         => 0,
        'price_prefix'  => '+',
        'subtract'      => 0,
        'quantity'      => 1,
        );
      }
    }
  }
// УДАЛИТЬ ЗА НЕНАДОБНОСТЬЮ
if ($index=='skip_zero_price') {  // Пример пользовательского фильтра - не загружать товары c нулевой ценой
  if ($output['price']==0) return NULL;
  }
// УДАЛИТЬ ЗА НЕНАДОБНОСТЬЮ
if ($index==1) {  // Пример пользовательского фильтра - не загружать товары дешевле 5000
  if ($output['price']<5000) return NULL;
  }
if ($index=='слияние названия и размера') {  // Пример пользовательского фильтра - слияние названия и размера
  if (isset($item->param_value)) {
    $output['name'] .= ' (' . (string)$item->param_value . ')';
    }
  }
if ($index==2) {  // Пример пользовательского фильтра - наценка 10% на Sony
  if ($output['vendor']=='Sony') $output['price'] *= 1.1;
  }
if ($index=='handybrands') {  // Пользовательский фильтр - удаляем акцию если цены совпадают
  if (isset($output['special'])) {
    if ($output['special']==$output['price']) unset($output['special']);
    }
  }
if ($index==3) {
  $output['price']    = 0;
  if (isset($item->prices)) {
    foreach ($item->prices->price as $value) {
      if ($value->order==2) $output['price'] = (float)$value->value;
      }
    }
  $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
  if (is_nan($output['price'])) return NULL;
  }
if ($index=='kill_minimum') {
  $output['minimum']  = 1;
  }
if ($index=='ak-cent.kz') {
  $output['price']    = 0;
  if (isset($item->prices)) {
    foreach ($item->prices->price as $value) {
      if ($value['type']=='Дилерская цена') $output['price'] = (float)$value->value;
      }
    }
  $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$output['price']);
  if (is_nan($output['price'])) return NULL;
  }
if ($index=='princforcat' && count($output['option'])) {  // Пользовательский фильтр
  // ----     ОПЦИИ      -----------------------------------------------------
	$princforcat_price  = $this->config->get('princforcat_price');
  $category_id        = $output['category_id'];

  foreach ($output['option'] as $key => $option) {
    // $option['option_id']
    // $option['value'] 
    // $option['price'] 
    if (!$option['value']) continue;

    $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($option['value']) . "' AND option_id = '" . $option['option_id'] . "'");
    if ($option_ids->row) $option_value_id = $option_ids->row['option_value_id'];
    else                  $option_value_id = 0;

//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("option_value_id - " . $option_value_id) . "', user = '" . $this->db->escape($data['user']) . "'");

    if (isset($princforcat_price[$category_id][$option['option_id']][$option_value_id])) {
      $output['option'][$key]['price'] = $princforcat_price[$category_id][$option['option_id']][$option_value_id];
      }
    }
	}

return $output; 
}

}
?>