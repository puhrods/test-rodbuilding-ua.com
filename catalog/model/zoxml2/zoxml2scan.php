<?php
class ModelZoXml2ZoXml2Scan extends Model {

protected function getGroups1C($categories,$item) {
  foreach ($item->Группы->Группа as $group) {
    if ($group->Ид!='') {
      $parent_id    = (isset($group->ИдРодителя)&&$group->ИдРодителя!='00000000-0000-0000-0000-000000000000')?(string)$group->ИдРодителя:0;  
      $categories[(string)$group->Ид] = array( 
        'parent_id'    => $parent_id,  
        'total'        => 0,
        'name'         => (string)$group->Наименование,
        'data'         => (string)$group->Ид,
        'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
        );
      }
    if (isset($group->Группы)) {
      $categories = $this->getGroups1C ($categories,$group);
      }
    }
  return $categories;
  }
  
protected function getCategories() {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sql .= " GROUP BY cp.category_id ORDER BY name ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

protected function engineAddCategory($data) {
	$this->db->query("INSERT INTO " . DB_PREFIX . "category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (int)$data['top'] . "', `column` = '1', sort_order = '0', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");
	$category_id = $this->db->getLastId();
	foreach ($data['category_description'] as $language_id => $value) $this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
	// MySQL Hierarchical Data Closure Table Pattern
	$level = 0;
	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");
	foreach ($query->rows as $result) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");
		$level++;
	}
	$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");

	if (isset($data['category_store'])) foreach ($data['category_store'] as $store_id) $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
	if (isset($data['keyword'])) $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
	$this->cache->delete('category');
	return $category_id;
	}

protected function addCategory ($name,$parent_id) {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE name = '" . $this->db->escape($name) . "'");
  if ($query->row) return $query->row['category_id'];

  $data['parent_id']  = $parent_id;
  $data['top']        = $data['parent_id']?0:1;
  $data['status']     = 1;
  $data['category_store'] = array (0);
  $data['keyword']    = $this->model_zoxml2_zoxml2->translit ($name,'');
  $data['category_description'] = array ();
  $data['category_description'][$this->config->get( 'config_language_id' )] = array (
    'name'              => $name,
    );
  
  return $this->engineAddCategory($data);
  }

public function doUserScan($data,$xml) {
  $session_key      = $data['session_key'];
  $user             = $data['user'];

if ($data['settings']['user_scan']=='bpsnico.ru') {
  foreach ($xml->shop->offers->offer as $item) {
    $vendor       = '(производитель не указан)';
    if (!empty($item->description)) {
      $parts = explode (' ', trim($item->description) );
      if (!empty($parts[1])) $vendor = trim($parts[1]);
      }
    // В БАЗУ
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='JS-Company') {
  $naideno_po_imeni = 0;
  $empty_sku        = 0;
  foreach ($xml->shop->offers->offer as $item) {
    $txt = trim($item->vendor) . ' ' . trim($item->model);
    $sql   = "SELECT * FROM " . DB_PREFIX . "product p ";
    $sql .= " LEFT JOIN  " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE";
    $sql .= " pd.name = '" . $this->db->escape($txt) . "'";
    $sql .= " AND pd.language_id = '" . (int)$this->config->get( 'config_language_id' ) . "'";
    $sql .= " AND p.model LIKE '%-44%'";
//    echo $txt;
    $query = $this->db->query($sql);
    if ($query->row) {
      $naideno_po_imeni ++;
      if (empty($query->row['sku'])) {
        $empty_sku ++;
        }
      $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
      $sql .= ", sku = '" . $this->db->escape(trim($item->model)) . "'";
      $sql .= ", supplier = '" . $this->db->escape($session_key) . "'";
      $sql .= " WHERE product_id = '" . (int)$query->row['product_id'] . "'";
      $this->db->query($sql);
      }
    }
  $txt = 'Найдено по имени: ' . $naideno_po_imeni;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $txt = 'Найдено без артикула: ' . $empty_sku;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  return TRUE;
  }
if ($data['settings']['user_scan']=='export_data_eburg') {
  $info_processed = 0;
  $info_progress  = 0; 
  foreach ($xml->list as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
/*
<cats>
<item>Софткоттон печатный (арт. MР)</item>
<item>Постельное белье</item>
</cats>
*/
    if (!empty($item->cats)) {
      $cats = array();
      foreach ($item->cats->item as $category) $cats[] = trim($category);
      if (empty($cats[1])) continue; // ЭТО МУСОР
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($cats[0]) . "' AND parent = '" . $this->db->escape($cats[1]) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->num_rows) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($cats[0]) . "', `parent` = '" . $this->db->escape($cats[1]) . "', `total` = '1'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE id = '" . (int)$query->row['id'] . "'");
        }
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='ebazaar') {
  $info_processed = 0;
  $info_progress  = 0; 
  $categories     = array();
  $txt            = "Найдено категорий (всего): " . count($xml->categories->category);
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  foreach ($xml->categories->category as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано категорий: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    if (!empty($item['id'])) {
      $parent_id    = empty($item['parent_id'])?0:trim($item['parent_id']);  
      $categories[trim($item['id'])] = array( 
        'parent_id'    => $parent_id,  
        'total'        => 0,
        'name'         => trim($item['title']),
        'data'         => trim($item['id']),
        'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
        );
      }
    }
  foreach ($xml->products->product as $item) {
    if (!empty($item->category_id)) {
      $category_id = trim($item->category_id);
      if (isset($categories[$category_id])) {
        $categories[$category_id]['total'] ++;
        }
      }
    }
  $txt = "Обработано категорий (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->num_rows) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . " ID: " . $item['data'] . "'");
      }
    else {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE id = '" . (int)$query->row['id'] . "'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='clear-fit_attributes') {
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
  $params           = array ();
  foreach ($xml->catalog->items->item as $item) {
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
            $params [$key] = true;
            } 
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='jorgen-svensson_attributes') {
  // <attrs>
  // <group name="Биомеханические свойства">
  // <attr name="Посадка">вертикальная</attr>
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
    if (!empty($item->attrs)) {
      foreach ($item->attrs->group as $group) {
        $key  = 'attrs_' . trim($group['name']) . '_';
        foreach ($group->attr as $attr) {
          $params [$key . trim($attr['name'])] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='samsonopt_attributes') {
  // <Характеристика>Линовка блока: клетка</Характеристика>
  $params           = array ();
  foreach ($xml->Товар as $item) {
    if (!empty($item->Характеристики)) {
      foreach ($item->Характеристики->Характеристика as $attribute) {
        $parts = explode (':', $attribute);
        if (empty($parts[1])) continue;
        $key = trim($parts[0]);
        if ($key) {
          $key  = 'Характеристика_' . $key;
          $params [$key] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='zavodsporta_attributes') {
  // <Атрибуты>Вес: 85 кг | Габариты: 119 х 60 х 137 см | Max нагрузка: 160 кг | Цвет: цвет сиденья-черный | Особенности: вертикальный | Наличие: No</Атрибуты>
  $params           = array ();
  foreach ($xml->Продукт as $item) {
    if (!empty($item->Атрибуты)) {
      $parts = explode ('|', $item->Атрибуты);
      foreach ($parts as $attribute) {
        $parts2 = explode (':', $attribute);
        $key = trim($parts2[0]);
        if ($key) {
          $key  = 'Атрибуты_' . $key;
          $params [$key] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='sunstones_attributes_from_description') {
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
  // <description><![CDATA[Артикул: 047306 <br>Ширина: 7 мм.  <br>Бренд: XUPING    <br>Вес: 2,7 г.   <br>Длина: 18 см.
    $vendor           = '';
    if (!empty($item->description)) {
      $parts = explode ('|', str_replace("<br>","|",trim($item->description)));
      foreach ($parts as $part) {
        $attribute = explode (':', trim($part));
        if (!empty($attribute[1])) {
          $key  = 'atribut_' . trim($attribute[0]);
          $params [$key] = true;
          if ($key=='atribut_Бренд') $vendor = trim($attribute[1]);
          }
        }
      }
    if (!$vendor) $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    // Обновляем псевдо-производителя HOST
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='sunstones_attributes') {
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
  // <atribut>Ширина---13 мм.|Бренд---XUPING|Вес---1,9 г.|Камень---Фианит(нат.)|Качество---Не темнеет, не облазит|Материал---Медицинский сплав|Покрытие---родий|Цвет---Хрустальный|Цвет3---Серебряный</atribut>
    $vendor           = '';
    if (!empty($item->atribut)) {
      foreach ($item->atribut as $attribute) {
        $variants = explode ('|', trim($item->atribut));
        foreach ($variants as $attribute) {
          $attribute = trim(str_replace("---","|",$attribute));
          $parts     = explode ('|', $attribute);
          if (!empty($parts[1])) {
            $key  = 'atribut_' . trim($parts[0]);
            $params [$key] = true;
            if ($key=='atribut_Бренд') $vendor = $parts[1];
            }
          }
        }
      }
    if (!$vendor) $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    // Обновляем псевдо-производителя HOST
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='neotren_attributes') {
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
    if (!empty($item->attrs)) {
      foreach ($item->attrs->attr as $attribute) {
        if (!empty($attribute['name'])) {
          $key  = 'attr_' . trim($attribute['name']);
          $params [$key] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='avangard-time.ru') {
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
    if (!empty($item->attributes)) {
      $attributes = html_entity_decode(trim($item->attributes), ENT_QUOTES, 'UTF-8');
      // <dl>
      //  <dt>Цифры</dt><dd>Арабские</dd>
      //  <dt>ГАРАНТИЯ</dt><dd>1 год</dd>
      //  <dt>Мелодия</dt><dd>4</dd>
      //  <dt>Отображение даты</dt><dd>Нет</dd>
      //  <dt>Тип механизма</dt><dd>Кварцевые</dd>
      //  <dt>Материал корпуса</dt><dd>Пластик</dd>
      //  <dt>Подсветка</dt><dd>Стрелок и часовых индексов</dd>
      //  <dt>Стразы</dt><dd>Нет</dd>
      //  <dt>Цвет циферблата</dt><dd>Разноцветный</dd>
      //  <dt>Страна происхождения</dt><dd>Южная Корея</dd>
      //  <dt>Ход</dt><dd>Шаговый</dd>
      //  </dl>
      $attributes = str_replace ("<dl>",  "", $attributes); 
      $attributes = str_replace ("</dl>", "", $attributes); 
      $attributes = str_replace ("<dt>",  "", $attributes); 
      $attributes = str_replace ("<dd>",  "", $attributes); 
      $attributes = str_replace ("</dd>", ";", $attributes); 
      $attributes = str_replace ("</dt>", "|", $attributes); 
      // Убираем косяки выгрузки
      $attributes = str_replace ('<',     "", $attributes);
      $attributes = str_replace ('>',     "", $attributes);
//$text = trim($item->code) . '->';
//$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'DEBUG', data = '" . $this->db->escape($text . $attributes) . "', user = '" . $this->db->escape($data['user']) . "'");
      $parts = explode (';', $attributes);
      foreach ($parts as $attribute) {
        $options = explode ('|', trim($attribute));
        if (!empty($options[0]) && !empty($options[1])) {
          $key  = 'attributes_' . trim($options[0]);
          $params [$key] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='maomag') {
  $params           = array ();
  foreach ($xml->shop->offers->offer as $item) {
    if (!empty($item->Варианты)) {
      $parts = explode (';', trim($item->Варианты));
      if (!empty($parts[3])) {
        $options = explode ('|', trim($parts[3]));
        foreach ($options as $value) {
          $keys = explode ('=', trim($value));
          $key  = trim($keys[0]);
          if ($key) {
            $key  = 'Опция_' . $key;
            $params [$key] = true;
            }
          }
        }
      }
    if (!empty($item->Характеристики)) {
      $parts = explode ('|', trim($item->Характеристики));
      foreach ($parts as $value) {
        $keys = explode (':', trim($value));
        $key  = trim($keys[0]);
        if ($key) {
          $key  = 'Характеристики_' . $key;
          $params [$key] = true;
          }
        }
      }
    }
  foreach ($params as $key => $value) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE `name` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($key) . "', `dest_type` = 'attr', `data` = '" . $this->db->escape($key) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $key) ."'");
      }
    }
  return TRUE;
  }
if ($data['settings']['user_scan']=='moderni-boty-cz') {
  $categories       = array();
  foreach ($xml->categories->category as $item) {
/*
  <category>
   <id>3</id>
   <parentid>0</parentid>
   <name>Dámská obuv</name>
   <position>1</position>
  </category>
*/
    $parent_id    = (string)$item->parentid;  
    $categories[(string)$item->id] = array( 
      'parent_id'    => $parent_id,  
      'total'        => 0,
      'name'         => (string)$item->name,
      'data'         => (string)$item->id,
      'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
      );
    
    }
  foreach ($xml->products->product as $item) {
    if (isset($item->categorys->categoryid)) {
      $categoryid = (string)$item->categorys->categoryid;
      if (isset($categories[$categoryid])) $categories[$categoryid]['total'] ++;
      }
    }
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        }
      }
  return TRUE;
  }
if ($data['settings']['user_scan']=='inpo_ru') {
  $categories       = array();
  $params           = array();
  $params['host']   = true;
  $info_processed   = 0;    
  $info_progress    = 0;    

//$query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_categories WHERE `session_key` = '" . $this->db->escape($session_key) . "'");

  foreach ($xml->group as $root_category) {
    $txt = "Обработка категории: " . $root_category['title'];
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
   foreach ($root_category->group as $sub_category) {
      $txt = "Обработка подкатегории: " . $sub_category['title'];
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
      $parent_id    = $root_category['no'];  
      $categories[(string)$sub_category['no']] = array( 
        'parent_id'    => 0,  
        'total'        => 0,
        'name'         => (string)$root_category['title'] . ' -> ' .(string)$sub_category['title'],
        'data'         => (string)$sub_category['no'],
        'parent'       => '',
        );
     foreach ($sub_category->item as $item) {
        $categories[(string)$sub_category['no']]['total'] ++;
        $info_total ++;
        $info_processed ++;
        if (++$info_progress==$data['module']['step']) {
          $info_progress = 0; 
          $txt = "Обработано товаров: " . $info_processed;
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
          }
        // Обновляем псевдо-производителя HOST
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        // КЛЮЧИ
        foreach ($item as $key => $value) $params[$key] = true;
        }
      }
    }
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='alexika_ru') {
  // ПРОИЗВОДИТЕЛИ
  $brands           = array ();
  foreach ($xml->brands->brand as $item) {
    $brands[(string)$item->id] = (string)$item->name;
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape((string)$item->name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape((string)$item->name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape((string)$item->name) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . (string)$item->name) ."'");
      }
    }  
  // КАТЕГОРИИ 1-го УРОВНЯ
  $categories       = array();
  foreach ($xml->categories->category as $item) {
    $categories[(string)$item->id] = array( 
      'parent_id'    => 0,  
      'total'        => 0,
      'name'         => (string)$item->name,
      'data'         => (string)$item->id,
      'parent'       => '',
      );
    }  
  // КАТЕГОРИИ 2-го УРОВНЯ
  foreach ($xml->subcategories->subcategory as $item) {
    $categories[(string)$item->id] = array( 
      'parent_id'    => (string)$item->category,  
      'total'        => 0,
      'name'         => (string)$item->name,
      'data'         => (string)$item->id,
      'parent'       => $categories[(string)$item->category]['name'],
      );
    }  
  //  АТРИБУТЫ
  $params           = array ();
  foreach ($xml->attributes->attribute as $item) {
    $attribute_key = 'attribute_' . (string)$item->name;
    $params[$attribute_key] = true;
    }
  //
  $info_processed   = 0;
  foreach ($xml->products->product as $item) {
    $vendor = '';
    if (isset($item->brand)) {
      if (isset($brands[(string)$item->brand])) {
        $vendor = $brands[(string)$item->brand];
        }
      }
    if ($vendor == '') $vendor = '(производитель не указан)';
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    // Обновляем псевдо-производителя HOST
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    // КАТЕГОРИИ
    if (isset($item->subcategory)) {
      $categories[(string)$item->subcategory]['total'] ++;
      }

    $info_processed ++;
    }
  $txt = "Обработано товаров (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='dveri_com') {
    $values = array ();
    $categories = array();
    $params = array ();
    $info_total = count($xml->products->product);
    $info_processed         = 0;    
    $info_progress          = 0;    
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
    // Категории
    $txt = "Найдено категорий (всего): " . count($xml->categories->category);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    foreach ($xml->categories->category as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано категорий: " . $info_processed;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        }
/*
<categories>
<category>
<id>167</id>
<name>Акции</name>
</category>
<category>
<id>57</id>
<name>Пена, ПСУЛ</name>
<parent_id>56</parent_id>
</category>
*/          
      if (!empty($item->id)) {
        $parent_id    = empty($item->parent_id)?0:(string)$item->parent_id;  
        $categories[(string)$item->id] = array( 
          'parent_id'    => $parent_id,  
          'total'        => 0,
          'name'         => (string)$item->name,
          'data'         => (string)$item->id,
          'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
          );
        }
      }
    $txt = "Обработано категорий (всего): " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    // Производители и атрибуты
    $info_processed         = 0;    
    $info_progress          = 0;    
    $params['host']      = true;

    foreach ($xml->products->product as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано товаров: " . $info_processed;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        }
      if (isset($item['available'])) $params['available'] = true;
      if (isset($item['id']))        $params['offer_id'] = true;

      foreach ($item as $key => $value) if ($key!='params') $params[$key] = true;
      if (isset($item->params->param)) foreach ($item->params->param as $item_value)      if (isset($item_value->name)) $params['param_' . $item_value->name] = true;
      if (isset($item->components->component)) foreach ($item->components->component as $item_value)      if (isset($item_value->name)) $params['component_' . $item_value->name] = true;
      $vendor       = '(производитель не указан)';
      if (isset($item->vendor)) $vendor = (string)$item->vendor;
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
      if (isset($item->category_id)) { 
        $categoryId = (string)$item->category_id;
        if (isset($categories[$categoryId])) $categories[$categoryId]['total'] ++;
        else {
          $offer_id         = isset($item['id'])?(string)$item['id']:0;
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Предупреждение: товар ' . $offer_id . ' ссылается не несуществующую категорию - ' . $categoryId) ."', user = '" . $this->db->escape($user) . "'");
          }
        }
    }
    $txt = "Обработано товаров (всего): " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
    // СБРОС В БАЗУ
    foreach ($categories as $item) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        if (!$query->row) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
          }
        else {
          $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='mib01') {
  $values           = array ();
  $categories       = array();
  $params           = array ();
  $info_total       = count($xml->Товар);    
  $info_processed   = 0;    
  $info_progress    = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Категории
  // Производители и атрибуты
  $info_processed         = 0;    
  $info_progress          = 0;    
  $params['host']         = true;
  $params['Идентификатор'] = true;
  $params['Артикул'] = true;
  $params['Модель'] = true;
  $params['Наименование'] = true;
  $params['ОписаниеКороткое'] = true;
  $params['ОписаниеДлинное'] = true;

  foreach ($xml->Товар as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    $cat_name     = '(категория не указана)';
    $vendor       = '(производитель не указан)';
    if (isset($item->Цена))     foreach ($item->Цена as $item_value) if (isset($item_value['ТипЦены'])) $params['Цена_' . $item_value['ТипЦены']] = true;
    if (isset($item->Свойство)) {
      foreach ($item->Свойство as $item_value) {
        if (isset($item_value['Наименование'])) {
          $params['Свойство_' . $item_value['Наименование']] = true;
          if ($item_value['Наименование']=='Производитель') $vendor = (string)$item_value;
          }
        }
      }
    if ($vendor=='') $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    // Обновляем псевдо-производителя HOST
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    // Категории
    $cat_name     = (string)$vendor;
    $par_cat_name = '';
    if (isset($categories[$cat_name])) $categories[$cat_name]['total'] ++;
    else {
      $categories[$cat_name] = array( 
        'parent_id'    => 0,  
        'total'        => 1,
        'name'         => $cat_name,
        'data'         => '',
        'parent'       => '',
        );
      }
  }
  $txt = "Обработано товаров (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
  
if ($data['settings']['user_scan']=='do_category_tree') {
  $categories = array();
  $input_categories = $xml;
  if ($data['settings']['tag_shop'])        $input_categories = $input_categories->$data['settings']['tag_shop'];
  if ($data['settings']['tag_categories'])  $input_categories = $input_categories->$data['settings']['tag_categories'];
  if ($data['settings']['tag_category'])    $input_categories = $input_categories->$data['settings']['tag_category'];
  if ($input_categories&&($data['settings']['tag_shop']||$data['settings']['tag_categories']||$data['settings']['tag_category'])) {
    $txt = "Найдено категорий (всего): " . count($input_categories);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
    $info_processed          = 0;    
    foreach ($input_categories as $item) {
      $info_processed ++;
      if ($item['id']!='') {
        $parent_id    = (isset($item['parentId'])&&$item['parentId']!='')?(string)$item['parentId']:0;  
        $categories[(string)$item['id']] = array( 
          'parent_id'    => $parent_id,  
          'category_id'  => 0,
          'name'         => (string)$item,
          'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
          );
        }
      }
    $txt = "Обработано категорий (всего): " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
    // Делаем второй проход на случай если структура нарушена
    $info_processed          = 0;    
    foreach ($input_categories as $item) {
      $info_processed ++;
      if ($item['id']!='') {
        $parent_id    = (isset($item['parentId'])&&$item['parentId']!='')?(string)$item['parentId']:0;  
        $categories[(string)$item['id']] = array( 
          'parent_id'    => $parent_id,  
          'category_id'  => 0,
          'name'         => (string)$item,
          'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
          );
        }
      }
    $txt = "Обработано категорий (второй проход): " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
    // Теперь создаем категории верхнего уровня
    $info_processed          = 0;    
    foreach ($categories as $key => $item) {
      if ($item['parent']=='') {
        $categories[$key]['category_id'] = $this->addCategory ($item['name'],0);
        $categories[$key]['parent_id']   = 0;
        $info_processed ++;
        }
      }
    // Теперь создаем категории 2-го уровня
    foreach ($categories as $key => $item) {
      if ($item['category_id']!=0) continue;
      if (!isset($categories[$item['parent_id']]))  {
        $txt = "Пропуск - родитель не найден для: " . $item['name'];
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
        continue;
        }
      $parent = $categories[$item['parent_id']];
      if ($parent['category_id']==0) continue; // пропускаем - это категория ниже
      $categories[$key]['category_id'] = $this->addCategory ($item['name'],$parent['category_id']);
      $categories[$key]['parent_id']   = $parent['category_id'];
      $info_processed ++;
      }
    $txt = "Создано категорий: " . $info_processed;
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($data['user']) . "'");
    // Теперь правим таблицу zoxml2_categories
    $this->load->model('catalog/category');
    $data['categories'] = array ();
    $category_info = $this->getCategories();
    foreach ($category_info as $result) {
      $data['categories'][$result['category_id']] = $result['name'];
      }
    foreach ($categories as $key => $result) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET category_id = '" . (int)$result['category_id'] . "', name = '" . $this->db->escape($data['categories'][$result['category_id']]) . "', parent = '' WHERE `data` = '" . $this->db->escape($key) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    }
  }
if ($data['settings']['user_scan']=='p5s_one_level') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  // ------
  $values                 = array ();
  $categories             = array();
  $params                 = array ();
  $info_total             = count($xml->product);
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Производители и атрибуты
  $info_processed         = 0;    
  $info_progress          = 0;    
  $params['host']         = true;
  $params['prodID']       = true;
  $params['vendorCode']   = true;
  $params['name']         = true;
  $params['vendor']       = true;
  $params['brutto']       = true;
  $params['batteries']    = true;
  $params['pack']         = true;
  $params['material']     = true;
  $params['lenght']       = true;
  $params['diameter']     = true;
  $params['CollectionName']         = true;
  $params['price_RetailPrice']      = true;
  $params['price_BaseRetailPrice']  = true;
  $params['price_WholePrice']       = true;
  $params['price_BaseWholePrice']   = true;
  $params['assortiment_sklad']      = true;
  $params['assortiment_color']      = true;
  $params['assortiment_size']       = true;
  $params['assortiment_barcode']    = true; // Артикул опции
  $params['assortiment_color_size'] = true; // Псевдо "Связанные опции"

  foreach ($xml->product as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    foreach ($item as $key => $value) $params[$key] = true;
    // Категории
    foreach ($item->categories->category as $category) {
      $cat_name     = '(категория не указана)';
      $par_cat_name = '';
      if (isset($category['Name']))     {
        $cat_name = (string)$category['Name'];
        if (isset($category['subName']))  $cat_name     .= " / " . (string)$category['subName'];
        }
     // -------------
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
      if (!isset($data['settings']['link2category_ids'])) break;
      }
    // -------------
    $vendor       = '(производитель не указан)';
    if (isset($item['vendor'])) $vendor = (string)$item['vendor'];
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
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '1'");
      }
  }
  $txt = "Обработано товаров (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='p5s') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  // ------
  $values                 = array ();
  $categories             = array();
  $params                 = array ();
  $info_total             = count($xml->product);
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Производители и атрибуты
  $info_processed         = 0;    
  $info_progress          = 0;    
  $params['host']         = true;
  $params['prodID']       = true;
  $params['vendorCode']   = true;
  $params['name']         = true;
  $params['vendor']       = true;
  $params['brutto']       = true;
  $params['batteries']    = true;
  $params['pack']         = true;
  $params['material']     = true;
  $params['lenght']       = true;
  $params['diameter']     = true;
  $params['CollectionName']         = true;
  $params['price_RetailPrice']      = true;
  $params['price_BaseRetailPrice']  = true;
  $params['price_WholePrice']       = true;
  $params['price_BaseWholePrice']   = true;
  $params['assortiment_sklad']      = true;
  $params['assortiment_color']      = true;
  $params['assortiment_size']       = true;
  $params['assortiment_barcode']    = true; // Артикул опции
  $params['assortiment_color_size'] = true; // Псевдо "Связанные опции"

  foreach ($xml->product as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    foreach ($item as $key => $value) $params[$key] = true;
    // -------------
    $cat_name     = '(категория не указана)';
    $par_cat_name = '';
    if (isset($item->categories->category['Name']))     $par_cat_name = (string)$item->categories->category['Name'];
    if (isset($item->categories->category['subName']))  $cat_name     = (string)$item->categories->category['subName'];
    $vendor       = '(производитель не указан)';
    if (isset($item['vendor'])) $vendor = (string)$item['vendor'];
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
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '1'");
      }
    // Категории
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
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='modniy-ostrov.com') {
  $session_key = $data['session_key'];
  $user        = $data['user'];

//  $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_categories WHERE `session_key` = '" . $this->db->escape($session_key) . "'");

  $values = array ();
  $categories = array();
  $params = array ();
  if ($data['settings']['tag_shop']) $info_total = count($xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer']);    
  else {
    if ($data['settings']['tag_offers']) $info_total = count($xml->$data['settings']['tag_offers']->$data['settings']['tag_offer']);
    else                                 $info_total = count($xml->$data['settings']['tag_offer']);
    }
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Производители и атрибуты
  $info_processed         = 0;    
  $info_progress          = 0;    
  $params['host']      = true;

  if ($data['settings']['tag_shop']) $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
  else {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_offer'];
    }
  foreach ($offers as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    if (isset($item['available'])) $params['available'] = true;
    if (isset($item['id']))        $params['offer_id'] = true;

    foreach ($item as $key => $value) if ($key!='params'&&$key!='param'&&$key!='specs') $params[$key] = true;
    if (isset($item->params)) foreach ($item->params as $item_value)      if (isset($item_value['name'])) $params['params_' . $item_value['name']] = true;
    if (isset($item->param))  foreach ($item->param  as $item_value)      if (isset($item_value['name'])) $params['param_'  . $item_value['name']] = true;
    if (isset($item->specs))  foreach ($item->specs->spec as $item_value) if (isset($item_value->name))   $params['spec_'   . $item_value->name]   = true;
    $cat_name     = '(категория не указана)';
    $par_cat_name = '';
    $vendor       = '(производитель не указан)';
    if (isset($item->vendor)) $vendor = (string)$item->vendor;
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
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '1'");
      }
    // Категории
    if (isset($item->categoryId)) $cat_name = (string)$item->categoryId;
    if (!$cat_name) $cat_name     = '(категория не указана)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($cat_name) . "' AND parent = '" . $this->db->escape($par_cat_name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE name = '" . $this->db->escape($cat_name) . "' AND parent = '" . $this->db->escape($par_cat_name) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($cat_name) . "', `parent` = '" . $this->db->escape($par_cat_name) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $par_cat_name . ' -> ' . $cat_name) ."'");
      }
    }
  $txt = "Обработано товаров (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='SNT') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  $values = array ();
  $categories = array();
  $params = array ();
  if ($data['settings']['tag_shop']) $info_total = count($xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer']);    
  else {
    if ($data['settings']['tag_offers']) $info_total = count($xml->$data['settings']['tag_offers']->$data['settings']['tag_offer']);
    else                                 $info_total = count($xml->$data['settings']['tag_offer']);
    }
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Категории
  $input_categories = $xml;
  if ($data['settings']['tag_shop'])        $input_categories = $input_categories->$data['settings']['tag_shop'];
  if ($data['settings']['tag_categories'])  $input_categories = $input_categories->$data['settings']['tag_categories'];
  if ($data['settings']['tag_category'])    $input_categories = $input_categories->$data['settings']['tag_category'];
  if ($input_categories&&($data['settings']['tag_shop']||$data['settings']['tag_categories']||$data['settings']['tag_category'])) {
    $txt = "Найдено категорий (всего): " . count($input_categories);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    foreach ($input_categories as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано категорий: " . $info_processed;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        }
      if ($item['id']!='') {
        $parent_id    = 0;  
        $name         = htmlspecialchars((string)$item,ENT_QUOTES);
        $categories[(string)$item['id']] = array( 
          'parent_id'    => $parent_id,  
          'total'        => 0,
          'name'         => $name,
          'data'         => (string)$item['id'],
          'parent'       => '',
          );
        }
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

  if ($data['settings']['tag_shop']) $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
  else {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_offer'];
    }
  foreach ($offers as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    if (isset($item['available'])) $params['available'] = true;
    if (isset($item['id']))        $params['offer_id'] = true;

    foreach ($item as $key => $value) if ($key!='features') $params[$key] = true;
    if (isset($item->features))  foreach ($item->features->feature as $item_value) if (isset($item_value->name))   $params['feature_'   . $item_value->name]   = true;
    $cat_name     = '(категория не указана)';
    $par_cat_name = '';
    $vendor       = '(производитель не указан)';
    if (isset($item->brand)) $vendor = (string)$item->brand;
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
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '1'");
      }
    // Категории
    if (isset($item->category)) {
      $categoryIds = explode (',', (string)$item->category);
      foreach ($categoryIds as $cat_Id) {
        $categoryId = trim($cat_Id);
        if (isset($categories[$categoryId])) $categories[$categoryId]['total'] ++;
        else {
          $offer_id         = isset($item['id'])?(string)$item['id']:0;
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Предупреждение: товар ' . $offer_id . ' ссылается не несуществующую категорию - ' . $categoryId) ."', user = '" . $this->db->escape($user) . "'");
          }
        }
      }
  }
  $txt = "Обработано товаров (всего): " . $info_processed;
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранение данных в БД') . "', user = '" . $this->db->escape($user) . "'");
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='ENT_QUOTES') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  $values = array ();
  $categories = array();
  $params = array ();
  if ($data['settings']['tag_shop']) $info_total = count($xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer']);    
  else {
    if ($data['settings']['tag_offers']) $info_total = count($xml->$data['settings']['tag_offers']->$data['settings']['tag_offer']);
    else                                 $info_total = count($xml->$data['settings']['tag_offer']);
    }
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // Категории
  $input_categories = $xml;
  if ($data['settings']['tag_shop'])        $input_categories = $input_categories->$data['settings']['tag_shop'];
  if ($data['settings']['tag_categories'])  $input_categories = $input_categories->$data['settings']['tag_categories'];
  if ($data['settings']['tag_category'])    $input_categories = $input_categories->$data['settings']['tag_category'];
  if ($input_categories&&($data['settings']['tag_shop']||$data['settings']['tag_categories']||$data['settings']['tag_category'])) {
    $txt = "Найдено категорий (всего): " . count($input_categories);
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
    foreach ($input_categories as $item) {
      $info_processed ++;
      if (++$info_progress==$data['module']['step']) {
        $info_progress = 0; 
        $txt = "Обработано категорий: " . $info_processed;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
        }
      if ($item['id']!='') {
        $parent_id    = (isset($item['parentId'])&&$item['parentId']!='')?(string)$item['parentId']:0;  
        $name         = htmlspecialchars((string)$item,ENT_QUOTES);
// Здесь можно вставить дополнительные проверки и преобразования
// Конец дополнительных проверок и преобразований
        $categories[(string)$item['id']] = array( 
          'parent_id'    => $parent_id,  
          'total'        => 0,
          'name'         => $name,
          'data'         => (string)$item['id'],
          'parent'       => isset($categories[$parent_id])?$categories[$parent_id]['name']:'',
          );
        }
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

  if ($data['settings']['tag_shop']) $offers = $xml->$data['settings']['tag_shop']->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
  else {
    if ($data['settings']['tag_offers']) $offers = $xml->$data['settings']['tag_offers']->$data['settings']['tag_offer'];
    else                                 $offers = $xml->$data['settings']['tag_offer'];
    }
  foreach ($offers as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    if (isset($item['available'])) $params['available'] = true;
    if (isset($item['id']))        $params['offer_id'] = true;

    foreach ($item as $key => $value) if ($key!='params'&&$key!='param'&&$key!='specs') $params[$key] = true;
    if (isset($item->params)) foreach ($item->params as $item_value)      if (isset($item_value['name'])) $params['params_' . $item_value['name']] = true;
    if (isset($item->param))  foreach ($item->param  as $item_value)      if (isset($item_value['name'])) $params['param_'  . $item_value['name']] = true;
    if (isset($item->specs))  foreach ($item->specs->spec as $item_value) if (isset($item_value->name))   $params['spec_'   . $item_value->name]   = true;
//      if (isset($item->attributes->attribute))  foreach ($item->attributes->attribute as $item_value) if (isset($item_value->name))   $params['attribute_'   . $item_value->name]   = true;
    $cat_name     = '(категория не указана)';
    $par_cat_name = '';
    $vendor       = '(производитель не указан)';
    if (isset($item->vendor)) $vendor = (string)$item->vendor;
    foreach ($item as $key => $value) {
      $option_key = $key;
      if ($key=='params') if (isset($value['name'])) $option_key = 'params_' . $value['name'];   
      if ($key=='param')  if (isset($value['name'])) $option_key = 'param_'  . $value['name'];   
      if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor')       $vendor = (string)$value;
      if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='cat_name')     $cat_name = (string)$value;
      if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='par_cat_name') $par_cat_name = (string)$value;
      if ($key=='specs')  {
        foreach ($value->spec as $item_value) {
          $option_key = 'spec_';
          if (isset($item_value->name)) $option_key .= (string)$item_value->name;
          if (isset($data['options'][$option_key])&&$data['options'][$option_key]['dest_type']=='vendor') $vendor = (string)$item_value->value;
          }
        }
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
    // Добавляем псевдо-производителя HOST
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = 'host', `total` = '1'");
      }
    // Категории
    if (isset($item->categoryId)||isset($item['categoryId'])) {
      $categoryId = (string)(isset($item->categoryId)?$item->categoryId:$item['categoryId']);
      if (isset($categories[$categoryId])) $categories[$categoryId]['total'] ++;
      else {
        $offer_id         = isset($item['id'])?(string)$item['id']:0;
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'warning', data = '" . $this->db->escape('Предупреждение: товар ' . $offer_id . ' ссылается не несуществующую категорию - ' . $categoryId) ."', user = '" . $this->db->escape($user) . "'");
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
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
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
  }
if ($data['settings']['user_scan']=='ALSO') {
  $session_key            = $data['session_key'];
  $user                   = $data['user'];
  $values                 = array ();
  $categories             = array();
  $params                 = array ();
  $params['QtyAvailable']  = array (
        'name' => 'QtyAvailable',
        'code' => 'QtyAvailable',
        'dest_type' => 'quantity',
        );
  $params['UnitPrice']     = array (
        'name' => 'UnitPrice',
        'code' => 'UnitPrice',
        'dest_type' => 'price',
        );
  $info_total             = count($xml->ListofCatalogDetails->CatalogItem);    
  $info_processed         = 0;    
  $info_progress          = 0;    
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape("Найдено товаров: " . $info_total . ". Старт обработки") . "', user = '" . $this->db->escape($user) . "'");
  // ---Производители и атрибуты
  foreach ($xml->ListofCatalogDetails->CatalogItem as $item) {
    $info_processed ++;
    if (++$info_progress==$data['module']['step']) {
      $info_progress = 0; 
      $txt = "Обработано товаров: " . $info_processed;
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape($txt) . "', user = '" . $this->db->escape($user) . "'");
      }
    $cat_name     = '(категория не указана)';
    $par_cat_name = '';
    $vendor       = '(производитель не указан)';
    foreach ($item->Product->Grouping->GroupBy as $GroupBy) {
      if ($GroupBy['GroupID']=='VendorID') $vendor   = (string)$GroupBy['Value'];
      if ($GroupBy['GroupID']=='ClassID')  $cat_name = (string)$GroupBy['Value'];
      }
    if (isset($item->Product->ProductID)) { //SKU
      $params['ProductID'] = array (
        'name' => 'ProductID',
        'code' => 'ProductID',
        'dest_type' => 'sku',
        );    
      }
    if (isset($item->Product->PartNumber)) { //Model
      $params['PartNumber'] = array (
        'name' => 'PartNumber',
        'code' => 'PartNumber',
        'dest_type' => 'model',
        );      
      }
    if (isset($item->Product->EANCode)) { //EAN
      $params['EANCode'] = array (
        'name' => 'EANCode',
        'code' => 'EANCode',
        'dest_type' => 'ean',
        );          
      }
    if (isset($item->Product->Description)) { //Name
      $params['Description'] = array (
        'name' => 'Description',
        'code' => 'Description',
        'dest_type' => 'name',
        );      
      }
    if (isset($item->Product->LongDesc)) { //Description
      $params['LongDesc'] = array (
        'name' => 'LongDesc',
        'code' => 'LongDesc',
        'dest_type' => 'description',
        );         
      }
    if (isset($item->Product->PeriodofWarranty)) { //Attribute:Warranty
      $params['PeriodofWarranty'] = array (
        'name' => 'PeriodofWarranty',
        'code' => 'PeriodofWarranty',
        'dest_type' => 'attr',
        );
      }
    // ------
    if (!$vendor) $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    // Категории
    if ($cat_name) {
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
  foreach ($params as $item) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE name = '" . $this->db->escape($item['name']) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item['name']) . "', `dest_type` = '" . $this->db->escape($item['dest_type']) . "', `data` = '" . $this->db->escape($item['code']) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $item['name']) ."'");
      }
    }
  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'end', data = '" . $this->db->escape('Сохранение данных в БД завершено!') . "', user = '" . $this->db->escape($user) . "'");
  }
if ($data['settings']['user_scan']=='karnatextile') {
  // Добавляем псевдо-производителя HOST
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
  if ($query->row) {
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = '" . count($xml->Товар) . "' WHERE name = 'host' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    }
  else {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = 'host', `total` = '" . count($xml->Товар) . "'");
    }
    // Параметры
    $params[] = array (
      'name' => 'Наименование',
      'code' => 'Наименование',
      'dest_type' => 'name',
      );
    $params[] = array ( 
      'name' => 'ХарактеристикаНоменклатуры',
      'code' => 'ХарактеристикаНоменклатуры',
      'dest_type' => 'option',
      );
    $params[] = array ( 
      'name' => 'Категория',
      'code' => 'Категория',
      'dest_type' => 'cat_name',
      );
    $params[] = array ( 
      'name' => 'Артикул',
      'code' => 'Артикул',
      'dest_type' => 'sku',
      );
    $params[] = array (
      'name' => 'Идентификатор',
      'code' => 'Идентификатор',
      'dest_type' => 'o_sku',
      );
    $params[] = array (
      'name' => 'Файл (изображение)',
      'code' => 'Файл',
      'dest_type' => 'image',
      );
    $params[] = array (
      'name' => 'Штрихкод',
      'code' => 'Штрихкод',
      'dest_type' => 'attr',
      );
    $params[] = array (
      'name' => 'ЕстьВНаличие',
      'code' => 'ЕстьВНаличие',
      'dest_type' => 'quantity',
      );
    $params[] = array (
      'name' => 'Цена',
      'code' => 'Цена',
      'dest_type' => 'price',
      );
  foreach ($xml->Товар as $item) {
    // Категория
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item["Категория"]) . "' AND parent = '' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE name = '" . $this->db->escape($item["Категория"]) . "' AND parent = '' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item["Категория"]) . "', `parent` = '', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item["Категория"]) ."'");
      }
    // Производитель и свойства
    $vendor       = '(производитель не указан)';
    foreach ($item->Свойствы->Свойство as $value) {
      $option_key = 'Свойство_' . (string)$value['Имя'];
      if ($option_key=='Свойство_Изготовитель') $vendor = trim($value['Значение']);
      $params[$option_key] = array (
        'name'      => $option_key,
        'code'      => $option_key,
        'dest_type' => 'attr',
        );
      }
    if ($vendor == '') $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    }

  foreach ($params as $item) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE name = '" . $this->db->escape($item['name']) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item['name']) . "', `dest_type` = '" . $this->db->escape($item['dest_type']) . "', `data` = '" . $this->db->escape($item['code']) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $item['name']) ."'");
      }
    }
  }
if ($data['settings']['user_scan']=='Номенклатура_Элемент') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  $categories = array();
  $categories = $this->getGroups1C($categories,$xml);
    // Добавляем всех родителей в путь
  foreach ($categories as $key => $item) {
    $parent_id = $item['parent_id'];
    $name      = '';
    while ($parent_id) {
      $parent_item = $categories[$parent_id];
      if ($name)  $name = $parent_item['name'] . " / " . $name;
      else        $name = $parent_item['name'];
      $parent_id        = $parent_item['parent_id'];
      }
    $categories[$key]['parent'] = $name;
    }

  // ТОВАРЫ
  foreach ($xml->Номенклатура->Элемент as $item) {
    if (isset($item->ИДРодителя)) {
      if (isset($categories[(string)$item->ИДРодителя])) $categories[(string)$item->ИДРодителя]['total'] ++;
      }
    
    $vendor = isset($item->БрендОсн)?(string)$item->БрендОсн:'(производитель не указан)';
    if ($vendor=='') $vendor = '(производитель не указан)';
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($vendor) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($vendor) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($session_key) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $vendor) ."'");
      }
    // Обновляем псевдо-производителя HOST
    $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($session_key) . "'");
    }
  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        }
      }
  return TRUE;
  }
if ($data['settings']['user_scan']=='eklektika') {
  $session_key = $data['session_key'];
  $user        = $data['user'];
  $categories = array();
  $categories = $this->getGroups1C($categories,$xml->Классификатор);
    // Добавляем всех родителей в путь
  foreach ($categories as $key => $item) {
    $parent_id = $item['parent_id'];
    $name      = '';
    while ($parent_id) {
      $parent_item = $categories[$parent_id];
      if ($name)  $name = $parent_item['name'] . " / " . $name;
      else        $name = $parent_item['name'];
      $parent_id        = $parent_item['parent_id'];
      }
    $categories[$key]['parent'] = $name;
    }

  // ТОВАРЫ
  foreach ($xml->Каталог->Товары->Товар as $item) {
    if (isset($item->Группы->Ид)) {
      foreach ($item->Группы->Ид as $id) {
        $id = trim($id);
        if (isset($categories[$id])) $categories[$id]['total'] ++;
        break;
        }
      }
    }

  // СБРОС В БАЗУ
  foreach ($categories as $item) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `data` = '" . $this->db->escape($item['data']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
      if (!$query->row) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($session_key) . "', `name` = '" . $this->db->escape($item['name']) . "', `parent` = '" . $this->db->escape($item['parent']) . "', `data` = '" . $this->db->escape($item['data']) . "', `total` = '" . $this->db->escape($item['total']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($session_key) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item['parent'] . ' -> ' . $item['name']) . "ID: " . $item['data'] . "'");
        }
      else {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = '" . $this->db->escape($item['total']) . "' WHERE name = '" . $this->db->escape($item['name']) . "' AND parent = '" . $this->db->escape($item['parent']) . "' AND `session_key` = '" . $this->db->escape($session_key) . "'");
        }
      }
  return TRUE;
  }
