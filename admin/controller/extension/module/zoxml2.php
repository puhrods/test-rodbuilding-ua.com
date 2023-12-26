<?php
################################################################################################
#  СТРУКТУРА ДАННЫХ ПОСТАВЩИКА
#  
#   supplier[session_key][settings]     - массив общих настроек (сохраняется в zoxml2_suppliers)
#   supplier[session_key][vendors]      - массив поставщиков и их настроек
#   supplier[session_key][categories]   - массив категорий и их настроек 
#   supplier[session_key][options]      - массив атрибутов\опций  и их настроек
#   supplier[session_key][log]          - массив истории операций
#  
#   supplier[session_key][settings]     - массив общих настроек (детализация с примером)
#   			'module'       => "zoxml2_textiloptom",
#   			'name'         => "Текстиль оптом",
#   			'session_key'  => 'textiloptom',
#   			'url'          => 'http://api.textiloptom.net/v4/api/productsVal.xml?api_key=',
#   			'license'      => 'f6913b4b400c260f4015a50f869a11fc',
################################################################################################
# ТАБЛИЦА В БД            - zoxml2_module
# id                      - уникальный id строки 
# key                     - ключ массива
# data                    - данные (JSON)
#  
# ТАБЛИЦА В БД            - zoxml2_suppliers
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# data                    - данные (array)
#  
# ТАБЛИЦА В БД            - zoxml2_vendors
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# name                    - оригинальное название производителя у поставщика
# manufacturer_id         - id производителя в БД 
# total                   - кол-во товаров
# margin                  - наценки
#  
# ТАБЛИЦА В БД            - zoxml2_categories
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# name                    - оригинальное название категории у поставщика        
# parent                  - родитель категории у поставщика        
# category_id             - id категории в БД 
# total                   - кол-во товаров
# data                    - прочие данные (опционально)
#  
# ТАБЛИЦА В БД            - zoxml2_options
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# name                    - оригинальное название опции у поставщика        
# dest_type               - пусто - игнорировать, option, attr, o_description, o_sku, country, sliv, ym, iprice       
# dest_id                 - id опции\атрибута в БД 
# data                    - прочие данные (опционально)
#  
# ТАБЛИЦА В БД            - zoxml2_replace
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# sort_order              - порядок использования 
# type                    - тип
# txt_before              - искомая строка       
# txt_after               - подстановка     
#  
# ТАБЛИЦА В БД            - zoxml2_log
# id                      - уникальный id строки 
# session_key             - идентификатор поставщика
# time                    - метка времени события
# user                    - user
# data                    - данные
################################################################################################
class ControllerExtensionModuleZoXml2 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/zoxml2');
		$this->document->setTitle($this->language->get('heading_title2'));
		$data['heading_title']  = $this->language->get('heading_title2');
		$data['button_cancel']  = $this->language->get('button_cancel');
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

    $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title2'),
			'href' => $this->url->link('extension/module/zoxml2', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['user_token']  = $this->session->data['user_token'];
		$data['real_token']  = $this->session->data['user_token'];
// -----------------------------------------------------------------------------
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='supplier'");
if (!$query->num_rows) $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `supplier` VARCHAR(63) NOT NULL");

$data['module'] = array ();
$data['module']['kill_log'] = 'nop';      
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
foreach ($query->rows as $result) $data['module'][$result['key']] = json_decode ($result['data']);

// Auto-detect
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='noindex' ");
if ($query->num_rows) $data['module']['can_noindex_new'] = true;
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='supplier' ");
if ($query->num_rows) $data['module']['is_supplier'] = TRUE;
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

$data['module']['is_meta_title']  = false;
$data['module']['is_seo_title']   = false;
$data['module']['is_meta_h1']     = false;
$data['module']['is_seo_h1']      = false;

$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` WHERE field='meta_title' ");
if ($query->num_rows) $data['module']['is_meta_title'] = true;
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` WHERE field='seo_title' ");
if ($query->num_rows) $data['module']['is_seo_title'] = true;
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` WHERE field='meta_h1' ");
if ($query->num_rows) $data['module']['is_meta_h1']    = true;
$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` WHERE field='seo_h1' ");
if ($query->num_rows) $data['module']['is_seo_h1']    = true;
// -----------------------------------------------------------------------------
if (empty($data['module']['default_supplier'])) $data['module']['default_supplier'] = "no";

$filter_supplier = '';
if (isset($this->request->get['filter_supplier'])) {
  $data['filter_supplier'] = $this->request->get['filter_supplier'];
  $data['user_token'] .= "&filter_supplier=" . $data['filter_supplier'];
  $filter_supplier = " WHERE session_key='" . $this->db->escape($this->request->get['filter_supplier']) . "'  OR session_key='0' ";
  }  
else {
  if ($data['module']['default_supplier']!='all') {
    $data['filter_supplier'] = $data['module']['default_supplier'];
    $data['user_token'] .= "&filter_supplier=" . $data['module']['default_supplier'];
    $filter_supplier = " WHERE session_key='" . $this->db->escape($data['module']['default_supplier']) . "'  OR session_key='0' ";
    }
  }
// -----------------------------------------------------------------------------
$data['extensions']  = $this->extensions(isset($data['module']['SSL']));

$data['suppliers_list'] = array();
$data['suppliers'] = array();
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_suppliers");
foreach ($query->rows as $result) {
  $value = json_decode ($result['data']);
  $data['suppliers_list'][$result['session_key']]= $value->name;
  $data['suppliers'][$result['session_key']]['settings'] = array(              
  			'module'       => $value->module,
  			'name'         => $value->name,
  			'url'          => isset($value->url)?$value->url:"",
  			'license'      => isset($value->license)?$value->license:"",
  			'session_key'  => $result['session_key'],
        'before'       => $value->before,
        'images'       => $value->images,
        'link'         => $value->link,
        'add_before'   => $value->add_before,
        'mul_after'    => $value->mul_after,
        'add_after'    => $value->add_after,
        'before_mode'  => $value->before_mode,
        'stores'       => isset($value->stores)?$value->stores:0,
        'image_save_to'     => isset($value->image_save_to)?$value->image_save_to:$result['session_key'],
        'image_save_as'     => isset($value->image_save_as)?$value->image_save_as:'url',
        'option_image_save_as'     => isset($value->option_image_save_as)?$value->option_image_save_as:'old',
        'curl_options'      => isset($value->curl_options)?$value->curl_options:'',
        'zo_image_loader'   => isset($value->zo_image_loader)?$value->zo_image_loader:'file_get_contents',
        'hide_by_attribute' => isset($value->hide_by_attribute)?$value->hide_by_attribute:'0',
        'user_exception'    => isset($value->user_exception)?$value->user_exception:'0',
        'user_exceptions'   => isset($value->user_exceptions)?$value->user_exceptions:'',
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
        'url_tpl'           => isset($value->url_tpl)?$value->url_tpl:'',
        'model_tpl'         => isset($value->model_tpl)?$value->model_tpl:'',
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
        'max_image_size'    => !empty($value->max_image_size)?$value->max_image_size:'',
        'img_path'          => isset($value->img_path)?$value->img_path:'',
        );
                    
  if (isset($value->link2category_ids))     $data['suppliers'][$result['session_key']]['settings']['link2category_ids'] = 1;
  if (isset($value->auto_atributes))        $data['suppliers'][$result['session_key']]['settings']['auto_atributes'] = 1;
  if (isset($value->hide_missing))          $data['suppliers'][$result['session_key']]['settings']['hide_missing'] = 1;
  if (isset($value->zero_missing))          $data['suppliers'][$result['session_key']]['settings']['zero_missing'] = 1;
  if (isset($value->xml2cache))             $data['suppliers'][$result['session_key']]['settings']['xml2cache'] = 1;
  if (isset($value->price_table4mc))        $data['suppliers'][$result['session_key']]['settings']['price_table4mc'] = 1;
  if (isset($value->hide))                  $data['suppliers'][$result['session_key']]['settings']['hide'] = 1;
  if (isset($value->hide_new))              $data['suppliers'][$result['session_key']]['settings']['hide_new'] = 1;
  if (isset($value->noindex_new))           $data['suppliers'][$result['session_key']]['settings']['noindex_new'] = 1;
  if (isset($value->log_new))               $data['suppliers'][$result['session_key']]['settings']['log_new'] = 1;
  if (isset($value->insert))                $data['suppliers'][$result['session_key']]['settings']['insert'] = 1;
  if (isset($value->update))                $data['suppliers'][$result['session_key']]['settings']['update'] = 1;
  if (isset($value->update_price))          $data['suppliers'][$result['session_key']]['settings']['update_price'] = 1;
  if (isset($value->update_special))        $data['suppliers'][$result['session_key']]['settings']['update_special'] = 1;
  if (isset($value->update_image))          $data['suppliers'][$result['session_key']]['settings']['update_image'] = 1;
  if (isset($value->update_quantity))       $data['suppliers'][$result['session_key']]['settings']['update_quantity'] = 1;
  if (isset($value->update_name))           $data['suppliers'][$result['session_key']]['settings']['update_name'] = 1;
  if (isset($value->update_description))    $data['suppliers'][$result['session_key']]['settings']['update_description'] = 1;
  if (isset($value->update_category))       $data['suppliers'][$result['session_key']]['settings']['update_category'] = 1;
  if (isset($value->update_atributes))      $data['suppliers'][$result['session_key']]['settings']['update_atributes'] = 1;
  if (isset($value->update_vendor))         $data['suppliers'][$result['session_key']]['settings']['update_vendor'] = 1;
  if (isset($value->update_sku))            $data['suppliers'][$result['session_key']]['settings']['update_sku'] = 1;
  if (isset($value->update_ean))            $data['suppliers'][$result['session_key']]['settings']['update_ean'] = 1;
  if (isset($value->update_minimum))        $data['suppliers'][$result['session_key']]['settings']['update_minimum'] = 1;
  if (isset($value->update_upc))            $data['suppliers'][$result['session_key']]['settings']['update_upc'] = 1;
  if (isset($value->update_jan))            $data['suppliers'][$result['session_key']]['settings']['update_jan'] = 1;
  if (isset($value->update_isbn))           $data['suppliers'][$result['session_key']]['settings']['update_isbn'] = 1;
  if (isset($value->update_mpn))            $data['suppliers'][$result['session_key']]['settings']['update_mpn'] = 1;
  if (isset($value->update_model))          $data['suppliers'][$result['session_key']]['settings']['update_model'] = 1;
  if (isset($value->update_weight))         $data['suppliers'][$result['session_key']]['settings']['update_weight'] = 1;
  if (isset($value->update_l_w_h))          $data['suppliers'][$result['session_key']]['settings']['update_l_w_h'] = 1;
  if (isset($value->update_stock_status_id))$data['suppliers'][$result['session_key']]['settings']['update_stock_status_id'] = 1;
  if (isset($value->no_update))             $data['suppliers'][$result['session_key']]['settings']['no_update'] = 1;
  if (isset($value->not_empty_only))        $data['suppliers'][$result['session_key']]['settings']['not_empty_only'] = 1;
  if (isset($value->user_filter))           $data['suppliers'][$result['session_key']]['settings']['user_filter'] = $value->user_filter;
  if (isset($value->user_scan))             $data['suppliers'][$result['session_key']]['settings']['user_scan'] = $value->user_scan;
  if (isset($value->user_xml_pre))          $data['suppliers'][$result['session_key']]['settings']['user_xml_pre'] = $value->user_xml_pre;
  if (isset($value->user_start))            $data['suppliers'][$result['session_key']]['settings']['user_start'] = $value->user_start;
  if (isset($value->user_pre))              $data['suppliers'][$result['session_key']]['settings']['user_pre'] = $value->user_pre;
  if (isset($value->user_ro))               $data['suppliers'][$result['session_key']]['settings']['user_ro'] = $value->user_ro;
  if (isset($value->user_after))            $data['suppliers'][$result['session_key']]['settings']['user_after'] = $value->user_after;
  if (isset($value->insert_analyzer))       $data['suppliers'][$result['session_key']]['settings']['insert_analyzer'] = $value->insert_analyzer;
  if (isset($value->update_analyzer))       $data['suppliers'][$result['session_key']]['settings']['update_analyzer'] = $value->update_analyzer;
  if (isset($value->update_use_script))     $data['suppliers'][$result['session_key']]['settings']['update_use_script'] = $value->update_use_script;
  if (isset($value->after))                 $data['suppliers'][$result['session_key']]['settings']['after']  = 1;
  if (isset($value->link_vendor))           $data['suppliers'][$result['session_key']]['settings']['link_vendor']  = 1;
  if (isset($value->link_supplier))         $data['suppliers'][$result['session_key']]['settings']['link_supplier']  = 1;

  if (isset($value->default_atribute_group))  $data['suppliers'][$result['session_key']]['settings']['default_atribute_group']  = $value->default_atribute_group;
  else                                        $data['suppliers'][$result['session_key']]['settings']['default_atribute_group']  = 0;
  if (isset($value->auto_atributes_db))       $data['suppliers'][$result['session_key']]['settings']['auto_atributes_db']  = $value->auto_atributes_db;
  else                                        $data['suppliers'][$result['session_key']]['settings']['auto_atributes_db']  = "common";

  $zoxml2_log = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_log WHERE session_key = '" . $this->db->escape($result['session_key']) . "' ORDER by id DESC LIMIT 1");
  if ($zoxml2_log->row)  $data['suppliers'][$result['session_key']]['settings']['log_last_id'] = $zoxml2_log->row['id'];
  else                   $data['suppliers'][$result['session_key']]['settings']['log_last_id'] = 0;
  if (isset($value->language))               $data['suppliers'][$result['session_key']]['settings']['language'] = $value->language;
  else                                       $data['suppliers'][$result['session_key']]['settings']['language'] = $this->config->get( 'config_language_id' );
  if (isset($value->all_languages))          $data['suppliers'][$result['session_key']]['settings']['all_languages'] = $value->all_languages;
  }

// МУЛЬТИМАГАЗИН
$data['all_stores'] = array();
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
foreach ($query->rows as $result) {
  $data['all_stores'][$result['store_id']] = $result['name'] . " (" . $result['url'] . ")";
  }
// ГРУППЫ АТРИБУТОВ
$data['attribute_groups'] = array();
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE language_id = '" . (int)$this->config->get( 'config_language_id' ) . "'");
foreach ($query->rows as $result) $data['attribute_groups'][$result['attribute_group_id']] = $result['name'];

// Делаем добавление недостающих полей в базы
$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "zoxml2_replace");
$is_mode = false;
foreach ($query->rows as $result) if ($result['Field']=='mode') $is_mode = true; 
if (!$is_mode) $this->db->query("ALTER TABLE " . DB_PREFIX . "zoxml2_replace ADD  `mode` VARCHAR(63) NOT NULL");

