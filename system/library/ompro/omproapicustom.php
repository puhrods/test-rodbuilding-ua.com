<?php
namespace ompro;

class omproapicustom {

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($name) {
		return $this->registry->get($name);
	}

	// *** расширяемые методы
	/*
		- orderDataProcessMethodList
		- orderDataFormatMethodList
		- productDataProcessMethodList
		- productDataFormatMethodList
		- xEditCustomApiMethodList
		- xEditActionApiMethodList

		- orderAddingDataList
		- productAddingDataList
		- userDataList
		- storeDataList

		- orderAddingData
		- productAddingData
		- userData
		- storeData
	*/


	// Методы - массивы данных ->
	// ключи массивов нужно добавлять в соответствующие списки (методы) для вывода их в таблице переменных и настройках

	// ключи массива order_data нужно добавлять в orderAddingDataList ()
	public function orderAddingData($order_info = array(), $order_products = array()) {
		return array();
	/*
		$order_data = $order_info;
		$order_data['order_data_1'] = 'order_data_1. count order_products = '. count($order_products);

		return $order_data;
		 */
	}

	// ключи массива product_adding_data - добавлять в productAddingDataList
	public function productAddingData($order_info = array(), $product_info = array(), $template = array()) {
		return array();
/*
		$strtoken =$this->ompro->strtoken;
		$product_adding_data = array();
		$product_adding_data['product_data_1'] = 'product_data_1. sku = '. $product_info['sku'];

		return $product_adding_data;
		 */
	}

	public function userData($user_id) {
		return array();
/*
		$user_data = array();
		$user_data['user_data_1'] = 'user_data_1. user_id = ' . $user_id;

		return $user_data;
		 */
	}

	public function storeData($order_info = array()) {
		return array();
/*
		$store_data = array();
		$store_data['store_data_1'] = 'store_data_1. store_id = ' . $order_info['store_id'];

		return $store_data;
		 */
	}


	///// Списки: расширяем аналогичные (одинаковые названия методов) из admin\model\sale\ompro_api.php ->

	// Список: API методы предварительной Обработки данных Заказа
	public function orderDataProcessMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('process_method' => 'exampleProcessMethod', 'name' => 'Custom API - exampleProcessMethod');

		return $methods;
		 */
	}

	// Список: API методы Формата данных Заказа
	public function orderDataFormatMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('process_method' => 'exampleFormatMethod', 'name' => 'Custom API - exampleFormatMethod');

		return $methods;
		 */
	}

	// Список: API методы предварительной Обработки данных Товара
	public function productDataProcessMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('process_method' => 'exampleProcessMethod', 'name' => 'Custom API - exampleProcessMethod');
		$methods[] = array('process_method' => 'getFullSrcImg', 'name' => 'Получить полную ссылку на изображение');
		return $methods;
