<?php
class ModelZoXml2ZoXml2After extends Model {

public function doUserAfter($data) {   
  $parts = explode (';',$data['settings']['user_after']);
  foreach ($parts as $user_after) {
    $this->__doUserAfter($data,$user_after); 
    }      
  }
public function __doUserAfter($data,$user_after) {                
    $session_key = $data['session_key'];

if ($user_after=='hpmodel_links_2_1') {
  require_once 'admin/model/module/hpmodel.php';
  $model = new ModelModuleHpmodel($this->registry);
  $model->update();
  }
if ($user_after=='hpmodel_links_2_3') {
  require_once 'admin/model/extension/module/hpmodel.php';
  $model = new ModelExtensionModuleHpmodel($this->registry);
  $model->update();
  }
if ($user_after=='hpmodel_links_3_0') {
  require_once 'admin/model/extension/module/hpmodel.php';
  $model = new ModelExtensionModuleHpmodel($this->registry);
  $model->update();
  }
if ($user_after=='KillBadSpecial') {
	$query = $this->db->query("SELECT ps.product_id FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = ps.product_id) WHERE p.price<=ps.price");
  foreach ($query->rows as $row) {
	  $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$row['product_id'] . "'");
    }
  }
if ($user_after=='sku2model') {
  $sql = "UPDATE " . DB_PREFIX . "product SET model=sku";
  $where = $this->model_zoxml2_zoxml2->getContext ($data);
  $this->db->query ($sql . $where);
  }
if ($user_after=='max_sort_order_for_empty_product') {
  $sql = "UPDATE " . DB_PREFIX . "product SET sort_order = '999', date_modified = NOW()";
  $where = $this->model_zoxml2_zoxml2->getContext ($data);
  $where .= " AND quantity < '1'";
  $this->db->query ($sql . $where);
  }
if ($user_after=='brainyfilter_clear_cache') {
  require_once 'admin/model/extension/module/brainyfilter.php';
  $model = new ModelExtensionModuleBrainyFilter($this->registry);
  $model->fillTaxRateTable();
  $model->fillCacheTable();
  }
if ($user_after=='set_main_image_from_additional') {
    $sql = "SELECT * FROM " . DB_PREFIX . "product ";
    $where = $this->model_zoxml2_zoxml2->getContext ($data);
    $where .= " AND image=''";
    $query = $this->db->query ($sql . $where);
    foreach ($query->rows as $result) {
      $product_id = $result['product_id'];
      $images = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' AND image!=''");
      if ($images->row) {
		    $this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), image  = '" . $this->db->escape($images->row['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
        }
      }
   }
if ($user_after=='amuroff_set_mpn') {
  $this->db->query("UPDATE " . DB_PREFIX . "product SET mpn =''");
  $this->db->query("UPDATE " . DB_PREFIX . "product SET mpn ='Бесплатная доставка' WHERE price>3000");
  $this->db->query("UPDATE " . DB_PREFIX . "product SET mpn ='' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_special WHERE price<3000)");
  }
if ($user_after=='js-company_set_model') {
  $where = $this->model_zoxml2_zoxml2->getContext ($data);
  $this->db->query("UPDATE " . DB_PREFIX . "product  SET model = CONCAT(product_id,'-44')" . $where);
//  $this->db->query("UPDATE " . DB_PREFIX . "product  SET status = '1', model = CONCAT(product_id,'-44')" . $where);
  if ($data['settings']['hide']) {
    $this->db->query("UPDATE " . DB_PREFIX . "product  SET status = '0'" . $where . " AND quantity<1");
    }
  $this->cache->delete('filterpro');
  }
