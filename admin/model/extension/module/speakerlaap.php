<?php
class ModelExtensionModuleSpeakerlaap extends Model {
    public function install() {
        $this->db->query("CREATE TABLE `" . DB_PREFIX . "last_action_in_panel` (`id_action` int(11) NOT NULL, `date` varchar(255) NOT NULL, `user_id` int(11) NOT NULL, `login` text NOT NULL, `ip` text NOT NULL, `action` varchar(500) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "last_action_in_panel` ADD PRIMARY KEY (`id_action`);");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "last_action_in_panel` MODIFY `id_action` int(11) NOT NULL AUTO_INCREMENT;");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "last_action_in_panel`");
    }
}