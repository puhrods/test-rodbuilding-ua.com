<?php
class zoAttributes {
	private $template;
  private $language_id;
  private $db;

	public function __construct($template = 'common', $language_id = 1, $db) {
		$this->db          = $db;
		$this->template    = $template;
		$this->language_id = $language_id;
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_" . $template .  "_atribute_groups (
            group_id          VARCHAR(127)  NOT NULL, 
            path_name         VARCHAR(512) NOT NULL,   
            dest_id           INT(11)      NOT NULL, 
            UNIQUE KEY `group_id` (`group_id`)
            ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");

  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_" . $template .  "_atributes (
            group_id          VARCHAR(127)  NOT NULL, 
            path_name         VARCHAR(512)  NOT NULL, 
            PropertyName      VARCHAR(256)  NOT NULL, 
            PropertyID        VARCHAR(32)   NOT NULL, 
            dest_id           INT(11)       NOT NULL, 
            KEY  `group_id` (`group_id`), KEY  `PropertyName` (`PropertyName`)
            ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
	}

public function addProductAtributeId( $SupplierAtributeGroupName, $SupplierAtributeGroupID, $SupplierAtributeName, $SupplierAtributeID) {
  $attribute_group_id = $this->getAttributeGroupId($SupplierAtributeGroupName, $SupplierAtributeGroupID);

  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_" . $this->template .  "_atributes WHERE group_id = '" . $this->db->escape(trim($SupplierAtributeGroupID)) . "' AND PropertyID = '" . $this->db->escape(trim($SupplierAtributeID)) . "'");
  if ($query->row) return $query->row['dest_id']; 
  // ТАКОГО АТРИБУТА ЕЩЕ НЕТ В БАЗЕ МОДУЛЯ - ДОБАВЛЯЕМ
  $dest_id = $this->getAttributeId($attribute_group_id, $SupplierAtributeName);
  $attr_path_name = '[' . $SupplierAtributeGroupName . ']' . '[' . $SupplierAtributeName . ']';
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_" . $this->template .  "_atributes SET group_id = '" . $this->db->escape(trim($SupplierAtributeGroupID)) . "', path_name = '" . $this->db->escape($attr_path_name) ."', PropertyID = '" . $this->db->escape(trim($SupplierAtributeID)) . "', PropertyName = '" . $this->db->escape(trim($SupplierAtributeName)) . "', dest_id = '" . (int)$dest_id . "'");
  return $dest_id;
  }
public function getProductAtributeId( $SupplierAtributeGroupName, $SupplierAtributeGroupID, $SupplierAtributeName,$SupplierAtributeID='') {
  $attribute_group_id = $this->getAttributeGroupId($SupplierAtributeGroupName, $SupplierAtributeGroupID);

  if (!$SupplierAtributeID) $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_" . $this->template .  "_atributes WHERE group_id = '" . $this->db->escape(trim($SupplierAtributeGroupID)) . "' AND PropertyName = '" . $this->db->escape(trim($SupplierAtributeName)) . "'");
  else                      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_" . $this->template .  "_atributes WHERE group_id = '" . $this->db->escape(trim($SupplierAtributeGroupID)) . "' AND PropertyID = '" . $this->db->escape(trim($SupplierAtributeID)) . "'");
  if ($query->row) return $query->row['dest_id']; 
  // ТАКОГО АТРИБУТА ЕЩЕ НЕТ В БАЗЕ МОДУЛЯ - ДОБАВЛЯЕМ
  if ($SupplierAtributeID) return 0;
  $dest_id = $this->getAttributeId($attribute_group_id, $SupplierAtributeName);
  $attr_path_name = '[' . $SupplierAtributeGroupName . ']' . '[' . $SupplierAtributeName . ']';
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_" . $this->template .  "_atributes SET group_id = '" . $this->db->escape(trim($SupplierAtributeGroupID)) . "', path_name = '" . $this->db->escape($attr_path_name) ."', PropertyName = '" . $this->db->escape(trim($SupplierAtributeName)) . "', dest_id = '" . (int)$dest_id . "'");
  return $dest_id;
  }

public function getAttributeId($attribute_group_id, $name) {
  // Ищем в базе сайта атрибут с именем $name принадлежащий группе $attribute_group_id
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN  " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.name = '" . $this->db->escape(trim($name)) . "' AND a.attribute_group_id = '" . (int)$attribute_group_id . "'");
  if ($query->row) return $query->row['attribute_id'];
  // Нет такого - добавляем
	$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$attribute_group_id . "', sort_order = '0'");
	$attribute_id = $this->db->getLastId();
	$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$this->language_id . "', name = '" . $this->db->escape(trim($name)) . "'");
  return $attribute_id;
  }

public function getAttributeGroupId($name, $id) {
  // ИЩЕМ В БАЗЕ МОДУЛЯ
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_" . $this->template .  "_atribute_groups WHERE group_id = '" . $this->db->escape(trim($id)) . "'");
  if ($query->row) return $query->row['dest_id'];
  // Ищем в базе сайта  trim($row->name)
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE name = '" . $this->db->escape($name) . "'");
  if ($query->row) $attribute_group_id = $query->row['attribute_group_id'];
  else {
  	$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = '0'");
  	$attribute_group_id = $this->db->getLastId();
  	$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$this->language_id . "', name = '" . $this->db->escape($name) . "'");
    }
  // ДОБАВЛЯЕМ В БАЗУ МОДУЛЯ
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_" . $this->template .  "_atribute_groups SET group_id = '" . $this->db->escape(trim($id)) . "', path_name = '" . $this->db->escape($name) ."', dest_id = '" . (int)$attribute_group_id . "'");
  return $attribute_group_id;
  }

}


class ModelZoXml2ZoXml2 extends Model {

public function myUrlEncode($string) {
  $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '+');
  $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "_");
  return str_replace($entities, $replacements, urlencode($string));
  }

public function myUrlEncode2($string) {
  $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '+');
  $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "_");
  return str_replace($entities, $replacements, $string);
  }
public function translit($text,$prefix='')	{
		$ru  = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ы', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю', 'Я', ' ', '/', '.', '"', "'");
		$tr  = array('a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'ts', 'ch', 'sh', 'sch', 'y', 'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'TS', 'CH', 'SH', 'SCH', 'Y', 'E', 'YU', 'YA', '-', '-', '-', '', '');
		$str = $prefix . strtolower (preg_replace('/[^A-Za-z0-9-_\/]+/', '', str_replace($ru, $tr, html_entity_decode($text))));

$str = str_replace("--", "-", $str ); 
   
    $i   = 0;
    do {
    $tmp = $str . ($i?$i:'');
    $i ++;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $tmp . "'");
    } while ($query->row);
    $i --;
    
    return ($str . ($i?$i:''));
	}
public function translit3($text,$prefix="")	{
		$ru  = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ы', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю', 'Я', ' ', '/', '.', '"', "'");
		$tr  = array('a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'ts', 'ch', 'sh', 'sch', 'y', 'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'TS', 'CH', 'SH', 'SCH', 'Y', 'E', 'YU', 'YA', '-', '-', '-', '', '');
		$str = $prefix . strtolower (preg_replace('/[^A-Za-z0-9-_\/]+/', '', str_replace($ru, $tr, $text)));

$str = str_replace("--", "-", $str ); 
   
    $i   = 0;
    do {
    $tmp = $str . ($i?$i:'');
    $i ++;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $tmp . "'");
    } while ($query->row);
    $i --;
    
    return ($str . ($i?$i:''));
	}

public function doSeoUrl($data,$output,$product_id) {
  if (empty($output['url_tpl'])) return;
   // SEO 1.5 - 2.3
  if (isset($data['module']['url_alias'])) {
      $output['url_tpl'] = $this->translit($output['url_tpl']);
      if ($output['url_tpl']) $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($output['url_tpl']) . "'");
      }
  // SEO 3.0
    if (isset($data['module']['seo_url'])) {
      $output['url_tpl'] = $this->translit3($output['url_tpl']);
      if ($output['url_tpl']) $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '0', language_id = '" . (int)$data['settings']['language'] . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($output['url_tpl']) . "'");
      }
    return $output;
    }

public function zoURLEncode($string) {
  $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '+');
  $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "_");
  return str_replace($entities, $replacements, urlencode($string));
  }


protected function ___link2category_id ($product_id,$category_id) {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$category_id . "'");
  if (!$query->row) {  
    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET 
            product_id = '" .       (int)$product_id . "', 
            category_id = '" .      $category_id . "'");
    }
  }
protected function do_category_up ($product_id,$category_id) {
  $cat_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE category_id = '" . (int)$category_id . "'");
  while ($cat_query->row) {
    $category_id = $cat_query->row['parent_id']; 
    if ($category_id>0) $this->___link2category_id ($product_id,$category_id);
    else break;
    $cat_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE category_id = '" . (int)$category_id . "'");
    }
  }
public function link2category_ids ($product_id,$category_ids,$do_category_up=FALSE) {
  foreach ($category_ids as $category_id) {
    $this->___link2category_id ($product_id,$category_id);
    if ($do_category_up) $this->do_category_up ($product_id,$category_id);
    }
  }