if ($data['settings']['user_scan']=='Элемент') {
  foreach ($xml->Элемент as $item) {
    // Категория
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item["Категория"]) . "' AND parent = '' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE name = '" . $this->db->escape($item["Категория"]) . "' AND parent = '' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item["Категория"]) . "', `parent` = '', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item["Категория"]) ."'");
      }
    // Производитель
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = '" . $this->db->escape($item["Производитель"]) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = '" . $this->db->escape($item["Производитель"]) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item["Производитель"]) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new brand', data = '" . $this->db->escape('Новый производитель: ' . $item["Производитель"]) ."'");
      }
    }
    // Параметры
    $params[] = array (
      'name' => 'Наименование',
      'code' => 'Наименование',
      'dest_type' => 'name',
      );
    $params[] = array ( 
      'name' => 'Описание',
      'code' => 'Описание',
      'dest_type' => 'description',
      );
    $params[] = array ( 
      'name' => 'Категория',
      'code' => 'Категория',
      'dest_type' => 'cat_name',
      );
    $params[] = array ( 
      'name' => 'Артикул',
      'code' => 'Артикул',
      'dest_type' => 'sku',
      );
    $params[] = array (
      'name' => 'Производитель',
      'code' => 'Производитель',
      'dest_type' => 'vendor',
      );
    $params[] = array (
      'name' => 'Цена',
      'code' => 'Цена',
      'dest_type' => 'price',
      );

  foreach ($params as $item) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE name = '" . $this->db->escape($item['name']) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item['name']) . "', `dest_type` = '" . $this->db->escape($item['dest_type']) . "', `data` = '" . $this->db->escape($item['code']) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $item['name']) ."'");
      }
    }
  }
