<?php
class ModelExtensionMayAdvancedOptions extends Model {
	const EVENT_PREFIX = 'may_ao_';

	private $events = array(
		// Admin
		'am_uugeug_after' => array(
			'trigger' => 'admin/model/user/user_group/editUserGroup/before',
			'action' => 'extension/may/advanced_options/mUserUserGroupEditUserGroupBefore'
		),
		'am_cpap_after' => array(
			'trigger' => 'admin/model/catalog/product/addProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductAddProductAfter'
		),
		'am_cpep_after' => array(
			'trigger' => 'admin/model/catalog/product/editProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductEditProductAfter'
		),
		'am_cpdp_after' => array(
			'trigger' => 'admin/model/catalog/product/deleteProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductDeleteProductAfter'
		),
		'am_cpcp_after' => array(
			'trigger' => 'admin/model/catalog/product/copyProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductCopyProductAfter'
		),
		'am_cpgp_after' => array(
			'trigger' => 'admin/model/catalog/product/getProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductGetProductAfter'
		),
		'am_cpgpds_after' => array(
			'trigger' => 'admin/model/catalog/product/getProductDescriptions/after',
			'action' => 'extension/may/advanced_options/mCatalogProductGetProductDescriptionsAfter'
		),
		'am_cpgps_after' => array(
			'trigger' => 'admin/model/catalog/product/getProducts/after',
			'action' => 'extension/may/advanced_options/mCatalogProductGetProductsAfter'
		),
		'am_codo_after' => array(
			'trigger' => 'admin/model/catalog/option/deleteOption/after',
			'action' => 'extension/may/advanced_options/mCatalogOptionDeleteOptionAfter'
		),
		'am_coeo_before' => array(
			'trigger' => 'admin/model/catalog/option/editOption/before',
			'action' => 'extension/may/advanced_options/mCatalogOptionDeleteOptionBefore'
		),
		'av_cocl_before' => array(
			'trigger' => 'admin/view/common/column_left/before',
			'action' => 'extension/may/advanced_options/vCommonColumnLeftBefore'
		),
		'av_cpf_before' => array(
			'trigger' => 'admin/view/catalog/product_form/before',
			'action' => 'extension/may/advanced_options/vCatalogProductFormBefore'
		),
		'av_sof_before' => array(
			'trigger' => 'admin/view/sale/order_form/before',
			'action' => 'extension/may/advanced_options/vSaleOrderFormBefore'
		),
		'av_soi_before' => array(
			'trigger' => 'admin/view/sale/order_info/before',
			'action' => 'extension/may/advanced_options/vSaleOrderInfoBefore'
		),
		'av_soiv_before' => array(
			'trigger' => 'admin/view/sale/order_invoice/before',
			'action' => 'extension/may/advanced_options/vSaleOrderInvoiceBefore'
		),
		'av_sos_before' => array(
			'trigger' => 'admin/view/sale/order_shipping/before',
			'action' => 'extension/may/advanced_options/vSaleOrderShippingBefore'
		),

		// Catalog
		'cm_choao_before' => array(
			'trigger' => 'catalog/model/checkout/order/addOrder/before',
			'action' => 'extension/may/advanced_options/mCheckoutOrderAddOrderBefore'
		),
		'cm_choeo_before' => array(
			'trigger' => 'catalog/model/checkout/order/editOrder/before',
			'action' => 'extension/may/advanced_options/mCheckoutOrderEditOrderBefore'
		),
		'cm_cpgp_after' => array(
			'trigger' => 'catalog/model/catalog/product/getProduct/after',
			'action' => 'extension/may/advanced_options/mCatalogProductGetProductAfter'
		),
		'cm_craoh_after' => array(
			'trigger' => 'catalog/model/checkout/order/addOrderHistory/after',
			'action' => 'extension/may/advanced_options/mCheckoutOrderAddOrderHistoryAfter'
		),
		'cv_pp_before' => array(
			'trigger' => 'catalog/view/product/product/before',
			'action' => 'extension/may/advanced_options/vProductProductBefore'
		),
		'cv_pc_before' => array(
			'trigger' => 'catalog/view/product/category/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_pmi_before' => array(
			'trigger' => 'catalog/view/product/manufacturer_info/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_ps_before' => array(
			'trigger' => 'catalog/view/product/special/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_psch_before' => array(
			'trigger' => 'catalog/view/product/search/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_emf_before' => array(
			'trigger' => 'catalog/view/extension/module/featured/before',
			'action' => 'extension/may/advanced_options/vProductFeaturedBefore'
		),
		'cv_eml_before' => array(
			'trigger' => 'catalog/view/extension/module/latest/before',
			'action' => 'extension/may/advanced_options/vProductLatestBefore'
		),
		'cv_emb_before' => array(
			'trigger' => 'catalog/view/extension/module/bestseller/before',
			'action' => 'extension/may/advanced_options/vProductBestsellerBefore'
		),
		'cv_cof_after' => array(
			'trigger' => 'catalog/view/common/footer/after',
			'action' => 'extension/may/advanced_options/vCommonFooterAfter'
		),
		'cv_coc_before' => array(
			'trigger' => 'catalog/view/common/cart/before',
			'action' => 'extension/may/advanced_options/vCommonCartBefore'
		),
		'cv_chc_before' => array(
			'trigger' => 'catalog/view/checkout/cart/before',
			'action' => 'extension/may/advanced_options/vCheckoutCartBefore'
		),
		'cv_chcf_before' => array(
			'trigger' => 'catalog/view/checkout/confirm/before',
			'action' => 'extension/may/advanced_options/vCheckoutConfirmBefore'
		),

		// SimpleCheckout
		'cv_chschc_before' => array(
			'trigger' => 'catalog/view/checkout/simplecheckout_cart/before',
			'action' => 'extension/may/advanced_options/vCheckoutCartBefore'
		),

		// Oct UltraStore
		'cv_octmpc_before' => array(
			'trigger' => 'catalog/view/octemplates/module/oct_popup_cart/before',
			'action' => 'extension/may/advanced_options/vOctModulePopupCartBefore'
		),

		// QuickCheckout
		'cv_dqcc_before' => array(
			'trigger' => 'catalog/view/d_quickcheckout/cart/before',
			'action' => 'extension/may/advanced_options/vDQuickCheckoutCartBefore'
		),
		'cc_dqccp_after' => array(
			'trigger' => 'catalog/controller/extension/d_quickcheckout/cart/prepare/after',
			'action' => 'extension/may/advanced_options/cExtensionDQuickCheckoutCartPrepareAfter'
		),
		'cm_dqcouo_before' => array(
			'trigger' => 'catalog/model/extension/d_quickcheckout/order/updateOrder/before',
			'action' => 'extension/may/advanced_options/mExtensionDQuickCheckoutOrderUpdateOrderBefore'
		),

		// Journal3
		'cc_j3nc_before' => array(
			'trigger' => 'catalog/controller/journal3/notification/cart/before',
			'action' => 'extension/may/advanced_options/cJournal3NotificationCartBefore'
		),
		'cv_j3chc_before' => array(
			'trigger' => 'catalog/view/journal3/checkout/cart/before',
			'action' => 'extension/may/advanced_options/vJournal3CheckoutCartBefore'
		),
		'cv_j3chch_before' => array(
			'trigger' => 'catalog/view/journal3/checkout/checkout/before',
			'action' => 'extension/may/advanced_options/vJournal3CheckoutCheckoutBefore'
		),
		'cv_j3mp_before' => array(
			'trigger' => 'catalog/view/journal3/module/products/before',
			'action' => 'extension/may/advanced_options/vJournal3ModuleProductsBefore'
		),
		'cm_dispatch_json_before' => array(
			'trigger' => 'catalog/model/extension/may/advanced_options/dispatchJson/before',
			'action' => 'extension/may/advanced_options/mDispatchJsonBefore'
		),
		'cm_j3os_before' => array(
			'trigger' => 'catalog/model/journal3/order/save/before',
			'action' => 'extension/may/advanced_options/mJournal3OrderSaveBefore'
		),

		// Basel
		'cv_pq_before' => array(
			'trigger' => 'catalog/view/product/quickview/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_pq_after' => array(
			'trigger' => 'catalog/view/product/quickview/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),

		// Kenza
		'kenza_cv_pq_before' => array(
			'trigger' => 'catalog/view/product/quick_view/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'kenza_cv_pq_after' => array(
			'trigger' => 'catalog/view/product/quick_view/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),

		// Zemez
		'cv_zemez_emzaqv_before' => array(
			'trigger' => 'catalog/view/extension/module/zemez_ajax_quick_view/before',
			'action' => 'extension/may/advanced_options/vZemezAjaxQuickviewBefore'
		),
		'cv_zemez_emzaqv_after' => array(
			'trigger' => 'catalog/view/extension/module/zemez_ajax_quick_view/after',
			'action' => 'extension/may/advanced_options/vZemezAjaxQuickviewAfter'
		),
		'cv_zemez_emzscp_before' => array(
			'trigger' => 'catalog/view/extension/module/zemez_single_category_product/before',
			'action' => 'extension/may/advanced_options/vZemezSingleCategoryProductBefore'
		),

		// Sinrato, Truemart, Mimosa
		'cv_poqp_before' => array(
			'trigger' => 'catalog/view/product/ocquickview/product/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_poqp_after' => array(
			'trigger' => 'catalog/view/product/ocquickview/product/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),
		'cv_emoclnocc_before' => array(
			'trigger' => 'catalog/view/extension/module/oclayerednavigation/occategory/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_emoclnoccf_before' => array(
			'trigger' => 'catalog/view/extension/module/oclayerednavigation/occategoryfilter/before',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_emoclnoccf_after' => array(
			'trigger' => 'catalog/view/extension/module/oclayerednavigation/occategoryfilter/after',
			'action' => 'extension/may/advanced_options/vAjaxCategoryAfter'
		),
		'cv_emocp_before' => array(
			'trigger' => 'catalog/view/extension/module/ocproduct/before',
			'action' => 'extension/may/advanced_options/vExtensionModuleOCProductBefore'
		),
		'cv_emoctp_before' => array(
			'trigger' => 'catalog/view/extension/module/octabproducts/before',
			'action' => 'extension/may/advanced_options/vExtensionModuleOCTabProductsBefore'
		),

		// Debaco, Drama
		'cv_ppqp_before' => array(
			'trigger' => 'catalog/view/plaza/quickview/product/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_ppqp_after' => array(
			'trigger' => 'catalog/view/plaza/quickview/product/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),
		'cv_pmptp_before' => array(
			'trigger' => 'catalog/view/plaza/module/ptproducts/before',
			'action' => 'extension/may/advanced_options/vPlazaModulePtProductsBefore'
		),
		'cv_pfc_before' => array(
			'trigger' => 'catalog/view/plaza/filter/category',
			'action' => 'extension/may/advanced_options/vProductCategoryBefore'
		),
		'cv_pfc_after' => array(
			'trigger' => 'catalog/view/plaza/filter/category',
			'action' => 'extension/may/advanced_options/vAjaxCategoryAfter'
		),

		// Revo
		'cv_scqv_before' => array(
			'trigger' => 'catalog/view/soconfig/quickview/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_scqv_after' => array(
			'trigger' => 'catalog/view/soconfig/quickview/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),
		'cc_scca_before' => array(
			'trigger' => 'catalog/controller/extension/soconfig/cart/add/before',
			'action' => 'extension/may/advanced_options/cSoconfigCartAddBefore'
		),
		'cv_scqc_before' => array(
			'trigger' => 'catalog/view/soconfig/quickcart/before',
			'action' => 'extension/may/advanced_options/vSoconfigQuickcartBefore'
		),
		'cm_sochgp_after' => array(
			'trigger' => 'catalog/model/extension/module/so_onepagecheckout/getProducts/after',
			'action' => 'extension/may/advanced_options/mSoonepagecheckoutGetProductsAfter'
		),

		// Mixbucket (mahardhi)
		'cv_pqq_before' => array(
			'trigger' => 'catalog/view/product/quickview/quickview/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_pqq_after' => array(
			'trigger' => 'catalog/view/product/quickview/quickview/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),

		// Cyberstore
		'cv_pcg_before' => array(
			'trigger' => 'catalog/view/product/cyber_gallery/before',
			'action' => 'extension/may/advanced_options/vProductCyberGalleryBefore'
		),
		'cv_emnfoc_before' => array(
			'trigger' => 'catalog/view/extension/module/newfastordercart/before',
			'action' => 'extension/may/advanced_options/vCommonCartBefore'
		),
		'cv_emqvp_before' => array(
			'trigger' => 'catalog/view/extension/module/quickviewpro/before',
			'action' => 'extension/may/advanced_options/vProductQuickviewBefore'
		),
		'cv_emqvp_after' => array(
			'trigger' => 'catalog/view/extension/module/quickviewpro/after',
			'action' => 'extension/may/advanced_options/vProductQuickviewAfter'
		),
	);

	public function install() {
		if ($this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "may_advanced_option_template'")->num_rows == 0) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "may_advanced_option` (
				`option_id` int(11) NOT NULL AUTO_INCREMENT,
				`option_name` varchar(255) NOT NULL,
				`children` varchar(255) NOT NULL,
				`swatch_image` int(1) DEFAULT NULL,
				`sort_order` int(3) DEFAULT NULL,
				`content` text,
				PRIMARY KEY (`option_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");

			try {
				$this->db->query("
					ALTER TABLE `" . DB_PREFIX . "may_advanced_option` ADD show_first_option_in_list int(1) DEFAULT NULL;
				");
			} catch (\Exception $e) {

			}

			$this->db->query("
				ALTER TABLE `" . DB_PREFIX . "may_advanced_option` RENAME `" . DB_PREFIX . "may_advanced_option_template`;
			");
		}

		$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "order_product` MODIFY model VARCHAR(2000);
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "may_advanced_option_product_config` (
			  `product_id` int(11) NOT NULL,
			  `option_name` varchar(255) NOT NULL,
			  `subtract` int(1) DEFAULT NULL,
			  `swatch_image` int(1) DEFAULT NULL,
			  `show_first_option_in_list` int(1) DEFAULT NULL,
			  PRIMARY KEY (`product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "may_advanced_option_product_value` (
			  `product_option_id` int(11) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `option_id` int(11) NOT NULL,
			  `combination_id` varchar(255) NOT NULL,
			  `option_value_id` int(11) NOT NULL,
			  `model` varchar(255),
			  `sku` varchar(255),
			  `upc` varchar(12),
			  `ean` varchar(14),
			  `jan` varchar(13),
			  `isbn` varchar(17),
			  `mpn` varchar(255),
			  `location` varchar(255),
			  `image` varchar(255),
			  `price` decimal(15,4),
			  `price_prefix` varchar(255),
			  `point` int(8),
			  `point_prefix` varchar(255),
			  `weight` decimal(15,8),
			  `weight_prefix` varchar(255),
			  `dimension_l` decimal(15,8),
			  `dimension_w` decimal(15,8),
			  `dimension_h` decimal(15,8),
			  `hide` int(1),
			  `quantity` int(4),
			  PRIMARY KEY (`product_option_id`,`option_value_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "may_advanced_option_product_value` MODIFY COLUMN image text;
		");

		// Normalize old version product option values
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` WHERE `value` LIKE '%:::%'");
		$tags = array();
		foreach ($query->rows as $row) {
			$combination = explode(':::', $row['value']);
			if (count($combination) != 3) {
				continue;
			}

			$tags[$row['product_id']] = array();

			$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_product_config` WHERE product_id = '" . (int)$row['product_id'] . "'");

			$values = json_decode($combination[2], true);

			$subtract = count($values) ? (int)$values['subtract'][0] : 0;
			$swatch_image = count($values) ? (int)$values['swatch_image'] : 1;
			$show_first_option_in_list = count($values) ? (int)$values['show_first_option_in_list'] : 1;

			$this->db->query("INSERT INTO `" . DB_PREFIX . "may_advanced_option_product_config` SET product_id = '" . (int)$row['product_id'] . "', option_name = '" . $this->db->escape($combination[0]) . "', subtract = '" . $subtract . "', swatch_image = '" . $swatch_image . "', show_first_option_in_list = '" . $show_first_option_in_list . "'");

			$this->db->query("DELETE FROM `" . DB_PREFIX . "may_advanced_option_product_value` WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");
			foreach ($values['model'] as $option_value_id => $option_value) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "may_advanced_option_product_value` SET product_option_id = '" . (int)$row['product_option_id'] . "', product_id = '" . (int)$row['product_id'] . "', option_id = '" . (int)$row['option_id'] . "', combination_id = '" . $combination[1] . "', option_value_id = '" . (int)$option_value_id . "', model = '" . (isset($values['model'][$option_value_id]) ? $values['model'][$option_value_id] : "") . "', sku = '" . (isset($values['sku'][$option_value_id]) ? $values['sku'][$option_value_id] : "") . "', upc = '" . (isset($values['upc'][$option_value_id]) ? $values['upc'][$option_value_id] : "") . "', ean = '" . (isset($values['ean'][$option_value_id]) ? $values['ean'][$option_value_id] : "") . "', jan = '" . (isset($values['jan'][$option_value_id]) ? $values['jan'][$option_value_id] : "") . "', isbn = '" . (isset($values['isbn'][$option_value_id]) ? $values['isbn'][$option_value_id] : "") . "', mpn = '" . (isset($values['mpn'][$option_value_id]) ? $values['mpn'][$option_value_id] : "") . "', location = '" . (isset($values['location'][$option_value_id]) ? $values['location'][$option_value_id] : "") . "', image = '" . (isset($values['image'][$option_value_id]) ? json_encode($values['image'][$option_value_id]) : json_encode(array())) . "', price = '" . (isset($values['price'][$option_value_id]) ? $values['price'][$option_value_id] : 0) . "', price_prefix = '" . (isset($values['price_prefix'][$option_value_id]) ? $values['price_prefix'][$option_value_id] : "") . "', point = '" . (isset($values['point'][$option_value_id]) ? $values['point'][$option_value_id] : 0) . "', point_prefix = '" . (isset($values['point_prefix'][$option_value_id]) ? $values['point_prefix'][$option_value_id] : "") . "', weight = '" . (isset($values['weight'][$option_value_id]) ? $values['weight'][$option_value_id] : 0) . "', weight_prefix = '" . (isset($values['weight_prefix'][$option_value_id]) ? $values['weight_prefix'][$option_value_id] : "") . "', dimension_l = '" . (isset($values['dimension_l'][$option_value_id]) ? $values['dimension_l'][$option_value_id] : 0) . "', dimension_w = '" . (isset($values['dimension_w'][$option_value_id]) ? $values['dimension_w'][$option_value_id] : 0) . "', dimension_h = '" . (isset($values['dimension_h'][$option_value_id]) ? $values['dimension_h'][$option_value_id] : 0) . "', hide = '" . (isset($values['hide'][$option_value_id]) ? $values['hide'][$option_value_id] : 0) . "', quantity = '" . (isset($values['quantity'][$option_value_id]) ? $values['quantity'][$option_value_id] : 0) . "'");

				if (!$values['hide'][$option_value_id]) {
					$tags[$row['product_id']] = array_merge($tags[$row['product_id']], array(
						isset($values['model'][$option_value_id]) ? $values['model'][$option_value_id] : "",
						isset($values['sku'][$option_value_id]) ? $values['sku'][$option_value_id] : "",
						isset($values['upc'][$option_value_id]) ? $values['upc'][$option_value_id] : "",
						isset($values['ean'][$option_value_id]) ? $values['ean'][$option_value_id] : "",
						isset($values['jan'][$option_value_id]) ? $values['jan'][$option_value_id] : "",
						isset($values['isbn'][$option_value_id]) ? $values['isbn'][$option_value_id] : "",
						isset($values['mpn'][$option_value_id]) ? $values['mpn'][$option_value_id] : "",
					));
				}
			}
			$this->db->query("UPDATE `" . DB_PREFIX . "product_option` SET `value`='may_advanced_option' WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");
		}

		if (!empty($tags)) {
			foreach ($tags as $product_id => $option_tags) {
				$option_tags = implode(',', array_filter(array_unique($option_tags)));
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE product_id = '" . (int)$product_id . "'");
				foreach ($query->rows as $row) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_description SET tag = '" . $this->db->escape($row['tag'] . ':::' . $option_tags) . "' WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$row['language_id'] . "'");
				}
			}
		}

		// From version 2.0.4.7
		try {
			$this->db->query("
				ALTER TABLE `" . DB_PREFIX . "may_advanced_option_product_value` ADD stock_status_id int(11) DEFAULT NULL;
			");
		} catch (\Exception $e) {
		}
	}

	public function uninstall() {
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "may_advanced_option_template`;");
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "may_advanced_option_product_config`;");
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "may_advanced_option_product_value`;");
	}

	public function getEvents() {
		$events = array();

		foreach ($this->events as $code => $event) {
			$events[self::EVENT_PREFIX . $code] = $event;
		}

		return $events;
	}

	public function isValidEvent($code) {
		return (strpos($code, self::EVENT_PREFIX) === 0);
	}
}