public function link2category_id ($engine,$product_id,$category_id,$do_category_up=FALSE) {
  if ($engine=='ocStore') {
  $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET 
          product_id = '" .       (int)$product_id . "', 
          main_category =         '1', 
          category_id = '" .      $category_id . "'");
  } else {
  $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET 
          product_id = '" .       (int)$product_id . "', 
          category_id = '" .      $category_id . "'");
          }
  if ($do_category_up) $this->do_category_up ($product_id,$category_id);
  }

	public function getVendor($session_key,$name) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE session_key = '" . $this->db->escape($session_key) . "' AND name = '" . $this->db->escape(trim($name)) . "'");
    if ($query->row) if ($query->row['manufacturer_id']>0) return $query->row['manufacturer_id'];
    return 0; // СДЕЛАТЬ ДОБАВЛЕНИЕ и УВЕДОМЛЕНИЕ!
  }

	public function getVendorAndMargin($data,$name) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE session_key = '" . $this->db->escape($data['session_key']) . "' AND name = '" . $this->db->escape(trim($name)) . "'");
    if ($query->row && $query->row['manufacturer_id']>0) {
      $ansver = array(
  			'manufacturer_id'  => $query->row['manufacturer_id'],
  			'margin'           => $query->row['margin'],
        );
      return $ansver;
      }
    if (!$query->row) { // У постащика новый бренд - выводим уведомление
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($name) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $name) ."'");
      }
    return NULL;
  }

	public function getCategoryAndMarginByID($session_key,$id) {
    if (empty($id)) return NULL;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($session_key) . "' AND `data` = '" . $this->db->escape($id) . "'");
    if ($query->row && $query->row['category_id']>0) {
      $ansver = array(
  			'category_id'  => $query->row['category_id'],
  			'margin'       => empty($query->row['margin'])?'':$query->row['margin'],
        );
      return $ansver;
      }
    // У поставщика появилась новая категория
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория. ID: ' . $id . ". Нажмите ЗАГРУЗИТЬ КАТЕГОРИИ, ПРОИЗВОДИТЕЛЕЙ И АТРИБУТЫ для получения полной статистики") ."'");
      }
    return NULL;
  }

	public function getCategory($session_key,$name,$parent="") {
    if (!$parent) $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($session_key) . "' AND name = '" . $this->db->escape(trim($name)) . "'");
    else $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($session_key) . "' AND name = '" . $this->db->escape(trim($name)) . "' AND parent = '" . $this->db->escape(trim($parent)) . "'");
    if ($query->row) return $query->row['category_id']>0?$query->row['category_id']:0;
    if ($name!="(категория не указана)") $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $name . ". Нажмите ЗАГРУЗИТЬ КАТЕГОРИИ, ПРОИЗВОДИТЕЛЕЙ И АТРИБУТЫ для получения полной статистики") ."'");
    return 0;
  }

	public function getCategoryByID($session_key,$id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($session_key) . "' AND `data` = '" . $this->db->escape(trim($id)) . "'");
    if ($query->row) return $query->row['category_id']>0?$query->row['category_id']:0;
    // У поставщика появилась новая категория
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория. ID: ' . trim($id) . ". Нажмите ЗАГРУЗИТЬ КАТЕГОРИИ, ПРОИЗВОДИТЕЛЕЙ И АТРИБУТЫ для получения полной статистики") ."'");
    return 0;
  }

	public function getContext($data) {
    $before_mode = $data['settings']['before_mode'];
    $session_key = $data['session_key'];
    $parts = explode ('|', $before_mode);
    if (isset($parts[0]) && $parts[0]=='supplier') $before_mode = 'supplier';
    if (!empty($parts[1]))                         $session_key = $parts[1];

    if ($before_mode=='all') $where=" WHERE product_id!='0'"; // ДОБАВЛЯЕМ НЕНУЖНУЮ ПРОВЕРКУ, чтобы не было пустых условий
    else                                         {
      if ($data['settings']['supplier']=='location')  $where=" WHERE location='" . $this->db->escape($session_key) . "'";
      if ($data['settings']['supplier']=='supplier')  $where=" WHERE supplier='" . $this->db->escape($session_key) . "'";
      if ($data['settings']['supplier']=='mpn')       $where=" WHERE mpn='" .      $this->db->escape($session_key) . "'";
      }
    if (!empty($data['module']['zoxml2_permisions'])) {
      $where .= " AND product_id NOT IN (SELECT zp_product_id as product_id FROM " . DB_PREFIX . "zoxml2_permisions WHERE zoxml2_permision_hold='1')";
      }
    return $where;
    }
	public function before($session_key,$data,$user) {
    $log_key     = $session_key;
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
    
    switch ($data['settings']['before']) {
      case 'nop': break;
      case 'del': {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Удаление продуктов" . "', user = '" . $this->db->escape($user) . "'");
    
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product p" . $where);
        foreach ($query->rows as $result) {
          $product_id = $result['product_id'];
    //$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Удаление продукта: " . $product_id . "', user = '" . $this->db->escape($user) . "'");
          usleep (100);
          // Удалить все выбранные 
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product                          WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute                WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description              WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount                 WHERE product_id = '" . (int)$product_id . "'");
          if (isset($data['module']['product_filter'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_filter                 WHERE product_id = '" . (int)$product_id . "'");
            }
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image                    WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option                   WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value             WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related                  WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward                   WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special                  WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category              WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download              WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout                WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store                 WHERE product_id = '" . (int)$product_id . "'");
      		$this->db->query("DELETE FROM " . DB_PREFIX . "review                           WHERE product_id = '" . (int)$product_id . "'");
          if (isset($data['module']['url_alias'])) $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
          if (isset($data['module']['seo_url']))   $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url   WHERE query = 'product_id=" . (int)$product_id. "'");

          if (!empty($data['module']['zoxml2_permisions'])) {
          	$this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_permisions       WHERE zp_product_id = '" . (int)$product_id . "'");
            }
      		
          if (isset($data['module']['mcg2'])) {
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr              WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr_special      WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr_discount     WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr_option       WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_input_price            WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_ym                  WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_supplier            WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_sliv                   WHERE product_id = '" . (int)$product_id . "'");
        		$this->db->query("DELETE FROM " . DB_PREFIX . "product_country_of_origin      WHERE product_id = '" . (int)$product_id . "'");
            }
          if (isset($data['module']['zoannouncement2'])) {
          		$this->db->query("DELETE FROM " . DB_PREFIX . "product_zoannouncement       WHERE product_id = '" . (int)$product_id . "'");
              }
          if (isset($data['module']['zotuning2'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_tuning                 WHERE product_id = '" . (int)$product_id . "'");
            }
          if (isset($data['module']['hpmodel_links'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "hpmodel_links                  WHERE product_id = '" . (int)$product_id . "'");
            }
          if (isset($data['module']['oc2'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_parent          WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_data      WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_zo_special             WHERE product_id = '" . (int)$product_id . "'");
            }
          if (isset($data['module']['ro2'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_variant_product WHERE product_id = '" . (int)$product_id . "'");        
            $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions_option          WHERE product_id = '" . (int)$product_id . "'");        
            $this->db->query("DELETE FROM " . DB_PREFIX . "relatedoptions                 WHERE product_id = '" . (int)$product_id . "'");  
            }
          if (isset($data['module']['poip'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "poip_option_image              WHERE product_id = '" . (int)$product_id . "'");        
            }
          }
         
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Удаление продуктов завершено" . "', user = '" . $this->db->escape($user) . "'");
        break;
        }
      case 'hide': {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Отключение продуктов" . "', user = '" . $this->db->escape($user) . "'");
        $this->db->query("UPDATE      " . DB_PREFIX . "product p SET status = '0'" . $where . $zoxml2_permisions);  
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Отключение продуктов завершено" . "', user = '" . $this->db->escape($user) . "'");
        break;
        }
      case 'zero_product': {
        $hide = $data['settings']['hide']==1?", status = '0'":'';
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества" . "', user = '" . $this->db->escape($user) . "'");
        if (isset($data['settings']['update_stock_status_id'])) $this->db->query("UPDATE      " . DB_PREFIX . "product p SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $hide . $where . $zoxml2_permisions);
        else                                                    $this->db->query("UPDATE      " . DB_PREFIX . "product p SET quantity = '0'" . $hide . $where . $zoxml2_permisions);
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества завершен" . "', user = '" . $this->db->escape($user) . "'");
        break;
        }
      case 'zero': {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества" . "', user = '" . $this->db->escape($user) . "'");
        $hide = $data['settings']['hide']==1?", status = '0'":'';
        if (isset($data['settings']['update_stock_status_id'])) $hide .= ", stock_status_id = '" . $data['settings']['stock_status_id'] . "'"; 
        $this->db->query("UPDATE " . DB_PREFIX . "product p SET quantity = '0'" . $hide . $where . $zoxml2_permisions);
        // опции
        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
        // связанные опции
        if (isset($data['module']['ro2'])) {
          $this->db->query("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
          }
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества завершен" . "', user = '" . $this->db->escape($user) . "'");
        break;
        }
      case 'zero_no_hide': {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества" . "', user = '" . $this->db->escape($user) . "'");
        if (isset($data['settings']['update_stock_status_id'])) $this->db->query("UPDATE " . DB_PREFIX . "product p SET quantity = '0', stock_status_id = '" . $data['settings']['stock_status_id'] . "'" . $where . $zoxml2_permisions);
        else                                                    $this->db->query("UPDATE " . DB_PREFIX . "product p SET quantity = '0'" . $where . $zoxml2_permisions);
        // опции
        $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
        // связанные опции
        if (isset($data['module']['ro2'])) {
          $this->db->query("UPDATE " . DB_PREFIX . "relatedoptions SET quantity = '0' WHERE product_id IN (SELECT product_id FROM " . DB_PREFIX . "product p"  . $where . $zoxml2_permisions . ")");
          }
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($log_key) . "', type = 'info', data = '" . "Сброс количества завершен" . "', user = '" . $this->db->escape($user) . "'");
        break;
        }
      }
    }

public function doPrice ($data,$price) {
  $price = preg_replace('/[^(\d|,|\.)]/misu', '', $price);
  $price    = (float)str_replace(",",".",$price);
//  $price    = str_replace(" ","", $price);
//  $price    = (float)str_replace (chr(194).chr(160), "", $price); 
  if (!empty($data['manufacturer_info']['margin'])) {
    $parts = explode (';', $data['manufacturer_info']['margin']);
    foreach ($parts as $part) {
      $rule = explode (':', $part);
      if ($price<=(float)$rule[0]) {
        if (!isset($rule[1])) return NAN; //- для блокировки загрузки товара
        $members = explode ('|', $rule[1]);
        if (count($members)<3) {
          if ($members[0]==0) return NAN; //- для блокировки загрузки товара
          $price *= (float)$rule[1];
          }
        else $price = ((float)$members[0] + (float)$price) * (float)$members[1] + (float)$members[2];
        return round($price, $data['settings']['round_mode']); 
        }
      }
    return round($price, $data['settings']['round_mode']); 
    }
  if (!empty($data['category_info']['margin'])) {
    $parts = explode (';', $data['category_info']['margin']);
    foreach ($parts as $part) {
      $rule = explode (':', $part);
      if ($price<=(float)$rule[0]) {
        if (!isset($rule[1])) return NAN; //- для блокировки загрузки товара
        $members = explode ('|', $rule[1]);
        if (count($members)<3) {
          if ($members[0]==0) return NAN; //- для блокировки загрузки товара
          $price *= (float)$rule[1];
          }
        else $price = ((float)$members[0] + (float)$price) * (float)$members[1] + (float)$members[2];
        return round($price, $data['settings']['round_mode']); 
        }
      }
    return round($price, $data['settings']['round_mode']); 
    }
  $price   += (float)$data['settings']['add_before'];
  $price   *= (float)$data['settings']['mul_after'];
  $price   += (float)$data['settings']['add_after'];
  if (!empty($data['settings']['price_table'])) {
    $parts = explode (';', $data['settings']['price_table']);
    foreach ($parts as $part) {
      $rule = explode (':', $part);
      if ($price<=(float)$rule[0]) {
        if (!isset($rule[1])) return NAN; //- для блокировки загрузки товара
        $members = explode ('|', $rule[1]);
        if (count($members)<3) {
          if ($members[0]==0) return NAN; //- для блокировки загрузки товара
          $price *= (float)$rule[1];
          }
        else $price = ((float)$members[0] + (float)$price) * (float)$members[1] + (float)$members[2];
        return round($price, $data['settings']['round_mode']); 
        }
      }
    }
  return round($price, $data['settings']['round_mode']); 
  }

public function doDefaultOption($option_key,$output,$data,$value) {
  if (isset($data['options'][$option_key])) {
    switch ($data['options'][$option_key]['dest_type']) {
      case 'option_value':                  break; 
      case 'option_values_over_semicolon':  break; 
      case 'option_values_over_comma':      break; 
      case 'option_values_over_pipe':       break; 
      case 'option_values_over_slash':      break; 
      default:                              return $output;
      }
    }
  else return $output;
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'BEFORE', data = '" . $this->db->escape(json_encode($value)) . "', user = '" . $this->db->escape($data['user']) . "'");
    $value = htmlspecialchars(trim($value));
    if (!$value) return $output;
  // !!! Цена еще не прошла ПОДСТАНОВКИ и ФОРМУЛУ
  // dov_ - default_option_value
  $quantity = $data['settings']['dov_quantity']=='{quantity}'?$output['quantity']:$data['settings']['dov_quantity'];

//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'BEFORE', data = '" . $this->db->escape(json_encode($output['price'])) . "', user = '" . $this->db->escape($data['user']) . "'");
  if ($data['settings']['dov_price']=='{price}') {
    $price = $this->doPrice ($data,$output['price']);
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'AFTER', data = '" . $this->db->escape(json_encode($price)) . "', user = '" . $this->db->escape($data['user']) . "'");
    if (is_nan($price)) return $output;
    $output['price'] = 0;
    }
  else {
    $price    = $data['settings']['dov_price'];
    }
 
  if (isset($data['options'][$option_key])) {
    switch ($data['options'][$option_key]['dest_type']) {
      case 'option_value':              { 
        $option = array(
        'option_id'     => $data['options'][$option_key]['dest_id'],
        'value'         => $value,
        'price_prefix'  => $data['settings']['dov_price_prefix'],
        'required'      => $data['settings']['dov_required'],
        'subtract'      => $data['settings']['dov_subtract'],
        'quantity'      => $quantity,
        'price'         => $price,
        );
        if (!empty($output['optsku']))          $option['optsku']       = $output['optsku'];
        if (!empty($output['io_sku']))          $option['sku']          = $output['io_sku'];
        if (!empty($output['io_upc']))          $option['upc']          = $output['io_upc'];
        if (!empty($output['io_model']))        $option['model']        = $output['io_model'];
        if (!empty($output['io_description']))  $option['description']  = $output['io_description'];
        if (!empty($output['poip_image'][$data['options'][$option_key]['dest_id']])) $option['poip'] = $output['poip_image'][$data['options'][$option_key]['dest_id']];  
        $output['option'][] = $option;
        break; 
        } 
      case 'option_values_over_semicolon':  { 
        $values = explode (';',(string)$value);
          foreach ($values as $option_value) {
            $output['option'][] = array(
            'option_id'     => $data['options'][$option_key]['dest_id'],
            'value'         => htmlspecialchars(trim($option_value)),
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'required'      => $data['settings']['dov_required'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $quantity,
            'price'         => $price,
            );
            }
          break; 
          } 
      case 'option_values_over_comma':  { 
        $values = explode (',',(string)$value);
          foreach ($values as $option_value) {
            $output['option'][] = array(
            'option_id'     => $data['options'][$option_key]['dest_id'],
            'value'         => htmlspecialchars(trim($option_value)),
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'required'      => $data['settings']['dov_required'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $quantity,
            'price'         => $price,
            );
            }
          break; 
          } 
      case 'option_values_over_pipe':   { 
        $values = explode ('|',(string)$value);
          foreach ($values as $option_value) {
            $output['option'][] = array(
            'option_id'     => $data['options'][$option_key]['dest_id'],
            'value'         => htmlspecialchars(trim($option_value)),
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'required'      => $data['settings']['dov_required'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $quantity,
            'price'         => $price,
            );
            }
        break; 
        } 
      case 'option_values_over_slash':   { 
        $values = explode ("/",(string)$value);
          foreach ($values as $option_value) {
            $output['option'][] = array(
            'option_id'     => $data['options'][$option_key]['dest_id'],
            'value'         => htmlspecialchars(trim($option_value)),
            'price_prefix'  => $data['settings']['dov_price_prefix'],
            'required'      => $data['settings']['dov_required'],
            'subtract'      => $data['settings']['dov_subtract'],
            'quantity'      => $quantity,
            'price'         => $price,
            );
            }
        break; 
        } 
      }
    }
  return $output;
  }
public function doParams($option_key,$output,$data,$value) {
  if (isset($data['options'][$option_key])) {
    switch ($data['options'][$option_key]['dest_type']) {
      case 'attr':  
        if (is_array($value)) break; 
        $value = trim($value);        {
        if(((int)$data['options'][$option_key]['dest_id'])>0 && $value) {
          if (empty($output['attr'][$data['options'][$option_key]['dest_id']])) $output['attr'][$data['options'][$option_key]['dest_id']] = $value;
          else $output['attr'][$data['options'][$option_key]['dest_id']] .= ", " . $value;
          }
        break;
        }
      case 'stock_status_id':          {
        if(((int)$data['options'][$option_key]['dest_id'])>0 && trim($value)) {
          $output['stock_status_id'] = $data['options'][$option_key]['dest_id'];
          }
        break;
        }
      case 'price':         { $output['price']        = trim((string)$value); break; } 
      case 'mpn':           { $output['mpn']          = trim((string)$value); break; } 
      case 'upc':           { $output['upc']          = trim((string)$value); break; } 
      case 'ean':           { $output['ean']          = trim((string)$value); break; } 
      case 'jan':           { $output['jan']          = trim((string)$value); break; } 
      case 'isbn':          { $output['isbn']         = trim((string)$value); break; } 
      case 'iprice':        { $output['iprice']       = trim((string)$value); break; } 
      case 'iprice_cur':    { $output['iprice_cur']   = trim((string)$value); break; } 
      case 'mc_price':      { $output['mc_price']     = trim((string)$value); break; } 
      case 'mc_price_cur':  { $output['mc_price_cur'] = trim((string)$value); break; } 
      case 'country':       { $output['country']      = trim((string)$value); break; } 
      case 'sliv':          { $output['sliv']         = trim((string)$value); break; } 
      case 'ym':            { $output['ym']           = trim((string)$value); break; } 
      case 'vendor':        { $output['vendor']       = trim((string)$value); break; } 
      case 'sku':           { $output['sku']          = trim((string)$value); break; } 
      case 'ro_sku':        { $output['ro_sku']       = trim((string)$value); break; } 
      case 'ro_model':      { $output['ro_model']     = trim((string)$value); break; } 
      case 'io_description':   { $output['io_description']       = trim((string)$value); break; } 
      case 'io_model':      { $output['io_model']     = trim((string)$value); break; } 
      case 'io_sku':        { $output['io_sku']       = trim((string)$value); break; } 
      case 'io_upc':        { $output['io_upc']       = trim((string)$value); break; } 
      case 'quantity':      { $output['quantity']     = trim((string)$value); break; } 
      case 'quantity_bool': { $output['quantity']     = trim((string)$value)=='true'?$data['settings']['quantity']:0; break; } 
      case 'minimum':       { $output['minimum']      = trim((string)$value); break; } 
      case 'weight':        { $output['weight']       = str_replace (",", ".", trim($value)) * (float)$output['weight_class_id_koeff']; 
                              if (!empty($data['settings']['weight_to_attr'])) {
                                $output['attr'][$data['settings']['weight_to_attr']] = $output['weight'];
                                }
                              break; } 
      case 'length':        { $output['length']       = str_replace (",", ".", trim($value)); 
                              if (!empty($data['settings']['length_to_attr'])) {
                                $output['attr'][$data['settings']['length_to_attr']] = $output['length'];
                                }
                              break; } 
      case 'width':         { $output['width']        = str_replace (",", ".", trim($value)); 
                              if (!empty($data['settings']['width_to_attr'])) {
                                $output['attr'][$data['settings']['width_to_attr']] = $output['width'];
                                }
                              break; } 
      case 'height':        { $output['height']       = str_replace (",", ".", trim($value)); 
                              if (!empty($data['settings']['height_to_attr'])) {
                                $output['attr'][$data['settings']['height_to_attr']] = $output['height'];
                                }
                              break; } 
      case 'l_w_h_x':       { // Габаритные размеры, перечисленные через любой разделитель
                              $value = str_replace (",", ".", trim($value));
                              if (empty($value)) break; 
                              $parts = preg_split ("/[^\d.]+/",$value); 
                              if (count($parts)<3) {
                                $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'Warning', data = '" . $this->db->escape("Формат габаритов не соответствует заявленному: " . $value . " Technical information: " . json_encode($parts) ) . "', user = '" . $this->db->escape($data['user']) . "'");
                                }
                              else {
                                $output['length']       = (string)$parts[0];
                                $output['width']        = (string)$parts[1];
                                $output['height']       = (string)$parts[2];
                                if (!empty($data['settings']['l_w_h_to_attr'])) {
                                  $l_w_h = str_replace ("{length}", (string)$output['length'], $data['settings']['l_w_h_template']);
                                  $l_w_h = str_replace ("{width}",  (string)$output['width'],  $l_w_h);
                                  $l_w_h = str_replace ("{height}", (string)$output['height'], $l_w_h);
                                  $output['attr'][$data['settings']['l_w_h_to_attr']] = $l_w_h;
                                  }
                                if (!empty($data['settings']['length_to_attr'])) {
                                  $output['attr'][$data['settings']['length_to_attr']] = $output['length'];
                                  }
                                if (!empty($data['settings']['width_to_attr'])) {
                                  $output['attr'][$data['settings']['width_to_attr']] = $output['width'];
                                  }
                                if (!empty($data['settings']['height_to_attr'])) {
                                  $output['attr'][$data['settings']['height_to_attr']] = $output['height'];
                                  }
                                }
                              break; 
                              } 
      case 'description':   { $output['description']  = trim((string)$value); break; }
      case 'add2description':   { $output['description']  .= trim((string)$value); break; }
      case 'announcement':  { $output['announcement'] = trim((string)$value); break; }
      case 'description_array':   { $output['description'] = $this->arrDamp ((array)$value); break; } 
      case 'oldprice':      { $output['oldprice']     = trim((string)$value); break; } 
      case 'model':         { $output['model']        = trim((string)$value); break; } 
      case 'name':          { $output['name']         = trim((string)$value); break; } 
      case 'location':      { $output['location']     = trim((string)$value); break; } 
      case 'cat_name':      { $output['cat_name']     = trim((string)$value); break; } 
      case 'par_cat_name':  { $output['par_cat_name'] = trim((string)$value); break; } 
      case 'poip':          { $output['poip_image'][$data['options'][$option_key]['dest_id']][] = $data['settings']['img_path'] . trim((string)$value); break; } 
      case 'image':         { 
        $value = trim($value); 
        if ($value) $output['image'][]      = $data['settings']['img_path'] . $value; 
        break; 
        } 
      case 'images':        { $output['image']        = $this->arrDamp2Array ($output['image'],(array)$value, $data['settings']['img_path']); break; } 
      case 'images_over_semicolon': { 
        $urls = explode (';', trim((string)$value));
        foreach ($urls as $url) $output['image'][] = $data['settings']['img_path'] . trim($url);
        break; 
        } 
      case 'images_over_comma': { 
        $urls = explode (',', trim((string)$value));
        foreach ($urls as $url) $output['image'][] = $data['settings']['img_path'] . trim($url);
        break; 
        } 
      case 'images_over_pipe': { 
        $urls = explode ('|', (string)$value);
        foreach ($urls as $url) $output['image'][] = $data['settings']['img_path'] . trim($url);
        break; 
        } 
      }
    }
  else {
    if ($option_key=='available') return $output;
    if ($option_key=='offer_id')  return $output;
//    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'doParams:unknown', data = '" . $this->db->escape($option_key) . "', user = '" . $this->db->escape($data['user']) . "'");
    }
  return $output;
  }

public function isInVariant ($relatedoptions_variant_id,$option_id) {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "relatedoptions_variant_option WHERE relatedoptions_variant_id = '" . (int)$relatedoptions_variant_id . "' AND option_id = '" . (int)$option_id . "'"); 
  if ($query->row) return true;
  return false;
  }

public function getVariantID ($variant) {
  $relatedoptions_variant_id = 0;
  $query = $this->db->query("SELECT DISTINCT relatedoptions_variant_id FROM " . DB_PREFIX . "relatedoptions_variant_option"); 
  foreach ($query->rows as $row) {
    $this_variant = false;
    foreach ($variant['options'] as $option) {
      $this_variant = $this->isInVariant($row['relatedoptions_variant_id'],$option['option_id']);
      if (!$this_variant) break;
      }
    if ($this_variant) { // Возможно это нужный вариант, но этот набор может быть не уникальным
      $count_query = $this->db->query("SELECT COUNT(`option_id`) AS total FROM " . DB_PREFIX . "relatedoptions_variant_option WHERE relatedoptions_variant_id = '" . (int)$row['relatedoptions_variant_id'] . "'"); 
      if (!$count_query->row) continue; // Такого не дожно быть, но береженого бог бережет
      if ($count_query->row['total']!=count($variant['options'])) continue; // Не совпадает кол-во!
      return $row['relatedoptions_variant_id'];
      }
    }
  if ($relatedoptions_variant_id==0) {
    // Создаем новый вариант!
    $relatedoptions_variant_name = "Вариант: ";
    foreach ($variant['options'] as $option) $relatedoptions_variant_name .= '[' . $option['option_id'] . ']';
    $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant SET relatedoptions_variant_name =  '" . $this->db->escape($relatedoptions_variant_name) . "'");   
    $relatedoptions_variant_id = $this->db->getLastId();
    foreach ($variant['options'] as $option) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_variant_option SET relatedoptions_variant_id =  '" . (int)$relatedoptions_variant_id . "', option_id =  '" . (int)$option['option_id'] . "'");   
      }
    }
  return $relatedoptions_variant_id;
  }

public function doReplase($output,$data) {
  if (!isset($data['replace'])) return $output;
  foreach ($data['replace'] as $result) {
    if ($result['type']=='description') $output['description'] = $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['description'] );
    if ($result['type']=='name')        $output['name'] =        $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['name'] );
    if ($result['type']=='sku')         $output['sku'] =         $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['sku'] );
    if ($result['type']=='model')       $output['model'] =       $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['model'] );
    if ($result['type']=='price')       $output['price'] =       $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['price'] );
    if ($result['type']=='quantity')    $output['quantity'] =    $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['quantity'] );
    if ($result['type']=='weight')      $output['weight'] =      $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , (string)$output['weight'] );
    if ($result['type']=='option')      {
      foreach ($output['option'] as $key => $option) {
        $output['option'][$key]['value'] = $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , $option['value'] );
        }
      if (isset($output['roption'])) {
        foreach ($output['roption'] as $offer_key => $offer) { 
          if (isset($offer['options'])) {
            foreach ($offer['options'] as $option_key => $option) {
              $output['roption'][$offer_key]['options'][$option_key]['value'] = $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , $option['value'] );
              }
            }
          }
        }
      }
    if ($result['type']=='attr')      {
      foreach ($output['attr'] as $key => $text) {
        $output['attr'][$key] = $this->doReplaceAction ( $data, $result['mode'], $result['txt_before'] , $result['txt_after'] , $text );
        }
      }
    }
  return $output;
}


public function doSQL($output,$data,$manufacturer_id) {
 // Формируем поисковый запрос
  $where = '';
  $sql   = "SELECT SQL_NO_CACHE * FROM " . DB_PREFIX . "product p ";
  if ($data['settings']['link']=='name') {
    $sql .= " LEFT JOIN  " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
    }
  if (!empty($data['module']['zoxml2_permisions'])) {
    $sql .= " LEFT JOIN  " . DB_PREFIX . "zoxml2_permisions zp ON (p.product_id = zp.zp_product_id)";
    }
  if ($data['settings']['link'] == 'sku') {
    if ($where) $where .= " AND ";
    $where .= "p.sku = '" . $this->db->escape($output['sku']) . "'";
    }
  if ($data['settings']['link'] == 'mpn') {
    if ($where) $where .= " AND ";
    $where .= "p.mpn = '" . $this->db->escape($output['mpn']) . "'";
    }
  if ($data['settings']['link'] == 'upc') {
    if ($where) $where .= " AND ";
    $where .= "p.upc = '" . $this->db->escape($output['upc']) . "'";
    }
  if ($data['settings']['link'] == 'ean') {
    if ($where) $where .= " AND ";
    $where .= "p.ean = '" . $this->db->escape($output['ean']) . "'";
    }
  if ($data['settings']['link'] == 'jan') {
    if ($where) $where .= " AND ";
    $where .= "p.jan = '" . $this->db->escape($output['jan']) . "'";
    }
  if ($data['settings']['link'] == 'isbn') {
    if ($where) $where .= " AND ";
    $where .= "p.isbn = '" . $this->db->escape($output['isbn']) . "'";
    }
  if ($data['settings']['link'] == 'name') {
    if ($where) $where .= " AND ";
    $where .= "pd.name = '" . $this->db->escape($output['name']) . "'";
    $where .= " AND pd.language_id = '" . (int)$this->config->get( 'config_language_id' ) . "'";
    }
  if ($data['settings']['link'] == 'model') {
    if ($where) $where .= " AND ";
    $where .= "p.model = '" . $this->db->escape($output['model']) . "'";
    }
  if (isset($data['settings']['link_vendor'])) {
    if ($where) $where .= " AND ";
    $where .= "p.manufacturer_id = '" . (int)$manufacturer_id . "'";
    }
  if (isset($data['settings']['link_supplier'])&&$data['settings']['supplier']!='nop') {   
    $parts = explode ('|', $data['settings']['before_mode']);
    if (!empty($parts[1]))                         $session_key = $parts[1];
    else                                           $session_key = $data['session_key'];
    if ($where) $where .= " AND ";
    if ($data['settings']['supplier']=='location')  $where .= "p.location = '" .   $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='supplier')  $where .= "p.supplier = '" .   $this->db->escape($session_key) . "'";
    if ($data['settings']['supplier']=='mpn')       $where .= "p.mpn = '" .        $this->db->escape($session_key) . "'";
    }
  $sql .= " WHERE " . $where . " GROUP BY p.product_id";
  return $sql;
  }


public function doMeta($output,$data) {
      if ($data['settings']['model_tpl'])              {
        $output['model'] =    str_replace ( '{model}',  $output['model'],                  $data['settings']['model_tpl'] );
        $output['model'] =    str_replace ( '{name}' ,  $output['name'],                   $output['model'] );
        $output['model'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['model'] );
        $output['model'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['model'] );
        $output['model'] =    str_replace ( '{price}' , $output['price'],                  $output['model'] );
        $output['model'] =    str_replace ( '{sku}',    $output['sku'],                    $output['model'] );
        } 
      if ($data['settings']['sku'])              {
        $output['sku'] =    str_replace ( '{sku}' ,   $output['sku'],                    $data['settings']['sku'] );
        $output['sku'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['sku'] );
        $output['sku'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['sku'] );
        $output['sku'] =    str_replace ( '{price}' , $output['price'],                  $output['sku'] );
        $output['sku'] =    str_replace ( '{model}',  $output['model'],                  $output['sku'] );
        $output['sku'] =    str_replace ( '{name}',   $output['name'],                   $output['sku'] );
        } 
/*
      if ($data['settings']['name_tpl'])              {
        $output['name'] =    str_replace ( '{name}' ,  $output['name'],                   $data['settings']['name_tpl'] );
        $output['name'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['name'] );
        $output['name'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['name'] );
        $output['name'] =    str_replace ( '{price}' , $output['price'],                  $output['name'] );
        $output['name'] =    str_replace ( '{model}',  $output['model'],                  $output['name'] );
        $output['name'] =    str_replace ( '{sku}',    $output['sku'],                    $output['name'] );
        } 
*/
      if ($data['settings']['name_tpl']) {
        $name_tpl = $data['settings']['name_tpl'];
        $name_tpl =    str_replace ( '{name}' ,  $output['name'],                   $name_tpl );
        $name_tpl =    str_replace ( '{brand}' , $output['vendor'],                 $name_tpl );
        $name_tpl =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $name_tpl );
        $name_tpl =    str_replace ( '{price}' , $output['price'],                  $name_tpl );
        $name_tpl =    str_replace ( '{model}',  $output['model'],                  $name_tpl );
        $name_tpl =    str_replace ( '{sku}',    $output['sku'],                    $name_tpl );
        $output['name'] = $name_tpl;
        } 
      if (isset($output['meta_keyword']))     {
        $output['meta_keyword'] =    str_replace ( '{name}',   $output['name'],                   $output['meta_keyword'] );
        $output['meta_keyword'] =    str_replace ( '{brand}',  $output['vendor'],                 $output['meta_keyword'] );
        $output['meta_keyword'] =    str_replace ( '{shop}',   $this->config->get('config_name'), $output['meta_keyword'] );
        $output['meta_keyword'] =    str_replace ( '{price}',  $output['price'],                  $output['meta_keyword'] );
        $output['meta_keyword'] =    str_replace ( '{model}',  $output['model'],                  $output['meta_keyword'] );
        $output['meta_keyword'] =    str_replace ( '{sku}',    $output['sku'],                    $output['meta_keyword'] );
        }        
      if (isset($output['meta_description'])) {
        $output['meta_description'] =    str_replace ( '{name}' ,  $output['name'],                   $output['meta_description'] );
        $output['meta_description'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['meta_description'] );
        $output['meta_description'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['meta_description'] );
        $output['meta_description'] =    str_replace ( '{price}',  $output['price'],                 $output['meta_description'] );
        $output['meta_description'] =    str_replace ( '{model}',  $output['model'],                  $output['meta_description'] );
        $output['meta_description'] =    str_replace ( '{sku}',    $output['sku'],                    $output['meta_description'] );
        }         
      if (isset($output['meta_title']))       {
        $output['meta_title'] =    str_replace ( '{name}' ,  $output['name'],                   $output['meta_title'] );
        $output['meta_title'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['meta_title'] );
        $output['meta_title'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['meta_title'] );
        $output['meta_title'] =    str_replace ( '{price}' , $output['price'],                  $output['meta_title'] );
        $output['meta_title'] =    str_replace ( '{model}',  $output['model'],                  $output['meta_title'] );
        $output['meta_title'] =    str_replace ( '{sku}',    $output['sku'],                    $output['meta_title'] );
        }         
      if (isset($output['meta_h1']))          {
        $output['meta_h1'] =    str_replace ( '{name}' ,  $output['name'],                   $output['meta_h1'] );
        $output['meta_h1'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['meta_h1'] );
        $output['meta_h1'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['meta_h1'] );
        $output['meta_h1'] =    str_replace ( '{price}',  $output['price'],                  $output['meta_h1'] );
        $output['meta_h1'] =    str_replace ( '{model}',  $output['model'],                  $output['meta_h1'] );
        $output['meta_h1'] =    str_replace ( '{sku}',    $output['sku'],                    $output['meta_h1'] );
        }        
      if (isset($output['seo_title']))       {
        $output['seo_title'] =    str_replace ( '{name}' ,  $output['name'],                   $output['seo_title'] );
        $output['seo_title'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['seo_title'] );
        $output['seo_title'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['seo_title'] );
        $output['seo_title'] =    str_replace ( '{price}' , $output['price'],                  $output['seo_title'] );
        $output['seo_title'] =    str_replace ( '{model}',  $output['model'],                  $output['seo_title'] );
        $output['seo_title'] =    str_replace ( '{sku}',    $output['sku'],                    $output['seo_title'] );
        }         
      if (isset($output['seo_h1']))          {
        $output['seo_h1'] =    str_replace ( '{name}' ,  $output['name'],                   $output['seo_h1'] );
        $output['seo_h1'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['seo_h1'] );
        $output['seo_h1'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['seo_h1'] );
        $output['seo_h1'] =    str_replace ( '{price}',  $output['price'],                  $output['seo_h1'] );
        $output['seo_h1'] =    str_replace ( '{model}',  $output['model'],                  $output['seo_h1'] );
        $output['seo_h1'] =    str_replace ( '{sku}',    $output['sku'],                    $output['seo_h1'] );
        }        
      if (isset($output['tag']))              {
        $output['tag'] =    str_replace ( '{name}' ,  $output['name'],                   $output['tag'] );
        $output['tag'] =    str_replace ( '{brand}' , $output['vendor'],                 $output['tag'] );
        $output['tag'] =    str_replace ( '{shop}' ,  $this->config->get('config_name'), $output['tag'] );
        $output['tag'] =    str_replace ( '{price}' , $output['price'],                  $output['tag'] );
        $output['tag'] =    str_replace ( '{model}',  $output['model'],                  $output['tag'] );
        $output['tag'] =    str_replace ( '{sku}',    $output['sku'],                    $output['tag'] );
        } 
      if (isset($output['url_tpl']))              {
        $output['url_tpl'] =    str_replace ( '{name}' ,  $output['name'],                   $output['url_tpl'] );
        $output['url_tpl'] =    str_replace ( '{model}',  $output['model'],                  $output['url_tpl'] );
        $output['url_tpl'] =    str_replace ( '{sku}',    $output['sku'],                    $output['url_tpl'] );
        $output['url_tpl'] =    str_replace ( '{brand}',  $output['vendor'],                 $output['url_tpl'] );
        } 
  return $output;
}

protected function doReplaceAction ($data, $mode, $a, $b, $c) {
  $musor = '{QWERTY}';
  switch ($mode) {
    case 'htmlspecialchars':  return str_replace('"', "'", $c); 
    case 'htmlentities':  return htmlentities ( $c, ENT_QUOTES ); 
    case 'replace':       return str_replace ( html_entity_decode($a), html_entity_decode($b), html_entity_decode($c) ); 
    case 'preg':          return preg_replace ( html_entity_decode($a), html_entity_decode($b), html_entity_decode($c) );
    case 'after':         return str_replace ( $musor, $b, str_replace ( $a, $a . $musor, $c ) );
    case 'before':        return str_replace ( $musor, $b, str_replace ( $a, $musor . $a, $c ) );
    case 'after_end':     return $c . $b;
    case 'before_begin':  return $b . $c;
    case 'translate': {
      if (empty($data['module']['ya_translate'])) return "Перевод невозможен - ключ АПИ Яндекс.Переводчика отсутствует.";
      $ch = curl_init(); 
      $action = array (
        'key'   => $data['module']['ya_translate'],
        'text'  => rawurlencode($a),
        'lang'  => $b . '-' . $c,
//        'format' => 'html'
        );
      curl_setopt($ch, CURLOPT_URL, 'https://translate.yandex.net/api/v1.5/tr.json/translate');
      curl_setopt($ch, CURLOPT_NOBODY, true); 
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_TIMEOUT, 59); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $action);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	$res = curl_exec($ch);
      $result = json_decode ($res);
      if (isset($module['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("CURL 2 YANDEX") . "'");
     	  $errNo = curl_errno($ch);
      	$err   = curl_error($ch);
      	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("http_response_status - " . $http_response_status) . "'");
        }
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'tranclation1', data = '" . $this->db->escape($res) . "'");
      $c = '';
      foreach ($result->text as $text) $c .= $text;
      curl_close ($ch);
      }
    }
  return $c;
}