if ($user_after=='ro_min_price') {

$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = 'ro_min_price', user = '" . $this->db->escape($data['user']) . "'");

  $old_values   = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE location='" . $data['session_key'] . "'");
  foreach ($old_values->rows as $result) {
    $product_id = $result['product_id'];
    $query = $this->db->query("SELECT SUM(`quantity`) AS total, MIN(price) as price FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), price = '" . $this->db->escape((float)$query->row['price']) . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    // АКЦИИ
    if (isset($data['settings']['update_special'])) {
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
      $query = $this->db->query("SELECT MIN(ros.price) as price FROM " . DB_PREFIX . "relatedoptions_special  ros LEFT JOIN " . DB_PREFIX . "relatedoptions ro ON (ro.relatedoptions_id = ros.relatedoptions_id) WHERE ro.product_id = '" . (int)$product_id . "'"); 
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '0', priority = '1', price = '" . $this->db->escape((float)$query->row['price']) . "'");
      }
    }
  }
if ($user_after=='qpstol_set_stock_status_id') {
  $old_values   = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE location='" . $data['session_key'] . "' AND quantity<'1'");
  foreach ($old_values->rows as $result) {
    $status_ids   = $this->db->query("SELECT stock_status_id FROM " . DB_PREFIX . "relatedoptions WHERE product_id='" . (int)$result['product_id'] . "' AND stock_status_id!='" . $data['settings']['stock_status_id'] . "'");
    if ($status_ids->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET stock_status_id='" . (int)$status_ids->row['stock_status_id'] . "'  WHERE product_id='" . (int)$result['product_id'] . "'");
      }
    }
  }
if ($user_after=='level99_models') {
  $old_values   = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE supplier='" . $data['session_key'] . "'");
  foreach ($old_values->rows as $result) {
    $model  = $result['product_id'];
    $model .= "-13";
    $this->db->query("UPDATE " . DB_PREFIX . "product SET model='" . $model . "'  WHERE product_id='" . $result['product_id'] . "'");
    // seo_title и seo_H1
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_title = name  WHERE product_id='" . $result['product_id'] . "' AND seo_title =''");
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_h1    = name  WHERE product_id='" . $result['product_id'] . "' AND seo_h1 =''");
    }
  }
if ($user_after=='tyrrussia_models') {
  $old_values   = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE supplier='" . $data['session_key'] . "'");
  foreach ($old_values->rows as $result) {
    $model  = $result['product_id'];
    $model .= "-10";
    $this->db->query("UPDATE " . DB_PREFIX . "product SET model='" . $model . "'  WHERE product_id='" . $result['product_id'] . "'");
    // seo_title и seo_H1
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_title = name  WHERE product_id='" . $result['product_id'] . "' AND seo_title =''");
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_h1    = name  WHERE product_id='" . $result['product_id'] . "' AND seo_h1 =''");
    }
  }
if ($user_after=='runlab_models') {
  $old_values   = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE supplier='" . $data['session_key'] . "'");
  foreach ($old_values->rows as $result) {
    $model  = $result['product_id'];
    $model .= "-11";
    $this->db->query("UPDATE " . DB_PREFIX . "product SET model='" . $model . "'  WHERE product_id='" . $result['product_id'] . "'");
    // seo_title и seo_H1
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_title = name  WHERE product_id='" . $result['product_id'] . "' AND seo_title =''");
    $this->db->query("UPDATE " . DB_PREFIX . "product_description SET seo_h1    = name  WHERE product_id='" . $result['product_id'] . "' AND seo_h1 =''");
    }
  }
if ($user_after=='SNT.Update') { // Скрипт обновления цен и остатков через АПИ snt.su
  // SNT_API_KEY должен быть прописаны в config.php в корне сайта
  // define('SNT_API_KEY', '3e7286_______________0eae15');

  if (isset($data['settings']['update'])) {
    $session_key = $data['session_key'];
    $user        = $data['user'];

    $where = $this->model_zoxml2_zoxml2->getContext ($data);
    if (isset($data['settings']['no_update'])) {
      $where .= " AND status ='1'";
      }

    $price_key = "price";
    if (defined('SNT_API_PRICE')) $price_key = SNT_API_PRICE;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . "Обновление цен и количества" . "', user = '" . $this->db->escape($user) . "'");
    // загружаем файл цен и остатков: http://snt.su/api/products/pages/?page=1&key=SNT_API_KEY
    $page = 1;
    while ($page>0) {
      $url = "http://snt.su/api/products/pages/?page=" . $page . "&key=" . SNT_API_KEY;
      if (isset($data['module']['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape($url) . "', user = '" . $this->db->escape($user) . "'");
        }
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$src = curl_exec($ch);
      curl_close ($ch);
      if (isset($data['module']['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape($src) . "', user = '" . $this->db->escape($user) . "'");
        }
      $values = json_decode ($src, true);
      if (!is_array($values)) {
        break;
        }
      $txt = 'SNT API: page - '. $page++; 
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      foreach ($values as $sku => $result) {
        if (isset($result['in_stock'])&&isset($result[$price_key])) {
          $price = $this->model_zoxml2_zoxml2->doPrice  ($data,$result[$price_key]);
          if (is_nan($price)) continue;
          $quantity = $result['in_stock']=='mnogo'?10:$result['in_stock'];
          if ($data['settings']['hide']&&$quantity<1) $status=0;
          else                                         $status=1;
          $sql = "UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', quantity = '" . (int)$quantity . "', date_modified = NOW(), price = '" . (float)$price . "'" . $where . " AND sku = '" . $this->db->escape($sku) . "'";
          $this->db->query ($sql);
          }
        }
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . "Обновление цен и количества - завершено" . "', user = '" . $this->db->escape($user) . "'");
    }
  }
if ($user_after=='Odeyalaoptom.Update') { // Скрипт обновления цен и остатков через АПИ Odeyalaoptom.ru
  // ODEYALAOPTOM_API_KEY и ODEYALAOPTOM_PARTNER_ID должны быть прописаны в config.php в корне сайта
    $url = 'http://odeyalaoptom.ru/sst/api/catalog/';
    $part     = 1;
    $parts    = 1;
    $updated  = 0;
    
    do {
      $request = array (
        'type'    => 'update',      // тип запроса
        'partner' => ODEYALAOPTOM_PARTNER_ID,    // ИД партнера
        'part'    => $part        // часть каталога
        );
  
  		$paramsJson = json_encode($request);
  		$paramsJson = base64_encode($paramsJson);
  		$signature = '';
  		foreach ($request as $val) $signature .=$val;
  		$signature .= ODEYALAOPTOM_API_KEY;
  		$signature = md5($signature);
  		$ch = curl_init($url);
  		curl_setopt($ch, CURLOPT_VERBOSE, 0);
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0); // используем локальный днс кеш
  		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0); // отключаем днс-кеширование
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		curl_setopt($ch, CURLOPT_POST, true);
  		curl_setopt($ch, CURLOPT_POSTFIELDS, 'RS='.$signature.'&data='.$paramsJson);
  		$res = curl_exec($ch);
  		$errNo = curl_errno($ch);
  		$err = curl_error($ch);
  		$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  		curl_close($ch);
  		$json = base64_decode($res);
  		$arResult = json_decode($json, 1);
  		if($arResult && $errNo == 0 && !$err && $http_response_status < 400) {
        if($arResult['STATUS'] == 1) {
          $parts        = $arResult['DATA']['CNT_PARTS'];
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("API - Загружена страница: " . $part . " из " . $parts) . "', user = '" . $this->db->escape($data['user']) . "'");
if (isset($data['module']['DEBUG'])) {
  $tmp = json_encode($arResult['DATA']['ITEMS']);
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape($tmp) . "', user = '" . $this->db->escape($data['user']) . "'");
  }        
          foreach ($arResult['DATA']['ITEMS'] as $item) {
            usleep (100);
            $sku                    = $item['ARTICLE'];
            $model                  = $item['ARTICLE'];
            $quantity               = $item['QUANTITY'];
            $price                  = $item['PRICE'];
            $price                  = $this->model_zoxml2_zoxml2->doPrice  ($data,$item['PRICE']);
            if (is_nan($price)) continue;

          if (isset($data['module']['DEBUG'])) {
            $tmp = "sku: " . $sku . " quantity: " . $quantity;
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape($tmp) . "', user = '" . $this->db->escape($data['user']) . "'");
            }        
           // Формируем поисковый запрос
            $where = '';
            $sql   = "SELECT * FROM " . DB_PREFIX . "product p ";
            if ($data['settings']['link'] == 'sku') {
              if ($where) $where .= " AND ";
              $where .= "p.sku = '" . $this->db->escape($sku) . "'";
              }
            if ($data['settings']['link'] == 'model') {
              if ($where) $where .= " AND ";
              $where .= "p.model = '" . $this->db->escape($model) . "'";
              }
            if (isset($data['settings']['link_supplier'])&&$data['settings']['link_supplier']!='nop') {   
              if ($where) $where .= " AND ";
              if ($data['settings']['supplier']=='location')  $where .= "p.location = '" .   $this->db->escape($data['session_key']) . "'";
              if ($data['settings']['supplier']=='supplier')  $where .= "p.supplier = '" .   $this->db->escape($data['session_key']) . "'";
              if ($data['settings']['supplier']=='mpn')       $where .= "p.mpn = '" .        $this->db->escape($data['session_key']) . "'";
              }
            $sql .= " WHERE " . $where . " GROUP BY p.product_id";
            $query = $this->db->query($sql);
            // ---------------------------------------------------------------------
            if ($query->row) { // Обновление продукта
              $product_id = $query->row['product_id'];
              $sql = "UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW()";
              if ( isset($data['settings']['update_price']))   $sql .= ", price = '" . (float)$price . "'";
              if (isset($data['settings']['update_quantity'])) $sql .= ", quantity = '" . (int)$quantity . "'";     
              $sql .= " WHERE product_id = '" . (int)$product_id . "'";
      	      $this->db->query($sql);
              $updated ++;
              }
            }
          }
        else break;
        }
      else break;
      } while ($part++<$parts);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape("API - обновлено товаров: " . $updated) . "', user = '" . $this->db->escape($data['user']) . "'");
    return;
    }