$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "zoxml2_vendors");
$is_margin = false;
foreach ($query->rows as $result) if ($result['Field']=='margin') $is_margin = true; 
if (!$is_margin) $this->db->query("ALTER TABLE " . DB_PREFIX . "zoxml2_vendors ADD  `margin` TEXT NOT NULL");

$query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "zoxml2_categories");
$is_path = false;
$is_margin = false;
$is_parent_code = false;
foreach ($query->rows as $result) {
  if ($result['Field']=='path')   $is_path = true; 
  if ($result['Field']=='margin') $is_margin = true; 
  if ($result['Field']=='parent_code') $is_parent_code = true; 
  }
if (!$is_parent_code)   $this->db->query("ALTER TABLE " . DB_PREFIX . "zoxml2_categories ADD  `parent_code`   TEXT NOT NULL");
if (!$is_path)          $this->db->query("ALTER TABLE " . DB_PREFIX . "zoxml2_categories ADD  `path`   TEXT NOT NULL");
if (!$is_margin)        $this->db->query("ALTER TABLE " . DB_PREFIX . "zoxml2_categories ADD  `margin` TEXT NOT NULL");

$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` WHERE field='zo_path' ");
if (!$query->num_rows) $this->db->query("ALTER TABLE " . DB_PREFIX . "category ADD  `zo_path` TEXT NOT NULL");

$this->load->model('catalog/manufacturer');
$this->load->model('catalog/category');
$this->load->model('catalog/option');
$this->load->model('catalog/attribute');
$this->load->model('tool/image');
$data['languages']     = array();
$data['all_options']   = array();
$data['attr_groups']   = array();
$types                 = array('select','radio','checkbox','image','checkbox_qty','radio_qty','image_qty','input_qty','input_qty_ns','input_qty_td','select_qty','area_hw','list');

$data['all_stock_status_id']   = array();
$stock_status_id  = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id='" . $this->config->get( 'config_language_id' ) . "'");
foreach ($stock_status_id->rows as $result) $data['all_stock_status_id'][$result['stock_status_id']] = $result['name'];

$languages = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
foreach ($languages->rows as $result) $data['languages'][$result['language_id']] = $result['name'];

$options = $this->model_catalog_option->getOptions($data['all_options']);
foreach ($options as $result) if (in_array ($result['type'],$types)) $data['all_options'][$result['option_id']] = $result['name'];

$groups = $this->model_catalog_attribute->getAttributes($data['attr_groups']);
foreach ($groups as $result) $data['attr_groups'][$result['attribute_id']] = $result['name'];

$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors " . $filter_supplier . " ORDER BY name ASC");
foreach ($query->rows as $result) {
  // ЗАМЕНИТЬ НА КОЛ-ВО БРЕНДОВ!
  $data['suppliers'][$result['session_key']]['vendors'][$result['id']] = 1;  
  }

$ask = array ('sort' => 'name');
if (isset($data['module']['do_category_sort'])) $category_info = $this->model_catalog_category->getCategories($ask);
else                                            $category_info = $this->model_catalog_category->getCategories(0);

$data['all_categories']   = array();
if(!isset($data['module']['do_category_autocomplete'])) {
  $data['all_categories'][] = array ('category_id' => 0, 'name' => 'Категория отключена');
  foreach ($category_info as $result) $data['all_categories'][] = array ('category_id' => $result['category_id'], 'name' => $result['name']); 
  }
else {
  $data['all_categories'][0] ='Категория отключена';
  foreach ($category_info as $result) $data['all_categories'][$result['category_id']] = $result['name']; 
  }
$data['all_categories'] = json_encode ($data['all_categories']);

$this->load->model('localisation/weight_class');
$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
$this->load->model('localisation/length_class');
$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
$this->load->model('localisation/tax_class');
$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories " . $filter_supplier . " ORDER by parent,name ASC");
foreach ($query->rows as $result) {
  if (!isset($data['module']['do_category_empty'])&&!$result['total']) continue;
  $data['suppliers'][$result['session_key']]['categories'][$result['id']] = array(
  			'id'               => $result['id'],
        );
  }

$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options" . $filter_supplier);
foreach ($query->rows as $result) {
  $data['suppliers'][$result['session_key']]['options'][$result['id']] = array(
  			'id'               => $result['id'],
  			'name'             => $result['name'],
  			'dest_type'        => $result['dest_type'],
  			'dest_id'          => $result['dest_id'],
        );
  }

$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_replace " . $filter_supplier . " ORDER by sort_order ASC");
foreach ($query->rows as $result) {
  $data['suppliers'][$result['session_key']]['replace'][$result['id']] = array(
  			'id'               => $result['id'],
  			'sort_order'       => $result['sort_order'],
  			'type'             => $result['type'],
        'mode'             => $result['mode']?$result['mode']:'replace',
  			'txt_before'       => $result['txt_before'],
  			'txt_after'        => $result['txt_after'],
        );
  }

// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 1 HOUR)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 2 HOUR)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 6 HOUR)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 12 HOUR)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 1 DAY)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 2 DAY)
// DELETE FROM zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL 3 DAY)

if ($data['module']['kill_log'] != 'nop') {
  $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_log WHERE time < DATE_SUB(NOW(), INTERVAL " . $data['module']['kill_log'] . ")");
  }

if (isset($data['module']['load_end_log'])) $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_log WHERE type='end' ORDER by id DESC");
else                                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_log " . $filter_supplier . " ORDER by id DESC");
foreach ($query->rows as $result) {
  if ($result['session_key']=='0' || $result['session_key']=='') {
    $data['system_log'][$result['id']] = array(
  			'type'             => $result['type'],
  			'data'             => $result['data'],
  			'time'             => $result['time'],
  			'user'             => $result['user'],
        );
    }
  else {
    $data['suppliers'][$result['session_key']]['log'][$result['id']] = array(
  			'type'             => $result['type'],
  			'data'             => $result['data'],
  			'time'             => $result['time'],
  			'user'             => $result['user'],
        );
    }
  }

if (isset($data['module']['load_event_log'])) {
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_events " . $filter_supplier . " ORDER by id DESC");
  foreach ($query->rows as $result) {
    $data['suppliers'][$result['session_key']]['events'][$result['id']] = array(
    			'type'             => $result['type'],
    			'data'             => $result['data'],
    			'time'             => $result['time'],
          );
    }
  }
unset ($data['suppliers'][0]);
if (isset($data['filter_supplier'])) {
  foreach ($data['suppliers'] as $key => $result) {
    if ($key!==$data['filter_supplier']) unset ($data['suppliers'][$key]);
    }
  }
if (!count($data['suppliers'])) {
  $data['all_categories'] = json_encode (array());
  $data['all_options']        = array();
  $data['attr_groups']        = array();
  }
// -----------------------------------------------------------------------------
	$data['header'] = $this->load->controller('common/header');
  if (!isset($data['module']['hide_system_menu'])) $data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');

  $this->registry->get('config')->set('template_engine', 'Template');
  $this->response->setOutput($this->load->view('extension/module/zoxml2', $data));
	}

  public function loadvendorpage() {
    $json = array ();
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if (isset($this->request->post['supplier'])&&isset($this->request->post['page'])) {

        $json['manufacturers']      = array();
        $json['manufacturers'][]    = array (
          'id'    => 0,
          'name'  => 'Производитель отключен',
          );
        $manufacturer_info     = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer ORDER BY name ASC");
        foreach ($manufacturer_info->rows as $result) {
          $json['manufacturers'][]   = array (
            'id'    => $result['manufacturer_id'],
            'name'  => $result['name'],
            );
          }

        $json['vendor']      = array();

        $conditions = " WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' ORDER by name ASC ";
        if (isset($this->request->post['limit']))               $conditions .= " LIMIT " . (int)$this->request->post['page']*100 . ", 100 ";                                    
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors" . $conditions);
        foreach ($query->rows as $result) {
          $json['vendor'][] = array(
          			'id'               => $result['id'],
          			'name'             => addslashes($result['name']),
          			'margin'           => $result['margin'],
          			'manufacturer_id'  => $result['manufacturer_id'],
          			'total'            => $result['total'],
                );
          }
        }
      }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
  public function loadcategorypage() {
    $json = array ();
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if (isset($this->request->post['supplier'])&&isset($this->request->post['page'])) {
        $conditions = " WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' ";
        if (empty($this->request->post['do_category_empty']))   $conditions .= " AND total!='0' ";
                                                                $conditions .= " ORDER by parent,name ASC ";
        if (isset($this->request->post['limit']))               $conditions .= " LIMIT " . (int)$this->request->post['page']*100 . ", 100 ";                                    
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories" . $conditions);
        foreach ($query->rows as $result) {
          $json[] = array(
          			'id'               => $result['id'],
          			'name'             => addslashes ($result['name']),
          			'margin'           => $result['margin'],
          			'parent'           => addslashes ($result['path']?$result['path']:$result['parent']),
          			'category_id'      => $result['category_id'],
          			'total'            => $result['total'],
          			'data'             => $result['data'],
                );
          }
        }
      }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
  public function progress() {
    $json = array ();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])&&isset($this->request->post['start_id'])) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_log WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' AND id > '" . (int)$this->request->post['start_id'] . "' ORDER by id ASC");
        foreach ($query->rows as $result) {
          $json[$result['id']] = array(
          			'id'               => $result['id'],
          			'type'             => $result['type'],
          			'data'             => $result['data'],
          			'time'             => $result['time'],
          			'user'             => $result['user'],
                );
          }
        }
      }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
  public function delrecord() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['id'])) {
        if ($this->request->post['id']=='all') $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_log");
        else $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_log WHERE `id` = '" . $this->db->escape($this->request->post['id']) . "'");
        }
      }
  }

  public function delrecords() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        if ($this->request->post['supplier']=='all') $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_log");
        else $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_log WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        }
      }
  }

  public function addfeed() {
    $mquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
    foreach ($mquery->rows as $result) $module[$result['key']] = json_decode ($result['data']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['add'])) {
        $action = array();
        foreach ($this->request->post['add'] as $extension => $add) {
          $action['module']   = $extension;
          $action['name']     = $add['name'];
          $action['url']      = $add['url'];
          $action['license']  = $add['key'];
          $action['user']     = $this->user->getUserName();
          }

    		$extensions  = $this->extensions(isset($module['SSL']));
        if (!isset($extensions[$action['module']])) {
          $err_txt = "Провайдер " . $action['module'] . " не найден!";
        	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = '" . $this->db->escape($err_txt) . "', user = '" . $this->db->escape($action['user']) . "'");
          }
        else {
          $action['run'] = $extensions[$action['module']]['add'];
          // RUN SCAN
        	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'info', data = 'Начало операции подключения поставщика', user = '" . $this->db->escape($action['user']) . "'");
          $ch = curl_init(); 
          curl_setopt($ch, CURLOPT_URL, $action['run']);
          curl_setopt($ch, CURLOPT_NOBODY, true); 
          curl_setopt($ch, CURLOPT_HEADER, false);
          curl_setopt($ch, CURLOPT_TIMEOUT, 59); 
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $action);
          curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 Firefox/35.0" );
          if (isset($module['SSL'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }
          if (defined('ZO_CURLOPT_FOLLOWLOCATION')) {
            curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt ( $ch, CURLOPT_POSTREDIR, 7 );
            }
        	$res = curl_exec($ch);
          if (isset($module['DEBUG'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("CURL - " . $action['run']) . "', user = '" . $this->db->escape($action['user']) . "'");
         	  $errNo = curl_errno($ch);
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("curl_errno - " . $errNo) . "', user = '" . $this->db->escape($action['user']) . "'");
          	$err   = curl_error($ch);
          $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("curl_error - " . $err) . "', user = '" . $this->db->escape($action['user']) . "'");
          	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("http_response_status - " . $http_response_status) . "', user = '" . $this->db->escape($action['user']) . "'");
            }
          curl_close ($ch);
          }
        }
		}
  }
    
  public function delfeed() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_suppliers  WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_vendors    WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_categories WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_options    WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_log        WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_events     WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_replace    WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        }
      }
  }

  public function delevent() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['id'])) {
        $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_events WHERE `id` = '" . $this->db->escape($this->request->post['id']) . "'");
        }
      }
  }

  public function delevents() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        if ($this->request->post['supplier']=='all') $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_events");
        else $query = $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_events WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        }
      }
  }

  public function addreplace() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])&&
          isset($this->request->post['txt_before'])&&
          isset($this->request->post['txt_after'])&&
          isset($this->request->post['type'])&&
          isset($this->request->post['mode'])&&
          isset($this->request->post['sort_order'])) 
          {
  			  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_replace SET   `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "', type = '" . $this->db->escape($this->request->post['type']) . "', mode = '" . $this->db->escape($this->request->post['mode']) . "', txt_before = '" . $this->db->escape($this->request->post['txt_before']) . "', txt_after = '" . $this->db->escape($this->request->post['txt_after']) . "', sort_order = '" . (int)$this->request->post['sort_order'] . "'");
        }
      }
    }

  public function delreplace() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['id'])) {
  		  $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_replace  WHERE `id` = '" . $this->db->escape($this->request->post['id']) . "'");
        }
      }
    }

  public function delreplaces() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
  		  $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_replace  WHERE `session_key` = '" . $this->db->escape($this->request->post['supplier']) . "'");
        }
      }
    }

  public function settings() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        foreach ($this->request->post['supplier'] as $session_key => $supplier) {
          // Настройки
          $this->db->query("DELETE FROM " . DB_PREFIX . "zoxml2_suppliers WHERE `session_key` = '" . $this->db->escape($session_key) . "'");
  			  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_suppliers SET   `session_key` = '" . $this->db->escape($session_key) . "', `data` = '" . $this->db->escape(json_encode($supplier['settings'])) . "'");
          // Производители
          if(isset($supplier['vendors'])) foreach ($supplier['vendors'] as $id => $result) {
    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET   `manufacturer_id` = '" . $this->db->escape($result['manufacturer_id']) . "', `margin` = '" . $this->db->escape($result['margin']) . "' WHERE `id` = '" . $this->db->escape($id) . "'");
            }
          // Категории
          if(isset($supplier['categories'])) foreach ($supplier['categories'] as $id => $result) {
    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET   `category_id` = '" . $this->db->escape($result['category_id']) . "', `margin` = '" . $this->db->escape($result['margin']) . "'  WHERE `id` = '" . $this->db->escape($id) . "'");
            }
          // Подстановки
          if(isset($supplier['replace'])) foreach ($supplier['replace'] as $id => $result) {
    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_replace SET   txt_before = '" . $this->db->escape($result['txt_before']) . "', txt_after = '" . $this->db->escape($result['txt_after']) . "', type = '" . $this->db->escape($result['type']) . "', mode = '" . $this->db->escape($result['mode']) . "', sort_order = '" . (int)$result['sort_order'] . "' WHERE `session_key` = '" . $this->db->escape($session_key) . "' AND  `id` = '" . $this->db->escape($id) . "'");
            }
          // Опции
          if(isset($supplier['options'])) foreach ($supplier['options'] as $id => $result) {
    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_options SET   `dest_type` = '" . $this->db->escape($result['dest_type']) . "', `dest_id` = '" . $this->db->escape($result['dest_id']) . "' WHERE `id` = '" . $this->db->escape($id) . "'");
            }
          }
        }
		  }
    else { // СООБЩЕНИЕ В СИСТЕМНЫЙ ЛОГ О НАРУШЕНИИ ПРАВ
      foreach ($this->request->post['supplier'] as $session_key => $supplier) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = 'У вас нет прав на изменение настроек!', user = '" . $this->db->escape($this->request->post['user']) . "'");
        }
      }
  }

  public function module() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['module'])) {
        $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_module");
        foreach ($this->request->post['module'] as $key => $data) {
  			  $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_module SET   `key` = '" . $this->db->escape($key) . "', `data` = '" . $this->db->escape(json_encode($data)) . "'");
          }
        }
		  }
    else { // СООБЩЕНИЕ В СИСТЕМНЫЙ ЛОГ О НАРУШЕНИИ ПРАВ
      $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'error', data = 'У вас нет прав на изменение настроек!', user = '-=SYSTEM=-'");
      }
  }

  public function install() {
    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` WHERE field='supplier' ");
    if (!$query->num_rows) $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD  `supplier` VARCHAR(63) NOT NULL");

  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_module     (id INT(11) AUTO_INCREMENT, `key` VARCHAR(63)       NOT NULL, data    TEXT NOT NULL,                                                                                                                               PRIMARY KEY (id)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_suppliers  (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, data    TEXT NOT NULL,                                                                                                                               PRIMARY KEY (id)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_vendors    (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, name    VARCHAR(127) NOT NULL,   manufacturer_id INT(11) NOT NULL, total INT(11) NOT NULL, margin TEXT NOT NULL,                                     PRIMARY KEY (id), KEY  `session_key` (`session_key`), KEY  `name` (`name`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_categories (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, data    VARCHAR(127) NOT NULL, name VARCHAR(511) NOT NULL,   parent    VARCHAR(127) NOT NULL,   category_id     INT(11) NOT NULL, total INT(11) NOT NULL, PRIMARY KEY (id), KEY  `session_key` (`session_key`), KEY  `name` (`name`), KEY  `parent` (`parent`), KEY  `data` (`data`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_options    (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, data    TEXT NOT NULL,    name VARCHAR(127) NOT NULL,   dest_type VARCHAR(31)  NOT NULL,   dest_id         INT(11) NOT NULL,                         PRIMARY KEY (id), KEY  `session_key` (`session_key`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_log        (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, data    TEXT NOT NULL,    type VARCHAR(15)  NOT NULL,   time TIMESTAMP NOT NULL,       user VARCHAR(31),                                             PRIMARY KEY (id), KEY  `session_key` (`session_key`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_events     (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, data    TEXT NOT NULL,    type VARCHAR(15)  NOT NULL,   time TIMESTAMP NOT NULL,                                                                     PRIMARY KEY (id), KEY  `session_key` (`session_key`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "zoxml2_replace    (id INT(11) AUTO_INCREMENT, session_key VARCHAR(63) NOT NULL, type VARCHAR(63) NOT NULL, mode VARCHAR(63) NOT NULL, txt_before  TEXT NOT NULL, sort_order INT(11)  NOT NULL,   txt_after  TEXT NOT NULL,                                      PRIMARY KEY (id), KEY  `session_key` (`session_key`)) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8");
  	$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_log");
  	$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "zoxml2_events");
    // -- Регистрация модуля в системе --
    $data = "module=Диспетчер XML v. 3.0";
    $ch = curl_init(); 
    $data.= "&mail=";
    $data.= $this->config->get('config_email');
    curl_setopt($ch, CURLOPT_URL, "http://igg-eco.ru/index.php?route=module/igglic"); 
    $data.= "&store=";
    $data.= $this->config->get('config_name');
    curl_setopt($ch, CURLOPT_POST, 1);
    $data.= "&www=";
    $data.= $_SERVER['SERVER_NAME'];
    $data.= "&ip=";
    $data.= $_SERVER['SERVER_ADDR'];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data ); curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); curl_exec ($ch); curl_close ($ch);    
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/zoxml2')) $this->error['warning'] = $this->language->get('error_permission');
		return !$this->error;
	}

	protected function extensions($use_ssl) {
    if ($use_ssl) $catalog = HTTPS_CATALOG;
    else          $catalog = HTTP_CATALOG;
		$extensions = array();
		$files = glob(DIR_CATALOG . 'controller/zoxml2/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('zoxml2/' . $extension);
				$extensions[$extension] = array(
					'module'               => $extension,
					'name'                 => $this->language->get('heading_title'),
					'need_path'            => $this->language->get('need_path'),
					'need_key'             => $this->language->get('need_key'),
					'disabled'             => $this->language->get('disabled'),
					'can_do_link'          => $this->language->get('can_do_link'),
					'path'                 => $catalog . 'index.php?route=zoxml2/' . $extension,
					'add'                  => $catalog . 'index.php?route=zoxml2/' . $extension . '/add',
					'scan'                 => $catalog . 'index.php?route=zoxml2/' . $extension . '/scan',
					'load'                 => $catalog . 'index.php?route=zoxml2/' . $extension . '/load',
					'link'                 => $catalog . 'index.php?route=zoxml2/' . $extension . '/link',
				);
			}
		}
    return $extensions;
  }

  public function scansupplier () {
    $mquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
    foreach ($mquery->rows as $result) $module[$result['key']] = json_decode ($result['data']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_suppliers WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "'");
        if ($query->row) {
          $value  = json_decode ($query->row['data']);
          $action = array(
          			'module'       => $value->module,
          			'name'         => $value->name,
          			'user'         => $this->user->getUserName(),
          			'url'          => $value->url,
          			'license'      => $value->license,
          			'session_key'  => $query->row['session_key'],
                );
      		$extensions  = $this->extensions(isset($module['SSL']));
          if (!isset($extensions[$action['module']])) {
            $err_txt = "Провайдер " . $action['module'] . " не найден!";
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'error', data = '" . $this->db->escape($err_txt) . "', user = '" . $this->db->escape($action['user']) . "'");
            }
          else {
            $action['run'] = $extensions[$action['module']]['scan'];
            // RUN SCAN
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'info', data = 'Начало операции загрузки данных', user = '" . $this->db->escape($action['user']) . "'");
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $action['run']);
            curl_setopt($ch, CURLOPT_NOBODY, true); 
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, isset($module['progress'])?5:10000); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $action);
            curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 Firefox/35.0" );
            if (isset($module['SSL'])) {
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
              }
            if (defined('ZO_CURLOPT_FOLLOWLOCATION')) {
              curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
              curl_setopt ( $ch, CURLOPT_POSTREDIR, 7 );
              }

            $res = curl_exec($ch);
            if (isset($module['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("CURL - " . $action['run']) . "', user = '" . $this->db->escape($action['user']) . "'");
            	$errNo = curl_errno($ch);
            	$err   = curl_error($ch);
            	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'debug', data = '" . $this->db->escape("http_response_status - " . $http_response_status) . "', user = '" . $this->db->escape($action['user']) . "'");
              }
            curl_close ($ch);
            }
          }
        }
		}
  }

  public function linksupplier () {
    $mquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
    foreach ($mquery->rows as $result) $module[$result['key']] = json_decode ($result['data']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_suppliers WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "'");
        if ($query->row) {
          $value  = json_decode ($query->row['data']);
          $action = array(
          			'module'       => $value->module,
          			'name'         => $value->name,
          			'user'         => $this->user->getUserName(),
          			'url'          => $value->url,
          			'license'      => $value->license,
          			'session_key'  => $query->row['session_key'],
                );
      		$extensions  = $this->extensions(isset($module['SSL']));
          if (!isset($extensions[$action['module']])) {
            $err_txt = "Провайдер " . $action['module'] . " не найден!";
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'error', data = '" . $this->db->escape($err_txt) . "', user = '" . $this->db->escape($action['user']) . "'");
            }
          else {
            $action['run'] = $extensions[$action['module']]['link'];
            // RUN SCAN
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'info', data = 'Начало операции привязки', user = '" . $this->db->escape($action['user']) . "'");
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $action['run']);
            curl_setopt($ch, CURLOPT_NOBODY, true); 
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, isset($module['progress'])?5:10000); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $action);
            curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 Firefox/35.0" );
            if (isset($module['SSL'])) {
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
              }
            if (defined('ZO_CURLOPT_FOLLOWLOCATION')) {
              curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
              curl_setopt ( $ch, CURLOPT_POSTREDIR, 7 );
              }

          	$res = curl_exec($ch);
            if (isset($module['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '0', type = 'debug', data = '" . $this->db->escape("CURL - " . $action['run']) . "', user = '" . $this->db->escape($action['user']) . "'");
            	$errNo = curl_errno($ch);
            	$err   = curl_error($ch);
            	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'debug', data = '" . $this->db->escape("http_response_status - " . $http_response_status) . "', user = '" . $this->db->escape($action['user']) . "'");
              }
            curl_close ($ch);
            }
          }
        }
		}
  }

  public function addallvendors () {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $manufacturer_description = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "manufacturer_description'");
        if ($query->row) $manufacturer_description = TRUE;

        $do_vendor_seourl         = false;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
        foreach ($query->rows as $result) {
          if ($result['key']=='do_vendor_seourl')         $do_vendor_seourl         = true;
          }
      
        $manufacturers = array ();
        $descriptions  = array ();
        $manufacturer_info     = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer ORDER BY name ASC");
        foreach ($manufacturer_info->rows as $result) $manufacturers[$result['manufacturer_id']] = $result['name'];

        $field_name       = false;
        $field_meta_title = false;
        $field_meta_h1    = false;
        if ($manufacturer_description) {
          $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "manufacturer_description` WHERE field='name' ");
      		if ($query->num_rows) $field_name       = true;
          $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "manufacturer_description` WHERE field='meta_title' ");
      		if ($query->num_rows) $field_meta_title = true;
          $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "manufacturer_description` WHERE field='meta_h1' ");
      		if ($query->num_rows) $field_meta_h1    = true;


          $manufacturer_info     = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_description");
          foreach ($manufacturer_info->rows as $result) $descriptions[$result['manufacturer_id']] = true;
          }
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_vendors WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "'");
        foreach ($query->rows as $result) {
          $name = $result['name']; 
          if ($name!='host' && $result['manufacturer_id']<1) {
            $extra = '';
            if ($field_name)        $extra .= "name       = '" . $this->db->escape($name)      . "', "; 
            if ($field_meta_title)  $extra .= "meta_title = '" . $this->db->escape($name)      . "', "; 
            if ($field_meta_h1)     $extra .= "meta_h1    = '" . $this->db->escape($name)      . "', "; 
            $manufacturer_id = array_search($result['name'],$manufacturers);
            if ($manufacturer_id===FALSE) {
          		$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($name) . "', sort_order = '0'");
          		$manufacturer_id = $this->db->getLastId();
        	    $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '0'");
              if ($manufacturer_description) {
		            $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET  " . $extra . "  manufacturer_id = '" . (int)$manufacturer_id . "', language_id = '" . (int)$this->config->get( 'config_language_id' ) . "'");
                }
              if ($do_vendor_seourl) {
                $vendor_seourl = $this->translit ($name);
			          $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($vendor_seourl) . "'");
                }
              $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET `manufacturer_id` = '" . (int)$manufacturer_id . "' WHERE `id` = '" . (int)$result['id'] . "'");
              }
            else {
              $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET `manufacturer_id` = '" . (int)$manufacturer_id . "' WHERE `id` = '" . (int)$result['id'] . "'");
              }
            }
          }
        }
		  $this->cache->delete('manufacturer');
		}
  }

