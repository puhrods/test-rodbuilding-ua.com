<?php
class ModelToolSpeakerlaaplog extends Model
{
    public function getListActions($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "last_action_in_panel` ORDER BY id_action DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getLastIdAction() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "last_action_in_panel` ORDER BY id_action DESC LIMIT 1;");
        foreach ($query->rows as $result) {
            $last_action_id = $result['id_action'];
        }
        return $last_action_id;
    }

    public function getTotalLastAction() {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "last_action_in_panel`";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getNextIdAction() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "last_action_in_panel` ORDER BY id_action DESC LIMIT 1;");
        foreach ($query->rows as $result) {
            $next_action_id = $result['id_action'] + 1;
        }
        return $next_action_id;
    }

    public function clearTableActions() {
        $query = $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "last_action_in_panel`;");
        return $query;
    }


    public function DeleteOldAction() {
        if ($this->config->get('module_speakerlaap_deletedays') <= 0) {
            $date_del = 2505600;
        } else {
            $date_del = $this->config->get('module_speakerlaap_deletedays') * 24 * 60 * 60;
        }

        $current_time = time();
        $this->db->query("DELETE FROM `" . DB_PREFIX . "last_action_in_panel` WHERE `date` + " . $date_del . " < " . $current_time . ";");
    }

    public function getLangId()
    {
        $speakerlaap_lang_id = $this->db->query("SELECT language_id as lang_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->config->get('config_admin_language') . "';");
        foreach ($speakerlaap_lang_id->rows as $speakerlaap_lang_id) {
            $selected_lang = $speakerlaap_lang_id['lang_id'];
        }
        return $selected_lang;
    }

    public function getLangName($id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getStoreName($id) {
        if ($id == 0) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `store_id` = '0' AND `key`='config_name';");
            foreach ($query->rows as $result) {
                $name = $result['value'];
            }
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$id . "'");
            foreach ($query->rows as $result) {
                $name = $result['name'];
            }
        }
        return $name;
    }

    public function getNameCategory($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameBlogCategory($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_category_description WHERE blog_category_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameProduct($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameArticle($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_description WHERE article_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameRecurring($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring_description WHERE recurring_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameFilter($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_group_description WHERE filter_group_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameAttributeGroup($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameAttribute($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameOption($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameManufacturer($item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$item_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameDownload($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameInformation($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['title'];
        }
        return $name;
    }

    public function getNameLayout($item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$item_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getNameBanner($item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$item_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getResultStatus($status) {
        if ($status == 1) {
            $status_name = $this->language->get('act_cat_enable');
        } else {
            $status_name = $this->language->get('act_cat_disable');
        }
        return $status_name;
    }

    public function LogActionCategoryAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $cat_name = $this->getNameCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_cat_add'), $this->db->escape($cat_name), $item_id) . "';");
    }

    public function LogActionCategoryEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $cat_name = $this->getNameCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_cat_edit'), $this->db->escape($cat_name), $item_id) . "';");
    }

    public function LogActionCategoryDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $cat_name = $this->getNameCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_cat_delete'), $this->db->escape($cat_name), $item_id) . "';");
    }

    public function LogActionCategoryRepair()
    {
        $this->load->language('speaker/speakerlaap');
        $get_last_action = $this->db->query("SELECT action as act FROM " . DB_PREFIX . "last_action_in_panel ORDER BY date DESC LIMIT 1;");
        foreach ($get_last_action->rows as $get_last_action) {
            $last_action = $get_last_action['act'];
        }
        if ($last_action != $this->language->get('act_cat_repair')) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . $this->language->get('act_cat_repair') . "';");
        }
    }

    public function LogActionCategoryStatus($item_id, $status) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $cat_name = $this->getNameCategory($selected_lang, $item_id);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_cat_status'), $this->db->escape($cat_name), $item_id, $status_name) . "';");
    }

    public function LogActionProductAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameProduct($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_prod_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionProductEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameProduct($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_prod_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionProductDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameProduct($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_prod_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionProductCopy($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameProduct($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_prod_copy'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionProductStatus($item_id, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameProduct($selected_lang, $item_id);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_prod_status'), $this->db->escape($name), $item_id, $status_name) . "';");
    }

    public function LogActionRecurringAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameRecurring($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_recur_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionRecurringCopy($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameRecurring($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_recur_copy'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionRecurringEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameRecurring($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_recur_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionRecurringDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameRecurring($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_recur_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionFilterAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameFilter($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_filter_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionFilterEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameFilter($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_filter_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionFilterDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameFilter($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_filter_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeGroupAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttributeGroup($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_g_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeGroupEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttributeGroup($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_g_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeGroupDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttributeGroup($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_g_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttribute($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttribute($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionAttributeDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameAttribute($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_attr_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionOptionAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameOption($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_option_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionOptionEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameOption($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_option_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionOptionDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameOption($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_option_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionManufacturerAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getNameManufacturer($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_manuf_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionManufacturerEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getNameManufacturer($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_manuf_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionManufacturerDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getNameManufacturer($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_manuf_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionDownloadAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameDownload($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_download_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionDownloadEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameDownload($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_download_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionDownloadDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameDownload($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_download_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionReviewAdd($item_id, $author, $product_id, $text, $rating, $status, $date_added)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $product_name = $this->getNameProduct($selected_lang, $product_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_review_add'), $item_id, $author, $this->db->escape($product_name), $this->db->escape($text), $rating, $status, $date_added) . "';");
    }

    public function LogActionReviewEdit($item_id, $author, $product_id, $text, $rating, $status, $date_added)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $product_name = $this->getNameProduct($selected_lang, $product_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_review_edit'), $item_id, $author, $this->db->escape($product_name), $this->db->escape($text), $rating, $status, $date_added) . "';");
    }

    public function LogActionReviewDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_review_delete'), $item_id) . "';");
    }

    public function LogActionInformationAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameInformation($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_info_add'), $name, $item_id) . "';");
    }

    public function LogActionInformationEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameInformation($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_info_edit'), $name, $item_id) . "';");
    }

    public function LogActionInformationDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameInformation($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_info_delete'), $name, $item_id) . "';");
    }

    public function LogActionInformationStatus($item_id, $status) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameInformation($selected_lang, $item_id);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_info_status'), $name, $item_id, $status_name) . "';");
    }

    public function LogActionArticleAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_article_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionArticleEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_article_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionArticleDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_article_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionArticleCopy($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_article_copy'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionArticleStatus($item_id, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $item_id);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_article_status'), $this->db->escape($name), $item_id, $status_name) . "';");
    }

    public function LogActionBlogCategoryAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameBlogCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_cat_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBlogCategoryEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameBlogCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_cat_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBlogCategoryDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameBlogCategory($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_cat_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBlogCategoryRepair() {
        $this->load->language('speaker/speakerlaap');
        $get_last_action = $this->db->query("SELECT action as act FROM " . DB_PREFIX . "last_action_in_panel ORDER BY date DESC LIMIT 1;");
        foreach ($get_last_action->rows as $get_last_action) {
            $last_action = $get_last_action['act'];
        }
        if ($last_action != $this->language->get('act_b_cat_repair')) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . $this->language->get('act_b_cat_repair') . "';");
        }
    }