public function arrDamp ($item) {
  $txt = '';
  if (is_array($item)) {
    foreach ($item as $value) {
      if (is_array($value)) $txt .= $this->arrDamp ($value);
      else { 
        if($txt) $txt .= '<br />';
        $txt .= $value;
        }
      }
    }
  else   $txt = $item;
  return $txt;
  }

public function arrDamp2Array ($txt, $item, $prefix) {
  if (is_array($item)) {
    foreach ($item as $value) {
      if (is_array($value)) $txt =  $this->arrDamp2Array ($txt, $value, $prefix);
      else                  $txt[] =  $prefix . (string)$value;
      }
    }
  else                      $txt[] = $prefix . (string)$item;
  return $txt;
  }

public function getDefOutput ($data) {
  $output = array ();
  if ($data['settings']['meta_keyword'])      $output['meta_keyword']     = $data['settings']['meta_keyword'];
  if ($data['settings']['meta_description'])  $output['meta_description'] = $data['settings']['meta_description'];
  if ($data['settings']['meta_title'])        $output['meta_title']       = $data['settings']['meta_title'];
  if ($data['settings']['meta_h1'])           $output['meta_h1']          = $data['settings']['meta_h1'];
  if ($data['settings']['seo_title'])         $output['seo_title']        = $data['settings']['seo_title'];
  if ($data['settings']['seo_h1'])            $output['seo_h1']           = $data['settings']['seo_h1'];
  if ($data['settings']['tag'])               $output['tag']              = $data['settings']['tag'];
  if ($data['settings']['url_tpl'])           $output['url_tpl']          = $data['settings']['url_tpl'];
  $output['description']      = '';
  $output['announcement']     = '';
  $output['sku']              = '';
  $output['model']            = ''; 
  $output['minimum']          = $data['settings']['minimum'];  
  $output['subtract']         = $data['settings']['subtract'];  
  $output['price']            = ''; 
  $output['quantity']         = $data['settings']['quantity']; 
  $output['name']             = ''; 
  $output['stock_status_id']  = $data['settings']['stock_status_id']; 
  $output['vendor']           = '';   
  $output['location']         = $data['settings']['supplier']=='location'?$data['session_key']:''; 
  $output['supplier']         = $data['settings']['supplier']=='supplier'?$data['session_key']:'';    // если это поле есть в базе!
  $output['mpn']              = $data['settings']['supplier']=='mpn'?$data['session_key']:''; 

  $output['upc']              = '';   
  $output['ean']              = '';   
  $output['jan']              = '';   
  $output['isbn']             = '';   

  $output['cat_id']           = '';   
  $output['cat_name']         = '';   
  $output['par_cat_name']     = '';   
  $output['attr']             = array();  
  $output['option']           = array(); 
  $output['image']            = array(); 
  $output['category_ids']     = array(); 
  
  // Вес и габариты
  $output['weight_class_id']  = (int)(empty($data['settings']['weight_class_id'])?$this->config->get( 'config_weight_class' ):$data['settings']['weight_class_id']);  
  $output['weight']           = 0; 
  $output['weight_class_id_koeff'] = 1; 

  $output['length_class_id']  = (int)(empty($data['settings']['length_class_id'])?$this->config->get( 'config_length_class' ):$data['settings']['length_class_id']);  
  $output['length']           = 0;  
  $output['width']            = 0;  
  $output['height']           = 0;  
  // ПРОЧЕЕ
  $output['shipping']         = 1;  
  $output['points']           = 0;  
  $output['tax_class_id']     = (int)(empty($data['settings']['tax_class_id'])?0:$data['settings']['tax_class_id']);  ;  
  
  
  return $output;
  }