public function docategoryseourl () {
  if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		$query = $this->db->query("SELECT category_id,name FROM " . DB_PREFIX . "category_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		foreach ($query->rows as $result) {
      $entry = 'category_id=' . (int)$result['category_id'];
		  $url_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($entry) . "'");
      if (empty($url_query->row['keyword'])) {
		    $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape($entry) . "'");
		    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '0', language_id = '" . (int)$this->config->get('config_language_id') . "', query = '" . $this->db->escape($entry) . "', keyword = '" . $this->db->escape($this->translit ($result['name'])) . "'");
        }
      }
    }  
	$this->cache->delete('category');
  }

  public function killcategories () {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET `category_id` = '0' WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "'");
        }
      }
    }

  public function lostcategories () {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' ORDER by parent,name ASC");
        foreach ($query->rows as $result) {
          $category_id = $result['category_id'];
          if ($category_id>0) { // ПРОВЕРЯЕМ СУЩЕСТВОВАНИЕ!
            $query2 = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
            if (!$query2->row) {
              $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET `category_id` = '0' WHERE `id` = '" . (int)$result['id'] . "'");
              }
            }
          }
        }
      }
    }

  protected function newCategory ($session_key,$name,$path,$do_category_seourl,$data) {
    // Создание категории
    $data['top']        = $data['parent_id']?0:1;
    $data['column']     = 1;
    $data['sort_order'] = 0;
    $data['status']     = 1;
    $data['image']      = '';
    $data['category_store'] = array (0);
    if ($do_category_seourl) $data['keyword'] = $this->translit ($name);
    $data['category_description'] = array ();
    $data['category_description'][$this->config->get( 'config_language_id' )] = array (
      'name'              => $name,
      'description'       => '',
      'meta_title'        => '',
      'meta_description'  => '',
      'meta_keyword'      => '',
      'meta_h1'           => '',
      'seo_title'         => '',
      'seo_h1'            => '',
      );
    
    $category_id =  $this->model_catalog_category->addCategory($data);
		$this->db->query("UPDATE " . DB_PREFIX . "category SET zo_path = '" . $this->db->escape($path) . "', date_modified = NOW() WHERE category_id = '" . (int)$category_id . "'");
    return $category_id;
    }
  protected function addCategory ($session_key,$name,$path,$do_category_seourl,$do_category_unique,$parent_id=0) {
    if ($name=='(категория не указана)') return 0;
    if ($do_category_unique) $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE name = '" . $this->db->escape($name) . "'");
    else                     $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.zo_path = '" . $this->db->escape($path) . "' AND cd.name = '" . $this->db->escape($name) . "'");
    if ($query->row) return $query->row['category_id'];

    $data = array();
    $data['parent_id'] = $parent_id;
    // Создание категории
    return $this->newCategory ($session_key,$name,$path,$do_category_seourl,$data);
    }

  protected function childrenCategories ($session_key,$parent_code,$path,$do_category_seourl,$do_category_unique,$parent_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' AND parent_code = '" . $this->db->escape($parent_code) . "'");
    if (!$query->num_rows) return;  
      // Создание дочерних категорий
    foreach ($query->rows as $result) {
      $category_id = $this->addCategory ($session_key,$result['name'],$path . "/" .$result['name'],$do_category_seourl,$do_category_unique,$parent_id);
      $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET `category_id` = '" . (int)$category_id . "' WHERE `id` = '" . (int)$result['id'] . "'");
      if ($result['data']) $this->childrenCategories ($session_key,$result['data'],$path . "/" .$result['name'],$do_category_seourl,$do_category_unique,$category_id);
      }
    }

  public function addallcategories () {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $do_category_seourl         = false;
        $do_category_unique         = false;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
        foreach ($query->rows as $result) {
          if ($result['key']=='do_category_seourl') $do_category_seourl = true;
          if ($result['key']=='do_category_unique') $do_category_unique = true;
          }
        $this->load->model('catalog/category');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_categories WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "' ORDER by parent,name ASC");
        // КАТЕГОРИИ 1-го УРОВНЯ
        foreach ($query->rows as $result) {
          $category_id = $result['category_id'];
          if ($category_id>0) { // ПРОВЕРЯЕМ СУЩЕСТВОВАНИЕ!
            $query2 = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
            if (!$query2->row) $category_id = 0;
            }
          if ($result['parent']) continue; // ТОЛЬКО 1-й УРОВЕНЬ!
          if ($category_id<1) {
            // У КОРНЕВЫХ PATH ПУСТОЙ! 
            $category_id = $this->addCategory ($this->request->post['supplier'],$result['name'],'',$do_category_seourl,$do_category_unique);
            $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET `category_id` = '" . (int)$category_id . "' WHERE `id` = '" . (int)$result['id'] . "'");
            }
          // КОРНЕВАЯ ЕСТЬ - СОЗДАТЬ ВСЕХ ДЕТЕЙ
          if (empty($result['data'])) continue;
          // У ДОЧЕРНИХ PATH НАЗВАНИЕ РОДИТЕЛЯ 
          $this->childrenCategories ($this->db->escape($this->request->post['supplier']),$result['data'],$result['name'],$do_category_seourl,$do_category_unique,$category_id);
          }
		    $this->cache->delete('category');
        }
      }
  }

  public function loadsupplier () {
    $mquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_module");
    foreach ($mquery->rows as $result) $module[$result['key']] = json_decode ($result['data']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_suppliers WHERE session_key = '" . $this->db->escape($this->request->post['supplier']) . "'");
        if ($query->row) {
          $value  = json_decode ($query->row['data']);
          $action = array(
          			'module'       => $value->module,
          			'name'         => $value->name,
          			'user'         => $this->user->getUserName() . " (" . $_SERVER['REMOTE_ADDR'] . ")",
          			'url'          => $value->url,
          			'license'      => $value->license,
          			'session_key'  => $query->row['session_key'],
                );
      		$extensions  = $this->extensions(isset($module['SSL']));
          if (!isset($extensions[$action['module']])) {
            $err_txt = "Провайдер " . $action['module'] . " не найден!";
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'error', data = '" . $this->db->escape($err_txt) . "', user = '" . $this->db->escape($action['user']) . "'");
            }
          else {
            $action['run'] = $extensions[$action['module']]['load'];
            // RUN LOAD
          	$this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'info', data = 'Начало операции загрузки данных', user = '" . $this->db->escape($action['user']) . "'");
            $log_id = $this->db->getLastId();
            ignore_user_abort(true);
            ini_set('max_execution_time', 0);
            $ch = curl_init(); 
            if (!empty($module['http_port'])) curl_setopt($ch, CURLOPT_PORT, (int)$module['http_port']); 
            curl_setopt($ch, CURLOPT_URL, $action['run']);
            curl_setopt($ch, CURLOPT_NOBODY, true); 
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, isset($module['progress'])?5:10000); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $action);
            curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 Firefox/35.0" );
            if (isset($module['SSL'])) {
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
              }
            if (defined('ZO_CURLOPT_FOLLOWLOCATION')) {
              curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
              curl_setopt ( $ch, CURLOPT_POSTREDIR, 7 );
              }
          	$res = curl_exec($ch);
            if (isset($module['DEBUG'])) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'debug', data = '" . $this->db->escape("CURL - " . $action['run']) . "', user = '" . $this->db->escape($action['user']) . "'");
            	$errNo = curl_errno($ch);
            	$err   = curl_error($ch);
            	$http_response_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($this->request->post['supplier']) . "', type = 'debug', data = '" . $this->db->escape("http_response_status - " . $http_response_status) . "', user = '" . $this->db->escape($action['user']) . "'");
              }
            }
          }
        }
		}
  }

