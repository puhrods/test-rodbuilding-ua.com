<?php
namespace Seomanager;

class SeoUrl {
	private $registry;
	private $log = null;

	public function __construct($registry) {
		$this->registry = $registry;

		if ($this->config->get('slasoft_seo_manager_type') == 'URLify') {
			require_once(DIR_SYSTEM . 'library/vendor/urlify.php');
		} else {
			$this->slugify = new \Cocur\Slugify\Slugify(array(
				'separator' => $this->config->get('slasoft_seo_manager_separator')?$this->config->get('slasoft_seo_manager_separator'):'-',
				'strip_tags' => false,
				'lowercase' => true,
				'trim' => true,
			));
		}
		if ($this->config->get('slasoft_seo_manager_log')){
			$this->log = NEW \LOG('slasoft_seomanager.log');
		}
	}

	public function __get($name) {
		return $this->registry->get($name);
	}

	private function transliteration($keyword) {
		if ($this->config->get('slasoft_seo_manager_type') == 'URLify') {
			$keyword = \URLify::filter(
				$keyword,
				60,
				$this->language_code, 
				false, 
				true,
				true,
				true,
				$this->config->get('slasoft_seo_manager_separator')?$this->config->get('slasoft_seo_manager_separator'):'-'
			);
		} else {
			$keyword = $this->slugify->slugify($keyword);
		}
		return $keyword;
	}

	private function getUniqueSlugs($keyword, $store_id, $separator = '-') {
		$counter = 0;
		$k = $keyword;
		do {
			$query = $this->db->query("
			SELECT seo_url_id
			FROM " . DB_PREFIX . "seo_url 
			WHERE keyword ='" . $this->db->escape($keyword) . "'
			AND store_id = '" . (int)$store_id . "'");

			if($query->num_rows) {
				$keyword = $k . $separator . ++$counter;
			}
		} while($query->num_rows);

		return $keyword;
	}

	private function insertKeyword($data_insert) {
		if ($data_insert['keyword']) {
			$keyword = html_entity_decode($data_insert['keyword'], ENT_QUOTES, 'UTF-8');

			$keyword = $this->transliteration($keyword);

			$slugs = $this->getExistingSlugs($keyword);
			if ($slugs) {
				$keyword = $this->getUniqueSlugs($keyword , $data_insert['store_id'],$this->config->get('slasoft_seo_manager_separator'));
			}
			$data_insert['keyword'] = $keyword;
			$last_id = $this->SystemInsertKeyword($data_insert);
		}
	}

	private function getExistingSlugs($keyword) {
		$sql = "SELECT COUNT(*) as total 
		FROM " . DB_PREFIX . "seo_url 
		WHERE keyword = '" . $this->db->escape($keyword) . "'";
		$result = $this->db->query($sql);
		return (int)$result->row['total'];
	}

	private function SystemInsertKeyword($data_insert) {
		$sql  = "INSERT INTO " . DB_PREFIX . "seo_url SET ";
		$sql .= " query = '" . $this->db->escape($data_insert['query']) . "'";
		$sql .= ", keyword = '" . $this->db->escape($data_insert['keyword']) . "'";
		$sql .= ", store_id = '" . (int)$data_insert['store_id'] . "'";
		$sql .= ", language_id = '" . (int)$data_insert['language_id'] . "'";
		$this->db->query($sql);
		return $this->db->getLastId();
	}
	
	public function MainGenerate(){
		$query = $this->db->query("SELECT language_id, code FROM " . DB_PREFIX . "language WHERE status = '1'");
		$language_data =  array();
		foreach ($query->rows as $result) {
			$language_data[$result['code']] = array(
				'language_id' => $result['language_id'],
				'code'        => $result['code'],
			);
		}
		$store_data = array();
		$store_data[] = array('store_id'=>0);
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
		$store_data = array_merge($store_data,$query->rows);
		foreach ($language_data as $language) {
			foreach ($store_data as $store) {
				$this->ProductGenerate(array(
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id'],
					'language_from' => $language['language_id'],
					'code' => $language['code'],
				)
				);
				$this->CategoryGenerate(array(
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id'],
					'language_from' => $language['language_id'],
					'code' => $language['code'],
				)
				);
			}
		}
	}
	
	private function logger ($message){
		if ($this->log) {
			$this->log->write($message);
		}
	}

	private function ProductGenerate($data){
		$this->logger('Start product');
		$this->logger($data);
		$sql = "SELECT p.*, pd.name, (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = p.manufacturer_id) as m_name 
		FROM `" . DB_PREFIX . "product` p
		LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = p.product_id)";
		$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
			ua.query = CONCAT('product_id=',p.product_id) 
			AND ua.store_id = '" . (int)$data['store_id'] . "'
			AND ua.language_id = '" . (int)$data['language_id'] . "')";
		$sql .=	" WHERE pd.language_id = '" . (int)$data['language_from'] . "'";
		$sql .= " AND `seo_url_id`  IS NULL";
		$results = $this->db->query($sql);
		$config_pattern = $this->config->get('slasoft_seo_manager_product_patern');
		if (isset($config_pattern[$data['language_id']][$data['store_id']])) {
			$pattern = $config_pattern[$data['language_id']][$data['store_id']];
		} else {
			$pattern = '[name]';
		}
		$this->logger('pattern:' . $pattern);
		
