<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionModuleLivePrice extends Model {

	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		
		\liveopencart\ext\liveprice::getInstance($this->registry);
	}

}

