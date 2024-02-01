<?php
class ModelExtensionModuleSlaSoftSeoManager extends Model {
	private $suffix = '';
	public function bulkCopy($data=array()) {
		$push = $insert_push = '';
		if ($this->checkPush()) {
			$push = ', su.push';
			$insert_push = ', push';
		}

		$sql = "INSERT INTO " . DB_PREFIX . "seo_url (query,keyword,language_id, store_id" . $insert_push . ")
			SELECT su.query,su.keyword,su.language_id, " . (int)$data['to_store'] . $push . " 
				FROM " . DB_PREFIX . "seo_url su 
				LEFT JOIN " . DB_PREFIX . "seo_url su1 ON(su1.query = su.query AND su1.language_id = su.language_id AND su1.store_id = " . (int)$data['to_store'] . ") 
				WHERE su.store_id = " . (int)$data['from_store'] . "
				AND su1.query IS NULL";

		if (isset($data['entity'])) {
			$sql .= " AND su.query LIKE '" . $this->db->escape($data['entity']) . "%'";
		}
		$this->db->query($sql);
		return $this->db->countAffected();
	}

	public function bulkLange($data=array()) {
		if (isset($data['to_language']) && isset($data['from_language']) && ($data['from_language'] != $data['to_language'])) {
			if (isset($data['rewrite'])) {
				$sql = "DELETE FROM " . DB_PREFIX . "seo_url WHERE language_id = " . (int)$data['to_language'];
				$this->db->query($sql);
			}
			$sql = "INSERT INTO " . DB_PREFIX . "seo_url (query,keyword,language_id, store_id)
				SELECT su.query,su.keyword," . (int)$data['to_language'] . ", su.store_id
					FROM " . DB_PREFIX . "seo_url su 
					LEFT JOIN " . DB_PREFIX . "seo_url su1 ON(su1.query = su.query AND su1.language_id = " . (int)$data['to_language'] . ") 
					WHERE su.language_id = " . (int)$data['from_language'] . "
					AND su1.query IS NULL";
	
			if (isset($data['entity'])) {
				$sql .= " AND su.query LIKE '" . $this->db->escape($data['entity']) . "%'";
			}
			$this->db->query($sql);
			return $this->db->countAffected();
		} 
		return 0;
	}

	public function checkPush() {
		$sql = "SHOW COLUMNS FROM " . DB_PREFIX . "seo_url LIKE 'push'";
		$result = $this->db->query($sql);
		return ($result->num_rows > 0);
	}