    public function LogActionBlogCategoryStatus($item_id, $status) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameBlogCategory($selected_lang, $item_id);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_cat_status'), $this->db->escape($name), $item_id, $status_name) . "';");
    }

    public function LogActionBlogReviewAdd($item_id, $author, $article_id, $text, $rating, $status, $date_added)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $article_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_review_add'), $item_id, $author, $this->db->escape($name), $this->db->escape($text), $rating, $status, $date_added) . "';");
    }

    public function LogActionBlogReviewEdit($item_id, $author, $article_id, $text, $rating, $status, $date_added)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $name = $this->getNameArticle($selected_lang, $article_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_review_edit'), $item_id, $author, $this->db->escape($name), $this->db->escape($text), $rating, $status, $date_added) . "';");
    }

    public function LogActionBlogReviewDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_b_review_delete'), $item_id) . "';");
    }

    public function LogActionLayoutAdd($item_id, $name) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_layout_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionLayoutEdit($item_id, $name) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_layout_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionLayoutDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getNameLayout($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_layout_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBannerAdd($item_id, $name) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_banner_add'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBannerEdit($item_id, $name) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_banner_edit'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionBannerDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getNameBanner($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_banner_delete'), $this->db->escape($name), $item_id) . "';");
    }

    public function LogActionSeoUrlAdd($url, $seo_url, $store, $language)
    {
        $this->load->language('speaker/speakerlaap');
        $lang_name = $this->getLangName($language);
        $store_name = $this->getStoreName($store);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_seo_url_add'), $url, $seo_url, $store_name, $lang_name) . "';");
    }

    public function LogActionSeoUrlEdit($url, $seo_url, $store, $language)
    {
        $this->load->language('speaker/speakerlaap');
        $lang_name = $this->getLangName($language);
        $store_name = $this->getStoreName($store);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_seo_url_edit'), $url, $seo_url, $store_name, $lang_name) . "';");
    }

    public function LogActionSeoUrlDelete($url, $seo_url, $store, $language)
    {
        $this->load->language('speaker/speakerlaap');
        $lang_name = $this->getLangName($language);
        $store_name = $this->getStoreName($store);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_seo_url_delete'), $url, $seo_url, $store_name, $lang_name) . "';");
    }

    public function getCustomerName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['firstname'] . " " . $result['lastname'];
        }
        return $name;
    }

    public function getReturnStatusName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_status WHERE return_status_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getReturnReasonName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return_status WHERE return_status_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function LogActionReturnAdd($item_id, $order_id, $date, $customer, $product, $reason_return, $comment, $status_return)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $customer_name = $this->getCustomerName($selected_lang, $customer);
        $return_name = $this->getReturnStatusName($selected_lang, $status_return);
        $reason_name = $this->getReturnReasonName($selected_lang, $reason_return);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_return_add'), $item_id, $order_id, $date, $customer_name, $product, $reason_name, $comment, $return_name) . "';");
    }

    public function LogActionReturnEdit($item_id, $order_id, $date, $customer, $product, $reason_return, $comment)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $customer_name = $this->getCustomerName($selected_lang, $customer);
        $reason_name = $this->getReturnReasonName($selected_lang, $reason_return);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_return_edit'), $item_id, $order_id, $date, $customer_name, $product, $reason_name, $comment) . "';");
    }

    public function LogActionReturnDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_return_delete'), $item_id) . "';");
    }

    public function getVoucherStatusName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "voucher_theme_description WHERE voucher_theme_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function LogActionVoucherAdd($item_id, $code, $from_name, $from_email, $to_name, $to_email, $voucher_theme_id, $amount, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $status_name = $this->getResultStatus($status);
        $voucher_theme_name = $this->getVoucherStatusName($selected_lang, $voucher_theme_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_add'), $item_id, $code, $from_name, $from_email, $to_name, $to_email, $voucher_theme_name, $amount, $status_name) . "';");
    }

    public function LogActionVoucherEdit($item_id, $code, $from_name, $from_email, $to_name, $to_email, $voucher_theme_id, $amount, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $status_name = $this->getResultStatus($status);
        $voucher_theme_name = $this->getVoucherStatusName($selected_lang, $voucher_theme_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_edit'), $item_id, $code, $from_name, $from_email, $to_name, $to_email, $voucher_theme_name, $amount, $status_name) . "';");
    }

    public function LogActionVoucherDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_delete'), $item_id) . "';");
    }

    public function LogActionVoucherThemeAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $voucher_theme_name = $this->getVoucherStatusName($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_theme_add'), $item_id, $voucher_theme_name) . "';");
    }

    public function LogActionVoucherThemeEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $voucher_theme_name = $this->getVoucherStatusName($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_theme_edit'), $item_id, $voucher_theme_name) . "';");
    }

    public function LogActionVoucherThemeDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $voucher_theme_name = $this->getVoucherStatusName($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_voucher_theme_delete'), $item_id, $voucher_theme_name) . "';");
    }

    public function getCustomerGroupName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function LogActionCustomerAdd($item_id, $first_last_name, $email, $customer_group_id, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $status_name = $this->getResultStatus($status);
        $customer_group_name = $this->getCustomerGroupName($selected_lang, $customer_group_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_add'), $item_id, $first_last_name, $email, $customer_group_name, $status_name) . "';");
    }

    public function LogActionCustomerEdit($item_id, $first_last_name, $email, $customer_group_id, $status)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $status_name = $this->getResultStatus($status);
        $customer_group_name = $this->getCustomerGroupName($selected_lang, $customer_group_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_edit'), $item_id, $first_last_name, $email, $customer_group_name, $status_name) . "';");
    }

    public function LogActionCustomerDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_delete'), $item_id) . "';");
    }

    public function LogActionCustomerGroupAdd($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $customer_group_name = $this->getCustomerGroupName($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_group_add'), $item_id, $customer_group_name) . "';");
    }

    public function LogActionCustomerGroupEdit($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $customer_group_name = $this->getCustomerGroupName($selected_lang, $item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_group_edit'), $item_id, $customer_group_name) . "';");
    }

    public function LogActionCustomerGroupDelete($item_id)
    {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='" . time() . "', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_customer_group_delete'), $item_id) . "';");
    }

    public function getCustomFieldName($lang_id, $item_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_description WHERE custom_field_id = '" . (int)$item_id . "' AND language_id ='" . $lang_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function getTypeOptionName($value) {
        if ($value == 'select') {
            $type = $this->language->get('type_select');
        } else if ($value == 'radio') {
            $type = $this->language->get('type_radio');
        } else if ($value == 'checkbox') {
            $type = $this->language->get('type_checkbox');
        } else if ($value == 'input') {
            $type = $this->language->get('type_input');
        } else if ($value == 'text') {
            $type = $this->language->get('type_text');
        } else if ($value == 'textarea') {
            $type = $this->language->get('type_textarea');
        } else if ($value == 'file') {
            $type = $this->language->get('type_file');
        } else if ($value == 'date') {
            $type = $this->language->get('type_date');
        } else if ($value == 'datetime') {
            $type = $this->language->get('type_datetime');
        } else if ($value == 'time') {
            $type = $this->language->get('type_time');
        }

        return $type;
    }

    public function LogActionCustomFieldAdd($item_id, $type) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $custom_field_name = $this->getCustomFieldName($selected_lang, $item_id);
        $type_name = $this->getTypeOptionName($type);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_custom_field_add'), $item_id, $custom_field_name, $type_name) . "';");
    }

    public function LogActionCustomFieldEdit($item_id, $type) {
        $this->load->language('speaker/speakerlaap');
        $selected_lang = $this->getLangId();
        $custom_field_name = $this->getCustomFieldName($selected_lang, $item_id);
        $type_name = $this->getTypeOptionName($type);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_custom_field_edit'), $item_id, $custom_field_name, $type_name) . "';");
    }

    public function LogActionCustomFieldDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_custom_field_delete'), $item_id) . "';");
    }

    public function LogActionMarketingAdd($item_id, $name, $code) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_marketing_add'), $item_id, $name, $code) . "';");
    }

    public function LogActionMarketingEdit($item_id, $name, $code) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_marketing_edit'), $item_id, $name, $code) . "';");
    }

    public function LogActionMarketingDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_marketing_delete'), $item_id) . "';");
    }

    public function getTypeCouponName($value) {
        if ($value == 'P') {
            $type = $this->language->get('act_coupon_p');
        }

        if ($value == 'F') {
            $type = $this->language->get('act_coupon_f');
        }

        return $type;
    }

    public function LogActionCouponAdd($item_id, $name, $code, $discount, $type, $total, $date_start, $date_end, $status) {
        $this->load->language('speaker/speakerlaap');
        $type_name = $this->getTypeCouponName($type);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_add'), $item_id, $name, $code, $type_name, $discount, $total, $date_start, $date_end, $status_name) . "';");
    }

    public function LogActionCouponEdit($item_id, $name, $code, $discount, $type, $total, $date_start, $date_end, $status) {
        $this->load->language('speaker/speakerlaap');
        $type_name = $this->getTypeCouponName($type);
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_edit'), $item_id, $name, $code, $type_name, $discount, $total, $date_start, $date_end, $status_name) . "';");
    }

    public function LogActionCouponDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_delete'), $item_id) . "';");
    }

    public function LogActionSettingStoreAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getStoreName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_add'), $item_id, $name) . "';");
    }

    public function LogActionSettingStoreEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getStoreName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_edit'), $item_id, $name) . "';");
    }

    public function LogActionSettingStoreDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_coupon_delete'), $item_id) . "';");
    }

    public function LogActionSettingSettingAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getStoreName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_store_add'), $item_id, $name) . "';");
    }

    public function LogActionSettingSettingEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getStoreName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_store_edit'), $item_id, $name) . "';");
    }

    public function LogActionSettingSettingDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_store_delete'), $item_id) . "';");
    }

    public function getUserName($item_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$item_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['username'] . " (" . $result['firstname'] . " " . $result['lastname'] . ")";
        }
        return $name;
    }

    public function LogActionSettingUserAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getUserName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_add'), $item_id, $name) . "';");
    }

    public function LogActionSettingUserEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getUserName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_edit'), $item_id, $name) . "';");
    }

    public function LogActionSettingUserDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_delete'), $item_id) . "';");
    }

    public function getUserGroupName($item_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$item_id . "'");
        foreach ($query->rows as $result) {
            $name = $result['name'];
        }
        return $name;
    }

    public function LogActionSettingUserGroupAdd($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getUserGroupName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_group_add'), $item_id, $name) . "';");
    }

    public function LogActionSettingUserGroupEdit($item_id) {
        $this->load->language('speaker/speakerlaap');
        $name = $this->getUserGroupName($item_id);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_group_edit'), $item_id, $name) . "';");
    }

    public function LogActionSettingUserGroupDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_user_group_delete'), $item_id) . "';");
    }

    public function LogActionSettingApiAdd($item_id, $name, $status) {
        $this->load->language('speaker/speakerlaap');
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_api_add'), $item_id, $name, $status_name) . "';");
    }

    public function LogActionSettingApiEdit($item_id, $name, $status) {
        $this->load->language('speaker/speakerlaap');
        $status_name = $this->getResultStatus($status);
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_api_edit'), $item_id, $name, $status_name) . "';");
    }

    public function LogActionSettingApiDelete($item_id) {
        $this->load->language('speaker/speakerlaap');
        $this->db->query("INSERT INTO `" . DB_PREFIX . "last_action_in_panel` SET date='". date('Y-m-d H:i:s', time()) ."', user_id='" . $this->user->getId() . "', login='" . $this->user->getUserName() . "', ip='" . $_SERVER['REMOTE_ADDR'] . "', action='" . sprintf($this->language->get('act_s_api_delete'), $item_id) . "';");
    }
}