		foreach($results->rows as $row) {
			$replaced = array(
				'[name]'      => $row['name'],
				'[model]'     => $row['model'],
				'[id]'        => $row['product_id'],
				'[mpn]'       => $row['mpn'],
				'[sku]'       => $row['sku'],
				'[isbn]'      => $row['isbn'],
				'[jan]'       => $row['jan'],
				'[m_name]'    => $row['m_name'],
				'[lang_code]' => $data['code'],
				'[lang_id]'   => $data['language_id'],
				'[store_id]'  => $data['store_id'],
			);
							
			$keyword = str_replace(array_keys($replaced),$replaced, $pattern);
			
			$data_insert = array(
				'query'       => 'product_id=' . (int)$row['product_id'], 
				'keyword'     => $keyword,
				'language_id' => $data['language_id'],
				'store_id'    => $data['store_id'],
				'push'        => false
			);
			$this->insertKeyword($data_insert);
		}
		$this->logger('Total product:' . count($results->rows));
		$this->logger('End Product');
	}

	private function CategoryGenerate($data){
		$this->logger('Start category');
		$this->logger($data);
		$sql = "SELECT c.*, cd.name
		FROM `" . DB_PREFIX . "category` c
		LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = c.category_id)";
		$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
			ua.query = CONCAT('category_id=',c.category_id) 
			AND ua.store_id = '" . (int)$data['store_id'] . "'
			AND ua.language_id = '" . (int)$data['language_id'] . "')";
		$sql .=	" WHERE cd.language_id = '" . (int)$data['language_from'] . "'";
		$sql .= " AND `seo_url_id`  IS NULL";
		$results = $this->db->query($sql);
		$config_pattern = $this->config->get('slasoft_seo_manager_category_patern');
		if (isset($config_pattern[$data['language_id']][$data['store_id']])) {
			$pattern = $config_pattern[$data['language_id']][$data['store_id']];
		} else {
			$pattern = '[name]';
		}
		$this->logger('pattern:' . $pattern);
		
		foreach($results->rows as $row) {
			$replaced = array(
				'[name]'      => $row['name'],
				'[id]'        => $row['category_id'],
				'[lang_code]' => $data['code'],
				'[lang_id]'   => $data['language_id'],
				'[store_id]'  => $data['store_id'],
			);
							
			$keyword = str_replace(array_keys($replaced),$replaced, $pattern);
			
			$data_insert = array(
				'query'       => 'category_id=' . (int)$row['category_id'], 
				'keyword'     => $keyword,
				'language_id' => $data['language_id'],
				'store_id'    => $data['store_id'],
				'push'        => false
			);
			$this->insertKeyword($data_insert);
		}
		$this->logger('Total caregory:' . count($results->rows));
		$this->logger('End Category');
	}
}