	public function checkBlog() {
		$sql = "SHOW TABLES LIKE '" . DB_PREFIX . "blog'";
		$result = $this->db->query($sql);
		return ($result->num_rows > 0);
	}

	
	public function getProduct($id) {
		$result = $this->db->query("SELECT pd.name FROM " . DB_PREFIX . "product p
		LEFT JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id
		WHERE p.product_id=" . (int)$id . "
		AND language_id=" . (int)$this->config->get('config_language_id'));
		if ($result->num_rows) {
			return $result->row['name'];
		} else {
			return false;
		}
	}
	
	public function getCategory($id) {
		$result = $this->db->query("SELECT pd.name FROM " . DB_PREFIX . "category p
		LEFT JOIN " . DB_PREFIX . "category_description pd ON p.category_id = pd.category_id
		WHERE p.category_id=" . (int)$id . "
		AND language_id=" . (int)$this->config->get('config_language_id'));
		if ($result->num_rows) {
			return $result->row['name'];
		} else {
			return false;
		}
	}
	
	public function getInformation($id) {
		$result = $this->db->query("SELECT pd.title as name FROM " . DB_PREFIX . "information p
		LEFT JOIN " . DB_PREFIX . "information_description pd ON p.information_id = pd.information_id
		WHERE p.information_id=" . (int)$id . "
		AND language_id=" . (int)$this->config->get('config_language_id'));
		if ($result->num_rows) {
			return $result->row['name'];
		} else {
			return false;
		}
	}
	
	public function getManufacturer($id) {
		$result = $this->db->query("SELECT p.name as name FROM " . DB_PREFIX . "manufacturer p
		WHERE p.manufacturer_id=" . (int)$id . "");
		if ($result->num_rows) {
			return $result->row['name'];
		} else {
			return false;
		}
	}
	
	
	public function categoryGetPath($category_id) {
		$sql = "SELECT GROUP_CONCAT(c1.category_id ORDER BY level SEPARATOR '_') path 
		FROM " . DB_PREFIX . "category_path cp 
		LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.path_id = c1.category_id) 
		WHERE cp.category_id = " . (int)$category_id . " 
		GROUP BY cp.category_id";
		$query = $this->db->query($sql);

		$path_category_id = (isset($query->row['path']) && $query->row['path']) ? $query->row['path'] : $category_id;
		return $path_category_id;
		
	}
	
	public function updateUrlAlias($data) {
		$sql_set = "`query` = '" . $this->db->escape($data['query']) . "', 
				`keyword` = '" . $this->db->escape($data['keyword']) . "',
				`store_id` = '" . (int)$data['store_id'] . "',
				`language_id` = '" . (int)$data['language_id'] . "'";

		if(!empty($data['seo_url_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET " . 
			$sql_set . "
				WHERE `seo_url_id` = '" . (int)$data['seo_url_id'] . "'");
			$last_id = $data['seo_url_id'];
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET ". 
			$sql_set);
			$last_id = $this->db->getLastId();
		}

		$this->cache->delete('seo_pro');
		$this->cache->delete('seo_url');

		return $last_id;
	}

	public function deleteUrlAlias($seo_url_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `seo_url_id` = '" . (int)$seo_url_id . "'");

		$this->cache->delete('seo_pro');
		$this->cache->delete('seo_url');
	}	

	public function getAlias($data = array()) {
		if ($data) {
			$result = $this->db->query("SELECT keyword FROM `" . DB_PREFIX . "seo_url` WHERE `keyword` = '" . $data['keyword'] . "'");
			return $result->rows;
		} else return array();
	}	
	
	public function getUrlAliases($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "seo_url` ua WHERE 1";

			if (isset($data['filter_keyword']) && $data['filter_keyword']) {
				$sql .= " AND `keyword` LIKE '%" . $this->db->escape($data['filter_keyword']) . "%'";
			}

			if (isset($data['filter_query']) && $data['filter_query']){
				$sql .= " AND query LIKE '%" . $this->db->escape($data['filter_query']) . "%'";
			}

			if (isset($data['filter_language_id']) && $data['filter_language_id']) {
				$sql .= " AND language_id ='" . (int)$data['filter_language_id'] . "'";
			}

			if (isset($data['filter_store_id']) && $data['filter_store_id']) {
				$sql .= " AND store_id ='" . (int)$data['filter_store_id'] . "'";
			}

			if (isset($data['filter_type']) && $data['filter_type']){
				switch ($data['filter_type']) {
						case 'product':
								$sql .= " AND query LIKE 'product_id=%'";
							break;
						case 'category':
								$sql .= " AND (query LIKE 'category_id=%' OR query LIKE 'path=%')";
							break;
						case 'information':
								$sql .= " AND query LIKE 'information_id=%'";
							break;
						case 'manufacturer':
								$sql .= " AND query LIKE 'manufacturer_id=%'";
							break;
						case 'other':
								$sql .= " AND (
								query NOT LIKE 'manufacturer_id=%' AND
								query NOT LIKE 'category_id=%' AND
								query NOT LIKE 'product_id=%' AND
								query NOT LIKE 'information_id=%'
								)";
							break;
				}
			}
			
			$sort_data = array(
				'ua.query', 
				'ua.keyword'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY ua.query";
			}

			if (isset($data['order']) && ($data['order'] == 'ASC')) {
				$sql .= " ASC";
			} else {
				$sql .= " DESC";
			}

			if (isset($data['start'])) {
				if ((int)$data['start'] < 0) {
					$data['start'] = 0;
				}
			} else {
				$data['start'] = 0;
			}

			if (isset($data['limit'])) {
				if ((int)$data['limit'] < 1) {
					$data['limit'] = 20;
				}
			} else {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

			$query = $this->db->query($sql);

			return  $query->rows;
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` ua ORDER BY ua.query");
			return $query->rows;
		}
	}

	// Total Aliases
	public function getTotalUrlAliases($data=array()) {
		
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seo_url` WHERE 1=1";

		if (isset($data['filter_keyword'])) {
			$sql .= " AND `keyword` LIKE '%" . $this->db->escape($data['filter_keyword']) . "%'";
		}

		if (isset($data['filter_query'])) {
			$sql .= " AND query LIKE '%" . $this->db->escape($data['filter_query']) . "%'";
		}

		if (isset($data['filter_language_id']) && $data['filter_language_id']){
			$sql .= " AND language_id ='" . (int)$data['filter_language_id'] . "'";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id']){
			$sql .= " AND store_id ='" . (int)$data['filter_store_id'] . "'";
		}

		if (isset($data['filter_type']) && $data['filter_type']){
			switch ($data['filter_type']) {
				case 'product':
						$sql .= " AND query LIKE 'product_id=%'";
					break;
				case 'category':
						$sql .= " AND query LIKE 'category_id=%'";
					break;
				case 'information':
						$sql .= " AND query LIKE 'information_id=%'";
					break;
				case 'manufacturer':
						$sql .= " AND query LIKE 'manufacturer_id=%'";
					break;
				case 'other':
						$sql .= " AND (
						query NOT LIKE 'manufacturer_id=%' AND
						query NOT LIKE 'category_id=%' AND
						query NOT LIKE 'product_id=%' AND
						query NOT LIKE 'information_id=%'
						)";
					break;
			}
		}
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function checkQuery($data) {
		$sql = "
		SELECT ua1.*
		FROM `" . DB_PREFIX . "seo_url` ua1
		JOIN `" . DB_PREFIX . "seo_url` ua2
		WHERE ua1.`query` = ua2.`query`
		AND ua1.seo_url_id <> ua2.seo_url_id

		AND ua1.store_id = ua2.store_id
		AND ua1.language_id = ua2.language_id
		ORDER BY ua1.`query`";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = (int)$this->config->get('config_limit_admin');
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function checkTotalQuery() {
		$sql = "
		SELECT COUNT(*) as total
		FROM `" . DB_PREFIX . "seo_url` ua1
		JOIN `" . DB_PREFIX . "seo_url` ua2
		WHERE ua1.`query` = ua2.`query`
		AND ua1.seo_url_id <> ua2.seo_url_id

		AND ua1.store_id = ua2.store_id
		AND ua1.language_id = ua2.language_id";

		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function checkKeyword($data) {
		$sql = "
		SELECT ua1.*
		FROM `" . DB_PREFIX . "seo_url` ua1
		JOIN `" . DB_PREFIX . "seo_url` ua2
		WHERE ua1.`keyword` = ua2.`keyword`
		AND ua1.seo_url_id <> ua2.seo_url_id
		AND ua1.`language_id` = ua2.`language_id`
		AND ua1.`store_id` = ua2.`store_id`
		ORDER BY ua1.`keyword`";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = (int)$this->config->get('config_limit_admin');
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function checkTotalKeyword() {
		$sql = "
		SELECT COUNT(*) as total
		FROM `" . DB_PREFIX . "seo_url` ua1
		JOIN `" . DB_PREFIX . "seo_url` ua2
		WHERE ua1.`keyword` = ua2.`keyword`
		AND ua1.seo_url_id <> ua2.seo_url_id
		AND ua1.`language_id` = ua2.`language_id`
		AND ua1.`store_id` = ua2.`store_id`";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	// Install/Uninstall
	public function install() {
	}
	
	public function installExtendedUrl() {
		$extendedUrls = array(
			'common/home' => '',
			'account/wishlist' => 'wishlist',
			'account/account' => 'my-account',
			'checkout/cart' => 'shopping-cart',
			'checkout/checkout' => 'checkout',
			'account/login' => 'login',
			'account/logout' => 'logout',
			'account/order' => 'order-history',
			'account/newsletter' => 'newsletter',
			'product/special' => 'specials',
			'affiliate/account' => 'affiliates',
			'checkout/voucher' => 'gift-vouchers',
			'product/manufacturer' => 'brands',
			'information/contact' => 'contact-us',
			'account/return/insert' => 'request-return',
			'information/sitemap' => 'sitemap',
			'account/forgotten' => 'forgot-password',
			'account/download' => 'downloads',
			'account/return' => 'returns',
			'account/transaction' => 'transactions',
			'account/register' => 'create-account',
			'product/compare' => 'compare-products',
			'product/search' => 'search',
			'account/edit' => 'edit-account',
			'account/password' => 'change-password',
			'account/address' => 'address-book',
			'account/voucher' => 'vouchers',
			'account/reward' => 'reward-points',
			'affiliate/edit' => 'edit-affiliate-account',
			'affiliate/password' => 'change-affiliate-password',
			'affiliate/payment' => 'affiliate-payment-options',
			'affiliate/tracking' => 'affiliate-tracking-code',
			'affiliate/transaction' => 'affiliate-transactions',
			'affiliate/logout' => 'affiliate-logout',
			'affiliate/forgotten' => 'affiliate-forgot-password',
			'affiliate/register' => 'create-affiliate-account',
			'affiliate/login' => 'affiliate-login',
		);
		
		foreach ($extendedUrls as $key=>$url) {
			$sql = "DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = '" . $key . "'";
			$result = $this->db->query($_sql);
			$sql = "INSERT INTO `" . DB_PREFIX . "seo_url` SET `query` = '" . $key . "', `keyword` = '" . $url . "'";
			$result = $this->db->query($_sql);
		}
		$this->cache->delete('seo_pro');
		$this->cache->delete('seo_url');
		return true;
	}

	public function uninstall() {
		return true;
	}

	public function categoryWithoutUrl($data) {
		$sql ="SELECT cd.category_id id, cd.name FROM `" . DB_PREFIX . "category` c
				LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = c.category_id)
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('category_id=',c.category_id) AND cd.language_id = ua.language_id
				WHERE  `seo_url_id`  IS NULL";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function categoryWithoutUrlTotal($data) {
		$sql ="SELECT COUNT(*) total FROM `" . DB_PREFIX . "category` c
				LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = c.category_id)
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('category_id=',c.category_id)
					AND cd.language_id = ua.language_id
				WHERE `seo_url_id`  IS NULL";
		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function productWithoutUrl($data) {
		$sql ="SELECT cd.product_id id, cd.name FROM `" . DB_PREFIX . "product` c
				LEFT JOIN `" . DB_PREFIX . "product_description` cd ON (cd.product_id = c.product_id)
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('product_id=',c.product_id) AND cd.language_id = ua.language_id
				WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND `seo_url_id`  IS NULL";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function productWithoutUrlTotal($data) {
		$sql ="SELECT COUNT(*) total FROM `" . DB_PREFIX . "product` c
				LEFT JOIN `" . DB_PREFIX . "product_description` cd ON (cd.product_id = c.product_id)
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('product_id=',c.product_id) AND cd.language_id = ua.language_id
				WHERE `seo_url_id`  IS NULL";
		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function informationWithoutUrl($data) {
		$sql ="SELECT cd.information_id id, cd.title as name FROM `" . DB_PREFIX . "information` c
				LEFT JOIN `" . DB_PREFIX . "information_description` cd ON (cd.information_id = c.information_id)
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('information_id=',c.information_id)
				WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND `seo_url_id`  IS NULL";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function informationWithoutUrlTotal($data) {
		$sql ="SELECT COUNT(*) total FROM `" . DB_PREFIX . "information` c
				LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('information_id=',c.information_id)
				WHERE `seo_url_id`  IS NULL";
		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function productWithoutCategoryTotal($data) {
		$sql ="SELECT COUNT(*) total FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id
				WHERE p2c.product_id  IS NULL";
		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function productWithoutCategory($data) {
		$sql ="SELECT p.product_id id, pd.name FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id
				LEFT JOIN `" . DB_PREFIX . "product_description` pd ON pd.product_id = p.product_id
				WHERE pd.language_id = " . $this->config->get('config_language_id') . " AND p2c.product_id  IS NULL";
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function productWithoutCategoryMainTotal($data) {
		$sql = "SHOW COLUMNS FROM " . DB_PREFIX . "product_to_category LIKE 'main_category'"; 
		$result = $this->db->query($sql);
		if (!$result->num_rows) return false;
		
		$sql ="SELECT COUNT(*) total FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id
				WHERE p2c.product_id  IS NULL";
		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function productWithoutCategoryMain($data) {
		$sql = "SHOW COLUMNS FROM " . DB_PREFIX . "product_to_category  LIKE 'main_category'"; 
		$result = $this->db->query($sql);
		if (!$result->num_rows) return false;
		
		$sql ="SELECT p.product_id id, pd.name FROM `" . DB_PREFIX . "product` p
				LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id
				LEFT JOIN `" . DB_PREFIX . "product_description` pd ON pd.product_id = p.product_id
				WHERE pd.language_id = " . $this->config->get('config_language_id') . " AND p2c.product_id  IS NULL";
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function deleteSeoUrl($data=array()) {
		$sql = "DELETE FROM " .DB_PREFIX . "seo_url WHERE 
			query LIKE '" . $this->db->escape($data['query']) . "=%'
			AND language_id = '" . (int)$data['language_id'] . "'
			AND store_id = '" . (int)$data['store_id'] . "'";

		$result = $this->db->query($sql);
		
	}

	public function getUniqueSlugs($keyword, $data_insert, $separator = '-') {
		$counter = 0;
		$k = $keyword;
		
		do {
			$query = $this->db->query("
			SELECT seo_url_id
			FROM " . DB_PREFIX . "seo_url 
			WHERE keyword ='" . $this->db->escape($keyword) . "'
			AND store_id = '" . (int)$data_insert['store_id'] . "'");
			if($query->num_rows) {
				$mode = $this->config->get('slasoft_seo_manager_dublicate');
				if ($mode == 'random_number') {
					$suffix = rand(1000000,999999);
				} elseif ($mode == 'random_symbol') {
					$suffix = $this->generateRandomString();
				} elseif ($mode == 'entity_id') {
					if (!empty($data_insert['entity_id'])) {
						if ($counter) {
							$suffix = $counter . $separator . $data_insert['entity_id'];
						} else {
							$suffix = $data_insert['entity_id'];
						}
						++$counter;
					} else {
						$suffix = ++$counter;
					}
				} else {
					$suffix = ++$counter;
				}
				$keyword = $k . $separator . $suffix;
			}
		} while($query->num_rows);

		return $keyword;
	}

	private function generateRandomString($length = 6) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	public function getExistingSlugs($keyword) {
		$sql = "SELECT COUNT(*) as total 
		FROM " . DB_PREFIX . "seo_url 
		WHERE keyword = '" . $this->db->escape($keyword) . "'";
		$result = $this->db->query($sql);
		return (int)$result->row['total'];
	}
	
	public function getCategories($data) {
		$sql = "SELECT cp.`category_id` AS `category_id`, 
		cd2.name, 
		GROUP_CONCAT(cd1.`name` ORDER BY cp.`level` SEPARATOR '-') AS `path`, 
		c1.`parent_id`, 
		c1.`sort_order` 
	FROM `" . DB_PREFIX . "category_path` cp 
		LEFT JOIN `" . DB_PREFIX . "category` c1 ON (cp.`category_id` = c1.`category_id`) 
		LEFT JOIN `" . DB_PREFIX . "category` c2 ON (cp.`path_id` = c2.`category_id`) 
		LEFT JOIN `" . DB_PREFIX . "category_description` cd1 ON (cp.`path_id` = cd1.`category_id`) 
		LEFT JOIN `" . DB_PREFIX . "category_description` cd2 ON (cp.`category_id` = cd2.`category_id`) 
";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('category_id=',cp.category_id) 
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}


		
		$sql .=	" 

		WHERE cd1.`language_id` = '" . (int)$data['language_from'] . "'
		AND cd2.`language_id` = '" . (int)$data['language_from'] . "'";

		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
		$sql .= " AND cp.category_id > " . (int)$data['last_id'];
		$sql .= " GROUP BY cp.category_id";
		$sql .= " ORDER BY cp.category_id";
		
		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		} else {
			$sql .= " LIMIT 200"; 
		}

		$results = $this->db->query($sql);
		
		return $results->rows;
	}

	public function getTotalCategories($data) {
		$sql = "SELECT COUNT(cd.category_id) total
		FROM `" . DB_PREFIX . "category` c
		LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = c.category_id)";

		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('category_id=',c.category_id) 
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE cd.language_id = '" . (int)$data['language_from'] . "'";

		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
					
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getProducts($data) {
		$sql = "SELECT p.*, pd.name, (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = p.manufacturer_id) as m_name 
		FROM `" . DB_PREFIX . "product` p
		LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = p.product_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('product_id=',p.product_id) 
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE pd.language_id = '" . (int)$data['language_from'] . "'";

		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
		$sql .= " AND p.product_id > " . (int)$data['last_id'];
		
		$sql .= " ORDER BY p.product_id";
		
		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		} else {
			$sql .= " LIMIT 200"; 
		}
		
		$results = $this->db->query($sql);
		return $results->rows;
	}

	public function getTotalProducts($data) {
		$sql = "SELECT COUNT(p.product_id) total
		FROM `" . DB_PREFIX . "product` p
		LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = p.product_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('product_id=',p.product_id) 
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE pd.language_id = '" . (int)$data['language_from'] . "'";
		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
					
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getInformations($data) {
		$sql = "SELECT i.*, id.title 
		FROM `" . DB_PREFIX . "information` i
		LEFT JOIN `" . DB_PREFIX . "information_description` id ON (i.information_id = id.information_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('information_id=',i.information_id)
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE id.language_id = '" . (int)$data['language_from'] . "'";

		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
		$sql .= " AND i.information_id > " . (int)$data['last_id'];
		
		$sql .= " ORDER BY i.information_id";
		
		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		} else {
			$sql .= " LIMIT 200"; 
		}
					
		$results = $this->db->query($sql);
		return $results->rows;
	}

	public function getTotalInformations($data) {
		$sql = "SELECT COUNT(i.information_id) total
		FROM `" . DB_PREFIX . "information` i
		LEFT JOIN `" . DB_PREFIX . "information_description` id ON (id.information_id = i.information_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('information_id=',i.information_id)
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE id.language_id = '" . (int)$data['language_from'] . "'";
		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
					
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getBlogs($data) {
		$sql = "SELECT b.*, bd.title 
		FROM `" . DB_PREFIX . "blog` b
		LEFT JOIN `" . DB_PREFIX . "blog_description` bd ON (b.blog_id = bd.blog_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('blog_id=',b.blog_id)
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE bd.language_id = '" . (int)$data['language_from'] . "'";

		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
		$sql .= " AND b.blog_id > " . (int)$data['last_id'];
		
		$sql .= " ORDER BY b.blog_id_id";
		
		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		} else {
			$sql .= " LIMIT 200"; 
		}
					
		$results = $this->db->query($sql);
		return $results->rows;
	}

	public function getTotalBlogs($data) {
		$sql = "SELECT COUNT(b.blog_id) total
		FROM `" . DB_PREFIX . "blog` b
		LEFT JOIN `" . DB_PREFIX . "blog_description` bd ON (bd.blog_id = b.blog_id)";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON (
				ua.query = CONCAT('blog_id=',b.blog_id)
				AND ua.store_id = '" . (int)$data['store_id'] . "'
				AND ua.language_id = '" . (int)$data['language_id'] . "')";
		}
		$sql .=	" WHERE bd.language_id = '" . (int)$data['language_from'] . "'";
		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
					
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getManufacturers($data) {
		$sql = "SELECT m.*
		FROM `" . DB_PREFIX . "manufacturer` m";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('manufacturer_id=',m.manufacturer_id) ";
			$sql .= " AND ua.store_id = '" . (int)$data['store_id'] . "'";
			$sql .= " AND ua.language_id = '" . (int)$data['language_id'] . "'";
		}

		$sql .= " WHERE 1 ";
		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
		$sql .= " AND m.manufacturer_id > " . (int)$data['last_id'];
		
		$sql .= " ORDER BY m.manufacturer_id";
		
		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		} else {
			$sql .= " LIMIT 200"; 
		}
					$this->log->write($sql);
		$results = $this->db->query($sql);
		return $results->rows;
	}

	public function getTotalManufacturers($data) {
		$sql = "SELECT COUNT(m.manufacturer_id) total
		FROM `" . DB_PREFIX . "manufacturer` m ";
		if (!$data['update']) {
			$sql .= " LEFT JOIN `" . DB_PREFIX . "seo_url` ua ON ua.query = CONCAT('manufacturer_id=',m.manufacturer_id) ";
			$sql .= " AND ua.store_id = '" . (int)$data['store_id'] . "'";
			$sql .= " AND ua.language_id = '" . (int)$data['language_id'] . "'";
		}
		$sql .=	" WHERE 1";
		if (!$data['update']) {
			$sql .= " AND `seo_url_id`  IS NULL";
		}
					
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function insertKeyword($data_insert,$suffix='') {
		$sql = "INSERT INTO " . DB_PREFIX . "seo_url SET ";
		$sql .= " query = '" . $this->db->escape($data_insert['query']) . "'";
		$sql .= ", keyword = '" . $this->db->escape($data_insert['keyword']  . $suffix) . "'";
		$sql .= ", store_id = '" . (int)$data_insert['store_id'] . "'";
		$sql .= ", language_id = '" . (int)$data_insert['language_id'] . "'";
		if ($data_insert['push']) {
			$sql .= ", push = '" . $this->db->escape($data_insert['push']) . "'";
		}
		$this->db->query($sql);
		return $this->db->getLastId();
	}
		
}