if ($data['settings']['user_scan']=='driada-sport.ru') {
  foreach ($xml->price as $item) {
    // Категория
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE name = '" . $this->db->escape($item["Уровень2"]) . "' AND parent = '" . $this->db->escape($item["Уровень1"]) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET total = (total + 1) WHERE name = '" . $this->db->escape($item["Уровень2"]) . "' AND parent = '" . $this->db->escape($item["Уровень1"]) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_categories SET session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item["Уровень2"]) . "', `parent` = '" . $this->db->escape($item["Уровень1"]) . "', `total` = '1'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events     SET session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new category', data = '" . $this->db->escape('Новая категория: ' . $item["Категория"]) ."'");
      }
    // Производитель
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE name = 'host' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if ($query->row) {
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET total = (total + 1) WHERE name = 'host' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
      }
    else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_vendors SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = 'host', `total` = '1'");
      }
    }
    // Параметры
    $params[] = array ( 
      'name' => 'Артикул',
      'code' => 'Артикул',
      'dest_type' => 'sku',
      );
    $params[] = array (
      'name' => 'Цена',
      'code' => 'Цена',
      'dest_type' => 'price',
      );

  foreach ($params as $item) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options WHERE name = '" . $this->db->escape($item['name']) . "' AND `session_key` = '" . $this->db->escape($data['session_key']) . "'");
    if (!$query->row) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_options SET   session_key = '" . $this->db->escape($data['session_key']) . "', `name` = '" . $this->db->escape($item['name']) . "', `dest_type` = '" . $this->db->escape($item['dest_type']) . "', `data` = '" . $this->db->escape($item['code']) . "'");
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_events  SET   session_key = '" . $this->db->escape($data['session_key']) . "', type = 'new option', data = '" . $this->db->escape('Новый параметр: ' . $item['name']) ."'");
      }
    }
  }
return NULL; 
}

}
?>