if ($user_after=='HideNoImageProducts') {
    $query = $this->db->query("UPDATE " . DB_PREFIX . "product SET status='0'  WHERE image=''");
    return;
    }
if ($user_after=='HideZeroProducts') {
    $query = $this->db->query("UPDATE " . DB_PREFIX . "product SET status='0'  WHERE quantity ='0'");
    return;
    }
if ($user_after=='KillEmptyROptionValues') {
    $parts = explode ('|', $data['settings']['before_mode']);
    if (!empty($parts[1]))                         $session_key = $parts[1];
    $where='';
    if ($data['settings']['supplier']=='location')  $where=" WHERE p.location='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where=" WHERE p.supplier='" . $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where=" WHERE p.mpn='" .      $this->db->escape($session_key) . "'";
    $zoxml2_permisions = '';
    $this->db->query("DELETE FROM  " . DB_PREFIX . "relatedoptions WHERE quantity = '0' AND product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
    return;
    }
if ($user_after=='KillEmptyOptionValues_only4this') {
    $before_mode = $data['settings']['before_mode'];
    $parts = explode ('|', $before_mode);
    if (isset($parts[0]) && $parts[0]=='supplier') $before_mode = 'supplier';
    if (!empty($parts[1]))                         $session_key = $parts[1];
    if ($before_mode=='all') $where='';
    else                                         {
      if ($data['settings']['supplier']=='location')  $where=" WHERE p.location='" . $this->db->escape($session_key) . "'";
      if ($data['settings']['supplier']=='supplier')  $where=" WHERE p.supplier='" . $this->db->escape($session_key) . "'";
      if ($data['settings']['supplier']=='mpn')       $where=" WHERE p.mpn='" .      $this->db->escape($session_key) . "'";
      }
    $zoxml2_permisions = '';
    if (!empty($data['module']['zoxml2_permisions'])) {
      if ($where) $zoxml2_permisions = " AND product_id NOT IN (SELECT zp_product_id as product_id FROM " . DB_PREFIX . "zoxml2_permisions WHERE zoxml2_permision_hold='1')";
      else $zoxml2_permisions =      " WHERE product_id NOT IN (SELECT zp_product_id as product_id FROM " . DB_PREFIX . "zoxml2_permisions WHERE zoxml2_permision_hold='1')";
      }
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE quantity = '0' AND product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
    return;
    }
if ($user_after=='KillEmptyOptionValues') {
    $query = $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE `quantity` = '0'");
    return;
    }
if ($user_after=='show_non_zero_products') {
    $query = $this->db->query("UPDATE " . DB_PREFIX . "product SET status='1'  WHERE location='" . $data['session_key'] . "' AND quantity > '0'");
    }
if ($user_after=='show_all_products') {
    $query = $this->db->query("UPDATE " . DB_PREFIX . "product SET status='1'  WHERE location='" . $data['session_key'] . "'");
    }
if ($user_after=='priceF') {
    $query = $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity='0'  WHERE location='" . $data['session_key'] . "' AND status = '1'");
    }
if ($user_after=='velvetovo_Комплектатор') {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Обработка цен опций" . "', user = '" . $this->db->escape($data['user']) . "'");

    if ($data['settings']['before_mode']=='all') $table='product';
    else                                         $table=$data['settings']['supplier']=='mcg2'?'product_to_supplier':'product';
    
    if ($data['settings']['before_mode']=='all') $where='';
    else                                         $where=$data['settings']['supplier']=='mcg2'?(" WHERE supplier='" . $data['session_key'] . "'"):(" WHERE location='" . $data['session_key'] . "'");
    
    $query = $this->db->query("SELECT product_id,price FROM " . DB_PREFIX . $table . $where);

    foreach ($query->rows as $result) {
      usleep ($data['module']['sleep']);
      $product_id = $result['product_id'];
      $price      = $result['price'];
      $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET price = '" . (float)$price . "' WHERE product_id = '" . (int)$product_id . "'");
      $this->db->query("UPDATE " . DB_PREFIX . "product              SET price = '0' WHERE product_id = '" . (int)$product_id . "'");
      }
  
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Обработка цен опций - завершено" . "', user = '" . $this->db->escape($data['user']) . "'");
    }
if ($user_after=='Нормализация') {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Нормализация цен опций" . "', user = '" . $this->db->escape($data['user']) . "'");


    $where = $this->model_zoxml2_zoxml2->getContext ($data);
    $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product " . $where);

    foreach ($query->rows as $result) {
      $product_id = $result['product_id'];
      usleep ($data['module']['sleep']);
      $old_values   = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE price>0 AND product_id = '" . (int)$product_id . "' ORDER BY price ASC");
      if ($old_values->row) {
        $price = (float)$old_values->row['price'];
        $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)$price . "' WHERE product_id = '" . (int)$product_id . "'");
        foreach ($old_values->rows as $option) {
          $this->db->query("UPDATE " . DB_PREFIX . "product_option_value  SET price = '" . (float)($option['price']-$price) . "' WHERE   product_option_value_id = '" . (int)$option['product_option_value_id'] . "'");
          }
        }
      }
  
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Нормализация цен опций - завершено. Товаров: " . count($query->rows) . "', user = '" . $this->db->escape($data['user']) . "'");
    }
