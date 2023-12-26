<?php
class ModelZoXml2ZoXml2UpdateAnalyzer extends Model {

public function doUpdateAnalyzer($output,$data,$item,$row) {
  $parts = explode (':',$data['settings']['update_analyzer']);
  if (isset($parts[1]))   return $this->old_doUpdateAnalyzer(trim($parts[0]),$output,$data,$item,$row,trim($parts[1]));
  return $this->old_doUpdateAnalyzer($data['settings']['update_analyzer'],$output,$data,$item,$row,'');
  }
public function old_doUpdateAnalyzer($index,$output,$data,$item,$row,$param) {
  $product_id = $row['product_id'];

  if ($index=='update_avatara') { 
    if (!empty($output['avatara']) && empty($row['avatara'])) {
      $data = $this->model_zoxml2_zoxml2->loadImage ($output['avatara'], $data, 0);
      if ($data['dest_image']) {
        $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), avatara = '" . $this->db->escape($data['dest_image']) . "' WHERE product_id = '" . (int)$product_id . "'");
        }
      }
    return $output;
    }
  if ($index=='updateUpc') {
    if (empty($output['upc'])) return NULL;
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), upc = '" . $this->db->escape($output['upc']) . "' WHERE product_id = '" . (int)$product_id . "'");
    return 1;
    }
  if ($index=='v4_biz_weight_correction') { 
    if ($output['weight']==0) $output['weight']= $row['weight'];
    return $output;
    }
  if ($index=='gifts.ru4ImprovedOptions_UpdateOptions_v2') {
    if (isset($item->free) && !empty($data['options']['free']['dest_id'])) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$data['options']['free']['dest_id'] . "'");
      $text = trim($item->free); 
      if ($text) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$data['options']['free']['dest_id'] . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
        }
      }
    if (isset($item->inwayamount) && !empty($data['options']['inwayamount']['dest_id'])) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$data['options']['inwayamount']['dest_id'] . "'");
      $text = trim($item->inwayamount); 
      if ($text) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$data['options']['inwayamount']['dest_id'] . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
        }
      }
    if (isset($item->inwayfree) && !empty($data['options']['inwayfree']['dest_id'])) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$data['options']['inwayfree']['dest_id'] . "'");
      $text = trim($item->inwayfree); 
      if ($text) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$data['options']['inwayfree']['dest_id'] . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
        }
      }
    return $output;
    }
  if ($index=='ean2related') {
    if (!empty($output['ean'])) {
  		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
  		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
      // <param name="Рекомендуемые товары">2174, 2186, 2192, 2224, 14183</param>
      $parts = explode (',',$output['ean']);
      $data['product_related'] = array();
      foreach ($parts as $related_sku) {
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape(trim($related_sku)) . "'");
        if (!empty($query->row['product_id'])) $data['product_related'][] = $query->row['product_id'];
        }
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
  			}
      return 1;
      }
    return 0;
    }
  if ($data['settings']['update_analyzer']=='min_20') {
    if ($output['quantity'] < 20) $output['quantity'] = 0; 
    return $output;
    }
  if ($index=='log_output') {
    if ($param=='all' || $param==$output['sku']) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'log_output', data = '" . $this->db->escape(json_encode($output)) . "', user = '" . $this->db->escape($data['user']) . "'");
    return $output;
    }
  if ($index=='sport-hunt') {
    if ($row['status'] != 0)      return $output;
    if ($output['quantity'] != 0) return $output;
    return 0;
    }
  if ($index=='zo_stocks') {
    if (!isset($row['updated_by'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `updated_by` VARCHAR(63)");
      }
    if (!isset($row['zo_stocks'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `zo_stocks` TEXT NOT NULL");
      $row['zo_stocks'] = '';
      }
    $zo_stocks = ($row['zo_stocks']=='')?array():json_decode($row['zo_stocks'],TRUE);
    $zo_stocks[$data['session_key']]['price']    = $output['price'];
    $zo_stocks[$data['session_key']]['quantity'] = $output['quantity'];
    $output['updated_by'] = $data['session_key'];
    // СБРАСЫВАЕМ СКЛАДЫ В БАЗУ
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), zo_stocks = '" . $this->db->escape(json_encode($zo_stocks)) . "' WHERE product_id = '" . (int)$product_id . "'");
    return $output;
    }
  if ($index=='zo_stocks_price_control') {
    if (!isset($row['updated_by'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `updated_by` VARCHAR(63)");
      }
    if (!isset($row['zo_stocks'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `zo_stocks` TEXT NOT NULL");
      $row['zo_stocks'] = '';
      }
    $zo_stocks = ($row['zo_stocks']=='')?array():json_decode($row['zo_stocks'],TRUE);
    $zo_stocks[$data['session_key']]['price']    = $output['price'];
    $zo_stocks[$data['session_key']]['quantity'] = $output['quantity'];
    // ВЫЧИСЛЯЕМ МИНИМАЛЬНУЮ И МАКСИМАЛЬНУЮ ЦЕНУ
    $min_price = $output['price'];
    $max_price = $output['price'];
    $min_price_key = $data['session_key'];
    foreach ($zo_stocks as $key => $stock) {
      if ($stock['price']<$min_price) {
        $min_price = $stock['price']; 
        $min_price_key = $key;
        }
      if ($stock['price']>$max_price) $max_price = $stock['price']; 
      }
    // ВЫЧИСЛЯЕМ МИНИМАЛЬНУЮ ЦЕНУ С КОНТРОЛЕМ ОСТАТКА
    $active_key = '';
    foreach ($zo_stocks as $key => $stock) {
      if ($stock['quantity']>0) {
        if ($stock['price']<=$max_price) {
          $max_price = $stock['price']; 
          $active_key = $key;
          }
        }
      }
    // ИТОГИ
    if (!$active_key) {  // У ВСЕХ ТОВАРОВ НУЛЕВЫЕ ОСТАТКИ
      $output['price'] = $min_price;
      $output['updated_by'] = $min_price_key;
      }
    else {
      $output['price'] = $max_price;
      $output['quantity'] = $zo_stocks[$active_key]['quantity'];
      $output['updated_by'] = $active_key;
      }
    // СБРАСЫВАЕМ СКЛАДЫ В БАЗУ
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), zo_stocks = '" . $this->db->escape(json_encode($zo_stocks)) . "' WHERE product_id = '" . (int)$product_id . "'");
    return $output;
    }
  if ($index=='zo_stocks_quantity_control') {
    if (!isset($row['updated_by'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `updated_by` VARCHAR(63)");
      }
    if (!isset($row['zo_stocks'])) {
      $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `zo_stocks` TEXT NOT NULL");
      $row['zo_stocks'] = '';
      }
    $zo_stocks = ($row['zo_stocks']=='')?array():json_decode($row['zo_stocks'],TRUE);
    $zo_stocks[$data['session_key']]['price']    = $output['price'];
    $zo_stocks[$data['session_key']]['quantity'] = $output['quantity'];
    // ВЫЧИСЛЯЕМ МИНИМАЛЬНУЮ ЦЕНУ С КОНТРОЛЕМ ОСТАТКА
    $active_key = $data['session_key'];
    foreach ($zo_stocks as $key => $stock) {
      if ($stock['quantity']>0) {
        if ($key!=$active_key) {
          $active_key = $key;
          break;  // У ОСНОВНОГО СКЛАДА  ПРИОРИТЕТ ВЫШЕ!
          }
        }
      }
    // ИТОГИ
    $output['quantity'] = $zo_stocks[$active_key]['quantity'];
    $output['updated_by'] = $active_key;
    // СБРАСЫВАЕМ СКЛАДЫ В БАЗУ
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), zo_stocks = '" . $this->db->escape(json_encode($zo_stocks)) . "' WHERE product_id = '" . (int)$product_id . "'");
    return $output;
    }
  if ($index=='p5s_stok__amuroff_UpdateROptions') {
    $output['quantity'] = 0;
    if (isset($data['settings']['update_quantity'])) {
      foreach ($item->assortiment->assort as $option) {
        $output['quantity'] += (int)$option['sklad'];
        $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$option['sklad'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($option['aID']) . "'");
        }
      $this->db->query ("UPDATE " . DB_PREFIX . "product_option_value SET quantity ='" . (int)$output['quantity'] . "'  WHERE product_id = '" . (int)$product_id . "'");
      }
    return $output;
    }
  if ($index=='p5s_stok__UpdateROptions') {
    $quantity   = 0;
    if (isset($data['settings']['update_quantity'])) {
      foreach ($item->assortiment->assort as $option) {
        // <assort aID="61" sklad="22" barcode="782421104207"
        $quantity += (int)$option['sklad'];
        $this->db->query ("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '" . (int)$option['sklad'] . "' WHERE product_id = '" . (int)$product_id . "' AND model = '" . $this->db->escape($option['aID']) . "'");
        }
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$product_id . "'");
      }
    if (isset($data['settings']['update_price'])) {
      $output['price'] = $this->model_zoxml2_zoxml2->doPrice  ($data,$item->price['BaseRetailPrice']);
      if (is_nan($output['price'])) return NULL;
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$output['price']) . "' WHERE product_id = '" . (int)$product_id . "'");
      }
    return 1;
    }
  if ($index=='pohodpro_ursus_price_control') {
    // Если текущая цена выше, то оставляем ее!
    if ($row['price'] > $output['price']) $output['price'] = $row['price']; 
    // Прописываем ID постащика
    $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
    $sql .= ", location = '" . $this->db->escape($data['session_key']) . "'";
    $sql .= " WHERE product_id = '" . (int)$product_id . "'";
    $this->db->query($sql);
    return $output;
    }
  if ($index=='set_date') {
    if (!empty($item->date)) {
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), date_added = '" . $this->db->escape($item->date) . "' WHERE product_id = '" . (int)$product_id . "'");
      }
    return $output;
    }
  if ($index=='set_mpn') {
    if (!empty($output['mpn'])) {
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), mpn = '" . $this->db->escape($output['mpn']) . "' WHERE product_id = '" . (int)$product_id . "'");
      }
    return $output;
    }
  if ($index=='poip') {
    if (isset($output['option'][0] )) $output['option'][0]['poip'] = $output['image'];
    return $output;
    }
  if ($index=='KeepStatus') {
    $output['KeepStatus'] = $row['status'];
    return $output;
    }
  if ($index=='supplier2supplier') {
    $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
    $sql .= ", supplier = '" . $this->db->escape($data['session_key']) . "'";
    $sql .= " WHERE product_id = '" . (int)$product_id . "'";
    $this->db->query($sql);
    return $output;
    }
  if ($index=='supplier2location') {
    $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
    $sql .= ", location = '" . $this->db->escape($data['session_key']) . "'";
    $sql .= " WHERE product_id = '" . (int)$product_id . "'";
    $this->db->query($sql);
    return $output;
    }
  if ($index=='SNT') {
    return NULL;
    }
  if ($index=='sport-hunt') {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape('ПРОПУСК - артикул уже есть на сайте: '. $output['sku']) . "', user = '" . $this->db->escape($data['user']) . "'");
    return NULL;
    }
  if ($index=='add_quantity') {
    $output['quantity'] += $row['quantity'];
    return $output;
    }
  if ($index=='skip_sbx') {
    if ($row['jan']=='sbx' && $row['quantity']>0) return NULL;
    return $output;
    }
  if ($index=='skip_sbx_shp_rct') {
    if ($row['jan']=='sbx' && $row['quantity']>0) return NULL;
    if ($row['jan']=='shp' && $row['quantity']>0) return NULL;
    if ($row['jan']=='rct' && $row['quantity']>0 && $row['price']<$output['price']) return NULL;
    return $output;
    }
  if ($index=='skip_for_rct') {
      if ($row['jan']=='sbx' && $row['quantity']>0) return NULL;
      if ($row['jan']=='shp' && $row['quantity']>0) return NULL;
      if ($row['jan']=='dbx' && $row['quantity']>0) return NULL;
      if ($row['jan']=='brc' && $row['quantity']>0 && $row['price']<=$output['price']) return NULL;
      return $output;
      }
  if ($index=='Skip_if_quantity_not_zero') {
    if ($row['quantity'] > 0) return NULL;
    }
//  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = 'Я UpdateAnalyzer!', user = '" . $this->db->escape($data['user']) . "'");
  return $output;
  }

}
?>