public function translit($text)	{
		$ru  = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ы', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ы', 'Э', 'Ю', 'Я', ' ', '/', '.', '"', "'");
		$tr  = array('a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'ts', 'ch', 'sh', 'sch', 'y', 'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'TS', 'CH', 'SH', 'SCH', 'Y', 'E', 'YU', 'YA', '-', '-', '-', '', '');
		$str = strtolower (preg_replace('/[^A-Za-z0-9-_\/]+/', '', str_replace($ru, $tr, $text)));

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
public function settings2xml() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      if (isset($this->request->post['supplier'])) {
        foreach ($this->request->post['supplier'] as $session_key => $supplier) {
          return $this->export_settings2xml($session_key,$supplier);
          }
        }
      }
    else { // СООБЩЕНИЕ В СИСТЕМНЫЙ ЛОГ О НАРУШЕНИИ ПРАВ
      foreach ($this->request->post['supplier'] as $session_key => $supplier) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = 'У вас нет прав на изменение настроек!', user = '" . $this->db->escape($this->request->post['user']) . "'");
        }
      }
  }
public function export_settings2xml($session_key,$supplier) {
  $src  = "<?xml version='1.0' encoding='UTF-8' ?>\n<ZOXML2>\n<SupplierMainSettings>\n";

  $src .= "<settings>\n";
  foreach ($supplier['settings'] as $tag => $value) {
    $src .= "<" . $tag . ">" . $value . "</" . $tag . ">\n";
    }
  $src .= "</settings>\n";

  // Подстановки
  if(isset($supplier['replace'])) {
    $src .= "<replaces>\n";
    foreach ($supplier['replace'] as $result) {
      $src .= "<replace>\n";
      $src .= "<txt_before>" . $result['txt_before'] . "</txt_before>\n";
      $src .= "<txt_after>" . $result['txt_after'] . "</txt_after>\n";
      $src .= "<type>" . $result['type'] . "</type>\n";
      $src .= "<mode>" . $result['mode'] . "</mode>\n";
      $src .= "<sort_order>" . $result['sort_order'] . "</sort_order>\n";
      $src .= "</replace>\n";
      }
    $src .= "</replaces>\n";
    }
  // Опции
  $filter_supplier = " WHERE session_key='" . $this->db->escape($session_key) . "'";
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zoxml2_options" . $filter_supplier);
  if ($query->row) {
    $src .= "<options>\n";
    foreach ($query->rows as $result) {
      $src .= "<option"  . " name='" . $result['name']. "' data='" . $result['data']. "'>" . $result['dest_type'] . "</option>\n";
      }
    $src .= "</options>\n";
    }
           // Производители
          if(isset($supplier['vendors'])) foreach ($supplier['vendors'] as $id => $result) {
//    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_vendors SET   `manufacturer_id` = '" . $this->db->escape($result['manufacturer_id']) . "', `margin` = '" . $this->db->escape($result['margin']) . "' WHERE `id` = '" . $this->db->escape($id) . "'");
            }
          // Категории
          if(isset($supplier['categories'])) foreach ($supplier['categories'] as $id => $result) {
//    			  $this->db->query("UPDATE " . DB_PREFIX . "zoxml2_categories SET   `category_id` = '" . $this->db->escape($result['category_id']) . "', `margin` = '" . $this->db->escape($result['margin']) . "'  WHERE `id` = '" . $this->db->escape($id) . "'");
            }

  $src .= "</SupplierMainSettings>\n</ZOXML2>";

  $dest = DIR_CACHE . $session_key . "_backup.xml";
  if (file_put_contents ($dest, $src )===FALSE)   {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'error', data = '" . $this->db->escape('file_put_contents не смог сохранить XML-файл!') ."', user = 'export'");
    $dest = '';
    }
  else {
    $this->db->query("INSERT INTO " . DB_PREFIX . "zoxml2_log SET session_key = '" . $this->db->escape($session_key) . "', type = 'info', data = '" . $this->db->escape('Сохранено в файл: ' . $dest) ."', user = 'export'");
    }
  
	$this->response->addHeader('Content-Type: text/plain');
	$this->response->setOutput($dest);
  }

}
?>