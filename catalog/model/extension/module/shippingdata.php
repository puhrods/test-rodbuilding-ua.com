<?php
class ModelModuleShippingData extends Model {
	public function getNovaPoshtaCities($area, $search = '') {
        if (!$area && !$search) {
            return $this->getDefaultCities();
        }

        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

		$sql = "SELECT `" . $novaposhta->description_field . "` as `Description` FROM `" . DB_PREFIX . "novaposhta_cities` WHERE 1";

		if ($area) {
		    $sql .= " AND `Area` = '" . $this->db->escape($area) . "'";
        }

		if ($search) {
			$sql .= " AND (`Description` LIKE '" . $this->db->escape($search) . "%' OR `DescriptionRu` LIKE '" . $this->db->escape($search) . "%')";
		}
		
		$sql .= " ORDER BY  `" . $novaposhta->description_field . "`";
		
		return $this->db->query($sql)->rows;
	}
	
	public function getNovaPoshtaWarehouses($city, $search = '') {
        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

        if (version_compare(VERSION, '3', '>=')) {
            $settings = $this->config->get('shipping_novaposhta');
        } else {
            $settings = $this->config->get('novaposhta');
        }

		$sql = "SELECT `" . $novaposhta->description_field . "` as `Description` FROM `" . DB_PREFIX . "novaposhta_warehouses` WHERE (`CityDescription` = '" . $this->db->escape($city) . "' OR `CityDescriptionRu` = '" . $this->db->escape($city) . "')";
			
        if ($search) {
            $sql .= " AND (`Description` LIKE '%" . $this->db->escape($search) . "%' OR `DescriptionRu` LIKE '%" . $this->db->escape($search) . "%')";
        }

		if (isset($settings['shipping_methods']['warehouse']['warehouse_types'])) {
			foreach ($settings['shipping_methods']['warehouse']['warehouse_types'] as $k => $v) {
                $settings['shipping_methods']['warehouse']['warehouse_types'][$k] = "'" . $v . "'";
			}

			$sql .= " AND `TypeOfWarehouse` IN (" . implode(',', $settings['shipping_methods']['warehouse']['warehouse_types']) . ")";
		}
		
		if (isset($this->session->data['shippingdata']['cart_weight']) && $settings['shipping_methods']['warehouse']['warehouses_filter_weight']) {
			$sql .= " AND (`TotalMaxWeightAllowed` >= '" . $this->session->data['shippingdata']['cart_weight'] . "' OR (`TotalMaxWeightAllowed` = 0 AND (`PlaceMaxWeightAllowed` >= '" . $this->session->data['shippingdata']['cart_weight'] . "' OR `PlaceMaxWeightAllowed` = 0)))";
		}

		$sql .= " ORDER BY `Number`+0";
				
		return $this->db->query($sql)->rows;	
	}

	private function getDefaultCities() {
        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

        $data = array();

        $cities = array(
            array(
                'Description'   => 'Київ',
                'DescriptionRu' => 'Киев'
            ),
            array(
                'Description'   => 'Харків',
                'DescriptionRu' => 'Харьков'
            ),
            array(
                'Description'   => 'Дніпро',
                'DescriptionRu' => 'Днепр'
            ),
            array(
                'Description'   => 'Одеса',
                'DescriptionRu' => 'Одесса'
            ),
            /* Delivery is temporarily not carried out
            array(
                'Description'   => 'Донецьк',
                'DescriptionRu' => 'Донецк'
            ),
            */
            array(
                'Description'   => 'Запоріжжя',
                'DescriptionRu' => 'Запорожье'
            ),
            array(
                'Description'   => 'Львів',
                'DescriptionRu' => 'Львов'
            ),
            array(
                'Description'   => 'Кривий Ріг',
                'DescriptionRu' => 'Кривой Рог'
            ),
            array(
                'Description'   => 'Миколаїв',
                'DescriptionRu' => 'Николаев'
            ),
            array(
                'Description'   => 'Маріуполь',
                'DescriptionRu' => 'Мариуполь'
            ),
            /* Delivery is temporarily not carried out
           array(
               'Description'   => 'Луганськ',
               'DescriptionRu' => 'Луганск'
           ),
           */
            array(
                'Description'   => 'Вінниця',
                'DescriptionRu' => 'Винница'
            ),
            /* Delivery is temporarily not carried out
           array(
               'Description'   => 'Севастополь',
               'DescriptionRu' => 'Севастополь'
           ),
           */
            /* Delivery is temporarily not carried out
          array(
              'Description'   => 'Сімферополь',
              'DescriptionRu' => 'Симферополь'
          ),
          */
            array(
                'Description'   => 'Херсон',
                'DescriptionRu' => 'Херсон'
            ),
            array(
                'Description'   => 'Полтава',
                'DescriptionRu' => 'Полтава'
            ),
            array(
                'Description'   => 'Чернігів',
                'DescriptionRu' => 'Чернигов'
            ),
            array(
                'Description'   => 'Черкаси',
                'DescriptionRu' => 'Черкассы'
            ),
            array(
                'Description'   => 'Суми',
                'DescriptionRu' => 'Сумы'
            ),
            array(
                'Description'   => 'Хмельницький',
                'DescriptionRu' => 'Хмельницкий'
            ),
            array(
                'Description'   => 'Житомир',
                'DescriptionRu' => 'Житомир'
            ),
            array(
                'Description'   => 'Кропивницький',
                'DescriptionRu' => 'Кропивницкий'
            ),
            array(
                'Description'   => 'Рівне',
                'DescriptionRu' => 'Ровно'
            ),
            array(
                'Description'   => 'Чернівці',
                'DescriptionRu' => 'Черновцы'
            ),
            array(
                'Description'   => 'Тернопіль',
                'DescriptionRu' => 'Тернополь'
            ),
            array(
                'Description'   => 'Івано-Франківськ',
                'DescriptionRu' => 'Ивано-Франковск'
            ),
            array(
                'Description'   => 'Луцьк',
                'DescriptionRu' => 'Луцк'
            ),
            array(
                'Description'   => 'Ужгород',
                'DescriptionRu' => 'Ужгород'
            )
        );

        foreach ($cities as $city) {
            $data[] = array(
                'Description' => $city[$novaposhta->description_field]
            );
        }

        return $data;
    }
}

class ModelExtensionModuleShippingData extends ModelModuleShippingData {

}