if ($user_after=='SetMinPrice') {
    
$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "SetMinPrice" . "', user = '" . $this->db->escape($data['user']) . "'");

  $where = $this->model_zoxml2_zoxml2->getContext ($data);
  $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product " . $where);
    
$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "SetMinPrice - найдено товаров: " . count($query->rows) . "', user = '" . $this->db->escape($data['user']) . "'");
$i = 0;
$j = 0;
  foreach ($query->rows as $result) {
    if ($i++ == 10) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "SetMinPrice - обновлено товаров: " . $j . "', user = '" . $this->db->escape($data['user']) . "'");
      $i = 0;
      }
    $product_id = $result['product_id'];
    $price_query = $this->db->query("SELECT SUM(`product_id`) AS total, MIN(price) as price FROM " . DB_PREFIX . "product_option_value WHERE price > '0' AND product_id = '" . (int)$product_id . "'"); 
    if ($price_query->row) {
      $j ++;
      if ($price_query->row['total']) $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)$price_query->row['price'] . "'  WHERE product_id = '" . (int)$product_id . "'");
      }
    }  
  }
if ($user_after=='МинЦена') {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Нормализация цен опций" . "', user = '" . $this->db->escape($data['user']) . "'");

    $table='product';
       if ($data['settings']['before_mode']=='all') $where='';
    else                                         $where=$data['settings']['supplier']=='mcg2'?(" WHERE supplier='" . $data['session_key'] . "'"):(" WHERE location='" . $data['session_key'] . "'");
    
    $query = $this->db->query("SELECT product_id, manufacturer_id FROM " . DB_PREFIX . $table . $where);
    $price_prefix = '=';
    $weight       = '2.5';
    $length       = '0.45';
    $width        = '0.15';
    $height       = '0.35';
    
    foreach ($query->rows as $result) {
      $product_id = $result['product_id'];
      $model = (string)$product_id . '-' . (string)$result['manufacturer_id'];
      usleep ($data['module']['sleep']);
      $old_values   = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE price>0 AND product_id = '" . (int)$product_id . "' ORDER BY price ASC");
      if ($old_values->row) {
        $price = (float)$old_values->row['price'];
        $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)$price . "', weight = '" . (float)$weight . "', length = '" . (float)$length . "', width = '" . (float)$width . "', height = '" . (float)$height . "', model = '" . $this->db->escape($model) . "'  WHERE product_id = '" . (int)$product_id . "'");
        foreach ($old_values->rows as $option) {
          $this->db->query("UPDATE " . DB_PREFIX . "product_option_value  SET price_prefix = '" . $this->db->escape($price_prefix) . "' WHERE   product_option_value_id = '" . (int)$option['product_option_value_id'] . "'");
          }
        }
      }
  
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . "Нормализация цен опций - завершено" . "', user = '" . $this->db->escape($data['user']) . "'");
    }

   }
}
?>