*/
	}

	// Список: API методы формата данных Товара
	public function productDataFormatMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('process_method' => 'exampleFormatMethod', 'name' => 'Custom API - exampleFormatMethod');

		return $methods;
		 */
	}

	//	Список: кастомные методы быстрого  редактирования
	public function xEditCustomApiMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('key' => 'xEditLinkCustomMethod', 'name' => 'Custom API -xEditLinkCustomMethod'); // пример

		return $methods;
		 */
	}

	// метод редактирования
	public function xEditCustomMethod($data) {
		// code to edit data
	}

	//	Список:  доп. действия после быстрого редактирования
	public function xEditActionApiMethodList() {
		return array();
/*
		$methods = array();
		$methods[] = array('action' => 'examplexEditActionMethod', 'name' => 'Custom API - examplexEditActionMethod');

		return $methods;
		 */
	}

	///// <-  Списки

	///// Списки доп. данных для учёта в настройках полей и Таблиц переменных: расширяем аналогичные методы (одинаковые названия) из admin\model\sale\ompro_api.php ->

	// Доп. данные заказа: ключи массива orderAddingData
	public function orderAddingDataList() {
		return array();
/*
		$data = array();
		$data[] = array('data_group_id' => 'order_info', 'key' => 'order_data_1', 'name' => 'Custom API - order_data_1');

		return $data;
 */
	}

	// Доп. данные товара: ключи массива productAddingData
	public function productAddingDataList() {
		return array();
/*
		$data = array();
		$data[] = array('data_group_id' => 'other_info', 'key' => 'product_data_1', 'name' => 'Custom API - product_data_1');

		return $data;
		 */
	}

	// Доп. данные Пользователя: ключи массива userData
	public function userDataList() {
		return array();
/*
		$data = array();
		$data[] = array('data_group_id' => 'user_info', 'key' => 'user_data_1', 'name' => 'Custom API - user_data_1');

		return $data;
		 */
	}

	// Доп. данные Магазина: ключи массива storeData
	public function storeDataList() {
		return array();
/*
		$data = array();
		$data[] = array('data_group_id' => 'store_info', 'key' => 'store_data_1', 'name' => 'Custom API - store_data_1');

		return $data;
		 */
	}


	// Доп. действия (API методы) после быстрого редактирования
	// Могут быть добавлены в списки: xEditActionApiMethodList
	public function examplexEditActionMethod($post = array()) {
		// action
	//	$this->log->write('Custom API - examplexEditActionMethod ');
	}



	//// Методы предварительной Обработки данных Заказа и Товара ->
	// Могут быть добавлены в списки: orderDataProcessMethodList productDataProcessMethodList

	public function exampleProcessMethod($value = '', $order_info = array(), $product_info = array()) {
	//	return (int)$value * 2;
	}

	public function getFullSrcImg($value = '', $order_info = array(), $product_info = array()) { // ???
		if (!preg_match('/^http/', $value)) {
			$this->load->model('tool/image');
			if (is_file(DIR_IMAGE . $value)) {
				$value = $this->model_tool_image->resize($value, 64, 64);
			} else {
				$value = $this->model_tool_image->resize('no_image.png', 64, 64);
			}
		}

		return $value;
	}


	//// Методы Формата  данных Заказа и Товара ->
	// Могут быть добавлены в списки: orderDataFormatMethodList, productDataFormatMethodList

	public function exampleFormatMethod($value = '', $order_info = array(), $product_info = array()) {
	//	return '<span class="fa fa-times text-red"> '.$value.'</span>';
	}

	// Кастомные API методы редактирования: ->
	// методы формирования ссылки для редактирования
	// Могут быть добавлены в xEditCustomApiMethodList
	public function xEditLinkCustomMethod($data) {
		//	return xEditLink
	}

	// <- Кастомные методы редактирования

	// Excel - форматы валют: ->
	// форматы, ключи добавлять в getCellFormatList
	public function getCurrencyFormats() {
		return array(
	//		'cur_usd3'	=> '"$ "#,##0.0_-'
		);
	}

	// Список форматов, ключи брать из getCurrencyFormats
	public function getCellFormatList() {
		return array(
	//		'cur_usd3' => 'Финансы: $ 1 000,0'
		);
	}
	// <- Excel - форматы валют


	// ОБЩИЕ переменные страницы, выводятся в шаблонах страницы ->
	// список переменных, добавлять из pageHtmlElemVars

	// список блочных элементов
	public function pageHtmlElemVarsBlockList() {
		return array(
	//		'key1' => 'name1',
		);
	}

	// список элементов колонок
	public function pageHtmlElemVarsColumnList() {
		return array(
		//	'key2' => 'name2',
		);
	}

	// список элементов панели инструментов
	public function pageHtmlElemVarsToolsList() {
		return array(
		//	'key3' => 'name3',
		);
	}

	// список элементов групп кнопок и полей
	public function pageHtmlElemVarsBtnGroupList() {
		return array(
	//		'key4' => 'name4',
		);
	}

	// переменные, ключи добавлять в pageHtmlElemVarsList
	public function pageHtmlElemVars($var = '', $all_orders_data = array()) {

		if ($var == 'key1') {
			return '';
		}

		if ($var == 'key2') {
			return '';
		}

		if ($var == 'key3') {
			return '';
		}

		if ($var == 'key4') {
			return '';
		}

	}
	// <- общие переменные страницы

	public function pageValueVarsList() {
		$prefix = 'pageValueVar_';

		return array(
	//		'[[{' .$prefix .'key123}]]' 	=> 'key123',
		);

	}

	public function pageValueVars($key = '', $all_orders_data = array()) {

		if ($key == 'key123') {
			return 'this is key123';
		}

	}

}