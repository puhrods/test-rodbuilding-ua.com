<?php

class ModelExtensionTotalHyperDiscountSetting extends Model {
    public function getVersion() {
        return '3.0.3';
    }
    
    public function getSetting()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hd_setting` ");
        if ($query->rows)
        {
            $data = array();
            foreach ($query->rows as $row)
                $data[$row['key']] = $row['value'];
            return $data;
        }
        else
        {
            return array();
        }
    }
    
}