public function loadImage ($input_img, $data, $category_id) {
  $data['dest_image'] = '';
  $image = '';
  $img   = trim(html_entity_decode ($input_img));
  if (empty($img)) return $data;
  $img   = str_replace(' ','%20',$img);
  if (!empty($data['module']['sleep_for_image_loader'])) usleep ($data['module']['sleep_for_image_loader']);
  $ext   = 'jpg';

  // ОСНОВНАЯ ПАПКА ДЛЯ СОХРАНЕНИЕЯ
  $folder = $data['VERSION']==1?"data/":"catalog/";
  if (!empty($data['settings']['image_save_to'])) {
    $folder .= $data['settings']['image_save_to'];
    if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
    $folder .= '/';
    }             

  // ПОДПАПКА
  $sub_folder = "md5";
  if ($data['settings']['image_save_as']=="url") {
    if (!isset($data['url_alias'][(int)$category_id])) {
      $url_query = NULL;
      if (isset($data['module']['url_alias'])) $url_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id. "'");
      if (isset($data['module']['seo_url']))   $url_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url   WHERE query = 'category_id=" . (int)$category_id. "'");
      if (!empty($url_query->row['keyword']))  $data['url_alias'][(int)$category_id] = $url_query->row['keyword'];
      else                                     $data['url_alias'][(int)$category_id] = (string)$category_id;
      }
    $sub_folder     = $data['url_alias'][(int)$category_id];   
    }


  $length = 0;  
  $src    = '';   
