<?php
class ModelSaleColorOrders extends Model {
	public function install() {
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 'b_color'");
		if (!$chk->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` ADD COLUMN  `b_color` varchar(32) NOT NULL default ''");
		 }
		 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 't_color'");
		 if (!$chk->num_rows) {
				 $this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` ADD COLUMN  `t_color` varchar(32) NOT NULL default ''");
			}
			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 's_color'");
			if (!$chk->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` ADD COLUMN  `s_color` int(1) NOT NULL default '0'");
			 }
	}

	public function uninstall() {
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 'b_color'");
		if ($chk->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` DROP COLUMN  `b_color`");
		 }
		 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 't_color'");
		 if ($chk->num_rows) {
				 $this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` DROP COLUMN  `t_color`");
			}
			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_status` WHERE `field` = 's_color'");
			if ($chk->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_status` DROP COLUMN  `s_color`");
			 }
	}
}