//  $data['settings']['zo_image_loader'] = 'ydisk';
  switch ($data['settings']['zo_image_loader']) {
    case 'ydisk': {
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, "https://cloud-api.yandex.net:443/v1/disk/public/resources/download?public_key=" . urlencode($img) );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      $response = curl_exec( $ch );
      $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
      curl_close ($ch);
      if ($code!=200) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("yd_getinfo: " . json_encode($response)) . "', user = '" . $this->db->escape($data['user']) . "'");
        break;
        }
      $response = json_decode( $response, true );
      $img = $response["href"];
      }
    case 'curl': {
       $ch = curl_init(); 
       curl_setopt($ch, CURLOPT_URL, $img);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      if (!empty($data['settings']['curl_options'])) {
        $curl_options = explode (';', trim($data['settings']['curl_options']));
        foreach ($curl_options as $curl_option) {
          $curl_option_values = explode ('|', trim($curl_option));
          if (isset($curl_option_values[0])&&isset($curl_option_values[1])&&isset($curl_option_values[2])) {
            switch (trim($curl_option_values[1])) {
              case "int": { curl_setopt($ch, constant(trim($curl_option_values[0])), (int)$curl_option_values[2]); break; }
              case "string": { curl_setopt($ch, constant(trim($curl_option_values[0])), (string)$curl_option_values[2]); break; }
              }
            }
          }
        }
    	 $src      = curl_exec($ch);
       $result   = curl_getinfo($ch);
       if (!$src || isset($data['module']['HARD_DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("curl_getinfo: " . json_encode($result)) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
       if (is_array($result)) {
        if (isset($result['content_type'])) {
          if (strpos ($result['content_type'],'image/png')!==FALSE) $ext = 'png';
          elseif (strpos ($result['content_type'],'image/jpg')!==FALSE) $ext = 'jpg';
          elseif (strpos ($result['content_type'],'image/jpeg')!==FALSE) $ext = 'jpg';
          elseif (strpos ($result['content_type'],'image/gif')!==FALSE) $ext = 'gif';
          else  {
            $txt = 'Файл: ' . $img . "   Недопустимый тип: " . json_encode($result['content_type']);
            if (isset($data['module']['HARD_DEBUG'])) {
             $txt .= "   Содержимое: " . $src;
             }
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
            $src = NULL; // Блокируем инык типы
            }
          }
        if (isset($result['download_content_length'])) {
          $length = trim($result['download_content_length']);
          }
        }
       curl_close ($ch);
       break;
      }              
    default: {
      $arrContextOptions=array(
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ),
      );
      $src = file_get_contents ($img, false, stream_context_create($arrContextOptions));  
      if (isset($http_response_header)) {
        if (!$src || isset($data['module']['HARD_DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("http_response_header: " . json_encode($http_response_header)) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        foreach ($http_response_header as $value) {
          if (strpos ($value,'X-Content-Type-Options')!==FALSE) continue;
          if (strpos ($value,'Content-Type')!==FALSE) {
            if (strpos ($value,'image/png')!==FALSE) $ext = 'png';
            elseif (strpos ($value,'image/gif')!==FALSE) $ext = 'gif';
            elseif (strpos ($value,'image/jpg')!==FALSE) $ext = 'jpg';
            elseif (strpos ($value,'image/jpeg')!==FALSE) $ext = 'jpg';
            else  {
              $txt = 'Файл: ' . $img . "   Недопустимый тип: " . json_encode($http_response_header);
              if (isset($data['module']['HARD_DEBUG'])) {
               $txt .= "   Содержимое: " . $src;
               }
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
              $src = NULL; // Блокируем инык типы
              }
            }
          if (strpos ($value,'Content-Length')!==FALSE) {
            $tmp = explode( ':', $value );
            if (isset($tmp[1])) $length = trim($tmp[1]);
            }
          }
        }
      }
    }

    
    $dest = 'IMG' . md5 ($img) . "." . $ext;
    if ($src) {
      // ПОДПАПКА
      if ($sub_folder=="md5") $sub_folder = substr ($dest , 3, 3);
      $folder .= $sub_folder;
      if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
      $folder .= '/';

      if (isset($data['settings']['max_image_size'])) {
      	$info   = getimagesizefromstring ($src); // (PHP 5 >= 5.4.0, PHP 7)!
      	$width  = $info[0];
      	$height = $info[1];
        $parts = explode (':', trim($data['settings']['max_image_size']));
        if (!isset($parts[1])) { // СТАРЫЙ ВАРИАНТ - ОГРАНИЧЕНИЕ ПО РАЗМЕРУ ФАЙЛА
          if ($length > $data['settings']['max_image_size']) {
            $img_data = imagecreatefromstring ($src);
            if ($img_data !== false) {
              $scale = sqrt($data['settings']['max_image_size'] / $length); 
              $img_data2 =  imagescale ($img_data, (int)($width * $scale), (int)($height * $scale)); // (PHP 5 >= 5.5.0, PHP 7)!
              imagedestroy($img_data);
              $quality = 90;
              $result = FALSE;
        			if ($ext == 'jpg') $result = imagejpeg($img_data2, DIR_IMAGE . $folder . $dest, $quality);
    //    			if ($ext == 'png') $result = imagepng ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
        			if ($ext == 'png') $result = imagejpeg ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
        			if ($ext == 'gif') $result = imagegif ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
              imagedestroy($img_data2);
              if (!$result) {
                $data['dest_image'] = '';
                }
              else          {
                $data['dest_image'] = $folder . $dest; 
                if (isset($data['module']['HARD_DEBUG'])) {
                  $size = filesize (DIR_IMAGE . $folder . $dest);
                  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("Размер исходного файла: " . $length . " Размер сохраненного файла: " . $size) . "', user = '" . $this->db->escape($data['user']) . "'");
                  }
                }
              } 
            $src = NULL;
            }
          }
        else { // НОВЫЙ ВАРИАНТ - ОГРАНИЧЕНИЯ ПО ГАБАРИТАМ
          if ($width>$parts[0] || $height>$parts[1]) {
            $img_data = imagecreatefromstring ($src);
            if ($img_data !== false) {
              $scaleW = 1;
              $scaleH = 1;
              if ($width>$parts[0])  $scaleW = $parts[0] / $width;
              if ($height>$parts[1]) $scaleH = $parts[1] / $height;
              $scale = min ($scaleW,$scaleH);
              $newW = (int)($width * $scale);
              $newH = (int)($height * $scale);
if (isset($data['module']['HARD_DEBUG'])) {
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("СТАРЫЕ ГАБАРИТЫ: " . $width . " x " . $height . "<br />НОВЫЕ ГАБАРИТЫ: " . $newW . " x " . $newH) . "', user = '" . $this->db->escape($data['user']) . "'");
  }
              $img_data2 =  imagescale ($img_data, $newW, $newH); // (PHP 5 >= 5.5.0, PHP 7)!
              imagedestroy($img_data);
              $quality = 90;
              $result = FALSE;
        			if ($ext == 'jpg') $result = imagejpeg($img_data2, DIR_IMAGE . $folder . $dest, $quality);
    //    			if ($ext == 'png') $result = imagepng ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
        			if ($ext == 'png') $result = imagejpeg ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
        			if ($ext == 'gif') $result = imagegif ($img_data2, DIR_IMAGE . $folder . $dest, $quality);
              imagedestroy($img_data2);
              if (!$result) {
                $data['dest_image'] = '';
                $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'error', data = '" . $this->db->escape("Ошибка сохранения файла: " . DIR_IMAGE . $folder . $dest) . "', user = '" . $this->db->escape($data['user']) . "'");
                }
              else          {
                $data['dest_image'] = $folder . $dest; 
                if (isset($data['module']['HARD_DEBUG'])) {
                  $size = filesize (DIR_IMAGE . $folder . $dest);
                  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("Размер исходного файла: " . $length . " Размер сохраненного файла: " . $size) . "', user = '" . $this->db->escape($data['user']) . "'");
                  }
                }
              } 
            $src = NULL;
            }
          }
        }
      if ($src) {
        if (file_put_contents (DIR_IMAGE . $folder . $dest, $src )===FALSE) $data['dest_image'] = '';
        else                                                                 $data['dest_image'] = $folder . $dest; 
        }
      } 
  return $data;
}
public function loadOptionImage ($input_img, $data, $option_id) {
  if (!empty($data['module']['sleep_for_image_loader'])) usleep ($data['module']['sleep_for_image_loader']);
  $dest_image = '';
  $image = '';
  $img   = html_entity_decode ($input_img);
  if (!$img) return $dest_image;
  $ext   = 'jpg';

  // ОСНОВНАЯ ПАПКА ДЛЯ СОХРАНЕНИЕЯ
  $folder = $data['VERSION']==1?"data/":"catalog/";
  if (!empty($data['settings']['image_save_to'])) {
    $folder .= $data['settings']['image_save_to'];
    if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
    $folder .= '/';
    }             

  // ПОДПАПКА
  $sub_folder = "md5";
  if ($data['settings']['option_image_save_as']=="old") {
    $sub_folder     = 'option-' . $option_id;   
    }
  $src      = '';
 switch ($data['settings']['zo_image_loader']) {
    case 'ydisk': {
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, "https://cloud-api.yandex.net:443/v1/disk/public/resources/download?public_key=" . urlencode($img) );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      $response = curl_exec( $ch );
      $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
      curl_close ($ch);
      if ($code!=200) break;
      $response = json_decode( $response, true );
      $img = $response["href"];
      }
    case 'curl': {
       $ch = curl_init(); 
       curl_setopt($ch, CURLOPT_URL, $img);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      if (!empty($data['settings']['curl_options'])) {
        $curl_options = explode (';', trim($data['settings']['curl_options']));
        foreach ($curl_options as $curl_option) {
          $curl_option_values = explode ('|', trim($curl_option));
          if (isset($curl_option_values[0])&&isset($curl_option_values[1])&&isset($curl_option_values[2])) {
            switch (trim($curl_option_values[1])) {
              case "int": { curl_setopt($ch, constant(trim($curl_option_values[0])), (int)$curl_option_values[2]); break; }
              case "string": { curl_setopt($ch, constant(trim($curl_option_values[0])), (string)$curl_option_values[2]); break; }
              }
            }
          }
        }
    	 $src      = curl_exec($ch);
       $result   = curl_getinfo($ch);
       if (!$src || isset($data['module']['HARD_DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("curl_getinfo: " . json_encode($result)) . "', user = '" . $this->db->escape($data['user']) . "'");
        }
       if (is_array($result)) {
        if (isset($result['content_type'])) {
if (isset($data['module']['DEBUG'])) {
$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("content_type: " . $result['content_type']) . "', user = '" . $this->db->escape($data['user']) . "'");
}
          if (strpos ($result['content_type'],'image/png')!==FALSE) $ext = 'png';
          if (strpos ($result['content_type'],'image/gif')!==FALSE) $ext = 'gif';
          }
        if (isset($result['download_content_length'])) {
          $length = trim($result['download_content_length']);
          }
        }
       curl_close ($ch);
       break;
      }              
    default: {
      $arrContextOptions=array(
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ),
      );
      $src = file_get_contents ($img, false, stream_context_create($arrContextOptions));  
      if (isset($http_response_header)) {
        if (!$src || isset($data['module']['HARD_DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape("http_response_header: " . json_encode($http_response_header)) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        foreach ($http_response_header as $value) {
          if (strpos ($value,'image/png')!==FALSE) $ext = 'png';
          if (strpos ($value,'image/gif')!==FALSE) $ext = 'gif';
          if (strpos ($value,'Content-Length')!==FALSE) {
            $tmp = explode( ':', $value );
            if (isset($tmp[1])) $length = trim($tmp[1]);
            }
          }
        }
      }
    }


    if ($src) {
      $dest = 'IMG' . md5 ($img) . "." . $ext;
      // ПОДПАПКА
      if ($sub_folder=="md5") $sub_folder = substr ($dest , 3, 3);
      $folder .= $sub_folder;
      if (!file_exists(DIR_IMAGE . $folder)) @mkdir(DIR_IMAGE . $folder, 0777);
      $folder .= '/';

      if (file_put_contents (DIR_IMAGE . $folder .  $dest, $src )===FALSE) $dest_image = '';
      else                                                                 $dest_image = $folder . $dest; 
      } 
  return $dest_image;
}
public function saveOptions ($options,$product_id, $data, $relatedoptions_id=0, $summ_quantity=FALSE) {
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
    // product_option value
    $option_ids = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE name = '" . $this->db->escape($option['value']) . "' AND option_id = '" . (int)$option_id . "'");
    if ($option_ids->row) $option_value_id = $option_ids->row['option_value_id'];
    else {
      $img = '';
      if (!empty($option['image'])) $img = $this->loadOptionImage ($option['image'], $data, $option_id); 
      $sort_order = isset($option['sort_order'])?$option['sort_order']:0;
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$option_id . "', sort_order = '" . (int)$sort_order . "', image = '" . $this->db->escape($img) . "'");
			$option_value_id = $this->db->getLastId();
      foreach ($data['languages'] as $language_id => $language_name) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', name = '" . $this->db->escape($option['value']) . "', option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "'");
        }
      }
  // -------
    $poip_image = '';
    $extra = '';
    $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND option_value_id = '" . (int)$option_value_id . "'");        
    if ($query->row) { // Делаем обновление сущестуюшей опции
      $product_option_value_id = $query->row['product_option_value_id'];
      if (isset($option['quantity']) && isset($data['settings']['update_quantity'])) {
        if (!$summ_quantity) $extra .= "quantity   = '" . (int)$option['quantity']   . "', "; 
        else                 $extra .= "quantity = (quantity + '" . (int)$option['quantity']   . "'), ";
        }
      if (isset($option['price'])    && isset($data['settings']['update_price']))    $extra .= "price      = '" . $this->db->escape((float)$option['price']) . "', price_prefix   = '" . $this->db->escape($option['price_prefix'])   . "', "; 
      if (isset($option['optsku'])  ) $extra .= "optsku   = '" . $this->db->escape($option['optsku'])   . "', "; 
      if (isset($option['description']))    $extra .= "description = '" . $this->db->escape($option['description']) . "', "; 
      if (isset($data['settings']['update_weight'])) {
        if (isset($option['weight'])  ) $extra .= "weight   = '" . $this->db->escape($option['weight'])   . "', "; 
        if (isset($option['weight_prefix'])  ) $extra .= "weight_prefix   = '" . $this->db->escape($option['weight_prefix'])   . "', "; 
        }
      $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET " . $extra . "  product_id = '" . (int)$product_id . "' WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");        
     // -------  POIP
      if (isset($data['module']['poip'])) {
        if (isset($data['settings']['update_image'])) { // ЖЕСТКИЙ РЕЖИМ - УДАЛЯЕМ ВСЕ КАРТИНКИ И ЗАГРУЖАЕМ ЗАНОВО
          $this->db->query("DELETE FROM " . DB_PREFIX . "poip_option_image WHERE product_option_value_id = '" . (int)$product_option_value_id . "'"); 
          }
        $poip_image_query = $this->db->query("SELECT *   FROM " . DB_PREFIX . "poip_option_image WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");        
        if (!$poip_image_query->row) {  // МЯГКОЕ ОБНОВЛЕНИЕ - ТОЛЬКО ЕСЛИ НЕТ КАРТИНКИ!
          if (!empty($option['poip'])) {
            foreach ($option['poip'] as $sort_order => $file) {
              $img = $this->loadOptionImage ($file, $data, $option_id);
              $this->db->query("INSERT INTO " . DB_PREFIX . "poip_option_image SET product_id = '" . (int)$product_id . "', product_option_id = '" . (int)$product_option_id . "', product_option_value_id = '" . (int)$product_option_value_id . "', 	image = '" . $this->db->escape($img) . "', sort_order = '" . (int)$sort_order . "'");        
              }
            }   
          }  
        }
      }
    else { // Такого значения опции нет - добавляем
      if ($option['quantity']<1&&isset($data['settings']['not_empty_only'])) continue;
      // Extra field
      if (isset($option['weight'])  ) $extra .= "weight   = '" . $this->db->escape($option['weight'])   . "', "; 
      if (isset($option['weight_prefix'])  ) $extra .= "weight_prefix   = '" . $this->db->escape($option['weight_prefix'])   . "', "; 
      if (isset($option['sku'])     ) $extra .= "sku      = '" . $this->db->escape($option['sku'])      . "', "; 
      if (isset($option['optsku'])  ) $extra .= "optsku   = '" . $this->db->escape($option['optsku'])   . "', "; 
      if (isset($option['upc'])     ) $extra .= "upc      = '" . $this->db->escape($option['upc'])      . "', "; 
      if (isset($option['jan'])     ) $extra .= "jan      = '" . $this->db->escape($option['jan'])      . "', "; 
      if (isset($option['code'])    ) $extra .= "code     = '" . $this->db->escape($option['code'])     . "', "; 
      if (isset($option['location'])) $extra .= "location = '" . $this->db->escape($option['location']) . "', "; 
  //  improvedoptions support
      if (isset($option['default_select'])) $extra .= "default_select = '" . (int)$option['default_select'] . "', "; 
      if (isset($option['description']))    $extra .= "description = '" . $this->db->escape($option['description']) . "', "; 
      if (isset($option['model']))          $extra .= "model = '" .       $this->db->escape($option['model']) . "', "; 

      $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET " . $extra . "  product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', quantity = '" . $this->db->escape($option['quantity']) . "', subtract = '" . $this->db->escape($option['subtract']) . "', price = '" . (float)$option['price'] . "', price_prefix = '" . $this->db->escape($option['price_prefix']) . "'");        
      $product_option_value_id = $this->db->getLastId();
     // -------  POIP
      if (isset($data['module']['poip']) && !empty($option['poip'])) {
        foreach ($option['poip'] as $sort_order => $file) {
          $img = $this->loadOptionImage ($file, $data, $option_id);
          $this->db->query("INSERT INTO " . DB_PREFIX . "poip_option_image SET product_id = '" . (int)$product_id . "', product_option_id = '" . (int)$product_option_id . "', product_option_value_id = '" . (int)$product_option_value_id . "', 	image = '" . $this->db->escape($img) . "', sort_order = '" . (int)$sort_order . "'");        
          }
        }
      }
 // -------
    if ($relatedoptions_id>0) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "relatedoptions_option SET   
        relatedoptions_id   = '" . (int)$relatedoptions_id . "', 
        product_id          = '" . (int)$product_id . "', 
        option_id           = '" . (int)$option_id . "', 
        option_value_id     = '" . (int)$option_value_id . "'");        
      }
    }
  if ($relatedoptions_id==0) {
    $query  = $this->db->query("SELECT SUM(`quantity`) AS total, SUM(`product_id`) AS total2  FROM " . DB_PREFIX . "product_option_value WHERE subtract!='0' AND product_id = '" . (int)$product_id . "'"); 
    if ($query->row['total2']>0) {
      $status = 1;
      if ($query->row['total']<1&&$data['settings']['hide']) $status = 0;
      $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), status = '" . (int)$status . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
      }
    else {
      // ВКЛЮЧИТЬ ТОВАРЫ БЕЗ ОПЦИЙ ПРИ ПОЛОЖИТЕЛЬНОМ ОСТАТКЕ
      if ($data['settings']['hide']) $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), status = '1' WHERE quantity > '0' AND product_id = '" . (int)$product_id . "'");
      }
    }
  else {
    $query = $this->db->query("SELECT SUM(`quantity`) AS total FROM " . DB_PREFIX . "relatedoptions WHERE product_id = '" . (int)$product_id . "'"); 
    $status = 1;
    if ($query->row['total']<1&&$data['settings']['hide']) $status = 0;
    $this->db->query ("UPDATE " . DB_PREFIX . "product SET date_modified = NOW(), status = '" . (int)$status . "', quantity = '" . (int)$query->row['total'] . "' WHERE product_id = '" . (int)$product_id . "'");
    }
  }
public function getDefSettings ($session_key,$user) {
  ignore_user_abort(true);
  ini_set('max_execution_time', -1);
  ini_set('memory_limit', -1);
  $this->db->query("SET session wait_timeout=28800");
  
  $data = array();
  $data['user']          = $user;
  $data['session_key']   = $session_key;
  $data['module'] = array ();
  $data['replace'] = array();
  $data['VERSION']          = 0;
  if (defined('VERSION')) {
    $parts             = explode(".",VERSION);
    $data['VERSION']   = isset($parts[0])?$parts[0]:0;
    }
  $data['dest_image'] = '';
  
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_suppliers WHERE session_key = '" . $this->db->escape($session_key) . "'");
  if ($query->row) {
    $value = json_decode ($query->row['data']);
    $data['settings'] = array(
    			'module'       => $value->module,
    			'name'         => $value->name,
    			'url'          => isset($value->url)?$value->url:"",
    			'license'      => isset($value->license)?$value->license:"",
          'supplier'     => isset($value->supplier)?$value->supplier:'supplier',
          'before'       => $value->before,
          'images'       => $value->images,
          'link'         => $value->link,
          'add_before'   => $value->add_before,
          'mul_after'    => $value->mul_after,
          'add_after'    => $value->add_after,
          'before_mode'  => $value->before_mode,
          'stores'       => isset($value->stores)?$value->stores:0,
          'image_save_to'     => isset($value->image_save_to)?$value->image_save_to:$session_key,
          'image_save_as'     => isset($value->image_save_as)?$value->image_save_as:'url',
          'option_image_save_as'     => isset($value->option_image_save_as)?$value->option_image_save_as:'old',
          'curl_options'      => isset($value->curl_options)?$value->curl_options:'',
          'zo_image_loader'   => isset($value->zo_image_loader)?$value->zo_image_loader:'file_get_contents',
          'hide_by_attribute' => isset($value->hide_by_attribute)?$value->hide_by_attribute:'0',
          'getXML_method'     => isset($value->getXML_method)?$value->getXML_method:'simplexml_load_file',
          'meta_keyword'      => isset($value->meta_keyword)?$value->meta_keyword:'',
          'meta_description'  => isset($value->meta_description)?$value->meta_description:'',
          'meta_title'        => isset($value->meta_title)?$value->meta_title:'',
          'meta_h1'           => isset($value->meta_h1)?$value->meta_h1:'',
          'seo_title'         => isset($value->seo_title)?$value->seo_title:'',
          'seo_h1'            => isset($value->seo_h1)?$value->seo_h1:'',
          'tag'               => isset($value->tag)?$value->tag:'',
          'sku'               => isset($value->sku)?$value->sku:'',
          'name_tpl'          => isset($value->name_tpl)?$value->name_tpl:'',
          'model_tpl'         => isset($value->model_tpl)?$value->model_tpl:'',
          'url_tpl'           => isset($value->url_tpl)?$value->url_tpl:'',
          'tag_shop'          => isset($value->tag_shop)?$value->tag_shop:'shop',
          'tag_offers'        => isset($value->tag_offers)?$value->tag_offers:'offers',
          'tag_offer'         => isset($value->tag_offer)?$value->tag_offer:'offer',
          'tag_categories'    => isset($value->tag_categories)?$value->tag_categories:'categories',
          'tag_category'      => isset($value->tag_category)?$value->tag_category:'category',
          'round_mode'        => isset($value->round_mode)?$value->round_mode:4,
          'price_table'       => isset($value->price_table)?$value->price_table:'',
          'dov_required'      => isset($value->dov_required)?$value->dov_required:0,
          'dov_price_prefix'  => isset($value->dov_price_prefix)?$value->dov_price_prefix:'+',
          'dov_subtract'      => isset($value->dov_subtract)?$value->dov_subtract:0,
          'dov_quantity'      => isset($value->dov_quantity)?$value->dov_quantity:1,
          'dov_price'         => isset($value->dov_price)?$value->dov_price:0,
          'quantity'          => isset($value->quantity)?$value->quantity:1,
          'minimum'           => isset($value->minimum)?$value->minimum:1,
          'subtract'          => isset($value->subtract)?$value->subtract:1,
          'stock_status_id'   => isset($value->stock_status_id)?$value->stock_status_id:5,
          'tax_class_id'      => isset($value->tax_class_id)?$value->tax_class_id:'',
          'length_class_id'   => isset($value->length_class_id)?$value->length_class_id:'',
          'weight_class_id'   => isset($value->weight_class_id)?$value->weight_class_id:'',
          'weight_to_attr'    => isset($value->weight_to_attr)?$value->weight_to_attr:0,   
          'length_to_attr'    => isset($value->length_to_attr)?$value->length_to_attr:0,   
          'width_to_attr'     => isset($value->width_to_attr)?$value->width_to_attr:0,   
          'height_to_attr'    => isset($value->height_to_attr)?$value->height_to_attr:0,   
          'l_w_h_to_attr'     => isset($value->l_w_h_to_attr)?$value->l_w_h_to_attr:0,   
          'l_w_h_template'    => isset($value->l_w_h_template)?$value->l_w_h_template:'{length}/{width}/{height}',   
          'stock_id'          => isset($value->stock_id)?$value->stock_id:0,
          'img_path'          => isset($value->img_path)?$value->img_path:'',
          );

    if (isset($value->link2category_ids))      $data['settings']['link2category_ids'] = 1;
    if (isset($value->ms_seller))              $data['settings']['ms_seller'] = $value->ms_seller;
    if (isset($value->hide_missing))           $data['settings']['hide_missing'] = 1;
    if (isset($value->zero_missing))           $data['settings']['zero_missing'] = 1;
    if (isset($value->xml2cache))              $data['settings']['xml2cache'] = 1;
    if (isset($value->price_table4mc))         $data['settings']['price_table4mc'] = 1;
    if (!empty($value->max_image_size))        $data['settings']['max_image_size']    = $value->max_image_size;
    if (isset($value->user_filter))            $data['settings']['user_filter']       = $value->user_filter;
    if (isset($value->user_scan))              $data['settings']['user_scan']         = $value->user_scan;
    if (isset($value->user_xml_pre))           $data['settings']['user_xml_pre']      = $value->user_xml_pre;
    if (isset($value->user_start))             $data['settings']['user_start']        = $value->user_start;
    if (isset($value->user_pre))               $data['settings']['user_pre']          = $value->user_pre;
    if (isset($value->user_ro))                $data['settings']['user_ro']           = $value->user_ro;
    if (isset($value->user_after))             $data['settings']['user_after']        = $value->user_after;
    if (isset($value->insert_analyzer))        $data['settings']['insert_analyzer']   = $value->insert_analyzer;
    if (isset($value->update_analyzer))        $data['settings']['update_analyzer']   = $value->update_analyzer;
    if (isset($value->update_use_script))      $data['settings']['update_use_script'] = $value->update_use_script;
    if (isset($value->hide))                    $data['settings']['hide'] = 1;
    else                                        $data['settings']['hide'] = 0;
    if (isset($value->hide_new))                $data['settings']['hide_new'] = 1;
    if (isset($value->noindex_new))            $data['settings']['noindex_new'] = 1;
    if (isset($value->log_new))                 $data['settings']['log_new'] = 1;
    if (isset($value->insert))                  $data['settings']['insert'] = 1;
    if (isset($value->update))                  $data['settings']['update'] = 1;
    if (isset($value->update_price))           $data['settings']['update_price'] = 1;
    if (isset($value->update_special))         $data['settings']['update_special'] = 1;
    if (isset($value->update_image))           $data['settings']['update_image'] = 1;
    if (isset($value->update_quantity))        $data['settings']['update_quantity'] = 1;
    if (isset($value->update_name))            $data['settings']['update_name'] = 1;
    if (isset($value->update_description))     $data['settings']['update_description'] = 1;
    if (isset($value->update_category))        $data['settings']['update_category'] = 1;
    if (isset($value->update_atributes))       $data['settings']['update_atributes'] = 1;
    if (isset($value->update_vendor))          $data['settings']['update_vendor'] = 1;
    if (isset($value->update_sku))             $data['settings']['update_sku'] = 1;
    if (isset($value->update_ean))            $data['settings']['update_ean'] = 1;
    if (isset($value->update_minimum))        $data['settings']['update_minimum'] = 1;
    if (isset($value->update_upc))            $data['settings']['update_upc'] = 1;
    if (isset($value->update_jan))            $data['settings']['update_jan'] = 1;
    if (isset($value->update_isbn))           $data['settings']['update_isbn'] = 1;
    if (isset($value->update_mpn))            $data['settings']['update_mpn'] = 1;
    if (isset($value->update_model))           $data['settings']['update_model'] = 1;
    if (isset($value->update_weight))          $data['settings']['update_weight'] = 1;
    if (isset($value->update_l_w_h))           $data['settings']['update_l_w_h'] = 1;
    if (isset($value->update_stock_status_id)) $data['settings']['update_stock_status_id'] = 1;
    if (isset($value->no_update))             $data['settings']['no_update'] = 1;
    if (isset($value->not_empty_only))        $data['settings']['not_empty_only'] = 1;
    if (isset($value->after))                 $data['settings']['after']  = 1;
    if (isset($value->link_vendor))           $data['settings']['link_vendor']  = 1;
    if (isset($value->link_supplier))         $data['settings']['link_supplier']  = 1;
    if (isset($value->language))               $data['settings']['language'] = $value->language;
    else                                       $data['settings']['language'] = $this->config->get( 'config_language_id' );
    if (isset($value->all_languages))          $data['settings']['all_languages'] = $value->all_languages;

    if (isset($value->default_atribute_group))  $data['settings']['default_atribute_group']  = $value->default_atribute_group;
    else                                        $data['settings']['default_atribute_group']  = 0;
    if (isset($value->auto_atributes_db))       $data['settings']['auto_atributes_db']  = $value->auto_atributes_db;
    else                                        $data['settings']['auto_atributes_db']  = "common";
    $data['settings']['default_atribute_group_name'] = $data['settings']['auto_atributes_db'];

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE language_id = '" . (int)$data['settings']['language'] . "' AND attribute_group_id = '" . (int)$data['settings']['default_atribute_group'] . "'");
    if ($query->row) $data['settings']['default_atribute_group_name'] = $query->row['name'];


    if (isset($value->auto_atributes))          {
      // АКТИВИРУЕМ РАБОТУ С АТРИБУТАМИ
      $data['zoAttributes'] = new zoAttributes ($data['settings']['auto_atributes_db'], $data['settings']['language'], $this->db);
      $data['settings']['auto_atributes'] = TRUE;
      }

    // МУЛЬТИМАГАЗИН
    $data['all_stores'] = array();
    $data['all_stores'][0] = 0;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
    foreach ($query->rows as $result) {
      $data['all_stores'][$result['store_id']] = $result['store_id'];
      }
    
    if (!empty($value->user_exception)&&!empty($value->user_exceptions)) {
      $user_exceptions_delimiter = ',';
      if (defined('zo_user_exceptions_delimiter')) $user_exceptions_delimiter = zo_user_exceptions_delimiter;
      if ($value->user_exception=="WhiteListOfSku") {
        $data['WhiteListOfSku'] = array();
        $values = explode ($user_exceptions_delimiter,(string)$value->user_exceptions);
        foreach ($values as $sku) $data['WhiteListOfSku'][trim($sku)] = true;
        }
      if ($value->user_exception=="BlackListOfSku") {
        $data['BlackListOfSku'] = array();
        $values = explode ($user_exceptions_delimiter,(string)$value->user_exceptions);
        foreach ($values as $sku) $data['BlackListOfSku'][trim($sku)] = true;
        }
      } 
    }

$data['languages']     = array();
$languages = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
foreach ($languages->rows as $result) $data['languages'][$result['language_id']] = $result['name'];
  // customer_group_id
  $data['customer_groups'] = array ();
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group ORDER by customer_group_id ASC");
  foreach ($query->rows as $result) {
    $data['customer_groups'][] = $result['customer_group_id'];
    }
  
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
  foreach ($query->rows as $result) $data['module'][$result['key']] = json_decode ($result['data']);
  if (!isset($data['module']['step']))  $data['module']['step']  = 10;
  if (!isset($data['module']['sleep'])) $data['module']['sleep'] = 100;
// Auto-detect
  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "url_alias'");
  if ($query->row) $data['module']['url_alias'] = TRUE;
  else       unset($data['module']['url_alias']);
  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "seo_url'");
  if ($query->row) $data['module']['seo_url'] = TRUE;
  else       unset($data['module']['seo_url']);
  $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='supplier' ");
  if ($query->num_rows) $data['module']['is_supplier'] = TRUE;
  $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='updated_by' ");
  if ($query->num_rows) $data['module']['is_updated_by'] = TRUE;
  else       unset($data['module']['is_supplier']);
  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_filter'");
  if ($query->row) $data['module']['product_filter'] = TRUE;
  else       unset($data['module']['product_filter']);
  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "manufacturer_description'");
  if ($query->row) $data['module']['manufacturer_description'] = TRUE;
  else       unset($data['module']['manufacturer_description']);
  $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_to_category` WHERE field='main_category'");
  if ($query->row) $data['module']['engine']   = 'ocStore'; 
  else             $data['module']['engine']   = 'Opencart';  
  $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "zoxml2_permisions'");
  if ($query->row) $data['module']['zoxml2_permisions'] = TRUE;
  
  if (isset($data['module']['mf_plus']) && $this->config->get( 'mfilter_plus_version' )) require_once DIR_SYSTEM . 'library/mfilter_plus.php';

  $query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product");
  $is_supplier = false;
  foreach ($query->rows as $result) if ($result['Field']=='supplier') $is_supplier = true; 
  if ($is_supplier) $data['module']['is_supplier'] = $is_supplier;
  
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE session_key = '" . $this->db->escape($session_key) . "'");
  foreach ($query->rows as $result) {
    $data['options'][$result['data']] = array(
    			'dest_type'        => $result['dest_type'],
    			'dest_id'          => $result['dest_id'],
          );
    }
  
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_replace WHERE session_key = '" . $this->db->escape($session_key) . "' ORDER by sort_order ASC");
  foreach ($query->rows as $result) {
    if ($result['type']=="xmlpre") $data['settings']['xmlpre_replaces'] = TRUE;
    $data['replace'][] = array(
    			'type'             => $result['type'],
          'mode'             => $result['mode']?$result['mode']:'replace',
    			'txt_before'       => $result['txt_before'],
    			'txt_after'        => $result['txt_after'],
          );
    }
  $data['url_alias'] = array();

  return $data;
  }

public function processOutput ($data,$output) {
  $session_key = $data['session_key'];
  $status = 1;
  if ($output['quantity']<1&&$data['settings']['hide']) $status = 0;
  $sql = $this->doSQL($output,$data,$output['manufacturer_id']);
  $query = $this->db->query($sql);
  // ---------------------------------------------------------------------
  if ($query->row) { // Обновление продукта
    $product_id = $query->row['product_id'];
    $status     = $query->row['status'];
    if (count($query->rows)>1) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape("sql запрос: " . $sql . " вернул более 1-й строки!") . "', user = '" . $this->db->escape($data['user']) . "'");
      }
    if (!isset($data['settings']['update'])) return $data; //continue;
    if (isset($data['settings']['no_update'])&&$status==0) return $data; //continue;
    // Обновление разрешено!
    // ПОЛЬЗОВАТЕЛЬСКИЙ АНАЛИЗАТОР
    if (!empty($data['settings']['update_analyzer'])) {
      $output = $this->model_zoxml2_zoxml2updateanalyzer->doUpdateAnalyzer($output,$data,$item,$query->row);
      if (!is_array($output)) {
        $data['info_update'] += $output;
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. Пользовательский анализатор") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        //continue;
        return $data;
        } 
      }
    // КАТЕГОРИЯ
    if (isset($data['settings']['update_category'])) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
      $this->link2category_id ($data['module']['engine'],$product_id,$output['category_id'],isset($data['module']['do_category_up'])?true:false);
      }
    // ПРОДУКТ
    $sql = "UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW()";
    if ( isset($data['settings']['update_price']))    {
      $sql .= ", price = '" . (float)$output['price'] . "'";
      if (isset($data['module']['costprice']) && isset($output['iprice']) ) { // Закупочная цена - поддержка только для https://extensions.myopencart.com/costprice-%D0%B7%D0%B0%D0%BA%D1%83%D0%BF%D0%BE%D1%87%D0%BD%D0%B0%D1%8F-%D1%86%D0%B5%D0%BD%D0%B0-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2-%D0%B2-opencart
        $sql .= ", cost = '" . $this->db->escape((float)$output['iprice']) . "'";
        }
      }
    if (isset($data['settings']['update_quantity'])) $sql .= ", quantity = '" . (int)$output['quantity'] . "', minimum = '" . (int)$output['minimum'] . "'";     
    if (isset($data['settings']['update_vendor']))   $sql .= ", manufacturer_id = '" . (int)$manufacturer_id . "'";
    if (isset($data['settings']['update_sku']))      $sql .= ", sku = '" . $this->db->escape($output['sku']) . "'";
    if (isset($data['settings']['update_model']))    $sql .= ", model = '" . $this->db->escape($output['model']) . "'";
    if (isset($data['settings']['update_weight']))   $sql .= ", weight_class_id = '" . $this->db->escape($output['weight_class_id']) . "', weight = '" . $this->db->escape($output['weight']) . "'";
    if (isset($data['settings']['update_l_w_h']))    $sql .= ", length_class_id = '" . $this->db->escape($output['length_class_id']) . "', length = '" . $this->db->escape($output['length']) . "', width = '" . $this->db->escape($output['width']) . "', height = '" . $this->db->escape($output['height']) . "'";
    
    $sql .= " WHERE product_id = '" . (int)$product_id . "'";

if (isset($data['module']['HARD_DEBUG'])) {
$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'HARD_DEBUG', data = '" . $this->db->escape("SQL: " . $this->db->escape($sql)) . "', user = '" . $this->db->escape($data['user']) . "'");
}
    $this->db->query($sql);
    // Акции
    if (isset($data['settings']['update_special'])) {
	    $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
      if (isset($output['special'])) {
        foreach ($data['customer_groups'] as $customer_group_id) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
          }
        }
      }
    // Цены
    if (isset($data['settings']['update_price'])) {
       // Валютная цена
      if ( isset($data['module']['mcg2']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) {
        if ($output['mc_price_cur']!=$this->config->get('config_currency')) {
	        $mc_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['mc_price_cur']) . "'");
          if ($mc_query->row) {
            $currency_id   = $mc_query->row['currency_id'];
         		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr  WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr  SET   product_id = '" . (int)$product_id . "', price = '" .    (float)$output['mc_price'] . "', currency_id = '" . (int)$currency_id . "'");
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
    // МЕТА
    if (isset($data['settings']['update_name']) && $data['settings']['language']!=0) {
      $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($output['name']) ."' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$data['settings']['language'] . "'");
      }
    if (isset($data['settings']['update_description']) && $data['settings']['language']!=0) {
      $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($output['description']) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$data['settings']['language'] . "'");
      }
    if ($data['settings']['images']!='nop' && isset($data['settings']['update_image']) && isset($output['image'][0])) {
      $data = $this->loadImage ($output['image'][0], $data, $category_id);
      $this->db->query("UPDATE " . DB_PREFIX . "product SET image  = '" . $this->db->escape($data['dest_image']) . "' WHERE product_id = '" . (int)$product_id . "'");
      if ($data['settings']['images']=='all') {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
        if (isset($output['image'][1])) {
          $order = 0;
          foreach ($output['image'] as $next_img) {
            if (!$order++) continue;
            $data = $this->loadImage ($next_img, $data, $category_id);
            if ($data['dest_image']) $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($data['dest_image']) . "', sort_order = '" . (int)$order . "'");
            }
          }
        }
      }
    if (isset($data['settings']['update_atributes']) && $data['settings']['language']!=0) {
      // ВАЖНО - удаляем только атрибуты указанного языка
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE language_id = '" . (int)$data['settings']['language'] . "' AND product_id = '" . (int)$product_id . "'");
      foreach ($output['attr'] as $attribute_id => $text) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$data['settings']['language'] . "', text = '" .  $this->db->escape($text) . "'");
        }
      }
    $data['info_update'] ++;
    }
  else {             // Добавление продукта
    if (!isset($data['settings']['insert'])) return $data; //continue;
    if (isset($data['settings']['not_empty_only'])&&$output['quantity']<=0) return $data; //continue;
    // Добавление разрешено!
    // ПОЛЬЗОВАТЕЛЬСКИЙ АНАЛИЗАТОР
    if (!empty($data['settings']['insert_analyzer'])) {
      $output = $this->model_zoxml2_zoxml2insertanalyzer->doInsertAnalyzer($output,$data,$item);
      if (!is_array($output)) {
        $data['info_insert'] += $output;
        if (isset($data['module']['DEBUG'])) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape("sku: " . $output['sku'] . ". Пропуск. Пользовательский анализатор") . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        //continue;
        return $data;
        } 
      }
    $data['dest_image'] = '';
    if ($data['settings']['images']!='nop' && isset($output['image'][0])) $data = $this->loadImage ($output['image'][0], $data, $output['category_id']);
    if (isset($data['settings']['hide_new'])) $status = 0;
    $supplier = '';
    if (isset($data['module']['is_supplier'])) $supplier = "supplier = '" . $this->db->escape($output['supplier']) . "',"; 
    $this->db->query("INSERT INTO " . DB_PREFIX . "product SET 
           model = '" .           $this->db->escape($output['model']) . "',
                    " .           $supplier . " 
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
           date_added = NOW()");
    $product_id = $this->db->getLastId();
   // Закупочная цена (AlexDW)
    if (isset($data['module']['costprice']) && isset($output['iprice']) ) { // Закупочная цена - поддержка для https://extensions.myopencart.com/costprice-%D0%B7%D0%B0%D0%BA%D1%83%D0%BF%D0%BE%D1%87%D0%BD%D0%B0%D1%8F-%D1%86%D0%B5%D0%BD%D0%B0-%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2-%D0%B2-opencart
      $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
      $sql .= ", cost = '" . $this->db->escape((float)$output['iprice']) . "'";
      $sql .= " WHERE product_id = '" . (int)$product_id . "'";
      $this->db->query($sql);
      }
   // Валютная цена
    if ( isset($data['module']['mcg2']) && isset($output['mc_price']) && isset($output['mc_price_cur']) ) {
      if ($output['mc_price_cur']!=$this->config->get('config_currency')) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['mc_price_cur']) . "'");
        if ($query->row) {
          $currency_id   = $query->row['currency_id'];
       		$this->db->query("DELETE FROM " . DB_PREFIX . "product_multycurr  WHERE product_id = '" . (int)$product_id . "'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_multycurr  SET   product_id = '" . (int)$product_id . "', price = '" .    (float)$output['mc_price'] . "', currency_id = '" . (int)$currency_id . "'");
          }
        }
      }
     // Закупочная цена
    if ( isset($data['module']['mcg2']) && isset($output['iprice'])) {
      if (!isset($output['iprice_cur'])) $output['iprice_cur'] = $this->config->get('config_currency');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency" . " WHERE `code` = '" . $this->db->escape($output['iprice_cur']) . "'");
        if ($query->row) {
          $currency_id   = $query->row['currency_id'];
       		$this->db->query("DELETE FROM " . DB_PREFIX . "product_input_price  WHERE product_id = '" . (int)$product_id . "'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_input_price  SET   product_id = '" . (int)$product_id . "', iprice = '" .   (float)$output['iprice'] . "', currency_id = '" . (int)$currency_id . "'");
          }
      }
    // Лог новых товаров
    if (isset($data['settings']['log_new'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new product', data = '" . $this->db->escape('ID: ' . $product_id ) ."'");
      }
    // АКЦИИ
    if (isset($output['special'])) {
      foreach ($data['customer_groups'] as $customer_group_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '1', price = '" . (float)$output['special'] . "'");
        }
      }
    // Атрибуты
    foreach ($output['attr'] as $attribute_id => $text) {
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
        if (!$order++) continue;
        $data = $this->loadImage ($next_img, $data, $output['category_id']);
        if ($data['dest_image']) $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($data['dest_image']) . "', sort_order = '" . (int)$order . "'");
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

    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET 
            product_id = '" .       (int)$product_id . "', 
            store_id = '0'");
    $this->link2category_id ($data['module']['engine'],$product_id,$output['category_id'],isset($data['module']['do_category_up'])?true:false);
    $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET 
            query = 'product_id=" . (int)$product_id . "', 
            keyword = '" . $this->translit ($output['name'],'') . "'");
    $data['info_insert'] ++;
    }
  // ПОЛЬЗОВАТЕЛЬСКИЙ СКРИТП
  if (!empty($data['settings']['update_use_script'])) {
    $this->model_zoxml2_zoxml2usescript->doUserScript($output,$data,$data['settings']['update_use_script'],$item,$product_id);
    }
  if (!empty($data['settings']['user_ro'])) {
    $this->model_zoxml2_zoxml2ro->saveROptions ($output,$product_id, $data);
    }
  return $data;
  }

public function saveAsLocal ($data) {
  if (!isset($data['settings']['load_method'])) return $data['settings']['url']; 

  $session_key = $data['session_key'];
  $user        = $data['user'];

  $src = NULL;
  if ($data['settings']['load_method']=='file_get_contents') {
    $src   = file_get_contents ($data['settings']['url']);
    }
  if ($data['settings']['load_method']=='CURL') {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $data['settings']['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_USERAGENT,'Chrome 11'); 
  	$src = curl_exec($ch);
//    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('CURL: ' . $src) ."', user = '" . $this->db->escape($user) . "'");
    }
//  return $src;
  if ($src) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение фида: ' . $data['settings']['url']) ."', user = '" . $this->db->escape($user) . "'");
    $dest = DIR_CACHE . $data['session_key'] . ".XML";
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение фида как: ' . $dest) ."', user = '" . $this->db->escape($user) . "'");
    if (file_put_contents ($dest, $src )===FALSE) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Ошибка Сохранение фида') ."', user = '" . $this->db->escape($user) . "'");
      return NULL;
      }
    return $dest;
    } 
  return NULL;
  }

public function getXML ($data, $debug_save_to='', $isXML = TRUE) {
  if (!$debug_save_to) $debug_save_to = $data['session_key'];
  $zo_image_loader = '';
  if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $this->load->model('zoxml2/zoxml2xmlpre');
  $session_key              = $data['session_key'];
  $user                     = $data['user'];
  $data['settings']['url']  = html_entity_decode ($data['settings']['url']);


  libxml_use_internal_errors(true);

  if (!isset($data['settings']['getXML_method']) || $data['settings']['getXML_method']=='simplexml_load_file') {
    if (!$isXML) return NULL; 
    $xml = $data['settings']['url']?simplexml_load_file($data['settings']['url'],"SimpleXMLElement"):NULL;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Создание XML-объекта завершено') ."', user = '" . $this->db->escape($data['user']) . "'");
    if (!$xml) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      foreach(libxml_get_errors() as $error) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      return NULL; 
      } 
    if (isset($data['module']['DEBUG'])) {
//      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape(json_encode($xml)) ."', user = '" . $this->db->escape($data['user']) . "'");
      }    
    return $xml; 
    }
    
  $src = NULL;
  if ($data['settings']['getXML_method']=='ssl_file_get_contents') {
    $arrContextOptions=array(
      "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ),
    );
    
    $src = file_get_contents ($data['settings']['url'], false, stream_context_create($arrContextOptions));    
    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    
    if ($src) {
      if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
      if (strlen($src)<2048) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Короткий ответ: ' . $src) ."', user = '" . $this->db->escape($data['user']) . "'");
      if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
        $dest = DIR_CACHE . $data['session_key'] . ".XML_PRE.YML";
        if (file_put_contents ($dest, $src )===FALSE)   {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
          return NULL;
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      if (!$isXML) return $src;
      return simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_get_contents не смог загрузить входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
    return NULL; 
    }
  if ($data['settings']['getXML_method']=='file_get_contents') {
    $src   = file_get_contents ($data['settings']['url']);
    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    
    if ($src) {
      if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
      if (strlen($src)<2048) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Короткий ответ: ' . $src) ."', user = '" . $this->db->escape($data['user']) . "'");
      if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
        $dest = DIR_CACHE . $data['session_key'] . ".XML_PRE.YML";
        if (file_put_contents ($dest, $src )===FALSE)   {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
          return NULL;
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      if (!$isXML) return $src;
      return simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
      }
    if (isset($http_response_header)) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'error', data = '" . $this->db->escape("file_get_contents не смог загрузить входной файл! http_response_header: " . json_encode($http_response_header)) . "', user = '" . $this->db->escape($data['user']) . "'");
    else                              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'error', data = '" . $this->db->escape('file_get_contents не смог загрузить входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
    return NULL; 
    }
  if ($data['settings']['getXML_method']=='CURL') {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $data['settings']['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	$src = curl_exec($ch);
    $curl_result = curl_getinfo($ch);
    curl_close ($ch);
    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    
    if ($src) {
      if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
      if (strlen($src)<2048) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Короткий ответ: ' . $src) ."', user = '" . $this->db->escape($data['user']) . "'");
      if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
        $dest = DIR_CACHE . $data['session_key'] . ".XML_PRE.YML";
        if (file_put_contents ($dest, $src )===FALSE)   {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
          return NULL;
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      if (!$isXML) return $src;
      $xml = simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
      unset ($src);
      if (!$xml) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной поток!') ."', user = '" . $this->db->escape($data['user']) . "'");
        foreach(libxml_get_errors() as $error) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($data['user']) . "'");
          }
        return NULL; 
        } 
      if (isset($data['module']['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('XML объект создан!') ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      return $xml;
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('CURL не смог загрузить входной файл! Ответ сервера: ' . json_encode($curl_result)) ."', user = '" . $this->db->escape($data['user']) . "'");
    return NULL; 
    }
  if ($data['settings']['getXML_method']=='ssl_CURL') {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $data['settings']['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  	$src = curl_exec($ch);
    curl_close ($ch);
    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $debug_save_to . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    
    if ($src) {
      if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) {
        $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
        }
      if (strlen($src)<2048) $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Короткий ответ: ' . $src) ."', user = '" . $this->db->escape($data['user']) . "'");
      if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
        $dest = DIR_CACHE . $debug_save_to . ".XML_PRE.YML";
        if (file_put_contents ($dest, $src )===FALSE)   {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
          return NULL;
          }
        else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
          }
        }
      if (isset($data['module']['DEBUG'])) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Размер файла: ' . strlen($src) . ' байт. Теперь преобразуем его в объект') ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      if (!$isXML) return $src;
try {
    $xml = simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    unset ($src);
    if (!$xml) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной поток!') ."', user = '" . $this->db->escape($data['user']) . "'");
      foreach(libxml_get_errors() as $error) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      return NULL; 
      } 
    if (isset($data['module']['DEBUG'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('XML объект создан!') ."', user = '" . $this->db->escape($data['user']) . "'");
      }
    return $xml;
} catch (Exception $e) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'Exception', data = '" . $this->db->escape(json_encode($e)) ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL; 
}
      }
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('CURL не смог загрузить входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
    return NULL; 
    }
  if ($data['settings']['getXML_method']=='HappyGifts') { // HappyGifts - центральный склад (Москва)
    $stocks = array();
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store0.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store1.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store2.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store3.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store4.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store5.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store6.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store7.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store8.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store9.xml';
    $stocks[] = 'ftp://clients:cLiENts2010@ftp.ipg.su/clients/Ostatki/store10.xml';

    $src  = "<?xml version='1.0' encoding='UTF-8' ?>\n<yml_catalog>\n<shop>\n<offers>\n";

    foreach ($stocks as $value) {
      $in_data   = file_get_contents ($value);
      if ($in_data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Загружен:' . $value) ."', user = '" . $this->db->escape($data['user']) . "'");
        $stock_xml =  simplexml_load_string ($in_data);
        unset ($in_data);
        if ($stock_xml) {
          foreach ($stock_xml->Остатки->Остаток as $item) {
            $src .= "<offer>\n";
            $src .= "<ИД>" . $item->ИД . "</ИД>\n";
            $src .= "<Свободный>" . $item->Свободный . "</Свободный>\n";
            $src .= "<Занятый>" . $item->Занятый . "</Занятый>\n";
            $src .= "<СвободныйВПути>" . $item->Поставка->СвободныйВПути . "</СвободныйВПути>\n";
            $src .= "<ЗанятыйВПути>" . $item->Поставка->ЗанятыйВПути . "</ЗанятыйВПути>\n";
            $src .= "</offer>\n";
            }
          unset ($stock_xml);
          }
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_get_contents не смог загрузить входной файл:' . $value) ."', user = '" . $this->db->escape($data['user']) . "'");
        } 
      }
    
    $src .= "</offers>\n</shop>\n</yml_catalog>";
    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    
    if (!$isXML) return $src;
    $xml = simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    return $xml; 
    }
  if ($data['settings']['getXML_method']=='csv_as_yml_utf8') {
    // Читаем
  if (defined('zo_image_loader')) $zo_image_loader = zo_image_loader;
    switch ($zo_image_loader) {
    case 'curl': {
       $ch = curl_init(); 
       curl_setopt($ch, CURLOPT_URL, $data['settings']['url']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//       curl_setopt($ch, CURLINFO_HEADER_OUT, true);
       curl_setopt($ch, CURLOPT_HEADER, false);
    	 $src      = curl_exec($ch);
       $csv      = curl_getinfo($ch);
       curl_close ($ch);
       break;
      }              
    default: $csv = file_get_contents ($data['settings']['url']);
    }          
    if (!$csv) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_get_contents не смог загрузить входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
   // Сохраняем в кеш
    $dest = DIR_CACHE . $data['session_key'] . ".CSV";
    if (file_put_contents ($dest, $csv )===FALSE)  {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    // Открываем
    if (($handle = fopen($dest, "r")) === FALSE)  {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('fopen не смог загрузить временный файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    if (($in_fields = fgetcsv($handle,0,";")) === FALSE)   {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('fgetcsv - ошибка чтения заголовка!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    // Делаем разбор заголовка
    foreach ($in_fields as $key => $value) {
      $in_fields[$key] = str_replace ( '[', '_' ,  $value );
      $in_fields[$key] = str_replace ( ']>', '>' , $in_fields[$key] );
      $in_fields[$key] = str_replace ( ']', '' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( '(', '_' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( ')', '_' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( ' ', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '/', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( ',', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '*', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '#', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '|', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '@', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '?', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '"', '_' ,  $in_fields[$key] );
      if (preg_match("/^\d/", $in_fields[$key])) $in_fields[$key] = "Column_" . $in_fields[$key];
      }
      
    $src  = "<?xml version='1.0' encoding='UTF-8' ?>\n<yml_catalog>\n<shop>\n<offers>\n";

    while (($in_data = fgetcsv($handle,0,";")) !== FALSE) {
      $src .= "<offer>\n";
      foreach ($in_data as $key => $value) {
        if (!$value) continue;
//        $value = str_replace ( "]", "" , $value );
        if (!empty($in_fields[$key])) {    
          if (strpos($in_fields[$key], 'name=')!==FALSE) $src .= "<param " . $in_fields[$key] . ">";
          else                                      $src .= "<" . $in_fields[$key] . ">";
          $src .= "<![CDATA[" . htmlspecialchars($value,ENT_QUOTES) . "]]>";
          if (strpos($in_fields[$key], 'name=')!==FALSE) $src .= "</param>\n";
          else                                      $src .= "</" . $in_fields[$key] . ">\n";
          }
        else {
          $src .= "<Column_" . $key . ">";
          $src .= "<![CDATA[" . htmlspecialchars($value,ENT_QUOTES) . "]]>";
          $src .= "</Column_" . $key . ">\n";
          }
        }
      $src .= "</offer>\n";
      }
      

    $src .= "</offers>\n</shop>\n</yml_catalog>";

    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    


    fclose($handle);
    if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
    if (!$isXML) return $src;
    $xml = simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    unset ($src);
    if (!$xml) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной поток!') ."', user = '" . $this->db->escape($data['user']) . "'");
      foreach(libxml_get_errors() as $error) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      return NULL; 
      } 
    if (isset($data['module']['DEBUG'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('XML объект создан!') ."', user = '" . $this->db->escape($data['user']) . "'");
      }
    return $xml;
    }
  if ($data['settings']['getXML_method']=='csv_as_yml_1251') {
    // Читаем
  if (defined('zo_image_loader')) $zo_image_loader = zo_image_loader;
    switch ($zo_image_loader) {
    case 'curl': {
       $ch = curl_init(); 
       curl_setopt($ch, CURLOPT_URL, $data['settings']['url']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    	 $src      = curl_exec($ch);
       $csv      = curl_getinfo($ch);
       curl_close ($ch);
       break;
      }              
    default: $csv = file_get_contents ($data['settings']['url']);
    }          
    if (!$csv) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_get_contents не смог загрузить входной файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
   // Сохраняем в кеш
    $dest = DIR_CACHE . $data['session_key'] . ".CSV";
    if (file_put_contents ($dest, $csv )===FALSE)  {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    // Открываем
    if (($handle = fopen($dest, "r")) === FALSE)  {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('fopen не смог загрузить временный файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    if (($in_fields = fgetcsv($handle,0,";")) === FALSE)   {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('fgetcsv - ошибка чтения заголовка!') ."', user = '" . $this->db->escape($data['user']) . "'");
      return NULL;
      }
 
    // Делаем разбор заголовка
    foreach ($in_fields as $key => $value) {
      $in_fields[$key] = str_replace ( '[', '_' ,  $value );
      $in_fields[$key] = str_replace ( ']>', '>' , $in_fields[$key] );
      $in_fields[$key] = str_replace ( ']', '' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( '(', '_' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( ')', '_' ,   $in_fields[$key] );
      $in_fields[$key] = str_replace ( ' ', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '/', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( ',', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( ':', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '%', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '*', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '#', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '|', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '@', '_' ,  $in_fields[$key] );
      $in_fields[$key] = str_replace ( '?', '_' ,  $in_fields[$key] );
      if (preg_match("/^\d/", $in_fields[$key])) $in_fields[$key] = "Column_" . $in_fields[$key];
      }
      
    $src  = "<?xml version='1.0' encoding='windows-1251' ?>\n<yml_catalog>\n<shop>\n<offers>\n";

    while (($in_data = fgetcsv($handle,0,";")) !== FALSE) {
      $src .= "<offer>\n";
      foreach ($in_data as $key => $value) {
        $value = trim(htmlspecialchars($value,ENT_QUOTES,'cp1251'));
        if (!$value) continue;
        if (!empty($in_fields[$key])) {    
          if (strpos($in_fields[$key], 'name=')!==FALSE) $src .= "<param " . $in_fields[$key] . ">";
          else                                      $src .= "<" . $in_fields[$key] . ">";
          $src .= "<![CDATA[" . $value . "]]>";
          if (strpos($in_fields[$key], 'name=')!==FALSE) $src .= "</param>\n";
          else                                      $src .= "</" . $in_fields[$key] . ">\n";
          }
        else {
          $src .= "<Column_" . $key . ">";
          $src .= htmlspecialchars($value,ENT_QUOTES);
          $src .= "</Column_" . $key . ">\n";
          }
        }
      $src .= "</offer>\n";
      }
      

    $src .= "</offers>\n</shop>\n</yml_catalog>";

    if (isset($data['module']['DEBUG'])||isset($data['settings']['xml2cache'])) {
      $dest = DIR_CACHE . $data['session_key'] . ".YML";
      if (file_put_contents ($dest, $src )===FALSE)   {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить временный YML-файл!') ."', user = '" . $this->db->escape($data['user']) . "'");
        return NULL;
        }
      else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('Временный YML-файл: ' . $dest) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      }    


    fclose($handle);
    if (!empty($data['settings']['user_xml_pre'])||isset($data['settings']['xmlpre_replaces'])) $src = $this->model_zoxml2_zoxml2xmlpre->doUserXMLPre($data,$src);
    if (!$isXML) return $src;
    $xml = simplexml_load_string ($src, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    unset ($src);
    if (!$xml) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('SimpleXMLElement не смог обработать входной поток!') ."', user = '" . $this->db->escape($data['user']) . "'");
      foreach(libxml_get_errors() as $error) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('Ошибка: ' . $error->message) ."', user = '" . $this->db->escape($data['user']) . "'");
        }
      return NULL; 
      } 
    if (isset($data['module']['DEBUG'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'DEBUG', data = '" . $this->db->escape('XML объект создан!') ."', user = '" . $this->db->escape($data['user']) . "'");
      }
    return $xml;
    }

  return NULL;
  }

}
?>